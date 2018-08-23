<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    /**
     * @var string
     */
    protected $table = 'f_a_qs';

    /**
     * @var array
     */
    protected $fillable = ['title', 'content', 'active'];

}
