<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $sales_order_id
 * @property string $product_code
 * @property int $quantity
 * @property float $weight
 * @property float $cbm
 * @property float $unit_price
 * @property string $discount_no
 * @property float $discount_amount
 * @property float $total_amount
 * @property string $ms_dynamics_key
 * @property string $added_when
 * @property string $added_by
 * @property string $edited_when
 * @property string $edited_by
 * @property string $deleted_when
 * @property string $deleted_by
 * @property boolean $deleted
 * @property string $discount
 * @property string $discount_description
 * @property boolean $pricing_type
 * @property string $disc_type
 * @property string $disctag
 * @property boolean $con_granted
 * @property boolean $applied
 * @property float $con_sellprice
 * @property string $remarks
 * @property float $priceaddon
 * @property string $discount_parse
 * @property string $line_no
 * @property string $lot_no
 * @property string $entry_no
 * @property int $is_deal
 */
class SalesOrderDetail extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'temp_sales_order_detail';
    const CREATED_AT = 'added_when';
    const UPDATED_AT = 'edited_when';

    /**
     * @var array
     */
    protected $fillable = ['sales_order_id', 'sales_office_code', 'product_code', 'quantity', 'weight', 'cbm', 'unit_price', 'discount_no', 'discount_amount', 'total_amount', 'ms_dynamics_key', 'is_deal' , 'added_when', 'added_by', 'edited_when', 'edited_by', 'deleted_when', 'deleted_by', 'deleted', 'discount', 'discount_description', 'pricing_type', 'disc_type', 'disctag', 'con_granted', 'applied', 'con_sellprice', 'remarks', 'priceaddon', 'discount_parse', 'line_no', 'lot_no', 'entry_no'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function discountCase()
    {
        return $this->belongsTo('App\Model\noc\DiscountCase', 'discount_no' , 'discount_m_case_no')->where('sales_office_no', '=', $this->sales_office_code);
    }
	
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
        return $this->belongsTo('App\Model\noc\Sku', 'product_code', 'code')->where('sales_office_no', '=', $this->sales_office_code);
    }
}
