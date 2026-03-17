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
class BalanceEmpties extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'balance_empties';
    const CREATED_AT = 'added_when';
    const UPDATED_AT = 'edited_when';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['document_no', 'customer_no', 'product_code', 'quantity', 'balance_empties', 'ms_dynamics_key', 'added_by', 'updated_by', 'deleted_by', 'deleted'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';
}
