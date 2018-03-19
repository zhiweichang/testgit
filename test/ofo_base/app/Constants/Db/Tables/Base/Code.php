<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class Code{
    const TABLE = 'code';

    const ID = 'id';
    const TYPE = 'type';
    const CODE_TYPE = 'code_type';
    const STATUS = 'status';
    const CREATE_USER_ID = 'create_user_id';
    const UPDATE_USER_ID = 'update_user_id';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';
    const OFO_TIME = 'ofo_time';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    public static $status = [
        self::STATUS_ENABLED => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
}