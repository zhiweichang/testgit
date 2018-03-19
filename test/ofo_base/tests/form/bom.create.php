<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/11/5
 * Time: 00:17
 */
$form = [
    'sku_id' => '2X0021',
    'user_id' => 26268,
    'detail' => [
        [
            'sku_id' => '2X1026',
            'num' => 1,
            'delivery_way' => 20,
        ],
        [
            'sku_id' => '9060154',
            'num' => 2,
            'delivery_way' => 10,
        ],
    ],
];
echo json_encode($form['detail'], JSON_PRETTY_PRINT) . PHP_EOL;