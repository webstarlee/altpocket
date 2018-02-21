<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\PushEvent;
use Cache;

class ImportCoinbase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $userid;
     protected $withdraws;
     protected $cachekey;

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
      app('App\Http\Controllers\ImportController')->importBalancesCB($this->userid);
      app('App\Http\Controllers\ImportController')->InsertBuysCB($this->userid);
      app('App\Http\Controllers\ImportController')->insertWithdrawsCB($this->userid, $this->withdraws);
      app('App\Http\Controllers\ImportController')->insertSellCB($this->userid);
      Cache::forget('investments'.$this->userid);
      Cache::forget('c_investments'.$this->userid);
      Cache::forget('deposits'.$this->userid);
      Cache::forget('withdraws'.$this->userid);
      Cache::forget('balances'.$this->userid);
      Cache::forget('balances-summed'.$this->userid);
      Cache::forget('deposits'.$this->userid);
      Cache::forget('withdraws'.$this->userid);



    }
}
