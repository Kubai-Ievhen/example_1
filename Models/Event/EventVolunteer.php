<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventVolunteer extends Model
{
    protected $fillable = ['event_demand_id', 'name', 'count', 'special_skills', 'description'];

    public function event_volunteer_response(){
        return $this->hasMany('App\Models\Event\EventVolunteerResponse');
    }

    public function event_demand(){
        return $this->belongsTo('App\Models\Event\EventDemand');
    }
}
