<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $zone_name
 * @property string $sales_office_no
 * @property string $description
 * @property string $ms_dynamics_key
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 * @property string $deleted_date
 * @property string $deleted_by
 * @property boolean $deleted
 * @property boolean $sys_21_synced
 * @property boolean $msd_synced
 */
class Zone extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'zone';

    /**
     * @var array
     */
    protected $fillable = ['zone_name', 'sales_office_no', 'description', 'ms_dynamics_key', 'created_date', 'created_by', 'updated_date', 'updated_by', 'deleted_date', 'deleted_by', 'deleted', 'sys_21_synced', 'msd_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';
}
