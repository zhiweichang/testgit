<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class Warehouse{
    const TABLE = 'warehouse';

    const ID = 'id';
    const WAREHOUSE_ID = 'warehouse_id';
    const NAME = 'name';
    const TYPE = 'type';
    const CITY = 'city';
    const ADDRESS = 'address';
    const FACTORY_ID = 'factory_id';

    const CONTACT_PERSON = 'contact_person';
    const CONTACT_MOBILE = 'contact_mobile';
    const CONTACT_EMAIL = 'contact_email';

    const MANAGER_NAME = 'manager_name';
    const MANAGER_MOBILE = 'manager_mobile';
    const MANAGER_EMAIL = 'manager_email';

    const FIRST_RECEIVER_NAME = 'first_receiver_name';
    const FIRST_RECEIVER_MOBILE = 'first_receiver_mobile';
    const FIRST_RECEIVER_EMAIL = 'first_receiver_email';

    const SECOND_RECEIVER_NAME = 'second_receiver_name';
    const SECOND_RECEIVER_MOBILE = 'second_receiver_mobile';
    const SECOND_RECEIVER_EMAIL = 'second_receiver_email';

    const STATUS = 'status';
    const CREATE_USER_ID = 'create_user_id';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';
    const ORG_IDS = "org_ids";
    const LONGITUDE = 'longitude';
    const LATITUDE = 'latitude';

    /**
     * 仓库类型
     */
    const TYPE_PACKAGE = 10;  //组装厂
    const TYPE_REPAIR = 20;  //维修厂
    const TYPE_MAKE = 30;  //车厂
    const TYPE_REPLACEMENT = 40; //备件库
    const TYPE_CENTER = 50;  //中心仓
    const TYPE_LOCK = 60;
    const TYPE_PARTS = 70;
    /**
     * 状态
     */
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
    
    public static function types() {
        return array(
            self::TYPE_PACKAGE => trans("message.WAREHOUSE_TYPE_PACKAGE"),
            self::TYPE_REPAIR => trans("message.WAREHOUSE_TYPE_REPAIR"),
            self::TYPE_MAKE => trans("message.WAREHOUSE_TYPE_MAKE"),
            self::TYPE_REPLACEMENT => trans("message.WAREHOUSE_TYPE_REPLACEMENT"),
            self::TYPE_CENTER => trans("message.WAREHOUSE_TYPE_CENTER"),
            self::TYPE_LOCK => trans("message.WAREHOUSE_TYPE_LOCK"),
            self::TYPE_PARTS => trans("message.WAREHOUSE_TYPE_PARTS"),
        );
    }

}