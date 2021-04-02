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
Route::group([
    'middleware' => 'auth:api'
], function () {
    /************************用户类**************************/
    #修改资料
    Route::put('change', "UserController@userUpdate");
    #获取个人信息
    Route::get('detail', "UserController@person");
    #退出登陆
    Route::get('logout', "UserController@logOut");
    #验证旧手机
    Route::get('check_old_mobile', "UserController@checkMobile");

});
