<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:16
 */

namespace App\Repositories;

use App\Models\Throws\ThrowWarehouse as ThrowWarehouseModel;
use App\Constants\Db\Tables\Base\ThrowWarehouse as ThrowWarehouseConst;

class ThrowWarehouse{
    /**
     * @param $throwCityId
     * @param null $status
     * @return mixed
     */
    public static function getByThrowCityId($throwCityId, $status = null){
        $builder = ThrowWarehouseModel::where(ThrowWarehouseConst::THROW_CITY_ID, $throwCityId);
        if(!empty($status)){
            $builder->where(ThrowWarehouseConst::STATUS, $status);
        }
        return $builder->orderBy(ThrowWarehouseConst::ID, 'asc')->get()->toArray();
    }

    /**
     * @param $throwCityIds
     * @param null $status
     * @return mixed
     */
    public static function listByThrowCityIds($throwCityIds, $status = null){
        $builder = ThrowWarehouseModel::whereIn(ThrowWarehouseConst::THROW_CITY_ID, $throwCityIds);
        if(!empty($status)){
            $builder->where(ThrowWarehouseConst::STATUS, $status);
        }
        return $builder->orderBy(ThrowWarehouseConst::ID, 'asc')->get();
    }
    /**
     * 
     * @param type $warehouseId
     * @return type
     */
    public static function listByWarehouseId($warehouseId) {
        return ThrowWarehouseModel::where(ThrowWarehouseConst::WAREHOUSE_ID,$warehouseId)
                ->get()->toArray();
    }
}