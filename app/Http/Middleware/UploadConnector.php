<?php

namespace App\Http\Middleware;

use Illuminate\Database\Eloquent\Model;
use SoapClient;
use SoapFault;
use SoapVar;;
use DB;
use Carbon\Carbon;
/* Jobs */
use App\Jobs\APIJobSalesOrderHandler as APIJobSalesOrderHandler;
use App\Jobs\APIJobHandler;
/* Utils */
use App\Utils\Globals;
use App\Utils\Params;
use App\Utils\Utils;
/* Model */
use App\Model\noc\CAF as CAF;
use App\Model\noc\CAFApplCreditReqQuestion;
use App\Model\noc\CAFApplCreditReqAnswer;
use App\Model\wms\Employee as WMSEmployee;
use App\Model\wms\IncomingInventory;
use App\Model\wms\IncomingInventoryDetail;
use App\Model\noc\Location as Location;
use App\Model\noc\LocationDetail as LocationDetail;
use App\Model\wms\OutgoingInventory;
use App\Model\wms\OutgoingInventoryDetail;
use App\Model\wms\OutgoingNotification as WMSOutgoingNotification;
use App\Model\wms\OutgoingNotificationDetail as WMSOutgoingNotificationDetail;
use App\Model\noc\Salesman as Salesman;
use App\Model\noc\SalesGroup as SalesGroup;
use App\Model\wms\Salesman as WMSSalesman;
use App\Model\noc\SalesOffice as SalesOffice;
use App\Model\wms\SalesOffice as WMSSalesOffice;
use App\Model\noc\SalesOrder;
use App\Model\noc\SalesOrderDetail;
use App\Model\noc\SalesOrderReturnable;
use App\Model\noc\Sku;
use App\Model\noc\TempCollectionCash;
use App\Model\noc\Invoice;
use App\Model\noc\InvoiceDetails;
use App\Model\noc\DiscountCase;
use App\Model\noc\DealsPromotion;
use App\Model\wms\Zone as WMSZone;
/* Schema */
use App\Data\InvoiceData;
use App\Data\InvoiceDetailData;
use App\Data\OutgoingInventoryData;
use App\Data\OutgoingInventoryDetailData;
use App\Data\OutgoingNotificationData as OutgoingNotificationData;
use App\Data\OutgoingNotificationDetailData as OutgoingNotificationDetailData;
use App\Data\LocationData as LocationData;
use App\Data\SalesOrderData;
use App\Data\SalesOrderDetailData;
use App\Data\SalesCreditMemoData;
use App\Data\SalesCreditMemoLineData;
use App\Data\NewCustomerRequestData;
use App\Data\CashEmptiesAdjustmentsData;
use App\Data\SalesShipmentData as SalesShipmentData;
use App\Data\StockConversionData as StockConversionData;
use App\Data\ReservationEntryData as ReservationEntryData;
use App\Data\CashReceiptData as CashReceiptData;
use App\Data\TransferOrderData as TransferOrderData;
use App\Data\TransferOrderDetailData as TransferOrderDetailData;

class UploadConnector extends Model
{
    /** Trigger Status */
    const STATUS_PENDING = "PENDING";
    const STATUS_ONGOING = "ONGOING";
    const STATUS_DONE = "DONE";
    /* Module - Upload */
    const MODULE_SALES_ORDER = "SALES ORDER";
    const MODULE_SALES_ORDER_DETAIL = "SALES ORDER DETAIL";
    const MODULE_SALES_ORDER_LOT = "SALES ORDER LOT";
    const MODULE_SALES_ORDER_RELEASE = "SALES ORDER RELEASE";
    const MODULE_SALES_ORDER_POST = "SALES ORDER POST";
    const MODULE_SALES_CREDIT_MEMO_POST = "CREDIT MEMO POST";
    const MODULE_CASH_RECEIPT_JOURNAL = "CASH RECEIPT JOURNAL";
    const MODULE_CASH_RECEIPT_POST = "CASH RECEIPT POST";
    const MODULE_TRANSFER_ORDER = "TRANSFER ORDER";
    const MODULE_TRANSFER_LOT = "TRANSFER LOT";
    const MODULE_SALES_RETURN_ORDER = "RETURN ORDER";
    const MODULE_SALES_RETURN_ORDER_DETAIL = "RETURN ORDER DETAIL";
    const MODULE_SALES_RETURN_ORDER_LOT = "RETURN ORDER LOT";
    const MODULE_NEW_LOCATION_CREATION = "NEW LOCATION REQUEST";
    const MODULE_SALES_ORDER_MULTIPLE = "SALES ORDER MULTIPLE";
    const MODULE_CREDIT_MEMO_MULTIPLE = "CRMEMO MULTIPLE";
    const MODULE_CASH_RECEIPT_MULTIPLE = "CSRCPT MULTIPLE";
    const MODULE_BATCH_TRANSACTION = "BATCH TRANSACTION";
    const MODULE_CAF_CREDIT_LIMIT = "CAF CREDIT LIMIT";
    /* Module Name - Upload */
    const MODULE_NAME_SALES_ORDER = "Sales Order";
    const MODULE_NAME_SALES_ORDER_DETAIL = "Sales Order Detail";
    const MODULE_NAME_SALES_ORDER_LOT = "Sales Order LOT Detail";
    const MODULE_NAME_SALES_ORDER_RELEASE = "Release Sales Order";
    const MODULE_NAME_SALES_ORDER_POST = "Post Sales Order";
    const MODULE_NAME_CASH_RECEIPT_JOURNAL = "Cash Receipt Journal";
    const MODULE_NAME_CASH_RECEIPT_POST = "Cash Receipt Post";
    const MODULE_NAME_TRANSFER_ORDER = "Transfer Order";
    const MODULE_NAME_TRANSFER_LOT = "Transfer LOT Detail";
    const MODULE_NAME_SALES_RETURN_ORDER = "Sales Return Order";
    const MODULE_NAME_SALES_RETURN_ORDER_DETAIL = "Sales Return Order Detail";
    const MODULE_NAME_SALES_RETURN_ORDER_LOT = "Sales Return Order LOT";
    const MODULE_NAME_NEW_LOCATION_CREATION = "New Customer Creation Request";
    const MODULE_NAME_SALES_CREDIT_MEMO_POST = "Sales Credit Memo Post";
    const MODULE_NAME_SALES_ORDER_MULTIPLE = "Batch Sales Order";
    const MODULE_NAME_CREDIT_MEMO_MULTIPLE = "Batch Sales Credit Memo";
    const MODULE_NAME_CASH_RECEIPT_MULTIPLE = "Batch Cash Receipt";
    const MODULE_NAME_BATCH_TRANSACTION = "Batch Transaction";
    const MODULE_NAME_CAF_CREDIT_LIMIT = "CAF credit limit";
    /* Log Level */
    const ERROR = "ERROR";
    const INFO = "INFO";
    const MSD_LOGGER_NAME = "NOCMSD.LOGGER";
    const BATCH_LIMIT = 100;
    const PATH = "UPLOAD/";

