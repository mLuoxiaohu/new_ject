<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;


/**
 * 收藏模型
 * Class Store
 * @package App\Http\Model
 */
class Store extends Model
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $fillable=['uid','lottery_id'];

    protected $hidden=['update_time'];

    public function kind(){
        return $this->hasOne(Kind::class,'id','lottery_id');
    }
}
