<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Location $location
 * @property CafApplCheckPayInfo[] $cafApplCheckPayInfos
 * @property CafApplCreditReqAnswer[] $cafApplCreditReqAnswers
 * @property CafAuthPer[] $cafAuthPers
 * @property int $id
 * @property int $location_id
 * @property string $added_by
 * @property string $added_when
 * @property boolean $application_type
 * @property string $cccr_no
 * @property boolean $centrix_synced
 * @property string $code
 * @property string $date
 * @property boolean $deleted
 * @property string $deleted_by
 * @property string $deleted_when
 * @property string $delivery_address
 * @property string $email_address
 * @property string $mobile_no
 * @property boolean $msd_synced
 * @property string $registered_address
 * @property string $registered_name
 * @property string $salesman_code
 * @property string $signature
 * @property string $sketch
 * @property boolean $status
 * @property boolean $sys_21_synced
 * @property string $telephone_no
 * @property string $tin
 * @property string $trade_name
 * @property string $updated_by
 * @property string $updated_when
 */
class CAF extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'caf';
    const CREATED_AT = 'added_when';
    const UPDATED_AT = 'updated_when';

    /**
     * @var array
     */
    protected $fillable = ['location_id', 'added_by', 'added_when', 'application_type', 'cccr_no', 'centrix_synced', 'code', 'date', 'deleted', 'deleted_by', 'deleted_when', 'delivery_address', 'email_address', 'mobile_no', 'msd_synced', 'registered_address', 'registered_name', 'salesman_code', 'signature', 'sketch', 'status', 'sys_21_synced', 'telephone_no', 'tin', 'trade_name', 'updated_by', 'updated_when'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('App\Model\noc\Location');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cafApplCheckPayInfos()
    {
        return $this->hasMany('App\Model\noc\CafApplCheckPayInfo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cafApplCreditReqAnswers()
    {
        return $this->hasMany('App\Model\noc\CafApplCreditReqAnswer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cafAuthPers()
    {
        return $this->hasMany('App\Model\noc\CafAuthPer');
    }
}
