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
use App\Models\Pn\Pn as PnModel;
use App\Constants\Db\Tables\Base\Pn as PnConst;
use App\Constants\Db\Tables\Base\MaterielConfig as MaterielConfigConst;
use App\Constants\Db\Tables\Base\Country as CountryConst;
use App\Constants\Db\Tables\Base\Supplier as SupplierConst;
use Exception;

class Pn{
    /**
     * 
     * @param type $level
     * @param type $mtype
     * @param type $country
     * @param type $name
     * @param type $component
     * @param type $content
     * @param type $supplierId
     * @param type $pnType
     * @param type $style
     * @param type $packageName
     * @param type $accuracyName
     * @param type $createUserId
     * @param type $note
     * @return type
     * @throws Exception
     */
    public static function create($level,$mtype,$country,$name,$component,$content,$supplierId,
                    $pnType,$style,$packageName,$accuracyName,$createUserId,$note){
        $types = self::listTypesByLevel($level,$pnType);
        if (!array_key_exists($mtype, $types)) {
            throw new Exception('物料类别和物料级别不匹配');
        }
        $componentDetail = MaterielConfig::getById($component);
        if (empty($componentDetail) || $componentDetail[MaterielConfigConst::IS_NOT_IMPORT] != MaterielConfigConst::IMPORT_FALSE
                || strlen($componentDetail[MaterielConfigConst::CODE]) != 4) {
            throw new Exception('部件类型错误');
        }
        if($componentDetail[MaterielConfigConst::MTYPE] != self::pnToPartConfigRelation($mtype)) {
            throw new Exception('部件类型和物料类别不匹配');
        }
        $supplierDetail  = Supplier::getBySupplierId($supplierId);
        if(empty($supplierDetail)) {
            throw new Exception('供应商错误');
        }
        $seriaNo = self::getMaxSerialNo($mtype,$component);
        $pnNo = self::getPnNo($mtype,$componentDetail,$seriaNo);
        if(!empty(self::getByPnNo($pnNo))) {
            throw new Exception('工程PN已经存在');
        }
        if(!empty(self::getMyLastCreate($mtype,$component,$createUserId))) {
            throw new Exception('添加PN太频繁,请刷新页面再操作');
        }
        $currTime = time();
        $pn = array(
            PnConst::PN_NO => $pnNo,
            PnConst::LEVEL => $level,
            PnConst::MTYPE => $mtype,
            PnConst::COUNTRY => $country,
            PnConst::NAME => $name,
            PnConst::COMPONENT => $component,
            PnConst::CONTENT => $content,
            PnConst::SERIAL_NO => $seriaNo,
            PnConst::STYLE => $style,
            PnConst::PACKAGE_NAME => $packageName,
            PnConst::ACCURACY_NAME => $accuracyName,
            PnConst::STATUS => PnConst::STATUS_ENABLED,
            PnConst::CREATE_TIME => $currTime,
            PnConst::UPDATE_TIME => $currTime,
            PnConst::CREATE_USER_ID => $createUserId,
            PnConst::UPDATE_USER_ID => $createUserId,
            PnConst::SUPPLIER_ID => $supplierId,
            PnConst::PN_TYPE => $pnType,
            PnConst::NOTE => $note,
            PnConst::IS_NOT_IMPORT => PnConst::IMPORT_FALSE,
        );
        PnModel::beginTransaction();
        
        try {
            $pn = PnModel::create($pn);
            if (!$pn) {
                throw new Exception('保存PN失败');
            }
        } catch (Exception $e) {
            PnModel::rollBack();
            throw $e;
        }
        PnModel::commit();
        return $pn;
    }
    

