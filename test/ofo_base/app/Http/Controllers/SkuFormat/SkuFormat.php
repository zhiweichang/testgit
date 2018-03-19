<?php

namespace App\Http\Controllers\SkuFormat;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\SkuFormat as SkuFormatRepository;
use App\Constants\Db\Tables\Base\SkuFormat as SkuFormatConst;
use Exception;

class SkuFormat extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function create(Request $request){
        $this->validate($request, [
            'type' => 'required',
            'value' => 'required',
            'create_user_id' => 'required|integer',
        ],[
            'type.required' => trans('message.FORMAT_TYPE').trans('message.NOT_EMPTY'),
            'value.required' => trans('message.FORMAT_VALUE').trans('message.NOT_EMPTY'),
            'create_user_id.required' => trans('message.CREATE_USER').trans('message.NOT_EMPTY'),
            'create_user_id.integer' => trans('message.CREATE_USER').trans('message.INVALID'),
        ]);
        $type = trim($request->input('type',0));
        $value = trim($request->input('value',''));
        $createUserId = $request->input('create_user_id',0);
        try{
            $skuFormat = SkuFormatRepository::create($type, $value, $createUserId);
            return $this->jsonSuccess(['type' => $skuFormat[SkuFormatConst::TYPE]]);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function update(Request $request){
        $this->validate($request, [
            'id' => 'required|integer',
            'value' => 'required',
            'update_user_id' => 'required|integer',
        ],[
            'id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"id")),
            'id.integer' => trans('message.ATTRIBUTE_INVALID',array("attribute"=>"id")),
            'value.required' => trans('message.FORMAT_VALUE').trans('message.NOT_EMPTY'),
            'update_user_id.required' => trans('message.UPDATE_USER').trans('message.NOT_EMPTY'),
            'update_user_id.integer' => trans('message.UPDATE_USER').trans('message.INVALID'),
        ]);
        $id = trim($request->input('id',0));
        $value = trim($request->input('value',''));
        $updateUserId = $request->input('update_user_id',0);
        try{
            SkuFormatRepository::update($id,$value,  $updateUserId);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function updateStatus(Request $request){
        $this->validate($request, [
            'id' => 'required|integer',
            'status' => 'required|in:' . implode(',', array_keys(SkuFormatConst::$status)),
            'update_user_id' => 'required|integer',
        ],[
            'id.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"id")),
            'id.integer' => trans('message.ATTRIBUTE_INVALID',array("attribute"=>"id")),
            'status.required' => trans('message.ATTRIBUTE_NOT_EMPTY',array("attribute"=>"STATUS")),
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'update_user_id.required' => trans('message.UPDATE_USER').trans('message.NOT_EMPTY'),
            'update_user_id.integer' => trans('message.UPDATE_USER').trans('message.INVALID'),
        ]);
        $id = $request->input('id',0);
        $status = $request->input('status',0);
        $updateUserId = $request->input('update_user_id',0);
        try{
            SkuFormatRepository::updateStatus($id, $status, $updateUserId);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getList(Request $request) {
        $this->validate($request, [
            'status' => 'sometimes|in:' . implode(',', array_keys(SkuFormatConst::$status)),
            'page' => 'integer',
            'perpage' => 'integer',
        ],[
            'status.in' => trans('message.ATTRIBUTE_ILLEGAL',array("attribute"=>"STATUS")),
            'page.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"page")),
            'perpage.integer' => trans('message.ATTRIBUTE_NEED_EMPTY_OR_INTEGER',array("attribute"=>"perpage")),
        ]);
        $page = $request->input('page',1);
        $perPage = $request->input('perpage',50);
        $status = $request->input('status',null);
        try{
            $skuFormats = SkuFormatRepository::getList($status, $page,$perPage);
            return $this->jsonSuccess($skuFormats);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
        
    }
}