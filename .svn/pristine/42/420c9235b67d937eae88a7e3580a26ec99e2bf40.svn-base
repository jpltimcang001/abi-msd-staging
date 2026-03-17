<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
/* Middleware */
use App\Http\Middleware\DownloadConnector;
use App\Http\Middleware\UploadConnector;
/* Utils */
use App\Utils\Globals;
use App\Utils\Params;
use App\Utils\Utils;
/* Schema */
use App\Data\InvoiceData;
use App\Data\InvoiceDetailData;

class APICashReceiptCreditMemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apirun:receiptcredit {sales_order_code} {company} {sales_office_no} {trigger_id}';

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
        //parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$so_code = $this->argument('code');
		$sales_office_no = $this->argument('sales_office_no');
		$trigger_id = $this->argument('trigger_id');
        $company = $this->argument('company');
        $date_from = date("Y-m-d") . " 00:00:00";
        $date_to = date("Y-m-d") . " 23:59:59";
		
        $build = SalesOrder::where('code', '=', $so_code)
        ->with(['salesman'])
        ->whereHas('salesman', function ($q) use ($sales_office_no) {
            $q->where('sales_office_no', '=', $sales_office_no);
        });
        $so_model = $build->first();

		$invoice_data =  Invoice::where('msd_synced', '=', 1)
                ->where('sales_office_no', '=', $sales_office_no)
                ->where('sales_order_code', '=', $so_model->code)
                ->whereBetween('invoice_date', [$date_from, $date_to])
                ->get();
            
        
		foreach ($invoice_data as $key => $invoice) {
			
		}
       
    } 
	
	 public static function syncMSDCashReceiptJournal($invoice_obj, $sales_office_no, $company, $trigger_id = null)
    {
        $date_from = date("Y-m-d") . " 00:00:00";
        $date_to = date("Y-m-d") . " 23:59:59";
        $sales_order_code =  $invoice_obj->sales_order_code;
        $invoice_code = $invoice_obj->code;
		$route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-journal']['list'];
		$url = Globals::soapABIMSDynamicsURL($route, $company);

        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", CashReceiptData::MODULE_CASH_RECEIPT) .  "-" . $invoice_code;

		$data = new CashReceiptData();
		$sales_order = $invoice_obj->salesOrder()->first();
		$temp_collection = $sales_order->tempCollectionCash()->first();
		$send_invoice_code = $invoice_obj->code;
		if ($temp_collection) {
			$temp_collection_breakdowns = $temp_collection->tempCollectionCashBreakdown()->get();
		}
		if (!isset($temp_collection) || count($temp_collection_breakdowns) == 0) {
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] No found collection/breakdown found for Invoice #" . $invoice_obj->code, ""); /* Save log info message */
			$invoice_obj->status = "cash_receipt";
			if ($invoice_obj->save())
				return false;
		}
		
		$salesman = $sales_order->salesman()->first();
		$location = $sales_order->location()->first();
		foreach ($temp_collection_breakdowns as $key => $temp_collection_breakdown) {
			if ($sales_order) {
				$total_amount = 0;
				$ct_slip = substr($invoice_obj->code, 3, 3) == "CTS";

				if ($ct_slip === true) {
					$total_amount = $temp_collection_breakdown->containers_amount;
					if ($total_amount == 0) {
						if (($invoice_obj->ct_slip == 1 && $sales_order->type == 0) || $sales_order->type == 1) {
							$invoice_obj->status = "cash_receipt";
						} else {
							$invoice_obj->status = "posted";
						}
						if ($invoice_obj->save())
							$success = true;
						continue;
					}
				} else {
					$total_amount = $temp_collection_breakdown->contents_amount;
				}
			}

			if ($location)
				$data->account_no = $location->code;
			if ($salesman && $temp_collection_breakdown->mode == 'Cash') {
				$data->batch_name = $salesman->cash_batch;
				if ($temp_collection_breakdown->containers_amount === 0) {
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] Mode Cash breakdown is with containers amount 0 in " . $invoice_obj->code, ""); /* Save log info message */
					if ($sales_order->transaction_type = 0) {
						if ($total_amount > 0) {
							$send_invoice_code = $temp_collection_breakdown->invoice_no;
						} else {
							$invoice_obj->status = "posted";
							$invoice_obj->save();
							return false;
						}
					} else {
						$invoice_obj->status = "cash_receipt";
						$invoice_obj->save();
						return false;
					}
				}
			}
			if ($total_amount == 0) {
				return false;
			}
			if ($salesman && $temp_collection_breakdown->mode == 'Check')
				$data->batch_name = $salesman->cheque_batch;

			$data->document_date = date("Y-m-d", strtotime($invoice_obj->invoice_date));
			$data->posting_date = date("Y-m-d", strtotime($invoice_obj->invoice_date));
			$data->document_type = 'Payment';
			$data->account_type = 'Customer';
			$data->amount = -$total_amount;
			$data->applies_to_doc_type = 'Invoice';
			$data->applies_to_doc_no = $send_invoice_code;
			$invoice_obj_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
			$invoice_obj_request = new SoapVar($invoice_obj_params, XSD_ANYXML);
			$soap_result = Globals::callSoapApiCreate($url, $invoice_obj_request, $sales_office_no);
			if (is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $invoice_obj->code . " " . $soap_result, "");
			} elseif ($soap_result && property_exists($soap_result, "CashReceiptJournals")) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $invoice_obj->code . " successfully uploaded.", ""); /* Save log info message */

			} else {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . CashReceiptData::MODULE_NAME_CASH_RECEIPT . "] " . $invoice_obj->code . " " . $soap_result, "");
			}
			Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);

			$sales_order_returnables = $sales_order->salesOrderReturnable()->first();
			if (empty($sales_order_returnables)) {
				$invoice_obj->status = "credit_memo_posted";

			} else {
				if (($invoice_obj->ct_slip == 1 && $sales_order->type == 0) || $sales_order->type == 1) {
					$invoice_obj->status = "cash_receipt";
				} else {
					$invoice_obj->status = "credit_memo_posted";
				}
			}
			if ($invoice_obj->save())
				$success = true;
		}
    }
	
	public static function syncMSDSalesCreditMemo($invoice_obj, $sales_office_no, $company, $trigger_id = null)
    {
        $date_from = date("Y-m-d") . " 00:00:00";
        $date_to = date("Y-m-d") . " 23:59:59";
        $sales_order_code =  $invoice_obj->sales_order_code;
        $invoice_code = $invoice_obj->code;
		$route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo']['list'];
		$url = Globals::soapABIMSDynamicsURL($route, $company);

        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO)  .  "-" . $invoice_code;
	
		$success = false;
		
		$ct_slip = substr($invoice_obj->code, 3, 3) == "CTS";
		
		if(!$ct_slip) {
			return false;
		}
		
		/* Assign each model to data for XML */
		$data = new SalesCreditMemoData;
		$sales_order = $invoice_obj->salesOrder()->first();
		$sales_order_returns = [];
		if ($sales_order) {
			$salesman = $sales_order->salesman()->first();
			$location = $sales_order->location()->first();
			/* Get sales order returnable quantity */
			$sales_order_returnables = $sales_order->salesOrderReturnable()->get();
			if (count($sales_order_returnables) <= 0) {
				$invoice_obj->status = "credit_memo_posted";
				$invoice_obj->updated_by = UploadConnector::MSD_LOGGER_NAME;
				$invoice_obj->updated_date = date("Y-m-d H:i:s");
				if ($invoice_obj->save())
					$success = true;
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] No Sales Order Returnable found for sales order " . $sales_order->code, ""); /* Save log info message */
				return false;
			}

		}
		if ($location)
			$data->location_code = $location->code;
		if ($salesman)
			$data->salesman_code = $salesman->code;
		$data->ct_slip = 'true';
		$data->empties_type = 'Deposit';
		$data->reason_code = 'EMPTIES';
		$data->document_date = date("Y-m-d", strtotime($invoice_obj->invoice_date));
		$data->due_date = date("Y-m-d", strtotime($invoice_obj->due_date));
		$data->applies_to_doc_type = 'Invoice';
		$data->applies_to_doc_no = $invoice_obj->code;
		/* Detail */
		//$invoice_obj_details = InvoiceDetails::where('inv_code', '=',  $invoice_obj->code)->get();
		if ($sales_order_returnables && count($sales_order_returnables) > 0) {
			foreach ($sales_order_returnables as $returnable) {
				//if($invoice_obj_detail->empties_type == "Loan") {
				//	continue;
				//}
				$data_detail = new SalesCreditMemoLineData;
				$sku_model = $returnable->sku()->first();
				if ($sku_model)
					$data_detail->no = $sku_model->sys_21;
				if ($returnable->return <= 0) {
					continue;
				}
				$data_detail->type = 'Item';
				$data_detail->quantity = $returnable->return;
				$data_detail->unit_price = $returnable->unit_price;
				$data_detail->ct_slip_no = $invoice_obj->code;
				$data_detail->empties_type = 'Deposit';
				//$data_detail->empties_line_no = $invoice_obj_detail->line_no;
				if ($salesman)
					$data_detail->zone_code = $salesman->zone;
				$data->salesCreditMemoLineData[] = $data_detail;
			}
			if (count($data->salesCreditMemoLineData) === 0) {
				$invoice_obj->status = "credit_memo_posted";
				$invoice_obj->updated_by = UploadConnector::MSD_LOGGER_NAME;
				$invoice_obj->updated_date = date("Y-m-d H:i:s");
				$invoice_obj->save();
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] No valid returnable data found for " . $sales_order->code, ""); /* Save log info message */
				continue;
			}

			$invoice_obj_params = "<ns1:Create>" . $data->xmlArrayLineStrings() . "</ns1:Create>";
			$invoice_obj_request = new SoapVar($invoice_obj_params, XSD_ANYXML);
			$soap_result = Globals::callSoapApiCreate($url, $invoice_obj_request, $sales_office_no);
			if (is_string($soap_result)) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $invoice_obj->code . " " . $soap_result, "");
				$invoice_obj->status = "credit_memo_posted";
				$invoice_obj->updated_by = UploadConnector::MSD_LOGGER_NAME;
				$invoice_obj->updated_date = date("Y-m-d H:i:s");
				$invoice_obj->save();
			} elseif ($soap_result && property_exists($soap_result, "SalesCreditMemoCard")) {
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . "] " . $invoice_obj->code . " successfully uploaded.", ""); /* Save log info message */
				/* Update invoice status */
				$invoice_obj->status = "credit_memo";
				$invoice_obj->ref_invoice = $soap_result->SalesCreditMemoCard->No;
				$invoice_obj->updated_by = UploadConnector::MSD_LOGGER_NAME;
				$invoice_obj->updated_date = date("Y-m-d H:i:s");
				if ($invoice_obj->save())
					$success = true;
			}
			Globals::saveJsonFile($file_name . "-" . ($key + 1), $data);
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
    public static function syncMSDSalesCreditMemoPost($invoice_obj, $sales_office_no, $company, $trigger_id = null)
    {
        $date_from = date("Y-m-d") . " 00:00:00";
        $date_to = date("Y-m-d") . " 23:59:59";
        $sales_order_code =  $invoice_obj->sales_order_code;
        $invoice_code = $invoice_obj->code;
		$route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo-post']['list'];
		$url = Globals::soapABIMSDynamicsURL($route, $company);
        

        $client = new SoapClient($url, array(
            'trace' => true,
            'login' => Globals::getSoapOptions()['username'],
            'password' => Globals::getSoapOptions()['password'],
        )
        );


        $success = false;
        if (!isset($invoice_obj->ref_invoice)) {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $invoice_obj->code . ": Invoice Reference number does not exist.", "");
            continue;
        }
        /* Create new data in MSD */
        $invoice_obj_params = "<ns1:SalesCreditMemoPost><ns1:documentNo>" . $invoice_obj->ref_invoice . "</ns1:documentNo></ns1:SalesCreditMemoPost>";
        $invoice_obj_request = new SoapVar($invoice_obj_params, XSD_ANYXML);

        try {
            $soap_result = $client->SalesCreditMemoPost($invoice_obj_request);
            if (is_string($soap_result)) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $invoice_obj->code . " " . $soap_result, "");
                $invoice_obj->status = "credit_memo_posted";
                $invoice_obj->updated_by = UploadConnector::MSD_LOGGER_NAME;
                $invoice_obj->updated_date = date("Y-m-d H:i:s");
                $invoice_obj->save();
            } else {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $invoice_obj->code . " successfully posted.", ""); /* Save log info message */
                $invoice_obj->status = "credit_memo_posted";
                $invoice_obj->updated_by = UploadConnector::MSD_LOGGER_NAME;
                $invoice_obj->updated_date = date("Y-m-d H:i:s");
                if ($invoice_obj->save())
                    $success = true;
            }
            Globals::saveJsonFile($file_name . "-" . $key, $invoice_obj);
        } catch (SoapFault $e) {
            if (is_string($e->getMessage()))
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_SALES_CREDIT_MEMO_POST . "] " . $invoice_obj->code . " " . $e->getMessage(), "");
        }
}

