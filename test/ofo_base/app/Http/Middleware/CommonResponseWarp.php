<?php
namespace App\Http\Middleware;

use App\Http\Responses\BicycleJsonResponse;
use App\Http\Responses\AppResponseInJson;
use Closure;
use Log;

class CommonResponseWarp
{
    use AppResponseInJson;

    public function handle($request, Closure $next)
    {
        $data = $request->all();
        Log::info('RAW_INPUT[' . json_encode($data) . ']');
        $response = $next($request);

        if (!$response instanceof BicycleJsonResponse) {
            if (config('app.debug')) {
                return $response;
            }
            if (method_exists($response, 'getOriginalContent')) {
                $response = $response->getOriginalContent();
            }
            $response = $this->jsonSuccess($response);
        }

        Log::info('RAW_OUTPUT[' . $response->getJsonData() . ']');

        return $response;
    }
}