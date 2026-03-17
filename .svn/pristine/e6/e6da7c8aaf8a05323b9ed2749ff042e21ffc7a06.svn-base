<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Salesman $salesman
 * @property Location $location
 * @property int $id
 * @property int $salesman_id
 * @property int $location_id
 * @property string $code
 * @property string $sales_order_date
 * @property string $delivery_date
 * @property int $cases
 * @property int $bottles
 * @property float $amount
 * @property string $remarks
 * @property boolean $type
 * @property boolean $transaction_type
 * @property string $si_code
 * @property string $discount_no
 * @property float $discount_amount
 * @property float $total_returns
 * @property string $longitude
 * @property string $latitude
 * @property float $distance
 * @property string $service_start_time
 * @property string $service_end_time
 * @property string $reason_code
 * @property string $posting_date
 * @property string $external_document_no
 * @property boolean $handheld_entry
 * @property string $handheld_reference_no
 * @property string $ms_dynamics_key
 * @property string $added_when
 * @property string $added_by
 * @property string $edited_when
 * @property string $edited_by
 * @property string $deleted_when
 * @property string $deleted_by
 * @property boolean $deleted
 * @property string $status
 * @property string $class
 * @property string $batch_id
 * @property int $wholesaler_id
 * @property string $end_order
 * @property string $confirm_order
 * @property string $category_reason_code
 * @property boolean $sys_21_synced
 * @property boolean $msd_synced
 */
class SalesOrder extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'temp_sales_order';
    const CREATED_AT = 'added_when';
    const UPDATED_AT = 'edited_when';

    /**
     * @var array
     */
    protected $fillable = ['salesman_id', 'location_id', 'code', 'sales_order_date','delivery_date', 'cases', 'bottles', 'amount', 'remarks', 'type', 'transaction_type', 'si_code', 'discount_no', 'discount_amount', 'total_returns', 'longitude', 'latitude', 'distance', 'service_start_time', 'service_end_time', 'reason_code', 'posting_date', 'external_document_no', 'handheld_entry', 'handheld_reference_no', 'ms_dynamics_key', 'added_when', 'added_by', 'edited_when', 'edited_by', 'deleted_when', 'deleted_by', 'deleted', 'status', 'class', 'batch_id', 'wholesaler_id', 'end_order', 'confirm_order', 'category_reason_code', 'sys_21_synced', 'is_cmos_edited', 'msd_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesman()
    {
        return $this->belongsTo('App\Model\noc\Salesman', 'salesman_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('App\Model\noc\Location', 'location_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOrderDetail()
    {
        return $this->hasMany('App\Model\noc\SalesOrderDetail', 'sales_order_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOrderReturnable()
    {
        return $this->hasMany('App\Model\noc\SalesOrderReturnable', 'sales_order_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice()
    {
        return $this->hasMany('App\Model\noc\Invoice', 'sales_order_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conversion()
    {
        return $this->hasMany('App\Model\noc\SalesOrderConversion', 'sales_order_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tempCollectionCash()
    {
        return $this->belongsTo('App\Model\noc\TempCollectionCash', 'id', 'sales_order_id');
    }
}
