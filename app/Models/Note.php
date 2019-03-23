<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Note extends Model
{
    protected $fillable = ['word', 'remark', 'status', 'category_id'];
    public static function getNotes(Request $request)
    {
        $query = self::latest()->leftJoin('words', 'notes.word', '=', 'words.word')
                    ->select('notes.*', 'senses', 'attrs', 'phonetics', 'star');
        $status = $request->input('status');
        if (in_array($status, [0,1])) {
            $query->where('status', $status);
        }

        if ($category = $request->input('category', 0)) {
            $query->where('category_id', $category);
        }
        return $query->paginate($request->input('pageSize', 30));
    }

    public static function hasWord($word)
    {
        return self::where('word', $word)->count() > 0;
    }

    public static function modify($word, array $all)
    {
        self::where('word', $word)->update($all);
    }

    public static function remove($word)
    {
        self::where('word', $word)->delete();
    }
}
