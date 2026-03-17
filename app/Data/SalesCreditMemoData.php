<?php

/** 
 * Container for Sales Credit Memo Data.  
 * 
 * */

namespace App\Data;

class SalesCreditMemoData
{
    const MODULE_SALES_CREDIT_MEMO = "SALES CREDIT MEMO";
    const MODULE_NAME_SALES_CREDIT_MEMO = "Sales Credit Memo";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        "CTSlip" => "ct_slip",
        "Sell_to_Customer_No" => "location_code",
        "Empties_Type" => "empties_type",
        "Reason_Code" => "reason_code",
        "Document_Date" => "document_date",
        "Due_Date" => "due_date",
        "Salesperson_Code" => "salesman_code",
        "Applies_to_Doc_Type" => "applies_to_doc_type",
        "Applies_to_ID" => "applies_to_id",
        "Applies_to_Doc_No" => "applies_to_doc_no",
    );

    public $ct_slip;
    public $location_code;
    public $empties_type;
    public $reason_code;
    public $document_date;
    public $due_date;
    public $salesman_code;
    public $applies_to_doc_type;
    public $applies_to_doc_no;
    public $applies_to_id;
    public $salesCreditMemoLineData = [];

	public $tempSalesReturnableId;
    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array();


    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, SalesCreditMemoData::dict)) {
            $key_val = SalesCreditMemoData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, SalesCreditMemoData::dict)) {
            $key_val = SalesCreditMemoData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, SalesCreditMemoData::dict);
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
        return array_search($key, SalesCreditMemoData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<ns1:SalesCreditMemoService>';
        foreach (SalesCreditMemoData::dict as $key => $value) {
			if(!isset($this->$value) || $this->$value === NULL){
				continue;
			}
            $xml_string .= "<ns1:" . $key . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $key . ">\n";
        }
        if (count($this->salesCreditMemoLineData) > 0) {
            $xml_string .= "<ns1:SalesCrMemoLines>";
            foreach ($this->salesCreditMemoLineData as $value3) {
                $xml_string .= $value3->XMLlineStrings();
            }
            $xml_string .= "</ns1:SalesCrMemoLines>";
        }
        $xml_string .= '</ns1:SalesCreditMemoService>';
        return  $xml_string;
    }
}
