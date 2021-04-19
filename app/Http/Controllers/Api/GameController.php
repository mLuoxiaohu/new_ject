<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\BaseController;
use App\Http\Model\Cate;
use App\Http\Model\Cole;
use App\Http\Model\Kind;
use App\Http\Model\Opinion;
use App\Http\Model\Plan;
use App\Http\Model\Record;
use App\Http\Model\Store;
use App\Http\Model\User;
use App\Http\Model\Yc;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\Providers\JWT;

class GameController extends BaseController
{

    private $user;
    private $auth;
    private $prefix = 'api';
    private $jwt;
    private $kind;
    private $input;
    private $open;

    public function __construct(User $user,
                                Factory $auth,
                                JWT $JWTAuth,
                                Kind $kind, Request $request, Record $record)
    {
        $this->jwt = $JWTAuth;
        $this->user = $user;
        $this->auth = $auth;
        $this->kind = $kind;
        $this->input = $request;
        $this->open = $record;
    }

    protected function authInit()
    {
        return $this->auth->guard($this->prefix);
    }


    /**
     * @desc 我的收藏
     * @route get
     * @route /self_store_list
     * @param Store $store
     * @return \Illuminate\Http\JsonResponse
     */
    public function game_store_list(Store $store){
        try{
            $list= $store->where('uid',$this->authInit()->id())->pluck('lottery_id');
            $rows = $this->kind->where(['none' => 0])->whereIn('id',$list)
                ->orderBy('sort', 'asc')
                ->get(['id', 'name', 'icon', 'date', 'abbr', 'video']);
            if(!$rows) return $this->_error(self::THE_LOTTERY_NULL);
            foreach ($rows as $key => &$value) {
                $arr = $this->open->where('kid', $value['id'])
                    ->orderBy('id', 'desc')
                    ->first(['kid', 'periods', 'number', 'time', 'next_time', 'adds']);
                $rows[$key]['periods'] = $arr['periods'];
                $rows[$key]['number'] = $arr['number'];
                if (in_array($arr['kid'], [18, 37, 38, 40])) {
                    $sxNumber = $this->getLhcOpenInfo($arr['adds']);
                } else {
                    $sxNumber = $this->getLhcTime($arr['number']);
                }

                $rows[$key]['sxlist'] = $sxNumber['sxNumber'];
                $type = explode("/", $value['date']);
                $rows[$key]['down'] = $this->timeCal($arr, $type);

                if ($rows[$key]['abbr'] == 'xjp' || $rows[$key]['abbr'] == 'amlhc' || $rows[$key]['abbr'] == 'hk6' || $rows[$key]['abbr'] == 'twlh') {
                    $rows[$key]['down'] = $this->timeCal($arr, '', true);
                }
            }
                return $this->_success($rows);
            } catch (\Exception $ex) {
                return $this->_error($ex->getMessage());
            }
    }

