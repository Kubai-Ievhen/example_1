<?php

namespace App\Http\Controllers\Event;

use App\Models\Event\Event;
use App\Models\Event\EventComment;
use App\Models\Event\EventCommentImage;
use App\Models\Event\EventImage;
use App\Resources\MediaHelper;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class CommentController extends Controller
{

    /**
     * @api {put} /api/event/:event_id/comment Create new Comment to event
     * @apiName Create Comment
     * @apiGroup Event Comment
     * @apiPermission User
     * @apiParam {Number} :event_id Id of event for commenting
     * @apiParam {String} content Comment content. Required
     * @apiParam {Array[]} images Array of array image data
     * @apiParam {Array[]} images.array Array of image data
     * @apiParam {File} images.array. File of image
     * @apiParamExample {json} Request-Example:
     *     {
     *       "content": "Coment content"
     *       "images":
     *          [
     *              1 :
     *                  [
     *                      'file' : 'file content'
     *                  ]
     *              2 :
     *                  [
     *                      'file' : 'file content'
     *                  ]
     *              ........
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Number}  current_page   Number of the page.
     * @apiSuccess (201) {Array[]}  data   Page data of comments.
     * @apiSuccess (201) {Array[]}  data.array   Data array of comment.
     * @apiSuccess (201) {Number}  data.array.id   Comment id.
     * @apiSuccess (201) {Number}  data.array.event_id   Event id.
     * @apiSuccess (201) {Number}  data.array.user_id   User id.
     * @apiSuccess (201) {Number}  data.array.content   Content of the comment.
     * @apiSuccess (201) {Number}  data.array.created_at   Content create date.
     * @apiSuccess (201) {Number}  data.array.like_count   Count of the comments likes.
     * @apiSuccess (201) {Array[]}  data.array.like   Array of the likes data.
     * @apiSuccess (201) {Array[]}  data.array.like.array   Array of the like data.
     * @apiSuccess (201) {Number}  data.array.like.array.id   Id of the like.
     * @apiSuccess (201) {Number}  data.array.like.array.user_id   User Id of the like.
     * @apiSuccess (201) {Number}  data.array.like.array.event_comment_id   Comment Id of the like.
     * @apiSuccess (201) {Array[]}  data.array.like.array.user   User data of the like.
     * @apiSuccess (201) {Number}  data.array.like.array.user.id   User id of the like.
     * @apiSuccess (201) {String}  data.array.like.array.user.name   User name of the like.
     * @apiSuccess (201) {Bool}  data.array.like.array.user.is_admin   User is_admin of the like.
     * @apiSuccess (201) {Array[]}  data.array.user   User data of the comment.
     * @apiSuccess (201) {Number}  data.array.user.id   User id of the comment.
     * @apiSuccess (201) {String}  data.array.user.name   User name of the comment.
     * @apiSuccess (201) {Bool}  data.array.user.is_admin   User is_admin of the comment.
     * @apiSuccess (201) {Array[]}  data.array.user.image   Array of data images.
     * @apiSuccess (201) {Array[]}  data.array.user.image.array   Data array of the image.
     * @apiSuccess (201) {Number}  data.array.user.image.array.id   Id of the image.
     * @apiSuccess (201) {Number}  data.array.user.image.array.event_comment_id   Comment id of the image.
     * @apiSuccess (201) {Number}  data.array.user.image.array.image_id   File id of the image.
     * @apiSuccess (201) {Array[]}  data.array.user.image.array.image   Data array of the image file.
     * @apiSuccess (201) {Number}  data.array.user.image.array.image.id   Id of the image file.
     * @apiSuccess (201) {String}  data.array.user.image.array.image.url   Url of the image file.
     * @apiSuccess (201) {String}  first_page_url   Url of the first page.
     * @apiSuccess (201) {String}  last_page_url   Url of the last page.
     * @apiSuccess (201) {String}  next_page_url   Url of the next page.
     * @apiSuccess (201) {String}  path   Base URL of request.
     * @apiSuccess (201) {Number}  from   Number of start element.
     * @apiSuccess (201) {Number}  to   Number of end element.
     * @apiSuccess (201) {Number}  total   Count of all elements.
     * @apiSuccess (201) {Number}  per_page   Elements on page.
     * @apiSuccess (201) {Number}  last_page   Number of the last page.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *   {
     *      "current_page":1,
     *      "data":
     *          [
     *              {
     *                  "id":12,
     *                  "event_id":7,
     *                  "user_id":6,
     *                  "content":"Test Comment",
     *                  "created_at":"2018-05-21 07:58:07",
     *                  "like_count":0,
     *                  "like":[],
     *                  "user":
     *                      {
     *                          "id":6,
     *                          "name":"Test_user",
     *                          "is_admin":0
     *                      },
     *                  "image":[]
     *              },
     *              {
     *                  "id":11,
     *                  "event_id":7,
     *                  "user_id":6,
     *                  "content":"Test Comment",
     *                  "created_at":"2018-05-21 07:55:54",
     *                  "like_count":3,
     *                  "like":
     *                      [
     *                          {
     *                              "id":1,
     *                              "user_id":6,
     *                              "event_comment_id":11,
     *                              "user":
     *                                  {
     *                                      "id":6,
     *                                      "name":"Test_user",
     *                                      "is_admin":0,
     *                                  }
     *                          },
     *                          {
     *                              "id":2,
     *                              "user_id":1,
     *                              "event_comment_id":11,
     *                              "user":
     *                                  {
     *                                      "id":1,
     *                                      "name":"Test User",
     *                                      "is_admin":0,
     *                                  }
     *                          },
     *                          ......
     *                      ],
     *                  "user":
     *                      {
     *                          "id":6,
     *                          "name":"Test_user",
     *                          "is_admin":0
     *                      },
     *                  "image":
     *                      [
     *                          {
     *                              "id":1,
     *                              "event_comment_id":11,
     *                              "image_id":2,
     *                              "image":
     *                                  {
     *                                      "id":2,
     *                                      "url":"storage\/images\/YuJPIpEwRN19wQoPctCVDVs1CkZ1mLFuHeDc7tfp.jpeg",
     *                                  }
     *                          }
     *                      ]
     *              },
     *              ........
     *          ],
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/7\/comment?page=1",
     *      "from":1,
     *      "last_page":2,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/7\/comment?page=2",
     *      "next_page_url":"http:\/\/charity.test\/api\/event\/7\/comment?page=2",
     *      "path":"http:\/\/charity.test\/api\/event\/7\/comment",
     *      "per_page":10,
     *      "prev_page_url":null,
     *      "to":10,
     *      "total":12
     *   }
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
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *          Event not found
     */
    public function create(Request $request, $event_id){
        if ($event = Event::find($event_id)){
            $validator = $this->isValid($request);

            $comment = new EventComment();
            $comment->content = $request->get('content');
            $comment->user_id = Auth::id();
            $comment->event_id = $event_id;
            $comment->save();

            if (!$validator->fails()){
                if ($request->has('images')){
                    MediaHelper::createImage($request, EventCommentImage::class,$comment,'comment');
                }

                return $this->store($event_id);
            }

            return response(json_encode($validator->errors()), 400);
        }

        return response('Event Not found', 404);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function isValid(Request $request){
        $validate_param = [
            'content' => 'required|min:3|max:1000',
            'images' => 'array|max:10',
            'images.*.file' => 'file|image|max:1025'
        ];

        return Validator::make($request->all(), $validate_param);
    }

    /**
     * @api {post} /api/event/:event_id/comment Gel comments of event
     * @apiName Shove comments
     * @apiGroup Event Comment
     * @apiParam {Number} :event_id Id of event for commenting
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Number}  current_page   Number of the page.
     * @apiSuccess (201) {Array[]}  data   Page data of comments.
     * @apiSuccess (201) {Array[]}  data.array   Data array of comment.
     * @apiSuccess (201) {Number}  data.array.id   Comment id.
     * @apiSuccess (201) {Number}  data.array.event_id   Event id.
     * @apiSuccess (201) {Number}  data.array.user_id   User id.
     * @apiSuccess (201) {Number}  data.array.content   Content of the comment.
     * @apiSuccess (201) {Number}  data.array.created_at   Content create date.
     * @apiSuccess (201) {Number}  data.array.like_count   Count of the comments likes.
     * @apiSuccess (201) {Array[]}  data.array.like   Array of the likes data.
     * @apiSuccess (201) {Array[]}  data.array.like.array   Array of the like data.
     * @apiSuccess (201) {Number}  data.array.like.array.id   Id of the like.
     * @apiSuccess (201) {Number}  data.array.like.array.user_id   User Id of the like.
     * @apiSuccess (201) {Number}  data.array.like.array.event_comment_id   Comment Id of the like.
     * @apiSuccess (201) {Array[]}  data.array.like.array.user   User data of the like.
     * @apiSuccess (201) {Number}  data.array.like.array.user.id   User id of the like.
     * @apiSuccess (201) {String}  data.array.like.array.user.name   User name of the like.
     * @apiSuccess (201) {Bool}  data.array.like.array.user.is_admin   User is_admin of the like.
     * @apiSuccess (201) {Array[]}  data.array.user   User data of the comment.
     * @apiSuccess (201) {Number}  data.array.user.id   User id of the comment.
     * @apiSuccess (201) {String}  data.array.user.name   User name of the comment.
     * @apiSuccess (201) {Bool}  data.array.user.is_admin   User is_admin of the comment.
     * @apiSuccess (201) {Array[]}  data.array.user.image   Array of data images.
     * @apiSuccess (201) {Array[]}  data.array.user.image.array   Data array of the image.
     * @apiSuccess (201) {Number}  data.array.user.image.array.id   Id of the image.
     * @apiSuccess (201) {Number}  data.array.user.image.array.event_comment_id   Comment id of the image.
     * @apiSuccess (201) {Number}  data.array.user.image.array.image_id   File id of the image.
     * @apiSuccess (201) {Array[]}  data.array.user.image.array.image   Data array of the image file.
     * @apiSuccess (201) {Number}  data.array.user.image.array.image.id   Id of the image file.
     * @apiSuccess (201) {String}  data.array.user.image.array.image.url   Url of the image file.
     * @apiSuccess (201) {String}  first_page_url   Url of the first page.
     * @apiSuccess (201) {String}  last_page_url   Url of the last page.
     * @apiSuccess (201) {String}  next_page_url   Url of the next page.
     * @apiSuccess (201) {String}  path   Base URL of request.
     * @apiSuccess (201) {Number}  from   Number of start element.
     * @apiSuccess (201) {Number}  to   Number of end element.
     * @apiSuccess (201) {Number}  total   Count of all elements.
     * @apiSuccess (201) {Number}  per_page   Elements on page.
     * @apiSuccess (201) {Number}  last_page   Number of the last page.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *   {
     *      "current_page":1,
     *      "data":
     *          [
     *              {
     *                  "id":12,
     *                  "event_id":7,
     *                  "user_id":6,
     *                  "content":"Test Comment",
     *                  "created_at":"2018-05-21 07:58:07",
     *                  "like_count":0,
     *                  "like":[],
     *                  "user":
     *                      {
     *                          "id":6,
     *                          "name":"Test_user",
     *                          "is_admin":0
     *                      },
     *                  "image":[]
     *              },
     *              {
     *                  "id":11,
     *                  "event_id":7,
     *                  "user_id":6,
     *                  "content":"Test Comment",
     *                  "created_at":"2018-05-21 07:55:54",
     *                  "like_count":3,
     *                  "like":
     *                      [
     *                          {
     *                              "id":1,
     *                              "user_id":6,
     *                              "event_comment_id":11,
     *                              "user":
     *                                  {
     *                                      "id":6,
     *                                      "name":"Test_user",
     *                                      "is_admin":0,
     *                                  }
     *                          },
     *                          {
     *                              "id":2,
     *                              "user_id":1,
     *                              "event_comment_id":11,
     *                              "user":
     *                                  {
     *                                      "id":1,
     *                                      "name":"Test User",
     *                                      "is_admin":0,
     *                                  }
     *                          },
     *                          ......
     *                      ],
     *                  "user":
     *                      {
     *                          "id":6,
     *                          "name":"Test_user",
     *                          "is_admin":0
     *                      },
     *                  "image":
     *                      [
     *                          {
     *                              "id":1,
     *                              "event_comment_id":11,
     *                              "image_id":2,
     *                              "image":
     *                                  {
     *                                      "id":2,
     *                                      "url":"storage\/images\/YuJPIpEwRN19wQoPctCVDVs1CkZ1mLFuHeDc7tfp.jpeg",
     *                                  }
     *                          }
     *                      ]
     *              },
     *              ........
     *          ],
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/7\/comment?page=1",
     *      "from":1,
     *      "last_page":2,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/7\/comment?page=2",
     *      "next_page_url":"http:\/\/charity.test\/api\/event\/7\/comment?page=2",
     *      "path":"http:\/\/charity.test\/api\/event\/7\/comment",
     *      "per_page":10,
     *      "prev_page_url":null,
     *      "to":10,
     *      "total":12
     *   }
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
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *          Event not found
     */
    public function store($event_id){
        if ($event = Event::find($event_id)){
            return $event->comments()->with('like.user', 'user', 'image.image')
                ->withCount('like')->orderBy('created_at', 'desc')->paginate(10);
        }

        return response('Event Not found', 404);
    }

    /**
     * @api {get} /api/event/:event_id/comment/:comment_id Gel comment data of event from id
     * @apiName Get comment
     * @apiGroup Event Comment
     * @apiParam {Number} :event_id Id of event for commenting
     * @apiParam {Number} :comment_id Id of comment
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Array[]}  array   Data array of comment.
     * @apiSuccess (201) {Number}  array.id   Comment id.
     * @apiSuccess (201) {Number}  array.event_id   Event id.
     * @apiSuccess (201) {Number}  array.user_id   User id.
     * @apiSuccess (201) {Number}  array.content   Content of the comment.
     * @apiSuccess (201) {Number}  array.created_at   Content create date.
     * @apiSuccess (201) {Number}  array.like_count   Count of the comments likes.
     * @apiSuccess (201) {Array[]}  array.like   Array of the likes data.
     * @apiSuccess (201) {Array[]}  array.like.array   Array of the like data.
     * @apiSuccess (201) {Number}  array.like.array.id   Id of the like.
     * @apiSuccess (201) {Number}  array.like.array.user_id   User Id of the like.
     * @apiSuccess (201) {Number}  array.like.array.event_comment_id   Comment Id of the like.
     * @apiSuccess (201) {Array[]}  array.like.array.user   User data of the like.
     * @apiSuccess (201) {Number}  array.like.array.user.id   User id of the like.
     * @apiSuccess (201) {String}  array.like.array.user.name   User name of the like.
     * @apiSuccess (201) {Bool}  array.like.array.user.is_admin   User is_admin of the like.
     * @apiSuccess (201) {Array[]}  array.user   User data of the comment.
     * @apiSuccess (201) {Number}  array.user.id   User id of the comment.
     * @apiSuccess (201) {String}  array.user.name   User name of the comment.
     * @apiSuccess (201) {Bool}  array.user.is_admin   User is_admin of the comment.
     * @apiSuccess (201) {Array[]}  array.user.image   Array of data images.
     * @apiSuccess (201) {Array[]}  array.user.image.array   Data array of the image.
     * @apiSuccess (201) {Number}  array.user.image.array.id   Id of the image.
     * @apiSuccess (201) {Number}  array.user.image.array.event_comment_id   Comment id of the image.
     * @apiSuccess (201) {Number}  array.user.image.array.image_id   File id of the image.
     * @apiSuccess (201) {Array[]}  array.user.image.array.image   Data array of the image file.
     * @apiSuccess (201) {Number}  array.user.image.array.image.id   Id of the image file.
     * @apiSuccess (201) {String}  array.user.image.array.image.url   Url of the image file.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *   [
     *       "id":11,
     *       "event_id":7,
     *       "user_id":6,
     *       "content":"Test Comment",
     *       "created_at":"2018-05-21 07:55:54",
     *       "like_count":3,
     *       "like":
     *           [
     *               {
     *                   "id":1,
     *                   "user_id":6,
     *                   "event_comment_id":11,
     *                   "user":
     *                       {
     *                           "id":6,
     *                           "name":"Test_user",
     *                           "is_admin":0,
     *                       }
     *               },
     *               {
     *                   "id":2,
     *                   "user_id":1,
     *                   "event_comment_id":11,
     *                   "user":
     *                       {
     *                           "id":1,
     *                           "name":"Test User",
     *                           "is_admin":0,
     *                       }
     *               },
     *               ......
     *           ],
     *       "user":
     *           {
     *               "id":6,
     *               "name":"Test_user",
     *               "is_admin":0
     *           },
     *       "image":
     *           [
     *               {
     *                   "id":1,
     *                   "event_comment_id":11,
     *                   "image_id":2,
     *                   "image":
     *                       {
     *                           "id":2,
     *                           "url":"storage\/images\/YuJPIpEwRN19wQoPctCVDVs1CkZ1mLFuHeDc7tfp.jpeg",
     *                       }
     *               }
     *           ]
     *   ]
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *          Event not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *          Comment not found
     */
    public function show($event_id, $comment_id){
        if ($event = Event::find($event_id)){
            if ($event->comments()->where('id', $comment_id)->get()){
                return $event->comments()->where('id', $comment_id)
                    ->with('like.user', 'user', 'image.image')
                    ->withCount('like')->get();
            }

            return response('Comment Not found', 404);
        }

        return response('Event Not found', 404);
    }

    /**
     * @api {delete} /api/event/:event_id/comment/:comment_id Remove comment data of event from id
     * @apiName Remove comment
     * @apiGroup Event Comment
     * @apiPermission User
     * @apiParam {Number} :event_id Id of event for commenting
     * @apiParam {Number} :comment_id Id of comment
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of remove comment.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *   [
     *      'status'=>true
     *   ]
     * @apiError (403) mess Access is denied. If the object does not belong to the user
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *          Forbidden
     * @apiError (404) mess Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *          Event not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *          Comment not found
     */
    public function destroy($event_id, $comment_id){
        if ($event = Event::find($event_id)){
            if ($comment = $event->comments()->where('id', $comment_id)->get()){
                if ($event->user_id == Auth::id() || Auth::user()->is_admin){
                    $comment->like()->delete();
                    $comment->delete();

                    return response( json_encode(['status'=>true]),200);
                }

                return response('Forbidden', 403);
            }

            return response('Comment Not found', 404);
        }

        return response('Event Not found', 404);
    }

    /**
     * @api {post} /event/user/:user_id/comments Gel comments of user
     * @apiName Users comments
     * @apiGroup Event Comment
     * @apiPermission User
     * @apiParam {Number} :user_id Id of user
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  current_page   Number of the page.
     * @apiSuccess (200) {Array[]}  data   Page data of comments.
     * @apiSuccess (200) {Array[]}  data.array   Data array of comment.
     * @apiSuccess (200) {Number}  data.array.id   Comment id.
     * @apiSuccess (200) {Number}  data.array.event_id   Event id.
     * @apiSuccess (200) {Number}  data.array.user_id   User id.
     * @apiSuccess (200) {Number}  data.array.content   Content of the comment.
     * @apiSuccess (200) {Number}  data.array.created_at   Content create date.
     * @apiSuccess (200) {Number}  data.array.like_count   Count of the comments likes.
     * @apiSuccess (200) {Array[]}  data.array.like   Array of the likes data.
     * @apiSuccess (200) {Array[]}  data.array.like.array   Array of the like data.
     * @apiSuccess (200) {Number}  data.array.like.array.id   Id of the like.
     * @apiSuccess (200) {Number}  data.array.like.array.user_id   User Id of the like.
     * @apiSuccess (200) {Number}  data.array.like.array.event_comment_id   Comment Id of the like.
     * @apiSuccess (200) {Array[]}  data.array.like.array.user   User data of the like.
     * @apiSuccess (200) {Number}  data.array.like.array.user.id   User id of the like.
     * @apiSuccess (200) {String}  data.array.like.array.user.name   User name of the like.
     * @apiSuccess (200) {Bool}  data.array.like.array.user.is_admin   User is_admin of the like.
     * @apiSuccess (200) {Array[]}  data.array.user   User data of the comment.
     * @apiSuccess (200) {Number}  data.array.user.id   User id of the comment.
     * @apiSuccess (200) {String}  data.array.user.name   User name of the comment.
     * @apiSuccess (200) {Bool}  data.array.user.is_admin   User is_admin of the comment.
     * @apiSuccess (200) {Array[]}  data.array.user.image   Array of data images.
     * @apiSuccess (200) {Array[]}  data.array.user.image.array   Data array of the image.
     * @apiSuccess (200) {Number}  data.array.user.image.array.id   Id of the image.
     * @apiSuccess (200) {Number}  data.array.user.image.array.event_comment_id   Comment id of the image.
     * @apiSuccess (200) {Number}  data.array.user.image.array.image_id   File id of the image.
     * @apiSuccess (200) {Array[]}  data.array.user.image.array.image   Data array of the image file.
     * @apiSuccess (200) {Number}  data.array.user.image.array.image.id   Id of the image file.
     * @apiSuccess (200) {String}  data.array.user.image.array.image.url   Url of the image file.
     * @apiSuccess (200) {Array[]}  data.array.event   Data array of the event.
     * @apiSuccess (200) {Number}  data.array.event.id   Event id
     * @apiSuccess (200) {String}  data.array.event.title   Event title
     * @apiSuccess (200) {String}  data.array.event.story   Event story
     * @apiSuccess (200) {String}  data.array.event.short_story   Event short story
     * @apiSuccess (200) {String}  data.array.event.address   Event address
     * @apiSuccess (200) {Bool}  data.array.event.is_approved   Event approved status(true - is approved)
     * @apiSuccess (200) {Bool}  data.array.event.is_submit   Event submit status(true - is submit)
     * @apiSuccess (200) {Array[]}  data.array.event.user   Event Author data
     * @apiSuccess (200) {Number}  data.array.event.user.id   Author user id
     * @apiSuccess (200) {String}  data.array.event.user.name  Author name
     * @apiSuccess (200) {Boot}  data.array.event.user.is_admin    Author it is user-admin(true - is admin)
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
     * @apiSuccess (200) {Number}  data.array.event.demand.demand_type.id  Id of demand type
     * @apiSuccess (200) {String}  data.array.event.demand.demand_type.title  Name of demand type
     * @apiSuccess (200) {Array[]}  data.array.event.preview  Data Preview of event
     * @apiSuccess (200) {Number}  data.array.event.preview.id   Event image id
     * @apiSuccess (200) {Number}  data.array.event.preview.image_id   Event image file id
     * @apiSuccess (200) {Number}  data.array.event.preview.is_preview   Preview status of image
     * @apiSuccess (200) {Array[]}  data.array.event.preview.image  Array Event image file data
     * @apiSuccess (200) {Number}  data.array.event.preview.image.id   Event image file id
     * @apiSuccess (200) {String}  data.array.event.preview.image.title   Event image file title
     * @apiSuccess (200) {String}  data.array.event.preview.image.url   Event image file url
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
     *                  "id":11,
     *                  "event_id":7,
     *                  "user_id":6,
     *                  "content":"Test Comment",
     *                  "created_at":"2018-05-21 07:55:54",
     *                  "like_count":3,
     *                  "like":
     *                      [
     *                          {
     *                              "id":1,
     *                              "user_id":6,
     *                              "event_comment_id":11,
     *                              "user":
     *                                  {
     *                                      "id":6,
     *                                      "name":"Test_user",
     *                                      "is_admin":0,
     *                                  }
     *                          },
     *                          {
     *                              "id":2,
     *                              "user_id":1,
     *                              "event_comment_id":11,
     *                              "user":
     *                                  {
     *                                      "id":1,
     *                                      "name":"Test User",
     *                                      "is_admin":0,
     *                                  }
     *                          },
     *                          ......
     *                      ],
     *                  "user":
     *                      {
     *                          "id":6,
     *                          "name":"Test_user",
     *                          "is_admin":0
     *                      },
     *                  "image":
     *                      [
     *                          {
     *                              "id":1,
     *                              "event_comment_id":11,
     *                              "image_id":2,
     *                              "image":
     *                                  {
     *                                      "id":2,
     *                                      "url":"storage\/images\/YuJPIpEwRN19wQoPctCVDVs1CkZ1mLFuHeDc7tfp.jpeg",
     *                                  }
     *                          },
     *                          ........
     *                      ],
     *                  "event":
     *                      {
     *                          "id": 7,
     *                          "title": "testing",
     *                          "story": "testing story",
     *                          "short_story": "short_story",
     *                          "address": "lalalala",
     *                          "type_destination_id": 10,
     *                          "purpose_id": 18,
     *                          "religion_id": 11,
     *                          "country_id": 1,
     *                          "state_id": 1,
     *                          "city_id": 1,
     *                          "user_id": 6,
     *                          "event_status_id": 0,
     *                          "is_approved": 0,
     *                          "is_submit": 0,
     *                          "created_at": "2018-05-21 06:55:34",
     *                          "updated_at": "2018-05-21 06:55:34",
     *                          "preview":
     *                              [
     *                                  "id":1,
     *                                  image_id":1,
     *                                  image":
     *                                     {
     *                                         "id":1,
     *                                         "title":"test",
     *                                         "url":"storage\/images\/Ur4qM78zhHholK9Y5ylcxoUYlwdx29efD9bBCNG2.jpeg"
     *                                     }
     *                              ]
     *                          "user":
     *                              {
     *                                  "id": 6,
     *                                  "name": "Test_user",
     *                                  "is_admin": 0
     *                              },
     *                          "event_status": null,
     *                          "purpose":
     *                              {
     *                                  "id": 18,
     *                                  "title": "Children & Education"
     *                              },
     *                          "religion":
     *                              {
     *                                  "id": 11,
     *                                  "title": "Christianity"
     *                              },
     *                          "type_destination":
     *                              {
     *                                  "id": 10,
     *                                  "title": "Charity"
     *                              },
     *                          "demand": [],
     *                          "country":
     *                              {
     *                                  "id": 1,
     *                                  "sortname": "AF",
     *                                  "name": "Afghanistan"
     *                              },
     *                          "state":
     *                              {
     *                                  "id": 1,
     *                                  "name": "Andaman and Nicobar Islands"
     *                              },
     *                          "city":
     *                              {
     *                                  "id": 1,
     *                                  "name": "Bombuflat"
     *                              }
     *                      }
     *              },
     *              ........
     *          ],
     *      "first_page_url":"http:\/\/charity.test\/api\/event\/7\/comment?page=1",
     *      "from":1,
     *      "last_page":2,
     *      "last_page_url":"http:\/\/charity.test\/api\/event\/7\/comment?page=2",
     *      "next_page_url":"http:\/\/charity.test\/api\/event\/7\/comment?page=2",
     *      "path":"http:\/\/charity.test\/api\/event\/7\/comment",
     *      "per_page":10,
     *      "prev_page_url":null,
     *      "to":10,
     *      "total":12
     *   }
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *          User not found
     */
    public function userComments($user_id){
        if (User::where('id', $user_id)->count()){
            return EventComment::where('user_id', $user_id)
                ->with('like.user', 'user', 'image.image', 'event', 'event.preview.image', 'event.user',
                    'event.event_status', 'event.purpose', 'event.religion', 'event.typeDestination',
                    'event.demand.demandType', 'event.country', 'event.state', 'event.city')
                ->withCount('like')->orderBy('created_at', 'desc')->paginate(10);
        }
        return response('User Not found', 404);
    }
}
