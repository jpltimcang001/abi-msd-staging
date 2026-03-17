<?php

/** 
 * Container for Invoice Data.  
 * 
 * */

namespace App\Data;

class BalanceData
{
    const MODULE_BALANCE = "BALANCE";
    const MODULE_NAME_BALANCE = "Balance";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        'Document_No' => 'document_no',
        'Customer_No' => 'customer_no',
        'External_Document_No' => 'reference',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'added_by',
        'updated_by',
        'deleted_by',
        'amount_mts',
        'amount_fulls',
        'balance_mts',
        'balance_fulls',
        'msd_synced',
    );

    public $id;
    public $document_no;
    public $amount_mts;
    public $amount_fulls;
    public $balance_mts;
    public $balance_fulls;
    public $customer_no;
    public $reference;
    public $msd_synced;
    public $ms_dynamics_key;
    public $added_by;
    public $edited_by;
    public $deleted_by;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, BalanceData::dict)) {
            $key_val = BalanceData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, BalanceData::dict)) {
            $key_val = BalanceData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, BalanceData::dict);
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
        return array_search($key, BalanceData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetBalanceCriteria xsi:type="urn:GetBalanceCriteria">';
        foreach (BalanceData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (BalanceData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetBalanceCriteria>';
        return  $xml_string;
    }
}
