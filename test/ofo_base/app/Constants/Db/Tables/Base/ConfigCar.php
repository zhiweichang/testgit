<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class ConfigCar{
    const TABLE = 'base_config_car';

    const ID = 'id';
    const CODE = 'code';
    const NAME = 'name';
    const CONTENT = 'content';
    const DETAIL = 'detail';
    const STATUS = 'status';
    const ORG_ID  = "org_id";
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
    const ORG_DXDT = 1;
    const ORG_HK = 2;
    public static $org =[
        self::ORG_DXDT=>"东峡大通",
        self::ORG_HK=>"OFO(HK)",
    ];
}
