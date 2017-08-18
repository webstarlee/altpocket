<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Cache;

class ImportBittrex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $userid;
     protected $withdraws;

     public $tries = 2;


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
      app('App\Http\Controllers\ImportController')->importDepositsB($this->userid);
      app('App\Http\Controllers\ImportController')->importTradesB($this->userid);
      app('App\Http\Controllers\ImportController')->insertBuysB($this->userid);
      app('App\Http\Controllers\ImportController')->importWithdrawsB($this->userid, $this->withdraws);
      app('App\Http\Controllers\ImportController')->insertSellsB($this->userid);
      app('App\Http\Controllers\ImportController')->importBalancesB($this->userid);
      Cache::forget('investments'.$this->userid);
      Cache::forget('b_investments'.$this->userid);
      Cache::forget('deposits'.$this->userid);
      Cache::forget('withdraws'.$this->userid);
      Cache::forget('balances'.$this->userid);

    }
}
