<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail; //邮箱类
class BaseController extends Controller{

   protected $TokenHeader   ='Bearer ';
   protected $salt          ='x-build';
   const HELLOW             ='欢迎访问';
   const FIND_SUCCESS       ='查询成功';
   const FIND_ERROR         ='查询失败';
   const REQUEST_SUCCESS    ='成功';
   const REQUEST_FAIL       ='失败';
   const OPERATION_SUCCESS  ='操作成功';
   const OPERATION_FAIL     ='操作失败';
   const UNKNOWN_ERROR      ='未知错误';
   const REQUEST_ERROR      ='参数错误';
   const PARAM_NOT_NULL     ='参数不能为空';
   const TYPE_ERROR         ='类型错误';
   const PARAM_LOSE         ='参数丢失';
   const CHECK_FAIL         ='验证失败';
   const CHECK_SUCCESS         ='验证失败';
   const TOKEN_ERROR        ='token错误';
   const TITLE_ERROR        ='标题重复';
   const LOGIN_ERROR        ='账号或密码错误';
   const LOGIN_SUCCESS      ='登录成功';
   const REGISTER_ERROR     ='注册失败';
   const REGISTER_SUCCESS   ='注册成功';
   const INVITE_CODE_ERROR  ='邀请码错误';
   const CODE_ERROR         ='验证码错误';
   const MOBILE_EXISTS      ='该手机已注册';
   const PROHIBIT_LOGIN     ='账号已禁用,请联系客服处理';
   const NOT_CHANGE_CONTENT ='没有需要修改内容';
   const INPUT_CODE         ='请输入验证码';
   const STOP_ARTICLE       ='您已被禁止发帖，或发帖内容违规，请联系版主处理';
   const OLD_MOBILE_EXPIRED ='旧手机验证过期,请重新验证';
   const UPDATE_FAIL        ='更新失败';
   const UPDATE_SUCCESS     ='更新成功';
   const USERNAME_CHANGE_ERR='更新失败,真实姓名只能修改一次';
   const MOBILE_NOT_EXISTS  ='手机号不存在';
   const INPUT_MOBILE       ='请输入手机号';
   const OLD_PASSWORD_NULL  ='旧密码不能为空';
   const NEW_PASS_EQ_OLD_PASS   ='新密码不能与旧密码相同';
   const OLD_PASSWORD_FAILED='旧密码密码错误';
   const OLD_JY_PASS_NULL   ='旧交易密码不能为空';
   const OLD_JY_PASS_FAILED ='旧交易密码密码错误';
   const UPLOAD_FILE_SUCCESS='图片上传成功';
   const UPLOAD_TYPE_ERROR  ='只允许上传.jpg .png .jpeg';
   const CONFIRM_PASSWORD   ='确认密码不能为空';
   const ADD_DATA_SUCCESS   ='创建成功';
   const ADD_DATA_FAILED    ='创建失败';
   const ADD_ARTICLE_SUCCESS='发贴成功';
   const ADD_ARTICLE_FAILED ='发帖失败';
    const COMMENT_SUCCESS='留言成功';
    const COMMENT_FAILED ='留言失败';
   const BANK_ADD_ERROR     ='银行卡最多只能添加5张';
   const JY_PASS_ERROR      ='交易密码错误';
   const USER_LOGIN_IN      ='用户已登录';
   const GOODLIKE               ='成功';
   const AGAIN            ='请勿重复操作';
   const NOTGOOD            ='您已赞过';
   const NOTHATE           ='您已踩过';
   const DELETE_SUCCESS     ='删除成功';
   const DELETE_FAILED      ='删除失败';
   const DELETE_DATA_NOT_NULL='要删除的数据不存在或已删除';
   const DATA_NULL          ='没有查找到数此数据';
   const COLLECTION_STORE_FAIL   ='该贴已收藏,请勿重复收藏';
   const COLLECTION_STORE_SUCCESS='收藏成功';
   const ARTICLE_ADD_SUCCESS=  '发帖成功';
    const ARTICLE_FAIL_SUCCESS='发帖失败';
   const PARAM_FAIL          ='参数错误';
    /**
     * 公共邮箱发送验证
     * @param $email
     */
    protected  function toSendMail($email){
        $title='LUOQI科技';
        $code=rand(00000,29999);
        Redis::setex($email,60,$code);
        $content="您的验证码为：".$code.'有效时间为60秒,请尽快使用！';
// 发送内容
//       Mail::send('index.index',['name'=>'value'],function ($message) use ($name,$content){
//       $message->to($name)->subject($content);
//       });
        Mail::raw($content, function ($message) use($email,$title) {
            $message->to($email)->subject($title);
        });
    }


