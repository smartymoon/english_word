<?php
/**
 * Created by PhpStorm.
 * 用蘑菇代理服务生成临时代理接口，用于爬虫，防止作业过程中IP被禁止
 * User: lee
 * Date: 2018/8/21
 * Time: 9:38
 */

namespace App\Libs;

use \Cache;
use GuzzleHttp\Client;

class MoguProxy
{
    private $api;
    public static $KEY = 'mogu_proxy_key';

    /**
     * MoguProxy constructor.
     */
    public function __construct()
    {
        $this->api = 'http://piping.mogumiao.com/proxy/api/get_ip_al?appKey=fa74dbb188714c4086ee2c3a2e08f193&count=1&expiryDate=0&format=1&newLine=2';
    }

    public function getProxy()
    {
        return Cache::has(self::$KEY) ? Cache::get(self::$KEY) : $this->makeProxy();
    }

    public function makeProxy()
    {
        // 清理缓存，再得新的，再缓存，再返回
        $this->forgetProxy();
        $client = new Client();
        $response = $client->get($this->api);
        $content = $response->getBody()->getContents();
        $res = json_decode($content, true);
        if ($res['code'] === "0") {
            bearyChat('获取代理成功:' . $content);
            Cache::forever(self::$KEY, $res['msg'][0]);
            return $res['msg'][0];
        }
        bearyChat("蘑菇代理获取失败:{$content}");
        abort(500, "蘑菇代理获取失败:{$content}");
    }

    public static function forgetProxy()
    {
        Cache::forget(self::$KEY);
    }
}