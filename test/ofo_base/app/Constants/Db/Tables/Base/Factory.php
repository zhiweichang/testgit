<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class Factory{
    const TABLE = 'supplier_factory';

    const ID = 'id';
    const SUPPLIER_ID ='supplier_id';
    const FACTORY_ID ='factory_id';
    const NAME = 'name';
    const CITY_ID = 'city_id';
    const ADDRESS = 'address';
    const CONTRACT_USER_NAME = 'contract_user_name';
    const CONTRACT_USER_MOBILE = 'contract_user_mobile';
    const STATUS = 'status';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    public static $status = [
        self::STATUS_ENABLED => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
}