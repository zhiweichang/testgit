<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\SkuFormat;

use App\Constants\Db\Tables\Base\SkuFormat as SkuFormatConst;
use App\Models\Base;

class SkuFormat extends Base {
    /**
     *
     */
    const CREATED_AT = SkuFormatConst::CREATE_TIME;
    const UPDATED_AT = SkuFormatConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = SkuFormatConst::TABLE;
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
        SkuFormatConst::ID,
        SkuFormatConst::TYPE,
        SkuFormatConst::VALUE,
        SkuFormatConst::STATUS,
        SkuFormatConst::CREATE_USER_ID,
        SkuFormatConst::CREATE_TIME,
        SkuFormatConst::UPDATE_TIME,
    ];

    protected $hidden = [
    ];
}