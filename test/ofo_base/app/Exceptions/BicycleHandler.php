<?php

namespace App\Exceptions;

use App\Http\Responses\AppResponseInJson;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use App\Constants\Err\Common as ErrCommon;
use Log;

class BicycleHandler extends ExceptionHandler
{
    use AppResponseInJson;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
//        AuthorizationException::class,
//        HttpException::class,
//        ModelNotFoundException::class,
//        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \App\Http\Responses\BicycleJsonResponse|\Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        if ($e instanceof \PDOException) {
            $code = ErrCommon::UNKNOW;
            $message = 'internal error.';
        } else if ($e instanceof ValidationException) {
            $code = ErrCommon::ERR_PARAMS_REQUEST;
            if (method_exists($e->getResponse(), 'getData')) {
                $message = head($e->getResponse()->getData())[0];
            } else {
                $message = 'The given data failed to pass validation.';
            }
        } else {
            $code = $e->getCode();

            if (!$code || ($code < 0))
            {
                $code = ErrCommon::UNKNOW;
            }
            $message = $e->getMessage();
        }

        Log::warning('Something Wrong! Exception Code: '.$code.', Msg: '. $e->getMessage() . ',File: ' . $e->getFile() . '[' . $e->getLine() . ']');

        return $this->jsonFailed($code, $message);
    }
}