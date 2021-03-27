<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

/**
 * 评论模型
 * Class Comment
 * @package App\Http\Model
 */
class Comment extends Model
{
    protected $dateFormat = 'U';  #是否为时间戳

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $table='comment';
    protected $fillable = [
       'article_id','uid','content','admin','level_uid','state'
    ];

    private $states=[1=>'待审核', 2=>'审核通过', 3=>'未通过审核'];

    protected $hidden=['update_time'];


    public function getStateAttribute(){
        return $this->states[$this->attributes['state']];
    }

    /**
     * 用户模型
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function user(){
        return $this->hasOne(User:: class,'id','uid')->select('state','id','nickname','avatar');
    }

    /**
     * 帖子内容
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function article(){
        return $this->hasOne(Article:: class,'id','article_id')->select('id','title');
    }

    public function getUpdateTimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['update_time']);
    }

    public function getCreateTimeAttribute()
    {
        return date('Y-m-d H:i:s', $this->attributes['create_time']);
    }
}
