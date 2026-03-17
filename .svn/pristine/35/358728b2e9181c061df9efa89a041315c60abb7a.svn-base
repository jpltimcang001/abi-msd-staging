<?php

namespace App\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalesOrderConnector extends Model
{
    /**
     * Run SQL query to get Sales Order Header data to send.
     * (05/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getSalesOrderHeaderData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT so.code, so.transaction_type, loc.code AS location_code, loc.sales_office_no, DATE(so.sales_order_date) AS sales_order_date,
                '' AS posting_date, so.delivery_date, '' AS external_document_no, sm.code AS salesman_code, so.remarks,
                '' AS handheld_entry, '' AS van_sale, '' AS handheld_reference_no
                
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
     * Run SQL query to get Sales Order Details data to send.
     * (05/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getSalesOrderLineData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT '' AS `type`, sod.product_code, loc.sales_office_no, sod.quantity, sku.uom, sod.unit_price,
                sod.discount_amount, '' AS percentage, '' AS foc_scheme, '' AS discount_scheme, '' AS scheme_item, '' AS foc_item,
                '' AS scheme_no, '' AS discount_amount1, '' AS discount_percentage1, '' AS discount_amount2, '' AS discount_percentage2,
                '' AS scheme_no1, '' AS scheme_no2, '' AS scheme_no3, '' AS scheme_no4, '' AS scheme_no5, '' AS empties, '' AS empties_deposit,
                '' AS handheld_line_reference_no
                
                FROM temp_sales_order so
                INNER JOIN temp_sales_order_detail sod ON sod.sales_order_id = so.id
                INNER JOIN location loc ON loc.id = so.location_id
                INNER JOIN sku ON sku.code = sod.product_code
                
                WHERE DATE(so.sales_order_date) BETWEEN :date_from AND :date_to";

        return DB::select(DB::raw($sql), [
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

    /**
     * Run SQL query to get Sales Order Lot data to send.
     * (05/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getSalesOrderLotData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT so.code, '' AS line_no, sod.product_code, '' AS batch_no, sku.uom, sod.quantity, '' AS handheld_reference_no, '' AS handheld_line_reference_no

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
     * Run SQL query to get Sales Order Release data to send.
     * (05/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getSalesOrderReleaseData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT so.code
                
                FROM temp_sales_order so
                INNER JOIN location loc ON loc.id = so.location_id
                
                WHERE DATE(so.sales_order_date) BETWEEN :date_from AND :date_to AND so.transaction_type = 0";

        return DB::select(DB::raw($sql), [
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

    /**
     * Run SQL query to get Sales Order Post data to send.
     * (05/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getSalesOrderPostData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT so.code
                
                FROM temp_sales_order so
                INNER JOIN location loc ON loc.id = so.location_id
                
                WHERE DATE(so.sales_order_date) BETWEEN :date_from AND :date_to AND so.transaction_type = 1";

        return DB::select(DB::raw($sql), [
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }
}
