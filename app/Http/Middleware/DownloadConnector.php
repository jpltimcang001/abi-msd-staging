<?php

namespace App\Http\Middleware;

use App\Data\BalanceData as BalanceData;
use App\Data\BalanceEmptiesData as BalanceEmptiesData;
use App\Data\CustomerDiscountGroupData as CustomerDiscountGroupData;
use App\Data\CustomerPostingGroupData as CustomerPostingGroupData;
use App\Data\CustomerPriceGroupData as CustomerPriceGroupData;
use App\Data\DiscountCaseData;
use App\Data\DistributionChannelData as DistributionChannelData;
use App\Data\GenBusPostingGroupData as GenBusPostingGroupData;
use App\Data\InventoryData as InventoryData;
use App\Data\InvoiceData as InvoiceData;
use App\Data\InvoiceDetailData;
use App\Data\LocationData as LocationData;
use App\Data\OutgoingInventoryData as OutgoingInventoryData;
use App\Data\OutgoingInventoryDetailData as OutgoingInventoryDetailData;
use App\Data\PaymentMethodData as PaymentMethodData;
use App\Data\PaymentTermsData as PaymentTermsData;
use App\Data\PromotionBudgetData as PromotionBudgetData;
use App\Data\PromotionData as PromotionData;
use App\Data\PromotionDetailData as PromotionDetailData;
use App\Data\PromotionDiscountLineData as PromotionDiscountLineData;
use App\Data\PromotionFocData as PromotionFocData;
use App\Data\PromotionLocationData as PromotionLocationData;
use App\Data\PserData;
use App\Data\SalesmanData as SalesmanData;
use App\Data\SalesmanTypeData as SalesmanTypeData;
use App\Data\SalesOfficeData as SalesOfficeData;
use App\Data\SalesOrderData;
use App\Data\SalesPriceData as SalesPriceData;
use App\Data\SKUData as SKUData;
use App\Data\SubChannelData as SubChannelData;
use App\Data\VATBusPostingGroupData as VATBusPostingGroupData;
use App\Data\ZoneData as ZoneData;
use App\Model\noc\BalanceEmpties;
use App\Model\noc\DistributionChannel as DistributionChannel;
use App\Model\noc\Invoice;
use App\Model\noc\InvoiceDetails as InvoiceDetail;
use App\Model\noc\InvoiceRefundDetail;
use App\Model\noc\Location;
use App\Model\noc\PickNote;
use App\Model\noc\Salesman as Salesman;
use App\Model\noc\SalesOffice as SalesOffice;
use App\Model\noc\SalesOrder;
use App\Model\noc\SalesOrderDetail;
use App\Model\noc\SalesOrderReturnable;
use App\Model\noc\Sku;
use App\Model\noc\TempCollectionCash;
use App\Model\noc\TempCollectionCashBreakdown;
use App\Model\wms\InventoryBreakdown;
use App\Model\wms\OutgoingNotification;
use App\Utils\DecimalTruncator;
use App\Utils\Globals;
use App\Utils\Params;
use App\Utils\Utils;
use DB;
use Illuminate\Database\Eloquent\Model;
use SoapVar;

