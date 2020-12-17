<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class User extends Model
{
    public static function login($data){
        $res = DB::table('user')->where('name',$data['username'])->first();
        if(!$res){
            return self::returnJson('没有注册！',-102);
        }
        if($res->password !== md5($res->salt.$data['password'])){
            return self::returnJson('账号密码不正确！',-103);
        }
        $token = self::serInfo($res);
        return self::returnJson('登录成功',0,$token);
    }
    public static function serInfo($res){
        $token = encrypt(request()->ip().$res->id."app".time());
        $key = md5($res->name."app");
        $info = json_encode([
            "name"=>$res->name,
            "id" => $res->id,
            "token" => $token
        ]);
        Redis::set($key,$info);
        return $token;
    }



}
