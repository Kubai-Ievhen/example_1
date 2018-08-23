<?php

namespace App\Http\Controllers\Event;

use App\Mail\VolunteerAnsverMailing;
use App\Models\Base\HashKey;
use App\Models\Event\Event;
use App\Models\Event\EventVolunteerResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Mail;


class VolunteerController extends Controller
{
    private $key_name = 'volunteer_ansver_confirm';

    /**
     * @api {post} /api/event/:event_id/volunteer/:volunteer_id Create new Response to Events Demand Volunteer
     * @apiName Create Response Volunteer
     * @apiGroup Response Volunteer
     * @apiPermission User
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {Number}  :volunteer_id   Volunteer id
     * @apiParam {Number}  count   Count to response position. Default 1
     * @apiParamExample {json} Request-Example:
     *     {
     *          "count":1,
     *      }
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of created response
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *  {
     *      'status' : true
     *  }
     * @apiError (404) data Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not found
     *      Event or Volunteers demand not found
     * @apiError (422) data Unprocessable Entity
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Entity
     *      The user has already responded to this event
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Entity
     *      The required number is already dialed
     */
    public function create(Request $request, $event_id, $volunteer_id){
        $event = Event::where('id',$event_id)->with(['demand.volunteers'=>function($query) use ($volunteer_id)
        {$query->where('id', $volunteer_id)->with('event_volunteer_response');}])->first();

        if ($event){
            $event_volunteer_responses = $event->demand->first()->volunteers->first()->event_volunteer_response;

            if (!$event_volunteer_responses->where('user_id',Auth::id())->count()){
                $count_response = $event_volunteer_responses->where('user_approved',true)->sum('count');
                $volunteers = $event->demand->first()->volunteers->first();

                if ($count_response<=$volunteers->count){
                    $data = [
                        'event_volunteer_id'=>$volunteers->id,
                        'user_id'=>Auth::id(),
                    ];

                    if ($request->has('count')){
                        $validator = $this->isValidCreate($request);
                        if(!$validator->fails()){
                            $data['count'] = $request->get('count');
                        } else{
                            return response(json_encode($validator->errors()), 400);
                        }
                    }

                    EventVolunteerResponse::create($data);

                    Mail::to(Auth::user()->email)
                        ->send(new VolunteerAnsverMailing($this->key_name, $event, $volunteers->name));

                    return ['status' => true];
                }

                return response('The required number is already dialed', 422);
            }

            return response('The user has already responded to this event', 422);
        }

        return response('Event or Volunteers demand not found', 404);
    }

    /**
     * @api {put} /api/event/:event_id/volunteer/:volunteer_id/:volunteer_response_id_response_id Creator approve volunteer response
     * @apiName Creator approve volunteer
     * @apiGroup Response Volunteer
     * @apiPermission User
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {Number}  :volunteer_id   Volunteer id
     * @apiParam {Number}  :volunteer_response_id_response_id   Volunteer response id
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of created response
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *  {
     *      'status' : true
     *  }
     * @apiError (404) data Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not found
     *     Event or Volunteers demand not found
     */
    public function creatorApprove($event_id,$volunteer_id,$volunteer_response_id){
        $event = Event::where('id',$event_id)->with(['demand.volunteers'=>function($query) use ($volunteer_id,$volunteer_response_id)
        {$query->where('id', $volunteer_id)->with(['event_volunteer_response' =>function($query) use($volunteer_response_id){
            $query->where('id', $volunteer_response_id);
        }]);}])->first();

        if($event_volunteer_responses = $event->demand->first()->volunteers->first()->event_volunteer_response){
            EventVolunteerResponse::where('id',$volunteer_response_id)->update(['creator_approved'=>true]);
            return ['status' => true];
        }

        return response('Event or Volunteers demand not found', 404);
    }

    /**
     * @api {get} /api/event/:event_id/volunteer/approve/:key User approve Response to Events Demand Volunteer
     * @apiName User approve Response Volunteer
     * @apiGroup Response Volunteer
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {Number}  :key   Hash key of approve request
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Bool}  status   Status of created response
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *      'status' : true
     *  }
     * @apiError (404) data Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not found
     *      Event not found
     * @apiError (400) message Bad Request
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *      [
     *          'message' : 'Event Volunteer Responses not found'
     *      ]
     */
    public function userApprove($event_id, $key){
        if ($user = HashKey::getUser($key,$this->key_name)){
            HashKey::destroy_key($key,$this->key_name,$user->id);

            if ($event = Event::where('id',$event_id)->with(['demand.volunteers.event_volunteer_response'=>function($query) use ($user){
                $query->where('user_id',$user->id)->limit(1);
            }])->first()){
                if ($respons_id = $event->demand->first()->volunteers->first()->event_volunteer_response->first()->id){
                    EventVolunteerResponse::where('id',$respons_id)->update(['user_approved'=>true]);

                    return response( json_encode(['status'=>true]),200);
                }

                return response(json_encode(['message' => 'Event Volunteer Responses not found']), 400);
            }

            return response('Event  not found',404);
        }

        return response(json_encode(['message' => 'invalid key or expiration date has expired']), 400);

    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function isValidCreate(Request $request){
        return Validator::make($request->all(), [
            'count' => 'numeric|min:1'
        ]);
    }
}


