<?php
/**
 * zhangjianguo@ofo.com
 */
namespace App\Libs;

class Util{
    /**
     * @param $params
     * @return mixed
     */
    public static function unserializeParams($params){
        return json_decode($params, true);
    }

    /**
     * @param $message
     * @param $routingKey
     * @param string $config
     * @return bool
     */
    public static function sendToMq($message, $routingKey, $config = 'ofo_base'){
        $headers = [
            'topic' => config("oforabbit.{$config}.exchange"),
            'rk' => $routingKey,
            'mq_time' => time(),
        ];
        return app('oforabbit')->send($config, $routingKey, $message, $headers);
    }
}
