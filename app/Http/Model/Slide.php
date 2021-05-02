<?php


namespace App\Http\Model;


use App\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    protected $fillable=['cover','url','state'];
    protected $hidden=['update_time'];

    public function getCoverAttribute(){
        return BaseController::isUrlHeader($this->attributes['cover']) ? $this->attributes['cover'] :  (BaseController::is_https() ? 'https://':'http://').($_SERVER["HTTP_HOST"] ?? $_SERVER['SERVER_ADDR'] ).'/user/'. $this->attributes['cover'];
    }
}
