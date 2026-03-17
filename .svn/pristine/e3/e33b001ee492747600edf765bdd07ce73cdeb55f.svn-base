<?php

/** 
 * Container for TransferOrderData.  
 * 
 * */

namespace App\Data;

class TransferOrderData
{
    const MODULE = "TRANSFER ORDER";
    const MODULE_NAME= "Transfer Order";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        "No" => "inventory_return_code",
        "PostingDate" => "inventory_return_date",
        "ShipmentDate" => "inventory_return_date",
        "TransferfromCode" => "employee_id",
        "TransfertoCode" => "so_zone",
        "TransferOrdertype" => "order_type",
        "ShortcutDimension1Code" => "so_code",
        "Salesman" => "employee_id",
		"DirectTransfer" => "direct_transfer"
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array();

    public $inventory_return_code;
    public $inventory_return_date;
    public $employee_id;
    public $so_zone;
    public $so_code;
    public $order_type;
	public $direct_transfer = "true";
    public $transferOrderDetail = [];

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, TransferOrderData::dict)) {
            $key_val = TransferOrderData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, TransferOrderData::dict)) {
            $key_val = TransferOrderData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, TransferOrderData::dict);
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
        return array_search($key, TransferOrderData::dict);
    }
	
	
    /**
     * Create XML array for MSD save request.
     * 
     */
    public function xmlMSDUpdateArrayString($with_detail = true)
    {
        $xml_string = '<ns1:TransferOrderService>';
        foreach (TransferOrderData::dict as $key => $value) {
			if(!isset($this->$value) || $this->$value === "" || $this->$value === null)
				continue;
            $xml_string .= "<ns1:" . $key . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $key . ">\n";
        }
        if (count($this->transferOrderDetail) > 0 && $with_detail) {
            $xml_string .= "<ns1:TransferLines>";
            foreach ($this->transferOrderDetail as $value3) {
                $xml_string .= $value3->xmlMSDUpdateArrayString();
            }
            $xml_string .= "</ns1:TransferLines>";
        }
        $xml_string .= '</ns1:TransferOrderService>';
        return  $xml_string;
    }
}
