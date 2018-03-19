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
use App\Models\Supplier\Supplier as SupplierModel;
use App\Models\Factory\Factory as FactoryModel;
use App\Models\Http\Api\Idservice as IdserviceApi;
use App\Constants\Db\Tables\Base\Supplier as SupplierConst;
use App\Constants\Db\Tables\Base\Factory as FactoryConst;
use Carbon\Carbon;
use Exception;


class Supplier{
    public static function create( $name, $shortName, $isGeneralTaxpayer,
                                  $rate, $createUserId, $bank, $bankCode, $account, $categoryId, $factories = [],$orgIds){
        $supplierId = IdserviceApi::getNewId();
        if(!preg_match("/^VEN\d{5}$/", $supplierId)) {
            throw new Exception(trans("message.SYSTERM_EXCEPTION"));
        }
        $dbSupplier = self::getBySupplierId($supplierId);
        if(!empty($dbSupplier)){
            throw new Exception(trans("message.SUPPER_CODE_EXSIT"));
        }
        $dbSupplier = self::getByName($name);
        if(!empty($dbSupplier)){
            throw new Exception(trans("message.SUPPER_NAME_EXSIT"));
        }
        $dbSupplier = self::getByShortName($shortName);
        if(!empty($dbSupplier)){
            throw new Exception(trans("message.SUPPER_SHORT_NAME_EXSIT"));
        }
        if(!self::checkOrgIds($orgIds)) {
            throw new Exception(trans("message.ORG_ID_WRONG"));
        }
        $data = [
            SupplierConst::SUPPLIER_ID => $supplierId,
            SupplierConst::NAME => $name,
            SupplierConst::SHORT_NAME => $shortName,
            SupplierConst::IS_GENERAL_TAXPAYER => $isGeneralTaxpayer,
            SupplierConst::RATE => $rate,
            SupplierConst::CREATE_USER_ID => $createUserId,
            SupplierConst::BANK => $bank,
            SupplierConst::BANK_CODE => $bankCode,
            SupplierConst::ACCOUNT => $account,
            SupplierConst::CATEGORY_ID => $categoryId,
            SupplierConst::STATUS => SupplierConst::STATUS_ENABLED,
            SupplierConst::ORG_IDS => self::getOrgIds($orgIds),
        ];
        SupplierModel::beginTransaction();
        try{
            $supplier = SupplierModel::create($data);
            if(!$supplier){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $_factories = [];
            if(!empty($factories)){
                $currTime = Carbon::now()->timestamp;
                $factoryIds = array_column($factories, 'factory_id');
                $_tmp = FactoryModel::whereIn(FactoryConst::FACTORY_ID, $factoryIds)->first();
                if(!empty($_tmp)){
                    throw new Exception(trans("message.FACTORY_ID_EXSIT",array("factory_id"=>$_tmp[FactoryConst::FACTORY_ID])));
                }
                $supplierId = $supplier[SupplierConst::SUPPLIER_ID];
                foreach($factories as $factory){
                    $_factories[] = [
                        FactoryConst::SUPPLIER_ID => $supplierId,
                        FactoryConst::FACTORY_ID => $factory['factory_id'],
                        FactoryConst::NAME => $factory['name'],
                        FactoryConst::CITY_ID => $factory['city_id'],
                        FactoryConst::ADDRESS => $factory['address'],
                        FactoryConst::CONTRACT_USER_NAME => $factory['contract_user_name'],
                        FactoryConst::CONTRACT_USER_MOBILE => $factory['contract_user_mobile'],
                        FactoryConst::STATUS => FactoryConst::STATUS_ENABLED,
                        FactoryConst::CREATE_TIME => $currTime,
                        FactoryConst::UPDATE_TIME => $currTime,
                    ];
                }
                if(!FactoryModel::insert($_factories)){
                    throw new Exception(trans("message.SAVE_FAIL"));
                }
            }
            $mqData = $supplier->toArray();
            $mqData['factories'] = $_factories;
            if(!Util::sendToMq($mqData, RoutingKey::SUPPLIER_CREATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            SupplierModel::rollBack();
            throw $e;
        }
        SupplierModel::commit();
        return $supplier;
    }

    /**
     * @param $supplierId
     * @param $name
     * @param $shortName
     * @param $isGeneralTaxpayer
     * @param $rate
     * @param $createUserId
     * @param $bank
     * @param $bankCode
     * @param $account
     * @param $categoryId
     * @param array $factories
     * @return bool
     * @throws Exception
     */
    public static function update($supplierId, $name, $shortName, $isGeneralTaxpayer,
                                  $rate, $createUserId, $bank, $bankCode, $account, $categoryId, $factories = [],$orgIds){
        $supplierId = strtoupper($supplierId);
        $dbSupplier = self::getBySupplierId($supplierId);
        if(empty($dbSupplier)){
            throw new Exception(trans("message.SUPPER_CODE_NO_EXSIT"));
        }
        $dbSupplier = self::getByName($name, null, $supplierId);
        if(!empty($dbSupplier)){
            throw new Exception(trans("message.SUPPER_NAME_EXSIT"));
        }
        $dbSupplier = self::getByShortName($shortName, null, $supplierId);
        if(!empty($dbSupplier)){
            throw new Exception(trans("message.SUPPER_SHORT_NAME_EXSIT"));
        }
        if(!self::checkOrgIds($orgIds)) {
            throw new Exception(trans("message.ORG_ID_WRONG"));
        }
        $data = [
            SupplierConst::NAME => $name,
            SupplierConst::SHORT_NAME => $shortName,
            SupplierConst::IS_GENERAL_TAXPAYER => $isGeneralTaxpayer,
            SupplierConst::RATE => $rate,
//            SupplierConst::CREATE_USER_ID => $createUserId,
            SupplierConst::BANK => $bank,
            SupplierConst::BANK_CODE => $bankCode,
            SupplierConst::ACCOUNT => $account,
            SupplierConst::CATEGORY_ID => $categoryId,
            SupplierConst::ORG_IDS => self::getOrgIds($orgIds),
        ];
        SupplierModel::beginTransaction();
        try{
            if(!SupplierModel::where(SupplierConst::SUPPLIER_ID, $supplierId)->update($data)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            if (FactoryModel::where(FactoryConst::SUPPLIER_ID, $supplierId)->first()) {
                if (!FactoryModel::where(FactoryConst::SUPPLIER_ID, $supplierId)->delete()) {
                    throw new Exception(trans("message.DELETE_FACTORY_FAIL"));
                }
            }
            $_factories = [];
            if(!empty($factories)){
                $currTime = Carbon::now()->timestamp;
                $factoryIds = array_column($factories, 'factory_id');
                $_tmp = FactoryModel::whereIn(FactoryConst::FACTORY_ID, $factoryIds)->first();
                if(!empty($_tmp)){
                    throw new Exception(trans("message.FACTORY_ID_EXSIT",array("factory_id"=>$_tmp[FactoryConst::FACTORY_ID])));
                }
                foreach($factories as $factory){
                    $_factories[] = [
                        FactoryConst::SUPPLIER_ID => $supplierId,
                        FactoryConst::FACTORY_ID => $factory['factory_id'],
                        FactoryConst::NAME => $factory['name'],
                        FactoryConst::CITY_ID => $factory['city_id'],
                        FactoryConst::ADDRESS => $factory['address'],
                        FactoryConst::CONTRACT_USER_NAME => $factory['contract_user_name'],
                        FactoryConst::CONTRACT_USER_MOBILE => $factory['contract_user_mobile'],
                        FactoryConst::STATUS => FactoryConst::STATUS_ENABLED,
                        FactoryConst::CREATE_TIME => $currTime,
                        FactoryConst::UPDATE_TIME => $currTime,
                    ];
                }
                if(!FactoryModel::insert($_factories)){
                    throw new Exception(trans("message.SAVE_FAIL"));
                }
            }
            $mqData = $data;
            $mqData[SupplierConst::SUPPLIER_ID] = $supplierId;
            $mqData['factories'] = $_factories;
            if(!Util::sendToMq($mqData, RoutingKey::SUPPLIER_UPDATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            SupplierModel::rollBack();
            throw $e;
        }
        SupplierModel::commit();
        return true;
    }

    /**
     * @param array $supplierIds
     * @param null $status
     * @return mixed
     */
    public static function listBySupplierIds(array $supplierIds, $status = null,$orgId=null){
        if(empty($supplierIds)) {
            return array();
        }
        $builder = SupplierModel::whereIn(SupplierConst::SUPPLIER_ID, $supplierIds);
        if(!empty($status)){
            $builder->where(SupplierConst::STATUS, $status);
        }
        if(!empty($orgId)) {
            $orgIds = pow(2,($orgId-1));
            $builder->whereRaw(SupplierConst::ORG_IDS."&".$orgIds."=".$orgIds);
        }
        return $builder->get()->toArray();
    }

    /**
     * @param $supplierId
     * @return mixed
     */
    public static function getBySupplierId($supplierId, $status = null,$orgId=null){
        $builder = SupplierModel::where(SupplierConst::SUPPLIER_ID, $supplierId);
        if(!empty($status)){
            $builder->where(SupplierConst::STATUS, $status);
        }
        if(!empty($orgId)) {
            $orgIds = pow(2,($orgId-1));
            $builder->whereRaw(SupplierConst::ORG_IDS."&".$orgIds."=".$orgIds);
        }
        return $builder->first();
    }
    /**
     * 
     * @param type $factoryId
     * @return type
     */
    public static function getByFactoryId($factoryId) {
        $supplierFactory = Factory::getByFactoryId($factoryId);
        if(empty($supplierFactory)) {
            return array();
        }
        return self::getBySupplierId($supplierFactory[FactoryConst::SUPPLIER_ID]);
    }
    
    /**
     * 
     * @return type
     */
    public static function getCategoryList() {
        $categories = SupplierConst::categories();
        $list = [];
        foreach ($categories as $k => $v) {
            $list[] = [
                'id' => $k,
                'name' => $v,
            ];
        }
        return $list;
    }

    /**
     * @param $name
     * @param null $status
     * @param null $exceptSupplierId
     */
    public static function getByName($name, $status = null, $exceptSupplierId = null){
        $builder = SupplierModel::where(SupplierConst::NAME, $name);
        if(!is_null($status)){
            $builder->where(SupplierConst::STATUS, $status);
        }
        if(!is_null($exceptSupplierId)){
            $builder->where(SupplierConst::SUPPLIER_ID, '!=', $exceptSupplierId);
        }
        return $builder->first();
    }

    /**
     * @param $shortName
     * @param null $status
     * @param null $exceptSupplierId
     */
    public static function getByShortName($shortName, $status = null, $exceptSupplierId = null){
        $builder = SupplierModel::where(SupplierConst::SHORT_NAME, $shortName);
        if(!is_null($status)){
            $builder->where(SupplierConst::STATUS, $status);
        }
        if(!is_null($exceptSupplierId)){
            $builder->where(SupplierConst::SUPPLIER_ID, '!=', $exceptSupplierId);
        }
        return $builder->first();
    }
    
    /**
     * 
     * @param type $supplierId
     * @param type $status
     * @param type $updateUserId
     * @throws Exception
     */
    public static function updateStatus($supplierId, $status, $updateUserId){
        $supplierId = strtoupper($supplierId);
        $supplier = self::getBySupplierId($supplierId);
        if(empty($supplier)){
            throw new Exception(trans("message.SUPPER_CODE_NO_EXSIT"));
        }
        if($supplier[SupplierConst::STATUS] == $status){
            throw new Exception(trans("message.UPDATE_STATUS_NO_NEED"));
        }
        
        $data = array(
            SupplierConst::STATUS=>$status,
            SupplierConst::UPDATE_TIME=>Carbon::now()->timestamp,
        );
        SupplierModel::beginTransaction();
        try{
            if(!SupplierModel::where(SupplierConst::SUPPLIER_ID, $supplierId)->update($data)){
                throw new Exception(trans("message.UPDATE_FAIL"));
            }
            $mqData[SupplierConst::SUPPLIER_ID] = $supplierId;
            $mqData['status'] = $status;
            
            if(!Util::sendToMq($mqData, RoutingKey::SUPPLIER_UPDATE_STATUS)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            SkuModel::rollBack();
            throw $e;
        }
        SupplierModel::commit();
        
    }
    
    /**
     * 
     * @param type $name
     * @param type $categoryId
     * @param type $supplierId
     * @param type $createUserId
     * @param type $createTimeStart
     * @param type $createTimeEnd
     * @param type $status
     * @param type $orgId
     * @param type $page
     * @param type $perpage
     * @param type $fields
     * @return type
     * @throws Exception
     */
    public static function getList($name, $categoryId, $supplierId,$createUserId,$createTimeStart
                    ,$createTimeEnd,$status,$orgId,$page,$perPage,$fields){
        $builder = SupplierModel::orderBy(SupplierConst::ID, "desc");
        !empty($name) && $builder->where(SupplierConst::NAME,'like','%'.addslashes($name)."%");
        !empty($categoryId) && $builder->where(SupplierConst::CATEGORY_ID,$categoryId);
        !empty($supplierId) && $builder->where(SupplierConst::SUPPLIER_ID,$supplierId);
        !empty($createUserId) && $builder->where(SupplierConst::CREATE_USER_ID,$createUserId);
        !empty($createTimeStart) && $builder->where(SupplierConst::CREATE_TIME,">=",$createTimeStart);
        !empty($createTimeEnd) && $builder->where(SupplierConst::CREATE_TIME,"<=",$createTimeEnd);
        !empty($status) && $builder->where(SupplierConst::STATUS,$status);
        if(!empty($orgId)) {
            $orgIds = pow(2,($orgId-1));
            $builder->whereRaw(SupplierConst::ORG_IDS."&".$orgIds."=".$orgIds);
        }
        if(!empty($fields) && is_array($fields)) {
            $fillable = $builder->getModel()->getFillable();
            foreach( $fields as $v ) {
                if(!in_array($v, $fillable)) {
                    throw new Exception(trans("message.FIELD_NO_EXSIT",array("field"=>$v)));
                }
            }
            $builder->select($fields);
        }
        $total = $builder->count();
        $list  = $builder->offset(($page-1)*$perPage)->take($perPage)->get()->toArray();
        return array(
            'total' => $total,
            'list' => $list,
        );
    }
    /**
     * 
     * @param type $supplierId
     * @param type $orgId
     * @return type
     */
    public static function getDetail($supplierId,$status=null, $orgId=null) {
        $supplier = self::getBySupplierId($supplierId,$status,$orgId);
        if(empty($supplier)) {
            return array();
        }
        $supplier["factorys"] = Factory::getBySupplierId($supplierId);
        return $supplier;
    }
    /**
     * 
     * @return type
     */
    public static function getRateList(){
        return array_keys(SupplierConst::$rates);
    }

    /**
     * 
     * @return type
     */
    private static function getSupplierId() {
        $maxId = SupplierModel::max(SupplierConst::ID);
        return "VEN" . sprintf("%05d", $maxId + 2100);//设置初始值根据数据情况取值为2100
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
            if (!array_key_exists($v, SupplierConst::$org)) {
                return false;
            }
        }
        return true;
    }
    
    
    
    
}
