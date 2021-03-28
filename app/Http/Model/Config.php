<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

class Config extends Model
{


    /**
     * 获取配置文件
     * @param $name 配置文件
     * @return mixed
     */
    public static  function  get_config($name){
       return self::where('key',$name)->value('value');
    }
}
