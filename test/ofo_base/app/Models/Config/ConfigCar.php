<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Config;

use App\Models\Base;
use App\Constants\Db\Tables\Base\ConfigCar as ConfigCarConst;

class ConfigCar extends Base {
    /**
     * @var string
     */
    protected $table = ConfigCarConst::TABLE;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        ConfigCarConst::ID,
        ConfigCarConst::CODE,
        ConfigCarConst::NAME,
        ConfigCarConst::CONTENT,
        ConfigCarConst::DETAIL,
        ConfigCarConst::STATUS,
        ConfigCarConst::ORG_ID,
        ConfigCarConst::CREATE_USER_ID,
        ConfigCarConst::UPDATE_USER_ID,
        ConfigCarConst::CREATE_TIME,
        ConfigCarConst::UPDATE_TIME,
    ];
    protected $hidden = [
        //'create_user_id',
        ConfigCarConst::OFO_TIME,
    ];
}