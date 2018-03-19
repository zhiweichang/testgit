<?php
namespace App\Models\Http\Api;
use Log;
use Ofo\Utils\Request\Facades\OfoRequest;
use Exception;

class Base
{
    protected static $domain;
    
    public static function url($url){
        $conf = config('connection.' . static::$domain);
        return $conf['host'].':'.$conf['port'].$url;
    }
    /**
     * 
     * @param type $url
     * @param type $data
     * @return type
     * @throws Exception
     */
    protected static function get($url, $data=array()) {
        $ret = OfoRequest::get($url, $data);
        $result = json_decode($ret, true);
        $log = "url:" . $url . "|data:" . json_encode($data) . "|ret:" . $ret;
        if (isset($result["code"]) && $result["code"] == 0) {
            Log::info($log);
            return $result;
        } else {
            Log::error($log);
            throw new Exception('请求异常');
        }
    }
    /**
     * 
     * @param type $url
     * @param type $data
     * @return type
     * @throws Exception
     */
    protected static function post($url, $data=array()) {
        $ret = OfoRequest::post($url, $data);
        $result = json_decode($ret, true);
        $log = "url:" . $url . "|data:" . json_encode($data) . "|ret:" . $ret;
        if (isset($result["code"]) && $result["code"] == 0) {
            Log::info($log);
            return $result;
        } else {
            Log::error($log);
            throw new Exception('请求异常');
        }
    }
}
?>
