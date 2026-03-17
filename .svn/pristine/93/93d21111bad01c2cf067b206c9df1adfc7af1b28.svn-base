<?php

/** 
 * Container for ReservationEntryData.  
 * 
 * */

namespace App\Data;

class ReservationEntryData
{
    const MODULE_RESERVATION_ENTRY = "RES. ENT.";
    const MODULE_NAME_RESERVATION_ENTRY = "Reservation Entry";
    
    /**
     * Dictionary of NOC => MSD columns.
     */
    const dict = array(
        "Key" => 'ms_dynamics_key',
        "ReservationStatus" => 'reservation_status',
        "ItemNo" => "sku_code",
        "LocationCode" => "zone_code",
        "LotNo" => "lot_no",
        "ShipmentDate" => "shipment_date",
        "QuantityBase" => 'quantity',
        "SourceType" => "source_type",
        "SourceSubtype" => 'source_subtype',
        "SourceID" => "source_id",
        "SourceRefNo" => "line_no",
        "CreationDate" => "creation_date",
        "ItemTracking" => "item_track",
    );

    /**
     * outgoing notification transaction types
     */
    const transaction_types = array(
        0 => [
            'code' => 'SO',
            'name' => 'Sales Order',
        ],
        1 => [
            'code' => 'SM',
            'name' => 'Salesman',
        ],
        2 => [
            'code' => 'WH',
            'name' => 'Sales Office',
        ],
    );

    public $reservation_status;
    public $sku_code;
    public $zone_code;
    public $shipment_date;
    public $source_type;
    public $source_subtype;
    public $source_id;
    public $quantity;
    public $creation_date;
    public $item_track;
    public $line_no;
    public $lot_no;
    public $entry_no;
    public $ms_dynamics_key;

    /**
     * Returns value using MSD key.
     */
    public function MSD($key)
    {
        if (array_key_exists($key, ReservationEntryData::dict)) {
            $key_val = ReservationEntryData::dict[$key];
            return $this->$key_val;
        } else
            return null;
    }

    /**
     * Sets value using MSD key.
     */
    public function setMSD($key, $value)
    {
        if (array_key_exists($key, ReservationEntryData::dict)) {
            $key_val = ReservationEntryData::dict[$key];
            $this->$key_val = $value;
        }
    }

    /**
     * Checks if MSD value exists.
     */
    public function hasMSD($key)
    {
        return array_key_exists($key, ReservationEntryData::dict);
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
        return array_search($key, ReservationEntryData::dict);
    }

    /**
     * Gets the module by type
     */
    public function getModuleByType($type)
    {
        return ReservationEntryData::MODULE_RESERVATION_ENTRY . ' ' . ReservationEntryData::transaction_types[$type]['code'];
    }

    /**
     * Gets the module name by type
     */
    public function getModuleNameByType($type)
    {
        return ReservationEntryData::MODULE_NAME_RESERVATION_ENTRY . ' (' . ReservationEntryData::transaction_types[$type]['name'] . ')';
    }

    /**
     * Create XML array for MSD.
     * 
     */
    public function xmlMSDArrayString()
    {
        $xml_string = '<ns1:ReservationEntriesService>';
        foreach (ReservationEntryData::dict as $key => $value) {
            $xml_string .= "<ns1:" . $key . ">" . ((isset($this->$value) || $this->$value !== NULL) ? htmlspecialchars($this->$value, ENT_XML1 | ENT_COMPAT, 'UTF-8') : "") . "</ns1:" . $key . ">\n";
        }
        $xml_string .= '</ns1:ReservationEntriesService>';
        return  $xml_string;
    }
}
