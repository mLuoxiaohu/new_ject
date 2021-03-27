<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{

    const CREATED_AT = 'create_time';
    protected $table='user_log';


    /***
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(){
        return $this->hasOne(User::class,'id','uid')->select('id','nickname');
    }
}
