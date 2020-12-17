<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Service\UserInfo;
use App\Http\Service\ReturnMsg;

class checkTokens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!isset($_COOKIE['web_app']) || empty($_COOKIE['web_app'])){
            return response()->json(ReturnMsg::getMsg(1000));
        }
        $token = decrypt($_COOKIE['web_app']);
        $key = "web_app".$token['id'];
        if($token['ip'] !== request()->ip()){
            //清除当前登录的数据
            setcookie('web_app',null);
            Redis::set($key,null);
            //并记录数据库
            
            return response()->json(ReturnMsg::getMsg(1004));
        }

        dd($_COOKIE);

//        $userInfo = $request->session()->get('name');// Redis::get(md5("app"));
//        if(!$userInfo){
//            return response()->json(['code'=>-1,'msg'=>'登录超时']);
//        }
//        $data = json_decode($userInfo,true);
//        if(!$data['token'] || $request->input('token') != $data['token']){
//            return response()->json(['code'=>-1,'msg'=>'登录已超时']);
//        }
//        $request -> merge([
//            'user'=>$data
//        ]);
//        return $next($request);
    }
}
