<?php

namespace App\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreditNoteConnector extends Model
{
    /**
     * Run SQL query to get Credit Note Header data to send.
     * (06/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getCreditNoteHeaderData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT so.code, loc.code AS location_code, DATE(so.sales_order_date) AS sales_order_date, '' AS handheld_reference_no,
                loc.sales_office_no, '' AS location, '' AS posting_date, sm.code AS salesman_code, loc.sub_salesman_code
                
                
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
     * Run SQL query to get Credit Note Header data to send.
     * (06/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getCreditNoteLineData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT so.code, '' AS line_no, sku.code AS product_code, sor.return, sku.uom, '' AS handheld_line_reference_no

                FROM temp_sales_order so
                INNER JOIN temp_sales_order_returnable sor ON sor.sales_order_id = so.id
                INNER JOIN sku ON sku.id = sor.sku_id
                
                WHERE DATE(so.sales_order_date) BETWEEN :date_from AND :date_to";

        return DB::select(DB::raw($sql), [
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }
}