    /**
     * 
     * @param type $id
     * @param type $level
     * @param type $mtype
     * @param type $country
     * @param type $name
     * @param type $component
     * @param type $content
     * @param type $supplierId
     * @param type $pnType
     * @param type $style
     * @param type $packageName
     * @param type $accuracyName
     * @param type $note
     * @param type $updateUserId
     * @return type
     * @throws Exception
     */
    public static function update($id,$level,$mtype,$country,$name,$component,$content,$supplierId,
                    $pnType,$style,$packageName,$accuracyName,$note,$updateUserId) {
        $pn = self::getById($id);
        if(empty($pn)) {
            throw new Exception('工程PN不存在');
        }
        if($pn[PnConst::IS_NOT_IMPORT] != PnConst::IMPORT_FALSE) {
            throw new Exception('导入数据不能修改');
        }
        $types = self::listTypesByLevel($level,$pnType);
        if (!array_key_exists($mtype, $types)) {
            throw new Exception('物料类别和物料级别不匹配');
        }
        
        $componentDetail = MaterielConfig::getById($component);
        if (empty($componentDetail) || $componentDetail[MaterielConfigConst::IS_NOT_IMPORT] != MaterielConfigConst::IMPORT_FALSE
                || strlen($componentDetail[MaterielConfigConst::CODE]) != 4) {
            throw new Exception('部件类型错误');
        }
        if($componentDetail[MaterielConfigConst::MTYPE] != self::pnToPartConfigRelation($mtype)) {
            throw new Exception('部件类型和物料类别不匹配');
        }
        
        $supplierDetail  = Supplier::getBySupplierId($supplierId);
        if(empty($supplierDetail)) {
            throw new Exception('供应商错误');
        }
        
        $pnNo = self::getPnNo($mtype,$componentDetail,$pn[PnConst::SERIAL_NO]);
        
        $pn = self::getByPnNo($pnNo,$id);
        if(!empty($pn)) {
            throw new Exception('工程PN已经存在');
        }
        $currTime = time();
        $pn = array(
            PnConst::PN_NO => $pnNo,
            PnConst::LEVEL => $level,
            PnConst::MTYPE => $mtype,
            PnConst::COUNTRY => $country,
            PnConst::NAME => $name,
            PnConst::COMPONENT => $component,
            PnConst::CONTENT => $content,
            PnConst::STYLE => $style,
            PnConst::PACKAGE_NAME => $packageName,
            PnConst::ACCURACY_NAME => $accuracyName,
            PnConst::SUPPLIER_ID => $supplierId,
            PnConst::PN_TYPE => $pnType,
            PnConst::NOTE => $note,
            PnConst::UPDATE_TIME => $currTime,
            PnConst::UPDATE_USER_ID => $updateUserId,
        );
        PnModel::beginTransaction();
        try {
            $pn = PnModel::where(PnConst::ID,$id)->update($pn);
            if (!$pn) {
                throw new Exception('保存PN失败');
            }
        } catch (Exception $e) {
            PnModel::rollBack();
            throw $e;
        }
        PnModel::commit();
        return $pn;
    }
    /**
     * 
     * @param type $pnNo
     * @param type $name
     * @param type $level
     * @param type $mtype
     * @param type $country
     * @param type $supplier
     * @param type $status
     * @param type $createUserId
     * @param type $createTimeStart
     * @param type $createTimeEnd
     * @param type $page
     * @param type $perPage
     * @return type
     */
    public static function getList($pnNo,$name,$level,$mtype,$country,$supplierId,$status,$createUserId,$createTimeStart
                    ,$createTimeEnd,$page,$perPage) {
        $builder = PnModel::orderBy(PnConst::ID, "desc"); 
        !empty($pnNo) && $builder->where(PnConst::PN_NO, $pnNo);
        !empty($name) && $builder->where(PnConst::NAME,'like', '%'.$name."%");
        !empty($level) && $builder->where(PnConst::LEVEL,$level);
        !empty($mtype) && $builder->where(PnConst::MTYPE, $mtype);
        !empty($country) && $builder->where(PnConst::COUNTRY, $country);
        !empty($supplierId) && $builder->where(PnConst::SUPPLIER_ID, $supplierId);
        !empty($status) && $builder->where(PnConst::STATUS, $status);
        !empty($createUserId) && $builder->where(PnConst::CREATE_USER_ID, $createUserId);
        !empty($createTimeStart) && $builder->where(PnConst::CREATE_TIME,">=",$createTimeStart);
        !empty($createTimeEnd) && $builder->where(PnConst::CREATE_TIME,"<=",$createTimeEnd);
        $count = $builder->count();
        $list = $builder->offset(($page-1)*$perPage)->take($perPage)->get()->toArray();
        $list = self::fillList($list);
        return array(
            "total"=>$count,
            "list"=>$list,
        );
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
        $ids = array_unique(array_column($list, PnConst::COMPONENT));
        $materielConfigs = MaterielConfig::getByIds($ids);
        $materielConfigs = array_column($materielConfigs, MaterielConfigConst::NAME, MaterielConfigConst::ID);
        $countryCodes = array_column($list, PnConst::COUNTRY);
        $countrys = Country::getByCodes($countryCodes);
        $countrys = array_column($countrys, CountryConst::NAME, CountryConst::CODE);
        $supplierIds = array_unique(array_column($list, PnConst::SUPPLIER_ID));
        $supplier = Supplier::listBySupplierIds($supplierIds);
        $supplier = array_column($supplier, SupplierConst::NAME, SupplierConst::SUPPLIER_ID);
        foreach ($list as $key => $val) {
            $val["component_name"] = isset($materielConfigs[$val[PnConst::COMPONENT]])?$materielConfigs[$val[PnConst::COMPONENT]]:'';
            $val["country_name"] = isset($countrys[$val[PnConst::COUNTRY]])?$countrys[$val[PnConst::COUNTRY]]:'';
            $val["mtype_name"] = isset(PnConst::$names[$val[PnConst::MTYPE]])?PnConst::$names[$val[PnConst::MTYPE]]:"";
            $val["good_supplier_name"] = isset($supplier[$val[PnConst::SUPPLIER_ID]])?$supplier[$val[PnConst::SUPPLIER_ID]]:'';
            $list[$key] = $val;
        }
        return $list;
    }
    /**
     * 
     * @param type $id
     * @return type
     * @throws Exception
     */
    public static function getDetail($id = 0) {
        $pn = self::getById($id);
        if (empty($pn)) {
            throw new Exception('工程PN不存在');
        }
        return self::fillDetail($pn);
    }
    /**
     * 
     * @param array $pn
     * @return type
     */
    private static function fillDetail( $pn) {
        $ids = array(
            $pn[PnConst::COMPONENT]
        );
        $materielConfigs = MaterielConfig::getByIds($ids);
        $materielConfigs = array_column($materielConfigs, MaterielConfigConst::NAME, MaterielConfigConst::ID);
        $country = Country::getByCode($pn[PnConst::COUNTRY]);
        $supplier = array();
        if(!empty($pn[PnConst::SUPPLIER_ID])) {
            $supplier = Supplier::getBySupplierId($pn[PnConst::SUPPLIER_ID]);
        }
        $pn["mtype_name"] = isset(PnConst::$names[$pn[PnConst::MTYPE]])?PnConst::$names[$pn[PnConst::MTYPE]]:"";
        $pn["country_name"] = isset($country[CountryConst::NAME])?$country[CountryConst::NAME]:"";
        $pn["component_name"] = isset($materielConfigs[$pn[PnConst::COMPONENT]])?$materielConfigs[$pn[PnConst::COMPONENT]]:''; 
        $pn["good_supplier_name"] = !empty($supplier)?$supplier[SupplierConst::NAME]:'';
        return $pn;
    }

