<?php


namespace App\Http\Model;


use App\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Model;

class Kind extends Model
{
    protected $table='kind';


    public $game_type=[3=>'高频彩',4=>'低频彩'];
    /**
     * 拼接图片地址
     * @return mixed|string
     */
    public function getIconAttribute(){
        return BaseController::isUrlHeader($this->attributes['icon']) ? $this->attributes['icon'] :  (BaseController::is_https() ? 'https://':'http://').($_SERVER["HTTP_HOST"] ?? $_SERVER['SERVER_ADDR'] ).'/user/'. $this->attributes['icon'];
    }
}
