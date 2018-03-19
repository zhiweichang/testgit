<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Throws;

use App\Models\Base;
use App\Constants\Db\Tables\Base\ThrowWarehouse as ThrowWarehouseConst;

class ThrowWarehouse extends Base {
    /**
     * @var string
     */
    protected $table = ThrowWarehouseConst::TABLE;
}