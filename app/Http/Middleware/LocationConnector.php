<?php

namespace App\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LocationConnector extends Model
{
    public static function getNewCustomerCreationRequestData($params = [])
    {
        $date_from = isset($params['date_from']) ? $params['date_from'] : date("Y-m-d");
        $date_to = isset($params['date_to']) ? $params['date_to'] : date("Y-m-d");

        $sql = "SELECT '' AS nccr_no, loc.code, dist.distributor_code, loc.sales_office_no, loc.name, '' AS name2, loc.address1, loc.address2,
                loc.zip_code, loc.town_no, loc.owner_name, loc.owner_contact, loc.cp_name_1, loc.cp_contact_no_1, loc.sub_salesman_code, loc.limit_fulls,
                locd.payment_terms_code, locd.payment_method_code, locd.customer_posting_group, locd.customer_price_group,
                loc.country, loc.sales_office_no, loc.fax_no, locd.vat_registration_no, loc.email_address, '' AS segment, '' AS sub_segment, loc.region_no,
                dis.name AS district, loc.sub_salesman_code, loc.salesman_code, loc.distribution_chanel AS distribution_channel, st.description AS store_type, '' AS draft, '' AS bus_type,
                loc.location_type, '' AS `chain`, '' AS location_group, DATE(loc.added_when) AS created_date, loc.added_by AS created_by, '' AS bus_unit, dist.distributor_name,
                '' AS call_frequency, loc.latitude, loc.longitude, '' AS branch_name, '' AS township, loc.account_no, loc.account_name,
                '' AS location_class, '' AS approval_status, '' AS company_contact_no, '' AS comments, '' AS picture, 
                '' AS mon, '' AS tue, '' AS wed, '' AS thu, '' AS fri, '' AS sat, '' AS sun, locd.gen_bus_posting_group, locd.vat_bus_posting_group,
                '' AS email_address2, '' AS email_address3, '' AS email_address4, locd.customer_disc_group
                
                FROM location loc
                INNER JOIN location_details locd ON locd.location_id = loc.id
                INNER JOIN sales_office sof ON sof.no = loc.sales_office_no
                INNER JOIN distributors dist ON dist.distributor_id = sof.distributor_id
                LEFT JOIN district dis ON dis.id = loc.district_id
                LEFT JOIN store_type st ON st.id = loc.store_type_id
                
                WHERE 1 = 1
                
                LIMIT 10";

        return DB::select(DB::raw($sql));
    }
}
