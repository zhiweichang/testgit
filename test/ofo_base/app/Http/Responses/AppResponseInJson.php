<?php
/**
 * Created by PhpStorm.
 * User: baizhe
 * Date: 2017/4/5
 * Time: 下午4:41
 */

namespace App\Http\Responses;

use App\Constants\Err\Common;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

trait AppResponseInJson
{
    public function jsonSuccess($data = [])
    {
        if ($data instanceof Arrayable && ! $data instanceof JsonSerializable) {
            $data = $data->toArray();
        }

        return $this->jsonResponse(Common::OK, $data);
    }

    public function jsonFailed($code, $message = '', $data = [])
    {
        return $this->jsonResponse($code, $data, $message);
    }

    private function jsonResponse($code, $data, $message = '')
    {
        $content = $this->normalizeResponseFormat($code, $data, $message);
        return new BicycleJsonResponse($content);
    }

    private function normalizeResponseFormat($code, $data, $message = '')
    {
        return [
            'code' => $code,
            'info' => $data,
            'message' => $message
        ];
    }

}
