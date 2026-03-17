<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Location $location
 * @property int $id
 * @property int $location_id
 * @property string $ms_dynamics_key
 * @property string $system_21_key
 * @property string $trade_name
 * @property string $search_name
 * @property string $business_style
 * @property string $address3
 * @property string $address4
 * @property string $address5
 * @property string $delivery_address_code
 * @property string $price_list_code
 * @property string $stockroom
 * @property string $vat_code
 * @property string $payment_term_code
 * @property string $currency_code
 * @property string $invoice_address_code
 * @property string $vat_registration_no
 * @property string $gen_bus_posting_group
 * @property string $vat_bus_posting_group
 * @property string $wht_business_posting_group
 * @property string $customer_posting_group
 * @property string $customer_price_group
 * @property string $customer_disc_group
 * @property boolean $allow_line_disc
 * @property boolean $prices_including_vat
 * @property string $payment_terms_code
 * @property string $payment_method_code
 * @property float $balance_mts
 * @property boolean $approval_status
 * @property string $ext
 * @property float $balance_fulls
 * @property string $comments
 * @property string $signature
 * @property string $as_tel_no_1
 * @property string $as_ext_1
 * @property string $as_email_address_1
 * @property string $as_signature_1
 * @property string $as_tel_no_2
 * @property string $as_ext_2
 * @property string $as_email_address_2
 * @property string $as_signature_2
 * @property string $cp_tel_no_1
 * @property string $cp_ext_1
 * @property string $cp_email_address_1
 * @property string $cp_signature_1
 * @property string $cp_tel_no_2
 * @property string $cp_ext_2
 * @property string $cp_email_address_2
 * @property string $cp_signature_2
 * @property string $added_by
 * @property string $added_when
 * @property string $updated_by
 * @property string $updated_when
 */
class LocationDetail extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'location_details';
    const CREATED_AT = 'added_when';
    const UPDATED_AT = 'updated_when';
	
    /**
     * @var array
     */
    protected $fillable = ['location_id', 'ms_dynamics_key', 'system_21_key', 'trade_name', 'search_name', 'business_style', 'address3', 'address4', 'address5', 'delivery_address_code', 'price_list_code', 'stockroom', 'vat_code', 'payment_term_code', 'currency_code', 'invoice_address_code', 'vat_registration_no', 'gen_bus_posting_group', 'vat_bus_posting_group', 'wht_business_posting_group', 'customer_posting_group', 'customer_price_group', 'customer_disc_group', 'allow_line_disc', 'prices_including_vat', 'payment_terms_code', 'payment_method_code', 'balance_mts', 'approval_status', 'ext', 'balance_fulls', 'comments', 'signature', 'as_tel_no_1', 'as_ext_1', 'as_email_address_1', 'as_signature_1', 'as_tel_no_2', 'as_ext_2', 'as_email_address_2', 'as_signature_2', 'cp_tel_no_1', 'cp_ext_1', 'cp_email_address_1', 'cp_signature_1', 'cp_tel_no_2', 'cp_ext_2', 'cp_email_address_2', 'cp_signature_2', 'synced_when', 'approved_when', 'added_by', 'added_when', 'updated_by', 'updated_when'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo('App\Model\noc\Location');
    }
}
