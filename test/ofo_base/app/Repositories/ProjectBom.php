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
use Carbon\Carbon;
use Exception;
use App\Models\ProjectBom\ProjectBom as ProjectBomModel;
use App\Models\ProjectBom\ProjectBomDetail as ProjectBomDetailModel;
use App\Models\Pn\Pn as PnModel;
use App\Constants\Db\Tables\Base\ProjectBom as ProjectBomConst;
use App\Constants\Db\Tables\Base\ProjectBomDetail as ProjectBomDetailConst;
use App\Constants\Db\Tables\Base\Pn as PnConst;
use App\Constants\Db\Tables\Base\MaterielConfig as MaterielConfigConst;



use Illuminate\Support\Facades\DB;

class ProjectBom{
    /**
     * 
     * @param type $pnId
     * @param array $detail
     * @throws Exception
     */
    private static function basicCheck($pnId, array $detail){
        $pnIds = [];
        foreach($detail as $idx=>$pn){
            $_pnId = array_get($pn, 'pn_id');
            if(empty($_pnId)){
                throw new Exception('子工程PnId不能为空');
            }
            if($_pnId == $pnId){
                throw new Exception('存在与父物料编号相同的子物料');
            }
            $_num = array_get($pn, 'num', 0);
            if(!is_integer($_num) || $_num <= 0){
                throw new Exception('数量传递错误');
            }
            if(in_array($_pnId, $pnIds)){
                throw new Exception("子物料{$_pnId}存在相同行");
            }
            $pnIds[] = $_pnId;
        }
        $pnIds[] = $pnId;
        $dbPnList = Pn::listByIds($pnIds);
        $dbPnIds = array_column($dbPnList, PnConst::ID);
        $diffPnIds = array_diff($pnIds, $dbPnIds);
        if(!empty($diffPnIds)){
            throw new Exception('物料' . implode('、', $diffPnIds) . '不存在或无效');
        }
    }

    /**
     * 
     * @param type $detail
     * @return boolean
     * @throws Exception
     */
    public static function duplicateCheck($bomName,$detail){
        if($bom = self::getBomByName($bomName)) {
            throw new Exception("该BOM名称已存在，请重新设置");
        }
        $pnNumMap = array();
        foreach($detail as $item ) {
            $pnNumMap[$item['pn_id']] = intval($item['num']);
        }
        $bomDetail = self::getBomDetailByPnIds(array_keys($pnNumMap));
        if(empty($bomDetail)) {
            return true;
        }
        $bomDetail = self::getDetailByIds(array_column($bomDetail, ProjectBomDetailConst::BOM_ID));
        if(empty($bomDetail)) {
            return true;
        }
        $bomDetailByBomId = array();
        foreach ($bomDetail as $item) {
            $bomDetailByBomId[$item[ProjectBomDetailConst::BOM_ID]][$item[ProjectBomDetailConst::PN_ID]] = $item[ProjectBomDetailConst::NUM];
        }
        foreach($bomDetailByBomId as $k=>$item ) {
            
            
            if(count($item) != count($pnNumMap)) {
                continue;
            } 
            if(empty(array_diff_assoc($item,$pnNumMap))) {
                throw new Exception("已经存在与该子物料组成方式相同的BOM：{$k}");
            }
        }
        return true;
    }
    /**
     * 
     * @param array $pnIds
     * @return type
     */
    private static function getBomDetailByPnIds(array $pnIds) {
        return ProjectBomDetailModel::whereIn(ProjectBomDetailConst::PN_ID,$pnIds)->get()->toArray();
    }

    /**
     * 
     * @param type $pnId
     * @param type $createUserId
     * @param array $detail
     * @param type $bomName
     * @return type
     * @throws Exception
     */
    public static function create($pnId, $createUserId, array $detail,$bomName){
        self::basicCheck($pnId, $detail);
        self::duplicateCheck($bomName,$detail);
        if(!$pn = Pn::getById($pnId)) {
            throw new Exception('父PN不存在');
        }
        ProjectBomModel::beginTransaction();
        try{
            $bom = self::createBasic($pnId, $createUserId,$bomName);
            $bomId = $bom[ProjectBomConst::ID];
            self::createDetail($bomId, $detail);
        }catch(Exception $e){
            ProjectBomModel::rollBack();
            throw $e;
        }
        ProjectBomModel::commit();
        return $bomId;
    }
    
