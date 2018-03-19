<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class ThrowWarehouse{
    const TABLE = 'throw_city_warehouse';

    const ID = 'id';
    const THROW_CITY_ID = 'throw_city_id';
    const WAREHOUSE_ID = 'warehouse_id';
    const STATUS = 'status';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    public static $status = [
        self::STATUS_ENABLED => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
}