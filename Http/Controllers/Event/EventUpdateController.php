<?php

namespace App\Http\Controllers\Event;

use App\Models\Event\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class EventUpdateController extends Controller
{
    /**
     * @api {get} /api/event/:event_id/update/ Get All updates of Event
     * @apiName Get Event Updates
     * @apiGroup Event Update
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {Number}  page   Page number
     * @apiParamExample {json} Request-Example:
     *     {
     *          "page":1,
     *      }
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  current_page   Number of the page.
     * @apiSuccess (200) {String}  first_page_url   Url of the first page.
     * @apiSuccess (200) {String}  last_page_url   Url of the last page.
     * @apiSuccess (200) {String}  next_page_url   Url of the next page.
     * @apiSuccess (200) {String}  path   Base URL of request.
     * @apiSuccess (200) {Number}  from   Number of start element.
     * @apiSuccess (200) {Number}  to   Number of end element.
     * @apiSuccess (200) {Number}  total   Count of all elements.
     * @apiSuccess (200) {Number}  per_page   Elements on page.
     * @apiSuccess (200) {Number}  last_page   Number of the last page.
     * @apiSuccess (200) {Array[]}  data   Array of updates data
     * @apiSuccess (200) {Array[]}  data.update   Update data
     * @apiSuccess (200) {Number}  data.update.id   Update id
     * @apiSuccess (200) {String}  data.update.title   Update title
     * @apiSuccess (200) {String}  data.update.content   Update content
     * @apiSuccess (200) {Number}  data.update.demand_type_id   Update demand type id
     * @apiSuccess (200) {Number}  data.update.event_id   Update Event id
     * @apiSuccess (200) {Date}  data.update.created_at   Update date create
     * @apiSuccess (200) {Array[]}  data.update.demand_type   Update demand type data
     * @apiSuccess (200) {String}  data.update.demand_type.title   Update demand type title
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *      "current_page":1,
     *      "data":
     *          [
     *              {
     *                  "id":1,
     *                  "title":"Test 1",
     *                  "content":"Testing",
     *                  "demand_type_id":2,
     *                  "event_id":4,
     *                  "created_at":null,
     *                  "demand_type":
     *                      {
     *                          "title":"Volunteers",
     *                      }
     *              },
     *              ......
     *          ],
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/4\/update\/?page=1",
     *      "from":1,
     *      "last_page":1,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/4\/update\/?page=1",
     *      "next_page_url":null,
     *      "path":"http:\/\/charity.test\/api\/event\/4\/update\/",
     *      "per_page":20,
     *      "prev_page_url":null,
     *      "to":5,
     *      "total":5
     *  }
     * @apiError (404) data Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not found
     *      Event not found
     */
    public function index($event_id)
    {
        if ($event = Event::find($event_id)){
            return $event->event_update()->with('demand_type')->orderBy('created_at', 'desc')->paginate(20);
        }
        return response('Event Not Found', 404);
    }

    /**
     * @api {post} /api/event/:event_id/update/ Create update of Event
     * @apiName Create Event Update
     * @apiGroup Event Update
     * @apiPermission User
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {String}  title   Update title. required|string|min:3|max:255
     * @apiParam {String}  content   Update content. required|string|min:3|max:1500
     * @apiParam {Number}  demand_type_id   Update demand type id. If demand type for All Demand used 0 - Do not specify a parameter.
     * @apiParamExample {json} Request-Example:
     *     {
     *          "title":"Some title",
     *          "title":"Some content",
     *          "demand_type_id":1,
     *      }
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  current_page   Number of the page.
     * @apiSuccess (200) {String}  first_page_url   Url of the first page.
     * @apiSuccess (200) {String}  last_page_url   Url of the last page.
     * @apiSuccess (200) {String}  next_page_url   Url of the next page.
     * @apiSuccess (200) {String}  path   Base URL of request.
     * @apiSuccess (200) {Number}  from   Number of start element.
     * @apiSuccess (200) {Number}  to   Number of end element.
     * @apiSuccess (200) {Number}  total   Count of all elements.
     * @apiSuccess (200) {Number}  per_page   Elements on page.
     * @apiSuccess (200) {Number}  last_page   Number of the last page.
     * @apiSuccess (200) {Array[]}  data   Array of updates data
     * @apiSuccess (200) {Array[]}  data.update   Update data
     * @apiSuccess (200) {Number}  data.update.id   Update id
     * @apiSuccess (200) {String}  data.update.title   Update title
     * @apiSuccess (200) {String}  data.update.content   Update content
     * @apiSuccess (200) {Number}  data.update.demand_type_id   Update demand type id
     * @apiSuccess (200) {Number}  data.update.event_id   Update Event id
     * @apiSuccess (200) {Date}  data.update.created_at   Update date create
     * @apiSuccess (200) {Array[]}  data.update.demand_type   Update demand type data
     * @apiSuccess (200) {String}  data.update.demand_type.title   Update demand type title
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *      "current_page":1,
     *      "data":
     *          [
     *              {
     *                  "id":1,
     *                  "title":"Test 1",
     *                  "content":"Testing",
     *                  "demand_type_id":2,
     *                  "event_id":4,
     *                  "created_at":null,
     *                  "demand_type":
     *                      {
     *                          "title":"Volunteers",
     *                      }
     *              },
     *              ......
     *          ],
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/4\/update\/?page=1",
     *      "from":1,
     *      "last_page":1,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/4\/update\/?page=1",
     *      "next_page_url":null,
     *      "path":"http:\/\/charity.test\/api\/event\/4\/update\/",
     *      "per_page":20,
     *      "prev_page_url":null,
     *      "to":5,
     *      "total":5
     *  }
     * @apiError (404) data Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not found
     *      Event not found
     * @apiError (400) array Array validation errors
     * @apiError (400) array.parameter parameter (key) and value of validation error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *          "email":
     *              [
     *                  "The email has already been taken.",
     *                  ........
     *              ],
     *            ......
     *      }
     * @apiError (403) error Error message
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *     Access is denied
     */
    public function store(Request $request, $event_id)
    {
        if ($event = Event::find($event_id)){
            if (Auth::user()->is_admin || $event->user_id == Auth::id()){
                $validator = $this->validateCreate($request);
                if(!$validator->fails()){
                    $update_id = $event->event_update()->insertGetId($request->all());

                    //TODO: Create mail message sender to users

                    return $this->index($event_id);
                }
                return response(json_encode($validator->errors()), 400);
            }
            return response('Access is denied', 403);
        }
        return response('Event Not Found', 404);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function validateCreate(Request $request){
        return Validator::make($request->all(), [
            'title' => 'required|string|min:3|max:255',
            'content' => 'required|string|min:3|max:1500',
            'demand_type_id' => 'exists:demand_types,id',
        ]);
    }

    /**
     * @api {get} /api/event/:event_id/update/:update_id Get Update
     * @apiName Get Update
     * @apiGroup Event Update
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {Number} :update_id   Update id
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  id   Update id
     * @apiSuccess (200) {String}  title   Update title
     * @apiSuccess (200) {String}  content   Update content
     * @apiSuccess (200) {Number}  demand_type_id   Update demand type id
     * @apiSuccess (200) {Number}  event_id   Update Event id
     * @apiSuccess (200) {Date}  created_at   Update date create
     * @apiSuccess (200) {Array[]}  demand_type   Update demand type data
     * @apiSuccess (200) {String}  demand_type.title   Update demand type title
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *      "id":1,
     *      "title":"Test 1",
     *      "content":"Testing",
     *      "demand_type_id":2,
     *      "event_id":4,
     *      "created_at":null,
     *      "demand_type":
     *          {
     *              "title":"Volunteers",
     *          }
     *  }
     * @apiError (404) data Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not found
     *      Event or Update not found
     */
    public function show($event_id, $update_id)
    {
        if ($event = Event::find($event_id)){
            $update = $event->event_update()->where('id', $update_id)->with('demand_type')->first();

            if ($update){
                return $update;
            }
        }

        return response('Event or Update not found', 404);
    }

    /**
     * @api {put} /api/event/:event_id/update/:update_id  Update update of Event
     * @apiName Update Event Update
     * @apiGroup Event Update
     * @apiPermission User
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {Number} :update_id   Update id
     * @apiParam {String}  title   Update title. string|min:3|max:255
     * @apiParam {String}  content   Update content. string|min:3|max:1500
     * @apiParam {Number}  demand_type_id   Update demand type id. If demand type for All Demand used 0 - Do not specify a parameter.
     * @apiParamExample {json} Request-Example:
     *     {
     *          "title":"Some title",
     *          "title":"Some content",
     *          "demand_type_id":1,
     *      }
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Number}  current_page   Number of the page.
     * @apiSuccess (201) {String}  first_page_url   Url of the first page.
     * @apiSuccess (201) {String}  last_page_url   Url of the last page.
     * @apiSuccess (201) {String}  next_page_url   Url of the next page.
     * @apiSuccess (201) {String}  path   Base URL of request.
     * @apiSuccess (201) {Number}  from   Number of start element.
     * @apiSuccess (201) {Number}  to   Number of end element.
     * @apiSuccess (201) {Number}  total   Count of all elements.
     * @apiSuccess (201) {Number}  per_page   Elements on page.
     * @apiSuccess (201) {Number}  last_page   Number of the last page.
     * @apiSuccess (201) {Array[]}  data   Array of updates data
     * @apiSuccess (201) {Array[]}  data.update   Update data
     * @apiSuccess (201) {Number}  data.update.id   Update id
     * @apiSuccess (201) {String}  data.update.title   Update title
     * @apiSuccess (201) {String}  data.update.content   Update content
     * @apiSuccess (201) {Number}  data.update.demand_type_id   Update demand type id
     * @apiSuccess (201) {Number}  data.update.event_id   Update Event id
     * @apiSuccess (201) {Date}  data.update.created_at   Update date create
     * @apiSuccess (201) {Array[]}  data.update.demand_type   Update demand type data
     * @apiSuccess (201) {String}  data.update.demand_type.title   Update demand type title
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *  {
     *      "current_page":1,
     *      "data":
     *          [
     *              {
     *                  "id":1,
     *                  "title":"Test 1",
     *                  "content":"Testing",
     *                  "demand_type_id":2,
     *                  "event_id":4,
     *                  "created_at":null,
     *                  "demand_type":
     *                      {
     *                          "title":"Volunteers",
     *                      }
     *              },
     *              ......
     *          ],
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/4\/update\/?page=1",
     *      "from":1,
     *      "last_page":1,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/4\/update\/?page=1",
     *      "next_page_url":null,
     *      "path":"http:\/\/charity.test\/api\/event\/4\/update\/",
     *      "per_page":20,
     *      "prev_page_url":null,
     *      "to":5,
     *      "total":5
     *  }
     * @apiError (404) data Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not found
     *      Event not found
     * @apiError (400) array Array validation errors
     * @apiError (400) array.parameter parameter (key) and value of validation error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *          "email":
     *              [
     *                  "The email has already been taken.",
     *                  ........
     *              ],
     *            ......
     *      }
     * @apiError (403) error Error message
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *     Access is denied
     */
    public function update(Request $request, $event_id, $update_id)
    {
        if ($event = Event::find($event_id)){
            $update = $event->event_update()->where('id', $update_id);
            if ($update->count()) {
                if (Auth::user()->is_admin || $event->user_id == Auth::id()) {
                    $validator = $this->validateUpdate($request);
                    if (!$validator->fails()) {

                        $update->update($request->all());

                        return $this->index($event_id);
                    }
                    return response(json_encode($validator->errors()), 400);
                }
                return response('Access is denied', 403);
            }
        }
        return response('Event or Update not found', 404);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function validateUpdate(Request $request){
        return Validator::make($request->all(), [
            'title' => 'string|min:3|max:255',
            'content' => 'string|min:3|max:1500',
            'demand_type_id' => 'exists:demand_types,id',
        ]);
    }

    /**
     * @api {delete} /api/event/:event_id/update/:update_id  Delete update of Event
     * @apiName Delete Event Update
     * @apiGroup Event Update
     * @apiPermission User
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiParam {Number} :event_id   Event id
     * @apiParam {Number} :update_id   Update id
     * @apiParam {String}  title   Update title. string|min:3|max:255
     * @apiParam {String}  content   Update content. string|min:3|max:1500
     * @apiParam {Number}  demand_type_id   Update demand type id. If demand type for All Demand used 0 - Do not specify a parameter.
     * @apiParamExample {json} Request-Example:
     *     {
     *          "title":"Some title",
     *          "title":"Some content",
     *          "demand_type_id":1,
     *      }
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (205) {Number}  current_page   Number of the page.
     * @apiSuccess (205) {String}  first_page_url   Url of the first page.
     * @apiSuccess (205) {String}  last_page_url   Url of the last page.
     * @apiSuccess (205) {String}  next_page_url   Url of the next page.
     * @apiSuccess (205) {String}  path   Base URL of request.
     * @apiSuccess (205) {Number}  from   Number of start element.
     * @apiSuccess (205) {Number}  to   Number of end element.
     * @apiSuccess (205) {Number}  total   Count of all elements.
     * @apiSuccess (205) {Number}  per_page   Elements on page.
     * @apiSuccess (205) {Number}  last_page   Number of the last page.
     * @apiSuccess (205) {Array[]}  data   Array of updates data
     * @apiSuccess (205) {Array[]}  data.update   Update data
     * @apiSuccess (205) {Number}  data.update.id   Update id
     * @apiSuccess (205) {String}  data.update.title   Update title
     * @apiSuccess (205) {String}  data.update.content   Update content
     * @apiSuccess (205) {Number}  data.update.demand_type_id   Update demand type id
     * @apiSuccess (205) {Number}  data.update.event_id   Update Event id
     * @apiSuccess (205) {Date}  data.update.created_at   Update date create
     * @apiSuccess (205) {Array[]}  data.update.demand_type   Update demand type data
     * @apiSuccess (205) {String}  data.update.demand_type.title   Update demand type title
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 205 OK
     *  {
     *      "current_page":1,
     *      "data":
     *          [
     *              {
     *                  "id":1,
     *                  "title":"Test 1",
     *                  "content":"Testing",
     *                  "demand_type_id":2,
     *                  "event_id":4,
     *                  "created_at":null,
     *                  "demand_type":
     *                      {
     *                          "title":"Volunteers",
     *                      }
     *              },
     *              ......
     *          ],
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/4\/update\/?page=1",
     *      "from":1,
     *      "last_page":1,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/4\/update\/?page=1",
     *      "next_page_url":null,
     *      "path":"http:\/\/charity.test\/api\/event\/4\/update\/",
     *      "per_page":20,
     *      "prev_page_url":null,
     *      "to":5,
     *      "total":5
     *  }
     * @apiError (404) data Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not found
     *      Event not found
     * @apiError (403) error Error message
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *     Access is denied
     */
    public function destroy($event_id, $update_id)
    {
        if ($event = Event::find($event_id)){
            $update = $event->event_update()->where('id', $update_id);
            if ($update->count()) {
                if (Auth::user()->is_admin || $event->user_id == Auth::id()) {
                        $update->delete();

                        return $this->index($event_id);
                }
                return response('Access is denied', 403);
            }
        }
        return response('Event or Update not found', 404);
    }
}
