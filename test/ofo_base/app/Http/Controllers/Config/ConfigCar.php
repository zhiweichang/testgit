<?php

namespace App\Http\Controllers\Config;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\ConfigCar as ConfigCarRepository;
use App\Constants\Db\Tables\Base\ConfigCar as ConfigCarConst;
use Exception;

class ConfigCar extends Controller{
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function create(Request $request){
        $this->validate($request, [
            'code' => 'required|ofo_config_code|min:1|max:64',
            'name' => 'required|string|min:1|max:64',
            'content' => 'required|string|min:1|max:512',
            'detail'=>'required|ofo_json_required|json',
            'org_id'=>'required|in:'.implode(',', array_keys(ConfigCarConst::$org)),
            'create_user_id' => 'required|integer',
            'status' => 'required|in:' . implode(',', array_keys(ConfigCarConst::$status)),
        ],[
            'code.required' => "参数代码不能为空",
            'code.ofo_config_code' => "参数代码只能包含字母数字下划线",
            'code.min' => "参数代码最小长度不能低于:min个字",
            'code.max' => "参数代码最大长度不能超过:max个字",
            'name.required' => "参数名称不能为空",
            'name.min' => "参数名称最小长度不能低于:min个字",
            'name.max' => "参数名称最大长度不能超过:max个字",
            'content.required' => "参数说明不能为空",
            'content.min' => "参数说明最小长度不能低于:min个字",
            'content.max' => "参数说明最大长度不能超过:max个字",
            'detail.required' => "配置详情不能为空",
            'detail.ofo_json_required' => "配置详情不能为空",
            'detail.json' => "配置详情不合法",
            'org_id.required' => "公司主体不能为空",
            'org_id.in' => "公司主体不合法",
            'status.required' => "状态不能为空",
            'status.in' => "状态不合法",
            'create_user_id.required' => "创建人不能为空",
            'create_user_id.integer' => "创建人不合法",
        ]);
        $code = trim($request->input('code',''));
        $name = trim($request->input('name',''));
        $content = trim($request->input('content',''));
        $detail = Util::unserializeParams($request->input("detail",''));
        $orgId  = $request->input("org_id");
        $createUserId = $request->input('create_user_id','');
        $status = $request->input('status',1);
        try{
            $configCar = ConfigCarRepository::create($code,$name, $content, $detail, $orgId, $createUserId, $status);
            return $this->jsonSuccess(['code' => $configCar[ConfigCarConst::CODE]]);
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
            'id' => 'required',
            'code' => 'required|ofo_config_code|min:1|max:64',
            'name' => 'required|string|min:1|max:64',
            'content' => 'required|string|min:1|max:512',
            'detail'=>'required|ofo_json_required|json',
            'org_id'=>'required|in:'.implode(',', array_keys(ConfigCarConst::$org)),
            'update_user_id' => 'required|integer',
            'status' => 'required|in:' . implode(',', array_keys(ConfigCarConst::$status)),
        ],[
            'id.required' => "id不能为空",
            'code.ofo_config_code' => "参数代码只能包含字母数字下划线",
            'code.min' => "参数代码最小长度不能低于:min个字",
            'code.max' => "参数代码最大长度不能超过:max个字",
            'name.required' => "参数名称不能为空",
            'name.min' => "参数名称最小长度不能低于:min个字",
            'name.max' => "参数名称最大长度不能超过:max个字",
            'content.required' => "参数说明不能为空",
            'content.min' => "参数说明最小长度不能低于:min个字",
            'content.max' => "参数说明最大长度不能超过:max个字",
            'detail.required' => "配置详情不能为空",
            'detail.ofo_json_required' => "配置详情不能为空",
            'detail.json' => "配置详情不合法",
            'org_id.required' => "公司主体不能为空",
            'org_id.in' => "公司主体不合法",
            'status.required' => "状态不能为空",
            'status.in' => "状态不合法",
            'update_user_id.required' => "修改人不能为空",
            'update_user_id.integer' => "修改人不合法",
        ]);
        $id = trim($request->input('id',0));
        $name = trim($request->input('name',''));
        $content = trim($request->input('content',''));
        $detail = Util::unserializeParams($request->input("detail",''));
        $orgId  = $request->input("org_id");
        $updateUserId = $request->input('update_user_id','');
        $status = $request->input('status',1);
        try{
            ConfigCarRepository::update($id,$name, $content, $detail, $orgId, $updateUserId, $status);
            return $this->jsonSuccess();
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    /**
     * 获取SKU列表
     * @param Request $request
     * @return array
     */
    public function getList(Request $request) {
        $this->validate($request, [
            'code' => 'string',
            'name' => 'string',
            'org_id' => 'sometimes|in:' . implode(',', array_keys(ConfigCarConst::$org)),
            'status' => 'sometimes|in:' . implode(',', array_keys(ConfigCarConst::$status)),
            'create_time_start' => 'integer',
            'create_time_end' => 'integer',
            'page' => 'integer',
            'perpage' => 'integer',
        ]);
        $code = trim($request->input("code",null));
        $name  = trim($request->input("name",null));
        $orgId   = $request->input("org_id",null);
        $status  = $request->input("status",null);
        $createTimeStart = $request->input("create_time_start",0);
        $createTimeEnd  = $request->input("create_time_end",0);
        $page    = $request->input("page",1);
        $perPage = $request->input("perpage",50);
        try{
            $configCars = ConfigCarRepository::getList($code, $name,$orgId,$status,$createTimeStart,$createTimeEnd,$page,$perPage);
            return $this->jsonSuccess($configCars);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

   
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function getDetail(Request $request){
        $this->validate($request, [
            'id' => 'required',
        ],[
            'id.required' => "ID不能为空",
        ]);
        $id = $request->input('id', null);
        try{
            $configCar = ConfigCarRepository::getDetail($id);
            return $this->jsonSuccess($configCar);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function listByCodes(Request $request){
        $this->validate($request, [
            'codes' => 'required|ofo_json_required|json',
            'status' => 'sometimes|in:' . implode(',', array_keys(ConfigCarConst::$status)),
        ],[
            'codes.required' => "编码不能为空",
            'codes.ofo_json_required' => "编码不能为空",
            'codes.json' => "编号格式不合法",
            'status.in' =>"状态不合法",
        ]);
        
        $codes = Util::unserializeParams($request->input('codes'));
        $status = $request->input('status', null);
        try{
            $configCars = ConfigCarRepository::listByCodes($codes, $status);
            return $this->jsonSuccess($configCars);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
}
