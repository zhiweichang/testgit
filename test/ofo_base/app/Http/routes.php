<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return "base data service for scm, framework version: " . $app->version();
});


$app->group(['prefix'=>'v1/sku', 'namespace'=>'App\Http\Controllers\Sku'], function ($app) {
    $app->post('create', 'Sku@create');
    $app->post('update', 'Sku@update');
    $app->post('update_status', 'Sku@updateStatus');
    $app->get('list_by_sku_ids', 'Sku@listBySkuIds');
    $app->get('list_by_types', 'Sku@listByTypes');
    $app->get('get_by_sku_id', 'Sku@getBySkuId');
    $app->get('list_advance', 'Sku@listBySkuIds');
    $app->get('get_list', 'Sku@getList');
    $app->get('stock_types','Sku@stockTypes');
    $app->get('sku_types','Sku@skuTypes');
    $app->post('check_bsns', 'Sku@checkBsns');
    $app->post('list_pn_by_sku_ids', 'Sku@listPnBySkuIds');
    $app->get('list_pn_by_sku_id', 'Sku@listPnBySkuId');
    $app->get('list_by_sku_or_name', 'Sku@listBySkuOrName');
});

$app->group(['prefix'=>'v1/supplier', 'namespace'=>'App\Http\Controllers\Supplier'], function ($app) {
    $app->post('create', 'Supplier@create');
    $app->post('update', 'Supplier@update');
    $app->post('update_status', 'Supplier@updateStatus');
    $app->get('list_by_supplier_ids', 'Supplier@listBySupplierIds');
    $app->get('get_by_supplier_id', 'Supplier@getBySupplierId');
    $app->get('get_list', 'Supplier@getList');
    $app->get('get_detail', 'Supplier@getDetail');
    $app->get('get_rate_list', 'Supplier@getRateList');
    $app->get('get_basic', 'Supplier@getBasic');
    $app->get('get_by_factory_id', 'Supplier@getByFactoryId');
    $app->get('get_category_list','Supplier@getCategoryList');
});

$app->group(['prefix'=>'v1/factory', 'namespace'=>'App\Http\Controllers\Factory'], function ($app) {
    $app->get('list_by_factory_ids', 'Factory@listByFactoryIds');
    $app->get('get_by_factory_id', 'Factory@getByFactoryId');
    $app->get('get_list', 'Factory@getList');
    $app->get('get_by_supplier_id', 'Factory@getBySupplierId');
    $app->get('list_by_city_ids', 'Factory@listByCityIds');
});

$app->group(['prefix'=>'v1/bom', 'namespace'=>'App\Http\Controllers'], function ($app) {
    $app->post('create', 'Bom\Bom@create');
    $app->post('update_status', 'Bom\Bom@updateStatus');
    $app->post('update', 'Bom\Bom@update');
    $app->get('get_by_bom_id', 'Bom\Bom@getByBomId');
    $app->get('list_by_bom_ids', 'Bom\Bom@listByBomIds');
    $app->get('get_list', 'Bom\Bom@getList');
});

$app->group(['prefix'=>'v1/warehouse', 'namespace'=>'App\Http\Controllers\Warehouse'], function ($app) {
    $app->post('create', 'Warehouse@create');
    $app->post('update', 'Warehouse@update');
    $app->post('update_status', 'Warehouse@updateStatus');
    $app->get('list_by_warehouse_ids', 'Warehouse@listByWarehouseIds');
    $app->post('list_by_warehouse_ids', 'Warehouse@listByWarehouseIds');
    $app->get('get_by_warehouse_id', 'Warehouse@getByWarehouseId');
    $app->get('list_by_city_ids', 'Warehouse@listByCityIds');
    $app->get('get_list', 'Warehouse@getList');
    $app->post('get_list', 'Warehouse@getList');
    $app->get('get_warehouse_type', 'Warehouse@getWarehouseType');
    $app->get('list_by_factory_ids', 'Warehouse@listByFactoryIds');
    $app->get('get_detail', 'Warehouse@getDetail');
    
    
    
    
    
});

$app->group(['prefix'=>'v1/city', 'namespace'=>'App\Http\Controllers\City'], function ($app) {
    $app->get('list_by_city_ids', 'City@listByCityIds');
    $app->post('get_list', 'City@getList');
    $app->post('list_by_city_names', 'City@listByCityNames');
    $app->post('list_by_city_codes', 'City@listByCityCode');
});