    /**
     * 
     * @param type $id
     * @param type $bomName
     * @param type $updateUserId
     * @return type
     * @throws Exception
     */
    public static function update($id, $bomName, $updateUserId) {
        $bom = self::getBasicById($id);
        if (empty($bom)) {
            throw new Exception('BOM不存在');
        }
        if ($bom = self::getBomByName($bomName, $id)) {
            throw new Exception("bom名称重复");
        }
        $currTime = time();
        $data = [
            ProjectBomConst::BOM_NAME => $bomName,
            ProjectBomConst::UPDATE_TIME => $currTime,
            ProjectBomConst::UPDATE_USER_ID => $updateUserId,
        ];

        ProjectBomModel::beginTransaction();

        try {
            if (!ProjectBomModel::where(ProjectBomConst::ID, $id)->update($data)) {
                throw new Exception('更新BOM名称失败');
            }
        } catch (Exception $e) {
            ProjectBomModel::rollBack();
            throw $e;
        }
        ProjectBomModel::commit();
        return $id;
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
        $bom = self::getBasicById($id);
        if (empty($bom)) {
            throw new Exception('工程BOM不存在');
        }
        if ($bom[ProjectBomConst::STATUS] == $status) {
            throw new Exception('已经是当前状态无需更新');
        }
        $data = [
            ProjectBomConst::STATUS => $status,
            ProjectBomConst::UPDATE_TIME => time(),
            ProjectBomConst::UPDATE_USER_ID => $updateUserId,
        ];
        ProjectBomModel::beginTransaction();
        try {
            if (!ProjectBomModel::where(ProjectBomConst::ID, $id)->update($data)) {
                throw new Exception('更新Pn状态失败');
            }
        } catch (Exception $e) {
            ProjectBomModel::rollBack();
            throw $e;
        }
        ProjectBomModel::commit();
        return true;
    }
    /**
     * 
     * @param type $bomId
     * @return type
     * @throws Exception
     */
    public static function getBomById($bomId) {
        
        $bom = self::getBasicById($bomId);
        if(empty($bom)) {
            throw new Exception('BOM信息不存在');
        }
        $bom["detail"] = self::getDetailById($bomId);
        return self::fillBomDetail($bom);
    }
    /**
     * 
     * @param type $bomId
     * @return type
     */
    private static function getDetailById($bomId) {
        return ProjectBomDetailModel::where(ProjectBomDetailConst::BOM_ID,$bomId)->get()->toArray();
    }
    
    /**
     * 
     * @param type $bomIds
     * @return type
     */
    private static function getDetailByIds($bomIds) {
        return ProjectBomDetailModel::whereIn(ProjectBomDetailConst::BOM_ID,$bomIds)->get()->toArray();
    }

