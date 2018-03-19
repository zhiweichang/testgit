<?php

namespace App\Http\Controllers\Warehouse;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\Warehouse as WarehouseRepository;
use App\Constants\Db\Tables\Base\Warehouse as WarehouseConst;
use Exception;

class Warehouse extends Controller {
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse|\App\Http\Responses\MobileJsonResponse
     */
    public function create(Request $request){
        $this->validate($request, [
            'warehouse_id' => 'required|string|min:1|max:32',
            'name' => 'required|string|min:1|max:32',
            'type' => 'required|in:' . implode(',', array_keys(WarehouseConst::types())),
            'factory_id' => 'required_if:type,' . WarehouseConst::TYPE_MAKE . '|string',
            'city' => 'required|integer',
            'address' => 'required|string|min:1|max:128',
            'contact_name' => 'required|string|min:1|max:32',
            'contact_mobile' => 'required|org_mobile|max:20',
            'contact_email' => 'required|max:64',
            'manager_name' => 'required|string|max:32',
            'manager_mobile' => 'required|org_mobile|max:20',
            'manager_email' => 'required|max:64',
            'first_receiver_name' => 'string|max:32',
            'first_receiver_mobile' => 'string|org_mobile|max:20',
            'first_receiver_email' => 'string|max:64',
            'second_receiver_name' => 'string|max:32',
            'second_receiver_mobile' => 'string|org_mobile|max:20',
            'second_receiver_email' => 'string|max:64',
            'create_user_id' => 'required|integer',
            'org_ids'=>'required|json',
            'longitude' => 'string|min:1|max:32',
            'latitude' => 'string|min:1|max:32',
        ], [
            'warehouse_id.required' => trans('message.WAREHOUSE_ID').trans('message.NOT_EMPTY'),
            'warehouse_id.string' => trans('message.WAREHOUSE_ID').trans('message.INVALID'),
            'warehouse_id.min' => trans('message.WAREHOUSE_ID').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'warehouse_id.max' => trans('message.WAREHOUSE_ID').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'name.required' => trans('message.WAREHOUSE_NAME').trans('message.NOT_EMPTY'),
            'name.string' => trans('message.WAREHOUSE_NAME').trans('message.INVALID'),
            'name.min' => trans('message.WAREHOUSE_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'name.max' => trans('message.WAREHOUSE_NAME').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'type.required' => trans('message.WAREHOUSE_TYPR').trans('message.NOT_EMPTY'),
            'type.in' => trans('message.WAREHOUSE_TYPR').trans('message.INVALID'),
            'factory_id.required_if' => trans('message.WAREHOUSE_SUPPER_FACTORY'),
            'factory_id.string' => trans('message.FACTORY_ID').trans('message.INVALID'),
            'city.required' =>  trans('message.CITY').trans('message.NOT_EMPTY'),
            'city.integer' => trans('message.CITY').trans('message.INVALID'),
            'address.required' => trans('message.ADDRESS').trans('message.NOT_EMPTY'),
            'address.string' => trans('message.ADDRESS').trans('message.INVALID'),
            'address.max' => trans('message.ADDRESS').trans('message.NEED_MORE_THAN',array("max"=>"128")),
            'contact_name.required' => trans('message.WAREHOUSE_CONTACT_NAME').trans('message.NOT_EMPTY'),
            'contact_name.string' => trans('message.WAREHOUSE_CONTACT_NAME').trans('message.INVALID'),
            'contact_name.min' => trans('message.WAREHOUSE_CONTACT_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'contact_name.max' => trans('message.WAREHOUSE_CONTACT_NAME').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'contact_mobile.required' => trans('message.WAREHOUSE_CONTACT_MOBILE').trans('message.NOT_EMPTY'),
            'contact_mobile.org_mobile' => trans('message.WAREHOUSE_CONTACT_MOBILE').trans('message.INVALID'),
            'contact_mobile.max' => trans('message.WAREHOUSE_CONTACT_MOBILE').trans('message.NEED_MORE_THAN',array("max"=>"20")),
            'contact_email.required' => trans('message.WAREHOUSE_CONTACT_EMAIL').trans('message.NOT_EMPTY'),
            'contact_email.max' => trans('message.WAREHOUSE_CONTACT_EMAIL').trans('message.NEED_MORE_THAN',array("max"=>"64")),
            'manager_name.required' => trans('message.WAREHOUSE_MANAGER').trans('message.NOT_EMPTY'),
            'manager_name.string' => trans('message.WAREHOUSE_MANAGER').trans('message.INVALID'),
            'manager_name.max' => trans('message.WAREHOUSE_MANAGER').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'manager_mobile.required' => trans('message.WAREHOUSE_MANAGER_MOBILE').trans('message.NOT_EMPTY'),
            'manager_mobile.org_mobile' => trans('message.WAREHOUSE_MANAGER_MOBILE').trans('message.INVALID'),
            'manager_mobile.max' => trans('message.WAREHOUSE_MANAGER_MOBILE').trans('message.NEED_MORE_THAN',array("max"=>"20")),
            'manager_email.required' => trans('message.WAREHOUSE_MANAGER_EMAIL').trans('message.NOT_EMPTY'),
            'manager_email.max' => trans('message.WAREHOUSE_MANAGER_EMAIL').trans('message.NEED_MORE_THAN',array("max"=>"64")),
            'first_receiver_name.string' => trans('message.WAREHOUSE_FIRST_RECEIVER').trans('message.NOT_EMPTY'),
            'first_receiver_name.max' => trans('message.WAREHOUSE_FIRST_RECEIVER').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'first_receiver_mobile.string' => trans('message.WAREHOUSE_FIRST_RECEIVER_MOBILE').trans('message.NOT_EMPTY'),
            'first_receiver_mobile.org_mobile' => trans('message.WAREHOUSE_FIRST_RECEIVER_MOBILE').trans('message.INVALID'),
            'first_receiver_mobile.max' => trans('message.WAREHOUSE_FIRST_RECEIVER_MOBILE').trans('message.NEED_MORE_THAN',array("max"=>"20")),
            'first_receiver_email.string' => trans('message.WAREHOUSE_FIRST_RECEIVER_EMAIL').trans('message.NOT_EMPTY'),
            'first_receiver_email.max' => trans('message.WAREHOUSE_FIRST_RECEIVER_EMAIL').trans('message.NEED_MORE_THAN',array("max"=>"64")),
            'second_receiver_name.string' => trans('message.WAREHOUSE_SECOND_RECEIVER').trans('message.NOT_EMPTY'),
            'second_receiver_name.max' => trans('message.WAREHOUSE_SECOND_RECEIVER').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'second_receiver_mobile.string' => trans('message.WAREHOUSE_SECOND_RECEIVER_MOBILE').trans('message.NOT_EMPTY'),
            'second_receiver_mobile.org_mobile' => trans('message.WAREHOUSE_SECOND_RECEIVER_MOBILE').trans('message.INVALID'),
            'second_receiver_mobile.max' => trans('message.WAREHOUSE_SECOND_RECEIVER_MOBILE').trans('message.NEED_MORE_THAN',array("max"=>"20")),
            'second_receiver_email.string' => trans('message.WAREHOUSE_SECOND_RECEIVER_EMAIL').trans('message.NOT_EMPTY'),
            'second_receiver_email.max' => trans('message.WAREHOUSE_SECOND_RECEIVER_EMAIL').trans('message.NEED_MORE_THAN',array("max"=>"64")),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'org_ids.required' => trans('message.ORG_ID').trans('message.NOT_EMPTY'),
            'org_ids.json' => trans('message.ORG_ID').trans('message.NEED_JSON'),
            'longitude.max' => trans('message.LONGITUDE').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'latitude.max' => trans('message.LATITUDE').trans('message.NEED_MORE_THAN',array("min"=>"32")),
        ]);
        $warehouseId = trim($request->input('warehouse_id',''));
        $name = trim($request->input('name',''));
        $type = $request->input('type','');
        $factoryId = $request->input('factory_id','');
        $city = $request->input('city','');
        $address = $request->input('address','');
        $contactPerson = $request->input('contact_name','');
        $contactMobile = $request->input('contact_mobile','');
        $contactEmail = $request->input('contact_email','');
        $managerPerson = $request->input('manager_name','');
        $managerMobile = $request->input('manager_mobile','');
        $managerEmail = $request->input('manager_email','');
        $firstReceiverPerson = $request->input('first_receiver_name', '');
        $firstReceiverMobile = $request->input('first_receiver_mobile', '');
        $firstReceiverEmail = $request->input('first_receiver_email', '');
        $secondReceiverPerson = $request->input('second_receiver_name', '');
        $secondReceiverMobile = $request->input('second_receiver_mobile', '');
        $secondReceiverEmail = $request->input('second_receiver_email', '');
        $createUserId = $request->input('create_user_id','');
        $longitude = $request->input('longitude','');
        $latitude = $request->input('latitude','');
        $orgIds = Util::unserializeParams($request->input("org_ids",''));
        try{
            $warehouse = WarehouseRepository::create($warehouseId, $name, $type, $factoryId, $city, $address,
                $contactPerson, $contactMobile, $contactEmail, $managerPerson, $managerMobile, $managerEmail,
                $firstReceiverPerson, $firstReceiverMobile, $firstReceiverEmail, $secondReceiverPerson, $secondReceiverMobile, $secondReceiverEmail,
                $createUserId,$orgIds, $longitude, $latitude);
            return $this->jsonSuccess([WarehouseConst::WAREHOUSE_ID => $warehouse[WarehouseConst::WAREHOUSE_ID]]);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse|\App\Http\Responses\MobileJsonResponse
     */
    public function update(Request $request){
        $this->validate($request, [
            'warehouse_id' => 'required|string|min:1|max:32',
            'name' => 'required|string|min:1|max:32',
            'type' => 'required|in:' . implode(',', array_keys(WarehouseConst::types())),
            'factory_id' => 'required_if:type,' . WarehouseConst::TYPE_MAKE . '|string',
            'city' => 'required|integer',
            'address' => 'required|string|min:1|max:128',
            'contact_name' => 'required|string|min:1|max:32',
            'contact_mobile' => 'required|org_mobile|max:20',
            'contact_email' => 'required|max:64',
            'manager_name' => 'required|string|max:32',
            'manager_mobile' => 'required|org_mobile|max:20',
            'manager_email' => 'required|max:64',
            'first_receiver_name' => 'string|max:32',
            'first_receiver_mobile' => 'string|org_mobile|max:20',
            'first_receiver_email' => 'string|max:64',
            'second_receiver_name' => 'string|max:32',
            'second_receiver_mobile' => 'string|org_mobile|max:20',
            'second_receiver_email' => 'string|max:64',
            'create_user_id' => 'required|integer',
            'org_ids'=>'required|json',
            'longitude' => 'string|min:1|max:32',
            'latitude' => 'string|min:1|max:32',
        ], [
            'warehouse_id.required' => trans('message.WAREHOUSE_ID').trans('message.NOT_EMPTY'),
            'warehouse_id.string' => trans('message.WAREHOUSE_ID').trans('message.INVALID'),
            'warehouse_id.min' => trans('message.WAREHOUSE_ID').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'warehouse_id.max' => trans('message.WAREHOUSE_ID').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'name.required' => trans('message.WAREHOUSE_NAME').trans('message.NOT_EMPTY'),
            'name.string' => trans('message.WAREHOUSE_NAME').trans('message.INVALID'),
            'name.min' => trans('message.WAREHOUSE_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'name.max' => trans('message.WAREHOUSE_NAME').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'type.required' => trans('message.WAREHOUSE_TYPR').trans('message.NOT_EMPTY'),
            'type.in' => trans('message.WAREHOUSE_TYPR').trans('message.INVALID'),
            'factory_id.required_if' => trans('message.WAREHOUSE_SUPPER_FACTORY'),
            'factory_id.string' => trans('message.FACTORY_ID').trans('message.INVALID'),
            'city.required' =>  trans('message.CITY').trans('message.NOT_EMPTY'),
            'city.integer' => trans('message.CITY').trans('message.INVALID'),
            'address.required' => trans('message.ADDRESS').trans('message.NOT_EMPTY'),
            'address.string' => trans('message.ADDRESS').trans('message.INVALID'),
            'address.max' => trans('message.ADDRESS').trans('message.NEED_MORE_THAN',array("max"=>"128")),
            'contact_name.required' => trans('message.WAREHOUSE_CONTACT_NAME').trans('message.NOT_EMPTY'),
            'contact_name.string' => trans('message.WAREHOUSE_CONTACT_NAME').trans('message.INVALID'),
            'contact_name.min' => trans('message.WAREHOUSE_CONTACT_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'contact_name.max' => trans('message.WAREHOUSE_CONTACT_NAME').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'contact_mobile.required' => trans('message.WAREHOUSE_CONTACT_MOBILE').trans('message.NOT_EMPTY'),
            'contact_mobile.org_mobile' => trans('message.WAREHOUSE_CONTACT_MOBILE').trans('message.INVALID'),
            'contact_mobile.max' => trans('message.WAREHOUSE_CONTACT_MOBILE').trans('message.NEED_MORE_THAN',array("max"=>"20")),
            'contact_email.required' => trans('message.WAREHOUSE_CONTACT_EMAIL').trans('message.NOT_EMPTY'),
            'contact_email.max' => trans('message.WAREHOUSE_CONTACT_EMAIL').trans('message.NEED_MORE_THAN',array("max"=>"64")),
            'manager_name.required' => trans('message.WAREHOUSE_MANAGER').trans('message.NOT_EMPTY'),
            'manager_name.string' => trans('message.WAREHOUSE_MANAGER').trans('message.INVALID'),
            'manager_name.max' => trans('message.WAREHOUSE_MANAGER').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'manager_mobile.required' => trans('message.WAREHOUSE_MANAGER_MOBILE').trans('message.NOT_EMPTY'),
            'manager_mobile.org_mobile' => trans('message.WAREHOUSE_MANAGER_MOBILE').trans('message.INVALID'),
            'manager_mobile.max' => trans('message.WAREHOUSE_MANAGER_MOBILE').trans('message.NEED_MORE_THAN',array("max"=>"20")),
            'manager_email.required' => trans('message.WAREHOUSE_MANAGER_EMAIL').trans('message.NOT_EMPTY'),
            'manager_email.max' => trans('message.WAREHOUSE_MANAGER_EMAIL').trans('message.NEED_MORE_THAN',array("max"=>"64")),
            'first_receiver_name.string' => trans('message.WAREHOUSE_FIRST_RECEIVER').trans('message.NOT_EMPTY'),
            'first_receiver_name.max' => trans('message.WAREHOUSE_FIRST_RECEIVER').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'first_receiver_mobile.string' => trans('message.WAREHOUSE_FIRST_RECEIVER_MOBILE').trans('message.NOT_EMPTY'),
            'first_receiver_mobile.org_mobile' => trans('message.WAREHOUSE_FIRST_RECEIVER_MOBILE').trans('message.INVALID'),
            'first_receiver_mobile.max' => trans('message.WAREHOUSE_FIRST_RECEIVER_MOBILE').trans('message.NEED_MORE_THAN',array("max"=>"20")),
            'first_receiver_email.string' => trans('message.WAREHOUSE_FIRST_RECEIVER_EMAIL').trans('message.NOT_EMPTY'),
            'first_receiver_email.max' => trans('message.WAREHOUSE_FIRST_RECEIVER_EMAIL').trans('message.NEED_MORE_THAN',array("max"=>"64")),
            'second_receiver_name.string' => trans('message.WAREHOUSE_SECOND_RECEIVER').trans('message.NOT_EMPTY'),
            'second_receiver_name.max' => trans('message.WAREHOUSE_SECOND_RECEIVER').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'second_receiver_mobile.string' => trans('message.WAREHOUSE_SECOND_RECEIVER_MOBILE').trans('message.NOT_EMPTY'),
            'second_receiver_mobile.org_mobile' => trans('message.WAREHOUSE_SECOND_RECEIVER_MOBILE').trans('message.INVALID'),
            'second_receiver_mobile.max' => trans('message.WAREHOUSE_SECOND_RECEIVER_MOBILE').trans('message.NEED_MORE_THAN',array("max"=>"20")),
            'second_receiver_email.string' => trans('message.WAREHOUSE_SECOND_RECEIVER_EMAIL').trans('message.NOT_EMPTY'),
            'second_receiver_email.max' => trans('message.WAREHOUSE_SECOND_RECEIVER_EMAIL').trans('message.NEED_MORE_THAN',array("max"=>"64")),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'org_ids.required' => trans('message.ORG_ID').trans('message.NOT_EMPTY'),
            'org_ids.json' => trans('message.ORG_ID').trans('message.NEED_JSON'),
            'longitude.max' => trans('message.LONGITUDE').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'latitude.max' => trans('message.LATITUDE').trans('message.NEED_MORE_THAN',array("min"=>"32")),
        ]);
        $warehouseId = $request->input('warehouse_id','');
        $name = trim($request->input('name',''));
        $type = $request->input('type','');
        $factoryId = $request->input('factory_id','');
        $city = $request->input('city','');
        $address = $request->input('address','');
        $contactPerson = $request->input('contact_name','');
        $contactMobile = $request->input('contact_mobile','');
        $contactEmail = $request->input('contact_email','');
        $managerPerson = $request->input('manager_name','');
        $managerMobile = $request->input('manager_mobile','');
        $managerEmail = $request->input('manager_email','');
        $firstReceiverPerson = $request->input('first_receiver_name', '');
        $firstReceiverMobile = $request->input('first_receiver_mobile', '');
        $firstReceiverEmail = $request->input('first_receiver_email', '');
        $secondReceiverPerson = $request->input('second_receiver_name', '');
        $secondReceiverMobile = $request->input('second_receiver_mobile', '');
        $secondReceiverEmail = $request->input('second_receiver_email', '');
        $createUserId = $request->input('create_user_id');
        $orgIds = Util::unserializeParams($request->input("org_ids"));
        $longitude = $request->input('longitude','');
        $latitude = $request->input('latitude','');
        try{
            WarehouseRepository::update($warehouseId, $name, $type, $factoryId, $city, $address,
                $contactPerson, $contactMobile, $contactEmail, $managerPerson, $managerMobile, $managerEmail,
                $firstReceiverPerson, $firstReceiverMobile, $firstReceiverEmail, $secondReceiverPerson, $secondReceiverMobile, $secondReceiverEmail,
                $createUserId,$orgIds, $longitude, $latitude);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse|\App\Http\Responses\MobileJsonResponse
     */
    public function updateStatus(Request $request){
        $this->validate($request, [
            'warehouse_id' => 'required|string',
            'status' => 'required|in:' . implode(',', array_keys(WarehouseConst::$status)),
            'update_user_id' => 'required|integer',
        ], [
            'warehouse_id.required' => trans('message.WAREHOUSE_ID').trans('message.NOT_EMPTY'),
            'warehouse_id.string' => trans('message.WAREHOUSE_ID').trans('message.INVALID'),
            'status.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"STATUS")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'update_user_id.required' => trans('message.UPDATE_USER').trans('message.NOT_EMPTY'),
            'update_user_id.integer' => trans('message.UPDATE_USER').trans('message.INVALID'),
        ]);
        $warehouseId = $request->input('warehouse_id');
        $status = $request->input('status');
        $updateUserId = $request->input('update_user_id');
        try{
            WarehouseRepository::updateStatus($warehouseId, $status, $updateUserId);
            return $this->jsonSuccess([]);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listByWarehouseIds(Request $request){
        $this->validate($request, [
            'warehouse_ids' => 'required',
            'status' => 'sometimes|in:' . implode(',', array_keys(WarehouseConst::$status)),
        ]);
        $params = $request->input();
        $params['warehouse_ids'] = Util::unserializeParams($params['warehouse_ids']);
        try{
            $warehouses = WarehouseRepository::listByWarehouseIds($params['warehouse_ids'], $params['status'] ?? null);
            return $this->jsonSuccess($warehouses);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getByWarehouseId(Request $request){
        $this->validate($request, [
            'warehouse_id' => 'required',
            'status' => 'sometimes|in:' . implode(',', array_keys(WarehouseConst::$status)),
        ]);
        $params = $request->input();
        try{
            $warehouse = WarehouseRepository::getByWarehouseId($params['warehouse_id'], $params['status'] ?? null);
            return $this->jsonSuccess($warehouse);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listByCityIds(Request $request) {
            $this->validate($request, [
                            'city_ids' => 'required|string',
            ]);
            $cityIds = Util::unserializeParams($request->input("city_ids"));
            try{
                    $warehouse = WarehouseRepository::getByCityIds($cityIds);
                    return $this->jsonSuccess($warehouse);
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
            'warehouse_ids' => 'json',
            'warehouse_id' => 'string',
            'name' => 'string|min:1|max:32',
            'city' => 'integer',
            'type' => 'json',
            'fields'=>'json',
            'status' => 'in:' . implode(',', array_keys(WarehouseConst::$status)),
            'create_user_id' => 'integer',
            'create_time_start' => 'integer',
            'create_time_end' => 'integer',
            'page' => 'integer',
            'perpage' => 'integer',
            'org_id' => 'sometimes|in:' . implode(',', array_keys(WarehouseConst::$org)),
        ], [
            'warehouse_ids.json' => trans('message.WAREHOUSE_ID').trans('message.INVALID'),
            'warehouse_id.string' => trans('message.WAREHOUSE_ID').trans('message.INVALID'),
            'name.string' => trans('message.WAREHOUSE_NAME').trans('message.INVALID'),
            'name.min' => trans('message.WAREHOUSE_NAME').trans('message.NEED_LESS_THAN',array("min"=>"1")),
            'name.max' => trans('message.WAREHOUSE_NAME').trans('message.NEED_MORE_THAN',array("max"=>"32")),
            'city.integer' => trans('message.CITY').trans('message.INVALID'),
            'type.in' => trans('message.WAREHOUSE_TYPR').trans('message.INVALID'),
            'fields.json' => trans("message.ATTRIBUTE_NEED_JSON",array("attribute"=>"fields")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'create_time_start.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_start")),
            'create_time_end.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_end")),
            'page.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"page")),
            'perpage.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"perpage")),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
        ]);
        $warehouseIds = Util::unserializeParams($request->input("warehouse_ids",''));
        $warehouseId = $request->input("warehouse_id",'');
        $name         = $request->input("name",'');
        $city         = $request->input("city",0);
        $type         = Util::unserializeParams($request->input("type",0));
        $fields       = Util::unserializeParams($request->input("fields"));
        $status       = $request->input("status",null);
        $createUserId = $request->input("create_user_id",0);
        $createTimeStart = $request->input("create_time_start",0);
        $createTimeEnd = $request->input("create_time_end",0);
        $page         = $request->input("page",1);
        $perPage      = $request->input("perpage",50);
        $orgId        = $request->input("org_id",0);
        try {
            $warehouses = WarehouseRepository::getList($warehouseIds,$warehouseId,$name,$city,
                    $type,$fields,$status,$createUserId,$createTimeStart,$createTimeEnd,$page,$perPage,$orgId);
            return $this->jsonSuccess($warehouses);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @return type
     */
    public function getWarehouseType() {
        try {
            $types = WarehouseRepository::getWarehouseType();
            return $this->jsonSuccess($types);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function listByFactoryIds(Request $request) {
        $this->validate($request, [
            'factory_ids' => 'required|json',
        ], [
            'factory_ids.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"factory_ids")),
            'factory_ids.json' => trans('message.FACTORY').trans('message.NEED_JSON'),
        ]);
        
         
        
        $factoryIds = Util::unserializeParams($request->input("factory_ids",''));
        try {
            $warehouses = WarehouseRepository::listByFactoryIds($factoryIds);
            return $this->jsonSuccess($warehouses);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
        
        
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getDetail(Request $request) {
        $this->validate($request, [
            'warehouse_id' => 'required',
            'status' => 'sometimes|in:' . implode(',', array_keys(WarehouseConst::$status)),
            'org_id' => 'sometimes|in:' . implode(',', array_keys(WarehouseConst::$org)),
        ], [
            'warehouse_id.required' => trans('message.WAREHOUSE_ID').trans('message.NOT_EMPTY'),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'org_id.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"ORG_ID")),
        ]);
        $warehouseId = $request->input("warehouse_id");
        $status = $request->input("status",null);
        $orgId  = $request->input("org_id",null);
        try {
            $warehouse = WarehouseRepository::getDetail($warehouseId,$status,$orgId);
            return $this->jsonSuccess($warehouse);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
        
    }

}
