<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class ThrowPoint{
    const TABLE = 'throw_point';

    const ID = 'id';
    const THROW_POINT_ID = 'throw_point_id'; //数据库中这里列都为0，居然没有用到...
    const CITY_ID = 'city_id';
    const THROW_AREA_ID = 'throw_area_id';
    const LONGITUDE = 'longtitude'; //Typo
    const LATITUDE = 'latitude';
    const ADDRESS = 'address';
    const TYPE = 'type';

    const CONTACT_USER_NAME = 'contract_user_name'; //Typo
    const CONTACT_USER_MOBILE = 'contract_user_mobile'; //Typo

    const STATUS = 'status';
    const CREATE_USER_ID = 'create_user_id';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';

    /**
     * 投放点类型
     */
    const TYPE_SUBWAY = 100;
    const TYPE_MALL = 200;
    const TYPE_WORK_SPACE = 300;
    const TYPE_SCHOOL = 400;
    const TYPE_UPTOWN = 500;
    const TYPE_SCENERY_AREA = 600;
    const TYPE_BUS_AREA = 700;
    const TYPE_HOSPITAL = 800;
    const TYPE_OTHER = 900;

    /**
     * 状态
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    public static $status = [
        self::STATUS_ENABLED => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
    
    public static function types() {
        return array(
            self::TYPE_SUBWAY => trans("message.TYPE_SUBWAY"),
            self::TYPE_MALL => trans("message.TYPE_MALL"),
            self::TYPE_WORK_SPACE => trans("message.TYPE_WORK_SPACE"),
            self::TYPE_SCHOOL => trans("message.TYPE_SCHOOL"),
            self::TYPE_UPTOWN => trans("message.TYPE_UPTOWN"),
            self::TYPE_SCENERY_AREA => trans("message.TYPE_SCENERY_AREA"),
            self::TYPE_BUS_AREA => trans("message.TYPE_SCENERY_AREA"),
            self::TYPE_HOSPITAL => trans("message.TYPE_HOSPITAL"),
            self::TYPE_OTHER => trans("message.TYPE_OTHER"),
        );
    }

}