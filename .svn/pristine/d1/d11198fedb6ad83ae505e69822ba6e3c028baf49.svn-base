<?php

namespace App\Utils;

use Illuminate\Database\Eloquent\Model;
use SoapVar;

class Utils extends Model
{
    /**
     * Sends soap data to NOC to be converted to new GetMSDynamicsTriggersCriteria data.
     * (04/04/2022)
     * 
     * @param sales_office_no 
     * @param module module location of request
     * @param status current status of request currently processed
     * @param added_by user that requested the request from NOC.
     * 
     * @return Array returns GetMSDynamicsTriggers created by NOC, converted to an array.
     */
    public static function saveTrigger($sales_office_no, $module, $status, $added_by)
    {
        $soap_client = Globals::soapClientABINOCCentralWS();
        $params = new SoapVar('<GetMSDynamicsTriggersCriteria xsi:type="urn:GetMSDynamicsTriggersCriteria">
                                    <sales_office_no xsi:type="xsd:string">' . $sales_office_no . '</sales_office_no>
                                    <module xsi:type="xsd:string">' . $module . '</module>
                                    <status xsi:type="xsd:string">' . $status . '</status>
                                    <added_by xsi:type="xsd:string">' . $added_by . '</added_by>
                                </GetMSDynamicsTriggersCriteria>', XSD_ANYXML);
        return (array) $soap_client->saveMSDynamicsTriggers($params); /* Convert as array */
    }

    public static function updateTriggerTotalRows($trigger_id, $total_rows, $increment = false)
    {
        /* ABI NOC Central WS */
        $soap_client = Globals::soapClientABINOCCentralWS();

        if ($increment) {
            /* Retrieve ms dynamics trigger if existing */
            $params = new SoapVar('<GetMSDynamicsTriggersCriteria xsi:type="urn:GetMSDynamicsTriggersCriteria">
                                    <id xsi:type="xsd:integer">' . $trigger_id . '</id>
                                </GetMSDynamicsTriggersCriteria>', XSD_ANYXML);
            $soap_result_obj = $soap_client->retrieveMSDynamicsTriggersByCriteria($params);
            $soap_result = isset($soap_result_obj[0]) ? (array) $soap_result_obj[0] : array(); /* Convert as array */

            $soap_result_total_rows = isset($soap_result['total_rows']) ? $soap_result['total_rows'] : 0;
            $total_rows = $soap_result_total_rows + $total_rows; /* New trigger total_rows rows value */
        }

        $params = new SoapVar('<GetMSDynamicsTriggersCriteria xsi:type="urn:GetMSDynamicsTriggersCriteria">
                                <id xsi:type="xsd:integer">' . $trigger_id . '</id>
                                <total_rows xsi:type="xsd:integer">' . $total_rows . '</total_rows>
                            </GetMSDynamicsTriggersCriteria>', XSD_ANYXML);
        return (array) $soap_client->saveMSDynamicsTriggers($params); /* Convert as array */
    }

    /**
     * Sends soap data to NOC to be converted to new GetMSDynamicsTriggersCriteria data, for login
     * failed rows.
     * (04/04/2022)
     * 
     * @param sales_office_no 
     * @param module module location of request
     * @param status current status of request currently processed
     * @param added_by user that requested the request from NOC.
     * 
     * @return Array returns GetMSDynamicsTriggers created by NOC, converted to an array.
     */
    public static function updateTriggerFailedRows($trigger_id, $failed_rows, $increment = false)
    {
        /* ABI NOC Central WS */
        $soap_client = Globals::soapClientABINOCCentralWS();

        if ($increment) {
            /* Retrieve ms dynamics trigger if existing */
            $params = new SoapVar('<GetMSDynamicsTriggersCriteria xsi:type="urn:GetMSDynamicsTriggersCriteria">
                                    <id xsi:type="xsd:integer">' . $trigger_id . '</id>
                                </GetMSDynamicsTriggersCriteria>', XSD_ANYXML);
            $soap_result_obj = $soap_client->retrieveMSDynamicsTriggersByCriteria($params);
            $soap_result = isset($soap_result_obj[0]) ? (array) $soap_result_obj[0] : array(); /* Convert as array */

            $soap_result_failed_rows = isset($soap_result['failed_rows']) ? $soap_result['failed_rows'] : 0;
            $failed_rows = $soap_result_failed_rows + $failed_rows; /* New trigger failed rows value */
        }

        $params = new SoapVar('<GetMSDynamicsTriggersCriteria xsi:type="urn:GetMSDynamicsTriggersCriteria">
                                <id xsi:type="xsd:integer">' . $trigger_id . '</id>
                                <failed_rows xsi:type="xsd:integer">' . $failed_rows . '</failed_rows>
                            </GetMSDynamicsTriggersCriteria>', XSD_ANYXML);
        return (array) $soap_client->saveMSDynamicsTriggers($params); /* Convert as array */
    }

    /**
     * Sends log data to NOC to be saved.
     * (04/04/2022)
     * 
     * @param sales_office_no 
     * @param module module location of request
     * @param status current status of request currently processed
     * @param added_by user that requested the request from NOC.
     * 
     * @return Array returns GetMSDynamicsLog created by NOC, converted to an array.
     */
    public static function saveLog($trigger_id, $sales_office_no, $timestamp, $level, $logger, $message, $added_by)
    {
        /* ABI NOC Central WS */
        $soap_client = Globals::soapClientABINOCCentralWS();

        $params = new SoapVar('<GetMSDynamicsLogCriteria xsi:type="urn:GetMSDynamicsLogCriteria">
                                <trigger_id xsi:type="xsd:integer">' . $trigger_id . '</trigger_id>
                                <sales_office_no xsi:type="xsd:string">' . $sales_office_no . '</sales_office_no>
                                <timestamp xsi:type="xsd:string">' . $timestamp . '</timestamp>
                                <level xsi:type="xsd:string">' . $level . '</level>
                                <logger xsi:type="xsd:string">' . $logger . '</logger>
                                <message xsi:type="xsd:string">' . htmlspecialchars($message) . '</message>
                                <added_by xsi:type="xsd:string">' . $added_by . '</added_by>
                             </GetMSDynamicsLogCriteria>', XSD_ANYXML);
							 
        return (array) $soap_client->saveMSDynamicsLog($params); /* Convert as array */
    }

    /**
     * Update MS Dynamics log/trigger data.
     * (04/04/2022)
     * 
     * @param sales_office_no 
     * @param module module location of request
     * @param status current status of request currently processed
     * @param added_by user that requested the request from NOC.
     * 
     * @return Array returns GetMSDynamicsTriggers created by NOC, converted to an array.
     */
    public static function updateTriggerStatus($trigger_id, $status)
    {
        /* ABI NOC Central WS */
        $soap_client = Globals::soapClientABINOCCentralWS();

        $params = new SoapVar('<GetMSDynamicsTriggersCriteria xsi:type="urn:GetMSDynamicsTriggersCriteria">
                                <id xsi:type="xsd:integer">' . $trigger_id . '</id>
                                <status xsi:type="xsd:string">' . $status . '</status>
                            </GetMSDynamicsTriggersCriteria>', XSD_ANYXML);
        return (array) $soap_client->saveMSDynamicsTriggers($params); /* Convert as array */
    }

    /**
     * Update MS Dynamics log/trigger date when finished.
     * (04/04/2022)
     * 
     * @param sales_office_no 
     * @param module module location of request
     * @param status current status of request currently processed
     * @param added_by user that requested the request from NOC.
     * 
     * @return Array returns GetMSDynamicsTriggers created by NOC, converted to an array.
     */
    public static function updateTriggerEndDate($trigger_id, $ended_date)
    {
        /* ABI NOC Central WS */
        $soap_client = Globals::soapClientABINOCCentralWS();

        $params = new SoapVar('<GetMSDynamicsTriggersCriteria xsi:type="urn:GetMSDynamicsTriggersCriteria">
                                <id xsi:type="xsd:integer">' . $trigger_id . '</id>
                                <ended_date xsi:type="xsd:string">' . $ended_date . '</ended_date>
                            </GetMSDynamicsTriggersCriteria>', XSD_ANYXML);
        return (array) $soap_client->saveMSDynamicsTriggers($params); /* Convert as array */
    }

    /**
     * Returns an array of item codes.
     * (04/04/2022)
     * 
     * @return Array returns item code suffix data.
     */
    public static function itemCodeSuffix()
    {
        return [
            'DEFAULT' => 'CSEF',
            'SUFFIX' => [
                0 => [
                    'BT' => 'BTLE',
                    'CS' => 'CSEE',
                    'PC' => 'PCE',
                    'BX' => 'BXE',
                    'PK' => 'PKE',
                ],
                1 => [
                    'BT' => 'BTLF',
                    'CS' => 'CSEF',
                    'PC' => 'PCF',
                    'BX' => 'BXF',
                    'PK' => 'PKF',
                ]
            ]
        ];
    }

    /**
     * Returns a string message append with or without square bracket
     * (28/07/2022)
     * 
     * @return string log message
     */
    public static function logMsg($msg)
    {
        $msg = trim($msg);
        return (substr($msg, 0, 1) == "[" ? "-" : " ") . $msg;
    }
}
