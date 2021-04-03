<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\BaseController;
use App\Http\Model\Kind;
use App\Http\Model\Record;
use App\Http\Model\User;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
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
     * @desc 开奖列表
     * @param cate_id 开奖类型id
     * @route /game_open_list
     * @method GET
     * @return \Illuminate\Http\JsonResponse
     */
    public function game_open_list()
    {
//        try {
            $cate_id = $this->input->get('cate_id','');
            if(empty($cate_id)) return $this->_error(self::PARAM_FAIL);
            //查询该分类的游戏
            $rows = $this->kind->where(array('cid' => $cate_id, 'none' => 0))
                ->orderBy('sort', 'asc')
                ->get(['id', 'name', 'icon', 'date', 'abbr', 'video']);
            foreach ($rows as $key => &$value) {

                var_dump($value);die;
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
//        } catch (\Exception $ex) {
//            return $this->_error($ex->getMessage());
//        }
    }

    private function getLhcOpenInfo($number)
    {

        $number = explode('|', $number)[0];
        $res = [];
        $res['sxNumber'] = $number;
        return $res;
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
    private function timeCal($arr,array $type, $xjp=false){
        if ($xjp) {
            // 单独计算新加坡彩
            $nextTime = $arr['next_time'];
            return $nextTime-time() > 0 ? $nextTime-time(): 0;
        }
        $count = count($type);
        $new = time();
        switch ($count) {
            case 4:
                //第一阶段时间范围
                $jd1 = explode('-',$type[0]);
                //第二阶段时间范围
                $jd2 = explode('-',$type[1]);
                //第一阶段时间戳
                $jd1_start = strtotime(date('Y-m-d H:i:s',strtotime($jd1[0])));
                $jd1_end = strtotime(date('Y-m-d H:i:s',strtotime($jd1[1])));
                //第二阶段时间戳
                $jd2_start = strtotime(date('Y-m-d H:i:s',strtotime($jd2[0])));
                $jd2_end = strtotime(date('Y-m-d H:i:s',strtotime($jd2[1])));
                //现在时间戳
                if($new>=$jd1_start && $new<=$jd1_end){
                    if(($arr['time']+$type[2])>time()){
                        $down = ($arr['time']+$type[2])-time()-15;
                    }else{
                        $down = 0;
                    }
                }else if($new>$jd2_start){
                    if(($arr['time']+$type[3])>time()){
                        $down = ($arr['time']+$type[3])-time()-15;
                    }else{
                        $down = 0;
                    }

                }else if( $new<$jd2_end){
                    if(($arr['time']+$type[3])>time()){
                        $down = ($arr['time']+$type[3])-time()-15;
                    }else{
                        $down = 0;
                    }

                }else{
                    //明天开始时间 - 今晚结束时间
                    $down = $jd1_start - $new;

                }
                break;
            case 2:
                $jd1 = explode('-',$type[0]);
                //第一阶段时间戳
                $jd1_start = strtotime(date('Y-m-d H:i:s',strtotime($jd1[0])));
                $jd1_end = strtotime(date('Y-m-d H:i:s',strtotime($jd1[1])));
                if($jd1_end-$jd1_start>0){
                    if($new>=$jd1_start && $new<=$jd1_end){
                        if(($arr['time']+$type[1])>time()){
                            $down = ($arr['time']+$type[1])-time()-15;
                        }else{
                            $down = 0;
                        }
                    }else{
                        //用第二天的开始时间减去现在的时间 算出剩余开奖时间
                        $next = strtotime('+1 day',strtotime(date('Y-m-d H:i:s',$jd1_start)));
                        $down = $next - $new;
                    }
                }else{
                    if($new>=$jd1_start){
                        if(($arr['time']+$type[1])>time()){
                            $down = ($arr['time']+$type[1])-time()-15;
                        }else{
                            $down = 0;
                        }
                    }else if($new<=$jd1_end){

                        if(($arr['time']+$type[1])>time()){
                            $down = ($arr['time']+$type[1])-time()-15;
                        }else{
                            $down = 0;
                        }
                    }else{
                        $down = $jd1_start - $new;
                    }
                }

                break;
            case 1:
                if(($arr['time']+$type[0])>time()){
                    $down = ($arr['time']+$type[0])-time()-15;
                }else{
                    $down = 0;
                }
                break;
        }
        return $down;
    }
}
