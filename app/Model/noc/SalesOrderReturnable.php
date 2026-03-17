<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $sales_order_id
 * @property int $sku_id
 * @property int $balance
 * @property int $delivery
 * @property int $return
 * @property int $net_qty
 * @property float $unit_price
 * @property string $reference_no
 * @property string $document_no
 * @property float $amount
 * @property string $added_when
 * @property string $added_by
 * @property string $edited_when
 * @property string $edited_by
 * @property string $deleted_when
 * @property string $deleted_by
 * @property boolean $deleted
 */
class SalesOrderReturnable extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'temp_sales_order_returnable';
    const CREATED_AT = 'added_when';
    const UPDATED_AT = 'edited_when';


    /**
     * @var array
     */
    protected $fillable = ['sales_order_id', 'sku_id', 'balance', 'delivery', 'document_no', 'return', 'net_qty', 'unit_price', 'reference_no', 'amount', 'added_when', 'added_by', 'edited_when', 'edited_by', 'deleted_when', 'deleted_by', 'deleted'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOrder()
    {
        return $this->belongsTo('App\Model\noc\SalesOrder', 'sales_order_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sku()
    {
        return $this->belongsTo('App\Model\noc\Sku', 'sku_id', 'id');
    }
}
