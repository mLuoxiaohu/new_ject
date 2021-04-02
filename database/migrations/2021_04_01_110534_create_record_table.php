<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kid')->comment('kid');
            $table->string('periods',25)->comment('期数');
            $table->string('number',100)->comment('开奖号码');
            $table->text('value')->nullable()->comment('预测值');
            $table->string('adds')->nullable();
            $table->integer('time')->comment('当前期时间戳');
            $table->integer('next_time')->comment('下期开奖时间戳');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('record');
    }
}
