<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:16
 */

namespace App\Repositories;

use App\Models\Factory\Factory as FactoryModel;
use App\Constants\Db\Tables\Base\Factory as FactoryConst;

class Factory{
    /**
     * @param array $factoryIds
     * @param null $status
     * @return mixed
     */
    public static function listByFactoryIds(array $factoryIds, $status = null){
        $builder = FactoryModel::whereIn(FactoryConst::FACTORY_ID, $factoryIds);
        if(!empty($status)){
            $builder->where(FactoryConst::STATUS, $status);
        }
        return $builder->get()->toArray();
    }

    /**
     * @param $factoryId
     * @param null $status
     */
    public static function getByFactoryId($factoryId, $status = null, $exceptFactoryId = null){
        $builder = FactoryModel::where(FactoryConst::FACTORY_ID, $factoryId);
        if(!empty($status)){
            $builder->where(FactoryConst::STATUS, $status);
        }
        if(!is_null($exceptFactoryId)){
            $builder->where(FactoryConst::FACTORY_ID, '!=', $exceptFactoryId);
        }
        return $builder->first();
    }
    /**
     * 
     * @param type $supplierId
     * @param type $factoryId
     * @param type $name
     * @param type $cityId
     * @param type $status
     * @param type $page
     * @param type $perPage
     * @return type
     */
    public static function getList($supplierId, $factoryId, $name, $cityId, $status, $page, $perPage) {
        $builder = FactoryModel::orderBy(FactoryConst::ID, "desc");
        !empty($supplierId) && $builder->where(FactoryConst::SUPPLIER_ID, $supplierId);
        !empty($factoryId) && $builder->where(FactoryConst::FACTORY_ID, $factoryId);
        !empty($name) && $builder->where(FactoryConst::NAME, 'like', '%' . $name . "%");
        !empty($cityId) && $builder->where(FactoryConst::CITY_ID, $cityId);
        !empty($status) && $builder->where(FactoryConst::STATUS, $status);
        $total = $builder->count();
        $list = $builder->offset(($page - 1) * $perPage)->take($perPage)->get()->toArray();
        return array(
            'total' => $total,
            'list' => $list,
        );
    }
    
    /**
     * 
     * @param type $supplierId
     * @return type
     */
    public static function getBySupplierId($supplierId){
        return FactoryModel::where(FactoryConst::SUPPLIER_ID, $supplierId)->get()->toArray();
    }
    /**
     * 
     * @param type $cityIds
     * @return type
     */
    public static function listByCityIds($cityIds = array()) {
        if (empty($cityIds)) {
            return array();
        }
        return FactoryModel::whereIn(FactoryConst::CITY_ID, $cityIds)->get()->toArray();
    }

}