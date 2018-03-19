<?php

namespace App\Http\Controllers\Supplier;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\Supplier as SupplierRepository;
use App\Constants\Db\Tables\Base\Supplier as SupplierConst;
use Exception;

class Supplier extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function create(Request $request){
        $this->validate($request, [
            'name' => 'required|string|min:1|max:32',
            'short_name' => 'required|string|min:1|max:32',
            'is_general_taxpayer' => 'required|in:' . implode(',', array_keys(SupplierConst::$isGeneralTaxpayer)),
            'rate' => 'required|in:' . implode(',', array_keys(SupplierConst::$rates)),
            'create_user_id' => 'required|integer',
            'bank' => 'sometimes|string|min:1|max:32',
            'bank_code' => 'sometimes|string|min:1|max:32',
            'account' => 'sometimes|string|min:8|max:25',
            'category_id' => 'required|in:' . implode(',', array_keys(SupplierConst::$categories)),
            'factories' => 'sometimes|json',
            'org_ids'=>'required|json',
        ], [
            'name.required' => trans('message.SUPPLIER_NAME').trans('message.NOT_EMPTY'),
            'name.string' => trans('message.SUPPLIER_NAME').trans('message.INVALID'),
            'name.min' => trans('message.SUPPLIER_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'name.max' => trans('message.SUPPLIER_NAME').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'short_name.required' => trans('message.SUPPLIER_SHORT_NAME').trans('message.NOT_EMPTY'),
            'short_name.string' => trans('message.SUPPLIER_SHORT_NAME').trans('message.INVALID'),
            'short_name.min' => trans('message.SUPPLIER_SHORT_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'short_name.max' => trans('message.SUPPLIER_SHORT_NAME').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'is_general_taxpayer.required' => trans('message.IS_GENERAL_TAXPAYER').trans('message.NOT_EMPTY'),
            'is_general_taxpayer.in' => trans('message.IS_GENERAL_TAXPAYER').trans('message.INVALID'),
            'rate.required' => trans('message.SUPPER_RATE').trans('message.NOT_EMPTY'),
            'rate.in' => trans('message.SUPPER_RATE').trans('message.INVALID'),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'bank.string' => trans('message.SUPPER_BANK').trans('message.INVALID'),
            'bank.min' => trans('message.SUPPER_BANK').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'bank.max' => trans('message.SUPPER_BANK').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'bank_code.string' => trans('message.SUPPER_BANK_CODE').trans('message.INVALID'),
            'bank_code.min' => trans('message.SUPPER_BANK_CODE').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'bank_code.max' => trans('message.SUPPER_BANK_CODE').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'account.string' => trans('message.SUPPER_ACCOUNT').trans('message.INVALID'),
            'account.min' => trans('message.SUPPER_ACCOUNT').trans('message.NEED_LESS_THAN',array("min"=>"8")),
            'account.max' => trans('message.SUPPER_ACCOUNT').trans('message.NEED_MORE_THAN',array("min"=>"25")),
            'category_id.required' => trans('message.SUPPER_CATEGORY_ID').trans('message.NOT_EMPTY'),
            'category_id.in' => trans('message.SUPPER_CATEGORY_ID').trans('message.INVALID'),
            'factories.json' => trans('message.FACTORIES').trans('message.NEED_JSON'),
            'org_ids.required' => trans('message.ORG_ID').trans('message.NOT_EMPTY'),
            'org_ids.json' => trans('message.ORG_ID').trans('message.NEED_JSON'),
        ]);
        $name = trim($request->input('name',''));
        $shortName = trim($request->input('short_name',''));
        $isGeneralTaxpayer = $request->input('is_general_taxpayer',0);
        $rate = $request->input('rate','');
        $createUserId = $request->input('create_user_id',0);
        $bank = $request->input('bank','');
        $bankCode = $request->input('bank_code','');
        $account = $request->input('account','');
        $categoryId = $request->input('category_id','');
        $factories = Util::unserializeParams($request->input('factories',''));
        $orgIds = Util::unserializeParams($request->input("org_ids",''));
        if(!empty($factories)){
            $rules = [
                'factory_id' => 'required|string|min:1|max:32',
                'name' => 'required|string|min:1|max:32',
                'city_id' => 'required|integer',
                'address' => 'required|min:1|max:64',
                'contract_user_name' => 'required|min:1|max:32',
                'contract_user_mobile' => 'required',
            ];
            $messages = [
                'factory_id.required' => trans('message.FACTORY_ID').trans('message.NOT_EMPTY'),
                'factory_id.string' => trans('message.FACTORY_ID').trans('message.INVALID'),
                'factory_id.min' =>  trans('message.FACTORY_ID').trans('message.NEED_LESS_THAN',array("min"=>"1")),
                'factory_id.max' =>  trans('message.FACTORY_ID').trans('message.NEED_MORE_THAN',array("min"=>"32")),
                'name.required' => trans('message.FACTORY_NAME').trans('message.NOT_EMPTY'),
                'name.string' => trans('message.FACTORY_NAME').trans('message.INVALID'),
                'name.min' => trans('message.FACTORY_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
                'name.max' =>  trans('message.FACTORY_NAME').trans('message.NEED_MORE_THAN',array("min"=>"32")),
                'city_id.required' =>  trans('message.FACTORY_CITY').trans('message.NOT_EMPTY'),
                'city_id.integer' => trans('message.FACTORY_CITY').trans('message.INVALID'),
                'address.required' =>  trans('message.FACTORY_ADDRESS').trans('message.NOT_EMPTY'),
                'address.min' => trans('message.FACTORY_ADDRESS').trans('message.NEED_LESS_THAN',array("min"=>"1")),
                'address.max' => trans('message.FACTORY_ADDRESS').trans('message.NEED_MORE_THAN',array("min"=>"32")),
                'contract_user_name.required' => trans('message.FACTORY_USER').trans('message.NOT_EMPTY'),
                'contract_user_name.min' => trans('message.FACTORY_USER').trans('message.NEED_LESS_THAN',array("min"=>"1")),
                'contract_user_name.max' => trans('message.FACTORY_USER').trans('message.NEED_MORE_THAN',array("min"=>"32")),
                'contract_user_mobile.required' => trans('message.FACTORY_TEL').trans('message.NOT_EMPTY'),
            ];
            foreach($factories as $factory){
                $validator = app('validator')->make($factory, $rules, $messages);
                if($validator->fails()){
                    $error = $validator->errors()->all()[0];
                    return $this->jsonFailed(Common::ERR_PARAMS_REQUEST, $error);
                }
            }
        }
        try{
            $supplier = SupplierRepository::create($name, $shortName,
                $isGeneralTaxpayer, $rate, $createUserId, $bank, $bankCode, $account, $categoryId, $factories,$orgIds);
            return $this->jsonSuccess(['supplier_id' => $supplier[SupplierConst::SUPPLIER_ID]]);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    public function update(Request $request){
        $this->validate($request, [
            'supplier_id' => 'required|string|min:1|max:32',
            'name' => 'required|string|min:1|max:32',
            'short_name' => 'required|string|min:1|max:32',
            'is_general_taxpayer' => 'required|in:' . implode(',', array_keys(SupplierConst::$isGeneralTaxpayer)),
            'rate' => 'required|in:' . implode(',', array_keys(SupplierConst::$rates)),
            'create_user_id' => 'required|integer',
            'bank' => 'sometimes|string|min:1|max:32',
            'bank_code' => 'sometimes|string|min:1|max:32',
            'account' => 'sometimes|string|min:8|max:25',
            'category_id' => 'required|in:' . implode(',', array_keys(SupplierConst::$categories)),
            'factories' => 'sometimes|json',
            'org_ids'=>'required|json',
        ], [
            'supplier_id.required' => trans('message.SUPPLIER_ID').trans('message.NOT_EMPTY'),
            'supplier_id.string' => trans('message.SUPPLIER_ID').trans('message.INVALID'),
            'supplier_id.min' => trans('message.SUPPLIER_ID').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'supplier_id.max' => trans('message.SUPPLIER_ID').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'name.required' => trans('message.SUPPLIER_NAME').trans('message.NOT_EMPTY'),
            'name.string' => trans('message.SUPPLIER_NAME').trans('message.INVALID'),
            'name.min' => trans('message.SUPPLIER_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'name.max' => trans('message.SUPPLIER_NAME').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'short_name.required' => trans('message.SUPPLIER_SHORT_NAME').trans('message.NOT_EMPTY'),
            'short_name.string' => trans('message.SUPPLIER_SHORT_NAME').trans('message.INVALID'),
            'short_name.min' => trans('message.SUPPLIER_SHORT_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'short_name.max' => trans('message.SUPPLIER_SHORT_NAME').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'is_general_taxpayer.required' => trans('message.IS_GENERAL_TAXPAYER').trans('message.NOT_EMPTY'),
            'is_general_taxpayer.in' => trans('message.IS_GENERAL_TAXPAYER').trans('message.INVALID'),
            'rate.required' => trans('message.SUPPER_RATE').trans('message.NOT_EMPTY'),
            'rate.in' => trans('message.SUPPER_RATE').trans('message.INVALID'),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'bank.string' => trans('message.SUPPER_BANK').trans('message.INVALID'),
            'bank.min' => trans('message.SUPPER_BANK').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'bank.max' => trans('message.SUPPER_BANK').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'bank_code.string' => trans('message.SUPPER_BANK_CODE').trans('message.INVALID'),
            'bank_code.min' => trans('message.SUPPER_BANK_CODE').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'bank_code.max' => trans('message.SUPPER_BANK_CODE').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'account.string' => trans('message.SUPPER_ACCOUNT').trans('message.INVALID'),
            'account.min' => trans('message.SUPPER_ACCOUNT').trans('message.NEED_LESS_THAN',array("min"=>"8")),
            'account.max' => trans('message.SUPPER_ACCOUNT').trans('message.NEED_MORE_THAN',array("min"=>"25")),
            
            'category_id.required' => trans('message.SUPPER_CATEGORY_ID').trans('message.NOT_EMPTY'),
            'category_id.in' => trans('message.SUPPER_CATEGORY_ID').trans('message.INVALID'),
            'factories.json' => trans('message.FACTORIES').trans('message.NEED_JSON'),
            'org_ids.required' => trans('message.ORG_ID').trans('message.NOT_EMPTY'),
            'org_ids.json' => trans('message.ORG_ID').trans('message.NEED_JSON'),
        ]);
        $supplierId = trim($request->input('supplier_id',''));
        $name = trim($request->input('name',''));
        $shortName = trim($request->input('short_name',''));
        $isGeneralTaxpayer = $request->input('is_general_taxpayer','');
        $rate = $request->input('rate','');
        $createUserId = $request->input('create_user_id','');
        $bank = $request->input('bank','');
        $bankCode = $request->input('bank_code','');
        $account = $request->input('account','');
        $categoryId = $request->input('category_id','');
        $factories = Util::unserializeParams($request->input('factories',''));
        $orgIds = Util::unserializeParams($request->input("org_ids",''));
        if(!empty($factories)){
            $rules = [
                'factory_id' => 'required|string|min:1|max:32',
                'name' => 'required|string|min:1|max:32',
                'city_id' => 'required|integer',
                'address' => 'required|min:1|max:64',
                'contract_user_name' => 'required|min:1|max:32',
                'contract_user_mobile' => 'required',
            ];
            $messages = [
                'factory_id.required' => trans('message.FACTORY_ID').trans('message.NOT_EMPTY'),
                'factory_id.string' => trans('message.FACTORY_ID').trans('message.INVALID'),
                'factory_id.min' =>  trans('message.FACTORY_ID').trans('message.NEED_LESS_THAN',array("min"=>"1")),
                'factory_id.max' =>  trans('message.FACTORY_ID').trans('message.NEED_MORE_THAN',array("min"=>"32")),
                'name.required' => trans('message.FACTORY_NAME').trans('message.NOT_EMPTY'),
                'name.string' => trans('message.FACTORY_NAME').trans('message.INVALID'),
                'name.min' => trans('message.FACTORY_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
                'name.max' =>  trans('message.FACTORY_NAME').trans('message.NEED_MORE_THAN',array("min"=>"32")),
                'city_id.required' =>  trans('message.FACTORY_CITY').trans('message.NOT_EMPTY'),
                'city_id.integer' => trans('message.FACTORY_CITY').trans('message.INVALID'),
                'address.required' =>  trans('message.FACTORY_ADDRESS').trans('message.NOT_EMPTY'),
                'address.min' => trans('message.FACTORY_ADDRESS').trans('message.NEED_LESS_THAN',array("min"=>"1")),
                'address.max' => trans('message.FACTORY_ADDRESS').trans('message.NEED_MORE_THAN',array("min"=>"32")),
                'contract_user_name.required' => trans('message.FACTORY_USER').trans('message.NOT_EMPTY'),
                'contract_user_name.min' => trans('message.FACTORY_USER').trans('message.NEED_LESS_THAN',array("min"=>"1")),
                'contract_user_name.max' => trans('message.FACTORY_USER').trans('message.NEED_MORE_THAN',array("min"=>"32")),
                'contract_user_mobile.required' => trans('message.FACTORY_TEL').trans('message.NOT_EMPTY'),
            ];
            foreach($factories as $factory){
                $validator = app('validator')->make($factory, $rules, $messages);
                if($validator->fails()){
                    $error = $validator->errors()->all()[0];
                    return $this->jsonFailed(Common::ERR_PARAMS_REQUEST, $error);
                }
            }
        }
        try{
            SupplierRepository::update($supplierId, $name, $shortName,
                $isGeneralTaxpayer, $rate, $createUserId, $bank, $bankCode, $account, $categoryId, $factories,$orgIds);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listBySupplierIds(Request $request){
        $this->validate($request, [
            'supplier_ids' => 'required',
            'status' => 'sometimes|in:' . implode(',', array_keys(SupplierConst::$status)),
            'org_id' => 'sometimes|in:' . implode(',', array_keys(SupplierConst::$org)),
        ]);
        $supplierIds = Util::unserializeParams($request->input("supplier_ids",''));
        $status      = $request->input("status",null);
        $orgId       = $request->input("org_id",null);
        try{
            $suppliers = SupplierRepository::listBySupplierIds($supplierIds,$status,$orgId);
            return $this->jsonSuccess($suppliers);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getBySupplierId(Request $request){
        $this->validate($request, [
            'supplier_id' => 'required',
            'status' => 'sometimes|in:' . implode(',', array_keys(SupplierConst::$status)),
            'org_id' => 'sometimes|in:' . implode(',', array_keys(SupplierConst::$org)),
        ]);
        $params = $request->input();
        $supplierId = $request->input("supplier_id");
        $status     = $request->input("status",null);
        $orgId      = $request->input("org_id",null);
        try{
            $supplier = SupplierRepository::getBySupplierId($supplierId, $status,$orgId);
            return $this->jsonSuccess($supplier);
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
            'supplier_id' => 'required',
            'status' => 'required|in:' . implode(',', array_keys(SupplierConst::$status)),
            'update_user_id' => 'required|integer',
        ],[
            'factory_id.required' => trans('message.FACTORY_ID').trans('message.NOT_EMPTY'),
            'status.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"STATUS")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'update_user_id.required' => trans('message.UPDATE_USER').trans('message.NOT_EMPTY'),
            'update_user_id.integer' => trans('message.UPDATE_USER').trans('message.INVALID'),
        ]);
        $supplierId = $request->input('supplier_id');
        $status = $request->input('status');
        $updateUserId = $request->input('update_user_id');
        try{
            SupplierRepository::updateStatus($supplierId, $status, $updateUserId);
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
            'name' => 'string',
            'status' => 'in:' . implode(',', array_keys(SupplierConst::$status)),
            'create_user_id' => 'integer',
            'create_time_start' => 'integer',
            'create_time_end' => 'integer',
            'fields' => "json",
            'category_id'=>"string",
            'supplier_id'=>"string",
            'org_id' => 'sometimes|in:' . implode(',', array_keys(SupplierConst::$org)),
            'page' => 'integer',
            'perpage' => 'integer',
        ],[
            'name.string' => trans('message.FACTORY_ID').trans('message.INVALID'),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'create_time_start.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_start")),
            'create_time_end.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_end")),
            'fields.json' => trans("message.ATTRIBUTE_NEED_JSON",array("attribute"=>"fields")),
            'category_id.string' => trans('message.SUPPER_CATEGORY_ID').trans('message.INVALID'),
            'supplier_id.string' => trans('message.SUPPLIER_ID').trans('message.INVALID'),
            'page.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"page")),
            'perpage.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"perpage")),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
        ]);
        $name = $request->input('name','');
        $categoryId = $request->input("category_id",null);
        $supplierId = $request->input("supplier_id",null);
        $createUserId = $request->input('create_user_id',null);
        $createTimeStart = $request->input("create_time_start",null);
        $createTimeEnd   = $request->input("create_time_end",null);
        $status = $request->input('status',0);
        $orgId    = $request->input("org_id",null);
        $page  = $request->input("page",1);
        $perPage = $request->input("perpage",50);
        $fields  = Util::unserializeParams($request->input("fields",''));
        
        try{
            $suppliers = SupplierRepository::getList($name, $categoryId, $supplierId,$createUserId,$createTimeStart
                    ,$createTimeEnd,$status,$orgId,$page,$perPage,$fields);
            return $this->jsonSuccess($suppliers);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    public function getDetail(Request $request) {
        $this->validate($request, [
            'org_id' => 'sometimes|in:' . implode(',', array_keys(SupplierConst::$org)),
            'status' => 'in:' . implode(',', array_keys(SupplierConst::$status)),
        ],[
            'supplier_id.required' => trans('message.SUPPLIER_ID').trans('message.NOT_EMPTY'),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
        ]);
        $supplierId = $request->input('supplier_id');
        $id = $request->input('id');
        if(empty($supplierId)) {
            $supplierId = $id;
        }
        $orgId = $request->input("org_id",null);
        $status= $request->input("status", null);
        try{
            $supplier = SupplierRepository::getDetail($supplierId,$status, $orgId);
            return $this->jsonSuccess($supplier);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
        
    }
    /**
     * 
     * @return type
     */
    public function getRateList(){
        try{
            $rateList = SupplierRepository::getRateList();
            return $this->jsonSuccess($rateList);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getBasic(Request $request) {
        $this->validate($request, [
            'supplier_id' => 'required',
            'status' => 'in:' . implode(',', array_keys(SupplierConst::$status)),
            'org_id' => 'sometimes|in:' . implode(',', array_keys(SupplierConst::$org)),
                ], [
            'supplier_id.required' => trans('message.SUPPLIER_ID').trans('message.NOT_EMPTY'),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
        ]);
        $supplierId = $request->input('supplier_id');
        $orgId = $request->input("org_id", null);
        $status= $request->input("status", null);
        try {
            $supplier = SupplierRepository::getBySupplierId($supplierId,$status,$orgId);
            return $this->jsonSuccess($supplier);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getByFactoryId(Request $request) {
        $this->validate($request, [
            'factory_id' => 'required',
                ], [
            'factory_id.required' => trans('message.FACTORY').trans('message.NOT_EMPTY'),
        ]);
        $factoryId = $request->input('factory_id');
        try {
            $supplier = SupplierRepository::getByFactoryId($factoryId);
            return $this->jsonSuccess($supplier);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getCategoryList(Request $request) {
        try {
            $categoryList = SupplierRepository::getCategoryList();
            return $this->jsonSuccess($categoryList);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

}

