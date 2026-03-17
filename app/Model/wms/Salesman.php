<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $salesman_id
 * @property string $team_leader_id
 * @property string $company_id
 * @property string $salesman_name
 * @property string $salesman_code
 * @property string $sales_office_id
 * @property string $zone_id
 * @property string $mobile_number
 * @property string $device_no
 * @property string $other_fields_1
 * @property string $other_fields_2
 * @property string $other_fields_3
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 * @property string $is_team_leader
 */
class Salesman extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'salesman';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'salesman_id';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['team_leader_id', 'company_id', 'salesman_name', 'salesman_code', 'sales_office_id', 'zone_id', 'mobile_number', 'device_no', 'other_fields_1', 'other_fields_2', 'other_fields_3', 'created_date', 'created_by', 'updated_date', 'updated_by', 'is_team_leader'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';
}
