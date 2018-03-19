<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\ProjectBom;

use App\Models\Base;
use App\Constants\Db\Tables\Base\ProjectBom as ProjectBomConst;

class ProjectBom extends Base {
    /**
     *
     */
    const CREATED_AT = ProjectBomConst::CREATE_TIME;
    const UPDATED_AT = ProjectBomConst::UPDATE_TIME;
    /**
     * @var string
     */
    protected $table = ProjectBomConst::TABLE;
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
        ProjectBomConst::ID,
        ProjectBomConst::PN_ID,
        ProjectBomConst::BOM_NAME,
        ProjectBomConst::STATUS,
        ProjectBomConst::CREATE_TIME,
        ProjectBomConst::UPDATE_TIME,
        ProjectBomConst::CREATE_USER_ID,
        ProjectBomConst::UPDATE_USER_ID,
    ];

    protected $hidden = [
    ];
}
