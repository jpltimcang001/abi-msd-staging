<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Artisan;

class APIJobHandler implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $str;
    protected $arr;
    /**
     * Create a new job instance.
     * 
     * @param str String to identify method used
     * @param arr Data from request.
     *
     * @return void
     */
    public function __construct($str, $arr)
    {
        $this->str = $str;
        $this->arr = $arr;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call(
            'apirun:send',
            [
                'type' => $this->str, 'data' => $this->arr
            ]
        );
        return;
    }
}
