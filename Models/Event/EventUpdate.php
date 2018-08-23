<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class EventUpdate extends Model
{
    protected $fillable = ['event_id', 'title', 'content', 'demand_type_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event(){
        return $this->belongsTo('App\Models\Event\Event');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function demand_type(){
        return $this->belongsTo('App\Models\Event\DemandType');
    }
}
