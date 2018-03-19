<?php
namespace App\Libs;

trait Singleton
{
    private static $_instance = null;
    private function __construct() {}
    /**
     * Forbid to clone the object
     */
    private function __clone()
    {
        throw new \Exception("Could not clone the object from class: ".__CLASS__);
    }
}
