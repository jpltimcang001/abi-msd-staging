<?php

/** 
 * Container for TransferOrderDetailData.  
 * 
 * */

namespace App\Data;

class TransferOrderDetailData
{
    const MODULE = "TRANSFER ORDER Detail";
    const MODULE_NAME= "Transfer Order Detail";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        "Item_No" => "sku_code",
        "Quantity" => "quantity",
        "Shortcut_Dimension_1_Code" => "so_code",
        "Shipment_Date" => "shipment_date",
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array();

    public $sku_code;
    public $quantity;
    public $so_code;
    public $shipment_date;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, TransferOrderDetailData::dict)) {
            $key_val = TransferOrderDetailData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, TransferOrderDetailData::dict)) {
            $key_val = TransferOrderDetailData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, TransferOrderDetailData::dict);
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
        return array_search($key, TransferOrderDetailData::dict);
    }
	
	
    /**
     * Create XML array for MSD save request.
     * 
     */
    public function xmlMSDUpdateArrayString($with_detail = true)
    {
        $xml_string = '<ns1:Transfer_Order_Line>';
        foreach (TransferOrderDetailData::dict as $key => $value) {
			if(!isset($this->$value) || $this->$value === "" || $this->$value === null)
				continue;
            $xml_string .= "<ns1:" . $key . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $key . ">\n";
        }
        $xml_string .= '</ns1:Transfer_Order_Line>';
        return  $xml_string;
    }
}
