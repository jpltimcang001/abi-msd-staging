<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Agency $agency
 * @property ProdBrand $prodBrand
 * @property ProdSubcat $prodSubcat
 * @property ProdGroup $prodGroup
 * @property ProdCategory $prodCategory
 * @property SalesOrderDeal[] $salesOrderDeals
 * @property SalesOrderDeal[] $salesOrderDeals
 * @property SalesOrderDetail[] $salesOrderDetails
 * @property SalesOrderReturnable[] $salesOrderReturnables
 * @property int $id
 * @property int $agency_id
 * @property int $brand_id
 * @property int $category_sub_id
 * @property int $groupseqid
 * @property int $category_id
 * @property string $code
 * @property string $name
 * @property string $uom
 * @property string $type
 * @property float $unit_price
 * @property float $sell_price
 * @property string $search_name
 * @property string $package
 * @property int $unit_case
 * @property float $weight
 * @property float $cbm
 * @property string $added_by
 * @property string $added_when
 * @property string $edited_by
 * @property string $edited_when
 * @property string $deleted_by
 * @property string $deleted_when
 * @property boolean $deleted
 * @property string $sales_office_no
 * @property string $upc_code
 * @property string $sys_21
 * @property string $image_file
 * @property string $sms_code
 * @property boolean $active
 * @property int $CAT
 * @property boolean $stock_type
 * @property string $vat_prod_posting_group
 * @property string $item_tracking_code
 * @property string $product_alias
 * @property string $proddiv_code
 * @property string $prodcat_code
 * @property string $prodbrand_code
 * @property string $prodvar_code
 * @property string $prodsubdiv_code
 * @property string $issue_unit
 * @property string $vat_code
 * @property string $class
 * @property string $group_major
 * @property string $group_minor
 * @property int $is_synced
 * @property int $sku_order
 * @property string $image
 * @property string $ms_dynamics_key
 * @property boolean $centrix_synced
 * @property boolean $onesoas_synced
 * @property boolean $sys_21_synced
 * @property boolean $msd_synced
 */
class Sku extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'sku';
    const CREATED_AT = 'added_when';
    const UPDATED_AT = 'edited_when';

    /**
     * @var array
     */
    protected $fillable = ['agency_id', 'brand_id', 'category_sub_id', 'groupseqid', 'category_id', 'code', 'name', 'uom', 'type', 'unit_price', 'sell_price', 'search_name', 'package', 'unit_case', 'weight', 'cbm', 'added_by', 'added_when', 'edited_by', 'edited_when', 'deleted_by', 'deleted_when', 'deleted', 'sales_office_no', 'upc_code', 'sys_21', 'image_file', 'sms_code', 'active', 'CAT', 'stock_type', 'vat_prod_posting_group', 'item_tracking_code', 'product_alias', 'proddiv_code', 'prodcat_code', 'prodbrand_code', 'prodvar_code', 'prodsubdiv_code', 'issue_unit', 'vat_code', 'class', 'group_major', 'group_minor', 'is_synced', 'sku_order', 'image', 'ms_dynamics_key', 'centrix_synced', 'onesoas_synced', 'sys_21_synced', 'msd_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agency()
    {
        return $this->belongsTo('App\Model\noc\Agency', null, 'agency_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodBrand()
    {
        return $this->belongsTo('App\Model\noc\ProdBrand', 'brand_id', 'brand_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodSubcat()
    {
        return $this->belongsTo('App\Model\noc\ProdSubcat', 'category_sub_id', 'category_sub_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodGroup()
    {
        return $this->belongsTo('App\Model\noc\ProdGroup', 'groupseqid', 'groupseqid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodCategory()
    {
        return $this->belongsTo('App\Model\noc\ProdCategory', 'category_id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOrderDeals()
    {
        return $this->hasMany('App\Model\noc\SalesOrderDeal', 'free_sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOrderDetails()
    {
        return $this->hasMany('App\Model\noc\SalesOrderDetail');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOrderReturnables()
    {
        return $this->hasMany('App\Model\noc\SalesOrderReturnable');
    }
}
