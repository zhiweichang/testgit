<?php

namespace App\Http\Controllers\Throws;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\ThrowCity as ThrowCityRepository;
use App\Constants\Db\Tables\Base\ThrowCity as ThrowCityConst;
use Exception;

class ThrowCity extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function create(Request $request){
        $this->validate($request, [
            'city_id' => 'required|integer',
            'priority' => 'required|integer|max:10000',
            'duration_time' => 'required|integer',
            'create_user_id' => 'required|integer',
            'warehouse_ids' => 'required|json',
            'stock_days' => 'required|integer|max:10000',
        ], [
            'warehouse_ids.required' => trans('message.WAREHOUSE_ID').trans('message.NOT_EMPTY'),
            'warehouse_ids.json' => trans('message.WAREHOUSE_ID').trans('message.NEED_JSON'),
            'priority.required' => trans('message.WAREHOUSE_PRIRRTTY').trans('message.NOT_EMPTY'),
            'priority.integer' => trans('message.WAREHOUSE_PRIRRTTY').trans('message.INVALID'),
            'priority.max' => trans('message.WAREHOUSE_PRIRRTTY').trans('message.NEED_MORE_THAN_INT',array("max",10000)),
            'duration_time.integer' => trans('message.WAREHOUSE_DUEATION_TIME').trans('message.INVALID'),
            'city_id.required' =>  trans('message.CITY').trans('message.NOT_EMPTY'),
            'city_id.integer' => trans('message.CITY').trans('message.INVALID'),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
	    'stock_days.required' => trans('message.WAREHOUSE_STOCK_DAYS').trans('message.NOT_EMPTY'),
            'stock_days.integer' => trans('message.WAREHOUSE_STOCK_DAYS').trans('message.INVALID'),
            'stock_days.max' => trans('message.WAREHOUSE_STOCK_DAYS').trans('message.NEED_MORE_THAN_INT',array("max",10000)),
        ]);
        $cityId = $request->input('city_id','');
        $priority = $request->input('priority','');
        $durationTime = $request->input('duration_time','');
        $createUserId = $request->input('create_user_id','');
        $warehouseIds = Util::unserializeParams($request->input('warehouse_ids',''));
	$stockDays  = $request->input("stock_days",3);
        try{
            ThrowCityRepository::create($cityId, $priority, $durationTime, $createUserId, $warehouseIds,$stockDays);
            return $this->jsonSuccess();
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
            'city_id' => 'required|integer',
            'priority' => 'required|integer|max:10000',
            'duration_time' => 'required|integer',
            'create_user_id' => 'required|integer',
            'warehouse_ids' => 'required|json',
            'stock_days' => 'required|integer|max:10000',
        ], [
            'city_id.required' =>  trans('message.CITY').trans('message.NOT_EMPTY'),
            'city_id.integer' => trans('message.CITY').trans('message.INVALID'),
            'priority.required' => trans('message.WAREHOUSE_PRIRRTTY').trans('message.NOT_EMPTY'),
            'priority.integer' => trans('message.WAREHOUSE_PRIRRTTY').trans('message.INVALID'),
            'priority.max' => trans('message.WAREHOUSE_PRIRRTTY').trans('message.NEED_MORE_THAN_INT',array("max",10000)),
            'duration_time.integer' => trans('message.WAREHOUSE_DUEATION_TIME').trans('message.INVALID'),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'warehouse_ids.required' => trans('message.WAREHOUSE_ID').trans('message.NOT_EMPTY'),
            'warehouse_ids.json' => trans('message.WAREHOUSE_ID').trans('message.NEED_JSON'),
	    'stock_days.required' => trans('message.WAREHOUSE_STOCK_DAYS').trans('message.NOT_EMPTY'),
            'stock_days.integer' => trans('message.WAREHOUSE_STOCK_DAYS').trans('message.INVALID'),
            'stock_days.max' => trans('message.WAREHOUSE_STOCK_DAYS').trans('message.NEED_MORE_THAN_INT',array("max",10000)),
        ]);
        $cityId = $request->input('city_id','');
        $priority = $request->input('priority','');
        $durationTime = $request->input('duration_time','');
        $createUserId = $request->input('create_user_id','');
        $warehouseIds = Util::unserializeParams($request->input('warehouse_ids',''));
	$stockDays  = $request->input("stock_days",3);
        try{
            ThrowCityRepository::update($cityId, $priority, $durationTime, $createUserId, $warehouseIds,$stockDays);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    public function updateStatus(Request $request){
        $this->validate($request, [
            'city_id' => 'required|integer',
            'status' => 'required|in:' . implode(',', array_keys(ThrowCityConst::$status)),
            'create_user_id' => 'required|integer',
        ], [
            'city_id.required' =>  trans('message.CITY').trans('message.NOT_EMPTY'),
            'city_id.integer' => trans('message.CITY').trans('message.INVALID'),
            'status.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"STATUS")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
        ]);
        $cityId = $request->input('city_id');
        $status = $request->input('status');
        $createUserId = $request->input('create_user_id');
        try{
            ThrowCityRepository::updateStatus($cityId, $status, $createUserId);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listByThrowCityIds(Request $request){
        $this->validate($request, [
            'throw_city_ids' => 'required|string',
            'status' => 'sometimes|in:' . implode(',', array_keys(ThrowCityConst::$status)),
        ]);
        $params = $request->input();
        $params['throw_city_ids'] = Util::unserializeParams($params['throw_city_ids']);
        try{
            $throw_cities = ThrowCityRepository::listByThrowCityIds($params['throw_city_ids'], $params['status'] ?? null);
            return $this->jsonSuccess($throw_cities);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW);
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getByThrowCityId(Request $request){
        $this->validate($request, [
            'throw_city_id' => 'required',
            'status' => 'sometimes|in:' . implode(',', array_keys(ThrowCityConst::$status)),
        ]);
        $params = $request->input();
        try{
            $throw_city = ThrowCityRepository::getByThrowCityId($params['throw_city_id'], $params['status'] ?? null);
            return $this->jsonSuccess($throw_city);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getDetailByThrowCityId(Request $request) {
        $this->validate($request, [
            'city_id' => 'required',
        ]);
        $throwCityId = $request->input("city_id",'');
        try{
            $throwCity = ThrowCityRepository::getDetailByThrowCityId($throwCityId);
            return $this->jsonSuccess($throwCity);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    public function getDetail(Request $request) {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $id = $request->input("id",'');
        try{
            $throwCity = ThrowCityRepository::getDetailById($id);
            return $this->jsonSuccess($throwCity);
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
            'city_id' => 'integer',
            'city_ids' => 'json',
            'warehouse_id' => 'string',
            'status' => 'in:' . implode(',', array_keys(ThrowCityConst::$status)),
            'create_user_id' => 'integer',
            'create_time_start' => 'integer',
            'create_time_end' => 'integer',
            'page' => 'integer',
            'perpage' => 'integer',
        ], [
            'city_id.integer' => trans('message.CITY').trans('message.INVALID'),
            'city_ids.json' => trans('message.CITY').trans('message.INVALID'),
            'warehouse_id.string' => trans('message.WAREHOUSE_ID').trans('message.INVALID'),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
            'create_time_begin.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_start")),
            'create_time_end.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"create_time_end")),
            'page.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"page")),
            'perpage.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"perpage")),
        ]);

        $cityId = $request->input("city_id",null);
        $cityIds = Util::unserializeParams($request->input("city_ids",null));
        $warehouseId = $request->input("warehouse_id",null);
        $status = $request->input("status",null);
        $createUserId = $request->input("create_user_id",null);
        $createTimeStart = $request->input("create_time_start",null);
        $createTimeEnd  = $request->input("create_time_end",null);
        $page  = $request->input("page",1);
        $perPage = $request->input("perpage",50);

        try{
            $throwCitys = ThrowCityRepository::getList($cityId, $cityIds,$warehouseId,$status,$createUserId,$createTimeStart
                    ,$createTimeEnd,$page,$perPage);
            return $this->jsonSuccess($throwCitys);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }

    }

}
