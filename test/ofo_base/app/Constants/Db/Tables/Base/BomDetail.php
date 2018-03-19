<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class BomDetail{
    const TABLE = 'bom_detail';

    const ID = 'id';
    const BOM_ID = 'bom_id';
    const SKU_ID = 'sku_id';
    const STATUS = 'status';
    const NUM = 'num';
    const DELIVERY_WAY = 'delivery_way';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';
    const OFO_TIME = 'ofo_time';


    //发料方式
    const DELIVERY_WAY_DIRECT = 10;
    const DELIVERY_WAY_SUPPLIER = 20;
    public static $deliveryWays = [
        self::DELIVERY_WAY_DIRECT => '客供领料', //直接领料
        self::DELIVERY_WAY_SUPPLIER => '供应商供料',
    ];

    //状态
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    public static $status = [
        self::STATUS_ENABLED => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
}