<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:16
 */

namespace App\Repositories;

use App\Constants\Mq\RoutingKey;
use App\Libs\Util;
use Carbon\Carbon;
use Exception;
use App\Models\Bom\Bom as BomModel;
use App\Models\Bom\BomDetail as BomDetailModel;
use App\Models\Sku\Sku as SkuModel;
use App\Constants\Db\Tables\Base\Bom as BomConst;
use App\Constants\Db\Tables\Base\BomDetail as BomDetailConst;
use App\Constants\Db\Tables\Base\Sku as SkuConst;
use Illuminate\Support\Facades\DB;

class Bom{
    /**
     * @param $skuId
     * @param array $skuList
     * @throws Exception
     */
    private static function basicCheck($skuId, array $skuList){
        $deliveryWays = BomDetailConst::$deliveryWays;
        $skuId = strtoupper($skuId);
        $skuIds = [];
        foreach($skuList as $idx=>$sku){
            $_skuId = array_get($sku, 'sku_id');
            if(empty($_skuId)){
                throw new Exception(trans("message.SON_SKU_CAN_NOT_BE_EMPTY"));
            }
            $_skuId = strtoupper($_skuId);
            $skuList[$idx]['sku_id'] = $_skuId;
            if($_skuId == $skuId){
                throw new Exception(trans("message.SON_FATHER_SKU_COMMON"));
            }
            $_deliveryWay = array_get($sku, 'delivery_way');
            if(!array_key_exists($_deliveryWay, $deliveryWays)){
                throw new Exception(trans('message.FEED_METHOD_PARAM_ERROR'));
            }
            $_num = array_get($sku, 'num', 0);
            if(!is_integer($_num) || $_num <= 0){
                throw new Exception(trans("message.PROPORT_NUMBER_TRAN_ERROR"));
            }
            if(in_array($_skuId, $skuIds)){
                throw new Exception(trans("message.SON_SKU_HAVE_COMMON",array("sku"=>$_skuId)));
            }
            $skuIds[] = $_skuId;
        }
        $skuIds[] = $skuId;
        $dbSkuList = Sku::listBySkuIds($skuIds, SkuConst::STATUS_ENABLED);
        $dbSkuIds = array_column($dbSkuList, SkuConst::SKU_ID);
        $diffSkuIds = array_diff($skuIds, $dbSkuIds);
        if(!empty($diffSkuIds)){
            throw new Exception(trans("message.SKU_NOT_EXSIST",array("sku"=>implode('、', $diffSkuIds))));
        }
        foreach($dbSkuList as $dbSku){
            if($dbSku[SkuConst::SKU_ID] != $skuId
                && $dbSku[SkuConst::STOCK_TYPE] == SkuConst::STOCK_TYPE_FINISHED_GOODS){
                throw new Exception(trans("message.SON_SKU_TYPE_WRONG",array("sku"=>$dbSku[SkuConst::SKU_ID]))); 
            }
        }
    }

    /**
     * 重复检查
     * @param $skuId
     * @param $skuList
     * @param null $bomId
     * @return bool
     * @throws Exception
     */
    public static function duplicateCheck($skuId, $skuList, $bomId = null){
        $builder = BomModel::where(BomConst::SKU_ID, $skuId);
        if(!empty($bomId)){
            $builder->where(BomConst::SKU_ID, '!=', $bomId);
        }
        //存在禁用的也不允许添加，操作启用即可
        //$builder->where(BomConst::STATUS, BomConst::STATUS_ENABLED);
        $boms = $builder->get()->toArray();
        if(empty($boms)){
            return true;
        }
        $skuNumMap = [];
        foreach ($skuList as $item){
            $skuNumMap[$item['sku_id']] = $item['num'];
        }
        $bomDetailMap = self::getBomDetailByBomIds(array_column($boms, BomConst::ID));
        $bomDetailSkuCount = count($skuList);
        foreach($boms as $bom){
            $bomDetail = array_get($bomDetailMap, $bom[BomConst::ID], []);
            if(count($bomDetail) !== $bomDetailSkuCount){
                continue;
            }
            $sameNum = 0;
            foreach($bomDetail as $item){
                $_skuId = $item[BomDetailConst::SKU_ID];
                $_num = $item[BomDetailConst::NUM];
                if(isset($skuNumMap[$_skuId]) && $skuNumMap[$_skuId] == $_num){
                    $sameNum++;
                }
            }
            if($bomDetailSkuCount == $sameNum){
                throw new Exception(trans("message.BOM_SON_SKU_COMMON",array("sku"=>$bom[BomConst::ID])));
            }
        }
        return true;
    }

