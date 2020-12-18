<?php
namespace App\Http\Service;

use App\Http\Service\Common;
use Illuminate\Support\Facades\DB;
use App\Http\Service\ReturnMsg;
class Goods{
    public static function getTypeList(){
        $res = DB::table('categary')
            ->select('user.name as uname', 'categary.name', 'categary.id', 'categary.pid', 'categary.created_time')
            ->leftJoin('user', 'user.id', '=', 'categary.operator')
            ->get();
        if(empty($res)){
            return ReturnMsg::getMsg(0,[]);
        }
        return ReturnMsg::getMsg(0,['list'=>Common::authGetTree(Common::objToArr($res))]);
    }

    public static function TypeAdd($data){
        //判断是否已存在

        //添加类型
        return [];
    }

}
