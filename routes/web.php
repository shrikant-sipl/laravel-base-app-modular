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

Route::group(['middleware' => 'prevent-back-history'], function(){
    /* Index route */
    Route::get('/', function () {
        return view('welcome');
    });

    /* Authentication routes */
    Auth::routes();

    /* Home page route */
    Route::get('/home', 'HomeController@index')->name('home');

});
