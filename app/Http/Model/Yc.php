<?php


namespace App\Http\Model;


use Couchbase\MutationToken;
use Illuminate\Database\Eloquent\Model;

/**
 * 预测
 * Class Yc
 * @package App\Http\Model
 */
class Yc extends Model
{
    protected $table='yc';

    public $states=[1=>'未开奖',2=>'中奖',3=>'未中奖'];

    public function getStateAttribute(){
        return  $this->states[$this->attributes['state']];
    }


    public function cole(){
        return $this->hasOne(Cole::class,'id','type')->select('id','name');
    }
    public function kind(){
        return $this->hasOne(Kind::class,'id','kid')->select('id','name');
    }
}
