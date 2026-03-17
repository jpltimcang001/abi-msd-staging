<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $shipment_no
 * @property string $sales_order_no
 * @property string $collection_sync
 * @property string $inventory_sync
 */
class PickNote extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'pick_note';
	public $timestamps = false;
    /**
     * @var array
     */
    protected $fillable = ['shipment_no', 'sales_order_no', 'collection_sync', 'inventory_sync'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';
}
