<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Code;

use App\Constants\Db\Tables\Base\CodeVersion as CodeVersionConst;
use Carbon\Carbon;

class CodeVersion extends Base {
    /**
     *
     */
    const CREATED_AT = CodeVersionConst::CREATE_TIME;
    const UPDATED_AT = CodeVersionConst::OFO_TIME;
    /**
     * @var string
     */
    protected $table = CodeVersionConst::TABLE;
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
        CodeVersionConst::ID,
        CodeVersionConst::CODE_TYPE,
        CodeVersionConst::CODE,
        CodeVersionConst::VERSION,
        CodeVersionConst::CREATE_TIME,
        CodeVersionConst::OFO_TIME,
    ];

    /**
     * @var array
     */
    protected $hidden = [
        CodeVersionConst::OFO_TIME,
    ];

    /**
     * @param $value
     * @return string
     */
    public function setUpdatedAtAttribute($value) {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    }
}