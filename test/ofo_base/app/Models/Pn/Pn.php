<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Pn;

use App\Models\Base;
use App\Constants\Db\Tables\Base\Pn as PnConst;

class Pn extends Base {
    /**
     *
     */
    const CREATED_AT = PnConst::CREATE_TIME;
    const UPDATED_AT = PnConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = PnConst::TABLE;
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
        PnConst::ID,
        PnConst::PN_NO,
        PnConst::LEVEL,
        PnConst::MTYPE,
        PnConst::COUNTRY,
        PnConst::NAME,
        PnConst::COMPONENT,
        PnConst::CONTENT,
        PnConst::SERIAL_NO,
        PnConst::STYLE,
        PnConst::STATUS,
        PnConst::CREATE_TIME,
        PnConst::UPDATE_TIME,
        PnConst::CREATE_USER_ID,
        PnConst::UPDATE_USER_ID,
        PnConst::SUPPLIER_ID,
        PnConst::PN_TYPE,
        PnConst::NOTE,
        PnConst::IS_NOT_IMPORT,
        PnConst::PACKAGE_NAME,
        PnConst::ACCURACY_NAME,
    ];
    protected $hidden = [
        PnConst::OFO_TIME,
    ];
}