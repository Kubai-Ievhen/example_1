<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Model;

class PostVideo extends Model
{
    protected $fillable = ['post_id', 'video_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(){
        return $this->belongsTo('App\Models\Post\Post');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function video(){
        return $this->belongsTo('App\Models\Base\Video');
    }
}