    /**
     * 
     * @param type $bom
     * @return type
     */
    private static function fillBomDetail($bom) {
        $pnIds = array_merge(array($bom[ProjectBomConst::PN_ID]), array_column($bom["detail"], ProjectBomDetailConst::PN_ID));
        $pnDetail = self::getBomPnDetail($pnIds);
        $pn = isset($pnDetail[$bom[ProjectBomConst::PN_ID]])?$pnDetail[$bom[ProjectBomConst::PN_ID]]:array();
        $bom = $bom+$pn;
       
        $detail = $bom["detail"];
        foreach($detail as $k=>$item) {
            $pn = isset($pnDetail[$item[ProjectBomDetailConst::PN_ID]])?$pnDetail[$item[ProjectBomDetailConst::PN_ID]]:array();
            $detail[$k] = $pn+$item;
        }
        $bom["detail"] =$detail;
        return $bom;
    }
    /**
     * 
     * @param type $bomId
     * @param type $bomName
     * @param type $fatherPnId
     * @param type $fatherPnName
     * @param type $sonPnId
     * @param type $sonPnName
     * @param type $createUserId
     * @param type $createTimeStart
     * @param type $createTimeEnd
     * @param type $page
     * @param type $perPage
     * @param type $status
     * @return type
     */
    public static function getList($bomId,$bomName,$fatherPnId, $fatherPnName,$sonPnId,$sonPnName
                    ,$createUserId,$createTimeStart,$createTimeEnd,$page,$perPage,$status){
        $bomIds = null;
        $pnIds   = null;
        if(!empty($fatherPnName) || !empty($fatherPnId)) {
            $pnIds = self::getPnIdsByIdAndName($fatherPnName,$fatherPnId);
        }
        if(!empty($sonPnId) || !empty($sonPnName)) {
            $bomIds = self::getBomIdsByDetail($sonPnName,$sonPnId);
        }
        return  self::getBomListAndCount($bomId,$bomName,$pnIds,$bomIds,$createUserId,$createTimeStart,
                $createTimeEnd,$page,$perPage,$status);
    }
    /**
     * 拼接sql 查询bom信息
     * @param type $skuIds
     * @param type $bomIds
     * @param type $createUserId
     * @param type $createTimeStart
     * @param type $createTimeEnd
     * @param type $orgId
     * @param type $page
     * @param type $perPage
     * @param type $status
     * @return array
     */
    public static function getBomListAndCount($bomId,$bomName,$pnIds,$bomIds,$createUserId,$createTimeStart,
                $createTimeEnd,$page,$perPage,$status){
        $builder = ProjectBomModel::orderBy(ProjectBomConst::ID, "desc");
        $return  = array("total"=>0,"list"=>[]);
        if(is_array($bomIds)) {
            if(!empty($bomIds)) {
                $builder->whereIn(ProjectBomConst::ID, $bomIds);
            } else {
                return $return;
            }
        }
        if(is_array($pnIds)) {
            if(!empty($pnIds)) {
                $builder->whereIn(ProjectBomConst::PN_ID, $pnIds);
            } else {
                return $return;
            }
        }
        !empty($bomId) && $builder->where(ProjectBomConst::ID, $bomId);
        !empty($bomName) && $builder->where(ProjectBomConst::BOM_NAME, "like", "%".$bomName."%");
        !empty($createUserId) && $builder->where(ProjectBomConst::CREATE_USER_ID, $createUserId);
        !empty($createTimeStart) && $builder->where(ProjectBomConst::CREATE_TIME, ">=",$createTimeStart);
        !empty($createTimeEnd) && $builder->where(ProjectBomConst::CREATE_TIME, "<=",$createTimeEnd);
        !empty($status) && $builder->where(ProjectBomConst::STATUS, $status);
        $return["total"] = $builder->count();
        $list = $builder->offset(($page-1)*$perPage)->take($perPage)->get()->toArray();
        if(!empty($list)) {
            $detail = self::getBomDetailByBomIds(array_column($list, ProjectBomConst::ID));
            foreach($list as $k=>$v ) {
                $list[$k]["detail"] = isset($detail[$v[ProjectBomConst::ID]])?$detail[$v[ProjectBomConst::ID]]:array();
            }
        }
        $list = self::fillList($list);
        $return["list"] = $list;
        return $return;
    }
    /**
     * 
     * @param array $list
     * @return type
     */
    private static function fillList(array $list) {
        if(empty($list)) 
            return array();
        $pnDetail = self::getBomPnDetail(array_column($list, ProjectBomConst::PN_ID));
        foreach($list as $key=>$val) {
            $pn = isset($pnDetail[$val[ProjectBomConst::PN_ID]])?$pnDetail[$val[ProjectBomConst::PN_ID]]:array();
            $list[$key] = $val+$pn;
        }
        return $list;
        
    }

    /**
     * 
     * @param type $sonSkuName
     * @param type $sonSkuId
     * @return array
     */
    private static function getPnIdsByIdAndName($fatherPnName, $fatherPnId) {
        $pnIds = array();
        if (!empty($fatherPnName)) {
            $pns = Pn::getPnByLikeName($fatherPnName);
            $pnIds = array_column($pns, PnConst::ID);
            if (!empty($fatherPnId)) {
                $pn = Pn::getByPnNo($fatherPnId);
                if (!empty($pn)) {
                    $fatherPnId = $pn[PnConst::ID];
                    $pnIds = array_intersect($pnIds, array($fatherPnId));
                }
            }
        } elseif (!empty($fatherPnId)) {
            $pn = Pn::getByPnNo($fatherPnId);
            if (!empty($pn)) {
                $pnIds[] = $pn[PnConst::ID];
            }
        }
        return $pnIds;
    }

    /**
     * 
     * @param type $sonPnName
     * @param type $sonPnId
     * @return type
     */
    private static function getBomIdsByDetail($sonPnName, $sonPnId) {
        $bomIds = array();
        $pnIds = array();
        if (!empty($sonPnName)) {
            $pn = Pn::getPnByLikeName($sonPnName);
            $pnIds = array_column($pn, PnConst::ID);
            if (!empty($sonPnId)) {
                $pn = Pn::getByPnNo($sonPnId);
                if (!empty($pn)) {
                    $sonPnId = $pn[PnConst::ID];
                    $pnIds = array_intersect($pnIds, array($sonPnId));
                }
            }
        } elseif (!empty($sonPnId)) {
            $pn = Pn::getByPnNo($sonPnId);
            if (!empty($pn)) {
                $sonPnId = $pn[PnConst::ID];
                $pnIds[] = $sonPnId;
            }
        }
        if (!empty($pnIds)) {
            $bomDetail = self::getBomDetailByPnIds($pnIds);
            $bomIds = array_column($bomDetail, ProjectBomDetailConst::BOM_ID);
        }
        return $bomIds;
    }

