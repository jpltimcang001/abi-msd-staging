<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class PromotionData
{
    const MODULE_PROMOTION = "PROMOTION";
    const MODULE_NAME_PROMOTION = "Promotion";

    /**
     * Dictionary of MSD => NOC columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        "No" => 'no',
        "Name" => 'name',
        "Long_Description" => 'description',
        "From_Date" =>  'start_date',
        "To_Date" =>  'end_date',
        "Scheme_Type" =>  'scheme_type',
        "Scheme_Activate" =>  'scheme_activate',
        "Scheme_Closed" =>  'scheme_closed',
        "Scheme_Priority" =>  'scheme_priority',
        "Exclusive_Promo" =>  'exclusive_promo',
        "Promotion_On_Discount" =>  'discount',
        "Promotion_On_FOC" => 'foc',
        "Multiple_Bundle_Validation" => 'bundle_validation',
        'Link_to_Bundle' => 'link_bundle',
        "FOC_Scheme" =>  'foc_scheme',
        "Discount_Scheme" =>  'discount_scheme',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'sales_office_no',
        'short_desc',
        'request_no',
        'added_by',
        'updated_by',
        'msd_synced',
    );

    public $id;
    public $no;
    public $ms_dynamics_key;
    public $name;
    public $start_date;
    public $end_date;
    public $scheme_activate;
    public $scheme_closed;
    public $scheme_type;
    public $scheme_priority;
    public $description;
    public $discount;
    public $foc;
    public $times_valid;
    public $exclusive_promo;
    public $quantity;
    public $pop_type;
    public $foc_scheme;
    public $discount_scheme;
    public $scheme_ledger;
    public $foc_ledger;
    public $discount_ledger;
    public $bundle_validation;
    public $link_bundle;
    public $published;
    public $consumer_promo;
    public $approval_status;
    public $foc_quantity;
    public $request_no;
    public $sales_office_no;
    public $short_desc;
    public $msd_synced;
    public $added_by;
    public $added_when;
    public $updated_by;
    public $updated_when;
    public $deleted_by;
    public $deleted_when;
    public $deleted;
    public $promotionLocation = [];
    public $promotionDiscount = [];
    public $promotionFOC = [];


    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, PromotionData::dict)) {
            $key_val = PromotionData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, PromotionData::dict)) {
            $key_val = PromotionData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, PromotionData::dict);
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
        return array_search($key, PromotionData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetPromotionCriteria xsi:type="urn:GetPromotionCriteria">';
        foreach (PromotionData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (PromotionData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetPromotionCriteria>';
        return  $xml_string;
    }
}
