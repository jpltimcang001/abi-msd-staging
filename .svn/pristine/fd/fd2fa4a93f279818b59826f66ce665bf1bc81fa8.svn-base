<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Artisan;

class APIJobSalesOrderHandler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $code;
    protected $sales_office_no;
    protected $trigger_id;
    /**
     * Create a new job instance.
     * 
     * @param str String to identify method used
     * @param arr Data from request.
     *
     * @return void
     */
    public function __construct($code, $sales_office_no, $trigger_id)
    {
        $this->code = $code;
        $this->sales_office_no = $sales_office_no;
        $this->trigger_id = $trigger_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call(
            'apirun:salesorder',
            [
                'code' => $this->code, 'sales_office_no' => $this->sales_office_no, 'trigger_id' => $this->trigger_id
            ]
        );
        return;
    }
}
