<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class SalesmanData
{
    const MODULE_SALESMAN = "SALESMAN";
    const MODULE_NAME_SALESMAN = "Sales Person";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        'Code' => 'code',
        'Name' => 'name',
        'Job_Title' => 'salesman_type_code',
        'E_Mail' => 'email',
        'Global_Dimension_1_Code' => 'short_description',
        'District' => 'district_code',
        'Sales_Group' => 'sales_group_code',
        'Location' => 'zone',
        'Cash_Batch' => 'cash_batch',
        'Cheque_Batch' => 'cheque_batch',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
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
    public $name;
    public $code;
    public $ms_dynamics_key;
    public $soas_code;
    public $gps_gate_code;
    public $district_id;
    public $district_code;
    public $salesman_type_id;
    public $salesman_type_code;
    public $salesman_type_description;
    public $sales_order_type;
    public $zone;
    public $mobile_number;
    public $sub_so_no;
    public $is_synced;
    public $is_synced2;
    public $sales_group_id;
    public $sales_group_code;
    public $unique_code;
    public $cmos_updated_version;
    public $division_code;
    public $password;
    public $email;
    public $distributor_name;
    public $address1;
    public $address2;
    public $tin_no;
    public $market_no;
    public $printer_mac_address;
    public $serial_no;
    public $machine_identification_no;
    public $ptu_no;
    public $ptu_date_issued;
    public $ptu_valid_until;
    public $SI_series;
    public $SO_series;
    public $CR_series;
    public $SI_reset_counter;
    public $SO_reset_counter;
    public $CR_reset_counter;
    public $cash_batch;
    public $cheque_batch;
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
        if (array_key_exists($key, SalesmanData::dict)) {
            $key_val = SalesmanData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, SalesmanData::dict)) {
            $key_val = SalesmanData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, SalesmanData::dict);
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
        return array_search($key, SalesmanData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetSalesmanCriteria xsi:type="urn:GetSalesmanCriteria">';
        foreach (SalesmanData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (SalesmanData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetSalesmanCriteria>';
        return  $xml_string;
    }

    /**
     * Create XML.
     * 
     */
    public function xmlLineStrings()
    {
        $xml_string = '';
        foreach (SalesmanData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . (($this->$value) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (SalesmanData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . (($this->$value2) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '';
        return  $xml_string;
    }
}
