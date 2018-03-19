<?php
/**
 * Created by PhpStorm.
 * User: jianguo
 * Date: 2017/11/4
 * Time: 23:28
 */
namespace App\Constants\Mq;

class RoutingKey{
    const BOM_CREATE = 'rk.base.bom.create';
    const BOM_UPDATE_STATUS = 'rk.base.bom.update_status';
    const BOM_UPDATE_DELIVERY_WAY = 'rk.base.bom.update_delivery_way';

    const SKU_CREATE = 'rk.base.sku.create';
    const SKU_UPDATE = 'rk.base.sku.update';
    const SKU_UPDATE_STATUS = 'rk.base.sku.update_status';

    const SUPPLIER_CREATE = 'rk.base.supplier.create';
    const SUPPLIER_UPDATE = 'rk.base.supplier.update';
    const SUPPLIER_UPDATE_STATUS = 'rk.base.supplier.update_status';

    const THROW_CITY_CREATE = 'rk.base.throw_city.create';
    const THROW_CITY_UPDATE = 'rk.base.throw_city.update';
    const THROW_CITY_UPDATE_STATUS = 'rk.base.throw_city.update_status';

    const THROW_AREA_CREATE = 'rk.base.throw_area.create';

    const THROW_POINT_CREATE = 'rk.base.throw_point.create';
    const THROW_POINT_UPDATE = 'rk.base.throw_point.update';
    const THROW_POINT_UPDATE_STATUS = 'rk.base.throw_point.update_status';

    const WAREHOUSE_CREATE = 'rk.base.warehouse.create';
    const WAREHOUSE_UPDATE = 'rk.base.warehouse.update';
    const WAREHOUSE_UPDATE_STATUS = 'rk.base.warehouse.update_status';
    
    
    const SKUFORMAT_CREATE = 'rk.base.sku_format.create';
    const SKUFORMAT_UPDATE = 'rk.base.sku_format.update';
    const SKUFORMAT_UPDATE_STATUS = 'rk.base.sku_format.update_status';
    
    const MATERIEL_CONFIG_CREATE = 'rk.base.materiel_config.create';
    const MATERIEL_CONFIG_UPDATE = 'rk.base.materiel_config.update';
}