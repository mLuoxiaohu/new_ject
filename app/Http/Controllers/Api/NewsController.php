<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\BaseController;
use App\Http\Model\News;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsController extends BaseController
{

    public function __construct()
    {

    }


    /**
     * @param News $news
     * @return \Illuminate\Http\JsonResponse
     */
    public static function get_news_contents()
    {
        #http://www.coindog.com/type/jinse/lives
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
        $url = 'http://api.coindog.com/live/list?' . http_build_query($httpParams);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlRes = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($curlRes, true);

        if (empty($json)) return false;
        $data_all = [];
        foreach ($json['list'][0]['lives'] as $k => &$v) {
            preg_match('/【(.*?)】/is', $v['content'], $result);
            $data = array(
                'nid'=>1,
                'news_id' => $v['id'],
                'content' => $v['content'],
                'time' => $v['created_at'],
                'title' => $result[1] ?? '',
            );

            array_push($data_all, $data);
        }
        DB::table((new News())->getTable())->insert($data_all);
    }

}
