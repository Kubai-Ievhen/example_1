<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventCommentImage extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comment(){
        return $this->belongsTo('App\Models\Event\EventComment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function image(){
        return $this->belongsTo('App\Models\Base\Image');
    }
}
