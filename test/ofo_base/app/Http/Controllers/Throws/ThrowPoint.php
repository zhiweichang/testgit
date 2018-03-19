<?php

namespace App\Http\Controllers\Throws;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\ThrowPoint as ThrowPointRepository;
use App\Constants\Db\Tables\Base\ThrowPoint as ThrowPointConst;
use Exception;

class ThrowPoint extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function create(Request $request){
        $this->validate($request, [
            'city_id' => 'required|integer',
            'longitude' => 'required|string|max:32',
            'latitude' => 'required|string|max:32',
            'address' => 'required|string|max:128',
            'throw_area_id' => 'required|integer',
            'type' => 'required|in:' . implode(',', array_keys(ThrowPointConst::types())),
            'contract_user_name' => 'required|string|max:32',
            'contract_user_mobile' => 'required|string|org_mobile|max:20',
            'create_user_id' => 'required|integer',
        ], [
            'city_id.required' =>  trans('message.CITY').trans('message.NOT_EMPTY'),
            'city_id.integer' => trans('message.CITY').trans('message.INVALID'),
            'longitude.required' => trans('message.LONGITUDE').trans('message.NOT_EMPTY'),
            'longitude.string' => trans('message.LONGITUDE').trans('message.INVALID'),
            'longitude.max' => trans('message.LONGITUDE').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'latitude.required' => trans('message.LATITUDE').trans('message.NOT_EMPTY'),
            'latitude.string' => trans('message.LATITUDE').trans('message.INVALID'),
            'latitude.max' => trans('message.LATITUDE').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'address.required' => trans('message.ADDRESS').trans('message.NOT_EMPTY'),
            'address.string' => trans('message.ADDRESS').trans('message.INVALID'),
            'address.max' => trans('message.ADDRESS').trans('message.NEED_MORE_THAN',array("min"=>"128")),
            'throw_area_id.required' => trans('message.THROW_AREA').trans('message.NOT_EMPTY'),
            'throw_area_id.integer' => trans('message.THROW_AREA').trans('message.INVALID'),
            'type.required' => trans('message.TYPE').trans('message.NOT_EMPTY'),
            'type.in' => trans('message.TYPE').trans('message.INVALID'),
            'contract_user_name.required' => trans('message.CONTRACT_USER_NAME').trans('message.NOT_EMPTY'),
            'contract_user_name.string' => trans('message.CONTRACT_USER_NAME').trans('message.INVALID'),
            'contract_user_name.max' => trans('message.CONTRACT_USER_NAME').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'contract_user_mobile.required' => trans('message.CONTRACT_PHONE').trans('message.NOT_EMPTY'),
            'contract_user_mobile.string' => trans('message.CONTRACT_PHONE').trans('message.INVALID'),
            'contract_user_mobile.org_mobile' => trans('message.CONTRACT_PHONE').trans('message.INVALID'),
            'contract_user_mobile.max' => trans('message.CONTRACT_PHONE').trans('message.NEED_MORE_THAN',array("min"=>"20")),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
        ]);
        $cityId = $request->input('city_id','');
        $longitude = $request->input('longitude','');
        $latitude = $request->input('latitude','');
        $address = $request->input('address','');
        $throwAreaId = $request->input('throw_area_id','');
        $type = $request->input('type','');
        $contractUserName = $request->input('contract_user_name','');
        $contractUserMobile = $request->input('contract_user_mobile','');
        $createUserId = $request->input('create_user_id','');
        try{
            $throwPoint = ThrowPointRepository::create($cityId, $longitude, $latitude, $address,
                $throwAreaId, $type, $contractUserName, $contractUserMobile, $createUserId);
            return $this->jsonSuccess(['throw_point_id' => $throwPoint[ThrowPointConst::ID]]);
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
            'throw_point_id' => 'required|integer',
            'city_id' => 'required|integer',
            'longitude' => 'required|string|max:32',
            'latitude' => 'required|string|max:32',
            'address' => 'required|string|max:128',
            'throw_area_id' => 'required|integer',
            'type' => 'required|in:' . implode(',', array_keys(ThrowPointConst::types())),
            'contract_user_name' => 'required|string|max:32',
            'contract_user_mobile' => 'required|string|org_mobile|max:20',
            'update_user_id' => 'required|integer',
        ], [
            'throw_point_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"throw_point_id")),
            'throw_point_id.integer' => trans('message.ATTRIBUTE_INVALID',array("attribute"=>"throw_point_id")),
            'city_id.required' =>  trans('message.CITY').trans('message.NOT_EMPTY'),
            'city_id.integer' => trans('message.CITY').trans('message.INVALID'),
            'longitude.required' => trans('message.LONGITUDE').trans('message.NOT_EMPTY'),
            'longitude.string' => trans('message.LONGITUDE').trans('message.INVALID'),
            'longitude.max' => trans('message.LONGITUDE').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'latitude.required' => trans('message.LATITUDE').trans('message.NOT_EMPTY'),
            'latitude.string' => trans('message.LATITUDE').trans('message.INVALID'),
            'latitude.max' => trans('message.LATITUDE').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'address.required' => trans('message.ADDRESS').trans('message.NOT_EMPTY'),
            'address.string' => trans('message.ADDRESS').trans('message.INVALID'),
            'address.max' => trans('message.ADDRESS').trans('message.NEED_MORE_THAN',array("min"=>"128")),
            'throw_area_id.required' => trans('message.THROW_AREA').trans('message.NOT_EMPTY'),
            'throw_area_id.integer' => trans('message.THROW_AREA').trans('message.INVALID'),
            'type.required' => trans('message.TYPE').trans('message.NOT_EMPTY'),
            'type.in' => trans('message.TYPE').trans('message.INVALID'),
            'contract_user_name.required' => trans('message.CONTRACT_USER_NAME').trans('message.NOT_EMPTY'),
            'contract_user_name.string' => trans('message.CONTRACT_USER_NAME').trans('message.INVALID'),
            'contract_user_name.max' => trans('message.CONTRACT_USER_NAME').trans('message.NEED_MORE_THAN',array("min"=>"32")),
            'contract_user_mobile.required' => trans('message.CONTRACT_PHONE').trans('message.NOT_EMPTY'),
            'contract_user_mobile.string' => trans('message.CONTRACT_PHONE').trans('message.INVALID'),
            'contract_user_mobile.org_mobile' => trans('message.CONTRACT_PHONE').trans('message.INVALID'),
            'contract_user_mobile.max' => trans('message.CONTRACT_PHONE').trans('message.NEED_MORE_THAN',array("min"=>"20")),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
        ]);
        $throwPointId = $request->input('throw_point_id','');
        $cityId = $request->input('city_id','');
        $longitude = $request->input('longitude','');
        $latitude = $request->input('latitude','');
        $address = $request->input('address','');
        $throwAreaId = $request->input('throw_area_id','');
        $type = $request->input('type','');
        $contractUserName = $request->input('contract_user_name','');
        $contractUserMobile = $request->input('contract_user_mobile','');
        $updateUserId = $request->input('update_user_id',0);
        try{
            ThrowPointRepository::update($throwPointId, $cityId, $longitude, $latitude, $address,
                $throwAreaId, $type, $contractUserName, $contractUserMobile, $updateUserId);
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
            'throw_point_id' => 'required|integer',
            'status' => 'required|in:' . implode(',', array_keys(ThrowPointConst::$status)),
            'update_user_id' => 'required|integer',
        ], [
            'throw_point_id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"throw_point_id")),
            'throw_point_id.integer' => trans('message.ATTRIBUTE_INVALID',array("attribute"=>"throw_point_id")),
            'status.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"STATUS")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'update_user_id.required' => trans('message.UPDATE_USER').trans('message.NOT_EMPTY'),
            'update_user_id.integer' => trans('message.UPDATE_USER').trans('message.INVALID'),
        ]);
        $throwPointId = $request->input('throw_point_id');
        $status = $request->input('status');
        $updateUserId = $request->input('update_user_id');
        try{
            ThrowPointRepository::updateStatus($throwPointId, $status, $updateUserId);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listByThrowPointIds(Request $request){
        $this->validate($request, [
            'throw_point_ids' => 'required|string',
            'status' => 'sometimes|in:' . implode(',', array_keys(ThrowPointConst::$status)),
        ]);
        $params = $request->input();
        $params['throw_point_ids'] = Util::unserializeParams($params['throw_point_ids']);
        try{
            $throw_points = ThrowPointRepository::listByThrowPointIds($params['throw_point_ids'], $params['status'] ?? null);
            return $this->jsonSuccess($throw_points);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse|\App\Http\Responses\MobileJsonResponse
     */
    public function listByThrowPointAddresses(Request $request){
        $this->validate($request, [
            'throw_point_addresses' => 'required|json',
            'status' => 'sometimes|in:' . implode(',', array_keys(ThrowPointConst::$status)),
        ]);
        $params = $request->input();
        $params['throw_point_addresses'] = Util::unserializeParams($params['throw_point_addresses']);
        try{
            $throw_points = ThrowPointRepository::listByThrowPointAddresses($params['throw_point_addresses'], $params['status'] ?? null);
            return $this->jsonSuccess($throw_points);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW);
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse|\App\Http\Responses\MobileJsonResponse
     */
    public function listByThrowCityId(Request $request){
        $this->validate($request, [
            'throw_city_id' => 'required|string',
            'status' => 'sometimes|in:' . implode(',', array_keys(ThrowPointConst::$status)),
        ]);
        $params = $request->input();
        try{
            $throw_points = ThrowPointRepository::listByThrowCityId($params['throw_city_id'], $params['status'] ?? null);
            return $this->jsonSuccess($throw_points);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getByThrowPointId(Request $request){
        $this->validate($request, [
            'throw_point_id' => 'required',
            'status' => 'sometimes|in:' . implode(',', array_keys(ThrowPointConst::$status)),
        ]);
        $params = $request->input();
        try{
            $throw_point = ThrowPointRepository::getByThrowPointId($params['throw_point_id'], $params['status'] ?? null);
            return $this->jsonSuccess($throw_point);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    public function getList(Request $request) {
        $this->validate($request, [
            'id' => 'integer',
            'address'=>'string',
            'city_ids' => 'json',
            'type' => 'integer',
            'status' => 'in:' . implode(',', array_keys(ThrowPointConst::$status)),
            'create_user_id' => 'integer',
            'throw_area_id' => 'integer',
            'create_time_start' => 'integer',
            'create_time_end' => 'integer',
            'page' => 'integer',
            'perpage' => 'integer',
        ], [
            'id.required' => trans('message.ATTRIBUTE_INVALID',array("attribute"=>"id")),
            'address.string'=>trans('message.ADDRESS').trans('message.INVALID'),
            'city_ids.json' => trans('message.CITY').trans('message.NEED_JSON'),
            'type.integer' => trans('message.TYPE').trans('message.INVALID'),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'throw_area_id.integer' => trans('message.THROW_AREA').trans('message.INVALID'),
            'create_time_begin.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_start")),
            'create_time_end.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_end")),
            'page.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"page")),
            'perpage.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"perpage")),
        ]);
        $id = $request->input("id",null);
        $address = $request->input("address",null);
        $cityIds = Util::unserializeParams($request->input("city_ids",null));
        $type  = $request->input("type",null);
        $status = $request->input("status",null);
        $createUserId = $request->input("create_user_id",null);
        $throwAreaId = $request->input("throw_area_id",null);
        $createTimeStart = $request->input("create_time_start",null);
        $createTimeEnd = $request->input("create_time_end",null);
        $page = $request->input("page",1);
        $perPage = $request->input("perpage",50);
        try{
            $throwPoints = ThrowPointRepository::getList($id,$address,$cityIds,$type,$status,$createUserId,$throwAreaId
                    ,$createTimeStart,$createTimeEnd,$page,$perPage);
            return $this->jsonSuccess($throwPoints);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
}