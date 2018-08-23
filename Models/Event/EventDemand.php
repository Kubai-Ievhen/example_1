<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventDemand extends Model
{
    protected $fillable = ['demand_type_id', 'event_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event(){
        return $this->belongsTo('App\Models\Event\Event');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function demandType(){
        return $this->belongsTo('App\Models\Event\DemandType');
    }

    public function volunteers(){
        return $this->hasMany('App\Models\Event\EventVolunteer');
    }

    public function supplies(){
        return $this->hasMany('App\Models\Event\EventSupply');
    }

    public function money(){
        return $this->hasMany('App\Models\Event\EventMoney');
    }
}
