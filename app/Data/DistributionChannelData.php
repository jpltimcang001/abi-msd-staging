<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class DistributionChannelData
{
    const MODULE_DISTRIBUTION_CHANNEL = "CHANNEL";
    const MODULE_NAME_DISTRIBUTION_CHANNEL = "Channel";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        'WIN_Code' => 'description',
        'WIN_Description' => 'code',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'name',
        'added_by',
        'edited_by',
        'msd_synced',
    );

    public $code;
    public $description;
    public $name;
    public $ms_dynamics_key;
    public $id;
    public $added_by;
    public $added_when;
    public $edited_by;
    public $edited_when;
    public $deleted_by;
    public $deleted_when;
    public $deleted;
    public $classification_id;
    public $classification_code;
    public $centrix_synced;
    public $onesoas_synced;
    public $sys_21_synced;
    public $msd_synced;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, DistributionChannelData::dict)) {
            $key_val = DistributionChannelData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, DistributionChannelData::dict)) {
            $key_val = DistributionChannelData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, DistributionChannelData::dict);
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
        return array_search($key, DistributionChannelData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetChannelTypeCriteria xsi:type="urn:GetChannelTypeCriteria">';
        foreach (DistributionChannelData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (DistributionChannelData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetChannelTypeCriteria>';
        return  $xml_string;
    }
}
