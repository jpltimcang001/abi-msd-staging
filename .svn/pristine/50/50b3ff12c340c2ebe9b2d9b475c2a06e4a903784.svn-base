<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class PromotionFOCData
{
    const MODULE_PROMOTION_FOC = "PROMOTION FOC";
    const MODULE_NAME_PROMOTION_FOC = "Promotion Foc Details";

    /**
     * Dictionary of MSD => NOC columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        "Scheme_No" => 'promotion_no',
        "Scheme_Item" => 'scheme_item',
        "Scheme_Item_UOM" => 'uom',
        "Scheme_Item_Quantity_Min" =>  'min',
        "Scheme_Item_Quantity_Max" =>  'max',
        "FOC_Item" =>  'foc_item',
        "Unit_Price" =>  'unit_price',
        "FOC_UOM" =>  'foc_uom',
        "FOC_Item_Desciption" =>  'foc_desc',
        "FOC_Item_Quantity" =>  'foc_qty',
        "FOC_Item2" => 'foc_item2',
        "FOC_UOM2" =>  'foc_uom2',
        "FOC_Item_Quantity2" => 'foc_item_qty2',
    );

    const dict_ext = array(
        'sales_office_no',
        'request_no'
    );

    public $promotion_no;
    public $ms_dynamics_key;
    public $name;
    public $scheme_item;
    public $uom;
    public $min;
    public $max;
    public $foc_item;
    public $unit_price;
    public $foc_uom;
    public $foc_desc;
    public $foc_qty;
    public $foc_item2;
    public $foc_uom2;
    public $foc_item_qty2;
    public $sales_office_no;
    public $request_no;


    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, PromotionFOCData::dict)) {
            $key_val = PromotionFOCData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, PromotionFOCData::dict)) {
            $key_val = PromotionFOCData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, PromotionFOCData::dict);
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
        return array_search($key, PromotionFOCData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetPromotionCriteria xsi:type="urn:GetPromotionCriteria">';
        foreach (PromotionFOCData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (PromotionFOCData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetPromotionCriteria>';
        return  $xml_string;
    }
}
