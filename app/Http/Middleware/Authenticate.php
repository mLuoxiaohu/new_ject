<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Contracts\Auth\Factory;
use Tymon\JWTAuth\Contracts\Providers\JWT;
class Authenticate
{
    private $auth;
    private $jwt;
    function __construct(Factory $factory,JWT $JWT)
    {
        $this->auth=$factory;
        $this->jwt=$JWT;
    }

    /**
     * 登录认证钩子
     * @param $request
     * @param Closure $next
     * @param null $site
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed
     */
    public function handle($request, Closure $next,$site=null)
    {
        try{
            $auth=$this->auth->guard($site); //切换认证站点
            if(!$auth->parser()->setRequest($request)->hasToken()) return response(['data'=>[],'message'=>'用户未登录或已过期','code'=>320]);
            $this->jwt->setSecret(config('jwt.'.$site.'_secret')); #切换认证密匙
            if ($auth->guest()) return response(['data'=>[],'message'=>'登录过期!','code'=>320]);
            return $next($request);
        }catch (\Exception $ex){
            return response(['data'=>[],'message'=>$ex->getMessage(),'code'=>400]);
        }

    }
}
