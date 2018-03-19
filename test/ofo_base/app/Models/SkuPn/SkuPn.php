<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\SkuPn;

use App\Constants\Db\Tables\Base\SkuPn as SkuPnConst;
use App\Models\Base;

class SkuPn extends Base {
    /**
     * @var string
     */
    protected $table = SkuPnConst::TABLE;
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * @var array
     */
    protected $fillable = [
        SkuPnConst::ID,
        SkuPnConst::SKU_ID,
        SkuPnConst::PN_ID,
    ];

    protected $hidden = [
    ];
}