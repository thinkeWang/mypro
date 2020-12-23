<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Service\ReturnMsg;
use App\Http\Service\Login;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    public function login(Request $request){
        $inp = $request->input();
        $validator =\Validator::make($inp,[
            'username'=>'required|numeric',
            'password'=>'required|string|min:6|max:16'
        ]);
        if(!$validator->passes()){
            return ReturnMsg::getMsg(1001);
        }
        return Login::login($inp);
    }
}
