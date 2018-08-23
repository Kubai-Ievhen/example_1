<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventComment extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event(){
        return $this->belongsTo('App\Models\Event\Event');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function like(){
        return $this->hasMany('App\Models\Event\EventCommentLike');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function image(){
        return $this->hasMany('App\Models\Event\EventCommentImage');
    }
}
