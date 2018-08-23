<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function event(){
        return $this->hasOne('App\Models\Event\EventImage');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function comment(){
        return $this->hasOne('App\Models\Event\EventCommentImage');
    }
}
