<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Region $region
 * @property Municipal[] $municipals
 * @property int $id
 * @property int $region_id
 * @property string $name
 * @property float $latitude
 * @property float $longitude
 */
class Province extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'province';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['region_id', 'name', 'latitude', 'longitude'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo('App\Model\noc\Region');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function municipals()
    {
        return $this->hasMany('App\Model\noc\Municipal');
    }
}
