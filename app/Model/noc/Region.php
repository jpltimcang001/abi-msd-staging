<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property SalesRegion $salesRegion
 * @property Province[] $provinces
 * @property int $id
 * @property int $sales_region_id
 * @property string $name
 */
class Region extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'region';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['sales_region_id', 'name'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesRegion()
    {
        return $this->belongsTo('App\Model\noc\SalesRegion');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function provinces()
    {
        return $this->hasMany('App\Model\noc\Province');
    }
}
