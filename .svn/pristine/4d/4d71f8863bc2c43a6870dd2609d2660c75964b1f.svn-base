<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $company_id
 * @property string $created_by
 * @property string $created_when
 * @property int $quantity
 * @property string $sales_office_no
 * @property string $sku_code
 * @property string $stock_type
 * @property string $transaction_date
 * @property string $expiration_date
 * @property string $reference_no
 * @property int $inventory_id
 * @property string $withdrawal_code
 * @property string $zone_code
 */
class InventoryBreakdown extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'inventory_breakdown';
    const CREATED_AT = 'created_when';

    /**
     * @var array
     */
    protected $fillable = ['company_id', 'created_by', 'created_when', 'inventory_id', 'expiration_date', 'reference_no', 'quantity', 'sales_office_no', 'sku_code', 'stock_type', 'transaction_date', 'withdrawal_code', 'zone_code'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';
}
