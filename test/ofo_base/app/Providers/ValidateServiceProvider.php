<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use App\Libs\Util;

class ValidateServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('mobile', function($attribute, $value, $parameters){
            return preg_match('/^1[345678]{1}\d{9}$/', $value);
        });
        Validator::extend('org_mobile', function($attribute, $value, $parameters){
            return preg_match('/^(\d|-)+$/', $value);
        });
        Validator::extend('ofo_config_code', function($attribute, $value, $parameters){
            return preg_match('/^(\w)+$/', $value);
        });
        Validator::extend('ofo_json_required', function($attribute, $value, $parameters){
            $value = Util::unserializeParams($value);
            return empty($value)?false:true;
        });
    }
}
