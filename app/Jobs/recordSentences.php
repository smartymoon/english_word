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

class recordSentences implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $word;
    private $client;
    private $dom;
    /**
     * Create a new job instance.
     *
     * @param $word
     */
    public function __construct($word)
    {
        $this->word = $word->word;
    }

    public function handle()
    {
        /*
        $proxy = (new MoguProxy)->getProxy();
        $this->client = new Client([
            'proxy' =>  $proxy['ip'] . ':' . $proxy['port'],
            'timeout' => 5
        ]);
        */
        $this->client = new Client();
        $this->dom = new HTML5DOMDocument();
        $this->getContent('http://youdao.com/w/'. $this->word .'/#keyfrom=dict2.top');
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
        $sentences = $this->getSentences();
        $record = Word::whereWord($this->word)->first();
        $record->sentences = $sentences;
        $record->save();
    }

    private function getSentences()
    {
        dump($this->word);
        try {
            $bi = $this->dom->querySelector('#bilingual');
            if ($bi) {
                $lis =  $bi->querySelectorAll('li');
            } else {
                abort(200);
            }
            $sentences = [];

            foreach($lis as $li) {
                $english = '';
                $chinese = '';
                $ps = $li->querySelectorAll('p');

                $spans = $ps->item(0)->querySelectorAll('span');
                foreach ($spans as $span) {
                    $english .= $span->innerHTML;
                }
                $a = $ps->item(0)->querySelector('a');
                $url = 'http://dict.youdao.com/dictvoice?audio=' . $a->getAttribute('data-rel');
                $spans = $ps->item(1)->querySelectorAll('span');
                foreach ($spans as $span) {
                    $chinese .= $span->innerHTML;
                }
                $sentences[] = compact('english','chinese','url');
            }
            return $sentences;
        }
        catch (\Exception $e) {
            abort('200');
        }
    }
}
