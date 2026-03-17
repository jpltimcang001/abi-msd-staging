<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Invoice $invoice
 * @property int $id
 * @property string $inv_code
 * @property int $inv_id
 * @property string $so_code
 * @property string $product_code
 * @property int $serve_quantity
 * @property float $unit_price
 * @property float $amount
 * @property string $discount
 * @property string $pricing_type
 * @property string $disc_no
 * @property float $disc_amt
 * @property float $weight
 * @property float $cbm
 * @property int $balance_empties
 * @property string $line_no
 * @property string $doc_no
 * @property string $empties_type
 * @property string $added_by
 * @property string $added_when
 * @property string $edited_by
 * @property string $edited_when
 */
class InvoiceDetails extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'invoice_details';
    const CREATED_AT = 'added_when';
    const UPDATED_AT = 'edited_when';
    /**
     * @var array
     */
    protected $fillable = ['inv_code', 'inv_id', 'so_code', 'product_code', 'serve_quantity', 'unit_price', 'amount', 'discount', 'pricing_type', 'disc_no', 'disc_amt', 'weight', 'cbm', 'line_no', 'balance_empties', 'doc_no', 'empties_type', 'added_by', 'added_when', 'edited_by', 'edited_when'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo('App\Model\noc\Invoice', 'inv_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sku()
    {
        return $this->belongsTo('App\Model\noc\Sku', 'product_code', 'code');
    }
}
