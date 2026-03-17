<?php

/** 
 * Container for Invoice Data.  
 * 
 * */

namespace App\Data;

class InvoiceData
{
    const MODULE_INVOICE = "INVOICE";
    const MODULE_NAME_INVOICE = "Invoice";

    const MODULE_POSTED_INVOICE = "POSTED INVOICE";
    const MODULE_NAME_POSTED_INVOICE = "Posted Invoice";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        'No' => 'code',
        'Order_No' => 'sales_order_code',
        'Amount_Including_VAT' => 'amount',
        'Due_Date' => 'due_date',
        'CTSlip' => 'ct_slip'
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'invoice_date',
        'invoice_updated',
        'status',
        'delivered',
        'created_by',
        'updated_by',
        'msd_synced',
		'sales_office_no',
    );

    public $id;
    public $code;
    public $sales_order_code;
    public $sales_office_no;
    public $amount;
    public $invoice_date;
    public $invoice_updated;
    public $encoder_id;
    public $status;
    public $delivered;
    public $delivery_date;
    public $pricing_type;
    public $inv_type;
    public $created_date;
    public $created_by;
    public $updated_by;
    public $updated_date;
    public $deleted_by;
    public $deleted_date;
    public $deleted;
    public $confirmed_order;
    public $uploaded;
    public $tax_rate;
    public $invdeals_amt;
    public $missionary;
    public $extract_date;
    public $extract_datel;
    public $paid;
    public $pickup;
    public $proddiv_cd;
    public $con_on_loan;
    public $terms;
    public $ref_invoice;
    public $ar_fulls_amount;
    public $ar_mts_amount;
    public $refund;
    public $iatype;
    public $htype;
    public $htype_date;
    public $ct_slip;
    public $onesoas_synced;
    public $msd_synced;
    public $ms_dynamics_key;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, InvoiceData::dict)) {
            $key_val = InvoiceData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, InvoiceData::dict)) {
            $key_val = InvoiceData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, InvoiceData::dict);
    }

    /**
     * Get key via NOC name.
     * 
     */
    public function NOC($key)
    {
        if (isset($this->$key))
            return $this->$key;
        else
            return null;
    }

    /**
     * Gets the MSD name from NOC key
     * 
     */
    public function getMSDNamefromNoc($key)
    {
        return array_search($key, InvoiceData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetInvoiceCriteria xsi:type="urn:GetInvoiceCriteria">';
        foreach (InvoiceData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (InvoiceData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetInvoiceCriteria>';
        return  $xml_string;
    }
}
