<?php

namespace App\Console\Commands;

use App\Jobs\recordWord;
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
                if(!Word::where('word', $word)->first()) {
                    recordWord::dispatch($word);
                }
            }
        }
    }

    private function getContent($url)
    {
        $response = $this->client->get($url);
        $content = $response->getBody()->getContents();
        $this->dom->loadHTML($content);
    }
}
