<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
/* Jobs */
use App\Jobs\APIJobHandler;
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
use App\Data\CustomerPriceGroupData as CustomerPriceGroupData;
use App\Data\CustomerDiscountGroupData as CustomerDiscountGroupData;
use App\Data\CustomerPostingGroupData as CustomerPostingGroupData;
use App\Data\DistributionChannelData as DistributionChannelData;
use App\Data\GenBusPostingGroupData as GenBusPostingGroupData;
use App\Data\InventoryData as InventoryData;
use App\Data\InvoiceData as InvoiceData;
use App\Data\InvoiceDetailData;
use App\Data\LocationData as LocationData;
use App\Data\NewCustomerRequestData;
use App\Data\OutgoingInventoryData as OutgoingInventoryData;
use App\Data\OutgoingInventoryDetailData as OutgoingInventoryDetailData;
use App\Data\OutgoingNotificationData as OutgoingNotificationData;
use App\Data\OutgoingNotificationDetailData as OutgoingNotificationDetailData;
use App\Data\PaymentMethodData as PaymentMethodData;
use App\Data\PaymentTermsData as PaymentTermsData;
use App\Data\PromotionBudgetData as PromotionBudgetData;
use App\Data\PromotionData as PromotionData;
use App\Data\PromotionDetailData as PromotionDetailData;
use App\Data\PromotionDiscountLineData as PromotionDiscountLineData;
use App\Data\PromotionLocationData as PromotionLocationData;
use App\Data\ReservationEntryData;
use App\Data\SalesCreditMemoData;
use App\Data\SalesmanData as SalesmanData;
use App\Data\SalesmanTypeData as SalesmanTypeData;
use App\Data\SalesOfficeData as SalesOfficeData;
use App\Data\SalesShipmentData as SalesShipmentData;
use App\Data\SalesOrderData;
use App\Data\SalesPriceData as SalesPriceData;
use App\Data\SKUData as SKUData;
use App\Data\SubChannelData as SubChannelData;
use App\Data\VATBusPostingGroupData as VATBusPostingGroupData;
use App\Data\ZoneData as ZoneData;

/*model*/

use App\Model\noc\SalesOffice as SalesOffice;
use App\Model\noc\Zone as Zone;

class APIController extends Controller
{
    /** ----------------------- HTTP REQUEST ------------------------- */
    /** --------------------- Download ~ Start ----------------------- */

