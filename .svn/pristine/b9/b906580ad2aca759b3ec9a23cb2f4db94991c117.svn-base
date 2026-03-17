<?php

/** 
 * Container for Location/Customer Data.  
 * 
 * */

namespace App\Data;

class LocationData
{
    const MODULE_LOCATION = "LOCATION";
    const MODULE_NAME_LOCATION = "Customer";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        'Global_Dimension_1_Code' => 'short_description',
        'No' => 'code',
        'Name' => 'name',
        'WIN_Trade_Name' => 'trade_name',
        'Search_name' => 'search_name',
        'Salesperson_Code' => 'salesman_code',
        'Address' => 'address1',
        'City' => 'address2',
        'Customer_Price_Group' => 'customer_price_group',
        'Credit_Limit_LCY' =>  'limit_fulls',
        'WIN_Credit_Limit_Containers' =>  'limit_mts',
        'Route' => 'sub_salesman_code',
        'Route_Sequence_No' => 'route_seq_code',
        'Payment_Terms_Code' => 'payment_terms_code',
        "Payment_Method_Code" => 'payment_method_code',
        "Customer_Disc_Group" => 'customer_disc_group',
        'Balance_LCY' => 'balance_fulls',
        'Balance_Empties_Amount' => 'balance_mts',
        'Comments' => 'comments',
        'VAT_Registration_No' => 'vat_registration_no',
        'E_Mail' => 'email_address',
        'VAT_Bus_Posting_Group' => 'vat_bus_posting_group',
        'WIN_Trade_Channel_Code' => 'distribution_channel',
        'WIN_Sub_Trade_Channel_Code' => 'store_type',
        'Old_Outlet_Code' => 'code2',
        'Latitude' => 'latitude',
		'Longitude' => 'longitude',
		// 'Post_Code' => 'zip_code',
		'Fax_No' => 'fax_no',
		'ContactName' => 'cp_name_1',
		'Temporary_Location' => 'temporary_location',
		'WIN_Owner_x0027_s_Full_Name'  => 'owner_name',
		'WIN_Owner_x0027_s_Mobile_Number'  => 'owner_contact',
		'Date_Open'  => 'date_open',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'added_by',
        'updated_by',
        "approval_status",
        "service_call_days",
		"synced_when",
		"approved_when",
		"deleted"
    );

    public $name;
    public $trade_name;
    public $search_name;
    public $salesman_code;
    public $customer_price_group;
    public $limit_fulls;
    public $sub_salesman_code;
    public $route_seq_code;
    public $payment_terms_code;
    public $payment_method_code;
    public $customer_disc_group;
    public $comments;
    public $vat_bus_posting_group;
    public $sales_office_no;
    public $code;
    public $distribution_channel_id;
    public $distribution_channel_code;
    public $distribution_channel;
    public $store_type_id;
    public $store_type_code;
    public $store_type;
    public $code2;
	public $temporary_location;
    public $ms_dynamics_key;
    public $added_by;
    public $added_when;
    public $updated_by;
    public $updated_when;

    public $id;
    public $balance_fulls = 0;
    public $balance_mts  = 0;
    public $system_21_key;
    public $distributor_id;
    public $distributor_code;
    public $sub_sales_office_no;
    public $short_description;
    public $business_style;
    public $tin;
    public $address1;
    public $address2;
    public $address3;
    public $address4;
    public $address5;
    public $barangay_id;
    public $municipal_id;
    public $province_id;
    public $region_id;
    public $owner_name;
    public $longitude;
    public $latitude;
    public $longitude2;
    public $latitude2;
    public $status;
    public $approval_status;
    public $owner_contact;
    public $sub_so_no;
    public $wholesaler_id;
    public $is_synced;
    public $priority;
    public $location_type;
    public $room_floor_bldg;
    public $st_subd;
    public $barangay;
    public $town_no;
    public $region_no;
    public $country;
    public $zip_code;
    public $account_no;
    public $account_name;
    public $tel_no;
    public $ext;
    public $fax_no;
    public $email_address;
    public $credit_term;
    public $limit_mts;
    public $vat_tag;
    public $date_open;
    public $as_name_1;
    public $as_position_1;
    public $as_email_address_1;
    public $as_tel_no_1;
    public $as_ext_1;
    public $as_name_2;
    public $as_position_2;
    public $as_email_address_2;
    public $as_tel_no_2;
    public $as_ext_2;
    public $cp_name_1;
    public $cp_position_1;
    public $cp_contact_no_1;
    public $cp_email_address_1;
    public $cp_tel_no_1;
    public $cp_ext_1;
    public $cp_name_2;
    public $cp_position_2;
    public $cp_contact_no_2;
    public $cp_email_address_2;
    public $cp_tel_no_2;
    public $cp_ext_2;
    public $district_id;
    public $bank;
    public $branch;
    public $sss_no;
    public $service_model;
    public $service_call_days;
    public $location_branch;
    public $delivery_address_code;
    public $price_list_code;
    public $stockroom;
    public $vat_code;
    public $payment_term_code;
    public $currency_code;
    public $invoice_address_code;
    public $vat_registration_no;
    public $gen_bus_posting_group;
    public $wht_business_posting_group;
    public $customer_posting_group;
    public $allow_line_disc;
    public $prices_including_vat;
    public $sv_potential_sales;
    public $sv_wv_beer;
    public $sv_wv_water;
    public $sv_wv_softdrinks;
    public $sv_wv_cobra;
    public $sv_outlets_status;
    public $centrix_synced;
    public $onesoas_synced;
    public $sys_21_synced;
    public $deleted_by;
    public $deleted_when;
	public $synced_when;
	public $approved_when;
    public $deleted = 0;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, LocationData::dict)) {
            $key_val = LocationData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, LocationData::dict)) {
            $key_val = LocationData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, LocationData::dict);
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
        return array_search($key, LocationData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<GetLocationCriteria xsi:type="urn:GetLocationCriteria">';
        foreach (LocationData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (LocationData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</GetLocationCriteria>';
        return  $xml_string;
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayUpdateLocation()
    {
        $xml_string = '<ns1:Create><ns1:CustomerModifyRequestCard>';
        foreach (LocationData::dict as $key => $value) {
			if( $this->$value === "" || $this->$value === null)
				continue;
			if($key === "No") 
				$akey = "Customer_No";
			else
				$akey = $key;

            $xml_string .= "<ns1:" . $akey . ">" . (($this->$value === "" || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $akey . ">\n";
        }
        $xml_string .= '</ns1:CustomerModifyRequestCard></ns1:Create>';
        return  $xml_string;
    }
}
