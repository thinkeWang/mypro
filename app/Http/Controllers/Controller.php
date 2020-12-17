<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static function returnJson($msg="",$code = 0,$data=[]){
        if($code != 0){
            return json_encode([
                "code"=> $code,
                "msg" => $msg
            ]);
        }
        return json_encode([
            "code" => $code,
            "msg"  => $msg,
            "data" => $data
        ]);
    }
}
