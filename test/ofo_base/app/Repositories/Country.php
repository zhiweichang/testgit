<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:16
 */

namespace App\Repositories;

use App\Libs\Util;
use Carbon\Carbon;
use Exception;
use App\Models\Country\Country as CountryModel;
use App\Constants\Db\Tables\Base\Country as CountryConst;
class Country {

    /**
     * @param type $code
     * @return type
     */
    public static function getByCode($code = "") {
        return CountryModel::where(CountryConst::CODE, $code)->first();
    }

    /**
     * 
     * @param type $codes
     * @return type
     */
    public static function getByCodes($codes = '') {
        return CountryModel::whereIn(CountryConst::CODE, $codes)->get()->toArray();
    }

    /**
     * 
     * @param type $name
     * @return type
     */
    public static function getByName($name = "") {
        return CountryModel::where(CountryConst::NAME, 'like', '%' . addslashes($name) . '%')->get()->toArray();
    }

}
