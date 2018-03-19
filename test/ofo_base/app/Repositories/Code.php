<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:16
 */

namespace App\Repositories;

use App\Models\Code\Code as CodeModel;
use App\Models\Code\CodeSupplier as CodeSupplierModel;
use App\Models\Code\CodeVersion as CodeVersionModel;
use App\Constants\Db\Tables\Base\Code as CodeConst;
use App\Constants\Db\Tables\Base\CodeSupplier as CodeSupplierConst;
use App\Constants\Db\Tables\Base\CodeVersion as CodeVersionConst;


class Code{
    /**
     * @param $id
     * @param null $status
     * @return mixed
     */
    public static function getCodeById($id, $status = null){
        $builder = CodeModel::where(CodeConst::ID, $id);
        if(!empty($status)){
            $builder->where(CodeConst::STATUS, $status);
        }
        return $builder->first();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getVersionById($id){
        return CodeVersionModel::find($id);
    }

    /**
     * @param $codeType
     * @param $code
     * @return mixed
     */
    public static function getSupplierByCodeTypeAndCode($codeType, $code){
        return CodeSupplierModel::where(CodeSupplierConst::CODE_TYPE, $codeType)
            ->where(CodeSupplierConst::CODE, $code)->first();
    }

    /**
     * @param $codeType
     * @param $code
     * @return mixed
     */
    public static function getVersionByCodeTypeAndCode($codeType, $code){
        return CodeVersionModel::where(CodeVersionConst::CODE_TYPE, $codeType)
            ->where(CodeVersionConst::CODE, $code)->first();
    }
    /**
     * 
     * @param array $ids
     * @return type
     */
    public static function listVersionByIds(array $ids) {
        return CodeVersionModel::whereIn(CodeVersionConst::ID,$ids)->get()->toArray();
    }
    /**
     * 
     * @param array $ids
     * @return type
     */
    public static function listCodeByIds(array $ids) {
        return CodeModel::whereIn(CodeConst::ID,$ids)->get()->toArray();
    }
}