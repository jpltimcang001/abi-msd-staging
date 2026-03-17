<?php

namespace App\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalesReturnConnector extends Model
{
    /**
     * Run SQL query to get Sales Return order data to send.
     * (06/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getSalesReturnOrderHeaderData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT so.code, so.transaction_type, loc.code AS location_code, loc.sales_office_no, DATE(so.sales_order_date) AS sales_order_date,
                '' AS posting_date, DATE(so.sales_order_date) AS sales_order_date, '' AS external_document_no, sm.code AS salesman_code,
                so.remarks, '' AS handheld_entry, '' AS handheld_reference_no, loc.sales_office_no
                                
                FROM temp_sales_order so
                INNER JOIN location loc ON loc.id = so.location_id
                INNER JOIN salesman sm ON sm.id = so.salesman_id
                
                WHERE DATE(so.sales_order_date) BETWEEN :date_from AND :date_to";

        return DB::select(DB::raw($sql), [
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

    /**
     * Run SQL query to get Sales Return order details data to send.
     * (06/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getSalesReturnOrderLineData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT '' AS `type`, sod.product_code, loc.sales_office_no, sod.quantity, sku.uom,
                sod.unit_price, sod.discount_amount, '' AS percentage, '' AS handheld_line_reference_no
                
                FROM temp_sales_order so
                INNER JOIN temp_sales_order_detail sod ON sod.sales_order_id = so.id
                INNER JOIN location loc ON loc.id = so.location_id
                INNER JOIN sku ON sku.code = sod.product_code AND IFNULL(sku.sales_office_no, '') = loc.sales_office_no
                
                WHERE DATE(so.sales_order_date) BETWEEN :date_from AND :date_to";

        return DB::select(DB::raw($sql), [
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

    /**
     * Run SQL query to get Sales Return Lot data to send.
     * (06/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getSalesReturnOrderLotData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT '' AS `type`, sod.product_code, loc.sales_office_no, sod.quantity, sku.uom,
                sod.unit_price, sod.discount_amount, '' AS percentage, '' AS handheld_line_reference_no
                
                FROM temp_sales_order so
                INNER JOIN temp_sales_order_detail sod ON sod.sales_order_id = so.id
                INNER JOIN location loc ON loc.id = so.location_id
                INNER JOIN sku ON sku.code = sod.product_code AND IFNULL(sku.sales_office_no, '') = loc.sales_office_no
                
                WHERE DATE(so.sales_order_date) BETWEEN :date_from AND :date_to";

        return DB::select(DB::raw($sql), [
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }
}
