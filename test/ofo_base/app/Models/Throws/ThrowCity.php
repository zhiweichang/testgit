<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Throws;

use App\Models\Base;
use App\Constants\Db\Tables\Base\ThrowCity as ThrowCityConst;

class ThrowCity extends Base {
    /**
     *
     */
    const CREATED_AT = ThrowCityConst::CREATE_TIME;
    const UPDATED_AT = ThrowCityConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = ThrowCityConst::TABLE;
    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * @var array
     */
    protected $fillable = [
        ThrowCityConst::ID,
        ThrowCityConst::CITY_ID,
        ThrowCityConst::PRIORITY,
        ThrowCityConst::DURATION_TIME,
        ThrowCityConst::IS_AUTO,
        ThrowCityConst::STATUS,
        ThrowCityConst::CREATE_USER_ID,
        ThrowCityConst::CREATE_TIME,
        ThrowCityConst::UPDATE_TIME,
        ThrowCityConst::STOCK_DAYS,
    ];

    /**
     * 小时转分钟
     * @param $value
     */
    public function setDurationTimeAttribute($value){
        $this->attributes[ThrowCityConst::DURATION_TIME] = $value * 60;
    }

    /**
     * 分钟转小时
     * @param $value
     * @return float
     */
    public function getDurationTimeAttribute($value){
        return round($value / 60, 2);
    }
}
