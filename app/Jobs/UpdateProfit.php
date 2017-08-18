<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use App\Stat;

class UpdateProfit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     public $tries = 2;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

      $users = User::all();

         foreach($users as $user){
             $invested = 0;
             $networth = 0;




             $stats = Stat::where('userid', $user->id)->first();


             if($stats){
                  $stats->legit = $user->getMoney();
                  $stats->save();


             } else {
                 $stats = new Stat;
                 $stats->legit = $user->getMoney();
                 $stats->userid = $user->id;
                 $stats->currency = $user->currency;
                 $stats->save();
             }














         }


    }
}
