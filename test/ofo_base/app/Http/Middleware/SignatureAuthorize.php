<?php

namespace App\Http\Middleware;

use App\Constants\Err\Common;
use Closure;
use App\Http\Responses\AppResponseInJson;
use Validator;

class SignatureAuthorize
{
    use AppResponseInJson;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param null|string $app specified app name
     * @return mixed
     */
    public function handle($request, Closure $next, $app = null)
    {
        if (config('app.debug') && config('ofosignature.skip_if_debug')) {
            return $next($request);
        }

        $params = $request->query();
        if ( ! $this->_validateSignatureRequiredParams($params)) {
            return $this->jsonFailed(Common::ERR_CHECK_SIGNATURE, 'check signature failed[a].');
        }

        $clientApp = $params['app_name'];
        if (($app && $app != $clientApp) || false === $secret = $this->_findAuthAppSecret($clientApp) ) {
            return $this->jsonFailed(Common::ERR_CHECK_SIGNATURE, 'check signature failed[b].');
        }

        $signed = $this->_getSignature($params, $secret);

        if (!hash_equals($signed, $params['sign'])) {
            return $this->jsonFailed(Common::ERR_CHECK_SIGNATURE, 'check signature failed[c].');
        }

        return $next($request);
    }

    private function _validateSignatureRequiredParams($params)
    {
        $validator = Validator::make($params, [
            'app_name' => 'required|alpha_dash',
            'ts' => 'required|integer|min:1492660669|max:2524579200',
            'sign' => 'required|alpha_num|size:32',
            'nonce' => 'size:16'
        ]);

        return $validator->passes();
    }

    private function _findAuthAppSecret($clientApp)
    {
        $authApps = config('ofosignature.apps');
        $appNameSecretMap = array_pluck($authApps, 'secret', 'name');

        return $appNameSecretMap[$clientApp] ?? false;
    }

    private function _getSignature($params, $secret)
    {
        $signingArr = array_except($params, 'sign');
        $signing = $this->_makeSigningStr($signingArr, $secret);
        return $this->_calcSignature($signing);
    }

    private function _makeSigningStr($params, $secret)
    {
        $str = '';
        ksort($params);
        foreach ($params as $k => $v) {
            $str .= "$k=$v";
        }
        return $str . $secret;
    }

    private function _calcSignature($signingStr) {

        return md5($signingStr);
    }
}