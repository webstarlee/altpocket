<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use App\Notifications\ImportComplete;
use App\Events\PushEvent;
use Cache;

class ImportPoloniex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $userid;

    public $tries = 2;
    public $timeout = 0;
    protected $withdraws;
    protected $cachekey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userid, $withdraws)
    {
        $this->userid = $userid;
        $this->withdraws = $withdraws;
    }

    public function boot()
    {

    }





    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->cachekey = 'Import-Poloniex2'.$this->userid;
        if(Cache::get($this->cachekey) !== null) {
           event(new PushEvent('You already have an import running!', 'error', $this->userid));
           return $this->userid;
       }

        Cache::put($this->cachekey, 1, 5);

        app('App\Http\Controllers\ImportController')->importDeposits($this->userid);
        app('App\Http\Controllers\ImportController')->importTrades($this->userid);
        app('App\Http\Controllers\ImportController')->insertBuys($this->userid);
        app('App\Http\Controllers\ImportController')->insertSells($this->userid);
        app('App\Http\Controllers\ImportController')->importWithdraws($this->userid, $this->withdraws);
        app('App\Http\Controllers\ImportController')->importBalances2($this->userid);
        app('App\Http\Controllers\ImportController')->safePoloniexCheck($this->userid);

        Cache::forget('investments'.$this->userid);
        Cache::forget('p_investments'.$this->userid);
        Cache::forget('deposits'.$this->userid);
        Cache::forget('withdraws'.$this->userid);
        Cache::forget('balances'.$this->userid);
        Cache::forget('balances-summed'.$this->userid);
        Cache::forget('deposits'.$this->userid);
        Cache::forget('withdraws'.$this->userid);


        Cache::forget($this->cachekey);

    }
}
