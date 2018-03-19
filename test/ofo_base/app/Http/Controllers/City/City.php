<?php

namespace App\Http\Controllers\City;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\City as CityRepository;
use Exception;

class City extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listByCityIds(Request $request){
        $this->validate($request, [
            'city_ids' => 'required|json',
        ]);
        $cityIds = $request->input('city_ids');
        $cityIds = Util::unserializeParams($cityIds);
        $orgId   = $request->input("org_id",0);
        try{
            $cities = CityRepository::listByCityIds($cityIds,$orgId);
            return $this->jsonSuccess($cities);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    /**
     * 获取城市列表
     * @param \App\Http\Controllers\City\Requests $request
     * @return type
     */
    public function getList(Request $request) {
        $orgId = $request->input("org_id",0);
        $cityName = $request->input("city_name",'');
        $cityCodes = $request->input("city_codes",'');
        $page     = $request->input("page",1);
        $perPage  = $request->input("perpage",50);
        $isShort  = $request->input("is_short",0);
        $flag     = $request->input("flag",1);
        try {
            $cities = CityRepository::getList($cityName,$orgId,$cityCodes,$isShort,$page,$perPage,$flag);
            return $this->jsonSuccess($cities);
        } catch (Exception $e) {
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listByCityNames(Request $request){
        $this->validate($request, [
            'city_names' => 'required|json',
        ]);
        $orgId = $request->input("org_id",0);
        $cityNames = Util::unserializeParams($request->input('city_names'));
        $isShort  = $request->input("is_short",0);     
        try{
            $cities = CityRepository::listByCityNames($orgId,$cityNames,$isShort);
            return $this->jsonSuccess($cities);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    
    public function listByCityCode( Request $request ){
        $this->validate($request, [
            'city_codes' => 'required|json',
        ]);
        $orgId = $request->input("org_id",0);
        $cityCodes = Util::unserializeParams($request->input('city_codes'));    
        try{
            $cities = CityRepository::listByCityCodes($orgId,$cityCodes);
            return $this->jsonSuccess($cities);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    
}