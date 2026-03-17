<?php

/** 
 * Container for Invoice Data.  
 * 
 * */

namespace App\Data;

class InvoiceDetailData
{
    const MODULE_INVOICE_DETAIL = "INVOICE DET";
    const MODULE_NAME_INVOICE_DETAIL = "Invoice Detail";

    const MODULE_POSTED_INVOICE_DETAIL = "POSTED INVOICE DET";
    const MODULE_NAME_POSTED_INVOICE_DETAIL = "Posted Invoice Detail";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        // "Key" 
        "Document_No" => 'inv_code',
        "Line_No" => 'line_no',
        "Quantity"  => 'serve_quantity',
        "Unit_Price" => 'unit_price',
        "Amount_Including_VAT" => 'amount',
        // "Sell_to_Customer_No"
        "Empties_Type" => 'empties_type',
        // "Variant_Code" 
        // "Description" 
        // "Description_2" 
        // "Shortcut_Dimension_1_Code" 
        // "Shortcut_Dimension_2_Code" 
        // "Balance_Empties" 
        // "Received_Empties" 
        // "Empties_Adjusted_Quantity" 
        // "Unit_of_Measure_Code" 
        // "Unit_of_Measure" 
        // "Unit_Cost_LCY" 
        // "Amount_Including_VAT" 
        // "Line_Discount_Percent" 
        // "Line_Discount_Amount" 
        // "Allow_Invoice_Disc" 
        // "Inv_Discount_Amount" 
        // "Appl_to_Item_Entry" 
        // "Job_No" 

    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'so_code',
        'product_code',
        'added_by',
        'edited_by',
    );

    public $inv_id;
    public $inv_code;
    public $so_code;
    public $product_code;
    public $serve_quantity;
    public $unit_price;
    public $amount;
    public $discount;
    public $pricing_type;
    public $disc_no;
    public $disc_amt;
    public $line_no;
    public $weight;
    public $empties_type;
    public $cbm;
    public $added_by;
    public $added_when;
    public $edited_by;
    public $edited_when;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, InvoiceDetailData::dict)) {
            $key_val = InvoiceDetailData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, InvoiceDetailData::dict)) {
            $key_val = InvoiceDetailData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, InvoiceDetailData::dict);
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
        return array_search($key, InvoiceDetailData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetInvoiceDetailCriteria xsi:type="urn:GetInvoiceDetailCriteria">';
        foreach (InvoiceDetailData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (InvoiceDetailData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetInvoiceDetailCriteria>';
        return  $xml_string;
    }
}