    /**
     * PHP判断当前协议是否为HTTPS
     */
    public static function is_https() {
        if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
            return true;
        } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }

    /**
     * 随机字符串
     * @param $length 长度
     * @return string
     */
    protected function str_rand($length){
        //字符组合
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($str)-1;
        $randstr = '';
        for ($i=0;$i<$length;$i++) {
            $num=mt_rand(0,$len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }

    /**
     * 加盐
     * @param $str
     * @return string
     */
    protected function enPass($str){
        return base64_encode($str).$this->salt;
    }

    /**
     * 解盐
     * @param $str
     * @return false|string
     */
    protected function dePass($str){
        $de=base64_decode($str);
        $len=strlen($de);
       return substr($de,0,$len - strlen($this->salt));
    }

    protected function checkMail($code,$email){
        if($code == Redis::get($email)) return true;
        return false;
    }


    /**
     * 获取参数
     * @param Request $input
     * @return array|string[]
     */
    protected function getParams(Request $input){
        $params=$input->only([
            'bank_image',
            'bank_name',
            'username',
            'key',
            'password',
            'birthday',
            'mobile',
            'code',
            'captcha',
            'avatar',
            'name',
            'qq',
            'wx',
            'alipay',
            'old_password',
            'invite',
            'bank_account',
            'type',
            'uid',
            'state',
            'bank_address',
            'remarks',
            'detail',
            'mark',
            'grade',
            'alipay_type',
            'nickname',
            'content',
            'title',
            'online'
        ]);
        return array_map(function ($value) {
            if (is_null($value)) {
                return '';
            }
            return $value;
        }, $params);
    }
    /**
     * 请求成功
     * @param array $data
     * @param string $msg
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function _success($data=[],$msg=self::REQUEST_SUCCESS,$code=CodeStatus::SUCCESS_CODE){
     return response()->json(['data'=>$data,'message'=>$msg,'code'=>$code]);
   }

    /**
     * 错误
     * @param string $msg
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function _error($msg=self::REQUEST_FAIL,$code=CodeStatus::FAIL_CODE){
       return response()->json(['data'=>[],'message'=>$msg,'code'=>$code]);

   }

    /**
     * 操作成功
     * @param $msg
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function OperationSuccess($msg=self::OPERATION_SUCCESS,$code=CodeStatus::SUCCESS_CODE){
       return response()->json(['data'=>[],'message'=>$msg,'code'=>$code]);
   }

   /**
    * 表单验证
    */
    protected function BaseValidator(Request $request,array $param,array $errCode,&$errors){
        try{
            $this->validate($request,$param,$errCode);
            return true;
        }catch(ValidationException $e){
            $errors=$e->validator->getMessageBag()->first();
            return false;
        }
   }

    /**
     *写入文件
     */
     public function writeJson(String $name="诛仙",String $image="http://www.luoqi.com/user/default.jpg"){

            $filename = public_path() . "\\data\\test.json";
            $file=file_get_contents($filename, true);
            if($file){
            $data=json_decode($file,true);
            $maxKey=count($data['data']);
            krsort($data["data"]);
            // print_r(iconv_get_encoding()); //得到当前页面编码信息
            $setid=$data["data"][$maxKey - 1]["setid"] + 1;
            ksort($data["data"]);
            $arr=[];
            $arr["num"]=array("http://www.luoqi.com/video/".$name.".mp4");
            $arr["name"]=$name;
            $arr["image"]=$image;
            $arr["setid"]=$setid;
           }else{
            $data=[];
            $data["code"]=200;
            $maxKey=0;
            $setid=1;
            $arr=[];
            $arr[$maxKey]["num"]=array("http://www.luoqi.com/video/".$name.".mp4");
            $arr[$maxKey]["name"]=$name;
            $arr[$maxKey]["image"]=$image;
            $arr[$maxKey]["setid"]=$setid;
           }

            isset($data["data"]) ? array_push($data["data"],$arr) : $data["data"]=$arr;
            $json_data=json_encode($data);
            return file_put_contents($filename,$json_data);
     }


    protected function curlJson($url = '', $data = array(),$headers = array()) {
        array_push($headers,"Content-Type: application/json;charset=utf-8");
        $data=json_encode($data);
        $postUrl = $url;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    // 信任任何证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);        // 表示不检查证书
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        $data = curl_exec($ch);//运行curl
        if (!$data)
        {
            return curl_error($ch);
        }
        curl_close($ch);

        return $data;
    }


    protected function curlPost($data, $url, $header = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch);
        if (!$info)
        {
            return curl_error($ch);
        }
        curl_close($ch);
        return $info;
    }


    /**
     * @desc 发送短信
     * @param $mobile
     * @return mixed
     */
    protected function sendMsg($mobile)
    {
        $code = Cache::get($mobile);
        if(!$code) {
            $code = rand(1000, 9999);
            Cache::add($mobile, $code, 60); //60
        }
        $content="【柒柒科技】您的验证码：{$code} 有效期60秒请尽快使用。";
        $account=config('site.account');
        $url=config('site.msg_url');
        $password=strtoupper(md5(config('site.msg_secret')));
        $body=array(
            'action'=>'send',
            'userid'=>rand(1,999),
            'account'=>$account,
            'password'=>$password,
            'mobile'=>$mobile,
            'extno'=>'',
            'content'=>$content,
            'sendtime'=>'',
        );
       return json_decode($this->curlPost($body,$url));
    }



    /**
     * @param $file 文件
     * @param string $file_path 次文件路径
     * @return array
     * @throws \Exception
     */
    protected function OneUploadFile($file,$file_path='user')
    {
        try {
            $allowed_extensions = ["png", "jpg", "jpeg"];
            $end = $file->getClientOriginalExtension();
            if ($end && !in_array($end, $allowed_extensions)) return array('code'=>400,'msg'=>self::UPLOAD_TYPE_ERROR);
            $time = time();
//            $path = public_path('user')."/{$file_path}";
            $path=$file_path;
            $fileName = "{$path}/{$time}_{$this->str_rand(10)}.{$end}";
            $re = $file->move($path, $fileName);
//            $fileName = "/{$file_path}/{$re->getFilename()}";
            $fileName=$re->getFilename();
            return  array('code'=>200,'msg'=>self::UPLOAD_FILE_SUCCESS,'name'=>$fileName);
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * 去除文本中，存在跨域攻击的脚本
     * @param $html
     * @param $isEscape, 是否做 htmlspecialchars
     * @return mixed|string
     */
    protected  function replaceDox1($html, $isEscape=false)
    {
        $html = htmlspecialchars_decode($html);
        preg_match_all("/\<([^\<]+)\>/is", $html, $ms);
        $searches[]  = '<';
        $replaces[] = '&lt;';
        $searches[]  = '>';
        $replaces[] = '&gt;';
        if ($ms[1]) {
            $allowTags = 'iframe|video|attach|img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote|strike|pre|code|embed';
            $ms[1]     = array_unique($ms[1]);
            foreach ($ms[1] as $value) {
                $searches[] = "&lt;" . $value . "&gt;";

                $value = str_replace('&amp;', '_uch_tmp_str_', $value);
                $value = htmlspecialchars($value);
                $value = str_replace('_uch_tmp_str_', '&amp;', $value);

                $value    = str_replace(array('\\', '/*'), array('.', '/.'), $value);
                $skipKeys = array('>','<','onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate',
                    'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange',
                    'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick',
                    'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate',
                    'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
                    'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel',
                    'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart',
                    'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop',
                    'onsubmit', 'onunload', 'javascript', 'script', 'eval', 'behaviour', 'expression','select','order','from','table','desc','delete','char','limit',
                    'update','sum','avg','count','modify','change','drop','trnlcate','where');
                $skipStr = implode('|', $skipKeys);
                $value   = preg_replace(array("/($skipStr)/i"), '.', $value);
                if (!preg_match("/^[\/|\s]?($allowTags)(\s+|$)/is", $value)) {
                    $value = '';
                }
                $replaces[] = empty($value) ? '' : "<" . str_replace('&quot;', '"', $value) . ">";
            }
        }
        $html = str_replace($searches, $replaces, $html);
        if ($isEscape) $html = htmlspecialchars($html);
        return $html;
    }

    /**
     * @todo 敏感词过滤，返回结果
     * @param string $string 要过滤的内容
     */
    protected  function replaceDox($string){
        $stringAfter = $string;  //替换后的内容
        $skipKeys = array('日','操','草','妈','onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate',
            'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange',
            'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick',
            'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate',
            'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
            'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel',
            'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart',
            'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop',
            'onsubmit', 'onunload', 'javascript', 'script', 'eval', 'behaviour', 'expression','select','order','from','table','desc','delete','char','limit',
            'update','sum','avg','count','modify','change','drop','trancate','set','微','q','加','支');
        $str=implode("|",$skipKeys);
        $pattern = "/".$str ."/i"; //定义正则表达式
        if(preg_match_all($pattern, $string, $matches)){ //匹配到了结果
            $patternList = $matches[0];  //匹配到的数组
            $replaceArray = array_combine($patternList,array_fill(0,count($patternList),'*')); //把匹配到的数组进行合并，替换使用
            $stringAfter = strtr($string, $replaceArray); //结果替换
        }
        return $stringAfter;
    }

    /**
     * @desc 删除图片
     * @param $file 文件名称
     */
    protected function deleteFile(string $file)
    {
        try {
            if(empty($file)) return true;
            $path = public_path('uploads') . '\\' . $file;
            unlink($path);
            return true;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * 检查网址是否带http or https头
     * @param $url
     */
    public static function isUrlHeader($url){
        $preg = "/^http(s)?:\\/\\/.+/";
        if(preg_match($preg,$url)) return true;
        return false;
    }

    /**
     * 随机生成姓名
     * @return string
     */
   protected function generateName(){
        $arrXing = $this->getXingList();
        $numbXing = count($arrXing);
        $arrMing = $this->getMingList();
        $numbMing =  count($arrMing);
        $Xing = $arrXing[mt_rand(0,$numbXing-1)];
        $Ming = $arrMing[mt_rand(0,$numbMing-1)].$arrMing[mt_rand(0,$numbMing-1)];

        $name = $Xing.$Ming;

        return $name;

    }


    /**
     * 获取姓氏
     * @return string[]
     */
    private function getXingList(){
        $arrXing=array('赵','钱','孙','李','周','吴','郑','王','冯','陈','褚','卫','蒋','沈','韩','杨','朱','秦','尤','许','何','吕','施','张','孔','曹','严','华','金','魏','陶','姜','戚','谢','邹',
            '喻','柏','水','窦','章','云','苏','潘','葛','奚','范','彭','郎','鲁','韦','昌','马','苗','凤','花','方','任','袁','柳','鲍','史','唐','费','薛','雷','贺','倪','汤','滕','殷','罗',
            '毕','郝','安','常','傅','卞','齐','元','顾','孟','平','黄','穆','萧','尹','姚','邵','湛','汪','祁','毛','狄','米','伏','成','戴','谈','宋','茅','庞','熊','纪','舒','屈','项','祝',
            '董','梁','杜','阮','蓝','闵','季','贾','路','娄','江','童','颜','郭','梅','盛','林','钟','徐','邱','骆','高','夏','蔡','田','樊','胡','凌','霍','虞','万','支','柯','管','卢','莫',
            '柯','房','裘','缪','解','应','宗','丁','宣','邓','单','杭','洪','包','诸','左','石','崔','吉','龚','程','嵇','邢','裴','陆','荣','翁','荀','于','惠','甄','曲','封','储','仲','伊',
            '宁','仇','甘','武','符','刘','景','詹','龙','叶','幸','司','黎','溥','印','怀','蒲','邰','从','索','赖','卓','屠','池','乔','胥','闻','莘','党','翟','谭','贡','劳','逄','姬','申',
            '扶','堵','冉','宰','雍','桑','寿','通','燕','浦','尚','农','温','别','庄','晏','柴','瞿','阎','连','习','容','向','古','易','廖','庾','终','步','都','耿','满','弘','匡','国','文',
            '寇','广','禄','阙','东','欧','利','师','巩','聂','关','荆','司马','上官','欧阳','夏侯','诸葛','闻人','东方','赫连','皇甫','尉迟','公羊','澹台','公冶','宗政','濮阳','淳于','单于','太叔',
            '申屠','公孙','仲孙','轩辕','令狐','徐离','宇文','长孙','慕容','司徒','司空');
        return $arrXing;

    }

    /**
     * 获取名字
     * @return string[]
     */
    public function getMingList(){
        $arrMing=array('伟','刚','勇','毅','俊','峰','强','军','平','保','东','文','辉','力','明','永','健','世','广','志','义','兴','良','海','山','仁','波','宁','贵','福','生','龙','元','全'
        ,'国','胜','学','祥','才','发','武','新','利','清','飞','彬','富','顺','信','子','杰','涛','昌','成','康','星','光','天','达','安','岩','中','茂','进','林','有','坚','和','彪','博','诚'
        ,'先','敬','震','振','壮','会','思','群','豪','心','邦','承','乐','绍','功','松','善','厚','庆','磊','民','友','裕','河','哲','江','超','浩','亮','政','谦','亨','奇','固','之','轮','翰'
        ,'朗','伯','宏','言','若','鸣','朋','斌','梁','栋','维','启','克','伦','翔','旭','鹏','泽','晨','辰','士','以','建','家','致','树','炎','德','行','时','泰','盛','雄','琛','钧','冠','策'
        ,'腾','楠','榕','风','航','弘','秀','娟','英','华','慧','巧','美','娜','静','淑','惠','珠','翠','雅','芝','玉','萍','红','娥','玲','芬','芳','燕','彩','春','菊','兰','凤','洁','梅','琳'
        ,'素','云','莲','真','环','雪','荣','爱','妹','霞','香','月','莺','媛','艳','瑞','凡','佳','嘉','琼','勤','珍','贞','莉','桂','娣','叶','璧','璐','娅','琦','晶','妍','茜','秋','珊','莎'
        ,'锦','黛','青','倩','婷','姣','婉','娴','瑾','颖','露','瑶','怡','婵','雁','蓓','纨','仪','荷','丹','蓉','眉','君','琴','蕊','薇','菁','梦','岚','苑','婕','馨','瑗','琰','韵','融','园'
        ,'艺','咏','卿','聪','澜','纯','毓','悦','昭','冰','爽','琬','茗','羽','希','欣','飘','育','滢','馥','筠','柔','竹','霭','凝','晓','欢','霄','枫','芸','菲','寒','伊','亚','宜','可','姬'
        ,'舒','影','荔','枝','丽','阳','妮','宝','贝','初','程','梵','罡','恒','鸿','桦','骅','剑','娇','纪','宽','苛','灵','玛','媚','琪','晴','容','睿','烁','堂','唯','威','韦','雯','苇','萱'
        ,'阅','彦','宇','雨','洋','忠','宗','曼','紫','逸','贤','蝶','菡','绿','蓝','儿','翠','烟');
        return $arrMing;
    }

    /**
     * @param int|null $var
     * @return string
     */
   protected function nicknameRand(int $var = null)
    {
        $tou=array('快乐','冷静','醉熏','潇洒','糊涂','积极','冷酷','深情','粗暴','温柔','可爱','愉快','义气','认真','威武','帅气','传统','潇洒','漂亮','自然','专一','听话','昏睡','狂野','等待','搞怪','幽默','魁梧','活泼','开心','高兴','超帅','留胡子','坦率','直率','轻松','痴情','完美','精明','无聊','有魅力','丰富','繁荣','饱满','炙热','暴躁','碧蓝','俊逸','英勇','健忘','故意','无心','土豪','朴实','兴奋','幸福','淡定','不安','阔达','孤独','独特','疯狂','时尚','落后','风趣','忧伤','大胆','爱笑','矮小','健康','合适','玩命','沉默','斯文','香蕉','苹果','鲤鱼','鳗鱼','任性','细心','粗心','大意','甜甜','酷酷','健壮','英俊','霸气','阳光','默默','大力','孝顺','忧虑','着急','紧张','善良','凶狠','害怕','重要','危机','欢喜','欣慰','满意','跳跃','诚心','称心','如意','怡然','娇气','无奈','无语','激动','愤怒','美好','感动','激情','激昂','震动','虚拟','超级','寒冷','精明','明理','犹豫','忧郁','寂寞','奋斗','勤奋','现代','过时','稳重','热情','含蓄','开放','无辜','多情','纯真','拉长','热心','从容','体贴','风中','曾经','追寻','儒雅','优雅','开朗','外向','内向','清爽','文艺','长情','平常','单身','伶俐','高大','懦弱','柔弱','爱笑','乐观','耍酷','酷炫','神勇','年轻','唠叨','瘦瘦','无情','包容','顺心','畅快','舒适','靓丽','负责','背后','简单','谦让','彩色','缥缈','欢呼','生动','复杂','慈祥','仁爱','魔幻','虚幻','淡然','受伤','雪白','高高','糟糕','顺利','闪闪','羞涩','缓慢','迅速','优秀','聪明','含糊','俏皮','淡淡','坚强','平淡','欣喜','能干','灵巧','友好','机智','机灵','正直','谨慎','俭朴','殷勤','虚心','辛勤','自觉','无私','无限','踏实','老实','现实','可靠','务实','拼搏','个性','粗犷','活力','成就','勤劳','单纯','落寞','朴素','悲凉','忧心','洁净','清秀','自由','小巧','单薄','贪玩','刻苦','干净','壮观','和谐','文静','调皮','害羞','安详','自信','端庄','坚定','美满','舒心','温暖','专注','勤恳','美丽','腼腆','优美','甜美','甜蜜','整齐','动人','典雅','尊敬','舒服','妩媚','秀丽','喜悦','甜美','彪壮','强健','大方','俊秀','聪慧','迷人','陶醉','悦耳','动听','明亮','结实','魁梧','标致','清脆','敏感','光亮','大气','老迟到','知性','冷傲','呆萌','野性','隐形','笑点低','微笑','笨笨','难过','沉静','火星上','失眠','安静','纯情','要减肥','迷路','烂漫','哭泣','贤惠','苗条','温婉','发嗲','会撒娇','贪玩','执着','眯眯眼','花痴','想人陪','眼睛大','高贵','傲娇','心灵美','爱撒娇','细腻','天真','怕黑','感性','飘逸','怕孤独','忐忑','高挑','傻傻','冷艳','爱听歌','还单身','怕孤单','懵懂');
        $do = array("的","爱","","与","给","扯","和","用","方","打","就","迎","向","踢","笑","闻","有","等于","保卫","演变");
        $wei=array('嚓茶','凉面','便当','毛豆','花生','可乐','灯泡','哈密瓜','野狼','背包','眼神','缘分','雪碧','人生','牛排','蚂蚁','飞鸟','灰狼','斑马','汉堡','悟空','巨人','绿茶','自行车','保温杯','大碗','墨镜','魔镜','煎饼','月饼','月亮','星星','芝麻','啤酒','玫瑰','大叔','小伙','哈密瓜，数据线','太阳','树叶','芹菜','黄蜂','蜜粉','蜜蜂','信封','西装','外套','裙子','大象','猫咪','母鸡','路灯','蓝天','白云','星月','彩虹','微笑','摩托','板栗','高山','大地','大树','电灯胆','砖头','楼房','水池','鸡翅','蜻蜓','红牛','咖啡','机器猫','枕头','大船','诺言','钢笔','刺猬','天空','飞机','大炮','冬天','洋葱','春天','夏天','秋天','冬日','航空','毛衣','豌豆','黑米','玉米','眼睛','老鼠','白羊','帅哥','美女','季节','鲜花','服饰','裙子','白开水','秀发','大山','火车','汽车','歌曲','舞蹈','老师','导师','方盒','大米','麦片','水杯','水壶','手套','鞋子','自行车','鼠标','手机','电脑','书本','奇迹','身影','香烟','夕阳','台灯','宝贝','未来','皮带','钥匙','心锁','故事','花瓣','滑板','画笔','画板','学姐','店员','电源','饼干','宝马','过客','大白','时光','石头','钻石','河马','犀牛','西牛','绿草','抽屉','柜子','往事','寒风','路人','橘子','耳机','鸵鸟','朋友','苗条','铅笔','钢笔','硬币','热狗','大侠','御姐','萝莉','毛巾','期待','盼望','白昼','黑夜','大门','黑裤','钢铁侠','哑铃','板凳','枫叶','荷花','乌龟','仙人掌','衬衫','大神','草丛','早晨','心情','茉莉','流沙','蜗牛','战斗机','冥王星','猎豹','棒球','篮球','乐曲','电话','网络','世界','中心','鱼','鸡','狗','老虎','鸭子','雨','羽毛','翅膀','外套','火','丝袜','书包','钢笔','冷风','八宝粥','烤鸡','大雁','音响','招牌','胡萝卜','冰棍','帽子','菠萝','蛋挞','香水','泥猴桃','吐司','溪流','黄豆','樱桃','小鸽子','小蝴蝶','爆米花','花卷','小鸭子','小海豚','日记本','小熊猫','小懒猪','小懒虫','荔枝','镜子','曲奇','金针菇','小松鼠','小虾米','酒窝','紫菜','金鱼','柚子','果汁','百褶裙','项链','帆布鞋','火龙果','奇异果','煎蛋','唇彩','小土豆','高跟鞋','戒指','雪糕','睫毛','铃铛','手链','香氛','红酒','月光','酸奶','银耳汤','咖啡豆','小蜜蜂','小蚂蚁','蜡烛','棉花糖','向日葵','水蜜桃','小蝴蝶','小刺猬','小丸子','指甲油','康乃馨','糖豆','薯片','口红','超短裙','乌冬面','冰淇淋','棒棒糖','长颈鹿','豆芽','发箍','发卡','发夹','发带','铃铛','小馒头','小笼包','小甜瓜','冬瓜','香菇','小兔子','含羞草','短靴','睫毛膏','小蘑菇','跳跳糖','小白菜','草莓','柠檬','月饼','百合','纸鹤','小天鹅','云朵','芒果','面包','海燕','小猫咪','龙猫','唇膏','鞋垫','羊','黑猫','白猫','万宝路','金毛','山水','音响','尊云','西安');
        $tou_num=rand(0,331);
        $do_num=rand(0,19);
        $wei_num=rand(0,327);
        $type = rand(0,1);
        if($type==0){
            return $tou[$tou_num].$do[$do_num].$wei[$wei_num];
        }else{
            return $wei[$wei_num].$tou[$tou_num];
        }
    }


}