    /**
     * 更新pn状态
     * @param type $id
     * @param type $status
     * @param type $updateUserId
     * @return boolean
     * @throws Exception
     */
    public static function updateStatus($id, $status, $updateUserId) {
        $pn = self::getById($id);
        if (empty($pn)) {
            throw new Exception('工程PN不存在');
        }
        if($pn[PnConst::IS_NOT_IMPORT] != PnConst::IMPORT_FALSE) {
            throw new Exception('导入数据禁止修改');
        }
        if ($pn[PnConst::STATUS] == $status) {
            throw new Exception('已经是当前状态无需更新');
        }
        $data = [
            PnConst::STATUS => $status,
            PnConst::UPDATE_TIME => time(),
            PnConst::UPDATE_USER_ID => $updateUserId,
        ];
        PnModel::beginTransaction();
        try {
            if (!PnModel::where(PnConst::ID, $id)->update($data)) {
                throw new Exception('更新Pn状态失败');
            }
        } catch (Exception $e) {
            PnModel::rollBack();
            throw $e;
        }
        PnModel::commit();
        return true;
    }
    /**
     * 物料类别
     * @param type $level
     * @return type
     */
    public static function materialType($level = 0,$pnType=0) {
        $result = array();
        if(empty($level) && empty($pnType)) {
            $types = PnConst::$names;
        } else {
            $types = self::listTypesByLevel($level,$pnType);
        }
        if (!empty($types)) {
            foreach ($types as $key => $val) {
                $result[] = array(
                    "code"=>$key,
                    "name"=>$val,
                );
            }
        }
        return $result;
    }

