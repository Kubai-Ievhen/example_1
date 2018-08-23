<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Purpose extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function event(){
        return $this->hasMany('App\Models\Event\Event');
    }
}
