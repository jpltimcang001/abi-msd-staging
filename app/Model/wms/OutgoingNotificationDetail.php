<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property OutgoingNotification $outgoingNotification
 * @property int $outgoing_notification_detail_id
 * @property int $outgoing_notification_id
 * @property string $company_id
 * @property string $batch_no
 * @property string $sku_code
 * @property int $request_quantity
 * @property string $remarks
 * @property string $line_no
 * @property string $entry_no
 * @property string $ms_dynamics_key
 * @property string $created_by
 * @property string $created_date
 * @property string $updated_by
 * @property string $updated_date
 */
class OutgoingNotificationDetail extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'outgoing_notification_detail';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'outgoing_notification_detail_id';

    /**
     * @var array
     */
    protected $fillable = ['outgoing_notification_id', 'company_id', 'sales_office_code', 'batch_no', 'sku_code', 'request_quantity', 'remarks', 'expiration_date', 'line_no', 'entry_no', 'ms_dynamics_key', 'created_by', 'created_date', 'updated_by', 'updated_date'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function outgoingNotification()
    {
        return $this->belongsTo('App\Model\wms\OutgoingNotification', 'outgoing_notification_id', 'outgoing_notification_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nocSku()
    {
        return $this->belongsTo('App\Model\noc\Sku', 'sku_code', 'code')->where('sales_office_no', $this->outgoingNotification()->first()->sales_office_code);
    }
}
