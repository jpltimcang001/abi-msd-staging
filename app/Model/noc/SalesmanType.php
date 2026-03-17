<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $added_by
 * @property string $added_when
 * @property string $code
 * @property boolean $deleted
 * @property string $deleted_by
 * @property string $deleted_when
 * @property string $description
 * @property string $edited_by
 * @property string $edited_when
 * @property string $ms_dynamics_key
 * @property boolean $msd_synced
 * @property boolean $sys_21_synced
 */
class SalesmanType extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'salesman_type';

    /**
     * @var array
     */
    protected $fillable = ['added_by', 'added_when', 'code', 'deleted', 'deleted_by', 'deleted_when', 'description', 'edited_by', 'edited_when', 'ms_dynamics_key', 'msd_synced', 'sys_21_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';
}
