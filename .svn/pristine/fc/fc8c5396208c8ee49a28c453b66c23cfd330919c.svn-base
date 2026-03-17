<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Distributor $distributor
 * @property SalesOffice $salesOffice
 * @property RouteTransaction[] $routeTransactions
 * @property SalesOrder[] $salesOrders
 * @property TempSalesOrder[] $tempSalesOrders
 * @property int $id
 * @property int $distributor_id
 * @property string $sales_office_no
 * @property string $name
 * @property string $code
 * @property string $soas_code
 * @property string $gps_gate_code
 * @property int $district_id
 * @property string $added_when
 * @property string $added_by
 * @property string $edited_when
 * @property string $edited_by
 * @property string $deleted_when
 * @property string $deleted_by
 * @property boolean $deleted
 * @property string $salesman_type
 * @property string $sales_order_type
 * @property string $zone
 * @property string $mobile_number
 * @property string $sub_so_no
 * @property int $is_synced
 * @property int $is_synced2
 * @property int $sales_group_id
 * @property string $unique_code
 * @property string $cmos_updated_version
 * @property string $division_code
 * @property string $password
 * @property string $email
 * @property string $cash_batch
 * @property string $cheque_batch
 * @property string $ms_dynamics_key
 * @property boolean $sys_21_synced
 * @property boolean $msd_synced
 */
class Salesman extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'salesman';

    /**
     * @var array
     */
    protected $fillable = ['distributor_id', 'cash_batch', 'cheque_batch', 'sales_office_no', 'name', 'code', 'soas_code', 'gps_gate_code', 'district_id', 'added_when', 'added_by', 'edited_when', 'edited_by', 'deleted_when', 'deleted_by', 'deleted', 'salesman_type', 'sales_order_type', 'zone', 'mobile_number', 'sub_so_no', 'is_synced', 'is_synced2', 'sales_group_id', 'unique_code', 'cmos_updated_version', 'division_code', 'password', 'email', 'ms_dynamics_key', 'sys_21_synced', 'msd_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function distributor()
    {
        return $this->belongsTo('App\Model\noc\Distributor', null, 'distributor_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOffice()
    {
        return $this->belongsTo('App\Model\noc\SalesOffice', 'sales_office_no', 'no');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo('App\Model\noc\Zone', 'zone', 'zone_name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function routeTransactions()
    {
        return $this->hasMany('App\Model\noc\RouteTransaction');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOrders()
    {
        return $this->hasMany('App\Model\noc\SalesOrder');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tempSalesOrders()
    {
        return $this->hasMany('App\Model\noc\TempSalesOrder');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wmsSalesOffice()
    {
        return $this->belongsTo('App\Model\wms\SalesOffice', 'sales_office_no', 'sales_office_code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesGroup()
    {
        return $this->belongsTo('App\Model\noc\SalesGroup', 'sales_group_id', 'id' );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesmanType()
    {
        return $this->belongsTo('App\Model\noc\SalesmanType', 'salesman_type', 'code');
    }
}
