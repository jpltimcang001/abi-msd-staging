<?php

/** 
 * Container for Stock Conversion Data.
 * 
 * */

namespace App\Data;

class StockConversionData
{
    const MODULE_STOCK_CONVERSION = "STOCK CONVERSION";
    const MODULE_NAME_STOCK_CONVERSION = "Stock Conversion";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        'documentNo' => 'code',
        'itemNo' => 'sku_code',
        'locationCode' => 'zone_code',
        'qty' => 'quantity',
        'lotNo' => 'lot_no',
        'sPCode' => 'salesman_code',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'sku_code',
    );

    public $code;
    public $sku_code;
    public $zone_code;
    public $quantity;
    public $lot_no;
    public $salesman_code;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, StockConversionData::dict)) {
            $key_val = StockConversionData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, StockConversionData::dict)) {
            $key_val = StockConversionData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, StockConversionData::dict);
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
        return array_search($key, StockConversionData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlMSDArrayString()
    {
        $xml_string = '<ns1:StockSplit>';
        foreach (StockConversionData::dict as $key => $value) {
            $xml_string .= "<ns1:" . $key . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $key . ">\n";
        }
        $xml_string .= '</ns1:StockSplit>';
        return  $xml_string;
    }
}
