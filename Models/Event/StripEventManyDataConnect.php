<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class StripEventManyDataConnect extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event(){
        return $this->belongsTo('App\Models\Event\Event');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event_money(){
        return $this->belongsTo('App\Models\Event\EventMoney');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(){
        return $this->belongsTo('App\Models\Geodata\Country');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function event_payment(){
        return $this->hasOne('App\Models\Event\StripEventPayment');
    }


}
