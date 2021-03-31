<?php


namespace App\Http\Model;


use App\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Model;

/**
 * @留言模型
 * Class Opinion
 * @package App\Http\Model
 */
class Opinion extends Model
{

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nickname','avatar','content','pid'
    ];
    protected $hidden=['update_time'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(){
        return $this->hasOne(User::class,'id','uid')->select('nickname','avatar');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(){
        return $this->hasMany(self::class,'pid','id')->select('avatar','pid','content','nickname','create_time');
    }

    /**
     * @return mixed|string
     */
    public function getAvatarAttribute(){
        return BaseController::isUrlHeader($this->attributes['avatar']) ? $this->attributes['avatar'] :  (BaseController::is_https() ? 'https://':'http://').($_SERVER["HTTP_HOST"] ?? $_SERVER['SERVER_ADDR'] ).'/user/'. $this->attributes['avatar'];
    }


}
