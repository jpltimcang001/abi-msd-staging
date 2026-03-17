<?php

/** 
 * Container for OutgoingInventoryDetail Data.  
 * 
 * */

namespace App\Data;

class OutgoingInventoryDetailData
{
    const MODULE_OUTGOING_INVENTORY_DETAIL = "OUTGOING INVENTORY";
    const MODULE_NAME_OUTGOING_INVENTORY_DETAIL = "Outgoing Inventory";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array();

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'sku_code',
        'uom_code',
        'planned_quantity',
        'quantity_issued',
        'batch_no',
        'expiration_date',
        'line_no',
        'created_by',
        'updated_by',
    );

    public $outgoing_inventory_detail_id;
    public $outgoing_inventory_id;
    public $company_id;
    public $company_code;
    public $dr_no;
    public $inventory_id;
    public $batch_no;
    public $sku_id;
    public $sku_code;
    public $uom_id;
    public $uom_code;
    public $sku_status_id;
    public $sku_status_code;
    public $source_zone_id;
    public $source_zone_code;
    public $unit_price;
    public $expiration_date;
    public $planned_quantity;
    public $quantity_issued;
    public $amount;
    public $return_date;
    public $status;
    public $remarks;
    public $campaign_no;
    public $pr_no;
    public $pr_date;
    public $plan_arrival_date;
    public $revised_delivery_date;
    public $po_no;
    public $bo_class;
    public $bo_class2;
    public $comp_code;
    public $rtvdpr_no;
    public $bsrrf_bsms;
    public $bo_source;
    public $billing;
    public $line_no;
    public $entry_no;
    public $centrix_synced;
    public $onesoas_synced;
    public $sys_21_synced;
    public $msd_synced;
    public $created_by;
    public $created_date;
    public $updated_by;
    public $updated_date;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, OutgoingInventoryDetailData::dict)) {
            $key_val = OutgoingInventoryDetailData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, OutgoingInventoryDetailData::dict)) {
            $key_val = OutgoingInventoryDetailData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, OutgoingInventoryDetailData::dict);
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
        return array_search($key, OutgoingInventoryDetailData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetOutgoingInventoryDetailCriteria xsi:type="urn:GetOutgoingInventoryDetailCriteria">';
        foreach (OutgoingInventoryDetailData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (OutgoingInventoryDetailData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetOutgoingInventoryDetailCriteria>';
        return  $xml_string;
    }
}
