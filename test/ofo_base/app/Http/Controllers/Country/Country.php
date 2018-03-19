<?php

namespace App\Http\Controllers\Country;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\Country as CountryRepository;
use App\Constants\Db\Tables\Base\Country as CountryConst;
use Exception;

class Country extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getCountryByName(Request $request){
        $this->validate($request, [
            'name' => 'required|string|min:1|max:64',
        ],[
            'name.required' => '国家名称不能为空',
            'name.string' => '国家名称无效',
            'name.min' => '国家名称最小长度不能低于:min个字',
            'name.max' => '国家名称最大长度不能超过:max个字',
        ]);
        $name    = trim($request->input("name"));
        
        try{
            $country = CountryRepository::getByName($name);
            return $this->jsonSuccess($country);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
}