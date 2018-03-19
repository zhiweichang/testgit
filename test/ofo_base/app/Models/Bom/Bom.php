<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Bom;

use App\Models\Base;
use App\Constants\Db\Tables\Base\Bom as BomConst;

class Bom extends Base {
    /**
     *
     */
    const CREATED_AT = BomConst::CREATE_TIME;
    const UPDATED_AT = BomConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = BomConst::TABLE;
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
        BomConst::SKU_ID,
        BomConst::CREATE_USER_ID,
        BomConst::STATUS,
        BomConst::CREATE_TIME,
        BomConst::UPDATE_TIME,
        BomConst::BOM_NAME,
        BomConst::ORG_IDS,
    ];

    protected $hidden = [
        //'create_user_id',
        BomConst::OFO_TIME,
    ];
}
