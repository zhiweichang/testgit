<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:25
 */
namespace App\Constants\Db\Tables\Base;

class Supplier{
    const TABLE = 'supplier';

    const ID = 'id';
    const SUPPLIER_ID ='supplier_id';
    const NAME = 'name';
    const SHORT_NAME = 'short_name';
    const IS_GENERAL_TAXPAYER = 'is_general_taxpayer';
    const RATE = 'rate';
    const BANK = 'bank';
    const BANK_CODE = 'bank_code';
    const CATEGORY_ID = 'category_id';
    const ACCOUNT = 'account';
    const STATUS = 'status';
    const CREATE_USER_ID = 'create_user_id';
    const CREATE_TIME = 'create_time';
    const UPDATE_TIME = 'update_time';
    const ORG_IDS = "org_ids";

    /**
     * 是否为一般纳税人
     */
    const IS_GENERAL_TAXPAYER_YES = 1;
    const IS_GENERAL_TAXPAYER_NO = 2;
    public static $isGeneralTaxpayer = [
        self::IS_GENERAL_TAXPAYER_YES => '是',
        self::IS_GENERAL_TAXPAYER_NO => '否',
    ];

    /**
     * 税率
     */
    const RATE_ZERO = 0;
    const RATE_THREE = 300;
    const RATE_SIX = 600;
    const RATE_ELEVEN = 1100;
    const RATE_SEVENTEEN = 1700;
    public static $rates = [
        self::RATE_ZERO => '0%',
        self::RATE_THREE => '3%',
        self::RATE_SIX => '6%',
        self::RATE_ELEVEN => '11%',
        self::RATE_SEVENTEEN => '17%',
    ];
    
    public static $categories = [
        '0101' => '供应链',
        '0102' => '劳务外包',
        '0103' => '办公仓储租赁',
        '0104' => '支付平台服务商',
        '0105' => '城市运营',
        '0106' => '市场营销',
        '0107' => '客服中心',
        '0108' => '短信数据',
        '0109' => '云服务',
        '0110' => '其他',
    ];

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
    
    public static function categories() {
        return array(
            '0101' => trans("message.SUPPLIER_0101"),
            '0102' => trans("message.SUPPLIER_0102"),
            '0103' => trans("message.SUPPLIER_0103"),
            '0104' => trans("message.SUPPLIER_0104"),
            '0105' => trans("message.SUPPLIER_0105"),
            '0106' => trans("message.SUPPLIER_0106"),
            '0107' => trans("message.SUPPLIER_0107"),
            '0108' => trans("message.SUPPLIER_0108"),
            '0109' => trans("message.SUPPLIER_0109"),
            '0110' => trans("message.SUPPLIER_0110"),
        );
    }

}