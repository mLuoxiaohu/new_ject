<?php

use Illuminate\Http\Request;
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
#帖子列表
Route::get('article_index', "ArticleController@list");
#帖子详情
Route::get('article_detail/{id}', "ArticleController@articleDetail");
#获取头像
Route::get('get_avatar', "UserController@randAvatar");
/************************blog**************************/
#他的信息
Route::get('bolg_detail/{id}', "UserController@bolgDetail");
#他的评论
Route::get('blog_comment/{id}', "ArticleController@blog_comment");
#他的帖子
Route::get('blog_article/{id}', "ArticleController@blog_article");

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
    /************************文章类**************************/
    #用户帖子
    Route::get('article_list', "ArticleController@index");
    #用户发帖
    Route::post('article_add', "ArticleController@add");
    #删除帖子
    Route::delete('article_del/{id}', "ArticleController@deleteArticle");
    #修改帖子
    Route::put('article_change/{id}', "ArticleController@change");
    #帖子点赞
    Route::get('article_like/{id}', "ArticleController@likes");
    #收藏帖子
    Route::get('article_coll/{id}', "ArticleController@collection");
    #发起评论
    Route::post('comment_add/{id}', "ArticleController@commentAdd");
    #收藏帖子列表
    Route::get('article_store', "ArticleController@selfArticle");
    #我的评论
    Route::get('self_comment', "ArticleController@self_comment");
    #修改帖子
    Route::put('article_rewrite/{id}', "ArticleController@ContinueRewrite");


});
