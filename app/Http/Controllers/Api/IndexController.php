<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Model\User;
use Illuminate\Contracts\Auth\Factory;
use Tymon\JWTAuth\Contracts\Providers\JWT;

class IndexController extends BaseController
{

    private $user;
    private $auth;
    private $prefix = 'api';
    private $jwt;

    public function __construct(User $user, Factory $auth, JWT $JWTAuth)
    {
        $this->jwt   = $JWTAuth;
        $this->user  = $user;
        $this->auth  = $auth;
    }

    protected function authInit()
    {
        return $this->auth->guard($this->prefix);
    }

    public function index()
    {
        try {
            $conf=array(
                'System'=>php_uname('s'),
                'System_version'=>php_uname('m'),
                'Time'=>php_uname('v'),
                'LanguageVersion'=>'PHP:'.phpversion(),
                'Master'=>'WelCome-I-Like-You'
            );
           return  $this->_success($conf,self::HELLOW);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }



}
