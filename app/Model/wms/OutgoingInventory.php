<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property OutgoingInventoryDetail[] $outgoingInventoryDetails
 * @property int $outgoing_inventory_id
 * @property string $company_id
 * @property string $dr_no
 * @property string $dr_date
 * @property string $rra_no
 * @property string $rra_date
 * @property string $source_zone_id
 * @property string $destination_zone_id
 * @property string $contact_person
 * @property string $contact_no
 * @property string $address
 * @property string $plan_delivery_date
 * @property string $transaction_date
 * @property string $status
 * @property string $remarks
 * @property float $total_amount
 * @property string $closed
 * @property string $created_by
 * @property string $created_date
 * @property string $updated_by
 * @property string $updated_date
 * @property string $recipients
 * @property int $is_synced
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
 * @property boolean $msd_synced
 */
class OutgoingInventory extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'outgoing_inventory';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'outgoing_inventory_id';

    /**
     * @var array
     */
    protected $fillable = ['company_id', 'dr_no', 'dr_date', 'rra_no', 'rra_date', 'source_zone_id', 'destination_zone_id', 'contact_person', 'contact_no', 'address', 'plan_delivery_date', 'transaction_date', 'status', 'remarks', 'total_amount', 'closed', 'created_by', 'created_date', 'updated_by', 'updated_date', 'recipients', 'is_synced', 'internal_code', 'uploaded', 'vehicle_no', 'extract_date', 'extract_date1', 'applied_date', 'emp_no', 'driver_emp_no', 'helper_emp_no', 'helper_emp_no2', 'tpart_no', 'sales_order_no', 'driver', 'comp_no', 'remarks2', 'truck_no', 'plate_no', 'diversion', 'hauler_code', 'msd_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function outgoingInventoryDetails()
    {
        return $this->hasMany('App\Model\wms\OutgoingInventoryDetail', 'outgoing_inventory_id', 'outgoing_inventory_id');
    }
}
