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
use App\Models\Sku\Sku as SkuModel;
use App\Models\Bom\Bom as BomModel;
use App\Constants\Db\Tables\Base\Sku as SkuConst;
use App\Constants\Db\Tables\Base\Code as CodeConst;
use App\Constants\Db\Tables\Base\CodeVersion as CodeVersionConst;
use App\Constants\Db\Tables\Base\Bom as BomConst;

use App\Models\SkuPn\SkuPn as SkuPnModel;
use App\Constants\Db\Tables\Base\SkuPn as SkuPnConst;

use App\Constants\Db\Tables\Base\Pn as PnConst;
use Exception;

class Sku{
    /**
     * @param $skuId
     * @param $name
     * @param $type
     * @param $codeId
     * @param $stockType
     * @param $hardwareVersionId
     * @param $createUserId
     * @param $format
     * @param int $weight
     * @return array|static
     * @throws Exception
     */
    public static function create($skuId, $name, $type, $codeId, $stockType, $hardwareVersionId, $createUserId, $format, $weight = 0,$orgIds=array(),$pnIds=array()){
        $skuId = strtoupper($skuId);
        $dbSku = self::getByName($name);
        if(!empty($dbSku)){
            throw new Exception(trans("message.SKU_NAME_EXSITS"));
        }
        $dbSku = self::getBySkuId($skuId);
        if(!empty($dbSku)){
            throw new Exception(trans("message.SKU_CODE_EXSITS"));
        }
        $dbCode = Code::getCodeById($codeId, CodeConst::STATUS_ENABLED);
        if(empty($dbCode)){
            throw new Exception(trans("message.SKU_PRODUCT_TYPE_NO_EXSIST"));
        }
        $dbVersion = Code::getVersionById($hardwareVersionId);
        if(empty($dbVersion)){
            throw new Exception(trans("message.SKU_HARD_VERSION_NO_EXSIT"));
        }
        if($dbVersion[CodeVersionConst::CODE_TYPE] != $dbCode[CodeConst::CODE_TYPE]){
            throw new Exception(trans("message.SKU_TYPE_AND_HARD_VERSION_NO_MATCH"));
        }
        if(!self::checkOrgIds($orgIds)) {
            throw new Exception(trans("message.ORG_ID_WRONG"));
        }
        
        
        //format字段目前不方便验证，也不是很有必要验证，暂时忽略
        $sku = [
            SkuConst::NAME => $name,
            SkuConst::SKU_ID => $skuId,
            SkuConst::TYPE => $type,
            SkuConst::PRODUCT_TYPE => $codeId,
            SkuConst::STOCK_TYPE => $stockType,
            SkuConst::HARDWARE_VERSION => $hardwareVersionId,
            SkuConst::CREATE_USER_ID => $createUserId,
            SkuConst::FORMAT => $format,
            SkuConst::WEIGHT => $weight,
            SkuConst::STATUS => SkuConst::STATUS_ENABLED,
            SkuConst::ORG_IDS => self::getOrgIds($orgIds),
        ];
        SkuModel::beginTransaction();
        try{
            $sku = SkuModel::create($sku);
            if(!$sku){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            self::createSkuToPn($skuId,$pnIds);
            $mqData = $sku->toArray();
            if(!Util::sendToMq($mqData, RoutingKey::SKU_CREATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            SkuModel::rollBack();
            throw $e;
        }
        SkuModel::commit();
        return $sku;
    }
    /**
     * 
     * @param type $skuId
     * @param type $pnIds
     * @return type
     * @throws Exception
     */
    private static function createSkuToPn($skuId, $pnIds){
        if(empty($pnIds)) {
            return true;
        }
        $rows = [];
        foreach($pnIds as $item){
            $rows[] = [
                SkuPnConst::SKU_ID => $skuId,
                SkuPnConst::PN_ID => $item,
            ];
        }
        if(!SkuPnModel::insert($rows)){
            throw new Exception('关联工程PN失败');
        }
        return $rows;
    }

    /**
     * @param $skuId
     * @param $name
     * @param $codeId
     * @param $stockType
     * @param $hardwareVersionId
     * @param $updateUserId
     * @param $format
     * @param int $weight
     * @return bool
     * @throws Exception
     */
    public static function update($skuId, $name,$type, $codeId, $stockType, $hardwareVersionId, $updateUserId, $format, $weight = 0,$orgIds=array(),$pnIds=array()){
        $skuId = strtoupper($skuId);
        $dbSku = self::getBySkuId($skuId);
        if(empty($dbSku)){
            throw new Exception(trans("message.SKU_NO_EXSIT"));
        }
        if(!empty(self::getByName($name, null, $skuId))){
            throw new Exception(trans("message.SKU_NAME_EXSITS"));
        }

        $dbCode = Code::getCodeById($codeId, CodeConst::STATUS_ENABLED);
        if(empty($dbCode)){
            throw new Exception(trans("message.SKU_PRODUCT_TYPE_NO_EXSIST"));
        }
        $dbVersion = Code::getVersionById($hardwareVersionId);
        if(empty($dbVersion)){
            throw new Exception(trans("message.SKU_HARD_VERSION_NO_EXSIT"));
        }
        if($dbVersion[CodeVersionConst::CODE_TYPE] != $dbCode[CodeConst::CODE_TYPE]){
            throw new Exception(trans("message.SKU_TYPE_AND_HARD_VERSION_NO_MATCH"));
        }
        if(!self::checkOrgIds($orgIds)) {
            throw new Exception(trans("message.ORG_ID_WRONG"));
        }
        //format字段目前不方便验证，也不是很有必要验证，暂时忽略
        $data = [
            SkuConst::NAME => $name,
            SkuConst::TYPE => $type,
            SkuConst::PRODUCT_TYPE => $codeId,
            SkuConst::STOCK_TYPE => $stockType,
            SkuConst::HARDWARE_VERSION => $hardwareVersionId,
            //SkuConst::CREATE_USER_ID => $updateUserId,
            SkuConst::FORMAT => $format,
            SkuConst::WEIGHT => $weight,
            SkuConst::ORG_IDS => self::getOrgIds($orgIds),
        ];
        SkuModel::beginTransaction();
        try{
            if(!SkuModel::where(SkuConst::SKU_ID, $skuId)->first()->update($data)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            if(BomModel::where(BomConst::SKU_ID,$skuId)->get()->toArray()) {
                    $bomOrg[BomConst::ORG_IDS] = self::getOrgIds($orgIds);
                    if(!BomModel::where(BomConst::SKU_ID,$skuId)->update($bomOrg)) {
                            throw new Exception(trans("message.UPDATE_FAIL"));
                    }
            }
            if(SkuPnModel::where(SkuPnConst::SKU_ID,$skuId)->get()->toArray()) {
                    if(!SkuPnModel::where(SkuPnConst::SKU_ID,$skuId)->delete()) {
                            throw new Exception('更新SKU,PN关系失败');
                    }
            }
            self::createSkuToPn($skuId,$pnIds);
            $mqData = $data;
            $mqData[SkuConst::SKU_ID] = $skuId;
            if(!Util::sendToMq($mqData, RoutingKey::SKU_UPDATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            SkuModel::rollBack();
            throw $e;
        }
        SkuModel::commit();
        return true;
    }

    /**
     * @param $skuId
     * @param $status
     * @param $updateUserId
     * @throws Exception
     */
    public static function updateStatus($skuId, $status, $updateUserId){
        $skuId = strtoupper($skuId);
        $sku = self::getBySkuId($skuId);
        if(empty($sku)){
            throw new Exception(trans("message.SKU_NO_EXSIT"));
        }
        if($sku[SkuConst::STATUS] == $status){
            throw new Exception(trans("message.UPDATE_STATUS_NO_NEED"));
        }
        SkuModel::beginTransaction();
        try{
            $sku[SkuConst::STATUS] = $status;
            if(!$sku->save()){
                throw new Exception(trans("message.UPDATE_FAIL"));
            }
            $mqData = [
                'sku_id' => $skuId,
                'status' => $status,
            ];
            if(!Util::sendToMq($mqData, RoutingKey::SKU_UPDATE_STATUS)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            SkuModel::rollBack();
            throw $e;
        }
        SkuModel::commit();
    }

    /**
     * 
     * @param array $skuIds
     * @param type $status
     * @param type $orgId
     * @param type $fields
     * @return type
     */
    public static function listBySkuIds(array $skuIds, $status = null, $orgId = null ,$fields = array()){
        $builder = SkuModel::whereIn(SkuConst::SKU_ID, $skuIds);
        if(!is_null($status)){
            $builder->where(SkuConst::STATUS, $status);
        }
        if(!empty($orgId)) {
            $orgIds = pow(2,($orgId-1));
            $builder->whereRaw(SkuConst::ORG_IDS."&".$orgIds."=".$orgIds);
        }
        if (!empty($fields) && is_array($fields)) {
            $fillable = $builder->getModel()->getFillable();
            foreach ($fields as $v) {
                if (!in_array($v, $fillable)) {
                    throw new Exception(trans("message.FIELD_NO_EXSIT",array("field"=>$v)));
                }
            }
            $builder->select($fields);
        }
        return $builder->get()->toArray();
    }

    /**
     * @param array $types
     * @param null $status
     * @return mixed
     */
    public static function listByTypes(array $types, $status = null, $orgId = null) {
        $builder = SkuModel::whereIn(SkuConst::TYPE, $types);
        if (!is_null($status)) {
            $builder->where(SkuConst::STATUS, $status);
        }
        if (!is_null($orgId)) {
            $orgIds = pow(2, ($orgId - 1));
            $builder->whereRaw(SkuConst::ORG_IDS . "&" . $orgIds . "=" . $orgIds);
        }
        return $builder->get()->toArray();
    }

    /**
     * @param $skuId
     * @param null $status
     * @return mixed
     */
    public static function getBySkuId($skuId, $status = null,$orgId = null){
        $builder = SkuModel::where(SkuConst::SKU_ID, $skuId);
        if(!is_null($status)){
            $builder->where(SkuConst::STATUS, $status);
        }
        if(!is_null($orgId)){
            $ordIds = pow(2, ($orgId - 1));
            $builder->whereRaw(SkuConst::ORG_IDS . "&" . $ordIds . "=" . $ordIds);
        }
        return $builder->first();
    }

    /**
     * @param $name
     * @param null $status
     * @param null $exceptSkuId
     * @return mixed
     */
    public static function getByName($name, $status = null, $exceptSkuId = null){
        $builder = SkuModel::where(SkuConst::NAME, $name);
        if(!is_null($status)){
            $builder->where(SkuConst::STATUS, $status);
        }
        if(!is_null($exceptSkuId)){
            $builder->where(SkuConst::SKU_ID, '!=', $exceptSkuId);
        }
        return $builder->first();
    }
    /**
     * @param $name
     * @param null $status
     * @return mixed
     */
    public static function getSkuByLikeName($name, $status = null){
        $builder = SkuModel::where(SkuConst::NAME, "like","%".addslashes($name)."%");
        if(!is_null($status)){
            $builder->where(SkuConst::STATUS, $status);
        }
        return $builder->get()->toArray();
    }
    /**
     * 
     * @param type $skuBsns
     * @throws Exception
     * @return array
     */
    public static function checkBsnsBySku($skuBsns) {
        if(count($skuBsns) > 200) {
            throw new Exception(trans("message.REQUEST_NOT_MORE_THAN"));
        }
       
       
        $skuIds = array_column($skuBsns, "sku_id");
        $bsns   = array_column($skuBsns, "bsn");
        if (empty($skuIds) && empty($bsns)) {
            throw new Exception("message.STUCK_WRONG");
        }
        $skus   = self::listBySkuIds($skuIds);
        if(empty($skus)) {
            throw new Exception(trans("message.GET_SKU_INFO_FAIL"));
        }
        $codes = Code::listCodeByIds(array_column($skus, SkuConst::PRODUCT_TYPE));
        $codes = array_column($codes, CodeConst::CODE_TYPE, CodeConst::ID);
        
        $codeVersions = Code::listVersionByIds(array_column($skus, SkuConst::HARDWARE_VERSION));
        
        $codeVersions = array_column($codeVersions,CodeVersionConst::CODE,CodeVersionConst::ID);
        
        $hardwareCodes = array();
        $productTypes  = array();
        foreach($skus as $val) {
            $hardwareCodes[$val[SkuConst::SKU_ID]] = $codeVersions[$val[SkuConst::HARDWARE_VERSION]];
            $productTypes[$val[SkuConst::SKU_ID]] = $codes[$val[SkuConst::PRODUCT_TYPE]];
        }
        foreach($skuBsns as $k=>$val) {
            if(strlen($val["bsn"]) <3 ) {
                $skuBsns[$k]["res"] = 0;
                continue;
            }
            $bsn = str_split($val["bsn"]);
            $skuId = $val["sku_id"];
            if($bsn[0] == $productTypes[$skuId] && strval($hardwareCodes[$skuId]) == "0") {
                $skuBsns[$k]["res"] = 1;
                continue;
            }
            if($bsn[0] != $productTypes[$skuId] || $bsn[2] != $hardwareCodes[$skuId]) {
                $skuBsns[$k]["res"] = 0;
                continue;
            }
            $skuBsns[$k]["res"] = 1;
        }
        return $skuBsns;
    }

    /**
     * 查询sku列表
     * @param type $skuId
     * @param type $name
     * @param type $type
     * @param type $productType
     * @param type $stockType
     * @param type $createUserId
     * @param type $createTimeStart
     * @param type $createTimeEnd
     * @param type $fields
     * @param type $orgId
     * @param type $status
     * @param type $page
     * @param type $perPage
     * @return type
     * @throws Exception
     */
    public static function getList($skuId, $name ,$type,$productType,$stockType,$createUserId
                    ,$createTimeStart,$createTimeEnd,$fields,$orgId,$status,$page=1,$perPage=50){
        $builder = SkuModel::orderBy(SkuConst::ID, "desc");
        !empty($skuId) && $builder->where(SkuConst::SKU_ID, strtolower($skuId));
        !empty($name) && $builder->where(SkuConst::NAME, 'like','%'. addslashes($name)."%");
        !empty($type) && $builder->where(SkuConst::TYPE, $type);
        !empty($productType) && $builder->where(SkuConst::PRODUCT_TYPE, $productType);
        !empty($stockType) && $builder->where(SkuConst::STOCK_TYPE,$stockType);
        !empty($createUserId) && $builder->where(SkuConst::CREATE_USER_ID,$createUserId);
        !empty($createTimeStart) && $builder->where(SkuConst::CREATE_TIME,'>=',$createTimeStart);
        !empty($createTimeEnd) && $builder->where(SkuConst::CREATE_TIME,'<=',$createTimeEnd);
        if(!empty($fields) && is_array($fields)) {
            $fillable = $builder->getModel()->getFillable();
            foreach( $fields as $v ) {
                if(!in_array($v, $fillable)) {
                    throw new Exception(trans("message.FIELD_NO_EXSIT",array("field"=>$v)));
                }
            }
            $builder->select($fields);
        }
        if(!empty($orgId)) {
            $orgIds = pow(2,($orgId-1));
            $builder->whereRaw(SkuConst::ORG_IDS."&".$orgIds."=".$orgIds);
        }
        !empty($status) && $builder->where(SkuConst::STATUS, $status);
        $total = $builder->count();
        $list  = $builder->offset(($page-1)*$perPage)->take($perPage)->get()->toArray();
        return array(
            'total' => $total,
            'list' => $list,
        );
    }
    
    /**
     * 获取库存类型
     * @return array
     */
    public static function stockTypes() {
        $stockTypes = array();
        foreach (SkuConst::stockTypes() as $code => $val) {
            $stockTypes[] = array(
                "code" => $code,
                "value" => $val,
            );
        }
        return $stockTypes;
    }

    /**
     * 获取sku 类型
     * @return type
     */
    public static function skuTypes() {
        $skuTypes = array();
        foreach (SkuConst::types() as $code => $val) {
            $skuTypes[] = array(
                "code" => $code,
                "value" => $val,
            );
        }
        return $skuTypes;
    }
    /**
     * 
     * @param type $skuIds
     * @return type
     */
    public static function listPnBySkuIds($skuIds) {
        $skuPnIds = self::listPnIdsBySkuIds($skuIds);
        if(empty($skuPnIds)) {
            return array();
        }
        $pns = Pn::listByIds(array_column($skuPnIds, SkuPnConst::PN_ID));
        $pnList = array();
        foreach($pns as $item) {
            $pnList[$item[PnConst::ID]] = $item;
        }
        $result = array();
        foreach($skuPnIds as $item ) {
            if(isset($pnList[$item[SkuPnConst::PN_ID]])) {
                $result[$item[SkuPnConst::SKU_ID]][] = $pnList[$item[SkuPnConst::PN_ID]];
            }
        }
        return $result;
    }
    /**
     * 
     * @param type $skuId
     * @return type
     */
    public static function listPnBySkuId($skuId) {
        $skuPnIds = self::listPnIdsBySkuId($skuId);
        if(empty($skuPnIds)) {
            return array();
        }
        return Pn::listByIds(array_column($skuPnIds, SkuPnConst::PN_ID));
    }
    
    
            
    /**
     * 
     * @param type $sku
     * @param type $page
     * @param type $perPage
     * @param type $orgId
     * @return type
     */
    public static function listBySkuOrName($sku, $page, $perPage, $orgId) {
        $builder = SkuModel::orderBy(SkuConst::ID, "desc");
        if (!empty($sku)) {
            $GLOBALS['sku'] = $sku;
            $builder->where(function($query) {
                $query->where(SkuConst::NAME, 'like', '%' . addslashes($GLOBALS['sku']) . "%")
                        ->orWhere(SkuConst::SKU_ID, 'like', '%' . addslashes($GLOBALS['sku']) . "%");
            });
        }
        if (!empty($orgId)) {
            $orgIds = pow(2, ($orgId - 1));
            $builder->whereRaw(SkuConst::ORG_IDS . "&" . $orgIds . "=" . $orgIds);
        }
        $builder->where(SkuConst::STATUS, SkuConst::STATUS_ENABLED);
        $total = $builder->count();
        $list = $builder->offset(($page - 1) * $perPage)->take($perPage)->get()->toArray();
        return array(
            'total' => $total,
            'list' => $list,
        );
    }

    /**
     * 
     * @param type $skuIds
     * @return type
     */
    private static function listPnIdsBySkuIds($skuIds) {
        return SkuPnModel::whereIn(SkuPnConst::SKU_ID,$skuIds)->get()->toArray();
    }
    
    private static function listPnIdsBySkuId($skuId) {
        return SkuPnModel::where(SkuPnConst::SKU_ID,$skuId)->get()->toArray();
    }

    /**
     * 获取db 经过存储后的ORGID 数据
     * @param type $orgIds
     * @return type
     */
    public static function getOrgIds($orgIds) {
        $dbOrgIds = 0;
        foreach ($orgIds as $orgId) {
            $dbOrgIds += intval($orgId) > 0 ? pow(2, (intval($orgId) - 1)) : 0;
        }
        return $dbOrgIds;
    }
    /**
     * 检查组织ID是否有效
     * @param type $ids
     * @return boolean
     */
    public static function checkOrgIds($ids) {
        if (empty($ids) || !is_array($ids)) {
            return false;
        }
        foreach ($ids as $v) {
            if (!array_key_exists($v, SkuConst::$org)) {
                return false;
            }
        }
        return true;
    }

}