    /**
     * Takes data from NOC and sends it to client REST API.
     * (27/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDWithdrawalRequest($method, $url, $data, $trigger_id = null)
    {
        $module = (new OutgoingNotificationData)->getModuleByType(0);
        $module_name = (new OutgoingNotificationData)->getModuleNameByType(0);
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $withdrawal_request_code = isset($data['withdrawal_request_code']) ? $data['withdrawal_request_code'] : "";
        $company_code = Globals::getWmsCompanyCode();

        /* Get models to send to MSD */
        $salesman = Salesman::select('code')->where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)->get()->toarray();
        $build = WMSOutgoingNotification::where('transaction_type', '=', 0)
            ->where('msd_synced', '=', 0)
			->where('withdrawal_code', '!=', "")
            ->whereIn('employee_code', $salesman)
            ->whereBetween('withdrawal_date', [$date_from, $date_to]);
        if ($withdrawal_request_code != "")
            $build = $build->where('withdrawal_code', '=', $withdrawal_request_code);
        $wms_data = count($salesman) > 0 ? $build->get() : [];
        $total_rows = count($wms_data);
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", $module);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */
        if ($total_rows > 0) {
            foreach ($wms_data as $key => $batch) {
                $success = false;
                /* Assign each model to data for XML */
                $data = new OutgoingNotificationData;
                $data = UploadConnector::assignToData($data, $batch); // Header
                $data->withdrawal_code = strtoupper($batch->withdrawal_code);
                $data->transfer_order_type = 'Withdrawal_Slip';
                $data->direct_transfer = 'true';
                /* Transfer From */
                $sales_office = WMSSalesOffice::with(['company'])
                    ->whereHas('company', function ($q) use ($company_code) {
                        $q->where('code', '=', $company_code);
                    })->where('sales_office_code', '=', $data->sales_office_code)->first();
                if ($sales_office && $sales_office->zone()->first())
                    $data->transfer_from = $sales_office->zone()->first()->zone_code;
                /* Transfer To */
                $employee = WMSEmployee::with(['company'])
                    ->whereHas('company', function ($q) use ($company_code) {
                        $q->where('code', '=', $company_code);
                    })->where('employee_code', '=', $data->employee_code)->first();
                if ($employee && $employee->zone()->first())
                    $data->transfer_to = $employee->zone()->first()->zone_code;
				$data->short_desc =  $sales_office->short_desc;
                /* Detail */
                $data_detail = [];
                $batch_details = $batch->outgoingNotificationDetails()->get();
                if (count($batch_details) > 0) {
                    foreach ($batch_details as $key => $batch_detail) {
                        $dt_data = new OutgoingNotificationDetailData;
                        $dt_data = UploadConnector::assignToData($dt_data, $batch_detail);
                        $dt_data->sales_office_code = $sales_office->short_desc;
                        $sku_model_noc = $batch_detail->nocSku()->first();

                        if ($sku_model_noc)
                            $dt_data->sku_code = $sku_model_noc->sys_21;
                        $data_detail[$key] = $dt_data;
                    }
                }
                $data->outgoingNotificationDetail = $data_detail;
                $is_new = false;

                /* Check if create or update */
                if (is_null($data->ms_dynamics_key) || empty($data->ms_dynamics_key)) {
                    /* Set as new data */
                    $is_new = true;
                    /* Create new data in MSD */
                    $batch_params = "<ns1:Create>" . $data->xmlMSDArrayString() . "</ns1:Create>";
                    $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
                    /** Log response message */
                    if (is_string($soap_result)) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->withdrawal_code . " " . $soap_result . ".", "");
                    } elseif ($soap_result && property_exists($soap_result, "TransferOrderService")) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->withdrawal_code . " successfully uploaded.", ""); /* Save log info message */
                        $success = true;
                    }
					print_r($url);
                    Globals::saveJsonFile($file_name . "-REQUEST-" . ($key + 1), $batch_params);
                    Globals::saveJsonFile($file_name . "-RESPONSE-" . ($key + 1), $soap_result);
                } else {
                    /* Used different API endpoint for updating */
                    $line_route = Params::values()['webservice']['abi_msd']['route']['transfer-order-subform']['list'];
                    $line_url = Globals::soapABIMSDynamicsURL($line_route, $company);
                    /* Generate header update request */
                    $batch_params = "<ns1:Update>" . $data->xmlMSDArrayString(false) . "</ns1:Update>";
                    $batch_request_header = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result_header = Globals::callSoapApiUpdate($url, $batch_request_header, $sales_office_no);
                    /* Save generated request as file backup */
                    Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
                    if (is_string($soap_result_header))
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->withdrawal_code . " " . $soap_result_header . ".", "");
                    elseif ($soap_result_header && property_exists($soap_result_header, "TransferOrderService")) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->withdrawal_code . " successfully re-uploaded.", ""); /* Save log info message */
                        $success = true;
                    }
                    /* Generate detail update request */
                    if (count($data->outgoingNotificationDetail) > 0 && $success) {
                        foreach ($data->outgoingNotificationDetail as $d_key => $detail_param) {
                            $batch_detail_params = "<ns1:Update>" . $detail_param->xmlMSDSubformArrayString() . "</ns1:Update>";
                            $batch_request_detail = new SoapVar($batch_detail_params, XSD_ANYXML);
                            $soap_result_detail = Globals::callSoapApiUpdate($line_url, $batch_request_detail, $sales_office_no);
                            /* Save generated request as file backup */
                            Globals::saveJsonFile($file_name . "_DETAIL-" . ($d_key + 1) . "-" . $key, $detail_param);
                            if (is_string($soap_result_detail)) {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->withdrawal_code . " " . $soap_result_detail . ".", "");
                            }
                        }
                    }
                }
                /**
                 * Retrieve Transfer Order Withdrawal Slip by No then save MS Dynamics Key both header and lines
                 * Need to read header MS Dynamics Key when updating lines and also while syncing record that already exist
                 * Both header and lines need to save MS Dynamics Key from the response
                 */
                $read_params = "<ns1:Read><ns1:No>" . $data->withdrawal_code . " </ns1:No></ns1:Read>";
                $read_request = new SoapVar($read_params, XSD_ANYXML);
                $soap_result_read = Globals::callSoapApiRead($url, $read_request, $sales_office_no);
                if ($soap_result_read && property_exists($soap_result_read, "TransferOrderService")) {
                    $header_response = $soap_result_read->TransferOrderService;
                    /* Assign MSD key to outgoing_notification */
                    $batch->msd_synced = 1;
                    $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                    $batch->updated_date = date("Y-m-d H:i:s");
                    UploadConnector::saveMsDynamicsKey($batch, $header_response->Key);
                    /* Assign MSD key to outgoing_notification_detail */
                    if (property_exists($header_response, "TransferLines") && count($header_response->TransferLines->Transfer_Order_Line) > 0) {
                        $header_lines = count($header_response->TransferLines->Transfer_Order_Line) > 1 ? $header_response->TransferLines->Transfer_Order_Line : [$header_response->TransferLines->Transfer_Order_Line];
                        foreach ($header_lines as $response_line) {
                            $detail_model = WMSOutgoingNotificationDetail::where('outgoing_notification_id', '=', $batch->outgoing_notification_id)->where(
                                'sku_code',
                                '=',
                                $response_line->Item_No .
                                '-' . $response_line->Unit_of_Measure_Code
                            )->first();
                            if ($detail_model && property_exists($response_line, "Key")) {
                                $detail_model->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                $detail_model->updated_date = date("Y-m-d H:i:s");
                                UploadConnector::saveMsDynamicsKey($detail_model, $response_line->Key);
                            }
                        }
                    }

                }

                //Update inventory
                // foreach($data->outgoingNotificationDetail as $data_detailed_sending ){
                //     $new_data = new InventoryAdditionData();
                //     $new_data->company_code = Globals::getWmsCompanyCode();
                //     $new_data->sku_code = $data_detailed_sending->sku_code;
                //     $new_data->sales_office_no = $data->sales_office_no;
                //     $new_data->short_desc = $sales_office->short_desc;
                //     $new_data->salesman_code = $data->salesman_code;
                //     $new_data->qty = $data_detailed_sending->request_quantity;
                //     $new_data->zone_code = $data->transfer_to;
                //     $new_data->expiration_date = $data_detailed_sending->expiration_date;
                //     $new_data->reference_no = $data->reference_no;

                //     $batch_params = '<GetBatchLocationCriteria xsi:type="urn:GetLocationCriteriaArray" soap-enc:arrayType="urn:GetLocationCriteria[]">';
                //     $batch_params .= $new_data->xmlArrayLineStrings();
                //     $batch_params .= '</GetBatchLocationCriteria>';
                //     $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                //     $soap_result = (array) $soap_client->addToLocation($batch_request);

                // }

                if ($success) {
                    Utils::updateTriggerTotalRows($trigger_id, 1, $key === 1 ? false : true); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerFailedRows($trigger_id, 1, $key === 1 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] No new uploaded " . strtolower($module_name) . " found.", ""); /* Save log info message */
        }
    }
	
	public static function syncMSDSplitting($method, $url, $data, $trigger_id = null){
		
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['Sales_Office']) ? $data['Sales_Office'] : "";
        $location = isset($data['Locationcode']) ? $data['Locationcode'] : "";
        $salesman = isset($data['Spcode']) ? $data['Spcode'] : "";
		$document_no =  isset($data['Documentno']) ? $data['Documentno'] : "";
		
		$sales_office_model = SalesOffice::where('short_desc', '=', $sales_office_no)->first();
		if($sales_office_model)
			$sales_office_number = $sales_office_model->no;

		$conversion_route = Params::values()['webservice']['abi_msd']['route']['stock-conversion']['list'];
		$conversion_url = Globals::soapABIMSDynamicsURL($conversion_route, $company, "codeunit");
		$file_name = "SPLIT-REQUEST-".date('Y-m-d')."-".$document_no;

		$data_conversion = new StockConversionData;
		$data_conversion->code = $document_no;
		$data_conversion->quantity = isset($data['Qty']) ? $data['Qty'] : "";
		$data_conversion->lot_no = isset($data['Lotno']) ? $data['Lotno'] : "";
		$data_conversion->salesman_code = $salesman;
		$data_conversion->zone_code = $location;
		$data_conversion->sku_code = $data['Itemno'];

		$conversion_params = $data_conversion->xmlMSDArrayString();
		$conversion_request = new SoapVar($conversion_params, XSD_ANYXML);
		$conversion_result = Globals::callSoapApiOther($conversion_url, $conversion_request, "StockSplit",  $data['Sales_Office'] );
			Globals::saveXMLFile($file_name . "-REQUEST-" . date('Ymd'), $conversion_params);
			Globals::saveJsonFile($file_name . "-RESULT-" . date('Ymd'), json_encode($conversion_result));
		if (is_string($conversion_result)) {
			Utils::saveLog($trigger_id, $sales_office_number, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[Stock Splitting] " . $document_no . ": ". $conversion_result . ".", "");
			
		} else {
			Utils::saveLog($trigger_id, $sales_office_number, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME,  "[Stock Splitting] " . $document_no ." successfully split and converted", ""); /* Save log info message */
		}
	}

    /**
     * Takes data from NOC and sends it to client REST API.
     * (29/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesOrder($method, $url, $data, $trigger_id = null)
    {
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $salesman_code = isset($data['params']['salesman_code']) ? $data['params']['salesman_code'] : "";
        $location_code = isset($data['params']['location_code']) ? $data['params']['location_code'] : "";
        $sales_order_code = isset($data['params']['sales_order_code']) ? $data['params']['sales_order_code'] : "";
        $date_from = (isset($data['params']['date_from']) ? $data['params']['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['params']['date_to']) ? $data['params']['date_to'] : date("Y-m-d")) . " 23:59:59";

        /* Get models to send to MSD */
        $salesman = Salesman::select('id')->where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)->get()->toarray();
        $build = SalesOrder::whereIn('transaction_type', [0, 1]) // 0 = Booking, 1 = Conventional
            ->whereIn('type', [0, 1]) // 0 = Cash, 1 = Credit
            ->where(function ($query) {
				$query->where('msd_synced', '=', 0)
					->orWhere('is_cmos_edited', '=', 1);
			})
            ->where('status', '!=', 'failed')
            ->where('status', '!=', 'posted')
            ->where('status', '!=', '')
            ->whereIn('salesman_id', $salesman)
            ->where('sales_order_date', '>=', $date_from);
            //->where('sales_order_date', '<=', $date_to);
        if ($salesman_code != "") 
            $build = $build->with(['salesman'])
                ->whereHas('salesman', function ($q) use ($salesman_code) {
                    $q->where('code', '=', $salesman_code);
                });
        if ($location_code != "")
            $build = $build->with(['location'])
                ->whereHas('location', function ($q) use ($location_code) {
                    $q->where('code', '=', $location_code);
                });
        if ($sales_order_code != "")
            $build = $build->where('code', '=', $sales_order_code);
        $noc_data = count($salesman) > 0 ? $build->get() : [];
        $total_rows = count($noc_data);
		
		
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesOrderData::MODULE_SALES_ORDER);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

		
        if ($total_rows > 0) {
            $success = false;

            foreach ($noc_data as $key => $batch) {
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
                        if ($batch->transaction_type == 0) {
                            if ($wms_sales_office) {
                                $wms_zone = $wms_sales_office->zone()->first();
                                $data_conversion->zone_code = $wms_zone->zone_code;
                            }
                        } else {
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
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $batch->code . "/ ". $stock->code. ": ". $conversion_result . ".", "");
                            $skip_batch = true;
                            continue;
                        } else {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $batch->code . "/ ". $stock->code." successfully split and converted", ""); /* Save log info message */
                        }
                    }
                    if ($skip_batch)
                        continue;
                } else {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] No stock conversion.", "");
                }

                /* Assign each model to data for XML */
                $data = new SalesOrderData;
                $data = UploadConnector::assignToData($data, $batch);
                $data->code = strtoupper($data->code);
                $data->order_type_sales = $batch->transaction_type == 1 ? "Mobile_Van_Sales" : "Mobile_PreOrder";
                $data->credit_invoice = $batch->type == 1 ? "true" : "false";
                $data->emtpies_type = "Loan" ;
                $data->sales_order_date = date("Y-m-d", strtotime($data->sales_order_date));
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
						$dt_data->sales_order_code = $data->code;
                        $batch_detail->sales_office_code = $batch->salesman()->first()->sales_office_no;
                        $sku_model = $batch_detail->sku()->first();
                        $discount_case_no = $batch_detail->discount_no;
                        $discount_case_nos = preg_split('/\s-\s/', $discount_case_no);
                        $discount_case_models = [];
						$promotion_deals_models =[];

                        foreach ($discount_case_nos as $discount_code) {
                            $dcn_result = DiscountCase::where('discount_m_case_no', '=', $discount_code)->first();
                            if ($dcn_result)
                                $discount_case_models[] = $dcn_result;
                            $prmd_result = DealsPromotion::where('deal_no', '=', $discount_code)
                                ->where('sales_office_no', '=', $batch_detail->sales_office_code)->first();
                            if ($prmd_result)
                                $promotion_deals_models[] = $prmd_result;
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
                        if (!empty($promotion_deals_models)) {
                            foreach ($promotion_deals_models as $dcm_mod){
                                    $dt_data->Scheme_No_1 = $dcm_mod->memo_doc_no;
                            }
							$batch_detail->is_deal = 1;
							$batch_detail->save();
                            $dt_data->foc = "true";
                        } else {
                            $dt_data->foc_item = ""; // Reset any value
                            $dt_data->foc = "false";
                        }
						
						
                        $dt_data->type = "Item";
                        $dt_data->empties_type =  $batch->transaction_type == 0 ? 'Loan' : 'Deposit';
                        $data_detail[$dt_key . "_FULLS"] = $dt_data;
                    }
                    foreach ($batch_empties as $dt_key => $batch_detail) {
                        $dt_data = new SalesOrderDetailData;
								$dt_data->sales_order_code = $data->code;


                        if ($batch_detail->qty_paid > 0) {
                            $dt_data_deposit = new SalesOrderDetailData;
                            $dt_data_loan = new SalesOrderDetailData;

                            $sales_order = $batch_detail->salesOrder()->get();
                            $sku_model = $batch_detail->sku()->first();
                            if ($sku_model) {
                                $dt_data_deposit->sku_code = $sku_model->sys_21;
                                $dt_data_loan->sku_code = $sku_model->sys_21;
                            }
							
							$dt_data_deposit->sales_order_code = $data->code;
							$dt_data_loan->sales_order_code = $data->code;

                            $dt_data_deposit->type = "Item";
                            $dt_data_loan->type = "Item";
                            $dt_data_deposit->empties_type = $batch->transaction_type == 0 ? 'Loan' : 'Deposit';
                            $dt_data_loan->empties_type = 'Loan';
                            $dt_data_deposit->quantity = $batch_detail->qty_paid + $batch_detail->return;
                            $dt_data_loan->quantity = $batch_detail->delivery - ($batch_detail->qty_paid + $batch_detail->return);
                            $dt_data_deposit->id = $batch_detail->id;
                            $dt_data_loan->id = $batch_detail->id;
                            $dt_data_deposit->ms_dynamics_key = $batch_detail->ms_dynamics_key;
                            $dt_data_loan->ms_dynamics_key = $batch_detail->ms_dynamics_key;

                            if ($dt_data_loan->quantity > 0) {
                                $data_detail[$dt_key . "_LOAN"] = $dt_data_loan;
                            }
                            $data_detail[$dt_key . "_DEPOSIT"] = $dt_data_deposit;

                        } else {

                            $sku_model = $batch_detail->sku()->first();

                            if ($sku_model)
                                $dt_data->sku_code = $sku_model->sys_21;

                            $dt_data->type = "Item";
                            $dt_data->empties_type = $batch->transaction_type == 0 ? 'Loan' : 'Deposit';
                            $dt_data->quantity=  $batch_detail->delivery;
                            $data_detail[$dt_key . "_EMPTIES"] = $dt_data;

                        }
                    }
                }
                $data->salesOrderDetail = $data_detail;
                /* Check if create or update */
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " sending.", ""); /* Save log info message */
                if (is_null($data->ms_dynamics_key) || empty($data->ms_dynamics_key)) {

                    /* Create new data in MSD */
                    $batch_params = "<ns1:Create>" . $data->xmlMSDArrayString() . "</ns1:Create>";
                    $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
                    Globals::saveJsonFile($batch->code . "-" . date('YmdHis'), $batch_params);
                    /** Log response message */
                    if (is_string($soap_result)) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result . ".", "");
                    } elseif ($soap_result && property_exists($soap_result, "SalesOrderService")) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " successfully uploaded.", ""); /* Save log info message */
                        /* Generate header update request */
                        // $batch_params = "<ns1:Update>
                                    // <ns1:SalesOrderService>
                                        // <ns1:Key>" . $soap_result->SalesOrderService->Key . "</ns1:Key>
                                        // <ns1:Status>Released</ns1:Status>            
                                    // </ns1:SalesOrderService>
                                // </ns1:Update>";
                        // $batch_request_header = new SoapVar($batch_params, XSD_ANYXML);
                        // $soap_result_header = Globals::callSoapApiUpdate($url, $batch_request_header, $sales_office_no);
                        // if (is_string($soap_result_header)){
                            // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_header . ".", "");
							// $batch->status = "failed";
							// $batch->save();
						// }
                        // elseif ($soap_result_header && property_exists($soap_result_header, "SalesOrderService")) {
                            // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " successfully updated released status.", ""); /* Save log info message */
                            // $success = true;
                        // }
                        $result_sos = $soap_result->SalesOrderService;
                        $inv_sql = "INSERT INTO invoice (code, sales_order_code, amount, due_date, invoice_date, invoice_updated, status, delivered, ct_slip, created_by, updated_by, msd_synced, sales_office_no) VALUES ";
						$append = false;
                        $has_insert = false;
						
						if(property_exists($result_sos, "Order_Type_Sales") && $result_sos->Order_Type_Sales == "Mobile_Van_Sales") {
							if(property_exists($result_sos, "Posting_No")) {
								
								// $msd_data_val = new InvoiceData();
								// $msd_data_val->code = $result_sos->Posting_No;
								// $msd_data_val->sales_order_code = $data->code;
								// $msd_data_val->amount = $data->total_returns;
								// $msd_data_val->due_date = date("Y-m-d");
								// $msd_data_val->invoice_date = date("Y-m-d");
								// $msd_data_val->invoice_updated   = date("Y-m-d");
								// $msd_data_val->status = "new";
								// $msd_data_val->delivered = 1;
								// $msd_data_val->ct_slip = 0;
								// $msd_data_val->created_by = DownloadConnector::MSD_LOGGER_NAME;
								// $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
								// $msd_data_val->msd_synced = 1;
								// $msd_data_val->sales_office_no = $sales_office_no;
								$inv_sql .= " ('". $result_sos->Posting_No."', '".$data->code."', ".$data->total_returns.", '".date("Y-m-d")."', '".date("Y-m-d")."', '".date("Y-m-d")."', 'new', 1, 1, '".DownloadConnector::MSD_LOGGER_NAME."', '".DownloadConnector::MSD_LOGGER_NAME."', 1, ".$sales_office_no.")";
								$append = true;
								$has_insert = true;

							}
							else {
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " there is no Posting_No property: " . json_encode($result_sos), ""); /* Save log info message */
							}
							if(property_exists($result_sos, "CT_Slip_Posting_No")) {
								
								// $msd_data_val_ct = new InvoiceData();
								// $msd_data_val_ct->code = $result_sos->CT_Slip_Posting_No;
								// $msd_data_val_ct->sales_order_code =  $data->code;
								// $msd_data_val_ct->amount = $data->amount - $data->total_returns;
								// $msd_data_val_ct->due_date = date("Y-m-d");
								// $msd_data_val_ct->invoice_date = date("Y-m-d");
								// $msd_data_val_ct->invoice_updated   = date("Y-m-d");
								// $msd_data_val_ct->status = "new";
								// $msd_data_val_ct->delivered = 1;
								// $msd_data_val_ct->ct_slip = 0;
								// $msd_data_val_ct->created_by = DownloadConnector::MSD_LOGGER_NAME;
								// $msd_data_val_ct->updated_by = DownloadConnector::MSD_LOGGER_NAME;
								// $msd_data_val_ct->msd_synced = 1;
								// $msd_data_val_ct->sales_office_no = $sales_office_no;

								if($append) $inv_sql .= " , ";
								$inv_sql .= " ('".$result_sos->CT_Slip_Posting_No."', '".$data->code."', ". ($data->amount - $data->total_returns).", '".date("Y-m-d")."', '".date("Y-m-d")."', '".date("Y-m-d")."', 'new', 1, 1, '".DownloadConnector::MSD_LOGGER_NAME."', '".DownloadConnector::MSD_LOGGER_NAME."', 1, ".$sales_office_no.")";
								
								$has_insert = true;
							}
							else {
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " there is no CT_Slip_Posting_No property: " . json_encode($result_sos), ""); /* Save log info message */
								
		
							}
							if($has_insert) {
								DB::insert($inv_sql);
							}
							if(property_exists($result_sos, "SalesLines")) {
								$batch_invline_params = '<GetInvoiceDetailCriteria xsi:type="urn:GetInvoiceDetailCriteriaArray" soap-enc:arrayType="urn:GetInvoiceDetailCriteria[]">';
						
								foreach($result_sos->SalesLines as $result_sos_obj ) {
							
									foreach($result_sos_obj as $result_sos_detail ) {	
									if(!isset($result_sos_detail->No) || $result_sos_detail == null) {
										continue;
									}
									
									$uom = isset($result_sos_detail->Unit_of_Measure_Code) ? $result_sos_detail->Unit_of_Measure_Code : "";
									$sku = Sku::where('code', '=', $result_sos_detail->No . '-' . $uom )->first();
									Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "]" . $sku->id, ""); /* Save log error message */
									
									$invline_returnable = SalesOrderReturnable::where('sku_id', '=', $sku->id)
									->where('sales_order_id', '=', $batch->id)
									->first();
									 
									if($result_sos_detail->Empties == 1 && property_exists($result_sos, "CT_Slip_Posting_No")) {
										
										// $msd_data_val_line = new InvoiceDetailData(); 
										// $msd_data_val_line->inv_code = $result_sos->CT_Slip_Posting_No;
										// $msd_data_val_line->product_code = $result_sos_detail->No . '-' . $uom; // Append UOM required in CMOS
										// $msd_data_val_line->line_no = $result_sos_detail->Line_No;
										// $msd_data_val_line->serve_quantity = ($invline_returnable == null ?   0 : $invline_returnable->delivery);
										// $msd_data_val_line->amount = $result_sos_detail->Quantity;
										// $msd_data_val_line->unit_price = $result_sos_detail->Unit_Price;
										// $msd_data_val_line->empties_type = $result_sos_detail->Empties_Type;
										// $msd_data_val_line->so_code =  $data->code;
										// $msd_data_val_line->added_by = DownloadConnector::MSD_LOGGER_NAME;
										// $msd_data_val_line->edited_by = DownloadConnector::MSD_LOGGER_NAME;
										$posting_no = $result_sos->CT_Slip_Posting_No ;
										if(DB::insert('insert into invoice_details (inv_id, inv_code, so_code, product_code, line_no, serve_quantity, unit_price, amount, empties_type, added_by, edited_by) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
											DB::raw('(select id from invoice where code = '. $posting_no.' and sales_order_code = '.$data->code . ' and sales_office_no = '. $sales_office_no.')'),
											$result_sos->CT_Slip_Posting_No,
											$data->code, 
											$result_sos_detail->No . '-' . $uom,
											$result_sos_detail->Line_No,
											($invline_returnable == null ?   0 : $invline_returnable->delivery),
											$result_sos_detail->Unit_Price,
											$result_sos_detail->Quantity,
											$result_sos_detail->Empties_Type,
											DownloadConnector::MSD_LOGGER_NAME, 
											DownloadConnector::MSD_LOGGER_NAME
										]))
										Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] Save invoice details " . $result_sos->CT_Slip_Posting_No . " " . $result_sos_detail->No . '-' . $uom, ""); /* Save log info message */
										else 
										Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] Unable to save invoice details " . $result_sos->CT_Slip_Posting_No . " " . $result_sos_detail->No . '-' . $uom, ""); /* Save log info message */
										
									}
									else if(property_exists($result_sos, "Posting_No")){
										
										// $msd_data_val_line = new InvoiceDetailData(); 
										// $msd_data_val_line->inv_code = $result_sos->Posting_No;
										// $uom = isset($result_sos_detail->Unit_of_Measure_Code) ? $result_sos_detail->Unit_of_Measure_Code : "";
										// $msd_data_val_line->product_code = $result_sos_detail->No . '-' . $uom; // Append UOM required in CMOS
										// $msd_data_val_line->line_no = $result_sos_detail->Line_No;
										// $msd_data_val_line->serve_quantity = ($invline_returnable == null ?   0 : $invline_returnable->delivery);
										// $msd_data_val_line->amount = $result_sos_detail->Quantity;
										// $msd_data_val_line->unit_price = $result_sos_detail->Unit_Price;
										// $msd_data_val_line->empties_type = $result_sos_detail->Empties_Type;
										// $msd_data_val_line->so_code =  $data->code;
										// $msd_data_val_line->added_by = DownloadConnector::MSD_LOGGER_NAME;
										// $msd_data_val_line->edited_by = DownloadConnector::MSD_LOGGER_NAME;
										$posting_no = $result_sos->Posting_No;
											
										if(DB::insert('insert into invoice_details (inv_id, inv_code, so_code, product_code, line_no, serve_quantity, unit_price, amount, empties_type, added_by, edited_by) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
											DB::raw('(select id from invoice where code = '. $posting_no .' and sales_order_code = '.$data->code . ' and sales_office_no = '. $sales_office_no.')'),
											$result_sos->Posting_No,
											$data->code, 
											$result_sos_detail->No . '-' . $uom,
											$result_sos_detail->Line_No,
											($invline_returnable == null ?   0 : $invline_returnable->delivery),
											$result_sos_detail->Unit_Price,
											$result_sos_detail->Quantity,
											$result_sos_detail->Empties_Type,
											DownloadConnector::MSD_LOGGER_NAME, 
											DownloadConnector::MSD_LOGGER_NAME
										]))
										Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] Save invoice details " . $result_sos->Posting_No . " " . $result_sos_detail->No . '-' . $uom, ""); /* Save log info message */
										else 
										Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] Unable to save invoice details " . $result_sos->CT_Slip_Posting_No . " " . $result_sos_detail->No . '-' . $uom, ""); /* Save log info message */
										

									}
									}
								}
							}
						}
                    }
                    Globals::saveJsonFile($file_name . "-" . ($key + 1), $batch_params);
                } else {

                   	/* Used different API endpoint for updating */
                    $line_route = Params::values()['webservice']['abi_msd']['route']['sales-order-line']['list'];
                    $line_url = Globals::soapABIMSDynamicsURL($line_route, $company);
					
                    $del_route = Params::values()['webservice']['abi_msd']['route']['sales-order-post']['list'];					
					$delete_url = Globals::soapABIMSDynamicsURL($del_route, $company, 'CodeUnit');
					$delete_params = 	"
						  <ns1:SalesOrderDeleteLines>
							 <ns1:documentNo>". $data->code ."</ns1:documentNo>
						  </ns1:SalesOrderDeleteLines>
					";
					 $delete_client = new SoapClient($delete_url, array(
						'trace' => true,
						'login' => Globals::getSoapOptions($sales_office_no)['username'],
						'password' => Globals::getSoapOptions($sales_office_no)['password'],
						)
					);
					$delete_request = new SoapVar($delete_params, XSD_ANYXML);
					$soap_delete = $delete_client->SalesOrderDeleteLines($delete_request);
					
					
					if (is_string($soap_delete)) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] "  . $data->code . " " . $soap_delete, "");
					} else {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] "  .   $data->code . " successfully assigned deleted", ""); /* Save log info message */
						
					}
					
					$batch_params_read = "<ns1:Read>
							 <ns1:Document_Type>Order</ns1:Document_Type>
							 <ns1:No>". $data->code ."</ns1:No>
						  </ns1:Read>";
					
                    $batch_read_header = new SoapVar($batch_params_read, XSD_ANYXML);
                    $soap_read = Globals::callSoapApiRead($url, $batch_read_header, $sales_office_no);
					
					if(isset($soap_read->SalesOrderService->Key)) {
						$data->ms_dynamics_key = $soap_read->SalesOrderService->Key;
						 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Updated Key after Delete", "");
					}
					elseif(is_string($soap_read))
						 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_read, "");
					else
						 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . json_encode($soap_read, JSON_PRETTY_PRINT), "");
                    
                    /* Generate header update request */
                    // $batch_params_open = "<ns1:Update>
						// <ns1:SalesOrderService>
							// <ns1:Key>". $data->ms_dynamics_key ."</ns1:Key>
							// <ns1:Status>Open</ns1:Status>
						// </ns1:SalesOrderService>

					// </ns1:Update>";
					
					
                    // $batch_open_header = new SoapVar($batch_params_open, XSD_ANYXML);
                    // $soap_open = Globals::callSoapApiUpdate($url, $batch_open_header, $sales_office_no);
					// if(isset($soap_open->SalesOrderService->Key)) {
						// $data->ms_dynamics_key = $soap_open->SalesOrderService->Key;
						 // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Set as Open", "");
					// }
					// elseif(is_string($soap_open))
						 // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_open, "");
					// else
						 // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . json_encode($soap_open, JSON_PRETTY_PRINT), "");
                    
					/* Generate header update request */
                    $batch_params = "<ns1:Update>" . $data->xmlMSDUpdateArrayString() . "</ns1:Update>";
                    $batch_request_header = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result_header = Globals::callSoapApiUpdate($url, $batch_request_header, $sales_office_no);
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $batch_params, "");
					$new_detail = [];
					$add_sku_detail = [];
					if(isset($soap_result_header->SalesOrderService->Key)) {
						$data->ms_dynamics_key = $soap_result_header->SalesOrderService->Key;
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Edit data successfuly uploaded.", "");
						// if (count($data_detail) > 0) {
							// foreach ($data_detail as $new_detail_data) {
								// if($new_detail_data) {
									// $new_detail[] = $new_detail_data;
								// }
							// }
						// }
						// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " new line no: " . json_encode($new_detail ) .".", "");
					}
					else if (is_string($soap_result_header)) {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_header . ".", "");
                            }
						
					
                    /* Save generated request as file backup */
                    Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
                    // if (is_string($soap_result_header))
                        // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_header, "");
                    // elseif ($soap_result_header && property_exists($soap_result_header, "SalesOrderService")) {
                        // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " successfully re-uploaded.", ""); /* Save log info message */
                        // $success = true;
                    // }
                    /* Generate detail update request */
                    // if (count($new_detail) > 0 ) {
                        // foreach ($new_detail as $d_key => $detail_param) {
                            // $batch_detail_params = "<ns1:Create>" . $detail_param->xmlMSDSubformArrayString() . "</ns1:Create>";
                            // $batch_request_detail = new SoapVar($batch_detail_params, XSD_ANYXML);
                            // $soap_result_detail = Globals::callSoapApiCreate($line_url, $batch_request_detail, $sales_office_no);
                            // /* Save generated request as file backup */
                            // Globals::saveJsonFile($file_name . "_DETAIL-" . ($d_key + 1) . "-" . $key, $detail_param);
                            // if (is_string($soap_result_detail)) {
                                // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_detail . ".", "");
                            // }
							// else
								// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Line NO UPDATED:" . $soap_result_detail->SalesOrderSubfrom->Line_No . " saved .", "");
                        // }
                    // }
					
                    // if (count($add_sku_detail) > 0 ) {
                        // foreach ($add_sku_detail as $d_key => $detail_param) {
                            // $batch_detail_params = "<ns1:Create>" . $detail_param->xmlMSDSubformArrayString() . "</ns1:Create>";
                            // $batch_request_detail = new SoapVar($batch_detail_params, XSD_ANYXML);
                            // $soap_result_detail = Globals::callSoapApiCreate($line_url, $batch_request_detail, $sales_office_no);
                            // /* Save generated request as file backup */
                            // Globals::saveJsonFile($file_name . "_DETAIL-" . ($d_key + 1) . "-" . $key, $detail_param);
                            // if (is_string($soap_result_detail)) {
                                // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_detail . ".", "");
                                // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . json_encode($detail_param). ".", "");
                            // }
							// else
								// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Line NO CREATED:" .$soap_result_detail->SalesOrderSubfrom->Line_No.  " saved .", "");
                        // }
                    // }
					
					$read_params = "<ns1:Read>
								<ns1:Document_Type>Order</ns1:Document_Type>
								<ns1:No>" . $data->code . "</ns1:No>
							</ns1:Read>";
					$read_request = new SoapVar($read_params, XSD_ANYXML);
					$soap_result_read = Globals::callSoapApiRead($url, $read_request, $sales_office_no);
					
					if ($soap_result_read && property_exists($soap_result_read, "SalesOrderService")) {
						$data->ms_dynamics_key = $soap_result_read->SalesOrderService->Key;
					}
					
                    /* Generate header update request */
                    // $batch_params_release =  "<ns1:Update>
						// <ns1:SalesOrderService>

							// <ns1:Key>". $data->ms_dynamics_key ."</ns1:Key>
							// <ns1:Status>Released</ns1:Status>
						// </ns1:SalesOrderService>

					// </ns1:Update>";
                    // $batch_release_header = new SoapVar($batch_params_release, XSD_ANYXML);
                    // $soap_result = Globals::callSoapApiUpdate($url, $batch_release_header, $sales_office_no);
					// if(isset($soap_result->SalesOrderService->Key)) {
						// $data->ms_dynamics_key = $soap_result->SalesOrderService->Key;
						 // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Set as Released", "");
					// }
					// elseif(is_string($soap_result))
						 // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $data->ms_dynamics_key, "");
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
					if($batch->is_cmos_edited == 1)
						$batch->status = 'approved';
                    $batch->is_cmos_edited = 0;
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
                                $detail_model_query = SalesOrderDetail::where('sales_order_id', '=', $batch->id)->where('product_code', '=', $product_code . '-' . $uom);
                                if($response_line->FOC_Item == "true" ){
									$detail_model_query = $detail_model_query->where('is_deal', '=', '1');
								} 
								$detail_model =  $detail_model_query->first();
								
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
                    Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] No new uploaded " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " found.", ""); /* Save log info message */
        }
    }
	
	
	
	
    /**
     * Takes data from NOC and sends it to client REST API.
     * (29/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
  
    public static function syncMSDSalesOrderQueue($method, $url, $json, $trigger_id = null)
    {
        $company = isset($json['company'])  ? $json['company'] : "BII Live";
        $sales_office_no = isset($json['sales_office_no']) ? $json['sales_office_no'] : "";
        $salesman_code = isset($json['salesman_code']) ? $json['salesman_code'] : "";
        $location_code = isset($json['location_code']) ? $json['location_code'] : "";
        $zone_code = isset($json['zone_code']) ? $json['zone_code'] : "";
        $sales_order_code = isset($json['code']) ? $json['code'] : "";
		$sales_office_model = SalesOffice::where('short_desc', '=', $sales_office_no)->first();
		if($sales_office_model)
			$sales_office_no = $sales_office_model->no;
		
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesOrderData::MODULE_SALES_ORDER);
       // Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */
		
		if($json['type'] == 5 and $json['transaction_type'] == 2) {
			$route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-journal']['list'];
			$crurl = Globals::soapABIMSDynamicsURL($route, $company);
			UploadConnector::syncMSDCashReceiptCollectionQueue($method, $crurl, $json, $sales_office_no, $trigger_id);
			return;
		}
		/* Assign each model to data for XML */
		$data = new SalesOrderData;
		foreach($json as $key => $value) {
			if(property_exists($data, $key))
				$data->$key = $value;
		}
		$data->code = strtoupper($data->code);
		$data->order_type_sales = $data->transaction_type == 1 ? "Mobile_Van_Sales" : "Mobile_PreOrder";
		$data->credit_invoice = $data->type == 1 ? "true" : "false";
		$data->emtpies_type = 'Loan' ;
		$data->sales_order_date = date("Y-m-d", strtotime($data->sales_order_date));
		$data->delivery_date = date("Y-m-d", strtotime($data->delivery_date));
		$data->posting_date = date("Y-m-d", strtotime($data->delivery_date));
		/* Get foreign key */
		if ($location_code)
			$data->location_code = $location_code;
		if ($salesman_code) {
			$data->salesman_code = $salesman_code;
		}
		if($zone_code) {
			$data->zone_code = $zone_code;
		}
		$data->empties_exist = "false";
		$data->ship = "false";
		$data->invoice = "false";
		/* Detail */
		$data_detail = [];
		$batch_details = $json['Order_detail_obj'];
		$batch_empties = $json['Order_return_obj'];
		if (count($batch_details) > 0 || count($batch_empties) > 0) {
			foreach ($batch_details as $dt_key => $batch_detail) {
				$dt_data = new SalesOrderDetailData;
				foreach($batch_detail as $key => $value) {
					if(property_exists($dt_data, $key))
						$dt_data->$key = $value;
				}
				
				$sku_model = Sku::where('code', '=', $batch_detail['product_code'] . "-" . $batch_detail['Uom'])->where('sales_office_no', '=', $sales_office_no)->first();
				 
				$dt_data->discount_scheme = "false";
				$dt_data->sku_code = isset($batch_detail['product_code']) ?  $batch_detail['product_code'] : "";
				if (isset($batch_detail['Discount_obj']) && !empty($batch_detail['Discount_obj'])) {
					$const_dcm = SalesOrderDetailData::SCHEMES;
					foreach ($batch_detail['Discount_obj'] as $dcm_key => $dcm_mod)
						if (in_array($dcm_key, $const_dcm)) {
							$dt_data->$dcm_key = $dcm_mod;
						}
				} else {
					$dt_data->discount_no = ""; // Reset any value
					$dt_data->discount_scheme = "false";
				}
				if (isset($batch_detail['Disc_amount_obj']) && !empty($batch_detail['Disc_amount_obj'])) {
					$const_dcm = SalesOrderDetailData::SCHEMES_AMOUNT;
					foreach ($batch_detail['Disc_amount_obj'] as $dcm_key => $dcm_mod)
						if (in_array($dcm_key, $const_dcm)) {
							$dt_data->$dcm_key = $dcm_mod;
							if($dt_data->$dcm_key != 0) {
								$dt_data->discount_scheme = "true";
							}
						}
				}
				if($dt_data->Discount_Amount_1_By_Amount == null && !isset($batch['new_discount'])) {
					$dt_data->Discount_Amount_1_By_Amount = $dt_data->Scheme_1_Discount_Amount ;
				}
				
				$dt_data->location_code = $zone_code;
				
				if (isset($batch_detail['memo_doc_no']) && !empty($batch_detail['memo_doc_no'])) {
					$dt_data->Scheme_No_1 = $batch_detail['memo_doc_no'];
					$dt_data->foc = "true";
				} else {
					$dt_data->foc_item = ""; // Reset any value
					$dt_data->foc = "false";
				}
				
				$dt_data->type = "Item";
				$dt_data->empties_type = 'Loan';
				$data_detail[$dt_key . "_FULLS"] = $dt_data;
			}
			foreach ($batch_empties as $dt_key => $batch_detail) {
				$dt_data = new SalesOrderDetailData;
				if ($batch_detail['qty'] > 0) {
					$dt_data_deposit = new SalesOrderDetailData;
					$dt_data_loan = new SalesOrderDetailData;

					if (isset($batch_detail['sku_code']) ) {
						$dt_data_deposit->sku_code = isset($batch_detail['sku_code']) ?  $batch_detail['sku_code']  : "";
						$dt_data_loan->sku_code =  isset($batch_detail['sku_code']) ?  $batch_detail['sku_code']  : "";
					}
					else{ 
						continue;
					}
					$dt_data_deposit->type = "Item";
					$dt_data_loan->type = "Item";
					$dt_data_deposit->empties_type = isset($batch_detail['empties_type']) ? $batch_detail['empties_type'] : ($json['transaction_type'] == 0 ? 'Loan' : 'Deposit');
					$dt_data_loan->empties_type = 'Loan';
					$dt_data_deposit->quantity = $batch_detail['qty'];
					$dt_data_loan->quantity = $batch_detail['delivery'] - ($dt_data_deposit->quantity);
					$dt_data_deposit->quantity = $batch_detail['qty'];
					$dt_data_deposit->location_code = $zone_code;
					
					$dt_data_loan->discount_scheme = "false";
					
					$dt_data_deposit->discount_scheme = "false";

					// if ($dt_data_loan->quantity > 0) {
						// $data_detail[$dt_key . "_LOAN"] = $dt_data_loan;
					// }
					$data_detail[$dt_key . "_DEPOSIT"] = $dt_data_deposit;

				} else {

					if ( isset($batch_detail['sys_21']) )
						$dt_data->sku_code =  isset($batch_detail['sys_21']) ?  $batch_detail['sys_21']  : "";
					
					$dt_data->type = "Item";
					$dt_data->empties_type = 'Loan';
					$dt_data->quantity=  isset($batch_detail['delivery']) ? $batch_detail['delivery'] : 0;
					$dt_data->line_no = isset($batch_detail['lot_no']) ? $batch_detail['lot_no'] : NULL;
					$dt_data->location_code = $zone_code;
					$dt_data->discount_scheme = "false";
					$data_detail[$dt_key . "_EMPTIES"] = $dt_data;
				}
			}
		}
		$data->salesOrderDetail = $data_detail;
		/* sales order queue is used to insert, placeholder value */
		$is_update = (!empty($json['ms_dynamics_log']) ? 1 : 0);
		/* Check if create or update */
		Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " sending.", ""); /* Save log info message */
		if ($is_update == 0) {

			/* Create new data in MSD */
			$batch_params = "<ns1:Create>" . $data->xmlMSDArrayString() . "</ns1:Create>";
			$batch_request = new SoapVar($batch_params, XSD_ANYXML);
			$soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
			Globals::saveXMLFile($file_name . "-REQUEST-" . date('Ymd'), $batch_params);
			Globals::saveJsonFile($file_name . "-RESULT-" . date('Ymd'), $soap_result);
			/** Log response message */
			if (is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result . ".", "");
			} elseif ($soap_result && property_exists($soap_result, "SalesOrderService")) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " successfully uploaded.", ""); /* Save log info message */
				
				$json['CT_Slip_Posting_No'] = $soap_result->SalesOrderService->CT_Slip_Posting_No;
				$json['Posting_No'] = $soap_result->SalesOrderService->Posting_No;
				
				$result_sos = $soap_result->SalesOrderService;
				$inv_sql = "INSERT INTO invoice (code, sales_order_code, amount, due_date, invoice_date, invoice_updated, status, delivered, ct_slip, created_by, updated_by, msd_synced, sales_office_no) VALUES ";
				$append = false;
				$has_insert = false;
				
				if(property_exists($result_sos, "Order_Type_Sales") && $result_sos->Order_Type_Sales == "Mobile_Van_Sales") {
					if(property_exists($result_sos, "Posting_No")) {
						$inv_sql .= " ('". $result_sos->Posting_No."', '".$data->code."', ".$data->total_returns.", '".date("Y-m-d")."', '".date("Y-m-d")."', '".date("Y-m-d")."', 'new', 1, 1, '".DownloadConnector::MSD_LOGGER_NAME."', '".DownloadConnector::MSD_LOGGER_NAME."', 1, ".$sales_office_no.")";
						$append = true;
						$has_insert = true;

					}
					if(property_exists($result_sos, "CT_Slip_Posting_No")) {

						if($append) $inv_sql .= " , ";
						$inv_sql .= " ('".$result_sos->CT_Slip_Posting_No."', '".$data->code."', ". ($data->amount - $data->total_returns).", '".date("Y-m-d")."', '".date("Y-m-d")."', '".date("Y-m-d")."', 'new', 1, 1, '".DownloadConnector::MSD_LOGGER_NAME."', '".DownloadConnector::MSD_LOGGER_NAME."', 1, ".$sales_office_no.")";
						
						$has_insert = true;
					}
					if($has_insert) {
						DB::insert($inv_sql);
					}
					if(property_exists($result_sos, "SalesLines")) {
						$batch_invline_params = '<GetInvoiceDetailCriteria xsi:type="urn:GetInvoiceDetailCriteriaArray" soap-enc:arrayType="urn:GetInvoiceDetailCriteria[]">';
				
						foreach($result_sos->SalesLines as $result_sos_obj ) {
					
							foreach($result_sos_obj as $result_sos_detail ) {	
								if(!isset($result_sos_detail->No) || $result_sos_detail == null) {
									continue;
								}
								
								$uom = isset($result_sos_detail->Unit_of_Measure_Code) ? $result_sos_detail->Unit_of_Measure_Code : "";
								$sku = $result_sos_detail->No;
								$returnables = $json['Order_return_obj'];
								$invline_returnable = 0;
								foreach( $returnables as $returnable) {
									if($returnable['sku_code'] == $sku) {
										$invline_returnable = $returnable['delivery'];
									}
								}
								 
								if( property_exists($result_sos, "CT_Slip_Posting_No")) {
									
									$posting_no = $result_sos->CT_Slip_Posting_No ;
									$invoiceExists = DB::selectOne('select id from invoice where code = ? and sales_order_code = ? and sales_office_no = ?', [
										$posting_no, 
										$data->code, 
										$sales_office_no
									]);

									if ($invoiceExists) {
										if (DB::insert(
											'insert into invoice_details (inv_id, inv_code, so_code, product_code, line_no, serve_quantity, unit_price, amount, empties_type, added_by, edited_by) 
											values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
											[
												$invoiceExists->id,
												$result_sos->CT_Slip_Posting_No,
												$data->code, 
												$result_sos_detail->No . '-' . $uom,
												isset($result_sos_detail->Line_No) ? $result_sos_detail->Line_No : "",
												$invline_returnable,
												isset($result_sos_detail->Unit_Price) ? $result_sos_detail->Unit_Price : 0,
												$result_sos_detail->Quantity,
												$result_sos_detail->Empties_Type,
												DownloadConnector::MSD_LOGGER_NAME, 
												DownloadConnector::MSD_LOGGER_NAME
											]
										)) {
											Utils::saveLog(
												$trigger_id, 
												$sales_office_no, 
												date("Y-m-d H:i:s"), 
												DownloadConnector::INFO, 
												DownloadConnector::MSD_LOGGER_NAME, 
												"[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] Save invoice details " . $result_sos->CT_Slip_Posting_No . " " . $result_sos_detail->No . '-' . $uom, 
												""
											);
										} else {
											Utils::saveLog(
												$trigger_id, 
												$sales_office_no, 
												date("Y-m-d H:i:s"), 
												DownloadConnector::ERROR, 
												DownloadConnector::MSD_LOGGER_NAME, 
												"[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] Unable to save invoice details " . $result_sos->CT_Slip_Posting_No . " " . $result_sos_detail->No . '-' . $uom, 
												""
											);
										}
									} else {
										Utils::saveLog(
											$trigger_id, 
											$sales_office_no, 
											date("Y-m-d H:i:s"), 
											DownloadConnector::ERROR, 
											DownloadConnector::MSD_LOGGER_NAME, 
											"[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] Invoice does not exist for code: " . $posting_no . " and sales_order_code: " . $data->code, 
											""
										);
									}

								}
								else if(property_exists($result_sos, "Posting_No")){
									
									$posting_no = $result_sos->Posting_No ;
									$invoiceExists = DB::selectOne('select id from invoice where code = ? and sales_order_code = ? and sales_office_no = ?', [
										$posting_no, 
										$data->code, 
										$sales_office_no
									]);

									if ($invoiceExists) {
										if (DB::insert(
											'insert into invoice_details (inv_id, inv_code, so_code, product_code, line_no, serve_quantity, unit_price, amount, empties_type, added_by, edited_by) 
											values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
											[
												$invoiceExists->id,
												$result_sos->Posting_No,
												$data->code, 
												$result_sos_detail->No . '-' . $uom,
												isset($result_sos_detail->Line_No) ? $result_sos_detail->Line_No : "",
												$invline_returnable,
												isset($result_sos_detail->Unit_Price) ? $result_sos_detail->Unit_Price : 0,
												$result_sos_detail->Quantity,
												$result_sos_detail->Empties_Type,
												DownloadConnector::MSD_LOGGER_NAME, 
												DownloadConnector::MSD_LOGGER_NAME
											]
										)) {
											Utils::saveLog(
												$trigger_id, 
												$sales_office_no, 
												date("Y-m-d H:i:s"), 
												DownloadConnector::INFO, 
												DownloadConnector::MSD_LOGGER_NAME, 
												"[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] Save invoice details " . $result_sos->Posting_No . " " . $result_sos_detail->No . '-' . $uom, 
												""
											);
										} else {
											Utils::saveLog(
												$trigger_id, 
												$sales_office_no, 
												date("Y-m-d H:i:s"), 
												DownloadConnector::INFO, 
												DownloadConnector::MSD_LOGGER_NAME, 
												"[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] Unable to save invoice details " . $result_sos->Posting_No . " " . $result_sos_detail->No . '-' . $uom, 
												""
											);
										}
									} else {
										Utils::saveLog(
											$trigger_id, 
											$sales_office_no, 
											date("Y-m-d H:i:s"), 
											DownloadConnector::INFO, 
											DownloadConnector::MSD_LOGGER_NAME, 
											"[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] Invoice does not exist for code: " . $posting_no . " and sales_order_code: " . $data->code, 
											""
										);
									}
								}
							}
						}
					}
				}
				
				if($json["transaction_type"] == 1) {
					UploadConnector::syncMSDCashReceiptJournalQueue($json, $trigger_id);
					if(count($json['Order_return_obj']) > 0) {
						
						dispatch(
							(new APIJobHandler('credit-memo-queue-new', json_encode($json)))
								->onQueue('api-queue-' . strtolower($company))
								->delay(\Carbon\Carbon::now()->addSeconds(30)) 
						);
					}
				}
			
			}
			
			
		
		} else {
			
			
			$read_params = "<ns1:Read>
						<ns1:Document_Type>Order</ns1:Document_Type>
						<ns1:No>" . $data->code . "</ns1:No>
					</ns1:Read>";
			$read_request = new SoapVar($read_params, XSD_ANYXML);
			$soap_result_read = Globals::callSoapApiRead($url, $read_request, $sales_office_no);
			
			if ($soap_result_read && property_exists($soap_result_read, "SalesOrderService")) {
				$data->ms_dynamics_key = $soap_result_read->SalesOrderService->Key;
			}
			

			/* Used different API endpoint for updating */
			$line_route = Params::values()['webservice']['abi_msd']['route']['sales-order-line']['list'];
			$line_url = Globals::soapABIMSDynamicsURL($line_route, $company);
		
			$del_route = Params::values()['webservice']['abi_msd']['route']['sales-order-post']['list'];					
			$delete_url = Globals::soapABIMSDynamicsURL($del_route, $company, 'CodeUnit');
			$delete_params = 	"
				  <ns1:SalesOrderDeleteLines>
					 <ns1:documentNo>". $data->code ."</ns1:documentNo>
				  </ns1:SalesOrderDeleteLines>
			";
			 $delete_client = new SoapClient($delete_url, array(
				'trace' => true,
				'login' => Globals::getSoapOptions($sales_office_no)['username'],
				'password' => Globals::getSoapOptions($sales_office_no)['password'],
				)
			);
			$delete_request = new SoapVar($delete_params, XSD_ANYXML);
			$soap_delete = $delete_client->SalesOrderDeleteLines($delete_request);
			
			
			if (is_string($soap_delete)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] "  . $data->code . " " . $soap_delete, "");
			} else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] "  .   $data->code . " successfully assigned deleted", ""); /* Save log info message */
				
					}
					
			$batch_params_read = "<ns1:Read>
					 <ns1:Document_Type>Order</ns1:Document_Type>
					 <ns1:No>". $data->code ."</ns1:No>
				  </ns1:Read>";
			
			$batch_read_header = new SoapVar($batch_params_read, XSD_ANYXML);
			$soap_read = Globals::callSoapApiRead($url, $batch_read_header, $sales_office_no);
			
			if(isset($soap_read->SalesOrderService->Key)) {
				$data->ms_dynamics_key = $soap_read->SalesOrderService->Key;
				 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Updated Key after Delete", "");
			}
			elseif(is_string($soap_read))
				 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_read, "");
			else
				 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . json_encode($soap_read, JSON_PRETTY_PRINT), "");
			
			/* Generate header update request */
			//$add_sku_detail = $data->getAllNewData();
			$data_update = clone $data;
			//$data_update->salesOrderDetail = $add_sku_detail;
			$batch_params = "<ns1:Update>" . $data_update->xmlMSDArrayString() . "</ns1:Update>";
			$batch_request_header = new SoapVar($batch_params, XSD_ANYXML);
			$soap_result_header = Globals::callSoapApiUpdate($url, $batch_request_header, $sales_office_no);
			Globals::saveJsonFile($file_name . "-SALES-ORDER-UPDATE-" . ($key + 1), $batch_params);
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $batch_params, "");
			$new_detail = [];
			$add_sku_detail = [];
			if(isset($soap_result_header->SalesOrderService->Key)) {
				$data->ms_dynamics_key = $soap_result_header->SalesOrderService->Key;
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Edit data successfuly uploaded.", "");
				$so_service = $soap_result_header->SalesOrderService;
				if (property_exists($so_service, "SalesLines") && property_exists($so_service->SalesLines, "Sales_Line_Service") && count($so_service->SalesLines->Sales_Line_Service) > 0) {
					foreach($so_service->SalesLines->Sales_Line_Service as $msd_return_line) {
						$new_detail_data = $data->updateDetailKey($msd_return_line->No,$msd_return_line->Empties_Type, $msd_return_line->Key, $msd_return_line->Line_No);
						if($new_detail_data) {
							$new_detail[] = $new_detail_data;
						}
					}
				}
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " new line no: " . json_encode($new_detail ) .".", "");
			}
				
			
			/* Save generated request as file backup */
			Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
			// if (is_string($soap_result_header))
				// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_header, "");
			// elseif ($soap_result_header && property_exists($soap_result_header, "SalesOrderService")) {
				// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " successfully re-uploaded.", ""); /* Save log info message */
				// $success = true;
			// }
			/* Generate detail update request */
			if (count($new_detail) > 0 ) {
				foreach ($new_detail as $d_key => $detail_param) {
					$batch_detail_params = "<ns1:Update>" . $detail_param->xmlMSDSubformArrayString() . "</ns1:Update>";
					$batch_request_detail = new SoapVar($batch_detail_params, XSD_ANYXML);
					$soap_result_detail = Globals::callSoapApiUpdate($line_url, $batch_request_detail, $sales_office_no);
					/* Save generated request as file backup */
					Globals::saveJsonFile($file_name . "_DETAIL-" . ($d_key + 1) . "-" . $key, $detail_param);
					if (is_string($soap_result_detail)) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_detail . ".", "");
					}
					else
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Line NO UPDATED:" . $soap_result_detail->SalesOrderSubfrom->Line_No . " saved .", "");
				}
			}
			
			// if (count($add_sku_detail) > 0 ) {
				// foreach ($add_sku_detail as $d_key => $detail_param) {
					// $batch_detail_params = "<ns1:Create>" . $detail_param->xmlMSDSubformArrayString() . "</ns1:Create>";
					// $batch_request_detail = new SoapVar($batch_detail_params, XSD_ANYXML);
					// $soap_result_detail = Globals::callSoapApiCreate($line_url, $batch_request_detail, $sales_office_no);
					// /* Save generated request as file backup */
					// Globals::saveJsonFile($file_name . "_DETAIL-" . ($d_key + 1) . "-" . $key, $detail_param);
					// if (is_string($soap_result_detail)) {
						// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $soap_result_detail . ".", "");
						// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . json_encode($detail_param). ".", "");
					// }
					// else
						// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Line NO CREATED:" .$soap_result_detail->SalesOrderSubfrom->Line_No.  " saved .", "");
				// }
			// }
			
			$read_params = "<ns1:Read>
						<ns1:Document_Type>Order</ns1:Document_Type>
						<ns1:No>" . $data->code . "</ns1:No>
					</ns1:Read>";
			$read_request = new SoapVar($read_params, XSD_ANYXML);
			$soap_result_read = Globals::callSoapApiRead($url, $read_request, $sales_office_no);
			
			if ($soap_result_read && property_exists($soap_result_read, "SalesOrderService")) {
				$data->ms_dynamics_key = $soap_result_read->SalesOrderService->Key;
			}
			
			/* Generate header update request */
			// $batch_params_release =  "<ns1:Update>
				// <ns1:SalesOrderService>

					// <ns1:Key>". $data->ms_dynamics_key ."</ns1:Key>
					// <ns1:Status>Released</ns1:Status>
				// </ns1:SalesOrderService>

			// </ns1:Update>";
			// $batch_release_header = new SoapVar($batch_params_release, XSD_ANYXML);
			// $soap_result = Globals::callSoapApiUpdate($url, $batch_release_header, $sales_office_no);
			// if(isset($soap_result->SalesOrderService->Key)) {
				// $data->ms_dynamics_key = $soap_result->SalesOrderService->Key;
				 // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " Set as Released", "");
			// }
			// elseif(is_string($soap_result))
				 // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] " . $data->code . " " . $data->ms_dynamics_key, "");
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
			$model = SalesOrder::where('id', '=', $json['id'])->first();
			if(!$model)
				return;
			$header_response = $soap_result_read->SalesOrderService;
			/* Assign MSD key to outgoing_notification */
			$model->msd_synced = 1;
			if($model->is_cmos_edited == 1)
				$model->status = 'approved';
			$model->is_cmos_edited = 0;
			$model->edited_by = UploadConnector::MSD_LOGGER_NAME;
			$model->edited_when = date("Y-m-d H:i:s");
			$model->synced_when = date("Y-m-d H:i:s");
			UploadConnector::saveMsDynamicsKey($model, $header_response->Key);
			/* Assign MSD key to outgoing_notification_detail */
			if (property_exists($header_response, "SalesLines") && property_exists($header_response->SalesLines, "Sales_Line_Service") && count($header_response->SalesLines->Sales_Line_Service) > 0) {
				$response_lines = is_array($header_response->SalesLines->Sales_Line_Service) ? $header_response->SalesLines->Sales_Line_Service : [$header_response->SalesLines->Sales_Line_Service];
				foreach ($response_lines as $response_line) {
					if (property_exists($response_line, "Key")) {
						$product_code = isset($response_line->No) ? $response_line->No : "";
						$uom = isset($response_line->Unit_of_Measure_Code) ? $response_line->Unit_of_Measure_Code : "";
						$line_no = isset($response_line->Line_No) ? $response_line->Line_No : "";
						$detail_model_query = SalesOrderDetail::where('sales_order_id', '=', $model->id)->where('product_code', '=', $product_code . '-' . $uom);
						if($response_line->FOC_Item == "true" ){
							$detail_model_query = $detail_model_query->where('is_deal', '=', '1');
						} 
						$detail_model =  $detail_model_query->first();
						
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
    }
	
    /**
     * Takes data from NOC and sends it to client REST API.
     * (30/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCashReceiptJournalQueue($data, $trigger_id = null)
    {
        print_r("[" . date("Y-m-d H:i:s") . "] Syncing Cash Receipt Journal " . "\n");
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $sales_order_code = isset($data['code']) ? $data['code'] : "";
        $invoice_code = isset($data['CT_Slip_Posting_No']) ? $data['CT_Slip_Posting_No'] : "";
		
		$sales_office_model = SalesOffice::where('short_desc', '=', $sales_office_no)->first();
		if($sales_office_model)
			$sales_office_no = $sales_office_model->no;
		
		
        $route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-journal']['list'];
        $company = isset($data['company'])  ? $data['company'] : "BII Live";
		$method = "POST";
        $url = Globals::soapABIMSDynamicsURL($route, $company);


        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", CashReceiptData::MODULE_CASH_RECEIPT) . '-' . $sales_order_code;
        Utils::updateTriggerTotalRows($trigger_id, 1); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */
		
		$success = false;
		$temp_collection = $data['Collection_cash_obj'][0];
		$temp_collection_breakdowns = $temp_collection['Collection_cash_breakdown_obj'];
		foreach ($temp_collection_breakdowns as $key => $temp_collection_breakdown) {
			
			$data_containers = new CashEmptiesAdjustmentsData();
			$data_contents = new CashReceiptData();  
			$data_contents->document_date = date("Y-m-d", strtotime($temp_collection['sales_order_date']));
			$data_contents->posting_date = date("Y-m-d");
			$data_containers->document_date = date("Y-m-d", strtotime($temp_collection['sales_order_date']));
			$data_containers->posting_date = date("Y-m-d");
			$data_containers->document_type = 'Payment';
			$data_contents->document_type = 'Payment';
			$data_containers->account_type = 'Customer';
			$data_contents->account_type = 'Customer';
			
			$data_containers->account_no = $data['location_code'];
			$data_contents->account_no = $data['location_code'];

			if( $temp_collection_breakdown['mode']  == 'Cash') {
				$data_containers->batch_name = $data['cash_batch'];
				$data_contents->batch_name = $data['cash_batch'];
			}
			if ($temp_collection_breakdown['mode'] == 'Check') {
				$data_containers->batch_name = $data['cheque_batch'];
				$data_contents->batch_name = $data['cheque_batch'];
				$data_containers->check_no = $temp_collection['check_no'];
				$data_containers->check_date = date('Y-m-d', strtotime($temp_collection['check_date']));
				$data_contents->check_no = $temp_collection['check_no'];
				$data_contents->check_date = date('Y-m-d', strtotime($temp_collection['check_date']));
			}

			$data_containers->amount = -$temp_collection_breakdown['containers_amount'];
			$data_contents->amount = -$temp_collection_breakdown['contents_amount'];
			$data_containers->ct_slip_no = $data['CT_Slip_Posting_No'];
			$data_contents->applies_to_doc_no = $data['Posting_No'];
			$data_containers->sku_code = $temp_collection_breakdown['sku_code'];
			$data_containers->quantity = $temp_collection_breakdown['quantity'];
			
			#OUTRIGHT TRANSACTIONS
			if($data['transaction_type'] != 1) {
				$data_containers->document_type = 'Invoice';
				$data_contents->document_type = 'Invoice';
				$data_containers->empties_type = 'Deposit';
			}
			
			
			$data_contents->applies_to_doc_type = 'Invoice';
			// $data_contents->quantity = $data['quantity'];
			// $data_containers->quantity = $data['quantity'];

			if($data_containers->amount != 0) {
				$route_container = Params::values()['webservice']['abi_msd']['route']['cash-empties-adjusment']['list'];
				$url_container = Globals::soapABIMSDynamicsURL($route_container, $company);
				$batch_params = "<ns1:Create>" . $data_containers->xmlArrayLineStrings() . "</ns1:Create>";
				$batch_request = new SoapVar($batch_params, XSD_ANYXML);
				$soap_result = Globals::callSoapApiCreate($url_container, $batch_request, $sales_office_no);
				Globals::saveXMLFile($file_name . "-CONTAINER-REQUEST" . ($key + 1), $batch_params);
				if (is_string($soap_result)) {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $sales_order_code . " Container: " . $soap_result, "");
				} elseif ($soap_result && property_exists($soap_result, "CashEmptiesAdjustments")) {
					Globals::saveJsonFile($file_name . "-CONTAINER-RESULT" . ($key + 1), $soap_result);
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $sales_order_code . " Container successfully uploaded.", ""); /* Save log info message */
				} else {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $sales_order_code . " Container: " . json_encode($soap_result), "");
				}
			}

			if($data_contents->amount != 0) {
				$batch_params = "<ns1:Create>" . $data_contents->xmlArrayLineStrings() . "</ns1:Create>";
				$batch_request = new SoapVar($batch_params, XSD_ANYXML);
				$soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
				Globals::saveXMLFile($file_name . "-CONTENTS-REQUEST" . ($key + 1), $batch_params);
				
				if (is_string($soap_result)) {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $sales_order_code . " Contents: " . $soap_result, "");
				} elseif ($soap_result && property_exists($soap_result, "CashReceiptJournals")) {
					Globals::saveJsonFile($file_name . "-CONTENTS-RESULT" . ($key + 1), $soap_result);
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $sales_order_code . " Contents successfully uploaded.", ""); /* Save log info message */
				} else {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $sales_order_code . " Contents: " . $soap_result, "");
				}
			}
		}
		dispatch(
			(new APIJobHandler('cash-reciept-post-queue', json_encode($data)))
				->onQueue('api-queue-' . strtolower($company))
				->delay(\Carbon\Carbon::now()->addSeconds(300)) 
		);
    }

    /**
     * Send a Sales Cash Receipt Journal Post Request
     * (25/08/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCashReceiptJournalPostQueue($data, $trigger_id = null)
    {
        print_r("[" . date("Y-m-d H:i:s") . "] Syncing Cash Receipt Journal Post " . "\n");
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $sales_order_code = isset($data['code']) ? $data['code'] : "";
        $invoice_code = isset($data['CT_Slip_Posting_No']) ? $data['CT_Slip_Posting_No'] : "";
		$sales_office_model = SalesOffice::where('short_desc', '=', $sales_office_no)->first();
		if($sales_office_model)
			$sales_office_no = $sales_office_model->no;
		
		
        $route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-post']['list'];
        $company = isset($data['company'])  ? $data['company'] : "BII Live";
		$method = "POST";
        $url = Globals::soapABIMSDynamicsURL($route, $company, 'CodeUnit');
		

        $client = new SoapClient($url, array(
            'trace' => true,
            'login' => Globals::getSoapOptions($sales_office_no)['username'],
            'password' => Globals::getSoapOptions($sales_office_no)['password'],

        )
        );
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", UploadConnector::MODULE_CASH_RECEIPT_POST);
        
        try {
			$temp_collection = $data['Collection_cash_obj'][0];
			$temp_collection_breakdowns = $temp_collection['Collection_cash_breakdown_obj'];
			$salesman = $data['salesman_code'];

			foreach ($temp_collection_breakdowns as $key => $temp_collection_breakdown) {
				try {
					$soap_result = null;
					if (strpos($temp_collection_breakdown['mode'], 'Cash') !== false) {
						$batch_params = "<ns1:PostCash><ns1:sPC>" . $salesman . "</ns1:sPC></ns1:PostCash>";
						$batch_request = new SoapVar($batch_params, XSD_ANYXML);
						$soap_result = $client->PostCash($batch_request);
						Globals::saveXMLFile($file_name . "-REQUEST-" . $key, $batch_request);
					}
					if (is_string($soap_result)) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[Cash " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_code . ": " . $soap_result, "");
					} else {
						if($soap_result != null) {
							Globals::saveJsonFile($file_name . "-RESULT-" . $key, $soap_result);
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[Cash " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_code . " successfully posted.", ""); /* Save log info message */
						}
					}
					if (strpos($temp_collection_breakdown['mode'], 'Check') !== false) {
						$batch_params = "<ns1:PostCheque><ns1:sPC>" . $salesman . "</ns1:sPC></ns1:PostCheque>";
						$batch_request = new SoapVar($batch_params, XSD_ANYXML);
						$soap_result = $client->PostCheque($batch_request);
						Globals::saveXMLFile($file_name . "-REQUEST-" . $key, $batch_request);
					}
					if (is_string($soap_result)) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[ Check " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_code . " " . $soap_result, "");
					} else {
						Globals::saveJsonFile($file_name . "-RESULT-" . $key, $soap_result);
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[ Check " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_code . " successfully posted.", ""); /* Save log info message */
						
					
					}
				} catch (SoapFault $e) {
					if (is_string($e->getMessage()))
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CASH_RECEIPT_POST . "] " . $invoice_code . " " . $e->getMessage(), "");
				}
			}
                


        } catch (SoapFault $e) {
            if (is_string($e->getMessage()))
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $batch->code . " " . $e->getMessage(), "");
        }
    }
	
    /**
     * Takes data from NOC and sends it to client REST API.
     * (06/07/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesCreditMemoQueue( $json, $trigger_id = null)
    {
        print_r("[" . date("Y-m-d H:i:s") . "] Syncing Credit Memo Queue " . "\n");
        $sales_office_no = isset($json['sales_office_no']) ? $json['sales_office_no'] : "";
        $date_from = (isset($json['date_from']) ? $json['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($json['date_to']) ? $json['date_to'] : date("Y-m-d")) . " 23:59:59";
        $sales_order_code = isset($json['sales_order_code']) ? $json['sales_order_code'] : "";
        $invoice_code = isset($json['CT_Slip_Posting_No']) ? $json['CT_Slip_Posting_No'] : "";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo']['list'];
        $company = isset($data['company'])  ? $data['company'] : "BMI";
		$sales_office_model = SalesOffice::where('short_desc', '=', $sales_office_no)->first();
		if($sales_office_model)
			$sales_office_no = $sales_office_model->no;
		
		
		$method = "POST";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
		$sales_order_returnables = $json['Order_return_obj'];

        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO);
        Utils::updateTriggerTotalRows($trigger_id, count($sales_order_returnables)); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

		$success = false;
		
		/* Assign each model to data for XML */
		$data = new SalesCreditMemoData;
		$data->location_code = $json['location_code'];
		$data->salesman_code = $json['salesman_code'];
		$data->ct_slip = 'true';
		$data->empties_type = 'Deposit';
		$data->reason_code = 'EMPTIES';
		#$data->document_date = date("Y-m-d", strtotime($batch->invoice_date));
		#$data->due_date = date("Y-m-d", strtotime($batch->due_date));
		$data->applies_to_doc_type = 'Invoice';
		$data->applies_to_doc_no = $json['CT_Slip_Posting_No'];
		$key = 1;
		/* Detail */
		if ($sales_order_returnables && count($sales_order_returnables) > 0) {
			foreach ($sales_order_returnables as $returnable) {
				//if($batch_detail->empties_type == "Loan") {
				//	continue;
				//}
				if($returnable['returns'] == 0) {
					continue;
				}
				$data_detail = new SalesCreditMemoLineData;
				$data_detail->no = $returnable['sku_code'];
				$data_detail->type = 'Item';
				$data_detail->quantity = $returnable['returns'];
				$data_detail->unit_price = $returnable['unit_price'];
				$data_detail->ct_slip_no = $json['CT_Slip_Posting_No'];
				$data_detail->empties_type = $returnable['empties_type'];
				$data_detail->zone_code = $json['zone_code'];
				// if($batch_detail)
					// $data_detail->empties_line_no = $batch_detail->line_no;
				$data->salesCreditMemoLineData[] = $data_detail;
			}
			if(empty($data->salesCreditMemoLineData)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $json['code'] . " - No Credit Memo Sent: No return." , "");
				return;
			}

			$batch_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
			$batch_request = new SoapVar($batch_params, XSD_ANYXML);
			$soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
			if (is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $json['code'] . " " . $soap_result, "");
				
			} elseif ($soap_result && property_exists($soap_result, "SalesCreditMemoService")) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " .$json['code'] . " successfully uploaded.", ""); /* Save log info message */
				$json['ref_invoice'] = $soap_result->SalesCreditMemoService->No;
				
				dispatch(
					(new APIJobHandler('credit-memo-post-queue', json_encode($json)))
						->onQueue('api-queue-' . strtolower($company))
						->delay(\Carbon\Carbon::now()->addSeconds(30)) 
				);
			}
			Globals::saveJsonFile($file_name . "-REQUEST", $batch_params);
			Globals::saveJsonFile($file_name . "-RESULT", $soap_result);
		} else {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] Item not found ". json_encode($json), "");
		
		}      
    }
	
	   public static function syncMSDSalesCreditMemoPostQueue($data, $trigger_id = null)
    {
        print_r("[" . date("Y-m-d H:i:s") . "] Syncing Credit Memo Queue Post" . "\n");
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $sales_order_code = isset($data['code']) ? $data['code'] : "";
        $invoice_code = isset($data['CT_Slip_Posting_No']) ? $data['CT_Slip_Posting_No'] : "";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo-post']['list'];
        $company = isset($data['company'])  ? $data['company'] : "BII Live";
		$method = "POST";
        $url = Globals::soapABIMSDynamicsURL($route, $company, 'CodeUnit');

        $client = new SoapClient($url, array(
				'trace' => true,
				'login' => Globals::getSoapOptions($sales_office_no)['username'],
				'password' => Globals::getSoapOptions($sales_office_no)['password'],
		));

		$success = false;
			$file_name = 'CREDIT-MEMO-' .$data['ref_invoice'].'-'.date('YmdHis');
		/* Create new data in MSD */
		$batch_params = "<ns1:SalesCreditMemoPost><ns1:documentNo>" . $data['ref_invoice'] . "</ns1:documentNo></ns1:SalesCreditMemoPost>";
		$batch_request = new SoapVar($batch_params, XSD_ANYXML);

		try {
			$soap_result = $client->SalesCreditMemoPost($batch_request);
			if (is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $data['code'] . " " . $soap_result, "");
			} else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $data['code'] . " successfully posted.", ""); /* Save log info message */
			}
			Globals::saveJsonFile($file_name . "-REQUEST-" , $batch_params);
			Globals::saveJsonFile($file_name . "-RESULT-", $soap_result);
		} catch (SoapFault $e) {
			if (is_string($e->getMessage()))
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $data['code']. " " . $e->getMessage(), "");
		}
    }

	
    /**
     * Takes data from NOC and sends it to client REST API.
     * (30/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesOrderReservationEntry($method, $url, $data, $trigger_id = null)
    {
        $module = (new ReservationEntryData)->getModuleByType(0);
        $module_name = (new ReservationEntryData)->getModuleNameByType(0);
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $salesman_code = isset($data['salesman_code']) ? $data['salesman_code'] : "";
        $location_code = isset($data['location_code']) ? $data['location_code'] : "";
        $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";

        /* Get models to send to MSD */
        $salesman = Salesman::select('id')->where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)->get()->toarray();
        $build = SalesOrder::where('transaction_type', '=', 1)
            ->where('msd_synced', '=', 1)
            ->where('status', '=', 'approved')
            ->whereIn('salesman_id', $salesman)
            ->whereBetween('sales_order_date', [$date_from, $date_to]);
        if ($salesman_code != "")
            $build = $build->with(['salesman'])
                ->whereHas('salesman', function ($q) use ($salesman_code) {
                    $q->where('code', '=', $salesman_code);
                });
        if ($location_code != "")
            $build = $build->with(['location'])
                ->whereHas('location', function ($q) use ($location_code) {
                    $q->where('code', '=', $location_code);
                });
        if ($sales_order_code != "")
            $build = $build->where('code', '=', $sales_order_code);
        $noc_data = count($salesman) > 0 ? $build->get() : [];
        $total_rows = count($noc_data);
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", $module);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            foreach ($noc_data as $key => $batch) {
                $success = false;
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
                        $data->source_type = 37;
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
                            Globals::saveJsonFile($file_name . "-REQUEST-" . ($key + 1), $batch_params);
                            Globals::saveJsonFile($file_name . "-RESULT-" . ($key + 1), json_encode($soap_result));
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
                                Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
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
                        Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                    } else {
                        Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] No new uploaded " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " found.", ""); /* Save log info message */
        }
    }

	/**
     * Takes data from NOC and sends it to client REST API.
     * (15/01/2024)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesOrderReservationEntryQueue($method, $url, $json, $trigger_id = null)
    {
        $module = (new ReservationEntryData)->getModuleByType(0);
        $module_name = (new ReservationEntryData)->getModuleNameByType(0);
        $company = isset($json['company']) ? $json['company'] : "";
        $sales_office_no = isset($json['sales_office_no']) ? $json['sales_office_no'] : "";
        $salesman_code = isset($json['salesman_code']) ? $json['salesman_code'] : "";
        $location_code = isset($json['location_code']) ? $json['location_code'] : "";
        $zone_code = isset($json['zone_code']) ? $json['zone_code'] : "";
        $sales_order_code = isset($json['code']) ? $json['code'] : "";
		$sales_office_model = SalesOffice::where('short_desc', '=', $sales_office_no)->first();
		if($sales_office_model)
			$sales_office_no = $sales_office_model->no;
       
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", $module);
        Utils::updateTriggerTotalRows($trigger_id, 1); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */
		
		$salesman_model = Salesman::where('id', '=', $json['salesman_id'])->first();

		$batch_details = $json['Order_detail_obj'];
		if ($batch_details && count($batch_details) > 0) {
			foreach ($batch_details as $key => $batch_detail) {
				$data = new ReservationEntryData; 
                $sku_model = Sku::where('code', '=', $batch_detail['product_code'] . "-" . $batch_detail['Uom'])->where('sales_office_no', '=', $sales_office_no)->first();
						 
				if ($sku_model)
					$data->sku_code = $sku_model->sys_21;
					
				$data->zone_code = $salesman_model->zone;
				$data->line_no = isset($batch_detail['line_no']) ? $batch_detail['line_no'] : "";
				$data->lot_no =  isset($batch_detail['lot_no']) ? $batch_detail['lot_no'] : "";
				$data->entry_no =  isset($batch_detail['entry_no']) ? $batch_detail['entry_no'] : "";
				$data->reservation_status = "Surplus";
				$data->quantity = -$batch_detail['quantity'];
				$data->source_id = $json['code'];
				$data->shipment_date = date('Y-m-d', strtotime($json['sales_order_date']));
				$data->source_type = 37;
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
					}
					Globals::saveJsonFile($file_name . "-REQUEST-" . ($key + 1), $batch_params);
					Globals::saveJsonFile($file_name . "-RESULT-" . ($key + 1), json_encode($soap_result));
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
						Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
						if (is_string($soap_result))
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->source_id . " " . $soap_result, "");
						elseif ($soap_result && property_exists($soap_result, "ReservationEntriesService")) {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->source_id . " successfully re-uploaded.", ""); /* Save log info message */
						}
					}
				}
			}
		}
           
    }
	
	/**
     * Takes data from NOC and sends it to client REST API.
     * (05/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesOrderPostQueue($method, $url, $json, $trigger_id = null)
    {
        $sales_office_no = isset($json['sales_office_no']) ? $json['sales_office_no'] : "";
        $salesman_code = isset($json['salesman_code']) ? $json['salesman_code'] : "";
        $location_code = isset($json['location_code']) ? $json['location_code'] : "";
        $sales_order_code = isset($json['sales_order_code']) ? $json['sales_order_code'] : "";
        $date_from = (isset($json['date_from']) ? $json['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($json['date_to']) ? $json['date_to'] : date("Y-m-d")) . " 23:59:59";
		
   
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesOrderData::MODULE_SALES_ORDER_POST);
        Utils::updateTriggerTotalRows($trigger_id, 1); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        $client = new SoapClient($url, array(
            'trace' => true,
            'login' => Globals::getSoapOptions($sales_office_no)['username'],
            'password' => Globals::getSoapOptions($sales_office_no)['password'],
			)
        );
		
		/* Create new data in MSD */
		$batch_params = "<ns1:SalesOrderPost><ns1:documentNo>" . $json['code'] . "</ns1:documentNo></ns1:SalesOrderPost>";
		$batch_request = new SoapVar($batch_params, XSD_ANYXML);

		try {
			$soap_result = $client->SalesOrderPost($batch_request);
			if (is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . "] " . $json['code'] . " " . $soap_result, "");
			} else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . "] " .  $json['code']. " successfully posted.", ""); /* Save log info message */
				
			}
			Globals::saveJsonFile($file_name . "-" .  $json['code'], $soap_result);
		} catch (SoapFault $e) {
			if (is_string($e->getMessage()))
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . "] " . $json['code']. " " . $e->getMessage(), "");
		}   
	}	
	
	/**
     * Takes data from NOC and sends it to client REST API.
     * (02/02/2024)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesOrderAssignLotsAndPost($method, $url, $json, $trigger_id = null)
    {
        $sales_office_no = isset($json['sales_office_no']) ? $json['sales_office_no'] : "";
        $salesman_code = isset($json['salesman_code']) ? $json['salesman_code'] : "";
        $location_code = isset($json['location_code']) ? $json['location_code'] : "";
        $sales_order_code = isset($json['sales_order_code']) ? $json['sales_order_code'] : "";
        $date_from = (isset($json['date_from']) ? $json['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($json['date_to']) ? $json['date_to'] : date("Y-m-d")) . " 23:59:59";
        $company = (isset($json['company'])) ? $json['company'] : 'BII';
		
   
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesOrderData::MODULE_SALES_ORDER_POST);
        Utils::updateTriggerTotalRows($trigger_id, 1); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        $client = new SoapClient($url, array(
            'trace' => true,
            'login' => Globals::getSoapOptions($sales_office_no)['username'],
            'password' => Globals::getSoapOptions($sales_office_no)['password'],
			)
        );
		
   
        $so_route = Params::values()['webservice']['abi_msd']['route']['sales-order']['list'];
        $so_url = Globals::soapABIMSDynamicsURL($so_route, $company);
		
		$data = SalesOrder::where('code', '=', $sales_order_code)->first();
		if(!$data) {
			print_r("DATA NOT FOUND \n");
			return;
		}
		$batch_params_read = "<ns1:Read>
							 <ns1:Document_Type>Order</ns1:Document_Type>
							 <ns1:No>". $data->code ."</ns1:No>
						  </ns1:Read>";
					
		$batch_read_header = new SoapVar($batch_params_read, XSD_ANYXML);
		$soap_read = Globals::callSoapApiRead($so_url, $batch_read_header, $sales_office_no);
		
		if(isset($soap_read->SalesOrderService->Key)) {
			$data->ms_dynamics_key = $soap_read->SalesOrderService->Key;
			 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . " and lots] " . $data->code . " Updated Key after Lots and Post", "");
		}
		elseif(is_string($soap_read))
			 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . " and lots] " . $data->code . " " . $soap_read, "");
		else
			 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . " and lots] " . $data->code . " " . json_encode($soap_read, JSON_PRETTY_PRINT), "");
		
		
		/* Generate header update request */
		$batch_params_open = "<ns1:Update>
			<ns1:SalesOrderService>
				<ns1:Key>". $data->ms_dynamics_key ."</ns1:Key>
				<ns1:Status>Open</ns1:Status>
			</ns1:SalesOrderService>

		</ns1:Update>";
		$batch_open_header = new SoapVar($batch_params_open, XSD_ANYXML);
		$soap_open = Globals::callSoapApiUpdate($so_url, $batch_open_header, $sales_office_no);
		if(isset($soap_open->SalesOrderService->Key)) {
			$data->ms_dynamics_key = $soap_open->SalesOrderService->Key;
			 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . " and lots] " . $data->code . " Set as Open", "");
		}
		elseif(is_string($soap_open))
			 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . " and lots] " . $data->code . " " . $soap_open, "");
		else
			 Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . " and lots] " . $data->code . " " . json_encode($soap_open, JSON_PRETTY_PRINT), "");
	 
		/* Create new data in MSD */
		$batch_params = "<ns1:SalesOrderAssignLotsAndPost><ns1:documentNo>" . $sales_order_code . "</ns1:documentNo></ns1:SalesOrderAssignLotsAndPost>";
		$batch_request = new SoapVar($batch_params, XSD_ANYXML);

		try {
			$soap_result = $client->SalesOrderAssignLotsAndPost($batch_request);
			if (is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . " and lots] " . $sales_order_code . " " . $soap_result, "");
			} else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . " and lots] " .  $sales_order_code . " successfully assigned lots and posted.", ""); /* Save log info message */
			}
			Globals::saveJsonFile($file_name . "- LOT", $soap_result);
		} catch (SoapFault $e) {
			if (is_string($e->getMessage()))
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . " and lots] " .$sales_order_code. " " . $e->getMessage(), "");
		}   
		
		$data->save();
		
	}	
	
    /**
     * Takes data from NOC and sends it to client REST API.
     * (05/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesOrderPost($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $salesman_code = isset($data['salesman_code']) ? $data['salesman_code'] : "";
        $location_code = isset($data['location_code']) ? $data['location_code'] : "";
        $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        /* Get models to send to MSD */
        $salesman = Salesman::select('id')->where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)->get()->toarray();
        $build = SalesOrder::where('transaction_type', '=', 1)
            ->where('msd_synced', '=', 1)
            ->where('status', '=', 'reserved')
            ->whereIn('salesman_id', $salesman)
            ->whereBetween('sales_order_date', [$date_from, $date_to]);
        if ($salesman_code != "")
            $build = $build->with(['salesman'])
                ->whereHas('salesman', function ($q) use ($salesman_code) {
                    $q->where('code', '=', $salesman_code);
                });
        if ($location_code != "")
            $build = $build->with(['location'])
                ->whereHas('location', function ($q) use ($location_code) {
                    $q->where('code', '=', $location_code);
                });
        if ($sales_order_code != "")
            $build = SalesOrder::where('code', '=', $sales_order_code);
        $noc_data = $build->get();
        $total_rows = count($noc_data);
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesOrderData::MODULE_SALES_ORDER_POST);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        $client = new SoapClient($url, array(
            'trace' => true,
            'login' => Globals::getSoapOptions($sales_office_no)['username'],
            'password' => Globals::getSoapOptions($sales_office_no)['password'],

        )
        );

        if ($total_rows > 0) {
            foreach ($noc_data as $key => $batch) {
                $success = false;
                /* Create new data in MSD */
                $batch_params = "<ns1:SalesOrderPost><ns1:documentNo>" . $batch->code . "</ns1:documentNo></ns1:SalesOrderPost>";
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);

                try {
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
                    Globals::saveJsonFile($file_name . "-" . $key, $batch);
                } catch (SoapFault $e) {
					$batch->status = "failed_post";
					$batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
					$batch->edited_when = date("Y-m-d H:i:s");
					if ($batch->save())
						$success = true;
                    if (is_string($e->getMessage()))
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . "] " . $batch->code . " " . $e->getMessage(), "");
                }

                if ($success) {
                    Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                }
                // $detail_arr = $batch->salesOrderDetail()->get();
                // if(count($detail_arr) > 0 ){
                //     foreach($detail_arr as $detail_batch) {      
                //         $sales_office_mod = SalesOffice::where('no', '=', $batch->sales_office_no)->first();                  
                //     $new_data = new InventoryAdditionData();
                //     $new_data->company_code = Globals::getWmsCompanyCode();
                //     $new_data->sku_code = $data_detailed_sending->product_code;
                //     $new_data->sales_office_no = $detail_batch->sales_office_code;
                //     $new_data->short_desc = $sales_office->short_desc;
                //     $new_data->salesman_code = $data->salesman_code;
                //     $new_data->qty = $data_detailed_sending->request_quantity;
                //     $new_data->zone_code = $data->transfer_to;
                //     $new_data->expiration_date = $data_detailed_sending->expiration_date;
                //     $new_data->reference_no = $data->reference_no;

                //     $batch_params = '<GetBatchLocationCriteria xsi:type="urn:GetLocationCriteriaArray" soap-enc:arrayType="urn:GetLocationCriteria[]">';
                //     $batch_params .= $new_data->xmlArrayLineStrings();
                //     $batch_params .= '</GetBatchLocationCriteria>';
                //     $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                //     $soap_result = (array) $soap_client->addToLocation($batch_request);
                //     }
                // }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . "] No new uploaded " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " found.", ""); /* Save log info message */
        }
    }

    public static function syncMSDOutrightSalesOrder($method, $url, $data, $trigger_id = null) {
        
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $salesman_code = isset($data['salesman_code']) ? $data['salesman_code'] : "";
        $location_code = isset($data['location_code']) ? $data['location_code'] : "";
        $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";

        /* Get models to send to MSD */
        $salesman = Salesman::select('id')->where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)->get()->toarray();
        $build = SalesOrder::whereIn('transaction_type', [0, 1]) // 0 = Booking, 1 = Conventional
            ->whereIn('type', [0, 1]) // 0 = Cash, 1 = Credit
            ->where('msd_synced', '=', 0)
            ->whereIn('salesman_id', $salesman)
            ->where('sales_order_date', '>=', $date_from)
            ->where('sales_order_date', '<=', $date_to);
        if ($salesman_code != "")
            $build = $build->with(['salesman'])
                ->whereHas('salesman', function ($q) use ($salesman_code) {
                    $q->where('code', '=', $salesman_code);
                });
        if ($location_code != "")
            $build = $build->with(['location'])
                ->whereHas('location', function ($q) use ($location_code) {
                    $q->where('code', '=', $location_code);
                });
        if ($sales_order_code != "")
            $build = $build->where('code', '=', $sales_order_code);
        $noc_data = count($salesman) > 0 ? $build->get() : [];
        $total_rows = count($noc_data);

        if ($total_rows > 0) {

            foreach ($noc_data as $key => $batch) {
                dispatch((new APIJobSalesOrderHandler($batch->code, $sales_office_no, $company, $trigger_id))->onQueue('api-queue-' . strtolower($company)));
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER . "] No new uploaded " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " found.", ""); /* Save log info message */
        }
    }

    public static function syncMSDTransferOrderQueue($method, $url, $data, $trigger_id = null) {
        
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $inventory_return = isset($data['inventory_return_code']) ? $data['inventory_return_code'] : "";
		$data_returned = $data;
		
		$wms_sales_office = WMSSalesOffice::where('sales_office_code', '=', $sales_office_no)->where('company_id', '=', Params::values()['abi_wms_company_id'])->first();
		$wms_sales_office_zone_code = $wms_sales_office->zone()->first();
		$wms_sales_office_zone_code = $wms_sales_office_zone_code != null ? $wms_sales_office_zone_code["zone_code"] : "";
			
		if($data_returned) {
			$header = new TransferOrderData;
			
			$data_to = $data_returned;
			$header->inventory_return_code = $data_to['inventory_return_code'];
			$header->inventory_return_date = $data_to['inventory_return_date'];
			$header->so_zone = $wms_sales_office_zone_code;
			$header->order_type = 'Product_Return_from_Salesman_location';
			$header->employee_id = $data_to['employee_id'];
			$header->so_code = isset($data_to['short_desc']) ? $data_to['short_desc'] : "";
			
			$details = [];
            foreach ($data_to['detail'] as $detail) {
				$detail_model = new TransferOrderDetailData;
				$detail_model->sku_code = $detail['sku_code'];
				$detail_model->quantity = $detail['quantity'];
				$detail_model->shipment_date = $data_to['inventory_return_date'];
				$detail_model->so_code = isset($data_to['short_desc']) ? $data_to['short_desc'] : "";
				$details[] = $detail_model;
			}
			
			$header->transferOrderDetail = $details;
			
			$batch_params = "<ns1:Create>" . $header->xmlMSDUpdateArrayString() . "</ns1:Create>";
			$batch_request = new SoapVar($batch_params, XSD_ANYXML);
			$soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
             Globals::saveJsonFile("TransferOrder-" . $header->inventory_return_code , $batch_params);
			
			if (is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . TransferOrderData::MODULE_NAME . "] " . $header->inventory_return_code . "error :  " . $soap_result, "");
			} elseif ($soap_result && property_exists($soap_result, "TransferOrderService")) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . TransferOrderData::MODULE_NAME . "] " . $header->inventory_return_code . " successfully uploaded.", ""); /* Save log info message */
			} else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . TransferOrderData::MODULE_NAME . "] " . $header->inventory_return_code . " error: " . $soap_result, "");
			}
		}
    }
	
	
    /**
     * Takes data from NOC and sends it to client REST API.
     * (30/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCashReceiptJournal($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = date("Y-m-d", strtotime("-3 days")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : "";
        $invoice_code = isset($data['invoice_code']) ? $data['invoice_code'] : "";

        /* Get models to send to MSD */
        $build = Invoice::where('msd_synced', '=', 1)
            ->whereBetween('invoice_date', [$date_from, $date_to])
            ->where('status', '=', 'new');
        if ($sales_office_no != "")
            $build = $build->where('sales_office_no', '=', $sales_office_no);
        if ($sales_order_code != "")
            $build = $build->where('sales_order_code', '=', $sales_order_code);
        if ($invoice_code != "")
            $build = $build->where('code', '=', $invoice_code);
        $noc_data = $build->get();
        $total_rows = count($noc_data);


        $build2 = SalesOrder::select('temp_sales_order.*')
            ->where('temp_sales_order.type', '=', 5)
            ->where('temp_sales_order.transaction_type', '=', 2)
            ->where('temp_sales_order.msd_synced', '=', 0)
            ->where('temp_sales_order.status', '!=', 'cash_receipt')
            ->where('temp_sales_order.status', '!=', 'failed_post')
            ->where('temp_sales_order.status', '!=', 'posted')
            ->whereBetween('temp_sales_order.sales_order_date', [$date_from, $date_to]);
        if ($sales_office_no != "") {
            $build2 = $build2->join('salesman', 'salesman.id', '=', 'temp_sales_order.salesman_id');
            $build2 = $build2->join('sales_office', 'sales_office.no', '=', 'salesman.sales_office_no');
            $build2 = $build2->where('sales_office.no', '=', $sales_office_no);
        }
        if ($sales_order_code != "")
            $build2 = $build2->where('temp_sales_order.code', '=', $sales_order_code);
        $noc_data_collection = $build2->get();
        $total_rows_collection = count($noc_data_collection);

        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", CashReceiptData::MODULE_CASH_RECEIPT);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows + $total_rows_collection); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            foreach ($noc_data as $key => $batch) {
                $success = false;
                $data = new CashReceiptData();
                $sales_order = $batch->salesOrder()->first();
                $temp_collection = $sales_order->tempCollectionCash()->first();
                $send_invoice_code = $batch->code;
                if ($temp_collection) {
                    $temp_collection_breakdowns = $temp_collection->tempCollectionCashBreakdown()->get();
                }
                if (!isset($temp_collection) || count($temp_collection_breakdowns) == 0) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] No found collection/breakdown found for Invoice #" . $batch->code, ""); /* Save log info message */
                    $batch->status = "cash_receipt";
                    if ($batch->save())
                        $success = true;
                    continue;
                }
                $salesman = $sales_order->salesman()->first();
                $location = $sales_order->location()->first();
                foreach ($temp_collection_breakdowns as $key => $temp_collection_breakdown) {
                    if ($sales_order) {
                        $data_containers = new CashEmptiesAdjustmentsData();
                        $data_contents = new CashReceiptData();  
						$data_contents->document_date = date("Y-m-d", strtotime($sales_order->sales_order_date));
						$data_contents->posting_date = date("Y-m-d");
						$data_containers->document_date = date("Y-m-d", strtotime($sales_order->sales_order_date));
						$data_containers->posting_date = date("Y-m-d");
						$data_containers->document_type = 'Payment';
						$data_containers->account_type = 'Customer';
						$data_contents->document_type = 'Payment';
						$data_contents->account_type = 'Customer';
                    }
                    else {
                        continue;
                    }

                    if ($location) {
                        $data_containers->account_no = $location->code;
                        $data_contents->account_no = $location->code;
                    }
                    else {
                        continue;
                    }

                    if ($salesman) {
                        if( $temp_collection_breakdown->mode == 'Cash') {
                            $data_containers->batch_name = $salesman->cash_batch;
                            $data_contents->batch_name = $salesman->cash_batch;
                        }
                        if ($salesman && $temp_collection_breakdown->mode == 'Check') {
                            $data_containers->batch_name = $salesman->cheque_batch;
                            $data_contents->batch_name = $salesman->cheque_batch;
                        }
                    }

                    $data_containers->amount = $temp_collection_breakdown->containers_amount;
                    $data_contents->amount = $temp_collection_breakdown->contents_amount;
                        
                    #$data_containers->applies_to_doc_no = $sales_order->code;
                    $data_contents->applies_to_doc_no = $temp_collection_breakdown->invoice_no;
					$data_containers->sku_code = $temp_collection->product_code;
					$data_containers->empties_type = 'Deposit';

                    if($data_containers->amount > 0) {
						$route_container = Params::values()['webservice']['abi_msd']['route']['cash-empties-adjusment']['list'];
						$url_container = Globals::soapABIMSDynamicsURL($route_container, $data['company']);
                        $batch_params = "<ns1:Create>" . $data_containers->xmlArrayLineStrings() . "</ns1:Create>";
                        $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                        $soap_result = Globals::callSoapApiCreate($url_container, $batch_request, $sales_office_no);
                        if (is_string($soap_result)) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $batch->code . " Container: " . $soap_result, "");
                        } elseif ($soap_result && property_exists($soap_result, "CashReceiptJournals")) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $batch->code . " Container successfully uploaded.", ""); /* Save log info message */
                        } else {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $batch->code . " Container: " . $soap_result, "");
                        }

                        Globals::saveJsonFile($file_name . "-CONTAINER-" . ($key + 1), $data_containers);
                    
                    }

                    if($data_contents->amount > 0) {
                        $batch_params = "<ns1:Create>" . $data_contents->xmlArrayLineStrings() . "</ns1:Create>";
                        $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                        $soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
                        
                        if (is_string($soap_result)) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $batch->code . " Contents: " . $soap_result, "");
                        } elseif ($soap_result && property_exists($soap_result, "CashReceiptJournals")) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $batch->code . " Contents successfully uploaded.", ""); /* Save log info message */
                        } else {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $batch->code . " Contents: " . $soap_result, "");
                        }

                        Globals::saveJsonFile($file_name . "-CONTENTS-" . ($key + 1), $data_contents);
                    
                    }

                }

                $sales_order_returnables = $sales_order->salesOrderReturnable()->first();
                if (empty($sales_order_returnables)) {
                    $batch->status = "credit_memo_posted";

                } else {
                    if (($batch->ct_slip == 1 && $sales_order->type == 0) || $sales_order->type == 1) {
                        $batch->status = "cash_receipt";
                    } else {
                        $batch->status = "credit_memo_posted";
                    }
                }

                if ($batch->save())
                    $success = true;

                if ($success) {
                    Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                }

            }

        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] No new downloaded " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " found.", ""); /* Save log info message */
        }

        if ($total_rows_collection > 0) {
            foreach ($noc_data_collection as $key => $batch) {
                $success = false;
                $temp_collection = $batch->tempCollectionCash()->first();
                if ($temp_collection) {
                    $temp_collection_breakdown = $temp_collection->tempCollectionCashBreakdown()->get();
                }
                if (!isset($temp_collection) || !isset($temp_collection_breakdown)) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] No found collection/breakdown found for Invoice #" . $batch->code, ""); /* Save log info message */
                    $batch->status = "cash_receipt";
                    $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
                    $batch->edited_when = date("Y-m-d H:i:s");
                    if ($batch->save())
                        $success = true;
                    continue;
                }
				foreach($temp_collection_breakdown as $key2 => $breakdown) {
					$data = new CashReceiptData();
					if ($breakdown->invoice_no != "") {
						$salesman = $batch->salesman()->first();
						$location = $batch->location()->first();
						$ct_slip = substr($breakdown->invoice_no, 3, 3) == "CTS";
						$total_amount = 0;
						if ($ct_slip === true) {
							$total_amount = $breakdown->containers_amount;
							if ($breakdown->containers_amount == 0) {
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] Mode Cash breakdown is with containers amount 0 in " . $batch->code, ""); /* Save log info message */
								continue;
							}
						} else {
							$total_amount = $breakdown->contents_amount;
						}
					} else {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] No found collection/breakdown found for Invoice #" . $batch->code, ""); /* Save log info message */
						$batch->status = "cash_receipt";
						if ($batch->save())
							$success = true;
						continue;
					}

					if (isset($location))
						$data->account_no = $location->code;
					if ($salesman && $temp_collection && $temp_collection->mode == 'Cash')
						$data->batch_name = $salesman->cash_batch;
					if ($salesman && $temp_collection && $temp_collection->mode == 'Check')
						$data->batch_name = $salesman->cheque_batch;

					$data->document_date = date("Y-m-d", strtotime($batch->sales_order_date));
					$data->posting_date = date("Y-m-d");
					$data->document_type = 'Payment';
					$data->account_type = 'Customer';
					$data->amount = -$total_amount;
					$data->applies_to_doc_type = 'Invoice';
					$data->applies_to_doc_no = $breakdown->invoice_no;
					
					$batch_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
					$batch_request = new SoapVar($batch_params, XSD_ANYXML);
					
					$soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . json_encode($soap_result), "");
						
					if (is_string($soap_result)) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $batch->code . " " . $soap_result, ""); $batch->status = "cash_receipt";
						 $batch->status = "failed";
						   if ($batch->save())
								$success = true;
					} elseif ($soap_result && property_exists($soap_result, "CashReceiptJournals")) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $batch->code . " successfully uploaded.", ""); /* Save log info message */
						$batch->status = "cash_receipt";
						$batch->msd_synced = 1;
						$batch->ms_dynamics_key = $soap_result->CashReceiptJournals->Key;
						if ($batch->save())
							$success = true;
					}
					Globals::saveJsonFile($file_name . "-" . ($key + 1) . "-" . $key2, $data);
				}
                if ($success) {
                    Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] No new downloaded " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " found.", ""); /* Save log info message */
        }
    }
	
	
    /**
     * Takes data from NOC and sends it to client REST API.
     * (30/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCashReceiptCollectionQueue($method, $url, $data, $sales_office_no, $trigger_id = null)
    {
		$noc_data = $data['Collection_cash_obj'];
		$post_route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-post']['list'];
        $post_url = Globals::soapABIMSDynamicsURL($post_route, $data['company'], "Codeunit");
        $client = new SoapClient($post_url, array(
				'trace' => true,
				'login' => Globals::getSoapOptions($sales_office_no)['username'],
				'password' => Globals::getSoapOptions($sales_office_no)['password'],
			)
        );
		
		foreach ($noc_data as $key => $temp_collection) {
			
			$success = false;
			if ($temp_collection) {
				$temp_collection_breakdown = $temp_collection['Collection_cash_breakdown_obj'];
			}
			if (empty($temp_collection_breakdown)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] No found collection/breakdown found for Invoice #" . $batch->code, ""); /* Save log info message */
				continue;
			}
			foreach($temp_collection_breakdown as $key2 => $breakdown) {
				$data_containers = new CashEmptiesAdjustmentsData();
			
				$data_containers->document_date = date("Y-m-d", strtotime($data['sales_order_date']));
				$data_containers->posting_date = date("Y-m-d");
				$data_containers->document_type = 'Payment';
				$data_containers->account_type = 'Customer';
				
				$data_contents = new CashReceiptData();  
				
				$data_contents->document_date = date("Y-m-d", strtotime($data['sales_order_date']));
				$data_contents->posting_date = date("Y-m-d");
				$data_contents->document_type = 'Payment';
				$data_contents->account_type = 'Customer';


				$data_contents->account_no = $data['location_code'];
				$data_containers->account_no = $data['location_code'];
				
				if ($temp_collection && $temp_collection['mode'] == 'Cash' && isset($data['cash_batch'])) {
					$data_containers->batch_name = $data['cash_batch'];
					$data_contents->batch_name   = $data['cash_batch'];
				}
				if ($temp_collection && $temp_collection['mode'] == 'Check' && isset($data['cheque_batch'])){
					$data_containers->batch_name = $data['cheque_batch'];
					$data_contents->batch_name   = $data['cheque_batch'];
				}

				$data_containers->amount = -$breakdown['containers_amount'];
				$data_contents->amount = -$breakdown['contents_amount'];
					
				$data_containers->applies_to_doc_type = 'Invoice';
				$data_contents->applies_to_doc_type = 'Invoice';
				
				// $data_containers->applies_to_doc_no = $breakdown['invoice_no'];
				$data_contents->applies_to_doc_no = $breakdown['invoice_no'];
				
				$data_containers->ct_slip_no = $breakdown['invoice_no'];
				$data_containers->zone_code = $data['salesman_code'];
				$data_containers->posting_group = 'Deposit';
				
				$data_containers->empties_type = 'Deposit';
				$data_containers->sku_code = $breakdown['sku_code'];

				if($data_containers->amount != 0) {
					$route_container = Params::values()['webservice']['abi_msd']['route']['cash-empties-adjusment']['list'];
					$url_container = Globals::soapABIMSDynamicsURL($route_container, $data['company']);
					$batch_params = "<ns1:Create>" . $data_containers->xmlArrayLineStrings() . "</ns1:Create>";
					$batch_request = new SoapVar($batch_params, XSD_ANYXML);
					$soap_result = Globals::callSoapApiCreate($url_container, $batch_request, $sales_office_no);
					if (is_string($soap_result)) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $data['code'] . " Container: " . json_encode($soap_result), "");
					} elseif ($soap_result && property_exists($soap_result, "CashEmptiesAdjustments")) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " .$data['code'] . " Container successfully uploaded.", ""); /* Save log info message */
					} else {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $data['code'] . " Container: " . json_encode($soap_result), "");
					}

					Globals::saveJsonFile("CASH-RECEIPT-COLLECTION-CONTAINER-" . date('Y-m-d') . ($key + 1), $batch_params);
				
				}

				if($data_contents->amount != 0) {
					$batch_params = "<ns1:Create>" . $data_contents->xmlArrayLineStrings() . "</ns1:Create>";
					$batch_request = new SoapVar($batch_params, XSD_ANYXML);
					$soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
					
					if (is_string($soap_result)) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $data['code'] . " Contents: " . $soap_result, "");
					} elseif ($soap_result && property_exists($soap_result, "CashReceiptJournals")) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " .$data['code'] . " Contents successfully uploaded.", ""); /* Save log info message */
					} else {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $data['code'] . " Contents: " . $soap_result, "");
					}

					Globals::saveJsonFile("CASH-RECEIPT-COLLECTION-CONTENTS-" . date('Y-m-d') . ($key + 1), $batch_params);
				}
				
				
				
				try{	
					if (strpos($temp_collection['mode'], 'Cash') !== false) {
						$batch_params = "<ns1:PostCash><ns1:sPC>" . $data['salesman_code'] . "</ns1:sPC></ns1:PostCash>";
						$batch_request = new SoapVar($batch_params, XSD_ANYXML);
						$soap_result = $client->PostCash($batch_request);
						if (is_string($soap_result)) {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[Cash Collection " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $data['code']  . " " . $soap_result, "");
						}
					}
					if (strpos( $temp_collection['mode'], 'Check') !== false) {
						$batch_params = "<ns1:PostCheque><ns1:sPC>" .  $data['salesman_code'] . "</ns1:sPC></ns1:PostCheque>";
						$batch_request = new SoapVar($batch_params, XSD_ANYXML);
						$soap_result = $client->PostCheque($batch_request);
						if (is_string($soap_result)) {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[Cash Collection " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $data['code']  . " " . $soap_result, "");
						}
					} 
							   
				} catch (SoapFault $e) {
					if (is_string($e->getMessage()))
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[ Cash Collection " . UploadConnector::MODULE_CASH_RECEIPT_POST . "] " .$data['code']  . " " . $e->getMessage(), "");
				}
				
				if (isset($soap_result) && is_string($soap_result)) {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[ Cash Collection " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $data['code']  . " " . $soap_result, "");
				} else {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[ Cash Collection" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $data['code']  . " successfully posted.", ""); /* Save log info message */
				}
				Globals::saveJsonFile("CASH-RECEIPT-COLLECTION-POST-" . date('Y-m-d') . ($key + 1), $batch_request);   
			}
			if ($success) {
				Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
			} else {
				Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
			}
		}
		
	}
	
    /**
     * Takes data from NOC and sends it to client REST API.
     * (30/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCashReceiptFromPicknote($temp_collection, $sales_order_id, $employee_code,  $sales_office_no, $trigger_id, $company)
    {
        $route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-journal']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
		$company = isset($data['company']) ? $data['company'] : "BII";   
        $post_route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-post']['list'];
        $post_url = Globals::soapABIMSDynamicsURL($post_route, $company, "Codeunit");
		$sales_order = SalesOrder::where('id', '=', $sales_order_id)->first();
		if($sales_order) {			
			$salesman = $sales_order->salesman()->first();
			if($salesman) {
				$sales_office_no = $salesman->sales_office_no;
			}			
		}

        $client = new SoapClient($post_url, array(
				'trace' => true,
				'login' => Globals::getSoapOptions($sales_office_no)['username'],
				'password' => Globals::getSoapOptions($sales_office_no)['password'],
			)
        );
		
		$location_so = "";
		$final_sales_office = "";
		
		if ($sales_order) {
			$data_containers = new CashEmptiesAdjustmentsData();
			
			$data_containers->document_date = date("Y-m-d", strtotime($sales_order->sales_order_date));
			$data_containers->posting_date = date("Y-m-d");
			$data_containers->document_type = 'Payment';
			$data_containers->account_type = 'Customer';
			
			$data_contents = new CashReceiptData();  
			
			$data_contents->document_date = date("Y-m-d", strtotime($sales_order->sales_order_date));
			$data_contents->posting_date = date("Y-m-d");
			$data_contents->document_type = 'Payment';
			$data_contents->account_type = 'Customer';
		}
		else {
			Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Sales Order not found.", ""); /* Save log info message */
			return;
		}
			
		$salesman = $sales_order->salesman()->first();
		$location = $sales_order->location()->first();
		
		
		if (!$salesman){
			Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Salesman not found.", ""); /* Save log info message */
			return;
		}

		if ($location) {
			$data_containers->account_no = $location->code;
			$data_contents->account_no = $location->code;
			$location_so = $location->sales_office_no;
		}
		else {
			Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Location not found.", ""); /* Save log info message */
			return;
		}

		if($location_so != "")
			$final_sales_office = $location_so;
		else
			$final_sales_office = $sales_office_no;
			
		if(count($temp_collection) <= 0) {
			Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " No temp collection found.", ""); /* Save log info message */
			
		}
		
		foreach ($temp_collection as $key => $temp_collection_breakdown) {
			$file_name = UploadConnector::PATH . $temp_collection_breakdown->id . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", CashReceiptData::MODULE_CASH_RECEIPT);
			
			$temp_collection_h = TempCollectionCash::where('id', '=', $temp_collection_breakdown->temp_collection_id)->first();
			

			if( $temp_collection_breakdown->mode == 'Cash') {
				$data_containers->batch_name = $salesman->cash_batch;
				$data_contents->batch_name = $salesman->cash_batch;
			}
			if ($salesman && $temp_collection_breakdown->mode == 'Check') {
				$data_containers->batch_name = $salesman->cheque_batch;
				$data_contents->batch_name = $salesman->cheque_batch;
				
				$data_containers->check_bank = $temp_collection_h->check_bank;
				$data_containers->check_no =  $temp_collection_h->check_no;
				$data_containers->check_date = date("Y-m-d", strtotime($temp_collection_h->check_date));
				
				
				$data_contents->check_bank = $temp_collection_h->check_bank;
				$data_contents->check_no =  $temp_collection_h->check_no;
				$data_contents->check_date = date("Y-m-d", strtotime($temp_collection_h->check_date));
			}

			$data_containers->empties_type = "Deposit";
			$data_containers->amount = -$temp_collection_breakdown->containers_amount;
			$data_containers->posting_group = 'Deposit';
			$data_contents->amount = -$temp_collection_breakdown->contents_amount;
			$data_containers->zone_code = $employee_code;
			$data_contents->zone_code = $employee_code;
			
			$containers_invoice = Invoice::where('sales_order_code', '=', $sales_order->code)->where('sales_office_no', '=', $sales_office_no)->where('code', 'LIKE', '%CTS%')->first();
			$contents_invoice = Invoice::where('sales_order_code', '=', $sales_order->code)->where('sales_office_no', '=', $sales_office_no)->where('code', 'LIKE', '%SINV%')->first();
			$containers_app_docno = "";
			$contents_app_docno = "";
			
			if($containers_invoice == null) {	
			Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Container invoice not found.", ""); /* Save log info message */
				//continue;
			}
			else
			$containers_app_docno = $containers_invoice->code;
			
			if($contents_invoice == null) {	
			Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Contents invoice not found.", ""); /* Save log info message */
				//continue;
			}
			else
			$contents_app_docno = $contents_invoice->code;
			
			$data_containers->ct_slip_no = $containers_app_docno;
			#$data_containers->applies_to_doc_no = $containers_app_docno;
			$data_contents->applies_to_doc_no = $contents_app_docno;
			$data_containers->applies_to_doc_type = 'Invoice';
			$data_contents->applies_to_doc_type = 'Invoice';
				
			if($data_containers->amount != 0) {
					$data_containers->sku_code = $temp_collection_h->product_code;

					$route_container = Params::values()['webservice']['abi_msd']['route']['cash-empties-adjusment']['list'];
					$url_container = Globals::soapABIMSDynamicsURL($route_container,$company);
					$batch_params = "<ns1:Create>" . $data_containers->xmlArrayLineStrings() . "</ns1:Create>";
					$batch_request = new SoapVar($batch_params, XSD_ANYXML);
					$soap_result = Globals::callSoapApiCreate($url_container, $batch_request, $sales_office_no);
				print($batch_params);
				if (is_string($soap_result)) {
					Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Container: " . $soap_result, "");
				} elseif ($soap_result && property_exists($soap_result, "CashEmptiesAdjustments")) {
					Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Container successfully uploaded.", ""); /* Save log info message */
				} else {
					Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Container: " . json_encode($soap_result), "");
				}

				Globals::saveJsonFile($file_name . "-CONTAINER-" . ($key + 1), $batch_params);
			
			}

			if($data_contents->amount != 0) {
				$batch_params = "<ns1:Create>" . $data_contents->xmlArrayLineStrings() . "</ns1:Create>";
				$batch_request = new SoapVar($batch_params, XSD_ANYXML);
				$soap_result = Globals::callSoapApiCreate($url, $batch_request, $final_sales_office);
				
				if (is_string($soap_result)) {
					Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Contents: " . $soap_result, "");
				} elseif ($soap_result && property_exists($soap_result, "CashReceiptJournals")) {
					Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Contents successfully uploaded.", ""); /* Save log info message */
				} else {
					Utils::saveLog($trigger_id, $final_sales_office, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . " PICK NOTE] " . $sales_order->code . " Contents: " . $soap_result, "");
					continue;
				}
				print(json_encode($soap_result, JSON_PRETTY_PRINT));
				Globals::saveJsonFile($file_name . "-CONTENTS-" . ($key + 1), $batch_params);
			}
			try{	
				if (strpos($temp_collection_breakdown->mode, 'Cash') !== false) {
					$batch_params = "<ns1:PostCash><ns1:sPC>" . $salesman->code . "</ns1:sPC></ns1:PostCash>";
					$batch_request = new SoapVar($batch_params, XSD_ANYXML);
					$soap_result = $client->PostCash($batch_request);
				}
				if (is_string($soap_result)) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[Picknote Cash " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $sales_order->code . " " . $soap_result, "");
                }
				if (strpos($temp_collection_breakdown->mode, 'Check') !== false) {
					$batch_params = "<ns1:PostCheque><ns1:sPC>" . $salesman->code . "</ns1:sPC></ns1:PostCheque>";
					$batch_request = new SoapVar($batch_params, XSD_ANYXML);
					$soap_result = $client->PostCheque($batch_request);
				} 
				if (is_string($soap_result)) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[Picknote Check " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $sales_order->code . " " . $soap_result, "");
                }
                           
			} catch (SoapFault $e) {
				if (is_string($e->getMessage()))
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CASH_RECEIPT_POST . " PICK NOTE] " . $sales_order->code . " " . $e->getMessage(), "");
			}
			 if (isset($soap_result) && is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . " PICK NOTE] " . $sales_order->code . " " . $soap_result, "");
			} else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . " PICK NOTE] " . $sales_order->code . " successfully posted.", ""); /* Save log info message */
			}
			Globals::saveJsonFile($file_name . "-" . $key . "-POST", $batch_request);   
		}    
    }

    /**
     * Send a Sales Cash Receipt Journal Post Request
     * (25/08/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCashReceiptJournalPost($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = date("Y-m-d", strtotime("-3 days"));
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : "";
        $invoice_code = isset($data['invoice_code']) ? $data['invoice_code'] : "";

        /* Get models to send to MSD */
        $build = Invoice::where('msd_synced', '=', 1)
            ->whereBetween('invoice_date', [$date_from, $date_to])
            ->where('status', '=', 'credit_memo_posted');
        if ($sales_office_no != "")
            $build = $build->where('sales_office_no', '=', $sales_office_no);
        if ($sales_order_code != "")
            $build = $build->where('sales_order_code', '=', $sales_order_code);
        if ($invoice_code != "")
            $build = $build->where('code', '=', $invoice_code);
        $noc_data = $build->get();
        $total_rows = count($noc_data);

        $build2 = SalesOrder::select('temp_sales_order.*')
            ->where('temp_sales_order.type', '=', 5)
            ->where('temp_sales_order.transaction_type', '=', 2)
            ->whereBetween('temp_sales_order.sales_order_date', [$date_from, $date_to])
            ->where('temp_sales_order.status', '=', "cash_receipt");
        if ($sales_office_no != "") {
            $build2 = $build2->join('salesman', 'salesman.id', '=', 'temp_sales_order.salesman_id');
			$build2 = $build2->join('sales_office', 'sales_office.no', '=', 'salesman.sales_office_no');
			$build2 = $build2->where('sales_office.no', '=', $sales_office_no);
		}
        if ($sales_order_code != "")
            $build2 = $build2->where('code', '=', $sales_order_code);
        $noc_data_collection = $build2->get();
        $total_rows_collection = count($noc_data_collection);

        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", UploadConnector::MODULE_CASH_RECEIPT_POST);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        $client = new SoapClient($url, array(
            'trace' => true,
            'login' => Globals::getSoapOptions($sales_office_no)['username'],
            'password' => Globals::getSoapOptions($sales_office_no)['password'],

        )
        );
		
        try {
            if ($total_rows > 0) {
                foreach ($noc_data as $key => $batch) {

                    $success = false;
                    if ($batch->salesOrder()->first() == null) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . ": No Sales Order Found", "");
                        continue;
                    }
                    $sales_order = $batch->salesOrder()->first();
                    if ($sales_order->tempCollectionCash()->first() == null) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . ": Temp Collection Cash does not exist.", "");
                        continue;
                    }
                    if ($sales_order->salesman()->first() == null) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . ": Salesman does not exist.", "");
                        continue;
                    }
                    $temp_collection = $sales_order->tempCollectionCash()->first();
                    $salesman = $sales_order->salesman()->first();
                    if ($temp_collection) {
                        $temp_collection_breakdowns = $temp_collection->tempCollectionCashBreakdown()->get()->toArray();
                    } else {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . ": Temp Collection Cash does not exist.", "");
                       // $batch->status = "failed_post";
                        $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->updated_date = date("Y-m-d H:i:s");
                        $batch->save();
                        continue;
                    }
                    if (count($temp_collection_breakdowns) == 0) {
                        continue;
                    }

                    if ($salesman == null) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . ": Salesman does not exist.", "");
                       // $batch->status = "failed_post";
                        $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->updated_date = date("Y-m-d H:i:s");
                        $batch->save();
                        continue;
                    }


                    foreach ($temp_collection_breakdowns as $key => $temp_collection_breakdown) {
                        try {
                            if (strpos($temp_collection->mode, 'Cash') !== false) {
                                $batch_params = "<ns1:PostCash><ns1:sPC>" . $salesman->code . "</ns1:sPC></ns1:PostCash>";
                                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                                $soap_result = $client->PostCash($batch_request);
                            }
                            if (is_string($soap_result)) {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[Cash " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . " " . $soap_result, "");
                                $batch->status = "failed_post";
                                $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                $batch->updated_date = date("Y-m-d H:i:s");
                                $batch->save();
                            } else {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[Cash " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . " successfully posted.", ""); /* Save log info message */
                                $batch->status = "posted";
                                $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                $batch->updated_date = date("Y-m-d H:i:s");
							}
                            if (strpos($temp_collection->mode, 'Check') !== false) {
                                $batch_params = "<ns1:PostCheque><ns1:sPC>" . $salesman->code . "</ns1:sPC></ns1:PostCheque>";
                                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                                $soap_result = $client->PostCheque($batch_request);
                            }
                            if (is_string($soap_result)) {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[ Check " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . " " . $soap_result, "");
                                $batch->status = "failed_post";
                                $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                $batch->updated_date = date("Y-m-d H:i:s");
                                $batch->save();
                            } else {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[ Check " . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . " successfully posted.", ""); /* Save log info message */
                                $batch->status = "posted";
                                $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                $batch->updated_date = date("Y-m-d H:i:s");
                                if ($batch->save())
                                    $success = true;
                            }
                            Globals::saveJsonFile($file_name . "-" . $key, $batch);
                        } catch (SoapFault $e) {
                            //$batch->status = "failed_post";
                            //$batch->save();
                            if (is_string($e->getMessage()))
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CASH_RECEIPT_POST . "] " . $batch->code . " " . $e->getMessage(), "");
                        }

                        if ($success) {
                            Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                        } else {
                            Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                        }
                    }
                }
            } else {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CASH_RECEIPT_POST . "] No new uploaded " . strtolower(UploadConnector::MODULE_NAME_CASH_RECEIPT_POST) . " found.", ""); /* Save log info message */
            }


            if ($total_rows_collection > 0) {
                foreach ($noc_data_collection as $key => $batch) {

                    $success = false;
                    $sales_order = $batch;
                    if ($sales_order->salesman()->first() == null) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . ": Salesman does not exist.", "");
                        continue;
                    }
                    $temp_collection = $sales_order->tempCollectionCash()->first();
                    $salesman = $sales_order->salesman()->first();
                    try {
                        if ($temp_collection->mode === 'Cash') {
                            $batch_params = "<ns1:PostCash><ns1:sPC>" . $salesman->code . "</ns1:sPC></ns1:PostCash>";
                            $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                            $soap_result = $client->PostCash($batch_request);
                        }
                        if ($temp_collection->mode === 'Check') {
                            $batch_params = "<ns1:PostCheque><ns1:sPC>" . $salesman->code . "</ns1:sPC></ns1:PostCheque>";
                            $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                            $soap_result = $client->PostCheque($batch_request);
                        }
                        if (is_string($soap_result)) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . " " . $soap_result, "");
                            $batch->status = "failed_post";
                            $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
                            $batch->edited_when = date("Y-m-d H:i:s");
                            $batch->save();
                        } else {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $batch->code . " successfully posted.", ""); /* Save log info message */
                                $batch->status = "posted";
                                $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
                                $batch->edited_when = date("Y-m-d H:i:s");
                                if ($batch->save())
                                    $success = true;
                        }
                    } catch (SoapFault $e) {
                        $batch->status = "failed_post";
                        $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->edited_when = date("Y-m-d H:i:s");
                        $batch->save();
                        if (is_string($e->getMessage()))
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CASH_RECEIPT_POST . "] " . $batch->code . " " . $e->getMessage(), "");
                    }
                }
            }

        } catch (SoapFault $e) {
            if (is_string($e->getMessage()))
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $batch->code . " " . $e->getMessage(), "");
        }
    }

    /**
     * Takes data from NOC and sends it to client REST API.
     * (06/07/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesCreditMemo($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : "";
        $invoice_code = isset($data['invoice_code']) ? $data['invoice_code'] : "";

        /* Get models to send to MSD */
        $build = Invoice::select('invoice.*')->where('invoice.msd_synced', '=', 1)
            ->whereBetween('invoice.invoice_date', [$date_from, $date_to])
            ->where('invoice.status', '=', 'cash_receipt')
            ->join('temp_sales_order', 'temp_sales_order.code', '=', 'invoice.sales_order_code')
            ->whereNested(function ($q) {
                $q->whereNested(function ($q2) {
                    $q2->where('invoice.ct_slip', '=', '1')->where('temp_sales_order.type', '=', '0');
                })->orWhere('temp_sales_order.type', '=', '1');
            });
        if ($sales_office_no != "")
            $build = $build->where('sales_office_no', '=', $sales_office_no);
        if ($sales_order_code != "")
            $build = $build->where('sales_order_code', '=', $sales_order_code);
        if ($invoice_code != "")
            $build = $build->where('code', '=', $invoice_code);
        $noc_data = $build->get();
        $total_rows = count($noc_data);

        // PULLOUT
        $build2 = SalesOrder::select('temp_sales_order.*')
            ->where('temp_sales_order.type', '=', 7)
            ->where('temp_sales_order.transaction_type', '=', 2)
            ->where('temp_sales_order.status', '=', 'unserved')
            ->whereBetween('temp_sales_order.sales_order_date', [$date_from, $date_to])
            ->where('temp_sales_order.msd_synced', '=', "0");
        if ($sales_office_no != "") {
            $build2 = $build2->join('salesman', 'salesman.id', '=', 'temp_sales_order.salesman_id');
            $build2 = $build2->join('sales_office', 'sales_office.no', '=', 'salesman.sales_office_no');
            $build2 = $build2->where('sales_office.no', '=', $sales_office_no);
        }
        if ($sales_order_code != "")
            $build2 = $build2->where('temp_sales_order.code', '=', $sales_order_code);
        $noc_data_collection = $build2->get();
        $total_rows_collection = count($noc_data_collection);

        // REFUND
        $refund = SalesOrder::select('temp_sales_order.*')
            ->whereIn('temp_sales_order.type', [3, 2])
            ->where('temp_sales_order.transaction_type', '=', 2)
            ->where('temp_sales_order.status', '=', 'approved')
            ->whereBetween('temp_sales_order.sales_order_date', [$date_from, $date_to])
            ->where('temp_sales_order.msd_synced', '=', "0");
        if ($sales_office_no != "") {
            $refund = $refund->join('salesman', 'salesman.id', '=', 'temp_sales_order.salesman_id');
            $refund = $refund->join('sales_office', 'sales_office.no', '=', 'salesman.sales_office_no');
            $refund = $refund->where('sales_office.no', '=', $sales_office_no);
        }
        if ($sales_order_code != "")
            $refund = $refund->where('temp_sales_order.code', '=', $sales_order_code);
        $refund_data_collection = $refund->get();
        $total_refund_collection = count($refund_data_collection);

        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            foreach ($noc_data as $key => $batch) {
                $success = false;
				
                $ct_slip = substr($batch->code, 3, 3) == "CTS";
                if(!$ct_slip) {
					continue;
				}
				/* Assign each model to data for XML */
                $data = new SalesCreditMemoData;
                $sales_order = $batch->salesOrder()->first();
                $sales_order_returns = [];
                if ($sales_order) {
                    $salesman = $sales_order->salesman()->first();
                    $location = $sales_order->location()->first();
                    /* Get sales order returnable quantity */
                    $sales_order_returnables = $sales_order->salesOrderReturnable()->get();
                    if (count($sales_order_returnables) <= 0) {
                        $batch->status = "credit_memo_posted";
                        $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->updated_date = date("Y-m-d H:i:s");
                        if ($batch->save())
                            $success = true;
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] No Sales Order Returnable found for sales order " . $sales_order->code, ""); /* Save log info message */
                        continue;
                    }

                }
                if ($location)
                    $data->location_code = $location->code;
                if ($salesman)
                    $data->salesman_code = $salesman->code;
                $data->ct_slip = 'true';
                $data->empties_type = 'Deposit';
                $data->reason_code = 'EMPTIES';
                $data->document_date = date("Y-m-d", strtotime($batch->invoice_date));
                $data->due_date = date("Y-m-d", strtotime($batch->due_date));
                $data->applies_to_doc_type = 'Invoice';
                $data->applies_to_doc_no = $batch->code;
                /* Detail */
                if ($sales_order_returnables && count($sales_order_returnables) > 0) {
                    foreach ($sales_order_returnables as $returnable) {
                        //if($batch_detail->empties_type == "Loan") {
                        //	continue;
                        //}
                        $data_detail = new SalesCreditMemoLineData;
                        $sku_model = $returnable->sku()->first();
						$so_model = $returnable->salesOrder()->first();
                        if ($sku_model)
                            $data_detail->no = $sku_model->sys_21;
                        if ($returnable->return <= 0) {
                            continue;
                        }
						
						$batch_detail = InvoiceDetails::where('so_code', '=',  $so_model->code)->
						where('product_code', '=',  $sku_model->code)->first();
						//Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] Sending credit memo for " .  $sku_model->code . " " . $batch->code, ""); /* Save log info message */
                        $data_detail->type = 'Item';
                        $data_detail->quantity = $returnable->return;
                        $data_detail->unit_price = $returnable->unit_price;
                        $data_detail->ct_slip_no = $batch->code;
                        $data_detail->empties_type = 'Deposit';
						if($batch_detail)
							$data_detail->empties_line_no = $batch_detail->line_no;
                        if ($salesman)
                            $data_detail->zone_code = $salesman->zone;
                        $data->salesCreditMemoLineData[] = $data_detail;
                    }
                    if (count($data->salesCreditMemoLineData) === 0) {
                        $batch->status = "credit_memo_posted";
                        $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->updated_date = date("Y-m-d H:i:s");
                        $batch->save();
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] No valid returnable data found for " . $sales_order->code, ""); /* Save log info message */
                        continue;
                    }

                    $batch_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
                    $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
                    if (is_string($soap_result)) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $batch->code . " " . $soap_result, "");
                        $batch->status = "credit_memo_posted";
                        $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->updated_date = date("Y-m-d H:i:s");
                        $batch->save();
                    } elseif ($soap_result && property_exists($soap_result, "SalesCreditMemoService")) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $batch->code . " successfully uploaded.", ""); /* Save log info message */
                        /* Update invoice status */
                        $batch->status = "credit_memo";
                        $batch->ref_invoice = $soap_result->SalesCreditMemoService->No;
                        $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->updated_date = date("Y-m-d H:i:s");
                        if ($batch->save())
                            $success = true;
                    }
                    Globals::saveJsonFile($file_name . "-" . ($key + 1), $batch_params);

                    if ($success) {
                        Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                    } else {
                        Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] No new downloaded " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " found.", ""); /* Save log info message */
        }


        if ($total_rows_collection > 0)  {
            foreach ($noc_data_collection as $key => $batch) {
                $success = false;
                /* Assign each model to data for XML */
                $data = new SalesCreditMemoData;
                $salesman = $batch->salesman()->first();
                $location = $batch->location()->first();
                /* Get sales order returnable quantity */


                $sales_order_returnables = $batch->salesOrderReturnable()->get();

                if (count($sales_order_returnables) <= 0) {
                    $batch->status = "posted";
                    if ($batch->save())
                        $success = true;
                    continue;
                }

                $sales_office_no_batch = $sales_office_no;

                if ($location) {
                    $data->location_code = $location->code;
                    $sales_office_no_batch = $location->sales_office_no;
                }
                if ($salesman) {
                    $data->salesman_code = $salesman->code;
                    $sales_office_no_batch = $salesman->sales_office_no;
                }

                $data->ct_slip = 'true';
                $data->empties_type = 'Deposit';
                $data->reason_code = 'PULLOUT';
                $data->document_date = date("Y-m-d");
                $data->due_date = date("Y-m-d");
                $data->applies_to_doc_type = 'Invoice';
		
				$pullout_data = [];

                if ($sales_order_returnables && count($sales_order_returnables) > 0) {
                    $applies_to_doc_no = "";
                    foreach ($sales_order_returnables as $returnable) {
						
						$data->applies_to_doc_no = $returnable->reference_no;
						if(!isset($pullout_data[$returnable->reference_no])) {
							$pullout_data[$returnable->reference_no] = clone $data;
						}
                        $data_detail = new SalesCreditMemoLineData;
                        $sku_model = $returnable->sku()->first();
                        if ($sku_model)
                            $data_detail->no = $sku_model->sys_21; 
						else
                            continue;
						

                        $data_detail->type = 'Item';
                        $data_detail->quantity = $returnable->return;
                        $data_detail->unit_price = $returnable->unit_price;
                        $data_detail->ct_slip_no = $returnable->reference_no;
                        $data_detail->empties_type = 'Deposit';
                        if ($data_detail->quantity == 0) {
                            continue;
                        }
						
						$pullout_data[$returnable->reference_no]->salesCreditMemoLineData[] = $data_detail;
						if($pullout_data[$returnable->reference_no]->tempSalesReturnableId == null) {
							$pullout_data[$returnable->reference_no]->tempSalesReturnableId = $returnable->id;
						}
                    }

                    if(count($pullout_data) === 0 )
                    {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $batch->code . " Nothing to send.");
                        $batch->status = "failed";
                        $batch->save();
                        continue;
                    }
					
					foreach ($pullout_data as $key => $data_pullout) {
						$batch_params = "<ns1:Create>" . $data_pullout->xmlArrayLineStrings() . "</ns1:Create>";
						$batch_request = new SoapVar($batch_params, XSD_ANYXML);
						$soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);

						if (is_string($soap_result)) {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $batch->code . " " . $soap_result, "");
							$batch->status = "failed";
							$batch->save();
						} elseif ($soap_result && property_exists($soap_result, "SalesCreditMemoService")) {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $batch->code . " successfully uploaded.", ""); /* Save log info message */
							/* Update invoice status */
							$batch->status = "credit_memo";;
							if ($batch->save())
								$success = true;
							
							$temp_returnable = SalesOrderReturnable::where('id', '=', $data_pullout->tempSalesReturnableId)->first();
							if($temp_returnable) {
								$temp_returnable->document_no = $soap_result->SalesCreditMemoService->No;
								$temp_returnable->save();
							}
						}
						
						Globals::saveJsonFile($file_name . "-" . ($key + 1), $batch_params);
					}

					if ($success) {
						Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
					} else {
						Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
					}
                }
            }
        }


        
        if ($total_refund_collection > 0)  {
            foreach ($refund_data_collection as $key => $batch) {
                $success = false;
                /* Assign each model to data for XML */
                $data = new SalesCreditMemoData;
                $salesman = $batch->salesman()->first();
                $location = $batch->location()->first();
                /* Get sales order returnable quantity */


                $sales_order_detail = $batch->salesOrderDetail()->get();

                if (count($sales_order_detail) <= 0) {
			     $batch->status = "posted";
                    if ($batch->save())
                        $success = true;
                    continue;
                }

                $sales_office_no_batch = $sales_office_no;

                if ($location) {
                    $data->location_code = $location->code;
                    $sales_office_no_batch = $location->sales_office_no;
                }
                if ($salesman) {
                    $data->salesman_code = $salesman->code;
                    $sales_office_no_batch = $salesman->sales_office_no;
                }
                
                if($batch->type == 2) {
                    $data->ct_slip = 'true';
                    $data->empties_type = 'Deposit';
                    $data->reason_code = 'REFUND';
                    $data->document_date = date("Y-m-d");
                    $data->due_date = date("Y-m-d");
                    $data->applies_to_doc_no = $batch->si_code;
                    $data->applies_to_doc_type = 'Invoice';
                }

                
                if($batch->type == 3) {
                    $data->ct_slip = 'true';
                    $data->empties_type = 'Loan';
                    $data->reason_code = 'PULLOUT';
                    $data->document_date = date("Y-m-d");
                    $data->due_date = date("Y-m-d");
                    $data->applies_to_doc_no = $batch->si_code;
                    $data->applies_to_doc_type = 'Invoice';
                }


                if ($sales_order_detail && count($sales_order_detail) > 0) {
                    $applies_to_doc_no = "";
                    foreach ($sales_order_detail as $detail) {
                        $data_detail = new SalesCreditMemoLineData;
                        $detail->sales_office_code = $salesman->sales_office_no;
                        $sku_model = $detail->sku()->first();
                        if ($sku_model)
                            $data_detail->no = $sku_model->sys_21; 
						else {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $batch->code . " detail product " . $detail->product_code . " not found.", "");
                            continue;
						}

                        $data_detail->type = 'Item';
                        $data_detail->quantity = abs($detail->quantity);
                        $data_detail->unit_price = $detail->unit_price;
                        
                        
                        if($batch->type == 2) {
                            $data_detail->empties_type = 'Deposit';
                        }
                
                        if($batch->type == 3) {
                            $data_detail->empties_type = 'Loan';
                        }
						
						$data->salesCreditMemoLineData[] = $data_detail;
                    }
					
						$batch_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
						$batch_request = new SoapVar($batch_params, XSD_ANYXML);
						$soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);

						if (is_string($soap_result)) {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $batch->code . " " . $soap_result, "");
							$batch->status = "failed";
							$batch->save();
						} elseif ($soap_result && property_exists($soap_result, "SalesCreditMemoService")) {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $batch->code . " successfully uploaded.", ""); /* Save log info message */
							/* Update invoice status */
							$batch->status = "posted";
							if ($batch->save())
								$success = true;
						}
						
						Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);

					if ($success) {
						Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
					} else {
						Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
					}
                }
            }
        }
    }
	
	
    /**
     * Syncs Pick Note Inventory to Loadboard and sends to NOC
     * (09/01/2024)
     * 
     * @param data Pick Note Inventory Data
	 * @param sales_office_no Sales Office Code
     * @param trigger_id ID for request trigger log
     * @param company Company Code
	 *
     * @return void
     */
    public static function syncMSDCreditMemoFromPicknote($sales_order_id, $returnable_data, $sales_office_no, $trigger_id = null, $company)
    {
   
        if ($sales_order_id != 0) {
		
			$sales_order = SalesOrder::where('id', '=', $sales_order_id)->first();
			if(!$sales_order) {
				return;
			}
		
			$salesman = $sales_order->salesman()->first();
			$location = $sales_order->location()->first();
			$sales_order_returnables = $returnable_data;
			$invoices = $sales_order->invoice()->get();
			
			if($salesman) {
				$sales_office_no = $salesman->sales_office_no;
			}
			
			if(count($invoices) == 0) {
				return;
			}
            foreach ($invoices as $key => $invoice) {
			
                $ct_slip = substr($invoice->code, 3, 3) == "CTS";

				
				/* Assign each model to data for XML */
                $data = new SalesCreditMemoData;
                $sales_order_returns = [];
                    
                $data->empties_type = 'Loan';
                $data->reason_code = 'Empties';
                $data->document_date = date("Y-m-d", strtotime($invoice->invoice_date));
                $data->due_date = date("Y-m-d", strtotime($invoice->due_date));
                $data->applies_to_doc_type = 'Invoice';

                if($ct_slip) {
					$data->applies_to_doc_no = $invoice->code;
					$data->ct_slip = 'true';
				}
				else{
					$data->applies_to_doc_no = $invoice->code;
					$data->ct_slip = 'false';
				}
				
                /* Detail */
                if ($sales_order_returnables && count($sales_order_returnables) > 0) {
                    foreach ($sales_order_returnables as $returnable) {
                        //if($batch_detail->empties_type == "Loan") {
                        //	continue; 
                        //}
							
						if(!isset($returnable['uom']))
						{
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE] No UOM data: " . json_encode($returnable), ""); /* Save log info message */
                       
							continue;
						}
						if(!$ct_slip and in_array($returnable['uom'] , ["BTL", "SHL"])){
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE] CTS: " . json_encode($returnable), ""); /* Save log info message */
							continue;
						}
						else if($ct_slip and in_array($returnable['uom'] , ["CSE"])) {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE] Not CTS: " . json_encode($returnable), ""); /* Save log info message */
							
							continue;
						}
						$data->salesman_code =$returnable['employee'];
						$data->location_code =$returnable['location_code'];
                        $data_detail = new SalesCreditMemoLineData;
                        $sku_model =  Sku::where('sys_21', '=', $returnable['product'])->where('sales_office_no', '=', $sales_office_no)->where('msd_synced', 1)->first();
						$so_model = $sales_order;
                        if ($sku_model)
                            $data_detail->no = $sku_model->sys_21;
		
						if($returnable['return'] <= 0) {
							continue;
						}
						
						$batch_detail = InvoiceDetails::where('so_code', '=',  $so_model->code)
						->where('product_code', '=',  $sku_model->code)
						->where('inv_id', '=', $invoice->id)
						->first();
						//Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] Sending credit memo for " .  $sku_model->code . " " . $batch->code, ""); /* Save log info message */
                        
						$data_detail->type = 'Item';
                        $data_detail->quantity = $returnable['return'];
                        $data_detail->unit_price = $returnable['unit_price'];
                        $data_detail->empties_type = 'Loan';
                        $data_detail->zone_code =  $returnable['employee'];

						if($batch_detail)
							$data_detail->empties_line_no = $batch_detail->line_no;

						if($ct_slip) {
							$data_detail->ct_slip_no = $invoice->code;
						}
                        $data->salesCreditMemoLineData[] = $data_detail;

                    }
					
                    if (count($data->salesCreditMemoLineData) === 0) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE] No valid returnable data found for " . $sales_order->code . " " . $invoice->code, ""); /* Save log info message */
                        continue;
                    }
					$route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo']['list'];
					$url = Globals::soapABIMSDynamicsURL($route, $company);

                    $batch_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
                    $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
			
					
					Globals::saveJsonFile(SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE_REQUEST-" . $sales_order->code. "-" . ($key + 1), $batch_params);
					Globals::saveJsonFile(SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE_RESULT-" . $sales_order->code. "-" . ($key + 1), $soap_result);
                    if (is_string($soap_result)) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE}] " . $invoice->code . " " . $soap_result, "");
                       
                    } elseif ($soap_result && property_exists($soap_result, "SalesCreditMemoService")) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE] " . $invoice->code . " successfully uploaded.", ""); /* Save log info message */
						$ref_no = $soap_result->SalesCreditMemoService->No;
                        if($ct_slip) {
							try {
								$route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo-post']['list'];
								$url = Globals::soapABIMSDynamicsURL($route, $company, "CodeUnit");
								$client = new SoapClient($url, array(
									'trace' => true,
									'login' => Globals::getSoapOptions($sales_office_no)['username'],
									'password' => Globals::getSoapOptions($sales_office_no)['password'],
								));
								$batch_params_p = "<ns1:SalesCreditMemoPost><ns1:documentNo>" . $ref_no . "</ns1:documentNo></ns1:SalesCreditMemoPost>";
								$batch_request_p = new SoapVar($batch_params_p, XSD_ANYXML);
								$soap_result = $client->SalesCreditMemoPost($batch_request_p);
							
							} catch (SoapFault $e) {
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE] " . $invoice->code . " " . $ref_no . " " . $e->getMessage(), "");
							}
							if (is_string($soap_result)) {
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE}] " . $invoice->code . " " . $ref_no . " " . $soap_result, "");
							}
							else {
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE] " . $invoice->code  . " " . $ref_no  . " successfully posted.", ""); /* Save log info message */
							}
						}
                    }
                }
				else {
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " PICK NOTE] No returnables found", "");
					
				}
            }
        } 

    }
	
	
    /**
     * Syncs Pick Note Inventory to Loadboard and sends to NOC
     * (09/01/2024)
     * 
     * @param data Pick Note Inventory Data
	 * @param sales_office_no Sales Office Code
     * @param trigger_id ID for request trigger log
     * @param company Company Code
	 *
     * @return void
     */
    public static function syncMSDCreditMemoCancelled($method, $url, $data, $sales_office_no, $trigger_id = null, $company)
    {
		if(!isset($data['sales_order_code'])) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel] Request error.", ""); /* Save log info message */
			return;
		}
	
   
        foreach($data['sales_order_code'] as $sales_order_code) {
		
			$sales_order = SalesOrder::where('code', '=', $sales_order_code)->first();
			if(!$sales_order) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel] No sales order " . $sales_order->code, ""); /* Save log info message */
				continue;
			}
		
			$salesman = $sales_order->salesman()->first();
			$location = $sales_order->location()->first();
			$invoices = $sales_order->invoice()->get();
			
			if($salesman) {
				$sales_office_no = $salesman->sales_office_no;
			}
			
			if(count($invoices) == 0) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel] No invoice data found for " . $sales_order->code, ""); /* Save log info message */
				continue;
			}
			
            foreach ($invoices as $key => $invoice) {
				
				/* Assign each model to data for XML */
                $data = new SalesCreditMemoData;
                    
                $data->ct_slip = 'true';
                $data->empties_type = 'Loan';
                $data->reason_code = 'Empties';
                $data->document_date = date("Y-m-d", strtotime($invoice->invoice_date));
                $data->due_date = date("Y-m-d", strtotime($invoice->due_date));
                $data->applies_to_doc_type = 'Invoice';
                $data->applies_to_doc_no = $invoice->code;
				$data->salesman_code = $salesman->code;
				$data->location_code = $location->code;
				
				$invoiceDetails = $invoice->invoiceDetails()->get();
				
                /* Detail */
                if ($invoiceDetails && count($invoiceDetails) > 0) {
                    foreach ($invoiceDetails as $detail) {
                        $data_detail = new SalesCreditMemoLineData;
                        $sku_model =  Sku::where('code', '=', $detail->product_code)->where('sales_office_no', '=', $sales_office_no)->first();
						$so_model = $sales_order;
                        if ($sku_model)
                            $data_detail->no = $sku_model->sys_21;
                        if ($detail->serve_quantity <= 0) {
                            continue;
                        }
						
						$data_detail->type = 'Item';
                        $data_detail->quantity = $detail->serve_quantity;
                        $data_detail->unit_price = $detail->unit_price;
                        $data_detail->ct_slip_no = $invoice->code;
                        $data_detail->empties_type = 'Loan';
                        $data_detail->zone_code =  $salesman->zone;
                        $data->salesCreditMemoLineData[] = $data_detail;
                    }
					
                    if (count($data->salesCreditMemoLineData) === 0) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel] No valid detail data found for " . $sales_order->code, ""); /* Save log info message */
                        continue;
                    }
					$route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo']['list'];
					$url = Globals::soapABIMSDynamicsURL($route, $company);

                    $batch_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
                    $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
					
					Globals::saveJsonFile(SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel" . $sales_order->code. "-" . ($key + 1), $batch_params);
                    if (is_string($soap_result)) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel] " . $sales_order->code . " " . $invoice->code . " " . $soap_result, "");
                       
                    } elseif ($soap_result && property_exists($soap_result, "SalesCreditMemoService")) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel] " . $sales_order->code . " " . $invoice->code . " " . " successfully uploaded.", ""); /* Save log info message */
						$ref_no = $soap_result->SalesCreditMemoService->No;
                        
						try {
							$route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo-post']['list'];
							$url = Globals::soapABIMSDynamicsURL($route, $company, "CodeUnit");
							$client = new SoapClient($url, array(
								'trace' => true,
								'login' => Globals::getSoapOptions($sales_office_no)['username'],
								'password' => Globals::getSoapOptions($sales_office_no)['password'],
							));
							$batch_params_p = "<ns1:SalesCreditMemoPost><ns1:documentNo>" . $ref_no . "</ns1:documentNo></ns1:SalesCreditMemoPost>";
							$batch_request_p = new SoapVar($batch_params_p, XSD_ANYXML);
							$soap_result = $client->SalesCreditMemoPost($batch_request_p);
						
						} catch (SoapFault $e) {
							if (is_string($e->getMessage()))
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel] " . $sales_order->code. " " . $ref_no . " " . $e->getMessage(), "");
						}
						if (is_string($soap_result)) {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel] " . $sales_order->code . " " . $ref_no . " " . $soap_result, "");
						}
						else {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel] " . $sales_order->code  . " " . $ref_no  . " successfully posted.", ""); /* Save log info message */
						}
                    }
                }
				else {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " Cancel] No details data found found", "");
					
				}
            }
        } 

    }

    /**
     * Send a Sales Credit Memo Post Request
     * (24/08/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesCreditMemoPost($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : "";
        $invoice_code = isset($data['invoice_code']) ? $data['invoice_code'] : "";

        /* Get models to send to MSD */
        $build = Invoice::where('msd_synced', '=', 1)
            ->whereBetween('invoice_date', [$date_from, $date_to])
            ->where('status', '=', 'credit_memo');
        if ($sales_office_no != "")
            $build = $build->where('sales_office_no', '=', $sales_office_no);
        if ($sales_order_code != "")
            $build = $build->where('sales_order_code', '=', $sales_order_code);
        if ($invoice_code != "")
            $build = $build->where('code', '=', $invoice_code);
        $noc_data = $build->get();
        $total_rows = count($noc_data);
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        $build2 = SalesOrder::select('temp_sales_order.*')
            ->where('temp_sales_order.type', '=', 7)
            ->where('temp_sales_order.transaction_type', '=', 2)
            ->whereBetween('temp_sales_order.sales_order_date', [$date_from, $date_to])
            ->where('temp_sales_order.status', '=', "credit_memo");

        if ($sales_office_no != "") {
            $build2 = $build2->join('salesman', 'salesman.id', '=', 'temp_sales_order.salesman_id');
            $build2 = $build2->join('sales_office', 'sales_office.no', '=', 'salesman.sales_office_no');
            $build2 = $build2->where('sales_office.no', '=', $sales_office_no);
        }

        if ($sales_order_code != "")
            $build2 = $build2->where('temp_sales_order.code', '=', $sales_order_code);
        $noc_data_collection = $build2->get();
        $total_rows_collection = count($noc_data_collection);

        $client = new SoapClient($url, array(
            'trace' => true,
            'login' => Globals::getSoapOptions($sales_office_no)['username'],
            'password' => Globals::getSoapOptions($sales_office_no)['password'],
        )
        );

        if ($total_rows > 0) {
            foreach ($noc_data as $key => $batch) {

                $success = false;
                if (!isset($batch->ref_invoice)) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $batch->code . ": Invoice Reference number does not exist.", "");
                    continue;
                }
                /* Create new data in MSD */
                $batch_params = "<ns1:SalesCreditMemoPost><ns1:documentNo>" . $batch->ref_invoice . "</ns1:documentNo></ns1:SalesCreditMemoPost>";
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);

                try {
                    $soap_result = $client->SalesCreditMemoPost($batch_request);
                    if (is_string($soap_result)) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $batch->code . " " . $soap_result, "");
                        $batch->status = "credit_memo_posted";
                        $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->updated_date = date("Y-m-d H:i:s");
                        $batch->save();
                    } else {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $batch->code . " successfully posted.", ""); /* Save log info message */
                        $batch->status = "credit_memo_posted";
                        $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->updated_date = date("Y-m-d H:i:s");
                        if ($batch->save())
                            $success = true;
                    }
                    Globals::saveJsonFile($file_name . "-" . $key, $batch);
                } catch (SoapFault $e) {
                    if (is_string($e->getMessage()))
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $batch->code . " " . $e->getMessage(), "");
                }

                if ($success) {
                    Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . "] No new uploaded " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " found.", ""); /* Save log info message */
        }



        if ($total_rows_collection > 0) {
            foreach ($noc_data_collection as $key => $batch) {
				
                
                $sales_order_returnables = $batch->salesOrderReturnable()->whereNotNull('document_no')->get();

					
				foreach($sales_order_returnables as $returnable) {
                    $success = false;

					if( $returnable->document_no == null ) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $batch->code . " Returnable has no document_no", "");
						continue;
					}
                    /* Create new data in MSD */
                    $batch_params = "<ns1:SalesCreditMemoPost><ns1:documentNo>" . $returnable->document_no . "</ns1:documentNo></ns1:SalesCreditMemoPost>";
                    $batch_request = new SoapVar($batch_params, XSD_ANYXML);

                    try {
                        $soap_result = $client->SalesCreditMemoPost($batch_request);
                        if (is_string($soap_result)) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " .$returnable->document_no  . " " . $soap_result, "");
                            $batch->status = "posted";
                            $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
                            $batch->edited_when = date("Y-m-d H:i:s");
                            $batch->save();
                        } else {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $returnable->document_no . " successfully posted.", ""); /* Save log info message */
                            $batch->status = "posted";
                            $batch->edited_by = UploadConnector::MSD_LOGGER_NAME;
                            $batch->edited_when = date("Y-m-d H:i:s");
                            if ($batch->save())
                                $success = true;
                        }
                        Globals::saveJsonFile($file_name . "-" . $key, $batch);
                    } catch (SoapFault $e) {
                        if (is_string($e->getMessage()))
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $returnable->document_no . " " . $e->getMessage(), "");
                    }
					
				}

                    if ($success) {
                        Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                    } else {
                        Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                    }                
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesOrderData::MODULE_NAME_SALES_ORDER_POST . "] No new uploaded " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " found.", ""); /* Save log info message */
        }
    }


    /**
     * Send a Manual Sales Credit Memo Post Request
     * (11/03/2025)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesCreditMemoPostManual($method, $url, $data, $trigger_id = null)
    {
		$credit_memo_no = isset($data['no']) ? $data['no'] : "";
		$sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		$sales_office_no = isset($json['sales_office_no']) ? $json['sales_office_no'] : "";

		try {
			$client = new SoapClient($url, array(
				'trace' => true,
				'login' => Globals::getSoapOptions($sales_office_no)['username'],
				'password' => Globals::getSoapOptions($sales_office_no)['password'],
			));
		} catch (Exception $e) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] SOAP Client Error: " . $e->getMessage(), "");
			return;
		}

		if($credit_memo_no != "") {
			$batch_post_params = "<ns1:SalesCreditMemoPost><ns1:documentNo>" . ($credit_memo_no) . "</ns1:documentNo></ns1:SalesCreditMemoPost>";
			$batch_post_request = new SoapVar($batch_post_params, XSD_ANYXML);

			try {
				$soap_post_result = $client->SalesCreditMemoPost($batch_post_request);
				if(is_string($soap_post_result)) {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . " Queue] SOAP Result " . $credit_memo_no . ": ". $soap_post_result, "");
				}
				else{
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . " Queue] Successfully posted " . $credit_memo_no, "");
				}
			} catch (\Exception $e) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] Error: " . $e->getMessage(), "");
			}
		}
	}

    /**
     * Send a Credit Memo Update Location Request
     * (02/04/2025)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCreditUpdateLocation($method, $url, $data, $sales_office_no, $trigger_id, $company)
    {
		$credit_memo_no = isset($data['documentNo']) ? $data['documentNo'] : "";
		$loc_code = isset($data['locationCode']) ? $data['locationCode'] : "";
		try {
			$client = new SoapClient($url, array(
				'trace' => true,
				'login' => Globals::getSoapOptions($sales_office_no)['username'],
				'password' => Globals::getSoapOptions($sales_office_no)['password'],
			));
		} catch (Exception $e) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[Credit Memo Update Location] SOAP Client Error: " . $e->getMessage(), "");
			return;
		}

		if($credit_memo_no != "") {
			$batch_post_params = "<ns1:SalesCreditMemoUpdateLocation><ns1:documentNo>" . ($credit_memo_no) . "</ns1:documentNo><ns1:locationCode>" . ($loc_code) . "</ns1:locationCode></ns1:SalesCreditMemoUpdateLocation>";
			$batch_post_request = new SoapVar($batch_post_params, XSD_ANYXML);

			try {
				$soap_post_result = $client->SalesCreditMemoUpdateLocation($batch_post_request);
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[Credit Meomo Update Location] Successfully updated " . $credit_memo_no, "");
			} catch (\Exception $e) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[Credit Meomo Update Location] Error: " . $e->getMessage(), "");
			}
		}
	}

	public static function syncMSDSalesCreditMemoPulloutQueue($method, $url, $json, $trigger_id = null) {
		$file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-PULLOUT-", SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO);
        $company = isset($json['company']) ? $json['company'] : "";

		// Initialize variables
		$sales_office_no = null;

		// Validate sales office
		$sales_office_model = SalesOffice::where('short_desc', '=', isset($json['sales_office_no']) ? $json['sales_office_no'] : '')->first();
		if ($sales_office_model) {
			$sales_office_no = $sales_office_model->no;
		} else {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] Sales Office No not found", "");
			return;
		}

        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo-post']['list'];
        $post_url = Globals::soapABIMSDynamicsURL($route, $company, "codeunit");
		try {
			$client = new SoapClient($post_url, array(
				'trace' => true,
				'login' => Globals::getSoapOptions($sales_office_no)['username'],
				'password' => Globals::getSoapOptions($sales_office_no)['password'],
			));
		} catch (Exception $e) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] SOAP Client Error: " . $e->getMessage(), "");
			return;
		}


		// Assign each model to data for XML
		$data = new SalesCreditMemoData;
		$data->salesCreditMemoLineData = array(); // Initialize property
		$data->location_code = isset($json['location_code']) ? $json['location_code'] : '';
		$data->salesman_code = isset($json['salesman_code']) ? $json['salesman_code'] : '';
		$data->ct_slip = 'true';
		$data->empties_type = 'Deposit';
		$data->document_date = date("Y-m-d");
		$data->due_date = date("Y-m-d");
		$data->applies_to_doc_type = 'Invoice';
		if($json['type'] == 2 or $json['type'] == 3) {
			$data->reason_code = 'REFUND';
		}
		else{
			$data->reason_code = 'PULLOUT';
		}
		
		$sales_order_returnables = isset($json['Order_return_obj']) ? $json['Order_return_obj'] : array();
		if (is_array($sales_order_returnables) && count($sales_order_returnables) > 0) {
			foreach ($sales_order_returnables as $returnable) {
				$data_detail = new SalesCreditMemoLineData;
				$data_detail->type = 'Item';
				$data_detail->quantity = abs($returnable['qty']);
				$data_detail->unit_price = $returnable['unit_price'];
				$data_detail->no = $returnable['sku_code'];
				$data_detail->empties_type = 'Deposit';
				$data->ct_slip = "true";
				$data_detail->no = $returnable['sku_code'];
				
				$data->applies_to_doc_no = $returnable['reference_no'];

				$lineNo = DB::table('invoice_details')
					->where('product_code', 'like', $returnable['sku_code'] . '%')
					->where('inv_code',  $returnable['reference_no'])
					->value('line_no');

				if($lineNo)
					$data_detail->empties_line_no = $lineNo;

				if($data->applies_to_doc_no == null or $data->applies_to_doc_no == "") {
					continue;
				}

				if ($data_detail->quantity > 0) {
					$data->salesCreditMemoLineData[] = $data_detail;
				}
			}

			$batch_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
			$batch_request = new SoapVar($batch_params, XSD_ANYXML);
			$soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);

			if (is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] SOAP Error: " . $soap_result, "");
			} elseif ($soap_result && property_exists($soap_result, "SalesCreditMemoService")) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] Successfully uploaded: " . $json['code'], "");
				$updatedRows = InvoiceDetails::where('inv_code', $data->applies_to_doc_no)
				->update(['doc_no' => $soap_result->SalesCreditMemoService->No]);
				if ($updatedRows) {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] Saved doc no code for invoice: " . $data->applies_to_doc_no, "");
				} else {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "]  Error: " . $json['code'] . " invoice of ". $data->applies_to_doc_no . " not found", "");
				}
				if($json['type'] == 2 or $json['type'] == 3) {
					$batch_approval_params = "<ns1:SendCreditMemoforApproval><ns1:salesCrMemoDocNo>" . ($soap_result->SalesCreditMemoService->No) . "</ns1:salesCrMemoDocNo></ns1:SendCreditMemoforApproval>";
					$batch_approval_request = new SoapVar($batch_approval_params, XSD_ANYXML);

                    $app_r = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo-post']['list'];
					$app_url = Globals::soapABIMSDynamicsURL($app_r, $company, 'codeunit');		
					$soap_result_approval = Globals::callSoapApiOther($app_url, $batch_approval_request, 'SendCreditMemoforApproval', $sales_office_no);
					if (is_string($soap_result_approval)) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] SOAP Error: " . $json['code'] . " - ". $soap_result_approval, "");
					} else {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] Approval sending success: " .  $json['code'], "");
					}
				}

				else {
					$batch_post_params = "<ns1:SalesCreditMemoPost><ns1:documentNo>" . ($soap_result->SalesCreditMemoService->No) . "</ns1:documentNo></ns1:SalesCreditMemoPost>";
					$batch_post_request = new SoapVar($batch_post_params, XSD_ANYXML);

					try {
						$soap_post_result = $client->SalesCreditMemoPost($batch_post_request);
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . " Queue] Successfully posted " . $soap_result->SalesCreditMemoService->No, "");
					} catch (\Exception $e) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] Error: " . $e->getMessage(), "");
					}
				}
			}
			Globals::saveJsonFile($file_name, $batch_params);
		} else {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] No order obj found: " .  $json['code'], "");
		}
		$data->applies_to_doc_no = "";
		$data->applies_to_id = "";
		$data->salesCreditMemoLineData = [];
		$sales_order_details = isset($json['Order_detail_obj']) ? $json['Order_detail_obj'] : array();
		if (is_array($sales_order_details) && count($sales_order_details) > 0) {
			foreach ($sales_order_details as $detail) {
				$data_detail = new SalesCreditMemoLineData;
				$data_detail->type = 'Item';
				$data_detail->quantity = abs($detail['quantity']);
				$data_detail->unit_price = $detail['unit_price'];
				$data_detail->no = $detail['product_code'];
				$data_detail->empties_type = 'Deposit';
				$data->ct_slip = "false";
				$data_detail->ct_slip_no = "";
				
				$data->applies_to_id =  $json['si_code'];

				if($data->applies_to_id == null or $data->applies_to_id =="" ) {
					continue;
				}
				if ($data_detail->quantity > 0) {
					$data->salesCreditMemoLineData[] = $data_detail;
				}
			}

			$batch_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
			$batch_request = new SoapVar($batch_params, XSD_ANYXML);
			$soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);

			if (is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] SOAP Error: " . $soap_result, "");
			} elseif ($soap_result && property_exists($soap_result, "SalesCreditMemoService")) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] Successfully uploaded.", "");

				$updatedRows = InvoiceDetails::where('inv_code', $data->applies_to_doc_no )
				->update(['doc_no' => $soap_result->SalesCreditMemoService->No]);
				if ($updatedRows) {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] Saved doc no code for invoice: " . $data->applies_to_doc_no, "");
				} else {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "]  Error: " . $json['code'] . " invoice of ". $data->applies_to_doc_no . " not found", "");
				}

				if($json['type'] == 2 or $json['type'] == 3) {
					$batch_approval_params = "<ns1:SendCreditMemoforApproval><ns1:salesCrMemoDocNo>" . ($soap_result->SalesCreditMemoService->No) . "</ns1:salesCrMemoDocNo></ns1:SendCreditMemoforApproval>";
					$batch_approval_request = new SoapVar($batch_approval_params, XSD_ANYXML);
					$soap_result_approval = Globals::callSoapApiOther($post_url, $batch_approval_request, 'SendCreditMemoforApproval', $sales_office_no);
					if (is_string($soap_result_approval)) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] SOAP Error: " . $json['code'] . " - ". $soap_result_approval, "");
					} else {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] Approval sending success: " .  $json['code'], "");
					}
				}
				else {
					$batch_post_params = "<ns1:SalesCreditMemoPost><ns1:documentNo>" . ($soap_result->SalesCreditMemoService->No) . "</ns1:documentNo></ns1:SalesCreditMemoPost>";
					$batch_post_request = new SoapVar($batch_post_params, XSD_ANYXML);

					try {
						$soap_post_result = $client->SalesCreditMemoPost($batch_post_request);
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . " Queue] Successfully posted " . $soap_result->SalesCreditMemoService->No, "");
					} catch (\Exception $e) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] Error: " . $e->getMessage(), "");
					}
				}
			}
			Globals::saveJsonFile($file_name, $batch_params);
		} else {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] No detail obj found: " .  $json['code'], "");
		}

	}


    /**
     * Takes data from NOC and sends it to client REST API.
     * (07/07/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDReturnRequest($method, $url, $data, $trigger_id = null)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $module = (new OutgoingNotificationData)->getModuleByType(1);
        $module_name = (new OutgoingNotificationData)->getModuleNameByType(1);
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $return_request_code = isset($data['return_request_code']) ? $data['return_request_code'] : "";
        $company_code = Globals::getWmsCompanyCode();

        /* Get models to send to MSD */
        $salesman = Salesman::select('code')->where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)->get()->toarray();
        $build = WMSOutgoingNotification::where('transaction_type', '=', 1)
            ->where('msd_synced', '=', 0)
            ->where('status', '=', 0)
            ->where('confirmed', '=', 0)
            ->whereIn('employee_code', $salesman)
            ->whereBetween('withdrawal_date', [$date_from, $date_to]);
        if ($return_request_code != "")
            $build = $build->where('withdrawal_code', '=', $return_request_code);
        $wms_data = count($salesman) > 0 ? $build->get() : [];
        $total_rows = count($wms_data);
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", $module);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            foreach ($wms_data as $key => $batch) {
                $success = false;
                /* Assign each model to data for XML */
                $data = new OutgoingNotificationData;
                $data = UploadConnector::assignToData($data, $batch); // Header
                $data->withdrawal_code = strtoupper($batch->withdrawal_code);
                $data->transfer_order_type = 'Product_Return_from_Salesman_location';
                $data->direct_transfer = 'true';
                /* Transfer From */
                $employee = WMSEmployee::with(['company'])
                    ->whereHas('company', function ($q) use ($company_code) {
                        $q->where('code', '=', $company_code);
                    })->where('employee_code', '=', $data->employee_code)->first();
                if ($employee && $employee->zone()->first())
                    $data->transfer_from = $employee->zone()->first()->zone_code;
                /* Transfer To */
                $sales_office = WMSSalesOffice::with(['company'])
                    ->whereHas('company', function ($q) use ($company_code) {
                        $q->where('code', '=', $company_code);
                    })->where('sales_office_code', '=', $sales_office_no)->first();
                if ($sales_office && $sales_office->zone()->first())
                    $data->transfer_to = $sales_office->zone()->first()->zone_code;
                $data->sales_office_code = $sales_office->short_desc;
                /* Detail */
                $data_detail = [];
                $batch_details = $batch->outgoingNotificationDetails()->get();
                if (count($batch_details) > 0) {
                    foreach ($batch_details as $key => $batch_detail) {
                        $dt_data = new OutgoingNotificationDetailData;
                        $dt_data = UploadConnector::assignToData($dt_data, $batch_detail);
						$dt_data->sales_office_code = $batch->short_desc;
                        $sku_model_noc = $batch_detail->nocSku()->first();

                        if ($sku_model_noc)
                            $dt_data->sku_code = $sku_model_noc->sys_21;
                        $data_detail[$key] = $dt_data;
                    }
                }
                $data->outgoingNotificationDetail = $data_detail;

                /* Check if create or update */
                if (is_null($data->ms_dynamics_key) || empty($data->ms_dynamics_key)) {
                    /* Create new data in MSD */
                    $batch_params = "<ns1:Create>" . $data->xmlMSDArrayString() . "</ns1:Create>";
                    $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);
                    /** Log response message */
                    if (is_string($soap_result)) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->withdrawal_code . " " . $soap_result, "");
                    } elseif ($soap_result && property_exists($soap_result, "TransferOrderService")) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->withdrawal_code . " successfully uploaded.", ""); /* Save log info message */
                        $success = true;
                    }
                    Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
                } else {
                    /* Used different API endpoint for updating */
                    $line_route = Params::values()['webservice']['abi_msd']['route']['transfer-order-subform']['list'];
                    $line_url = Globals::soapABIMSDynamicsURL($line_route, $company);
                    /* Generate header update request */
                    $batch_params = "<ns1:Update>" . $data->xmlMSDArrayString(false) . "</ns1:Update>";
                    $batch_request_header = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result_header = Globals::callSoapApiUpdate($url, $batch_request_header, $sales_office_no);
                    /* Save generated request as file backup */
                    Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
                    if (is_string($soap_result_header))
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->withdrawal_code . " " . $soap_result_header, "");
                    elseif ($soap_result_header && property_exists($soap_result_header, "TransferOrderService")) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->withdrawal_code . " successfully re-uploaded.", ""); /* Save log info message */
                        $success = true;
                    }
                    /* Generate detail update request */
                    if (count($data->outgoingNotificationDetail) > 0 && $success) {
                        foreach ($data->outgoingNotificationDetail as $d_key => $detail_param) {
                            $batch_detail_params = "<ns1:Update>" . $detail_param->xmlMSDSubformArrayString() . "</ns1:Update>";
                            $batch_request_detail = new SoapVar($batch_detail_params, XSD_ANYXML);
                            $soap_result_detail = Globals::callSoapApiUpdate($line_url, $batch_request_detail, $sales_office_no);
                            /* Save generated request as file backup */
                            Globals::saveJsonFile($file_name . "_DETAIL-" . ($d_key + 1) . "-" . $key, $detail_param);
                            if (is_string($soap_result_detail)) {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $data->withdrawal_code . " " . $soap_result_detail . ".", "");
                            }
                        }
                    }
                }
                /**
                 * Retrieve Transfer Order Return Slip by No then save MS Dynamics Key both header and lines
                 * Need to read header MS Dynamics Key when updating lines and also while syncing record that already exist
                 * Both header and lines need to save MS Dynamics Key from the response
                 */
                $read_params = "<ns1:Read><ns1:No>" . $data->withdrawal_code . " </ns1:No></ns1:Read>";
                $read_request = new SoapVar($read_params, XSD_ANYXML);
                $soap_result_read = Globals::callSoapApiRead($url, $read_request, $sales_office_no);
                if ($soap_result_read && property_exists($soap_result_read, "TransferOrderService")) {

                    /** When successful, clear all inventory in zones */
                    $soap_client->deleteInventoryOfSalesman($data->employee_code);

                    $header_response = $soap_result_read->TransferOrderService;
                    /* Assign MSD key to outgoing_notification */
                    $batch->msd_synced = 1;
                    $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                    $batch->updated_date = date("Y-m-d H:i:s");
                    UploadConnector::saveMsDynamicsKey($batch, $header_response->Key);

                    /* WMS Outgoing Inventory (Confirmed) */
                    $oi_data = new OutgoingInventoryData();
                    $oi_data->dr_no = $data->withdrawal_code;
                    $oi_data->dr_date = $batch->withdrawal_date;
                    $oi_data->transaction_date = $batch->withdrawal_date;
                    $oi_data->company_code = Globals::getWmsCompanyCode();
                    $oi_data->source_zone_code = $data->transfer_from;
                    $oi_data->destination_zone_code = $data->transfer_to;
                    $oi_data->created_by = UploadConnector::MSD_LOGGER_NAME;
                    $oi_data->updated_by = UploadConnector::MSD_LOGGER_NAME;
                    $oi_data->msd_synced = 1;

                    /* Assign MSD key to outgoing_notification_detail */
                    $oi_data_detail = [];
                    if (property_exists($header_response, "TransferLines") && count($header_response->TransferLines->Transfer_Order_Line) > 0) {
                        foreach ($header_response->TransferLines->Transfer_Order_Line as $response_line) {
                            $detail_model = WMSOutgoingNotificationDetail::where('outgoing_notification_id', '=', $batch->outgoing_notification_id)->where('sku_code', '=', $response_line->Item_No . '-' . $response_line->Unit_of_Measure_Code)->first();
                            if ($detail_model && property_exists($response_line, "Key")) {
                                $detail_model->line_no = $response_line->Line_No;
                                $detail_model->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                $detail_model->updated_date = date("Y-m-d H:i:s");
                                UploadConnector::saveMsDynamicsKey($detail_model, $response_line->Key);

                                /* WMS Outgoing Inventory Detail (Confirmed) */
                                $oi_dt_data = new OutgoingInventoryDetailData;
                                $detail_model->sales_office_code = $batch->sales_office_code;
                                $sku_model_noc = $detail_model->nocSku()->first();

                                if ($sku_model_noc) {
                                    $oi_dt_data->sku_code = $sku_model_noc->code;
                                    $oi_dt_data->uom_code = $sku_model_noc->uom;
                                }
                                $oi_dt_data->planned_quantity = $detail_model->request_quantity;
                                $oi_dt_data->quantity_issued = $detail_model->request_quantity;
                                $oi_dt_data->batch_no = $detail_model->lot_no;
                                $oi_dt_data->expiration_date = $detail_model->expiration_date;
                                $oi_dt_data->line_no = $detail_model->line_no;
                                $oi_dt_data->created_by = UploadConnector::MSD_LOGGER_NAME;
                                $oi_dt_data->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                $oi_data_detail[] = $oi_dt_data;
                            }
                        }
                    }
                    $oi_data->outgoingInventoryDetail = $oi_data_detail;
                    /* Save WMS Outgoing Inventory (Confirmed) */
                    $oi_request = new SoapVar($oi_data->xmlArrayLineStrings(), XSD_ANYXML);
                    $oi_soap_result = (array) $soap_client->saveOutgoingInventory($oi_request);
                    /** Log response message */
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] " . $oi_data->dr_no . " " . $oi_soap_result['message'], ""); /* Save log info message */
                    /** Log total rows */
                    if ($key === 0) {
                        Utils::updateTriggerTotalRows($trigger_id, $oi_soap_result['total_rows']); /* Update trigger total rows */
                    } else {
                        Utils::updateTriggerTotalRows($trigger_id, $oi_soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                    }
                    /** Log failed rows */
                    Utils::updateTriggerFailedRows($trigger_id, $oi_soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                    /** Log response error */
                    if (isset($oi_soap_result['error']) && $oi_soap_result['error'] > 0) {
                        foreach ($oi_soap_result['error'] as $k_e => $v_err_msg) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                        }
                    }
                }

                if ($success) {
                    Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] No new uploaded " . strtolower($module_name) . " found.", ""); /* Save log info message */
        }
    }

    /**
     * Takes data from NOC and sends it to client REST API.
     * (30/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDReturnRequestReservationEntrySalesman($method, $url, $data, $trigger_id = null)
    {
        $module = (new ReservationEntryData)->getModuleByType(1);
        $module_name = (new ReservationEntryData)->getModuleNameByType(1);
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $company_code = Globals::getWmsCompanyCode();

        $salesman = Salesman::select('code')->where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)->get()->toarray();
        $wms_data = WMSOutgoingNotification::where('transaction_type', '=', 1)
            ->where('status', '=', 0) // Not yet decrease stock from salesman zone
            ->where('msd_synced', '=', 1)
            ->whereIn('employee_code', $salesman)
            ->whereBetween('withdrawal_date', [$date_from, $date_to])
            ->get();
        $total_rows = count($wms_data);
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", $module);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            foreach ($wms_data as $key => $batch) {
                $success = false;
                $sales_office_code = $batch->sales_office_code;
                $employee = WMSEmployee::with(['company', 'salesOffice'])
                    ->whereHas('company', function ($q) use ($company_code) {
                        $q->where('code', '=', $company_code);
                    })->whereHas('salesOffice', function ($q) use ($sales_office_code) {
                    $q->where('sales_office_code', '=', $sales_office_code);
                })->where('employee_code', '=', $batch->employee_code)->first();

                $outgoing_inventory = OutgoingInventory::where('dr_no', '=', $batch->withdrawal_code)->first();

                /* Assign each model to data for XML */
                $batch_details = $outgoing_inventory ? $outgoing_inventory->outgoingInventoryDetails()->get() : [];
                if ($batch_details && count($batch_details) > 0) {
                    foreach ($batch_details as $batch_detail) {
                        /* Only return inventory with LotNo */
                        if ($batch_detail->batch_no != "") {
                            $data = new ReservationEntryData;
                            $sku_model = $batch_detail->sku()->first();
                            $sku_model->sales_office_code = $sales_office_code;
                            $sku_model_noc = $sku_model->nocSku()->first();
                            unset($sku_model->sales_office_code);

                            if ($sku_model_noc)
                                $data->sku_code = $sku_model_noc->sys_21;
                            $data->zone_code = $employee->zone()->first()->zone_code;
                            $data->reservation_status = "Surplus";
                            $data->line_no = $batch_detail->line_no;
                            $data->lot_no = $batch_detail->batch_no;
                            $data->entry_no = $batch_detail->entry_no; // Entry No for Transfer Out
                            $data->quantity = -$batch_detail->quantity_issued;
                            $data->source_id = $batch->withdrawal_code;
                            $data->shipment_date = date('Y-m-d', strtotime($batch->withdrawal_date));
                            $data->source_type = 5741;
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
                                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "]-[" . $data->source_id . "|" . $data->sku_code . "|" . $data->lot_no . "] " . $soap_result, "");
                                } elseif ($soap_result && property_exists($soap_result, "ReservationEntriesService")) {
                                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "]-[" . $data->source_id . "|" . $data->sku_code . "|" . $data->lot_no . "] successfully uploaded.", ""); /* Save log info message */
                                    /* Outgoing Inventory Detail */
                                    $batch_detail->entry_no = isset($soap_result->ReservationEntriesService->EntryNo) ? $soap_result->ReservationEntriesService->EntryNo : "";
                                    $batch_detail->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                    $batch_detail->updated_date = date("Y-m-d H:i:s");
                                    if ($batch_detail->save())
                                        $success = true;
                                }
                                Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
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
                                    Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
                                    if (is_string($soap_result))
                                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "]-[" . $data->source_id . "|" . $data->sku_code . "|" . $data->lot_no . "] " . $soap_result, "");
                                    elseif ($soap_result && property_exists($soap_result, "ReservationEntriesService")) {
                                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "]-[" . $data->source_id . "|" . $data->sku_code . "|" . $data->lot_no . "] successfully re-uploaded.", ""); /* Save log info message */
                                        /* Outgoing Inventory Detail */
                                        $batch_detail->entry_no = isset($soap_result->ReservationEntriesService->EntryNo) ? $soap_result->ReservationEntriesService->EntryNo : "";
                                        $batch_detail->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                        $batch_detail->updated_date = date("Y-m-d H:i:s");
                                        if ($batch_detail->save())
                                            $success = true;
                                    }
                                }
                            }
                        }
                    }

                    if ($success) {
                        /* Outgoing Notification */
                        $batch->status = 1; // Decreased stock from salesman zone
                        $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->updated_date = date("Y-m-d H:i:s");
                        /* Outgoing Inventory */
                        $outgoing_inventory->msd_synced = 1;
                        $outgoing_inventory->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $outgoing_inventory->updated_date = date("Y-m-d H:i:s");
                        if ($batch->save() && $outgoing_inventory->save())
                            Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                        Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                    } else {
                        Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] No new uploaded " . strtolower($module_name) . " found.", ""); /* Save log info message */
        }
    }

    /**
     * Takes data from NOC and sends it to client REST API.
     * (30/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDReturnRequestReservationEntryWarehouse($method, $url, $data, $trigger_id = null)
    {
        $module = (new ReservationEntryData)->getModuleByType(2);
        $module_name = (new ReservationEntryData)->getModuleNameByType(2);
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $company_code = Globals::getWmsCompanyCode();

        $salesman = Salesman::select('id')->where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)->get()->toarray();
        $wms_data = WMSOutgoingNotification::where('transaction_type', '=', 1)
            ->where('status', '=', 1) // Decreased stock from salesman zone
            ->where('confirmed', '=', 0) // Not yet transferred stock to warehouse
            ->where('msd_synced', '=', 1)
            ->whereIn('employee_code', $salesman)
            ->whereBetween('withdrawal_date', [$date_from, $date_to])
            ->get();
        $total_rows = count($wms_data);
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", $module);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            foreach ($wms_data as $key => $batch) {
                $success = false;
                $sales_office = WMSSalesOffice::with(['company'])
                    ->whereHas('company', function ($q) use ($company_code) {
                        $q->where('code', '=', $company_code);
                    })->where('sales_office_code', '=', $batch->sales_office_code)->first();

                $incoming_inventory = IncomingInventory::where('dr_no', '=', $batch->withdrawal_code)->first();

                /* Assign each model to data for XML */
                $batch_details = $incoming_inventory ? $incoming_inventory->incomingInventoryDetails()->get() : [];
                if ($batch_details && count($batch_details) > 0) {
                    foreach ($batch_details as $batch_detail) {
                        $data = new ReservationEntryData;
                        $sku_model = $batch_detail->sku()->first();
                        $sku_model->sales_office_code = $batch->sales_office_code;
                        $sku_model_noc = $sku_model->nocSku()->first();
                        unset($sku_model->sales_office_code);

                        if ($sku_model_noc)
                            $data->sku_code = $sku_model_noc->sys_21;
                        $data->zone_code = $sales_office->zone()->first()->zone_code;
                        $data->reservation_status = "Surplus";
                        $data->line_no = $batch_detail->line_no;
                        $data->lot_no = $batch_detail->batch_no;
                        $data->entry_no = $batch_detail->entry_no; // Entry No for Transfer In
                        $data->quantity = $batch_detail->quantity_received;
                        $data->source_id = $batch->withdrawal_code;
                        $data->shipment_date = date('Y-m-d', strtotime($batch->withdrawal_date));
                        $data->source_type = 5741;
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
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "]-[" . $data->source_id . "|" . $data->sku_code . "|" . $data->lot_no . "] " . $soap_result, "");
                            } elseif ($soap_result && property_exists($soap_result, "ReservationEntriesService")) {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "]-[" . $data->source_id . "|" . $data->sku_code . "|" . $data->lot_no . "] successfully uploaded.", ""); /* Save log info message */
                                /* Outgoing Inventory Detail */
                                $batch_detail->entry_no = isset($soap_result->ReservationEntriesService->EntryNo) ? $soap_result->ReservationEntriesService->EntryNo : "";
                                $batch_detail->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                $batch_detail->updated_date = date("Y-m-d H:i:s");
                                if ($batch_detail->save())
                                    $success = true;
                            }
                            Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
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
                                Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
                                if (is_string($soap_result))
                                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "]-[" . $data->source_id . "|" . $data->sku_code . "|" . $data->lot_no . "] " . $soap_result, "");
                                elseif ($soap_result && property_exists($soap_result, "ReservationEntriesService")) {
                                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "]-[" . $data->source_id . "|" . $data->sku_code . "|" . $data->lot_no . "] successfully re-uploaded.", ""); /* Save log info message */
                                    /* Outgoing Inventory Detail */
                                    $batch_detail->entry_no = isset($soap_result->ReservationEntriesService->EntryNo) ? $soap_result->ReservationEntriesService->EntryNo : "";
                                    $batch_detail->updated_by = UploadConnector::MSD_LOGGER_NAME;
                                    $batch_detail->updated_date = date("Y-m-d H:i:s");
                                    if ($batch_detail->save())
                                        $success = true;
                                }
                            }
                        }
                    }

                    if ($success) {
                        /* Outgoing Notification */
                        $batch->confirmed = 1; // Transferred stock to warehouse
                        $batch->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $batch->updated_date = date("Y-m-d H:i:s");
                        /* Outgoing Inventory */
                        $incoming_inventory->msd_synced = 1;
                        $incoming_inventory->updated_by = UploadConnector::MSD_LOGGER_NAME;
                        $incoming_inventory->updated_date = date("Y-m-d H:i:s");
                        if ($batch->save() && $incoming_inventory->save())
                            Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
                        Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                    } else {
                        Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] No new uploaded " . strtolower($module_name) . " found.", ""); /* Save log info message */
        }
    }

    /**
     * Takes data from NOC and sends it to client REST API.
     * (30/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDNewCustomerCreationRequest($method, $url, $data, $trigger_id = null, $is_auto = 0)
    {
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $salesman_code = isset($data['salesman_code']) ? $data['salesman_code'] : "";
        $location_code = isset($data['location_code']) ? $data['location_code'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";

        $salesman = Salesman::select('code')->where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)->get();
        $build = Location::where('sales_office_no', '=', $sales_office_no)
            ->whereIn('salesman_code', $salesman)
            ->whereHas('locationDetail', function ($q) {
                $q->where('approval_status', '=', 0)->whereNested(function ($q) {
                    $q->where('ms_dynamics_key', '=', '')->orWhereNull('ms_dynamics_key');
                });
            })
			->where(function ($query) use ($date_from, $date_to) {
				$query->whereBetween('added_when', [$date_from, $date_to])
					  ->orWhereBetween('updated_when', [$date_from, $date_to]);
			});
        if ($location_code != "")
            $build = $build->where('code', '=', $location_code);
        if ($salesman_code != "")
            $build = $build->where('salesman_code', '=', $salesman_code);
        $noc_data = count($salesman) > 0 ? $build->get() : [];
        $total_rows = count($noc_data);
        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", NewCustomerRequestData::MODULE_NEW_CUSTOMER_REQUEST);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */


        /** Get all regions from MSD*/
        $region_route = Params::values()['webservice']['abi_msd']['route']['sales-region']['list'];
        $region_url = Globals::soapABIMSDynamicsURL($region_route, $company);
        $msd_soap_result = Globals::callSoapApiReadMultiple($region_url, array(), $sales_office_no);
        $msd_region_result = null;

        if (isset($msd_soap_result->ReadMultiple_Result->Region)) {
            if (count($msd_soap_result->ReadMultiple_Result->Region) == 1) {
                $msd_region_result = array($msd_soap_result->ReadMultiple_Result->Region);
            } else if (count($msd_soap_result->ReadMultiple_Result->Region) > 1) {
                $msd_region_result = $msd_soap_result->ReadMultiple_Result->Region;
            }
        }
        if ($total_rows > 0) {
            foreach ($noc_data as $key => $batch) {
                $success = false;
                $batch_detail = $batch->locationDetail()->first();

                $data = new NewCustomerRequestData;
                UploadConnector::assignToData($data, $batch_detail);
                UploadConnector::assignToData($data, $batch);
                $salesman_model = Salesman::where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)
                    ->where('code', '=', $batch->salesman_code)->first();
                $store_type_model = $batch->storeType()->first();
                $sales_office_mod = SalesOffice::where('no', '=', $batch->sales_office_no)->first();

                if ($sales_office_mod)
                    $data->sales_office_no = $sales_office_mod->short_desc;
                $barangay_mod = $batch->barangay()->first();
                if ($barangay_mod)
                    $data->barangay_code = $barangay_mod->name;
                $region_mod = $batch->region()->first();
                if ($region_mod) {
                    $sales_region = $region_mod->salesRegion()->first();
                    $region_result = "";
                    //** Convert code from sales region based on MSD Region downloaded */
                    if ($msd_region_result && $sales_region) {
                        foreach ($msd_region_result as $region) {
                            if (isset($region->WIN_Description) && strpos(strtoupper($region->WIN_Description), strtoupper($sales_region->name))) {
                                $region_result = $region->WIN_Code;
                                break;
                            }
                        }
                    }
                    $data->region_code = ($region_result != "") ? $region_result : "ASC";
                }
                $district_mode = $batch->district()->first();
                if ($district_mode) {
                    $data->district_code = $district_mode->code;
                }
                $province_mod = $batch->province()->first();
                if ($province_mod)
                    $data->province_code = $province_mod->name;

                $municipal_mode = $batch->municipal()->first();
                if ($municipal_mode) {
                    $data->municipal_code = $municipal_mode->code;
                }

                /**Create default values for _group values */
                if (empty($data->gen_bus_posting_group)) {
                    $data->gen_bus_posting_group = "DOMESTIC";
                }
                if (empty($data->vat_bus_posting_group)) {
                    $data->vat_bus_posting_group = "PVT";
                }
                if (empty($data->wht_business_posting_group)) {
                    $data->wht_business_posting_group = "NON-EWT";
                }
                if (empty($data->customer_posting_group)) {
                    $data->customer_posting_group = "DOMESTIC";
                }
                if (empty($data->currency_code)) {
                    $data->currency_code = "PHP";
                }
                if (empty($data->customer_price_group)) {
                    $data->customer_price_group = "NATIONAL";
                }
                if (!isset($data->prices_including_vat)) {
                    $data->prices_including_vat = true;
                }

                if ($salesman_model) {
                    $sales_group_code = $salesman_model->salesGroup()->first();
                    if ($sales_group_code) {
                        $data->sales_group_code = $sales_group_code->code;
                    }
                    $data->salesman_type = $salesman_model->salesman_type;
                    $data->territory_code = $salesman_model->code;
                    $data->zone_code = $salesman_model->zone;
                }

                if ($store_type_model) {
                    $data->sub_channel = $store_type_model->code;
                    $data->store_type_description = $store_type_model->description;
                }

                if (!empty($batch->service_call_days)) {
                    $exploded_days = explode(",", $batch->service_call_days);
                    foreach ($exploded_days as $number) {
                        switch ($number) {
                            case "1":
                                $data->sunday = 1;
                                break;
                            case "2":
                                $data->monday = 1;
                                break;
                            case "3":
                                $data->tuesday = 1;
                                break;
                            case "4":
                                $data->wednesday = 1;
                                break;
                            case "5":
                                $data->thursday = 1;
                                break;
                            case "6":
                                $data->friday = 1;
                                break;
                            case "7":
                                $data->saturday = 1;
                                break;
                        }
                    }
                }

                //constants
                $data->country_region_code = "PH";

                /* Create new data in MSD */
                $batch_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = Globals::callSoapApiCreate($url, $batch_request, $sales_office_no);

                /** Log response message */
                if (is_string($soap_result)) {
					
					if($is_auto == 0) {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . NewCustomerRequestData::MODULE_NAME_NEW_CUSTOMER_REQUEST . "] " . $batch->code . " " . $soap_result, "");
					}
                } elseif ($soap_result && property_exists($soap_result, "CustomerRequestCard")) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . NewCustomerRequestData::MODULE_NAME_NEW_CUSTOMER_REQUEST . "]-[" . $batch->code . "] successfully uploaded.", ""); /* Save log info message */
                    $batch_detail->ms_dynamics_key = isset($soap_result->CustomerRequestCard->Key) ? $soap_result->CustomerRequestCard->Key : NULL;
                    $batch_detail->updated_by = UploadConnector::MSD_LOGGER_NAME;
                    $batch_detail->updated_when = date("Y-m-d H:i:s");
                    $batch_detail->approved_when = date("Y-m-d H:i:s");
                    if ($batch_detail->save())
                        $success = true;
                }
				if($is_auto == 0) {
					Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
				}
            }
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . NewCustomerRequestData::MODULE_NAME_NEW_CUSTOMER_REQUEST . "] End " . strtolower(NewCustomerRequestData::MODULE_NAME_NEW_CUSTOMER_REQUEST) . " upload.", ""); /* Save log info message */

            if ($success) {
                Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
            } else {
                Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . NewCustomerRequestData::MODULE_NAME_NEW_CUSTOMER_REQUEST . "] No new uploaded " . strtolower(NewCustomerRequestData::MODULE_NAME_NEW_CUSTOMER_REQUEST) . " found.", ""); /* Save log info message */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . NewCustomerRequestData::MODULE_NAME_NEW_CUSTOMER_REQUEST . "] End " . strtolower(NewCustomerRequestData::MODULE_NAME_NEW_CUSTOMER_REQUEST) . " upload.", ""); /* Save log info message */
        }
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */

    }

	/**
     * Takes data from NOC and sends it to client REST API.
     * (04/01/2025)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDLocationUpdate($method, $url, $data, $trigger_id = null)
    {
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $location_code = isset($data['location']) ? $data['location'] : "";
		$module_name = "Location Detail Update";
			
		
        $loc_query = Location::where('sales_office_no', '=', $sales_office_no)->
		where('code', '=', $location_code)->where('deleted', '=', 0);
        $loc_obj = $loc_query->first();
		if($loc_obj){

			$loc_detail = $loc_obj->locationDetail()->first();

            $msd_data_val = new LocationData();
			UploadConnector::assignToData($msd_data_val, $loc_obj);
			UploadConnector::assignToData($msd_data_val, $loc_detail);
			$params = $msd_data_val->xmlArrayUpdateLocation();
			$request = new SoapVar($params, XSD_ANYXML);
			$soap_result = Globals::callSoapApiCreate($url, $request, $sales_office_no);

            if (isset($soap_result->CustomerModifyRequestCard)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] outlet " .$location_code . " update request created", "");
			}
			else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . $module_name . "] outlet " . $soap_result, "");
			}
                  
			return $soap_result;
		}
	}

    /**
     * Takes data from NOC and sends it to client REST API.
     * (30/09/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCafCreditLimit($method, $url, $data, $trigger_id = null)
    {
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $salesman_code = isset($data['salesman_code']) ? $data['salesman_code'] : "";

        $caf_query = CAF::where('status', '=', 0)->
            whereBetween('date', [$date_from, $date_to]);

        if ($salesman_code != "") {
            $caf_query->where('salesman_code', '=', $salesman_code);
        }

        $caf_array = $caf_query->get();
        Utils::updateTriggerTotalRows($trigger_id, count($caf_array)); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_ONGOING); /* Update trigger status */

        foreach ($caf_array as $caf) {

            if ($caf->cccr_no == "" || $caf->cccr_no == NULL) {
                $location_caf = $caf->location()->first();
                if (!$location_caf) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CAF_CREDIT_LIMIT . "] outlet code " . $caf->registered_name . "(" . $caf->location_id . ") not found", "");
                    continue;
                }
                $location_details_caf = LocationDetail::where('location_id', '=', $location_caf->id)->first();
                if (!$location_details_caf) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CAF_CREDIT_LIMIT . "] outlet details code " . $caf->registered_name . "(" . $caf->location_id . ") not found", "");
                    continue;
                }

                $loc_msd_key = $location_details_caf->ms_dynamics_key;
                $loc_code = $location_caf->code;
                $question_post_dated = CAFApplCreditReqQuestion::select('id')->
                    where('question', '=', 'Post Dated Checks')->
                    first();
                $question_else = CAFApplCreditReqQuestion::select('id')->
                    where('title', '=', 'Collaterals')->
                    where('question', '!=', 'Post Dated Checks')->
                    pluck('id')->
                    toArray();

                if ($question_post_dated)
                    $question_answer_post_dated = CAFApplCreditReqAnswer::select()->
                        where('caf_id', '=', $caf->id)->
                        where('question_id', '=', $question_post_dated->id)->
                        first();
                if ($question_else)
                    $question_answer_else = CAFApplCreditReqAnswer::select()->
                        where('caf_id', '=', $caf->id)->
                        whereIn('question_id', $question_else)->
                        first();

                $credit_limit = 0;

                //credit limit
                if (isset($question_answer_post_dated->answer) && $question_answer_post_dated->answer == 1) {
                    $credit_limit = 20000;
                } else if (isset($question_answer_else->answer) && $question_answer_else->answer == 1) {
                    $credit_limit = 500000;
                }

                if ($credit_limit == 0) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CAF_CREDIT_LIMIT . "] Credit Limit is 0", "");
                    continue;
                }

                $params = "<ns1:Create> <ns1:CustomerModifyRequestCard>";
                $params .= " <ns1:Key>" . $loc_msd_key . "</ns1:Key>";
                $params .= " <ns1:Customer_No>" . $loc_code . "</ns1:Customer_No>";
                $params .= " <ns1:Credit_Limit_LCY>" . $credit_limit . "</ns1:Credit_Limit_LCY>";
                $params .= " <ns1:WIN_Credit_Limit_Containers>" . $credit_limit . "</ns1:WIN_Credit_Limit_Containers>";
                $params .= "</ns1:CustomerModifyRequestCard> </ns1:Create>";
                $request = new SoapVar($params, XSD_ANYXML);
                $soap_result = Globals::callSoapApiCreate($url, $request, $sales_office_no);
                if (is_string($soap_result)) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CAF_CREDIT_LIMIT . "] " . $caf->code . " - " . $soap_result, "");
                } else {
                    $cccr_no = NULL;
                    if (isset($soap_result->CustomerModifyRequestCard)) {
                        $cccr_no = $soap_result->CustomerModifyRequestCard->CCCR_No;
                        if ($cccr_no) {
                            $caf->cccr_no = $cccr_no;
                            if ($caf->save()) {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CAF_CREDIT_LIMIT . "] " . $caf->code . " - successfully saved cccr_no", "");
                            }
                        }
                    }
                }
            } else {
                $params = "<ns1:Read> <ns1:CCCR_No>" . $caf->cccr_no . "</ns1:CCCR_No></ns1:Read> ";
                $request = new SoapVar($params, XSD_ANYXML);

                $soap_result = Globals::callSoapApiRead($url, $request, $sales_office_no);


                if (is_string($soap_result)) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CAF_CREDIT_LIMIT . "] " . $caf->code . " - " . $soap_result, "");
                } else {
                    if ($soap_result->CustomerModifyRequestCard->WIN_Approved_By !== null || $soap_result->CustomerModifyRequestCard->WIN_Approved_Date !== null) {
                        $location_caf = $caf->location()->first();
                        if (!$location_caf) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CAF_CREDIT_LIMIT . "] outlet code " . $caf->registered_name . "(" . $caf->location_id . ") not found", "");
                            continue;
                        }
                        $caf->status = 1;
                        $location_caf->limit_fulls = $soap_result->CustomerModifyRequestCard->Credit_Limit_LCY;
                        $location_caf->limit_mts = $soap_result->CustomerModifyRequestCard->WIN_Credit_Limit_Containers;
                        if ($caf->save() && $location_caf->save()) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CAF_CREDIT_LIMIT . "] " . $caf->code . " - successfully updated status to approve", "");
                        }
                    }
                }
            }
        }
    }


    /**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (08/06/2025)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesShipment($method, $url, $data, $trigger_id = null)
    {
        $company = isset($data['company']) ? $data['company'] : "";
		if($company == null) {
					return;
		}
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		$sales_office_obj = SalesOffice::where('no', '=', $sales_office_no)->first();
		$short_desc = "";
		if($sales_office_obj) {
			$short_desc = $sales_office_obj->short_desc;
		}
		foreach($data['order_list'] as $order) {
			$object = new SalesShipmentData;
			$object->order_no = $order['sales_order_number'];

			$read_route = Params::values()['webservice']['abi_msd']['route']['sales-shipment']['list'];
			$read_url = Globals::soapABIMSDynamicsURL($read_route, $company);
			$read_data = array(
				'company' => $company,
				'sales_office' => $sales_office_no,
				'params' => array(
					'Order_No' =>  $order['sales_order_number']
				)
			);
			$msd_soap_result = Globals::callSoapApiReadMultiple($read_url, $read_data, $sales_office_no);
			$final_result = [];
			if(isset($msd_soap_result->ReadMultiple_Result->PostedSalesShipmentService) and is_array($msd_soap_result->ReadMultiple_Result->PostedSalesShipmentService)){
				$final_result = $msd_soap_result->ReadMultiple_Result->PostedSalesShipmentService[0];
			} elseif(isset($msd_soap_result->ReadMultiple_Result->PostedSalesShipmentService)) {
				$final_result = $msd_soap_result->ReadMultiple_Result->PostedSalesShipmentService;
			} else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesShipmentData::MODULE_NAME_SALES_SHIPMENT . "] " . $order['sales_order_number'] . " not found.", "");
                continue;
			}
			$object->msd_key = $final_result->Key;
			$object->pick_no = $data['shipment_number'];
			$object->driver_no = $data['driver_no'];
			$object->helper_no = $data['helper1_id'];
			$object->helper2_no = $data['helper2_id'];
			$params = $object->createParamsJson();

			$update_route = Params::values()['webservice']['abi_msd']['route']['sales-shipment']['list'];
			$update_url = Globals::soapABIMSDynamicsURL($update_route, $company);
			$update_data = array(
				'PostedSalesShipmentService' => $params
			);
			$update_result = Globals::callSoapApiUpdate($update_url, $update_data, $sales_office_no);
			if(is_string($update_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesShipmentData::MODULE_NAME_SALES_SHIPMENT . "] " . $update_result, "");
			}
			else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesShipmentData::MODULE_NAME_SALES_SHIPMENT . "] Updated " . $order['sales_order_number'] . " shipment.", "");
			}
		}
    }

    /**
     * Save MSD keys to database
     * (28/06/2022)
     * 
     * @param saveObject the model to be saved at
     * @param key the string to be saved
     * 
     * @return void
     *  */
    public static function saveMsDynamicsKey($saveObject, $key)
    {
        $saveObject->ms_dynamics_key = $key;
        $saveObject->save();
    }

    /**
     * Save model attributes in data container
     * (30/06/2022)
     * 
     * @param data the data container
     * @param model the model
     * 
     * @return data the data container
     */
    public static function assignToData($data, $model)
    {
        foreach ($model->getAttributes() as $key => $value) {
            if (property_exists($data, $key))
                $data->$key = $value;
        }
        return $data;
    }
}