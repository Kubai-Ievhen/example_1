<?php
/**
 * Created by PhpStorm.
 * User: yevhen
 * Date: 13.06.18
 * Time: 11:32
 */

namespace App\Resources;


use App\SystemParameters;

class SystemParametersSingleton
{
    private static $instance = null;
    public $data = null;

    private static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
    }

    public function __construct()
    {
        $this->data = SystemParameters::all();
    }

    public static function getParam($name){
        self::getInstance();
        return self::$instance->data->where('name',$name)->first()->value;
    }

    public static function updateParam($name,$new_value){
        self::getInstance();
        SystemParameters::where('name',$name)->update(['value'=> $new_value]);
        self::$instance->data = SystemParameters::all();
    }

    public static function updateParamId($id,$new_value){
        self::getInstance();
        SystemParameters::where('id',$id)->update(['value'=> $new_value]);
        self::$instance->data = SystemParameters::all();
    }

    public static function getAll(){
        self::getInstance();
        return self::$instance->data->all();
    }
}