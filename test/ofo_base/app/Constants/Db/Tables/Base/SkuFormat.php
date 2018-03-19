<?php
/**
 * 作者:杜建强
 */
namespace App\Constants\Db\Tables\Base;

class SkuFormat{
    const TABLE = 'sku_format';

    const ID = 'id';
    const TYPE = 'type';
    const VALUE = 'value';
    const STATUS = 'status';
    const CREATE_USER_ID = 'create_user_id';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';
    
    //SKU状态
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    public static $status = [
        self::STATUS_ENABLED => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
}