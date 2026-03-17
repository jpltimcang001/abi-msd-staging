<?php

/** 
 * Container for SKU Data.  
 * 
 * */

namespace App\Data;

class SKUData
{
    const MODULE_SKU = "SKU";
    const MODULE_NAME_SKU = "Product";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        'No' => 'sys_21',
        'Description' => 'name',
        '_x0031_000000016' => 'product_alias',
        'Item_Tracking_Code' => 'upc_code',
        '_x0031_000000039' => 'active',
        'VAT_Prod_Posting_Group' => 'vat_prod_posting_group',
        'Item_Tracking_Code' => 'item_tracking_code',
        'Unit_Price' => 'unit_price',
        'Item_Category_Code' => 'brand_name',
        'Product_Segment' => 'category_name',
        'Empty_1_Quantity' => 'unit_case',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'company_code',
        'short_description',
        'sales_office_no',
        'code',
        'uom',
        'sell_price',
        'stock_type',
        'full_1_code',
        'full_1_unit_case',
        'full_2_code',
        'full_2_unit_case',
        'empty_1_code',
        'empty_2_code',
        'empty_2_unit_case',
        'added_by',
        'edited_by',
        'msd_synced',
		'sys_21_synced',
        'deleted',
    );

    public $id;
    public $company_id;
    public $company_code;
    public $distributor_id;
    public $distributor_code;
    public $ms_dynamics_key;
    public $sales_office_no;
    public $sub_sales_office_no;
    public $short_description;
    public $code;
    public $name;
    public $uom;
    public $type;
    public $unit_price;
    public $sell_price;
    public $brand_id;
    public $brand_code;
    public $brand_name;
    public $package;
    public $unit_case;
    public $weight;
    public $cbm;
    public $groupseqid;
    public $category_sub_id;
    public $agency_id;
    public $category_id;
    public $category_code;
    public $category_name;
    public $sub_category_id;
    public $sub_category_code;
    public $sub_category_name;
    public $upc_code;
    public $sys_21;
    public $image_file;
    public $sms_code;
    public $active;
    public $CAT;
    public $stock_type;
    public $vat_prod_posting_group;
    public $item_tracking_code;
    public $product_alias;
    public $proddiv_code;
    public $prodcat_code;
    public $prodbrand_code;
    public $prodvar_code;
    public $prodsubdiv_code;
    public $issue_unit;
    public $vat_code;
    public $class;
    public $group_major;
    public $group_minor;
    public $is_synced;
    public $sku_order;
    public $image;
    public $full_1_code;
    public $full_1_unit_case;
    public $full_2_code;
    public $full_2_unit_case;
    public $empty_1_code;
    public $empty_1_unit_case;
    public $empty_2_code;
    public $empty_2_unit_case;
    public $centrix_synced;
    public $onesoas_synced;
    public $sys_21_synced;
    public $msd_synced;
    public $added_by;
    public $added_when;
    public $edited_by;
    public $edited_when;
    public $deleted_by;
    public $deleted_when;
    public $deleted;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, SKUData::dict)) {
            $key_val = SKUData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, SKUData::dict)) {
            $key_val = SKUData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, SKUData::dict);
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
        return array_search($key, SKUData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetSkuCriteria xsi:type="urn:GetSkuCriteria">';
        foreach (SKUData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (SKUData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetSkuCriteria>';
        return  $xml_string;
    }
}
