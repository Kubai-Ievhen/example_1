<?php

namespace App\Models\Base;

use App\User;
use Illuminate\Database\Eloquent\Model;

class HashKey extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'key', 'name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\User');
    }

    /**
     * @param $key
     * @param $name
     * @return mixed
     */
    public static function getUser($key, $name){
        $key = self::where('key',$key)->where('name',$name)->first();
        return User::find($key->user_id);
    }

    /**
     * @param $user_id
     * @param $name
     * @return string
     */
    public static function keyGenerate($user_id,$name){
        $key = md5(time()*random_int(0,100));
        self::create(['user_id'=>$user_id, 'key'=>$key, 'name'=>$name]);
        return $key;
    }

    public static function destroy_key($key,$name,$user_id){
        self::where('key',$key)->where('name',$name)->where('user_id', $user_id)->delete();
    }
}
