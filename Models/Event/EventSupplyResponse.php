<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventSupplyResponse extends Model
{
    public function event_supply(){
        return $this->belongsTo('App\Models\Event\EventSupply');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }
}
