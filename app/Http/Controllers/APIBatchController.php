<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SoapVar;
/* Jobs */
use App\Jobs\APIJobHandler;
/* Middleware */
use App\DownloadForm;
use App\Utils\Globals;
use App\Utils\Params;
/* Schema */
use App\Data\LocationData;
use App\Data\SalesmanData as SalesmanData;

class APIBatchController extends Controller
{
    /** ----------------------  HTTP REQUEST ------------------------- */
    /** --------------------- Download ~ Start ----------------------- */

    /**
     * Retrieve Location info (location) from RESTful API data and sends it to the NOC.
     * (04/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveBatchCustomer(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_sm_data_val = new SalesmanData();
        $msd_sm_data_val->short_description = isset($data['params']['short_description']) ? $data['params']['short_description'] : NULL;
        $msd_sm_data_val->code = isset($data['params']['salesman_code']) ? $data['params']['salesman_code'] : NULL;
        $msd_sm_data_val->msd_synced = 1;
        $msd_sm_data_val->deleted = 0;
        /* Get all MSD valid salesman */
        $sm_params = '<GetSalesmanCriteria xsi:type="urn:GetSalesmanCriteria">';
        $sm_params .= $msd_sm_data_val->xmlLineStrings();
        $sm_params .= '</GetSalesmanCriteria>';
        $sm_request = new SoapVar($sm_params, XSD_ANYXML);
        $sm_soap_result = (array) $soap_client->retrieveSalesmanByCriteria($sm_request);

        $total_sm = count($sm_soap_result);
        if (isset($data['company']) && $total_sm > 0) {
            foreach ($sm_soap_result as $k_sm => $v_sm) {
                $criteria = [
                    'company' => $data['company'],
                    'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
                    'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
                    'params' => [
                        'Global_Dimension_1_Code' => $v_sm->short_description,
                        'Salesperson_Code' => $v_sm->code,
                    ]
                ];

                $rest_result = json_decode(Globals::callRESTAPI("POST", Params::values()['server_ip'] . "/api/queue/customer", json_encode($criteria)));
            }
        }

        return [
            "success" => true,
            "message" => LocationData::MODULE_NAME_LOCATION . " successfully created queue for the " . $total_sm . " salesman."
        ];
    }

    /** --------------------- Download ~ End ------------------------- */
}
