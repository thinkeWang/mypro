<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Service\Common;
use App\Http\Service\ReturnMsg;
use Illuminate\Support\Facades\Redis;

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
            return response()->json(ReturnMsg::getMsg(-1000));
        }
        $token = decrypt($_COOKIE['web_app']);
        $key = "web_app".$token['id'];
        if($token['ip'] !== request()->ip()){
            //清除当前登录的数据
            setcookie('web_app',null,0);
            //并记录数据库
            return response()->json(ReturnMsg::getMsg(-1001));
        }
        $info = Redis::get($key);
        if(!$info){
            return response()->json(ReturnMsg::getMsg(-1002));
        }
        $request -> merge([
            'user'=>decrypt($info)
        ]);
        return $next($request);
    }
}
