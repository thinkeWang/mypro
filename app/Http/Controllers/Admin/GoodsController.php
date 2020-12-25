<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\ReturnMsg;
use App\Http\Service\Goods;

class GoodsController extends Controller
{
    public function GoodsList(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'pageIndex'=>'required|numeric',
            'pageSize'=>'required|numeric|min:10|max:50'
        ]);
        if(!$validator->passes() || !empty($inp['gtype']) && !is_numeric($inp['gtype']) || !empty($inp['gname']) && !is_string($inp['gname'])){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::GoodsList($inp);
    }

    /***
     * @param Request $request
     * @return array
     * 获取属性名称列表
     */
    public function goodsAdd(Request $request){
        $inp = $request->input();
        if($request->isMethod('GET')){
            return Goods::getSkuList($inp);
        }else{
            $validator =\Validator::make($inp,[
                'pageIndex'=>'required|numeric',
                'pageSize'=>'required|numeric|min:10|max:50'
            ]);
            if(!$validator->passes() || !empty($inp['gtype']) && !is_numeric($inp['gtype']) || !empty($inp['gname']) && !is_string($inp['gname'])){
                return ReturnMsg::getMsg(1004);
            }
            return Goods::GoodsList($inp);
        }

    }

    /**
     * @param Request $request
     * 商品类型列表以及父
     */
    public function CategaryList(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'pageIndex'=>'required|numeric',
            'pageSize'=>'required|numeric|min:2|max:50'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::getTypeList($inp);
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
    /**
     * @param Request $request
     * 商品类型编辑
     */
    public function CategaryEdit(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'name'=>'required|string',
            'id'=>'required|numeric',
            'pid'=>'required|numeric'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::TypeEdit($inp);
    }
    /**
     * @param Request $request
     * 商品类型删除
     */
    public function CategaryDel(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'id'=>'required|numeric',
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::TypeDel($inp);
    }

    /***
     * @param Request $request
     * @return array
     * 商铺sku列表
     */
    public static function goodsSku(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'pageIndex'=>'required|numeric',
            'pageSize'=>'required|numeric|min:10|max:50'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::goodsSku($inp);
    }

    /***
     * @param Request $request
     * @return array
     * 商铺sku添加
     */
    public static function SkuAdd(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'pid'=>'required|numeric',
            'name'=>'required|string'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::SkuAdd($inp);
    }

    /***
     * @param Request $request
     * @return array
     * 商铺sku添加
     */
    public static function SkuValAdd(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'id'=>'required|numeric',
            'name'=>'required|string'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::SkuValAdd($inp);
    }

    /***
     * @param Request $request
     * @return array
     * 商铺sku删除
     */
    public static function SkuValDel(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'id'=>'required|numeric'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::SkuValDel($inp);
    }
    /***
     * @param Request $request
     * @return array
     * 获取skulist
     */
    public static function getskukey(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'id'=>'required|numeric'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::getskukey($inp);
    }
    /***
     * @param Request $request
     * @return array
     * 获取sku值
     */
    public static function getskuval(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'id'=>'required|numeric'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Goods::getskuval($inp);
    }




}
