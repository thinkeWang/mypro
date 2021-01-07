<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('name')->default('0')->comment('手机账号');
            $table->string('password')->default('')->comment('密码');
            $table->string('salt')->default('')->comment('盐');
            $table->integer('status')->default('1')->comment('状态');
            $table->timestamp('created_time')->default(date('Y-m-d H:i:s'))->comment('创建时间');
            $table->timestamp('updated_time')->default(date('Y-m-d H:i:s'))->comment('更新时间');
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