    /**
     * Retrieve Location info (location) from RESTful API data and sends it to the NOC.
     * (04/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveCustomer(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger($sales_office_no, LocationData::MODULE_LOCATION, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(LocationData::MODULE_NAME_LOCATION) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomer($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(LocationData::MODULE_NAME_LOCATION) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }
	
	 public function retrievePickNote(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['posted-pick-note']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger($sales_office_no, "PICK NOTE", DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve pick note maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPostedPickNote($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved pick note maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Product info (sku) from RESTful API data and sends it to the NOC.
     * (04/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function retrieveProduct(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['product']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger($sales_office_no, SKUData::MODULE_SKU, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(SKUData::MODULE_NAME_SKU) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDProduct($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(SKUData::MODULE_NAME_SKU) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Promotion Header (promotion) info from RESTful API data and sends it to the NOC.
     * (04/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function retrievePromotion(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, PromotionData::MODULE_PROMOTION, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionData::MODULE_NAME_PROMOTION) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionNew($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionData::MODULE_NAME_PROMOTION) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    
    /**
     * Retrieve Promotion Header (promotion) info from RESTful API data and sends it to the NOC.
     * (04/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function retrievePromotionDeal(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, PromotionData::MODULE_PROMOTION, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionData::MODULE_NAME_PROMOTION) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionDeals($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionData::MODULE_NAME_PROMOTION) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Promotion Line (promotion_detail) info from RESTful API data and sends it to the NOC.
     * (05/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function retrievePromotionLine(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-line']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, PromotionDetailData::MODULE_PROMOTION_DETAIL, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionDetailData::MODULE_NAME_PROMOTION_DETAIL) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionLine($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionDetailData::MODULE_NAME_PROMOTION_DETAIL) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Promotion Location (promotion_location) info from RESTful API data and sends it to the NOC.
     * (04/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function retrievePromotionCustomer(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-customer']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, PromotionLocationData::MODULE_PROMOTION_LOCATION, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionLocationData::MODULE_NAME_PROMOTION_LOCATION) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionCustomer($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionLocationData::MODULE_NAME_PROMOTION_LOCATION) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Promotion Discount (promotion_discount) info from RESTful API data and sends it to the NOC.
     * (05/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function retrievePromotionDiscountLine(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-discount-line']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, PromotionDiscountLineData::MODULE_PROMOTION_DISCOUNT, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionDiscountLineData::MODULE_NAME_PROMOTION_DISCOUNT) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionDiscountLine($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionDiscountLineData::MODULE_NAME_PROMOTION_DISCOUNT) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Promotion Budget info (promotion_budget) from RESTful API data and sends it to the NOC.
     * (05/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function retrievePromotionBudget(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['promotion-budget']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, PromotionBudgetData::MODULE_PROMOTION_BUDGET, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PromotionBudgetData::MODULE_NAME_PROMOTION_BUDGET) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPromotionBudget($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PromotionBudgetData::MODULE_NAME_PROMOTION_BUDGET) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Sales Price (discount_qualifying) info from RESTful API data and sends it to the NOC.
     * (05/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function retrieveSalesPrice(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-price']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, SalesPriceData::MODULE_SALES_PRICE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(SalesPriceData::MODULE_NAME_SALES_PRICE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDSalesPrice($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(SalesPriceData::MODULE_NAME_SALES_PRICE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Location Discount info (location_discount) from RESTful API data and sends it to the NOC.
     * (04/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveCustomerDiscount(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['winspire']['route']['customer-discount']['list'];
        $url = Globals::restClientABIMSDynamicsWinspireAPI($route);
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger("", LocationData::MODULE_LOCATION_DISCOUNT, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(LocationData::MODULE_NAME_LOCATION_DISCOUNT) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncCustomerDiscount($method, $url, false, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(LocationData::MODULE_NAME_LOCATION_DISCOUNT) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Customer Discount (outgoing_inventory) info from RESTful API data and sends it to the NOC.
     * (05/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function retrieveStockInSalesmanWarehouse(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['stock-in-salesman-warehouse']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
		

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, InventoryData::MODULE_INVENTORY, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(InventoryData::MODULE_NAME_INVENTORY) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDStockInSalesmanWarehouse($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(InventoryData::MODULE_NAME_INVENTORY) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Pending Customer Creation info (location) from RESTful API data and sends it to the NOC.
     * (05/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function retrievePendingCustomerCreationRequest(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi-msd']['route']['pending-customer-creation-request']['list'];
        $url = Globals::restClientABIMSDynamicsWinspireAPI($route);
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger("", DownloadConnector::MODULE_PENDING_LOCATION_CREATION, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(DownloadConnector::MODULE_NAME_PENDING_LOCATION_CREATION) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncPendingCustomerCreationRequest($method, $url, false, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(DownloadConnector::MODULE_NAME_PENDING_LOCATION_CREATION) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Customer Price Group info (customer_price_group) from MSD API data and sends it to the NOC.
     * (03/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveCustomerPriceGroup(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-price-group']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger("", CustomerPriceGroupData::MODULE_CUSTOMER_PRICE_GROUP, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(CustomerPriceGroupData::MODULE_NAME_CUSTOMER_PRICE_GROUP) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomerPriceGroup($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(CustomerPriceGroupData::MODULE_NAME_CUSTOMER_PRICE_GROUP) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Customer Posting Group info (customer_posting_group) from MSD API data and sends it to the NOC.
     * (03/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveCustomerPostingGroup(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-posting-group']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger("", CustomerPostingGroupData::MODULE_CUSTOMER_POSTING_GROUP, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(CustomerPostingGroupData::MODULE_NAME_CUSTOMER_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomerPostingGroup($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(CustomerPostingGroupData::MODULE_NAME_CUSTOMER_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Customer Discount Group info (customer_discount_group) from MSD API and sends it to the NOC.
     * (06/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveCustomerDiscountGroup(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-discount-group']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger("", CustomerDiscountGroupData::MODULE_CUSTOMER_DISCOUNT_GROUP, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(CustomerDiscountGroupData::MODULE_NAME_CUSTOMER_DISCOUNT_GROUP) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomerDiscountGroup($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(CustomerDiscountGroupData::MODULE_NAME_CUSTOMER_DISCOUNT_GROUP) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve General Business Posting Group info (gen_bus_posting_group) from MSD API data and sends it to the NOC.
     * (03/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveGenBusPostingGroup(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['gen-bus-posting-group']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger("", GenBusPostingGroupData::MODULE_GEN_BUS_POSTING_GROUP, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(GenBusPostingGroupData::MODULE_NAME_GEN_BUS_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDGenBusPostingGroup($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(GenBusPostingGroupData::MODULE_NAME_GEN_BUS_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve General Business Posting Group info (VAT_BUS_posting_group) from MSD API data and sends it to the NOC.
     * (03/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveVATBusPostingGroup(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['vat-bus-posting-group']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger("", VATBusPostingGroupData::MODULE_VAT_BUS_POSTING_GROUP, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(VATBusPostingGroupData::MODULE_NAME_VAT_BUS_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDVATBusPostingGroup($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(VATBusPostingGroupData::MODULE_NAME_VAT_BUS_POSTING_GROUP) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Distribution Channel info (distribution_channel) from MSD API data and sends it to the NOC.
     * (09/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveDistributionChannel(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['distribution-channel']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger($sales_office_no, DistributionChannelData::MODULE_DISTRIBUTION_CHANNEL, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDDistributionChannel($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Sub Channel info (store_type) from MSD API data and sends it to the NOC.
     * (09/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveSubChannel(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['sub-channel']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger($sales_office_no, DistributionChannelData::MODULE_DISTRIBUTION_CHANNEL, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(SubChannelData::MODULE_NAME_SUB_CHANNEL) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDSubChannel($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(SubChannelData::MODULE_NAME_SUB_CHANNEL) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve General Business Posting Group info (PAYMENT_METHOD) from MSD API data and sends it to the NOC.
     * (03/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrievePaymentMethod(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['payment-method']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger("", PaymentMethodData::MODULE_PAYMENT_METHOD, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PaymentMethodData::MODULE_NAME_PAYMENT_METHOD) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPaymentMethod($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PaymentMethodData::MODULE_NAME_PAYMENT_METHOD) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve General Business Posting Group info (PAYMENT_TERMS) from MSD API data and sends it to the NOC.
     * (03/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrievePaymentTerms(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['payment-terms']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger("", PaymentTermsData::MODULE_PAYMENT_TERMS, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(PaymentTermsData::MODULE_NAME_PAYMENT_TERMS) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPaymentTerms($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, "", date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(PaymentTermsData::MODULE_NAME_PAYMENT_TERMS) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve General Business Posting Group info (Salesman) from MSD API data and sends it to the NOC.
     * (03/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveSalesman(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['salesman']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger($sales_office_no, SalesmanData::MODULE_SALESMAN, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(SalesmanData::MODULE_NAME_SALESMAN) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDSalesman($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(SalesmanData::MODULE_NAME_SALESMAN) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }


    /**
     * Retrieve General Business Posting Group info (SalesmanType) from MSD API data and sends it to the NOC.
     * (03/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveSalesmanType(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['salesman-type']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger($sales_office_no, SalesmanTypeData::MODULE_SALESMAN_TYPE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDSalesmanType($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }


    /**
     * Retrieve Location info (Zone) from MSD API data and sends it to the NOC.
     * (14/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveLocation(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['location']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            $trigger = Utils::saveTrigger($sales_office_no, ZoneData::MODULE_ZONE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(ZoneData::MODULE_NAME_ZONE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDZone($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(ZoneData::MODULE_NAME_ZONE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Posted Sales Invoice Header (invoice) from MSD API data and sends it to the NOC.
     * (04/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrievePostedSalesInvoice(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['posted-invoice']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            $trigger = Utils::saveTrigger($sales_office_no, InvoiceData::MODULE_POSTED_INVOICE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPostedSalesInvoice($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(InvoiceData::MODULE_NAME_POSTED_INVOICE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Posted Sales Invoice Lines (invoice_details) from MSD API data and sends it to the NOC.
     * (04/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrievePostedSalesInvoiceLine(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['posted-invoice-line']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            $trigger = Utils::saveTrigger($sales_office_no, InvoiceDetailData::MODULE_POSTED_INVOICE_DETAIL, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDPostedSalesInvoiceLine($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Customer Balance (balance) from MSD API data and sends it to the NOC.
     * (15/11/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveCustomerBalance(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-balance']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            $trigger = Utils::saveTrigger($sales_office_no, BalanceData::MODULE_BALANCE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomerBalance($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Customer Balance Empties from MSD API data and sends it to the NOC.
     * (05/03/2023)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function retrieveCustomerBalanceEmpties(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "GET";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-balance-empties']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
		
        try {
            $trigger = Utils::saveTrigger($sales_office_no, BalanceData::MODULE_BALANCE, DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncMSDCustomerBalanceEmpties($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update tr
            igger end date */

