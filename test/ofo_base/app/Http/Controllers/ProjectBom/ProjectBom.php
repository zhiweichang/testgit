<?php

namespace App\Http\Controllers\ProjectBom;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\ProjectBom as ProjectBomRepository;
use App\Constants\Db\Tables\Base\ProjectBom as ProjectBomConst;
use App\Constants\Db\Tables\Base\Pn as PnConst;
use Exception;

class ProjectBom extends Controller {

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function create(Request $request) {
        $this->validate($request, [
            'bom_name' => 'required|string|min:1|max:64',
            'pn_id' => 'required|integer',
            'create_user_id' => 'required|integer',
            'detail' => 'required|json',
                ], [
            'bom_name.required' => 'BOM名称不能为空',
            'bom_name.string' => 'BOM名称无效',
            'bom_name.min' => 'BOM名称最小长度不能低于:min个字',
            'bom_name.max' => 'BOM名称最大长度不能超过:max个字',
            'pn_id.required' => ':attribute 不能为空',
            'pn_id.integer' => ':attribute 无效',
            'create_user_id.required' => ':attribute 不能为空',
            'create_user_id.integer' => ':attribute 必须为整数',
            'detail.required' => ':attribute 不能为空',
            'detail.json' => ':attribute 必须为JSON格式',
        ]);

        $pnId = $request->input('pn_id');
        $createUserId = $request->input('create_user_id');
        $detail = Util::unserializeParams($request->input('detail'));
        $bomName = $request->input("bom_name");
        try {
            $bomId = ProjectBomRepository::create($pnId, $createUserId, $detail, $bomName);
            return $this->jsonSuccess($bomId);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
            'bom_name' => 'required|string|min:1|max:64',
            'update_user_id' => 'required|integer',
                ], [
            'id.required' => ':attribute 不能为空',
            'id.integer' => ':attribute 无效',
            'bom_name.required' => 'BOM名称不能为空',
            'bom_name.string' => 'BOM名称无效',
            'bom_name.min' => 'BOM名称最小长度不能低于:min个字',
            'bom_name.max' => 'BOM名称最大长度不能超过:max个字',
            'update_user_id.required' => '创建人不能为空',
            'update_user_id.integer' => '创建人无效',
        ]);

        $id = $request->input('id');
        $bomName = $request->input("bom_name");
        $updateUserId = $request->input("update_user_id");
        try {
            $bomId = ProjectBomRepository::update($id,$bomName,$updateUserId);
            return $this->jsonSuccess($bomId);
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
            'status' => 'required|in:' . implode(',', array_keys(ProjectBomConst::$status)),
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
            ProjectBomRepository::updateStatus($id, $status, $updateUserId);
            return $this->jsonSuccess();
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getByBomId(Request $request){
        $this->validate($request, [
            'id' => 'required|integer',
        ]);
        $bomId = $request->input('id', '');
        try{
            $bom = ProjectBomRepository::getBomById($bomId);
            return $this->jsonSuccess($bom);
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
            'id' => 'integer',
            'name' => 'string',
            'father_pn_no' => 'string',
            'father_pn_name' => 'string',
            'son_pn_no' => 'string',
            'son_pn_name' => 'string',
            'create_user_id' => 'integer',
            'create_time_start' => 'integer',
            'create_time_end' => 'integer',
            'status' => 'sometimes|in:' . implode(',', array_keys(PnConst::$status)),
            'page' => 'integer',
            'perpage' => 'integer',
            
        ],[
            'id.integer' => ':attribute 为空或者为数字',
            'name.string' => ':attribute 为空或者为字符串',
            'father_pn_id.string' => ':attribute 为空或者为字符串',
            'father_pn_name.string' => ':attribute 为空或者为字符串',
            'son_pn_id.string' => ':attribute 为空或者为字符串',
            'son_pn_name.string' => ':attribute 为空或者为字符串',
            'create_user_id.integer' => ':attribute 为空或者为数字',
            'create_time_start.integer' => ':attribute 为空或者为时间戳',
            'create_time_end.integer' => ':attribute 为空或者为时间戳',
            'status.in' => ':attribute 不合法',
            'page' => ':attribute 为空或者为数字',
            'perpage' => ':attribute 为空或者为数字',
        ]);
        $bomId = $request->input("id",null);
        $bomName = $request->input("name",null);
        $fatherPnId = $request->input('father_pn_no',null);
        $fatherPnName = $request->input('father_pn_name',null);
        $sonPnId = $request->input('son_pn_no',null);
        $sonPnName = $request->input('son_pn_name',null);
        $createUserId = $request->input('create_user_id',null);
        $createTimeStart = $request->input('create_time_start',null);
        $createTimeEnd = $request->input('create_time_end',null);
        $page = $request->input('page',1);
        $perPage = $request->input('perpage',50);
        $status = $request->input('status',null);
        try{
            $boms = ProjectBomRepository::getList($bomId,$bomName,$fatherPnId, $fatherPnName,$sonPnId,$sonPnName
                    ,$createUserId,$createTimeStart,$createTimeEnd,$page,$perPage,$status);
            return $this->jsonSuccess($boms);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

}
