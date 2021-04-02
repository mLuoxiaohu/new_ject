<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{


    protected $fillable=['cover','url','state'];
    protected $hidden=['update_time'];

}
