<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class StripEventPayment extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stripe(){
        return $this->belongsTo('App\Models\Event\StripEventManyDataConnect');
    }
}
