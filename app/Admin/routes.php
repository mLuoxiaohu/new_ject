<?php

use Illuminate\Routing\Router;

Admin::routes();

//语言包
//composer require "overtrue/laravel-lang:~3.0"
Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
//    $router->get('/user_list', 'ListController@index');

    $router->resource('/user_list', ListController::class);
    $router->resource('/leave_msg', LeaveMsg::class);
});
