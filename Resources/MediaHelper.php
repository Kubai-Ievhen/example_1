<?php
/**
 * Created by PhpStorm.
 * User: yevhen
 * Date: 28.05.18
 * Time: 15:28
 */

namespace App\Resources;


use App\Models\Base\Image;
use App\Models\Base\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Post\PostVideo;
use App\Models\Post\PostImage;
use App\Models\Event\EventVideo;
use App\Models\Event\EventImage;
use App\Models\Event\EventCommentImage;
use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    /**
     * @param Request $request
     * @param $video_class
     * @param Model $object
     * @param String $object_name
     */
    public static function createVideo(Request $request, $video_class, Model $object,String $object_name){
        $data = $request->get('videos');
        $videos = [];

        foreach ($data as $video){
            $videos[] = [
                'video_id' => Video::insertGetId($video),
                $object_name.'_id' => $object->id,
            ];
        }

        $video_class::insert($videos);
    }

    /**
     * @param Request $request
     * @param $video_class
     * @param Model $object
     * @param String $object_name
     */
    public static function createImage(Request $request, $video_class, Model $object, String $object_name){
        $images = $request->file('images');
        $images_data = [];
        $new_preview = 0;
        foreach ($request->get('images') as $key=>$image) {
            $url = $images[$key]['file']->store('public/images');
            $url = str_replace('public', 'storage', $url);

            $images_data[] = [
                'image_id' => Image::insertGetId(['url' => $url, 'title' => $image['title']]),
                $object_name.'_id' => $object->id,
                'is_preview' => $image['is_preview'],
            ];
            $new_preview += $image['is_preview'];
        }

        if ($new_preview > 0){
            $video_class::where($object_name.'_id', $object->id)->update(['is_preview' => 0]);
        }

        $video_class ::insert($images_data);
    }

    /**
     * @param Request $request
     * @param $title
     * @return mixed
     */
    public static function createImageUno(Request $request, $title){
        $url = $request->file('image')->store('public/images');
        $url = str_replace('public', 'storage', $url);

        return Image::insertGetId(['url' => $url, 'title' => $title]);
    }

    public static function removeImageUno($image_id){
        $image = Image::fing($image_id);
        $url = str_replace('storage','public',  $image->url);
        Storage::delete($url);

        Image::where('id',$image_id)->delete();

    }
}
