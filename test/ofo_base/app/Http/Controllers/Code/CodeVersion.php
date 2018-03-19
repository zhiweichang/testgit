<?php

namespace App\Http\Controllers\Code;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Code as CodeRepository;
use Exception;

class CodeVersion extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse|\App\Http\Responses\MobileJsonResponse
     */
    public function getByCodeTypeAndCode(Request $request){
        $this->validate($request, [
            'code_type' => 'required|string',
            'code' => 'required|string',
        ],[
            'code_type.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"code_type")),
            'code_type.string' => trans('message.ATTRIBUTE_INVALID',array("attribute"=>"code_type")),
            'code.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"code")),
            'code.string' =>  trans('message.ATTRIBUTE_INVALID',array("attribute"=>"code")),
        ]);
        $codeType = $request->input('code_type');
        $code = $request->input('code');
        try{
            $version = CodeRepository::getVersionByCodeTypeAndCode($codeType, $code);
            return $this->jsonSuccess($version);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
}