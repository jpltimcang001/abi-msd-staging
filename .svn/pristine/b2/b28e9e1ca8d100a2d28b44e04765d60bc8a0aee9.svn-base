<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $added_by
 * @property string $added_when
 * @property float $containers_amount
 * @property float $contents_amount
 * @property int $sales_order_id
 * @property int $temp_collection_id
 * @property float $total_amount
 * @property string $updated_by
 * @property string $updated_when
 */
class TempCollectionCashBreakdown extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'temp_collection_cash_breakdown';
    // Specify the column name for the creation timestamp
    const CREATED_AT = 'added_when';

    // Specify the column name for the update timestamp
    const UPDATED_AT = 'updated_when';

    /**
     * @var array
     */
    protected $fillable = ['added_by', 'added_when', 'containers_amount', 'contents_amount', 'sales_order_id', 'temp_collection_id', 'total_amount', 'updated_by', 'updated_when'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tempCollectionCash()
    {
        return $this->belongsTo('App\Model\noc\TempCollectionCash', 'temp_collection_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOrder()
    {
        return $this->belongsTo('App\Model\noc\SalesOrder', 'sales_order_id');
    }
}
