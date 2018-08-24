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

Route::group(['namespace' => 'Mini'], function () {
    Route::get('login/{code}', 'AuthController@login');
    Route::get('dict/{word}', 'WordsController@lookUp');

    Route::group(['middleware'=>'auth:mini'], function() {

    });
});
