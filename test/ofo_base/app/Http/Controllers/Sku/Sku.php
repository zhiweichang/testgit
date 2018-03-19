<?php

namespace App\Http\Controllers\Sku;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\Sku as SkuRepository;
use App\Constants\Db\Tables\Base\Sku as SkuConst;
use Exception;

class Sku extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    
            
            
    public function create(Request $request){
        $this->validate($request, [
            'name' => 'required|string|min:1|max:64',
            'sku_id' => 'required|string|min:2|max:32',
            'type' => 'required|in:' . implode(',', array_keys(SkuConst::types())),
            'product_type' => 'required|integer',
            'stock_type' => 'required|in:' . implode(',', array_keys(SkuConst::stockTypes())),
            'hardware_version' => 'required|integer',
            'create_user_id' => 'required|integer',
            'format' => 'required|string|min:1|max:32',
            'weight' => 'sometimes|numeric', //小数，小数点后最多保留3位
            'org_ids'=>'required|json',
            'pn_ids'=>'required|ofo_json_required|json',
        ],[
            'name.required' => trans('message.SKU_NAME').trans('message.NOT_EMPTY'),
            'name.string' => trans('message.SKU_NAME').trans('message.INVALID'),
            'name.min' => trans('message.SKU_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'name.max' => trans('message.SKU_NAME').trans('message.NEED_MORE_THAN',array("min"=>"64")),
            'sku_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"SKU")),
            'sku_id.string' => trans('message.ATTRIBUTE_INVALID',array("attribute"=>"SKU")),
            'sku_id.min' => trans('message.ATTRIBUTE_NEED_LESS_THAN',array("attribute"=>"SKU","min"=>"1")),
            'sku_id.max' => trans('message.ATTRIBUTE_NEED_MORE_THAN',array("attribute"=>"SKU","max"=>"32")),
            'type.required' => trans('message.SKU_CLASS').trans('message.NOT_EMPTY'),
            'type.in' => trans('message.SKU_CLASS').trans('message.INVALID'),
            'product_type.required' => trans('message.PRODUCT_TYPE').trans('message.NOT_EMPTY'),
            'product_type.integer' => trans('message.PRODUCT_TYPE').trans('message.INVALID'),
            'stock_type.required' => trans('message.STOCK_TYPE').trans('message.NOT_EMPTY'),
            'stock_type.in' => trans('message.STOCK_TYPE').trans('message.INVALID'),
            'hardware_version.required' => trans('message.HARDWARE_VERSION').trans('message.NOT_EMPTY'),
            'hardware_version.integer' => trans('message.HARDWARE_VERSION').trans('message.INVALID'),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'format.required' => trans('message.SKU_SPECIFICATION').trans('message.NOT_EMPTY'),
            'format.string' => trans('message.SKU_SPECIFICATION').trans('message.INVALID'),
            'format.min' => trans('message.SKU_SPECIFICATION').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'format.max' => trans('message.SKU_SPECIFICATION').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'weight.numeric' => trans('message.WEIGHT').trans('message.NEED_INTEGER'),
            'org_ids.required' => trans('message.ORG_ID').trans('message.NOT_EMPTY'),
            'org_ids.json' => trans('message.ORG_ID').trans('message.NEED_JSON'),
            'pn_ids.required' => trans('message.PROJECT_PN').trans('message.NOT_EMPTY'),
            'pn_ids.ofo_json_required' => trans('message.PROJECT_PN').trans('message.NOT_EMPTY'),
            'pn_ids.json' => trans("message.PROJECT_PN").trans('message.NEED_JSON'),
        ]);
        $name = trim($request->input('name',''));
        $skuId = trim($request->input('sku_id',''));
        $type = $request->input('type',0);
        $codeId = $request->input('product_type',0); //注意：传来的是自增ID
        $stockType = $request->input('stock_type',0);
        $hardwareVersionId = $request->input('hardware_version','');//注意：传来的是自增ID
        $createUserId = $request->input('create_user_id','');
        $format = $request->input('format','');
        $weight = $request->input('weight','');
        $orgIds = Util::unserializeParams($request->input("org_ids",''));
        $pnIds  = Util::unserializeParams($request->input("pn_ids",''));
        try{
            $sku = SkuRepository::create($skuId,$name, $type, $codeId, $stockType, $hardwareVersionId, $createUserId, $format, $weight,$orgIds,$pnIds);
            return $this->jsonSuccess(['sku_id' => $sku[SkuConst::SKU_ID]]);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function update(Request $request){
        $this->validate($request, [
            'name' => 'required|string|min:1|max:64',
            'sku_id' => 'required|string|min:2|max:32',
            'type' => 'required|in:' . implode(',', array_keys(SkuConst::types())),
            'product_type' => 'required|integer',
            'stock_type' => 'required|in:' . implode(',', array_keys(SkuConst::stockTypes())),
            'hardware_version' => 'required|integer',
            'update_user_id' => 'required|integer',
            'format' => 'required|string|min:1|max:32',
            'weight' => 'sometimes|numeric', //小数，小数点后最多保留3位
            'org_ids'=>'required|json',
            'pn_ids'=>'required|ofo_json_required|json',
        ],[
            'name.required' => trans('message.SKU_NAME').trans('message.NOT_EMPTY'),
            'name.string' => trans('message.SKU_NAME').trans('message.INVALID'),
            'name.min' => trans('message.SKU_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'name.max' => trans('message.SKU_NAME').trans('message.NEED_MORE_THAN',array("min"=>"64")),
            'sku_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"SKU")),
            'sku_id.string' => trans('message.ATTRIBUTE_INVALID',array("attribute"=>"SKU")),
            'sku_id.min' => trans('message.ATTRIBUTE_NEED_LESS_THAN',array("attribute"=>"SKU","min"=>"1")),
            'sku_id.max' => trans('message.ATTRIBUTE_NEED_MORE_THAN',array("attribute"=>"SKU","max"=>"32")),
            'type.required' => trans('message.SKU_CLASS').trans('message.NOT_EMPTY'),
            'type.in' => trans('message.SKU_CLASS').trans('message.INVALID'),
            'product_type.required' => trans('message.PRODUCT_TYPE').trans('message.NOT_EMPTY'),
            'product_type.integer' => trans('message.PRODUCT_TYPE').trans('message.INVALID'),
            'stock_type.required' => trans('message.STOCK_TYPE').trans('message.NOT_EMPTY'),
            'stock_type.in' => trans('message.STOCK_TYPE').trans('message.INVALID'),
            'hardware_version.required' => trans('message.HARDWARE_VERSION').trans('message.NOT_EMPTY'),
            'hardware_version.integer' => trans('message.HARDWARE_VERSION').trans('message.INVALID'),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'format.required' => trans('message.SKU_SPECIFICATION').trans('message.NOT_EMPTY'),
            'format.string' => trans('message.SKU_SPECIFICATION').trans('message.INVALID'),
            'format.min' => trans('message.SKU_SPECIFICATION').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'format.max' => trans('message.SKU_SPECIFICATION').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'weight.numeric' => trans('message.WEIGHT').trans('message.NEED_INTEGER'),
            'org_ids.required' => trans('message.ORG_ID').trans('message.NOT_EMPTY'),
            'org_ids.json' => trans('message.ORG_ID').trans('message.NEED_JSON'),
            'pn_ids.required' => trans('message.PROJECT_PN').trans('message.NOT_EMPTY'),
            'pn_ids.ofo_json_required' => trans('message.PROJECT_PN').trans('message.NOT_EMPTY'),
            'pn_ids.json' => trans("message.PROJECT_PN").trans('message.NEED_JSON'),
        ]);
        $name = trim($request->input('name',''));
        $skuId = trim($request->input('sku_id',''));
        $type = $request->input('type',0);
        $codeId = $request->input('product_type',0); //注意：传来的是自增ID
        $stockType = $request->input('stock_type',0);
        $hardwareVersionId = $request->input('hardware_version','');//注意：传来的是自增ID
        $updateUserId = $request->input('update_user_id',0);
        $format = $request->input('format',0);
        $weight = $request->input('weight',0);
        $orgIds = Util::unserializeParams($request->input("org_ids",''));
        $pnIds  = Util::unserializeParams($request->input("pn_ids",''));
        try{
            SkuRepository::update($skuId,$name,$type, $codeId, $stockType, $hardwareVersionId, $updateUserId, $format, $weight,$orgIds,$pnIds);
            return $this->jsonSuccess();
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
            'sku_id' => 'required|string|min:2|max:32',
            'status' => 'required|in:' . implode(',', array_keys(SkuConst::$status)),
            'update_user_id' => 'required|integer',
        ],[
            'sku_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"sku_id")),
            'sku_id.string' => trans('message.ATTRIBUTE_INVALID',array("attribute"=>"sku_id")),
            'sku_id.min' => trans('message.ATTRIBUTE_NEED_LESS_THAN',array("attribute"=>"sku_id","min"=>"1")),
            'sku_id.max' => trans('message.ATTRIBUTE_NEED_MORE_THAN',array("attribute"=>"sku_id","max"=>"32")),
            'status.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"STATUS")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'update_user_id.required' => trans('message.UPDATE_USER').trans('message.NOT_EMPTY'),
            'update_user_id.integer' => trans('message.UPDATE_USER').trans('message.INVALID'),
        ]);
        $skuId = $request->input('sku_id','');
        $status = $request->input('status',0);
        $updateUserId = $request->input('update_user_id',0);
        try{
            SkuRepository::updateStatus($skuId, $status, $updateUserId);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listBySkuIds(Request $request){
        $this->validate($request, [
            'sku_ids' => 'required|json',
            'status' => 'sometimes|in:' . implode(',', array_keys(SkuConst::$status)),
            'org_id' => 'sometimes|in:' . implode(',', array_keys(SkuConst::$org)),
        ],[
            'sku_ids.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"sku_ids")),
            'sku_ids.json' => trans('message.ATTRIBUTE_NEED_JSON',array("attribute"=>"sku_ids")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
        ]);
        
        $skuIds = Util::unserializeParams($request->input('sku_ids'));
        $fields = $request->input('fields', null);
        $status = $request->input('status', null);
        $orgId = $request->input("org_id",null);
        try{
            $skus = SkuRepository::listBySkuIds($skuIds, $status, $orgId,$fields);
            return $this->jsonSuccess($skus);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse|\App\Http\Responses\MobileJsonResponse
     */
    public function listByTypes(Request $request) {
        $this->validate($request, [
            'types' => 'required|json',
            'status' => 'sometimes|in:' . implode(',', array_keys(SkuConst::$status)),
            'org_id' => 'sometimes|integer'
                ], [
            'types.required' => trans('message.ATTRIBUTE_NOT_EMPTY', array("attribute" => "types")),
            'types.json' => trans('message.ATTRIBUTE_NEED_JSON', array("attribute" => "types")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL', array("attribute" => "STATUS")),
        ]);
        $types = Util::unserializeParams($request->input("types"));
        $status = $request->input("status", null);
        $orgId = $request->input("org_id", null);
        try {
            $skus = SkuRepository::listByTypes($types, $status, $orgId);
            return $this->jsonSuccess($skus);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getBySkuId(Request $request){
        $this->validate($request, [
            'sku_id' => 'required',
            'status' => 'sometimes|in:' . implode(',', array_keys(SkuConst::$status)),
            'org_id' => 'sometimes|in:' . implode(',', array_keys(SkuConst::$org)),
        ],[
            'sku_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"sku_id")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL', array("attribute" => "STATUS")),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
        ]);
        $skuId = trim($request->input("sku_id"));
        $status= $request->input("status",null);
        $orgId = $request->input("org_id",null);
        try{
            $sku = SkuRepository::getBySkuId($skuId, $status,$orgId);
            return $this->jsonSuccess($sku);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 获取SKU列表
     * @param Request $request
     * @return array
     */
    public function getList(Request $request) {
        $this->validate($request, [
            'sku_id' => 'string',
            'name' => 'string',
            'type' => 'string|in:' . implode(',', array_keys(SkuConst::types())),
            'product_type' => 'string',
            'stock_type' => 'string|in:'.implode(',', array_keys(SkuConst::stockTypes())),
            'status' => 'string',
            'create_user_id' => 'integer',
            'create_time_start' => 'integer',
            'create_time_end' => 'integer',
            'page' => 'integer',
            'perpage' => 'integer',
            'org_id' => 'sometimes|in:' . implode(',', array_keys(SkuConst::$org)),
        ],[
            'sku_id.string' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_STRING',array("attribute"=>"sku_id")),
            'name.string' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_STRING',array("attribute"=>"name")),
            'type.string' => trans('message.SKU_CLASS').trans('message.NEED_EMPTY_OR_STRING'),
            'type.in' => trans('message.SKU_CLASS').trans('message.INVALID'),
            'product_type.string' => trans('message.PRODUCT_TYPE').trans('message.NEED_EMPTY_OR_STRING'),
            'stock_type.string' => trans('message.STOCK_TYPE').trans('message.NEED_EMPTY_OR_STRING'),
            'stock_type.in' => trans('message.STOCK_TYPE').trans('message.INVALID'),
            'create_user_id.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_user_id")),
            'create_time_start.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_start")),
            'create_time_end.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_end")),
            'page.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"page")),
            'perpage.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"perpage")),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
        ]);
        $skuId = trim($request->input("sku_id",null));
        $name  = trim($request->input("name",null));
        $type  = $request->input("type",null);
        $productType  = $request->input("product_type",null);
        $stockType = $request->input("stock_type",null);
        $status  = $request->input("status",null);
        $createUserId = $request->input("create_user_id",null);
        $createTimeStart = $request->input("create_time_start",null);
        $createTimeEnd  = $request->input("create_time_end",null);
        $fields  = $request->input("fields",null);
        $page    = $request->input("page",1);
        $perPage = $request->input("perpage",50);
        $orgId   = $request->input("org_id",null);
        
        try{
            $sku = SkuRepository::getList($skuId, $name,$type,$productType,$stockType,
                    $createUserId,$createTimeStart,$createTimeEnd,$fields,$orgId,$status,$page,$perPage);
            return $this->jsonSuccess($sku);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    /**
     * 存货列表
     * @return type
     */
    public function stockTypes() {
        try{
            $stockTypes = SkuRepository::stockTypes();
            return $this->jsonSuccess($stockTypes);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }   
    }
    /**
     * sku类别
     * @return type
     */
    public function skuTypes() {
        try {
            $skuTypes = SkuRepository::skuTypes();
            return $this->jsonSuccess($skuTypes);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /*
     * 
     * @param Request $request
     * @return type
     */
    public function checkBsns(Request $request) {
        $this->validate($request, [
            'sku_bsns' => 'required|json',
                ], [
            'sku_bsns.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"sku_bsns")),
            'sku_bsns.json' => trans('message.ATTRIBUTE_NEED_JSON',array("attribute"=>"sku_bsns")),
        ]);
        $skuBsns = Util::unserializeParams($request->input("sku_bsns"));
        try {
            $list = SkuRepository::checkBsnsBySku($skuBsns);
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
    public function listPnBySkuIds(Request $request) {
        $this->validate($request, [
            'sku_ids' => 'required|json',
        ],[
            'sku_ids.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"sku_ids")),
            'sku_ids.json' => trans('message.ATTRIBUTE_NEED_JSON',array("attribute"=>"sku_ids")),
        ]);
        $skuIds = Util::unserializeParams($request->input("sku_ids"));
        try{
            $pn = SkuRepository::listPnBySkuIds($skuIds);
            return $this->jsonSuccess($pn);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    public function listPnBySkuId(Request $request) {
        $this->validate($request, [
            'sku_id' => 'required',
        ],[
            'sku_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"sku_id")),
        ]);
        $skuId = $request->input("sku_id");
        try{
            $pn = SkuRepository::listPnBySkuId($skuId);
            return $this->jsonSuccess($pn);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function listBySkuOrName(Request $request) {
        $this->validate($request, [
            'page' => 'integer',
            'perpage' => 'integer',
            'org_id' => 'sometimes|in:' . implode(',', array_keys(SkuConst::$org)),
        ],[
            'page.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"page")),
            'perpage.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"perpage")),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
        ]);
        $sku = $request->input("sku",'');
        $page    = $request->input("page",1);
        $perPage = $request->input("perpage",50);
        $orgId   = $request->input("org_id",null);
        try{
            $skus = SkuRepository::listBySkuOrName($sku,$page,$perPage,$orgId);
            return $this->jsonSuccess($skus);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

}
