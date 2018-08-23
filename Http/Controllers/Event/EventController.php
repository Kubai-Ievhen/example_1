<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Admin\AdminEventController;
use App\Models\Event\ChatMessage;
use App\Models\Event\DemandType;
use App\Models\Event\Event;
use App\Models\Event\EventDemand;
use App\Models\Event\EventVideo;
use App\Models\Event\EventView;
use App\Models\Event\EventVolunteer;
use App\Models\Event\EventSupply;
use App\Models\Event\EventMoney;
use App\Models\Base\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;


class EventController extends Controller
{
    /**
     * @api {put} /api/event/create Create new Event
     * @apiName Create Event
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} type_destination Id of type destination ("Type" from specification). Required
     * @apiParam {Number} purpose Id of purpose. Required
     * @apiParam {Number} religion Id of religion. Required
     * @apiParam {Number} country Id of country. Required
     * @apiParam {Number} state Id of state. Required
     * @apiParam {Number} city Id of city. Required
     * @apiParam {String} address Contact address for even. Required, min length 3, max length 255
     * @apiParam {String} title Title of even. Required, min length 3, max length 255
     * @apiParam {String} short_story Short description of event. Required, min length 3, max length 255
     * @apiParam {String} story Description of event. Required, min length 10.
     * @apiParam {String} finish_date Finish Date of event. Required.
     * @apiParamExample {json} Request-Example:
     *     {
     *       "type_destination": 1
     *       "purpose": 2
     *       "religion": 0
     *       "country": 2
     *       "state": 0
     *       "city": 1
     *       "title": 'somme title'
     *       "address": 'somme address'
     *       "short_story": 'some text'
     *       "story": 'some text'
     *       "finish_date":"2018-07-17"
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {String}  title   Payment Frequency name.
     * @apiSuccess (201) {String}  story   Payment Frequency name.
     * @apiSuccess (201) {String}  short_story   Payment Frequency name.
     * @apiSuccess (201) {String}  address   Payment Frequency name.
     * @apiSuccess (201) {Number}  type_destination_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  purpose_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  religion_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  country_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  state_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  city_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  user_id   Payment Frequency name.
     * @apiSuccess (201) {Date}  updated_at   Payment Frequency name.
     * @apiSuccess (201) {Date}  created_at   Payment Frequency name.
     * @apiSuccess (201) {Number}  id   Payment Frequency name.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *      {
     *          "title":"testing",
     *          "story":"testing story",
     *          "short_story":"short_story",
     *          "address":"lalalala",
     *          "type_destination_id":"1",
     *          "purpose_id":"1",
     *          "religion_id":"1",
     *          "country_id":"1",
     *          "state_id":"1",
     *          "city_id":"1",
     *          "user_id":1,
     *          "finish_date":"2018-07-17"
     *          "updated_at":"2018-05-15 11:41:00",
     *          "created_at":"2018-05-15 11:41:00",
     *          "id":1
     *      }
     * @apiError (400) array Array validation errors
     * @apiError (400) array.parameter parameter (key) and value of validation error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *          "city":
     *              [
     *                  "The city field is required.",
     *                  ......
     *              ],
     *          "short_story":
     *              [
     *                  "The short story field is required.",
     *                  .....
     *              ],
     *          .....
     *      }
     */
    public function create(Request $request){
        $validator = $this->isValid($request);

        if (!$validator->fails()){
            $event = new Event();
            $event->user_id = Auth::id();

            return $this->saveEvent($event,$request);
        }

        return response(json_encode($validator->errors()), 400);
    }

