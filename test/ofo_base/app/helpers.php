<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/9/15
 * Time: 下午5:52
 */

if(!function_exists('trans')){
    function trans($key,$array=array()){
        return app('translator')->trans($key,$array);
    }
}