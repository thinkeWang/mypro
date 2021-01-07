<?php
namespace App\Http\Service;

use App\Http\Service\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Service\ReturnMsg;
use Illuminate\Database\Query;
class Goods{
    /***
     * @param $inp
     * @return array
     * 商品列表
     */
    public static function GoodsList($inp){
        $gname = isset($inp['gname'])&&!empty($inp['gname']) ? $inp['gname'] : "";
        $gtype = isset($inp['gtype'])&&!empty($inp['gtype']) ? $inp['gtype'] : "";
        $query = DB::table('goods')
            ->select('user.name as uname', 'categary.name as tname','goods_detail.*','goods.*',
                'goods_detail.id as dId','goods_detail.gdesc as desc','goods_detail.operator as doperator',
                'goods_detail.created_at as dcreated_at','goods_detail.updated_at as dupdated_at')
            ->leftJoin('goods_detail', 'goods.id', '=', 'goods_detail.gid')
            ->leftJoin('categary', 'goods.category_id', '=', 'categary.id')
            ->leftJoin('user', 'goods.operator', '=', 'user.id')
            ->orderBy('goods.id')
            ->when($gname, function ($query) use ( $gname ) {
                return $query->where('goods.gname', $gname);
            })
            ->when($gtype, function ($query) use ($gtype) {
                return $query->where('goods.category_id', $gtype);
            });
        $count = $query->count();
        $res = $query->offset(($inp['pageIndex']-1)*$inp['pageSize'])
            ->limit($inp['pageSize'])
            ->get();
        $ee = Common::objToArr($res);
        //获取所有类型
        $TypeRes = self::getTypeList(array());
        return ReturnMsg::getMsg(0,['list'=>$ee,'pageTotal'=>$count,'type'=>$TypeRes['list']]);
    }

