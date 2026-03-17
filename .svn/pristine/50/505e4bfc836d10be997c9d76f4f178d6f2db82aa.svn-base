<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/* Download */
Route::post('download/customer-discount-group', 'APIController@retrieveCustomerDiscountGroup');
Route::post('download/customer-price-group', 'APIController@retrieveCustomerPriceGroup');
Route::post('download/customer-posting-group', 'APIController@retrieveCustomerPostingGroup');
Route::post('download/gen-bus-posting-group', 'APIController@retrieveGenBusPostingGroup');
Route::post('download/vat-bus-posting-group', 'APIController@retrieveVATBusPostingGroup');
Route::post('download/payment-method', 'APIController@retrievePaymentMethod');
Route::post('download/payment-terms', 'APIController@retrievePaymentTerms');
Route::post('download/channel', 'APIController@retrieveDistributionChannel');
Route::post('download/location', 'APIController@retrieveLocation');
Route::post('download/salesman-type', 'APIController@retrieveSalesmanType');
Route::post('download/salesman', 'APIController@retrieveSalesman');
Route::post('download/stock-in-salesman-warehouse', 'APIController@retrieveStockInSalesmanWarehouse');
Route::post('download/customer', 'APIController@retrieveCustomer');
Route::post('download/product', 'APIController@retrieveProduct');
Route::post('download/sales-price', 'APIController@retrieveSalesPrice');
Route::post('download/promotion', 'APIController@retrievePromotion');
Route::post('download/promotion-line', 'APIController@retrievePromotionLine');
Route::post('download/promotion-customer', 'APIController@retrievePromotionCustomer');
Route::post('download/promotion-discount-line', 'APIController@retrievePromotionDiscountLine');
Route::post('download/promotion-budget', 'APIController@retrievePromotionBudget');
Route::post('download/customer-discount', 'APIController@retrieveCustomerDiscount');
Route::post('download/pending-customer-creation-request', 'APIController@retrievePendingCustomerCreationRequest');
Route::post('download/pending-credit-control-change-request', 'APIController@retrievePendingCreditControlChangeRequest');
Route::post('download/posted-sales-invoice', 'APIController@retrievePostedSalesInvoice');
Route::post('download/posted-sales-invoice-header', 'APIController@retrievePostedSalesInvoiceHeader');
Route::post('download/posted-sales-invoice-line', 'APIController@retrievePostedSalesInvoiceLine');
Route::post('download/customer-balance', 'APIController@retrieveCustomerBalance');
Route::post('download/customer-balance-empties', 'APIController@retrieveCustomerBalanceEmpties');
Route::post('download/posted-pick-note', 'APIController@retrievePickNote');
Route::post('download/posted-pick-note-collection', 'APIController@retrievePickNoteCollection');
Route::post('download/posted-pick-note-inventory', 'APIController@retrievePickNoteInventory');
/* Upload */
Route::post('upload/withdrawal-request', 'APIController@generateWithdrawalRequest');
Route::post('upload/sales-order', 'APIController@generateSalesOrder');
Route::post('upload/sales-order-reservation-entry', 'APIController@generateSalesOrderReservationEntry');
Route::post('upload/sales-order-post', 'APIController@generateSalesOrderPost');
Route::post('upload/sales-order-cash-receipts', 'APIController@generateSalesOrderCashReceipt');
Route::post('upload/return-request', 'APIController@generateReturnRequest');
Route::post('upload/return-request-reservation-entry-salesman', 'APIController@generateReturnRequestReservationEntrySalesman');
Route::post('upload/return-request-reservation-entry-sales-office', 'APIController@generateReturnRequestReservationEntryWarehouse');
Route::post('upload/credit-note-header', 'APIController@generateCreditNoteHeader');
Route::post('upload/credit-note-line', 'APIController@generateCreditNoteLine');
Route::post('upload/cash-receipt-journal', 'APIController@generateCashReceiptJournal');
Route::post('upload/cash-receipt-journal-post', 'APIController@generateCashReceiptJournalPost');
Route::post('upload/sales-credit-memo', 'APIController@generateSalesCreditMemo');
Route::post('upload/sales-credit-memo-post', 'APIController@generateSalesCreditMemoPost');
Route::post('upload/new-customer-creation-request', 'APIController@generateNewCustomerCreationRequest');
Route::post('upload/new-credit-control-change-request', 'APIController@generateNewCreditControlChangeRequest');
Route::post('upload/caf-credit-limit', 'APIController@generateCafCreditLimit');
Route::post('upload/update-location', 'APIController@generateLocationUpdate');
/* Console (Download) */
Route::post('queue/customer-discount-group', 'APIController@queueRetrieveCustomerDiscountGroup');
Route::post('queue/customer-price-group', 'APIController@queueRetrieveCustomerPriceGroup');
Route::post('queue/customer-posting-group', 'APIController@queueRetrieveCustomerPostingGroup');
Route::post('queue/gen-bus-posting-group', 'APIController@queueRetrieveGenBusPostingGroup');
Route::post('queue/vat-bus-posting-group', 'APIController@queueRetrieveVATBusPostingGroup');
Route::post('queue/payment-method', 'APIController@queueRetrievePaymentMethod');
Route::post('queue/payment-terms', 'APIController@queueRetrievePaymentTerms');
Route::post('queue/channel', 'APIController@queueRetrieveDistributionChannel');
Route::post('queue/location', 'APIController@queueRetrieveLocation');
Route::post('queue/salesman-type', 'APIController@queueRetrieveSalesmanType');
Route::post('queue/salesman', 'APIController@queueRetrieveSalesman');
Route::post('queue/stock-in-salesman-warehouse', 'APIController@queueRetrieveStockInSalesmanWarehouse');
Route::post('queue/customer', 'APIController@queueRetrieveCustomer');
Route::post('queue/product', 'APIController@queueRetrieveProduct');
Route::post('queue/sales-price', 'APIController@queueRetrieveSalesPrice');
Route::post('queue/promotion', 'APIController@queueRetrievePromotion');
Route::post('queue/promotion-line', 'APIController@queueRetrievePromotionLine');
Route::post('queue/promotion-customer', 'APIController@queueRetrievePromotionCustomer');
Route::post('queue/promotion-discount-line', 'APIController@queueRetrievePromotionDiscountLine');
Route::post('queue/promotion-budget', 'APIController@queueRetrievePromotionBudget');
Route::post('queue/posted-sales-invoice', 'APIController@queueRetrievePostedSalesInvoice');
Route::post('queue/posted-sales-invoice-line', 'APIController@queueRetrievePostedSalesInvoiceLine');
Route::post('queue/customer-discount', 'APIController@queueRetrieveCustomerDiscount');
Route::post('queue/pending-customer-creation-request', 'APIController@queueRetrievePendingCustomerCreationRequest');
Route::post('queue/pending-credit-control-change-request', 'APIController@queueRetrievePendingCreditControlChangeRequest');
Route::post('queue/customer-balance', 'APIController@queueRetrieveCustomerBalance');
Route::post('queue/trigger-stock-in-salesman', 'APIController@queueRetreieveStockInSalesmanExternal');
Route::post('queue/customer-balance-empties', 'APIController@queueRetrieveCustomerBalanceEmpties');
Route::post('queue/trigger-invoice-picknote', 'APIController@queueRetreieveInvoiceAndPickNoteExternal');
Route::post('queue/picknote-collection-inventory', 'APIController@queueRetrievePickNoteInventoryCollection');
Route::post('queue/sales-order-lots-post', 'APIController@queueSalesOrderAssignLotsAndPost');
Route::post('queue/transfer-order-request', 'APIController@queueRetrieveTransferOrderDownloadRequest');
Route::post('queue/save-approved-refund', 'APIController@queueSaveApproveRefund');
/* Console (Upload) */
Route::post('queue/withdrawal-request', 'APIController@queueGenerateWithdrawalRequest');
Route::post('queue/sales-order', 'APIController@queueGenerateSalesOrder');
Route::post('queue/sales-order-queue', 'APIController@queueGenerateSalesOrderNew');
Route::post('queue/credit-memo-queue', 'APIController@queueGenerateCreditMemoNew');
Route::post('queue/sales-order-reservation-entry', 'APIController@queueGenerateSalesOrderReservationEntry');
Route::post('queue/sales-order-post', 'APIController@queueGenerateSalesOrderPost');
Route::post('queue/credit-note-header', 'APIController@queueGenerateCreditNoteHeader');
Route::post('queue/sales-credit-memo', 'APIController@queueGenerateSalesCreditMemo');
Route::post('queue/sales-credit-memo-post', 'APIController@queueGenerateSalesCreditMemoPost');
Route::post('queue/credit-note-line', 'APIController@queueGenerateCreditNoteLine');
Route::post('queue/cash-receipt-journal', 'APIController@queueGenerateCashReceiptJournal');
Route::post('queue/cash-receipt-journal-post', 'APIController@queueGenerateCashReceiptJournalPost');
Route::post('queue/new-customer-creation-request', 'APIController@queueGenerateNewCustomerCreationRequest');
Route::post('queue/new-credit-control-change-request', 'APIController@queueGenerateNewCreditControlChangeRequest');
Route::post('queue/sales-order-cash-receipts', 'APIController@queueGenerateSalesOrderCashReceipt');
Route::post('queue/return-request', 'APIController@queueGenerateReturnRequest');
Route::post('queue/return-request-reservation-entry-salesman', 'APIController@queueGenerateReturnRequestReservationEntrySalesman');
Route::post('queue/return-request-reservation-entry-sales-office', 'APIController@queueGenerateReturnRequestReservationEntryWarehouse');
Route::post('queue/caf-credit-limit', 'APIController@queueGenerateCafCreditLimit');
Route::post('queue/credit-memo-cancelled-location-queue', 'APIController@queueSalesOrderCancelledLocation');
Route::post('queue/sales-shipment', 'APIController@queuePostedSalesShipment');
Route::post('queue/sales-credit-memo-post-manual', 'APIController@queueSalesCreditMemoPostManual');
Route::post('queue/sales-credit-memo-update-location', 'APIController@queueCreditMemoUpdateLocation');
/* Batch Download */
Route::post('download/batch-customer', 'APIBatchController@retrieveBatchCustomer');
/* Console (Batch Download) */
Route::post('queue/customer-multiple', 'APIController@queueRetrieveCustomerMultiple');
Route::post('queue/stocks-multiple', 'APIController@queueRetrieveStocksMultiple');
Route::post('queue/invoice-multiple', 'APIController@queueRetrieveSalesInvoiceMultiple');
/* Console (Batch Upload) */
Route::post('queue/sales-order-multiple', 'APIController@queueGenerateSalesOrderMultiple');
Route::post('queue/sales-credit-memo-multiple', 'APIController@queueRunSalesCreditMemoMultiple');
Route::post('queue/cash-receipt-journal-multiple', 'APIController@queueRunCashReceiptMultiple');
Route::post('queue/batch-transaction', 'APIController@queueRunBatchTransaction');
