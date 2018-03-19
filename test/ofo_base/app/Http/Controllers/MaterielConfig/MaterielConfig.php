<?php

namespace App\Http\Controllers\MaterielConfig;

use App\Constants\Err\Common;
use App\Http\Controllers\Controller;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Repositories\MaterielConfig as MaterielConfigRepository;
use App\Constants\Db\Tables\Base\MaterielConfig as MaterielConfigConst;
use Exception;

class MaterielConfig extends Controller{
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function create(Request $request){
        $this->validate($request, [
            'mtype' => 'required|in:' . implode(',', array_keys(MaterielConfigConst::$types)),
            'code' => 'required|string|min:1|max:32',
            'name' => 'required|string|min:1|max:32',
            'create_user_id' => 'required|integer',
        ],[
            'mtype.required' => '配置类型不能为空',
            'mtype.in' => '配置类型无效',
            'code.required' => '请填写代码',
            'code.string' => '编码无效',
            'code.min' => '编码最小长度不能低于:min个字',
            'code.max' => '编码最大长度不能超过:max个字',
            'name.required' => '请填写名称',
            'name.string' => ':attribute 无效',
            'name.min' => ':attribute 最小长度不能低于:min个字',
            'name.max' => ':attribute 最大长度不能超过:max个字',
            'create_user_id.required' => '创建人不能为空',
            'create_user_id.integer' => '创建人无效',
        ]);
        $mtype = $request->input('mtype');
        $code = $request->input('code');
        $name = trim($request->input('name'));
        $createUserId = $request->input('create_user_id','');
        try{
            $materielConfig = MaterielConfigRepository::create($mtype,$code, $name, $createUserId);
            return $this->jsonSuccess(['id' => $materielConfig[MaterielConfigConst::ID]]);
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
            'code' => 'required|string|min:1|max:32',
            'name' => 'required|string|min:1|max:32',
            'update_user_id' => 'required|integer',
        ],[
            'id.required' => 'ID不能为空',
            'id.integer' => 'ID无效',
            'code.required' => '请填写代码',
            'code.string' => '编码无效',
            'code.min' => '编码最小长度不能低于:min个字',
            'code.max' => '编码最大长度不能超过:max个字',
            'name.required' => '请填写名称',
            'name.string' => ':attribute 无效',
            'name.min' => ':attribute 最小长度不能低于:min个字',
            'name.max' => ':attribute 最大长度不能超过:max个字',
            'update_user_id.required' => '修改人不能为空',
            'update_user_id.integer' => '修改人无效',
        ]);
        $id = $request->input('id');
        $code = $request->input('code');
        $name = trim($request->input('name'));
        $updateUserId = $request->input('update_user_id','');
        try{
            $materielConfig = MaterielConfigRepository::update($id,$code, $name, $updateUserId);
            return $this->jsonSuccess(['id' => $id]);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }

    
    /**
     * @param Request $request
     * @return \App\Http\Responses\BicycleJsonResponse
     */
    public function listByTypes(Request $request){
        $this->validate($request, [
            'types' => 'required|json',
            'name' => 'string',
        ],[
            'types.required' => ':attribute 不能为空',
            'types.json' => ':attribute 不合法',
            'name.string' => ':attribute 不合法',
        ]);
        $types = Util::unserializeParams($request->input("types"));
        $name  = trim($request->input("name",''));
        try{
            $materies = MaterielConfigRepository::listByTypes($types,$name);
            return $this->jsonSuccess($materies);
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
            'type' => 'required|integer',
            'name' => 'sometimes|string',
            'page' => 'sometimes|integer',
            'perpage' => 'sometimes|integer',
        ],[
            'type.required' => ':attribute 不能为空',
            'type.integer' => ':attribute 不合法',
            'name.string' => ':attribute 不合法',
            'page.integer' => '页数无效',
            'perpage.integer' => '页数大小无效',
        ]);
        $type = $request->input("type");
        $page = $request->input("page", 1);
        $name = trim($request->input("name", ""));
        $perPage = $request->input("perpage", 50);
        try{
            $materies = MaterielConfigRepository::getList($type,$name,$page,$perPage);
            return $this->jsonSuccess($materies);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function getDetail(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
        ],[
            'id.required' => ':attribute 不能为空',
            'id.integer' => ':attribute 不合法',
        ]);
        $id = $request->input("id");
        try{
            $materie = MaterielConfigRepository::getDetail($id);
            return $this->jsonSuccess($materie);
        }catch(Exception $e){
            return $this->jsonFailed(Common::UNKNOW, $e->getMessage());
        }
    }
}