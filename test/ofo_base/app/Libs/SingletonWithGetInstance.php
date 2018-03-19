<?php
namespace App\Libs;

use Bicycle\Libs\Singleton;

trait SingletonWithGetInstance
{
    use App;

    static function get_instance()
    {
        if (is_null(self::$_instance)) {
            $class = __CLASS__;
            self::$_instance = new $class;
        }
        return self::$_instance;
    }
}
