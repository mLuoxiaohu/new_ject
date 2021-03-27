<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

/***
 * 文章模型
 * Class Article
 * @package App\Http\Model
 */
class Article extends Model
{

    protected $table='user_article';
    protected $dateFormat = 'U';  #是否为时间戳

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $fillable = [
        'title','uid','content','hate','like','admin','create_time',
        'update_time','browse','top','type','state'
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
        return $this->hasOne(User:: class,'id','uid')->select('id','nickname','avatar');
    }

    /**
     * 点赞模型
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function good(){
        return $this->hasMany(Good:: class,'article_id','id')->select('id','article_id');
    }

    /**
     * 评论列表
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comment(){
        return $this->hasMany(Comment:: class,'article_id','id')
            ->select('uid','article_id','content','create_time');
    }

    public function store(){
        return $this->hasMany(UserArticleStore:: class,'article_id','id');
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
