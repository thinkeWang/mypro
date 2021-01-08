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
    public static function getPrice($data){
        $sku = json_decode($data['skuRadioArray'],true);
        if(is_null($sku)){
            return ReturnMsg::getMsg(1033);
        }
        foreach($sku as $kay => $val){
            $keyid = explode('sku',$kay)[0];
            $res = DB::table('goods_detail')->where('gid',$data['gid'])
                ->where('gskukey',$keyid)
                ->where('gskuval',$val)
                ->first();
            if($res){
                break;
            }
        }
        if(isset($res)){
            return ReturnMsg::getMsg(0,['price'=>$res->goodsprice/100]);
        }else{
            return ReturnMsg::getMsg(1034);
        }
    }
}
