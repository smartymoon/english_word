<?php

namespace App\Http\Controllers\Iview;

use App\Http\Controllers\Controller;
use App\Http\Resources\Note as NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $words = Note::getNotes($request);
        return NoteResource::collection($words);
    }

    /*
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Note::hasWord($request->word)) {
            return $this->fail('单词已添加');
        }
        Note::create($request->all());
        return $this->success('添加成功');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $word
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $word)
    {
        Note::modify($word, $request->all());
        return $this->success('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note $note
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($word)
    {
        Note::remove($word);
        return $this->success('删除成功');
    }
}
