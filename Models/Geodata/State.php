<?php

namespace App\Models\Geodata;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(){
        return $this->belongsTo('App\Models\Geodata\Country');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities(){
        return $this->hasMany('App\Models\Geodata\City');
    }
}
