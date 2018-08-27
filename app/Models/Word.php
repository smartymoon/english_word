<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    public static $TO_CHECK = 0;
    public static $PASS_CHECK = 1;
    public static $FAIL_CHECK = 2;
    public $timestamps = false;
    protected $fillable = ['word', 'senses', 'attrs', 'star', 'phonetics'];
    protected $casts = [
        'attrs' => 'array',
        'sentences' => 'array',
        'senses' => 'array',
        'if_recite' => 'boolean'
    ];
}
