<?php

namespace App\Http\Controllers\Bom;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\Bom as BomRepository;
use App\Constants\Db\Tables\Base\Bom as BomConst;
use Exception;

class Bom extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function create(Request $request){
        $this->validate($request, [
            'sku_id' => 'required',
            'user_id' => 'required|integer',
            'detail' => 'required|json',
            'bom_name' => 'required|string|min:1|max:64',
        ],[
            'sku_id.required' =>trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"SKU_ID")),
            'user_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"USER_ID")),
            'user_id.integer' => trans('message.ATTRIBUTE_NEED_INTEGER',array("attribute"=>"USER_ID")),
            'sku_list.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"SKU_LIST")),
            'sku_list.json' => trans('message.ATTRIBUTE_NEED_JSON',array("attribute"=>"SKU_LIST")),
            'bom_name.required' => trans('message.BOM_NAME').trans('message.NOT_EMPTY'),
            'bom_name.string' => trans('message.BOM_NAME').trans('message.INVALID'),
            'bom_name.min' => trans('message.BOM_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'bom_name.max' => trans('message.BOM_NAME').trans('message.NEED_MORE_THAN',array("min"=>"64")),
        ]);
        $skuId = $request->input('sku_id');
        $userId = $request->input('user_id');
        $detail = Util::unserializeParams($request->input('detail',''));
        $bomName = $request->input("bom_name",'');
        try{
            $bomId = BomRepository::create($skuId, $userId, $detail,$bomName);
            return $this->jsonSuccess($bomId);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function updateStatus(Request $request){
        $this->validate($request, [
            'bom_id' => 'required',
            'user_id' => 'required|integer',
            'status' => 'required|in:' . implode(',', array_keys(BomConst::$status)),
        ],[
            'bom_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"BOM_ID")),
            'user_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"USER_ID")),
            'user_id.integer' => trans('message.ATTRIBUTE_NEED_INTEGER',array("attribute"=>"USER_ID")),
            'status.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"STATUS")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
        ]);
        $bomId = $request->input('bom_id','');
        $status = $request->input('status',0);
        $userId = $request->input('user_id',0);
        try{
            BomRepository::updateStatus($bomId, $status, $userId);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * 更新发料方式
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function update(Request $request){
        $this->validate($request, [
            'bom_id' => 'required',
            'user_id' => 'required|integer',
            'detail' => 'required|json',
            'bom_name' => 'required|string|min:1|max:64',
        ],[
            'bom_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"BOM_ID")),
            'user_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"USER_ID")),
            'user_id.integer' => trans('message.ATTRIBUTE_NEED_INTEGER',array("attribute"=>"USER_ID")),
            'detail.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"SKU_LIST")),
            'detail.json' => trans('message.ATTRIBUTE_NEED_JSON',array("attribute"=>"SKU_LIST")),
            'bom_name.required' => trans('message.BOM_NAME').trans('message.NOT_EMPTY'),
            'bom_name.string' => trans('message.BOM_NAME').trans('message.INVALID'),
            'bom_name.min' => trans('message.BOM_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'bom_name.max' => trans('message.BOM_NAME').trans('message.NEED_MORE_THAN',array("min"=>"64")),
        ]);
        $bomId = $request->input('bom_id');
        $userId = $request->input('user_id');
        $detail = Util::unserializeParams($request->input('detail',''));
        $bomName = $request->input("bom_name",'');
        try{
            BomRepository::update($bomId, $userId, $detail,$bomName);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getByBomId(Request $request){
        $this->validate($request, [
            'bom_id' => 'sometimes',
            'sku_id' => 'sometimes',
            'status' => 'sometimes|in:' . implode(',', array_keys(BomConst::$status)),
            'org_id' => 'sometimes|in:' . implode(',', array_keys(BomConst::$org)),
        ]);
        $bomId = $request->input('bom_id', '');
        $skuId = $request->input('sku_id', '');
        $status = $request->input('status', null);
        $orgId = $request->input('org_id',null);
        try{
            $bom = BomRepository::getByBomIdOrSkuId($bomId, $skuId, $status,$orgId);
            return $this->jsonSuccess($bom);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse|\App\Http\Responses\MobileJsonResponse
     */
    public function listByBomIds(Request $request){
        $this->validate($request, [
            'bom_ids' => 'required|json',
            'status' => 'sometimes|in:' . implode(',', array_keys(BomConst::$status)),
            'org_id' => 'sometimes|in:' . implode(',', array_keys(BomConst::$org)),
        ],[
            'bom_ids.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"BOM_IDS")),
            'bom_ids.json' => trans('message.ATTRIBUTE_NEED_JSON',array("attribute"=>"BOM_IDS")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
        ]);
        $bomIds = $request->input('bom_ids','');
        $bomIds = Util::unserializeParams($bomIds);
        $status = $request->input('status',null);
        $orgId = $request->input('org_id',null);
        try{
            $skus = BomRepository::listByBomIds($bomIds, $status,$orgId);
            return $this->jsonSuccess($skus);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    /**
     * 供应链管理后台查询
     * @param Request $request
     * @return type
     */
    public function getList(Request $request) {
        $this->validate($request, [
            'id' => 'integer',
            'father_sku_id' => 'string',
            'father_sku_name' => 'string',
            'son_sku_id' => 'string',
            'son_sku_name' => 'string',
            'create_user_id' => 'integer',
            'create_time_start' => 'integer',
            'create_time_end' => 'integer',
            'status' => 'sometimes|in:' . implode(',', array_keys(BomConst::$status)),
            'page' => 'integer',
            'perpage' => 'integer',
            'org_id' => 'sometimes|in:' . implode(',', array_keys(BomConst::$org)),
        ],[
            'id.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"id")),
            'father_sku_id.string' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_STRING',array("attribute"=>"father_sku_id")),
            'father_sku_name.string' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_STRING',array("attribute"=>"father_sku_name")),
            'son_sku_id.string' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_STRING',array("attribute"=>"son_sku_id")),
            'son_sku_name.string' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_STRING',array("attribute"=>"son_sku_name")),
            'create_user_id.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_user_id")),
            'create_time_start.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_start")),
            'create_time_end.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_end")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'page.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"page")),
            'perpage.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"perpage")),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
        ]);
        $bomId = trim($request->input("id",null));
        $fatherSkuId = trim($request->input('father_sku_id',null));
        $fatherSkuName = trim($request->input('father_sku_name',null));
        $sonSkuId = trim($request->input('son_sku_id',null));
        $sonSkuName = trim($request->input('son_sku_name',null));
        $createUserId = $request->input('create_user_id',null);
        $createTimeStart = $request->input('create_time_start',null);
        $createTimeEnd = $request->input('create_time_end',null);
        $orgId = $request->input('org_id',null);
        $page = $request->input('page',1);
        $perPage = $request->input('perpage',50);
        $status = $request->input('status',null);
        try{
            $boms = BomRepository::getList($bomId,$fatherSkuId, $fatherSkuName,$sonSkuId,$sonSkuName
                    ,$createUserId,$createTimeStart,$createTimeEnd,$orgId,$page,$perPage,$status);
            return $this->jsonSuccess($boms);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
}