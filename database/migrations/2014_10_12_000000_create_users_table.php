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
            $table->string('avatar')->default('/default0.png')->comment('头像');
            $table->char('mobile',13)->unique()->comment('用户名');
            $table->string('password',100)->comment('密码');
            $table->decimal('coin',8,2)->default(0)->comment('钱包余额');
            $table->enum('state',[1,2,3,4])->default('1')->comment('状态 1 正常 2禁用');
            $table->enum('sex',[1,2,3])->default('1')->comment('性别 1 未知 2 男 3女');
            $table->string('signature')->nullable()->comment('个性签名');
            $table->tinyInteger('login_fail')->default(0)->comment('登录失败次数');
            $table->string('login_ip',40)->nullable()->comment('登录ip');
            $table->timestamp('login_time')->nullable()->comment('登录时间');
            $table->timestamp('create_time')->nullable()->comment('创建时间');
            $table->timestamp('update_time')->nullable()->comment('更新时间');
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
