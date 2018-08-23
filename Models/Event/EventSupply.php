<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventSupply extends Model
{
    protected $fillable = ['event_demand_id', 'name', 'count'];

    public function event_supply_response(){
        return $this->hasMany('App\Models\Event\EventSupplyResponse');
    }

    public function event_demand(){
        $this->belongsTo('App\Models\Event\EventDemand');
    }
}
