<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 查找单词，使用频率，主要意思工具，用 倍洽进行沟通
Route::post('words/lookup', 'WordsController@lookUp');

