<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $so_code
 * @property string $inv_code
 * @property float $quantity
 * @property float $amount
 * @property string $line_no
 * @property string $added_by
 * @property string $added_when
 * @property string $updated_by
 * @property string $updated_when
 */
class InvoiceRefundDetail extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'invoice_refund_detail';
    
    
    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'so_code',
        'inv_code',
        'quantity',
        'amount',
        'line_no',
        'added_by',
        'added_when',
        'updated_by',
        'updated_when'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOrder()
    {
        return $this->belongsTo('App\Model\noc\SalesOrder', 'so_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo('App\Model\noc\Invoice', 'inv_code', 'code');
    }
}
