<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportHitBTC implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $userid;
    protected $withdraws;

    public $tries = 1;
    public $timeout = 0;


   public function __construct($userid, $withdraws)
   {
       $this->userid = $userid;
       $this->withdraws = $withdraws;
   }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app('App\Http\Controllers\NewImportController')->importTradesHitBTC($this->userid);
    }
}
