<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Throws;

use App\Models\Base;
use App\Constants\Db\Tables\Base\ThrowPoint as ThrowPointConst;

class ThrowPoint extends Base {
    /**
     *
     */
    const CREATED_AT = ThrowPointConst::CREATE_TIME;
    const UPDATED_AT = ThrowPointConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = ThrowPointConst::TABLE;
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
        ThrowPointConst::ID,
        ThrowPointConst::CITY_ID,
        ThrowPointConst::THROW_AREA_ID,
        ThrowPointConst::LONGITUDE,
        ThrowPointConst::LATITUDE,
        ThrowPointConst::ADDRESS,
        ThrowPointConst::TYPE,
        ThrowPointConst::CONTACT_USER_NAME,
        ThrowPointConst::CONTACT_USER_MOBILE,
        ThrowPointConst::STATUS,
        ThrowPointConst::CREATE_USER_ID,
        ThrowPointConst::CREATE_TIME,
        ThrowPointConst::UPDATE_TIME,
    ];
}