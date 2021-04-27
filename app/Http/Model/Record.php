<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $table='record';


    public function kind()
    {
        return $this->hasOne(Kind::class,'id','kid');
    }
}
