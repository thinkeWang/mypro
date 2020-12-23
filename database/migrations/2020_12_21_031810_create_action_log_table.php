<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('')->comment('操作名称');
            $table->string('method')->default('')->comment('操作方法');
            $table->string('sql')->default('')->comment('操作sql');
            $table->integer('operator')->default(0)->comment('操作人');
            $table->enum('type', [0,1,2,3])->default(0)->comment('0:查询，1：增，2：删，3：改');
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
        //
    }
}
