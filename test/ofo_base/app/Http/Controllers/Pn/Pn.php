<?php

namespace App\Http\Controllers\Pn;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\Pn as PnRepository;
use App\Constants\Db\Tables\Base\Pn as PnConst;
use Exception;

class Pn extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function create(Request $request){
        $this->validate($request, [
            'level' => 'required|in:' . implode(',', array_keys(PnConst::$level)),
            'mtype' => 'required|in:' . implode(',', array_keys(PnConst::$codes)),
            'country' => 'required|string|min:2|max:2',
            'name' => 'required|string|min:1|max:32',
            'component' => 'required|integer',
            'content' => 'string|min:1|max:200',
            'supplier_id' => 'required|string',
            'pn_type' => 'required|in:' . implode(',', array_keys(PnConst::$pnTypes)),
            'style' => 'required_if:mtype,' . PnConst::TYPE_DXDT_ONLY_BIKE . '|string',
            'package_name' => 'required_if:mtype,' . PnConst::TYPE_ELECTRIC_LOCK . '|string|min:1|max:16',
            'accuracy_name' => 'required_if:mtype,' . PnConst::TYPE_ELECTRIC_LOCK . '|string|min:1|max:16',
            'create_user_id' => 'required|integer',
            'note' => 'string|min:1|max:200',
        ],[
            'level.required' => '物料级别不能为空',
            'level.in' => '物料级别无效',
            'mtype.required' => '物料类别不能为空',
            'mtype.in' => '物料类别无效',
            'country.required' => '国家不能为空',
            'country.string' => '国家无效',
            'country.min' => '国家最小长度不能低于:min个字',
            'country.max' => '国家最大长度不能超过:max个字',
            'name.required' => '物料名称不能为空',
            'name.string' => '物料名称无效',
            'name.min' => '物料名称最小长度不能低于:min个字',
            'name.max' => '物料名称最大长度不能超过:max个字',
            'component.required' => '部件类型不能为空',
            'component.integer' => '部件类型无效',
            'content.string' => '物料描述无效',
            'content.min' => '物料描述最小长度不能低于:min个字',
            'content.max' => '物料描述最大长度不能超过:max个字',
            'supplier_id.required' => '供应商不能为空',
            'supplier_id.string' => '供应商无效',
            'pn_type.required' => '物料类型不能为空',
            'pn_type.in' => '物料类型无效',
            'style.required_if' => '款式不能为空',
            'style.string' => '款式无效',
            'package_name.required_if' => '封装不能为空',
            'package_name.min' => '封装最小长度不能低于:min个字',
            'package_name.max' => '封装最大长度不能超过:max个字',
            'accuracy_name.required_if' => '精度不能为空',
            'accuracy_name.min' => '精度最小长度不能低于:min个字',
            'accuracy_name.max' => '精度最大长度不能超过:max个字',
            'create_user_id.required' => '创建人不能为空',
            'create_user_id.integer' => '创建人无效',
            'note.string' => '备注无效',
            'note.min' => '备注最小长度不能低于:min个字',
            'note.max' => '备注最大长度不能超过:max个字',
        ]);
        $level = $request->input('level');
        $mtype = $request->input('mtype');
        $country = $request->input('country');
        $name    = trim($request->input("name"));
        $component = $request->input("component");
        $content  = trim($request->input("content"));
        $supplierId = $request->input("supplier_id");
        $pnType = $request->input("pn_type",1);
        $style    = trim($request->input("style",""));
        $packageName  = $request->input("package_name","");
        $accuracyName = $request->input("accuracy_name","");
        $createUserId = $request->input('create_user_id','');
        $note  = $request->input("note",''); 
        try{
            $pn = PnRepository::create($level,$mtype,$country,$name,$component,$content,$supplierId,
                    $pnType,$style,$packageName,$accuracyName,$createUserId,$note);
            return $this->jsonSuccess(['id' => $pn[PnConst::ID]]);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    public function update(Request $request){
        $this->validate($request, [
            'id'   => 'required|integer',
            'level' => 'required|in:' . implode(',', array_keys(PnConst::$level)),
            'mtype' => 'required|in:' . implode(',', array_keys(PnConst::$codes)),
            'country' => 'required|string|min:2|max:2',
            'name' => 'required|string|min:1|max:32',
            'component' => 'required|integer',
            'content' => 'string|min:1|max:200',
            'supplier_id' => 'required|string',
            'pn_type' => 'required|in:' . implode(',', array_keys(PnConst::$pnTypes)),
            'style' => 'required_if:mtype,' . PnConst::TYPE_DXDT_ONLY_BIKE . '|string',
            'package_name' => 'required_if:mtype,' . PnConst::TYPE_ELECTRIC_LOCK . '|string|min:1|max:16',
            'accuracy_name' => 'required_if:mtype,' . PnConst::TYPE_ELECTRIC_LOCK . '|string|min:1|max:16',
            'note' => 'string|min:1|max:200',
            'update_user_id' => 'required|integer',
        ],[
            'id.required' => 'ID不能为空',
            'id.integer' => 'ID无效',
            'level.required' => '物料级别不能为空',
            'level.in' => '物料级别无效',
            'mtype.required' => '物料类别不能为空',
            'mtype.in' => '物料类别无效',
            'country.required' => '国家不能为空',
            'country.string' => '国家无效',
            'country.min' => '国家最小长度不能低于:min个字',
            'country.max' => '国家最大长度不能超过:max个字',
            'name.required' => '物料名称不能为空',
            'name.string' => '物料名称无效',
            'name.min' => '物料名称最小长度不能低于:min个字',
            'name.max' => '物料名称最大长度不能超过:max个字',
            'component.required' => '部件类型不能为空',
            'component.integer' => '部件类型无效',
            'content.string' => '物料描述无效',
            'content.min' => '物料描述最小长度不能低于:min个字',
            'content.max' => '物料描述最大长度不能超过:max个字',
            'supplier_id.required' => '供应商不能为空',
            'supplier_id.string' => '供应商无效',
            'pn_type.required' => '物料类型不能为空',
            'pn_type.in' => '物料类型无效',
            'style.required_if' => '款式不能为空',
            'style.string' => '款式无效',
            'package_name.required_if' => '封装不能为空',
            'package_name.min' => '封装最小长度不能低于:min个字',
            'package_name.max' => '封装最大长度不能超过:max个字',
            'accuracy_name.required_if' => '精度不能为空',
            'accuracy_name.min' => '精度最小长度不能低于:min个字',
            'accuracy_name.max' => '精度最大长度不能超过:max个字',
            'note.string' => '备注无效',
            'note.min' => '备注最小长度不能低于:min个字',
            'note.max' => '备注最大长度不能超过:max个字',
            'update_user_id.required' => '修改人不能为空',
            'update_user_id.integer' => '修改人无效',
        ]);
        $id    = $request->input("id");
        $level = $request->input('level');
        $mtype = $request->input('mtype');
        $country = $request->input('country');
        $name    = trim($request->input("name"));
        $component = $request->input("component");
        $content  = trim($request->input("content"));
        $supplierId = $request->input("supplier_id");
        $pnType = $request->input("pn_type",1);
        $style    = trim($request->input("style",""));
        $packageName  = $request->input("package_name",'');
        $accuracyName = $request->input("accuracy_name",'');
        $note  = $request->input("note",''); 
        $updateUserId = $request->input('update_user_id','');
        try{
            $pn = PnRepository::update($id,$level,$mtype,$country,$name,$component,$content,$supplierId,
                    $pnType,$style,$packageName,$accuracyName,$note,$updateUserId);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getList(Request $request) {
        $this->validate($request, [
            'pn_no' => 'sometimes|string',
            'name' => 'sometimes|string',
            'level' => 'sometimes|in:' . implode(',', array_keys(PnConst::$level)),
            'mtype' => 'sometimes|in:' . implode(',', array_keys(PnConst::$codes)),
            'country' => 'sometimes|string',
            'supplier_id' => 'sometimes|string',
            'status' => 'sometimes|in:' . implode(",", array_keys(PnConst::$status)),
            'create_user_id' => 'sometimes|integer',
            'create_time_start' => 'sometimes|integer',
            'create_time_end' => 'sometimes|integer',
            'page' => 'integer',
            'perpage' => 'integer',
                ], [
            'pn_no.string' => 'pn_no无效',
            'name.string' => '物料名称无效',
            'level.in' => '物料级别无效',
            'mtype.in' => '物料类型无效',
            'country.string' => '国家无效',
            'supplier_id.string' => '供应商无效',
            'status.in' => '状态无效',
            'create_user_id.integer' => '创建人无效',
            'create_time_start.integer' => '开始时间无效',
            'create_time_end.integer' => '结束时间无效',
            'page.integer' => '页数无效',
            'perpage.integer' => '页数大小无效',
        ]);
        $pnNo = $request->input("pn_no", '');
        $name = trim($request->input("name", ""));
        $level = $request->input("level", 0);
        $mtype = $request->input("mtype", 0);
        $country = $request->input("country", '');
        $supplierId = $request->input("supplier_id", 0);
        $status = $request->input("status", 0);
        $createUserId = $request->input("create_user_id", 0);
        $createTimeStart = $request->input("create_time_start", 0);
        $createTimeEnd = $request->input("create_time_end", 0);
        $page = $request->input("page", 1);
        $perPage = $request->input("perpage", 50);
        try {
            $list = PnRepository::getList($pnNo, $name, $level, $mtype, $country, $supplierId, $status, $createUserId, $createTimeStart
                            , $createTimeEnd, $page, $perPage);
            return $this->jsonSuccess($list);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getById(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
                ], [
            'id.required' => ':attribute 不能为空',
            'id.integer' => ':attribute 必须为整数',
        ]);
        $id = $request->input('id', 0);
        try {
            $pn = PnRepository::getDetail($id);
            return $this->jsonSuccess($pn);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function listByIds(Request $request) {
        $this->validate($request, [
            'ids' => 'required|json',
                ], [
            'ids.required' => ':attribute 不能为空',
            'ids.json' => ':attribute 不合法',
        ]);
        $ids = Util::unserializeParams($request->input("ids"));
        try {
            $pns = PnRepository::listByIds($ids);
            return $this->jsonSuccess($pns);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * 
     * @param Request $request
     * @return type
     */
    public function updateStatus(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
            'update_user_id' => 'required|integer',
            'status' => 'required|in:' . implode(',', array_keys(PnConst::$status)),
                ], [
            'id.required' => ':attribute 不能为空',
            'id.integer' => ':attribute 必须为整数',
            'update_user_id.required' => ':attribute 不能为空',
            'update_user_id.integer' => ':attribute 必须为整数',
            'status.required' => ':attribute 不能为空',
            'status.in' => ':attribute 不合法',
        ]);
        $id = $request->input('id', 0);
        $status = $request->input('status', 0);
        $updateUserId = $request->input('update_user_id', 0);
        try {
            PnRepository::updateStatus($id, $status, $updateUserId);
            return $this->jsonSuccess();
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 获取物料类别
     * @param Request $request
     * @return type
     */
    public function materialType(Request $request) {
        $this->validate($request, [
            'level' => 'sometimes|in:' . implode(',', array_keys(PnConst::$level)),
            'pn_type' => 'sometimes|in:' . implode(',', array_keys(PnConst::$pnTypes)),
                ], [
            'level.in' => '物料级别无效',
            'pn_type.in' => '物料类型无效',
        ]);
        $level = $request->input('level', 0);
        $pnType = $request->input('pn_type', 0);
        try {
            $list = PnRepository::materialType($level,$pnType);
            return $this->jsonSuccess($list);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

}