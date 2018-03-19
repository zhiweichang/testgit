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
use Exception;
use App\Models\Warehouse\Warehouse as WarehouseModel;
use App\Models\Factory\Factory as FactoryModel;
use App\Constants\Db\Tables\Base\Warehouse as WarehouseConst;
use App\Constants\Db\Tables\Base\Factory as FactoryConst;

class Warehouse{
    /**
     * @param $warehouseId
     * @param $name
     * @param $type
     * @param $factoryId
     * @param $city
     * @param $address
     * @param $contactName
     * @param $contactMobile
     * @param $contactEmail
     * @param $managerName
     * @param $managerMobile
     * @param $managerEmail
     * @param $firstReceiverName
     * @param $firstReceiverMobile
     * @param $firstReceiverEmail
     * @param $secondReceiverName
     * @param $secondReceiverMobile
     * @param $secondReceiverEmail
     * @param $createUserId
     * @param $orgIds
     * @param $longitude
     * @param $latitude
     * @return static
     * @throws Exception
     */
    public static function create($warehouseId, $name, $type, $factoryId, $city, $address,
                                  $contactName, $contactMobile, $contactEmail, $managerName, $managerMobile, $managerEmail,
                                  $firstReceiverName, $firstReceiverMobile, $firstReceiverEmail, $secondReceiverName, $secondReceiverMobile, $secondReceiverEmail,
                                  $createUserId,$orgIds, $longitude, $latitude){
        $warehouseId = strtoupper($warehouseId);
        $contactMobile = trim($contactMobile);
        if(!empty(self::getByWarehouseId($warehouseId))){
            throw new Exception(trans("message.WAREHOUSE_ID_EXSIT"));
        }
        if(!empty(self::getByName($name))){
            throw new Exception(trans("message.WAREHOUSE_NAME_EXSIT"));
        }
        if(WarehouseConst::TYPE_MAKE == $type){
            if(empty($factoryId)){
                throw new Exception(trans("message.WAREHOUSE_CHOUSE_FACTORY_ID"));
            }
            if(empty(Factory::getByFactoryId($factoryId, FactoryConst::STATUS_ENABLED))){
                throw new Exception(trans("message.WAREHOUSE_FACTORY_ID_NO_EXSIT"));
            }
            $_warehouse = self::getByFactoryId($factoryId);
            if(!empty($_warehouse)){
                throw new Exception(trans("message.WAREHOUSE_FACTORY_HAS_BANG_DING",array("warehouse_id"=>$_warehouse[WarehouseConst::WAREHOUSE_ID])));
            }
        }
        if(!self::checkOrgIds($orgIds)) {
            throw new Exception(trans("message.ORG_ID_WRONG"));
        }
        $data = [
            WarehouseConst::WAREHOUSE_ID => $warehouseId,
            WarehouseConst::NAME => $name,
            WarehouseConst::TYPE => $type,
            WarehouseConst::FACTORY_ID => $factoryId,
            WarehouseConst::CITY => $city,
            WarehouseConst::ADDRESS => $address,
            WarehouseConst::CONTACT_PERSON => $contactName,
            WarehouseConst::CONTACT_MOBILE => $contactMobile,
            WarehouseConst::CONTACT_EMAIL => $contactEmail,
            WarehouseConst::MANAGER_NAME => $managerName,
            WarehouseConst::MANAGER_MOBILE => $managerMobile,
            WarehouseConst::MANAGER_EMAIL => $managerEmail,
            WarehouseConst::FIRST_RECEIVER_NAME => $firstReceiverName,
            WarehouseConst::FIRST_RECEIVER_MOBILE => $firstReceiverMobile,
            WarehouseConst::FIRST_RECEIVER_EMAIL => $firstReceiverEmail,
            WarehouseConst::SECOND_RECEIVER_NAME => $secondReceiverName,
            WarehouseConst::SECOND_RECEIVER_MOBILE => $secondReceiverMobile,
            WarehouseConst::SECOND_RECEIVER_EMAIL => $secondReceiverEmail,
            WarehouseConst::CREATE_USER_ID => $createUserId,
            WarehouseConst::STATUS => WarehouseConst::STATUS_ENABLED,
            WarehouseConst::ORG_IDS => self::getOrgIds($orgIds),
            WarehouseConst::LONGITUDE => $longitude,
            WarehouseConst::LATITUDE => $latitude,
        ];
        WarehouseModel::beginTransaction();
        try{
            $warehouse = WarehouseModel::create($data);
            if(!$warehouse){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $mqData = $warehouse->toArray();
            if(!Util::sendToMq($mqData, RoutingKey::WAREHOUSE_CREATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            WarehouseModel::rollBack();
            throw $e;
        }
        WarehouseModel::commit();
        return $warehouse;
    }

    /**
     * @param $warehouseId
     * @param $name
     * @param $type
     * @param $factoryId
     * @param $city
     * @param $address
     * @param $contactName
     * @param $contactMobile
     * @param $contactEmail
     * @param $managerName
     * @param $managerMobile
     * @param $managerEmail
     * @param $firstReceiverName
     * @param $firstReceiverMobile
     * @param $firstReceiverEmail
     * @param $secondReceiverName
     * @param $secondReceiverMobile
     * @param $secondReceiverEmail
     * @param $createUserId
     * @param $orgIds
     * @param $longitude
     * @param $latitude
     * @return bool
     * @throws Exception
     */
    public static function update($warehouseId, $name, $type, $factoryId, $city, $address,
                                  $contactName, $contactMobile, $contactEmail, $managerName, $managerMobile, $managerEmail,
                                  $firstReceiverName, $firstReceiverMobile, $firstReceiverEmail, $secondReceiverName, $secondReceiverMobile, $secondReceiverEmail,
                                  $createUserId, $orgIds, $longitude, $latitude){
        $warehouseId = strtoupper($warehouseId);
        $contactMobile = trim($contactMobile);
        $dbWarehouse = self::getByWarehouseId($warehouseId);
        if(empty($dbWarehouse)){
            throw new Exception(trans("message.WAREHOUSE_NO_EXSIT"));
        }
        if(!empty(self::getByName($name, null, $warehouseId))){
            throw new Exception(trans("message.WAREHOUSE_NAME_EXSIT"));
        }
        if(WarehouseConst::TYPE_MAKE == $type){
            if(empty($factoryId)){
                throw new Exception(trans("message.WAREHOUSE_CHOUSE_FACTORY_ID"));
            }
            if(empty(Factory::getByFactoryId($factoryId, FactoryConst::STATUS_ENABLED))){
                throw new Exception(trans("message.WAREHOUSE_FACTORY_ID_NO_EXSIT"));
            }
            $_warehouse = self::getByFactoryId($factoryId, null, $warehouseId);
            if(!empty($_warehouse)){
                throw new Exception(trans("message.WAREHOUSE_FACTORY_HAS_BANG_DING",array("warehouse_id"=>$_warehouse[WarehouseConst::WAREHOUSE_ID])));
            }
        }
        self::checkWarehouseType($dbWarehouse,$type);
        
        if(!self::checkOrgIds($orgIds)) {
            throw new Exception(trans("message.ORG_ID_WRONG"));
        }

        $data = [
            WarehouseConst::NAME => $name,
            WarehouseConst::TYPE => $type,
            WarehouseConst::FACTORY_ID => $factoryId,
            WarehouseConst::CITY => $city,
            WarehouseConst::ADDRESS => $address,
            WarehouseConst::CONTACT_PERSON => $contactName,
            WarehouseConst::CONTACT_MOBILE => $contactMobile,
            WarehouseConst::CONTACT_EMAIL => $contactEmail,
            WarehouseConst::MANAGER_NAME => $managerName,
            WarehouseConst::MANAGER_MOBILE => $managerMobile,
            WarehouseConst::MANAGER_EMAIL => $managerEmail,
            WarehouseConst::FIRST_RECEIVER_NAME => $firstReceiverName,
            WarehouseConst::FIRST_RECEIVER_MOBILE => $firstReceiverMobile,
            WarehouseConst::FIRST_RECEIVER_EMAIL => $firstReceiverEmail,
            WarehouseConst::SECOND_RECEIVER_NAME => $secondReceiverName,
            WarehouseConst::SECOND_RECEIVER_MOBILE => $secondReceiverMobile,
            WarehouseConst::SECOND_RECEIVER_EMAIL => $secondReceiverEmail,
//            WarehouseConst::CREATE_USER_ID => $createUserId,
            WarehouseConst::ORG_IDS => self::getOrgIds($orgIds),
            WarehouseConst::LONGITUDE => $longitude,
            WarehouseConst::LATITUDE => $latitude,
        ];
        WarehouseModel::beginTransaction();
        try{
            if(!WarehouseModel::where(WarehouseConst::WAREHOUSE_ID, $warehouseId)->update($data)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $mqData = $data;
            $mqData[WarehouseConst::WAREHOUSE_ID] = $warehouseId;
            if(!Util::sendToMq($mqData, RoutingKey::WAREHOUSE_UPDATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            WarehouseModel::rollBack();
            throw $e;
        }
        WarehouseModel::commit();
        return true;
    }

    /**
     * @param $warehouseId
     * @param $status
     * @param $updateUserId
     * @return bool
     * @throws Exception
     */
    public static function updateStatus($warehouseId, $status, $updateUserId){
        $warehouse = self::getByWarehouseId($warehouseId);
        if(empty($warehouse)){
            throw new Exception(trans("message.WAREHOUSE_NO_EXSIT"));
        }
        if($warehouse[WarehouseConst::STATUS] == $status){
            throw new Exception(trans("message.UPDATE_STATUS_NO_NEED"));
        }
        $data = [
            WarehouseConst::STATUS => $status,
        ];
        WarehouseModel::beginTransaction();
        try{
            if(!WarehouseModel::where(WarehouseConst::WAREHOUSE_ID, $warehouseId)->update($data)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $mqData = $data;
            $mqData[WarehouseConst::WAREHOUSE_ID] = $warehouseId;
            if(!Util::sendToMq($mqData, RoutingKey::WAREHOUSE_UPDATE_STATUS)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            WarehouseModel::rollBack();
            throw $e;
        }
        WarehouseModel::commit();
        return true;
    }

    /**
     * @param array $warehouseIds
     * @param null $status
     * @return mixed
     */
    public static function listByWarehouseIds(array $warehouseIds, $status = null){
        $builder = WarehouseModel::whereIn(WarehouseConst::WAREHOUSE_ID, $warehouseIds);
        if(!empty($status)){
            $builder->where(WarehouseConst::STATUS, $status);
        }
        return $builder->get()->toArray();
    }

    /**
     * @param $warehouseId
     * @param null $status
     * @param null $exceptWarehouseId
     * @return mixed
     */
    public static function getByWarehouseId($warehouseId, $status = null, $exceptWarehouseId = null,$orgId =null){
        $builder = WarehouseModel::where(WarehouseConst::WAREHOUSE_ID, $warehouseId);
        if(!empty($status)){
            $builder->where(WarehouseConst::STATUS, $status);
        }
        if(!is_null($exceptWarehouseId)){
            $builder->where(WarehouseConst::WAREHOUSE_ID, '!=', $exceptWarehouseId);
        }
        if (!empty($orgId)) {
            $orgIds = pow(2, ($orgId - 1));
            $builder->whereRaw(WarehouseConst::ORG_IDS . "&" . $orgIds . "=" . $orgIds);
        }
        return $builder->first();
    }

    /**
     * @param $name
     * @param null $status
     * @param null $exceptWarehouseId
     * @return mixed
     */
    public static function getByName($name, $status = null, $exceptWarehouseId = null){
        $builder = WarehouseModel::where(WarehouseConst::NAME, $name);
        if(!empty($status)){
            $builder->where(WarehouseConst::STATUS, $status);
        }
        if(!is_null($exceptWarehouseId)){
            $builder->where(WarehouseConst::WAREHOUSE_ID, '!=', $exceptWarehouseId);
        }
        return $builder->first();
    }

    /**
     * @param $factoryId
     * @param null $status
     * @param null $exceptWarehouseId
     * @return mixed
     */
    public static function getByFactoryId($factoryId, $status = null, $exceptWarehouseId = null){
        $builder = WarehouseModel::where(WarehouseConst::FACTORY_ID, $factoryId);
        if(!empty($status)){
            $builder->where(WarehouseConst::STATUS, $status);
        }
        if(!is_null($exceptWarehouseId)){
            $builder->where(WarehouseConst::WAREHOUSE_ID, '!=', $exceptWarehouseId);
        }
        return $builder->first();
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
            if (!array_key_exists($v, WarehouseConst::$org)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 简称仓库只支持维修仓和备件库直接的转换
     * @param array $data
     * @param int $type
     * @throws Exception
     */
    public static function checkWarehouseType($data=array(),$type=0) {
        $types = array(
            WarehouseConst::TYPE_REPAIR,
            WarehouseConst::TYPE_REPLACEMENT,
        );
        if($data[WarehouseConst::TYPE] != $type) {
            if(!in_array($type, $types) || !in_array($data[WarehouseConst::TYPE], $types)) {
                throw new Exception(trans("message.WAREHOUSE_TYPE_CHANGR_CHECK_WRONG"));
            }
        }
    }

    public static function getByCityIds($cityIds = array()) {
            return WarehouseModel::whereIn(WarehouseConst::CITY, $cityIds)->get()->toArray();
    }
    /**
     * 
     * @param type $warehouseIds
     * @param type $name
     * @param type $city
     * @param type $type
     * @param type $fields
     * @param type $status
     * @param type $createUserId
     * @param type $createTimeStart
     * @param type $createTimeEnd
     * @param type $page
     * @param type $perPage
     * @param type $orgId
     * @return type
     * @throws Exception
     */
    public static function getList($warehouseIds,$warehouseId, $name, $city, $type, $fields, $status, $createUserId, $createTimeStart, $createTimeEnd, $page, $perPage, $orgId) {
        $builder = WarehouseModel::orderBy(WarehouseConst::ID, "desc");
        !empty($warehouseIds) && $builder->whereIn(WarehouseConst::WAREHOUSE_ID, $warehouseIds);
        !empty($warehouseId) && $builder->where(WarehouseConst::WAREHOUSE_ID, $warehouseId);
        !empty($name) && $builder->where(WarehouseConst::NAME, "like", '%' . addslashes($name) . "%");
        !empty($city) && $builder->where(WarehouseConst::CITY, $city);
        !empty($type) && $builder->where(WarehouseConst::TYPE, $type);
        !empty($status) && $builder->where(WarehouseConst::STATUS, $status);
        !empty($createUserId) && $builder->where(WarehouseConst::CREATE_USER_ID, $createUserId);
        !empty($createTimeStart) && $builder->where(WarehouseConst::CREATE_TIME, '>=', $createTimeStart);
        !empty($createTimeEnd) && $builder->where(WarehouseConst::CREATE_TIME, '<=', $createTimeEnd);
        if (!empty($orgId)) {
            $orgIds = pow(2, ($orgId - 1));
            $builder->whereRaw(WarehouseConst::ORG_IDS . "&" . $orgIds . "=" . $orgIds);
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
        $total = $builder->count();
        $list = $builder->offset(($page - 1) * $perPage)->take($perPage)->get()->toArray();
        return array(
            'total' => $total,
            'list' => $list,
        );
    }
    /**
     * 
     * @return type
     */
    public static function getWarehouseType() {
        $result = array();
        foreach (WarehouseConst::types() as $key => $val) {
            $result[] = array(
                "code" => $key,
                "value" => $val,
            );
        }
        return $result;
    }
    /**
     * 
     * @param type $factoryIds
     * @return type
     */
    public static function listByFactoryIds($factoryIds) {
        return WarehouseModel::whereIn(WarehouseConst::FACTORY_ID, $factoryIds)->get()->toArray();
    }
    /**
     * 
     * @param type $warehouseId
     * @param type $status
     * @param type $orgId
     * @return type
     */
    public static function getDetail($warehouseId,$status,$orgId){
        $warehouse = self::getByWarehouseId($warehouseId,$status,null,$orgId);
        if(!empty($warehouse) && !empty($warehouse[WarehouseConst::FACTORY_ID])) {
            $warehouse["factory"] = Factory::getByFactoryId($warehouse[WarehouseConst::FACTORY_ID]);
        }
        return $warehouse;
    }

}
