<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});




//Route::group(function(){
    Route::match(['get','post'],'login','Admin\LoginController@login')->name('login');

    Route::group(['middleware'=>['checkTokens']],function(){
        /*商品信息*/
        Route::match(['get','post'],'goodsList','Admin\GoodsController@GoodsList');
        Route::match(['get','post'],'goodsAdd','Admin\GoodsController@goodsAdd');
        Route::match(['get','post'],'getskukey','Admin\GoodsController@getskukey');
        Route::match(['get','post'],'getskuval','Admin\GoodsController@getskuval');
        Route::match(['get'],'goodsBelong','Admin\GoodsController@goodsBelong');//获取所属商品列表
        Route::match(['get'],'goodsstatus','Admin\GoodsController@goodsstatus');//获取所属商品列表
        /*商品类型信息*/
        Route::match(['get'],'goodsCategary','Admin\GoodsController@CategaryList');
        Route::match(['post'],'CategaryAdd','Admin\GoodsController@CategaryAdd');
        Route::match(['post'],'CategaryEdit','Admin\GoodsController@CategaryEdit');
        Route::match(['post'],'CategaryDel','Admin\GoodsController@CategaryDel');
        /*商品属性信息*/
        Route::match(['get'],'goodsSku','Admin\GoodsController@goodsSku');     //属性+属性值列表
        Route::match(['post'],'goodsSkuAdd','Admin\GoodsController@SkuAdd');   //属性添加

        Route::match(['post'],'SkuValAdd','Admin\GoodsController@SkuValAdd');  //属性值添加
        Route::match(['post'],'SkuValDel','Admin\GoodsController@SkuValDel');  //属性值删除

        /*商城首页*/

        Route::match(['get'],'goodsDetail','Admin\ProductController@index');//获取所属商品列表
        Route::match(['post'],'getPrice','Admin\ProductController@getPrice');//获取所属商品列表





        Route::match(['post'],'upload','CommonController@uploadImg');  //图片上传
        Route::match(['get'],'getupload/{id}','CommonController@getImg');  //获取图片

    });
//});
