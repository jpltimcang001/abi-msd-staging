<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property TempSalesOrder $tempSalesOrder
 * @property int $id
 * @property string $sales_order_code
 * @property string $code
 * @property string $sku_code
 * @property int $quantity
 * @property string $lot_no
 * @property string $added_when
 * @property string $added_by
 * @property string $edited_when
 * @property string $edited_by
 */
class SalesOrderConversion extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'sales_order_conversion';

    /**
     * @var array
     */
    protected $fillable = ['sales_order_code', 'sales_office_code', 'code', 'sku_code', 'quantity', 'lot_no', 'added_when', 'added_by', 'edited_when', 'edited_by'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOrder()
    {
        return $this->belongsTo('app\Model\noc\TempSalesOrder', 'sales_order_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sku()
    {
        return $this->belongsTo('App\Model\noc\Sku', 'sku_code', 'code')->where('sales_office_no', '=', $this->sales_office_code);
    }
}
