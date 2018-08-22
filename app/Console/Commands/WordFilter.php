<?php

namespace App\Console\Commands;

use App\Models\Word;
use Illuminate\Console\Command;

class WordFilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'word:filter {star=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'filter the word from 海星';

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
         Word::where('if_grasp', Word::$TO_CHECK)->whereStar($this->argument('star'))->chunk(10, function($words) {
            foreach ($words as $word) {
                if ($this->confirm($word->word)) {
                    $word->if_grasp = Word::$PASS_CHECK;
                } else {
                    $word->if_grasp = Word::$FAIL_CHECK;
                }
                $word->save();
            }
        });
    }
}
