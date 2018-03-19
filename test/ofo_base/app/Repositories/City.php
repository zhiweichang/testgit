<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:16
 */

namespace App\Repositories;

use Ofo\Utils\Request\Facades\OfoRequest;

class City{
    /**
     * @param array $cityIds
     * @param null $status
     * @return mixed
     */
    public static function listByCityIds(array $cityIds, $orgId){
        $config = config('soa.noah');
        $url = "http://{$config['host']}:{$config['port']}/noah/v1/city/get_by_city_codes";
        $params = [
            'city_codes' => $cityIds,
        ];
        $ret = OfoRequest::get($url, $params);
        $arr = (array) json_decode($ret, true);
        $cities = array_get($arr, 'info');
        return $cities;
    }
    
    public static function getList($cityName,$orgId,$cityCodes,$isShort,$page,$perPage,$flag=1){
        if($orgId == 1 ) {
            $config = config('soa.noah');
            $url = "http://{$config['host']}:{$config['port']}/noah/v1/city/get_list";
            $params = [
                "short_name"=>$cityName,
                "city_codes" =>$cityCodes,
                "page"=>$page,
                "perpage" =>$perPage,
            ];
            $ret = OfoRequest::post($url, $params);
            $arr = (array) json_decode($ret, true);
            $citiesCn = array_get($arr, 'info');
            $cities = array();
            if($citiesCn["list"]) {
                foreach ($citiesCn["list"] as $k=>$v ) {
                    $cities[$k]["city_code"] = $v["city_code"];
                    $cities[$k]["short_name"] = $v["short_name"];
                    $cities[$k]["country_id"] = '270056';
                    $cities[$k]["country"] = trans("message.CHINA");
                }
            }
        } else {
            $config = config('soa.noahintl');
            if(!$isShort) {
                $url = "http://{$config['host']}:{$config['port']}/ofocms/v1/city/getCityInfoByCityName";
                $params["cityName"] = $cityName;
            } else {
                $url = "http://{$config['host']}:{$config['port']}/ofocms/v1/city/getCityInfoByCityShortName";
                $params["cityShortName"] = $cityName;
            }
            $params["flag"] = strval($flag);
            
            $ret = OfoRequest::post($url, $params);
            $arr = (array) json_decode($ret, true);
            $orgCities = array_get($arr, 'data');
            $cities = array();
            if (!empty($orgCities)) {
                foreach ($orgCities as $k => $v) {
                    if (!empty($cityCodes)) {
                        if (in_array($v["cityId"], $cityCodes)) {
                            $cities[$k]["city_code"] = $v["cityId"];
                            $cities[$k]["short_name"] = $v["city"];
                            $cities[$k]["country_id"] = $v["countryId"];
                            $cities[$k]["country"] = $v["country"];
                        }
                    } else {
                        $cities[$k]["city_code"] = $v["cityId"];
                        $cities[$k]["short_name"] = $v["city"];
                        $cities[$k]["country_id"] = $v["countryId"];
                        $cities[$k]["country"] = $v["country"];
                    }
                }
            }
        }
	sort($cities);
        
        return array("list"=>$cities);
    }
    
    /**
     * @param array $cityIds
     * @param null $status
     * @return mixed
     */
    public static function listByCityNames($orgId,$cityNames,$isShort){
        if( $orgId == 1 ) {
            $config = config('soa.noah');
            $url = "http://{$config['host']}:{$config['port']}/noah/v1/city/get_by_city_names";
            $params = [
                "city_names"=>$cityNames,
                "is_short" =>$isShort,
            ];
            $ret = OfoRequest::post($url, $params);
            $arr = (array) json_decode($ret, true);
            $citiesCn = array_get($arr, 'info');
            $cities = array();
            if($citiesCn) {
                foreach ($citiesCn as $k=>$v ) {
                    $cities[$k]["city_code"] = $v["city_code"];
                    $cities[$k]["short_name"] = $v["short_name"];
                    $cities[$k]["city"] = $v["city"];
                }
            }
        } else {
            $config = config('soa.noahintl');
            if(!$isShort) {
                $url = "http://{$config['host']}:{$config['port']}/ofocms/v1/city/getCityInfoByCityNames";
                $params["cityNames"] = json_encode($cityNames);
            } else {
                $url = "http://{$config['host']}:{$config['port']}/ofocms/v1/city/getCityInfoByCityShortNames";
                $params["cityShortNames"] = json_encode($cityNames);
            }
            $ret = OfoRequest::post($url, $params);
            $arr = (array) json_decode($ret, true);
            $orgCities = array_get($arr, 'data');
            $cities = array();
            if(!empty($orgCities)) {
                foreach( $orgCities as $k=>$v ) {
                    $cities[$k]["city_code"] = $v["cityId"];
                    $cities[$k]["short_name"] = $v["city"];
                    $cities[$k]["city"] = $v["city"];
                }
            }
        }
        return $cities;
        
    }
    
    public static function listByCityCodes($orgId,$cityCodes){
        $cityCodes=!empty($cityCodes)?array_values(array_unique($cityCodes)):array();
        
        if ($orgId == 1) {
            $config = config('soa.noah');
            $url = "http://{$config['host']}:{$config['port']}/noah/v1/city/get_by_city_codes";
            $params = [
                "city_codes" => $cityCodes,
            ];
            $ret = OfoRequest::get($url, $params);
            $arr = (array) json_decode($ret, true);
            $citiesCn = array_get($arr, 'info');
            $cities = array();
            if ($citiesCn) {
                foreach ($citiesCn as $k => $v) {
                    $cities[$k]["city_code"] = $v["city_code"];
                    $cities[$k]["short_name"] = $v["short_name"];
                    $cities[$k]["city"] = $v["city"];
                }
            }
        } else {
            $config = config('soa.noahintl');
            $url = "http://{$config['host']}:{$config['port']}/ofocms/v1/city/getCityInfoByCityIds";
            $params["cityIds"] = json_encode($cityCodes);
            $ret = OfoRequest::post($url, $params);
            $arr = (array) json_decode($ret, true);
            $orgCities = array_get($arr, 'data');
            $cities = array();
            if (!empty($orgCities)) {
                foreach ($orgCities as $k => $v) {
                    $cities[$k]["city_code"] = $v["cityId"];
                    $cities[$k]["short_name"] = $v["city"];
                    $cities[$k]["city"] = $v["city"];
                }
            }
        }
        return $cities;
    }
}


 
