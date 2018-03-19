<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/8/15
 * Time: 下午2:19
 */
namespace App\Models\Country;

use App\Models\Base;
use App\Constants\Db\Tables\Base\Country as CountryConst;

class Country extends Base {
    /**
     * @var string
     */
    protected $table = CountryConst::TABLE;
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * @var array
     */
    protected $fillable = [
        CountryConst::ID,
        CountryConst::CODE,
        CountryConst::NAME,
    ];
    protected $hidden = [
    ];
}
