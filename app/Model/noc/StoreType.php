<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $added_by
 * @property string $added_when
 * @property boolean $centrix_staging_iom_synced
 * @property string $code
 * @property boolean $deleted
 * @property string $deleted_by
 * @property string $deleted_when
 * @property string $description
 * @property string $distribution_channel_id
 * @property string $edited_by
 * @property string $edited_when
 * @property boolean $onesoas_synced
 */
class StoreType extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'store_type';

    /**
     * @var array
     */
    protected $fillable = ['added_by', 'added_when', 'centrix_staging_iom_synced', 'code', 'deleted', 'deleted_by', 'deleted_when', 'description', 'distribution_channel_id', 'edited_by', 'edited_when', 'onesoas_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';
}
