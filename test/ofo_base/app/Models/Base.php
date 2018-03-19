<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/11/5
 * Time: 10:56
 */
namespace App\Models;

use App\Constants\Db\Connections;
use Illuminate\Database\Eloquent\Model;

class Base extends Model {
    /**
     *
     */
    const CONNECTION = Connections::SCM;

    /**
     * @var string
     */
    protected $connection = self::CONNECTION;

    /**
     *
     */
    public static function beginTransaction(){
        static::resolveConnection(static::CONNECTION)->beginTransaction();
    }

    /**
     *
     */
    public static function rollBack(){
        static::resolveConnection(static::CONNECTION)->rollBack();
    }

    /**
     *
     */
    public static function commit(){
        static::resolveConnection(static::CONNECTION)->commit();
    }
}