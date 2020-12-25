<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gid')->default(0)->comment('商品id');
            $table->string('goodsname')->default('')->comment('商品名称');
            $table->string('goodssku')->default('')->comment('sku信息（json格式，列表中用）');
            $table->integer('goodsprice')->default(0)->comment('商品价格');
            $table->integer('goodsnum')->default(0)->comment('商品数量');
            $table->enum('isdel',['0','1'])->default('0')->comment('是否删除：0：否，1：是');
            $table->text('gdesc')->comment('商品描述');
            $table->integer('operator')->default(0)->comment('操作人');
            $table->index('gid');
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
