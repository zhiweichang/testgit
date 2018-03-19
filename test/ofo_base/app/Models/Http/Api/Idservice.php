<?php
namespace App\Models\Http\Api;
use App\Models\Http\Api\Base;

/**
 * ID生成器
 */
class Idservice extends Base{
    protected static $domain = "ofo_idservice";
    protected static $URL_GET_NEW_ID = "/sequence/get-new-id";
    public static function getNewId(){
	    $result =  self::get(self::url(self::$URL_GET_NEW_ID),array("customer"=>"accessory_claim"));
	    return isset($result["info"])?$result["info"]:'';
    }
}
?>