    /***
     * 添加商品
     */
    public static function goodsAdd($data){
        if($data['belongtypeid'] == 0 && empty($data['cid'])){
            return ReturnMsg::getMsg(1022);
        }
        if($data['belongtypeid'] != 0){
            $data['cid'] = $data['belongtypeid'];
        }
        //判断类型是否一致
        $exit = DB::table('categary')
            ->leftJoin('skukey', 'categary.id', '=', 'skukey.cid')
            ->leftJoin('skuvalue', 'skuvalue.kid', '=', 'skukey.id')
            ->where('categary.id',$data['cid'])
            ->where('skukey.id',$data['skuKey'])
            ->where('skuvalue.id',$data['skuVal'])
            ->count();
        if(!$exit){
            return ReturnMsg::getMsg(1021);
        }
        if($data['belongtypeid'] == 0){//新增还不存在的商品
            //判断是否存在此商品（根据名称和类型）
            $count = DB::table('goods')
                ->where('gname',$data['gname'])
                ->where('category_id',$data['cid'])
                ->count();
            if($count){
                return ReturnMsg::getMsg(1023);
            }
            $gdata = array();
            $gdata['category_id'] = $data['cid'];
            $gdata['gname'] = $data['gname'];
            $gdata['gprice'] = $data['gprice']*100;
            $gdata['title'] = $data['title'];
            $gdata['gnum'] = $data['gnum'];
            $gdata['gstatus'] = $data['status'];
            $gdata['gdesc'] = $data['gtext'];
            $gdata['operator'] = Common::getId();
            $gdata['created_at'] = date('Y-m-d H:i:s');
            $detal = array();
            $detal['goodsname'] = $data['gname'];
            $detal['goodsprice'] = $data['gprice']*100;
            $detal['goodsnum'] = $data['gnum'];
            $detal['isdel'] = '0';
            $detal['gdesc'] = $data['gtext'];
            $detal['img'] = $data['gupload'];
            $detal['gskukey'] = $data['skuKey'];
            $detal['gskuval'] = $data['skuVal'];
            $detal['operator'] = Common::getId();
            $detal['created_at'] = date('Y-m-d H:i:s');
            $key = DB::table('skukey')->where('id',$data['skuKey'])->first();
            $key = get_object_vars($key);
            if(!$key){
                return ReturnMsg::getMsg(1020);
            }
            $val = DB::table('skuvalue')->where('id',$data['skuVal'])->first();
            $val = get_object_vars($val);
            if(!$val){
                return ReturnMsg::getMsg(1017);
            }
            $sku = array();
            $sku[$data['skuKey']]['keyName'] = $key['name'];
            $sku[$data['skuKey']]['valid'] = $data['skuVal'];
            $sku[$data['skuKey']]['valname'] = $val['name'];
            $gsku = array();
            $gsku[$data['skuKey']][0]['keyName'] = $key['name'];
            $gsku[$data['skuKey']][0]['valid'] = $data['skuVal'];
            $gsku[$data['skuKey']][0]['valname'] = $val['name'];
            $detal['goodssku'] = json_encode($sku,JSON_UNESCAPED_UNICODE);
            $gdata['gsku'] = json_encode($gsku,JSON_UNESCAPED_UNICODE);
            //添加商品
            DB::beginTransaction();
            try{
                $ins = DB::table('goods')->insertGetId($gdata);
                if(!$ins){
                    DB::rollBack();
                    return ReturnMsg::getMsg(1024);
                }
                $detal['gid'] = $ins;
                DB::connection()->enableQueryLog();
                $insd = DB::table('goods_detail')->insert($detal);
                $logs = DB::getQueryLog();
                $log['actionName'] =  '增加商品:'.$data['gname'];
                $log['sql'] = $logs[0];
                if(!$insd){
                    DB::rollBack();
                    return ReturnMsg::getMsg(1025);
                }
                //记录日志
                Common::actionLog($log);
                DB::commit();
                return ReturnMsg::getMsg(0);
            }catch (\Exception $e) {
                //接收异常处理并回滚
                DB::rollBack();
                return ReturnMsg::getMsg(1026);
            }
        }else{//新增关联的商品
            //查看商品表中是否存在此关联商品
            $count = DB::table('goods')
                ->where('gname',$data['gname'])
                ->where('category_id',$data['cid'])
                ->first();
            if(!$count){
                return ReturnMsg::getMsg(1027);
            }
            if($count->id != $data['belongid']){
                return ReturnMsg::getMsg(1028);
            }
            //查看详细表中是否存在此属性商品
            $detail = DB::table('goods_detail')
                ->where('gid',$data['cid'])
                ->where('goodsname',$data['gname'])
                ->where('gskukey',$data['skuKey'])
                ->where('gskuval',$data['skuVal'])
                ->count();
            if($detail){
                return ReturnMsg::getMsg(1029);
            }
            //添加商品
            $detal = array();
            $detal['gid'] = $data['belongid'];
            $detal['goodsname'] = $data['gname'];
            $detal['goodsprice'] = $data['gprice']*100;
            $detal['goodsnum'] = $data['gnum'];
            $detal['gdesc'] = $data['gtext'];
            $detal['img'] = $data['gupload'];
            $detal['gskukey'] = $data['skuKey'];
            $detal['gskuval'] = $data['skuVal'];
            $detal['operator'] = Common::getId();
            $detal['created_at'] = date('Y-m-d H:i:s');
            $key = DB::table('skukey')->where('id',$data['skuKey'])->first();
            $key = get_object_vars($key);
            if(!$key){
                return ReturnMsg::getMsg(1020);
            }
            $val = DB::table('skuvalue')->where('id',$data['skuVal'])->first();
            $val = get_object_vars($val);
            if(!$val){
                return ReturnMsg::getMsg(1017);
            }
            $sku = array();
            $sku[$data['skuKey']]['keyName'] = $key['name'];
            $sku[$data['skuKey']]['valid'] = $data['skuVal'];
            $sku[$data['skuKey']]['valname'] = $val['name'];
            $detal['goodssku'] = json_encode($sku,JSON_UNESCAPED_UNICODE);

            //添加商品
            DB::beginTransaction();
            try{
                DB::connection()->enableQueryLog();
                $insd = DB::table('goods_detail')->insert($detal);
                $logs = DB::getQueryLog();
                $log['actionName'] =  '增加新属性商品:'.$data['gname'];
                $log['sql'] = $logs[0];
                if(!$insd){
                    DB::rollBack();
                    return ReturnMsg::getMsg(1025);
                }
                //更新goods表的sku和价格范围
                $goods = DB::table('goods')->where('id',$data['belongid'])->first();
                $goods = get_object_vars($goods);
                $gsku = json_decode($goods['gsku'],true);
                if(isset($gsku[$data['skuKey']])){
                    $tempkey = $gsku[$data['skuKey']];
                    foreach ($tempkey as $item) {
                        if($item['valid'] == $data['skuVal']){
                            DB::rollBack();
                            return ReturnMsg::getMsg(1029);
                        }
                    }
                }
                $arr = [
                  'keyName'  =>$key['name'],
                  'valid'    =>$data['skuVal'],
                  'valname'  =>$val['name']
                ];
                $gsku[$data['skuKey']][] = $arr;

                //获取goods表价格取值范围，并比较，换最大和最小的价格
                $data['gprice'] = $data['gprice']*100;
                if(!strstr($goods['gprice'],'~')){//说明只有一个商品，不存在最低价格与最高价格
                    if($goods['gprice'] > $data['gprice']){
                        $update['gprice'] = $data['gprice'].'~'.$goods['gprice'];
                    }else{
                        $update['gprice'] = $goods['gprice'].'~'.$data['gprice'];
                    }
                }else{//说明有多个商品，存在最低价格与最高价格
                    $reef = explode('~',$goods['gprice']);
                    if($data['gprice'] < $reef[0]){//替换最小价格
                        $update['gprice'] = $data['gprice'].'~'.$reef[1];
                    }else if ($data['gprice'] > $reef[1]){
                        $update['gprice'] = $reef[0].'~'.$data['gprice'];
                    }
                }
                $update['gsku'] = json_encode($gsku,JSON_UNESCAPED_UNICODE);
                $up = DB::table('goods')->where('id',$data['belongid'])->update($update);
                if(!$up){
                    DB::rollBack();
                    return ReturnMsg::getMsg(1024);
                }
                //记录日志
                Common::actionLog($log);
                DB::commit();
                return ReturnMsg::getMsg(0);
            }catch (\Exception $e) {
                //接收异常处理并回滚
                DB::rollBack();
                return ReturnMsg::getMsg(1026);
            }

        }


    }
    /***
     * 添加商品
     */
    public static function getBelongGoods($data){
        //判断类型是否一致
        $exit = DB::table('goods')
            ->get();
        if(!$exit){
            return ReturnMsg::getMsg(1021);
        }
        //判断是否存在此商品（根据名称和类型）
        $count = DB::table('categary')
            ->where('gname',$data['gname'])
            ->where('category_id',$data['cid'])
            ->where('category_id',$data['cid'])
            ->where('category_id',$data['cid'])
            ->count();
    }
    /***
     * 添加商品
     */
    public static function goodsstatus($data){
        //判断类型是否一致
        $exit = DB::table('goods_detail')->where('id',$data['id'])->first();
        if(!$exit){
            return ReturnMsg::getMsg(1030);
        }
        if(intval($exit->isdel) === intval($data['status'])){
            return ReturnMsg::getMsg(1031);
        }
        //修改此商品状态
        DB::connection()->enableQueryLog();
        $count = DB::table('goods_detail')->where('id',$data['id'])->update(['isdel' => strval(intval($data['status']))]);
        $logs = DB::getQueryLog();
        if(!$count){
            return ReturnMsg::getMsg(1032);
        }
        $log['actionName'] = $data['status']?"上架":'下架' . '商品:'.$data['id'];
        $log['sql'] = $logs[0];
        //记录日志
        Common::actionLog($log);
        return ReturnMsg::getMsg(0);
    }
    /***
     * 获取所属商品
     */
    public static function goodsBelong($data){
        $ee = DB::table('goods')
            ->where('category_id',$data['id'])
            ->get();
        $ee = Common::objToArr($ee);
        return ReturnMsg::getMsg(0,['list'=>$ee]);
    }

