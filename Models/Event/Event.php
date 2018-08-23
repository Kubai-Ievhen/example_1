<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * @var string
     */
    protected $table = 'events';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(){
        return $this->hasMany('App\Models\Event\EventComment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(){
        return $this->hasMany('App\Models\Event\EventImage');
    }

    /**
     * @return mixed
     */
    public function event_image_preview(){
        return $this->hasOne('App\Models\Event\EventImage')->where('is_preview', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function videos(){
        return $this->hasMany('App\Models\Event\EventVideo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event_status(){
        return $this->belongsTo('App\Models\Event\EventStatus');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purpose(){
        return $this->belongsTo('App\Models\Base\Purpose');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function religion(){
        return $this->belongsTo('App\Models\Base\Religion');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typeDestination(){
        return $this->belongsTo('App\Models\Event\TypeDestination');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chat(){
        return $this->hasMany('App\Models\Event\ChatMessage');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function demand(){
        return $this->hasMany('App\Models\Event\EventDemand');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(){
        return $this->belongsTo('App\Models\Geodata\Country');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state(){
        return $this->belongsTo('App\Models\Geodata\State');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city(){
        return $this->belongsTo('App\Models\Geodata\City');
    }

    public function event_views(){
        return $this->hasMany('App\Models\Event\EventView');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function event_update(){
        return $this->hasMany('App\Models\Event\EventUpdate');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stripe(){
        return $this->hasOne('App\Models\Event\StripEventManyDataConnect');
    }
}
