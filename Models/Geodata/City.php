<?php

namespace App\Models\Geodata;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state(){
        return $this->belongsTo('App\Models\Geodata\State');
    }
}
