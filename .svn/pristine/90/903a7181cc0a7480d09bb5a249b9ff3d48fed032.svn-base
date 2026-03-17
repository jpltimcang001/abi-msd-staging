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
use App\Data\BalanceData as BalanceData;
use App\Data\BalanceEmptiesData as BalanceEmptiesData;
use App\Data\CashReceiptData;
use App\Data\CustomerDiscountGroupData;
use App\Data\CustomerPostingGroupData;
use App\Data\CustomerPriceGroupData;
use App\Data\DistributionChannelData;
use App\Data\GenBusPostingGroupData;
use App\Data\InventoryData;
use App\Data\InvoiceData;
use App\Data\InvoiceDetailData;
use App\Data\LocationData;
use App\Data\NewCustomerRequestData;
use App\Data\OutgoingNotificationData;
use App\Data\PaymentMethodData;
use App\Data\PaymentTermsData;
use App\Data\PromotionBudgetData;
use App\Data\PromotionData;
use App\Data\PromotionDetailData;
use App\Data\PromotionDiscountLineData;
use App\Data\PromotionLocationData;
use App\Data\PromotionFOCData;
use App\Data\ReservationEntryData;
use App\Data\SalesCreditMemoData;
use App\Data\SalesmanData;
use App\Data\SalesmanTypeData;
use App\Data\SalesOrderData;
use App\Data\SalesPriceData;
use App\Data\SKUData;
use App\Data\SubChannelData;
use App\Data\VATBusPostingGroupData;
use App\Data\ZoneData;
/* Models */
use App\Model\noc\Invoice;
use App\Model\noc\SalesOrder;
use App\Model\noc\SalesOffice as SalesOffice;