    public static function getSkuList($data){
        //获取商品所有类型
        $typeList = self::getTypeList([]);
        //获取所有属性key值
        $key = DB::table('skukey')->get();
        $keyList = Common::objToArr($key);
        //获取所有属性value值
        $val = DB::table('skuvalue')->get();
        $valList = Common::objToArr($val);
        return ReturnMsg::getMsg(0,['List'=>['typeList'=>$typeList['list'],'keyList'=>$keyList,'valList'=>$valList]]);
    }

    public static function getTypeList($inp){
        $pageIndex = isset($inp['pageIndex'])&&!empty($inp['pageIndex']) ? $inp['pageIndex'] : 0;
        $pageSize = isset($inp['pageSize'])&&!empty($inp['pageSize']) ? $inp['pageSize'] : 0;
        $query = DB::table('categary')
            ->select('user.name as uname', 'categary.name', 'categary.id', 'categary.pid', 'categary.created_time')
            ->leftJoin('user', 'user.id', '=', 'categary.operator');
        $count = $query->count();
        $res = $query->when($pageIndex, function ($query) use ( $pageIndex ,$pageSize) {
            return $query->offset(($pageIndex-1)*$pageSize)
                ->limit($pageSize);
        })->get();
        $ee = Common::objToArr($res);
        return ReturnMsg::getMsg(0,['list'=>$ee,'pageTotal'=>$count-1]);
    }

