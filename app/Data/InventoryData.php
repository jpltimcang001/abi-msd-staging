<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class InventoryData
{
    const MODULE_INVENTORY = "INVENTORY";
    const MODULE_NAME_INVENTORY = "Stocks";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        "Location_Code"  => 'zone_code',
        "Lot_No"  => 'reference_no',
        "Remaining_Quantity" => 'qty',
        "Unit_of_Measure_Code" => 'uom_code',
        "Expiration_Date" => 'expiration_date',
		"Document_No" => 'document_no',
		"Document_Type" => 'document_type',
		"Wihdrawal_Code" => 'withdrawal_code',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'sku_code',
        'created_by',
        'updated_by',
    );

    public $inventory_id;
    public $company_id;
    public $sku_id;
    public $sku_code;
    public $cost_per_unit;
    public $qty;
    public $uom_id;
    public $uom_code;
    public $zone_id;
    public $zone_code;
    public $sku_status_id;
    public $sku_status_code;
    public $transaction_date;
    public $created_date;
    public $created_by;
    public $updated_by;
    public $updated_date;
    public $expiration_date;
    public $reference_no;
    public $campaign_no;
    public $pr_no;
    public $pr_date;
    public $plan_arrival_date;
    public $revised_delivery_date;
    public $po_no;
    public $transaction_reference_no;
    public $remarks;
	
	public $document_no;
	public $document_type;
	public $withdrawal_code;
	

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, InventoryData::dict)) {
            $key_val = InventoryData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, InventoryData::dict)) {
            $key_val = InventoryData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, InventoryData::dict);
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
        return array_search($key, InventoryData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetInventoryCriteria xsi:type="urn:GetInventoryCriteria">';
        foreach (InventoryData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (InventoryData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetInventoryCriteria>';
        return  $xml_string;
    }
}
