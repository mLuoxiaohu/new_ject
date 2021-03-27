<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table='config';


    /**
     * @param string $name
     */
    public static  function  get_config($name='article'){
       return self::where('keyname',$name)->value('val') == 1 ? true :false;
    }
}
