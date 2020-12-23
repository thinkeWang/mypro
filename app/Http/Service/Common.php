<?php
namespace App\Http\Service;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

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
    /**
     * @param $res
     * @return string
     * 获取用户信息
     */
    public static function getInfo(){
        $key = APPNAME."app";
        if(is_null($_COOKIE[$key]) || empty($_COOKIE[$key])){
            return false;
        }
        $data =decrypt($_COOKIE[$key]);
        if(!$data || ($data['ip'] != request()->ip())){
            setcookie($key,null,1);
            return false;
        }
        $info =[
            'name' =>  $data["name"],
            'id' =>  $data['id'],
            'ip' =>  $data['ip']
        ];
        return json_encode($info);
    }


    //获取用户id
    public static function getId(){
        $info = self::getInfo();
        if(!$info){
            return 0;
        }
        $id = json_decode($info,true);
        if(!$id['id']){
            return 0;
        }
        return $id['id'];
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
        $arrays = array();
        foreach($objectall as $object)
        {
            $arrays[] =  (array) $object;
        }
        return $arrays;
    }
    /*
    *记录操作日志
    */
    public static function actionLog($res){
        $str = '0';
        switch (true){
            case is_int(strpos($res['sql']['query'],'insert')):
                $str = '1';
                break;
            case is_int(strpos($res['sql']['query'],'delete')):
                $str = '2';
                break;
            case is_int(strpos($res['sql']['query'],'update')):
                $str = '3';
                break;
        }
        $where['name'] = $res['actionName'];
        $where['method'] =request()->path();
        $where['sql'] = json_encode($res['sql']);
        $where['operator'] = self::getId();
        $where['created_at'] = date('Y-m-d H:i:s');
        $where['type'] = $str;
        DB::table('action_log')->insert($where);
    }


}
