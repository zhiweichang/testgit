<?php

/**
 * User: djq
 * Date: 2018/1/2
 * Time: 下午2:16
 */

namespace App\Repositories;

use App\Constants\Mq\RoutingKey;
use App\Libs\Util;
use Carbon\Carbon;
use Exception;
use App\Models\MaterielConfig\MaterielConfig as MaterielConfigModel;
use App\Constants\Db\Tables\Base\MaterielConfig as MaterielConfigConst;
use App\Constants\Db\Tables\Base\Pn as PnConst;
use Illuminate\Support\Facades\DB;

class MaterielConfig {

    /**
     * 
     * @param type $mtype
     * @param type $code
     * @param type $name
     * @param type $createUserId
     * @return type
     * @throws Exception
     */
    public static function create($mtype, $code, $name, $createUserId) {
        self::checkCode($mtype, $code);
        $materielConfig = self::getByMtypeCode($code, $mtype);
        if (!empty($materielConfig)) {
            throw new Exception('该'.MaterielConfigConst::$types[$mtype].'代码已存在，请重新设置');
        }
        $materielConfig = self::getByMtypeName($name, $mtype);
        if (!empty($materielConfig)) {
            throw new Exception('该'.MaterielConfigConst::$types[$mtype].'名称已存在，请重新设置');
        }
        $currTime = time();
        $data = array(
            MaterielConfigConst::CODE => $code,
            MaterielConfigConst::NAME => $name,
            MaterielConfigConst::MTYPE => $mtype,
            MaterielConfigConst::STATUS => MaterielConfigConst::STATUS_ENABLED,
            MaterielConfigConst::CREATE_TIME => $currTime,
            MaterielConfigConst::UPDATE_TIME => $currTime,
            MaterielConfigConst::CREATE_USER_ID => $createUserId,
            MaterielConfigConst::UPDATE_USER_ID => $createUserId,
            MaterielConfigConst::IS_NOT_IMPORT => MaterielConfigConst::IMPORT_FALSE,
        );
        MaterielConfigModel::beginTransaction();
        try {
            $materielConfig = MaterielConfigModel::create($data);
            if (empty($materielConfig)) {
                throw new Exception('保存配置信息失败');
            }
        } catch (Exception $e) {
            MaterielConfigModel::rollBack();
            throw $e;
        }
        MaterielConfigModel::commit();
        return $materielConfig;
    }

    /**
     * @param $bomId
     * @param $userId
     * @param $deliveryWays
     * @return bool
     * @throws Exception
     */
    public static function update($id, $code, $name, $updateUserId) {
        $materielConfig = self::getById($id);
        if (empty($materielConfig)) {
            throw new Exception('配置信息不存在');
        }
        self::checkCode($materielConfig[MaterielConfigConst::MTYPE], $code);
        if($materielConfig[MaterielConfigConst::IS_NOT_IMPORT] != MaterielConfigConst::IMPORT_FALSE) {
            throw new Exception('导入数据不能修改');
        }
        $info = self::getByMtypeCode($code, $materielConfig[MaterielConfigConst::MTYPE], $id);
        if (!empty($info)) {
            throw new Exception('该'.MaterielConfigConst::$types[$materielConfig[MaterielConfigConst::MTYPE]].'代码已存在，请重新设置');
        }
        $info = self::getByMtypeName($name, $materielConfig[MaterielConfigConst::MTYPE], $id);
        if (!empty($info)) {
            throw new Exception('该'.MaterielConfigConst::$types[$materielConfig[MaterielConfigConst::MTYPE]].'名称已存在，请重新设置');
        }
        
        
        $currTime = time();
        $data = [
            MaterielConfigConst::NAME => $name,
            MaterielConfigConst::UPDATE_TIME => $currTime,
            MaterielConfigConst::UPDATE_USER_ID => $updateUserId,
        ];
        $checkPnUsed = Pn::checkUsedConfig($id);
        if(empty($checkPnUsed)) {
            $data[MaterielConfigConst::CODE] = $code;
        }
        MaterielConfigModel::beginTransaction();
        try {
            if (!MaterielConfigModel::where(MaterielConfigConst::ID, $id)->update($data)) {
                throw new Exception('更新配置信息失败');
            }
        } catch (Exception $e) {
            MaterielConfigModel::rollBack();
            throw $e;
        }
        MaterielConfigModel::commit();
        return true;
    }

    /**
     * 
     * @param type $types
     * @return type
     */
    public static function listByTypes($types, $name = null) {
        $build = MaterielConfigModel::whereIn(MaterielConfigConst::MTYPE, $types)
                ->where(MaterielConfigConst::IS_NOT_IMPORT, MaterielConfigConst::IMPORT_FALSE)
                ->orderBy(MaterielConfigConst::ID, "desc");
        if (!empty($name)) {
            $build->where(MaterielConfigConst::NAME, "like", "%" . addslashes($name) . "%");
        }
        $list = $build->get()->toArray();
        return self::fillList($list);
    }

