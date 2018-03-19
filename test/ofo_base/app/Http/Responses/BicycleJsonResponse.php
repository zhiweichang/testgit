<?php

/**
 * Created by PhpStorm.
 * User: baizhe
 * Date: 2017/4/5
 * Time: 下午5:00
 */

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class BicycleJsonResponse extends JsonResponse
{
    public function getJsonData()
    {
        return $this->data;
    }
}
