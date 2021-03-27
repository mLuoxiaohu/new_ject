<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;
/**
 * 用户文章收藏
 * Class UserArticleStore
 * @package App\Http\Model
 */
class UserArticleStore extends Model
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    protected $table='user_article_store';
    protected $fillable=['article_id','uid','is_delete'];

    protected $hidden=['is_delete'];

    public function article(){
        return $this->hasOne(Article:: class,'id','article_id');
    }


}
