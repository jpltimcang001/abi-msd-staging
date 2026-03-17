<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class SalesOfficeData
{
    const MODULE_SALES_OFFICE = "SALES OFFICE";
    const MODULE_NAME_SALES_OFFICE = "Sales Office";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array();

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'company',
		'no',
        'msd_synced',
        'deleted'
    );

    public $ms_dynamics_key;
    public $no;
    public $distributor_id;
    public $distributor_code;
    public $company;
    public $sales_office_no;
    public $sub_sales_office_no;
    public $short_description;
    public $code;
    public $name;
    public $short_name;
    public $market_no;
    public $type;
    public $manager;
    public $address1;
    public $address2;
    public $barangay_id;
    public $tin;
    public $fax_no;
    public $depot_code;
    public $longitude;
    public $latitude;
    public $tel_no;
    public $email;
    public $sys_21;
    public $centrix_synced;
    public $onesoas_synced;
    public $sys_21_synced;
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
        if (array_key_exists($key, SalesOfficeData::dict)) {
            $key_val = SalesOfficeData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, SalesOfficeData::dict)) {
            $key_val = SalesOfficeData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, SalesOfficeData::dict);
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
        return array_search($key, SalesOfficeData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetSalesOfficeCriteria xsi:type="urn:GetSalesOfficeCriteria">';
        foreach (SalesOfficeData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . (($this->$value) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (SalesOfficeData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . (($this->$value2) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetSalesOfficeCriteria>';
        return  $xml_string;
    }

    /**
     * Create XML.
     * 
     */
    public function xmlLineStrings()
    {
        $xml_string = '';
        foreach (SalesOfficeData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . (($this->$value) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (SalesOfficeData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . (($this->$value2) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '';
        return  $xml_string;
    }
}
