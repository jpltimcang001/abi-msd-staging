<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Brand $brand
 * @property Company $company
 * @property Poi $poi
 * @property SalesOffice $salesOffice
 * @property SkuCategory $skuCategory
 * @property SkuClassification $skuClassification
 * @property SkuSubCategory $skuSubCategory
 * @property Uom $uom
 * @property Zone $zone
 * @property Inventory[] $inventories
 * @property OfftakeAvgDaily[] $offtakeAvgDailies
 * @property OfftakeDetail[] $offtakeDetails
 * @property PoiSku[] $poiSkus
 * @property SkuCombine[] $skuCombines
 * @property SkuCombine[] $skuCombines
 * @property SkuConvertion[] $skuConvertions
 * @property SkuCustomDataValue[] $skuCustomDataValues
 * @property SkuGroup[] $skuGroups
 * @property SkuGroup[] $skuGroups
 * @property SkuImage[] $skuImages
 * @property SkuLocationRestock[] $skuLocationRestocks
 * @property string $sku_id
 * @property string $brand_id
 * @property string $company_id
 * @property string $default_poi_id
 * @property string $default_sales_office_id
 * @property string $type
 * @property string $sku_classification_id
 * @property string $sub_type
 * @property string $default_uom_id
 * @property string $default_zone_id
 * @property string $sku_code
 * @property string $sku_name
 * @property string $description
 * @property float $default_unit_price
 * @property float $default_sell_price
 * @property string $supplier
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 * @property int $low_qty_threshold
 * @property int $high_qty_threshold
 * @property string $barcode
 * @property string $user_define_field_1
 * @property string $user_define_field_2
 * @property string $user_define_field_3
 * @property float $length
 * @property float $width
 * @property float $height
 * @property float $cbm
 * @property float $weight
 * @property string $ms_dynamics_key
 * @property boolean $deleted
 * @property boolean $active
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
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'sku_id';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['brand_id', 'company_id', 'sales_office_code', 'default_poi_id', 'default_sales_office_id', 'type', 'sku_classification_id', 'sub_type', 'default_uom_id', 'default_zone_id', 'sku_code', 'sku_name', 'description', 'default_unit_price', 'default_sell_price', 'supplier', 'created_date', 'created_by', 'updated_date', 'updated_by', 'low_qty_threshold', 'high_qty_threshold', 'barcode', 'user_define_field_1', 'user_define_field_2', 'user_define_field_3', 'length', 'width', 'height', 'cbm', 'weight', 'ms_dynamics_key', 'deleted', 'active', 'sys_21_synced', 'msd_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo('App\Brand', null, 'brand_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Company', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poi()
    {
        return $this->belongsTo('App\Poi', 'default_poi_id', 'poi_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOffice()
    {
        return $this->belongsTo('App\SalesOffice', 'default_sales_office_id', 'sales_office_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function skuCategory()
    {
        return $this->belongsTo('App\SkuCategory', 'type', 'sku_category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function skuClassification()
    {
        return $this->belongsTo('App\SkuClassification', null, 'sku_classification_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function skuSubCategory()
    {
        return $this->belongsTo('App\SkuSubCategory', 'sub_type', 'sku_sub_category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uom()
    {
        return $this->belongsTo('App\Uom', 'default_uom_id', 'uom_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo('App\Zone', 'default_zone_id', 'zone_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories()
    {
        return $this->hasMany('App\Inventory', null, 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offtakeAvgDailies()
    {
        return $this->hasMany('App\OfftakeAvgDaily', null, 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offtakeDetails()
    {
        return $this->hasMany('App\OfftakeDetail', null, 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function poiSkus()
    {
        return $this->hasMany('App\PoiSku', null, 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuParentCombines()
    {
        return $this->hasMany('App\SkuCombine', 'parent_sku_id', 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuChildCombines()
    {
        return $this->hasMany('App\SkuCombine', 'child_sku_id', 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuConvertions()
    {
        return $this->hasMany('App\SkuConvertion', null, 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuCustomDataValues()
    {
        return $this->hasMany('App\SkuCustomDataValue', null, 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuParentGroups()
    {
        return $this->hasMany('App\SkuGroup', 'parent_sku_id', 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuChildGroups()
    {
        return $this->hasMany('App\SkuGroup', 'child_sku_id', 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuImages()
    {
        return $this->hasMany('App\SkuImage', null, 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuLocationRestocks()
    {
        return $this->hasMany('App\SkuLocationRestock', null, 'sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nocSku()
    {
        return $this->belongsTo('App\Model\noc\Sku', 'sku_code', 'code')->where('sales_office_no', $this->sales_office_code);
    }
}