public static function syncMSDCashReceiptJournalPost($invoice_obj, $sales_office_no, $company, $trigger_id = null)
    {
        $date_from = date("Y-m-d") . " 00:00:00";
        $date_to = date("Y-m-d") . " 23:59:59";
        $sales_order_code =  $invoice_obj->sales_order_code;
        $invoice_code = $invoice_obj->code;
        $route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-journal-post']['list'];
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        

        $file_name = UploadConnector::PATH . ($trigger_id != null ? $trigger_id . "-" : "") . date("YmdHis-") . str_replace(" ", "-", UploadConnector::MODULE_CASH_RECEIPT_POST);
       
        $client = new SoapClient($url, array(
            'trace' => true,
            'login' => Globals::getSoapOptions()['username'],
            'password' => Globals::getSoapOptions()['password'],

        )
        );

        $success = false;
        if ($invoice_obj->salesOrder()->first() == null) {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_obj->code . ": No Sales Order Found", "");
            continue;
        }
        $sales_order = $invoice_obj->salesOrder()->first();
        if ($sales_order->tempCollectionCash()->first() == null) {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_obj->code . ": Temp Collection Cash does not exist.", "");
            continue;
        }
        if ($sales_order->salesman()->first() == null) {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_obj->code . ": Salesman does not exist.", "");
            continue;
        }
        $temp_collection = $sales_order->tempCollectionCash()->first();
        $salesman = $sales_order->salesman()->first();
        if ($temp_collection) {
            $temp_collection_breakdowns = $temp_collection->tempCollectionCashBreakdown()->get()->toArray();
        } else {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_obj->code . ": Temp Collection Cash does not exist.", "");
            $invoice_obj->status = "failed_post";
            $invoice_obj->updated_by = UploadConnector::MSD_LOGGER_NAME;
            $invoice_obj->updated_date = date("Y-m-d H:i:s");
            $invoice_obj->save();
            continue;
        }
        if (count($temp_collection_breakdowns) == 0) {
            continue;
        }

        if ($salesman == null) {
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_obj->code . ": Salesman does not exist.", "");
            $invoice_obj->status = "failed_post";
            $invoice_obj->updated_by = UploadConnector::MSD_LOGGER_NAME;
            $invoice_obj->updated_date = date("Y-m-d H:i:s");
            $invoice_obj->save();
            continue;
        }


        foreach ($temp_collection_breakdowns as $key => $temp_collection_breakdown) {
            try {
                if (strpos($temp_collection->mode, 'Cash') !== false) {
                    $invoice_obj_params = "<ns1:PostCash><ns1:sPC>" . $salesman->code . "</ns1:sPC></ns1:PostCash>";
                    $invoice_obj_request = new SoapVar($invoice_obj_params, XSD_ANYXML);
                    $soap_result = $client->PostCash($invoice_obj_request);
                }
                if (strpos($temp_collection->mode, 'Check') !== false) {
                    $invoice_obj_params = "<ns1:PostCheque><ns1:sPC>" . $salesman->code . "</ns1:sPC></ns1:PostCheque>";
                    $invoice_obj_request = new SoapVar($invoice_obj_params, XSD_ANYXML);
                    $soap_result = $client->PostCheque($invoice_obj_request);
                }
                if (is_string($soap_result)) {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_obj->code . " " . $soap_result, "");
                    $invoice_obj->status = "failed_post";
                    $invoice_obj->updated_by = UploadConnector::MSD_LOGGER_NAME;
                    $invoice_obj->updated_date = date("Y-m-d H:i:s");
                    $invoice_obj->save();
                } else {
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . "] " . $invoice_obj->code . " successfully posted.", ""); /* Save log info message */
                    $invoice_obj->status = "posted";
                    $invoice_obj->updated_by = UploadConnector::MSD_LOGGER_NAME;
                    $invoice_obj->updated_date = date("Y-m-d H:i:s");
                    if ($invoice_obj->save())
                        $success = true;
                }
                Globals::saveJsonFile($file_name . "-" . $key, $invoice_obj);
            } catch (SoapFault $e) {
                $invoice_obj->status = "failed_post";
                $invoice_obj->save();
                if (is_string($e->getMessage()))
                    Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::ERROR, UploadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_CASH_RECEIPT_POST . "] " . $invoice_obj->code . " " . $e->getMessage(), "");
            }

            if ($success) {
                Utils::updateTriggerTotalRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger total rows */
            } else {
                Utils::updateTriggerFailedRows($trigger_id, 1, $key === 0 ? false : true); /* Update trigger failed rows = existing + response failed_rows */
            }
        }
    }
    
}
