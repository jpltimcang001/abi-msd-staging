<?php

/** 
 * Container for OutgoingNotification Data.  
 * 
 * */

namespace App\Data;

use App\Model\wms\OutgoingNotification;

class OutgoingNotificationData
{
    const MODULE_OUTGOING_NOTIFICATION = "TRANSFER ORDER";
    const MODULE_NAME_OUTGOING_NOTIFICATION = "Transfer Order";

    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        "Key" => 'ms_dynamics_key',
        "No" => 'withdrawal_code',
        "TransferfromCode" => 'transfer_from',
        "TransfertoCode" => 'transfer_to',
        "Salesman" => 'employee_code',
        "ShipmentDate" => 'withdrawal_date',
        "TransferOrdertype" => 'transfer_order_type',
        'DirectTransfer' => 'direct_transfer',
        'ShortcutDimension1Code' => 'short_desc'
    );

    /**
     * attributes passed to the request in addition to the ones in $dict
     */
    const dict_ext = array();

    /**
     * outgoing notification transaction types
     */
    const transaction_types = array(
        0 => [
            'code' => 'WS',
            'name' => 'Withdrawal Slip',
        ],
        1 => [
            'code' => 'RS',
            'name' => 'Return Slip',
        ],
    );

    public $outgoing_notification_id;
    public $company_id;
    public $employee_code;
    public $sales_office_code;
    public $short_desc;
    public $withdrawal_code;
    public $withdrawal_date;
    public $status;
    public $transaction_type;
    public $ms_dynamics_key;
    public $confirmed;
    public $created_by;
    public $created_date;
    public $updated_by;
    public $updated_date;
    public $uploaded;
    public $onesoas_synced;
    public $msd_synced;

    public $transfer_from;
    public $transfer_to;
    public $transfer_order_type;
    public $direct_transfer;
    public $outgoingNotificationDetail = [];

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, OutgoingNotificationData::dict)) {
            $key_val = OutgoingNotificationData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, OutgoingNotificationData::dict)) {
            $key_val = OutgoingNotificationData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, OutgoingNotificationData::dict);
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
        return array_search($key, OutgoingNotificationData::dict);
    }

    /**
     * Gets the module by type
     */
    public function getModuleByType($type)
    {
        return OutgoingNotificationData::MODULE_OUTGOING_NOTIFICATION . ' ' . OutgoingNotificationData::transaction_types[$type]['code'];
    }

    /**
     * Gets the module name by type
     */
    public function getModuleNameByType($type)
    {
        return OutgoingNotificationData::MODULE_NAME_OUTGOING_NOTIFICATION . ' ' . OutgoingNotificationData::transaction_types[$type]['name'];
    }

    /**
     * Create XML array.
     * 
     */
    public function xmlArrayLineStrings()
    {
        $xml_string = '<item>';
        foreach (OutgoingNotificationData::dict as $key => $value) {
            $xml_string .= "<" . $value . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value . ">\n";
        }
        foreach (OutgoingNotificationData::dict_ext as $value2) {
            $xml_string .= "<" . $value2 . ">" . ((isset($this->$value2) || $this->$value2 !== NULL) ? htmlspecialchars($this->$value2, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</" . $value2 . ">\n";
        }
        if (count($this->OutgoingNotificationDetail) > 0) {
            $xml_string .= "<OutgoingNotificationDetail>";
            foreach ($this->OutgoingNotificationDetail as $value3) {
                $xml_string .= $value3->XMLlineStrings();
            }
            $xml_string .= "</OutgoingNotificationDetail>";
        }
        $xml_string .= '</item>';
        return  $xml_string;
    }

    /**
     * Create XML array for MSD save request.
     * 
     */
    public function xmlMSDArrayString($with_detail = true)
    {
        $xml_string = '<ns1:TransferOrderService>';
        foreach (OutgoingNotificationData::dict as $key => $value) {
            $xml_string .= "<ns1:" . $key . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $key . ">";
        }
        if (count($this->outgoingNotificationDetail) > 0 && $with_detail) {
            $xml_string .= "<ns1:TransferLines>";
            foreach ($this->outgoingNotificationDetail as $value3) {
                $xml_string .= $value3->xmlMSDArrayString();
            }
            $xml_string .= "</ns1:TransferLines>";
        }
        $xml_string .= '</ns1:TransferOrderService>';
        return  $xml_string;
    }
}
