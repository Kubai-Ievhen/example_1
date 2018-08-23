<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventStatus extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function event(){
        return $this->hasMany('App\Models\Event\Event');
    }
}
