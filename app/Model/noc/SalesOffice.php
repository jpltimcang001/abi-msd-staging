<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Distributor $distributor
 * @property Barangay $barangay
 * @property Invoice[] $invoices
 * @property Location[] $locations
 * @property SalesOrder[] $salesOrders
 * @property Salesman[] $salesmen
 * @property string $no
 * @property int $distributor_id
 * @property int $barangay_id
 * @property string $market_no
 * @property string $type
 * @property string $code
 * @property string $name
 * @property string $manager
 * @property string $short_name
 * @property string $address1
 * @property string $address2
 * @property string $tin
 * @property string $fax_no
 * @property string $depot_code
 * @property string $longitude
 * @property string $latitude
 * @property string $added_by
 * @property string $added_when
 * @property string $edited_by
 * @property string $tel_no
 * @property string $edited_when
 * @property string $deleted_by
 * @property string $deleted_when
 * @property boolean $deleted
 * @property string $email
 * @property string $sub_so_no
 * @property string $short_desc
 * @property string $sys_21
 * @property string $ms_dynamics_key
 * @property boolean $centrix_synced
 * @property boolean $onesoas_synced
 * @property boolean $sys_21_synced
 * @property boolean $msd_synced
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
    protected $primaryKey = 'no';

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
    protected $fillable = ['distributor_id', 'barangay_id', 'market_no', 'type', 'code', 'name', 'manager', 'short_name', 'address1', 'address2', 'tin', 'fax_no', 'depot_code', 'longitude', 'latitude', 'added_by', 'added_when', 'edited_by', 'tel_no', 'edited_when', 'deleted_by', 'deleted_when', 'deleted', 'email', 'sub_so_no', 'short_desc', 'sys_21', 'ms_dynamics_key', 'centrix_synced', 'onesoas_synced', 'sys_21_synced', 'msd_synced'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function distributor()
    {
        return $this->belongsTo('App\Distributor', null, 'distributor_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barangay()
    {
        return $this->belongsTo('App\Barangay');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany('App\Invoice', 'sales_office_no', 'no');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations()
    {
        return $this->hasMany('App\Location', 'sales_office_no', 'no');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOrders()
    {
        return $this->hasMany('App\SalesOrder', 'sales_office_no', 'no');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesmen()
    {
        return $this->hasMany('App\Salesman', 'sales_office_no', 'no');
    }
}
