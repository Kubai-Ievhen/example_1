<?php

namespace App\Http\Controllers\Event;

use App\Models\Event\EventComment;
use App\Models\Event\EventCommentLike;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentLikeController extends Controller
{
    /**
     * @api {get} /api/event/comment/:comment_id/like Like or Dislike comment
     * @apiName Like
     * @apiGroup Event Comment
     * @apiPermission User
     * @apiParam {Number} :comment_id Id of comment
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  count   Count of  likes.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *          10
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *              Comment Not Found
     */
    public function like($comment_id){
        if (EventComment::find($comment_id)){
            if ($like = EventCommentLike::where('event_comment_id', $comment_id)->where('user_id', Auth::id())->count()){
                EventCommentLike::where('event_comment_id', $comment_id)->where('user_id', Auth::id())->delete();
            }else{
                EventCommentLike::create(['event_comment_id' => $comment_id, 'user_id' => Auth::id()]);
            }

            return $this->count($comment_id);
        }

        return response('Comment Not found', 404);
    }

    /**
     * @api {delete} /api/like/:like_id/remove Dislike comment
     * @apiName Dislike
     * @apiGroup Event Comment
     * @apiPermission User
     * @apiParam {Number} :like_id Id of like
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Number}  status   Status of remove like.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *   [
     *      'status':true
     *   ]
     * @apiError (404) mess Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *              Like Not Found
     */
    public function remove($like_id){
        if (EventCommentLike::where('id', $like_id)->where('user_id', Auth::id())->count()){
            EventCommentLike::where('id', $like_id)->where('user_id', Auth::id())->delete();

            return response( json_encode(['status'=>true]),201);
        }

        return response('Like Not found', 404);
    }

    /**
     * @api {get} /api/event/comment/:comment_id/like/count Count comments likes
     * @apiName Like count
     * @apiGroup Event Comment
     * @apiParam {Number} :comment_id Id of comment
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  count   Count of  likes.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *          10
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *              Comment Not Found
     */
    public function count($comment_id){
        if (EventComment::find($comment_id)) {
            return EventCommentLike::where('event_comment_id', $comment_id)->count();
        }

        return response('Comment Not found', 404);
    }

    /**
     * @api {get} /api/event/comment/:comment_id/likes Likes comment
     * @apiName Likes comment
     * @apiGroup Event Comment
     * @apiParam {Number} :comment_id Id of comment
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Array[]}  likes   Likes data array.
     * @apiSuccess (200) {Array[]}  likes.like   Like data array.
     * @apiSuccess (200) {Number}  likes.like.id   Like id.
     * @apiSuccess (200) {Number}  likes.like.user_id   Like user id.
     * @apiSuccess (200) {Number}  likes.like.event_comment_id   Like to comment with id.
     * @apiSuccess (200) {Array[]}  likes.like.user   Likes user data array.
     * @apiSuccess (200) {Number}  likes.like.user.id   User id.
     * @apiSuccess (200) {String}  likes.like.user.name   User name.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *  [
     *      {
     *          "id":1,
     *          "user_id":1,
     *          "event_comment_id":1,
     *          "user":
     *              {
     *                  "id":1,
     *                  "name":"User Name",
     *              }
     *      }
     *  ]
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *              Comment Not Found
     */
    public function likes($comment_id){
        if (EventComment::find($comment_id)) {
            return EventCommentLike::where('event_comment_id', $comment_id)->with('user:id,name')
                ->orderBy('created_at', 'desc')->get();
        }

        return response('Comment Not found', 404);
    }

    /**
     * @api {post} /api//user/:user_id/likes Get Event data
     * @apiName Get Event
     * @apiGroup Event Comment
     * @apiParam {Number} :user_id Id of user
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Array[]}  data   Array Likes Data.
     * @apiSuccess (200) {Array[]}  data.array   Like Data array
     * @apiSuccess (200) {Number}  data.array.id   Like id
     * @apiSuccess (200) {Array[]}  data.array.event_comment   Comment data array
     * @apiSuccess (200) {Number}  data.array.event_comment.id   Comment id
     * @apiSuccess (200) {String}  data.array.event_comment.content   Comment content
     * @apiSuccess (200) {Date}  data.array.event_comment.created_at   Comment create date
     * @apiSuccess (200) {Array[]}  data.array.event_comment.event   Event data array of Comment
     * @apiSuccess (200) {Number}  data.array.event_comment.event.id   Event id
     * @apiSuccess (200) {String}  data.array.event_comment.event.title   Event title
     * @apiSuccess (200) {String}  data.array.event_comment.event.short_story   Event short_story
     * @apiSuccess (200) {Array[]}  data.array.event_comment.event.event_status   Event status data array
     * @apiSuccess (200) {Number}  data.array.event_comment.event.event_status.id   Event status id
     * @apiSuccess (200) {String}  data.array.event_comment.event.event_status.title   Event status title
     * @apiSuccess (200) {Array[]}  data.array.event_comment.user   User data array of Comment
     * @apiSuccess (200) {Number}  data.array.event_comment.user.id   User id
     * @apiSuccess (200) {String}  data.array.event_comment.user.name   User name
     *
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *  [
     *      {
     *          "id":5,
     *          "event_comment":
     *              {
     *                  "id":11,
     *                  "content":"Test Comment",
     *                  "created_at":"2018-05-21 07:55:54",
     *                  "event":
     *                      {
     *                          "id":7,
     *                          "title":"testing",
     *                          "short_story":"short_story",
     *                          "event_status":
     *                              {
     *                                  "id":12,
     *                                  "title":"Trending"
     *                              }
     *                      },
     *                  "user":
     *                      {
     *                          "id":6,
     *                          "name":"Test_user"
     *                      }
     *              }
     *      },
     *      .......
     *
     *  ]
     * @apiError (404) mess User Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *      User Not Found
     */
    public function userLikes($user_id){
        if ($user = User::find($user_id)){
            return $user->likes()->with('eventComment.event.event_status','eventComment.user')->get();
        }

        return response('User Not found', 404);

    }
}
