<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categary', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('')->comment('分类名称');
            $table->integer('pid')->default(0)->comment('父id');
            $table->integer('operator')->default(0)->comment('操作人');
            $table->timestamp('created_time')->default(date('Y-m-d H:i:s'))->comment('创建时间');
            $table->timestamp('update_time')->default(date('Y-m-d H:i:s'))->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
