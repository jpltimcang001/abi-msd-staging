<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $added_by
 * @property string $added_when
 * @property string $code
 * @property boolean $deleted
 * @property string $deleted_by
 * @property string $deleted_when
 * @property string $updated_by
 * @property string $updated_when
 */
class SalesGroup extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'sales_group';

    /**
     * @var array
     */
    protected $fillable = ['added_by', 'added_when', 'code', 'deleted', 'deleted_by', 'deleted_when', 'updated_by', 'updated_when'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';
}
