<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/11/5
 * Time: 10:56
 */
namespace App\Models\Code;

use App\Constants\Db\Connections;

class Base extends \App\Models\Base {
    /**
     *
     */
    const CONNECTION = Connections::SN;

    /**
     * @var string
     */
    protected $connection = self::CONNECTION;
}