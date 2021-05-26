<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\BaseController;
use App\Http\Model\News;
use Illuminate\Support\Facades\Log;

class NewsController extends BaseController
{

    public function __construct()
    {

    }


    public function get_news_contents(News $news)
    {
        $accessKey = 'a15badd5fd7418f6ad49629792d4a51b';
        $secretKey = '5687d270949a5acc';
        $httpParams = array(
            'access_key' => $accessKey,
            'date' => time()
        );

        $signParams = array_merge($httpParams, array('secret_key' => $secretKey));

        ksort($signParams);
        $signString = http_build_query($signParams);

        $httpParams['sign'] = strtolower(md5($signString));

        $url = 'http://api.coindog.com/topic/list?' . http_build_query($httpParams);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $curlRes = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($curlRes, true);
        return $this->_success($json);
    }

}
