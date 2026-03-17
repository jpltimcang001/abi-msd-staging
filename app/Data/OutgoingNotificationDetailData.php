<?php

/** 
 * Container for OutgoingNotificationDetail Data.  
 * 
 * */

namespace App\Data;

class OutgoingNotificationDetailData
{
    const MODULE_OUTGOING_NOTIFICATION_DETAIL = "TRANSFER ORDER DET";
    const MODULE_NAME_OUTGOING_NOTIFCATION_DETAIL = "Transfer Order Detail";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        'Key' => 'ms_dynamics_key',
        'Item_No' => 'sku_code',
        'Quantity' => 'request_quantity',
		'Shortcut_Dimension_1_Code' => 'sales_office_code',
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array(
        'batch_no',
        'expiration_date',
    );

    public $outgoing_notification_detail_id;
    public $outgoing_notification_id;
    public $company_id;
    public $batch_no;
    public $sku_code;
    public $request_quantity;
    public $remarks;
    public $expiration_date;
    public $sales_office_code;
    public $ms_dynamics_key;
    public $created_by;
    public $created_date;
    public $updated_by;
    public $updated_date;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, OutgoingNotificationDetailData::dict)) {
            $key_val = OutgoingNotificationDetailData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, OutgoingNotificationDetailData::dict)) {
            $key_val = OutgoingNotificationDetailData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, OutgoingNotificationDetailData::dict);
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
        return array_search($key, OutgoingNotificationDetailData::dict);
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<item>';
        foreach (OutgoingNotificationDetailData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (OutgoingNotificationDetailData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        $xml_string .= '</item>';
        return  $xml_string;
    }

    /**
     * Create XML array for MSD.
     * 
     */
    public function xmlMSDArrayString()
    {
        $xml_string = '<ns1:Transfer_Order_Line>';
        foreach (OutgoingNotificationDetailData::dict as $key => $value) {
            $xml_string .= "<ns1:" . $key . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $key . ">";
        }
        $xml_string .= '</ns1:Transfer_Order_Line>';
        return  $xml_string;
    }

    /**
     * Create XML array for MSD TransferOrderSubform.
     * 
     */
    public function xmlMSDSubformArrayString()
    {
        $xml_string = '<ns1:TransferOrderSubform>';
        foreach (OutgoingNotificationDetailData::dict as $key => $value) {
            $xml_string .= "<ns1:" . $key . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $key . ">";
        }
        $xml_string .= '</ns1:TransferOrderSubform>';
        return  $xml_string;
    }
}
