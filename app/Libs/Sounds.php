<?php
/**
 *  用于播放声音的
 * Created by PhpStorm.
 * User: lee
 * Date: 2018/8/26
 * Time: 18:20
 */

namespace App\Libs;


use GuzzleHttp\Client;

class Sounds
{
    public function play($url)
    {
        // 拿来声音，再放回去
        $client = new Client();
        $response = $client->get($url);
        $content = $response->getBody()->getContents();
        return $content;
    }
}