    /**
     * 
     * @param type $level
     * @return boolean|array
     */
    private static function listTypesByLevel($level = 0,$pnType=0) {
        $types = array(
            PnConst::COMMON_BIKE => array(
                PnConst::PN_LEVEL_SECOND => array(
                    PnConst::TYPE_DXDT_ONLY_BIKE => PnConst::$names[PnConst::TYPE_DXDT_ONLY_BIKE],
                    PnConst::TYPE_ORG_ONLY_BIKE => PnConst::$names[PnConst::TYPE_ORG_ONLY_BIKE],
                    PnConst::TYPE_DXDT_LOCK => PnConst::$names[PnConst::TYPE_DXDT_LOCK],
                    PnConst::TYPE_ORG_LOCK => PnConst::$names[PnConst::TYPE_ORG_LOCK],
                ),
                PnConst::PN_LEVEL_THIRD => array(
                    PnConst::TYPE_DXDT_BIKE_PARTS => PnConst::$names[PnConst::TYPE_DXDT_BIKE_PARTS],
                    PnConst::TYPE_ORG_BIKE_PARTS => PnConst::$names[PnConst::TYPE_ORG_BIKE_PARTS],
                    PnConst::TYPE_ELECTRIC_LOCK => PnConst::$names[PnConst::TYPE_ELECTRIC_LOCK],
                    PnConst::TYPE_STRUCT_LOCK => PnConst::$names[PnConst::TYPE_STRUCT_LOCK],
                ),
            ),
            PnConst::ELECTRONIC_BIKE => array(
                PnConst::PN_LEVEL_SECOND => array(
                    PnConst::TYPE_THREE_ELECTRIC => PnConst::$names[PnConst::TYPE_THREE_ELECTRIC],
                    PnConst::TYPE_ELECTRIC_MACHINE_LOCK => PnConst::$names[PnConst::TYPE_ELECTRIC_MACHINE_LOCK],
                    PnConst::TYPE_ELECTRIC_BIKE => PnConst::$names[PnConst::TYPE_ELECTRIC_BIKE],
                ),
                PnConst::PN_LEVEL_THIRD => array(
                    PnConst::TYPE_THREE_ELECTRIC_PARTS => PnConst::$names[PnConst::TYPE_THREE_ELECTRIC_PARTS],
                    PnConst::TYPE_ELECTRIC_MACHINE_LOCK_PARTS => PnConst::$names[PnConst::TYPE_ELECTRIC_MACHINE_LOCK_PARTS],
                    PnConst::TYPE_ELECTRIC_BIKE_PARTS => PnConst::$names[PnConst::TYPE_ELECTRIC_BIKE_PARTS],
                ),
            )
        );
        if (array_key_exists($pnType, $types)) {
            if(array_key_exists($level, $types[$pnType])) {
                return $types[$pnType][$level];
            }
        }
        return false;
    }
    /**
     * 根据PNno 获取工程PN
     * @param type $pnNo
     * @return type
     */
    public static function getByPnNo($pnNo = "", $exceptSkuId = null) {
        $build = PnModel::where(PnConst::PN_NO, $pnNo);
        if (!empty($exceptSkuId)) {
            $build->where(PnConst::ID, "!=", $exceptSkuId);
        }
        return $build->first();
    }

    /**
     * 根据ID获取工程PN
     * @param type $id
     * @return type
     */
    public static function getById($id=0) {
        return PnModel::where(PnConst::ID,$id)->first();
    }
    /**
     * 
     * @param type $id
     * @return type
     */
    public static function checkUsedConfig($id = 0) {
        return PnModel::where(PnConst::COMPONENT, $id)->get()->toArray();
    }
    