    /**
     * @desc 预测类型
     * @method get
     * @route /game_yc_type
     * @param id 彩种id
     * @return \Illuminate\Http\JsonResponse
     */
    public function yucolle(Cole $cole){
        try{
           $id = $this->input->get('id');
            if (empty($id)) return $this->_error(self::PARAM_FAIL);
            $list=$cole->where('kid',$id)->get(['id','name']);
            if ($list) return $this->_success($list);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * 预测彩种详情
     * @method get
     * @route /game_yc_list
     * @param num 显示条数
     * @param id 彩种id
     * @param  type 预测类型id
     * @return \Illuminate\Http\JsonResponse
     */
    public function prediction(Yc $yc){
        try {
            $limit = $this->input->get('num', 20);
            $id = $this->input->get('id');
            $type=$this->input->get('type');
            if (empty($id) || empty($type)) return $this->_error(self::PARAM_FAIL);
            $list=$yc->where(['kid'=>$id,'type'=>$type])->with('cole')->limit($limit)->get(['type','qi_start','qi_end','value','bonus','state']);
            if ($list) return $this->_success($list);
            return $this->_error();
            return $this->_error();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 六合彩专版查询
     * @method get
     * @route /lh_special
     * @return \Illuminate\Http\JsonResponse
     */
    public function lhcSpecial()
    {
        try {
            $wx = unserialize(env('WUXIN'));
            $sx = unserialize(env('SHENXIAO'));
            $day = unserialize(env('DAY'));
            if ($wx && $sx && $day) return $this->_success(array('wx' => $wx, 'sx' => $sx, 'day' => $day));
            return $this->_error();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 获取有直播的彩种列表
     * @method get
     * @route /game_live_all
     * @return \Illuminate\Http\JsonResponse
     */
    public function gatLiveAllGame()
    {
        try {
            $info = $this->kind->where(array('video' => 1, 'none' => 0))->orderBy('sort', 'asc')->get(['id', 'name', 'icon', 'abbr']);
            if ($info) return $this->_success($info);
            return $this->_error();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }

    }


    /**
     * @desc 直播列表
     * @method route
     * @route /game_live_list
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLiveList()
    {
        try {
            $rows = $this->kind->where(array('video' => 1, 'none' => 0))->orderBy('id', 'desc')->get(['id', 'icon', 'date', 'abbr']);
            foreach ($rows as $key => &$value) {
                $arr = $this->open->where(array('kid' => $value['id']))->orderBy('id', 'desc')->first(['periods', 'number', 'time']);
                if ($value['abbr'] != 'hk6') {
                    $type = explode("/", $value['date']);
                    $rows[$key]['down'] = $this->timeCal($arr, $type);
                } else {
                    $sxNumber = $this->getLhcTime($arr['number']);
                    $prev = date('d', $arr['time']);
                    if ($sxNumber['kj'] == $prev) {
                        $rows[$key]['down'] = $sxNumber['down'];
                    } else {
                        $rows[$key]['down'] = 0;
                    }
                }
            }
            if ($rows) return $this->_success($rows);
            return $this->_error('未获取到内容');
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }


    /**
     * @desc 获取单个开奖记录
     * @param id 彩种id
     * @method get
     * @route /game_open_ones
     * @return \Illuminate\Http\JsonResponse
     */
    public function againTime()
    {
        try {
            $id = $this->input->get('id');
            if (empty($id)) return $this->_error(self::PARAM_FAIL);
            $row = $this->kind->where('id', $id)
                ->orderBy('id', 'desc')
                ->first(['name', 'icon', 'date', 'abbr', 'video']);
            $arr = $this->open->where('kid', $id)
                ->orderBy('periods', 'desc')
                ->orderBy('time', 'desc')
                ->first(['periods', 'number', 'time', 'next_time']);
            $row['periods'] = $arr['periods'];
            $row['number'] = explode(",",$arr['number']);
            if (empty($row) || empty($arr)) return $this->_error(self::DATA_NULL);
            if (true) {
                $type = explode("/", $row['date']);
                $row['down'] = $this->timeCal($arr, $type);
                if ($row['abbr'] == 'xjp' || $row['abbr'] == 'amlhc' || $row['abbr'] == 'hk6' || $row['abbr'] == 'bjkl8' || $row['abbr'] == 'twlh') {
                    $row['down'] = $this->timeCal($arr, '', true);
                }
            } else {
                $prev = date('d', $arr['time']);  // record 4
                $sxNumber = $this->getLhcTime($arr['number']); // record
                $row['sxlist'] = $sxNumber['sxNumber'];
                if ($sxNumber['kj'] == $prev) {
                    $row['down'] = $sxNumber['down'];
                } else {
                    $row['down'] = 0;
                }
            }
            return $this->_success($row);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }


    /**
     * @desc 游戏类型
     * @route /game_type
     * @method GET
     * @return \Illuminate\Http\JsonResponse
     */
    public function game_type()
    {
        try {
            $result = $this->kind->game_type;
            return $this->_success($result);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }

    }

    /**
     * @desc 彩票计划列表
     * @method get
     * @route /game_cate_list
     * @return \Illuminate\Http\JsonResponse
     */
    public function cateGamesList(Cate $cate)
    {
        try {
            $result = $cate->with('kind')->get();
            return $this->_success($result);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 获取所有彩种
     * @method Get
     * @route /game_all
     * @return \Illuminate\Http\JsonResponse
     */
    public function game_all()
    {
        try {
            $result = $this->kind->where('none', 0)
                ->orderBy('sort', 'asc')
                ->get(['id', 'name', 'icon', 'abbr']);
            return $this->_success($result);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 开奖列表
     * @param cate_id 开奖类型id
     * @route /game_open_list
     * @method GET
     * @return \Illuminate\Http\JsonResponse
     */
    public function game_open_list()
    {
        try {
            $cate_id = $this->input->get('cate_id', '');
            if (empty($cate_id)) return $this->_error(self::PARAM_FAIL);
            //查询该分类的游戏
            $rows = $this->kind->where(['cid' => $cate_id, 'none' => 0])
                ->orderBy('sort', 'asc')
                ->get(['id', 'name', 'icon', 'date', 'abbr', 'video']);
            foreach ($rows as $key => &$value) {
                $arr = $this->open->where('kid', $value['id'])
                    ->orderBy('id', 'desc')
                    ->first(['kid', 'periods', 'number', 'time', 'next_time', 'adds']);
                if (true) {  // $value['abbr'] != 'hk6'
                    $rows[$key]['periods'] = $arr['periods'];
                    $rows[$key]['number'] = $arr['number'];
                    if (in_array($arr['kid'], [18, 37, 38, 40])) {
                        $sxNumber = $this->getLhcOpenInfo($arr['adds']);
                    } else {
                        $sxNumber = $this->getLhcTime($arr['number']);
                    }

                    $rows[$key]['sxlist'] = $sxNumber['sxNumber'];
                    $type = explode("/", $value['date']);
                    $rows[$key]['down'] = $this->timeCal($arr, $type);

                    if ($rows[$key]['abbr'] == 'xjp' || $rows[$key]['abbr'] == 'amlhc' || $rows[$key]['abbr'] == 'hk6' || $rows[$key]['abbr'] == 'twlh') {
                        $rows[$key]['down'] = $this->timeCal($arr, '', true);
                    }
                } else {
                    $rows[$key]['periods'] = $arr['periods'];
                    $rows[$key]['number'] = $arr['number'];
                    $sxNumber = $this->getLhcTime($arr['number']);
                    $rows[$key]['sxlist'] = $sxNumber['sxNumber'];
                    $prev = date('d', $arr['time']);
                    if ($sxNumber['kj'] == $prev) {
                        $rows[$key]['down'] = $sxNumber['down'];
                    } else {
                        $rows[$key]['down'] = 0;
                    }
                }
            }
            return $this->_success($rows);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    private function getLhcOpenInfo($number)
    {

        $number = explode('|', $number)[0];
        $res = [];
        $res['sxNumber'] = $number;
        return $res;
    }


    /**
     * @desc 获取彩种开奖记录
     * @route /game_record
     * @param num 每页多少条 default 20
     * @param id 彩种id 必填
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPerRecord()
    {
        try {
            $limit = $this->input->get('num', 20);
            $id = $this->input->get('id');


            if (empty($id)) return $this->_error(self::PARAM_FAIL);
            $db_record = DB::table('record');
            if ($id == 28 || $id == 23 || $id == 41 || $id == 1) {
                $info = $db_record->select(DB::raw('distinct(periods),number,time'))
                    ->where('kid', $id)->orderBy('time', 'desc')
                    ->limit($limit)->get();
            } else {
                $info = $db_record->select(DB::raw('distinct(periods),number,time'))
                    ->where('kid', $id)->orderBy(DB::raw('periods * 1'), 'desc')
                    ->limit($limit)->get();
            }
            $row = $this->kind->where('id', $id)->first(['abbr', 'name', 'code']);

            if ($info) foreach ($info as $key => &$v) $v->number = explode(',', $v->number);

            if ($info) return $this->_success(['info' => $info, 'abbr' => $row['abbr'], 'name' => $row['name'], 'code' => $row['code']]);
            return $this->_error();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }



    /**
     * @desc  获取澳门,新加坡,香港开奖
     * @route /game_open_other
     * @method get
     * @param  num 每页多少条 default 20
     * @param  id 彩种id 18香港,37新加坡，38澳门，40台湾
     */
    public function gameOpenOther(){
        //获取参数
        try{
        $limit = $this->input->get('num', 20);
        $id = $this->input->get('id');
        if(empty($id) || empty($limit)) return $this->_error(self::PARAM_FAIL);

        if(!in_array($id,$this->kind->other)) return $this->_error(self::PARAM_NOT_EXISTS);
        $info = $this->open->where(array('kid'=>$id))
            ->orderBy('periods', 'desc')
            ->limit($limit)->get(['periods','number','adds','time','next_time'])->toArray();
        foreach($info as $k => &$v) {
           $ex=explode('|',$v['adds']);
           $v=array(
                'periods'=>$v['periods'],
                'wx'=>explode(",",$ex[1]),
                'sx'=>explode(",",$ex[0]),
                'color'=>explode(",",$ex[2]),
                'number'=> explode(',', $v['number']),
                'next_time'=>date('Y-m-d H:i:s',$v['next_time']),
                'time'=>date('Y-m-d H:i:s',$v['time']),
            );
        }
            if ($info) return $this->_success($info);
            return $this->_error();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }



    /**
     * @desc 获取彩种开奖记录（新加坡，澳门专用）
     * @route /game_lh_record
     * @param num 每页多少条 default 20
     * @param id 彩种id 必填
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLhcRecord()
    {
        try {
            $limit = $this->input->get('num', 20);
            $id = $this->input->get('id');
            if (empty($id)) return $this->_error(self::PARAM_FAIL);
            $info = $this->open->where('kid', $id)
                ->orderBy('id', 'desc')
                ->limit($limit)->get(['periods', 'number', 'adds', 'time'])->toArray();
            if ($info) {
                foreach ($info as $key => &$v) {
                    $v['number'] = explode(',', $v['number']);
                }
            }
            return $this->_success($info);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 获取计划开奖记录
     * @route /game_plan_record
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlanRecord()
    {
        try {
            //获取参数
            $limit = $this->input->get('num', 20);
            $id = $this->input->get('id');
            if (empty($id) || empty($limit)) return $this->_error(self::PARAM_FAIL);
            if (in_array($id, [11])) {
                $info = $this->open->where('kid', $id)
                    ->orderBy(DB::raw('periods * 1'), 'desc')
                    ->limit($limit)
                    ->get(['id', 'periods', 'number', 'value', 'time']);
            } else {
                $info = $this->open->where('kid', $id)
                    ->orderBy('id', 'desc')
                    ->limit($limit)
                    ->get(['id', 'periods', 'number', 'value', 'time']);
            }
            foreach ($info as $key => $value) $info[$key]['value'] = unserialize($value['value']);
            $row = $this->kind->where('id', $id)->first(['abbr', 'name', 'code']);
            //生肖计算
            $sx = unserialize(env('SHENXIAO'));
            if ($row['abbr'] == 'hk6' || $row['abbr'] == 'xjp' || $row['abbr'] == 'amlhc' || $row['abbr'] == 'twlh') {
                foreach ($info as $key => $value) {
                    //字符串转数组
                    $numArr = explode(',', $value['number']);
                    $sxNumber = array();
                    foreach ($numArr as $k => $v) foreach ($sx as $i => $j) if (in_array($v, $j)) $sxNumber[$k] = $i;
                    $info[$key]['sx'] = $sxNumber;
                }
            }
            return $this->_success(array('info' => $info, 'abbr' => $row['abbr'], 'name' => $row['name'], 'code' => $row['code']));
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }

    }


    /**
     * @desc 获取本期计划
     * @param id 彩种id
     * @method get
     * @route /game_plan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getbqPlan(Plan $plan)
    {
        try {
            $id = $this->input->get('id');
            $info = $plan->where('kid', $id)->orderBy('id', 'desc')->first();
            $info['value'] = unserialize($info['value']);
            $row = $this->kind->where('id', $id)->first(['abbr', 'name', 'code']);
            if ($info) return $this->_success(array('info' => $info, 'abbr' => $row['abbr'], 'name' => $row['name'], 'code' => $row['code']));
            return $this->_error();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }



    /**
     * @desc 六合彩开奖及生肖
     * @param $number 开奖号码
     * @return array
     */
    private function getLhcTime($number)
    {

        $rows = $this->open->where(['kid' => 18, 'number' => $number])->value('periods') ?? 0;
        if ($rows < 8) {
            $sx = unserialize(env('SHENXIAO'));
        } else {
            $sx = unserialize(env('SHENXIAO'));
        }
        $day = unserialize(env('DAY'));
        //字符串转数组
        $numArr = explode(',', $number);
        $sxNumber = array();
        foreach ($numArr as $k => $v) {
            foreach ($sx as $i => $j) {
                if (in_array($v, $j)) {
                    $sxNumber[$k] = $i;
                }
            }
        }
        $tyear = date('Y', time());
        $tmoth = date('m', time());
        $today = date('d', time());
        $maxArr = array();
        foreach ($day as $k => $v) {
            if ($v['month'] == $tmoth) {
                foreach ($v['list'] as $i => $j) {
                    if ($j >= $today) {
                        $maxArr[$i] = $j;
                    }
                    if ($j <= $today) {
                        $prevkj[$i] = $j;
                    }
                }

                if (count($maxArr) == 0) {
                    $month = $day[$k + 1]['month'];
                    $next = min($day[$k + 1]['list']);
                } else {
                    $month = $v['month']; // 1
                    $next = array_shift($maxArr);  // 4

                }

                if (count($prevkj) == 0) {
                    $kj = max($day[$k - 1]['list']);
                } else {
//                    $kj = array_pop($prevkj); // 4
                    // 2020 年 改
                    if (count($prevkj) == 1 && $prevkj[0] != $today) {
                        $kj = $next;
                    } else {
                        $kj = max($prevkj); // 2
                    }
                }
                $tt = $tyear . '-' . $month . '-' . $next . ' 21:30:00';
                $tt = strtotime($tt); // 1578144600 2020/1/7 21:30:00

                //如果时间在今天 并且大于开奖时间 就请求下一天的开奖
                if ($today == $next && time() > $tt) {
                    $again = min($maxArr);
                    if ($again == null || $again == '') {
                        $next = min($day[$k + 1]['list']);
                    } else {
                        $next = $again;
                    }
                }
                //如果时间在今天 并且小于开奖时间 [还是请求上一天] => 【依旧请求今天的时间】
                if ($today == $next && time() < $tt) {
                    $begin = max($prevkj);  // 2
//                    print_r( $prevkj);exit;
                    if ($begin == null || $begin == '') {
                        $kj = min($day[$k - 1]['list']);
                    } else {
                        $kj = $begin;
                    }
                }
            }
        }
        $nextTime = $tyear . '-' . $month . '-' . $next . ' 21:30:00';
        $nextTime = strtotime($nextTime);

        $down = $nextTime - time();

        if ($down < 0) $down = 0;

        if (!$kj) $kj = $down;

        $arr['kj'] = $kj;
        $arr['down'] = $down;
        $arr['sxNumber'] = $sxNumber;
        return $arr;
    }


    /**
     * @desc 计算时间倒计时
     * @param $arr 开奖数组 array
     * @param $type 类型 int
     * @param false $xjp 是否为新加坡 bool
     * @return false|int
     */
    private function timeCal($arr, $type, $xjp = false)
    {
        if ($xjp) {
            // 单独计算新加坡彩
            $nextTime = $arr['next_time'];
            return $nextTime - time() > 0 ? $nextTime - time() : 0;
        }
        $count = count($type);
        $new = time();
        switch ($count) {
            case 4:
                //第一阶段时间范围
                $jd1 = explode('-', $type[0]);
                //第二阶段时间范围
                $jd2 = explode('-', $type[2]);
                //第一阶段时间戳
                $jd1_start = strtotime(date('Y-m-d H:i:s', strtotime($jd1[0])));
                $jd1_end = strtotime(date('Y-m-d H:i:s', strtotime($jd1[1])));
                //第二阶段时间戳
                $jd2_start = strtotime(date('Y-m-d H:i:s', strtotime($jd2[0])));
                $jd2_end = strtotime(date('Y-m-d H:i:s', strtotime($jd2[1])));
                //现在时间戳
                if ($new >= $jd1_start && $new <= $jd1_end) {
                    if (($arr['time'] + $type[2]) > time()) {
                        $down = ($arr['time'] + $type[2]) - time() - 15;
                    } else {
                        $down = 0;
                    }
                } else if ($new > $jd2_start) {
                    if (($arr['time'] + $type[3]) > time()) {
                        $down = ($arr['time'] + $type[3]) - time() - 15;
                    } else {
                        $down = 0;
                    }

                } else if ($new < $jd2_end) {
                    if (($arr['time'] + $type[3]) > time()) {
                        $down = ($arr['time'] + $type[3]) - time() - 15;
                    } else {
                        $down = 0;
                    }

                } else {
                    //明天开始时间 - 今晚结束时间
                    $down = $jd1_start - $new;

                }
                break;
            case 2:
                $jd1 = explode('-', $type[0]);
                //第一阶段时间戳
                $jd1_start = strtotime(date('Y-m-d H:i:s', strtotime($jd1[0])));
                $jd1_end = strtotime(date('Y-m-d H:i:s', strtotime($jd1[1])));
                if ($jd1_end - $jd1_start > 0) {
                    if ($new >= $jd1_start && $new <= $jd1_end) {
                        if (($arr['time'] + $type[1]) > time()) {
                            $down = ($arr['time'] + $type[1]) - time() - 15;
                        } else {
                            $down = 0;
                        }
                    } else {
                        //用第二天的开始时间减去现在的时间 算出剩余开奖时间
                        $next = strtotime('+1 day', strtotime(date('Y-m-d H:i:s', $jd1_start)));
                        $down = $next - $new;
                    }
                } else {
                    if ($new >= $jd1_start) {
                        if (($arr['time'] + $type[1]) > time()) {
                            $down = ($arr['time'] + $type[1]) - time() - 15;
                        } else {
                            $down = 0;
                        }
                    } else if ($new <= $jd1_end) {

                        if (($arr['time'] + $type[1]) > time()) {
                            $down = ($arr['time'] + $type[1]) - time() - 15;
                        } else {
                            $down = 0;
                        }
                    } else {
                        $down = $jd1_start - $new;
                    }
                }

                break;
            case 1:
                if (($arr['time'] + $type[0]) > time()) {
                    $down = ($arr['time'] + $type[0]) - time() - 15;
                } else {
                    $down = 0;
                }
                break;
        }
        return $down;
    }
}
