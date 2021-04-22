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

//Route::get('/', function () {
//    return view('welcome');
//});
#安装后台库
//composer require encore/laravel-admin:1.*

Route::get('index', "IndexController@index");
Route::group([
//    'middleware' => 'auth:api'
], function () {


});
