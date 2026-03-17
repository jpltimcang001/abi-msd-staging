<?php

/** 
 * Container for PSER Data.  
 * 
 * */

namespace App\Data;

class PserData
{
    const MODULE_PSER = "PSER";
    const MODULE_NAME_PSER = "PSER";

    /**
     * Dictionary of MSD => NOC columns.
     */
    const dict = array();

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'sales_office_no',
        'short_description',
        'location_code',
        'so_per_cd',
        'pser_ho_code',
        'so_dt_rqstd',
        'due_date',
        'sku_required',
        'pser_title',
        'brf_desc',
        'added_by',
        'edited_by',
        'msd_synced',
    );

    public $id;
    public $distributor_id;
    public $distributor_code;
    public $sales_office_no;
    public $sub_sales_office_no;
    public $short_description;
    public $salesman_code;
    public $location_id;
    public $location_code;
    public $comp_code;
    public $so_per_cd;
    public $pser_ho_code;
    public $date;
    public $so_dt_rqstd;
    public $due_date;
    public $brand_required;
    public $sku_required;
    public $pser_title;
    public $brf_desc;
    public $msd_synced;
    public $added_by;
    public $added_when;
    public $edited_by;
    public $edited_when;
    public $deleted_by;
    public $deleted_when;
    public $deleted;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, PserData::dict)) {
            $key_val = PserData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, PserData::dict)) {
            $key_val = PserData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, PserData::dict);
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
        return array_search($key, PserData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetPserCriteria xsi:type="urn:GetPserCriteria">';
        foreach (PserData::dict as $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (PserData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetPserCriteria>';
        return  $xml_string;
    }
}
