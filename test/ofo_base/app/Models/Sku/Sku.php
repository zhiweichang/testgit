<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Sku;

use App\Constants\Db\Tables\Base\Sku as SkuConst;
use App\Models\Base;

class Sku extends Base {
    /**
     *
     */
    const CREATED_AT = SkuConst::CREATE_TIME;
    const UPDATED_AT = SkuConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = SkuConst::TABLE;
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
        SkuConst::ID,
        SkuConst::SKU_ID,
        SkuConst::NAME,
        SkuConst::TYPE,
        SkuConst::FORMAT,
        SkuConst::WEIGHT,
        SkuConst::STATUS,
        SkuConst::CREATE_USER_ID,
        SkuConst::PRODUCT_TYPE,
        SkuConst::STOCK_TYPE,
        SkuConst::HARDWARE_VERSION,
        SkuConst::CREATE_TIME,
        SkuConst::UPDATE_TIME,
        SkuConst::ORG_IDS,

    ];

    protected $hidden = [
    ];

    public function setWeightAttribute($value){
        $this->attributes[SkuConst::WEIGHT] = intval(floatval($value) * 1000);
    }

    public function getWeightAttribute($weight){
        return $weight / 1000;
    }
}