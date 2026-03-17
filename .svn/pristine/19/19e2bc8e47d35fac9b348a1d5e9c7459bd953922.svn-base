<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class PromotionLocationData
{
    const MODULE_PROMOTION_LOCATION = "PROMOTION LOCATION";
    const MODULE_NAME_PROMOTION_LOCATION = "Promotion Customer";

    /**
     * Dictionary of MSD => NOC columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        "Scheme_No" => 'promotion_no',
        "Customer_Code" => 'location_code',
        // "Customer_Name" => 'join location->name',
        "Scheme_Start_Date" => 'start_date',
        "Scheme_End_Date" => 'end_date',
        "WIN_Sale_office_Code" => 'sales_office_no',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'request_no',
        'sales_office_no',
        'added_by',
        'updated_by',
        'msd_synced',
    );

    public $id;
    public $distributor_id;
    public $ms_dynamics_key;
    public $distributor_code;
    public $sales_office_no;
    public $sub_sales_office_no;
    public $short_description;
    public $promotion_id;
    public $promotion_no;
    public $location_id;
    public $location_code;
    public $start_date;
    public $end_date;
    public $msd_synced;
    public $added_by;
    public $added_when;
    public $updated_by;
    public $updated_when;
    public $deleted_by;
    public $deleted_when;
    public $request_no;
    public $deleted;
    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, PromotionLocationData::dict)) {
            $key_val = PromotionLocationData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, PromotionLocationData::dict)) {
            $key_val = PromotionLocationData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, PromotionLocationData::dict);
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
        return array_search($key, PromotionLocationData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetPromotionLocationCriteria xsi:type="urn:GetPromotionLocationCriteria">';
        foreach (PromotionLocationData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (PromotionLocationData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetPromotionLocationCriteria>';
        return  $xml_string;
    }
}
