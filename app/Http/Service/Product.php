<?php
namespace App\Http\Service;

use App\Http\Service\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Service\ReturnMsg;
use Illuminate\Database\Query;
class Product{
    public static function index($data){
        $detail = DB::table('goods_detail')
        ->leftJoin('goods', 'goods.id', '=', 'goods_detail.gid')
        ->select('goods.*','goods_detail.*')
        ->where('goods_detail.id',$data['id'])
        ->first();
        $detail = get_object_vars($detail);
        return ReturnMsg::getMsg(0,['list'=>$detail]);
    }
}
