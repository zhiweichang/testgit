<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Throws;

use App\Models\Base;
use App\Constants\Db\Tables\Base\ThrowArea as ThrowAreaConst;

class ThrowArea extends Base {
    /**
     * @var string
     */
    protected $table = ThrowAreaConst::TABLE;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        ThrowAreaConst::ID,
        ThrowAreaConst::CITY_ID,
        ThrowAreaConst::NAME,
        ThrowAreaConst::CREATE_TIME,
    ];
}