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
use App\Models\Config\ConfigCar as ConfigCarModel;
use App\Constants\Db\Tables\Base\ConfigCar as ConfigCarConst;
use App\Constants\Db\Tables\Base\Warehouse as WarehouseConst;
use Exception;

class ConfigCar {

    /**
     * 
     * @param type $code
     * @param type $name
     * @param type $content
     * @param type $detail
     * @param type $orgId
     * @param type $createUserId
     * @param type $status
     * @return type
     * @throws Exception
     */
    public static function create($code, $name, $content, $detail, $orgId, $createUserId, $status) {
        $code = strtoupper($code);
        $configCar = self::getByCode($code,null,$orgId);
        if (!empty($configCar)) {
            throw new Exception('编码已经存在,请重新设置');
        }
        $configCar = self::getByName($name,null,null,$orgId);
        if (!empty($configCar)) {
            throw new Exception('名称已经存在,请重新设置');
        }
        $detail = self::checkDetail($detail);
        $currTime = time();
        $configCar = array(
            ConfigCarConst::CODE => $code,
            ConfigCarConst::NAME => $name,
            ConfigCarConst::CONTENT => $content,
            ConfigCarConst::DETAIL => json_encode($detail),
            ConfigCarConst::STATUS => $status,
            ConfigCarConst::ORG_ID => $orgId,
            ConfigCarConst::CREATE_USER_ID => $createUserId,
            ConfigCarConst::UPDATE_USER_ID => $createUserId,
            ConfigCarConst::CREATE_TIME => $currTime,
            ConfigCarConst::UPDATE_TIME => $currTime,
        );
        ConfigCarModel::beginTransaction();
        try {
            $configCar = ConfigCarModel::create($configCar);
            if (!$configCar) {
                throw new Exception('保存配置失败');
            }
        } catch (Exception $e) {
            ConfigCarModel::rollBack();
            throw $e;
        }
        ConfigCarModel::commit();
        return $configCar;
    }

    /**
     * 
     * @param type $id
     * @param type $name
     * @param type $content
     * @param type $detail
     * @param type $orgId
     * @param type $createUserId
     * @param type $status
     * @return type
     * @throws Exception
     */
    public static function update($id, $name, $content, $detail, $orgId, $updateUserId, $status) {
        $configCar = self::getById($id);
        if (empty($configCar)) {
            throw new Exception('配置不存在');
        }
        $configCar = self::getByCode($configCar[ConfigCarConst::CODE],null,$orgId,$id);
        if (!empty($configCar)) {
            throw new Exception('编码已经存在,请重新设置');
        }
        
        $configCar = self::getByName($name, null, $id,$orgId);
        if (!empty($configCar)) {
            throw new Exception('名称已经存在,请重新设置');
        }
        $detail = self::checkDetail($detail);
        $currTime = time();
        $configCar = array(
            ConfigCarConst::NAME => $name,
            ConfigCarConst::CONTENT => $content,
            ConfigCarConst::DETAIL => json_encode($detail),
            ConfigCarConst::STATUS => $status,
            ConfigCarConst::ORG_ID => $orgId,
            ConfigCarConst::UPDATE_USER_ID => $updateUserId,
            ConfigCarConst::UPDATE_TIME => $currTime,
        );
        ConfigCarModel::beginTransaction();
        try {
            $configCar = ConfigCarModel::where(ConfigCarConst::ID, $id)->update($configCar);
            if (!$configCar) {
                throw new Exception('保存配置失败');
            }
        } catch (Exception $e) {
            ConfigCarModel::rollBack();
            throw $e;
        }
        ConfigCarModel::commit();
        return $configCar;
    }

