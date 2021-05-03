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
    $router->resource('/user_list', ListController::class);
    $router->resource('/leave_msg', LeaveMsgController::class);
    $router->resource('/lottery_list', LotteryController::class);
    $router->resource('/lottery_record', OpenLotteryController::class);
    $router->resource('/lottery_rule', RuleController::class);
//    轮播
    $router->resource('/user_slide', SlideListController::class);
    $router->resource('/user_news', NewsListController::class);
});
