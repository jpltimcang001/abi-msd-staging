<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property OutgoingNotificationDetail[] $outgoingNotificationDetails
 * @property int $outgoing_notification_id
 * @property string $company_id
 * @property string $employee_code
 * @property string $sales_office_code
 * @property string $withdrawal_code
 * @property string $withdrawal_date
 * @property string $document_no
 * @property string $status
 * @property int $transaction_type
 * @property string $confirmed
 * @property string $created_by
 * @property string $created_date
 * @property string $updated_by
 * @property string $updated_date
 * @property boolean $uploaded
 * @property boolean $onesoas_synced
 * @property boolean $msd_synced
 */
class OutgoingNotification extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'outgoing_notification';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'outgoing_notification_id';

    /**
     * @var array
     */
    protected $fillable = ['company_id', 'employee_code', 'sales_office_code', 'withdrawal_code', 'withdrawal_date', 'document_no','status', 'transaction_type', 'confirmed', 'ms_dynamics_key', 'created_by', 'created_date', 'updated_by', 'updated_date', 'uploaded', 'onesoas_synced', 'msd_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function outgoingNotificationDetails()
    {
        return $this->hasMany('App\Model\wms\OutgoingNotificationDetail', 'outgoing_notification_id', 'outgoing_notification_id');
    }
}
