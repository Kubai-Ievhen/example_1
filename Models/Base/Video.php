<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'url'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function event(){
        return $this->hasOne('App\Models\Event\EventVideo');
    }
}
