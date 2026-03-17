<?php

/** 
 * Container for Invoice Data.  
 * 
 * */

namespace App\Data;

class BalanceEmptiesData
{
    const MODULE_BALANCE_EMPTIES = "BALANCE EMPTIES";
    const MODULE_NAME_BALANCE_EMPTIES = "Balance";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        'Document_No' => 'invoice_code',
        'Line_No' => 'line_no',
        'No' => 'sku_code',
        'Balance_Empties' => 'balance_empties',
        'Shortcut_Dimension_1_Code' => 'short_code',
		'Sell_to_Customer_No' => 'location_code',
		'Quantity' => 'quantity',
		'Balance_Empties' => 'balance_empties',
		'Key' => 'ms_dynamics_key'
    );


    public $invoice_code;
    public $line_no;
    public $sku_code;
    public $balance_empties;
    public $short_code;
	public $location_code;
	public $ms_dynamics_key;
	public $quantity;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, BalanceEmptiesData::dict)) {
            $key_val = BalanceEmptiesData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, BalanceEmptiesData::dict)) {
            $key_val = BalanceEmptiesData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, BalanceEmptiesData::dict);
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
        return array_search($key, BalanceEmptiesData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetBalanceCriteria xsi:type="urn:GetBalanceCriteria">';
        foreach (BalanceEmptiesData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        $xml_string .= '</GetBalanceCriteria>';
        return  $xml_string;
    }
}
