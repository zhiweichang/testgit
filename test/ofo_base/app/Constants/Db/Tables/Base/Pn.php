<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class Pn{
    const TABLE = 'base_pn_common';
    const ID = 'id';
    const PN_NO = 'pn_no';
    const LEVEL = 'level';
    const MTYPE = 'mtype'; //物料分类
    const COUNTRY = 'country';
    const NAME = 'name';
    const COMPONENT = 'component';
    const CONTENT = 'content';
    const SERIAL_NO = 'serial_no';
    const STYLE = "style";
    const PACKAGE_NAME = "package_name";
    const ACCURACY_NAME = "accuracy_name";
    const STATUS = "status";
    const CREATE_TIME = "create_time";
    const UPDATE_TIME = "update_time";
    const CREATE_USER_ID = "create_user_id";
    const UPDATE_USER_ID = "update_user_id";
    const OFO_TIME = 'ofo_time';
    const SUPPLIER_ID = "supplier_id";
    const PN_TYPE = "pn_type";
    const NOTE = "note";
    const IS_NOT_IMPORT = "is_not_import";
    
    const TYPE_DXDT_ONLY_BIKE = 201;
    const TYPE_ORG_ONLY_BIKE  = 202;
    const TYPE_DXDT_LOCK = 203;
    const TYPE_ORG_LOCK  = 204;
    const TYPE_THREE_ELECTRIC = 205;
    const TYPE_ELECTRIC_MACHINE_LOCK = 206;
    const TYPE_ELECTRIC_BIKE = 207;
    const TYPE_DXDT_BIKE_PARTS = 301;
    const TYPE_ORG_BIKE_PARTS = 302;
    const TYPE_THREE_ELECTRIC_PARTS = 303;
    const TYPE_ELECTRIC_MACHINE_LOCK_PARTS = 304;
    const TYPE_ELECTRIC_BIKE_PARTS = 305;
    const TYPE_ELECTRIC_LOCK = 306;
    const TYPE_STRUCT_LOCK = 307;
    
    public static $names = [
        self::TYPE_DXDT_ONLY_BIKE => '国内裸车',
        self::TYPE_ORG_ONLY_BIKE => '海外裸车',
        self::TYPE_DXDT_LOCK => '国内整锁',
        self::TYPE_ORG_LOCK => '海外整锁',
        self::TYPE_THREE_ELECTRIC => '三电（电池、电机、中控）',
        self::TYPE_ELECTRIC_MACHINE_LOCK => '电机锁',
        self::TYPE_ELECTRIC_BIKE => '电助力裸车',
        self::TYPE_DXDT_BIKE_PARTS => '国内车配件',
        self::TYPE_ORG_BIKE_PARTS => '海外车配件',
        self::TYPE_THREE_ELECTRIC_PARTS => '三电配件（电池、电机、中控)',
        self::TYPE_ELECTRIC_MACHINE_LOCK_PARTS => '电机锁配件',
        self::TYPE_ELECTRIC_BIKE_PARTS => '电助力车配件',
        self::TYPE_ELECTRIC_LOCK => '电子锁配件',
        self::TYPE_STRUCT_LOCK => '结构锁配件',
    ];
    public static $codes = [
        self::TYPE_DXDT_ONLY_BIKE => 'N',
        self::TYPE_ORG_ONLY_BIKE => 'Y',
        self::TYPE_DXDT_LOCK => 'A',
        self::TYPE_ORG_LOCK => 'K',
        self::TYPE_THREE_ELECTRIC => 'D',
        self::TYPE_ELECTRIC_MACHINE_LOCK => 'L',
        self::TYPE_ELECTRIC_BIKE => 'P',
        self::TYPE_DXDT_BIKE_PARTS => 'F',
        self::TYPE_ORG_BIKE_PARTS => 'C',
        self::TYPE_THREE_ELECTRIC_PARTS => 'R',
        self::TYPE_ELECTRIC_MACHINE_LOCK_PARTS => 'S',
        self::TYPE_ELECTRIC_BIKE_PARTS => 'T',
        self::TYPE_ELECTRIC_LOCK => 'E',
        self::TYPE_STRUCT_LOCK => 'M',
    ];

    //PN状态
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    public static $status = [
        self::STATUS_ENABLED => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
    const COMMON_BIKE = 1; 
    const ELECTRONIC_BIKE = 2;
    static $pnTypes = [
        self::COMMON_BIKE => '自行车',
        self::ELECTRONIC_BIKE => '电助力车',
    ];
    
    const PN_LEVEL_SECOND = 2;
    const PN_LEVEL_THIRD  = 3;
    static $level = [
        self::PN_LEVEL_SECOND=>"二级",
        self::PN_LEVEL_THIRD=>"三级",
    ];
    
    const IMPORT_TRUE = 0;//导入数据
    const IMPORT_FALSE = 1;//非导入数据
    static $import = [
        self::IMPORT_TRUE => "导入数据",
        self::IMPORT_FALSE => "非导入数据",
    ];
}
