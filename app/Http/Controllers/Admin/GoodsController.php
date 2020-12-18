<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\ReturnMsg;
use App\Http\Service\Goods;

class GoodsController extends Controller
{
    public function GoodsList(Request $request){

        return ReturnMsg::getMsg(0);
    }

    /**
     * @param Request $request
     * 商品类型列表以及父
     */
    public function CategaryList(Request $request){
        return Goods::getTypeList();
    }

    /**
     * @param Request $request
     * 商品类型添加
     */
    public function CategaryAdd(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'name'=>'required|string',
            'pid'=>'required|numeric'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::TypeAdd($inp);
    }



}
