<?php

namespace App\Http\Middleware;

use Closure;

class checkTokens
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        dd($_COOKIE);
        return $next($request);
//        if (explode('?',$request->getUri())[0] === route('login')) {
//            return $next($request);
//        }
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
