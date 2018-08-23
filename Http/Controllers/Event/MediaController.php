<?php

namespace App\Http\Controllers\Event;

use App\Models\Event\EventImage;
use App\Models\Event\EventVideo;
use App\Models\Base\Image;
use App\Resources\MediaHelper;
use App\Models\Base\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Event\Event;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Storage;



class MediaController extends Controller
{
    /**
     * @api {post} /api/event/:event_id/media/create Create video and image to event
     * @apiName Create media
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} :event_id Id of event
     * @apiParam {string} _token X-CSRF-TOKEN
     * @apiParam {Array[]} videos Array of array video data
     * @apiParam {Array[]} videos.array Array of video data
     * @apiParam {String} videos.array.url URL of video
     * @apiParam {String} videos.array.title Title of video
     * @apiParam {Array[]} images Array of array image data
     * @apiParam {Array[]} images.array Array of image data
     * @apiParam {File} images.array.file File of image
     * @apiParam {String} images.array.title Title of image
     * @apiParam {Bool} images.array.is_preview This image is preview of event
     * @apiParamExample {json} Request-Example:
     *     {
     *       "videos":
     *          [
     *              1 :
     *                  [
     *                      'title' : 'Same title',
     *                      'url' : 'Same URL'
     *                  ]
     *              2 :
     *                  [
     *                      'title' : 'Same title',
     *                      'url' : 'Same URL'
     *                  ]
     *              ........
     *          ]
     *        "images":
     *          [
     *              1 :
     *                  [
     *                      'title' : 'Same title',
     *                      'file' : 'file content'
     *                      "is_preview": 0
     *                  ]
     *              2 :
     *                  [
     *                      'title' : 'Same title',
     *                      'file' : 'file content'
     *                      "is_preview": 1
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
     *          "videos":
     *              [
     *                  "The videos field is required."
     *              ]
     *      }
     */
    public function create(Request $request, $id){
        if ($event = Event::find($id)){
            $validate = $this->isValid($request);

            if (!$validate->fails()){
                if ($request->has('videos')){
                    MediaHelper::createVideo($request, EventVideo::class,$event,'event');
                }

                if ($request->has('images')) {
                    MediaHelper::createImage($request, EventImage::class,$event,'event');
                }

                return response( json_encode(['status'=>true]),201);
            }

            return response(json_encode($validate->errors()), 400);
        }

        return response('Event not found', 404);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function isValid(Request $request){
        $validate_param = [
            'videos' => 'array',
            'videos.*.title' => 'string|min:3|max:255',
            'videos.*.url' => 'string|url',
            'images' => 'array',
            'images.*.title' => 'string|min:3|max:255',
            'images.*.file' => 'file|image|max:1025',
        ];

        return Validator::make($request->all(), $validate_param);
    }

    /**
     * @api {put} /api/event/media/:id/update_video Update video of event
     * @apiName Update Video
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} :id Id of video
     * @apiParam {String} title New title of video
     * @apiParamExample {json} Request-Example:
     *     {
     *       "title": "New title"
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of update title of video (true - ok).
     * @apiError (404) video Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Media not found
     * @apiError (400) message No value title
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *      Bad Request
     * @apiError (403) mess Access is denied. If the object does not belong to the user
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *          Forbidden
     */
    public function updateTitleVideo(Request $request, $id){
        return $this->updateTitleMedia($request, $id, EventVideo::class);
    }

    /**
     * @api {put} /api/event/media/:id/update_image Update image of event
     * @apiName Update Image
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} :id Id of image
     * @apiParam {String} title New title of image
     * @apiParamExample {json} Request-Example:
     *     {
     *       "title": "New title"
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of update title of image (true - ok).
     * @apiError (404) image Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Media not found
     * @apiError (400) message No value title
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *      Bad Request
     * @apiError (403) mess Access is denied. If the object does not belong to the user
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *          Forbidden
     */
    public function updateTitleImage(Request $request, $id){
        return $this->updateTitleMedia($request, $id, EventImage::class);
    }

    /**
     * @param Request $request
     * @param $id
     * @param $model
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    private function updateTitleMedia(Request $request, $id, $model){
        if ($request->has('title')){
            if ($media = $model::find($id)){
                $event = $media->event()->get();
                if ($event->user_id == Auth::id()){
                    $media->update(['title' => $request->get('title')]);

                    return response( json_encode(['status'=>true]),201);
                }

                return response('Forbidden', 403);
            }

            return response('Media not found', 404);
        }

        return response('Bad Request', 400);
    }

    /**
     * @api {delete} /api/event/media/:id/video Delete video of event
     * @apiName Delete video
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} :id Id of video
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of remove  video (true - ok).
     * @apiError (404) image Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Media not found
     */
    public function removeVideo($id){
        if (Video::find($id)->delete()){
            EventVideo::where('video_id', $id)->delete();

            return response( json_encode(['status'=>true]),201);
        }

        return response('Media not found', 404);
    }

    /**
     * @api {delete} /api/event/media/:id/image Delete image of event
     * @apiName Delete image
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} :id Id of image
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of remove image (true - ok).
     * @apiError (404) image Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Media not found
     */
    public function removeImage($id){
        if ($media = Image::find($id)){
            $url = str_replace('storage', 'public', $media->url);
            Storage::delete($url);
            $media->delete();
            EventImage::where('image_id', $id)->delete();

            return response( json_encode(['status'=>true]),201);
        }

        return response('Media not found', 404);
    }

    /**
     * @api {put} /api/event/:event_id/media/:image_id/preview Change preview of event
     * @apiName Change preview of event
     * @apiGroup Event
     * @apiPermission User
     * @apiParam {Number} :image_id Id of image for new preview
     * @apiParam {Number} :event_id Id of event
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of change preview of event (true - ok).
     * @apiError (404) video Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not found
     *      Image not foun
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not found
     *      Event not found
     * @apiError (403) mess Access is denied. If the object does not belong to the user
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 403 Forbidden
     *          Forbidden
     */
    public function changeEventPreview($event_id, $image_id){
        if($event = Event::find($event_id)){
            if ($image = EventImage::find($image_id)){
                if ($event->user_id == Auth::id()){
                    EventImage::where('event_id', $event->id)->update(['is_preview' => 0]);
                    EventImage::where('id', $image_id)->update(['is_preview' => 1]);

                    return response( json_encode(['status'=>true]),201);
                }

                return response('Forbidden', 403);
            }

            return response('Image not found', 404);
        }

        return response('Event not found', 404);
    }
}
