<?php

/** 
 * Container for OutgoingInventory Data.  
 * 
 * */

namespace App\Data;

class OutgoingInventoryData
{
    const MODULE_OUTGOING_INVENTORY = "OUTGOING INVENTORY";
    const MODULE_NAME_OUTGOING_INVENTORY = "Outgoing Inventory";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array();

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'company_code',
        'dr_no',
        'source_zone_code',
        'destination_zone_code',
        'created_by',
        'updated_by',
        'msd_synced',
    );

    public $outgoing_inventory_id;
    public $company_id;
    public $company_code;
    public $dr_no;
    public $sales_office_no;
    public $salesman_code;
    public $dr_date;
    public $rra_no;
    public $rra_date;
    public $source_zone_id;
    public $source_zone_code;
    public $destination_zone_id;
    public $destination_zone_code;
    public $contact_person;
    public $contact_no;
    public $address;
    public $plan_delivery_date;
    public $transaction_date;
    public $status;
    public $remarks;
    public $remarks2;
    public $total_amount;
    public $closed;
    public $recipients;
    public $internal_code;
    public $is_synced;
    public $uploaded;
    public $vehicle_no;
    public $extract_date;
    public $extract_date1;
    public $applied_date;
    public $emp_no;
    public $driver_emp_no;
    public $helper_emp_no;
    public $helper_emp_no2;
    public $tpart_no;
    public $sales_order_no;
    public $driver;
    public $comp_no;
    public $truck_no;
    public $plate_no;
    public $diversion;
    public $hauler_code;
    public $centrix_synced;
    public $onesoas_synced;
    public $sys_21_synced;
    public $msd_synced;
    public $created_by;
    public $created_date;
    public $updated_by;
    public $updated_date;
    public $outgoingInventoryDetail = [];

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, OutgoingInventoryData::dict)) {
            $key_val = OutgoingInventoryData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, OutgoingInventoryData::dict)) {
            $key_val = OutgoingInventoryData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, OutgoingInventoryData::dict);
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
        return array_search($key, OutgoingInventoryData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetOutgoingInventoryCriteria xsi:type="urn:GetOutgoingInventoryCriteria">';
        foreach (OutgoingInventoryData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (OutgoingInventoryData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        if (count($this->outgoingInventoryDetail) > 0) {
            $xml_string .= '<outgoingInventoryDetail xsi:type="soapenc:Array" soap-enc:arrayType="urn:GetOutgoingInventoryDetailCriteria[]" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/">';
            foreach ($this->outgoingInventoryDetail as $value3) {
                $xml_string .= $value3->xmlArrayLineStrings();
            }
            $xml_string .= "</outgoingInventoryDetail>";
        }
        $xml_string .= '</GetOutgoingInventoryCriteria>';
        return  $xml_string;
    }
}
