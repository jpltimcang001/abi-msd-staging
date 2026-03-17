<?php

namespace App\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreditControlConnector extends Model
{
    /**
     * Run SQL query to get Transfer order data to send.
     * (06/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getNewCreditControlChangeRequestData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT cc.no, loc.code AS location_code, cc.date, cc.requester_name, cc.requester_reason, cc.sales_office_no, cc.credit_limit,
                cc.payment_terms_code, cc.payment_method_code, cc.comments, cc.status
                
                FROM credit_control cc
                INNER JOIN location loc ON loc.id = cc.location_id
                
                WHERE cc.status = 0";

        return DB::select(DB::raw($sql));
    }
}
