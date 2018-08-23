<?php

namespace App\Http\Controllers\Event;

use App\Models\Event\ChatMessage;
use App\Models\Event\Event;
use App\Resources\HelperResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class ChatController extends Controller
{
    /**
     * @api {put} /api/event/:event_id/chat/create Create new message to event chat
     * @apiName Create message
     * @apiGroup Event Chat
     * @apiPermission User
     * @apiParam {Number} :event_id Id of event
     * @apiParam {String} message Message content
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {array[]}  messages   Array of all message in chat.
     * @apiSuccess (201) {Object[]}  messages.message   Message data
     * @apiSuccess (201) {String}  messages.message.id Message id
     * @apiSuccess (201) {String}  messages.message.event_id Chat from event of id
     * @apiSuccess (201) {Number}  messages.message.user_id User id for messaging
     * @apiSuccess (201) {bool}  messages.message.admin_send Message is from admin
     * @apiSuccess (201) {String}  messages.message.content Messages content
     * @apiSuccess (201) {bool}  messages.message.is_read Messages status of reading
     * @apiSuccess (201) {Date}  messages.message.created_at Message updated date
     * @apiSuccess (201) {Date}  messages.message.updated_at Message created date
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *      [
     *          {
     *              "id":1,
     *              "event_id":1,
     *              "user_id":1,
     *              "admin_send":0,         //This message send user
     *              "content":"test",
     *              "is_read":0,
     *              "created_at":"2018-05-15 13:06:52",
     *              "updated_at":"2018-05-15 13:06:52"
     *          },
     *          {
     *              "id":2,
     *              "event_id":1,
     *              "user_id":1,
     *              "admin_send":1,         //This message send admin
     *              "content":"test",
     *              "is_read":0,
     *              "created_at":"2018-05-15 13:07:14",
     *              "updated_at":"2018-05-15 13:07:14"
     *          }
     *      ]
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Event not found
     * @apiError (400) array Array validation errors
     * @apiError (400) array.parameter parameter(key) and value of validation error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *          "message":
     *              [
     *                  "The message field is required."
     *              ]
     *      }
     */
    public function create(Request $request, $id){
        if ($event = Event::find($id)){
            $validate = $this->isValid($request);

            if (!$validate->fails()){
                $event->chat()->create([
                    'user_id' => $event->user_id,
                    'content' => $request->get('message'),
                    'admin_send' => Auth::user()->is_admin,
                ]);

                return response(json_encode($event->chat()->get()),201);
            }

            return response(json_encode($validate->errors()), 400);
        }

        return response('Event not found', 404);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function isValid(Request $request){
        $validate_param = [
            'message' => 'required',
        ];

        return Validator::make($request->all(), $validate_param);
    }

    /**
     * @api {get} /api/event/user/chat Get All Users Chats
     * @apiName Users Chats
     * @apiGroup Event Chat
     * @apiPermission User
     * @apiParam {Number} page Number of page. Default 1
     * @apiParamExample {json} Request-Example:
     *     {
     *          "page":2
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  current_page   Number of the page.
     * @apiSuccess (200) {Array[]}  data   Page data of chats.
     * @apiSuccess (200) {Array[]}  data.array   Data array of massage (event id for chat).
     * @apiSuccess (200) {Number}  data.array.id   Message id
     * @apiSuccess (200) {Number}  data.array.event_id   Event id
     * @apiSuccess (200) {Number}  data.array.user_id   User id
     * @apiSuccess (200) {Bool}  data.array.admin_send   Message sender. If TRUE - is send of Admin, else it is send user
     * @apiSuccess (200) {String}  data.array.content   Message content
     * @apiSuccess (200) {Bool}  data.array.is_read   Reading status
     * @apiSuccess (200) {Date}  data.array.created_at   created data and time
     * @apiSuccess (200) {Array[]}  data.array.event   Event data array
     * @apiSuccess (200) {Number}  data.array.event.id   Event id
     * @apiSuccess (200) {String}  data.array.event.name   Event name
     * @apiSuccess (200) {Array[]}  data.array.user   User data array
     * @apiSuccess (200) {Number}  data.array.user.id   User id
     * @apiSuccess (200) {String}  data.array.user.name   User name
     * @apiSuccess (200) {Number}  data.array.count   Count of all message from the chat
     * @apiSuccess (200) {Number}  data.array.unread   Count unread message from the chat
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
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/user\/chat?page=1",
     *      "current_page":1,
     *      "from":1,
     *      "last_page":1,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/user\/chat?page=1",
     *      "next_page_url":null,
     *      "path":"http:\/\/charity.test\/api\/event\/user\/chat?",
     *      "per_page":20,
     *      "prev_page_url":null,
     *      "to":2,
     *      "total":2,
     *      "data":
     *          {
     *              "6":
     *                  {
     *                      "id":7,
     *                      "event_id":6,
     *                      "user_id":6,
     *                      "admin_send":0,
     *                      "content":"test",
     *                      "is_read":0,
     *                      "created_at":"2018-09-15 13:09:15",
     *                      "updated_at":"2018-09-15 13:09:15",
     *                      "event":
     *                          {
     *                              "id":6,
     *                              "title":"testing"
     *                          },
     *                      "user":
     *                          {
     *                              "id":6,
     *                              "name":"Test_user"
     *                          },
     *                      "count":3,
     *                      "unread":2
     *                  },
     *              .......
     *           }
     *   }
     */
    public function getUserChats(Request $request){
        $message = ChatMessage::where('user_id',Auth::id())->with('event', 'user')->get();
        $message = $message->sortByDesc('created_at')->groupBy('event_id');

        return $this->getChatsList($request,$message,0);
    }

    /**
     * @api {get} /api/event/:event_id/chat/user Get message of the chat from event
     * @apiName Users Event Chat
     * @apiGroup Event Chat
     * @apiPermission User
     * @apiParam {Number} event_id Event id
     * @apiParam {Number} page Number of page. Default 1
     * @apiParamExample {json} Request-Example:
     *     {
     *          "page":2
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  current_page   Number of the page.
     * @apiSuccess (200) {Array[]}  data   Page data of chats.
     * @apiSuccess (200) {Array[]}  data.array   Data array of massage.
     * @apiSuccess (200) {Number}  data.array.id   Message id
     * @apiSuccess (200) {Number}  data.array.event_id   Event id
     * @apiSuccess (200) {Number}  data.array.user_id   User id
     * @apiSuccess (200) {Bool}  data.array.admin_send   Message sender. If TRUE - is send of Admin, else it is send user
     * @apiSuccess (200) {String}  data.array.content   Message content
     * @apiSuccess (200) {Bool}  data.array.is_read   Reading status
     * @apiSuccess (200) {Date}  data.array.created_at   created data and time
     * @apiSuccess (200) {Array[]}  data.array.event   Event data array
     * @apiSuccess (200) {Number}  data.array.event.id   Event id
     * @apiSuccess (200) {String}  data.array.event.name   Event name
     * @apiSuccess (200) {Array[]}  data.array.user   User data array
     * @apiSuccess (200) {Number}  data.array.user.id   User id
     * @apiSuccess (200) {String}  data.array.user.name   User name
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
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/7\/chat\/user?page=1",
     *      "current_page":1,
     *      "from":1,
     *      "last_page":1,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/7\/chat\/user?page=1",
     *      "next_page_url":null,
     *      "path":"http:\/\/charity.test\/api\/event\/7\/chat\/user?",
     *      "per_page":10,
     *      "prev_page_url":null,
     *      "to":5,
     *      "total":5,
     *      "data":
     *          {
     *              "2":
     *                  {
     *                      "id":4,
     *                      "event_id":7,
     *                      "user_id":6,
     *                      "admin_send":0,
     *                      "content":"123546",
     *                      "is_read":1,
     *                      "created_at":"2018-05-16 13:17:25",
     *                      "updated_at":"2018-05-16 13:17:25",
     *                      "event":
     *                          {
     *                              "id":7,
     *                              "title":"testing"
     *                          },
     *                      "user":
     *                          {
     *                              "id":6,
     *                              "name":"Test_user"
     *                          }
     *                  },
     *                  ........
     *          }
     *   }
     */
    public function getUserEventChat(Request $request, $event_id){
        return $this->getEventChat($request,$event_id,Auth::id(),0);
    }

    /**
     * @api {get} /api/admin/user/chat Get All Admins Chats
     * @apiName Admins Chats
     * @apiGroup Event Chat
     * @apiPermission Admin
     * @apiParam {Number} page Number of page. Default 1
     * @apiParamExample {json} Request-Example:
     *     {
     *          "page":2
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  current_page   Number of the page.
     * @apiSuccess (200) {Array[]}  data   Page data of chats.
     * @apiSuccess (200) {Array[]}  data.array   Data array of massage (event id for chat).
     * @apiSuccess (200) {Number}  data.array.id   Message id
     * @apiSuccess (200) {Number}  data.array.event_id   Event id
     * @apiSuccess (200) {Number}  data.array.user_id   User id
     * @apiSuccess (200) {Bool}  data.array.admin_send   Message sender. If TRUE - is send of Admin, else it is send user
     * @apiSuccess (200) {String}  data.array.content   Message content
     * @apiSuccess (200) {Bool}  data.array.is_read   Reading status
     * @apiSuccess (200) {Date}  data.array.created_at   created data and time
     * @apiSuccess (200) {Array[]}  data.array.event   Event data array
     * @apiSuccess (200) {Number}  data.array.event.id   Event id
     * @apiSuccess (200) {String}  data.array.event.name   Event name
     * @apiSuccess (200) {Array[]}  data.array.user   User data array
     * @apiSuccess (200) {Number}  data.array.user.id   User id
     * @apiSuccess (200) {String}  data.array.user.name   User name
     * @apiSuccess (200) {Number}  data.array.count   Count of all message from the chat
     * @apiSuccess (200) {Number}  data.array.unread   Count unread message from the chat
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
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/user\/chat?page=1",
     *      "current_page":1,
     *      "from":1,
     *      "last_page":1,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/user\/chat?page=1",
     *      "next_page_url":null,
     *      "path":"http:\/\/charity.test\/api\/event\/user\/chat?",
     *      "per_page":20,
     *      "prev_page_url":null,
     *      "to":2,
     *      "total":2,
     *      "data":
     *          {
     *              "6":
     *                  {
     *                      "id":7,
     *                      "event_id":6,
     *                      "user_id":6,
     *                      "admin_send":0,
     *                      "content":"test",
     *                      "is_read":0,
     *                      "created_at":"2018-09-15 13:09:15",
     *                      "updated_at":"2018-09-15 13:09:15",
     *                      "event":
     *                          {
     *                              "id":6,
     *                              "title":"testing"
     *                          },
     *                      "user":
     *                          {
     *                              "id":6,
     *                              "name":"Test_user"
     *                          },
     *                      "count":3,
     *                      "unread":2
     *                  },
     *              .......
     *           }
     *   }
     */
    public function getAdminMessages(Request $request){
        $message = ChatMessage::with('event', 'user')->get();
        $message = $message->sortByDesc('created_at')->groupBy('event_id');

        return $this->getChatsList($request,$message,1);
    }

    /**
     * @api {get} /api/admin/:event_id/chat/user Get message of the chat from event for Admin
     * @apiName Admins Event Chat
     * @apiGroup Event Chat
     * @apiPermission Admin
     * @apiParam {Number} event_id Event id
     * @apiParam {Number} page Number of page. Default 1
     * @apiParamExample {json} Request-Example:
     *     {
     *          "page":2
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  current_page   Number of the page.
     * @apiSuccess (200) {Array[]}  data   Page data of chats.
     * @apiSuccess (200) {Array[]}  data.array   Data array of massage.
     * @apiSuccess (200) {Number}  data.array.id   Message id
     * @apiSuccess (200) {Number}  data.array.event_id   Event id
     * @apiSuccess (200) {Number}  data.array.user_id   User id
     * @apiSuccess (200) {Bool}  data.array.admin_send   Message sender. If TRUE - is send of Admin, else it is send user
     * @apiSuccess (200) {String}  data.array.content   Message content
     * @apiSuccess (200) {Bool}  data.array.is_read   Reading status
     * @apiSuccess (200) {Date}  data.array.created_at   created data and time
     * @apiSuccess (200) {Array[]}  data.array.event   Event data array
     * @apiSuccess (200) {Number}  data.array.event.id   Event id
     * @apiSuccess (200) {String}  data.array.event.name   Event name
     * @apiSuccess (200) {Array[]}  data.array.user   User data array
     * @apiSuccess (200) {Number}  data.array.user.id   User id
     * @apiSuccess (200) {String}  data.array.user.name   User name
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
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/7\/chat\/user?page=1",
     *      "current_page":1,
     *      "from":1,
     *      "last_page":1,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/7\/chat\/user?page=1",
     *      "next_page_url":null,
     *      "path":"http:\/\/charity.test\/api\/event\/7\/chat\/user?",
     *      "per_page":10,
     *      "prev_page_url":null,
     *      "to":5,
     *      "total":5,
     *      "data":
     *          {
     *              "2":
     *                  {
     *                      "id":4,
     *                      "event_id":7,
     *                      "user_id":6,
     *                      "admin_send":0,
     *                      "content":"123546",
     *                      "is_read":1,
     *                      "created_at":"2018-05-16 13:17:25",
     *                      "updated_at":"2018-05-16 13:17:25",
     *                      "event":
     *                          {
     *                              "id":7,
     *                              "title":"testing"
     *                          },
     *                      "user":
     *                          {
     *                              "id":6,
     *                              "name":"Test_user"
     *                          }
     *                  },
     *                  ........
     *          }
     *   }
     */
    public function getAdminEventChat(Request $request, $event_id, $user_id){
        return $this->getEventChat($request,$event_id,$user_id,1);
    }

    /**
     * @param Request $request
     * @param $user_id
     * @return string
     */
    private function getChatsList(Request $request, $message, $sender){
        $url_pref = $sender?'admin.':'';

        $message->transform(function ($item, $key) use ($sender){
            $data = $item->first()->toArray();
            $data['count'] = $item->count();
            $data['unread'] = $item->where('is_read',0)->where('admin_send',$sender)->count();

            return $data;
        });

        $page = $request->has('page')?$request->get('page'):1;
        $page_data = HelperResource::formPaginationData($request,$message, $page,20,$url_pref.'event.user_message');
        $page_data['data'] = $message->sortByDesc('created_at')->forPage($page, 20)->toArray();

        return json_encode($page_data);
    }

    /**
     * @param Request $request
     * @param $event_id
     * @param $user_id
     * @return string
     */
    private function getEventChat(Request $request, $event_id, $user_id,$sender){
        $page = $request->has('page')?$request->get('page'):1;
        $url_prefix = $sender?'admin.':'';

        $message = ChatMessage::where('user_id',$user_id)->where('event_id',$event_id)
            ->with('event', 'user')->get();
        $message = $message->sortByDesc('created_at');

        $page_data = HelperResource::formPaginationData($request,$message, $page,10,
            $url_prefix.'event.chat.user_event',['event_id' => $event_id]);
        $page_data['data'] = $message->sortByDesc('created_at')->forPage($page, 10)->toArray();

        ChatMessage::where('user_id',$user_id)->where('event_id',$event_id)->where('is_read',0)
            ->where('admin_send',$sender)->update(['is_read'=>1]);

        return json_encode($page_data);
    }
}
