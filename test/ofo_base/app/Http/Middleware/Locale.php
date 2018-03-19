<?php
/**
 * 供应链多语言支持
 * @date: 2018-01-11 12:42
 * @author: zhangjianguo@ofo.com
 */
namespace App\Http\Middleware; //注意检查下此处的命名空间名称是否和你当前App的名称一致
 
use Closure;
use Illuminate\Http\Request;
 
class Locale {
    public function handle(Request $request, Closure $next) {
        $lang = $request->input('lang'); //为了测试方便支持参数中直接传入lang参数，优先级最高
        if(empty($lang)){
            $lang = $request->input('LANG'); //大写的也支持下吧
        }
        if(empty($lang)){
            $lang = $_SERVER['HTTP_LANG'] ?? ''; //从上游请求中提取语言环境参数
        }
        if(empty($lang)){
            $lang = 'zh-cn'; //你项目中默认的语言包目录名称
        }
        $lang = strtolower($lang); //是否需要转换大小写取决于resources/lang/目录下的子目录名称的大小写情况
        app('translator')->setLocale($lang);
        return $next($request);
    }
}