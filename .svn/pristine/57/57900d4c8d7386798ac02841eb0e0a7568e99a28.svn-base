<?php

/** 
 * Container for CashReceipt Data.  
 * 
 * */

namespace App\Data;

class CashEmptiesAdjustmentsData
{
    const MODULE_CASH_RECEIPT_EMPTIES = "CASH RECEIPT EMPTIES";
    const MODULE_NAME_CASH_RECEIPT_EMPTIES = "Cash Receipt Empties";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        "Document_Date" => "document_date",
        "Posting_Date" => "posting_date",
        "Document_Type" => "document_type",
        "Account_Type" => "account_type",
        "Account_No" => "account_no",
		"No" => "sku_code",
        "Amount" => "amount",
        "Applies_to_Doc_Type" => "applies_to_doc_type",
        "Applies_to_Doc_No" => "applies_to_doc_no",
        "Salespers_Purch_Code" => "zone_code",
        "Check_No" => "check_no",
        "Check_Date" => "check_date",
        "Comments" => "check_bank",
		"Posting_Group" => "posting_group",
		"CT_Slip_No" => "ct_slip_no",
		"Empties_Type" => 'empties_type'
    );

    public $document_date;
    public $posting_date;
    public $document_type;
    public $account_type;
    public $account_no;
    public $amount;
    public $applies_to_doc_type;
    public $applies_to_doc_no;
    public $batch_name;
	public $zone_code;
	public $check_bank;
	public $check_no;
	public $ct_slip_no;
	public $check_date;
	public $posting_group;
	public $sku_code;
	public $empties_type;

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array();


    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, CashEmptiesAdjustmentsData::dict)) {
            $key_val = CashEmptiesAdjustmentsData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, CashEmptiesAdjustmentsData::dict)) {
            $key_val = CashEmptiesAdjustmentsData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, CashEmptiesAdjustmentsData::dict);
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
        return array_search($key, CashEmptiesAdjustmentsData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<ns1:CurrentJnlBatchName>' . $this->batch_name . '</ns1:CurrentJnlBatchName>';
        $xml_string .= '<ns1:CashEmptiesAdjustments>';
        foreach (CashEmptiesAdjustmentsData::dict as $key => $value) {
			if($this->$value != "" && $this->$value != null) {
				$xml_string .= "<ns1:" . $key . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $key . ">\n";
			}
        }
        $xml_string .= '</ns1:CashEmptiesAdjustments>';
        return  $xml_string;
    }
}
