<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SoapClient;
use SoapFault;
use SoapVar;
use Artisan;

/* Middleware */
use App\Http\Middleware\DownloadConnector;
use App\Http\Middleware\UploadConnector;
/* Utils */
use App\Utils\Globals;
use App\Utils\Params;
use App\Utils\Utils;
/* Schema */
use App\Data\ReservationEntryData;
use App\Data\SalesOrderData;
use App\Data\SalesOrderDetailData;
/* Model */
use App\Model\noc\DiscountCase;
use App\Model\noc\SalesOrder;
use App\Model\noc\SalesOrderDetail;
use App\Model\wms\SalesOffice as WMSSalesOffice;

class APISalesOrderHandler extends Command
{
	
	const BATCH_SALES_ORDER_NAME = "BULK SO UPLOAD";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apirun:salesorder {code} {sales_office_no} {company} {trigger_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Sales Order to Cash Receipt Post';

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
		$so_code = $this->option('code');
		$sales_office_no = $this->option('sales_office_no');
		$trigger_id = $this->option('trigger_id');
		$company = $this->option('company');

        if($trigger_id == 0) {
            $trigger = Utils::saveTrigger($sales_office_no, APISalesOrderHandler::BATCH_SALES_ORDER_NAME , DownloadConnector::STATUS_PENDING, "");
			$trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
        }
		
        $build = SalesOrder::where('code', '=', $so_code)
        ->with(['salesman'])
        ->whereHas('salesman', function ($q) use ($sales_office_no) {
            $q->where('sales_office_no', '=', $sales_office_no);
        });
        $batch = $build->first();

        if(!$batch) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . APISalesOrderHandler::BATCH_SALES_ORDER_NAME . "] " .  $so_code . " not found.", "");      
            return false;
        }
        
		if(!$this->salesOrderUpload($batch, $sales_office_no, $company, $trigger_id)){
            return;
        }
        
		if($batch->transaction_type == 1) {
            if(!$this->salesOrderReservationEntryUpload($batch, $sales_office_no,$company,  $trigger_id)){
                return;
            }
            
            if(!$this->salesOrderPostUpload($batch, $sales_office_no,$company, $trigger_id)){
                return;
            }

			Artisan::call(
				'apirun:invoice',
				[
					'code' => $so_code, 'sales_office_no' => $sales_office_no, 'company' => $company, 'trigger_id' => $trigger_id
				]
			);
        } 
       
    }

    public function salesOrderUpload($batch, $sales_office_no, $company, $trigger_id = null ) {

        $route = Params::values()['webservice']['abi_msd']['route']['sales-order']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesOrderData::MODULE_NAME_SALES_ORDER ). " " . $batch->code;
        $success = false;

        $stock_conversion = $batch->conversion()->get();
        if ($stock_conversion && count($stock_conversion) > 0) {
            $skip_batch = false;
            foreach ($stock_conversion as $stock) {
                $conversion_route = Params::values()['webservice']['abi_msd']['route']['stock-conversion']['list'];
                $conversion_url = Globals::soapABIMSDynamicsURL($conversion_route, $company, "codeunit");
                $salesman = $batch->salesman()->first();
                $wms_sales_office = WMSSalesOffice::where('sales_office_code', '=', $salesman->sales_office_no)->where('company_id', '=', Params::values()['abi_wms_company_id'])->first();

                $data_conversion = new StockConversionData;
                $data_conversion = UploadConnector::assignToData($data_conversion, $stock);
                $data_conversion->salesman_code = $salesman->code;
                if($batch->transaction_type == 0) {
                    if($wms_sales_office) {
                        $wms_zone = $wms_sales_office->zone()->first();
                        $data_conversion->zone_code = $wms_zone->zone_code;
                    }
                }
                else {
                    $data_conversion->zone_code = $salesman->zone;
                }
                $stock->sales_office_code = $salesman->sales_office_no;
                $sku_model = $stock->sku()->first();
                unset($stock->sales_office_code);

                if ($sku_model)
                    $data_conversion->sku_code = $sku_model->sys_21;

                $conversion_params = $data_conversion->xmlMSDArrayString();
                $conversion_request = new SoapVar($conversion_params, XSD_ANYXML);
                $conversion_result = Globals::callSoapApiOther($conversion_url, $conversion_request, "StockSplit", $sales_office_no);
                if (is_string($conversion_result)) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $batch->code . " " . $conversion_result . ".", "");
                    $skip_batch = true;
                   continue;
                } else {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $batch->code . " successfully split and converted", ""); /* Save log info message */
                }
            }
            if ($skip_batch)
                return false;
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] No stock conversion.", "");
        }

        /* Assign each model to data for XML */
        $data = new SalesOrderData;
        $data = UploadConnector::assignToData($data, $batch);
        $data->code = strtoupper($data->code);
        $data->order_type_sales = $batch->transaction_type == 1 ? "Mobile_Van_Sales" : "Mobile_PreOrder";
        $data->credit_invoice = $batch->type == 1 ? "true" : "false";
        $data->emtpies_type = $batch->type == 1 ? "Loan" : "Deposit";
        $data->sales_order_date  = date("Y-m-d", strtotime($data->sales_order_date));
        /* Get foreign key */
        $location = $batch->location()->first();
        $salesman = $batch->salesman()->first();
        if ($location)
            $data->location_code = $location->code;
        if ($salesman) {
            $wms_sales_office = WMSSalesOffice::where('sales_office_code', '=', $salesman->sales_office_no)->where('company_id', '=', Params::values()['abi_wms_company_id'])->first();
            $wms_sales_office_zone_code = $wms_sales_office->zone()->first();
            $wms_sales_office_zone_code = $wms_sales_office_zone_code != null ? $wms_sales_office_zone_code["zone_code"] : "";
            $data->salesman_code = $salesman->code;
            $data->zone_code = $batch->transaction_type == 1 ? $salesman->zone : $wms_sales_office_zone_code;
        }
        $data->empties_exist = "false";
        $data->ship = "false";
        $data->invoice = "false";
        /* Detail */
        $data_detail = [];
        $batch_details = $batch->salesOrderDetail()->get();
        $batch_empties = $batch->salesOrderReturnable()->get();
        
        if (count($batch_details) > 0 || count($batch_empties) > 0) {
            foreach ($batch_details as $dt_key => $batch_detail) {
                $dt_data = new SalesOrderDetailData;
                $dt_data = UploadConnector::assignToData($dt_data, $batch_detail);
                $batch_detail->sales_office_code = $batch->salesman()->first()->sales_office_no;
                $sku_model = $batch_detail->sku()->first();
                $discount_case_no = $batch_detail->discount_no;
                $discount_case_nos = preg_split('/(\s*-*\s*)*-+(\s*-*\s*)*/', $discount_case_no);
                $discount_case_models = [];

                foreach ($discount_case_nos as $discount_code) {
                    $dcn_result =  DiscountCase::where('discount_m_case_no', '=', $discount_code)
                        ->where('sales_office_no', '=', $batch_detail->sales_office_code)->first();
                    if ($dcn_result)
                        $discount_case_models[] = $dcn_result;
                }
                unset($batch_detail->sales_office_code);

                if ($sku_model)
                    $dt_data->sku_code = $sku_model->sys_21;
                if (!empty($discount_case_models)) {
                    $const_dcm = SalesOrderDetailData::SCHEMES;
                    foreach ($discount_case_models as $dcm_key => $dcm_mod)
                        if (isset($const_dcm[$dcm_key])) {
                            $attr_key = $const_dcm[$dcm_key];
                            $dt_data->$attr_key = $dcm_mod->disc_type_no;
                        }
                    $dt_data->discount_scheme = "true";
                } else {
                    $dt_data->discount_no = ""; // Reset any value
                    $dt_data->discount_scheme = "false";
                }
                $dt_data->type = "Item";
                $dt_data->empties_type = 'Deposit';
                $data_detail[$dt_key. "_FULLS"] = $dt_data;
            }
            foreach ($batch_empties as $dt_key => $batch_detail) {
                $dt_data = new SalesOrderDetailData;

                
                if($batch_detail->qty_paid > 0) {
                    $dt_data_deposit = new SalesOrderDetailData;
                    $dt_data_loan = new SalesOrderDetailData;

                    $sales_order = $batch_detail->salesOrder()->get();
                    $sku_model = $batch_detail->sku()->first();
                    if ($sku_model) {
                        $dt_data_deposit->sku_code = $sku_model->sys_21;
                        $dt_data_loan->sku_code = $sku_model->sys_21;
                    }
                    $dt_data_deposit->type = "Item";
                    $dt_data_loan->type = "Item";
                    $dt_data_deposit->empties_type = 'Deposit';
                    $dt_data_loan->empties_type = 'Loan';
                    $dt_data_deposit->quantity =  $batch_detail->qty_paid + $batch_detail->return; 
                    $dt_data_loan->quantity =  $batch_detail->delivery - ($batch_detail->qty_paid + $batch_detail->return);
                    
                    if( $dt_data_loan->quantity > 0) {
                        $data_detail[$dt_key. "_LOAN"] = $dt_data_loan;
                    }
                    $data_detail[$dt_key. "_DEPOSIT"] = $dt_data_deposit;

                }
                else {

                    $sku_model = $batch_detail->sku()->first();

                    if ($sku_model)
                        $dt_data->sku_code = $sku_model->sys_21;

                    $dt_data->type = "Item";
                    $dt_data->empties_type = 'Deposit';
                    $dt_data->quantity=  $batch_detail->return;

                    $data_detail[$dt_key . "_EMPTIES"] = $dt_data;
                
                }
            }
        }
        $data->salesOrderDetail = $data_detail;
        /* Check if create or update */
        if (is_null($data->ms_dynamics_key) || empty($data->ms_dynamics_key)) {
            
            /* Create new data in MSD */
            $batch_params = "<ns1:Create>" . $data->xmlMSDArrayString() . "</ns1:Create>";
            $batch_request = new SoapVar($batch_params, XSD_ANYXML);
            $soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
            /** Log response message */
            if (is_string($soap_result)) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result . ".", "");
            } elseif ($soap_result && property_exists($soap_result, "SalesOrderService")) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " successfully uploaded.", ""); /* Save log info message */
                /* Generate header update request */
                $batch_params = "<ns1:Update>
                                    <ns1:SalesOrderService>
                                        <ns1:Key>" . $soap_result->SalesOrderService->Key . "</ns1:Key>
                                        <ns1:Status>Released</ns1:Status>            
                                    </ns1:SalesOrderService>
                                </ns1:Update>";
                $batch_request_header = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result_header = Globals::callSoapApiUpdate($url, $batch_request_header, $sales_office_no);
                if (is_string($soap_result_header))
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_header . ".", "");
                elseif ($soap_result_header && property_exists($soap_result_header, "SalesOrderService")) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " successfully updated released status.", ""); /* Save log info message */
                    $success = true;
                }
            }
            Globals::saveJsonFile($file_name, $data);
        } else {
            
            /* Used different API endpoint for updating */
            $line_route = Params::values()['webservice']['abi_msd']['route']['sales-order-line']['list'];
            $line_url = Globals::soapABIMSDynamicsURL($line_route, $company);
            /* Generate header update request */
            $batch_params = "<ns1:Update>" . $data->xmlMSDArrayString(false) . "</ns1:Update>";
            $batch_request_header = new SoapVar($batch_params, XSD_ANYXML);
            $soap_result_header = Globals::callSoapApiUpdate($url, $batch_request_header, $sales_office_no);
            /* Save generated request as file backup */
            Globals::saveJsonFile($file_name, $data);
            if (is_string($soap_result_header))
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_header, "");
            elseif ($soap_result_header && property_exists($soap_result_header, "SalesOrderService")) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " successfully re-uploaded.", ""); /* Save log info message */
                $success = true;
            }
            /* Generate detail update request */
            if (count($data->salesOrderDetail) > 0 && $success) {
                foreach ($data->salesOrderDetail as $d_key => $detail_param) {
                    $batch_detail_params = "<ns1:Update>" . $detail_param->xmlMSDSubformArrayString() . "</ns1:Update>";
                    $batch_request_detail = new SoapVar($batch_detail_params, XSD_ANYXML);
                    $soap_result_detail = Globals::callSoapApiUpdate($line_url, $batch_request_detail, $sales_office_no);
                    /* Save generated request as file backup */
                    Globals::saveJsonFile($file_name . "_DETAIL-" . ($d_key + 1) , $detail_param);
                    if (is_string($soap_result_detail)) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_detail . ".", "");
                    }
                }
            }
        }
        /**
         * Retrieve Sales Order by No then save MS Dynamics Key both header and lines
         * Need to read header MS Dynamics Key when updating lines and also while syncing record that already exist
         * Both header and lines need to save MS Dynamics Key from the response
         */
        $read_params = "<ns1:Read>
                            <ns1:Document_Type>Order</ns1:Document_Type>
                            <ns1:No>" . $data->code . "</ns1:No>
                        </ns1:Read>";
        $read_request = new SoapVar($read_params, XSD_ANYXML);
        $soap_result_read = Globals::callSoapApiRead($url, $read_request, $sales_office_no);
        if (is_string($soap_result_read)) {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_read . ".", "");
        } elseif ($soap_result_read && property_exists($soap_result_read, "SalesOrderService")) {
            $header_response = $soap_result_read->SalesOrderService;
            /* Assign MSD key to outgoing_notification */
            $batch->msd_synced = 1;
            $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
            $batch->edited_when = date("Y-m-d H:i:s");
            UploadConnector::saveMsDynamicsKey($batch, $header_response->Key);
            /* Assign MSD key to outgoing_notification_detail */
            if (property_exists($header_response, "SalesLines") && property_exists($header_response->SalesLines, "Sales_Line_Service") && count($header_response->SalesLines->Sales_Line_Service) > 0) {
                $response_lines = is_array($header_response->SalesLines->Sales_Line_Service) ? $header_response->SalesLines->Sales_Line_Service : [$header_response->SalesLines->Sales_Line_Service];
                foreach ($response_lines as $response_line) {
                    if (property_exists($response_line, "Key")) {
                        $product_code = isset($response_line->No) ? $response_line->No : "";
                        $uom = isset($response_line->Unit_of_Measure_Code) ? $response_line->Unit_of_Measure_Code : "";
                        $line_no = isset($response_line->Line_No) ? $response_line->Line_No : "";
                        $detail_model = SalesOrderDetail::where('sales_order_id', '=', $batch->id)->where('product_code', '=', $product_code . '-' . $uom)->first();
                            
                        if ($detail_model) {
                            $detail_model->line_no = $line_no;
                            $detail_model->edited_by = UploadConnector::MSD_LOGGER_NAME;
                            $detail_model->edited_when = date("Y-m-d H:i:s");
                            UploadConnector::saveMsDynamicsKey($detail_model, $response_line->Key);
                        }
                    }
                }
            }
        }

        if ($success) {
            Utils::updateTriggerTotalRows($trigger_id, 1,  false); /* Update trigger total rows */
        } else {
            Utils::updateTriggerFailedRows($trigger_id, 1, false); /* Update trigger failed rows = existing + response failed_rows */
        }

        return true;
    }

    
    public function salesOrderReservationEntryUpload($batch, $sales_office_no, $company, $trigger_id = null ) {

        $route = Params::values()['webservice']['abi_msd']['route']['reservation-entry']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $success = false;
        $module = (new ReservationEntryData)->getModuleByType(0);
        $module_name = (new ReservationEntryData)->getModuleNameByType(0);
		$file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", $module_name ). " " . $batch->code;
        
        /* Assign each model to data for XML */
        $batch_details = $batch->salesOrderDetail()->whereNotNull('line_no')->get();
        if ($batch_details && count($batch_details) > 0) {
            foreach ($batch_details as $batch_detail) {
                $data = new ReservationEntryData;
                $batch_detail->sales_office_code = $batch->salesman()->first()->sales_office_no;
                $sku_model = $batch_detail->sku()->first();
                unset($batch_detail->sales_office_code);

                if ($sku_model)
                    $data->sku_code = $sku_model->sys_21;
                $data->zone_code = $batch->salesman()->first()->zone;
                $data->line_no = $batch_detail->line_no;
                $data->lot_no = $batch_detail->lot_no;
                $data->entry_no = $batch_detail->entry_no;
                $data->reservation_status = "Surplus";
                $data->quantity = -$batch_detail->quantity;
                $data->source_id = $batch->code;
                $data->line_no = $batch_detail->line_no;
                $data->shipment_date = date('Y-m-d', strtotime($batch->sales_order_date));
                $data->source_type =  37;
                $data->source_subtype = '_x0031_';
                $data->item_track = "Lot_No";
                $data->creation_date = date('Y-m-d'); // Fixed?

                /* Check if create or update */
                if (is_null($data->entry_no) || empty($data->entry_no)) {
                    /* Create new data in MSD */
                    $batch_params = "<ns1:Create>" . $data->xmlMSDArrayString() . "</ns1:Create>";
                    $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
                    /** Log response message */
                    if (is_string($soap_result)) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->source_id . " " . $soap_result, "");
                    } elseif ($soap_result && property_exists($soap_result, "ReservationEntriesService")) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->source_id . " successfully uploaded.", ""); /* Save log info message */
                        /* Header */
                        $batch->status = "reserved";
                        $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->edited_when = date("Y-m-d H:i:s");
                        /* Detail */
                        $batch_detail->entry_no = isset($soap_result->ReservationEntriesService->EntryNo) ? $soap_result->ReservationEntriesService->EntryNo : "";
                        $batch_detail->edited_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch_detail->edited_when = date("Y-m-d H:i:s");
                        if ($batch->save() && $batch_detail->save())
                            $success = true;
                    }
                    Globals::saveJsonFile($file_name, $data);
                } else {
                    /**
                     * Retrieve Reservation Entries by EntryNo then get MS Dynamics Key from response
                     * Used MS Dynamics Key to update the record in MSD through Update method
                     */
                    $read_params = "<ns1:Read><ns1:EntryNo>" . $data->entry_no . "</ns1:EntryNo></ns1:Read>";
                    $read_request = new SoapVar($read_params, XSD_ANYXML);
                    $soap_result_read = Globals::callSoapApiRead($url, $read_request, $sales_office_no);
                    if ($soap_result_read && property_exists($soap_result_read, "ReservationEntriesService")) {
                        $data->ms_dynamics_key = $soap_result_read->ReservationEntriesService->Key;
                        /* Generate header update request */
                        $batch_params = "<ns1:Update>" . $data->xmlMSDArrayString(false) . "</ns1:Update>";
                        $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                        $soap_result = Globals::callSoapApiUpdate($url, $batch_request, $sales_office_no);
                        /* Save generated request as file backup */
                        Globals::saveJsonFile($file_name, $data);
                        if (is_string($soap_result))
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->source_id . " " . $soap_result, "");
                        elseif ($soap_result && property_exists($soap_result, "ReservationEntriesService")) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->source_id . " successfully re-uploaded.", ""); /* Save log info message */
                            /* Header */
                            $batch->status = "reserved";
                            $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
                            $batch->edited_when = date("Y-m-d H:i:s");
                            /* Detail */
                            $batch_detail->entry_no = isset($soap_result->ReservationEntriesService->EntryNo) ? $soap_result->ReservationEntriesService->EntryNo : "";
                            $batch_detail->edited_by = UploadConnector::MSD_LOGGER_NAME;
                            $batch_detail->edited_when = date("Y-m-d H:i:s");
                            if ($batch->save() && $batch_detail->save())
                                $success = true;
                        }
                    }
                }
            }

            if ($success) {
                Utils::updateTriggerTotalRows($trigger_id, 1, false); /* Update trigger total rows */
            } else {
                Utils::updateTriggerFailedRows($trigger_id, 1, false); /* Update trigger failed rows = existing + response failed_rows */
            }
        }
        
        return true;
    }

    public function salesOrderPostUpload($batch, $sales_office_no, $company, $trigger_id = null ) {

        $route = Params::values()['webservice']['abi_msd']['route']['sales-order-post']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company, "CodeUnit");
        $success = false;
        /* Create new data in MSD */
        $batch_params = "<ns1:SalesOrderPost><ns1:documentNo>" . $batch->code . "</ns1:documentNo></ns1:SalesOrderPost>";
        $batch_request = new SoapVar($batch_params, XSD_ANYXML);
		$file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesOrderData::MODULE_NAME_SALES_ORDER_POST ). " " . $batch->code;
        
        try {
            $client = new SoapClient($url, array(
                'trace' => true,
                'login' => Globals::getSoapOptions()['username'],
                'password' => Globals::getSoapOptions()['password'],
    
            ));
            $soap_result = $client->SalesOrderPost($batch_request);
            if (is_string($soap_result)) {
                $batch->status = "failed_post";
                $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
                $batch->edited_when = date("Y-m-d H:i:s");
                if ($batch->save())
                    $success = true;
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . "] " . $batch->code . " " . $soap_result, "");
            } else {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . "] " . $batch->code . " successfully posted.", ""); /* Save log info message */
                $batch->status = "posted";
                $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
                $batch->edited_when = date("Y-m-d H:i:s");
                if ($batch->save())
                    $success = true;
            }
            Globals::saveJsonFile($file_name, $batch);
        } catch (SoapFault $e) {
            if (is_string($e->getMessage())) 
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . "] " . $batch->code . " " . $e->getMessage(), "");
        }

        if ($success) {
            Utils::updateTriggerTotalRows($trigger_id, 1, false); /* Update trigger total rows */
        } else {
            Utils::updateTriggerFailedRows($trigger_id, 1, false); /* Update trigger failed rows = existing + response failed_rows */
        }
        return true;
    }
}
