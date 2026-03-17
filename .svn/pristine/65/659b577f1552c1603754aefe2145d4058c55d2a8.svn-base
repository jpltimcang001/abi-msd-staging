<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property IncomingInventory $incomingInventory
 * @property int $incoming_inventory_detail_id
 * @property int $incoming_inventory_id
 * @property string $company_id
 * @property int $inventory_id
 * @property string $batch_no
 * @property string $sku_id
 * @property string $uom_id
 * @property string $sku_status_id
 * @property string $source_zone_id
 * @property float $unit_price
 * @property string $expiration_date
 * @property int $planned_quantity
 * @property int $quantity_received
 * @property float $amount
 * @property string $return_date
 * @property string $status
 * @property string $remarks
 * @property string $campaign_no
 * @property string $pr_no
 * @property string $pr_date
 * @property string $plan_arrival_date
 * @property string $revised_delivery_date
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 * @property string $po_no
 * @property string $truck_bay
 * @property string $BO_Class
 * @property string $comp_code
 * @property string $rtvdpr_no
 * @property string $BSRRF_BSMS
 * @property string $expiry_date
 * @property string $bo_source
 * @property string $billing
 * @property string $BO_Class2
 * @property string $line_no
 * @property string $entry_no
 */
class IncomingInventoryDetail extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'incoming_inventory_detail';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'incoming_inventory_detail_id';

    /**
     * @var array
     */
    protected $fillable = ['incoming_inventory_id', 'company_id', 'inventory_id', 'batch_no', 'sku_id', 'uom_id', 'sku_status_id', 'source_zone_id', 'unit_price', 'expiration_date', 'planned_quantity', 'quantity_received', 'amount', 'return_date', 'status', 'remarks', 'campaign_no', 'pr_no', 'pr_date', 'plan_arrival_date', 'revised_delivery_date', 'created_date', 'created_by', 'updated_date', 'updated_by', 'po_no', 'truck_bay', 'BO_Class', 'comp_code', 'rtvdpr_no', 'BSRRF_BSMS', 'expiry_date', 'bo_source', 'billing', 'BO_Class2', 'line_no', 'entry_no'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function incomingInventory()
    {
        return $this->belongsTo('App\IncomingInventory', null, 'incoming_inventory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sku()
    {
        return $this->belongsTo('App\Model\wms\Sku', 'sku_id', 'sku_id');
    }
}
