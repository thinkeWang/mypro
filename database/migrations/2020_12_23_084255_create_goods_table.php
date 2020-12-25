<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->default(0)->comment('所属类型');
            $table->string('gname')->default('')->comment('商品名称');
            $table->string('gprice')->default('')->comment('商品价格区间以“~”区分');
            $table->string('title')->default('')->comment('商品标题');
            $table->integer('gnum')->default(0)->comment('商品数量');
            $table->tinyInteger('gstatus')->default(1)->comment('是否上架');
            $table->text('gdesc')->comment('商品描述');
            $table->integer('operator')->default(0)->comment('操作人');
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
