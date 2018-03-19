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
use App\Models\Throws\ThrowCity as ThrowCityModel;
use App\Models\Throws\ThrowWarehouse as ThrowWarehouseModel;
use App\Models\Warehouse\Warehouse as WarehouseModel;
use App\Constants\Db\Tables\Base\ThrowCity as ThrowCityConst;
use App\Constants\Db\Tables\Base\Warehouse as WarehouseConst;
use App\Constants\Db\Tables\Base\ThrowWarehouse as ThrowWarehouseConst;

class ThrowCity{
    /**
     * @param $cityId
     * @param $priority
     * @param $durationTime
     * @param $createUserId
     * @param $warehouseIds
     * @param $stockDays
     * @return static
     * @throws Exception
     */
    public static function create($cityId, $priority, $durationTime, $createUserId, $warehouseIds,$stockDays){
        $warehouseIds = array_unique($warehouseIds);
        $dbItem = self::getByThrowCityId($cityId);
        if(!empty($dbItem)){
            throw new Exception(trans("message.THROE_CITY_EXSIT"));
        }
        $dbWarehouseIds = WarehouseModel::whereIn(WarehouseConst::WAREHOUSE_ID, $warehouseIds)
            ->where(WarehouseConst::STATUS, WarehouseConst::STATUS_ENABLED)
            ->lists(WarehouseConst::WAREHOUSE_ID)->toArray();
        $diffWarehouseIds = array_diff($warehouseIds, $dbWarehouseIds);
        if(!empty($diffWarehouseIds)){
            throw new Exception(trans("message.THROE_WAREHOUSE_NO_EXSIT",array("warehouse"=>implode('、', $diffWarehouseIds))));
        }
        ThrowCityModel::beginTransaction();
        try{
            $data = [
                ThrowCityConst::CITY_ID => $cityId,
                ThrowCityConst::PRIORITY => $priority,
                ThrowCityConst::DURATION_TIME => $durationTime,
                ThrowCityConst::IS_AUTO => count($warehouseIds) > 1 ? ThrowCityConst::IS_AUTO_NO : ThrowCityConst::IS_AUTO_YES,
                ThrowCityConst::STATUS => ThrowCityConst::STATUS_ENABLED,
                ThrowCityConst::CREATE_USER_ID => $createUserId,
                ThrowCityConst::STOCK_DAYS => $stockDays,
            ];
            $throwCity = ThrowCityModel::create($data);
            if(!$throwCity){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $throwWarehouses = [];
            foreach($warehouseIds as $warehouseId){
                $throwWarehouses[] = [
                    ThrowWarehouseConst::THROW_CITY_ID => $cityId,
                    ThrowWarehouseConst::WAREHOUSE_ID => $warehouseId,
                    ThrowWarehouseConst::STATUS => ThrowWarehouseConst::STATUS_ENABLED,
                ];
            }
            if(!ThrowWarehouseModel::insert($throwWarehouses)){
                throw new Exception(trans("message.SAVE_THROW_WAREHOURSE_FAIL"));
            }
            $mqData = $data;
            $mqData['warehosues'] = $throwWarehouses;
            if(!Util::sendToMq($mqData, RoutingKey::THROW_CITY_CREATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            ThrowCityModel::rollBack();
            throw $e;
        }
        ThrowCityModel::commit();
        return $throwCity;
    }

    /**
     * @param $cityId
     * @param $priority
     * @param $durationTime
     * @param $createUserId
     * @param $warehouseIds
     * @param $stockDays
     * @return bool
     * @throws Exception
     */
    public static function update($cityId, $priority, $durationTime, $createUserId, $warehouseIds,$stockDays){
        $warehouseIds = array_unique($warehouseIds);
        $dbItem = self::getByThrowCityId($cityId);
        if(empty($dbItem)){
            throw new Exception(trans("message.THROE_CITY_NO_EXSIT"));
        }
        $dbWarehouseIds = WarehouseModel::whereIn(WarehouseConst::WAREHOUSE_ID, $warehouseIds)
            ->where(WarehouseConst::STATUS, WarehouseConst::STATUS_ENABLED)
            ->lists(WarehouseConst::WAREHOUSE_ID)->toArray();
        $diffWarehouseIds = array_diff($warehouseIds, $dbWarehouseIds);
        if(!empty($diffWarehouseIds)){
            throw new Exception(trans("message.THROE_WAREHOUSE_NO_EXSIT",array("warehouse"=>implode('、', $diffWarehouseIds))));
        }
        ThrowCityModel::beginTransaction();
        try{
            $data = [
                ThrowCityConst::PRIORITY => $priority,
                ThrowCityConst::DURATION_TIME => $durationTime,
                ThrowCityConst::IS_AUTO => count($warehouseIds) > 1 ? ThrowCityConst::IS_AUTO_NO : ThrowCityConst::IS_AUTO_YES,
//                ThrowCityConst::CREATE_USER_ID => $createUserId,
                ThrowCityConst::STOCK_DAYS => $stockDays,
            ];
            if(!ThrowCityModel::where(ThrowCityConst::CITY_ID, $cityId)->first()->update($data)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            if(!ThrowWarehouseModel::where(ThrowWarehouseConst::THROW_CITY_ID, $cityId)->delete()){
                throw new Exception(trans("message.DEL_THROW_CITY_FAIL"));
            }
            $throwWarehouses = [];
            foreach($warehouseIds as $warehouseId){
                $throwWarehouses[] = [
                    ThrowWarehouseConst::THROW_CITY_ID => $cityId,
                    ThrowWarehouseConst::WAREHOUSE_ID => $warehouseId,
                    ThrowWarehouseConst::STATUS => ThrowWarehouseConst::STATUS_ENABLED,
                ];
            }
            if(!ThrowWarehouseModel::insert($throwWarehouses)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $mqData = $data;
            $mqData[ThrowCityConst::CITY_ID] = $cityId;
            $mqData['warehosues'] = $throwWarehouses;
            if(!Util::sendToMq($mqData, RoutingKey::THROW_CITY_UPDATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            ThrowCityModel::rollBack();
            throw $e;
        }
        ThrowCityModel::commit();
        return true;
    }

    /**
     * @param $cityId
     * @param $status
     * @param $createUserId
     * @return bool
     * @throws Exception
     */
    public static function updateStatus($cityId, $status, $createUserId){
        $dbItem = self::getByThrowCityId($cityId);
        if(empty($dbItem)){
            throw new Exception(trans("message.THROE_CITY_NO_EXSIT"));
        }
        if($dbItem[ThrowCityConst::STATUS] == $status){
            throw new Exception(trans("message.UPDATE_STATUS_NO_NEED"));
        }
        ThrowCityModel::beginTransaction();
        try{
            $data = [
                ThrowCityConst::STATUS => $status,
            ];
            if(!ThrowCityModel::where(ThrowCityConst::CITY_ID, $cityId)->update($data)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $mqData = [
                ThrowCityConst::CITY_ID => $cityId,
                ThrowCityConst::STATUS => $status,
            ];
            if(!Util::sendToMq($mqData, RoutingKey::THROW_CITY_UPDATE_STATUS)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            ThrowCityModel::rollBack();
            throw $e;
        }
        ThrowCityModel::commit();
        return true;
    }

    /**
     * @param array $throwCityIds
     * @param null $status
     * @return mixed
     */
    public static function listByThrowCityIds(array $throwCityIds, $status = null){
        $builder = ThrowCityModel::whereIn(ThrowCityConst::CITY_ID, $throwCityIds);
        if(!empty($status)){
            $builder->where(ThrowCityConst::STATUS, $status);
        }
        return $builder->get()->toArray();
    }

    /**
     * @param $throwCityId
     * @return mixed
     */
    public static function getByThrowCityId($throwCityId, $status = null){
        $builder = ThrowCityModel::where(ThrowCityConst::CITY_ID, $throwCityId);
        if(!empty($status)){
            $builder->where(ThrowCityConst::STATUS, $status);
        }
        return $builder->first();
    }
    /**
     * 
     * @param type $id
     * @param type $status
     * @return type
     */
    public static function getById($id, $status = null){
        $builder = ThrowCityModel::where(ThrowCityConst::ID, $id);
        if(!empty($status)){
            $builder->where(ThrowCityConst::STATUS, $status);
        }
        return $builder->first();
    }
    /**
     * 
     * @param type $throwCityId
     * @return type
     */
    public static function getDetailByThrowCityId($throwCityId) {
        $throwCityDetail = self::getByThrowCityId($throwCityId);
        $throwWarehouse = ThrowWarehouse::getByThrowCityId($throwCityId);
        if (!empty($throwWarehouse)) {
            $throwCityDetail["throw_city_warehouse"] = Warehouse::listByWarehouseIds(array_column($throwWarehouse, ThrowWarehouseConst::WAREHOUSE_ID));
        }
        return $throwCityDetail;
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public static function getDetailById($id) {
        $throwCityDetail = self::getById($id);
        if(empty($throwCityDetail)) {
           return array(); 
        }
        return self::getDetailByThrowCityId($throwCityDetail[ThrowCityConst::CITY_ID]);
    }
    
    /**
     *
     * @param type $cityId
     * @param type $warehouseId
     * @param type $status
     * @param type $createUserId
     * @param type $createTimeBegin
     * @param type $createTimeEnd
     * @param type $page
     * @param type $perPage
     * @return type
     */
    public static function getList($cityId, $cityIds,$warehouseId,$status,$createUserId,$createTimeStart
                    ,$createTimeEnd,$page,$perPage) {
        $builder = ThrowCityModel::orderBy(ThrowCityConst::ID, "desc");
        !empty($cityId) && $builder->where(ThrowCityConst::CITY_ID,$cityId);
        !empty($cityIds) && $builder->whereIn(ThrowCityConst::CITY_ID,$cityIds);
        if(!empty($warehouseId)) {
            $throwWarehouses =  ThrowWarehouse::listByWarehouseId($warehouseId);
            !empty($throwWarehouses) && $builder->whereIn(ThrowCityConst::CITY_ID, array_column($throwWarehouses, ThrowWarehouseConst::THROW_CITY_ID));
        }
        !empty($status) && $builder->where(ThrowCityConst::STATUS,$status);
        !empty($createUserId) && $builder->where(ThrowCityConst::CREATE_USER_ID,$createUserId);
        !empty($createTimeStart) && $builder->where(ThrowCityConst::CREATE_TIME,'>=',$createTimeStart);
        !empty($createTimeEnd) && $builder->where(ThrowCityConst::CREATE_TIME,"<=",$createTimeEnd);
        $total = $builder->count();
        $list  = $builder->offset(($page-1)*$perPage)->take($perPage)->get()->toArray();
        $list = self::fillList($list);
        return array(
            'total' => $total,
            'list' => $list,
        );
    }
    /**
     *
     * @param type $list
     * @return type
     */
    private static function fillList($list) {
        if(!empty($list)) {
            $throwWarehouse = ThrowWarehouse::listByThrowCityIds(array_column($list, ThrowCityConst::CITY_ID));
            $cityWarehouses = array();
            foreach($throwWarehouse as $key=>$val) {
                $cityWarehouses[$val[ThrowWarehouseConst::THROW_CITY_ID]][] = $val[ThrowWarehouseConst::WAREHOUSE_ID];
            }
            foreach($list as $k=>$val) {
                $list[$k]["warehouse_ids"] = $cityWarehouses[$val[ThrowCityConst::CITY_ID]];
            }
        }
        return $list;
    }

}
