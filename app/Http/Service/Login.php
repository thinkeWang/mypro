<?php
namespace App\Http\Service;

use App\Http\Service\ReturnMsg;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class Login{
    public static function login($data){
        $res = DB::table('user')->where('name',$data['username'])->first();
        if(!$res){
            return ReturnMsg::getMsg(1002);
        }
        if($res->password !== md5($res->salt.$data['password'])){
            return ReturnMsg::getMsg(1003);
        }
        UserInfo::setInfo($res);
        return ReturnMsg::getMsg(0);
    }


}
