<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $added_by
 * @property string $added_when
 * @property float $amount
 * @property float $card_amount
 * @property string $card_authorization
 * @property string $card_date
 * @property string $card_name
 * @property string $card_no
 * @property float $cash_amount
 * @property float $cash_loose_amount
 * @property int $cash_quantity_1
 * @property int $cash_quantity_10
 * @property int $cash_quantity_100
 * @property int $cash_quantity_1000
 * @property int $cash_quantity_10c
 * @property int $cash_quantity_20
 * @property int $cash_quantity_200
 * @property int $cash_quantity_25c
 * @property int $cash_quantity_5
 * @property int $cash_quantity_50
 * @property int $cash_quantity_500
 * @property int $cash_quantity_50c
 * @property int $cash_quantity_5c
 * @property string $check_account_no
 * @property float $check_amount
 * @property string $check_bank
 * @property string $check_branch
 * @property string $check_date
 * @property string $check_no
 * @property string $check_type
 * @property string $chkrem_no
 * @property boolean $cont_dep
 * @property string $document_date
 * @property string $document_number
 * @property string $mode
 * @property float $other_amount
 * @property string $other_date
 * @property string $other_doc_no
 * @property string $other_remarks
 * @property string $other_type
 * @property string $reference_number
 * @property string $remarks
 * @property string $sales_order_date
 * @property int $sales_order_id
 * @property string $type
 * @property string $type_of_payment
 * @property string $updated_by
 * @property string $updated_when
 */
class TempCollectionCash extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'temp_collection_cash';
    // Specify the column name for the creation timestamp
    const CREATED_AT = 'added_when';

    // Specify the column name for the update timestamp
    const UPDATED_AT = 'updated_when';


    /**
     * @var array
     */
    protected $fillable = ['added_by', 'added_when', 'amount', 'card_amount', 'card_authorization', 'card_date', 'card_name', 'card_no', 'cash_amount', 'cash_loose_amount', 'cash_quantity_1', 'cash_quantity_10', 'cash_quantity_100', 'cash_quantity_1000', 'cash_quantity_10c', 'cash_quantity_20', 'cash_quantity_200', 'cash_quantity_25c', 'cash_quantity_5', 'cash_quantity_50', 'cash_quantity_500', 'cash_quantity_50c', 'cash_quantity_5c', 'check_account_no', 'check_amount', 'check_bank', 'check_branch', 'check_date', 'check_no', 'check_type', 'chkrem_no', 'cont_dep', 'document_date', 'document_number', 'mode', 'other_amount', 'other_date', 'other_doc_no', 'other_remarks', 'other_type', 'reference_number', 'remarks', 'sales_order_date', 'sales_order_id', 'type', 'type_of_payment', 'updated_by', 'updated_when'];

    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tempCollectionCashBreakdown()
    {
        return $this->hasMany('App\Model\noc\TempCollectionCashBreakdown', 'temp_collection_id', 'id');
    }
}
