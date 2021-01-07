<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Service\Product;
class ProductController extends Controller
{
    public static function index(Request $request){
        $data = $request->input();
        $validator =\Validator::make($data,[
            'id'=>'required|numeric'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1004);
        }
        return Product::index($data);
    }
}
