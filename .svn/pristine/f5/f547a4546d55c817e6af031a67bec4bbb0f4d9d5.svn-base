<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class PromotionDetailData
{
    const MODULE_PROMOTION_DETAIL = "PROMOTION DET";
    const MODULE_NAME_PROMOTION_DETAIL = "Promotion Line";

    /**
     * Dictionary of MSD => NOC columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        "Scheme_No" => 'promotion_no',
        "Unit_Price" => 'unit_price',
        "Item_UOM" => 'uom',
        // "Item_Description" => 'join sku->name',
        "Scheme_Start_Date" => 'start_date',
        "Scheme_End_Date" => 'end_date',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'product_no',
        'added_by',
        'updated_by',
        'msd_synced',
    );

    public $id;
    public $ms_dynamics_key;
    public $promotion_id;
    public $promotion_no;
    public $product_no;
    public $uom;
    public $start_date;
    public $end_date;
    public $msd_synced;
    public $added_by;
    public $added_when;
    public $updated_by;
    public $updated_when;
    public $deleted_by;
    public $deleted_when;
    public $deleted;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, PromotionDetailData::dict)) {
            $key_val = PromotionDetailData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, PromotionDetailData::dict)) {
            $key_val = PromotionDetailData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, PromotionDetailData::dict);
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
        return array_search($key, PromotionDetailData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetPromotionDetailCriteria xsi:type="urn:GetPromotionDetailCriteria">';
        foreach (PromotionDetailData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (PromotionDetailData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetPromotionDetailCriteria>';
        return  $xml_string;
    }
}
