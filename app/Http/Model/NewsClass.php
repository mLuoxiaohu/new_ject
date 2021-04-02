<?php


namespace App\Http\Model;


use Illuminate\Database\Eloquent\Model;

/**
 * 新闻类型分类
 * Class NewsClass
 * @package App\Http\Model
 */
class NewsClass extends Model
{
    protected $table='newsclass';
    protected $fillable=['name','abbr'];

}
