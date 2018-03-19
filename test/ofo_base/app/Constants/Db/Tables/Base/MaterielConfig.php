<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class MaterielConfig{
    const TABLE = 'base_pn_material_config_common';
    const ID = 'id';
    const CODE = 'code';
    const NAME = 'name';
    const MTYPE = 'mtype';
    const STATUS = 'status';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';
    const CREATE_USER_ID = 'create_user_id';
    const UPDATE_USER_ID = 'update_user_id';
    const OFO_TIME = 'ofo_time';
    const IS_NOT_IMPORT = "is_not_import";
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    public static $status = [
        self::STATUS_ENABLED => '启用',
        self::STATUS_DISABLED => '禁用',
    ];
    const ORG_CAR_PARTS = 101;//海外车物料部件类型
    const LOCK_PARTS = 201;//锁物料部件类型
    const DXDT_CAR_PARTS = 301;//国内车物料部件类型
    const ELECTRIC_BIKE_PARTS=401;//电助力车部件类型
    public static $types = [
        self::ORG_CAR_PARTS=>"海外车物料部件类型",
        self::LOCK_PARTS=>"锁物料部件类型",
        self::DXDT_CAR_PARTS=>"国内车物料部件类型",
        self::ELECTRIC_BIKE_PARTS=>"电助力车部件类型",
        
    ];
    const IMPORT_TRUE = 0;//导入数据
    const IMPORT_FALSE = 1;//非导入数据
    static $import = [
        self::IMPORT_TRUE => "导入数据",
        self::IMPORT_FALSE => "非导入数据",
    ];
}
