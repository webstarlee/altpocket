<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Stat;
use App\BittrexTrade;
use App\PoloTrade;

class UpdateProfitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:profit {--queue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Legit Profits to the leaderboards.';

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
      $users = User::all();

         foreach($users as $user){
             $invested = 0;
             $networth = 0;




             $stats = Stat::where('userid', $user->id)->first();
             $bittrex_trades = BittrexTrade::where([['userid', '=', $user->id], ['handled', '=', 0]])->select('id')->count();
             $polo_trades = PoloTrade::where([['userid', '=', $user->id], ['handled', '=', 0]])->select('id')->count();

             if($bittrex_trades == 0 && $polo_trades == 0)
             {
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
}
