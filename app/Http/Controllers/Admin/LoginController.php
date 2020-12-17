<?php

namespace App\Http\Controllers\Admin;

use App\Http\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index(Request $request){
        setcookie('wzy',rand(),time()+3600,'/');
        Session()->put('qq',rand());
        echo 'ok';
    }



    public function login(Request $request){
        setcookie('abc','haha',time()+3600,'/');
        Session()->put('ww',111);
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'username'=>'required|numeric',
            'password'=>'required|string|min:6|max:16'
        ]);
        if(!$validator->passes()){
            return self::returnJson("请填写正确账号和密码",-101);
        }
        return User::login($inp);
    }
}
