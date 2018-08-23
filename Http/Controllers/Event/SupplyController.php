<?php

namespace App\Http\Controllers\Event;

use App\Mail\SupplyAnsverMailing;
use App\Models\Base\DeliveryOption;
use App\Models\Event\Event;
use App\Models\Event\EventSupplyResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Base\HashKey;


class SupplyController extends Controller
{
    private $key_name = 'supply_ansver_confirm';

    /**
     * @api {post} /api/event/:event_id/supply/response Create new Response to Events Demand Supply
     * @apiName Create Response Supply
     * @apiGroup Response Supply
     * @apiPermission User
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {Array[]}  data   Supply response data array
     * @apiParam {Object}  data.obj   Supply response data
     * @apiParam {Number}  data.obj.objsupply_id   Supply id
     * @apiParam {Number}  data.obj.supply_id   Supply id
     * @apiParam {Number}  count   Count to response position. Default 1
     * @apiParamExample {json} Request-Example:
     *   data:
     *      [
     *          {
     *              "supply_id":1,
     *              "count":1,
     *          },
     *          .....
     *      ]
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
     *      Event or Supplies demand not found
     */
    public function create(Request $request, $event_id){
        $validator = $this->isValidCreate($request);
        if($validator->fails()){
            return response(json_encode($validator->errors()), 400);
        }

        $data = $request->get('data');
        $data_to_mail = [];
        $event = Event::where('id',$event_id)->first();
        foreach ($data as $datum) {
            $supply_id = $datum['supply_id'];

            $event = Event::where('id',$event_id)->with(['demand.supplies'=>function($query) use ($supply_id)
            {$query->where('id', $supply_id)->with('event_supply_response');}])->first();

            if ($event) {
                $event_supply_responses = $event->demand->first()->supplies->first()->event_supply_response;

                if (!$event_supply_responses->where('user_id', Auth::id())->count()) {
                    $count_response = $event_supply_responses->where('user_approved', true)->sum('count');
                    $supply = $event->demand->first()->supplies->first();

                    if ($count_response <= $supply->count) {
                        $data = [
                            'event_supply_id' => $supply->id,
                            'user_id' => Auth::id(),
                        ];

                        $data['count'] = $datum['count'];

                        $data_to_mail[] = [
                            'supply' => $supply->name,
                            'description' => $supply->description,
                            'count' => $datum['count']
                        ];

                        EventSupplyResponse::create($data);
                    }
                }
            }
            return response('Event or Supplies demand not found', 404);
        }

        Mail::to(Auth::user()->email)
            ->send(new SupplyAnsverMailing($this->key_name, $event,$data_to_mail));
        //TODO: Mail send with inform fot approve
        return ['status' => true];
    }

    /**
     * @api {get} /api/event/:event_id/supply/approve/:key User approve Response to Events Demand Supply
     * @apiName User approve Response Supply
     * @apiGroup Response Supply
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
     *          'message' : 'Event Supply Responses not found'
     *      ]
     */
    public function userApproveIntention($event_id, $key){
        if ($user = HashKey::getUser($key,$this->key_name)){
            HashKey::destroy_key($key,$this->key_name,$user->id);

            if ($event = Event::where('id',$event_id)->with(['demand.supplies.event_supply_response'=>function($query) use ($user){
                $query->where('user_id',$user->id)->where('user_approved',0);
            }])->first()){

                $resp_id = [];
                foreach ($event->demand as $demand) {
                    if ($demand->supplies->count()){
                        foreach ($demand->supplies as $supply) {
                            if ($supply->event_supply_response->count()){
                                foreach ($supply->event_supply_response as $event_supply_response) {
                                    $resp_id[] = $event_supply_response->id;
                                }
                            }
                        }
                    }
                }

                if (count($resp_id)){
                    EventSupplyResponse::whereIn('user_id',$resp_id)->update(['user_approved'=>true]);

                    return response( json_encode(['status'=>true]),200);
                }

                return response(json_encode(['message' => 'Event Supply Responses not found']), 400);
            }

            return response('Event  not found',404);
        }

        return response(json_encode(['message' => 'invalid key or expiration date has expired']), 400);
    }

    /**
     * @api {put} /api/event/:event_id/supply/:supply_id/:supply_response_id/send User send parcel
     * @apiName User send parcel
     * @apiGroup Response Supply
     * @apiPermission User
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {Number}  :supply_id   Supply id
     * @apiParam {Number}  :supply_response_id   Supply response id
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
     *      Event or Supplies demand not found
     */
    public function userSendParcel($event_id,$supply_id,$supply_response_id){
        $event = Event::where('id',$event_id)->with(['demand.supplies'=>function($query) use ($supply_id,$supply_response_id)
        {$query->where('id', $supply_id)->with(['event_supply_response' =>function($query) use($supply_response_id){
            $query->where('id', $supply_response_id)->where('user_id',Auth::id());
            }]);
        }])->first();

        if($event_supply_responses = $event->demand->first()->supplies->first()->event_supply_response){
            EventSupplyResponse::where('id',$supply_response_id)->update(['parcel_status'=>'sent', 'user_approved'=>true]);
            return ['status' => true];
        }

        return response('Event or Supplies demand not found', 404);
    }

    /**
     * @api {put} /api/event/:event_id/supply/:supply_id/:supply_response_id/received Creator received parcel
     * @apiName Creator received parcel
     * @apiGroup Response Supply
     * @apiPermission User
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {Number}  :supply_id   Supply id
     * @apiParam {Number}  :supply_response_id   Supply response id
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
     *      Event or Supplies demand not found
     */
    public function creatorApproveParcel($event_id,$supply_id,$supply_response_id){
        $event = Event::where('id',$event_id)->with(['demand.supplies'=>function($query) use ($supply_id,$supply_response_id)
        {$query->where('id', $supply_id)->with(['event_supply_response' =>function($query) use($supply_response_id){
            $query->where('id', $supply_response_id);
        }]);}])->first();

        if($event_supply_responses = $event->demand->first()->supplies->first()->event_supply_response){
            EventSupplyResponse::where('id',$supply_response_id)->update(['parcel_status'=>'received']);
            return ['status' => true];
        }

        return response('Event or Supplies demand not found', 404);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function isValidCreate(Request $request){
        return Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.supply_id' => 'required|numeric',
            'data.*.count' => 'required|numeric|min:1',
        ]);
    }
    /**
     * @api {get} /api/event/delivery_options Get all delivery options
     * @apiName Delivery Options
     * @apiGroup Supply
     * @apiPermission User
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Array}  array   Data Delivery Options data
     * @apiSuccess (200) {Number}  array.id   Delivery Options id
     * @apiSuccess (200) {String}  array.title   Delivery Options title
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *  [
     *      {
     *          "id":1,
     *          "title":"Ship",
     *      },
     *      {
     *          "id":2,
     *          "title":"Drop-Off",
     *      },
     *      {
     *          "id":3,
     *          "title":"Pickup",
     *      }
     *  ]
     */

    public function getDeliveryOptions(){
        return DeliveryOption::all();
    }
}
