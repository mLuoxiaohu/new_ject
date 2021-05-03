<?php


namespace App\Http\Model;


use App\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table='news';

    protected $fillable=['title','icon','content','time','nid'];

    /**
     * time
     * @return mixed|string
     */
    public function getTimeAttribute(){
        return date('Y-m-d H:i:s',$this->attributes['time']);
    }

    public function newclass(){
        return $this->hasOne(NewsClass::class,'id','nid');
    }

    public function getIconAttribute(){
        return   BaseController::isUrlHeader($this->attributes['icon']) ? $this->attributes['icon'] :  (BaseController::is_https() ? 'https://':'http://').($_SERVER["HTTP_HOST"] ?? $_SERVER['SERVER_ADDR'] ).'/user/'. $this->attributes['icon'];
    }

}
