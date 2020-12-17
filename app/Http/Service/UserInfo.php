<?php
namespace App\Http\Service;

use Illuminate\Support\Facades\Redis;

const APPNAME = 'web_';
class UserInfo{
    /**
     * @param $res
     * @return string
     * 设置用户信息
     */
    public static function setInfo($res){
        $key = APPNAME."app";
        $data =[
          'name' =>  $res->name,
          'id' =>  $res->id,
          'ip' =>  request()->ip()
        ];
        setcookie($key,encrypt($data),time()+86400,'/');
        Redis::setex($key.$res->id,86400,encrypt($data));
    }

}
