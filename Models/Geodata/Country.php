<?php

namespace App\Models\Geodata;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states(){
        return $this->hasMany('App\Models\Geodata\State');
    }
}
