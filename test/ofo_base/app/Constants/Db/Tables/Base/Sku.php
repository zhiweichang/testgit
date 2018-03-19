<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class Sku{
    const TABLE = 'sku';

    const ID = 'id';
    const SKU_ID = 'sku_id';
    const NAME = 'name';
    const TYPE = 'type'; //物料分类
    const FORMAT = 'format';
    const WEIGHT = 'weight';
    const STATUS = 'status';
    const CREATE_USER_ID = 'create_user_id';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';
    const PRODUCT_TYPE ='product_type'; //是ofo_sn库中code表
    const STOCK_TYPE = 'stock_type';
    const HARDWARE_VERSION = 'hardware_version';
    const ORG_IDS = "org_ids";

    //物料分类
    const TYPE_ONLY_BIKE = 10;  //裸车
    const TYPE_MECHANICAL_LOCK = 20;  //机械锁
    const TYPE_INTELLIGENT_LOCK = 30;  //智能锁
    const TYPE_MECHANICAL_BIKE = 40;  //机械锁整车
    const TYPE_INTELLIGENT_BIKE = 50;  //智能锁整车
    const TYPE_PARTS = 60;  //配件
    const TYPE_LOCK_PARTS = 70;//锁配件
    const TYPE_ONLY_LOCK = 80;//裸锁
    

    //库存类型
    const STOCK_TYPE_RAW_MATERIAL = 10;
    const STOCK_TYPE_SEMI_GOODS = 20;
    const STOCK_TYPE_FINISHED_GOODS = 30;

    //SKU状态
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
    /**
     * 
     * @return type
     */
    public static function types() {
        return array(
            self::TYPE_ONLY_BIKE => trans("message.TYPE_ONLY_BIKE"),
            self::TYPE_MECHANICAL_LOCK => trans("message.TYPE_MECHANICAL_LOCK"),
            self::TYPE_INTELLIGENT_LOCK => trans("message.TYPE_INTELLIGENT_LOCK"),
            self::TYPE_MECHANICAL_BIKE => trans("message.TYPE_MECHANICAL_BIKE"),
            self::TYPE_INTELLIGENT_BIKE => trans("message.TYPE_INTELLIGENT_BIKE"),
            self::TYPE_PARTS => trans("message.TYPE_PARTS"),
            self::TYPE_LOCK_PARTS => trans("message.TYPE_LOCK_PARTS"),
            self::TYPE_ONLY_LOCK => trans("message.TYPE_ONLY_LOCK"),
        );
    }
    /**
     * 
     * @return type
     */
    public static function stockTypes() {
        return array(
            self::STOCK_TYPE_RAW_MATERIAL => trans("message.STOCK_TYPE_RAW_MATERIAL"),
            self::STOCK_TYPE_SEMI_GOODS => trans("message.STOCK_TYPE_SEMI_GOODS"),
            self::STOCK_TYPE_FINISHED_GOODS => trans("message.STOCK_TYPE_FINISHED_GOODS"),
        );
    }
}
