<?php

/** 
 * Container for Discount Case Data.  
 * 
 * */

namespace App\Data;

class DiscountCaseData
{
    const MODULE_DISCOUNT_CASE = "DISCOUNT";
    const MODULE_NAME_DISCOUNT_CASE = "Discount";

    /**
     * Dictionary of MSD => NOC columns.
     */
    const dict = array();

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'sales_office_no',
        'short_description',
        'location_code',
        'discount_m_case_no',
        'disc_type_no',
        'document_no',
        'pser_cd',
        'discount_case_cd',
        'description',
        'product_no',
        'min',
        'max',
        'from_date',
        'to_date',
        'amount',
        'percentage',
        'added_by',
        'updated_by',
        'msd_synced',
    );

    /**
     * attributes passed to the request in getting discount type code
     */
    const discount_types = array(
        1 => 'Discount_by_Amount',
        2 => 'Discount_by_Percent',
    );

    public $id;
    public $distributor_id;
    public $distributor_code;
    public $sales_office_no;
    public $sub_sales_office_no;
    public $short_description;
    public $location_id;
    public $location_code;
    public $distribution_channel_code;
    public $distribution_channel;
    public $discount_m_case_no;
    public $disc_type_no;
    public $document_no;
    public $discount_case_cd;
    public $description;
    public $product_no;
    public $min;
    public $max;
    public $amount;
    public $percentage;
    public $date;
    public $from_date;
    public $to_date;
    public $office_source;
    public $post_date;
    public $extract_date;
    public $sales_accounting_code;
    public $pser_cd;
    public $level;
    public $level_4;
    public $level_5;
    public $is_synced;
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
        if (array_key_exists($key, DiscountCaseData::dict)) {
            $key_val = DiscountCaseData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, DiscountCaseData::dict)) {
            $key_val = DiscountCaseData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, DiscountCaseData::dict);
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
        return array_search($key, DiscountCaseData::dict);
    }

    /**
     * Gets the MSD name from NOC key
     * 
     */
    public function getMSDDiscountType($name)
    {
        $discount_type = array_search($name, DiscountCaseData::discount_types);
        return $discount_type ? $discount_type : 0;
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetDiscountCaseCriteria xsi:type="urn:GetDiscountCaseCriteria">';
        foreach (DiscountCaseData::dict as $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (DiscountCaseData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetDiscountCaseCriteria>';
        return  $xml_string;
    }
}
