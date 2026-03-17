<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Company $company
 * @property EmployeeStatus $employeeStatus
 * @property EmployeeType $employeeType
 * @property SalesOffice $salesOffice
 * @property Zone $zone
 * @property EmployeePoi[] $employeePois
 * @property Offtake[] $offtakes
 * @property string $employee_id
 * @property string $company_id
 * @property string $employee_status
 * @property string $employee_type
 * @property string $sales_office_id
 * @property string $default_zone_id
 * @property string $employee_code
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $address1
 * @property string $address2
 * @property string $barangay_id
 * @property string $home_phone_number
 * @property string $work_phone_number
 * @property string $birth_date
 * @property string $date_start
 * @property string $date_termination
 * @property string $password
 * @property string $supervisor_id
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 * @property string $user_define_field_1
 * @property string $user_define_field_2
 * @property string $user_define_field_3
 * @property string $email
 * @property boolean $deleted
 * @property string $sales_group_id
 * @property string $unique_employee_code
 */
class Employee extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'employee';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'employee_id';

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
    protected $fillable = ['company_id', 'employee_status', 'employee_type', 'sales_office_id', 'default_zone_id', 'employee_code', 'first_name', 'last_name', 'middle_name', 'address1', 'address2', 'barangay_id', 'home_phone_number', 'work_phone_number', 'birth_date', 'date_start', 'date_termination', 'password', 'supervisor_id', 'created_date', 'created_by', 'updated_date', 'updated_by', 'user_define_field_1', 'user_define_field_2', 'user_define_field_3', 'email', 'deleted', 'sales_group_id', 'unique_employee_code'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Model\wms\Company', 'company_id', 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employeeStatus()
    {
        return $this->belongsTo('App\Model\wms\EmployeeStatus', 'employee_status', 'employee_status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employeeType()
    {
        return $this->belongsTo('App\Model\wms\EmployeeType', 'employee_type', 'employee_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOffice()
    {
        return $this->belongsTo('App\Model\wms\SalesOffice', 'sales_office_id', 'sales_office_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo('App\Model\wms\Zone', 'default_zone_id', 'zone_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeePois()
    {
        return $this->hasMany('App\Model\wms\EmployeePoi', null, 'employee_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offtakes()
    {
        return $this->hasMany('App\Model\wms\Offtake', null, 'employee_id');
    }
}
