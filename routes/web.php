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
        Route::match(['get','post'],'goodsCategary','Admin\GoodsController@CategaryList');
        Route::match(['get','post'],'CategaryAdd','Admin\GoodsController@CategaryAdd');

    });
//});
