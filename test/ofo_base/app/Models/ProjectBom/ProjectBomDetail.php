<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\ProjectBom;

use App\Models\Base;
use App\Constants\Db\Tables\Base\ProjectBomDetail as ProjectBomDetailConst;

class ProjectBomDetail extends Base {
    /**
     * @var string
     */
    protected $table = ProjectBomDetailConst::TABLE;
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
        ProjectBomDetailConst::ID,
        ProjectBomDetailConst::BOM_ID,
        ProjectBomDetailConst::PN_ID,
        ProjectBomDetailConst::NUM,
    ];

    protected $hidden = [
    ];
}
