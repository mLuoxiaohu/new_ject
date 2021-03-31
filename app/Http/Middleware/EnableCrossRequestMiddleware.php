<?php
namespace App\Http\Middleware;
use Closure;

/**
 * Class EnableCrossRequestMiddleware
 * 跨域请求
 * @package App\Http\Middleware
 */
class EnableCrossRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        #允许访问域名
//        $allow_origin = [
//            'http://192.151.194.52'
//        ];
//        if (in_array($origin, $allow_origin)) {
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With,User-Agent,Content-Type, Cookie, Accept, Authorization');
            $response->header('Access-Control-Expose-Headers', 'Authorization, X-Requested-With');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
            $response->header('Access-Control-Allow-Credentials', 'true');
//        }
        return $response;
    }
}
