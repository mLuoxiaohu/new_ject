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
     * kid 彩种id
     * periods 期号
     */
    public function plan()
    {
        try {
            $kid=26; $periods=202122;
            $list = DB::table('cole')->whereIn('kid',[25,26,27,28,29])->get()->toArray();
            // $this->write('cole',json_decode($list),'cole');
//            var_dump($list);die;
            $data = array();
            foreach ($list as $k => &$v) {
                // "number-14"
//                $num_qi = substr($v['name'], -4, -2) - 1;
//                $start_qi = $periods + 1;
//                $end_qi = $periods + $num_qi;
//                $ex = explode('-', $v['value']);
//                $arr = explode(',', $v['reference']); #号码
//                $id  =$v['id'];
                $num_qi = ((int)substr((string)$v->name, -4, -2)) -1;
                $start_qi = $periods + 1;
                $end_qi = $periods + $num_qi;
                $ex = explode('-', $v->value);
                $arr = explode(',', $v->reference); #号码
                $id  =$v->id;

                // $this->write('cole',"id:".$id,'cole');
                switch (trim($ex[0])) {
                    case "number": #号码预测
                        shuffle($arr);
                        $value = array_slice($arr, 0, $ex[1]); #ex[1] 14
                        asort($value);
                        for ($i = 0; $i < count($value); $i++) if ((int)$value[$i] < 10) $value[$i] = "0" . $value[$i];
                        $val = implode(",", $value);
                        array_push($data, array(
                            'kid' => $kid,
                            'value' => $val,
                            'type' =>$id,
                            'qi_start' => $start_qi,
                            'qi_end' => $end_qi
                        ));
                        break;
                    case "dx": #大小
                        shuffle($arr);
                        $val = array_slice($arr, 0, 1);
                        array_push($data, array(
                            'kid' => $kid,
                            'value' => $val[0],
                            'type' => $id,
                            'qi_start' => $start_qi,
                            'qi_end' => $end_qi
                        ));
                        break;
                    case "ds":  #单双
                        shuffle($arr);
                        $val = array_slice($arr, 0, 1);
                        array_push($data, array(
                            'kid' => $kid,
                            'value' => $val[0],
                            'type' => $id,
                            'qi_start' => $start_qi,
                            'qi_end' => $end_qi
                        ));
                        break;
                    case "ws": #尾数
                        shuffle($arr);
                        $value = array_slice($arr, 0, $ex[1]); #ex[1] 14
                        asort($value);
                        $val = implode(",", $value);
                        array_push($data, array(
                            'kid' => $kid,
                            'value' => $val,
                            'type' => $id,
                            'qi_start' => $start_qi,
                            'qi_end' => $end_qi
                        ));
                        break;
                    case "sx":
                        shuffle($arr);
                        $value = array_slice($arr, 0, $ex[1]); #ex[1] 14
                        asort($value);
                        $val = implode(",", $value);
                        array_push($data, array(
                            'kid' => $kid,
                            'value' => $val,
                            'type' => $id,
                            'qi_start' => $start_qi,
                            'qi_end' => $end_qi
                        ));
                        break;
                    case "wx":#五行
                        shuffle($arr);
                        $value = array_slice($arr, 0, $ex[1]); #ex[1] 14
                        asort($value);
                        $val = implode(",", $value);
                        array_push($data, array(
                            'kid' => $kid,
                            'value' => $val,
                            'type' => $id,
                            'qi_start' => $start_qi,
                            'qi_end' => $end_qi
                        ));
                        break;
                }
                unset($id,$arr,$ex,$end_qi,$start_qi,$num_qi);
            }

//            return $data;die;
//            $list='[{"kid":30,"value":"01,03,05,08,10","type":"100","qi_start":20210413208,"qi_end":20210413209},{"kid":30,"value":"01,02,03,08,09","type":"101","qi_start":20210413208,"qi_end":20210413208},{"kid":30,"value":"02,03,04,07,08","type":"102","qi_start":20210413208,"qi_end":20210413210},{"kid":30,"value":"01,03,04,06,07","type":"103","qi_start":20210413208,"qi_end":20210413209},{"kid":30,"value":"01,02,03,06,10","type":"104","qi_start":20210413208,"qi_end":20210413208},{"kid":30,"value":"02,03,04,08,09","type":"105","qi_start":20210413208,"qi_end":20210413210},{"kid":30,"value":"08","type":"106","qi_start":20210413208,"qi_end":20210413209},{"kid":30,"value":"07","type":"107","qi_start":20210413208,"qi_end":20210413208},{"kid":30,"value":"10","type":"108","qi_start":20210413208,"qi_end":20210413210},{"kid":30,"value":"07","type":"109","qi_start":20210413208,"qi_end":20210413209}]';
//            $data=json_decode($list,true);
            $newArray=[];
            foreach ($data as $k => &$v) {
                $db=DB::table('yc')->where(['kid' => $v['kid'],'type'=>$v['type']])->orderBy('id','desc')->first();
                if($db){
                    if($db->qi_start == $v['qi_start'] && $db->qi_end == $v['qi_end']) continue;
                    # 历史记录的 结束期数 大于 新预测的开始期数 并且 上次未中奖
                    if($db->qi_end >= $v['qi_start'] && $db->state == 2) continue;
                }
//                if($db['qi_start'] == $v['qi_start'] && $db['qi_end'] == $v['qi_start'])  continue;
//                # 历史记录的 结束期数 大于 新预测的开始期数 并且 上次未中奖
//                if($db['qi_end'] >= $v['qi_start'] && $db['state'] == 2)  continue;
                array_push($newArray,$v);
            }

            $log=is_string($newArray)?$newArray:json_encode($newArray);
            $this->write('cole',$log,'cole');
            return $newArray;
//            var_dump($newArray);die;
            DB::table('yc')->insert($newArray);
            unset($newArray,$data);
        }catch (Exception $ex){
            $this->write('采集日志',$ex->getMessage(),'cole');
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
    public function Carousel(Slide $slide)
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
                ->get(['title', 'time', 'id']);
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
            if (!empty($type)) $news->where('nid', $type);
            $num = $request->get('num', 10);
            $result = $news->orderBy('time', 'desc')
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
    public function newsType(NewsClass $newsClass){
        try {
            $result =$newsClass->where('visible',0)->get(['name','id']);
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
