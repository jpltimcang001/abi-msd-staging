<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class SubChannelData
{
    const MODULE_SUB_CHANNEL = "STORE TYPE";
    const MODULE_NAME_SUB_CHANNEL = "Store Type";

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
        'channel_type_id',
        'channel_type_code',
        'added_by',
        'edited_by',
        'msd_synced',
    );

    public $id;
    public $code;
    public $description;
    public $channel_type_id;
    public $channel_type_code;
    public $added_by;
    public $edited_by;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, SubChannelData::dict)) {
            $key_val = SubChannelData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, SubChannelData::dict)) {
            $key_val = SubChannelData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, SubChannelData::dict);
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
        return array_search($key, SubChannelData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetSubChannelTypeCriteria xsi:type="urn:GetSubChannelTypeCriteria">';
        foreach (SubChannelData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">";
        }
        foreach (SubChannelData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">";
        }
        $xml_string .= '</GetSubChannelTypeCriteria>';
        return  $xml_string;
    }
}
