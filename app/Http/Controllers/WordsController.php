<?php

namespace App\Http\Controllers;

use App\Models\Word;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use IvoPetkov\HTML5DOMDocument;

class WordsController extends Controller
{
    //
    public function lookUp(Request $request)
    {
        $word = trim(substr($request->input('text'),1));
        if (!$word) {
            return [
                'text' => '发送空消息,不好'
            ];
        }

        // api 通讯
        try {
            $client = new Client();
            $response = $client->get('http://dict.cn/' . $word);
            $content = $response->getBody()->getContents();
            $dom = new HTML5DOMDocument();
            $dom->loadHTML($content);
            if ($dom->querySelector('.ifufind')) {
                return [
                    'text' => '没有查找到该单词 '
                ];
            }
            $frequentElement = $dom->querySelector('.level-title');

            $frequent = $frequentElement ? $frequentElement->getAttribute('level') : '没有频率标识';

            $basic = $example = '';
            // basic
            $basicElement = $dom->querySelector('#dict-chart-basic');
            if ($basicElement) {
                $basics = json_decode(urldecode($basicElement->getAttribute('data')), true);
                foreach ($basics as $item) {
                    $basic .=  "- {$item['sense']} : {$item['percent']}% \n";
                }
            }

            // example
            $exampleElement = $dom->querySelector('#dict-chart-examples');
            if ($exampleElement) {
                $examples  = json_decode(urldecode($exampleElement->getAttribute('data')), true);
                foreach ($examples as $item) {
                   $example .=  "- {$item['pos']} : {$item['percent']}% \n";
                }
            }

            // 结合返回
            return [
                "text" => $frequent,
                "attachments" => [
                    [
                        "title" => '词意分布',
                        "text" => $basic
                    ],
                    [
                        "title" => '词性分布',
                        "text" => $example
                    ],
                ]
            ];
        } catch (\Exception $e) {
            return [
                'text' => '和 dict.cn 通讯出错!'
            ];
        }
    }

    public function printWords(int $star)
    {
       $words = Word::where('if_grasp', Word::$PASS_CHECK)
           ->whereStar($star)->orderBy('word', 'asc')->get();
       return view('words.print', compact('words'));
    }
}
