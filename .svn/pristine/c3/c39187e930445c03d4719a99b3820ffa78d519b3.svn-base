<?php

namespace App\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReceiptJournalConnector extends Model
{
    /**
     * Run SQL query to get Cash Reciept Journal data to send.
     * (06/04/22)
     * 
     * @param params Array of data that is used in the query.
     * 
     * @return query Returns query result.
     */
    public static function getCashReceiptJournalData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT recj.batch_name, recj.line_no, recj.posting_date, recj.document_date, recj.document_type, recj.document_no,
                recj.external_document_no, recj.account_type, recj.account_no, loc.name AS location_name, recj.salesman_code, recj.payment_method_code,
                recj.amount, recj.comments, recj.bal_account_type, recj.bal_account_no, recj.location_bank, recj.check_no, recj.check_date, recj.added_by, recj.added_when
                
                FROM receipt_journal recj
                INNER JOIN location loc ON loc.id = recj.location_id
                
                WHERE DATE(recj.created_when) BETWEEN :date_from AND :date_to";

        return DB::select(DB::raw($sql), [
            'date_from' => $date_from,
            'date_to' => $date_to
        ]);
    }
}
