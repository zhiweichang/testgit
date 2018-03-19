<?php

namespace App\Http\Controllers\Throws;

use App\Libs\Util;
use Exception;
use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ThrowWarehouse as ThrowWarehouseRepository;
use App\Constants\Db\Tables\Base\ThrowWarehouse as ThrowWarehouseConst;

class ThrowWarehouse extends Controller{

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getByThrowCityId(Request $request){
        $this->validate($request, [
            'throw_city_id' => 'required',
            'status' => 'sometimes|in:' . implode(',', array_keys(ThrowWarehouseConst::$status)),
        ]);
        $params = $request->input();
        try{
            $throwCityWarehouses = ThrowWarehouseRepository::getByThrowCityId($params['throw_city_id'], $params['status'] ?? null);
            return $this->jsonSuccess($throwCityWarehouses);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse|\App\Http\Responses\MobileJsonResponse
     */
    public function listByThrowCityIds(Request $request){
        $this->validate($request, [
            'throw_city_ids' => 'required|json',
            'status' => 'sometimes|in:' . implode(',', array_keys(ThrowWarehouseConst::$status)),
        ],[
            'throw_city_ids.json' => ':attribute must be json',
        ]);
        $params = $request->input();
        $params['throw_city_ids'] = Util::unserializeParams($params['throw_city_ids']);
        try{
            $throwCityWarehouses = ThrowWarehouseRepository::listByThrowCityIds($params['throw_city_ids'], $params['status'] ?? null);
            return $this->jsonSuccess($throwCityWarehouses);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
}