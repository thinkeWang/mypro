<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkuvalueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skuvalue', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('')->comment('sku值');
            $table->integer('kid')->default(0)->comment('sku键表id');
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
