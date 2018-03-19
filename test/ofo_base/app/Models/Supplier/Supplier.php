<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Supplier;

use App\Constants\Db\Tables\Base\Supplier as SupplierConst;
use App\Models\Base;

class Supplier extends Base {
    /**
     *
     */
    const CREATED_AT = SupplierConst::CREATE_TIME;
    const UPDATED_AT = SupplierConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = SupplierConst::TABLE;
    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * @var array
     */
    protected $fillable = [
        SupplierConst::ID,
        SupplierConst::SUPPLIER_ID,
        SupplierConst::NAME,
        SupplierConst::SHORT_NAME,
        SupplierConst::IS_GENERAL_TAXPAYER,
        SupplierConst::RATE,
        SupplierConst::BANK,
        SupplierConst::BANK_CODE,
        SupplierConst::CATEGORY_ID,
        SupplierConst::ACCOUNT,
        SupplierConst::CREATE_USER_ID,
        SupplierConst::STATUS,
        SupplierConst::CREATE_TIME,
        SupplierConst::UPDATE_TIME,
        SupplierConst::ORG_IDS,
    ];
}