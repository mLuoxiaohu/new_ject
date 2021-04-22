<?php

namespace App\Http\Model;
use App\Http\Controllers\BaseController;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    use DefaultDatetimeFormat;
    use Notifiable;
//    protected $dateFormat = 'U';  #是否为时间戳
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nickname','mobile','sex','password','avatar','state','create_time',
        'update_time','signature','login_ip','login_time','coin'
    ];


    public $states=[
        1=>'正常',2=>'禁用'
    ];
    public $man=[
        1=>'未知',2=>'男',3=>'女'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password','update_time','login_fail'
    ];


    public function getSexAttribute(){
        return $this->man[$this->attributes['sex']];
    }

    public function getAvatarAttribute(){
        return BaseController::isUrlHeader($this->attributes['avatar']) ? $this->attributes['avatar'] :  (BaseController::is_https() ? 'https://':'http://').($_SERVER["HTTP_HOST"] ?? $_SERVER['SERVER_ADDR'] ).'/user/'. $this->attributes['avatar'];
    }



    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }




}
