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

class APIInvoiceDownloadHandler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apirun:invoice {--date-from=0} {--date-to=0} {--company=} {--sales-office=}  {--so_code=} {--code=} {--is-auto=}';

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
        $so_code = $this->option('so_code');
        $code = $this->option('code');
        $is_auto = $this->option('is-auto');

		if($is_auto == "") {
			$is_auto = 0;
		} else {
			$is_auto = 1;
		}

        $data = ['params' => []];
		
        if ($date_from != 0 && $this->validateDate($date_from))
            $data['params']['Document_Date'] = ">=". date('Y-m-d', strtotime($date_from));
        else
            $data['params']['Document_Date'] =  ">=". date('Y-m-d', strtotime('-1 Month'));
		
		$data['params']['Shortcut_Dimension_1_Code'] = $sales_office_no;
        $data['is_auto'] = $is_auto;

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
					$data['params']['Shortcut_Dimension_1_Code'] = $v_so->short_desc;
					$data['params']['Order_No'] = $so_code;
		
                    /** Send queue */
                       
                }
            }
        }
		else {   
			
            $soap_client = Globals::soapClientABINOCCentralWS();
            $msd_so_data_val = new SalesOfficeData();
			if ($sales_office_no != "")
                $msd_so_data_val->no = $sales_office_no;
			if ($company != "")
                $msd_so_data_val->company = $company;
			
			$msd_so_data_val->deleted = 0;
            /* Get all MSD valid sales office */
            $so_params = '<GetSalesOfficeCriteria xsi:type="urn:GetSalesOfficeCriteria">';
            $so_params .= $msd_so_data_val->xmlLineStrings();
            $so_params .= '</GetSalesOfficeCriteria>';
            $so_request = new SoapVar($so_params, XSD_ANYXML);
            $so_soap_result = (array) $soap_client->retrieveSalesOfficeByCriteria($so_request);
               

			$data['sales_office_no'] = $sales_office_no;
			$data['company'] = $company;
			$data['params']['Order_No'] = $so_code;

			if(count($so_soap_result) > 0) {
				$data['params']['Shortcut_Dimension_1_Code'] = $so_soap_result[0]->short_description;
			}
			/** Send queue */
			Artisan::call(
				'apirun:send',
				[
					'type' => 'invoice-multiple', 'data' => json_encode($data)
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
