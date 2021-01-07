<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Service\Common;
use App\Http\Service\ReturnMsg;
use Illuminate\Contracts\Encryption\DecryptException;
class CommonController extends Controller
{
    public function uploadImg(Request $request){
        $realName = $request->file('productImg')->getClientOriginalName();
        $name = 'productImg/'.date('Ymd');
        $path = $request->file('productImg')->store($name);
        if($path){
            Common::upload($name,$path,$realName,'productImg');
        }
        return ReturnMsg::getMsg(0,['url'=>encrypt($path)]);
    }
    public function getImg(Request $request){
        $url = $request->getRequestUri();
        $i = explode('/',$url);
        $decrypt = $i[2];
        $url = decrypt($decrypt);
        $path = storage_path() ."/app/". $url;
        //查看目录下是否存在文件
        if(!file_exists($path)){
            //报404错误
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");
            exit;
        }

        //输出图片
        header('Content-type: image/jpg');
        echo file_get_contents($path);
        exit;
    }
}
