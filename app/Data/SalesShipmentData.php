<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class SalesShipmentData
{
    const MODULE_SALES_SHIPMENT = "SALES SHIPMENT";
    const MODULE_NAME_SALES_SHIPMENT = "Sales Shipment";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
		"Key" => "msd_key",
		"No" => "no",
		"Sell_to_Customer_No" => "outlet_code",
		"Order_No" => "so_code",
		"Salesperson_Code" => "salesman_code",
		"Pick_No" => "pick_no",
		"Driver_No" => "driver_no",
		"Helper_No" => "helper_no",
		"Helper_2_Name" => "helper2_no",
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array();

    public $msd_key;
	public $no;
	public $outlet_code;
	public $so_code;
	public $salesman_code;
	public $pick_no;
	public $driver_code;
	public $helper_no;
	public $helper2_no;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, SalesShipmentData::dict)) {
            $key_val = SalesShipmentData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, SalesShipmentData::dict)) {
            $key_val = SalesShipmentData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, SalesShipmentData::dict);
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
        return array_search($key, SalesShipmentData::dict);
    }

	public function createParamsJson() {
		$data = [];
        foreach (SalesShipmentData::dict as $key => $value) {
			if(isset($this->$value) && !empty($this->$value)) {
				$data[$key] = htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
			}
        }
		return $data;
	}

}
