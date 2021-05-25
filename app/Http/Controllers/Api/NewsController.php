<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\BaseController;

class NewsController extends BaseController
{

    public function __construct()
    {

    }




    public function get_news_contents(){
       $content= file_get_contents(env('NEWS_URL').'?key='.env('NEWS_KEY').'&num=20');
        $decode = is_array($content) ? $content : json_decode($content, true);
        if (empty($decode) || $decode['code'] != 200) return $this->_error($decode['msg']);
        return $this->_success($decode['newslist']);
    }

}
