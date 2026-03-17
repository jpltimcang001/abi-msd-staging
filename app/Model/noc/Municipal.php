<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Province $province
 * @property Barangay[] $barangays
 * @property int $id
 * @property int $province_id
 * @property string $name
 */
class Municipal extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'municipal';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['province_id', 'name'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province()
    {
        return $this->belongsTo('App\Province');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function barangays()
    {
        return $this->hasMany('App\Barangay');
    }
}
