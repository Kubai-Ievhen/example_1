<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventVideo extends Model
{
    protected $fillable = ['event_id', 'video_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event(){
        return $this->belongsTo('App\Models\Event\Event');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function video(){
        return $this->belongsTo('App\Models\Base\Video');
    }
}
