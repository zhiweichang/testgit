<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class Bom{
    const TABLE = 'bom';

    const ID = 'id';
    const SKU_ID = 'sku_id';
    const STATUS = 'status';
    const CREATE_USER_ID = 'create_user_id';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';
    const OFO_TIME = 'ofo_time';
    const BOM_NAME = 'bom_name';
    const ORG_IDS  = "org_ids";

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