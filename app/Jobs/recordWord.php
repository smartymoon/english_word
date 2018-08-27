<?php

namespace App\Jobs;

use App\Libs\MoguProxy;
use App\Models\Word;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use IvoPetkov\HTML5DOMDocument;

class recordWord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $word;
    private $dom;
    private $client;

    /**
     * Create a new job instance.
     *
     * @param $word
     */
    public function __construct($word)
    {
        $this->word = $word;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $proxy = (new MoguProxy)->getProxy();
        $this->client = new Client([
            'proxy' =>  $proxy['ip'] . ':' . $proxy['port'],
            'timeout' => 5
        ]);
        $this->dom = new HTML5DOMDocument();

        $word = $this->word;
        $this->getContent('http://dict.cn/' . $word);
        $attrs = $this->getAttrs();
        $star = $this->getStar();
        $senses = $this->getSenses();
        $phonetics = $this->getPhonetics();
        Word::create(compact('word', 'star', 'senses', 'attrs', 'phonetics'));
    }

    private function getContent($url)
    {
        try {
            $response = $this->client->get($url);
            $content = $response->getBody()->getContents();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            MoguProxy::forgetProxy();
            $message = 'guzzle error:'. $url;
            bearyChat($message);
            abort(500, $message);
        }
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
        $bdos = $this->dom->querySelectorAll("bdo");
        return $bdos->count() == 2 ? $bdos->item(1)->innerHTML : '';
    }
}
