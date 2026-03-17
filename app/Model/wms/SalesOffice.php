<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Company $company
 * @property Zone $zone
 * @property Employee[] $employees
 * @property Notification[] $notifications
 * @property Sku[] $skus
 * @property SkuLocationRestock[] $skuLocationRestocks
 * @property SkuSalesOffice[] $skuSalesOffices
 * @property Zone[] $zones
 * @property string $sales_office_id
 * @property string $company_id
 * @property string $default_zone_id
 * @property string $distributor_id
 * @property string $sales_office_code
 * @property string $sales_office_name
 * @property string $address1
 * @property string $address2
 * @property string $barangay_id
 * @property string $municipal_id
 * @property string $province_id
 * @property string $region_id
 * @property string $sales_region_id
 * @property float $latitude
 * @property float $longitude
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 * @property string $email
 * @property string $distributor_group_id
 * @property boolean $deleted
 * @property string $short_desc
 * @property string $main_sales_office_code
 */
class SalesOffice extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'sales_office';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'sales_office_id';

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
    protected $fillable = ['company_id', 'default_zone_id', 'distributor_id', 'sales_office_code', 'sales_office_name', 'address1', 'address2', 'barangay_id', 'municipal_id', 'province_id', 'region_id', 'sales_region_id', 'latitude', 'longitude', 'created_date', 'created_by', 'updated_date', 'updated_by', 'email', 'distributor_group_id', 'deleted', 'short_desc', 'main_sales_office_code'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Model\wms\Company', 'company_id', 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo('App\Model\wms\Zone', 'default_zone_id', 'zone_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany('App\Model\wms\Employee', null, 'sales_office_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany('App\Model\wms\Notification', null, 'sales_office_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skus()
    {
        return $this->hasMany('App\Model\wms\Sku', 'default_sales_office_id', 'sales_office_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuLocationRestocks()
    {
        return $this->hasMany('App\Model\wms\SkuLocationRestock', null, 'sales_office_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuSalesOffices()
    {
        return $this->hasMany('App\Model\wms\SkuSalesOffice', null, 'sales_office_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zones()
    {
        return $this->hasMany('App\Model\wms\Zone', null, 'sales_office_id');
    }
}
