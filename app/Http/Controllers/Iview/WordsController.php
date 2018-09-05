<?php

namespace App\Http\Controllers\Iview;

use App\Libs\Sounds;
use App\Models\Word;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WordsController extends Controller
{
    //
    public function index(Request $request)
    {
        $star = $request->input('star', 5);
        $query = Word::whereStar($star)->where('if_grasp', Word::$FAIL_CHECK);
        if ($request->input('onlyMarked') === 'true') {
            $query->where('remark', '<>', '');
        }
        $words = $query->paginate($request->input('pageSize', 30));
        return \App\Http\Resources\Word::collection($words);
    }

    public function show ($word)
    {
        $word = Word::whereWord($word)->first();
        return new \App\Http\Resources\Word($word);
    }

    public function setStatus($word, $status)
    {
        $word = Word::whereWord($word)->first();
        $word->if_recite = (boolean) $status;
        $word->save();
        return $this->success('标记成功');
    }

    public function listen($word)
    {
        $sound = new Sounds();
        $voice = $sound->play('https://dict.youdao.com/dictvoice?audio='. $word .'&type=2');
        return response($voice, 200 , [
            'content-type' =>'audio/mpeg'
        ]);
    }

    public function sentenceListen(Request $request)
    {
        $url = $request->input('url');
        $sound = new Sounds();
        $voice = $sound->play(urldecode($url));
        return response($voice, 200 , [
            'content-type' =>'audio/mpeg'
        ]);
    }

    public function remark($word, Request $request)
    {
        $word = Word::whereWord($word)->first();
        $word->remark = $request->input('remark');
        $word->save();
        return $this->success('笔记添加成功');
    }
}
