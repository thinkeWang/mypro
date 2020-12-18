<?php
namespace App\Http\Service;

use Illuminate\Support\Facades\Redis;

const APPNAME = 'web_';
class Common{
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

    //无限极分类
    /*$level:权限级别
    *time:2020/12/18
     * $exclude:编辑时去掉当前id下的子权限
     * */
    public static function authGetTree($arr, $pid=0, $level=0,$exclude=false){
        static $result = array();
        foreach($arr as $value){
            if($value['id']==$exclude){
                continue;
            }
            if($value['pid'] == $pid){
                $value['level'] = $level;
                $result[] = $value;
                self::authGetTree($arr, $value['id'], $level+1,$exclude);
            }
        }
        return $result;
    }

    /**
     * @param $objectall 对象
     * @return array
     * 将对象转换为数组
     */
    public static function objToArr($objectall){
        if(empty($objectall)){
            return array();
        }
        foreach($objectall as $object)
        {
            $arrays[] =  (array) $object;
        }
        return $arrays;
    }



}
