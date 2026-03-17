<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property IncomingInventoryDetail[] $incomingInventoryDetails
 * @property int $incoming_inventory_id
 * @property string $company_id
 * @property string $dr_no
 * @property string $dr_date
 * @property string $rra_no
 * @property string $rra_date
 * @property string $source_zone_id
 * @property string $destination_zone_id
 * @property string $contact_person
 * @property string $contact_no
 * @property string $transaction_date
 * @property string $plan_delivery_date
 * @property string $status
 * @property string $remarks
 * @property float $total_amount
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 * @property string $recipients
 * @property string $internal_code
 * @property boolean $uploaded
 * @property string $vehicle_no
 * @property string $extract_date
 * @property string $extract_date1
 * @property string $applied_date
 * @property string $emp_no
 * @property string $driver_emp_no
 * @property string $helper_emp_no
 * @property string $helper_emp_no2
 * @property string $tpart_no
 * @property string $sales_order_no
 * @property string $driver
 * @property string $comp_no
 * @property string $remarks2
 * @property string $truck_no
 * @property string $plate_no
 * @property boolean $diversion
 * @property string $hauler_code
 * @property boolean $onesoas_synced
 * @property boolean $msd_synced
 */
class IncomingInventory extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'incoming_inventory';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'incoming_inventory_id';

    /**
     * @var array
     */
    protected $fillable = ['company_id', 'dr_no', 'dr_date', 'rra_no', 'rra_date', 'source_zone_id', 'destination_zone_id', 'contact_person', 'contact_no', 'transaction_date', 'plan_delivery_date', 'status', 'remarks', 'total_amount', 'created_date', 'created_by', 'updated_date', 'updated_by', 'recipients', 'internal_code', 'uploaded', 'vehicle_no', 'extract_date', 'extract_date1', 'applied_date', 'emp_no', 'driver_emp_no', 'helper_emp_no', 'helper_emp_no2', 'tpart_no', 'sales_order_no', 'driver', 'comp_no', 'remarks2', 'truck_no', 'plate_no', 'diversion', 'hauler_code', 'onesoas_synced', 'msd_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function incomingInventoryDetails()
    {
        return $this->hasMany('App\Model\wms\IncomingInventoryDetail', 'incoming_inventory_id', 'incoming_inventory_id');
    }
}
