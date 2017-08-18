<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Crypto;
use App\bittrex;
use App\Polo;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->call('App\Http\Controllers\SignatureController@updateSignature')
                 ->everyTenMinutes();

       $schedule->call('App\Http\Controllers\InvestmentController@currencies')
                ->everyTenMinutes();

        $schedule->call('App\Http\Controllers\InvestmentController@worldcoin')
                 ->everyFiveMinutes();

       $schedule->call('App\Http\Controllers\TrackingController@updateEthermine')
                ->everyThirtyMinutes();


        $schedule->call('App\Http\Controllers\TrackingController@updateNanopool')
                 ->everyThirtyMinutes();
       $schedule->call('App\Http\Controllers\TrackingController@updateEthereum')->everyThirtyMinutes();

       $schedule->call('App\Http\Controllers\TrackingController@updateNicehash')->everyThirtyMinutes();

        $schedule->call(function(){

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'https://api.coinmarketcap.com/v1/ticker?convert=EUR');
        $response = $res->getBody();
        $cryptos = json_decode($response, true);


        foreach($cryptos as $crypto){
          if($crypto['name'] != "BatCoin" && $crypto['name'] != "iCoin" && $crypto['name'] != "NetCoin" && $crypto['name'] != "The Aladin" && $crypto['name'] != "BetaCoin" && $crypto['name'] != "Arcade Token" && $crypto['name'] != "Tickets" && $crypto['name'] != "Happy Creator Coin"){
            if(Crypto::where('symbol', $crypto['symbol'])->exists()){
                $newcrypto = Crypto::where('symbol', $crypto['symbol'])->first();
                if($crypto['symbol'] == "XLM"){
                $str =  Crypto::where('symbol', 'STR')->first();
                $str->price_usd = $crypto['price_usd'];
                $str->price_btc = $crypto['price_btc'];
                $str->price_eur = $crypto['price_eur'];
                $str->percent_change_24h = $crypto['percent_change_24h'];
                $str->save();
                }
            } else {
                $newcrypto = new Crypto;
            }
            if($crypto['symbol'] != "DBIC")
            {
              $newcrypto->name = $crypto['name'];
            }
            $newcrypto->symbol = $crypto['symbol'];
            $newcrypto->price_usd = $crypto['price_usd'];
            $newcrypto->price_btc = $crypto['price_btc'];
            $newcrypto->price_eur = $crypto['price_eur'];
            $newcrypto->percent_change_24h = $crypto['percent_change_24h'];
            $newcrypto->save();
          }
        }

            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', 'https://bittrex.com/api/v1.1/public/getmarketsummaries');
            $response = $res->getBody();
            $cryptos = json_decode($response, true);

                foreach($cryptos['result'] as $crypto){
                    if(strpos($crypto['MarketName'], 'BTC-') !== false){
                        $symbol = str_replace('BTC-', '', $crypto['MarketName']);
                        if(bittrex::where('symbol', $symbol)->first()){
                            $coin = bittrex::where('symbol', $symbol)->first();
                        } else {
                        $coin = new bittrex;
                        }
                        $coin->symbol = $symbol;
                        $coin->price_btc = $crypto['Last'];
                        $coin->save();
                    } else {
                      if(bittrex::where('symbol', $crypto['MarketName'])->first()){
                          $coin = bittrex::where('symbol', $crypto['MarketName'])->first();
                      } else {
                          $coin = new bittrex;
                      }
                      $coin->symbol = $crypto['MarketName'];
                      $coin->price_btc = $crypto['Last'];
                      $coin->save();
                    }
                }

            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', 'https://poloniex.com/public?command=returnTicker');
            $response = $res->getBody();
            $cryptos = json_decode($response, true);

            foreach($cryptos as $key => $crypto){
                if(strpos($key, 'BTC_') !== false){
                   $symbol = str_replace('BTC_', '', $key);
                    if(Polo::where('symbol', $symbol)->exists()){
                        $coin = Polo::where('symbol', $symbol)->first();
                    } else {
                        $coin = new Polo;
                    }
                    $coin->symbol = $symbol;
                    $coin->price_btc = $crypto['last'];
                    $coin->save();
                } else {
                  if(Polo::where('symbol', $key)->exists()){
                      $coin = Polo::where('symbol', $key)->first();
                  } else {
                      $coin = new Polo;
                  }
                  $coin->symbol = $key;
                  $coin->price_btc = $crypto['last'];
                  $coin->save();
                }
            }


        });
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