$app->group(['prefix'=>'v1/throws', 'namespace'=>'App\Http\Controllers\Throws'], function ($app) {
    $app->post('point/create', 'ThrowPoint@create');
    $app->post('point/update', 'ThrowPoint@update');
    $app->post('point/update_status', 'ThrowPoint@updateStatus');
    $app->get('point/list_by_throw_point_ids', 'ThrowPoint@listByThrowPointIds');
    $app->get('point/list_by_throw_city_id', 'ThrowPoint@listByThrowCityId');
    $app->get('point/list_by_throw_point_addresses', 'ThrowPoint@listByThrowPointAddresses');
    $app->get('point/get_by_throw_point_id', 'ThrowPoint@getByThrowPointId');
    $app->get('point/get_list', 'ThrowPoint@getList');

    $app->post('city/create', 'ThrowCity@create');
    $app->post('city/update', 'ThrowCity@update');
    $app->post('city/update_status', 'ThrowCity@updateStatus');
    $app->get('city/list_by_throw_city_ids', 'ThrowCity@listByThrowCityIds');
    $app->get('city/get_by_throw_city_id', 'ThrowCity@getByThrowCityId');
    $app->get('city/get_list', 'ThrowCity@getList');
    
    $app->get('city/get_detail_by_city_id', 'ThrowCity@getDetailByThrowCityId');
    $app->get('city/get_detail', 'ThrowCity@getDetail');
    
    

    $app->post('area/create', 'ThrowArea@create');
    $app->get('area/list_by_throw_area_ids', 'ThrowArea@listByThrowAreaIds');
    $app->get('area/get_by_throw_area_id', 'ThrowArea@getByThrowAreaId');
    $app->get('area/get_by_city_id', 'ThrowArea@getByCityId');

    $app->get('warehouse/get_by_throw_city_id', 'ThrowWarehouse@getByThrowCityId');
    $app->get('warehouse/list_by_throw_city_ids', 'ThrowWarehouse@listByThrowCityIds');
});

$app->group(['prefix'=>'v1/code', 'namespace'=>'App\Http\Controllers\Code'], function ($app) {
    $app->get('supplier/get_by_code_type_and_code', 'CodeSupplier@getByCodeTypeAndCode');
    $app->get('version/get_by_code_type_and_code', 'CodeVersion@getByCodeTypeAndCode');
});

$app->group(['prefix'=>'v1/sku_format', 'namespace'=>'App\Http\Controllers\SkuFormat'], function ($app) {
    $app->post('create', 'SkuFormat@create');
    $app->post('update_status', 'SkuFormat@updateStatus');
    $app->post('update', 'SkuFormat@update');
    $app->get('get_list', 'SkuFormat@getList');
});
$app->group(['prefix'=>'v1/materiel_config', 'namespace'=>'App\Http\Controllers\MaterielConfig'], function ($app) {
    $app->post('create', 'MaterielConfig@create');
    $app->get('list_by_types', 'MaterielConfig@listByTypes');
    $app->post('update', 'MaterielConfig@update');
    $app->get('get_list', 'MaterielConfig@getList');
    $app->get('get_detail', 'MaterielConfig@getDetail');
});

$app->group(['prefix'=>'v1/pn', 'namespace'=>'App\Http\Controllers\Pn'], function ($app) {
    $app->post('create', 'Pn@create');
    $app->post('update', 'Pn@update');
    $app->get('get_list', 'Pn@getList');
    $app->post('update_status', 'Pn@updateStatus');
    $app->get('material_type', 'Pn@materialType');
    $app->get('get_by_id', 'Pn@getById');
    $app->get('list_by_ids', 'Pn@listByIds');
});

$app->group(['prefix'=>'v1/country', 'namespace'=>'App\Http\Controllers\Country'], function ($app) {
    $app->get('get_country_by_name', 'Country@getCountryByName');
});

$app->group(['prefix' => 'v1/project_bom', 'namespace' => 'App\Http\Controllers\ProjectBom'], function ($app) {
    $app->post('create', 'ProjectBom@create');
    $app->post('update', 'ProjectBom@update');
    $app->get('get_list', 'ProjectBom@getList');
    $app->get('get_by_id', 'ProjectBom@getByBomId');
    $app->post('update_status', 'ProjectBom@updateStatus');
    $app->get('get_list', 'ProjectBom@getList');
});



$app->group(['prefix'=>'v1/config', 'namespace'=>'App\Http\Controllers\Config'], function ($app) {
    $app->post('car/create', 'ConfigCar@create');
    $app->post('car/update', 'ConfigCar@update');
    $app->get('car/get_list', 'ConfigCar@getList');
    $app->get('car/get_detail', 'ConfigCar@getDetail');
    $app->get('car/list_by_codes', 'ConfigCar@listByCodes');
});

