<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;
use SoapVar;
/* Utils */
use App\Utils\Globals;
/* Schema */
use App\Data\SalesOfficeData;
/* Model */
use App\Model\noc\Salesman;

class AllDownloadHandler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apirun:allDownload {--date-from=0} {--date-to=0} {--company=} {--sales-office=} {--so-short=} {--salesman=} {--location-code=} {--discount-scheme=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Customer/Sku of sales office';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date_from = $this->option('date-from');
        $date_to = $this->option('date-to');
        $company =  $this->option('company');
        $sales_office_no = $this->option('sales-office');
        $so_code = $this->option('so-short');
        $salesman = $this->option('salesman');
		$location_code = $this->option('location-code');
		$discount_scheme = $this->option('discount-scheme');
		
		if($salesman == "") {
			$salesman = Salesman::select('code')->where('msd_synced', '=', 1)->where('deleted', '=', 0)->where('sales_office_no', '=', $sales_office_no)->get()->toarray();
		}
		if(count($salesman) == 0) {
			$salesman = "";
		}

        $data = ['params' => []];
		
        if ($date_from != 0 && $this->validateDate($date_from))
            $data['params']['date_from'] = date('Y-m-d', strtotime($date_from));
        // else
            // $data['params']['date_from'] = date('Y-m-d', strtotime('-1 Month'));

        if ($date_to != 0 && $this->validateDate($date_to))
            $data['params']['date_to']  = date('Y-m-d', strtotime($date_to));
        else
            $data['params']['date_to'] = date('Y-m-d');
		
		$data['params']['Global_Dimension_1_Code'] = $so_code;

        if ($company == "" || $sales_office_no == "") {	
            /* Get all MSD valid sales office */
            $soap_client = Globals::soapClientABINOCCentralWS();
            $msd_so_data_val = new SalesOfficeData();
            if ($sales_office_no != "")
                $msd_so_data_val->no = $sales_office_no;
            if ($company != "")
                $msd_so_data_val->company = $company;
            $msd_so_data_val->msd_synced = 1;
            $msd_so_data_val->deleted = 0;
            /* Get all MSD valid sales office */
            $so_params = '<GetSalesOfficeCriteria xsi:type="urn:GetSalesOfficeCriteria">';
            $so_params .= $msd_so_data_val->xmlLineStrings();
            $so_params .= '</GetSalesOfficeCriteria>';
            $so_request = new SoapVar($so_params, XSD_ANYXML);
            $so_soap_result = (array) $soap_client->retrieveSalesOfficeByCriteria($so_request);

            if (count($so_soap_result) > 0) {
                foreach ($so_soap_result as $v_so) {
                    $data['company'] = $v_so->company;
                    $data['sales_office_no'] = $v_so->no;
					$data['params']['Salesperson_Code'] = $salesman;
					$data['params']['No'] = $location_code;
					$data['params']['Scheme_Code'] = $discount_scheme;
                    /** Send queue */
                    Artisan::call(
                        'apirun:send',
                        [
                            'type' => 'all-download', 'data' => json_encode($data)
                        ]
                    );      
                }
            }
        }
		else {
                    $data['sales_office_no'] = $sales_office_no;
                    $data['company'] = $company;
					$data['params']['Salesperson_Code'] = $salesman;
					$data['params']['No'] = $location_code;
					$data['params']['Scheme_Code'] = $discount_scheme;
                    /** Send queue */
                    Artisan::call(
                        'apirun:send',
                        [
                            'type' => 'all-download', 'data' => json_encode($data)
                        ]
                    );
            
		
		}
    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = date($format, strtotime($date));
        return $d;
    }
}
