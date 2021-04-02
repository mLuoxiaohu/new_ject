<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsclassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsclass', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',20)->comment('分类名称');
            $table->string('abbr')->comment('采集缩写');
            $table->tinyInteger('visible')->default(0)->comment('1关闭 2开启');
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
        Schema::dropIfExists('newsclass');
    }
}
