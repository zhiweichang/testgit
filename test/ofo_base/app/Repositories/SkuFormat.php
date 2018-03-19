<?php
/**
 * Created by PhpStorm.
 * User: jianqiang
 * Date: 2017/8/15
 * Time: 下午2:16
 */

namespace App\Repositories;
use Carbon\Carbon;
use App\Constants\Mq\RoutingKey;
use App\Libs\Util;
use App\Models\SkuFormat\SkuFormat as SkuFormatModel;
use App\Constants\Db\Tables\Base\SkuFormat as SkuFormatConst;
use App\Constants\Db\Tables\Base\Code as CodeConst;
use App\Constants\Db\Tables\Base\CodeVersion as CodeVersionConst;
use Exception;

class SkuFormat{
    /**
     * 
     * @param type $type
     * @param type $value
     * @param type $createUserId
     * @return type
     * @throws Exception
     */
    public static function create($type, $value, $createUserId){
        $dbSkuFormat = self::getByType($type);
        if(!empty($dbSkuFormat)){
            throw new Exception(trans("message.SKU_FORMAT_EXSIT"));
        }
        $currTime = Carbon::now()->timestamp;
        $skuFormat = [
            SkuFormatConst::TYPE => $type,
            SkuFormatConst::VALUE => $value,
            SkuFormatConst::CREATE_USER_ID => $createUserId,
            SkuFormatConst::CREATE_TIME => $currTime,
            SkuFormatConst::UPDATE_TIME => $currTime,
            SkuFormatConst::STATUS => SkuFormatConst::STATUS_ENABLED,
        ];
        SkuFormatModel::beginTransaction();
        try{
            $skuFormat = SkuFormatModel::create($skuFormat);
            if(!$skuFormat){
                 throw new Exception(trans("message.SAVE_FAIL"));
            }
            $mqData = $skuFormat->toArray();
            if(!Util::sendToMq($mqData, RoutingKey::SKUFORMAT_CREATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            SkuFormatModel::rollBack();
            throw $e;
        }
        SkuFormatModel::commit();
        return $skuFormat;
    }

    /**
     * 
     * @param type $id
     * @param type $value
     * @param type $updateUserId
     * @return boolean
     * @throws Exception
     */
    public static function update($id,$value,$updateUserId){
        //$updateUserId 备用,等方案定后在确定是否使用
        $id = intval($id);
        $dbSkuFormat = self::getById($id);
        if(empty($dbSkuFormat)){
            throw new Exception(trans("message.SKU_FORMAT_NO_EXSIT"));
        }
        $data = [
            SkuFormatConst::VALUE => $value,
            SkuFormatConst::UPDATE_TIME => Carbon::now()->timestamp,
        ];
        SkuFormatModel::beginTransaction();
        try{
            if(!SkuFormatModel::where(SkuFormatConst::ID, $id)->update($data)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $mqData = $data;
            $mqData[SkuFormatConst::ID] = $id;
            if(!Util::sendToMq($mqData, RoutingKey::SKUFORMAT_UPDATE)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            SkuFormatModel::rollBack();
            throw $e;
        }
        SkuFormatModel::commit();
        return true;
    }

    /**
     * 
     * @param type $id
     * @param type $status
     * @param type $updateUserId
     * @throws Exception
     */
    public static function updateStatus($id, $status, $updateUserId){
        $id = intval($id);
        $dbSkuFormat = self::getById($id);
        if(empty($dbSkuFormat)){
            throw new Exception(trans("message.SKU_FORMAT_NO_EXSIT"));
        }
        if($dbSkuFormat[SkuFormatConst::STATUS] == $status){
            throw new Exception(trans("message.UPDATE_STATUS_NO_NEED"));
        }
        SkuFormatModel::beginTransaction();
        try{
            $data = [
                SkuFormatConst::STATUS => $status,
                SkuFormatConst::UPDATE_TIME => Carbon::now()->timestamp,
            ];
            if(!SkuFormatModel::where(SkuFormatConst::ID, $id)->update($data)){
                throw new Exception(trans("message.SAVE_FAIL"));
            }
            $mqData = [
                'id' => $id,
                'status' => $status,
            ];
            if(!Util::sendToMq($mqData, RoutingKey::SKUFORMAT_UPDATE_STATUS)){
                throw new Exception(trans("message.SEND_MESSAGE_FAIL"));
            }
        }catch(Exception $e){
            SkuFormatModel::rollBack();
            throw $e;
        }
        SkuFormatModel::commit();
    }
    /**
     * 
     * @param type $status
     * @param type $page
     * @param type $perPage
     * @return type
     */
    public static function getList($status=null, $page,$perPage) {
        $builder = SkuFormatModel::orderBy(SkuFormatConst::ID, "desc");
        if(!empty($status)) {
            $builder->where(SkuFormatConst::STATUS,$status);
        }
        return array(
            "total"=>$builder->count(),
            "list"=>$builder->offset(($page-1)*$perPage)->take($perPage)->get()->toArray(),
        );
    }

    





    /**
     * 
     * @param type $type
     * @param type $status
     * @return type
     */
    public static function getByType($type, $status = null){
        $builder = SkuFormatModel::where(SkuFormatConst::TYPE, $type);
        if(!is_null($status)){
            $builder->where(SkuFormatConst::STATUS, $status);
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
        $builder = SkuFormatModel::where(SkuFormatConst::ID, $id);
        if(!is_null($status)){
            $builder->where(SkuFormatConst::STATUS, $status);
        }
        return $builder->first();
    }
}