    public static function TypeAdd($data){
        //判断类型名称是否已存在
        $type = DB::table('categary')->where('name',$data['name'])->count();
        if($type){
            return ReturnMsg::getMsg(1005);
        }
        //判断pid是否已存在
        $pid = DB::table('categary')->where('id',$data['pid'])->count();
        if(!$pid){
            return ReturnMsg::getMsg(1006);
        }
        //添加类型
        $where['name'] = $data['name'];
        $where['pid'] = $data['pid'];
        $where['created_time'] = date('Y-m-d H:i:s');
        $where['operator'] = Common::getId();
        DB::connection()->enableQueryLog();
        $add = DB::table('categary')->insert($where);
        $logs = DB::getQueryLog();
        if(!$add){
            return ReturnMsg::getMsg(1007);
        }
        $log['actionName'] =  '增加商品类型:'.$data['name'];
        $log['sql'] = $logs[0];
        //记录日志
        Common::actionLog($log);
        return ReturnMsg::getMsg(0);
    }

    public static function TypeEdit($data){
        //判断类型名称是否已存在
        $type = DB::table('categary')->where('id',$data['id'])->first();
        if(!$type){
            return ReturnMsg::getMsg(1008);
        }
        //是否做修改
        if($data['pid'] == $type->pid && $data['name'] == $type->name ){
            return ReturnMsg::getMsg(1009);
        }
        //判断pid是否存在
        $pid = DB::table('categary')->where('id',$data['pid'])->count();
        if(!$pid){
            return ReturnMsg::getMsg(1006);
        }
        //判断名称是否已存在（除了当前的name）
        $pida = DB::table('categary')->where('name',$data['name'])->where('id','!=',$data['id'])->count();
        if($pida){
            return ReturnMsg::getMsg(1005);
        }
        //修改类型
        DB::connection()->enableQueryLog();
        $save = DB::table('categary')->where('id',$data['id'])->update([
            'name' =>$data['name'],
            'pid'  =>$data['pid'],
            'update_time'  =>date('Y-m-d H:i:s'),
            'operator'  =>$data['user']['id']
        ]);
        $logs = DB::getQueryLog();
        if(!$save){
            return ReturnMsg::getMsg(1010);
        }
        $log['actionName'] =  '编辑商品类型:'.$data['name'];
        $log['sql'] = $logs[0];
        //记录日志
        Common::actionLog($log);
        return ReturnMsg::getMsg(0);
    }

    /***
     * @param $data
     * 删除类型
     */
    public static function TypeDel($data){
        //判断名称是否已存在（除了当前的name）
        $pida = DB::table('categary')->where('id',$data['id'])->first();
        if(!$pida){
            return ReturnMsg::getMsg(1008);
        }
        $SonType = DB::table('categary')->where('pid',$data['id'])->count();
        if($SonType){
            return ReturnMsg::getMsg(1008);
        }
        DB::connection()->enableQueryLog();
        $del = DB::table('categary')->where('id',$data['id'])->delete();
        $logs = DB::getQueryLog();
        if(!$del){
            return ReturnMsg::getMsg(1012);
        }
        $log['actionName'] =  '删除商品类型:'.$pida->name;
        $log['sql'] = $logs[0];
        //记录日志
        Common::actionLog($log);
        return ReturnMsg::getMsg(0);
    }

    /***
     * 获取sku属性键名
     */
    public static function goodsSku($inp){

        $query = DB::table('skukey')
            ->select('user.name as uname', 'categary.name as cname', 'skukey.*')
            ->leftJoin('categary', 'categary.id', '=', 'skukey.cid')
            ->leftJoin('user', 'user.id', '=', 'skukey.operator');
        $count = $query->count();
        $res= $query->offset(($inp['pageIndex']-1)*$inp['pageSize'])
            ->limit($inp['pageSize'])
            ->get();
        $ee = Common::objToArr($res);
        if(!$ee){
            return ReturnMsg::getMsg(0,['list'=>[]]);
        }
        //获取查询出来的id
        $idlist = self::getIdList($ee);
        //根据id去连表拼接子数据（属性值数据）
        $newRes = self::getNewList($ee,$idlist);
        //获取类型列表
        $TypeRes = self::getTypeList($inp);
        if($TypeRes['code'] !== 0 || empty($TypeRes['list'])){
            $TypeRes = [];
        }else{
            $TypeRes = $TypeRes['list'];
        }
        return ReturnMsg::getMsg(0,['list'=>$newRes,'pageTotal'=>$count,'goodsType'=>$TypeRes]);
    }

