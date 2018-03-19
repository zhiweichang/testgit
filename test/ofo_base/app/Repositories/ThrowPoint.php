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
use App\Models\Throws\ThrowPoint as ThrowPointModel;
use App\Models\Throws\ThrowArea as ThrowAreaModel;
use App\Constants\Db\Tables\Base\ThrowPoint as ThrowPointConst;
use App\Constants\Db\Tables\Base\ThrowArea as ThrowAreaConst;

class ThrowPoint{
    /**
     * @param $cityId
     * @param $longitude
     * @param $latitude
     * @param $address
     * @param $throwAreaId
     * @param $type
     * @param $contractUserName
     * @param $contractUserMobile
     * @param $createUserId
     * @return static
     * @throws Exception
     */
    public static function create($cityId, $longitude, $latitude, $address,
                                  $throwAreaId, $type, $contractUserName, $contractUserMobile, $createUserId){
        $dbThrowArea = ThrowAreaModel::where(ThrowAreaConst::ID, $throwAreaId)
            ->where(ThrowAreaConst::CITY_ID, $cityId)->first();
        if(empty($dbThrowArea)){
            throw new Exception(trans("message.THROW_AREA_NO_EXSIT"));
        }
        $data = [
            ThrowPointConst::CITY_ID => $cityId,
            ThrowPointConst::LONGITUDE => $longitude,
            ThrowPointConst::LATITUDE => $latitude,
            ThrowPointConst::ADDRESS => $address,
            ThrowPointConst::THROW_AREA_ID => $throwAreaId,
            ThrowPointConst::TYPE => $type,
            ThrowPointConst::CONTACT_USER_NAME => $contractUserName,
            ThrowPointConst::CONTACT_USER_MOBILE => $contractUserMobile,
            ThrowPointConst::CREATE_USER_ID => $createUserId,
            ThrowPointConst::STATUS=> ThrowPointConst::STATUS_ENABLED,

        ];
        ThrowPointModel::beginTransaction();
        try{
            $throwPoint = ThrowPointModel::create($data);
            if(!$throwPoint){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $throwPoint[ThrowPointConst::THROW_POINT_ID] = $throwPoint[ThrowPointConst::ID]; //DB中有此字段，但是实际没有使用
            $mqData = $throwPoint->toArray();
            if(!Util::sendToMq($mqData, RoutingKey::THROW_POINT_CREATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            ThrowPointModel::rollBack();
            throw $e;
        }
        ThrowPointModel::commit();
        return $throwPoint;
    }

    /**
     * @param $throwPointId
     * @param $cityId
     * @param $longitude
     * @param $latitude
     * @param $address
     * @param $throwAreaId
     * @param $type
     * @param $contractUserName
     * @param $contractUserMobile
     * @param $updateUserId
     * @return bool
     * @throws Exception
     */
    public static function update($throwPointId, $cityId, $longitude, $latitude, $address,
                                  $throwAreaId, $type, $contractUserName, $contractUserMobile, $updateUserId){
        $dbThrowPoint = ThrowPointModel::where(ThrowPointConst::ID, $throwPointId)->first();
        if(empty($dbThrowPoint)){
            throw new Exception(trans("message.THROW_POINT_NO_EXSIT"));
        }
        $dbThrowArea = ThrowAreaModel::where(ThrowAreaConst::ID, $throwAreaId)
            ->where(ThrowAreaConst::CITY_ID, $cityId)->first();
        if(empty($dbThrowArea)){
            throw new Exception(trans("message.THROW_AREA_NO_EXSIT"));
        }
        $data = [
            ThrowPointConst::CITY_ID => $cityId,
            ThrowPointConst::LONGITUDE => $longitude,
            ThrowPointConst::LATITUDE => $latitude,
            ThrowPointConst::ADDRESS => $address,
            ThrowPointConst::THROW_AREA_ID => $throwAreaId,
            ThrowPointConst::TYPE => $type,
            ThrowPointConst::CONTACT_USER_NAME => $contractUserName,
            ThrowPointConst::CONTACT_USER_MOBILE => $contractUserMobile,
//            ThrowPointConst::CREATE_USER_ID => $updateUserId,

        ];
        ThrowPointModel::beginTransaction();
        try{
            if(!ThrowPointModel::where(ThrowPointConst::ID, $throwPointId)->update($data)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $mqData = $data;
            $mqData[ThrowPointConst::THROW_POINT_ID] = $throwPointId;
            if(!Util::sendToMq($mqData, RoutingKey::THROW_POINT_UPDATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            ThrowPointModel::rollBack();
            throw $e;
        }
        ThrowPointModel::commit();
        return true;
    }

    /**
     * @param $throwPointId
     * @param $status
     * @param $updateUserId
     * @return bool
     * @throws Exception
     */
    public static function updateStatus($throwPointId, $status, $updateUserId){
        $dbThrowPoint = ThrowPointModel::where(ThrowPointConst::ID, $throwPointId)->first();
        if(empty($dbThrowPoint)){
            throw new Exception(trans("message.THROW_POINT_NO_EXSIT"));
        }
        if($dbThrowPoint[ThrowPointConst::STATUS] == $status){
            throw new Exception(trans("message.UPDATE_STATUS_NO_NEED"));
        }
        $data = [
            ThrowPointConst::STATUS => $status,
            //ThrowPointConst::CREATE_USER_ID => $updateUserId,
        ];
        ThrowPointModel::beginTransaction();
        try{
            if(!ThrowPointModel::where(ThrowPointConst::ID, $throwPointId)->update($data)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $mqData = $data;
            $mqData[ThrowPointConst::THROW_POINT_ID] = $throwPointId;
            if(!Util::sendToMq($mqData, RoutingKey::THROW_POINT_UPDATE_STATUS)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            ThrowPointModel::rollBack();
            throw $e;
        }
        ThrowPointModel::commit();
        return true;
    }

    /**
     * @param array $throwPointIds
     * @param null $status
     * @return mixed
     */
    public static function listByThrowPointIds(array $throwPointIds, $status = null){
        $builder = ThrowPointModel::whereIn(ThrowPointConst::ID, $throwPointIds);
        if(!empty($status)){
            $builder->where(ThrowPointConst::STATUS, $status);
        }
        return $builder->get()->toArray();
    }

    /**
     * @param array $throwPointAddresses
     * @param null $status
     * @return mixed
     */
    public static function listByThrowPointAddresses(array $throwPointAddresses, $status = null){
        $builder = ThrowPointModel::whereIn(ThrowPointConst::ADDRESS, $throwPointAddresses);
        if(!empty($status)){
            $builder->where(ThrowPointConst::STATUS, $status);
        }
        return $builder->get()->toArray();
    }

    /**
     * @param $throwCityId
     * @param null $status
     * @return mixed
     */
    public static function listByThrowCityId($throwCityId, $status = null){
        $builder = ThrowPointModel::where(ThrowPointConst::CITY_ID, $throwCityId);
        if(!empty($status)){
            $builder->where(ThrowPointConst::STATUS, $status);
        }
        return $builder->get()->toArray();
    }

    /**
     * @param $throwPointId
     * @param null $status
     * @return mixed
     */
    public static function getByThrowPointId($throwPointId, $status = null){
        $builder = ThrowPointModel::where(ThrowPointConst::ID, $throwPointId);
        if(!empty($status)){
            $builder->where(ThrowPointConst::STATUS, $status);
        }
        return $builder->first();
    }
    /**
     * 
     * @param type $id
     * @param type $address
     * @param type $cityIds
     * @param type $type
     * @param type $status
     * @param type $createUserId
     * @param type $throwAreaId
     * @param type $createTimeStart
     * @param type $createTimeEnd
     * @param type $page
     * @param type $perPage
     * @return type
     */
    public static function getList($id,$address,$cityIds,$type,$status,$createUserId,$throwAreaId
                    ,$createTimeStart,$createTimeEnd,$page,$perPage) {
        $builder = ThrowPointModel::orderBy(ThrowPointConst::ID, "desc");
        !empty($id) && $builder->where(ThrowPointConst::ID,$id);
        !empty($address) && $builder->where(ThrowPointConst::ADDRESS,'like', '%'.addslashes($address)."%");
        !empty($cityIds) && $builder->whereIn(ThrowPointConst::CITY_ID,$cityIds);
        !empty($type) && $builder->where(ThrowPointConst::TYPE,$type);
        !empty($status) && $builder->where(ThrowPointConst::STATUS,$status);
        !empty($createUserId) && $builder->where(ThrowPointConst::CREATE_USER_ID,$createUserId);
        !empty($throwAreaId) && $builder->where(ThrowPointConst::THROW_AREA_ID,$throwAreaId);
        !empty($createTimeStart) && $builder->where(ThrowPointConst::CREATE_TIME,">=",$createTimeStart);
        !empty($createTimeEnd) && $builder->where(ThrowPointConst::CREATE_TIME,"<=",$createTimeEnd);
        $total = $builder->count();
        $list  = $builder->offset(($page-1)*$perPage)->take($perPage)->get()->toArray();
        return array(
            'total' => $total,
            'list' => $list,
        );
    }

}
