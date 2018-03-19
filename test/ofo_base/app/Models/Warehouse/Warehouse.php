<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Warehouse;

use App\Models\Base;
use App\Constants\Db\Tables\Base\Warehouse as WarehouseConst;

class Warehouse extends Base {
    /**
     *
     */
    const CREATED_AT = WarehouseConst::CREATE_TIME;
    const UPDATED_AT = WarehouseConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = WarehouseConst::TABLE;
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
        WarehouseConst::ID,
        WarehouseConst::WAREHOUSE_ID,
        WarehouseConst::NAME,
        WarehouseConst::TYPE,
        WarehouseConst::FACTORY_ID,
        WarehouseConst::CITY,
        WarehouseConst::ADDRESS,
        WarehouseConst::CONTACT_PERSON,
        WarehouseConst::CONTACT_MOBILE,
        WarehouseConst::CONTACT_EMAIL,
        WarehouseConst::MANAGER_NAME,
        WarehouseConst::MANAGER_MOBILE,
        WarehouseConst::MANAGER_EMAIL,
        WarehouseConst::FIRST_RECEIVER_NAME,
        WarehouseConst::FIRST_RECEIVER_MOBILE,
        WarehouseConst::FIRST_RECEIVER_EMAIL,
        WarehouseConst::SECOND_RECEIVER_NAME,
        WarehouseConst::SECOND_RECEIVER_MOBILE,
        WarehouseConst::SECOND_RECEIVER_EMAIL,
        WarehouseConst::STATUS,
        WarehouseConst::CREATE_USER_ID,
        WarehouseConst::CREATE_TIME,
        WarehouseConst::UPDATE_TIME,
        WarehouseConst::ORG_IDS,
        WarehouseConst::LONGITUDE,
        WarehouseConst::LATITUDE,
    ];
}