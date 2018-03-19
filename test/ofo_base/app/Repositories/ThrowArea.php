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
use App\Models\Throws\ThrowArea as ThrowAreaModel;
use App\Constants\Db\Tables\Base\ThrowArea as ThrowAreaConst;

class ThrowArea{
    /**
     * @param $cityId
     * @param $areaName
     * @return static
     * @throws Exception
     */
    public static function create($cityId, $areaName){
        $dbItem = ThrowAreaModel::where(ThrowAreaConst::CITY_ID, $cityId)
            ->where(ThrowAreaConst::NAME, $areaName)->first();
        if(!empty($dbItem)){
            throw new Exception(trans("message.THROW_AREA_EXSIT"));
        }
        $data = [
            ThrowAreaConst::CITY_ID => $cityId,
            ThrowAreaConst::NAME => $areaName,
            ThrowAreaConst::CREATE_TIME => time(),
        ];
        ThrowAreaModel::beginTransaction();
        try{
            $area = ThrowAreaModel::create($data);
            if(!$area){
                throw new Exception(trans("message.UPDATE_FAIL"));
            }
            $mqData = $area->toArray();
            if(!Util::sendToMq($mqData, RoutingKey::THROW_AREA_CREATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            ThrowAreaModel::rollBack();
            throw $e;
        }
        ThrowAreaModel::commit();
        return $area;
    }
    /**
     * @param array $throwAreaIds
     * @return mixed
     */
    public static function listByThrowAreaIds(array $throwAreaIds){
        $builder = ThrowAreaModel::whereIn(ThrowAreaConst::ID, $throwAreaIds);
        return $builder->get()->toArray();
    }

    /**
     * @param $throwAreaId
     * @return mixed
     */
    public static function getByThrowAreaId($throwAreaId){
        $builder = ThrowAreaModel::where(ThrowAreaConst::ID, $throwAreaId);
        return $builder->first();
    }
    /**
     * 
     * @param type $cityId
     * @return type
     */
    public static function getByCityId($cityId) {
        return ThrowAreaModel::where(ThrowAreaConst::CITY_ID, $cityId)->get()->toArray();
    }
}