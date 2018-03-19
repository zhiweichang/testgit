<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\MaterielConfig;

use App\Models\Base;
use App\Constants\Db\Tables\Base\MaterielConfig as MaterielConfigConst;

class MaterielConfig extends Base {
    /**
     *
     */
    const CREATED_AT = MaterielConfigConst::CREATE_TIME;
    const UPDATED_AT = MaterielConfigConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = MaterielConfigConst::TABLE;
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
        MaterielConfigConst::ID,
        MaterielConfigConst::CODE,
        MaterielConfigConst::NAME,
        MaterielConfigConst::MTYPE,
        MaterielConfigConst::STATUS,
        MaterielConfigConst::CREATE_TIME,
        MaterielConfigConst::UPDATE_TIME,
        MaterielConfigConst::CREATE_USER_ID,
        MaterielConfigConst::UPDATE_USER_ID,
        MaterielConfigConst::IS_NOT_IMPORT,
    ];
    protected $hidden = [
    ];
}
