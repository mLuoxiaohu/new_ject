<?php


namespace App\Http\Controllers\Common;


use App\Http\InterfaceIo\Good;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class ClickGood implements Good
{

    public static $redis;
    private static $key="like";
    protected static $user_key=null;
    private static $num=1;

    public function init()
    {

        // TODO: Implement init() method.
    }

    protected static function createKey($uid, $active){
      return self::$user_key=$active.'_'.$uid;
    }

    public static function add($uid, $article,$type)
    {
        self::createKey($uid, $article);
        $val=Redis::get(self::$user_key) ?? false;
        if($val === false)  {
            Redis::setex(self::$user_key,86400,$type);
            Redis::zIncrBy(self::$key,self::$num,"article:".$article);
            return $val;
        }else{
            return $val;
        }

        // TODO: Implement add() method.
    }
    public static function search($uid,$article){

    }

    public function cancel($uid, $article)
    {
        // TODO: Implement dontLove() method.
    }
}
