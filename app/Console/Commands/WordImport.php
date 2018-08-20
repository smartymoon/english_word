<?php

namespace App\Console\Commands;

use App\Models\Word;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
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
    private $client;
    private $dom;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->dom = new HTML5DOMDocument();

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
                $this->getContent($url);
                $this->getWords();
            }
        }
    }

    private function makePageUrl($star, $group)
    {
        return "http://dict.cn/dir/base{$star}-{$group}.html";
    }

    private function getWords()
    {
        $groups = $this->dom->querySelectorAll('.hub-detail-group');
        foreach ($groups as $group)
        {
            $links = $group->querySelectorAll('a');
            foreach ($links as $link)
            {
                $word = substr($link->getAttribute('href'), 1);
                $wordMeans = $this->getWordMeans($word);
                Word::create($wordMeans);
            }
        }
    }

    private function getWordMeans($word)
    {
        $this->getContent('http://dict.cn/' . $word);

        $attrs = $this->getAttrs();
        $star = $this->getStar();
        $senses = $this->getSenses();
        $phonetics = $this->getPhonetics();

        return compact('word', 'star', 'senses', 'attrs', 'phonetics');
    }

    private function getContent($url)
    {
        $response = $this->client->get($url);
        $content = $response->getBody()->getContents();
        $this->dom->loadHTML($content);
    }

    private function getSenses()
    {
        $senses = [];
        $basicElement = $this->dom->querySelector('#dict-chart-basic');
        if ($basicElement) {
            $basics = json_decode(urldecode($basicElement->getAttribute('data')), true);
            // sense
            foreach ($basics as $item) {
                array_push($senses, "{$item['percent']}% {$item['sense']}");
            }
        }
        return array_slice($senses, 0, 3);
    }

    private function getAttrs()
    {
        $attrs = [];
        $exampleElement = $this->dom->querySelector('#dict-chart-examples');
        if ($exampleElement) {
            $examples  = json_decode(urldecode($exampleElement->getAttribute('data')), true);
            // pos
            foreach ($examples as $item) {
                array_push($attrs, "{$item['percent']}% {$item['pos']}");
            }
        }
        return array_slice($attrs, 0, 3);
    }

    private function getStar()
    {
        $frequentElement = $this->dom->querySelector('.level-title');
        $level = $frequentElement->getAttribute('level');
        preg_match('/(\d)星/', $level, $res);
        return $res[1];
    }

    private function getPhonetics()
    {
        return $this->dom->querySelectorAll("bdo")->item(1)->innerHTML;
    }
}
