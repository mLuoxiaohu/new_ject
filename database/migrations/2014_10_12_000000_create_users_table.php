<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nickname',20)->comment('昵称');
            $table->string('avatar')->default('/default.png')->comment('头像');
            $table->string('username',18)->unique()->comment('用户名');
            $table->string('password',100)->comment('密码');
            $table->decimal('coin',8,2)->default(0)->comment('钱包余额');
            $table->enum('state',[1,2,3,4])->default('1')->comment('状态 1 正常 2禁用');
            $table->enum('sex',[1,2,3])->default('1')->comment('性别 1 未知 2 男 3女');
            $table->enum('is_article',[1,2])->default('1')->comment('性别 1 可以发帖 2禁止发帖');
            $table->string('signature')->nullable()->comment('个性签名');
            $table->enum('is_admin',[1,2])->default('1')->comment('是否为管理：1否 2是');
            $table->enum('is_machine',[1,2])->default('1')->comment('是否为机器：1否 2是');
            $table->tinyInteger('login_fail')->default(0)->comment('登录失败次数');
            $table->string('qq',20)->nullable()->comment('qq号');
            $table->string('wx',30)->nullable()->comment('微信号');
            $table->string('login_ip',40)->nullable()->comment('登录ip');
            $table->integer('login_time')->nullable()->comment('登录时间');
            $table->integer('create_time')->nullable()->comment('创建时间');
            $table->integer('update_time')->nullable()->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
