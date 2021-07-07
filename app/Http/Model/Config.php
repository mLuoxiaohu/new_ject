<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

class Config extends Model
{


    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','key','value'
    ];
    /**
     * 获取配置文件
     * @param $name 配置文件
     * @return mixed
     */
    public static  function  get_config($name){
        $value=self::where('key',$name)->value('value');
       return  $value ? json_decode($value,true) : false;
    }
}
