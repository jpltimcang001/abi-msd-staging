<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Aisle $aisle
 * @property Company $company
 * @property Level $level
 * @property SalesOffice $salesOffice
 * @property Shelf $shelf
 * @property Employee[] $employees
 * @property Inventory[] $inventories
 * @property InventoryHistory[] $inventoryHistories
 * @property Notification[] $notifications
 * @property SalesOffice[] $salesOffices
 * @property Sku[] $skus
 * @property SkuLocationRestock[] $skuLocationRestocks
 * @property string $zone_id
 * @property string $aisle_id
 * @property string $company_id
 * @property string $level_id
 * @property string $sales_office_id
 * @property string $shelf_id
 * @property string $zone_code
 * @property string $zone_name
 * @property string $description
 * @property string $ms_dynamics_key
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 * @property boolean $deleted
 */
class Zone extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'zone';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'zone_id';

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
    protected $fillable = ['aisle_id', 'company_id', 'level_id', 'sales_office_id', 'shelf_id', 'zone_code', 'zone_name', 'description', 'ms_dynamics_key', 'created_date', 'created_by', 'updated_date', 'updated_by', 'deleted'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function aisle()
    {
        return $this->belongsTo('App\Model\wms\Aisle', null, 'aisle_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Model\wms\Company', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo('App\Model\wms\Level', null, 'level_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOffice()
    {
        return $this->belongsTo('App\Model\wms\SalesOffice', null, 'sales_office_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shelf()
    {
        return $this->belongsTo('App\Model\wms\Shelf', null, 'shelf_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany('App\Model\wms\Employee', 'default_zone_id', 'zone_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories()
    {
        return $this->hasMany('App\Model\wms\Inventory', null, 'zone_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventoryHistories()
    {
        return $this->hasMany('App\Model\wms\InventoryHistory', 'destination_zone_id', 'zone_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany('App\Model\wms\Notification', null, 'zone_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOffices()
    {
        return $this->hasMany('App\Model\wms\SalesOffice', 'default_zone_id', 'zone_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skus()
    {
        return $this->hasMany('App\Model\wms\Sku', 'default_zone_id', 'zone_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuLocationRestocks()
    {
        return $this->hasMany('App\Model\wms\SkuLocationRestock', null, 'zone_id');
    }
}