    /**
     * @param $bomIds
     * @return array
     */
    private static function getBomDetailByBomIds($bomIds){
        $items = ProjectBomDetailModel::whereIn(ProjectBomDetailConst::BOM_ID, $bomIds)->get()->toArray();
        $map = [];
        if(!empty($items)){
            $pnDetail = self::getBomPnDetail(array_column($items, ProjectBomDetailConst::PN_ID));
            foreach ($items as $item){
                $pn = isset($pnDetail[$item[ProjectBomDetailConst::PN_ID]])?$pnDetail[$item[ProjectBomDetailConst::PN_ID]]:array();
                $map[$item[ProjectBomDetailConst::BOM_ID]][] = $item+$pn;
            }
        }
        return $map;
    }
    /**
     * 
     * @param type $pnIds
     * @return array
     */
    private static function getBomPnDetail($pnIds = array()) {
        if(empty($pnIds)) {
            return array();
        }
        $pns = Pn::listByIds($pnIds);
        $materielConfigs = MaterielConfig::getByIds(array_column($pns, PnConst::COMPONENT));
        $materielConfigs = array_column($materielConfigs, MaterielConfigConst::NAME, MaterielConfigConst::ID);
        $result = array();
        foreach($pns as $key=>$val ) {
            $result[$val[PnConst::ID]] = array(
                PnConst::ID=> $val[PnConst::ID],
                PnConst::PN_NO => $val[PnConst::PN_NO],
                PnConst::NAME => $val[PnConst::NAME],
                PnConst::MTYPE => $val[PnConst::MTYPE],
                "mtype_name" => PnConst::$names[$val[PnConst::MTYPE]],
                PnConst::COMPONENT => $val[PnConst::COMPONENT],
                PnConst::COMPONENT."_name" => isset($materielConfigs[$val[PnConst::COMPONENT]])?$materielConfigs[$val[PnConst::COMPONENT]]:"",
            );
        }
        return $result;
    }

    /**
     * 
     * @param type $pnId
     * @param type $createUserId
     * @param type $bomName
     * @return type
     * @throws Exception
     */
    private static function createBasic($pnId, $createUserId, $bomName) {
        $currTime = time();
        $bom = ProjectBomModel::create([
                    ProjectBomConst::PN_ID => $pnId,
                    ProjectBomConst::BOM_NAME => $bomName,
                    ProjectBomConst::STATUS => ProjectBomConst::STATUS_ENABLED,
                    ProjectBomConst::CREATE_USER_ID => $createUserId,
                    ProjectBomConst::UPDATE_USER_ID => $createUserId,
                    ProjectBomConst::CREATE_TIME => $currTime,
                    ProjectBomConst::UPDATE_TIME => $currTime,
        ]);
        $bomId = $bom[ProjectBomConst::ID] ?? null;
        if (empty($bomId)) {
            throw new Exception('主物料保存失败');
        }
        return $bom;
    }

    /**
     * 
     * @param type $bomId
     * @param type $detail
     * @return type
     * @throws Exception
     */
    private static function createDetail($bomId, $detail) {

        $rows = [];
        foreach ($detail as $item) {
            $rows[] = [
                ProjectBomDetailConst::BOM_ID => $bomId,
                ProjectBomDetailConst::PN_ID => $item['pn_id'],
                ProjectBomDetailConst::NUM => $item['num'],
            ];
        }
        if (!ProjectBomDetailModel::insert($rows)) {
            throw new Exception('子物料保存失败');
        }
        return $rows;
    }
    /**
     * 
     * @param type $name
     * @return type
     */
    public static function getBomByName($name = '', $exceptId = null) {
        $build = ProjectBomModel::where(ProjectBomConst::BOM_NAME, $name);
        if (!empty($exceptId)) {
            $build->where(ProjectBomConst::ID, "!=", $exceptId);
        }
        return $build->first();
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public static function getBasicById($id = 0) {
        return ProjectBomModel::where(ProjectBomConst::ID,$id)->first()->toArray();
    }
    
}
