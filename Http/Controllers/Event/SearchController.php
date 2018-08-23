<?php

namespace App\Http\Controllers\Event;

use App\Models\Event\Event;
use App\Models\Event\EventStatus;
use App\Http\Controllers\GeodataController;
use App\Resources\HelperResource;
use App\Models\Base\SortOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class SearchController extends Controller
{
    /**
     * @api {get} /api/event/search Search
     * @apiName Search
     * @apiGroup Event Search
     * @apiParam {String} word Searching word.
     * @apiParam {Number} type_destination Id of events type destination.
     * @apiParam {Number} purpose Id of events purpose.
     * @apiParam {Number} religion Id of events religion.
     * @apiParam {Number} country Id of events country.
     * @apiParam {Number} state Id of events state.
     * @apiParam {Number} city Id of events city.
     * @apiParam {Number} profit Type of profit(0- all; 1 - with profit; 2- with out profit).
     * @apiParam {Number} sort Id of sorting parameter.
     * @apiParam {Number} page Number of the page. Default 1.
     * @apiParamExample {json} Request-Example:
     *     {
     *          "word":"Many",
     *          "type_destination":2,
     *          "purpose":2,
     *          "religion":2,
     *          "country":2,
     *          "state":2,
     *          "city":2,
     *          "sort":2,
     *          "profit":0,
     *          "page":2,
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  current_page   Number of the page.
     * @apiSuccess (200) {Array[]}  data   Page data of events.
     * @apiSuccess (200) {Array[]}  data.array   Data array of event.
     * @apiSuccess (200) {Number}  data.array.event.id   Event id
     * @apiSuccess (200) {String}  data.array.event.title   Event title
     * @apiSuccess (200) {String}  data.array.event.story   Event story
     * @apiSuccess (200) {String}  data.array.event.short_story   Event short story
     * @apiSuccess (200) {String}  data.array.event.address   Event address
     * @apiSuccess (200) {Bool}  data.array.event.is_approved   Event approved status(true - is approved)
     * @apiSuccess (200) {Bool}  data.array.event.is_submit   Event submit status(true - is submit)
     * @apiSuccess (200) {Array[]}  data.array.event.images  Array Event preview
     * @apiSuccess (200) {Array[]}  data.array.event.images.array  Array image data
     * @apiSuccess (200) {Number}  data.array.event.images.array.id   Event image id
     * @apiSuccess (200) {Number}  data.array.event.images.array.image_id   Event image file id
     * @apiSuccess (200) {Number}  data.array.event.images.array.is_preview   Preview status of image
     * @apiSuccess (200) {Array[]}  data.array.event.images.array.image  Array Event image file data
     * @apiSuccess (200) {String}  data.array.event.images.array.image.title   Event image file title
     * @apiSuccess (200) {String}  data.array.event.images.array.image.url   Event image file url
     * @apiSuccess (200) {Array[]}  data.array.event.event_status  Array Event status data
     * @apiSuccess (200) {Number}  data.array.event.event_status.id   Event status id
     * @apiSuccess (200) {String}  data.array.event.event_status.title   Event status title
     * @apiSuccess (200) {Array[]}  data.array.event.purpose  Array Event purpose data
     * @apiSuccess (200) {Number}  data.array.event.purpose.id   Event purpose id
     * @apiSuccess (200) {String}  data.array.event.purpose.title   Event purpose title
     * @apiSuccess (200) {Array[]}  data.array.event.religion  Array Event religion data
     * @apiSuccess (200) {Number}  data.array.event.religion.id   Event religion id
     * @apiSuccess (200) {String}  data.array.event.religion.title   Event religion title
     * @apiSuccess (200) {Array[]}  data.array.event.type_destination  Array Event type destination data
     * @apiSuccess (200) {Number}  data.array.event.type_destination.id   Event type destination id
     * @apiSuccess (200) {String}  data.array.event.type_destination.title   Event type destination title
     * @apiSuccess (200) {Array[]}  data.array.event.country  Array Event country data
     * @apiSuccess (200) {Number}  data.array.event.country.id   Event country id
     * @apiSuccess (200) {String}  data.array.event.country.name   Event country name
     * @apiSuccess (200) {String}  data.array.event.country.sortname   Event country sortname
     * @apiSuccess (200) {Array[]}  data.array.event.state  Array Event state data
     * @apiSuccess (200) {Number}  data.array.event.state.id   Event state id
     * @apiSuccess (200) {String}  data.array.event.state.name   Event state name
     * @apiSuccess (200) {Array[]}  data.array.event.city  Array Event city data
     * @apiSuccess (200) {Number}  data.array.event.city.id   Event city id
     * @apiSuccess (200) {String}  data.array.event.city.name   Event city name
     * @apiSuccess (200) {Array[]}  data.array.event.demand  Array Event demand data
     * @apiSuccess (200) {Number}  data.array.event.demand.id  Event demand data id
     * @apiSuccess (200) {Number}  data.array.event.demand.demand_type_id  Event demand data demand type id
     * @apiSuccess (200) {Array[]}  data.array.event.demand.demand_type  Array Event demand data demand type data
     * @apiSuccess (200) {Array[]}  data.array.event.demand.demand_type.id  Id of demand type
     * @apiSuccess (200) {Array[]}  data.array.event.demand.demand_type.title  Name of demand type
     * @apiSuccess (200) {Array[]}  data.array.event.demand.money  Array Event demand data demand type array money data
     * @apiSuccess (200) {Array[]}  data.array.event.demand.money.array  Event demand data demand type array money data
     * @apiSuccess (200) {Number}  data.array.event.demand.money.array.id  Array Event demand data demand type money id
     * @apiSuccess (200) {Number}  data.array.event.demand.money.array.event_demand_id  Array Event demand data demand type money event demand id
     * @apiSuccess (200) {Number}  data.array.event.demand.money.array.summ  Array Event demand data demand type money summ
     * @apiSuccess (200) {Array[]}  data.array.event.demand.volunteers  Array Event demand data demand type array volunteers data
     * @apiSuccess (200) {Array[]}  data.array.event.demand.volunteers.array  Event demand data demand type array volunteers data
     * @apiSuccess (200) {Number}  data.array.event.demand.volunteers.array.id  Array Event demand data demand type volunteers id
     * @apiSuccess (200) {Number}  data.array.event.demand.volunteers.array.event_demand_id  Array Event demand data demand type volunteers event demand id
     * @apiSuccess (200) {String}  data.array.event.demand.volunteers.array.name  Array Event demand data demand type volunteers name
     * @apiSuccess (200) {Number}  data.array.event.demand.volunteers.array.count  Array Event demand data demand type volunteers count
     * @apiSuccess (200) {Array[]}  data.array.event.demand.supplies  Array Event demand data demand type array supplies data
     * @apiSuccess (200) {Array[]}  data.array.event.demand.supplies.array  Event demand data demand type array supplies data
     * @apiSuccess (200) {Number}  data.array.event.demand.supplies.array.id  Array Event demand data demand type supplies id
     * @apiSuccess (200) {Number}  data.array.event.demand.supplies.array.event_demand_id  Array Event demand data demand type supplies event demand id
     * @apiSuccess (200) {String}  data.array.event.demand.supplies.array.name  Array Event demand data demand type supplies name
     * @apiSuccess (200) {Number}  data.array.event.demand.supplies.array.count  Array Event demand data demand type supplies count
     * @apiSuccess (200) {Array[]}  data.array.event.stripe  Array Event stripe data
     * @apiSuccess (200) {Numeric}  data.array.event.stripe.id  Id stripe data
     * @apiSuccess (200) {Numeric}  data.array.event.stripe.event_id  Event id of stripe data
     * @apiSuccess (200) {Numeric}  data.array.event.stripe.event_money_id  Event money id of stripe data
     * @apiSuccess (200) {String}  data.array.event.stripe.stripe_account_id  Stripe account id
     * @apiSuccess (200) {String}  data.array.event.stripe.email  Stripe account email
     * @apiSuccess (200) {Array[]}  data.array.event.stripe.event_payment  Array Event stripe data payment
     * @apiSuccess (200) {Array[]}  data.array.event.stripe.event_payment.array  Array stripe data payment
     * @apiSuccess (200) {Number}  data.array.event.stripe.event_payment.array.id  Payment id
     * @apiSuccess (200) {Number}  data.array.event.stripe.event_payment.array.strip_event_many_data_connect_id  Payment stripe event money data connect id
     * @apiSuccess (200) {Number}  data.array.event.stripe.event_payment.array.amount  Payment amount (in cents)
     * @apiSuccess (200) {Number}  data.array.event.stripe.event_payment.array.amount_result  Payment amount result(in cents)
     * @apiSuccess (200) {String}  data.array.event.stripe.event_payment.array.stripe_charge_id  Payment stripe charge id
     * @apiSuccess (200) {Number}  data.array.event.stripe.event_payment.array.profit  Site profit(in cents)
     * @apiSuccess (200) {Number}  data.array.event.stripe.event_payment.array.stripe_profit  Stripe profit(in cents)
     * @apiSuccess (200) {String}  first_page_url   Url of the first page.
     * @apiSuccess (200) {String}  last_page_url   Url of the last page.
     * @apiSuccess (200) {String}  next_page_url   Url of the next page.
     * @apiSuccess (200) {String}  path   Base URL of request.
     * @apiSuccess (200) {Number}  from   Number of start element.
     * @apiSuccess (200) {Number}  to   Number of end element.
     * @apiSuccess (200) {Number}  total   Count of all elements.
     * @apiSuccess (200) {Number}  per_page   Elements on page.
     * @apiSuccess (200) {Number}  last_page   Number of the last page.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *   {
     *      "current_page":1,
     *      "data":
     *          [
     *              {
     *                  "id":4,
     *                  "title":"testing",
     *                  "story":"testing story",
     *                  "short_story":"short_story",
     *                  "address":"lalalala",
     *                  "type_destination_id":10,
     *                  "purpose_id":18,
     *                  "religion_id":11,
     *                  "country_id":1,
     *                  "state_id":1,
     *                  "city_id":1,
     *                  "user_id":1,
     *                  "event_status_id":0,
     *                  "is_approved":1,
     *                  "is_submit":1,
     *                  "created_at":"2018-05-17 11:39:00",
     *                  "updated_at":"2018-05-17 11:39:00",
     *                  "finish_date":"2018-05-17",
     *                  "comments_count":0,
     *                  "event_views_count":0,
     *                  "images":
     *                      [
     *                          {
     *                              "id":3,
     *                              "event_id":4,
     *                              "image_id":5,
     *                              "created_at":null,
     *                              "updated_at":null,
     *                              "is_preview":1,
     *                              "image":
     *                                  {
     *                                      "title":"test2",
     *                                      "url":"storage\/images\/YuJPIpEwRN19wQoPctCVDVs1CkZ1mLFuHeDc7tfp.jpeg",
     *                                  }
     *                          }
     *                      ]
     *                  "event_status":
     *                      {
     *                          "id":12,
     *                          "title":"Trending"
     *                      }
     *                  "purpose":
     *                      {
     *                          "id":18,
     *                          "title":"Children & Education"
     *                      },
     *                  "religion":
     *                      {
     *                          "id":11,
     *                          "title":"Christianity"
     *                      },
     *                  "type_destination":
     *                      {
     *                          "id":10,
     *                          "title":"Charity"
     *                      },
     *                  "country":
     *                      {
     *                          "id":1,
     *                          "name":"Afghanistan",
     *                          "sortname":"AF"
     *                      },
     *                  "state":
     *                      {
     *                          "id":1,
     *                          "name":"Andaman and Nicobar Islands"
     *                      },
     *                  "city":
     *                      {
     *                          "id":1,
     *                          "name":"Bombuflat"
     *                      },
     *                  "demand":
     *                      [
     *                          {
     *                              "id":1,
     *                              "demand_type_id":2,
     *                              "demand_type":
     *                                  {
     *                                      "id":1,
     *                                      "title":"Many",
     *                                  },
     *                              "volunteers":[],
     *                              "supplies":[],
     *                              "money":
     *                                  [
     *                                      {
     *                                          "id":2,
     *                                          "event_demand_id":1,
     *                                          "summ":1230000,
     *                                      }
     *                                  ]
     *                          },
     *                      ],
     *                  "stripe":
     *                      {
     *                          "id":1,
     *                          "event_id":7,
     *                          "event_money_id":1,
     *                          "stripe_account_id":"acct_1CcvanEgMSxGRqza",
     *                          "email":"test@test.tt",
     *                          "event_payment":
     *                              {
     *                                  "id":1,
     *                                  "strip_event_many_data_connect_id":1,
     *                                  "amount":1000,
     *                                  "amount_result":883,
     *                                  "stripe_charge_id":"dghsfdghfds",
     *                                  "profit":10,
     *                                  "stripe_profit":113,
     *                              },
     *                              .....
     *                      }
     *              },
     *          ],
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/search?page=1",
     *      "from":1,
     *      "last_page":1,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/search?page=1",
     *      "next_page_url":null,
     *      "path":"http:\/\/charity.test\/api\/event\/search",
     *      "per_page":16,
     *      "prev_page_url":null,
     *      "to":4,
     *      "total":4
     *  }
     * @apiError (400) array Array validation errors
     * @apiError (400) array.parameter parameter (key) and value of validation error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *          "content":
     *              [
     *                  "The city field is required.",
     *                  ......
     *              ],
     *          .....
     *      }
     */
    public function search(Request $request){
        $validator = $this->isValidParameners($request);
        if ($validator->fails()){
            return response(json_encode($validator->errors()), 400);
        }
        $close_status = EventStatus::where('name', 'closed')->first();
        $event_search = Event::where('event_status_id','!=', $close_status->id)->where('is_approved', true);

        if ($request->has('word')){
            $word = $request->get('word');
            $event_search = $event_search->where(function ($query) use ($word){
                $query->where('title', 'like', '%'.$word.'%')->orWhere('short_story', 'like', '%'.$word.'%');
            });
        };

        $event_search = $this->ifParameter($request,$event_search,'type_destination');
        $event_search = $this->ifParameter($request,$event_search,'purpose');
        $event_search = $this->ifParameter($request,$event_search,'religion');
        $event_search = $this->ifParameter($request,$event_search,'country');
        $event_search = $this->ifParameter($request,$event_search,'state');
        $event_search = $this->ifParameter($request,$event_search,'city');

        if ($request->has('profit')){ // 0- all; 1 - with profit; 2- with out profit
            if ($request->get('profit') == 1){
                $event_search = $event_search->whereHas('demand.money', function ($query){
                    $query->where('event_moneys', '>', 0);
                });
            }elseif ($request->get('profit') == 2){
                $event_search = $event_search->where(function ($query){
                    $query->whereHas('demand.money', function ($query1){
                        $query1->where('event_moneys', 0);
                    })->orWhereDoesntHave('demand.money');
                });
            }
        }

        $event_search = $event_search->with(['images'=>function($query){$query->where('is_preview', true)->with('image');}])
            ->with( 'event_status', 'purpose', 'religion', 'typeDestination', 'country', 'state', 'city','demand.demandType',
                'demand.volunteers', 'demand.supplies','demand.money', 'stripe.event_payment')
            ->withCount('comments')->withCount('event_views');

        if ($request->has('sort')){
            $page = 1;
            if ($request->has('page')){
                $page = $request->get('page');
            }

            $sort = SortOption::find($request->get('sort'));
            switch ($sort->name){
                case 'featured':
                    return $this->featuredAndTrendingFormData('featured',$event_search, $request,$page);
                    break;
                case 'trending':
                    $event_search = $event_search->orderBy('event_views_count', 'desc');
                    break;
                case 'just_launched':
                    $event_search = $event_search->orderBy('updated_at', 'asc');
                    break;
                case 'oldest':
                    $event_search = $event_search->orderBy('updated_at', 'desc');
                    break;
                case 'ending_soon':
                    $event_search = $event_search->orderBy('finish_date', 'desc');
                    break;
                case 'closest_to_me':
                    if (Auth::user()){
                        $user_city = Auth::user()->city()->first();
                        $event_search = $event_search->get();
                        $event_search->transform(function ($item, $key) use ($user_city){

                            if ($user_city->lat == $item->city->lat && $user_city->lon == $item->city->lon){
                                $item->dist = 0;
                            } else{
                                $item->dist =GeodataController::distanceCalculation($user_city, $item->city);
                            }

                            return $item;
                        });

                        $page_data = HelperResource::formPaginationData($request,$event_search, $page,16,'event.search.get');
                        $page_data['data'] = $event_search->sortBy('dist')->forPage($page, 16)->toArray();

                        return json_encode($page_data);
                    }

                    break;
            }

        }

        return $event_search->paginate(16);
    }

    /**
     * @param $param
     * @param $event_search
     * @param Request $request
     * @param $page
     * @return mixed
     */
    private function featuredAndTrendingFormData($param, $event_search, Request $request, $page){
        $featured = EventStatus::where('name', $param)->first();
        $event_search_1 = clone $event_search;
        $event_search_featured = $event_search->where('event_status_id', $featured->id)->get();
        $event_search_else = $event_search_1->where('event_status_id', '<>', $featured->id)->get();
        $event_search = $event_search_featured->concat($event_search_else);

        $page_data = HelperResource::formPaginationData($request,$event_search, $page,16,'event.search.get');
        $page_data['data'] = $event_search->forPage($page, 16)->toArray();

        return json_encode($page_data);
    }

    /**
     * @param Request $request
     * @param Event $event
     * @param $param_name
     * @return Event
     */
    private function ifParameter(Request $request, $event, $param_name){
        if ($request->has($param_name)){
            return $event->where($param_name.'_id', $request->get($param_name));
        };

        return $event;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function isValidParameners(Request $request){
        $validate_param = [
            'type_destination' => 'exists:type_destinations,id',
            'purpose' => 'exists:purposes,id',
            'religion' => 'exists:religions,id',
            'country' => 'exists:countries,id',
            'state' => 'exists:states,id',
            'city' => 'exists:cities,id',
            'sort' => 'exists:sort_options,id',
            'profit' => 'number|min:0|max:2',
        ];

        return Validator::make($request->all(), $validate_param);
    }
}
