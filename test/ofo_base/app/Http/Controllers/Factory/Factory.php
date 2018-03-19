<?php

namespace App\Http\Controllers\Factory;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\Factory as FactoryRepository;
use App\Constants\Db\Tables\Base\Factory as FactoryConst;
use Exception;

class Factory extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listByFactoryIds(Request $request){
        $this->validate($request, [
            'factory_ids' => 'required|json',
            'status' => 'sometimes|in:' . implode(',', array_keys(FactoryConst::$status)),
        ]);
        $params = $request->input();
        $params['factory_ids'] = Util::unserializeParams($params['factory_ids']);
        try{
            $factories = FactoryRepository::listByFactoryIds($params['factory_ids']);
            return $this->jsonSuccess($factories);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getByFactoryId(Request $request){
        $this->validate($request, [
            'factory_id' => 'required',
            'status' => 'sometimes|in:' . implode(',', array_keys(FactoryConst::$status)),
        ]);
        $params = $request->input();
        try{
            $factory = FactoryRepository::getByFactoryId($params['factory_id'], $params['status'] ?? null);
            return $this->jsonSuccess($factory);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    public function getList(Request $request) {
        $this->validate($request, [
            'supplier_id' => 'string',
            'factory_id' => 'string',
            'name'=>'string',
            'city_id'=>'string',
            'status' => 'sometimes|in:' . implode(',', array_keys(FactoryConst::$status)),
            'page' => 'integer',
            'perpage'=>'integer',
        ]);
        $supplierId = $request->input("supplier_id",null);
        $factoryId  = $request->input("factory_id",null);
        $name   = $request->input("name",null);
        $cityId = $request->input("city_id",null);
        $status = $request->input("status",null);
        $page   = $request->input("page",1);
        $perPage = $request->input("perpage",50);
        try{
            $factorys = FactoryRepository::getList($supplierId, $factoryId,$name,$cityId,$status,$page,$perPage);
            return $this->jsonSuccess($factorys);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getBySupplierId(Request $request) {
        $this->validate($request, [
            'supplier_id' => 'required',
                ], [
            'supplier_id.required' => trans('message.SUPPLIER_ID').trans('message.NOT_EMPTY'),
        ]);
        $supplierId = $request->input('supplier_id');
        try{
            $factorys = FactoryRepository::getBySupplierId($supplierId);
            return $this->jsonSuccess($factorys);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function listByCityIds(Request $request) {
        $this->validate($request, [
            'city_ids' => 'required|json',
        ]);
        $cityIds = Util::unserializeParams($request->input("city_ids"));
        try {
            $factories = FactoryRepository::listByCityIds($cityIds);
            return $this->jsonSuccess($factories);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

}