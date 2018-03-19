<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Factory;

use App\Constants\Db\Tables\Base\Factory as FactoryConst;
use App\Models\Base;

class Factory extends Base {
    /**
     *
     */
    const CREATED_AT = FactoryConst::CREATE_TIME;
    const UPDATED_AT = FactoryConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = FactoryConst::TABLE;
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
        FactoryConst::ID,
        FactoryConst::SUPPLIER_ID,
        FactoryConst::FACTORY_ID,
        FactoryConst::NAME,
        FactoryConst::CITY_ID,
        FactoryConst::ADDRESS,
        FactoryConst::CONTRACT_USER_NAME,
        FactoryConst::CONTRACT_USER_MOBILE,
        FactoryConst::STATUS,
        FactoryConst::CREATE_TIME,
        FactoryConst::UPDATE_TIME,
    ];
}