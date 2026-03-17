<?php

namespace App\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransferConnector extends Model
{
    /**
     * Run SQL query to get Transfer order data to send.
     * (06/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getTransferOrderData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT zone_source.zone_code AS source_zone_code, zone_dest.zone_code AS destination_zone_code, 'Normal' AS transfer_type

                FROM outgoing_inventory oi
                INNER JOIN outgoing_inventory_detail oid ON oid.outgoing_inventory_id = oi.outgoing_inventory_id
                INNER JOIN zone zone_dest ON zone_dest.zone_id = oi.destination_zone_id
                INNER JOIN zone zone_source ON zone_source.zone_id = oid.source_zone_id
                
                WHERE oi.transaction_date BETWEEN :date_from AND :date_to";

        /**NOTE: Raw SQL converted to Laravel Query builder, missing columns in local database?
         *  DB::table(DB::raw("noc.outgoing_inventory AS oi"))
            ->join(DB::raw('noc.outgoing_inventory_detail AS oid'), "oid.outgoing_inventory_id", "=", "oi.outgoing_inventory_id")
            ->join(DB::raw("centrix.zone AS zone_dest"), "zone_dest.zone_id", "=", "oi.destination_zone_id")
            ->join(DB::raw("centrix.zone as zone_source"), "zone_source.zone_id", "=", "oid.source_zone_id")
            ->select(DB::raw("SELECT zone_source.zone_code AS source_zone_code, zone_dest.zone_code AS destination_zone_code, 'Normal' AS transfer_type"))
            ->whereBetween('oi.transaction_date', [$date_from, $date_to])
            ->get();
         */
        return DB::select(DB::raw($sql), [
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

    /**
     * Run SQL query to get Transfer Lot data to send.
     * (06/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getTransferLotData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        /**sql raw data (missing/misnamed cols (?)) 
        $sql = "SELECT oi.dr_no, '' AS line_no, sku.sku_code, oid.batch_no, uom.uom_code, oid.quantity_issued,
                '' AS handheld_reference_no, '' AS handheld_line_reference_no
                
                FROM outgoing_inventory oi
                INNER JOIN outgoing_inventory_detail oid ON oid.outgoing_inventory_id = oi.outgoing_inventory_id
                INNER JOIN sku ON sku.sku_id = oid.sku_id
                INNER JOIN uom ON uom.uom_id = oid.uom_id
                
                WHERE oi.transaction_date BETWEEN :date_from AND :date_to";
         */

        return DB::table(DB::raw("noc.outgoing_inventory AS oi"))
            ->join(DB::raw('noc.outgoing_inventory_detail AS oid'), "oid.outgoing_inventory_id", "=", "oi.outgoing_inventory_id")
            ->join(DB::raw("centrix.sku AS sku"), "sku.code", "=", "oid.sku_id")
            ->join(DB::raw("centrix.uom AS uom"), "uom.name", "=", "oid.uom_id")
            ->select(DB::raw("oi.dr_no, '' AS line_no, sku.code as sku_code, oid.batch_no, uom.name as uom_code, oid.quantity_issued, '' AS handheld_reference_no, '' AS handheld_line_reference_no"))
            ->whereBetween('oi.transaction_date', [$date_from, $date_to])
            ->get();
    }
}
