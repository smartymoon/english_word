<?php

namespace App\Console\Commands;

use App\Models\Word;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use IvoPetkov\HTML5DOMDocument;

class WordImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'word:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import words from dict.cn';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $groups = [
            5 => [0,1,2,3,4],
            4 => [0,1,2,3,4,5,6],
            3 => [0,1,2,3,4,5,6,7,8,9],
            2 => [0,1,2,3,4,5,6,7,8,9,10,11,12,13],
        ];

        foreach ($groups as $star => $teams)
        {
            foreach($teams as $team) {
                $url = $this->makePageUrl($star, $team);
                $dom = $this->getContent($url);
                $this->getWords($dom);
            }
        }
    }

    private function makePageUrl($star, $group)
    {
        return "http://dict.cn/dir/base{$star}-{$group}.html";
    }

    private function getWords($dom)
    {

        $groups = $dom->querySelectorAll('.hub-detail-group');
        foreach ($groups as $group)
        {
            $links = $group->querySelectorAll('a');
            foreach ($links as $link)
            {
                $word = substr($link->getAttribute('href'), 1);
                $wordMeans = $this->getWordMeans($word);
                Word::create($wordMeans);
            }
            sleep(2);
        }
    }

    private function getWordMeans($word)
    {
        $dom = $this->getContent('http://dict.cn/' . $word);

        $attrs = $this->getAttrs($dom);
        $star = $this->getStar($dom);
        $senses = $this->getSenses($dom);
        $phonetics = $this->getPhonetics($dom);

        return compact('word', 'star', 'senses', 'attrs', 'phonetics');
    }

    private function getContent($url)
    {
        $client = new Client();
        $response = $client->get($url);
        $content = $response->getBody()->getContents();
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($content);
        return $dom;
    }

    private function getSenses(HTML5DOMDocument $dom)
    {
        $senses = [];
        $basicElement = $dom->querySelector('#dict-chart-basic');
        if ($basicElement) {
            $basics = json_decode(urldecode($basicElement->getAttribute('data')), true);
            // sense
            foreach ($basics as $item) {
                array_push($senses, "{$item['percent']}% {$item['sense']}");
            }
        }
        return array_slice($senses, 0, 3);
    }

    private function getAttrs(HTML5DOMDocument $dom)
    {
        $attrs = [];
        $exampleElement = $dom->querySelector('#dict-chart-examples');
        if ($exampleElement) {
            $examples  = json_decode(urldecode($exampleElement->getAttribute('data')), true);
            // pos
            foreach ($examples as $item) {
                array_push($attrs, "{$item['percent']}% {$item['pos']}");
            }
        }
        return array_slice($attrs, 0, 3);
    }

    private function getStar(HTML5DOMDocument $dom)
    {
        $frequentElement = $dom->querySelector('.level-title');
        $level = $frequentElement->getAttribute('level');
        preg_match('/(\d)星/', $level, $res);
        return $res[1];
    }

    private function getPhonetics(HTML5DOMDocument $dom)
    {
        return $dom->querySelectorAll("bdo")->item(1)->innerHTML;
    }
}
