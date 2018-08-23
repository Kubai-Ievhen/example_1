<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventView extends Model
{
    protected $fillable = ['user_id', 'event_id'];

    protected $table='event_views';
}
