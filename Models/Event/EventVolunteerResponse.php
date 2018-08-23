<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventVolunteerResponse extends Model
{
    protected $fillable = ['event_volunteer_id', 'user_id'];

    public function event_volunteer(){
        return $this->belongsTo('App\Models\Event\EventVolunteer');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }}