    /***
     * 重新组装id列表
     */
    public static function getIdList(Array $data){
        $id = array();
        foreach ($data as $k => $v){
            $id[] = $v['id'];
        }
        return $id;
    }

    /***
     *重新组装sku值列表
     */
    public static function getNewList(Array $Res,Array $IdList){
        $Son = DB::table('skuvalue')->whereIn('kid',$IdList)->get();
        $tempArr = array();
        foreach ($Res as $ks => $key) {
            $Res[$ks]['content'] = [];
        }
        if(!$Son || is_null($Son)){
            return $Res;
        }
        $Son = Common::objToArr($Son);
        foreach ($Son as  $item) {
            $tempArr[$item['kid']][] = $item;
        }
        if(!empty($tempArr)){
            foreach ($Res as $ks => $key) {
                if(!isset($tempArr[$key['id']])){
                    $Res[$ks]['content'] = [];
                }else{
                    $Res[$ks]['content'] = $tempArr[$key['id']];
                }
            }
        }
        return $Res;
    }

    /***
     * 添加商品sku
     */
    public static function SkuAdd($data){
        $count = DB::table('skukey')
            ->where('cid',$data['pid'])
            ->where('name',$data['name'])
            ->count();
        if($count){
            return ReturnMsg::getMsg('1013');
        }
        DB::connection()->enableQueryLog();
        $res = DB::table('skukey')->insert([
            'name' => $data['name'],
            'cid' => $data['pid'],
            'created_time' => date('Y-m-d H:i:s'),
            'operator' => $data['user']['id']
        ]);
        $logs = DB::getQueryLog();
        if(!$res){
            return ReturnMsg::getMsg('1014');
        }
        $log['actionName'] =  '添加商品sku:'.$data['name'];
        $log['sql'] = $logs[0];
        //记录日志
        Common::actionLog($log);
        return ReturnMsg::getMsg(0);
    }

    public static function SkuValAdd($data){
        $count = DB::table('skuvalue')
            ->where('kid',$data['id'])
            ->where('name',$data['name'])
            ->count();
        if($count){
            return ReturnMsg::getMsg('1015');
        }
        DB::connection()->enableQueryLog();
        $res = DB::table('skuvalue')->insert([
            'name' => $data['name'],
            'kid' => $data['id'],
            'created_time' => date('Y-m-d H:i:s'),
            'operator' => $data['user']['id']
        ]);
        $logs = DB::getQueryLog();
        if(!$res){
            return ReturnMsg::getMsg('1016');
        }
        $log['actionName'] =  '添加sku值:'.$data['name'];
        $log['sql'] = $logs[0];
        //记录日志
        Common::actionLog($log);
        return ReturnMsg::getMsg(0);
    }

    /***
     * @param $data
     * @return array
     * 删除属性值
     */
    public static function SkuValDel($data){
        $count = DB::table('skuvalue')
            ->where('id',$data['id'])
            ->count();
        if(!$count){
            return ReturnMsg::getMsg('1017');
        }
        DB::connection()->enableQueryLog();
        $res = DB::table('skuvalue')->where('id',$data['id'])->delete();
        $logs = DB::getQueryLog();
        if(!$res){
            return ReturnMsg::getMsg('1018');
        }
        $log['actionName'] =  '删除sku值-id为:'.$data['id'];
        $log['sql'] = $logs[0];
        //记录日志
        Common::actionLog($log);
        return ReturnMsg::getMsg(0);
    }
    /***
     * @param $data
     * @return array
     * 获取属性key
     */
    public static function getskukey($data){
        $count = DB::table('categary')
            ->where('id',$data['id'])
            ->count();
        if(!$count){
            return ReturnMsg::getMsg('1019');
        }
        $res = DB::table('skukey')
            ->where('cid',$data['id'])
            ->get();
        $res = Common::objToArr($res);
        return ReturnMsg::getMsg(0,['list'=>$res]);
    }
    /***
     * @param $data
     * @return array
     * 获取属性val
     */
    public static function getskuval($data){
        $count = DB::table('skukey')
            ->where('id',$data['id'])
            ->count();
        if(!$count){
            return ReturnMsg::getMsg('1020');
        }
        $res = DB::table('skuvalue')
            ->where('kid',$data['id'])
            ->get();
        $res = Common::objToArr($res);
        return ReturnMsg::getMsg(0,['list'=>$res]);
    }

}
