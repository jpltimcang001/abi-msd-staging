<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Municipal $municipal
 * @property SalesOffice[] $salesOffices
 * @property int $id
 * @property int $municipal_id
 * @property string $name
 * @property int $population
 * @property int $household
 * @property string $latitude
 * @property string $longitude
 */
class Barangay extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'barangay';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['municipal_id', 'name', 'population', 'household', 'latitude', 'longitude'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function municipal()
    {
        return $this->belongsTo('App\Model\noc\Municipal');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOffices()
    {
        return $this->hasMany('App\Model\noc\SalesOffice');
    }
}