    /**
     * 
     * @param type $code
     * @param type $name
     * @param type $orgId
     * @param type $status
     * @param type $createTimeStart
     * @param type $createTimeEnd
     * @param type $page
     * @param type $perPage
     * @return type
     */
    public static function getList($code, $name, $orgId, $status, $createTimeStart, $createTimeEnd, $page, $perPage) {
        $builder = ConfigCarModel::orderBy(ConfigCarConst::ID, "desc");
        !empty($code) && $builder->where(ConfigCarConst::CODE, $code);
        !empty($name) && $builder->where(ConfigCarConst::NAME, 'like', '%' . addslashes($name) . "%");
        !empty($orgId) && $builder->where(ConfigCarConst::ORG_ID, $orgId);
        !empty($status) && $builder->where(ConfigCarConst::STATUS, $status);
        !empty($createTimeStart) && $builder->where(ConfigCarConst::CREATE_TIME, ">=", $createTimeStart);
        !empty($createTimeEnd) && $builder->where(ConfigCarConst::CREATE_TIME, "<=", $createTimeEnd);
        $count = $builder->count();
        $list = $builder->offset(($page - 1) * $perPage)->take($perPage)->get()->toArray();
        $list = self::fillList($list);
        return array(
            "total" => $count,
            "list" => $list,
        );
    }

    /**
     * 
     * @param type $id
     * @return type
     * @throws Exception
     */
    public static function getDetail($id = "") {
        $configCar = self::getById($id);
        if (empty($configCar)) {
            throw new Exception('配置不存在');
        }
        $configCar[ConfigCarConst::DETAIL] = self::fillDetail($configCar[ConfigCarConst::DETAIL]);
        return $configCar;
    }
    /**
     * 
     * @param type $codes
     * @param type $status
     * @return type
     */
    public static function listByCodes($codes = array(),$status=null){
        $configCars = self::getByCodes($codes,$status);
        return self::fillList($configCars);
    }
    /**
     * 
     * @param type $codes
     * @param type $status
     * @return type
     */
    public static function getByCodes($codes = array(),$status=null) {
        $build = ConfigCarModel::whereIn(ConfigCarConst::CODE, $codes);
        if (!empty($status)) {
            $build->where(ConfigCarConst::STATUS, $status);
        }
        return $build->get()->toArray();
    }
    

    /**
     * 
     * @param type $code
     * @param type $status
     * @return type
     */
    public static function getByCode($code, $status = null,$orgId = null,$exceptId = null) {
        $build = ConfigCarModel::where(ConfigCarConst::CODE, $code);
        if (!empty($status)) {
            $build->where(ConfigCarConst::STATUS, $status);
        }
        if(!empty($orgId)) {
            $build->where(ConfigCarConst::ORG_ID,$orgId);
        }
        if(!empty($exceptId)) {
            $build->where(ConfigCarConst::ID, "!=", $exceptId);
        }
        return $build->first();
    }
    /**
     * 
     * @param type $id
     * @return type
     */
    public static function getById($id) {
        return ConfigCarModel::where(ConfigCarConst::ID, $id)->first();
    }

    /**
     * 
     * @param type $name
     * @param type $status
     * @param type $exceptCodeId
     * @return type
     */
    public static function getByName($name, $status = null, $exceptId = null,$orgId = null) {
        $build = ConfigCarModel::where(ConfigCarConst::NAME, $name);
        if (!empty($status)) {
            $build->where(ConfigCarConst::STATUS, $status);
        }
        if (!empty($exceptId)) {
            $build->where(ConfigCarConst::ID, "!=", $exceptId);
        }
        if(!empty($orgId)) {
            $build->where(ConfigCarConst::ORG_ID,$orgId);
        }
        return $build->first();
    }

    /**
     * 
     * @param type $detail
     * @throws Exception
     */
    private static function checkDetail($detail = array()) {
        $types = WarehouseConst::types();
        foreach ($detail as $item) {
            if (!array_key_exists($item, $types)) {
                throw new Exception('配置详情错误code:' . $item);
            }
        }
        return $detail;
    }

    /**
     * 
     * @param type $list
     * @return type
     */
    private static function fillList($list = array()) {
        if (empty($list)) {
            return array();
        }
        foreach ($list as $key => $val) {
            $list[$key][ConfigCarConst::DETAIL] = self::fillDetail($val[ConfigCarConst::DETAIL]);
        }
        return $list;
    }

    /**
     * 
     * @param type $detail
     * @return type
     */
    private static function fillDetail($detail) {
        $detail = json_decode($detail,true);
        $types = WarehouseConst::types();
        foreach ($detail as $key => $item) {
            $detail[$key] = array(
                "code" => $item,
                "value" => $types[$item]
            );
        }
        return $detail;
    }

}