            return [
                "success" => true,
                "message" => "Success",

            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
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
    public function retrievePickNoteCollection(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
		$loadboard_url = Globals::createLoadboardURL('retrieve-collection');
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            $trigger = Utils::saveTrigger($sales_office_no, "PICK NOTE COLLECTION", DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            //Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncLoadBoardPickNoteCollection($method, $loadboard_url, $data, $trigger_id); /* Call MSD REST API then process the response */
            //Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
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
    public function retrievePickNoteInventory(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
		
        $data = json_decode($request->getContent(), true);
        $method = "POST";
		$loadboard_url = Globals::createLoadboardURL('retrieve-inventory');
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";

        try {
            $trigger = Utils::saveTrigger($sales_office_no, "PICK NOTE INVENTORY", DownloadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            //Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Started retrieve " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            DownloadConnector::syncLoadBoardPickNoteInventory($method, $loadboard_url, $data, $trigger_id); /* Call MSD REST API then process the response */
            //Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), DownloadConnector::INFO, DownloadConnector::MSD_LOGGER_NAME, "Done retrieved " . strtolower(BalanceData::MODULE_NAME_BALANCE) . " maintenance.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, DownloadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }
    /** --------------------- Download ~ End ------------------------- */

    /** --------------------- Upload ~ Start ------------------------- */

    /**
     * Generate Withdrawawl Request info from NOC data and sends it to MSD.
     * (27/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateWithdrawalRequest(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['transfer-order']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, (new OutgoingNotificationData)->getModuleByType(0), UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower((new OutgoingNotificationData)->getModuleNameByType(0)) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDWithdrawalRequest($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower((new OutgoingNotificationData)->getModuleNameByType(0)) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Generate Sales Order info from NOC data and sends it to MSD.
     * (27/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateSalesOrder(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-order']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, SalesOrderData::MODULE_SALES_ORDER, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrder($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Generate Sales Order Reservation Entry info from NOC data and sends it to MSD.
     * (30/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateSalesOrderReservationEntry(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['reservation-entry']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, (new ReservationEntryData)->getModuleByType(0), UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower((new ReservationEntryData)->getModuleNameByType(0)) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrderReservationEntry($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower((new ReservationEntryData)->getModuleNameByType(0)) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Generate Cash Receipt Journal info from NOC data and sends it to MSD.
     * (05/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateCashReceiptJournal(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-journal']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, CashReceiptData::MODULE_CASH_RECEIPT, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(CashReceiptData::MODULE_NAME_CASH_RECEIPT) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDCashReceiptJournal($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(CashReceiptData::MODULE_NAME_CASH_RECEIPT) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Send a Sales Credit Memo Post request to MSD.
     * (24/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateCashReceiptJournalPost(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['cash-receipt-journal-post']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company, "Codeunit");

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_CASH_RECEIPT_POST, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_CASH_RECEIPT_POST) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDCashReceiptPost($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_NAME_CASH_RECEIPT_POST) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Generate Sales Credit Memo info from NOC data and sends it to MSD.
     * (06/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateSalesCreditMemo(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, SalesCreditMemoData::MODULE_SALES_CREDIT_MEMO, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesCreditMemo($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Send a Sales Credit Memo Post request to MSD.
     * (24/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateSalesCreditMemoPost(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-credit-memo-post']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company, "Codeunit");

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_SALES_CREDIT_MEMO_POST, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_SALES_CREDIT_MEMO_POST) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesCreditMemoPost($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_NAME_SALES_CREDIT_MEMO_POST) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve Sales Order Release info from NOC data and sends it REST API.
     * (01/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateSalesOrderPost(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['sales-order-post']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company, "CodeUnit");

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, SalesOrderData::MODULE_SALES_ORDER_POST, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDSalesOrderPost($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(SalesOrderData::MODULE_NAME_SALES_ORDER_POST) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Generate Return Request info from NOC data and sends it to MSD.
     * (07/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateReturnRequest(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['transfer-order']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, (new OutgoingNotificationData)->getModuleByType(1), UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower((new OutgoingNotificationData)->getModuleNameByType(1)) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDReturnRequest($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower((new OutgoingNotificationData)->getModuleNameByType(1)) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Generate Return Request Reservation Entry Salesman info from NOC data and sends it to MSD.
     * (07/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateReturnRequestReservationEntrySalesman(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
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

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Generate Return Request Reservation Entry Warehouse info from NOC data and sends it to MSD.
     * (07/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateReturnRequestReservationEntryWarehouse(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
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

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve New Customer Creation Request info from NOC data and sends it REST API.
     * (06/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateNewCustomerCreationRequest(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['new-customer-creation-request']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, UploadConnector::MODULE_NEW_LOCATION_CREATION, UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync " . strtolower(UploadConnector::MODULE_NAME_NEW_LOCATION_CREATION) . " transaction.", ""); /* Save log message */
            UploadConnector::syncMSDNewCustomerCreationRequest($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced " . strtolower(UploadConnector::MODULE_NAME_NEW_LOCATION_CREATION) . " transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve generate CAF Credit Limit from NOC data and sends it REST API.
     * (03/10/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateCafCreditLimit(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
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

            return [
                "success" => true,
                "message" => "Success",
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Retrieve generate CAF Credit Limit from NOC data and sends it REST API.
     * (03/10/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function generateLocationUpdate(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
        $method = "POST";
        $route = Params::values()['webservice']['abi_msd']['route']['customer-modify-request']['list'];
        $company = isset($data['company']) ? $data['company'] : "";
        $sales_office_no = isset($data['sales_office_no']) ? $data['sales_office_no'] : "";
        $added_by = isset($data['added_by']) ? $data['added_by'] : "";
        $url = Globals::soapABIMSDynamicsURL($route, $company);

        try {
            /* Insert trigger */
            $trigger = Utils::saveTrigger($sales_office_no, "Location Update", UploadConnector::STATUS_PENDING, $added_by);
            $trigger_id = isset($trigger['id']) ? $trigger['id'] : 0;

            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Started sync Location Update transaction.", ""); /* Save log message */
            $test = UploadConnector::syncMSDLocationUpdate($method, $url, $data, $trigger_id); /* Call MSD REST API then process the response */
            Utils::saveLog($trigger_id, $sales_office_no, date("Y-m-d H:i:s"), UploadConnector::INFO, UploadConnector::MSD_LOGGER_NAME, "Done synced Location Update transaction.", ""); /* Save log message */
            Utils::updateTriggerStatus($trigger_id, UploadConnector::STATUS_DONE); /* Update trigger status */
            Utils::updateTriggerEndDate($trigger_id, date("Y-m-d H:i:s")); /* Update trigger end date */

            return [
                "success" => true,
                "message" => $test,
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /** ---------------------- Upload ~ End -------------------------- */

    /** ---------------------- QUEUE REQUEST ------------------------- */
    /** --------------------- Download ~ Start ----------------------- */

    /**
     * Runs the job version of the retrieveCustomer method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveCustomer(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('customer', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => LocationData::MODULE_NAME_LOCATION . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveProduct method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveProduct(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('product', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => SKUData::MODULE_NAME_SKU . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrievePromotion method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePromotion(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('promotion', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => PromotionData::MODULE_NAME_PROMOTION . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrievePromotionLine method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePromotionLine(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('promotion-line', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => PromotionDetailData::MODULE_NAME_PROMOTION_DETAIL . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrievePromotionCustomer method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePromotionCustomer(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('promotion-customer', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => PromotionLocationData::MODULE_NAME_PROMOTION_LOCATION . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrievePromotionDiscountLine method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePromotionDiscountLine(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('promotion-discount-line', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => PromotionDiscountLineData::MODULE_NAME_PROMOTION_DISCOUNT . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrievePromotionBudget method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePromotionBudget(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('promotion-budget', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => PromotionBudgetData::MODULE_NAME_PROMOTION_BUDGET . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveSalesPrice method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveSalesPrice(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('sales-price', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => SalesPriceData::MODULE_NAME_SALES_PRICE . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveCustomerDiscount method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveCustomerDiscount(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('customer-discount', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => DownloadConnector::MODULE_NAME_LOCATION_DISCOUNT . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveStockInSalesmanWarehouse method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveStockInSalesmanWarehouse(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('stock-in-salesman-warehouse', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => InventoryData::MODULE_NAME_INVENTORY . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    
    /**
     * Retrieve generate Download Stock from NOC data and sends it to MSD, for external
     * programt to use
     * (02/24/2023)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function queueRetreieveStockInSalesmanExternal(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
		$params = [];
		$company = isset($data['company']) ? $data['company'] : "BII";
		
		
        if(isset($data['document_no']) ) {
            $params["Document_Type"] =  'Transfer_Receipt' ;
            $params["Document_No"] =  $data['document_no'] ;
        }


        if(isset($data['zone_code']) ) {
            $salesman_model = Zone::where('zone_name', '=', $data['zone_code'] )->get();
            if(count($salesman_model) <= 0 ){
                return [
                    "success" => false,
                    "message" => InventoryData::MODULE_NAME_INVENTORY . " failed to find zone " . $data['zone_code']
                ];
            }
            else 
				$params["Location_Code"] =  $data['zone_code'] ;
        }
        
        if(isset($data['sales_office_no']) ) {
            $sales_office_model = SalesOffice::where('short_desc', '=',  $data['sales_office_no'])
            ->orWhere('no','=',  $data['sales_office_no'])            
            ->first();
            
            if(count($sales_office_model) <= 0 ){
                return [
                    "success" => false,
                    "message" => InventoryData::MODULE_NAME_INVENTORY . " failed to find sales office " . $data['sales_office_no']
                ];
            }
            else {
                $data['sales_office_no'] =  $sales_office_model->no;
            }
        }
		
        $data_sw =  [
            'company' => $company,
            'sales_office_no' => isset($data['sales_office_no']) ? $data['sales_office_no'] : "",
            'withdrawal_code' => isset($data['withdrawal_code']) ? $data['withdrawal_code'] : "",
            'params' => $params
        ];


        if(isset($data['sales_office_no']) ) {
            $sales_office_model = SalesOffice::where('short_desc', '=',  $data['sales_office_no'])
            ->orWhere('no','=',  $data['sales_office_no'])            
            ->first();
            
            if(count($sales_office_model) <= 0 ){
                return [
                    "success" => false,
                    "message" => InventoryData::MODULE_NAME_INVENTORY . " failed to find sales office " . $data['sales_office_no']
                ];
            }
            else {
                $data['sales_office_no'] =  $sales_office_model->no;
            }
        }
        
        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('stock-in-salesman-warehouse', json_encode($data_sw)))->onQueue('api-queue-' . strtolower($company)) );
            return [
                "success" => true,
                "message" => InventoryData::MODULE_NAME_INVENTORY . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    
    /**
     * Retrieve generate Download Stock from NOC data and sends it to MSD, for external
     * programt to use
     * (02/24/2023)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function queueRetreieveInvoiceAndPickNoteExternal(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = json_decode($request->getContent(), true);
		$params = [];		
		
        // if(isset($data['sales_order_code']) ) {
            // $params["Sales_Order_Code"] =  $data['sales_order_code'] ;
        // }

        // if(isset($data['sales_office']) ) {
            // $sales_office_model = SalesOffice::where('short_desc', '=',  $data['sales_office'])
            // ->orWhere('no','=',  $data['sales_office'])            
            // ->first();
            
            // if(count($sales_office_model) <= 0 ){
                // return [
                    // "success" => false,
                    // "message" => "[Posted Pick Note] Failed to find sales office " . $data['sales_office_no']
                // ];
            // }
            // else {
                // $params['Sales_Office_No'] =  $sales_office_model->no;
                // $params['Short_Code'] =  $sales_office_model->short_desc;
            // }
        // }
		
        $data_inv =  [
            'company' => "Parallel",
            'sales_office_no' => isset($params['Sales_Office_No']) ? $params['Sales_Office_No'] : "",
            'params' => array(
                'Order_No' => isset($params["Sales_Order_Code"]) ? $params["Sales_Order_Code"] : "" ,
                'Shortcut_Dimension_1_Code' => isset($params["Short_Code"]) ? $params["Short_Code"] : "" , 
                'Pick_No' => isset($data["pick_note"]) ? $data["pick_note"] : "" 
            ),
        ];
		
        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('posted-pick-note-multiple', json_encode($data_inv)))->onQueue('api-queue-parallel'));
            return [
                "success" => true,
                "message" => "Successfully added request to queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }
    /**
     * Runs the job version of the retrievePendingCustomerCreationRequest method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePendingCustomerCreationRequest(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('pending-customer-creation-request', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => DownloadConnector::MODULE_NAME_PENDING_LOCATION_CREATION . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveCustomerPriceGroup method.
     * (03/06/2022) 
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveCustomerPriceGroup(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('customer-price-group', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => CustomerPriceGroupData::MODULE_NAME_CUSTOMER_PRICE_GROUP . " successfully executed retrieved maintenance through console app."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the queueRetrieveCustomerPostingGroup method.
     * (06/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveCustomerPostingGroup(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('customer-posting-group', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => CustomerPostingGroupData::MODULE_NAME_CUSTOMER_POSTING_GROUP . " successfully executed retrieved maintenance through console app."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
        }
    }

    /**
     * Runs the job version of the queueRetrieveCustomerDiscountGroup method.
     * (06/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveCustomerDiscountGroup(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('customer-discount-group', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => CustomerDiscountGroupData::MODULE_NAME_CUSTOMER_DISCOUNT_GROUP . " successfully executed retrieved maintenance through console app."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
        }
    }


    /**
     * Runs the job version of the retrieveGenBusPosting method.
     * (16/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveGenBusPostingGroup(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('gen-bus-posting-group', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => GenBusPostingGroupData::MODULE_NAME_GEN_BUS_POSTING_GROUP . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveVatBusPostingGroup method.
     * (16/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveVatBusPostingGroup(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('vat-bus-posting-group', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => VatBusPostingGroupData::MODULE_NAME_VAT_BUS_POSTING_GROUP . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveDistributionChannel method.
     * (16/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveDistributionChannel(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('channel', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => DistributionChannelData::MODULE_NAME_DISTRIBUTION_CHANNEL . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrievePaymentMethod method.
     * (16/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePaymentTerms(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('payment-terms', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => PaymentTermsData::MODULE_NAME_PAYMENT_TERMS . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrievePaymentMethod method.
     * (16/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePaymentMethod(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('payment-method', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => PaymentMethodData::MODULE_NAME_PAYMENT_METHOD . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveSalesman method.
     * (16/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveSalesman(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('salesman', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => SalesmanData::MODULE_NAME_SALESMAN . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveSalesmanType method.
     * (16/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveSalesmanType(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('salesman-type', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => SalesmanTypeData::MODULE_NAME_SALESMAN_TYPE . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveLocation method.
     * (16/06/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveLocation(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('location', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => ZoneData::MODULE_NAME_ZONE . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveLocation method.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePostedSalesInvoice(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('posted-sales-invoice', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => InvoiceData::MODULE_NAME_POSTED_INVOICE . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the retrieveLocation method.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePostedSalesInvoiceLine(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('posted-sales-invoice-line', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => InvoiceDetailData::MODULE_NAME_POSTED_INVOICE_DETAIL . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs a combined Channel and Customer request
     * (15/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveCustomerMultiple(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('customer-multiple', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => DownloadConnector::MODULE_NAME_LOCATION_MULTIPLE . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs a combined Channel and Customer request
     * (16/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveStocksMultiple(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('stocks-multiple', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => DownloadConnector::MODULE_NAME_INVENTORY_MULTIPLE . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }


    /**
     * Runs a combined Channel and Customer request
     * (16/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveSalesInvoiceMultiple(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "Parallel";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('invoice-multiple', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => DownloadConnector::MODULE_NAME_INVOICE_MULTIPLE . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs a combined Channel and Customer request
     * (16/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveCustomerBalance(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('customer-balance', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => BalanceData::MODULE_NAME_BALANCE . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs a combined Channel and Customer request
     * (11/04/2023)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveCustomerBalanceEmpties(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('customer-balance-empties', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => BalanceEmptiesData::MODULE_NAME_BALANCE_EMPTIES . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs a combined Pick note and inventory and collection
     * (11/04/2023)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrievePickNoteInventoryCollection(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "Parallel";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('picknote-inventory-collection', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Picknote upload successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }
	
    /**
     * Runs a combined Pick note and inventory and collection
     * (11/04/2023)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRetrieveTransferOrderDownloadRequest(Request $request)
    {
        $data = json_decode($request->getContent(), true);
		//$data = isset($data['data'])? $data['data'] :  [];
        $company = isset($data['company']) ? $data['company'] : "Parallel";
		if(empty($data)) {
			return [
                "success" => false,
                "message" => "Transfer Order Request data not valid"
            ];
		}
        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('transfer-order', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Transfer Order Request successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }
	
    /**
     * Runs cancelled location
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueSalesOrderCancelledLocation(Request $request)
    {
        $data = json_decode($request->getContent(), true);
		//$data = isset($data['data'])? $data['data'] :  [];
        $company = isset($data['company']) ? $data['company'] : "Parallel";
		if(empty($data)) {
			return [
                "success" => false,
                "message" => "Credit Memo Location Cancel Request data not valid"
            ];
		}
        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('credit-memo-location-cancel', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Credit Memo Location Cancel Request successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }
	
    /**
     * Endpoint for Approved Refund
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueSaveApproveRefund(Request $request)
    {
        $data = json_decode($request->getContent(), true);
		//$data = isset($data['data'])? $data['data'] :  [];
        $company = isset($data['company']) ? $data['company'] : "Parallel";
		if(empty($data)) {
			return [
                "success" => false,
                "message" => "Save Approve Refund Request Request data not valid"
            ];
		}
        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('save-approved-refund', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Save Approve Refund Request successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }


    /** --------------------- Download ~ End ------------------------- */

    /** --------------------- Upload ~ Start ------------------------- */

    /**
     * Runs the job version of the generateWithdrawalRequest method.
     * (01/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function queueGenerateWithdrawalRequest(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('withdrawal-request', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => (new OutgoingNotificationData)->getModuleNameByType(0) . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the queueGenerateSalesOrder method.
     * (01/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function queueGenerateSalesOrder(Request $request)
    {
        $data = json_decode($request->getContent(), true);
		if(!$data)
			$data = json_decode($request, true);
		
        $company = isset($data['company']) ? $data['company'] : "Parallel";

        try {
            /** Dispatches new Job */
            $test = dispatch((new APIJobHandler('sales-order', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => SalesOrderData::MODULE_NAME_SALES_ORDER . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }
	
	
    /**
     * Runs the job version of the queueGenerateSalesOrder method.
     * (01/2024)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function queueGenerateSalesOrderNew(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            $test = dispatch((new APIJobHandler('sales-order-queue', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => SalesOrderData::MODULE_NAME_SALES_ORDER . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }
	
    /**
     * Runs the job version of the queueGenerateSalesOrder method.
     * (01/2024)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function queueGenerateCreditMemoNew(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            $test = dispatch((new APIJobHandler('credit-memo-queue', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => SalesOrderData::MODULE_NAME_SALES_ORDER . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }


    /**
     * Runs the job version of the queueGenerateSalesOrderReservationEntry method.
     * (04/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions  
     */
    public function queueGenerateSalesOrderReservationEntry(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('sales-order-reservation-entry', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => ReservationEntryData::MODULE_NAME_RESERVATION_ENTRY . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the generateSalesOrderPost method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateSalesOrderPost(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('sales-order-post', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => UploadConnector::MODULE_NAME_SALES_ORDER_POST . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the generateCashReceiptJournal method.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateCashReceiptJournal(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('cash-receipt-journal', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => CashReceiptData::MODULE_NAME_CASH_RECEIPT . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the generateCashReceiptJournalPost method.
     * (30/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateCashReceiptJournalPost(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('cash-receipt-journal-post', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => UploadConnector::MODULE_NAME_CASH_RECEIPT_POST . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the generateSalesCreditMemo method.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateSalesCreditMemo(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('sales-credit-memo', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => SalesCreditMemoData::MODULE_NAME_SALES_CREDIT_MEMO . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the generateSalesCreditMemoPost method.
     * (25/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateSalesCreditMemoPost(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('sales-credit-memo-post', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => UploadConnector::MODULE_NAME_SALES_CREDIT_MEMO_POST . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the generateReturnRequest method.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateReturnRequest(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('return-request', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => (new OutgoingNotificationData)->getModuleNameByType(1) . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the generateReturnRequestReservationEntrySalesman method.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateReturnRequestReservationEntrySalesman(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('return-request-reservation-entry-salesman', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => (new ReservationEntryData)->getModuleNameByType(1) . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }


    /**
     * Runs the job version of the generateReturnRequestReservationEntryWarehouse method.
     * (08/07/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateReturnRequestReservationEntryWarehouse(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('return-request-reservation-entry-sales-office', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => (new ReservationEntryData)->getModuleNameByType(2) . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the generateNewCustomerCreationRequest method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateNewCustomerCreationRequest(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('new-customer-creation-request', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => UploadConnector::MODULE_NAME_NEW_LOCATION_CREATION . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the job version of the generateNewCustomerCreationRequest method.
     * (07/04/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateCafCreditLimit(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('caf-credit-limit', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => UploadConnector::MODULE_NAME_CAF_CREDIT_LIMIT . " successfully created queue to execute on background."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }
	
	public function queueSalesOrderAssignLotsAndPost(Request $request)
	{
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "Parallel";
		
		
		  try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('batch-sales-order-assign-lots-and-post', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Sales Order Assign Lots and Post upload successfully created queue to execute on background.",
				"data" => json_encode($data)
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

	public function queuePostedSalesShipment(Request $request)
	{
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "Parallel";
		  try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('sales-shipment', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Posted Sales Shipment upload successfully created queue to execute on background.",
				"data" => json_encode($data)
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

	public function queueSalesCreditMemoPostManual(Request $request)
	{
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "Parallel";
		  try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('credit-memo-post-manual', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Sales Credit Memo Post request upload successfully created queue to execute on background.",
				"data" => json_encode($data)
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

	public function queueCreditMemoUpdateLocation(Request $request)
	{
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "Parallel";
		  try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('credit-memo-update-location', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Sales Credit Memo Update Location request upload successfully created queue to execute on background.",
				"data" => json_encode($data)
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }
    /** ---------------------- Upload ~ End -------------------------- */

    /** ------------------ Batch Upload ~ Start ---------------------- */

    /**
     * Runs the console command generateSalesOrderMultiple
     * (17/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueGenerateSalesOrderMultiple(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('sales-order-multiple', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Sales Order successfully created queue to execute on background in sequence."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the console command runSalesCreditMemoMultiple
     * (26/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRunSalesCreditMemoMultiple(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('sales-credit-memo-multiple', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Sales Credit Memo successfully created queue to execute on background in sequence."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the console command runSalesCreditMemoMultiple
     * (26/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRunCashReceiptMultiple(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('cash-receipt-multiple', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Cash Receipt successfully created queue to execute on background in sequence."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /**
     * Runs the console command RunBatchTranslation
     * (17/08/2022)
     * 
     * @param request - body of the HTML request
     * 
     * @return - returns success message if able to complete without exceptions 
     * */
    public function queueRunBatchTransaction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $company = isset($data['company']) ? $data['company'] : "";

        try {
            /** Dispatches new Job */
            dispatch((new APIJobHandler('batch-transaction', $request->getContent()))->onQueue('api-queue-' . strtolower($company)));
            return [
                "success" => true,
                "message" => "Upload Transaction successfully created queue to execute on background in sequence."
            ];
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            throw $exc;
        }
    }

    /** ------------------- Batch Upload ~ End ----------------------- */
}
