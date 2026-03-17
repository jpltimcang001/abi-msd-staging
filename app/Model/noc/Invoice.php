<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property SalesOffice $salesOffice
 * @property InvoiceDetail[] $invoiceDetails
 * @property PaymentInvoice[] $paymentInvoices
 * @property PaymentInvoiceSub[] $paymentInvoiceSubs
 * @property int $id
 * @property string $sales_office_no
 * @property string $code
 * @property string $sales_order_code
 * @property float $amount
 * @property string $invoice_date
 * @property string $invoice_updated
 * @property int $encoder_id
 * @property string $status
 * @property int $delivered
 * @property string $delivery_date
 * @property string $pricing_type
 * @property string $inv_type
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 * @property string $deleted_date
 * @property string $deleted_by
 * @property boolean $deleted
 * @property string $confirmed_order
 * @property boolean $uploaded
 * @property float $tax_rate
 * @property float $invdeals_amt
 * @property boolean $missionary
 * @property string $extract_date
 * @property string $extract_date1
 * @property boolean $paid
 * @property boolean $pickup
 * @property string $proddiv_cd
 * @property boolean $con_on_loan
 * @property integer $terms
 * @property string $ref_invoice
 * @property float $ar_fulls_amount
 * @property float $ar_mts_amount
 * @property float $ar_fulls_payment
 * @property float $ar_mts_payment
 * @property boolean $refund
 * @property string $iatype
 * @property string $htype
 * @property string $htype_date
 * @property boolean $cts_slip
 * @property boolean $msd_synced
 * @property boolean $onesoas_synced
 */
class Invoice extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'invoice';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    /**
     * @var array
     */
    protected $fillable = ['sales_office_no', 'approval_code', 'approved_date', 'approved_by', 'code', 'sales_order_code', 'amount', 'invoice_date', 'invoice_updated', 'encoder_id', 'status', 'delivered', 'delivery_date', 'pricing_type', 'inv_type', 'created_date', 'created_by', 'updated_date', 'updated_by', 'deleted_date', 'deleted_by', 'deleted', 'confirmed_order', 'uploaded', 'tax_rate', 'invdeals_amt', 'missionary', 'extract_date', 'extract_date1', 'paid', 'pickup', 'proddiv_cd', 'con_on_loan', 'terms', 'ref_invoice', 'ar_fulls_amount', 'ar_mts_amount', 'ar_fulls_payment', 'ar_mts_payment', 'refund', 'iatype', 'htype', 'htype_date', 'cts_slip', 'msd_synced', 'onesoas_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOffice()
    {
        return $this->belongsTo('App\Model\noc\SalesOffice', 'sales_office_no', 'no');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOrder()
    {
        return $this->belongsTo('App\Model\noc\SalesOrder', 'sales_order_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceDetails()
    {
        return $this->hasMany('App\Model\noc\InvoiceDetails', 'inv_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentInvoices()
    {
        return $this->hasMany('App\Model\noc\PaymentInvoice');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentInvoiceSubs()
    {
        return $this->hasMany('App\Model\noc\PaymentInvoiceSub');
    }
}
