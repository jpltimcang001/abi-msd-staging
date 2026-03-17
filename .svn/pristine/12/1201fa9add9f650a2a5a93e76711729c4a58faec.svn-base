<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;
use DateTime;


class SalesOrderBatchTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apirun:SalesOrderBatch {--date-from=0} {--date-to=0} {--company=""} {--sales-office=""}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run sync of Sales Order Batch Transaction';

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
        $data = [
            'company' => $company,
            'sales_office_no' => $sales_office_no,            
            'params' => [ ]];
        if($date_from != 0 && $this->validateDate($date_from)) 
            $data['params']['date_from'] = DateTime::createFromFormat('Y-m-d', $date_from)->format("Y-m-d");
        else
            $data['params']['date_from'] = date('Y-m-d');
        
        if($date_to != 0 && $this->validateDate($date_to)) 
            $data['params']['date_to']  = DateTime::createFromFormat('Y-m-d', $date_to)->format("Y-m-d");
        else
            $data['params']['date_to'] = date('Y-m-d');
        
        Artisan::call(
            'apirun:send',
            [
                'type' => 'batch-transaction', 'data' => json_encode($data)
            ]
        );
    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
