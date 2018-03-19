<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Code;

use App\Constants\Db\Tables\Base\Code as CodeConst;

class Code extends Base {
    /**
     *
     */
    const CREATED_AT = CodeConst::CREATE_TIME;
    const UPDATED_AT = CodeConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = CodeConst::TABLE;
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
        CodeConst::ID,
        CodeConst::TYPE,
        CodeConst::CODE_TYPE,
        CodeConst::STATUS,
        CodeConst::CREATE_USER_ID,
        CodeConst::UPDATE_USER_ID,
        CodeConst::CREATE_TIME,
        CodeConst::UPDATE_TIME,
        CodeConst::OFO_TIME,
    ];

    protected $hidden = [
        CodeConst::OFO_TIME,
    ];
}