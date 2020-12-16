<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoodsController extends Controller
{
    public function GoodsList(Request $request){
//        var_dump($_COOKIE);die;
        dd($request->session()->get('name'));
        return json_encode([
            'code'=>0,
            'msg'=>'ok'
        ]);
    }
}
