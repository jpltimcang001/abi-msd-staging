<?php

/** 
 * Container for Sales Credit Memo Data.  
 * 
 * */

namespace App\Data;

class SalesCreditMemoLineData
{
    const MODULE_SALES_CREDIT_MEMO_LINE = "SALES CREDIT MEMO DET.";
    const MODULE_NAME_SALES_CREDIT_MEMO_LINE = "Sales Credit Memo Detail";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        "Type" => "type",
        "No" => "no",
        "Quantity" => "quantity",
        "Unit_Price" => "unit_price",
        "CT_Slip_No" => "ct_slip_no",
        "Empties_Type" => "empties_type",
        "Empties_SalInv_Line_No" => "empties_line_no",
        'Location_Code' => 'zone_code',
    );

    public $type;
    public $no;
    public $quantity;
    public $unit_price;
    public $ct_slip_no;
    public $empties_type;
    public $empties_line_no;
    public $zone_code;

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array();

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, SalesCreditMemoLineData::dict)) {
            $key_val = SalesCreditMemoLineData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, SalesCreditMemoLineData::dict)) {
            $key_val = SalesCreditMemoLineData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, SalesCreditMemoLineData::dict);
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
        return array_search($key, SalesCreditMemoLineData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function XMLlineStrings()
    {
        $xml_string = '<ns1:SalesCreditmemoLineService>';
        foreach (SalesCreditMemoLineData::dict as $key => $value) {
			if(empty($this->$value)) {
				continue;
			}
			else {
				$xml_string .= "<ns1:" . $key . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $key . ">\n";
			}
        }
        $xml_string .= '</ns1:SalesCreditmemoLineService>';
        return  $xml_string;
    }
}
