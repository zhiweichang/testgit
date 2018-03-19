<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Code;

use App\Constants\Db\Tables\Base\CodeSupplier as CodeSupplierConst;
use Carbon\Carbon;

class CodeSupplier extends Base {
    /**
     *
     */
    const CREATED_AT = CodeSupplierConst::CREATE_TIME;
    const UPDATED_AT = CodeSupplierConst::OFO_TIME;
    /**
     * @var string
     */
    protected $table = CodeSupplierConst::TABLE;
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
        CodeSupplierConst::ID,
        CodeSupplierConst::CODE_TYPE,
        CodeSupplierConst::CODE,
        CodeSupplierConst::SUPPLIER_ID,
        CodeSupplierConst::FACTORY_ID,
        CodeSupplierConst::CREATE_TIME,
        CodeSupplierConst::OFO_TIME,
    ];

    /**
     * @var array
     */
    protected $hidden = [
        CodeSupplierConst::OFO_TIME,
    ];

    /**
     * @param $value
     * @return string
     */
    public function setUpdatedAtAttribute($value) {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    }
}