    /**
     * @param $skuId
     * @param $userId
     * @param array $skuList
     * @return null
     * @throws Exception
     */
    public static function create($skuId, $userId, array $skuList,$bomName=''){
        self::basicCheck($skuId, $skuList);
        self::duplicateCheck($skuId, $skuList);
        if(!$sku = Sku::getBySkuId($skuId)) {
            throw new Exception(trans("message.FATHER_SON_NOT_EXSIST"));
        }
        BomModel::beginTransaction();
        try{
            $bom = self::createBasic($skuId, $userId,$bomName,$sku["org_ids"]);
            $bomId = $bom[BomConst::ID];
            self::createDetail($bomId, $skuList);
            $mqData = [
                'bom_id' => $bomId,
                'user_id' => $userId,
                'sku_id' => $skuId,
                'bom_name'=>$bomName,
                'detail' => $skuList,
            ];
            if(!Util::sendToMq($mqData, RoutingKey::BOM_CREATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            BomModel::rollBack();
            throw $e;
        }
        BomModel::commit();
        return $bomId;
    }

    /**
     * @param $skuId
     * @param $userId
     * @return null
     * @throws Exception
     */
    private static function createBasic($skuId, $userId,$bomName,$orgIds){
        $bom = BomModel::create([
            BomConst::SKU_ID => $skuId,
            BomConst::CREATE_USER_ID => $userId,
            BomConst::STATUS => BomConst::STATUS_ENABLED,
            BomConst::BOM_NAME => $bomName,
            BomConst::ORG_IDS => $orgIds,
        ]);
        $bomId = $bom[BomConst::ID] ?? null;
        if(empty($bomId)){
            throw new Exception(trans("message.SAVE_FAIL"));
        }
        return $bom;
    }

    /**
     * @param $bomId
     * @param $skuList
     * @return array
     * @throws Exception
     */
    private static function createDetail($bomId, $skuList){
        $currTime = Carbon::now()->timestamp;
        $rows = [];
        foreach($skuList as $item){
            $rows[] = [
                BomDetailConst::BOM_ID => $bomId,
                BomDetailConst::SKU_ID => $item['sku_id'],
                BomDetailConst::NUM => $item['num'],
                BomDetailConst::STATUS => BomDetailConst::STATUS_ENABLED,
                BomDetailConst::DELIVERY_WAY => $item['delivery_way'],
                BomDetailConst::CREATE_TIME => $currTime,
                BomDetailConst::UPDATE_TIME => $currTime,
            ];
        }
        if(!BomDetailModel::insert($rows)){
            throw new Exception(trans("message.SAVE_FAIL"));
        }
        return $rows;
    }

    /**
     * @param $bomId
     * @param $status
     * @param $userId
     * @return bool
     * @throws Exception
     */
    public static function updateStatus($bomId, $status, $userId){
        $bom = self::getBomBasic($bomId);
        $bomId = array_get($bom, BomConst::ID);
        if(empty($bomId)){
            throw new Exception(trans("message.BOM_NOT_EXSITS"));
        }
        if($bom[BomConst::STATUS] == $status){
            throw new Exception(trans("message.STATUS_NOT_NEED_UPDATE"));
        }
        $data = [
            BomConst::STATUS => $status,
            BomConst::UPDATE_TIME => time(),
        ];
        BomModel::beginTransaction();
        try{
            if(!BomModel::where(BomConst::ID, $bomId)->update($data)){
                throw new Exception(trans("message.UPDATE_FAIL"));
            }
            $mqData = [
                'bom_id' => $bomId,
                'status' => $status,
                'user_id' => $userId,
            ];
            if(!Util::sendToMq($mqData, RoutingKey::BOM_UPDATE_STATUS)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            BomModel::rollBack();
            throw $e;
        }
        BomModel::commit();
        return true;
    }

    /**
     * @param $bomId
     * @param $userId
     * @param $deliveryWays
     * @return bool
     * @throws Exception
     */
    public static function update($bomId, $userId, $deliveryWays,$bomName=''){
        $bom = self::getBomBasic($bomId);
        $bomId = array_get($bom, BomConst::ID);
        if(empty($bomId)){
            throw new Exception(trans("message.BOM_NOT_EXSITS"));
        }
        if($bom[BomConst::STATUS] == BomConst::STATUS_DISABLED){
            throw new Exception(trans("message.BOM_DISABLE"));
        }
        $detail = self::getBomDetail($bomId);
        $detail = array_pluck($detail, null, BomDetailConst::SKU_ID);
        $currTime = time();
        $data = [
            BomConst::BOM_NAME => $bomName,
            BomConst::UPDATE_TIME => $currTime,
        ];
        BomDetailModel::beginTransaction();
        $newDetail = [];
        try{
            if(!BomModel::where(BomConst::ID, $bomId)->update($data)){
                throw new Exception(trans("message.UPDATE_BOM_NAME_FAIL"));
            }
            foreach($deliveryWays as $item){
                $_skuId = strtoupper($item['sku_id'] ?? '');
                if(empty($_skuId)){
                    throw new Exception(trans("message.SON_SKU_CAN_NOT_BE_EMPTY"));
                }
                if(!array_key_exists($_skuId, $detail)){
                    throw new Exception(trans("message.SKU_NOT_EXSIST",array("sku"=>$_skuId)));
                }
                $_deliveryWay = $item['delivery_way'] ?? '';
                if(!array_key_exists($_deliveryWay, BomDetailConst::$deliveryWays)){
                    throw new Exception(trans('message.FEED_METHOD_PARAM_ERROR'));
                }
                $data = [
                    BomDetailConst::DELIVERY_WAY => $_deliveryWay,
                    BomDetailConst::UPDATE_TIME => $currTime,
                ];
                /**
                if($_deliveryWay != $item[BomDetailConst::DELIVERY_WAY]
                    && !BomDetailModel::where(BomDetailConst::BOM_ID,$bomId)::where(BomDetailConst::SKU_ID,$_skuId)->update($data)){
                    throw new Exception("更新子物料{$_skuId}发料方式失败");
                }
                 */
                if(!BomDetailModel::where(BomDetailConst::BOM_ID,$bomId)->where(BomDetailConst::SKU_ID,$_skuId)->update($data)){
                    //&& !BomDetailModel::where(BomDetailConst::ID)->update($data)){
                    throw new Exception(trans("message.UPDATE_FAIL"));
                }
                $newDetail[] = [
                    'sku_id' => $_skuId,
                    'delivery_way' => $_deliveryWay,
                ];
            }
            $mqData = [
                'bom_id' => $bomId,
                'user_id' => $userId,
                'bom_name'=>$bomName,
                'detail' => $newDetail,
            ];
            if(!Util::sendToMq($mqData, RoutingKey::BOM_UPDATE_DELIVERY_WAY)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            BomDetailModel::rollBack();
            throw $e;
        }
        BomDetailModel::commit();
        return true;
    }

    /**
     * @param $skuId
     * @return mixed|static
     */
    public static function getByBomIdOrSkuId($bomId, $skuId, $status = null,$orgId=null){
        if(empty($bomId) && empty($skuId)) {
            throw new Exception(trans("message.BOM_ID_AND_SKU_ID_ALL_EMPTY"));
        }
        $bom = self::getBomBasic($bomId, $skuId, $status,$orgId);
        $bomId = array_get($bom, BomConst::ID);
        if(!empty($bomId)){
            $bom['detail'] = self::getBomDetail($bomId);
        }
        return $bom;
    }

    /**
     * @param null $bomId
     * @param null $skuId
     * @param null $status
     * @return mixed
     */
    public static function getBomBasic($bomId = null, $skuId = null, $status = null,$orgId=null){
        $builder = new BomModel();
        if(!empty($bomId)){
            $builder = $builder->where(BomConst::ID, $bomId);
        }
        if(!empty($skuId)){
            $builder = $builder->where(BomConst::SKU_ID, $skuId);
        }
        if(!empty($status)){
            $builder = $builder->where(BomConst::STATUS, $status);
        }
        if(!empty($orgId)){
            $ordIds = pow(2, ($orgId - 1));
            $builder = $builder->whereRaw(BomConst::ORG_IDS . "&" . $ordIds . "=" . $ordIds);
        }
        return $builder->first();
    }

    /**
     * @param $bomId
     * @return mixed
     */
    public static function getBomDetail($bomId){
        return BomDetailModel::where(BomDetailConst::BOM_ID, $bomId)->get()->toArray();
    }

    /**
     * @param $bomIds
     * @return array
     */
    private static function getBomDetailByBomIds($bomIds){
        $items = BomDetailModel::whereIn(BomDetailConst::BOM_ID, $bomIds)->get()->toArray();
        $map = [];
        if(!empty($items)){
            foreach ($items as $item){
                $map[$item[BomDetailConst::BOM_ID]][] = $item;
            }
        }
        return $map;
    }
    
    /**
     * @param $skuIds
     * @return array
     */
    private static function getBomDetailBySkuIds($skuIds){
        return  BomDetailModel::whereIn(BomDetailConst::SKU_ID, $skuIds)->get()->toArray();
    }

    /**
     * @param array $bomIds
     * @param null $status
     * @return mixed
     */
    public static function listByBomIds(array $bomIds, $status = null,$orgId=null){
        $builder = BomModel::whereIn(BomConst::ID, $bomIds);
        if(!is_null($status)){
            $builder->where(BomConst::STATUS, $status);
        }
        if(!empty($orgId)){
            $ordIds = pow(2, ($orgId - 1));
            $builder = $builder->whereRaw(BomConst::ORG_IDS . "&" . $ordIds . "=" . $ordIds);
        }
        return $builder->get()->toArray();
    }
    
    /**
     * 供应链管理后台查询
     * @param type $fatherSkuId 父SKUID
     * @param type $fatherSkuName 父SKU名称
     * @param type $sonSkuId 子SKUID
     * @param type $sonSkuName 子SKU名称
     * @param type $createUserId 创建人ID
     * @param type $createTimeStart 开始时间
     * @param type $createTimeEnd 结束时间
     * @param type $orgId 组织ID 1:东峡大通,2:HK
     * @param type $page 页数
     * @param type $perPage 每页大小
     * @param type $status 状态
     * @return type array
     */
    public static function getList($bomId,$fatherSkuId, $fatherSkuName,$sonSkuId,$sonSkuName
                    ,$createUserId,$createTimeStart,$createTimeEnd,$orgId,$page,$perPage,$status){
        $bomIds = null;
        $skuIds   = null;
        if(!empty($fatherSkuName) || !empty($fatherSkuId)) {
            $skuIds = self::getSkuIdsByIdAndName($fatherSkuName,$fatherSkuId);
        }
        if(!empty($sonSkuId) || !empty($sonSkuName)) {
            $bomIds = self::getBomIdsByDetail($sonSkuName,$sonSkuId);
        }
        return  self::getBomListAndCount($bomId,$skuIds,$bomIds,$createUserId,$createTimeStart,
                $createTimeEnd,$orgId,$page,$perPage,$status);
    }
    /**
     * 拼接sql 查询bom信息
     * @param type $skuIds
     * @param type $bomIds
     * @param type $createUserId
     * @param type $createTimeStart
     * @param type $createTimeEnd
     * @param type $orgId
     * @param type $page
     * @param type $perPage
     * @param type $status
     * @return array
     */
    public static function getBomListAndCount($bomId,$skuIds,$bomIds,$createUserId,$createTimeStart,
                $createTimeEnd,$orgId,$page,$perPage,$status){
        $builder = BomModel::orderBy(BomConst::ID, "desc");
        $return  = array("total"=>0,"list"=>[]);
        if(is_array($bomIds)) {
            if(!empty($bomIds)) {
                $builder->whereIn(BomConst::ID, $bomIds);
            } else {
                return $return;
            }
        }
        if(is_array($skuIds)) {
            if(!empty($skuIds)) {
                $builder->whereIn(BomConst::SKU_ID, $skuIds);
            } else {
                return $return;
            }
        }
        !empty($bomId) && $builder->where(BomConst::ID, $bomId);
        !empty($createUserId) && $builder->where(BomConst::CREATE_USER_ID, $createUserId);
        !empty($createTimeStart) && $builder->where(BomConst::CREATE_TIME, ">=",$createTimeStart);
        !empty($createTimeEnd) && $builder->where(BomConst::CREATE_TIME, "<=",$createTimeEnd);
        if(!empty($orgId)) {
            $ordIds = pow(2,($orgId-1));
            $builder->whereRaw(BomConst::ORG_IDS."&".$ordIds."=".$ordIds);
        }
        !empty($status) && $builder->where(BomConst::STATUS, $status);
        $return["total"] = $builder->count();
        $list = $builder->offset(($page-1)*$perPage)->take($perPage)->get()->toArray();
        if(!empty($list)) {
            $detail = self::getBomDetailByBomIds(array_column($list, BomConst::ID));
            foreach($list as $k=>$v ) {
                $list[$k]["detail"] = isset($detail[$v[BomConst::ID]])?$detail[$v[BomConst::ID]]:array();
            }
        }
        $return["list"] = $list;
        return $return;
    }
    /**
     * 
     * @param type $sonSkuName
     * @param type $sonSkuId
     * @return array
     */
    private static function getSkuIdsByIdAndName($fatherSkuName,$fatherSkuId) {
        $skuIds = array();
        if(!empty($fatherSkuName)) {
             $sku = Sku::getSkuByLikeName($fatherSkuName);
             $skuIds= array_column($sku, SkuConst::SKU_ID);
             if(!empty($fatherSkuId)) {
                     $skuIds = in_array($fatherSkuId, $skuIds)?[$fatherSkuId]:array();
             }
        } elseif(!empty($fatherSkuId)) {
            $skuIds[] = $fatherSkuId;
        }
        return $skuIds;
    }
    /**
     * 
     * @param type $sonSkuName
     * @param type $sonSkuId
     * @return array
     */
    private static function getBomIdsByDetail($sonSkuName,$sonSkuId) {
        $bomIds = array();
        $skuIds = array();
        if(!empty($sonSkuName)) {
            $sku = Sku::getSkuByLikeName($sonSkuName);
            $skuIds= array_column($sku, SkuConst::SKU_ID);
            if(!empty($sonSkuId)) {
                    $skuIds = in_array($sonSkuId, $skuIds)?[$sonSkuId]:array();
            }
        } elseif(!empty($sonSkuId)) {
            $skuIds[] = $sonSkuId;
        }
        if(!empty($skuIds)) {
            $bomDetail = self::getBomDetailBySkuIds($skuIds);
            $bomIds = array_column($bomDetail, BomDetailConst::BOM_ID);
        }
        return $bomIds;
    }
    
}
