<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class VATBusPostingGroupData
{
    const MODULE_VAT_BUS_POSTING_GROUP = "VAT BUS. POST GROUP";
    const MODULE_NAME_VAT_BUS_POSTING_GROUP = "VAT Business Posting Group";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        'Code' => 'code',
        'Description' => 'description',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'updated_by',
        'added_by',
        'msd_synced',
    );

    public $code;
    public $description;
    public $ms_dynamics_key;
    public $id;
    public $msd_synced;
    public $sys_21_synced;
    public $added_by;
    public $added_when;
    public $updated_by;
    public $updated_when;
    public $deleted_by;
    public $deleted_when;
    public $deleted;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, VATBusPostingGroupData::dict)) {
            $key_val = VATBusPostingGroupData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, VATBusPostingGroupData::dict)) {
            $key_val = VATBusPostingGroupData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, VATBusPostingGroupData::dict);
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
        return array_search($key, VATBusPostingGroupData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetVatBusPostingGroupCriteria xsi:type="urn:GetVatBusPostingGroupCriteria">';
        foreach (VATBusPostingGroupData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (VATBusPostingGroupData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetVatBusPostingGroupCriteria>';
        return  $xml_string;
    }
}
