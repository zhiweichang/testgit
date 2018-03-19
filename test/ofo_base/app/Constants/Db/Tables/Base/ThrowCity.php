<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class ThrowCity{
    const TABLE = 'throw_city';

    const ID = 'id';
    const CITY_ID = 'city_id';
    const PRIORITY = 'priority';
    const DURATION_TIME = 'duration_time'; //Typo
    const IS_AUTO = 'is_auto';
    const STATUS = 'status';
    const CREATE_USER_ID = 'create_user_id';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';
    const STOCK_DAYS = 'stock_days';

    /**
     * 是否自动分配投放计划单
     */
    const IS_AUTO_YES = 1;
    const IS_AUTO_NO = 2;

    /**
     * 状态
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    public static $status = [
        self::STATUS_ENABLED => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
}
