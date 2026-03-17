<?php

/** 
 * Container for Zone Data.  
 * 
 * */

namespace App\Data;

class ZoneData
{
    const MODULE_ZONE = "ZONE";
    const MODULE_NAME_ZONE = "Location";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        "Key"  => 'ms_dynamics_key',
        "Code"  => 'zone_code',
        "Name"  =>  'description',
        // "Location_Type" => "",
        "Sales_Office"  => 'short_description',
        // "Return_Location" =>  "",
        // "Return_Location_code"  =>  "",
        // "OPCO_Dealer_Code"  =>  "",
        // "PO_Dealer_Code"  =>  "",

    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'company_code',
        'zone_name',
        'is_default_zone',
        'created_by',
        'updated_by',
        'msd_synced',
    );

    public $id;
    public $company_id;
    public $company_code;
    public $distributor_id;
    public $distributor_code;
    public $ms_dynamics_key;
    public $sales_office_no;
    public $sub_sales_office_no;
    public $short_description;
    public $is_default_zone;
    public $zone_code;
    public $zone_name;
    public $description;
    public $aisle_id;
    public $aisle_code;
    public $shelf_id;
    public $shelf_code;
    public $level_id;
    public $level_code;
    public $created_date;
    public $created_by;
    public $updated_by;
    public $updated_date;
    public $deleted_by;
    public $deleted_date;
    public $deleted;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, ZoneData::dict)) {
            $key_val = ZoneData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, ZoneData::dict)) {
            $key_val = ZoneData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, ZoneData::dict);
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
        return array_search($key, ZoneData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetZoneCriteria xsi:type="urn:GetZoneCriteria">';
        foreach (ZoneData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (ZoneData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetZoneCriteria>';
        return  $xml_string;
    }
}
