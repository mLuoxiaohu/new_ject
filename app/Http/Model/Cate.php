<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

class Cate extends Model
{
   protected $table='cate';


   protected $hidden=['create_time','update_time'];


   public function kind(){
       return $this->hasMany(Kind::class,'cid','cate_id');
   }
}
