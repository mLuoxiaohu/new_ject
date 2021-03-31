<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpinionsTable extends Migration
{
    /**
     * Run the migrations.
     * 用户留言表
     * @return void
     */
    public function up()
    {
        Schema::create('opinions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('avatar',100)->comment('头像');
            $table->integer('pid')->index()->default(0)->comment('上级');
            $table->text('nickname')->comment('昵称');
            $table->text('content')->comment('留言内容');
            $table->enum('state',[1,2,3])->comment('1 审核中 2 审核通过 3 未通过');
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
        Schema::dropIfExists('opinions');
    }
}
