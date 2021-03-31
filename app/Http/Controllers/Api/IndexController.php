<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Model\Config;
use App\Http\Model\User;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
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


    /**
     * @desc 关于我们
     * @method GET
     * @route api/about
     * @return \Illuminate\Http\JsonResponse
     */
    public function about(){
        try{
           $result=json_decode(Config::get_config('about'),true);

           if($result) return $this->_success($result);
           return  $this->_error();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    public function about_add(Config $config,Request $request){
        try{
            $param = [
                "qq" => ['required','regex:/^[1-9]\d{4,10}$/'],
                "online" => ['required','regex:/(https?|http?|ftp?):\/\/?/i'],
            ];
            $message = [
                "qq.required" => "QQ不能为空",
                "qq.regex" => '请输入正确的QQ号',
                'online.required' => '网址不能为空',
                'online.regex'=>'请输入正确的网址',
            ];
            if (!$this->BaseValidator($request, $param, $message, $error)) return $this->_error($error);
            $input = $this->getParams($request);
            $data=array(
                'key'=>'about',
                'value'=>json_encode($input,JSON_UNESCAPED_UNICODE),
                'name'=>'关于我们'
            );
            $result=$config->create($data);
            if($result) return $this->_success();
            return  $this->_error();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }

    }


}
