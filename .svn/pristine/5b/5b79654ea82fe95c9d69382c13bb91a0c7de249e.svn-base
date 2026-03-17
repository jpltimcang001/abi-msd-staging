<?php

/** 
 * Container for Customer Price Group Data.  
 * 
 * */

namespace App\Data;

class SalesPriceData
{
    const MODULE_SALES_PRICE = "SALES PRICE";
    const MODULE_NAME_SALES_PRICE = "Sales Price";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        "Key" => 'ms_dynamics_key',
        "SalesType" =>  'sales_type',
        "SalesCode" => 'pricing_type_code',
        // "ItemNo" =>  'product_no',
        // "VariantCode" => '',
        "CurrencyCode" =>  'currency_code',
        "UnitofMeasureCode" => 'uom',
        "MinimumQuantity" => 'min',
        "PublishedPrice" => 'value_rate_1',
        // "Cost" => '',
        // "Costplus" => '',
        "DiscountAmount" => 'discount_amount',
        "UnitPrice" => 'value_rate_2',
        "StartingDate" => 'start_date',
        "EndingDate" => 'end_date',
        "PriceIncludesVAT" => 'price_includes_vat',
        // "AllowLineDisc" => '',
        "AllowInvoiceDisc" => 'allow_invoice_disc',
        "VATBusPostingGrPrice" => 'vat_bus_posting_group',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'rate_code',
        'product_no',
        'added_by',
        'updated_by',
        'msd_synced',
    );

    public $id;
    public $ms_dynamics_key;
    public $distributor_id;
    public $distributor_code;
    public $sales_office_no;
    public $sub_sales_office_no;
    public $short_description;
    public $salesman_code;
    public $price_function_code;
    public $pricing_type_code;
    public $description;
    public $price_sequence_code;
    public $rate_code;
    public $location_id;
    public $location_code;
    public $delivery_address_code;
    public $product_no;
    public $date;
    public $start_date;
    public $end_date;
    public $uom;
    public $qualifying_limit_1;
    public $qualifying_limit_2;
    public $qualifying_limit_3;
    public $value_rate_1;
    public $value_rate_2;
    public $value_rate_3;
    public $percentage_rate_1;
    public $percentage_rate_2;
    public $percentage_rate_3;
    public $source;
    public $currency_code;
    public $sales_type;
    public $discount_amount;
    public $min;
    public $vat_bus_posting_group;
    public $prices_including_vat;
    public $allow_invoice_disc;
    public $centrix_synced;
    public $onesoas_synced;
    public $sys_21_synced;
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
        if (array_key_exists($key, SalesPriceData::dict)) {
            $key_val = SalesPriceData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, SalesPriceData::dict)) {
            $key_val = SalesPriceData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, SalesPriceData::dict);
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
        return array_search($key, SalesPriceData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetDiscountQualifyingCriteria xsi:type="urn:GetDiscountQualifyingCriteria">';
        foreach (SalesPriceData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (SalesPriceData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetDiscountQualifyingCriteria>';
        return  $xml_string;
    }
}