    public static function checkUsedConfigs($ids = 0) {
        return PnModel::whereIn(PnConst::COMPONENT, $ids)->get()->toArray();
    }
    /**
     * 
     * @param array $ids
     * @return type
     */
    public static function listByIds(array $ids) {
        $list =  PnModel::whereIn(PnConst::ID,$ids)->get()->toArray();
        return self::fillList($list);
    }
    
    
    /**
     * 
     * @param type $name
     * @param type $status
     * @return type
     */
    public static function getPnByLikeName($name, $status = null){
        $builder = PnModel::where(PnConst::NAME, "like","%".addslashes($name)."%");
        if(!is_null($status)){
            $builder->where(PnConst::STATUS, $status);
        }
        return $builder->get()->toArray();
    }
    /**
     * 
     * @param type $mtype
     */
    private static function pnToPartConfigRelation($mtype) {
        $relation = array(
            PnConst::TYPE_DXDT_ONLY_BIKE => MaterielConfigConst::DXDT_CAR_PARTS,
            PnConst::TYPE_ORG_ONLY_BIKE => MaterielConfigConst::ORG_CAR_PARTS,
            PnConst::TYPE_DXDT_LOCK => MaterielConfigConst::LOCK_PARTS,
            PnConst::TYPE_ORG_LOCK => MaterielConfigConst::LOCK_PARTS,
            PnConst::TYPE_THREE_ELECTRIC => MaterielConfigConst::ELECTRIC_BIKE_PARTS,
            PnConst::TYPE_ELECTRIC_MACHINE_LOCK => MaterielConfigConst::ELECTRIC_BIKE_PARTS,
            PnConst::TYPE_ELECTRIC_BIKE => MaterielConfigConst::ELECTRIC_BIKE_PARTS,
            PnConst::TYPE_DXDT_BIKE_PARTS => MaterielConfigConst::DXDT_CAR_PARTS,
            PnConst::TYPE_ORG_BIKE_PARTS => MaterielConfigConst::ORG_CAR_PARTS,
            PnConst::TYPE_THREE_ELECTRIC_PARTS => MaterielConfigConst::ELECTRIC_BIKE_PARTS,
            PnConst::TYPE_ELECTRIC_MACHINE_LOCK_PARTS => MaterielConfigConst::ELECTRIC_BIKE_PARTS,
            PnConst::TYPE_ELECTRIC_BIKE_PARTS => MaterielConfigConst::ELECTRIC_BIKE_PARTS,
            PnConst::TYPE_ELECTRIC_LOCK => MaterielConfigConst::LOCK_PARTS,
            PnConst::TYPE_STRUCT_LOCK => MaterielConfigConst::LOCK_PARTS,
        );
        if(array_key_exists($mtype, $relation)) {
            return $relation[$mtype];
        }
        return false;
    }
    /**
     * 
     * @param type $mtype
     * @param type $componentDetail
     * @param type $seriaNo
     * @return type
     */
    private static function getPnNo($mtype,$componentDetail,$seriaNo) {
        return PnConst::$codes[$mtype].$componentDetail[MaterielConfigConst::CODE].sprintf("%03d",$seriaNo);
    }
    /**
     * 
     * @param type $mtype
     * @param type $component
     * @return type
     */
    private static function getMaxSerialNo($mtype = 0, $component) {
        $serialNo = 0;
        $result = PnModel::where(PnConst::MTYPE, $mtype)
                ->where(PnConst::COMPONENT, $component)
                ->where(PnConst::IS_NOT_IMPORT, PnConst::IMPORT_FALSE)
                ->orderBy(PnConst::ID,"desc")->first();
        if (!empty($result)) {
            $serialNo = $result[PnConst::SERIAL_NO];
        }
        return intval($serialNo) + 1;
    }
    /**
     * 
     * @param type $mtype
     * @param type $component
     * @param type $createUserId
     * @return type
     */
    private static function getMyLastCreate($mtype, $component, $createUserId) {
        return PnModel::where(PnConst::MTYPE, $mtype)
                        ->where(PnConst::COMPONENT, $component)
                        ->where(PnConst::CREATE_USER_ID, $createUserId)
                        ->where(PnConst::CREATE_TIME, ">", time() - 5)
                        ->where(PnConst::IS_NOT_IMPORT, PnConst::IMPORT_FALSE)
                        ->orderBy(PnConst::ID, "desc")->first();
    }
}
