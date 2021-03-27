<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\BaseController;
use App\Http\Model\Agent;
use App\Http\Model\BankCard;
use App\Http\Model\Grade;
use App\Http\Model\Recharge;
use App\Http\Model\UserRebate;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Contracts\Providers\JWT;

/**
 * Class WalletController
 * @package App\Http\Controllers\Api
 */
class WalletController extends BaseController
{

    private $auth;
    private $prefix = 'api';
    private $jwt;
    private $bank;     #用户银行卡
    private $site_url; #获取根url
    private $grade;    #等级
    private $agent;    #代理
    private $rebate;   #返佣
    private $recharge; #充值
    public function __construct(Factory $auth,
                                JWT $JWTAuth,
                                BankCard $bankCard,
                                URL $url,
                                Grade $grade,
                                Agent $agent,
                                UserRebate $userRebate,Recharge $recharge)
    {
        $this->jwt = $JWTAuth;
        $this->auth = $auth;
        $this->bank = $bankCard;
        $this->site_url=$url::previous();
        $this->grade=$grade;
        $this->agent=$agent;
        $this->rebate=$userRebate;
        $this->recharge=$recharge;
    }

    private function authInit()
    {
        return $this->auth->guard($this->prefix);
    }

    /***
     * 钱包界面
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        try {
         $auth=$this->authInit();
         $user=$auth->user();
         $children_people_all_number=$this->agent->where('pid',$auth->id())->count('id'); // 1 级
         $count_coin=$this->rebate->where('uid',$auth->id())->sum('coin');
         $a= $this->agent->getTable();
         $b= $this->recharge->getTable();
         $children_recharge_people_number=DB::table($a.' as a')
             ->join($b.' as b','a.uid','=','b.uid')
             ->whereIn('b.state',['success','confirm'])
             ->where('a.pid',$auth->id())
             ->groupBy('b.uid')
             ->get('b.uid');
         $agent_list=$this->grade->where('type',$this->grade::TYPE_TOW)->orderBy('grade')->get(['icon','name','grade','condition']);
         $data=array(
             'user'=>array(
                 'id'=>$user->id,
                 'coin'=>$user->coin,
                 'agent_type'=>$user->agent_type,
             ),
             'agent'=>array(
                 'count_coin'=>$count_coin, #累积收益
                 'count_people'=>$children_people_all_number,
                 'count_recharge_people'=>count($children_recharge_people_number)
             ),
             'agent_list'=>$agent_list,
         );
         return $this->_success($data);
        }catch (\Exception $ex){
            return $this->_error($ex->getMessage());
        }
    }
}
