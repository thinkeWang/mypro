<?php
namespace App\Http\Service;

class ReturnMsg{
    public static $msgMap = [
        '0' => 'ok',
        /*登录*/
        '1000'      =>  '账号过期，请重新登录',
        '1001'      =>  '账号或密码格式不正确',
        '1002'      =>  '没有注册！',
        '1003'      =>  '账号密码不正确！',
        '1004'      =>  '您已在其他账号中登录',
    ];


    /**
     *获取标准提示信息
     */
    public static function getMsg($code, $others = []){
        $error = [
            'code' => $code,
            'msg' => self::$msgMap[$code]
        ];

        if(is_array($others) && !empty($others)){
            $error = array_merge($error, $others);
        }
        return $error;
    }


}