class APICommandHandler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apirun:send {type} {data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Console API commands';

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
        switch ($this->argument('type')) {
                /* Download */
            case "customer-discount-group":
                $this->runCustomerDiscountGroup();
                break;
            case "customer-price-group":
                $this->runCustomerPriceGroup();
                break;
            case "customer-posting-group":
                $this->runCustomerPostingGroup();
                break;
            case "gen-bus-posting-group":
                $this->runGenBusPostingGroup();
                break;
            case "vat-bus-posting-group":
                $this->runVATBusPostingGroup();
                break;
            case "payment-method":
                $this->runPaymentMethod();
                break;
            case "payment-terms":
                $this->runPaymentTerms();
                break;
            case "channel":
                $this->runDistributionChannel();
                break;
            case "sub-channel":
                $this->runSubChannel();
                break;
            case "location":
                $this->runLocation();
                break;
            case "salesman-type":
                $this->runSalesmanType();
                break;
            case "salesman":
                $this->runSalesman();
                break;
            case "customer":
                $this->runCustomer();
                break;
            case "product":
                $this->runProduct();
                break;
            case "sales-price":
                $this->runSalesPrice();
                break;
            case "promotion":
                $this->runPromotion();
                break;
            case "promotion-line":
                $this->runPromotionLine();
                break;
            case "promotion-customer":
                $this->runPromotionCustomer();
                break;
            case "promotion-discount-line":
                $this->runPromotionDiscountLine();
                break;
            case "promotion-budget":
                $this->runPromotionBudget();
                break;
            case "promotion-deal":
                $this->runPromotionDeal();
                break;
            case "posted-sales-invoice":
                $this->runPostedSalesInvoice();
                break;
            case "posted-sales-invoice-booking":
                $this->runPostedSalesInvoiceBooking();
                break;
            case "posted-sales-invoice-line":
                $this->runPostedSalesInvoiceLine();
                break;
            case "posted-sales-invoice-line-booking":
                $this->runPostedSalesInvoiceLineBooking();
                break;
            case "stock-in-salesman-warehouse":
                $this->runStockInSalesmanWarehouse();
                break;
            case "pending-customer-creation-request":
                $this->runPendingCustomerCreationRequest();
                break;
            case "customer-balance":
                $this->runCustomerBalance();
                break;
            case "customer-balance-empties":
                $this->runCustomerBalanceEmpties();
                break;
			case "posted-pick-note":
				$this->runPostedPickNote();
				break;
			case "posted-pick-note-multiple":
				$this->runInvoicePickNoteDownload();
				break;
			case "picknote-inventory-collection":
				$this->runMultiplePickNote();
				break;
			case "transfer-order":
				$this->runTransferOrder();
				break;
			case "credit-memo-post-manual":
				$this->runSalesCreditMemoManual();
				break;
                /* Batch Download */
            case "customer-multiple":
                $this->runCustomerMultiple();
                break;
            case "stocks-multiple":
                $this->runStocksMultiple();
                break;
            case "invoice-multiple":
                $this->runSalesInvoiceMultiple();
                break;
                /* Upload */
            case "withdrawal-request":
                $this->runWithdrawalRequest();
                break;
            case "sales-order":
                $this->runSalesOrder();
                break;
            case "sales-order-reservation-entry":
                $this->runSalesOrderReservationEntry();
                break;
            case "sales-order-post":
                $this->runSalesOrderPost();
                break;
            case "sales-order-assign-lots-and-post":
                $this->runSalesOrderAssignLotsAndPost();
                break;
            case "cash-receipt-journal":
                $this->runCashReceiptJournal();
                break;
            case "cash-receipt-journal-post":
                $this->runCashReceiptJournalPost();
                break;
            case "sales-credit-memo":
                $this->runSalesCreditMemo();
                break;
            case "sales-credit-memo-post":
                $this->runSalesCreditMemoPost();
                break;
            case "credit-memo-queue":
                $this->runSalesCreditMemoQueue();
                break;
            case "return-request":
                $this->runReturnRequest();
                break;
            case "return-request-reservation-entry-salesman":
                $this->runReturnRequestReservationEntrySalesman();
                break;
            case "return-request-reservation-entry-sales-office":
                $this->runReturnRequestReservationEntryWarehouse();
                break;
            case "new-customer-creation-request":
                $this->runNewCustomerCreationRequest();
                break;
            case "caf-credit-limit":
                $this->runCafCreditLimit();
                break;
            case "all-download":
                $this->runAllDownload();
                break;
			case "credit-memo-location-cancel":
				$this->runSalesCreditMemoCancel();
				break;
			case "credit-memo-update-location":
				$this->runSalesCreditMemoUpdateLocation();
				break;
			case "save-approved-refund":
				$this->runSaveApprovedRefund();
				break;
                /* Batch Upload */
            case "sales-order-queue":
                $this->runSalesOrderBatch();
                break;
            case "sales-order-multiple":
                $this->runSalesOrderMultiple();
                break;
            case "sales-credit-memo-multiple":
                $this->runSalesCreditMemoMultiple();
                break;
            case "cash-receipt-multiple":
                $this->runCashReceiptMultiple();
                break;
            case "batch-transaction":
                $this->runBatchTransaction();
                break;
            case "batch-balance":
                $this->runBatchBalanceDownload();
                break;
            case "batch-sales-order-assign-lots-and-post":
                $this->runSalesOrderDockExit();
                break;
            case "sales-shipment":
                $this->runSalesShipmentUpload();
                break;
                /* Batch Sequence */
            case "withdraw-stock-salesorder":
                $this->runWithdrawStockSalesOrder();
                break;
            default:
                return;
        }
    }

    /** --------------------- Download ~ Start ----------------------- */

    /** 
     * Version of retrieveCustomerDiscountGroup that is run in the console.
     * (03/06/2022)
     * 
     * @return void
     * */
    public function runCustomerDiscountGroup($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-discount-group']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger("", CustomerDiscountGroupData::MODULE_CUSTOMER_DISCOUNT_GROUP, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(CustomerDiscountGroupData::MODULE_NAME_CUSTOMER_DISCOUNT_GROUP) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomerDiscountGroup($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(CustomerDiscountGroupData::MODULE_NAME_CUSTOMER_DISCOUNT_GROUP) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrieveCustomer that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    private function runCustomer($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, LocationData::MODULE_LOCATION, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(LocationData::MODULE_NAME_LOCATION) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomer($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(LocationData::MODULE_NAME_LOCATION) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrieveProduct that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runProduct($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['product']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, SKUData::MODULE_SKU, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(SKUData::MODULE_NAME_SKU) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDProduct($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(SKUData::MODULE_NAME_SKU) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrievePromotion that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPromotion($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, PromotionData::MODULE_PROMOTION, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionData::MODULE_NAME_PROMOTION) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionNew($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionData::MODULE_NAME_PROMOTION) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    
    /**
     * Version of retrievePromotion that is run in the console.
al     * 
     * @return void
     */
    public function runPromotionDeal($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no,PromotionFOCData::MODULE_PROMOTION_FOC, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
			
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(PromotionFOCData::MODULE_NAME_PROMOTION_FOC) . "\n");
           
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionFOCData::MODULE_NAME_PROMOTION_FOC) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionDeals($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionFOCData::MODULE_NAME_PROMOTION_FOC) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrievePromotionLine that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPromotionLine($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-line']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, PromotionDetailData::MODULE_PROMOTION_DETAIL, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionDetailData::MODULE_NAME_PROMOTION_DETAIL) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionLine($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionDetailData::MODULE_NAME_PROMOTION_DETAIL) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrievePromotionCustomer that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPromotionCustomer($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-customer']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, PromotionLocationData::MODULE_PROMOTION_LOCATION, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionLocationData::MODULE_NAME_PROMOTION_LOCATION) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionCustomer($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionLocationData::MODULE_NAME_PROMOTION_LOCATION) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }


    /**
     * Version of retrievePromotionDiscountLine that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPromotionDiscountLine($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-discount-line']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, PromotionDiscountLineData::MODULE_PROMOTION_DISCOUNT, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionDiscountLineData::MODULE_NAME_PROMOTION_DISCOUNT) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionDiscountLine($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionDiscountLineData::MODULE_NAME_PROMOTION_DISCOUNT) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrievePromotionBudget that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPromotionBudget($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-budget']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, PromotionBudgetData::MODULE_PROMOTION_BUDGET, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionBudgetData::MODULE_NAME_PROMOTION_BUDGET) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionBudget($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionBudgetData::MODULE_NAME_PROMOTION_BUDGET) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrievePostedSalesInvoice that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPostedSalesInvoice($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['posted-invoice']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, InvoiceData::MODULE_POSTED_INVOICE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPostedSalesInvoice($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
    /**
     * Version of retrievePostedSalesInvoice that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPostedDirectSalesInvoice($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['posted-invoice']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, InvoiceData::MODULE_POSTED_INVOICE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
			$result = [];

            if (!$from_batch) 
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " maintenance.", ""); /* Save log message */
            
			$result = DownloadConnector::syncMSDDirectPostedSalesInvoice($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            
			if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return $result;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
    /**
     * Version of retrievePostedSalesInvoice that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPostedDirectSalesInvoiceLine($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['posted-invoice-line']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, InvoiceData::MODULE_POSTED_INVOICE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPostedDirectSalesInvoiceLine($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }


    /**
     * Version of retrievePostedSalesInvoice that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPostedSalesInvoiceBooking($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['posted-invoice']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, InvoiceData::MODULE_POSTED_INVOICE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPostedSalesInvoiceBooking($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrievePostedSalesInvoice that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPostedSalesInvoiceLine($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['posted-invoice-line']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, InvoiceDetailData::MODULE_POSTED_INVOICE_DETAIL, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPostedSalesInvoiceLine($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrievePostedSalesInvoice that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPostedSalesInvoiceLineBooking($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['posted-invoice-line']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, InvoiceDetailData::MODULE_POSTED_INVOICE_DETAIL, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPostedSalesInvoiceLineBooking($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrieveSalesPrice that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runSalesPrice($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-price']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, SalesPriceData::MODULE_SALES_PRICE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(SalesPriceData::MODULE_NAME_SALES_PRICE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDSalesPrice($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(SalesPriceData::MODULE_NAME_SALES_PRICE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrieveStockInSalesmanWarehouse that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runStockInSalesmanWarehouse($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['stock-in-salesman-warehouse']['list'];
        $company = (isset($data['company']) && $data['company'] !="") ? $data['company'] : "BII Live";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, InventoryData::MODULE_INVENTORY, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(InventoryData::MODULE_NAME_INVENTORY) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDStockInSalesmanWarehouse($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(InventoryData::MODULE_NAME_INVENTORY) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of retrievePendingCustomerCreationRequest that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runPendingCustomerCreationRequest($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['new-customer-creation-request']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, DownloadConnector::MODULE_PENDING_LOCATION_CREATION, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(DownloadConnector::MODULE_NAME_PENDING_LOCATION_CREATION) . " maintenance.", ""); /* Save log message */
            UploadConnector::syncMSDNewCustomerCreationRequest($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(DownloadConnector::MODULE_NAME_PENDING_LOCATION_CREATION) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Version of retrieveCustomerPriceGroup that is run in the console.
     * (03/06/2022)
     * 
     * @return void
     * */
    public function runCustomerPriceGroup($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-price-group']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, CustomerPriceGroupData::MODULE_CUSTOMER_PRICE_GROUP, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(CustomerPriceGroupData::MODULE_NAME_CUSTOMER_PRICE_GROUP) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomerPriceGroup($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(CustomerPriceGroupData::MODULE_NAME_CUSTOMER_PRICE_GROUP) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Version of retrieveCustomerPostingGroup that is run in the console.
     * (03/06/2022)
     * 
     * @return void
     * */
    public function runCustomerPostingGroup($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-posting-group']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, CustomerPostingGroupData::MODULE_CUSTOMER_POSTING_GROUP, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(CustomerPostingGroupData::MODULE_NAME_CUSTOMER_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomerPostingGroup($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(CustomerPostingGroupData::MODULE_NAME_CUSTOMER_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Version of retrieveGenBusPostingGroup that is run in the console.
     * (16/06/2022)
     * 
     * @return void
     * */
    public function runGenBusPostingGroup($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['gen-bus-posting-group']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, GenBusPostingGroupData::MODULE_GEN_BUS_POSTING_GROUP, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(GenBusPostingGroupData::MODULE_NAME_GEN_BUS_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDGenBusPostingGroup($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(GenBusPostingGroupData::MODULE_NAME_GEN_BUS_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Version of retrieveVATBusPostingGroup that is run in the console.
     * (16/06/2022)
     * 
     * @return void
     * */
    public function runVATBusPostingGroup($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['vat-bus-posting-group']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, VATBusPostingGroupData::MODULE_VAT_BUS_POSTING_GROUP, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(VATBusPostingGroupData::MODULE_NAME_VAT_BUS_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDVATBusPostingGroup($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(VATBusPostingGroupData::MODULE_NAME_VAT_BUS_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Version of retrieveDistributionChannel that is run in the console.
     * (16/06/2022)
     * 
     * @return void
     * */
    public function runDistributionChannel($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['distribution-channel']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, DistributionChannelData::MODULE_DISTRIBUTION_CHANNEL, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDDistributionChannel($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    public function runSubChannel($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['sub-channel']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, SubChannelData::MODULE_SUB_CHANNEL, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(SubChannelData::MODULE_NAME_SUB_CHANNEL) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDSubChannel($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(SubChannelData::MODULE_NAME_SUB_CHANNEL) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Version of retrievePaymentMethod that is run in the console.
     * (16/06/2022)
     * 
     * @return void
     * */
    public function runPaymentMethod($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['payment-method']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, PaymentMethodData::MODULE_PAYMENT_METHOD, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PaymentMethodData::MODULE_NAME_PAYMENT_METHOD) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPaymentMethod($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PaymentMethodData::MODULE_NAME_PAYMENT_METHOD) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Version of retrievePaymentTerms that is run in the console.
     * (16/06/2022)
     * 
     * @return void
     * */
    public function runPaymentTerms($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['payment-terms']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger("", PaymentTermsData::MODULE_PAYMENT_TERMS, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PaymentTermsData::MODULE_NAME_PAYMENT_TERMS) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPaymentTerms($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PaymentTermsData::MODULE_NAME_PAYMENT_TERMS) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Version of retrieveSalesman that is run in the console.
     * (16/06/2022)
     * 
     * @return void
     * */
    public function runSalesman($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['salesman']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, SalesmanData::MODULE_SALESMAN, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(SalesmanData::MODULE_NAME_SALESMAN) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDSalesman($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(SalesmanData::MODULE_NAME_SALESMAN) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }


    /** 
     * Version of retrieveSalesmanType that is run in the console.
     * (16/06/2022)
     * 
     * @return void
     * */
    public function runSalesmanType($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['salesman-type']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, SalesmanTypeData::MODULE_SALESMAN_TYPE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDSalesmanType($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Version of retrieveLocation that is run in the console.
     * (16/06/2022)
     * 
     * @return void
     * */
    public function runLocation($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['location']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, ZoneData::MODULE_ZONE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(ZoneData::MODULE_NAME_ZONE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDZone($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(ZoneData::MODULE_NAME_ZONE) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    
    /**
     * Version of retrieveCustomerBalance that is run in the console.
     * (23/11/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runCustomerBalance($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-balance']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, BalanceData::MODULE_BALANCE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomerBalance($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    
    /**
     * Version of retrieveCustomerBalance that is run in the console.
     * (11/04/2023)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runCustomerBalanceEmpties($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-balance-empties']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $from_batch = ($trigger != null);

        try {
            $trigger = $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, BalanceEmptiesData::MODULE_BALANCE_EMPTIES, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomerBalanceEmpties($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return; 
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
	 public function runPostedPickNote($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['posted-pick-note']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $from_batch = ($trigger != null);

        try {
            $trigger = $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, "PICK NOTE", DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;


            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve pick note maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPostedPickNote($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
             if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved pick note maintenance.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return; 
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
    /**
     * Retrieve PickNoteCollection
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function runPickNoteCollection($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
		$loadboard_url = Globals::createLoadboardURL('retrieve-collection');
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $from_batch = ($trigger != null);

        try {
            $trigger = $trigger = ($trigger != null) ? $trigger :  Utils::saveTrigger($sales_office_no, "PICK NOTE COLLECTION", DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            //Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncLoadBoardPickNoteCollection($method, $loadboard_url, $data, $trigger_id); /* Call MSD REST API then process the response */
            //Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
             if (!$from_batch) {
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
		}
            return; 
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
	 public function runTransferOrder($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['transfer-order']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
       
        try {
            $trigger = $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, "Transfer Order", DownloadConnector::STATUS_PENDING, 'Test');
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
			UploadConnector::syncMSDTransferOrderQueue($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            return; 
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
	/**
     * Retrieve PickNoteInventory
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function runPickNoteInventory($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
		$loadboard_url = Globals::createLoadboardURL('retrieve-inventory');
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
		$from_batch = ($trigger != null);
        try {
            $trigger = $trigger = ($trigger != null) ? $trigger :  Utils::saveTrigger($sales_office_no, "PICK NOTE INVENTORY", DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            //Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncLoadBoardPickNoteInventory($method, $loadboard_url, $data, $trigger_id); /* Call MSD REST API then process the response */
            //Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
             if (!$from_batch) {
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
			}
            return; 
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
    /** --------------------- Download ~ End ------------------------- */

    /** --------------------- Upload ~ Start ------------------------- */

    /**
     * Version of runWithdrawalRequest that is run in the console.
     * (01/07/2022)
     * 
     * @return void
     */
    public function runWithdrawalRequest($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['transfer-order']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, (new OutgoingNotificationData)->getModuleByType(0), UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower((new OutgoingNotificationData)->getModuleNameByType(0)) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDWithdrawalRequest($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower((new OutgoingNotificationData)->getModuleNameByType(0)) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of runSalesOrder that is run in the console.
     * (01/07/2022)
     * 
     * @return void
     */
    public function runSalesOrder($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-order']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, SalesOrderData::MODULE_SALES_ORDER, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrder($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of runSalesOrder that is run in the console.
     * (01/07/2022)
     * 
     * @return void
     */
    public function runSalesOrderQueueO($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-order']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, SalesOrderData::MODULE_SALES_ORDER, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrderQueue($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
	/**
     * Retrieve Sales Credit Memo Cancel
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function runSalesCreditMemoCancel($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $company = isset($data['company']) ? $data['company'] : "";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo']['list'];
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
		$from_batch = ($trigger != null);
        try {
            $trigger = $trigger = ($trigger != null) ? $trigger :  Utils::saveTrigger($sales_office_no, "CREDIT MEMO CANCEL", UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
            UploadConnector::syncMSDCreditMemoCancelled($method, $url, $data,  $sales_office_no, $trigger_id, $company); /* Call MSD REST API then process the response */
            if (!$from_batch) {
				Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
				Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
			}
            return; 
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

	public function runSalesCreditMemoUpdateLocation($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $company = isset($data['company']) ? $data['company'] : "";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo-post']['list'];
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
		$from_batch = ($trigger != null);
        try {
            $trigger = $trigger = ($trigger != null) ? $trigger :  Utils::saveTrigger($sales_office_no, "CREDIT UPDATE LOCATION", UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
            UploadConnector::syncMSDCreditUpdateLocation($method, $url, $data,  $sales_office_no, $trigger_id, $company); /* Call MSD REST API then process the response */
            if (!$from_batch) {
				Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
				Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
			}
            return; 
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	/**
     * Saved Approved Refund
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function runSaveApprovedRefund($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
		if($sales_office_no){
			$sales_office_obj = SalesOffice::where('short_desc', '=', $sales_office_no)->first();
			$sales_office_no = $sales_office_obj->no;
		}
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
		$from_batch = ($trigger != null);
        try {
            $trigger = ($trigger != null) ? $trigger :  Utils::saveTrigger($sales_office_no, "APPROVED REFUND", UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
            DownloadConnector::syncMSDSaveApprovedRefund($data, $sales_office_no, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
				Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
				Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
			}
            return; 
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

	/**
     * Upload Posted Sales Shipment
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function runSalesShipmentUpload($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $company = isset($data['company']) ? $data['company'] : "";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-shipment']['list'];
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
		$from_batch = ($trigger != null);
        try {
            $trigger = ($trigger != null) ? $trigger :  Utils::saveTrigger($sales_office_no, "SALES SHIPMENT", UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
            UploadConnector::syncMSDSalesShipment($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
				Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
				Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
			}
            return; 
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

	/**
     * Upload Manual upload of sales credit manual
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function runSalesCreditMemoManual($data = null, $trigger = null)
    {
       $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;

        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo-post']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company, "Codeunit");
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_SALES_CREDIT_MEMO_POST, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_SALES_CREDIT_MEMO_POST) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesCreditMemoPostManual($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_NAME_SALES_CREDIT_MEMO_POST) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of runSalesOrderReservationEntry that is run in the console.
     * (04/07/2022)
     * 
     * @return void
     */
    public function runSalesOrderReservationEntry($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['reservation-entry']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger :  Utils::saveTrigger($sales_office_no, (new ReservationEntryData)->getModuleByType(0), UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower((new ReservationEntryData)->getModuleNameByType(0)) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrderReservationEntry($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower((new ReservationEntryData)->getModuleNameByType(0)) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of generateSalesOrderPost that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runSalesOrderPost($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-order-post']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company, "CodeUnit");
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger :  Utils::saveTrigger($sales_office_no, SalesOrderData::MODULE_SALES_ORDER_POST, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrderPost($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of syncMSDSalesOrderAssignLotsAndPost that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runSalesOrderAssignLotsAndPost($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-order-post']['list'];
        $company = isset($data['company']) ? $data['company'] : "BII Live";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company, "CodeUnit");
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger :  Utils::saveTrigger($sales_office_no, SalesOrderData::MODULE_SALES_ORDER_POST . " LOT", UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync Lots and " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrderAssignLotsAndPost($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced Lots and " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }


    /**
     * Version of generateNewCustomerCreationRequest that is run in the console.
     * (07/04/2022)
     * 
     * @return void
     */
    public function runNewCustomerCreationRequest($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['new-customer-creation-request']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $from_batch = ($trigger != null);
		$is_auto = isset($data['is_auto']) ? $data['is_auto'] : 0;

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_NEW_LOCATION_CREATION, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if ($from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_NEW_LOCATION_CREATION) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDNewCustomerCreationRequest($method, $url, $data, $trigger_id, $is_auto); /* Call MSD REST API then process the response */
            if ($from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_NAME_NEW_LOCATION_CREATION) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of generateCashReceiptJournal that is run in the console.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runCashReceiptJournal($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-journal']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, CashReceiptData::MODULE_CASH_RECEIPT, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(CashReceiptData::MODULE_NAME_CASH_RECEIPT) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDCashReceiptJournal($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(CashReceiptData::MODULE_NAME_CASH_RECEIPT) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * (25/08/22)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runCashReceiptJournalPost($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-post']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company, "Codeunit");
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_CASH_RECEIPT_POST, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_CASH_RECEIPT_POST) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDCashReceiptJournalPost($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_NAME_CASH_RECEIPT_POST) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of generateSalesCreditMemo that is run in the console.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runSalesCreditMemo($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesCreditMemo($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if ($from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
	
    /**
     * Version of generateSalesCreditMemo that is run in the console.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runSalesCreditMemoQueue($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesCreditMemoPulloutQueue($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if ($from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * (25/08/22)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runSalesCreditMemoPost($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;

        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo-post']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company, "Codeunit");
        $from_batch = ($trigger != null);

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_SALES_CREDIT_MEMO_POST, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_SALES_CREDIT_MEMO_POST) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesCreditMemoPost($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_NAME_SALES_CREDIT_MEMO_POST) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of generateReturnRequest that is run in the console.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runReturnRequest($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['transfer-order']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, (new OutgoingNotificationData)->getModuleByType(1), UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower((new OutgoingNotificationData)->getModuleNameByType(1)) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDReturnRequest($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower((new OutgoingNotificationData)->getModuleNameByType(1)) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of generateReturnRequestReservationEntrySalesman that is run in the console.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runReturnRequestReservationEntrySalesman()
    {
        $data = json_decode($this->argument('data'), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['reservation-entry']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, (new ReservationEntryData)->getModuleByType(1), UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower((new ReservationEntryData)->getModuleNameByType(1)) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDReturnRequestReservationEntrySalesman($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower((new ReservationEntryData)->getModuleNameByType(1)) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of generateReturnRequestReservationEntryWarehouse that is run in the console.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runReturnRequestReservationEntryWarehouse()
    {
        $data = json_decode($this->argument('data'), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['reservation-entry']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, (new ReservationEntryData)->getModuleByType(2), UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower((new ReservationEntryData)->getModuleNameByType(2)) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDReturnRequestReservationEntryWarehouse($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower((new ReservationEntryData)->getModuleNameByType(2)) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Version of runCAFCreditLimit that is run in the console.
     * (03/10/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runCafCreditLimit()
    {       
        $data = json_decode($this->argument('data'), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-modify-request']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_CAF_CREDIT_LIMIT, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_CAF_CREDIT_LIMIT) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDCafCreditLimit($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_CAF_CREDIT_LIMIT) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }


    /** ---------------------- Upload ~ End -------------------------- */

    /** ----------------- Batch Download ~ Start --------------------- */

    /** 
     * Run Distribution Channel then Customer sync in one request
     * (16/06/2022)
     * 
     * @param 
     * 
     * @return void
     * */
    public function runCustomerMultiple()
    {
        $data = json_decode($this->argument('data'), true);
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $salesman_params = isset($data['params']['Salesperson_Code']) ? $data['params']['Salesperson_Code'] : "";
        $params_l =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'batch_enabled' => true,
            'params' => [
                'Global_Dimension_1_Code' => isset($data['params']['Global_Dimension_1_Code']) ? $data['params']['Global_Dimension_1_Code'] : "",
                'No' =>  isset($data['params']['No']) ? $data['params']['No'] : "",
            ]
        ];
        $params_d =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'params' => [
                'WIN_Code' => isset($data['params']['WIN_Code']) ? $data['params']['WIN_Code'] : "",
            ]
        ];

        try {
            $trigger = Utils::saveTrigger($sales_office_no, DownloadConnector::MODULE_LOCATION_MULTIPLE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE) . " maintenance.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Started " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . " maintenance.", ""); /* Save log message */
            $this->runDistributionChannel($params_d, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Finished " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . " maintenance.", ""); /* Save log message */

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Started " . strtolower(LocationData::MODULE_NAME_LOCATION) . " maintenance.", ""); /* Save log message */
            if (is_array($salesman_params) && count($salesman_params) > 0) {
                foreach ($salesman_params as $sm) {
                    $new_param = $params_l;
                    $new_param['params']['Salesperson_Code'] = $sm;
                    print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtoupper($sm) . " " . strtolower(LocationData::MODULE_NAME_LOCATION) . "\n");
                    $this->runCustomer($new_param, $trigger);
                }
            } elseif ($salesman_params != "") {
                $params_l['params']['Salesperson_Code'] = $salesman_params;
                print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(LocationData::MODULE_NAME_LOCATION) . "\n");
                $this->runCustomer($params_l, $trigger);
            }
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Finished " . strtolower(LocationData::MODULE_NAME_LOCATION) . " maintenance.", ""); /* Save log message */


            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
	 /** 
     * Run Distribution Channel, Customer then Product sync in one request
     * (16/06/2022)
     * 
     * @param 
     * 
     * @return void
     * */
    public function runAllDownload()
    {
        $data = json_decode($this->argument('data'), true);
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $salesman_params = isset($data['params']['Salesperson_Code']) ? $data['params']['Salesperson_Code'] : "";
        $params_l =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'batch_enabled' => true,
            'params' => [
                'Global_Dimension_1_Code' => isset($data['params']['Global_Dimension_1_Code']) ? $data['params']['Global_Dimension_1_Code'] : "",
                'No' =>  isset($data['params']['No']) ? $data['params']['No'] : "",
                'Salesperson_Code' =>  isset($data['params']['Salesperson_Code']) ? $data['params']['Salesperson_Code'] : "",
				//'Last_Date_Modified' => isset($data['params']['date_from']) ? $data['params']['date_from'] : "",
            ]
        ];
        $params_d =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'params' => [
                'WIN_Code' => isset($data['params']['WIN_Code']) ? $data['params']['WIN_Code'] : "",
            ]
        ];
        $data_sm =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'batch_enabled' => true,
            'params' => [
                'Code' => isset($data['params']['Code']) ? $data['params']['Code'] : "",
            ]
        ];
        $params_sku =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'params' => [
                //'WIN_Code' => isset($data['params']['WIN_Code']) ? $data['params']['WIN_Code'] : "",
                'InventoryField' => '>0',
                'Blocked' => 'false',
            ]
        ];
        $params_dc =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'batch_enabled' => true,
            'params' => [
                'No' =>  isset($data['params']['Scheme_Code']) ? $data['params']['Scheme_Code'] : "",
				'To_Date' => ">".  ( isset($data['params']['date_from']) ? $data['params']['date_from'] : date("Y-m-d")),
				'Customer_Code' => isset($data['params']['No']) ? $data['params']['No'] : "",
				'Published' =>  "true",
				//'From_Date' => "<". ( isset($data['params']['date_from']) ? $data['params']['date_from'] : date("Y-m-d", strtotime("-3 days"))),
				'SystemModifiedAt' => ">". date("Y-m-d", strtotime("-1 week"))
            ]
        ];
		
        try {
            $trigger = Utils::saveTrigger($sales_office_no, DownloadConnector::MODULE_LOCATION_MULTIPLE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
            
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE) . " maintenance.", ""); /* Save log message */
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Started " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . " maintenance.", ""); /* Save log message */
            $this->runDistributionChannel($params_d, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Finished " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . " maintenance.", ""); /* Save log message */
            
            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SubChannelData::MODULE_NAME_SUB_CHANNEL) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Started " . strtolower(SubChannelData::MODULE_NAME_SUB_CHANNEL) . " maintenance.", ""); /* Save log message */
            $this->runSubChannel($params_d, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Finished " . strtolower(SubChannelData::MODULE_NAME_SUB_CHANNEL) . " maintenance.", ""); /* Save log message */

			print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesmanData::MODULE_NAME_SALESMAN) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . "] Started " . strtolower(SalesmanData::MODULE_NAME_SALESMAN)  . " maintenance.", ""); /* Save log message */
            $this->runSalesman($data_sm, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . "] Finished " . strtolower(SalesmanData::MODULE_NAME_SALESMAN)  . " maintenance.", ""); /* Save log message */

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Started " . strtolower(LocationData::MODULE_NAME_LOCATION) . " maintenance.", ""); /* Save log message */
            if (is_array($salesman_params) && count($salesman_params) > 0) {
                foreach ($salesman_params as $sm) {
                    $new_param = $params_l;
                    $new_param['params']['Salesperson_Code'] = $sm['code'];
                    print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtoupper($sm['code']) . " " . strtolower(LocationData::MODULE_NAME_LOCATION) . "\n");
                    $this->runCustomer($new_param, $trigger);
                }
            } elseif ($salesman_params != "" && !is_array($salesman_params)) {
                $params_l['params']['Salesperson_Code'] = $salesman_params;
                print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(LocationData::MODULE_NAME_LOCATION) . "\n");
                $this->runCustomer($params_l, $trigger);
            }
			else {
                print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(LocationData::MODULE_NAME_LOCATION) . "\n");
                $this->runCustomer($params_l, $trigger);				
			}
			
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SKUData::MODULE_NAME_SKU) . "\n");
			$this->runProduct($params_sku, $trigger);
			
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Finished Promotions maintenance.", ""); /* Save log message */
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(PromotionData::MODULE_NAME_PROMOTION) . "\n");
            $this->runPromotion($params_dc, $trigger);
				
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . "] Finished Deals maintenance.", ""); /* Save log message */
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(PromotionFOCData::MODULE_NAME_PROMOTION_FOC) . "\n");
			$this->runPromotionDeal($params_dc, $trigger);

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Run Zone, Salesman Type, Salesman then Stock in Salesman Warehouse sync in one request
     * (16/06/2022)
     * 
     * @return void
     * */
    public function runStocksMultiple()
    {
        $data = json_decode($this->argument('data'), true);
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $data_l =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'params' => [
                'Code' => isset($data['params']['zone_code']) ? $data['params']['zone_code'] : "",
            ]
        ];
        $data_st =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'batch_enabled' => true,
            'params' => [
                'WIN_Code' => isset($data['params']['WIN_Code']) ? $data['params']['WIN_Code'] : "",
            ]
        ];
        $data_sm =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'batch_enabled' => true,
            'params' => [
                'Code' => isset($data['params']['Code']) ? $data['params']['Code'] : "",
            ]
        ];
        $data_sw =  [
            'company' => $data['company'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'batch_enabled' => true,
            'params' => [
                'Location_Code' => isset($data['params']['Location_Code']) ? $data['params']['Location_Code'] : "",
            ]
        ];

        try {
            $trigger = Utils::saveTrigger($sales_office_no, DownloadConnector::MODULE_INVENTORY_MULTIPLE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE) . " maintenance.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(ZoneData::MODULE_NAME_ZONE) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . "] Started " . strtolower(ZoneData::MODULE_NAME_ZONE) . " maintenance.", ""); /* Save log message */
            $this->runLocation($data_l, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . "] Finished " . strtolower(ZoneData::MODULE_NAME_ZONE) . " maintenance.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . "] Started " . strtolower(SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE) . " maintenance.", ""); /* Save log message */
            $this->runSalesmanType($data_st, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . "] Started " . strtolower(SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE) . " maintenance.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesmanData::MODULE_NAME_SALESMAN) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . "] Started " . strtolower(SalesmanData::MODULE_NAME_SALESMAN)  . " maintenance.", ""); /* Save log message */
            $this->runSalesman($data_sm, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . "] Finished " . strtolower(SalesmanData::MODULE_NAME_SALESMAN)  . " maintenance.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(InventoryData::MODULE_INVENTORY) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . "] Started " . strtolower(InventoryData::MODULE_NAME_INVENTORY) . " maintenance.", ""); /* Save log message */
            $this->runStockInSalesmanWarehouse($data_sw, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . "] Finished " . strtolower(InventoryData::MODULE_NAME_INVENTORY) . " maintenance.", ""); /* Save log message */

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /** 
     * Run Posted Sails Invoice then Posted Sales invoice line
     * (16/06/2022)
     * 
     * @return void
     * */
    public function runSalesInvoiceMultiple($data = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $data_header =  [
            'company' => $data['company'],
			'is_auto' => $data['is_auto'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'batch_enabled' => true,
            'sales_order_code' => isset($data['params']['Order_No']) ? $data['params']['Order_No'] : "",
            'params' => [
                'Shortcut_Dimension_1_Code' => isset($data['params']['Shortcut_Dimension_1_Code']) ? $data['params']['Shortcut_Dimension_1_Code'] : "",
                'No' => isset($data['params']['Order_No']) ? $data['params']['Order_No'] : "",
                'Document_Date' => isset($data['params']['Document_Date']) ? $data['params']['Document_Date'] : ">". date('Y-m-d', strtotime("-3 days")) ,
            ]
        ];
        $data_line =  [
             'company' => $data['company'],
			'is_auto' => $data['is_auto'],
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'added_by' => isset($data['added_by']) ? $data['added_by'] : "",
            'batch_enabled' => true,
            'sales_order_code' => isset($data['params']['Order_No']) ? $data['params']['Order_No'] : "",
            'params' => [
                'Shortcut_Dimension_1_Code' => isset($data['params']['Shortcut_Dimension_1_Code']) ? $data['params']['Shortcut_Dimension_1_Code'] : "",
                //'Document_Date' => isset($data['params']['Document_Date']) ? $data['params']['Document_Date'] : "",
            ]
        ];


        try {
            $trigger = Utils::saveTrigger($sales_office_no, DownloadConnector::MODULE_INVOICE_MULTIPLE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE) . " maintenance.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE . "] Started " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE)  . " maintenance.", ""); /* Save log message */
            $invoice_result = $this->runPostedDirectSalesInvoice($data_header, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE . "] Finished " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE)  . " maintenance.", ""); /* Save log message */
			

			$batch_data = array_chunk($invoice_result, 15);
			
			foreach ($batch_data as $key => $batch) {
				$data_line['params']['Document_No'] = implode($batch, "|");
				print_r( "DOWNLOADED: " . $data_line['params']['Document_No']);

				print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . "\n");
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE . "] Started " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " maintenance.", ""); /* Save log message */
				$this->runPostedDirectSalesInvoiceLine($data_line, $trigger);
				Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE . "] Finished " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " maintenance.", ""); /* Save log message */
			}
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
    /** ------------------ Batch Download ~ End ---------------------- */

    /** ------------------ Batch Upload ~ Start ---------------------- */

    /**
     * (17/08/22)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runSalesOrderMultiple($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_SALES_ORDER_MULTIPLE, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE) . " transaction.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Started " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER)  . " maintenance.", ""); /* Save log message */
            $this->runSalesOrder($data, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Finished " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER)  . " maintenance.", ""); /* Save log message */


            print_r("[" . date("Y-m-d H:i:s") . "] Syncing Booking posts and lots\n");
			$this->runSalesOrderDockExitMultiBooking($data, $trigger);
			
            $reservation_name = (new ReservationEntryData)->getModuleByType(0);
            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower($reservation_name) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Started " . $reservation_name  . " maintenance.", ""); /* Save log message */
            $this->runSalesOrderReservationEntry($data, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Finished " . $reservation_name  . " maintenance.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Started " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST)   . " maintenance.", ""); /* Save log message */
            $this->runSalesOrderPost($data, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Finished " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST)   . " maintenance.", ""); /* Save log message */

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
	    public function runSalesOrderDockExit($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_SALES_ORDER_MULTIPLE, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE) . " transaction.", ""); /* Save log message */
			
			$data_l =  [
				'company' => isset($data['company']) ? $data['company'] : "BII Live",
				'sales_office_no' => $sales_office_no,
				'params' => [
					'Order_No' => isset($data['sales_order_code']) ? $data['sales_order_code'] : "",
				]
			];
		
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " LOTS AND POST \n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Started " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER)  . " maintenance.", ""); /* Save log message */
            $this->runSalesOrderAssignLotsAndPost($data, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Finished " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER)  . " maintenance.", ""); /* Save log message */

			print_r("[" . date("Y-m-d H:i:s") . "] Downloading " . strtolower(DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" .DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE. "] Started Downloading ", ""); /* Save log message */
			$this->runSalesInvoiceMultiple($data_l);
			print_r("[" . date("Y-m-d H:i:s") . "] Finished Downloading " . strtolower(DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" .DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE. "] Finished Downloading ", ""); /* Save log message */
           
		} catch (\Exception $exc) {
            throw $exc;
        }
	}
	
	
	public function runSalesOrderDockExitMultiBooking($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
		$date_from =  date('Y-m-d');
		$date_to =  date('Y-m-d');

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_SALES_ORDER_MULTIPLE, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE) . " transaction.", ""); /* Save log message */
			$get_data = SalesOrder::leftJoin('invoice', 'temp_sales_order.code', '=', 'invoice.sales_order_code')
			->leftJoin('invoice_details', 'invoice.id', '=', 'invoice_details.inv_id')
			->leftJoin('location', 'temp_sales_order.location_id', '=', 'location.id')
            ->where('temp_sales_order.transaction_type', '=', 0)
            ->where('temp_sales_order.msd_synced', '=', 1)
            ->where('location.sales_office_no', '=', $sales_office_no)
			->select('temp_sales_order.code')
			->whereBetween('sales_order_date', [$date_from, $date_to])
			->where(function ($query) {
				$query->whereNull('invoice.id')
			->orwhereNull('invoice_details.id');
			})
            ->get();
			foreach($get_data as $code) {
				if(isset($code['code'])) {
					$data_l =  [
						'company' => isset($data['company']) ? $data['company'] : "BII Live",
						'sales_office_no' => $sales_office_no,
						'sales_order_code' =>  $code['code'],
						'params' => [
							'Order_No' => $code['code'],
						]
					];
					print_r("[" . date("Y-m-d H:i:s") . "] Syncing LOTS AND POST for ". $code['code'] ." \n");
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Started " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER)  . " maintenance.", ""); /* Save log message */
					$this->runSalesOrderAssignLotsAndPost($data_l, $trigger);
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Finished " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER)  . " maintenance.", ""); /* Save log message */

					print_r("[" . date("Y-m-d H:i:s") . "] Downloading " . strtolower(DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE) . " for ". $code['code'] ."\n");
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" .DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE. "] Started Downloading ", ""); /* Save log message */
					$this->runSalesInvoiceMultiple($data_l);
					Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" .DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE. "] Finished Downloading ", ""); /* Save log message */
				   
				}
			}
			
		} catch (\Exception $exc) {
            throw $exc;
        }
	}
	
    /**
     * (26/08/22)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runSalesCreditMemoMultiple($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_CREDIT_MEMO_MULTIPLE, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_CREDIT_MEMO_MULTIPLE) . " transaction.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CREDIT_MEMO_MULTIPLE . "] Started " . strtolower(SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO)   . " maintenance.", ""); /* Save log message */
            $this->runSalesCreditMemo($data, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CREDIT_MEMO_MULTIPLE . "] Finished " . strtolower(SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO)   . " maintenance.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(UploadConnector::MODULE_SALES_CREDIT_MEMO_POST) . "\n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CREDIT_MEMO_MULTIPLE . "] Started " .  strtolower(UploadConnector::MODULE_SALES_CREDIT_MEMO_POST) . " maintenance.", ""); /* Save log message */
            $this->runSalesCreditMemoPost($data, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_CREDIT_MEMO_MULTIPLE . "] Finished " .  strtolower(UploadConnector::MODULE_SALES_CREDIT_MEMO_POST)  . " maintenance.", ""); /* Save log message */

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_NAME_CREDIT_MEMO_MULTIPLE) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * (26/08/22)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runCashReceiptMultiple($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_CASH_RECEIPT_MULTIPLE, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_CASH_RECEIPT_MULTIPLE) . " transaction.", ""); /* Save log message */

            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(CashReceiptData::MODULE_NAME_CASH_RECEIPT) . "\n");
            $this->runCashReceiptJournal($data);
            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(UploadConnector::MODULE_CASH_RECEIPT_JOURNAL) . "\n");
            $this->runCashReceiptJournalPost($data);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_NAME_CASH_RECEIPT_MULTIPLE) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
    /**
     * (22/08/23)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runInvoicePickNoteDownload($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

		
        $data_inv =  [
            'company' => $company,
            'sales_office_no' => $sales_office_no,
            'params' => array(
                'Order_No' => $data["params"]["Order_No"],
                'Shortcut_Dimension_1_Code' => $data["params"]["Shortcut_Dimension_1_Code"], 
            ),
        ];
		
        $data_pn =  [
            'company' => $company,
            'sales_office_no' => $sales_office_no,
            'Sales_Order_Code' => $data["params"]["Order_No"],
            'params' => array(
                'No' => $data["params"]["Pick_No"],
            ),
        ];
		
        try {
            print_r("[" . date("Y-m-d H:i:s") . "] Syncing Invoice \n");
            $this->runPostedSalesInvoiceBooking($data_inv); 
            print_r("[" . date("Y-m-d H:i:s") . "] Syncing Invoice Line\n");
            $this->runPostedSalesInvoiceLineBooking($data_inv); 
            print_r("[" . date("Y-m-d H:i:s") . "] Syncing Pick Note\n");
            $this->runPostedPickNote($data_pn); // Balance Empties
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    /**
     * (22/08/23)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runBatchBalanceDownload($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        $data_ba =  [
            'company' => $company,
            'sales_office_no' => $sales_office_no,
            'added_by' => $added_by,
			'date_from' => isset($data['params']['date_from']) ? $data['params']['date_from'] : "",
			'date_to' => isset($data['params']['date_to']) ? $data['params']['date_to'] : "",
            'params' => [
                'Salesperson_Code' =>  isset($data['params']['salesman']) ? $data['params']['salesman'] : "",
            ]
		
        ];

        $data_bae =  [
            'company' => $company,
            'sales_office_no' => $sales_office_no,
            'added_by' => $added_by,
			'date_from' => isset($data['params']['date_from']) ? $data['params']['date_from'] : "",
			'date_to' => isset($data['params']['date_to']) ? $data['params']['date_to'] : "",
            'params' => [
                'Balance_Empties' => ">0",
				'Shortcut_Dimension_1_Code' =>  isset($data['params']['so_code']) ? $data['params']['so_code'] : "",
            ]
		
        ];

        try {
            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(BalanceData::MODULE_BALANCE) . "\n");
			$this->runCustomerBalance($data_ba); // Balance
            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(BalanceEmptiesData::MODULE_BALANCE_EMPTIES) . "\n");
            $this->runCustomerBalanceEmpties($data_bae); // Balance Empties
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
    /**
     * (17/08/22)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runBatchTransaction($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        $data_so =  [
            'company' => $company,
            'sales_office_no' => $sales_office_no,
            'added_by' => $added_by,
            'params' => [
                'date_from' => isset($data['params']['date_from']) ? $data['params']['date_from'] : "",
                'date_to' => isset($data['params']['date_to']) ? $data['params']['date_to'] : "",
                'salesman_code' => isset($data['params']['salesman_code']) ? $data['params']['salesman_code'] : "",
                'location_code' => isset($data['params']['location_code']) ? $data['params']['location_code'] : "",
                'sales_order_code' => isset($data['params']['sales_order_code']) ? $data['params']['sales_order_code'] : "",
            ]
        ];
        $data_si = [
            'company' => $company,
            'sales_office_no' => $sales_office_no,
            'added_by' => $added_by,
            'date_from' => isset($data['params']['date_from']) ? $data['params']['date_from'] : "",
            'date_to' => isset($data['params']['date_to']) ? $data['params']['date_to'] : "",
            'params' => [
                'No' => isset($data['params']['No']) ? $data['params']['No'] : "",
                'Document_Date' => date('Y-m-d')
            ]
        ];
        $data_cr =  [
            'company' => $company,
            'sales_office_no' => $sales_office_no,
            'added_by' => $added_by,
            'date_from' => isset($data['params']['date_from']) ? $data['params']['date_from'] : "",
            'date_to' => isset($data['params']['date_to']) ? $data['params']['date_to'] : "",
            'params' => [
                'sales_order_code' => isset($data['params']['sales_order_code']) ? $data['params']['sales_order_code'] : "",
                'invoice_code' => isset($data['params']['invoice_code']) ? $data['params']['invoice_code'] : "",
            ]
        ];
        $data_cm =  [
            'company' => $company,
            'sales_office_no' => $sales_office_no,
            'added_by' => $added_by,
            'date_from' => isset($data['params']['date_from']) ? $data['params']['date_from'] : "",
            'date_to' => isset($data['params']['date_to']) ? $data['params']['date_to'] : "",
            'params' => [
                'sales_order_code' => isset($data['params']['sales_order_code']) ? $data['params']['sales_order_code'] : "",
                'invoice_code' => isset($data['params']['invoice_code']) ? $data['params']['invoice_code'] : "",
            ]
        ];

        try {
            //$this->runSalesOrderMultiple($data_so);
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing Invoice\n");
            $this->runSalesInvoiceMultiple($data_si);
            //print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(CashReceiptData::MODULE_NAME_CASH_RECEIPT) . "\n");
            //$this->runCashReceiptJournal($data_cr); // Separate MSD Sync Cash Receipt from Cash Receipt Post
            //$this->runSalesCreditMemoMultiple($data_cm);
            //print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(UploadConnector::MODULE_CASH_RECEIPT_JOURNAL) . "\n");
           // $this->runCashReceiptJournalPost($data_cr); // Cash Receipt Post should be last to post all the transaction by salesman code
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
	
    /**
     * (17/08/22)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    // public function runMultiplePickNote($data = null, $trigger = null)
    // {
        // $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        // $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : "";
		// $company = isset($data['company']) ? $data['company'] : "";
		// $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        // $added_by = isset($data['added_by']) ? $data['added_by'] : DownloadConnector::MSD_LOGGER_NAME;
        // $data_so =  [
            // 'company' => $company,
            // 'sales_office_no' => $sales_office_no,
            // 'added_by' => $added_by,
            // 'params' => [
                // 'sales_order_code' => isset($data['params']['sales_order_code']) ? $data['params']['sales_order_code'] : "",
            // ]
        // ];
		
        // $data_pi = [
            // 'company' => $company,
            // 'sales_office_no' => $sales_office_no,
            // 'added_by' => $added_by,
            // 'sales_order_code' => isset($data['params']['sales_order_code']) ? $data['params']['sales_order_code'] : "",
        // ];
		
		// $trigger = Utils::saveTrigger($sales_office_no, "Sync Pick Note", UploadConnector::STATUS_PENDING, $added_by);
		// $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
		
		// print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . "\n");
		// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Sync Pick Note] Started " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST)   . " maintenance.", ""); /* Save log message */
		// $this->runSalesOrderPost($data, $trigger);
		// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Sync Pick Note] Finished " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST)   . " maintenance.", ""); /* Save log message */

		// print_r("[" . date("Y-m-d H:i:s") . "] Syncing picknote collection\n");
		// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Sync Pick Note] Started sending pick note collection.", ""); /* Save log message */
		// $this->runPickNoteCollection($data, $trigger);
		// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Sync Pick Note] Finished sending pick note collection.", ""); /* Save log message */
		
		// print_r("[" . date("Y-m-d H:i:s") . "] Syncing picknote inventory\n");
		// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Sync Pick Note] Started sending pick note inventory", ""); /* Save log message */
		// $this->runPickNoteInventory($data, $trigger);
		// Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Sync Pick Note] Finished sending pick note inventory", ""); /* Save log message */

    // }




    /**
     * (12/02/24)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runMultiplePickNote($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $sales_order_code = isset($data['sales_order_code']) ? $data['sales_order_code'] : "";
		$company = isset($data['company']) ? $data['company'] : "BII Live";
		$sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "750300";
        $added_by = isset($data['added_by']) ? $data['added_by'] : DownloadConnector::MSD_LOGGER_NAME;
		
		if(isset($data['sales_order_code']) && !is_array($data['sales_order_code']))
			$data['sales_order_code'] = [$data['sales_order_code']];
        else if(!isset($data['sales_order_code']) ){
			print_r(json_encode($data));
			return;
		}		
		
		foreach($data['sales_order_code'] as $sales_code) {
			$data_so =  [
				'company' => $company,
				'sales_office_no' => $sales_office_no,
				'added_by' => $added_by,
				'sales_order_code' => $sales_code,
			];
			print_r(json_encode($data_so, JSON_PRETTY_PRINT));
			
			$trigger = Utils::saveTrigger($sales_office_no, "PickNote Inv and Col", UploadConnector::STATUS_PENDING, $added_by);
			$trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
			
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing picknote collection\n");
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Sync Pick Note] ". $sales_code . " : Started sending pick note collection.", ""); /* Save log message */
			$this->runPickNoteCollection($data_so, $trigger);
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Sync Pick Note] ". $sales_code . " : Finished sending pick note collection.", ""); /* Save log message */
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing picknote inventory\n");
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Sync Pick Note] ". $sales_code . " : Started sending pick note inventory", ""); /* Save log message */
			$this->runPickNoteInventory($data_so, $trigger);
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[Sync Pick Note] ". $sales_code . " : Finished sending pick note inventory", ""); /* Save log message */
		}

    }
    /** ------------------- Batch Upload ~ End ----------------------- */

    /** ----------------- Batch Sequence ~ Start --------------------- */

    /**
     * (20/09/22)
     * 
     * @param data - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function runWithdrawStockSalesOrder($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        $data =  [
            'company' => $company,
            'sales_office_no' => $sales_office_no,
            'added_by' => $added_by,
            'params' => [
                'date_from' => isset($data['params']['date_from']) ? $data['params']['date_from'] : "",
                'date_to' => isset($data['params']['date_to']) ? $data['params']['date_to'] : "",
            ]
        ];

        try {
            $withdrawal_name = (new OutgoingNotificationData)->getModuleByType(0);
            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower($withdrawal_name) . "\n");
            $this->runWithdrawalRequest($data);
            print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE) . "\n");
            $this->runStocksMultiple($data);
            // print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE) . "\n");
            //$this->runSalesOrderMultiple($data);
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	 
    /**
     * Version of runSalesOrder that is run in the console.
     * (01/07/2024)
     * 
     * @return void
     */
    public function runSalesOrderQueue($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-order']['list'];
        $company = isset($data['company']) && $data['company'] != "" ? $data['company'] : "BII Live";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);
		$json = isset($data['json']) ? $data['json'] : "";

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, SalesOrderData::MODULE_SALES_ORDER, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrderQueue($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
    /**
     * Version of runReservationEntryQueue that is run in the console.
     * (01/16/2024)
     * 
     * @return void
     */
    public function runReservationEntryQueue($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['reservation-entry']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $from_batch = ($trigger != null);
		$json = isset($data['json']) ? $data['json'] : "";

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no, (new ReservationEntryData)->getModuleByType(0), UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower((new ReservationEntryData)->getModuleByType(0)) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrderReservationEntryQueue($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower((new ReservationEntryData)->getModuleByType(0)) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
                Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	
    /**
     * Version of runSalesOrderPostQueue that is run in the console.
     * (01/16/2024)
     * 
     * @return void
     */
    public function runSalesOrderPostQueue($data = null, $trigger = null)
    {
        $data = ($data === null) ? json_decode($this->argument('data'), true) : $data;
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-order-post']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company, "CodeUnit");
        $from_batch = ($trigger != null);
		$json = isset($data['json']) ? $data['json'] : "";

        try {
            $trigger = ($trigger != null) ? $trigger : Utils::saveTrigger($sales_office_no,  strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST), UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            if (!$from_batch) Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrderPostQueue($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            if (!$from_batch) {
                Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . " transaction.", ""); /* Save log message */
                Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
					Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */
            }
            return;
        } catch (\Exception $exc) {
            throw $exc;
        }
    }
	 
	 
	 /** 
     * Run Distribution Channel, Customer then Product sync in one request
     * (01/16/2022)
     * 
     * @param 
     * 
     * @return void
     * */
    public function runSalesOrderBatch()
    {
        $data = json_decode($this->argument('data'), true);
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
		
		$trigger = Utils::saveTrigger($sales_office_no, "Sales Order Queue", DownloadConnector::STATUS_PENDING, $added_by);
		$trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
		Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started sending " . strtolower("Sales Order Queue") . " sales order.", ""); /* Save log message */
	
	
		print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesOrderData::MODULE_SALES_ORDER) . "\n");
        $this->runSalesOrderQueue($data, $trigger);
	
		
		if($data['transaction_type'] != 0 ) {
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower((new ReservationEntryData)->getModuleByType(0)) . "\n");
			$this->runReservationEntryQueue($data, $trigger);
			
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . "\n");
			$this->runSalesOrderPostQueue($data, $trigger);
			
			//$invoices = Invoice::where()
		}
		else {
			
			$data_l =  [
				'company' => isset($data['company']) ? $data['company'] : "BII Live",
				'sales_office_no' => $sales_office_no,
				'sales_order_code' =>  $data['code'],
				'params' => [
					'Order_No' => $data['code'],
				]
			];
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " LOTS AND POST \n");
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Started " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER)  . " maintenance.", ""); /* Save log message */
            $this->runSalesOrderAssignLotsAndPost($data_l, $trigger);
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "[" . UploadConnector::MODULE_NAME_SALES_ORDER_MULTIPLE . "] Finished " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER)  . " maintenance.", ""); /* Save log message */

		}
    }


	 /** 
		 * Run Distribution Channel, Customer then Product sync in one request
		 * (01/16/2022)
		 * 
		 * @param 
		 * 
		 * @return void
		 * */
		public function runCreditMemoBatch()
		{
			$data = json_decode($this->argument('data'), true);
			$sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
			$added_by = isset($data['added_by']) ? $data['added_by'] : "";
			
			$trigger = Utils::saveTrigger($sales_office_no, "Credit Memo Queue", DownloadConnector::STATUS_PENDING, $added_by);
			$trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
			Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started sending " . strtolower("Credit Memo Queue") . ".", ""); /* Save log message */
		
		
			print_r("[" . date("Y-m-d H:i:s") . "] Syncing Credit Memo \n");
			$this->runSalesOrderQueue($data, $trigger);
		}



    /** ------------------ Batch Sequence ~ End ---------------------- */
}
