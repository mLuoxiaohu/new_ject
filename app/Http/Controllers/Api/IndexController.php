<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Model\Config;
use App\Http\Model\Kind;
use App\Http\Model\News;
use App\Http\Model\NewsClass;
use App\Http\Model\Slide;
use App\Http\Model\User;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\Providers\JWT;

class IndexController extends BaseController
{

    private $user;
    private $auth;
    private $prefix = 'api';
    private $jwt;
    private $kind;

    public function __construct(User $user, Factory $auth, JWT $JWTAuth, Kind $kind)
    {
        $this->jwt = $JWTAuth;
        $this->user = $user;
        $this->auth = $auth;
        $this->kind = $kind;
    }

    protected function authInit()
    {
        return $this->auth->guard($this->prefix);
    }

    public function index()
    {
        try {
            $conf = array(
                'System' => php_uname('s'),
                'System_version' => php_uname('m'),
                'Time' => php_uname('v'),
                'LanguageVersion' => 'PHP:' . phpversion(),
                'Master' => 'WelCome-I-Like-You'
            );
            return $this->_success($conf, self::HELLOW);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }


    /**
     * @desc 首页游戏列表
     * @method GET
     * @route /game_list
     * @return \Illuminate\Http\JsonResponse
     */
    public function game_list()
    {
        try {
            $game = $this->kind->where(['index' => 1, 'none' => 0])
                ->orderBy('sort', 'asc')
                ->get(['name', 'icon', 'id', 'info']);
            return $this->_success($game);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 首页轮播图
     * @method GET
     * @route /carousel_list
     * @return \Illuminate\Http\JsonResponse
     */
    public function carousel(Slide $slide)
    {
        try {
            $carousel = $slide->where('state', 1)
                ->orderBy('id', 'desc')
                ->get();
            return $this->_success($carousel);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 首页新闻列表（只显示10条）
     * @method GET
     * @route /news_list
     * @return \Illuminate\Http\JsonResponse
     */
    public function newsList(News $news)
    {
        try {
            $news_list = $news->orderBy('time', 'desc')
                ->limit(10)
                ->get(['title', 'time', 'id','content']);
            return $this->_success($news_list);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 更多新闻
     * @method GET
     * @route /news_more
     * @param type_id 新闻类型id 可选
     * @param mum 每页多少条 default 10 可选
     * @param page 当前页 defaul 1 可选
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function newsMore(News $news, Request $request)
    {
        try {
            $type = $request->get('type_id', '');
            $userSql = $news->newQuery();
            if (!empty($type)) $userSql->where('nid', $type);
            $num = $request->get('num', 10);
            $result = $userSql->orderBy('time', 'desc')
                ->paginate($num, ['title', 'time', 'id', 'content', 'nid']);
            return $this->_success($result);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }

    }

    /**
     * @desc 新闻类型
     * @route /news_type
     * @method GET
     * @param NewsClass $newsClass
     * @return \Illuminate\Http\JsonResponse
     */
    public function newsType(NewsClass $newsClass)
    {
        try {
            $result = $newsClass->where('visible', 0)->get(['name', 'id']);
            return $this->_success($result);
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
    public function about()
    {
        try {
            $result = json_decode(Config::get_config('about'), true);
            if ($result) return $this->_success($result);
            return $this->_error();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    public function about_add(Config $config, Request $request)
    {
        try {
            $param = [
                "qq" => ['required', 'regex:/^[1-9]\d{4,10}$/'],
                "online" => ['required', 'regex:/(https?|http?|ftp?):\/\/?/i'],
            ];
            $message = [
                "qq.required" => "QQ不能为空",
                "qq.regex" => '请输入正确的QQ号',
                'online.required' => '网址不能为空',
                'online.regex' => '请输入正确的网址',
            ];
            if (!$this->BaseValidator($request, $param, $message, $error)) return $this->_error($error);
            $input = $this->getParams($request);
            $data = array(
                'key' => 'about',
                'value' => json_encode($input, JSON_UNESCAPED_UNICODE),
                'name' => '关于我们'
            );
            $result = $config->create($data);
            if ($result) return $this->_success();
            return $this->_error();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }

    }


}