    /**
     * @param $event
     * @param Request $request
     * @return mixed
     */
    private function saveEvent($event, Request $request){
        $event->title               = $request->get('title')??$event->title;
        $event->story               = $request->get('story')??$event->story;
        $event->short_story         = $request->get('short_story')??$event->short_story;
        $event->address             = $request->get('address')??$event->address;
        $event->type_destination_id = $request->get('type_destination')??$event->type_destination_id;
        $event->purpose_id          = $request->get('purpose')??$event->purpose_id;
        $event->religion_id         = $request->get('religion')??$event->religion_id;
        $event->country_id          = $request->get('country')??$event->country_id;
        $event->state_id            = $request->get('state')??$event->state_id;
        $event->city_id             = $request->get('city')??$event->city_id;
        $event->finish_date         = $request->get('finish_date')??$event->finish_date;

        $event->save();

        return $event;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function isValid(Request $request){
        $validate_param = [
            'type_destination' => 'required|exists:type_destinations,id',
            'purpose' => 'required|exists:purposes,id',
            'religion' => 'required|exists:religions,id',
            'country' => 'required|exists:countries,id',
            'state' => 'required|exists:states,id',
            'city' => 'required|exists:cities,id',
            'address' => 'required|min:3|max:255',
            'short_story' => 'required|min:3|max:255',
            'title' => 'required|min:3|max:255',
            'story' => 'required|min:10',
            'finish_date' => 'required',
        ];

        return Validator::make($request->all(), $validate_param);
    }

    /**
     * @api {put} /api/event/:event_id/demand/create Create demand to event
     * @apiName Create demand
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} :event_id Id of event
     * @apiParam {Array[]} money Array of array money data
     * @apiParam {Array[]} money.array Array of money data
     * @apiParam {Number} money.array.summ Sum of needed many (in dollars). Required
     * @apiParam {Number} money.array.payment_frequency Id of payment frequency. Required
     * @apiParam {Array[]} volunteers Array of array volunteers data
     * @apiParam {Array[]} volunteers.array Array of volunteer data
     * @apiParam {String} volunteers.array.name Name of volunteer. Required
     * @apiParam {Number} volunteers.array.count Count of volunteer. Required
     * @apiParam {String} volunteers.array.description Description of volunteer.
     * @apiParam {Bool} volunteers.array.special_skills Needed Special skills of volunteer.
     * @apiParam {Array[]} supplies Array of array supplies data
     * @apiParam {Array[]} supplies.array Array of supply data
     * @apiParam {String} supplies.array.name Name of supply. Required
     * @apiParam {Number} supplies.array.count Count of supply. Required
     * @apiParam {Number} supplies.array.description Description of supply details of parcel transfer. Required
     * @apiParamExample {json} Request-Example:
     *     {
     *       "money":
     *          [
     *              1 :
     *                  [
     *                      'summ' : 10000,
     *                      'payment_frequency' : 2
     *                  ]
     *          ],
     *        "volunteers":
     *          [
     *              1 :
     *                  [
     *                      'name' : 'Same name',
     *                      'count' : 10,
     *                      'special_skills' : 0
     *                      'description' : "Same description"
     *                  ]
     *              2 :
     *                  [
     *                      name' : 'Same name',
     *                      'count' : 15,
     *                      'special_skills' : 1
     *                      'description' : "Same description"
     *                  ]
     *              ........
     *          ],
     *        "supplies":
     *          [
     *              1 :
     *                  [
     *                      'name' : 'Same name',
     *                      'count' : 10,
     *                      'description' : "Same description"
     *                  ]
     *              2 :
     *                  [
     *                      name' : 'Same name',
     *                      'count' : 15,
     *                      'description' : "Same description"
     *                  ]
     *              ........
     *          ]
     *        "_token" : "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of saving (true - ok).
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Event not found
     * @apiError (400) array Array validation errors
     * @apiError (400) array.parameter parameter(key) and value of validation error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *          "supplies":
     *              [
     *                  "The videos field is required."
     *              ]
     *      }
     * @apiError (403) mess Access is denied. If the object does not belong to the user
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *          Forbidden
     */
    public function createDemand(Request $request, $event_id){
        if ($event = Event::find($event_id)){
            if ($event->user_id == Auth::id()){
                $validate = $this->isValidDemand($request);

                if (!$validate->fails()){
                    if ($request->has('volunteers')){
                        $this->saveEventDemands($request->get('volunteers'),'volunteers', $event_id);
                    }

                    if ($request->has('supplies')){
                        $this->saveEventDemands($request->get('supplies'),'supplies', $event_id);
                    }

                    if ($request->has('money')){
                        $this->saveEventDemands($request->get('money'),'money', $event_id);
                    }

                    return response( json_encode(['status'=>true]),201);
                }

                return response(json_encode($validate->errors()), 400);
            }

            return response('Forbidden', 403);
        }

        return response('Event not found', 404);
    }

    /**
     * @param Request $request
     * @param $demand
     * @param $event_id
     */
    private function saveEventDemands($demands, $demand, $event_id){
             $object = EventDemand::create([
                 'demand_type_id' => $this->getDemandTypeId($demand),
                 'event_id' =>$event_id
             ]);

             $data=[];

             foreach ($demands as $key => $volunteer){
                 $data[$key] = array_merge($volunteer,['event_demand_id' => $object->id]);
             }

             $object->$demand()->createMany($data);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function isValidDemand(Request $request){
        $validate_param = [
            'money' => 'array',
            'money.*.summ' => 'numeric',
            'volunteers' => 'array',
            'volunteers.*.name' => 'string|min:2|max:255',
            'volunteers.*.count' => 'numeric',
            'volunteers.*.description' => 'string|min:2|max:255',
            'supplies' => 'array',
            'supplies.*.name' => 'string|min:2|max:255',
            'supplies.*.count' => 'numeric',
        ];

        return Validator::make($request->all(), $validate_param);
    }

    /**
     * @param $name
     * @return mixed
     */
    private function getDemandTypeId($name){
        $demandType = DemandType::where('name', $name)->first();

        return $demandType->id;
    }

    /**
     * @api {put} /api/event/:id/update Update new Event
     * @apiName Update Event
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} type_destination Id of type destination ("Type" from specification). Required
     * @apiParam {Number} purpose Id of purpose. Required
     * @apiParam {Number} religion Id of religion. Required
     * @apiParam {Number} country Id of country. Required
     * @apiParam {Number} state Id of state. Required
     * @apiParam {Number} city Id of city. Required
     * @apiParam {String} address Contact address for even. Required, min length 3, max length 255
     * @apiParam {String} title Title of even. Required, min length 3, max length 255
     * @apiParam {String} short_story Short description of event. Required, min length 3, max length 255
     * @apiParam {String} story Description of event. Required, min length 10.
     * @apiParamExample {json} Request-Example:
     *     {
     *       "type_destination": 1
     *       "purpose": 2
     *       "religion": 0
     *       "country": 2
     *       "state": 0
     *       "city": 1
     *       "title": 'somme title'
     *       "address": 'somme address'
     *       "short_story": 'some text'
     *       "story": 'some text'
     *       "
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {String}  title   Payment Frequency name.
     * @apiSuccess (201) {String}  story   Payment Frequency name.
     * @apiSuccess (201) {String}  short_story   Payment Frequency name.
     * @apiSuccess (201) {String}  address   Payment Frequency name.
     * @apiSuccess (201) {Number}  type_destination_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  purpose_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  religion_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  country_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  state_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  city_id   Payment Frequency name.
     * @apiSuccess (201) {Number}  user_id   Payment Frequency name.
     * @apiSuccess (201) {Date}  updated_at   Payment Frequency name.
     * @apiSuccess (201) {Date}  created_at   Payment Frequency name.
     * @apiSuccess (201) {Number}  id   Payment Frequency name.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *      {
     *          "title":"testing",
     *          "story":"testing story",
     *          "short_story":"short_story",
     *          "address":"lalalala",
     *          "type_destination_id":"1",
     *          "purpose_id":"1",
     *          "religion_id":"1",
     *          "country_id":"1",
     *          "state_id":"1",
     *          "city_id":"1",
     *          "user_id":1,
     *          "updated_at":"2018-05-15 11:41:00",
     *          "created_at":"2018-05-15 11:41:00",
     *          "id":1
     *      }
     * @apiError (400) array Array validation errors
     * @apiError (400) array.parameter parameter (key) and value of validation error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *          "city":
     *              [
     *                  "The city field is required.",
     *                  ......
     *              ],
     *          "short_story":
     *              [
     *                  "The short story field is required.",
     *                  .....
     *              ],
     *          .....
     *      }
     * @apiError (403) mess Access is denied. If the object does not belong to the user
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *          Forbidden
     */
    public function updateEvent(Request $request, $id){
        $validator = $this->isValid($request);

        if (!$validator->fails()){
            if ($event = Event::find($id)){
                if ($event->user_id == Auth::id()){
                    return $this->saveEvent($event,$request);
                }

                return response('Forbidden', 403);
            }

            return response('Event Not Found', 404);
        }

        return response(json_encode($validator->errors()), 400);
    }

    /**
     * @api {get} /api/event/:id Get Event data
     * @apiName Get Event
     * @apiGroup Event
     * @apiParam {Number} :id Id event
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  id   Event id
     * @apiSuccess (200) {String}  title   Event title
     * @apiSuccess (200) {String}  story   Event story
     * @apiSuccess (200) {String}  short_story   Event short story
     * @apiSuccess (200) {String}  address   Event address
     * @apiSuccess (200) {Bool}  is_approved   Event approved status(true - is approved)
     * @apiSuccess (200) {Bool}  is_submit   Event submit status(true - is submit)
     * @apiSuccess (200) {Bool}  send_back   Event is send back
     * @apiSuccess (200) {Date}  created_at   Event created at
     * @apiSuccess (200) {Date}  finish_date   Event finish date
     * @apiSuccess (200) {Array[]}  user   Event Author data
     * @apiSuccess (200) {Number}  user.id   Author user id
     * @apiSuccess (200) {String}  user.name  Author name
     * @apiSuccess (200) {Boot}  user.is_admin    Author it is user-admin(true - is admin)
     * @apiSuccess (200) {Array[]}  images  Array Event image data
     * @apiSuccess (200) {Number}  images.id   Event image id
     * @apiSuccess (200) {Number}  images.image_id   Event image file id
     * @apiSuccess (200) {Number}  images.is_preview   Preview status of image
     * @apiSuccess (200) {Array[]}  images.image  Array Event image file data
     * @apiSuccess (200) {Number}  images.image.id   Event image file id
     * @apiSuccess (200) {String}  images.image.title   Event image file title
     * @apiSuccess (200) {String}  images.image.url   Event image file url
     * @apiSuccess (200) {Array[]}  videos  Array Event video data
     * @apiSuccess (200) {Number}  videos.id   Event video id
     * @apiSuccess (200) {Number}  videos.video_id   Event video file id
     * @apiSuccess (200) {Array[]}  videos.video  Array Event video file data
     * @apiSuccess (200) {Number}  videos.video.id   Event video file id
     * @apiSuccess (200) {String}  videos.video.title   Event video file title
     * @apiSuccess (200) {String}  videos.video.url   Event video file url
     * @apiSuccess (200) {Array[]}  event_status  Array Event status data
     * @apiSuccess (200) {Number}  event_status.id   Event status id
     * @apiSuccess (200) {String}  event_status.title   Event status title
     * @apiSuccess (200) {Array[]}  purpose  Array Event purpose data
     * @apiSuccess (200) {Number}  purpose.id   Event purpose id
     * @apiSuccess (200) {String}  purpose.title   Event purpose title
     * @apiSuccess (200) {Array[]}  religion  Array Event religion data
     * @apiSuccess (200) {Number}  religion.id   Event religion id
     * @apiSuccess (200) {String}  religion.title   Event religion title
     * @apiSuccess (200) {Array[]}  type_destination  Array Event type destination data
     * @apiSuccess (200) {Number}  type_destination.id   Event type destination id
     * @apiSuccess (200) {String}  type_destination.title   Event type destination title
     * @apiSuccess (200) {Array[]}  country  Array Event country data
     * @apiSuccess (200) {Number}  country.id   Event country id
     * @apiSuccess (200) {String}  country.name   Event country name
     * @apiSuccess (200) {String}  country.sortname   Event country sortname
     * @apiSuccess (200) {Array[]}  state  Array Event state data
     * @apiSuccess (200) {Number}  state.id   Event state id
     * @apiSuccess (200) {String}  state.name   Event state name
     * @apiSuccess (200) {Array[]}  city  Array Event city data
     * @apiSuccess (200) {Number}  city.id   Event city id
     * @apiSuccess (200) {String}  city.name   Event city name
     * @apiSuccess (200) {String}  city.lon   Event city lon
     * @apiSuccess (200) {String}  city.lat   Event city lat
     * @apiSuccess (200) {Array[]}  demand  Array Event demand data
     * @apiSuccess (200) {Number}  demand.id  Demand data id
     * @apiSuccess (200) {Number}  demand.demand_type_id  Demand type id
     * @apiSuccess (200) {Array[]}  demand.demand_type  Demand type data
     * @apiSuccess (200) {Array[]}  demand.demand_type.id  Id of demand type
     * @apiSuccess (200) {Array[]}  demand.demand_type.title  Name of demand type
     * @apiSuccess (200) {Array[]}  demand.money  Demand type array money data
     * @apiSuccess (200) {Array[]}  demand.money.array  Demand type array money data
     * @apiSuccess (200) {Number}  demand.money.array.id  Demand type money id
     * @apiSuccess (200) {Number}  demand.money.array.event_demand_id  Demand type money event demand id
     * @apiSuccess (200) {Number}  demand.money.array.summ  Demand type money summ
     * @apiSuccess (200) {Array[]}  demand.volunteers  Demand type array volunteers data
     * @apiSuccess (200) {Array[]}  demand.volunteers.array  Demand type array volunteers data
     * @apiSuccess (200) {Number}  demand.volunteers.array.id  Demand type volunteers id
     * @apiSuccess (200) {Number}  demand.volunteers.array.event_demand_id  Demand type volunteers event demand id
     * @apiSuccess (200) {String}  demand.volunteers.array.name  Demand type volunteers name
     * @apiSuccess (200) {Number}  demand.volunteers.array.count  Demand type volunteers count
     * @apiSuccess (200) {Bool}  demand.volunteers.array.special_skills  Demand type volunteers need special skills
     * @apiSuccess (200) {Bool}  demand.volunteers.array.description  Demand type volunteers description
     * @apiSuccess (200) {Array[]}  demand.volunteers.array.event_volunteer_response  Demand type volunteers responses data array
     * @apiSuccess (200) {Array[]}  demand.volunteers.array.event_volunteer_response.array  Demand type volunteers response data array
     * @apiSuccess (200) {Number}  demand.volunteers.array.event_volunteer_response.array.id  Demand type volunteers response id
     * @apiSuccess (200) {Number}  demand.volunteers.array.event_volunteer_response.array.event_volunteer_id  Id of events volunteer
     * @apiSuccess (200) {Number}  demand.volunteers.array.event_volunteer_response.array.user_id  User Id of response
     * @apiSuccess (200) {Array[]}  demand.supplies  Demand type array supplies data
     * @apiSuccess (200) {Array[]}  demand.supplies.array  Demand type array supplies data
     * @apiSuccess (200) {Number}  demand.supplies.array.id  Demand type supplies id
     * @apiSuccess (200) {Number}  demand.supplies.array.event_demand_id  Demand type supplies event demand id
     * @apiSuccess (200) {String}  demand.supplies.array.name  Demand type supplies name
     * @apiSuccess (200) {Number}  demand.supplies.array.count  Demand type supplies count
     * @apiSuccess (200) {Number}  demand.supplies.array.delivery_options_id  Demand type supplies delivery options id
     * @apiSuccess (200) {Number}  demand.supplies.array.description  Demand type supplies description
     * @apiSuccess (200) {Array[]}  demand.supplies.array.event_supply_response  Demand type supplies response
     * @apiSuccess (200) {Number}  demand.supplies.array.event_supply_response.id  Demand type supplies response id
     * @apiSuccess (200) {Number}  demand.supplies.array.event_supply_response.event_supply_id  Demand type supplies response supply id
     * @apiSuccess (200) {Number}  demand.supplies.array.event_supply_response.user_id  Demand type supplies response user id
     * @apiSuccess (200) {Number}  demand.supplies.array.event_supply_response.count  Demand type supplies response count
     * @apiSuccess (200) {String}  demand.supplies.array.event_supply_response.parcel_status  Demand type supplies response parcel status
     * @apiSuccess (200) {Array[]}  stripe  Array Event stripe data
     * @apiSuccess (200) {Numeric}  stripe.id  Id stripe data
     * @apiSuccess (200) {Numeric}  stripe.event_id  Event id of stripe data
     * @apiSuccess (200) {Numeric}  stripe.event_money_id  Event money id of stripe data
     * @apiSuccess (200) {String}  stripe.stripe_account_id  Stripe account id
     * @apiSuccess (200) {String}  stripe.email  Stripe account email
     * @apiSuccess (200) {Array[]}  stripe.event_payment  Array Event stripe data payment
     * @apiSuccess (200) {Array[]}  stripe.event_payment.array  Array stripe data payment
     * @apiSuccess (200) {Number}  stripe.event_payment.array.id  Payment id
     * @apiSuccess (200) {Number}  stripe.event_payment.array.strip_event_many_data_connect_id  Payment stripe event money data connect id
     * @apiSuccess (200) {Number}  stripe.event_payment.array.amount  Payment amount (in cents)
     * @apiSuccess (200) {Number}  stripe.event_payment.array.amount_result  Payment amount result(in cents)
     * @apiSuccess (200) {String}  stripe.event_payment.array.stripe_charge_id  Payment stripe charge id
     * @apiSuccess (200) {Number}  stripe.event_payment.array.profit  Site profit(in cents)
     * @apiSuccess (200) {Number}  stripe.event_payment.array.stripe_profit  Stripe profit(in cents)
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *      "id":7,
     *      "title":"testing",
     *      "story":"testing story",
     *      "short_story":"short_story",
     *      "address":"lalalala",
     *      "is_approved":1,
     *      "is_submit":1,
     *      "created_at":"2018-05-21 06:55:34",
     *      "finish_date":null,
     *      "send_back":0,
     *      "images":
     *          [
     *              {
     *                  "id":4,
     *                  "image_id":2,
     *                  "is_preview":1,
     *                  "image":
     *                      {
     *                          "id":2,
     *                          "title":"test2",
     *                          "url":"storage\/images\/YuJPIpEwRN19wQoPctCVDVs1CkZ1mLFuHeDc7tfp.jpeg",
     *                      },
     *                      ......
     *              },
     *              ......
     *          ],
     *      "videos":
     *          [
     *              {
     *                  "id":1,
     *                  "video_id":13,
     *                  "video":
     *                      {
     *                          "id":13,
     *                          "title":"test1",
     *                          "url":"http:\/\/apidocjs.com\/",
     *                      },
     *                      ......
     *              },
     *              ........
     *          ]
     *      "user":
     *          {
     *              "id":6,
     *              "name":"Test_user",
     *              "is_admin":0,
     *          },
     *      "event_status":
     *          {
     *              "id":6,
     *              "name":"trending",
     *              "title":"Trending",
     *          },
     *      "purpose":
     *          {
     *              "id":6,
     *              "title":"Children & Education",
     *          },
     *      "religion":
     *          {
     *              "id":6,
     *              "title":"Christianity",
     *          },
     *      "type_destination":
     *          {
     *              "id":6,
     *              "title":"Charity",
     *          },
     *      "country":
     *          {
     *              "id":6,
     *              "sortname":"AF",
     *              "name":"Afghanistan",
     *          },
     *      "state":
     *          {
     *              "id":6,
     *              "name":"Andaman and Nicobar Islands",
     *          },
     *      "city":
     *          {
     *              "id":6,
     *              "name":"Kondapalle",
     *              "lon":"82.2719086",
     *              "lat":"17.562337"
     *          },
     *      "stripe":
     *          {
     *              "id":1,
     *              "event_id":7,
     *              "event_money_id":1,
     *              "stripe_account_id":"acct_1CcvanEgMSxGRqza",
     *              "email":"test@test.tt",
     *              "event_payment":
     *                  {
     *                      "id":1,
     *                      "strip_event_many_data_connect_id":1,
     *                      "amount":1000,
     *                      "amount_result":883,
     *                      "stripe_charge_id":"dghsfdghfds",
     *                      "profit":10,
     *                      "stripe_profit":113,
     *                  },
     *                  .....
     *          },
     *      "demand":
     *          [
     *              {
     *                  "id":1,
     *                  "demand_type_id":2,
     *                  "demand_type":
     *                      {
     *                          "id":2
     *                          "title":"Volunteers",
     *                      },
     *                  "money":
     *                      [
     *                          {
     *                              "id":2,
     *                              "event_demand_id":1,
     *                              "summ":1230000,
     *                          }
     *                      ],
     *                  "volunteers":
     *                      [
     *                          {
     *                              "id":2,
     *                              "event_demand_id":1,
     *                              "name":"test 2",
     *                              "count":22,
     *                              "special_skills":0,
     *                              "description":null,
     *                              "event_volunteer_response":
     *                                  [
     *                                      {
     *                                          "id":4,
     *                                          "event_volunteer_id":2,
     *                                          "user_id":1,
     *                                      },
     *                                      ......
     *                                  ]
     *                          },
     *                          .......
     *                       ],
     *                  "supplies":
     *                      [
     *                          {
     *                              "id":1,
     *                              "event_demand_id":2,
     *                              "name":"Test 1",
     *                              "count":2,
     *                              "delivery_id":0,
     *                              "description":null,
     *                              "event_supply_response":
     *                                  [
     *                                      {
     *                                          "id":1,
     *                                          "event_supply_id":1,
     *                                          "user_id":2,
     *                                          "count":2,
     *                                          "parcel_status":"pending",
     *                                      },
     *                                      .........
     *                                  ]
     *                          },
     *                          ........
     *                      ],
     *              }
     *          ]
     *  }
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Event Not Found
     */
    public function show($id){
        if ($event = Event::find($id)){
            if (Auth::user()){
                if (!EventView::where('event_id', $id)->where('user_id',Auth::id())->count()){
                    EventView::create(['user_id'=>Auth::id(), 'event_id'=>$id]);
                }
            }
            return Event::where('id',$id)->with('user', 'images.image', 'videos.video', 'event_status', 'purpose', 'religion',
                'typeDestination', 'country', 'state', 'city', 'demand.demandType', 'demand.money', 'stripe.event_payment')
                ->with(['demand.volunteers.event_volunteer_response' => function($query){
                    $query->where('user_approved', 1);
                }])
                ->with(['demand.supplies' => function($query){
                    $query->with(['event_supply_response'=>function($query1){
                        $query1->where('user_approved', 1);
                    }]);
                }])->first();

        } else{
            return response('Event Not Found', 404);
        }

    }

    /**
     * @api {put} /api/event/:event_id/demand/update Update demand of event
     * @apiName Update demand
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} :event_id Id of event
     * @apiParam {Array[]} money Array of array money data
     * @apiParam {Array[]} money.remove.arrayArray of money id for remove
     * @apiParam {Array[]} money.add.array Array of money id for add
     * @apiParam {Number} money.add.array.summ Sum of need mоney. Required
     * @apiParam {Number} money.add.array.payment_frequency Id of payment frequency. Required
     * @apiParam {String} money.add.array.account PayPal account. Required
     * @apiParam {Array[]} money.update.array Array of money id for update
     * @apiParam {Number} money.update.array.id Id of updated mоney data. Required
     * @apiParam {Number} money.update.array.summ Sum of updated mоney. Required
     * @apiParam {Number} money.update.array.payment_frequency Id of payment frequency. Required
     * @apiParam {String} money.update.array.account PayPal account. Required
     * @apiParam {Array[]} volunteers Array of array volunteers data
     * @apiParam {Array[]} volunteers.remove.array Array of volunteer id for remove
     * @apiParam {Array[]} volunteers.add.array Array of volunteer id for add
     * @apiParam {String} volunteers.add.array.name Name of volunteer. Required
     * @apiParam {Number} volunteers.add.array.count Count of volunteer. Required
     * @apiParam {String} volunteers.add.array.description Description of volunteer.
     * @apiParam {Bool} volunteers.add.array.special_skills Needed Special skills of volunteer.
     * @apiParam {Array[]} volunteers.update.array Array of volunteer id for update
     * @apiParam {String} volunteers.update.array.name Name of volunteer. Required
     * @apiParam {Number} volunteers.update.array.count Count of volunteer. Required
     * @apiParam {Number} volunteers.update.array.id Id of volunteer. Required
     * @apiParam {String} volunteers.update.array.description Description of volunteer.
     * @apiParam {Bool} volunteers.update.array.special_skills Needed Special skills of volunteer.
     * @apiParam {Array[]} supplies Array of array supplies data
     * @apiParam {Array[]} supplies.remove.array Array of supply id for remove
     * @apiParam {Array[]} supplies.add.array Array of supply id for add
     * @apiParam {String} supplies.add.array.name Name of supply. Required
     * @apiParam {Number} supplies.add.array.count Count of supply. Required
     * @apiParam {Number} supplies.add.array.description Description of supply details of parcel transfer. Required
     * @apiParam {Array[]} supplies.update.array Array of supply id for update
     * @apiParam {String} supplies.update.array.name Name of supply. Required
     * @apiParam {Number} supplies.update.array.count Count of supply. Required
     * @apiParam {Number} supplies.update.array.id Id of supply. Required
     * @apiParam {Number} supplies.update.array.description Description of supply details of parcel transfer. Required
     * @apiParamExample {json} Request-Example:
     *     {
     *       "money":
     *          [
     *              "remove":
     *                  [
     *                      1
     *                  ]
     *              "add":
     *                  [
     *                      1 :
     *                          [
     *                              'summ' : 10000,
     *                              'payment_frequency' : 2
     *                              'account' : '12345678932174'
     *                          ]
     *                  ]
     *              "update":
     *                  [
     *                      1 :
     *                          [
     *                              'id' : 1,
     *                              'summ' : 10000,
     *                              'payment_frequency' : 2
     *                          ]
     *          ],
     *      "volunteers":
     *          [
     *              "remove":
     *                  [
     *                      1,
     *                      2,
     *                      .....
     *                  ]
     *              "add":
     *                  [
     *                      1 :
     *                          [
     *                              'name' : 'Same name',
     *                              'count' : 10,
     *                              'special_skills' : 1
     *                              'description' : "Same description"
     *                          ]
     *                      2 :
     *                          [
     *                              'name' : 'Same name',
     *                              'count' : 15,
     *                              'special_skills' : 1
     *                              'description' : "Same description"
     *                          ]
     *                      ........
     *                  ]
     *              "update":
     *                  [
     *                      1 :
     *                          [
     *                              'id' : 1,
     *                              'name' : 'Same name',
     *                              'count' : 10
     *                          ]
     *                      2 :
     *                          [
     *                              'id' : 2,
     *                              'name' : 'Same name',
     *                              'count' : 15
     *                          ]
     *                      ........
     *                  ]
     *          ],
     *      "supplies":
     *          [
     *              "remove":
     *                  [
     *                      1,
     *                      2,
     *                      .....
     *                  ]
     *              "add":
     *                  [
     *                      1 :
     *                          [
     *                              'name' : 'Same name',
     *                              'count' : 10,
     *                              'description' : "Same description"
     *                          ]
     *                      2 :
     *                          [
     *                              name' : 'Same name',
     *                              'count' : 15,
     *                              'description' : "Same description"
     *                          ]
     *                      ........
     *                  ]
     *              "update":
     *                  [
     *                      1 :
     *                          [
     *                              'id' : 1,
     *                              'name' : 'Same name',
     *                              'count' : 10
     *                              'description' : "Same description"
     *                          ]
     *                      2 :
     *                          [
     *                              'id' : 2,
     *                              'name' : 'Same name',
     *                              'count' : 15
     *                              'description' : "Same description"
     *                          ]
     *                      ........
     *                  ]
     *          ]
     *        "_token" : "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of saving (true - ok).
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Event not found
     * @apiError (400) array Array validation errors
     * @apiError (400) array.parameter parameter(key) and value of validation error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *          "supplies":
     *              [
     *                  "The videos field is required."
     *              ]
     *      }
     * @apiError (403) mess Access is denied. If the object does not belong to the user
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *          Forbidden
     */
    public function updateDemand(Request $request, $event_id){
        if ($event = Event::find($event_id)){
            if ($event->user_id == Auth::id()){
                $validate = $this->isValidDemand($request);

                if (!$validate->fails()){
                    $this->updateDemandData($request,$event_id, 'volunteers', EventVolunteer::class, get_class(new EventVolunteer()));
                    $this->updateDemandData($request,$event_id, 'supplies', EventSupply::class, get_class(new EventSupply()));
                    $this->updateDemandData($request,$event_id, 'money', EventMoney::class, get_class(new EventMoney()));

                    return response( json_encode(['status'=>true]),201);
                }

                return response(json_encode($validate->errors()), 400);
            }

            return response('Forbidden', 403);
        }

        return response('Event not found', 404);
    }

    /**
     * @param Request $request
     * @param $event_id
     * @param $parametre
     * @param $class
     * @param $model_name
     */
    private function updateDemandData(Request $request, $event_id, $parameter_name, $class, $model_name){
        if ($request->has($parameter_name)){
            $volunteers = $request->get($parameter_name);

            if (isset($volunteers['remove'])){
                $class::whereIn('id', $volunteers['remove'])->delete();
            }

            if (isset($volunteers['add'])){
                if ($parameter_name == 'money' && $money = Event::find($event_id)->demand()->money()){
                    $class::where('id', $money->id)->delete();
                }

                $this->saveEventDemands($volunteers['add'],$parameter_name, $event_id);
            }

            if (isset($volunteers['update'])){
                $this->updateEventDemands($model_name, $volunteers['update']);
            }
        }
    }

    /**
     * @param Request $request
     * @param $demand
     * @param $event_id
     */
    private function updateEventDemands($model_name, $demands){
        foreach ($demands as $demand){
            if ($model_name == 'EventMoney'){
                $model_name::where('id', $demand['id'])->update([
                    'summ' =>$demand['summ']
                ]);
            }else{
                $model_name::where('id', $demand['id'])->update([
                    'name' => $demand['name'],
                    'count' =>$demand['count']
                ]);
            }

        }
    }

    /**
     * @api {delete} /api/event/:id Delete  event
     * @apiName Delete event
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} :id Id of event
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of remove event (true - ok).
     * @apiError (404) Event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Event not found
     * @apiError (403) mess Access is denied. If the object does not belong to the user
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *          Forbidden
     */
    public function remove($id){
        if ($event = Event::find($id)){
            if ($event->user_id == Auth::id() || Auth::user()->is_admin){
                $data = $event->comments()->get();

                foreach ($data as $datum){
                    $datum->like()->delete();
                }

                $data = $event->images()->get();

                foreach ($data as $datum){
                    $images = $datum->image();
                    $img_url = [];

                    foreach ($images->get() as $item){
                        $img_url[] = str_replace('storage', 'public', $item->url);
                    }
                    Storage::delete($img_url);

                    $datum->image()->delete();
                }

                $data = $event->videos()->get();

                foreach ($data as $datum){
                    $datum->video()->delete();
                }

                $data = $event->demand()->get();

                foreach ($data as $datum){
                    $datum->volunteers()->delete();
                }

                foreach ($data as $datum){
                    $datum->supplies()->delete();
                }

                foreach ($data as $datum){
                    $datum->money()->delete();
                }

                $event->comments()->delete();
                $event->images()->delete();
                $event->videos()->delete();
                $event->demand()->delete();
                $event->delete();

                return response( json_encode(['status'=>true]),201);
            }
            return response('Forbidden', 403);
        }

        return response('Event not found', 404);
    }


}
