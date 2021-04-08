<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/***
 * @api
 * @version v1
 * @methodType {get put post delete}
 * @routeMsg web
 */
#首页
Route::get('index', "IndexController@index");
#登陆
Route::post('login', "UserController@login");
#注册
Route::post('register', "UserController@register");
#获取短信验证码
Route::get('mobile_code', "UserController@MobileCode");
#获取图片验证码
Route::get('image_code', "UserController@ImageCode");
#忘记密码
Route::post('forget', "UserController@forgetPwd");
#上传图片
Route::post('upload', "UserController@UploadFile");
#获取头像
Route::get('get_avatar', "UserController@randAvatar");
#用户留言
Route::post('leave_message', "UserController@leave_message");
#留言列表
Route::get('leave_message_list', "UserController@leave_message_list");
#关于我们
Route::get('about', "IndexController@about");
#关于我们添加（后台功能）
Route::post('about_add', "IndexController@about_add");

/*******************游戏列表********************/
#游戏类型
Route::get('game_type', "GameController@game_type");

/*****************首页数据***********************/
#首页游戏列表
Route::get('game_list', "IndexController@game_list");
#首页轮播
Route::get('carousel_list', "IndexController@Carousel");
#首页新闻列表
Route::get('news_list', "IndexController@newsList");
#更多新闻
Route::get('news_more', "IndexController@newsMore");
#新闻类型
Route::get('news_type', "IndexController@newsMore");
#开奖类型
Route::get('game_type', "GameController@game_type");
#开奖列表
Route::get('game_open_list', "GameController@game_open_list");
#开奖记录（六合）
Route::get('game_lh_record', "GameController@getLhcRecord");
#开奖记录
Route::get('game_record', "GameController@getPerRecord");
#开奖计划
Route::get('game_plan', "GameController@getbqPlan");
#彩票计划列表
Route::get('game_cate_list', "GameController@cateGamesList");
#获取单个开奖记录
Route::get('game_open_ones', "GameController@againTime");
#彩票列表
Route::get('game_all', "GameController@game_all");
#直播列表
Route::get('game_live_list', "GameController@getLiveList");
#获取直播菜单列表
Route::get('game_live_all', "GameController@gatLiveAllGame");
#六合专版查询
Route::get('game_lh_special', "GameController@lhcSpecial");
#香港澳门新加坡开奖
Route::get('game_open_other', "GameController@gameOpenOther");
Route::group([
    'middleware' => 'auth:api'
], function () {
    /************************用户类**************************/
    #修改资料
    Route::post('change', "UserController@userUpdate");
    #获取个人信息
    Route::get('detail', "UserController@person");
    #退出登陆
    Route::get('logout', "UserController@logOut");
    #验证旧手机
    Route::get('check_old_mobile', "UserController@checkMobile");

});
