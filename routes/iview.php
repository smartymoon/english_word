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

Route::group(['namespace' => 'Iview'], function () {
    Route::post('login', 'AuthController@login');
    Route::get('words/{word}/play', 'WordsController@listen');

    Route::group(['middleware'=>'auth:iview'], function() {
        Route::post('logout', 'AuthController@logout');

        Route::get('words', 'WordsController@index');
        Route::put('words/{word}/remark', 'WordsController@remark');
        Route::post('words/{word}/{status}', 'WordsController@setStatus');
    });
});
