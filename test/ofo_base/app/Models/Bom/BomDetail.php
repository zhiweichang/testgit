<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Bom;

use App\Models\Base;
use App\Constants\Db\Tables\Base\BomDetail as BomDetailConst;

class BomDetail extends Base {
    /**
     *
     */
    const CREATED_AT = BomDetailConst::CREATE_TIME;
    const UPDATED_AT = BomDetailConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = BomDetailConst::TABLE;
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
        BomDetailConst::ID,
        BomDetailConst::BOM_ID,
        BomDetailConst::SKU_ID,
        BomDetailConst::STATUS,
        BomDetailConst::NUM,
        BomDetailConst::DELIVERY_WAY,
        BomDetailConst::CREATE_TIME,
        BomDetailConst::UPDATE_TIME,
    ];

    protected $hidden = [
        BomDetailConst::OFO_TIME,
    ];
}
