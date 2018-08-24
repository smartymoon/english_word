<?php

namespace App\Http\Controllers\Mini;

use App\Models\Word;
use App\Http\Controllers\Controller;

class WordsController extends Controller
{
    //
    public function lookUp(string $word)
    {
        if ($word) {
            $data = Word::whereWord(trim($word))->first();
            return $data ? new \App\Http\Resources\Word($data) : $this->fail('单词没查到');
        }
        return $this->fail('单词不能为空');
    }
}
