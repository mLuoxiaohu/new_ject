<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\BaseController;
use App\Http\Model\Opinion;
use App\Http\Model\User;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Faker\Generator as Image;

#随机头像re

/**
 * 用户控制器
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends BaseController
{


    private $user;
    private $auth;
    private $prefix = 'api';
    private $jwt;

    public function __construct(User $user,
                                Factory $auth,
                                JWT $JWTAuth

    )
    {
        $this->jwt = $JWTAuth;
        $this->auth = $auth;
        $this->user = $user;

    }
     /********************留言**************/
    /**
     * @desc 用户留言
     * @route api/leave_message
     * @param title 标题
     * @param content 内容
     * @return \Illuminate\Http\JsonResponse
     */
   public function leave_message(Opinion $opinion,Request $request){
       try{
           $param = [
               "name" => "required|min:1|max:14",
               "content" => "required|min:1",
           ];
           $message = [
               "name.required" => "名称不能为空",
               "name.unique" => '名称已存在',
               "name.min" => '名称不能小于1个字符，大于14个字符',
               "name.max" => '名称不能小于1个字符，大于14个字符',
               'content.required' => '内容不能为空',
               "content.min" => "内容不能小于1个字符",
           ];
           if (!$this->BaseValidator($request, $param, $message, $error)) return $this->_error($error);
           $input = $this->getParams($request);
           $input['nickname'] = $this->replaceDox($input['name']);
           $input['content'] = $this->replaceDox($input['content']);
           $input['avatar']='default'.rand(0,13).'.png';
           $result= $opinion->create($input);
           if($result) return $this->_success();
           return $this->_error();
       } catch (\Exception $ex) {
           return $this->_error($ex->getMessage());
       }
   }

    /**
     * @desc 用户留言列表
     * @method GET
     * @param num 每页多少条 default 10 可选
     * @param page 当前页  default 1   可选
     * @route /api/leave_message_list
     * @return \Illuminate\Http\JsonResponse
     */
   public function leave_message_list(Opinion $opinion,Request $request){
       try{
           $num=$request->get('num',10);
           $result= $opinion->with(['children'=>function($sql){
               $sql->where('state','2');
           }])
               ->where(['state'=>'2','pid'=>0])->paginate($num,['nickname','avatar','id','content','create_time']);
           if($result) return $this->_success($result);
           return $this->_error();
       } catch (\Exception $ex) {
           return $this->_error($ex->getMessage());
       }
   }



    /**
     * @desc 获取头像
     * @route /get_avatar
     * @method get
     * @return \Illuminate\Http\JsonResponse
     */
   public function randAvatar(){
       try {
           $data=[];
           for ($i=0;$i < 13;$i++) {
              $data[$i]=(BaseController::is_https() ? 'https://':'http://').($_SERVER["HTTP_HOST"] ?? $_SERVER['SERVER_ADDR'] ).'/user/'.'default'.$i.'.png';
           }
           return $this->_success($data);
       } catch (\Exception $ex) {
           return $this->_error($ex->getMessage());
       }
   }

    /**
     * @desc 个人信息
     * @method get
     * @route /detail
     * @return \Illuminate\Http\JsonResponse
     */
    public function person()
    {
        try {
            $userRe = $this->user->withCount(['article','articleStore'=>function($sql){
                $sql->where('is_delete',1);
            }])->find($this->authInit()->id());
            return $this->_success($userRe);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 发帖用户个人信息
     * @method get
     * @route /detail/{ta的id}
     * @return \Illuminate\Http\JsonResponse
     */
    public function bolgDetail($uid)
    {
        try {
            $userRe = $this->user->withCount(['article','articleStore'=>function($sql){
                $sql->where('is_delete',1);
            }])->find($uid);
            return $this->_success($userRe);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }


    /**
     * 添加用户提现银行卡
     * @method POST
     * @route /add_withdraw_bank
     * @param bank_name    银行卡名字
     * @param name         真实名称
     * @param bank_account 银行卡账号
     * @param bank_address 银行卡支行信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addWithdrawBank(Request $request)
    {
        try {

            $param = [
                "bank_name" => "required",
                "name" => "required",
                "bank_account" => "required|regex:/^\d{15,19}$/",
                'bank_address' => 'required',
                'code' => 'required',
                'key' => 'required'
            ];

            $message = [
                "bank_name.required" => "银行卡名称不能为空",
                'bank_account.required' => '银行卡账号不能为空',
                'name.required' => '持卡人不能为空',
                'bank_address.required' => '支行信息不能为空',
            ];

            if (!$this->BaseValidator($request, $param, $message, $error)) return $this->_error($error);
            $input = $this->getParams($request);
            $auth = $this->authInit();
            $model = $this->withdraw_bank;
            #检测银行卡是否超过上限
            $input['uid'] = $auth->id();
            $re = $model->create($input);
            if (!$re) return $this->_error(self::ADD_DATA_FAILED);
            return $this->_success([], self::ADD_DATA_SUCCESS);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }


    /**
     * @desc 删除提现银行卡
     * @route /delete_withdraw_bank/{银行卡id}
     * @mothod delete
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteWithdrawBank($id)
    {
        try {
            $result = $this->withdraw_bank->where('id', $id)->first();
            if (!$result) return $this->_error(self::DELETE_DATA_NOT_NULL);
            $isOk = $result->delete();
            if (!$isOk) return $this->_error(self::DELETE_FAILED);
            return $this->_success([], self::DELETE_SUCCESS);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * 上传文件
     * @param Request $request
     * @param post
     * @return \Illuminate\Http\JsonResponse
     */
    public function UploadFile(Request $request)
    {
        try {
            $file = $request->file('file');
            $allowed_extensions = ["png", "jpg", "jpeg", 'gif', 'bmp'];
            $end = $file->getClientOriginalExtension();
            if ($end && !in_array($end, $allowed_extensions)) return $this->_error(self::UPLOAD_TYPE_ERROR);
            $time = time();
            $date = date('Ymd', time());
            $path = public_path('uploads');
            $fileName = "{$path}/{$time}_{$this->str_rand(10)}.{$end}";
            $re = $file->move($path, $fileName);
            $fileName = "{$date}/{$re->getFilename()}";
            return $this->_success(['ImgUrl' => $fileName], self::UPLOAD_FILE_SUCCESS);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }


    /**
     * @msg 账号密码登陆
     * @route api/login
     * @method post
     * @param $mobile 账号
     * @param $password 密码
     * @param $code 图片验证码
     * @param $key 验证码key
     * @param Request $request
     * @return user obj
     */
    public function login(Request $request)
    {
        try {
            $param = [
                "mobile" => "required",
                "password" => "required",
//                'code' => "required",
//                'key' => "required",
            ];
            $message = [
                "mobile.required" => "手机号不能为空",
                "password.required" => "密码不能为空",
//                'code.required' => '请输入验证码',
//                'key.required' => 'key必填',
            ];
            if (!$this->BaseValidator($request, $param, $message, $error)) return $this->_error($error);
            $check = $this->getParams($request);
//            if (Cache::get($check['key']) != $check['code']) return $this->_error(self::CODE_ERROR);
//            Cache::forget($check['key']); #验证成功删除缓存
            unset($check['code'],$check['key']);
            $auth = $this->authInit();
            $this->jwt->setSecret(config('jwt.' . $this->prefix . '_secret')); #切换认证secret模块
            if (!$token = $auth->attempt($check)) {
                #登录失败
                return $this->_error(self::LOGIN_ERROR);
            }
            $user = $auth->user();
            if ($user->state == '2') return $this->_error(self::PROHIBIT_LOGIN);
            $user->login_ip = $request->getClientIp();
            $user->login_time = Carbon::now()->toDateTimeString();
            $user->save();
            return $this->_success(['token' => $this->TokenHeader . $token, 'user' => $auth->user(), 'token_type' => 'Authorization']);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * 修改个人信息
     * @route /change
     * @mothod put
     * @msg 以下是修改资料用的参数
     * @param Request $request
     * @param avatar   file 头像   可选
     * @param nickname  string 昵称 可选
     * @param signature string 个性签名 可选
     * @msg 以下是修改登陆密码使用{修改密码时以下三个参数必填}
     * @param password  string 要修改的密码 可选
     * @param old_password  string 旧密码 可选
     * @param confirm_password  string 确认密码 可选
     * @msg 以下是修改手机号传参 注 修改手机号需先请求 check_old_mobile 接口认证
     * @param mobile  int 新手机号
     * @param code  int 验证码
     * @return \Illuminate\Http\JsonResponse
     */
    public function userUpdate(Request $request)
    {
        try {
            $param = [
                "mobile" => "regex:/^1[3456789][0-9]{9}$/",
                'password' => 'min:6|max:14',
                'signature'=>'min:6|max:40',
                'confirm_password'=>'same:password',
                'avatar' => 'mimes:jpeg,bmp,png,jpg,gif',
            ];
            $message = [
                "password.min" => "密码不能小于6位",
                "password.max" => "密码不能大于18位",
                'confirm_password.same'=>'确认密码和新密码不一致',
                "signature.min" => "个性签名不能小于6位",
                "signature.max" => "个性签名不能大于40位",
                'avatar.mimes' => '只支持图片:jpeg,bmp,png,jpg,gif 格式!'
            ];
            if (!$this->BaseValidator($request, $param, $message, $error)) return $this->_error($error);
            $input = $this->getParams($request);
            $auth = $this->authInit();
            $id = $auth->id();
            if (isset($input['avatar'])) {
                $fileImg = $this->OneUploadFile($input['avatar']);
                if ($fileImg['code'] != 200) return $this->_error($fileImg['msg']);
                $input['avatar'] = $fileImg['name'] ?? '/default0.png';
            }
            #删除旧图片
            if (isset($input['avatar'])) {
                $old = $auth->user()->avatar;
                if (!empty($old)) $this->deleteFile($old);
            }
            if (count($input) <= 0) return $this->_error(self::NOT_CHANGE_CONTENT);
            #手机修改
            if (isset($input['mobile'])) {
                if(Cache::get($id) == null || Cache::get($id) !=1) return $this->_error(self::OLD_MOBILE_EXPIRED);
                $mobile = $input['mobile'];
                $is_mobile=$this->user->where('id','!=',$id)->where('mobile',$mobile)->value('id');
                if($is_mobile) return $this->_error(self::MOBILE_EXISTS);
                if (!isset($input['code'])) return $this->_error(self::INPUT_CODE);
                if (Cache::get($mobile) == null || Cache::get($mobile) != $input['code']) return $this->_error(self::CODE_ERROR);
            }
            unset($input['code']);
            #密码修改
            if (isset($input['password'])) {
                if ($input['password'] == $input['old_password']) return $this->_error(self::NEW_PASS_EQ_OLD_PASS);
                if (!isset($input['old_password'])) return $this->_error(self::OLD_PASSWORD_NULL);
                $check = [
                    'id' => $id,
                    'password' => $input['old_password']
                ];
                unset($input['old_password']);
                if (!$auth->attempt($check)) return $this->_error(self::OLD_PASSWORD_FAILED);
                $input['password']=Hash::make($input['password']);
            }
            $result = $this->user->where('id', $id)->update($input);
            if (!$result) return $this->_error(self::UPDATE_FAIL);
            #更新token
            $oldToken = $auth->getToken();
            $newToken = $this->refreshToken($oldToken);
            #更新数据
            $user = $this->user->find($id);
            $auth->login($user);
            return $this->_success(['token' => $newToken, 'user' => $auth->user(), 'token_type' => 'Authorization'], self::UPDATE_SUCCESS);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 忘记密码用手机号找回
     * @method post
     * @route /forget
     * @param mobile 手机号
     * @param password 新密码
     * @param code 手机验证码
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPwd(Request $request)
    {
        try {
            $param = [
                "mobile" => ['required','regex:/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/'],
                'password' =>['min:6','max:14'],
                'code' => ['required'],
            ];
            $message = [
                "password.min" => "密码不能小于6位",
                "password.max" => "密码不能大于14位",
                'mobile.regex' => '手机号格式不正确',
                'mobile.required' => '账号必填',
                'code.required' => '请输入短信验证码'
            ];
            if (!$this->BaseValidator($request, $param, $message, $error)) return $this->_error($error);
            $input = $this->getParams($request);
            if (!isset($input['mobile'])) return $this->_error(self::INPUT_MOBILE);
            if (!isset($input['code'])) return $this->_error(self::INPUT_CODE);
            $mobile = $input['mobile'];
            if (Cache::get($mobile) != $input['code']) return $this->_error(self::CODE_ERROR);
            $obj = $this->user->where('mobile', $input['mobile'])->first();
            if (!$obj) return $this->_error(self::MOBILE_NOT_EXISTS);
            $obj->password = Hash::make($input['password']);
            $result = $obj->save();
            if (!$result) return $this->_error(self::UPDATE_FAIL);
            unset($input);
            return $this->_success([], self::UPDATE_SUCCESS);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 修改手机验证旧手机接口
     * @param mobile 旧手机号
     * @param code 验证码
     * @route /check_old_mobile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkMobile(Request $request){
         try{
             $code=$request->request->get('code');
             $old_mobile=$request->request->get('mobile');

             if(empty($code) || empty($mobile)) return $this->_error(self::PARAM_FAIL);
             $id=$this->authInit()->id();
             $mobile= $old_mobile.$id;
             if (Cache::get($mobile) == null || Cache::get($mobile) != $code['code']) return $this->_error(self::CODE_ERROR);
             Cache::add($id,1,240);
             return $this->_success([],self::CHECK_SUCCESS);
         } catch (\Exception $ex) {
             return $this->_error($ex->getMessage());
         }
    }

    /**
     * 获取短信code码
     * @method GET
     * @route mobile_code
     * @param $mobile 手机号
     * @return msg
     */
    public function MobileCode(Request $input)
    {
        try {
            $param = [
                "mobile" => "required|regex:/^1[3456789][0-9]{9}$/"];
            $message = [
                "mobile.required" => "手机号不能为空",
                'mobile.regex' => '手机号格式不正确'
            ];
            if (!$this->BaseValidator($input, $param, $message, $error)) return $this->_error($error);
            $mobile = $input->get('mobile');
            #start:上线以后打开一下注释代码
//            $re=$this->sendMsg($mobile);
//            if($re->returnstatus =='Faild') return  $this->_error($re->message);
//            return $this->_success([]);
            #end:结束
            /*********分割**********/
            #start:上线以后注释以下代码
            $code = Cache::get($mobile);
            if (!$code) {
                $code = rand(1000, 9999);
                $id=$this->authInit()->id();
                if($id){
                    Cache::add($mobile.$id, $code, 240); //60
                }else{
                    Cache::add($mobile, $code, 60); //60
                }

            }
            $content = "【柒柒科技】您的验证码：{$code} 有效期60秒请尽快使用。";

            return $this->_success(['code'=>$code], $content);
            #end:结束
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * @desc 获取验证码图片
     * @route /image_code
     * @method GET
     * @return \Illuminate\Http\JsonResponse
     */
    public function ImageCode()
    {
        try {
            $phrase = new PhraseBuilder;
            // 设置验证码位数
            $code = $phrase->build(4);
            // 生成验证码图片的Builder对象，配置相应属性
            $builder = new CaptchaBuilder($code, $phrase);
            // 设置背景颜色25,25,112
            $builder->setBackgroundColor(255, 255, 255);
            // 设置倾斜角度
            $builder->setMaxAngle(20);
            // 设置验证码后面最大行数
            $builder->setMaxBehindLines(8);
            // 设置验证码前面最大行数
            $builder->setMaxFrontLines(8);
            // 设置验证码颜色
            $builder->setTextColor(0, 204, 153);
            // 可以设置图片宽高及字体
            $builder->build($width = 150, $height = 40, $font = null);
            // 获取验证码的内容
            $phrase = strtolower($builder->getPhrase());
            // 把内容存入 cache，3分钟后过期
            $client_id = Uuid::uuid1()->toString();
            Cache::put($client_id, $phrase, Carbon::now()->addMinutes(3));
            return $this->_success(['key' => $client_id, 'image' => $builder->inline()]);
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }

    private function authInit()
    {
        return $this->auth->guard($this->prefix);
    }

    /**
     * #用户注册逻辑
     * @route /register
     * @param mobile 手机号
     * @param password 密码
     * @param code 手机验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $param = [
                "mobile" => ['required','unique:users','regex:/^1[3456789][0-9]{9}$/'],
                "password" => ['required','regex:/^[a-z\d_]{6,14}$/i'],
                "code" => "required",
            ];
            $message = [
                "mobile.unique" => '此手机号已注册',
                "mobile.required" => "手机号不能为空",
                "mobile.regex" => "手机号格式不正确",
                "password.required" => "密码不能为空",
                'password.regex'=>'密码只能为只能为数字母6~14位',
                'code.required' => '请输入验证码',
            ];
            if (!$this->BaseValidator($request, $param, $message, $error)) return $this->_error($error);
            $input = $this->getParams($request);
            if (Cache::get($input['mobile']) != $input['code']) return $this->_error(self::CODE_ERROR);
            Cache::forget($input['mobile']); #验证成功删除缓存
//            if (isset($input['invite'])) {1011
//                $obj = $this->code->where('code', trim($input['invite']))->first(['uid']);
//                if (empty($obj)) return $this->_error(self::INVITE_CODE_ERROR);
//                $input['pid'] = $obj->uid;
//            }
            #写入用户数据
            $end_str = substr($input['mobile'], 7, strlen($input['mobile']));
            $input['nickname'] = '手机用户：@' . $end_str;
            $input['login_ip'] = $request->getClientIp();
            $input['password'] = Hash::make(strtolower($input['password'])); #laravel Auth验证密码必须hash密码
//            $input['avatar'] = $image->imageUrl(300, 300);
            $input['signature']   = '这个人很懒什么都没留下!';
            $input['avatar']   = 'default0.png';
            $input['login_time'] = Carbon::now()->toDateTimeString();
//            $minGrade = $this->grade->getMinGrade();
//            if ($minGrade === false) return $this->_error(self::ADD_DATA_FAILED);
//            $input['alipay_type'] = $minGrade;
            $reg = $this->user->create($input);
            #创建用户上下级关系
//            if (isset($input['invite'])) {
//                $this->agent->uid = $reg->id;
//                $this->agent->pid = $input['pid'];
//                $this->agent->save();
//            }
            #加入用户邀请码
//            $this->code->uid = $reg->id;
//            $this->code->code = $this->str_rand(8);
//            $this->code->save();
            unset($obj, $input);
            if (!$reg) return $this->_error(self::REGISTER_ERROR);
            DB::commit();
            $user = $this->user->where('id',$reg->id)->first( 'nickname','mobile','sex','password','avatar',
                'signature','login_time','coin');
            $auth = $this->authInit();
            $auth->login($user);
            return $this->_success(['token' => $this->TokenHeader . $auth->getToken(), 'user' => $auth->user(), 'token_type' => 'Authorization'], self::REGISTER_SUCCESS);
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->_error($ex->getMessage());
        }
    }

    /**
     * 刷新token
     * @param null $old_token 旧token{可选}
     * @return string
     */
    private function refreshToken($old_token = null)
    {
        $auth = $this->authInit();
        if ($old_token) {
            $token = $this->TokenHeader . $auth->refresh($old_token); //刷新token
            $auth->invalidate($old_token);
            return $token;
        } else {
            $token = $this->TokenHeader . $auth->refresh(); //刷新token
            $auth->invalidate(true);
            return $token;
        }

    }

    /**
     * @route  /logout
     * 用户退出
     */
    public function logOut()
    {
        try {
            $this->authInit()->invalidate(true);
            return $this->OperationSuccess();
        } catch (\Exception $ex) {
            return $this->_error($ex->getMessage());
        }
    }
}
