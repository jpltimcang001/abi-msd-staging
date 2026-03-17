<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $sales_office_no
 * @property string $name
 * @property string $added_when
 * @property string $added_by
 * @property string $edited_when
 * @property string $edited_by
 * @property string $deleted_when
 * @property string $deleted_by
 * @property boolean $deleted
 * @property string $supervisor_name
 * @property boolean $centrix_synced
 * @property boolean $onesoas_synced
 */
class District extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'district';

    /**
     * @var array
     */
    protected $fillable = ['code', 'sales_office_no', 'name', 'added_when', 'added_by', 'edited_when', 'edited_by', 'deleted_when', 'deleted_by', 'deleted', 'supervisor_name', 'centrix_synced', 'onesoas_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';
}