class DownloadConnector extends Model
{
    /** Trigger Status */
    const STATUS_PENDING = "PENDING";
    const STATUS_ONGOING = "ONGOING";
    const STATUS_DONE = "DONE";
    /* Module - Download */
    const MODULE_LOCATION_DISCOUNT = "LOCATION DISCOUNT";
    const MODULE_PENDING_LOCATION_CREATION = "NEW LOCATION";
    const MODULE_LOCATION_MULTIPLE = "LOCATION MULTIPLE";
    const MODULE_INVENTORY_MULTIPLE = "INVENTORY MULTIPLE";
    const MODULE_INVOICE_MULTIPLE = "INVOICE MULTIPLE";
    /* Module Name - Download */
    // const MODULE_NAME_LOCATION = "Customer";
    const MODULE_NAME_LOCATION_DISCOUNT = "Customer Discount";
    const MODULE_NAME_PENDING_LOCATION_CREATION = "Pending Customer Creation Request";
    const MODULE_NAME_LOCATION_MULTIPLE = "Batch Customer";
    const MODULE_NAME_INVENTORY_MULTIPLE = "Batch Stocks";
    const MODULE_NAME_INVOICE_MULTIPLE = "Batch Invoice";
    /* Log Level */
    const ERROR = "ERROR";
    const INFO = "INFO";
    const MSD_LOGGER_NAME = "NOCMSD.LOGGER";
    const BATCH_LIMIT = 100;
    const PATH = "DOWNLOAD/";

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (07/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    // public static function syncMSDCustomer($method, $url, $data, $trigger_id = null)
    // {
		// gc_enable();
        // set_time_limit(0);
        // ini_set('memory_limit', '-1');
        // $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        // $batch_enabled = isset($data['batch_enabled']) && $data['batch_enabled'] ? true : false;
        // $short_desc = isset($data['params']['Global_Dimension_1_Code']) ? $data['params']['Global_Dimension_1_Code'] : "";
        // $salesman_code = isset($data['params']['Salesperson_Code']) ? $data['params']['Salesperson_Code'] : "";
		// $location_code =  isset($data['params']['No']) ? $data['params']['No'] : "";
		// $date_from =  isset($data['params']['Last_Date_Modified']) ? $data['params']['Last_Date_Modified'] : "";		
        
		// DB::table('location')
		// ->join('location_details', 'location.id', '=', 'location_details.location_id')
		// ->where(function($q) { 
			// $q->whereNotNull('location_details.ms_dynamics_key')
			  // ->orWhere('location_details.ms_dynamics_key', '<>', '');
		// })
		// ->when($sales_office_no, function ($query) use ($sales_office_no) {
			// $query->where('location.sales_office_no', '=', $sales_office_no);
		// })
		// ->when($salesman_code, function ($query) use ($salesman_code) {
			// $query->where('location.salesman_code', '=', $salesman_code);
		// })
		// ->when($location_code, function ($query) use ($location_code) {
			// $query->where('location.code', '=', $location_code);
		// })
		// ->update([
			// 'location.deleted' => 1,
			// 'location.deleted_by' => DownloadConnector::MSD_LOGGER_NAME,
			// 'location.deleted_when' => date('Y-m-d H:i:s'),
		// ]);
		
		// print_r("[" . date("Y-m-d H:i:s") . "] Downloading.\n");	
		
        // $soap_client = Globals::soapClientABINOCCentralWS();
        // $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
		// if($msd_soap_result) {
			// print_r("[" . date("Y-m-d H:i:s") . "] Downloaded Data from " . $salesman_code . "\n");
			
		// }
		// $msd_data = array();
       
        // if (isset($msd_soap_result->ReadMultiple_Result->Customer)) {
            // $msd_init_data = $msd_soap_result->ReadMultiple_Result->Customer;
			// file_put_contents(storage_path("customers-readmultiple-result.json"), json_encode($msd_init_data, JSON_PRETTY_PRINT));
            // if (count($msd_init_data) === 1) {
                // $msd_init_data = array($msd_soap_result->ReadMultiple_Result->Customer);
            // }
			// print_r("[" . date("Y-m-d H:i:s") . "] Found " . strval(count($msd_init_data) ) . " Customer Data.\n");
            // foreach ($msd_init_data as $value) {
                // $msd_data_val = new LocationData();
                // $service_call_days = array();

                // foreach (get_object_vars($value) as $att_key => $att_value) {
                    // switch ($att_key) {
                        // case "Sunday":
                            // if ($att_value == 1)
                                // $service_call_days[] = "1";
                            // break;
                        // case "Monday":
                            // if ($att_value == 1)
                                // $service_call_days[] = "2";
                            // break;
                        // case "Tuesday":
                            // if ($att_value == 1)
                                // $service_call_days[] = "3";
                            // break;
                        // case "Wednesday":
                            // if ($att_value == 1)
                                // $service_call_days[] = "4";
                            // break;
                        // case "Thursday":
                            // if ($att_value == 1)
                                // $service_call_days[] = "5";
                            // break;
                        // case "Friday":
                            // if ($att_value == 1)
                                // $service_call_days[] = "6";
                            // break;
                        // case "Saturday":
                            // if ($att_value == 1)
                                // $service_call_days[] = "7";
                            // break;
						// case "Blocked":
							// if($att_value == "All") {
								// $msd_data_val->deleted = 1;
							// }
							// break;
                        // default:
                            // $msd_data_val->setMSD($att_key, $att_value);
                            // break;
                    // }
                // }
                // $msd_data_val->approval_status = 1;
                // $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                // $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                // $msd_data_val->synced_when = date('Y-m-d H:i:s');
                // $msd_data_val->service_call_days = implode(";", $service_call_days);
                // $msd_data[] = $msd_data_val;
            // }
            // /* Save generated response as file backup */
            // $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", LocationData::MODULE_LOCATION);
            // Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->Customer);
        // }
		// gc_collect_cycles();
        // $total_rows = count($msd_data);
        // if (!$batch_enabled)
            // Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        // Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */
        // if ($total_rows > 0) {
            // $batch_data = array_chunk($msd_data, 50); // Create batch

            // foreach ($batch_data as $key => $batch) {

                // $batch_params = '<GetBatchLocationCriteria xsi:type="urn:GetLocationCriteriaArray" soap-enc:arrayType="urn:GetLocationCriteria[]">';
                // foreach ($batch as $line) {
                    // $batch_params .= $line->xmlArrayLineStrings();
                // }
                // $batch_params .= '</GetBatchLocationCriteria>';
                // $batch_request = new SoapVar($batch_params, XSD_ANYXML);

                // $soap_result = (array) $soap_client->saveBatchLocation($batch_request);
                // /** Log response message */
                // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . LocationData::MODULE_NAME_LOCATION . "]-[" . $short_desc . "|" . json_encode($salesman_code) . "] " . $soap_result['message'], ""); /* Save log info message */
                
				// print_r("[" . date("Y-m-d H:i:s") . "] " . $soap_result['message'] . "\n");
				// /** Log total rows */
                // if ($key === 0) {
                    // Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], $batch_enabled); /* Update trigger total rows */
                // } else {
                    // Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                // }
                // /** Log failed rows */
                // Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                // /** Log response error */
                // if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    // foreach ($soap_result['error'] as $k_e => $v_err_msg) {
						// print_r("[" . date("Y-m-d H:i:s") . "] " . $v_err_msg . "\n");
                        // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . LocationData::MODULE_NAME_LOCATION . "]-[" . $short_desc . "|" . json_encode($salesman_code) . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                    // }
                // }
				// unset($batch_params);
				// unset($batch_request);
				// unset($soap_result);
				// unset($batch);

				// gc_collect_cycles();
            // }
			// unset($batch_data);
			// unset($msd_data);

			// gc_collect_cycles();
        // } else {
            // Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . LocationData::MODULE_NAME_LOCATION . "]-[" . $short_desc . "|" . json_encode($salesman_code) . "] No maintenance found.", ""); /* Save log info message */
        // }
    // }
	public static function syncMSDCustomer($method, $url, $data, $trigger_id = null)
	{
		set_time_limit(0);
		ini_set('memory_limit', '-1');

		if (function_exists('gc_enable')) {
			gc_enable();
		}

		$sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		$batch_enabled = isset($data['batch_enabled']) && $data['batch_enabled'] ? true : false;
		$short_desc = isset($data['params']['Global_Dimension_1_Code']) ? $data['params']['Global_Dimension_1_Code'] : "";
		$salesman_code = isset($data['params']['Salesperson_Code']) ? $data['params']['Salesperson_Code'] : "";
		$location_code = isset($data['params']['No']) ? $data['params']['No'] : "";

		// Soft delete
		DB::table('location')
			->join('location_details', 'location.id', '=', 'location_details.location_id')
			->where(function ($q) {
				$q->whereNotNull('location_details.ms_dynamics_key')
				  ->orWhere('location_details.ms_dynamics_key', '<>', '');
			})
			->when($sales_office_no, function ($query) use ($sales_office_no) {
				$query->where('location.sales_office_no', '=', $sales_office_no);
			})
			->when($salesman_code, function ($query) use ($salesman_code) {
				$query->where('location.salesman_code', '=', $salesman_code);
			})
			->when($location_code, function ($query) use ($location_code) {
				$query->where('location.code', '=', $location_code);
			})
			->update(array(
				'location.deleted' => 1,
				'location.deleted_by' => DownloadConnector::MSD_LOGGER_NAME,
				'location.deleted_when' => date('Y-m-d H:i:s'),
			));

		$soap_client = Globals::soapClientABINOCCentralWS();
	
		$pageSize = 20;
		$bookmarkKey = null;
		$hasMoreData = true;
		$iteration = 0;

		print_r("[" . date("Y-m-d H:i:s") . "] Starting Paginated Download.\n");

		do {

			$iteration++;

			$msd_soap_result = Globals::callSoapApiReadMultiple(
				$url,
				$data,
				$sales_office_no,
				$pageSize,
				$bookmarkKey
			);

			if (
				!$msd_soap_result ||
				!isset($msd_soap_result->ReadMultiple_Result->Customer)
			) {
				$hasMoreData = false;

				if ($iteration == 1) {
					Utils::saveLog(
						$trigger_id,
						$sales_office_no,
						date("Y-m-d H:i:s"),
						DownloadConnector::ERROR,
						DownloadConnector::MSD_LOGGER_NAME,
						"No maintenance found.",
						""
					);
				}

				break;
			}

			$msd_init_data = $msd_soap_result->ReadMultiple_Result->Customer;

			if (is_object($msd_init_data)) {
				$msd_init_data = array($msd_init_data);
			}

			print_r("[" . date("Y-m-d H:i:s") . "] Page {$iteration}: Processing " . count($msd_init_data) . " records.\n");

			$msd_data = array();

			foreach ($msd_init_data as $value) {

				$msd_data_val = new LocationData();
				$service_call_days = array();

				foreach (get_object_vars($value) as $att_key => $att_value) {

					switch ($att_key) {

						case "Key":
							$bookmarkKey = $att_value;
							$msd_data_val->ms_dynamics_key = $att_value;
							break;

						case "Sunday":
							if ($att_value == 1) $service_call_days[] = "1";
							break;

						case "Monday":
							if ($att_value == 1) $service_call_days[] = "2";
							break;

						case "Tuesday":
							if ($att_value == 1) $service_call_days[] = "3";
							break;

						case "Wednesday":
							if ($att_value == 1) $service_call_days[] = "4";
							break;

						case "Thursday":
							if ($att_value == 1) $service_call_days[] = "5";
							break;

						case "Friday":
							if ($att_value == 1) $service_call_days[] = "6";
							break;

						case "Saturday":
							if ($att_value == 1) $service_call_days[] = "7";
							break;

						case "Blocked":
							if ($att_value == "All") {
								$msd_data_val->deleted = 1;
							}
							break;

						default:
							$msd_data_val->setMSD($att_key, $att_value);
							break;
					}
				}

				$msd_data_val->approval_status = 1;
				$msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
				$msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
				$msd_data_val->synced_when = date('Y-m-d H:i:s');
				$msd_data_val->service_call_days = implode(";", $service_call_days);

				$msd_data[] = $msd_data_val;

				unset($service_call_days);
			}

			if (count($msd_data) > 0) {

				$batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT);

				foreach ($batch_data as $batch) {

					$batch_params = '<GetBatchLocationCriteria xsi:type="urn:GetLocationCriteriaArray" soap-enc:arrayType="urn:GetLocationCriteria[]">';

					foreach ($batch as $line) {
						$batch_params .= $line->xmlArrayLineStrings();
					}

					$batch_params .= '</GetBatchLocationCriteria>';

					$batch_request = new SoapVar($batch_params, XSD_ANYXML);

					$soap_result = (array)$soap_client->saveBatchLocation($batch_request);

					Utils::updateTriggerTotalRows(
						$trigger_id,
						$soap_result['total_rows'],
						true
					);

					Utils::updateTriggerFailedRows(
						$trigger_id,
						$soap_result['failed_rows'],
						true
					);

					// FREE MEMORY AFTER EVERY SOAP CALL
					unset(
						$batch_params,
						$batch_request,
						$soap_result,
						$batch,
						$line
					);

					if (function_exists('gc_collect_cycles')) {
						gc_collect_cycles();
					}
				}

				unset($batch_data);

				foreach ($msd_data as $k => $obj) {
					unset($msd_data[$k]);
				}

				unset($msd_data);

				if (function_exists('gc_collect_cycles')) {
					gc_collect_cycles();
				}
			}

			if (count($msd_init_data) < $pageSize) {
				$hasMoreData = false;
			}

			// FREE PAGE MEMORY
			unset(
				$msd_init_data,
				$msd_soap_result,
				$value,
				$att_key,
				$att_value,
				$msd_data_val
			);

			if (function_exists('gc_collect_cycles')) {
				gc_collect_cycles();
			}

			print_r(sprintf(
				"[%s] Memory: %.2f MB | Peak: %.2f MB\n",
				date('Y-m-d H:i:s'),
				memory_get_usage(true) / 1048576,
				memory_get_peak_usage(true) / 1048576
			));

		} while ($hasMoreData);

		unset($soap_client);

		if (function_exists('gc_collect_cycles')) {
			gc_collect_cycles();
		}

		Utils::updateTriggerStatus(
			$trigger_id,
			DownloadConnector::STATUS_DONE
		);

		print_r("[" . date("Y-m-d H:i:s") . "] Sync Finished.\n");
	}

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (04/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPromotion($method, $url, $data, $trigger_id = null)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        /* Header */

        if (isset($data['params']['No'])) {
            unset($data['params']['No']);
        }
        $msd_promo_data = [];
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionList)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionList) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionList as $value) {
                    $msd_data_val = new PromotionData();
					if($value->Scheme_Type == "Item_Trade_Offer")
						continue;
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_promo_data[$msd_data_val->no] = $msd_data_val;
                }
            } else {
                $msd_data_val = new PromotionData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PromotionList) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_promo_data[$msd_data_val->no] = $msd_data_val;
            }
        }
        /* Location */
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-customer']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionCustomerList)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionCustomerList) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionCustomerList as $value) {
                    $msd_data_val = new PromotionLocationData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->sales_office_no = "";
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    if (isset($msd_promo_data[$msd_data_val->promotion_no])) {
                        $msd_promo_data[$msd_data_val->promotion_no]->promotionLocation[] = $msd_data_val;
                    }
                }
            } else {
                $msd_data_val = new PromotionLocationData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PromotionCustomerList) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->sales_office_no = "";
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                if (isset($msd_promo_data[$msd_data_val->promotion_no])) {
                    $msd_promo_data[$msd_data_val->promotion_no]->promotionLocation[] = $msd_data_val;
                }
            }
        }
        /* Discount */
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-discount-line']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform as $value) {
                    $msd_data_val = new PromotionDiscountLineData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        switch ($att_key) {
                            case "Scheme_Item":
                                $uom = isset($value->Scheme_Item_UOM) ? $value->Scheme_Item_UOM : "";
                                $msd_data_val->product_no = $att_value . '-' . $uom; // Append UOM required in CMOS
                        }
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    if (isset($msd_promo_data[$msd_data_val->promotion_no]) && count($msd_promo_data[$msd_data_val->promotion_no]->promotionLocation) > 0) {
                        foreach ($msd_promo_data[$msd_data_val->promotion_no]->promotionLocation as $promo_location_value) {
                            $promo_location_value->promotionDiscount[] = $msd_data_val;
                        }
                    }
                }
            } else {
                $msd_data_val = new PromotionDiscountLineData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform) as $att_key => $att_value) {
                    switch ($att_key) {
                        case "Scheme_Item":
                            $uom = isset($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform->Scheme_Item_UOM) ? $msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform->Scheme_Item_UOM : "";
                            $msd_data_val->product_no = $att_value . '-' . $uom; // Append UOM required in CMOS
                    }
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                if (isset($msd_promo_data[$msd_data_val->promotion_no]) && count($msd_promo_data[$msd_data_val->promotion_no]->promotionLocation) > 0) {
                    foreach ($msd_promo_data[$msd_data_val->promotion_no]->promotionLocation as $promo_location_value) {
                        $promo_location_value->promotionDiscount[] = $msd_data_val;
                    }
                }
            }
        }

        $msd_data = $msd_pser_data = [];
        if (!empty($msd_promo_data)) {
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", DiscountCaseData::MODULE_DISCOUNT_CASE);

            foreach ($msd_promo_data as $pkey => $promo) {
                Globals::saveJsonFile($file_name . "_" . $pkey, $promo);
            }

            $msd_so_data_val = new SalesOfficeData();
            if ($sales_office_no != "") {
                $msd_so_data_val->no = $sales_office_no;
            } else {
                $msd_so_data_val->company = $company;
            }
            $msd_so_data_val->deleted = 0;
            /* Get all MSD valid sales office */
            $so_params = '<GetSalesOfficeCriteria xsi:type="urn:GetSalesOfficeCriteria">';
            $so_params .= $msd_so_data_val->xmlLineStrings();
            $so_params .= '</GetSalesOfficeCriteria>';
            $so_request = new SoapVar($so_params, XSD_ANYXML);
            $so_soap_result = (array) $soap_client->retrieveSalesOfficeByCriteria($so_request);
            if (count($so_soap_result) > 0) {
                foreach ($so_soap_result as $v_so) {
                    $loc_codes = Location::select('code')->where('sales_office_no', '=', $v_so->no)->get()->keyBy('code')->toarray();
                    $sku_codes = Sku::select('code')->where('sales_office_no', '=', $v_so->no)->get()->keyBy('code')->toarray();
                    foreach ($msd_promo_data as $promo) {
                        if (isset($promo->promotionLocation) && count($promo->promotionLocation) > 0) {
                            foreach ($promo->promotionLocation as $promo_location) {
                                if (!isset($loc_codes[$promo_location->location_code])) {
                                    continue; // isset faster than in_array
                                }
                                if (isset($promo_location->promotionDiscount) && count($promo_location->promotionDiscount) > 0) {

                                    foreach ($promo_location->promotionDiscount as $promo_discount) {
                                        /* Dicsount */
                                        if (!isset($sku_codes[$promo_discount->product_no])) {
                                            continue; // isset faster than in_array
                                        }
                                        $msd_data_val = new DiscountCaseData();
                                        $msd_data_val->sales_office_no = $v_so->no;
                                        $msd_data_val->short_description = $v_so->short_description;
                                        $msd_data_val->location_code = $promo_location->location_code;
                                        $msd_data_val->discount_m_case_no = str_replace("-", "", $promo_location->location_code . '-' . $promo_discount->promotion_no . '-' . $promo_discount->product_no) . $msd_data_val->getMSDDiscountType($promo_discount->discount_type);
                                        $msd_data_val->disc_type_no = $promo_discount->promotion_no;
                                        $msd_data_val->document_no = $promo_discount->promotion_no;
                                        $msd_data_val->pser_cd = $promo_discount->promotion_no;
                                        $msd_data_val->discount_case_cd = $promo_discount->promotion_no;
                                        $msd_data_val->description = $promo->description;
                                        $msd_data_val->product_no = $promo_discount->product_no;
                                        $msd_data_val->min = $promo_discount->min;
                                        $msd_data_val->max = $promo_discount->max;
                                        $msd_data_val->from_date = $promo_location->start_date;
                                        $msd_data_val->to_date = $promo_location->end_date;
                                        $msd_data_val->amount = $promo_discount->discount_amount;
                                        $msd_data_val->percentage = $promo_discount->percentage;
                                        $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                                        $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                                        $msd_data_val->msd_synced = 1;
                                        $msd_data[] = $msd_data_val;
                                        /* PSER */
                                        if (!isset($msd_pser_data[$msd_data_val->sales_office_no][$msd_data_val->disc_type_no])) {
                                            $pser_data_val = new PserData();
                                            $pser_data_val->sales_office_no = $msd_data_val->sales_office_no;
                                            $pser_data_val->short_description = $msd_data_val->short_description;
                                            $pser_data_val->location_code = $msd_data_val->location_code;
                                            $pser_data_val->so_per_cd = $msd_data_val->disc_type_no;
                                            $pser_data_val->pser_ho_code = $msd_data_val->disc_type_no;
                                            $pser_data_val->so_dt_rqstd = $msd_data_val->from_date;
                                            $pser_data_val->due_date = $msd_data_val->to_date;
                                            $pser_data_val->sku_required = $msd_data_val->product_no;
                                            $pser_data_val->pser_title = $promo->name;
                                            $pser_data_val->brf_desc = $msd_data_val->description;
                                            $pser_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                                            $pser_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                                            $pser_data_val->msd_synced = 1;
                                            $msd_pser_data[$msd_data_val->sales_office_no][$msd_data_val->disc_type_no] = $pser_data_val;
                                        } else {
                                            $pser_data_val = $msd_pser_data[$msd_data_val->sales_office_no][$msd_data_val->disc_type_no];
                                            $pser_data_val->sku_required = !str_contains($pser_data_val->sku_required, $msd_data_val->product_no) ? $pser_data_val->sku_required . "," . $msd_data_val->product_no : $pser_data_val->sku_required;
                                            $msd_pser_data[$msd_data_val->sales_office_no][$msd_data_val->disc_type_no] = $pser_data_val;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        unset($msd_promo_data);
        unset($so_soap_result);
        $total_rows = count($msd_data);
        $total_pser_rows = count($msd_pser_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows + $total_pser_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        /* Discount */
        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, 1000); // Create batch
            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchDiscountCaseCriteria xsi:type="urn:GetDiscountCaseCriteriaArray" soap-enc:arrayType="urn:GetDiscountCaseCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchDiscountCaseCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchDiscountCase($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $v_err_msg) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE . "] No maintenance found.", ""); /* Save log info message */
        }

        /* PSER */
        if ($total_pser_rows > 0) {
            foreach ($msd_pser_data as $pser_data) {
                $batch_data = array_chunk($pser_data, 1000); // Create batch

                foreach ($batch_data as $key => $batch) {

                    $batch_params = '<GetBatchPserCriteria xsi:type="urn:GetPserCriteriaArray" soap-enc:arrayType="urn:GetPserCriteria[]">';
                    foreach ($batch as $line) {
                        $batch_params .= $line->xmlArrayLineStrings();
                    }
                    $batch_params .= '</GetBatchPserCriteria>';
                    $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result = (array) $soap_client->saveBatchPser($batch_request);
                    /** Log response message */
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . PserData::MODULE_NAME_PSER . "] " . $soap_result['message'], ""); /* Save log info message */
                    /** Log total rows */
                    if ($key === 0) {
                        Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                    } else {
                        Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                    }
                    /** Log failed rows */
                    Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                    /** Log response error */
                    if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                        foreach ($soap_result['error'] as $v_err_msg) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . PserData::MODULE_NAME_PSER . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                        }
                    }
                }
            }
        }
    }
    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (04/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
   /* public static function syncMSDPromotionNew($method, $url, $data, $trigger_id = null)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('display_errors', 'On');
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		$customer_code = isset($data['params']['Customer_Code']) ? $data['params']['Customer_Code'] : "";
		$sales_office_obj = SalesOffice::where('no', '=', $sales_office_no)->first();
		$short_desc = $sales_office_obj->short_desc;
		$soap_client = Globals::soapClientABINOCCentralWS();
		
		if (isset($data['params']['Customer_Code'])) {
			unset($data['params']['Customer_Code']);
		}
		
		print_r("[" . date("Y-m-d H:i:s") . "] Downloading Discount No List\n");
           
        $request_no = $sales_office_no . Date("YmdHis");
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        // Header
	
        if (isset($data['params']['No'])) {
			$data['params']['Scheme_No'] = $data['params']['No'];
            unset($data['params']['No']);
        }

        $msd_promo_data = [];
		$promotion_list = [];
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionList)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionList) > 0) {
				$total_promo = count($msd_soap_result->ReadMultiple_Result->PromotionList);
				$promotion_value = count($msd_soap_result->ReadMultiple_Result->PromotionList) > 1 ? $msd_soap_result->ReadMultiple_Result->PromotionList : [$msd_soap_result->ReadMultiple_Result->PromotionList];
                foreach ($promotion_value as $value) {
					$promotion_list[] = $value->No;
					$promotion = [];
					$promotion['no'] = $value->No;
					$promotion['name'] =  isset($value->Name) ? $value->Name : "";
					$promotion['description'] = isset($value->Long_Description) ? $value->Long_Description : "";
					$promotion['sales_office_no'] = $sales_office_no;
					$promotion['short_desc'] = $short_desc;
					$promotion['start_date'] =  isset($value->From_Date) ? $value->From_Date : "";
					$promotion['end_date'] =  isset( $value->To_Date) ? $value->To_Date : "";
					$promotion['scheme_type'] =   isset($value->Scheme_Type) ? $value->Scheme_Type : "";
					$promotion['scheme_activate'] =   isset($value->Scheme_Activate) ? $value->Scheme_Activate : "";
					$promotion['exclusive_promo'] =   isset($value->Exclusive_Promo) ? $value->Exclusive_Promo : "";
					$promotion['discount'] =   isset($value->Promotion_On_Discount) ? $value->Promotion_On_Discount : "";
					$promotion['foc'] =   isset($value->Promotion_On_FOC) ?  $value->Promotion_On_FOC  : "";
					$promotion['bundle_validation'] =   isset($value->Multiple_Bundle_Validation) ? $value->Multiple_Bundle_Validation : "";
					$promotion['link_bundle'] =   isset($value->Link_to_Bundle) ? $value->Link_to_Bundle : "";
					$promotion['foc_scheme'] =   isset($value->FOC_Scheme) ? $value->FOC_Scheme : "";
					$promotion['discount_scheme'] =   isset($value->Discount_Scheme) ? : ""; 
					$promotion["request_no"] = $request_no;
					$msd_promo_data[] = $promotion;
					print_r('Saved ' . $promotion['no'] . "\n");
                }
				
				$total_promo_s = count($msd_promo_data);
				print_r("[" . date("Y-m-d H:i:s") . "] Saved " . $total_promo_s . " out of " . $total_promo .  "\n");
            } 
        }
		if(count($msd_promo_data) <= 0 ) {
			print_r("No promo found");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "] No promo data found.", ""); ## Save log error message
			return;
		}
		unset($msd_soap_result);
		
		$data_l = $data;
		
		unset($data_l['params']['To_Date']);
		unset($data_l['params']['From_Date']);
		unset($data_l['params']['Published']);
        unset($data_l['params']['SystemModifiedAt']);
			
		
		if ($customer_code != "") {
			$data_l['params']['Customer_Code'] = $customer_code;
		}
		
		$data_l['params']['Scheme_No'] = implode($promotion_list, "|");
		$data_l['params']['WIN_Sale_office_Code'] = $short_desc;
		$data_l['params']['Scheme_End_Date'] = ">". date('Y-m-d');
		$data_l['params']['Scheme_Start_Date'] = "<". date('Y-m-d');
		print_r("[" . date("Y-m-d H:i:s") . "] Downloading Discount Location List\n");
		
        ## Location 
		$msd_promo_location_data = [];
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-customer']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data_l, $sales_office_no);
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionCustomerList)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionCustomerList) > 0) {
				$total_promo_l = count($msd_soap_result->ReadMultiple_Result->PromotionCustomerList);
				$loc_objs = [];
				$promotion_arr = [];
				if(count($msd_soap_result->ReadMultiple_Result->PromotionCustomerList) == 1) {
					$promotion_arr = [$msd_soap_result->ReadMultiple_Result->PromotionCustomerList];
				}
				else {
					$promotion_arr = $msd_soap_result->ReadMultiple_Result->PromotionCustomerList;
				}
                foreach ($promotion_arr as $value) {	
				
					if(!in_array($value->Scheme_No, $promotion_list))
						continue;
					if(!isset($value->Customer_Code))
						continue;
					if(isset($loc_objs[$value->Customer_Code])) {						
						$loc_obj = $loc_objs[$value->Customer_Code];
					}
					else {

						$loc_obj = Location::where('code', '=', $value->Customer_Code)
							->where('sales_office_no', '=', $sales_office_no)
							->where('deleted', '=', 0)
							->whereNotNull('salesman_code')
							->where('salesman_code', '!=', '')
							->first();
				    if($loc_obj == null) {
						continue;
					}
					else
						$loc_objs[$value->Customer_Code] = $loc_obj;
					}
					$promotion_loc = [];
					$promotion_loc['promotion_no'] = $value->Scheme_No;
					$promotion_loc['location_code'] = isset($value->Customer_Code) ? $value->Customer_Code : "";
					$promotion_loc['location_id'] = $loc_obj->id;
					$promotion_loc['start_date'] = isset($value->Scheme_Start_Date) ? $value->Scheme_Start_Date : "";
					$promotion_loc['end_date'] =  isset($value->Scheme_End_Date) ? $value->Scheme_End_Date: "";
					$promotion_loc["request_no"] = $request_no;
					$msd_promo_location_data[] = $promotion_loc;
					print_r('Saved ' . $promotion_loc['promotion_no'] . " " . $promotion_loc['location_code'] .  "\n");
                }
				$total_promo_l_s = count($msd_promo_location_data);
					print_r('Saved ' . $total_promo_l_s . " out of " . $total_promo_l .  "\n");
            } 				

        }
		if(count($msd_promo_location_data) <= 0 ) {
			print_r("No location found");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "] No location data found.", ""); # Save log error message
			return;
		}
		unset($msd_soap_result);
		unset($loc_objs);
		print_r("[" . date("Y-m-d H:i:s") . "] Downloading Discount SKU List\n");

		$data_d = $data;
		
		unset($data_d['params']['To_Date']);
		unset($data_d['params']['From_Date']);
		unset($data_d['params']['Published']);
        #Discount 
		$msd_promo_discount_data = [];
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-discount-line']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data_d, $sales_office_no);
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform) > 1) {
				$total_promo_d = count($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform);
				$sku_objs = [];
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform as $value) {
					if(!in_array($value->Scheme_No, $promotion_list))
						continue;
					if(!isset($value->Scheme_Item)) {
						print_r('There is no Scheme Item in '. $value->Scheme_No . "\n");
						continue;
					}
					if(!isset($value->Scheme_Item_UOM)) {
						print_r('There is no Scheme Item UOM in '. $value->Scheme_Item . "\n");
						continue;
					}
					
					
					if(isset($sku_objs[($value->Scheme_Item . '-' . $value->Scheme_Item_UOM) ])) {						
						$sku_obj = $sku_objs[($value->Scheme_Item . '-' . $value->Scheme_Item_UOM) ];
					}
					else {
						$sku_obj = SKU::where('code', '=', ($value->Scheme_Item . '-' . $value->Scheme_Item_UOM))->where('sales_office_no', '=', $sales_office_no)->where('deleted', '=', 0)->first();
						if($sku_obj == null) {
							print_r('There is no SKU ' . ($value->Scheme_Item . '-' . $value->Scheme_Item_UOM) . ' for sales office '. $sales_office_no . "\n");
							continue;
						}
						else
							$sku_objs[($value->Scheme_Item . '-' . $value->Scheme_Item_UOM) ] = $sku_obj;
					
					}
					$promotion_disc= [];
					$promotion_disc['promotion_no'] = $value->Scheme_No;
					$promotion_disc['product_no'] = isset($value->Scheme_Item) ? ($value->Scheme_Item . '-' . $value->Scheme_Item_UOM) : "";
					$promotion_disc['uom'] =  isset($value->Scheme_Item_UOM) ? $value->Scheme_Item_UOM : "";
					$promotion_disc['min'] =  isset($value->Scheme_Item_Quantity_Min) ? $value->Scheme_Item_Quantity_Min : "";
					$promotion_disc['max'] =   isset($value->Scheme_Item_Quantity_Max) ? $value->Scheme_Item_Quantity_Max : "";
					$promotion_disc["discount_type"] = isset($value->Discount_Type) ? (new DiscountCaseData)->getMSDDiscountType($value->Discount_Type) : "";
					$promotion_disc['discount_amount'] =   isset($value->Discount_Amount) ? $value->Discount_Amount: "";
					$promotion_disc['percentage'] = isset( $value->Discount_Percent) ? $value->Discount_Percent : "";
					$promotion_disc["request_no"] = $request_no;
					$msd_promo_discount_data[] = $promotion_disc;
					print_r('Saved ' . $promotion_disc['promotion_no'] . " " . $promotion_disc['product_no'] .  "\n");
				}
				$total_promo_l_d = count($msd_promo_discount_data);
					print_r('Saved ' . $total_promo_l_d . " out of " . $total_promo_d .  "\n");
            } 
        }
		if(count($msd_promo_discount_data) <= 0 ) {
			print_r("No location found");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "] No discount data found.", ""); /* Save log error message
			return;
		}
		unset($msd_soap_result);
		unset($sku_objs);


        $msd_data = $msd_pser_data = [];
        if (!empty($msd_promo_data)) {
            /* Save generated response as file backup 
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", DiscountCaseData::MODULE_DISCOUNT_CASE);

            foreach ($msd_promo_data as $pkey => $promo) {
                Globals::saveJsonFile($file_name . "_" . $pkey, $promo);
            }

            $msd_so_data_val = new SalesOfficeData();
            if ($sales_office_no != "") {
                $msd_so_data_val->no = $sales_office_no;
            } else {
                $msd_so_data_val->company = $company;
            }
            $msd_so_data_val->deleted = 0;
            /* Get all MSD valid sales office
            $so_params = '<GetSalesOfficeCriteria xsi:type="urn:GetSalesOfficeCriteria">';
            $so_params .= $msd_so_data_val->xmlLineStrings();
            $so_params .= '</GetSalesOfficeCriteria>';
            $so_request = new SoapVar($so_params, XSD_ANYXML);
            $so_soap_result = (array) $soap_client->retrieveSalesOfficeByCriteria($so_request);

			$final_data = [];

			foreach ($msd_promo_data as $key => $promo_batch) {
			$final_data[$promo_batch['no']] = [];
				foreach ($msd_promo_location_data as  $location_batch) {
					if($location_batch['promotion_no'] != $promo_batch['no'])
						continue;
					foreach ($msd_promo_discount_data as $discount_batch) {
						if($discount_batch['promotion_no'] != $promo_batch['no'])
							continue;
						$discount_m_case_no = $location_batch['location_code'] . $promo_batch['no'] . $discount_batch['product_no']. $discount_batch['discount_type'];
						
						if(isset($final_data[$promo_batch['no']][$discount_m_case_no]))
							continue;
						$new_data = [
							'discount_m_case_no' => $discount_m_case_no,
							'disc_type_no'       => $promo_batch['no'],
							'sales_office_no'    => $sales_office_no,
							'location_id'        => $location_batch['location_id'],
							'product_no'         => $discount_batch['product_no'],
							'document_no'        => $promo_batch['no'],
							'discount_case_cd'   => $promo_batch['no'],
							'description'        => addslashes($promo_batch['name']), // Escape single quotes
							'amount'             => $discount_batch['discount_amount'],
							'percentage'         => number_format($discount_batch['percentage'], 4),
							'from_date'          => $promo_batch['start_date'],
							'to_date'            => $promo_batch['end_date'],
							'deleted'            => 0,
							'pser_cd'            => $promo_batch['no'],
							'added_by'           => DownloadConnector::MSD_LOGGER_NAME,
							'msd_synced'         => 1
						];
					$final_data[$promo_batch['no']][$discount_m_case_no ] = $new_data;
					}
					print_r("Finished creating ". $promo_batch['no'] ." data of ". $location_batch['location_code'] . "\n");
				}
			}
			
			unset($msd_promo_data);
			unset($msd_promo_location_data);
			unset($msd_promo_discount_data);
			$item_count_create = 0;
			$item_count_update = 0;
			$saved_promos = [];

            foreach($final_data as $key => $promo) {
            //            $soap_result = $soap_client->createDiscountCaseFromCache($request_no, $promo->no, $limit, $limit_new, DownloadConnector::MSD_LOGGER_NAME ); 
				
				if(count($promo) <= 0) {
					print_r("Promotion " . $key . " has no items. \n");
					continue;
				}
				$saved_promos[] = $key;
				print_r("Promotion " . $key . " start process. \n");
				
				
				// $promo_of_discount = DB::select('SELECT discount_m_case_no, description, from_date, to_date, amount, percentage, location_id, deleted FROM discount_m_case where sales_office_no = "' . $sales_office_no . '" and disc_type_no = "'. $key . '"');
				// $pcodes = [];
				// $all_codes = [];
				
				// foreach($promo_of_discount as $item_value) {
					// $pcodes[$item_value->discount_m_case_no] = ['description' => $item_value->description, 'from_date' => $item_value->from_date, 'to_date' => $item_value->to_date, 'amount' => $item_value->amount, 'percentage' => $item_value->percentage, 'location_id' => $item_value->location_id, 'deleted' => $item_value->deleted];
				//	$all_codes[] = $item_value->discount_m_case_no;
				// }

				// unset($promo_of_discount);
				// Get total count
				$totalCount = DB::table('discount_m_case')
					->where('sales_office_no', $sales_office_no)
					->where('disc_type_no', $key)
					->count();
				if($totalCount <= 0) {
					$totalCount = count($promo);
				}

				print_r("Promotion " . $key . " check found " . $totalCount . " items \n");
				$inserted = [];
				// Process in batches of 1000
				for ($offset = 0; $offset < $totalCount; $offset += 1000) {
					$item_create = [];
					$item_update = [];
					$pcodes = DB::table('discount_m_case')
						->where('sales_office_no', $sales_office_no)
						->where('disc_type_no', $key)
						->select('discount_m_case_no', 'description', 'from_date', 'to_date', 'amount', 'product_no', 'percentage', 'location_id', 'deleted')
						->offset($offset)
						->limit(1000)
						->get()
						->keyBy('discount_m_case_no'); // Key the collection by discount_m_case_no for easier lookup

					foreach ($promo as $key2 => $item) {
						if (isset($pcodes[$item['discount_m_case_no']])) {
							$temp_p_val = $pcodes[$item['discount_m_case_no']];
							if ($item['from_date'] != $temp_p_val->from_date || 
								$item['to_date'] != $temp_p_val->to_date || 
								$item['amount'] != $temp_p_val->amount || 
								$item['location_id'] != $temp_p_val->location_id || 
								$item['product_no'] != $temp_p_val->product_no || 
								$item['percentage'] != $temp_p_val->percentage || 
								$item['description'] != $temp_p_val->description || 
								$temp_p_val->deleted == 1) {
								$item_update[] = $item;
							}
							unset($promo[$key2]);
						} else {
							if(!isset($inserted[$item['discount_m_case_no']])) {
								$item_create[] = $item;
								$inserted[$item['discount_m_case_no']] = 1;
							}
						}
					}

					if(count($item_create) > 0) {
						$insert_query = "";
						$batch_insert = array_chunk($item_create, 500);
						$insertCount = 0;
						$total_count = count($item_create);
						foreach($batch_insert as $data_insert){
							$insert_query = "";
							foreach($data_insert as $discount_data) {
								$insert_query .= "( '" . implode($discount_data, "','") . "') ,";
							}
							try {
								DB::insert('INSERT IGNORE INTO discount_m_case 
								(discount_m_case_no, disc_type_no, sales_office_no, location_id, product_no, document_no, discount_case_cd, description, amount, percentage,  from_date, to_date, deleted, pser_cd, added_by, msd_synced)
								VALUES ' .
								rtrim($insert_query, ","));
								$insertCount += count($data_insert);
								print_r("Created " . $insertCount. " out of " . $total_count . " items in ". $key ." \n");
							} catch (\Illuminate\Database\QueryException $e) {
								continue;
							}

						}
					}
					if(count($item_update) > 0) {
						// Execute the update queries
						$batch_update = array_chunk($item_update, 200);
						foreach($batch_update as $data_update) {
							DB::beginTransaction();
							try {
								$update_query = "";
									$col_update = [
									//'disc_type_no' => ' disc_type_no = CASE',
									'location_id' => ' location_id = CASE',
									//'product_no' => ' product_no = CASE',
									//'discount_case_cd' => ' discount_case_cd = CASE',
									'description' => ' description = CASE',
									'amount' => ' amount = CASE',
									'percentage' => ' percentage = CASE',
									'from_date' => ' from_date = CASE',
									'to_date' => ' to_date = CASE',
									'deleted' => ' deleted = CASE',
									//'pser_cd' => ' pser_cd = CASE',
									//'msd_synced' => ' msd_synced = CASE',
									//'updated_by'  => ' updated_by = CASE'
									
								];
								$update_query .= "UPDATE discount_m_case SET ";
								
								foreach ($data_update as $discount_data) {
									$when_cond = ' WHEN discount_m_case_no = "'.$discount_data['discount_m_case_no'] .'"  ';
									//$col_update['disc_type_no'] .= $when_cond . " AND disc_type_no <> '" . $discount_data['disc_type_no'] . "' THEN '" . $discount_data['disc_type_no'] . "' ";
									$col_update['location_id'] .= $when_cond .  " AND location_id <> '" . $discount_data['location_id'] . "' THEN '" . $discount_data['location_id'] . "' ";
									//$col_update['discount_case_cd'] .= $when_cond .  " AND discount_case_cd <> '"  . $discount_data['discount_case_cd'] . "' THEN '" . $discount_data['discount_case_cd'] . "'  ";
									//$col_update['product_no'] .= $when_cond .  " AND product_no <> '"  . $discount_data['product_no'] . "'  THEN '" . $discount_data['product_no'] . "' ";
									$col_update['description'] .= $when_cond .  " AND description <> '" . $discount_data['description'] . "' THEN '" . $discount_data['description'] . "'  ";
									$col_update['amount'] .= $when_cond .  " THEN '" . $discount_data['amount'] . "'  ";
									$col_update['percentage'] .= $when_cond . " THEN '" . $discount_data['percentage'] . "'  ";
									$col_update['from_date'] .= $when_cond . " THEN '" . $discount_data['from_date'] . "'  ";
									$col_update['to_date'] .= $when_cond .  " THEN '" . $discount_data['to_date'] . "'  ";
									$col_update['deleted'] .= $when_cond .  " THEN '" . $discount_data['deleted'] . "'  ";
									//$col_update['msd_synced'] .= $when_cond . " THEN '" . $discount_data['msd_synced'] . "'  ";
									//$col_update['pser_cd'] .= $when_cond . " AND pser_cd <> '"  . $discount_data['pser_cd'] . "' THEN '" . $discount_data['pser_cd'] . "'  ";
									//$col_update['updated_by'] .= $when_cond . " THEN '" . DownloadConnector::MSD_LOGGER_NAME . "'  ";
									//$col_update['updated_when'] .= $when_cond . " THEN '" . date('Y-m-d H:i:s') . "'  ";
								}
								
								
								foreach($col_update as $key_a => $column) {
									$update_query .= $column . " ELSE " .$key_a . " END ,";
								}
								$update_query  = rtrim($update_query, ",");
								$update_query .= ' WHERE disc_type_no = "' . $key . '" and sales_office_no = "' . $sales_office_no . '"';
								DB::update($update_query);
								DB::commit();
								print_r("Updated " . count($data_update) . " items in ". $key ." \n");
							} catch (Exception $e) {
								DB::rollback();
							}
						}
					}
				}
				  Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "] Saved discount for " . $key, ""); /* Save log error message 
			
				print_r("Promotion " . $key . " done. \n");
				unset($final_data[$key]);
				
			}
			// if (!isset($data['params']['SystemModifiedAt']) || empty($data['params']['SystemModifiedAt'])) {

				// DB::table('discount_m_case')
				// ->where('sales_office_no', $sales_office_no)
				// ->whereNotIn('disc_type_no', $saved_promos)
				// ->update(['deleted' => 1]);
			// }

            $soap_result = $soap_client->deletePromotionCache($request_no );
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "]" . $soap_result, ""); /* Save log error message

        }
		
    }
	*/
	
	public static function syncMSDPromotionNew($method, $url, $data, $trigger_id = null)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('display_errors', 'On');
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		$customer_code = isset($data['params']['Customer_Code']) ? $data['params']['Customer_Code'] : "";
		$sales_office_obj = SalesOffice::where('no', '=', $sales_office_no)->first();
		$short_desc = $sales_office_obj->short_desc;
		$soap_client = Globals::soapClientABINOCCentralWS();
		
		if (isset($data['params']['Customer_Code'])) {
			unset($data['params']['Customer_Code']);
		}
		
		print_r("[" . date("Y-m-d H:i:s") . "] Downloading Discount No List\n");
           
        $request_no = $sales_office_no . Date("YmdHis");
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        /* Header */
	
        if (isset($data['params']['No'])) {
			$data['params']['Scheme_No'] = $data['params']['No'];
            unset($data['params']['No']);
        }

        $msd_promo_data = [];
		$promotion_list = [];
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionList)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionList) > 0) {
				$total_promo = count($msd_soap_result->ReadMultiple_Result->PromotionList);
				$promotion_value = count($msd_soap_result->ReadMultiple_Result->PromotionList) > 1 ? $msd_soap_result->ReadMultiple_Result->PromotionList : [$msd_soap_result->ReadMultiple_Result->PromotionList];
				$promo_i = 0;
                foreach ($promotion_value as $value) {
					$promo_i++;
					self::cliSyncProgress($request_no, 'Promotion list', $promo_i, $total_promo);
					$no = $value->No;
					$promotion_list[] = $no;
					$msd_promo_data[] = [
						'no' => $no,
						'name' => isset($value->Name) ? $value->Name : '',
						'description' => isset($value->Long_Description) ? $value->Long_Description : '',
						'sales_office_no' => $sales_office_no,
						'short_desc' => $short_desc,
						'start_date' => isset($value->From_Date) ? $value->From_Date : '',
						'end_date' => isset($value->To_Date) ? $value->To_Date : '',
						'scheme_type' => isset($value->Scheme_Type) ? $value->Scheme_Type : '',
						'scheme_activate' => isset($value->Scheme_Activate) ? $value->Scheme_Activate : '',
						'exclusive_promo' => isset($value->Exclusive_Promo) ? $value->Exclusive_Promo : '',
						'discount' => isset($value->Promotion_On_Discount) ? $value->Promotion_On_Discount : '',
						'foc' => isset($value->Promotion_On_FOC) ? $value->Promotion_On_FOC : '',
						'bundle_validation' => isset($value->Multiple_Bundle_Validation) ? $value->Multiple_Bundle_Validation : '',
						'link_bundle' => isset($value->Link_to_Bundle) ? $value->Link_to_Bundle : '',
						'foc_scheme' => isset($value->FOC_Scheme) ? $value->FOC_Scheme : '',
						'discount_scheme' => isset($value->Discount_Scheme) ? $value->Discount_Scheme : '',
						'request_no' => $request_no,
					];
                }
				
				$total_promo_s = count($msd_promo_data);
				print_r("[" . date("Y-m-d H:i:s") . "] Saved " . $total_promo_s . " out of " . $total_promo .  "\n");
            } 
        }
		if(count($msd_promo_data) <= 0 ) {
			print_r("No promo found");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "] No promo data found.", ""); /* Save log error message */
			return;
		}
		unset($msd_soap_result);
		
		$data_l = $data;
		

        if (isset($data_l['params']['SystemModifiedAt'])) {
            unset($data_l['params']['SystemModifiedAt']);
        }
		unset($data_l['params']['To_Date']);
		unset($data_l['params']['From_Date']);
		unset($data_l['params']['Published']);
			
		
		if ($customer_code != "") {
			$data_l['params']['Customer_Code'] = $customer_code;
		}
		
		$promotion_list = array_values(array_unique($promotion_list));
		$promotionSet = array_flip($promotion_list);
		$data_l['params']['WIN_Sale_office_Code'] = $short_desc;
		/* Inclusive "active on run date": strict >/< excluded rows starting or ending today. */
		$today = date('Y-m-d');
		$data_l['params']['Scheme_End_Date'] = '>=' . $today;
		$data_l['params']['Scheme_Start_Date'] = '<=' . $today;
		print_r("[" . date("Y-m-d H:i:s") . "] Downloading Discount Location List\n");
		print_r("[" . date("Y-m-d H:i:s") . "] Scheme count: " . count($promotion_list) . "\n");
        /* Location */
		$msd_promo_location_data = [];
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-customer']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company); //dd($url);
		$loc_objs = [];
		$promotion_arr = [];
		$schemeChunks = array_chunk($promotion_list, 35);
		$totalSchemeChunks = count($schemeChunks);
		foreach ($schemeChunks as $chunkIndex => $schemeChunk) {
			$data_l['params']['Scheme_No'] = implode("|", $schemeChunk);
			print_r("[" . date("Y-m-d H:i:s") . "] Fetching location chunk " . ($chunkIndex + 1) . "/" . $totalSchemeChunks . " (" . count($schemeChunk) . " schemes)\n");
			$msd_soap_result = Globals::callSoapApiReadMultiple($url, $data_l, $sales_office_no);
			if (!isset($msd_soap_result->ReadMultiple_Result->PromotionCustomerList)) {
				continue;
			}
			$chunkRows = $msd_soap_result->ReadMultiple_Result->PromotionCustomerList;
			if (count($chunkRows) == 1) {
				$promotion_arr[] = $chunkRows;
			} else {
				foreach ($chunkRows as $row) {
					$promotion_arr[] = $row;
				}
			}
		}
        if (count($promotion_arr) > 0) {
				$total_promo_l = count($promotion_arr);
				$loc_i = 0;
                foreach ($promotion_arr as $value) {	
					$loc_i++;
					self::cliSyncProgress($request_no, 'Discount locations', $loc_i, $total_promo_l);
				
					if(!isset($promotionSet[$value->Scheme_No]))
						continue;
					if(!isset($value->Customer_Code))
						continue;
					if(isset($loc_objs[$value->Customer_Code])) {						
						$loc_obj = $loc_objs[$value->Customer_Code];
					}
					else {

						$loc_obj = Location::where('code', '=', $value->Customer_Code)
							->where('sales_office_no', '=', $sales_office_no)
							->where('deleted', '=', 0)
							->whereNotNull('salesman_code')
							->where('salesman_code', '!=', '')
							->first();
				    if($loc_obj == null) {
						continue;
					}
					else
						$loc_objs[$value->Customer_Code] = $loc_obj;
					}
					$promotion_loc = [];
					$promotion_loc['promotion_no'] = $value->Scheme_No;
					$promotion_loc['location_code'] = isset($value->Customer_Code) ? $value->Customer_Code : "";
					$promotion_loc['location_id'] = $loc_obj->id;
					$promotion_loc['start_date'] = isset($value->Scheme_Start_Date) ? $value->Scheme_Start_Date : "";
					$promotion_loc['end_date'] =  isset($value->Scheme_End_Date) ? $value->Scheme_End_Date: "";
					$promotion_loc["request_no"] = $request_no;
					$msd_promo_location_data[] = $promotion_loc;
					print_r('Saved ' . $promotion_loc['promotion_no'] . " " . $promotion_loc['location_code'] .  "\n");
                }
				$total_promo_l_s = count($msd_promo_location_data);
					print_r('Saved ' . $total_promo_l_s . " out of " . $total_promo_l .  "\n");
        }
		if(count($msd_promo_location_data) <= 0 ) {
			print_r("No location found");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "] No location data found.", ""); /* Save log error message */
			return;
		}
		unset($msd_soap_result);
		unset($loc_objs);
		print_r("[" . date("Y-m-d H:i:s") . "] Downloading Discount SKU List\n");

		$data_d = $data;
		
		unset($data_d['params']['To_Date']);
		unset($data_d['params']['From_Date']);
		unset($data_d['params']['Published']);
        /* Discount */
		$msd_promo_discount_data = [];
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-discount-line']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data_d, $sales_office_no);
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform) > 1) {
				$total_promo_d = count($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform);
				$sku_objs = [];
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform as $value) {
					if(!in_array($value->Scheme_No, $promotion_list))
						continue;
					if(!isset($value->Scheme_Item)) {
						print_r('There is no Scheme Item in '. $value->Scheme_No . "\n");
						continue;
					}
					if(!isset($value->Scheme_Item_UOM)) {
						print_r('There is no Scheme Item UOM in '. $value->Scheme_Item . "\n");
						continue;
					}
					
					
					if(isset($sku_objs[($value->Scheme_Item . '-' . $value->Scheme_Item_UOM) ])) {						
						$sku_obj = $sku_objs[($value->Scheme_Item . '-' . $value->Scheme_Item_UOM) ];
					}
					else {
						$sku_obj = SKU::where('code', '=', ($value->Scheme_Item . '-' . $value->Scheme_Item_UOM))->where('sales_office_no', '=', $sales_office_no)->where('deleted', '=', 0)->first();
						if($sku_obj == null) {
							print_r('There is no SKU ' . ($value->Scheme_Item . '-' . $value->Scheme_Item_UOM) . ' for sales office '. $sales_office_no . "\n");
							continue;
						}
						else
							$sku_objs[($value->Scheme_Item . '-' . $value->Scheme_Item_UOM) ] = $sku_obj;
					
					}
					$promotion_disc= [];
					$promotion_disc['promotion_no'] = $value->Scheme_No;
					$promotion_disc['product_no'] = isset($value->Scheme_Item) ? ($value->Scheme_Item . '-' . $value->Scheme_Item_UOM) : "";
					$promotion_disc['uom'] =  isset($value->Scheme_Item_UOM) ? $value->Scheme_Item_UOM : "";
					$promotion_disc['min'] =  isset($value->Scheme_Item_Quantity_Min) ? $value->Scheme_Item_Quantity_Min : "";
					$promotion_disc['max'] =   isset($value->Scheme_Item_Quantity_Max) ? $value->Scheme_Item_Quantity_Max : "";
					$promotion_disc["discount_type"] = isset($value->Discount_Type) ? (new DiscountCaseData)->getMSDDiscountType($value->Discount_Type) : "";
					$promotion_disc['discount_amount'] =   isset($value->Discount_Amount) ? $value->Discount_Amount: "";
					$promotion_disc['percentage'] = isset( $value->Discount_Percent) ? $value->Discount_Percent : "";
					$promotion_disc["request_no"] = $request_no;
					$msd_promo_discount_data[] = $promotion_disc;
					print_r('Saved ' . $promotion_disc['promotion_no'] . " " . $promotion_disc['product_no'] .  "\n");
				}
				$total_promo_l_d = count($msd_promo_discount_data);
					print_r('Saved ' . $total_promo_l_d . " out of " . $total_promo_d .  "\n");
            } 
        }
		if(count($msd_promo_discount_data) <= 0 ) {
			print_r("No location found");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "] No discount data found.", ""); /* Save log error message */
			return;
		}
		unset($msd_soap_result);
		unset($sku_objs);


        $msd_data = $msd_pser_data = [];
        if (!empty($msd_promo_data)) {
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", DiscountCaseData::MODULE_DISCOUNT_CASE);

            foreach ($msd_promo_data as $pkey => $promo) {
                Globals::saveJsonFile($file_name . "_" . $pkey, $promo);
            }

            $msd_so_data_val = new SalesOfficeData();
            if ($sales_office_no != "") {
                $msd_so_data_val->no = $sales_office_no;
            } else {
                $msd_so_data_val->company = $company;
            }
            $msd_so_data_val->deleted = 0;
            /* Get all MSD valid sales office */
            $so_params = '<GetSalesOfficeCriteria xsi:type="urn:GetSalesOfficeCriteria">';
            $so_params .= $msd_so_data_val->xmlLineStrings();
            $so_params .= '</GetSalesOfficeCriteria>';
            $so_request = new SoapVar($so_params, XSD_ANYXML);
            $so_soap_result = (array) $soap_client->retrieveSalesOfficeByCriteria($so_request);

			$locationsByPromo = [];
			foreach ($msd_promo_location_data as $location_batch) {
				$pno = $location_batch['promotion_no'];
				if (!isset($locationsByPromo[$pno])) {
					$locationsByPromo[$pno] = [];
				}
				$locationsByPromo[$pno][] = $location_batch;
			}
			$discountsByPromo = [];
			foreach ($msd_promo_discount_data as $discount_batch) {
				$pno = $discount_batch['promotion_no'];
				if (!isset($discountsByPromo[$pno])) {
					$discountsByPromo[$pno] = [];
				}
				$discountsByPromo[$pno][] = $discount_batch;
			}

			$final_data = [];

			$promoDataCount = count($msd_promo_data);
			$promoIdx = 0;
			foreach ($msd_promo_data as $key => $promo_batch) {
				$promoIdx++;
				self::cliSyncProgress($request_no, 'Build discount matrix', $promoIdx, $promoDataCount);
				$final_data[$promo_batch['no']] = [];
				$locationsForPromo = isset($locationsByPromo[$promo_batch['no']]) ? $locationsByPromo[$promo_batch['no']] : [];
				$discountsForPromo = isset($discountsByPromo[$promo_batch['no']]) ? $discountsByPromo[$promo_batch['no']] : [];
				foreach ($locationsForPromo as $location_batch) {
					foreach ($discountsForPromo as $discount_batch) {
						$discount_m_case_no = $location_batch['location_code'] . $promo_batch['no'] . $discount_batch['product_no']. $discount_batch['discount_type'];
						
						if(isset($final_data[$promo_batch['no']][$discount_m_case_no]))
							continue;
						$new_data = [
							'discount_m_case_no' => $discount_m_case_no,
							'disc_type_no'       => $promo_batch['no'],
							'sales_office_no'    => $sales_office_no,
							'location_id'        => $location_batch['location_id'],
							'product_no'         => $discount_batch['product_no'],
							'document_no'        => $promo_batch['no'],
							'discount_case_cd'   => $promo_batch['no'],
							'description'        => addslashes($promo_batch['name']), // Escape single quotes
							'amount'             => $discount_batch['discount_amount'],
							'percentage'         => number_format($discount_batch['percentage'], 4),
							'from_date'          => $promo_batch['start_date'],
							'to_date'            => $promo_batch['end_date'],
							'deleted'            => 0,
							'pser_cd'            => $promo_batch['no'],
							'added_by'           => DownloadConnector::MSD_LOGGER_NAME,
							'msd_synced'         => 1
						];
					$final_data[$promo_batch['no']][$discount_m_case_no ] = $new_data;
					}
					print_r("Finished creating ". $promo_batch['no'] ." data of ". $location_batch['location_code'] . "\n");
				}
			}
			
			unset($msd_promo_data);
			unset($msd_promo_location_data);
			unset($msd_promo_discount_data);
			unset($locationsByPromo);
			unset($discountsByPromo);
			$saved_promos = [];

			$totalFinalPromos = count($final_data);
			$finalPromoIdx = 0;
            foreach($final_data as $key => $promo) {
				$finalPromoIdx++;
				self::cliSyncProgress($request_no, 'DB save promotions', $finalPromoIdx, $totalFinalPromos);
            //            $soap_result = $soap_client->createDiscountCaseFromCache($request_no, $promo->no, $limit, $limit_new, DownloadConnector::MSD_LOGGER_NAME ); 
				
				if(count($promo) <= 0) {
					print_r("Promotion " . $key . " has no items. \n");
					continue;
				}
				$saved_promos[] = $key;
				print_r("Promotion " . $key . " start process. \n");

				/* Process in bounded chunks — huge schemes OOM'd when loading all IDs + all existing rows + all inserts in memory. */
				$promoRows = array_values($promo);
				if (isset($final_data[$key])) {
					$final_data[$key] = [];
				}
				$totalPromoRows = count($promoRows);
				$promoChunkSize = 800;
				$idInQuerySize = 350;
				print_r("Promotion " . $key . " syncing " . $totalPromoRows . " rows in chunks of " . $promoChunkSize . "\n");

				$insertCols = ['discount_m_case_no', 'disc_type_no', 'sales_office_no', 'location_id', 'product_no', 'document_no', 'discount_case_cd', 'description', 'amount', 'percentage', 'from_date', 'to_date', 'deleted', 'pser_cd', 'added_by', 'msd_synced'];
				$pdo = DB::connection()->getPdo();
				$chunkIndex = 0;
				foreach (array_chunk($promoRows, $promoChunkSize) as $itemChunk) {
					$chunkIndex++;
					$existingRows = [];
					$idsForChunk = [];
					foreach ($itemChunk as $rowIn) {
						if (!empty($rowIn['discount_m_case_no'])) {
							$idsForChunk[$rowIn['discount_m_case_no']] = true;
						}
					}
					$idList = array_keys($idsForChunk);
					unset($idsForChunk);
					foreach (array_chunk($idList, $idInQuerySize) as $idChunk) {
						$rows = DB::table('discount_m_case')
							->select('discount_m_case_no', 'description', 'from_date', 'to_date', 'amount', 'product_no', 'percentage', 'location_id', 'deleted')
							->where('sales_office_no', $sales_office_no)
							->where('disc_type_no', $key)
							->whereIn('discount_m_case_no', $idChunk)
							->get();
						foreach ($rows as $row) {
							$existingRows[$row->discount_m_case_no] = $row;
						}
						unset($rows);
					}
					unset($idList);

					$item_create = [];
					$item_update = [];
					foreach ($itemChunk as $item) {
						if (isset($existingRows[$item['discount_m_case_no']])) {
							$temp_p_val = $existingRows[$item['discount_m_case_no']];
							if ($item['from_date'] != $temp_p_val->from_date ||
								$item['to_date'] != $temp_p_val->to_date ||
								$item['amount'] != $temp_p_val->amount ||
								$item['location_id'] != $temp_p_val->location_id ||
								$item['product_no'] != $temp_p_val->product_no ||
								$item['percentage'] != $temp_p_val->percentage ||
								$item['description'] != $temp_p_val->description ||
								$temp_p_val->deleted == 1) {
								$item_update[] = $item;
							}
						} else {
							$item_create[] = $item;
						}
					}
					unset($existingRows);

					if (count($item_create) > 0) {
						$insertCount = 0;
						$total_count = count($item_create);
						foreach (array_chunk($item_create, 400) as $data_insert) {
							$placeholders = [];
							$bindings = [];
							foreach ($data_insert as $discount_data) {
								$placeholders[] = '(' . implode(',', array_fill(0, count($insertCols), '?')) . ')';
								foreach ($insertCols as $col) {
									$bindings[] = isset($discount_data[$col]) ? $discount_data[$col] : null;
								}
							}
							$sql = 'INSERT IGNORE INTO discount_m_case (' . implode(',', $insertCols) . ') VALUES ' . implode(',', $placeholders);
							try {
								DB::insert($sql, $bindings);
								$insertCount += count($data_insert);
								print_r("[" . $key . "] chunk " . $chunkIndex . " created " . $insertCount . "/" . $total_count . "\n");
							} catch (\Illuminate\Database\QueryException $e) {
								continue;
							}
						}
					}
					unset($item_create);

					if (count($item_update) > 0) {
						foreach (array_chunk($item_update, 80) as $data_update) {
							DB::beginTransaction();
							try {
								$col_update = [
									'location_id' => ' location_id = CASE',
									'description' => ' description = CASE',
									'amount' => ' amount = CASE',
									'percentage' => ' percentage = CASE',
									'from_date' => ' from_date = CASE',
									'to_date' => ' to_date = CASE',
									'deleted' => ' deleted = CASE',
									'updated_by' => ' updated_by = CASE',
								];
								foreach ($data_update as $discount_data) {
									$when_cond = ' WHEN discount_m_case_no = ' . $pdo->quote($discount_data['discount_m_case_no']) . ' ';
									$col_update['location_id'] .= $when_cond . ' AND location_id <> ' . $pdo->quote($discount_data['location_id']) . ' THEN ' . $pdo->quote($discount_data['location_id']) . ' ';
									$col_update['description'] .= $when_cond . ' AND description <> ' . $pdo->quote($discount_data['description']) . ' THEN ' . $pdo->quote($discount_data['description']) . '  ';
									$col_update['amount'] .= $when_cond . ' THEN ' . $pdo->quote($discount_data['amount']) . '  ';
									$col_update['percentage'] .= $when_cond . ' THEN ' . $pdo->quote($discount_data['percentage']) . '  ';
									$col_update['from_date'] .= $when_cond . ' THEN ' . $pdo->quote($discount_data['from_date']) . '  ';
									$col_update['to_date'] .= $when_cond . ' THEN ' . $pdo->quote($discount_data['to_date']) . '  ';
									$col_update['deleted'] .= $when_cond . ' THEN ' . $pdo->quote($discount_data['deleted']) . '  ';
									$col_update['updated_by'] .= $when_cond . ' THEN ' . $pdo->quote(DownloadConnector::MSD_LOGGER_NAME) . '  ';
								}
								$update_query = 'UPDATE discount_m_case SET ';
								foreach ($col_update as $key_a => $column) {
									$update_query .= $column . ' ELSE ' . $key_a . ' END ,';
								}
								$update_query = rtrim($update_query, ',');
								$idsInBatch = [];
								foreach ($data_update as $d) {
									$idsInBatch[] = $d['discount_m_case_no'];
								}
								$inClause = implode(',', array_map(function ($id) use ($pdo) {
									return $pdo->quote($id);
								}, $idsInBatch));
								$update_query .= ' WHERE disc_type_no = ' . $pdo->quote($key) . ' AND sales_office_no = ' . $pdo->quote($sales_office_no) . ' AND discount_m_case_no IN (' . $inClause . ')';
								DB::update($update_query);
								DB::commit();
								print_r("[" . $key . "] chunk " . $chunkIndex . " updated " . count($data_update) . " rows\n");
							} catch (\Exception $e) {
								DB::rollback();
							}
						}
					}
					unset($item_update, $itemChunk);
				}
				unset($promoRows);
				  Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "] Saved discount for " . $key, ""); /* Save log error message */
			
				print_r("Promotion " . $key . " done. \n");
				unset($final_data[$key]);
				
			}
			// if (!isset($data['params']['SystemModifiedAt']) || empty($data['params']['SystemModifiedAt'])) {

				// DB::table('discount_m_case')
				// ->where('sales_office_no', $sales_office_no)
				// ->whereNotIn('disc_type_no', $saved_promos)
				// ->update(['deleted' => 1]);
			// }

            $soap_result = $soap_client->deletePromotionCache($request_no );
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "]" . $soap_result, ""); /* Save log error message */

        }
		
    }
	
	protected static function cliSyncProgress($runId, $phase, $current, $total, $barWidth = 28)
    {
        if (PHP_SAPI !== 'cli' || $total < 1) {
            return;
        }
        $pct = (int) floor(($current * 100) / $total);
        $pct = min(100, max(0, $pct));
        static $reported = [];
        $bucketKey = $runId . '|' . $phase;
        $bucket = (int) floor($pct / 5) * 5;
        if (!isset($reported[$bucketKey])) {
            $reported[$bucketKey] = -1;
        }
        if ($current === 1 || $current >= $total || $bucket > $reported[$bucketKey]) {
            $reported[$bucketKey] = $bucket;
            $filled = (int) round($barWidth * $current / $total);
            $filled = min($barWidth, max(0, $filled));
            $bar = str_repeat('#', $filled) . str_repeat('-', $barWidth - $filled);
            fwrite(STDOUT, sprintf("[%s] %s |%s| %d%% (%d/%d)\n", date('Y-m-d H:i:s'), $phase, $bar, $pct, $current, $total));
            fflush(STDOUT);
        }
    }

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (09/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPromotionDeals($method, $url, $data, $trigger_id = null)
    {
		gc_enable();
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('display_errors', 'On');
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		$sales_office_obj = SalesOffice::where('no', '=', $sales_office_no)->first();

        $soap_client = Globals::soapClientABINOCCentralWS();
		
        $request_no = $sales_office_no . Date("YmdHis");

        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        /* Header */

        if (isset($data['params']['No'])) {
            unset($data['params']['No']);
        }
        $loc_codes = Location::select('code')->where('sales_office_no', '=', $sales_office_no)->get()->keyBy('code')->toarray();
        $sku_codes = Sku::select('code')->where('sales_office_no', '=', $sales_office_no)->get()->keyBy('code')->toarray();

        $msd_promo_data = [];
		print_r("Reading PromotionList Data \n");
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionList)) {
		print_r("Found PromotionList Data \n");
			
            if (count($msd_soap_result->ReadMultiple_Result->PromotionList) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionList as $value) {
					if($value->Scheme_Type != 'Item_Trade_Offer') {
							continue;
					}
                    $msd_data_val = new PromotionData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data_val->request_no = $request_no;
                    $msd_promo_data[$msd_data_val->no] = $msd_data_val;
					print_r("Save " . $msd_data_val->no . "\n");
                }
            } else {
				if($msd_soap_result->ReadMultiple_Result->PromotionList->Scheme_Type != 'Item_Trade_Offer') {
						continue;
				}
                $msd_data_val = new PromotionData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PromotionList) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data_val->request_no = $request_no;
                $msd_promo_data[$msd_data_val->no] = $msd_data_val;
            }
        }
		print_r("End Search PromotionList Data \n");
		gc_collect_cycles();
		print_r("Start Search FOC Data \n");

        /* FOC Data */
		gc_enable();
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-foc']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
		print_r(isset($msd_soap_result->ReadMultiple_Result->PromotionFOCDetails));
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionFOCDetails)) {
		print_r("Found FOC Data \n");
            if (count($msd_soap_result->ReadMultiple_Result->PromotionFOCDetails) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionFOCDetails as $value) {
                    $msd_data_val = new PromotionFocData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->request_no = $request_no;
                    if (isset($msd_promo_data[$msd_data_val->promotion_no])) {
						print_r("Save FOC Data " . $msd_data_val->scheme_item . "\n");
                        $msd_promo_data[$msd_data_val->promotion_no]->promotionFOC[] = $msd_data_val;
                    }
                }
            } else {
                $msd_data_val = new PromotionFocData();
                foreach (get_object_vars($value) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->request_no = $request_no;
                if (isset($msd_promo_data[$msd_data_val->no])) {
                    $msd_promo_data[$msd_data_val->promotion_no]->promotionFOC[] = $msd_data_val;
                }
            }
        }
		print_r("End Search FOC Data \n");
		gc_collect_cycles();
        
		gc_enable();
		print_r("Start Location Data \n");
        /* Location */
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-customer']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionCustomerList)) {
			print_r("Found Location Data \n");
            if (count($msd_soap_result->ReadMultiple_Result->PromotionCustomerList) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionCustomerList as $value) {
                    $msd_data_val = new PromotionLocationData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data_val->request_no = $request_no;
                    if($sales_office_obj != null && $sales_office_obj->short_desc != $msd_data_val->sales_office_no ) {
                        continue;
                    }
                    if (isset($msd_promo_data[$msd_data_val->promotion_no]) && isset($loc_codes[$msd_data_val->location_code])) {
						print_r("Save Location ". $msd_data_val->location_code ."\n");
                        $msd_promo_data[$msd_data_val->promotion_no]->promotionLocation[] = $msd_data_val;
                    }
                }
            } else {
                $msd_data_val = new PromotionLocationData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PromotionCustomerList) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data_val->request_no = $request_no;			
				if($sales_office_no !== "" && $sales_office_no != $msd_data_val->sales_office_no ) {
					continue;
				}
                else if(isset($msd_promo_data[$msd_data_val->promotion_no]) && isset($loc_codes[$msd_data_val->location_code])) {
                    $msd_promo_data[$msd_data_val->promotion_no]->promotionLocation[] = $msd_data_val;
                }
            }
        }
		print_r("End Search Location Data \n");
		gc_collect_cycles();
        
		print_r("Start Discount Data \n");

        /* Discount */
		gc_enable();
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-discount-line']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        if (isset($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform as $value) {
                    $msd_data_val = new PromotionDiscountLineData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        switch ($att_key) {
                            case "Scheme_Item":
                                $uom = isset($value->Scheme_Item_UOM) ? $value->Scheme_Item_UOM : "";
                                $msd_data_val->product_no = $att_value . '-' . $uom; // Append UOM required in CMOS
                        }
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->discount_type = (new DiscountCaseData)->getMSDDiscountType($msd_data_val->discount_type);
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->request_no = $request_no;
                    $msd_data_val->msd_synced = 1;
                    if (isset($msd_promo_data[$msd_data_val->promotion_no]) && isset($sku_codes[$msd_data_val->product_no])) {
						print_r("Save Discount ". $msd_data_val->product_no ."\n");
                        $msd_promo_data[$msd_data_val->promotion_no]->promotionDiscount[] = $msd_data_val;
                    }
                }
            } else {
                $msd_data_val = new PromotionDiscountLineData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform) as $att_key => $att_value) {
                    switch ($att_key) {
                        case "Scheme_Item":
                            $uom = isset($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform->Scheme_Item_UOM) ? $msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform->Scheme_Item_UOM : "";
                            $msd_data_val->product_no = $att_value . '-' . $uom; // Append UOM required in CMOS
                    }
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->request_no = $request_no;
                $msd_data_val->msd_synced = 1;
                if (isset($msd_promo_data[$msd_data_val->promotion_no]) && isset($sku_codes[$msd_data_val->product_no])) {
                    $msd_promo_data[$msd_data_val->promotion_no]->promotionDiscount[] = $msd_data_val;
                }
            }
        }
		print_r("End Discount Location Data \n");
		gc_collect_cycles();
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */
		gc_enable();
        if (!empty($msd_promo_data)) {

            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", DiscountCaseData::MODULE_DISCOUNT_CASE);

            foreach ($msd_promo_data as $pkey => $promo) {
                Globals::saveJsonFile($file_name . "_" . $pkey, $promo);
            }

            $msd_so_data_val = new SalesOfficeData();
            if ($sales_office_no != "") {
                $msd_so_data_val->no = $sales_office_no;
            } else {
                $msd_so_data_val->company = $company;
            }
            $msd_so_data_val->deleted = 0;
            /* Get all MSD valid sales office */
            $so_params = '<GetSalesOfficeCriteria xsi:type="urn:GetSalesOfficeCriteria">';
            $so_params .= $msd_so_data_val->xmlLineStrings();
            $so_params .= '</GetSalesOfficeCriteria>';
            $so_request = new SoapVar($so_params, XSD_ANYXML);
            $so_soap_result = (array) $soap_client->retrieveSalesOfficeByCriteria($so_request);

            if (count($so_soap_result) > 0) {
                foreach ($so_soap_result as $v_so) {
                    $batch_data = array_chunk($msd_promo_data, DownloadConnector::BATCH_LIMIT);

                    foreach ($batch_data as $key => $batch) {
                        $batch_params = '<GetPromotionCriteria xsi:type="urn:GetPromotionCriteriaArray" soap-enc:arrayType="urn:GetPromotionCriteria[]">';
                        foreach ($batch as $line) {
                            $line->sales_office_no = $v_so->no;
                            $line->short_desc = $v_so->short_description;
                            $batch_params .= $line->xmlArrayLineStrings();
                        }
                        $batch_params .= '</GetPromotionCriteria>';
                        $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                        $soap_result = (array) $soap_client->saveBatchCachePromotion($batch_request);
						print_r("Saved ". $soap_result['total_rows'] ." of total ". count($msd_promo_data) ."in promotion Cache\n");
                        /** Log response message */
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE . "| Promotion] " . $soap_result['message'], ""); /* Save log info message */
                        /** Log total rows */
                        if ($key === 0) {
                            Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                        } else {
                            Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                        }
                        /** Log failed rows */
                        Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                        /** Log response error */
                        if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                            foreach ($soap_result['error'] as $v_err_msg) {
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE . "| Promotion]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                            }
                        }
                    }
					
				foreach($msd_promo_data as $promo) {
                        if (isset($promo->promotionFOC) && count($promo->promotionFOC) > 0) {
                            $batch_data = array_chunk($promo->promotionFOC, DownloadConnector::BATCH_LIMIT);

                            foreach ($batch_data as $key => $batch) {
                                $batch_params = '<GetBatchPromotionFOCCriteria xsi:type="urn:GetBatchPromotionFOCCriteriaArray" soap-enc:arrayType="urn:GetPromotionDiscountCriteria[]">';
                                foreach ($batch as $line) {
                                    $line->sales_office_no = $v_so->no;
                                    $batch_params .= $line->xmlArrayLineStrings();
                                }
                                $batch_params .= '</GetBatchPromotionFOCCriteria>';
                                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                                $soap_result = (array) $soap_client->saveBatchCacheFOC($batch_request);
								print_r("Saved ". $soap_result['total_rows'] ." of total ".count($promo->promotionFOC) ." in FOC Cache\n");
                                /** Log response message */
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE . " | Promotion Discount] " . $soap_result['message'], ""); /* Save log info message */
                                /** Log total rows */
                                if ($key === 0) {
                                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                                } else {
                                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                                }
                                /** Log failed rows */
                                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                                /** Log response error */
                                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                                    foreach ($soap_result['error'] as $v_err_msg) {
                                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  " | Promotion Discount]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                                    }
                                }
                            }
                        }

                        if (isset($promo->promotionLocation) && count($promo->promotionLocation) > 0) {
                            $batch_data = array_chunk($promo->promotionLocation, DownloadConnector::BATCH_LIMIT);

                            foreach ($batch_data as $key => $batch) {
                                $batch_params = '<GetBatchPromotionLocationCriteria xsi:type="urn:GetPromotionLocationCriteriaArray" soap-enc:arrayType="urn:GetPromotionLocationCriteria[]">';
                                foreach ($batch as $line) {
										$line->sales_office_no = $v_so->no;
										$line->short_desc = $v_so->short_description;
                                    $batch_params .= $line->xmlArrayLineStrings();
                                }
                                $batch_params .= '</GetBatchPromotionLocationCriteria>';
                                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                                $soap_result = (array) $soap_client->saveBatchCacheLocation($batch_request);
								print_r("Saved ". $soap_result['total_rows'] ." of total ".count($promo->promotionLocation) ." in FOC Cache\n");
                                /** Log response message */
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE . " | Promotion Location] " . $soap_result['message'], ""); /* Save log info message */
                                /** Log total rows */
                                if ($key === 0) {
                                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                                } else {
                                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                                }
                                /** Log failed rows */
                                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                                /** Log response error */
                                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                                    foreach ($soap_result['error'] as $v_err_msg) {
                                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  " | Promotion Location]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                                    }
                                }
                            }
                        }
                        if (isset($promo->promotionDiscount) && count($promo->promotionDiscount) > 0) {
                            $batch_data = array_chunk($promo->promotionDiscount, 1000);

                            foreach ($batch_data as $key => $batch) {
                                $batch_params = '<GetBatchPromotionDiscountCriteria xsi:type="urn:GetPromotionDiscountCriteriaArray" soap-enc:arrayType="urn:GetPromotionDiscountCriteria[]">';
                                foreach ($batch as $line) {
                                    $batch_params .= $line->xmlArrayLineStrings();
                                }
                                $batch_params .= '</GetBatchPromotionDiscountCriteria>';
                                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                                $soap_result = (array) $soap_client->saveBatchCacheDiscount($batch_request);
                                /** Log response message */
                                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE . " | Promotion Discount] " . $soap_result['message'], ""); /* Save log info message */
                                /** Log total rows */
                                if ($key === 0) {
                                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                                } else {
                                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                                }
                                /** Log failed rows */
                                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                                /** Log response error */
                                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                                    foreach ($soap_result['error'] as $v_err_msg) {
                                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  " | Promotion Discount]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                                    }
                                }
                            }
                        }
                    }
                }
            }

            foreach($msd_promo_data as $promo) {
				print_r("Saving ". $promo->no ." in database \n");
                if(count($promo->promotionFOC) <= 0) {
                    continue;
                }
                $with_sql = "SELECT DISTINCT REPLACE(CONCAT(a.location_code,b.no, d.scheme_item, d.uom), '-', '') AS deal_no, a.request_no as memo_doc_no,
                b.sales_office_no as sales_office_no, d.promotion_no as promo_no, b.name as deal_desc, (CONCAT(d.scheme_item, '-', d.uom)) as prod_no_1, d.min as deal_qty1, 
                (CONCAT( d.foc_item , '-',  d.foc_uom ))as prod_no_2, d.unit_price as deal_amt, d.foc_qty as deal_qty2, b.start_date as deal_fromdate,
                b.end_date as deal_todate, d.ms_dynamics_key as ms_dynamics_key,
                '". DownloadConnector::MSD_LOGGER_NAME."' as added_by, 1 as msd_synced, loc.code as outlet_code
                FROM cache_promotion_location a 
                INNER JOIN cache_promotion b ON a.promotion_no = b.no AND a.request_no = b.`request_no`
                INNER JOIN location loc on a.location_id = loc.id
                INNER JOIN cache_promotion_foc d on d.promotion_no = b.no AND b.request_no = d.`request_no`
                WHERE a.request_no = '". $request_no ."' 
                AND b.request_no = '". $request_no ."' 
                AND d.request_no = '". $request_no ."' 
                AND a.promotion_no = '" . $promo->no . "'";

                $create_sql = "INSERT IGNORE INTO deals_promotion (deal_no, memo_doc_no, sales_office_no, promo_no, deal_desc, prod_no_1, deal_qty1, prod_no_2, deal_amt, deal_qty2, deal_fromdate, deal_todate,  ms_dynamics_key,
                added_by, msd_synced, outlet_code) 
                ". $with_sql . " AND REPLACE(CONCAT(a.location_code,b.no, d.scheme_item, d.uom), '-', '')  NOT IN ( SELECT deal_no FROM deals_promotion) group by deal_no" ;

                $update_sql = "UPDATE deals_promotion AS disc, (". $with_sql ." AND REPLACE(CONCAT(a.location_code,b.no, d.scheme_item, d.uom), '-', '')  IN ( SELECT deal_no FROM deals_promotion)) AS updt SET
                disc.memo_doc_no = updt.memo_doc_no, disc.deal_no = updt.deal_no, disc.deal_qty1 = updt.deal_qty1, disc.sales_office_no = updt.sales_office_no, disc.promo_no = updt.promo_no,
                disc.prod_no_2 = updt.prod_no_2,  disc.deal_amt = updt.deal_amt,  disc.prod_no_1 = updt.prod_no_1,
                disc.deal_qty2 = updt.deal_qty2, disc.deal_fromdate = updt.deal_fromdate,
                disc.deal_todate = updt.deal_todate, disc.deal_desc = updt.deal_desc,
                disc.ms_dynamics_key = updt.ms_dynamics_key, disc.added_by = updt.added_by, disc.msd_synced = 1, disc.outlet_code =  updt.outlet_code
                WHERE updt.deal_no = disc.deal_no AND 
                (
                disc.deal_no <> updt.deal_no OR disc.deal_qty1 <> updt.deal_qty1 OR disc.memo_doc_no <> updt.memo_doc_no OR disc.prod_no_1 <> updt.prod_no_1  OR disc.deal_desc <> updt.deal_desc  OR
                disc.prod_no_2 <> updt.prod_no_2 OR disc.sales_office_no <> updt.sales_office_no OR disc.deal_amt <> updt.deal_amt OR
                disc.deal_qty2 <> updt.deal_qty2 OR disc.deal_fromdate <> updt.deal_fromdate OR
                disc.deal_todate <> updt.deal_todate 
                )";
                try {
                    $item_create = DB::insert($with_sql . " AND REPLACE(CONCAT(a.location_code,b.no, d.scheme_item, d.uom), '-', '') NOT IN ( SELECT deal_no FROM deals_promotion)");
                    DB::insert($create_sql);
                    $item_update = DB::insert($with_sql . " AND REPLACE(CONCAT(a.location_code,b.no, d.scheme_item, d.uom), '-', '')  IN ( SELECT deal_no FROM deals_promotion)");
                    DB::update($update_sql);
                    $message =  "Processed ". $promo->no .". Created " . $item_create . " items." . 
                    " Updated ". $item_update . " items.";
                } catch (Exception $exc) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE  . "]" . $exc->getMessage(), ""); /* Save log error message */
                }
                
                
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "]" . $message, ""); /* Save log error message */
            }

			if(is_array($soap_result))
				$soap_result = json_encode($soap_result);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DiscountCaseData::MODULE_NAME_DISCOUNT_CASE .  "]" . $soap_result, ""); /* Save log error message */
			gc_collect_cycles();
        }
    }
    

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (05/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPromotionLine($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->PromotionSchemeLineSubform)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionSchemeLineSubform) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionSchemeLineSubform as $value) {
                    $msd_data_val = new PromotionDetailData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        switch ($att_key) {
                            case "Item_No":
                                $uom = isset($value->Item_UOM) ? $value->Item_UOM : "";
                                $msd_data_val->product_no = $att_value . '-' . $uom; // Append UOM required in CMOS
                        }
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new PromotionDetailData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PromotionSchemeLineSubform) as $att_key => $att_value) {
                    switch ($att_key) {
                        case "Item_No":
                            $uom = isset($msd_soap_result->ReadMultiple_Result->PromotionSchemeLineSubform->Item_UOM) ? $$msd_soap_result->ReadMultiple_Result->PromotionSchemeLineSubform->Item_UOM : "";
                            $msd_data_val->product_no = $att_value . '-' . $uom; // Append UOM required in CMOS
                    }
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", PromotionDetailData::MODULE_PROMOTION_DETAIL);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->PromotionSchemeLineSubform);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchPromotionDetailCriteria xsi:type="urn:GetPromotionDetailCriteriaArray" soap-enc:arrayType="urn:GetPromotionDetailCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchPromotionDetailCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchPromotionDetail($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . PromotionDetailData::MODULE_NAME_PROMOTION_DETAIL . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . PromotionDetailData::MODULE_NAME_PROMOTION_DETAIL . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . PromotionDetailData::MODULE_NAME_PROMOTION_DETAIL . "] No maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (04/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPromotionCustomer($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";

        $company = isset($data['company']) ? $data['company'] : "";
        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->PromotionCustomerList)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionCustomerList) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionCustomerList as $value) {
                    $msd_data_val = new PromotionLocationData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->sales_office_no = "";
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new PromotionLocationData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PromotionCustomerList) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->sales_office_no = "";
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", PromotionLocationData::MODULE_PROMOTION_LOCATION);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->PromotionCustomerList);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchPromotionLocationCriteria xsi:type="urn:GetPromotionLocationCriteriaArray" soap-enc:arrayType="urn:GetPromotionLocationCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchPromotionLocationCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchPromotionLocation($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . PromotionLocationData::MODULE_NAME_PROMOTION_LOCATION . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . PromotionLocationData::MODULE_NAME_PROMOTION_LOCATION . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . PromotionLocationData::MODULE_NAME_PROMOTION_LOCATION . "] No maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (05/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPromotionDiscountLine($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform as $value) {
                    $msd_data_val = new PromotionDiscountLineData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        switch ($att_key) {
                            case "Scheme_Item":
                                $uom = isset($value->Scheme_Item_UOM) ? $value->Scheme_Item_UOM : "";
                                $msd_data_val->product_no = $att_value . '-' . $uom; // Append UOM required in CMOS
                        }
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new PromotionDiscountLineData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform) as $att_key => $att_value) {
                    switch ($att_key) {
                        case "Scheme_Item":
                            $uom = isset($msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform->Scheme_Item_UOM) ? $msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform->Scheme_Item_UOM : "";
                            $msd_data_val->product_no = $att_value . '-' . $uom; // Append UOM required in CMOS
                    }
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", PromotionDiscountLineData::MODULE_PROMOTION_DISCOUNT);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->PromotionSchemeDiscountSubform);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchPromotionDiscountCriteria xsi:type="urn:GetPromotionDiscountCriteriaArray" soap-enc:arrayType="urn:GetPromotionDiscountCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchPromotionDiscountCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchPromotionDiscount($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . PromotionDiscountLineData::MODULE_NAME_PROMOTION_DISCOUNT . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . PromotionDiscountLineData::MODULE_NAME_PROMOTION_DISCOUNT . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . PromotionDiscountLineData::MODULE_NAME_PROMOTION_DISCOUNT . "] No maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (05/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPromotionBudget($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->PromotionSalesOfficeWise)) {
            if (count($msd_soap_result->ReadMultiple_Result->PromotionSalesOfficeWise) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PromotionSalesOfficeWise as $value) {
                    $msd_data_val = new PromotionBudgetData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new PromotionBudgetData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PromotionSalesOfficeWise) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", PromotionBudgetData::MODULE_PROMOTION_BUDGET);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->PromotionSalesOfficeWise);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchPromotionBudgetCriteria xsi:type="urn:GetPromotionBudgetCriteriaArray" soap-enc:arrayType="urn:GetPromotionBudgetCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchPromotionBudgetCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchPromotionBudget($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . PromotionBudgetData::MODULE_NAME_PROMOTION_BUDGET . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(PromotionBudgetData::MODULE_NAME_PROMOTION_BUDGET) . " maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (05/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesPrice($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->SalesPriceService)) {
            if (count($msd_soap_result->ReadMultiple_Result->SalesPriceService) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->SalesPriceService as $value) {
                    $msd_data_val = new SalesPriceData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        switch ($att_key) {
                            case "ItemNo":
                                $uom = isset($value->UnitofMeasureCode) ? $value->UnitofMeasureCode : "";
                                $msd_data_val->product_no = $att_value . '-' . $uom; // Append UOM required in CMOS\
                                break;
                            case "UnitPrice":
                                $att_value = DecimalTruncator::truncate($att_value, 2);
                                break;
                        }
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->rate_code = 2;
                    $msd_data_val->msd_synced = 1;
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new SalesPriceData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->SalesPriceService) as $att_key => $att_value) {
                    switch ($att_key) {
                        case "ItemNo":
                            $uom = isset($msd_soap_result->ReadMultiple_Result->SalesPriceService->UnitofMeasureCode) ? $msd_soap_result->ReadMultiple_Result->SalesPriceService->UnitofMeasureCode : "";
                            $msd_data_val->product_no = $att_value . '-' . $uom; // Append UOM required in CMOS
                            break;
                        case "UnitPrice":
                            $att_value = DecimalTruncator::truncate($att_value, 2);
                            break;

                    }
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->rate_code = 2;
                $msd_data_val->msd_synced = 1;
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesPriceData::MODULE_SALES_PRICE);
            Globals::saveJsonFile($file_name, $msd_data);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        /* Sync Sales Price by batch thru ABI NOC Web Service */
        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchDiscountQualifyingCriteria xsi:type="urn:GetDiscountQualifyingCriteriaArray" soap-enc:arrayType="urn:GetDiscountQualifyingCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchDiscountQualifyingCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchDiscountQualifying($batch_request); /* Convert response as array */
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . SalesPriceData::MODULE_NAME_SALES_PRICE . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . SalesPriceData::MODULE_NAME_SALES_PRICE . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . SalesPriceData::MODULE_NAME_SALES_PRICE . "] No maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (04/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncCustomerDiscount($method, $url, $data, $trigger_id = null)
    {
        /* ABI NOC Central WS */
        $soap_client = Globals::soapClientABINOCCentralWS();
        /* MS Dynamics API */
        $rest_result = Globals::callRESTAPI($method, $url, $data);
        $msd_data = json_decode($rest_result, true);
        $total_rows = count($msd_data);
        /* Save generated response as file backup */
        $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", DownloadConnector::MODULE_LOCATION_DISCOUNT);
        Globals::saveJsonFile($file_name, $msd_data);

        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        $item_code_uom_suffix = Utils::itemCodeSuffix();

        /* Sync Customer by batch thru ABI NOC Web Service */
        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchDiscountQualifyingCriteria xsi:type="urn:GetDiscountQualifyingCriteriaArray" soap-enc:arrayType="urn:GetDiscountQualifyingCriteria[]">';
                foreach ($batch as $line) {
                    /* Fixed values */
                    $line['SALES_OFFICE_NO'] = "";
                    $line['SUB_SALES_OFFICE_NO'] = "";
                    $line['SHORT_DESCRIPTION'] = "";
                    $line['SKU_NO'] = "";
                    $line['RATE_CODE'] = "1"; // Percentage discount
                    $line['SYNCED_BY'] = DownloadConnector::MSD_LOGGER_NAME;

                    $batch_params .= '<item>
                                        <discount_code xsi:type="xsd:string">' . $line['Code'] . '</discount_code>
                                        <pricing_type_code xsi:type="xsd:string">' . $line['Sales_Code'] . '</pricing_type_code>
                                        <currency_code xsi:type="xsd:string">' . $line['Currency_Code'] . '</currency_code>
                                        <start_date xsi:type="xsd:string">' . $line['Starting_Date'] . '</start_date>
                                        <percentage_rate_1 xsi:type="xsd:string">' . $line['Line_Discount_Percent'] . '</percentage_rate_1>
                                        <sales_type xsi:type="xsd:string">' . $line['Sales_Type'] . '</sales_type>
                                        <min xsi:type="xsd:integer">' . $line['Minimum_Quantity'] . '</min>
                                        <end_date xsi:type="xsd:string">' . $line['Ending_Date'] . '</end_date>
                                        <price_function_code xsi:type="xsd:string">' . $line['Type'] . '</price_function_code>
                                        <uom xsi:type="xsd:string">' . $line['Unit_of_Measure_Code'] . '</uom>
                                        <product_no xsi:type="xsd:string">' . $line['SKU_NO'] . '</product_no>
                                        <rate_code xsi:type="xsd:string">' . $line['RATE_CODE'] . '</rate_code>
                                        <sales_office_no xsi:type="xsd:string">' . $line['SALES_OFFICE_NO'] . '</sales_office_no>
                                        <sub_sales_office_no xsi:type="xsd:string">' . $line['SUB_SALES_OFFICE_NO'] . '</sub_sales_office_no>
                                        <short_description xsi:type="xsd:string">' . $line['SHORT_DESCRIPTION'] . '</short_description>
                                        <centrix_synced xsi:type="xsd:integer">0</centrix_synced>
                                        <onesoas_synced xsi:type="xsd:integer">0</onesoas_synced>
                                        <sys_21_synced xsi:type="xsd:integer">0</sys_21_synced>
                                        <msd_synced xsi:type="xsd:integer">1</msd_synced>
                                        <added_by xsi:type="xsd:string">' . $line['SYNCED_BY'] . '</added_by>
                                        <updated_by xsi:type="xsd:string">' . $line['SYNCED_BY'] . '</updated_by>
                                        <deleted_by xsi:type="xsd:string"></deleted_by>
                                        <deleted xsi:type="xsd:integer">0</deleted>
                                    </item>';
                }
                $batch_params .= '</GetBatchDiscountQualifyingCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);

                $soap_result = (array) $soap_client->saveBatchDiscountQualifying($batch_request); /* Convert response as array */
                /** Log response message */
                Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_DISCOUNT . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(DownloadConnector::MODULE_NAME_LOCATION_DISCOUNT) . " maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (05/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */

    public static function syncMSDStockInSalesmanWarehouse($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $batch_enabled = isset($data['batch_enabled']) && $data['batch_enabled'] ? true : false;

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        $msd_data = array();

        $msd_init_data = isset($msd_soap_result->ReadMultiple_Result->DownloadStockinSalesmanWarehouse) ? $msd_soap_result->ReadMultiple_Result->DownloadStockinSalesmanWarehouse : [];
        
		if (count($msd_init_data) > 0) {
			if(!is_array($msd_init_data))
				$msd_init_data = [$msd_init_data];
            foreach ($msd_init_data as $value) {
				if(!isset($value->Unit_of_Measure_Code )) {
					continue;
				}
                 if(!in_array($value->Unit_of_Measure_Code , ["CSEF", "SHLE", "BTLF", "BTLE"]) ) {
                    continue;
                }
                $msd_data_val = new InventoryData();
                foreach (get_object_vars($value) as $att_key => $att_value) {
                    switch ($att_key) {
                        case "Item_No":
                            $uom = isset($value->Unit_of_Measure_Code) ? $value->Unit_of_Measure_Code : "";
                            $msd_data_val->sku_code = $att_value . '-' . $uom; // Append UOM required in CMOS
                            break;
                        case "Unit_of_Measure_Code":
                            $uom = strlen($att_value) == 4 ? substr($att_value, 0, -1) : $att_value; // Remove stock type from base UOM						
                            $msd_data_val->uom_code = $uom;
                            break;
                        default:
                            $msd_data_val->setMSD($att_key, $att_value);
                            break;
                    }
                }
                $msd_data_val->created_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", InventoryData::MODULE_INVENTORY);
            Globals::saveJsonFile($file_name, $msd_init_data);
        }
        $total_rows = count($msd_data);
        if (!$batch_enabled)
            Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        /* Sync Stock in Salesman Warehouse by batch thru ABI NOC Web Service */
        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            /* Sync Outgoing Notification */
            if(isset($data['withdrawal_code'])) {
                $withd_code = $data['withdrawal_code'];
                $outgoing_model = OutgoingNotification::where('withdrawal_code', '=', $withd_code)
                ->first();

                if($outgoing_model) {
                    $outgoing_model->document_no = isset($data['params']['Document_No']) ? $data['params']['Document_No'] : null;
                    if($outgoing_model->save()) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InventoryData::MODULE_NAME_INVENTORY . "] " . " document_no " .  $outgoing_model->document_no . " synced with withdrawal " . $withd_code, "");
                    }
                }

            }

            /* Sync Inventory */
            foreach ($batch_data as $key => $batch) {

                $insert_breakdown = [];

                $batch_params = '<GetBatchInventoryCriteria xsi:type="urn:GetInventoryCriteriaArray" soap-enc:arrayType="urn:GetInventoryCriteria[]">';
                foreach ($batch as $line) {
					if(!isset($data['withdrawal_code']))
						continue;
                    $sku = Sku::select('stock_type')->where('code', $line->sku_code)->where('deleted', 0)->where('msd_synced', 1)->first();
                    $breakdown = InventoryBreakdown::where('zone_code', $line->zone_code)
                        ->where('sku_code', $line->sku_code)
                        ->where('withdrawal_code', $data['withdrawal_code'])
                        ->first();
					if(!isset($sku)) {
						continue;
					}

                    if(isset($line->document_no) && $line->document_no != "" && $breakdown === null) {
                        $insert_breakdown[] = [
                            'company_id' =>  Params::values()['abi_wms_company_id'], 
                            'quantity' => $line->qty, 
                            'sales_office_no' => $sales_office_no, 
                            'zone_code' => $line->zone_code, 
                            'sku_code' => $line->sku_code, 
                            'reference_no' => $line->reference_no, 
                            'withdrawal_code' => isset($data['withdrawal_code']) ? $data['withdrawal_code'] : $line->withdrawal_code,
                            'stock_type' => $sku->stock_type,
                            'expiration_date' => $line->expiration_date,
                            'transaction_date' =>  date("Y-m-d H:i:s"), 
                            'created_by' => DownloadConnector::MSD_LOGGER_NAME
                        ];
                    }

                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchInventoryCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchInventory($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InventoryData::MODULE_NAME_INVENTORY . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], $batch_enabled); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InventoryData::MODULE_NAME_INVENTORY . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                    }
                }
				if(!empty($insert_breakdown)) {
                    InventoryBreakdown::insert($insert_breakdown);
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InventoryData::MODULE_NAME_INVENTORY . "] Inserted inventory_breakdown data", ""); /* Save log info message */
                
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InventoryData::MODULE_NAME_INVENTORY . "] No " . strtolower(InventoryData::MODULE_NAME_INVENTORY) . " found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (05/04/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncPendingCustomerCreationRequest($method, $url, $data, $trigger_id = null)
    {
        /* ABI NOC Central WS */
        $soap_client = Globals::soapClientABINOCCentralWS();
        /* MS Dynamics API */
        $rest_result = Globals::callRESTAPI($method, $url, $data);
        $msd_data = json_decode($rest_result, true);
        $total_rows = count($msd_data);
        /* Save generated response as file backup */
        $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", DownloadConnector::MODULE_PENDING_LOCATION_CREATION);
        Globals::saveJsonFile($file_name, $msd_data);

        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        /* Sync Pending Customer Creation Request by batch thru ABI NOC Web Service */
        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchLocationCriteria xsi:type="urn:GetLocationCriteriaArray" soap-enc:arrayType="urn:GetLocationCriteria[]">';
                foreach ($batch as $line) {
                    /* Fixed values */
                    $line['SALES_OFFICE_NO'] = "123456";
                    $line['SUB_SALES_OFFICE_NO'] = "123456";
                    $line['SHORT_DESCRIPTION'] = "1GO";
                    $line['STATUS'] = 1; // $line['Status']
                    $line['SYNCED_BY'] = DownloadConnector::MSD_LOGGER_NAME;

                    $batch_params .= '<item>
                                        <ms_dynamics_key xsi:type="xsd:string">1</ms_dynamics_key>
                                        <sales_office_no xsi:type="xsd:string">' . $line['SALES_OFFICE_NO'] . '</sales_office_no>
                                        <sub_sales_office_no xsi:type="xsd:string">' . $line['SUB_SALES_OFFICE_NO'] . '</sub_sales_office_no>
                                        <short_description xsi:type="xsd:string">' . $line['SHORT_DESCRIPTION'] . '</short_description>
                                        <code xsi:type="xsd:string">' . $line['NCCR_No'] . '</code>
                                        <name xsi:type="xsd:string">' . $line['Outlet_Name'] . '</name>
                                        <code2 xsi:type="xsd:string">' . $line['Proposed_Customer_No'] . '</code2>
                                        <owner_name xsi:type="xsd:string">' . $line['Owner_Name'] . '</owner_name>
                                        <comments xsi:type="xsd:string">' . $line['Comments'] . '</comments>
                                        <status xsi:type="xsd:integer">' . $line['STATUS'] . '</status>
                                        <centrix_synced xsi:type="xsd:integer">0</centrix_synced>
                                        <onesoas_synced xsi:type="xsd:integer">0</onesoas_synced>
                                        <sys_21_synced xsi:type="xsd:integer">0</sys_21_synced>
                                        <added_by xsi:type="xsd:string">' . $line['SYNCED_BY'] . '</added_by>
                                        <updated_by xsi:type="xsd:string">' . $line['SYNCED_BY'] . '</updated_by>
                                        <deleted_by xsi:type="xsd:string"></deleted_by>
                                        <deleted xsi:type="xsd:integer">0</deleted>
                                    </item>';
                }
                $batch_params .= '</GetBatchLocationCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);

                $soap_result = (array) $soap_client->saveBatchLocation($batch_request); /* Convert response as array */
                /** Log response message */
                Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_PENDING_LOCATION_CREATION . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(DownloadConnector::MODULE_NAME_PENDING_LOCATION_CREATION) . " maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCustomerPriceGroup($method, $url, $data, $trigger_id = null)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->CustomerPriceGroup)) {
            if (count($msd_soap_result->ReadMultiple_Result->CustomerPriceGroup) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->CustomerPriceGroup as $value) {
                    $msd_data_val = new CustomerPriceGroupData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new CustomerPriceGroupData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->CustomerPriceGroup) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", CustomerPriceGroupData::MODULE_CUSTOMER_PRICE_GROUP);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->CustomerPriceGroup);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchCustomerPriceGroupCriteria xsi:type="urn:GetCustomerPriceGroupCriteriaArray" soap-enc:arrayType="urn:GetCustomerPriceGroupCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchCustomerPriceGroupCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchCustomerPriceGroup($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . CustomerPriceGroupData::MODULE_NAME_CUSTOMER_PRICE_GROUP . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(CustomerPriceGroupData::MODULE_NAME_CUSTOMER_PRICE_GROUP) . " maintenance found.", ""); /* Save log info message */
        }
    }



    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCustomerPostingGroup($method, $url, $data, $trigger_id = null)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->CustomerPostingGroup)) {
            if (count($msd_soap_result->ReadMultiple_Result->CustomerPostingGroup) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->CustomerPostingGroup as $value) {
                    $msd_data_val = new CustomerPostingGroupData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new CustomerPostingGroupData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->CustomerPostingGroup) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", CustomerPostingGroupData::MODULE_CUSTOMER_POSTING_GROUP);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->CustomerPostingGroup);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchCustomerPostingGroupCriteria xsi:type="urn:GetCustomerPostingGroupCriteriaArray" soap-enc:arrayType="urn:GetCustomerPostingGroupCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchCustomerPostingGroupCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchCustomerPostingGroup($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . CustomerPostingGroupData::MODULE_NAME_CUSTOMER_POSTING_GROUP . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(CustomerPostingGroupData::MODULE_NAME_CUSTOMER_POSTING_GROUP) . " maintenance found.", ""); /* Save log info message */
        }
    }


    /**
     * Turns data obtained from client RESTful API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDCustomerDiscountGroup($method, $url, $data, $trigger_id = null)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->CustomerDiscountGroup)) {
            if (count($msd_soap_result->ReadMultiple_Result->CustomerDiscountGroup) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->CustomerDiscountGroup as $value) {
                    $msd_data_val = new CustomerDiscountGroupData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new CustomerPriceGroupData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->CustomerDiscountGroup) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", CustomerDiscountGroupData::MODULE_CUSTOMER_DISCOUNT_GROUP);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->CustomerDiscountGroup);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchCustomerDiscountGroupCriteria xsi:type="urn:GetCustomerDiscountGroupCriteriaArray" soap-enc:arrayType="urn:GetCustomerDiscountGroupCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchCustomerDiscountGroupCriteria>';

                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchCustomerDiscountGroup($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . CustomerDiscountGroupData::MODULE_NAME_CUSTOMER_DISCOUNT_GROUP . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(CustomerDiscountGroupData::MODULE_NAME_CUSTOMER_DISCOUNT_GROUP) . " maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client <SD API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDGenBusPostingGroup($method, $url, $data, $trigger_id = null)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->GenBusinessPostingGroup)) {
            if (count($msd_soap_result->ReadMultiple_Result->GenBusinessPostingGroup) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->GenBusinessPostingGroup as $value) {
                    $msd_data_val = new GenBusPostingGroupData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new GenBusPostingGroupData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->GenBusinessPostingGroup) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", GenBusPostingGroupData::MODULE_GEN_BUS_POSTING_GROUP);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->GenBusinessPostingGroup);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchGenBusPostingGroupCriteria xsi:type="urn:GetGenBusPostingGroupCriteriaArray" soap-enc:arrayType="urn:GetGenBusPostingGroupCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchGenBusPostingGroupCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchGenBusPostingGroup($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . GenBusPostingGroupData::MODULE_NAME_GEN_BUS_POSTING_GROUP . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(GenBusPostingGroupData::MODULE_NAME_GEN_BUS_POSTING_GROUP) . " maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client <SD API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDVATBusPostingGroup($method, $url, $data, $trigger_id = null)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->VATBusinessPostingGroup)) {
            if (count($msd_soap_result->ReadMultiple_Result->VATBusinessPostingGroup) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->VATBusinessPostingGroup as $value) {
                    $msd_data_val = new VATBusPostingGroupData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new VATBusPostingGroupData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->VATBusinessPostingGroup) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", VATBusPostingGroupData::MODULE_VAT_BUS_POSTING_GROUP);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->VATBusinessPostingGroup);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchVatBusPostingGroupCriteria xsi:type="urn:GetVatBusPostingGroupCriteriaArray" soap-enc:arrayType="urn:GetVatBusPostingGroupCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchVatBusPostingGroupCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchVatBusPostingGroup($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . VATBusPostingGroupData::MODULE_NAME_VAT_BUS_POSTING_GROUP . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(VATBusPostingGroupData::MODULE_NAME_VAT_BUS_POSTING_GROUP) . " maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (09/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDDistributionChannel($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $batch_enabled = isset($data['batch_enabled']) && $data['batch_enabled'] ? true : false;

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->TradeChannelCode)) {
            if (count($msd_soap_result->ReadMultiple_Result->TradeChannelCode) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->TradeChannelCode as $value) {
                    $msd_data_val = new DistributionChannelData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->name = $msd_data_val->description;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new DistributionChannelData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->TradeChannelCode) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->name = $msd_data_val->description;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", DistributionChannelData::MODULE_DISTRIBUTION_CHANNEL);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->TradeChannelCode);
        }
        $total_rows = count($msd_data);
        if (!$batch_enabled)
            Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchChannelTypeCriteria xsi:type="urn:GetChannelTypeCriteriaArray" soap-enc:arrayType="urn:GetChannelTypeCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchChannelTypeCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchChannelType($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], $batch_enabled); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . " maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (12/09/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSubChannel($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $batch_enabled = isset($data['batch_enabled']) && $data['batch_enabled'] ? true : false;

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->SubTradeChannelCode)) {
            if (count($msd_soap_result->ReadMultiple_Result->SubTradeChannelCode) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->SubTradeChannelCode as $value) {
                    $msd_data_val = new SubChannelData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
					if($msd_data_val->code == null) {
						continue;
					}
                    $dc_model = DistributionChannel::where('code', '=',  substr($msd_data_val->code, 0, 3) )->where('msd_synced', '=',  1 )->first();
                    if(!$dc_model) {
                        continue;
                    }
                    $msd_data_val->channel_type_id = $dc_model->id;
                    $msd_data_val->channel_type_code = $dc_model->code;
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new SubChannelData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->SubTradeChannelCode) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
				if($msd_data_val->code == "") {
					continue;
				}
                $dc_model = DistributionChannel::select('id, code')->where('code', '=',  substr($msd_data_val->code, 0, 3) );
                if(!$dc_model) {
                    continue;
                }
                $msd_data_val->channel_type_id = $dc_model->id;
                $msd_data_val->channel_type_code = $dc_model->code;
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SubChannelData::MODULE_SUB_CHANNEL);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->SubTradeChannelCode);
        }
        $total_rows = count($msd_data);
        if (!$batch_enabled)
            Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {

            foreach ($msd_data as $key => $batch) {
				try {
					$batch_params = trim($batch->xmlArrayLineStrings());
					$batch_request = new SoapVar($batch_params, XSD_ANYXML);
					$soap_result =  (string) $soap_client->saveSubChannelType($batch_request);
				} catch (\SoapFault $ex) {
					$soap_result = $soap_client->__getLastResponse();
				}
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . SubChannelData::MODULE_NAME_SUB_CHANNEL . "] " . $soap_result, ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, 1, $batch_enabled); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, 1, true); /* Update trigger total rows = existing + response total_rows */
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(SubChannelData::MODULE_NAME_SUB_CHANNEL) . " maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client <SD API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPaymentMethod($method, $url, $data, $trigger_id = null)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->PaymentMethod)) {
            if (count($msd_soap_result->ReadMultiple_Result->PaymentMethod) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PaymentMethod as $value) {
                    $msd_data_val = new PaymentMethodData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new PaymentMethodData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PaymentMethod) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", PaymentMethodData::MODULE_PAYMENT_METHOD);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->PaymentMethod);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchPaymentMethodCriteria xsi:type="urn:GetPaymentMethodCriteriaArray" soap-enc:arrayType="urn:GetPaymentMethodCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchPaymentMethodCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchPaymentMethod($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . PaymentMethodData::MODULE_NAME_PAYMENT_METHOD . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(PaymentMethodData::MODULE_NAME_PAYMENT_METHOD) . " maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client <SD API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPaymentTerms($method, $url, $data, $trigger_id = null)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->PaymentTerms)) {
            if (count($msd_soap_result->ReadMultiple_Result->PaymentTerms) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->PaymentTerms as $value) {
                    $msd_data_val = new PaymentTermsData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new PaymentTermsData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PaymentTerms) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", PaymentTermsData::MODULE_PAYMENT_TERMS);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->PaymentTerms);
        }
        $total_rows = count($msd_data);
        Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchPaymentTermsCriteria xsi:type="urn:GetPaymentTermsCriteriaArray" soap-enc:arrayType="urn:GetPaymentTermsCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchPaymentTermsCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchPaymentTerms($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . PaymentTermsData::MODULE_NAME_PAYMENT_TERMS . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(PaymentTermsData::MODULE_NAME_PAYMENT_TERMS) . " maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDProduct($method, $url, $data, $trigger_id = null)
    {
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		
        $sku_delete_query = Sku::where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no);
        if(isset($data['params']['No']) && $data['params']['No'] != "") {
            $sku_delete_query->where('no', 'like', $data['params']['No'] + '%');
        }
        $sku_delete_query->update(array('deleted'=>1)); 
        
        /* BOM */
        $route = Params::values()['webservice']['abi_msd']['route']['product-bom']['list'];
        $bom_url = Globals::soapABIMSDynamicsURL($route, $company);
        $bom_data = $data;
        if (isset($bom_data['params']['No'])) {
            $bom_data['params'] = [
                'Parent_Item_No' => $bom_data['params']['No'],
            ];
            unset($bom_data['params']['No']);
        }
		else {
			$bom_data['params'] = [];
		}
        $msd_soap_result = Globals::callSoapApiReadMultiple($bom_url, $bom_data);
        $msd_bom_data = [];
        if (isset($msd_soap_result->ReadMultiple_Result->BOMComponentsService)) {
            if (count($msd_soap_result->ReadMultiple_Result->BOMComponentsService) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->BOMComponentsService as $value) {
                    $parent_sku_code = isset($value->Parent_Item_No) ? $value->Parent_Item_No : "";
                    $parent_uom = isset($value->Unit_of_Measure_Code) ? $value->Unit_of_Measure_Code : "";
                    $full_2_code = isset($value->No) ? rtrim($value->No . "-" . $parent_uom, "-") : "";
                    $full_2_unit_case = isset($value->Quantity_per) ? $value->Quantity_per : 0;

                    if (!isset($msd_bom_data[$parent_sku_code])) {
                        $msd_bom_data[$parent_sku_code] = [
                            'full_1_code' => $full_2_code,
                            'full_1_unit_case' => $full_2_unit_case,
                        ];
                    }
					print_r("[".date('Y-m-d H:i:s')."] Synced BOM data of " .  $parent_sku_code ." - " . $full_2_code .": " . $full_2_unit_case."\n");
                }
            } else {
                $parent_sku_code = isset($msd_soap_result->ReadMultiple_Result->BOMComponentsService->Parent_Item_No) ? $msd_soap_result->ReadMultiple_Result->BOMComponentsService->Parent_Item_No : "";
                $parent_uom = isset($msd_soap_result->ReadMultiple_Result->BOMComponentsService->Unit_of_Measure_Code) ? $msd_soap_result->ReadMultiple_Result->BOMComponentsService->Unit_of_Measure_Code : "";
                $full_2_code = isset($msd_soap_result->ReadMultiple_Result->BOMComponentsService->No) ? rtrim($msd_soap_result->ReadMultiple_Result->BOMComponentsService->No . "-" . $parent_uom, "-") : "";
                $full_2_unit_case = isset($msd_soap_result->ReadMultiple_Result->BOMComponentsService->Quantity_per) ? $msd_soap_result->ReadMultiple_Result->BOMComponentsService->Quantity_per : 0;

                if (!isset($msd_bom_data[$parent_sku_code])) {
                    $msd_bom_data[$parent_sku_code] = [
                        'full_1_code' => $full_2_code,
                        'full_1_unit_case' => $full_2_unit_case,
                    ];
                }
            }
        }
        if (isset($data['params']['SystemModifiedAt'])) {
            unset($data['params']['SystemModifiedAt']);
            $data['params']['Last_Date_Modified'] = date('Y-m-d');
        }

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = $so_soap_result = array();

        if (isset($msd_soap_result->ReadMultiple_Result->Items)) {
			print(count($msd_soap_result->ReadMultiple_Result->Items));
            $msd_so_data_val = new SalesOfficeData();
            if ($sales_office_no != "") {
                $msd_so_data_val->no = $sales_office_no;
            } else {
                $msd_so_data_val->msd_synced = 1;
            }
            $msd_so_data_val->deleted = 0;
            /* Get all MSD valid sales office */
            $so_params = '<GetSalesOfficeCriteria xsi:type="urn:GetSalesOfficeCriteria">';
            $so_params .= $msd_so_data_val->xmlLineStrings();
            $so_params .= '</GetSalesOfficeCriteria>';
            $so_request = new SoapVar($so_params, XSD_ANYXML);
            $so_soap_result = (array) $soap_client->retrieveSalesOfficeByCriteria($so_request);
        }

        if (count($so_soap_result) > 0) {
            foreach ($so_soap_result as $v_so) {
                if (count($msd_soap_result->ReadMultiple_Result->Items) > 0) {
                    if(count($msd_soap_result->ReadMultiple_Result->Items) === 1) {
                        $all_items = [$msd_soap_result->ReadMultiple_Result->Items];
                    }
                    else {
                        $all_items = $msd_soap_result->ReadMultiple_Result->Items;
                    }
					
                    foreach ($all_items as $value) {
                        $msd_data_val = new SKUData();
                        foreach (get_object_vars($value) as $att_key => $att_value) {
                            $msd_data_val->setMSD($att_key, $att_value);
                        }

                        $sku_code = isset($value->No) ? $value->No : "";
                        $stock_type = isset($value->_x0031_000000004) ? ($value->_x0031_000000004 == 1 ? 0 : 1) : 1;
                        $base_uom = isset($value->Base_Unit_of_Measure) ? $value->Base_Unit_of_Measure : "";
                        $uom = strlen($base_uom) == 4 ? rtrim($base_uom, ($stock_type == 1 ? "F" : "E")) : $base_uom; // Remove stock type from base UOM

                        if(!in_array($base_uom, ["CSEF", "SHLE", "BTLF", "BTLE"]) ) {
                            continue;
                        }
						$route_sps = Params::values()['webservice']['abi_msd']['route']['sales-price']['list'];
						$url_sps =  Globals::soapABIMSDynamicsURL($route_sps, $company);
						$data_sp = [
						'company' => $data['company'],
						'sales_office_no' => $sales_office_no,
						'params' => array(
							'ItemNo' => $value->No,
							'UnitofMeasureCode' => $base_uom
						),
						];
						// IF Zamboanga Sales office
						if($sales_office_no == "780900") {
							$data_sp['params']['SalesCode'] = 'ZAMBOANGA';
						}
						else {
							$data_sp['params']['SalesCode'] = 'NATIONAL';
						}
						$sp_result = Globals::callSoapApiReadMultiple($url_sps, $data_sp, $sales_office_no);
						if(isset($sp_result->ReadMultiple_Result->SalesPriceService->UnitPrice)) {
							$unit_price = $sp_result->ReadMultiple_Result->SalesPriceService->UnitPrice;
						} 
						else if(isset($sp_result->ReadMultiple_Result->SalesPriceService) and is_array($sp_result->ReadMultiple_Result->SalesPriceService)){
							$latest_price = "";
							$latest_date = strtotime("0001-01-01");
							$now = time(); // Get the current timestamp

							foreach($sp_result->ReadMultiple_Result->SalesPriceService as $price) {
								$cur_time = strtotime($price->StartingDate);
								if($cur_time >= $latest_date && $cur_time <= $now) {
									$latest_date = $cur_time;
									$latest_price = $price->UnitPrice;
								}
							}
							$unit_price = $latest_price;
						}
						else {
							$unit_price = isset($value->Unit_Price) ? $value->Unit_Price : 0;
						}
                        
                        $unit_price = DecimalTruncator::truncate($unit_price, 2);
						
                        $case_unit_price = isset($value->Unit_Price) ? $value->Unit_Price : 0;
                        $shell_sku_code = isset($value->_x0031_000000021) ? $value->_x0031_000000021 : "";
                        //$shell_full_sku_code = $shell_sku_code != "" ? $shell_sku_code . "-SHLF" : "";
                        $shell_empty_sku_code = $shell_sku_code != "" ? $shell_sku_code . "-SHLE" : "";
                        $shell_unit_case = isset($value->Empty_2_Quantity) ? $value->Empty_2_Quantity : 0;
                        $shell_unit_price = isset($value->Empty_2_Cost) ? ($shell_unit_case != 0 ? $value->Empty_2_Cost : 0) : 0;
                        $bottle_sku_code = isset($value->_x0031_000000003) ? $value->_x0031_000000003 : "";
                        //$bottle_full_sku_code = $bottle_sku_code != "" ? $bottle_sku_code . "-BTLF" : "";
                        $bottle_empty_sku_code = $bottle_sku_code != "" ? $bottle_sku_code . "-BTLE" : "";
                        $bottle_unit_case = isset($value->Empty_1_Quantity) ? $value->Empty_1_Quantity : 0;
                        $bottle_unit_price = isset($value->Empty_1_Cost) ? ($bottle_unit_case != 0 ? $value->Empty_1_Cost : 0) : 0;

                        $msd_data_val->code = $sku_code . '-' . $base_uom; // Append UOM required in CMOS
						$msd_data_val->unit_price = $unit_price;
                        $msd_data_val->uom = $uom;
                        $msd_data_val->sell_price = $unit_price;
                        $msd_data_val->stock_type = $stock_type;
                        $msd_data_val->unit_case = isset($msd_bom_data[$msd_data_val->sys_21]) ? $msd_bom_data[$msd_data_val->sys_21]['full_1_unit_case'] : 1;
                        $msd_data_val->full_1_code = isset($msd_bom_data[$msd_data_val->sys_21]) ? $msd_bom_data[$msd_data_val->sys_21]['full_1_code'] : "";
                        $msd_data_val->full_1_unit_case = isset($msd_bom_data[$msd_data_val->sys_21]) ? $msd_bom_data[$msd_data_val->sys_21]['full_1_unit_case'] : 0;
                        $msd_data_val->full_2_code = "";
                        $msd_data_val->full_2_unit_case = 0;
                        $msd_data_val->empty_1_code = $bottle_empty_sku_code;
                        $msd_data_val->empty_1_unit_case = $bottle_unit_case;
                        $msd_data_val->empty_2_code = $shell_empty_sku_code;
                        $msd_data_val->empty_2_unit_case = $shell_unit_case;

                        $msd_data_val->company_code = Globals::getWmsCompanyCode();
                        $msd_data_val->short_description = $v_so->short_description;
                        $msd_data_val->sales_office_no = $v_so->no;
                        $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                        $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                        $msd_data_val->msd_synced = 1;
                        $msd_data_val->sys_21_synced = 0;
                        $msd_data_val->deleted = 0;
                        $msd_data[] = $msd_data_val;
						print_r("[".date('Y-m-d H:i:s')."] Added data of " .  $sku_code ."-" . $base_uom ." \n");
                    }
                }   
            }
            $total_rows = count($msd_data);
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SKUData::MODULE_SKU);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->Items);
            Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

            if ($total_rows > 0) {
                $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

                foreach ($batch_data as $key => $batch) {

                    $batch_params = '<GetBatchSkuCriteria xsi:type="urn:GetSkuCriteriaArray" soap-enc:arrayType="urn:GetSkuCriteria[]">';
                    foreach ($batch as $line) {
                        $batch_params .= $line->xmlArrayLineStrings();
                    }
                    $batch_params .= '</GetBatchSkuCriteria>';
                    $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                    $soap_result = (array) $soap_client->saveBatchSku($batch_request);
                    $soap_result_wms = (array) $soap_client->saveWmsBatchSku($batch_request);
                    /** Log response message */
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . SKUData::MODULE_NAME_SKU . "] " . $soap_result['message'], ""); /* Save log info message */
                    /** Log total rows */
                    if ($key === 0) {
                        Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
                    } else {
                        Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                    }
                    /** Log failed rows */
                    Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                    /** Log response error */
                    if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                        foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . SKUData::MODULE_NAME_SKU . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                        }
                    }
                }
            } else {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . SKUData::MODULE_NAME_SKU . "] No maintenance found.", ""); /* Save log info message */
            }
        }
    }

    /**
     * Turns data obtained from client <SD API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesman($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $batch_enabled = isset($data['batch_enabled']) && $data['batch_enabled'] ? true : false;

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->SalespersonPurchaser)) {
            if (count($msd_soap_result->ReadMultiple_Result->SalespersonPurchaser) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->SalespersonPurchaser as $value) {
                    $msd_data_val = new SalesmanData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new SalesmanData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->SalespersonPurchaser) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            };
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesmanData::MODULE_SALESMAN);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->SalespersonPurchaser);
        }
        $total_rows = count($msd_data);
        if (!$batch_enabled)
            Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {
                $batch_params = '<GetBatchSalesmanCriteria xsi:type="urn:GetSalesmanCriteriaArray" soap-enc:arrayType="urn:GetSalesmanCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchSalesmanCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchSalesman($batch_request);
                $soap_result_wms = (array) $soap_client->saveWmsBatchSalesman($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . SalesmanData::MODULE_NAME_SALESMAN . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], $batch_enabled); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . SalesmanData::MODULE_NAME_SALESMAN . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . SalesmanData::MODULE_NAME_SALESMAN . "] No maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client <SD API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDSalesmanType($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $batch_enabled = isset($data['batch_enabled']) && $data['batch_enabled'] ? true : false;

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();

        if (isset($msd_soap_result->ReadMultiple_Result->ServiceModel)) {
            if (count($msd_soap_result->ReadMultiple_Result->ServiceModel) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->ServiceModel as $value) {
                    $msd_data_val = new SalesmanTypeData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new SalesmanTypeData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->ServiceModel) as $att_key => $att_value) {
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesmanTypeData::MODULE_SALESMAN_TYPE);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->ServiceModel);
        }
        $total_rows = count($msd_data);
        if (!$batch_enabled)
            Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchSalesmanTypeCriteria xsi:type="urn:GetSalesmanTypeCriteriaArray" soap-enc:arrayType="urn:GetSalesmanTypeCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchSalesmanTypeCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchSalesmanType($batch_request);
                $soap_result_wms = (array) $soap_client->saveWmsBatchSalesmanType($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], $batch_enabled); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE . "] No maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDZone($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $batch_enabled = isset($data['batch_enabled']) && $data['batch_enabled'] ? true : false;

        $soap_client = Globals::soapClientABINOCCentralWS();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data);
        $msd_data = array();
		
        if (isset($msd_soap_result->ReadMultiple_Result->Locations)) {
            if (count($msd_soap_result->ReadMultiple_Result->Locations) > 1) {
                foreach ($msd_soap_result->ReadMultiple_Result->Locations as $value) {
                    $msd_data_val = new ZoneData();
                    foreach (get_object_vars($value) as $att_key => $att_value) {
                        switch ($att_key) {
                            case "Code":
                                $msd_data_val->zone_name = $att_value;
                            case "Location_Type":
                                if ($att_value == "Main_WH")
                                    $msd_data_val->is_default_zone = true; // Sales office default zone
                        }
                        $msd_data_val->setMSD($att_key, $att_value);
                    }
                    $msd_data_val->company_code = Globals::getWmsCompanyCode();
                    $msd_data_val->created_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                    $msd_data_val->msd_synced = 1;
                    $msd_data[] = $msd_data_val;
                }
            } else {
                $msd_data_val = new ZoneData();
                foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->Locations) as $att_key => $att_value) {
                    switch ($att_key) {
                        case "Code":
                            $msd_data_val->zone_name = $att_value;
                        case "Location_Type":
                            if ($att_value == "Main_WH")
                                $msd_data_val->is_default_zone = true; // Sales office default zone
                    }
                    $msd_data_val->setMSD($att_key, $att_value);
                }
                $msd_data_val->company_code = Globals::getWmsCompanyCode();
                $msd_data_val->created_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                $msd_data_val->msd_synced = 1;
                $msd_data[] = $msd_data_val;
            }
            /* Save generated response as file backup */
            $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", ZoneData::MODULE_ZONE);
            Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->Locations);
        }
        $total_rows = count($msd_data);
        if (!$batch_enabled)
            Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
        Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

        if ($total_rows > 0) {
            $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

            foreach ($batch_data as $key => $batch) {

                $batch_params = '<GetBatchZoneCriteria xsi:type="urn:GetZoneCriteriaArray" soap-enc:arrayType="urn:GetZoneCriteria[]">';
                foreach ($batch as $line) {
                    $batch_params .= $line->xmlArrayLineStrings();
                }
                $batch_params .= '</GetBatchZoneCriteria>';
                $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                $soap_result = (array) $soap_client->saveBatchZone($batch_request);
                $soap_result_wms = (array) $soap_client->saveWmsBatchZone($batch_request);
                /** Log response message */
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . ZoneData::MODULE_NAME_ZONE . "] " . $soap_result['message'], ""); /* Save log info message */
                /** Log total rows */
                if ($key === 0) {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], $batch_enabled); /* Update trigger total rows */
                } else {
                    Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                }
                /** Log failed rows */
                Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                /** Log response error */
                if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                    foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . ZoneData::MODULE_NAME_ZONE . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . ZoneData::MODULE_NAME_ZONE . "] No maintenance found.", ""); /* Save log info message */
        }
    }

    /**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPostedSalesInvoice($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $soap_client = Globals::soapClientABINOCCentralWS();

        $salesman = Salesman::select('id')->where('msd_synced', '=', 1)->where('sales_office_no', '=', $sales_office_no)->get()->toarray();
        $noc_data = count($salesman) > 0 ?
            SalesOrder::with(['invoice'])
            ->whereDoesntHave('invoice', function ($q) {
                $q->where('status', '!=', 'new');
            })
            ->whereIn('salesman_id', $salesman)
            ->where('transaction_type', '=', 1)
            ->where('msd_synced', '=', 1)
            ->where('deleted', '=', 0)
            ->where('status', '=', 'posted')
           ->whereBetween('sales_order_date', [$date_from, $date_to])
            ->get() : [];
			
        if (count($noc_data) > 0) {
            foreach ($noc_data as $noc_data_model) {
                $soap_data = [];
                $soap_data['params'] = ['Order_No' => $noc_data_model->code];

                $msd_soap_result = Globals::callSoapApiReadMultiple($url, $soap_data, $sales_office_no);
                if (isset($msd_soap_result->ReadMultiple_Result->PostedSalesInvoicesService)) {
                    $msd_data = $msd_soap_result->ReadMultiple_Result->PostedSalesInvoicesService;
                    $total_rows = count($msd_data);
                    $msd_data = $total_rows === 1 ? [$msd_data] : $msd_data;
                    /* Save generated response as file backup */
                    $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", InvoiceData::MODULE_POSTED_INVOICE);
                    Globals::saveJsonFile($file_name, $msd_data);
                    Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

                    /* Sync Invoice by batch thru ABI NOC Web Service */
                    if ($total_rows > 0) {
                        $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

                        foreach ($batch_data as $key => $batch) {
                            $header_data = [];
                            foreach ($batch as $row) {
                                if (!isset($row->No)) {
                                    continue;
                                }
                                if (!isset($header_data[$row->No])) {
                                    $msd_data_val = new InvoiceData();
                                    foreach (get_object_vars($row) as $att_key => $att_value) {
                                        switch ($att_key) {
                                            case "Posting_Date":
                                                $msd_data_val->invoice_date = date("Y-m-d H:i:s", strtotime($row->Posting_Date));
                                                $msd_data_val->invoice_updated = date("Y-m-d H:i:s", strtotime($row->Posting_Date));
                                            case "Delivery_Status":
                                                $msd_data_val->delivered = $att_value ? 1 : 0;
                                        }
                                        $msd_data_val->setMSD($att_key, $att_value);
                                    }
                                    $msd_data_val->code = $row->No;
                                    $msd_data_val->status = "new";
                                    $msd_data_val->created_by = DownloadConnector::MSD_LOGGER_NAME;
                                    $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                                    $msd_data_val->msd_synced = 1;
                                    $header_data[$row->No] = $msd_data_val;
                                }
                            }
                            $batch_params = '<GetBatchInvoiceCriteria xsi:type="urn:GetInvoiceCriteriaArray" soap-enc:arrayType="urn:GetInvoiceCriteria[]">';
                            foreach ($header_data as $line) {
                                $batch_params .= $line->xmlArrayLineStrings();
                            }
                            $batch_params .= '</GetBatchInvoiceCriteria>';
                            $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                            $soap_result = (array) $soap_client->saveBatchInvoice($batch_request);
                            /** Log response message */
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] " . $soap_result['message'], ""); /* Save log info message */
                            /** Log total rows */
                            Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                            /** Log failed rows */
                            Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                            /** Log response error */
                            if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                                foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                                }
                            }
                        }
                    } else {
                        Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] No " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " found.", ""); /* Save log info message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "There's no newly posted " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " transaction.", ""); /* Save log info message */
        }
    }    

    /**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (06/10/2023)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPostedSalesInvoiceBooking($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $soap_client = Globals::soapClientABINOCCentralWS();
        $sales_order_code_arr = isset($data['params']['Order_No']) ? $data['params']['Order_No'] : null;
		
		if(!is_array($sales_order_code_arr)) {
			$sales_order_code_arr = [$sales_order_code_arr];
		}
		foreach($sales_order_code_arr as $sales_order_code) {
			if($sales_order_code == null || $sales_order_code == "")
				continue;
			$data_inv =  [
				'company' => $data['company'],
				'sales_office_no' => $sales_office_no,
				'params' => array(
					'Order_No' => $sales_order_code,
					'Shortcut_Dimension_1_Code' => $data['params']['Shortcut_Dimension_1_Code'] 
				),
			];
			$msd_soap_result = Globals::callSoapApiReadMultiple($url, $data_inv, $sales_office_no);
			if (isset($msd_soap_result->ReadMultiple_Result->PostedSalesInvoicesService)) {
				$msd_data = $msd_soap_result->ReadMultiple_Result->PostedSalesInvoicesService;
				$total_rows = count($msd_data);
				$msd_data = $total_rows === 1 ? [$msd_data] : $msd_data;
				/* Save generated response as file backup */
				$file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", InvoiceData::MODULE_POSTED_INVOICE);
				Globals::saveJsonFile($file_name, $msd_data);
				Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

				/* Sync Invoice by batch thru ABI NOC Web Service */
				if ($total_rows > 0) {
					$batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

					foreach ($batch_data as $key => $batch) {
						$header_data = [];
						foreach ($batch as $row) {
							if (!isset($row->No)) {
								continue;
							}
							if (!isset($header_data[$row->No])) {
								$msd_data_val = new InvoiceData();
								foreach (get_object_vars($row) as $att_key => $att_value) {
									switch ($att_key) {
										case "Posting_Date":
											$msd_data_val->invoice_date = date("Y-m-d H:i:s", strtotime($row->Posting_Date));
											$msd_data_val->invoice_updated = date("Y-m-d H:i:s", strtotime($row->Posting_Date));
										case "Delivery_Status":
											$msd_data_val->delivered = $att_value ? 1 : 0;
									}
									$msd_data_val->setMSD($att_key, $att_value);
								}
								$msd_data_val->code = $row->No;
								$msd_data_val->status = "new";
								$msd_data_val->created_by = DownloadConnector::MSD_LOGGER_NAME;
								$msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
								$msd_data_val->msd_synced = 1;
								$header_data[$row->No] = $msd_data_val;
							}
						}
						$batch_params = '<GetBatchInvoiceCriteria xsi:type="urn:GetInvoiceCriteriaArray" soap-enc:arrayType="urn:GetInvoiceCriteria[]">';
						foreach ($header_data as $line) {
							$batch_params .= $line->xmlArrayLineStrings();
						}
						$batch_params .= '</GetBatchInvoiceCriteria>';
						$batch_request = new SoapVar($batch_params, XSD_ANYXML);
						$soap_result = (array) $soap_client->saveBatchInvoice($batch_request);
						/** Log response message */
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] " . $soap_result['message'], ""); /* Save log info message */
						/** Log total rows */
						Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
						/** Log failed rows */
						Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
						/** Log response error */
						if (isset($soap_result['error']) && $soap_result['error'] > 0) {
							foreach ($soap_result['error'] as $k_e => $v_err_msg) {
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
							}
						}
					}
				} 
			} else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] No " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " found.", ""); /* Save log info message */
			}
		}
    }

	/**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (20/02/2024)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDDirectPostedSalesInvoice($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $soap_client = Globals::soapClientABINOCCentralWS();
		$msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
		$invoice_result = [];

		if (isset($msd_soap_result->ReadMultiple_Result->PostedSalesInvoicesService)) {
			$msd_data = $msd_soap_result->ReadMultiple_Result->PostedSalesInvoicesService;
			$total_rows = count($msd_data);
			$msd_data = $total_rows === 1 ? [$msd_data] : $msd_data;
			/* Save generated response as file backup */
			$file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", InvoiceData::MODULE_POSTED_INVOICE);
			Globals::saveJsonFile($file_name, $msd_data);
			Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

			/* Sync Invoice by batch thru ABI NOC Web Service */
			if ($total_rows > 0) {
				$batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

				foreach ($batch_data as $key => $batch) {
					$header_data = [];
					foreach ($batch as $row) {
						if (!isset($row->No)) {
							continue;
						}
						if (!isset($header_data[$row->No])) {
							$msd_data_val = new InvoiceData();
							foreach (get_object_vars($row) as $att_key => $att_value) {
								switch ($att_key) {
									case "Posting_Date":
										$msd_data_val->invoice_date = date("Y-m-d H:i:s", strtotime($row->Posting_Date));
										$msd_data_val->invoice_updated = date("Y-m-d H:i:s", strtotime($row->Posting_Date));
									case "Delivery_Status":
										$msd_data_val->delivered = $att_value ? 1 : 0;
								}
								$msd_data_val->setMSD($att_key, $att_value);
							}
							$msd_data_val->code = $row->No;
							$msd_data_val->status = "direct download";
							$msd_data_val->created_by = DownloadConnector::MSD_LOGGER_NAME;
							$msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
							$msd_data_val->sales_office_no = $sales_office_no;
							$msd_data_val->msd_synced = 1;
							$header_data[$row->No] = $msd_data_val;
							array_push($invoice_result, $row->No);
						}
					}
					$batch_params = '<GetBatchInvoiceCriteria xsi:type="urn:GetInvoiceCriteriaArray" soap-enc:arrayType="urn:GetInvoiceCriteria[]">';
					foreach ($header_data as $line) {
						$batch_params .= $line->xmlArrayLineStrings();
					}
					$batch_params .= '</GetBatchInvoiceCriteria>';
					$batch_request = new SoapVar($batch_params, XSD_ANYXML);
					$soap_result = (array) $soap_client->saveBatchInvoice($batch_request);
					/** Log response message */
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "] " . $soap_result['message'], ""); /* Save log info message */
					/** Log total rows */
					Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
					/** Log failed rows */
					Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
					/** Log response error */
					if (isset($soap_result['error']) && $soap_result['error'] > 0) {
						foreach ($soap_result['error'] as $k_e => $v_err_msg) {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceData::MODULE_NAME_POSTED_INVOICE . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
						}
					}
				}
		   }
		}

		return $invoice_result;
    }  
	
    /**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (08/06/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPostedSalesInvoiceLine($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_from = (isset($data['date_from']) ? $data['date_from'] : date("Y-m-d")) . " 00:00:00";
        $date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
        $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : null;
        $soap_client = Globals::soapClientABINOCCentralWS();

        $noc_query = Invoice::where('msd_synced', '=', 1)
            ->where('sales_office_no', '=', $sales_office_no)
			 ->where(function ($query) {
				$query->where('status', '=', 'new')
					->orWhere('status', '=', 'direct download');
			})
            ->whereBetween('invoice_date', [$date_from, $date_to]);
        if($sales_order_code != null) {
            $noc_query->where('sales_order_code', '=', $sales_order_code);
        }
        
        $noc_data = $noc_query->get();
		

        if (count($noc_data) > 0) {
            foreach ($noc_data as $noc_data_model) {
                $soap_data = [];
                $soap_data['params'] = ['Document_No' => $noc_data_model->code,];
                $msd_soap_result = Globals::callSoapApiReadMultiple($url, $soap_data, $sales_office_no);
                if (isset($msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM)) {
                    $msd_data = $msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM;
                    $total_rows = count($msd_data);
                    $msd_data = $total_rows === 1 ? [$msd_data] : $msd_data;
                    /* Save generated response as file backup */
                    $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", InvoiceDetailData::MODULE_POSTED_INVOICE_DETAIL);
                    Globals::saveJsonFile($file_name, $msd_data);
                    Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

                    /* Sync Invoice Detail by batch thru ABI NOC Web Service */
                    if ($total_rows > 0) {
                        $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

                        foreach ($batch_data as $key => $batch) {
                            $data = [];
                            foreach ($batch as $row) {
                                $msd_data_val = new InvoiceDetailData();
                                foreach (get_object_vars($row) as $att_key => $att_value) {
                                    switch ($att_key) {
                                        case "No":
                                            $uom = isset($row->Unit_of_Measure_Code) ? $row->Unit_of_Measure_Code : "";
                                            $msd_data_val->product_code = $att_value . '-' . $uom; // Append UOM required in CMOS
                                    }
                                    $msd_data_val->setMSD($att_key, $att_value);
                                }
                                $msd_data_val->so_code = $noc_data_model->sales_order_code;
                                $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                                $msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
                                $data[] = $msd_data_val;
                            }
                            $batch_params = '<GetInvoiceDetailCriteria xsi:type="urn:GetInvoiceDetailCriteriaArray" soap-enc:arrayType="urn:GetInvoiceDetailCriteria[]">';
                            foreach ($data as $line) {
                                $batch_params .= $line->xmlArrayLineStrings();
                            }
                            $batch_params .= '</GetInvoiceDetailCriteria>';

                            $batch_request = new SoapVar($batch_params, XSD_ANYXML);
                            $soap_result = (array) $soap_client->saveBatchInvoiceDetail($batch_request);
                            /** Log response message */
                            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . "] " . $soap_result['message'], ""); /* Save log info message */
                            /** Log total rows */
                            Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
                            /** Log failed rows */
                            Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
                            /** Log response error */
                            if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                                foreach ($soap_result['error'] as $k_e => $v_err_msg) {
									
									if($is_auto == 0) 
										Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
                                }
                            }
                        }
                    } else {
						if($is_auto == 0) 
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . "] No " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " found.", ""); /* Save log info message */
                    }
                }
            }
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "There's no newly downloaded " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . ".", ""); /* Save log info message */
        }
    }
	
	/**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (20/02/2024)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPostedDirectSalesInvoiceLine($method, $url, $data, $trigger_id = null)
    {
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $date_to = (isset($data['date_to'])) ? $data['date_to'] : date('Y-m-d', strtotime('+1 day')) . " 23:59:59";
        $date_from = (isset($data['date_from'])) ? $data['date_from'] : date('Y-m-d', strtotime('-1 month')) . " 00:00:00";
        $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : null;
        $is_auto = isset($data['is_auto']) ? $data['is_auto'] : 0;
        $soap_client = Globals::soapClientABINOCCentralWS();

		$msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
		if (isset($msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM)) {
			$msd_data = $msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM;
			$total_rows = count($msd_data);
			$msd_data = $total_rows === 1 ? [$msd_data] : $msd_data;
			/* Save generated response as file backup */
			$file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", InvoiceDetailData::MODULE_POSTED_INVOICE_DETAIL);
			Globals::saveJsonFile($file_name, $msd_data);
			Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

			/* Sync Invoice Detail by batch thru ABI NOC Web Service */
			if ($total_rows > 0) {
				$batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch
					
				foreach ($batch_data as $key => $batch) {
					$data = [];
					foreach ($batch as $row) {
						$msd_data_val = new InvoiceDetailData();
						foreach (get_object_vars($row) as $att_key => $att_value) {
							switch ($att_key) {
								case "No":
									$uom = isset($row->Unit_of_Measure_Code) ? $row->Unit_of_Measure_Code : "";
									$msd_data_val->product_code = $att_value . '-' . $uom; // Append UOM required in CMOS
							}
							$msd_data_val->setMSD($att_key, $att_value);
						}
						$invdata = Invoice::where('code', $row->Document_No)->where('msd_synced', 1)->first();
						$msd_data_val->so_code = !empty($invdata) ? $invdata->sales_order_code : "EMPTY";
						$msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
						$msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
						if($msd_data_val->line_no == "" or $msd_data_val->line_no == null) 
						{
							continue;
						}
						$data[] = $msd_data_val;
					}
					$batch_params = '<GetInvoiceDetailCriteria xsi:type="urn:GetInvoiceDetailCriteriaArray" soap-enc:arrayType="urn:GetInvoiceDetailCriteria[]">';
					foreach ($data as $line) {
						$batch_params .= $line->xmlArrayLineStrings();
					}
					$batch_params .= '</GetInvoiceDetailCriteria>';

					$batch_request = new SoapVar($batch_params, XSD_ANYXML);
					$soap_result = (array) $soap_client->saveBatchInvoiceDetail($batch_request);
					/** Log response message */
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . "] " . $soap_result['message'], ""); /* Save log info message */
					/** Log total rows */
					Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
					/** Log failed rows */
					Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
					/** Log response error */
					if (isset($soap_result['error']) && $soap_result['error'] > 0) {
						foreach ($soap_result['error'] as $k_e => $v_err_msg) {
							print_r("\n" . $v_err_msg);
							if($is_auto == 0) {
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
							}
						}
					}
				}
			} else {
				if($is_auto == 0) {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . "] No " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " found.", ""); /* Save log info message */
				}
			}
		}
            
    }
	
    /**
     * Download Pick Note data and send to LoadBoardAPI
     * (10/10/2023)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPostedPickNote($method, $url, $data, $trigger_id = null)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";        
		$sales_office_obj = SalesOffice::where('no', '=', $sales_office_no)->where('deleted', '=', '0')->first();
        $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
		$sales_order_array = $data['Sales_Order_Code'];
        $is_auto = isset($data['is_auto']) ? $data['is_auto'] : 0;

		if(!isset( $msd_soap_result->ReadMultiple_Result->PostedPickNote)) {
			
			if($is_auto == 0) 
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Posted Pick Note]: Pick Note ". $data['params']['No'] . " not found ", ""); /* Save log info .message */
            return;
		}
		$msd_data = $msd_soap_result->ReadMultiple_Result->PostedPickNote;
		$loadboard_url = Globals::createLoadboardURL('save-shipment');
		if(!$sales_office_obj) {
			if($is_auto == 0) 
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Posted Pick Note]: Pick Note ". $sales_office_no . " not found ", ""); /* Save log info .message */
             return;
		}
		if(count($msd_data) == 1) {
			$msd_data = [$msd_data];
		}
		if(count($msd_data) <= 0) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Posted Pick Note] Pick Note ". $data['params']['No'] , "");

		}
		foreach($msd_data as $batch) {
			$returnable_count =[];
			$data_pick = [];
			$data_pick['shipment_no'] = $batch->No;
			$data_pick['sales_office_no'] = $sales_office_no;
			$data_pick['short_desc'] = $sales_office_obj->short_desc;
			$data_pick['delivery_date'] = $batch->Delivery_Date;
			$data_pick['detail'] = [];
			if(isset( $batch->Posted_Pick_Line->WIN_Posted_Pick_Line))
				$posted_data = $batch->Posted_Pick_Line->WIN_Posted_Pick_Line;
			else
				$posted_data = [];
			if(count($posted_data) == 1) {
				$posted_data = [$posted_data];
			}
			$location_sequence = [];
			$so_data = [];
			if(count($posted_data) <= 0) {
				if($is_auto == 0) 
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Posted Pick Note] ".$batch->No . " has no Posted Pick Line", "");
				continue;
			}
			foreach($sales_order_array as $so_code) {
				$line_data = [];
				$so_obj = SalesOrder::where('code', '=', $so_code)->where('deleted', '=', '0')->first();
			
				if(isset($so_obj) && !array_key_exists($so_obj->code, $so_data)) {
					$loc_obj = Location::where('id', '=', $so_obj->location_id)->where('deleted', '=', 0)->where('sales_office_no', '=', $sales_office_no)->first();
					if($loc_obj) {
						$line_data['sales_order_no'] = $so_obj->code;
						$line_data['location_code1'] = (string) $loc_obj->id;
						$line_data['location_code2'] = $loc_obj->code;
						$line_data['location_code3'] = $loc_obj->code2;
						$line_data['location_name'] = $loc_obj->name;
						$line_data['address1'] = $loc_obj->address1;
						$line_data['address2'] = $loc_obj->address2;
						$line_data['longitude'] = $loc_obj->longitude;
						$line_data['latitude'] = $loc_obj->latitude;
						$line_data['psgc'] = is_null($loc_obj->barangay_id) ? "0" : $loc_obj->barangay_id;
						$line_data['total_cases'] = (float)  $so_obj->cases;
						$line_data['fulls_amount'] = (float) ($so_obj->amount - $so_obj->total_returns);
						$line_data['mts_amount'] = (float) $so_obj->total_returns;
						$line_data['total_amount'] = (float) $so_obj->amount;
						if(in_array($loc_obj->id, $location_sequence)) {
							$sequence = (string)  (array_search($loc_obj->id, $location_sequence) +1);
							$line_data['sequence'] = $sequence;
						}
						else {
							$location_sequence[] = $loc_obj->id;
							$sequence = (string)  (array_search($loc_obj->id, $location_sequence) +1);
							$line_data['sequence'] = $sequence;
							
						}
					} 
					$so_data[$so_obj->code] = $line_data;
					
					$picknote = PickNote::where('sales_order_no', '=', $so_obj->code)->where('shipment_no', '=', $batch->No)->first();
					if(!$picknote){
						$picknote = new PickNote();
						$picknote->shipment_no =  $batch->No;
						$picknote->sales_order_no = $so_obj->code;
						$picknote->save();
					}
					
					$so_data_details = $so_obj->salesOrderDetail()->get();
					$so_data_returnables = $so_obj->salesOrderReturnable()->get();
					
					foreach($so_data_details as $so_obj_detail) {
						$sku_obj = Sku::where('code', '=', $so_obj_detail->product_code )
						->where('deleted', '=', 0)->where('sales_office_no', '=', $sales_office_no)->where('msd_synced', '=', 1)->first();
						if($sku_obj) {
							$sku_data = [];
							$sku_data['sku_code'] = $sku_obj->code;
							$sku_data['uom'] = $sku_obj->uom;
							$sku_data['description'] = $sku_obj->name;
							$sku_data['code'] = $sku_obj->sys_21;
							$sku_data['unit_price'] = $sku_obj->unit_price;
							$sku_data['planned_qty'] =  $so_obj_detail->quantity;
							$sku_data['return_qty'] = 0;
							$sku_data['order_type'] = 'O';
							
							$all_sku[] = $sku_data;			
						}
					}
					
					foreach($so_data_returnables as $so_ret_detail) {
						$sku_obj = Sku::where('id', '=', $so_ret_detail->sku_id )
						->where('deleted', '=', 0)->where('msd_synced', '=', 1)->first();
						if($sku_obj) {
							$sku_data = [];
							$sku_data['sku_code'] = $sku_obj->code;
							$sku_data['uom'] = $sku_obj->uom;
							$sku_data['description'] = $sku_obj->name;
							$sku_data['code'] = $sku_obj->sys_21;
							$sku_data['unit_price'] = $sku_obj->unit_price;
							$sku_data['planned_qty'] =  $so_ret_detail->delivery;
							$sku_data['return_qty'] = $so_ret_detail->return;
							$sku_data['order_type'] = 'P';							
							$all_sku[] = $sku_data;			
						}						
					}
					
					
					$so_data[$so_obj->code]['order'] = $all_sku;
				}
				
			}
			if(empty($so_data))
				continue;
			$data_pick['detail'] = array_values($so_data);
			// if(!empty($data_pick)) {
				// $data_json = json_encode($data_pick, JSON_PRETTY_PRINT);
				// $msg = Globals::callLoadboardAPI('POST', $loadboard_url, $data_json);
				// $msg_json = json_decode($msg);
				// if(isset($msg_json->success) && $msg_json->success == false) {
					// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Posted Pick Note] ".$batch->No . " sent, response from loadboard: [". $msg . "]", "");
					// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Posted Pick Note] ".$batch->No . " Sent: [". $data_json . "]", "");
				// }
				// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Posted Pick Note] ".$batch->No . " sent, response from loadboard: [". $msg . "]", ""); /* Save log info .message */
                   
			// }		
		}	
	}
	
    /**
     * Download Pick Note data and send to LoadBoardAPI
     * (10/10/2023)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncLoadBoardPickNoteCollection($method, $url, $data, $trigger_id = null)
    {
		$so_obj = SalesOrder::where('code', '=', $data['sales_order_code'])->first();
		$company = $data['company'];
		$sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		
		print($data['sales_order_code']);
		if(!$so_obj) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Pick Note Collection] ".$data['sales_order_code'] . " sent, has no sales order object.", "");
			return;
		}
		
		$pick_note_data = json_encode(array("sales_order_number" => "['".$so_obj->code."']"));
		
		$salesman = $so_obj->salesman()->first();
		
		if(!$salesman) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Pick Note Collection] ".$data['sales_order_code'] . " Salesman not found.", "");
			return;
		}
		
		$sales_office_no = $salesman->sales_office_no;
		
		$pick_note_download = Globals::callLoadboardAPI($method, $url, $pick_note_data);
		$data_returned = json_decode($pick_note_download, true);

		print_r("Start sending...\n");
	
		print_r("DATA: " . json_encode($data_returned));
			
		if($data_returned['success'] && isset($data_returned['data'])) {
			$data_array = $data_returned['data'];
			$collection_list = [];
			print_r("DATA: " . json_encode($data_array));
			foreach($data_array as $data_so)
			{
				if(empty($data_so) || !is_array($data_so)) 
					continue;
				foreach($data_so as $data_collection)
				{
					$so_obj = SalesOrder::where('code', '=', $data_collection['sales_order_number'])->first();
					if(!$so_obj) {
						continue;
					}
					$new_data = new TempCollectionCash();
					$new_data->sales_order_id = $so_obj->id;
					$new_data->document_number = $data_collection['document_no'];
					$new_data->type = $data_collection['type'];
					$new_data->type_of_payment = $data_collection['type_of_payment'];
					$new_data->remarks = $data_collection['remarks'];
					$new_data->mode = $data_collection['mode'];
					$new_data->product_code = $data_collection['sku_code'];
					$new_data->cash_quantity_1000 = $data_collection['cash_qty_1000'];
					$new_data->cash_quantity_500 = $data_collection['cash_qty_500'];
					$new_data->cash_quantity_200 = $data_collection['cash_qty_200'];
					$new_data->cash_quantity_100 = $data_collection['cash_qty_100'];
					$new_data->cash_quantity_50 = $data_collection['cash_qty_50'];
					$new_data->cash_quantity_20 = $data_collection['cash_qty_20'];
					$new_data->cash_quantity_10 = $data_collection['cash_qty_10'];
					$new_data->cash_quantity_5 = $data_collection['cash_qty_5'];
					$new_data->cash_quantity_1 = $data_collection['cash_qty_1'];
					$new_data->cash_quantity_50c = $data_collection['cash_qty_50c'];
					$new_data->cash_quantity_25c = $data_collection['cash_qty_25c'];
					$new_data->cash_quantity_10c = $data_collection['cash_qty_10c'];
					$new_data->cash_quantity_5c = $data_collection['cash_qty_5c'];
					$new_data->cash_loose_amount = $data_collection['cash_loose_amount'];
					$new_data->cash_amount = $data_collection['cash_amount'];
					$new_data->check_bank = $data_collection['check_bank'];
					$new_data->check_account_no = $data_collection['check_account_no'];
					$new_data->check_no = $data_collection['check_no'];
					$new_data->check_type = $data_collection['check_type'];
					$new_data->check_amount = $data_collection['check_amount'];
					$new_data->check_date = $data_collection['check_date'];
					if($new_data->save()) {
						$new_data_breakdown = new TempCollectionCashBreakdown();
						$new_data_breakdown->temp_collection_id = $new_data->id;
						$new_data_breakdown->sales_order_id = $so_obj->id;
						$new_data_breakdown->contents_amount = $data_collection['fulls_amt'];
						$new_data_breakdown->containers_amount = $data_collection['fulls_mts'];
						$new_data_breakdown->total_amount = $data_collection['fulls_amt'] + $data_collection['fulls_mts'];
						$new_data_breakdown->invoice_no = $data_collection['invoice_code'];
						$new_data_breakdown->mode = $data_collection['mode'];
						$new_data_breakdown->save();
						$collection_list[] = $new_data_breakdown;
					}
				}
			}
			if(!empty($collection_list)) {
				print_r("Sending picknote...\n");
				UploadConnector::syncMSDCashReceiptFromPicknote($collection_list, $so_obj->id,  $data_collection['employee_id'], $sales_office_no, $trigger_id, $company);
			}
			else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Pick Note Collection] Breadown saving failed.", "");

			}
		}
		else {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Pick Note Collection] Pick note not found.", "");

		}
			
		
		return;
	}
	
	
    /**
     * Download Pick Note data and send to LoadBoardAPI
     * (10/10/2023)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncLoadBoardPickNoteInventory($method, $url, $data, $trigger_id = null)
    {
		$so_obj = SalesOrder::where('code', '=', $data['sales_order_code'])->first();
		$company = $data['company'];
		$sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		if(!$so_obj) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Pick Note Inventory] ".$data['sales_order_code'] . " sent, has no sales order object.", "");
			return;
		}
		
		$pick_note_data = json_encode(array("sales_order_number" => "['".$so_obj->code."']"));
		$salesman = $so_obj->salesman()->first();
		
		$pick_note_download = Globals::callLoadboardAPI($method, $url, $pick_note_data);
		$data_returned = json_decode($pick_note_download, true);
		$all_data = [];
		
		if(!isset($data_returned['success']) || !$data_returned['success']){
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Pick Note Inventory] ".$data['sales_order_code'] . " picknote not found.", "");
			return;
		}
		foreach($data_returned['data'][0] as $datas) {
			if($datas) {
				if(isset($datas['code']) and (isset($datas['uom']))) {
					$all_data[$datas['code']] = array(
						'return' => $datas['actual_qty'],
						'product' =>$datas['code'],
						'unit_price' =>$datas['unit_price'],
						'uom' => $datas['uom'],
						'employee' => isset($datas['employee_id']) ? $datas['employee_id'] : "" ,
						'location_code' => isset($datas['location_code']) ? $datas['location_code'] : "" ,
					);
				}
			}
		}
		
		if(!$salesman) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Pick Note Inventory] ".$data['sales_order_code'] . " Salesman not found.", "");
			return;
		}
		
		$sales_office_no = $salesman->sales_office_no;
		if(!$so_obj) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Pick Note Inventory] ".$data['sales_order_code'] . " sent, has no sales order object.", "");
			return;
		}
		
		UploadConnector::syncMSDCreditMemoFromPicknote($so_obj->id, $all_data, $sales_office_no, $trigger_id, $company);
		return;
	}
	

    /**
     * Turns data obtained from client MSD API to XML data and sends it to NOC.
     * (06/10/2022)
     * 
     * @param method HTTP request type for REST API.
     * @param url URL for REST API.
     * @param data HTTP data from NOC request
     * @param trigger_id ID for request trigger log
     * 
     * @return void
     */
    public static function syncMSDPostedSalesInvoiceLineBooking($method, $url, $data, $trigger_id = null)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $sales_order_code_arr = isset($data['params']['Order_No']) ? $data['params']['Order_No'] : null;
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        
		if(!is_array($sales_order_code_arr)) {
			$sales_order_code_arr = [$sales_order_code_arr];
		}
		foreach($sales_order_code_arr as $sales_order) {
			if($sales_order == null || $sales_order == "")
				continue;
			
			$noc_query = Invoice::where('msd_synced', '=', 1)
				->where('sales_office_no', '=', $sales_office_no)
				->where('status', '=', 'new');
			if($sales_order != null) {
				$noc_query->where('sales_order_code', '=', $sales_order);
			}
			$noc_data = $noc_query->get();

			if (count($noc_data) > 0) {
				foreach ($noc_data as $noc_data_model) {
					$soap_data = [];
					$soap_data['params'] = ['Document_No' => $noc_data_model->code,];
					$msd_soap_result = Globals::callSoapApiReadMultiple($url, $soap_data, $sales_office_no);
					if (isset($msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM)) {
						$msd_data = $msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM;
						$total_rows = count($msd_data);
						$msd_data = $total_rows === 1 ? [$msd_data] : $msd_data;
						/* Save generated response as file backup */
						$file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", InvoiceDetailData::MODULE_POSTED_INVOICE_DETAIL);
						Globals::saveJsonFile($file_name, $msd_data);
						Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

						/* Sync Invoice Detail by batch thru ABI NOC Web Service */
						if ($total_rows > 0) {
							$batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

							foreach ($batch_data as $key => $batch) {
								$data = [];
								foreach ($batch as $row) {
									$msd_data_val = new InvoiceDetailData();
									foreach (get_object_vars($row) as $att_key => $att_value) {
										switch ($att_key) {
											case "No":
												$uom = isset($row->Unit_of_Measure_Code) ? $row->Unit_of_Measure_Code : "";
												$msd_data_val->product_code = $att_value . '-' . $uom; // Append UOM required in CMOS
										}
										$msd_data_val->setMSD($att_key, $att_value);
									}
									$msd_data_val->so_code = $noc_data_model->sales_order_code;
									$msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
									$msd_data_val->edited_by = DownloadConnector::MSD_LOGGER_NAME;
									$data[] = $msd_data_val;
								}
								$batch_params = '<GetInvoiceDetailCriteria xsi:type="urn:GetInvoiceDetailCriteriaArray" soap-enc:arrayType="urn:GetInvoiceDetailCriteria[]">';
								foreach ($data as $line) {
									$batch_params .= $line->xmlArrayLineStrings();
								}
								$batch_params .= '</GetInvoiceDetailCriteria>';

								$batch_request = new SoapVar($batch_params, XSD_ANYXML);
								$soap_result = (array) $soap_client->saveBatchInvoiceDetail($batch_request);
								/** Log response message */
								Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . "] " . $soap_result['message'], ""); /* Save log info message */
								/** Log total rows */
								Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
								/** Log failed rows */
								Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
								/** Log response error */
								if (isset($soap_result['error']) && $soap_result['error'] > 0) {
									foreach ($soap_result['error'] as $k_e => $v_err_msg) {
										Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . "]" . Utils::logMsg($v_err_msg), ""); /* Save log error message */
									}
								}
							}
						} else {
							Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . "] No " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " found.", ""); /* Save log info message */
						}
					}else {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . "] No " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " found.", ""); /* Save log info message */
					}
				}
			}
		}
    }

    
    /**
    * Turns data obtained from client <SD API to XML data and sends it to Webservice.
    * (08/06/2022)
    * 
    * @param method HTTP request type for REST API.
    * @param url URL for REST API.
    * @param data HTTP data from NOC request
    * @param trigger_id ID for request trigger log
    * 
    * @return void
    */
   public static function syncMSDCustomerBalance($method, $url, $data, $trigger_id = null, $company = null)
   {
		$sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		$date_from = (isset($data['date_from']) ? $data['date_from'] : date('Y-m-d', strtotime('-7 days'))) . " 00:00:00";
		$date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";
		$soap_client = Globals::soapClientABINOCCentralWS();
		$msd_data = array();
		$sales_office_obj = SalesOffice::where('no', '=', $sales_office_no)->first();
		if($sales_office_obj) {
			$short_desc = $sales_office_obj->short_desc;
			$data['params']['Global_Dimension_1_Code'] = $short_desc;
		}
	   
       $msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
	   if($sales_office_no != "")
		   DB::table('BALANCE AS bl')
			->join('location AS l', function ($join) use($sales_office_no) {
				$join->on('l.code', '=', 'bl.customer_no')
					->where('l.sales_office_no', '=',  $sales_office_no);
			})
			->update([
				'bl.deleted' => 1,
		]);
		file_put_contents(storage_path("qamote.log"), "DUMAAN DITO".PHP_EOL, FILE_APPEND);
       if (isset($msd_soap_result->ReadMultiple_Result->CustomerLedgerEntriesService)) {
           if (count($msd_soap_result->ReadMultiple_Result->CustomerLedgerEntriesService) > 1) {
               foreach ($msd_soap_result->ReadMultiple_Result->CustomerLedgerEntriesService as $value) {
                   $msd_data_val = new BalanceData();
                   foreach (get_object_vars($value) as $att_key => $att_value) {
                       $msd_data_val->setMSD($att_key, $att_value);
                   }
                   $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
                   $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
                   $msd_data_val->msd_synced = 1;
                   $msd_data_val->deleted = 0;
                   if(strpos($msd_data_val->document_no, "CTS") !== false) {
                    $msd_data_val->amount_mts = $value->Amount;
                    $msd_data_val->balance_mts = $value->Remaining_Amount;
                    $msd_data_val->amount_fulls = 0;
                    $msd_data_val->balance_fulls = 0;
                   }
                   else {
                    $msd_data_val->amount_mts =0;
                    $msd_data_val->balance_mts = 0;
                    $msd_data_val->amount_fulls = $value->Amount;
                    $msd_data_val->balance_fulls = $value->Remaining_Amount;
                   }
				   
			   // $timestamp = strtotime($value->Posting_Date);
			   // if(strtotime($date_from) < $timestamp && strtotime($date_to) > $timestamp)
					$msd_data[] = $msd_data_val;  
               }
           } else {
               $msd_data_val = new BalanceData();
               foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->CustomerLedgerEntriesService) as $att_key => $att_value) {
                   $msd_data_val->setMSD($att_key, $att_value);
               }
               $msd_data_val->added_by = DownloadConnector::MSD_LOGGER_NAME;
               $msd_data_val->updated_by = DownloadConnector::MSD_LOGGER_NAME;
               $msd_data_val->msd_synced = 1;
                   $msd_data_val->deleted = 0;
               if(strpos($msd_data_val->document_no, "CTS") !== false ) {
                $msd_data_val->amount_mts = $value->Amount;
                $msd_data_val->balance_mts = $value->Remaining_Amount;
                $msd_data_val->amount_fulls = 0;
                $msd_data_val->balance_fulls = 0;
               }
               else {
                $msd_data_val->amount_mts =0;
                $msd_data_val->balance_mts = 0;
                $msd_data_val->amount_fulls = $value->Amount;
                $msd_data_val->balance_fulls = $value->Remaining_Amount;
               }
               $msd_data[] = $msd_data_val;
           }
           /* Save generated response as file backup */
           $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", BalanceData::MODULE_NAME_BALANCE);
           Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->CustomerLedgerEntriesService);
       }
       $total_rows = count($msd_data);
       

       Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */

       Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */

       if ($total_rows > 0) {
           $batch_data = array_chunk($msd_data, DownloadConnector::BATCH_LIMIT); // Create batch

			
           foreach ($batch_data as $key => $batch) {
               $batch_params = '<GetBalanceCriteria xsi:type="urn:GetBalanceCriteriaArray" soap-enc:arrayType="urn:GetBalanceCriteria[]">';
               foreach ($batch as $line) {
                   $batch_params .= $line->xmlArrayLineStrings();
               }
               $batch_params .= '</GetBalanceCriteria>';
               $batch_request = new SoapVar($batch_params, XSD_ANYXML);
               $soap_result = (array) $soap_client->saveBatchBalance($batch_request);
               /** Log response message */
               Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . BalanceData::MODULE_NAME_BALANCE . "] " . $soap_result['message'], ""); /* Save log info message */
               /** Log total rows */
               if ($key === 0) {
                   Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows']); /* Update trigger total rows */
               } else {
                   Utils::updateTriggerTotalRows($trigger_id, $soap_result['total_rows'], true); /* Update trigger total rows = existing + response total_rows */
               }
               /** Log failed rows */
               Utils::updateTriggerFailedRows($trigger_id, $soap_result['failed_rows'], true); /* Update trigger failed rows = existing + response failed_rows */
               /** Log response error */
               if (isset($soap_result['error']) && $soap_result['error'] > 0) {
                   foreach ($soap_result['error'] as $k_e => $v_err_msg) {
                       Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, $v_err_msg, ""); /* Save log error message */
                   }
               }
           }
       } else {
           Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "No " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance found.", ""); /* Save log info message */
       }
   }

   public static function syncMSDCustomerBalanceEmpties($method, $url, $data, $trigger_id = null)
   {
		gc_enable();
		$sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";        
		$date_from = (isset($data['date_from']) ? $data['date_from'] : date('Y-m-d', strtotime('-7 days'))) . " 00:00:00";
		$date_to = (isset($data['date_to']) ? $data['date_to'] : date("Y-m-d")) . " 23:59:59";    
		$sales_office_obj = SalesOffice::where('no', '=', $sales_office_no)->first();
		if($sales_office_obj) {
			$short_desc = $sales_office_obj->short_desc;
			$data['params']['Shortcut_Dimension_1_Code'] = $short_desc;	
		}
	   
	   if($sales_office_no != "")
		   DB::table('balance_empties AS ble')
			->join('location AS l', function ($join) use($sales_office_no) {
				$join->on('l.code', '=', 'ble.customer_no')
					->where('l.sales_office_no', '=',  $sales_office_no);
			})
			->update([
				'ble.deleted' => 1,
			]);

		
		$soap_client = Globals::soapClientABINOCCentralWS();
		$msd_soap_result = Globals::callSoapApiReadMultiple($url, $data, $sales_office_no);
		$msd_data = array();
		if (isset($msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM)) {
			if (count($msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM) > 1) {
				foreach ($msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM as $value) {
					$msd_data_val = new BalanceEmptiesData();
					foreach (get_object_vars($value) as $att_key => $att_value) {
						$msd_data_val->setMSD($att_key, $att_value);
					}
					if($short_desc)
						$msd_data_val->short_desc = $short_desc;
					
					$msd_data[] = $msd_data_val;
				}
			} else {
				$msd_data_val = new BalanceEmptiesData();
				foreach (get_object_vars($msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM) as $att_key => $att_value) {
					$msd_data_val->setMSD($att_key, $att_value);
				}
				if($short_desc)
					$msd_data_val->short_desc = $short_desc;
				
				$msd_data[] = $msd_data_val;
			}
			/* Save generated response as file backup */
			$file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", BalanceEmptiesData::MODULE_NAME_BALANCE_EMPTIES);
			Globals::saveJsonFile($file_name, $msd_soap_result->ReadMultiple_Result->PostedSalesInvoiceLinesSCM);
		}
		
		$total_rows = count($msd_data);
		Utils::updateTriggerTotalRows($trigger_id, $total_rows); /* Update trigger total rows */
		Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_ONGOING); /* Update trigger status */
		if ($total_rows > 0) {
			foreach ($msd_data as $key => $balance_empties) {
				$sys_21 = "";

				$sku_model = DB::table('sku')
					->where('sys_21', '=', $balance_empties->sku_code)
					->where('sales_office_no', '=', $sales_office_no)
					->where('msd_synced', 1)
					->first();					
				
				if ($sku_model) {
					$sys_21 = $sku_model->sys_21; 
				} else {
					Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . BalanceEmptiesData::MODULE_NAME_BALANCE_EMPTIES . "] Unable to find sku" . $balance_empties->sku_code . "-" . $sys_21, ""); /* Save log info message */
					continue;
				}
				
				$balance = BalanceEmpties::where('product_code', $sku_model->sys_21)
				->where('document_no', $balance_empties->invoice_code)
				->where('customer_no', $balance_empties->location_code)
				->where('line_no', $balance_empties->line_no)
				->first();
				
				if ($balance) {
					$balance->quantity = $balance_empties->quantity;
					$balance->balance_empties = $balance_empties->balance_empties;
					$balance->ms_dynamics_key = $balance_empties->ms_dynamics_key;
					$balance->edited_by = DownloadConnector::MSD_LOGGER_NAME;
					$balance->deleted = 0;
					if($balance->save()) {
						Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . BalanceEmptiesData::MODULE_NAME_BALANCE_EMPTIES . "] Saved balance empty ". $balance_empties->balance_empties . " of " . $balance_empties->invoice_code . " " . $sku_model->code, ""); /* Save log info message */
						/** Log total rows */
						if ($key === 0) {
							Utils::updateTriggerTotalRows($trigger_id, 1); /* Update trigger total rows */
						} 
					} else {
						/** Log failed rows */
						Utils::updateTriggerFailedRows($trigger_id, 1, true); /* Update trigger failed rows = existing + response failed_rows */
						Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[" . BalanceEmptiesData::MODULE_NAME_BALANCE_EMPTIES . "] Failed to save the balance empty of " . $balance_empties->invoice_code . " " . $sys_21, ""); /* Save log error message */
					}               
				
				}   
				else {
					$balance_obj = new BalanceEmpties;
					$balance_obj->product_code = $balance_empties->sku_code;
					$balance_obj->customer_no = $balance_empties->location_code;
					$balance_obj->document_no = $balance_empties->invoice_code;
					$balance_obj->line_no = $balance_empties->line_no;
					$balance_obj->quantity = $balance_empties->quantity;
					$balance_obj->balance_empties = $balance_empties->balance_empties;
					$balance_obj->ms_dynamics_key = $balance_empties->ms_dynamics_key;
					$balance_obj->added_by = DownloadConnector::MSD_LOGGER_NAME;
					if($balance_obj->save())
						Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . BalanceEmptiesData::MODULE_NAME_BALANCE_EMPTIES . "] Saved balance empties of " . $balance_empties->invoice_code . " " . $balance_empties->sku_code, ""); /* Save log info message */
					else	
						Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . BalanceEmptiesData::MODULE_NAME_BALANCE_EMPTIES . "] Unable to save " . $balance_empties->invoice_code . " " . $balance_empties->sku_code, ""); /* Save log info message */
					continue;
				}

			}
		}
		gc_collect_cycles();
   }


   public static function syncMSDSaveApprovedRefund($data, $sales_office_no, $trigger_id = null){
		$location_code = isset($data['location_code']) ? $data['location_code'] : ""; 
		$salesman_code = isset($data['salesman_code']) ? $data['salesman_code'] : ""; 
		$sales_order_code = isset($data['sales_order_no']) ? $data['sales_order_no'] : "";  
		$approval_code = isset($data['approval_code']) ? $data['approval_code'] : "";  
		$approved_by = isset($data['approved_by']) ? $data['approved_by'] : "";  
		$approval_date = isset($data['approval_date']) ? $data['approval_date'] : "";
		$invoices = isset($data['invoice']) ? $data['invoice'] : [];
		$cts = isset($data['cts']) ? $data['cts'] : [];

		$invoice_all = Invoice::where('sales_order_code', $sales_order_code)
		->where('sales_office_no', $sales_office_no)
		->select('invoice.*') 
		->get();
		if($invoice_all) {
			foreach($invoice_all as $invoice) {	
				$invoice->approved_by = $approved_by;
				$invoice->approved_date = $approval_date;
				$invoice->approval_code = $approval_code;
				if($invoice->save()){
					if($invoice->ct_slip == 0) {
						foreach ($invoices as $inv_detail_data) {	
							$invoice_code =  isset($data['external_document_no']) ? $data['external_document_no'] : $invoice->code;
							$sku = Sku::query()
							->where('deleted', 0)
							->where('sales_office_no', $sales_office_no)
							->where('sys_21', $inv_detail_data['product_code'])
							->where('msd_synced', 1)
							->first();

							$inv_detail_obj = InvoiceRefundDetail::query()
							->where('inv_code', $invoice_code)
							->where('so_code', $sales_order_code)
							->where('product', $sku->code)
							->where('line_no', $inv_detail_data['line_no'])
							->first();

							if($inv_detail_obj) {
								$inv_detail_obj->quantity = $inv_detail_data['quantity'];
								$inv_detail_obj->amount = $inv_detail_data['amount'];
								if($inv_detail_obj->save()){
									Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Save Approved Refund] Updated Refund Data of " . $invoice_code . ": " . $inv_detail_data['product_code'] . " " . $inv_detail_data['line_no'], ""); /* Save log info message */
								}
								else{
									Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Save Approved Refund] Error updating invoice " . $invoice_code . ": " . $inv_detail_data['product_code'] . " " . $inv_detail_data['line_no'] , ""); /* Save log info message */
				
								}
							} else {
								$new_inv_detail = new InvoiceRefundDetail;
								$new_inv_detail->inv_code = $invoice_code;
								$new_inv_detail->so_code = $sales_order_code;
								$new_inv_detail->product = $sku->code;
								$new_inv_detail->line_no = $inv_detail_data['line_no'];
								$new_inv_detail->quantity = $inv_detail_data['quantity'];
								$new_inv_detail->amount = $inv_detail_data['amount'];
								$new_inv_detail->added_by = DownloadConnector::MSD_LOGGER_NAME;
								if($new_inv_detail->save()){
									Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Save Approved Refund] Saved Refund Data of " . $invoice_code . ": " . $inv_detail_data['product_code'] . " " . $inv_detail_data['line_no'], ""); /* Save log info message */
								}
								else{
									Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Save Approved Refund] Error saving invoice " . $invoice_code . ": " . $inv_detail_data['product_code'] . " " . $inv_detail_data['line_no'] , ""); /* Save log info message */
								}

							}
						}
					}
					else {
						foreach ($cts as $inv_detail_data) {
							$invoice_code =  isset($inv_detail_data['ctslip_no']) ? $inv_detail_data['ctslip_no'] : $invoice->code;
							$sku = Sku::query()
							->where('deleted', 0)
							->where('sales_office_no', $sales_office_no)
							->where('sys_21', $inv_detail_data['product_code'])
							->where('msd_synced', 1)
							->first();

							$inv_detail_obj = InvoiceRefundDetail::query()
							->where('inv_code', $invoice_code)
							->where('so_code', $sales_order_code)
							->where('product', $sku->code)
							->where('line_no', $inv_detail_data['line_no'])
							->first();

							if($inv_detail_obj) {
								$inv_detail_obj->quantity = $inv_detail_data['quantity'];
								$inv_detail_obj->amount = $inv_detail_data['amount'];
								$inv_detail_obj->updated_by = DownloadConnector::MSD_LOGGER_NAME;
								$inv_detail_obj->updated_when = date('Y-m-d H:i:s');
								if($inv_detail_obj->save()){
									Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Save Approved Refund] Updated Refund Data of " . $invoice_code . ": " . $inv_detail_data['product_code'] . " " . $inv_detail_data['line_no'], ""); /* Save log info message */
								}
								else{
									Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Save Approved Refund] Error updating invoice " . $invoice_code . ": " . $inv_detail_data['product_code'] . " " . $inv_detail_data['line_no'] , ""); /* Save log info message */
				
								}
							} else {
								$new_inv_detail = new InvoiceRefundDetail;
								$new_inv_detail->inv_code = $invoice_code;
								$new_inv_detail->so_code = $sales_order_code;
								$new_inv_detail->product = $sku->code;
								$new_inv_detail->line_no = $inv_detail_data['line_no'];
								$new_inv_detail->quantity = $inv_detail_data['quantity'];
								$new_inv_detail->amount = $inv_detail_data['amount'];
								$new_inv_detail->added_by = DownloadConnector::MSD_LOGGER_NAME;
								if($new_inv_detail->save()){
									Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Save Approved Refund] Saved Refund Data of " . $invoice_code . ": " . $inv_detail_data['product_code'] . " " . $inv_detail_data['line_no'], ""); /* Save log info message */
								}
								else{
									Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Save Approved Refund] Error saving invoice " . $invoice_code . ": " . $inv_detail_data['product_code'] . " " . $inv_detail_data['line_no'] , ""); /* Save log info message */
								}

							}
						}
					}
				}
				else {
						Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Save Approved Refund] Error updating invoice " . $invoice->code . ": " . $inv_detail_data['product_code'] . " " . $inv_detail_data['line_no'] , ""); /* Save log info message */
				}
			}
           $file_name = DownloadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . "SAVE-APPROVED-REFUND";
           Globals::saveJsonFile($file_name, $data);
		}
		else {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::ERROR, DownloadConnector::MSD_LOGGER_NAME, "[Save Approved Refund] Error updating invoice of " . $sales_order_code . ", no such invoice exists.", ""); /* Save log info message */
		}
	}	

	
}
