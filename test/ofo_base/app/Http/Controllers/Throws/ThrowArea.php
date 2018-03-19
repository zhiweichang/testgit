<?php

namespace App\Http\Controllers\Throws;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\ThrowArea as ThrowAreaRepository;
use App\Constants\Db\Tables\Base\ThrowArea as ThrowAreaConst;
use Exception;

class ThrowArea extends Controller{

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function create(Request $request){
        $this->validate($request, [
            'city_id' => 'required|integer',
            'name' => 'required|string',
        ], [
            'city_id.required' =>  trans('message.CITY').trans('message.NOT_EMPTY'),
            'city_id.integer' => trans('message.CITY').trans('message.INVALID'),
            'name.required' => trans('message.AREA').trans('message.NOT_EMPTY'),
            'name.string' => trans('message.AREA').trans('message.INVALID'),
        ]);
        $cityId = $request->input('city_id','');
        $name = $request->input('name','');
        try{
            $throwArea = ThrowAreaRepository::create($cityId, $name);
            return $this->jsonSuccess(['throw_area_id' => $throwArea[ThrowAreaConst::ID]]);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listByThrowAreaIds(Request $request){
        $this->validate($request, [
            'throw_area_ids' => 'required|string',
        ]);
        $params = $request->input();
        $params['throw_area_ids'] = Util::unserializeParams($params['throw_area_ids']);
        try{
            $throw_areas = ThrowAreaRepository::listByThrowAreaIds($params['throw_area_ids']);
            return $this->jsonSuccess($throw_areas);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getByThrowAreaId(Request $request){
        $this->validate($request, [
            'throw_area_id' => 'required',
        ]);
        $params = $request->input();
        try{
            $throw_area = ThrowAreaRepository::getByThrowAreaId($params['throw_area_id']);
            return $this->jsonSuccess($throw_area);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getByCityId(Request $request) {
        $this->validate($request, [
            'city_id' => 'required',
        ]);
        $cityId = $request->input("city_id");
        try{
            $throwAreas = ThrowAreaRepository::getByCityId($cityId);
            return $this->jsonSuccess($throwAreas);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
}