    /**
     * 
     * @param type $type
     * @param type $page
     * @param type $perPage
     * @return type
     */
    public static function getList($type,$name, $page = 1, $perPage = 50) {
        $return = array("total" => 0, "list" => array());
        $build = MaterielConfigModel::where(MaterielConfigConst::MTYPE, $type)
                ->where(MaterielConfigConst::IS_NOT_IMPORT, MaterielConfigConst::IMPORT_FALSE)
                ->orderBy(MaterielConfigConst::ID, "desc");
        if(!empty($name)) {
            $build->where(MaterielConfigConst::NAME,'like','%'.addslashes($name)."%");
        }
        $total = $build->count();
        $list = $build->offset(($page - 1) * $perPage)->take($perPage)->get()->toArray();
        $list = self::fillList($list);
        return array(
            "total" => $total,
            "list" => $list,
        );
    }
    /**
     * 
     * @param type $list
     * @return int
     */
    private static function fillList($list) {
        if (empty($list)) {
            return array();
        }
        $ids = array_column($list, MaterielConfigConst::ID);
        $pnIds = Pn::checkUsedConfigs($ids);
        $usedId = array_unique(array_column($pnIds, PnConst::COMPONENT));
        foreach ($list as $key => $val) {
            if (in_array($val[MaterielConfigConst::ID], $usedId)) {
                $val["is_used"] = 1;
            } else {
                $val["is_used"] = 0;
            }
            $list[$key] = $val;
        }
        return $list;
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public static function getDetail($id) {
        return MaterielConfigModel::where(MaterielConfigConst::ID,$id)->first();
    }

    /**
     * 
     * @param type $mtype
     * @param type $code
     * @throws Exception
     */
    private static function checkCode($mtype, $code) {
        $types = array(
            MaterielConfigConst::ORG_CAR_PARTS => array("range" => array(4, 4), "reg" => '/^[A-Za-z0-9]+$/','msg'=>"必须为4位数字或者字母或者两者的组合"),
            MaterielConfigConst::LOCK_PARTS => array("range" => array(4, 4), "reg" => '/^[A-Za-z0-9]+$/','msg'=>"必须为4位数字或者字母或者两者的组合"),
            MaterielConfigConst::DXDT_CAR_PARTS => array("range" => array(4, 4), "reg" => '/^[A-Za-z0-9]+$/','msg'=>"必须为4位数字或者字母或者两者的组合"),
            MaterielConfigConst::ELECTRIC_BIKE_PARTS => array("range" => array(4, 4), "reg" => '/^[A-Za-z0-9]+$/','msg'=>"必须为4位数字或者字母或者两者的组合"),
        );
        $lenth = strlen($code);
        if (!($lenth >= $types[$mtype]["range"][0] && $lenth <= $types[$mtype]["range"][1]) || !preg_match($types[$mtype]["reg"], $code)) {
            throw new Exception($types[$mtype]["msg"]);
        }
    }

    /**
     * 
     * @param type $code
     * @param type $mtype
     */
    public static function getByMtypeCode($code = '', $mtype = 0, $id = null) {
        $build = MaterielConfigModel::where(MaterielConfigConst::MTYPE, $mtype)
                ->where(MaterielConfigConst::CODE, $code)
                ->where(MaterielConfigConst::IS_NOT_IMPORT, MaterielConfigConst::IMPORT_FALSE);
        if (!empty($id)) {
            $build->where(MaterielConfigConst::ID, "!=", $id);
        }
        return $build->get()->toArray();
    }

    /**
     * 
     * @param type $name
     * @param type $mtype
     */
    public static function getByMtypeName($name = '', $mtype = 0, $id = null) {
        $build = MaterielConfigModel::where(MaterielConfigConst::MTYPE, $mtype)
                ->where(MaterielConfigConst::NAME, $name)
                ->where(MaterielConfigConst::IS_NOT_IMPORT, MaterielConfigConst::IMPORT_FALSE);
        if (!empty($id)) {
            $build->where(MaterielConfigConst::ID, "!=", $id);
        }
        return $build->get()->toArray();
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public static function getById($id = 0) {
        return MaterielConfigModel::where(MaterielConfigConst::ID, $id)->first();
    }

    /**
     * 
     * @param array $ids
     * @return type
     */
    public static function getByIds(array $ids) {
        return MaterielConfigModel::whereIn(MaterielConfigConst::ID, $ids)->get()->toArray();
    }

}
