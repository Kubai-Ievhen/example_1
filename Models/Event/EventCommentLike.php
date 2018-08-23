<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventCommentLike extends Model
{
    protected $fillable = ['user_id', 'event_comment_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eventComment(){
        return $this->belongsTo('App\Models\Event\EventComment');
    }
}
