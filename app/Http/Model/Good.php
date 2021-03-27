<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    protected $table='good';

    protected $fillable = ['article_id','uid','type'];

}
