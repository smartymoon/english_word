<?php

namespace App\Console\Commands;

use App\Jobs\recordSentences;
use App\Models\Word;
use Illuminate\Console\Command;

class WordsSentence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'word:sentence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入单词的例句';

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
        Word::where('sentences', '')->chunk(10, function($words) {
            foreach ($words as $word) {
                recordSentences::dispatch($word);
            }
        });
    }
}
