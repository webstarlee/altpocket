<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Crypto;
use App\bittrex;
use App\Polo;
use App\Token;
use App\Historical;
use App\Multiplier;
use App\Events\PriceEvent;
use Cache;
use App\Jobs\UpdateCmc;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UpdateProfitCommand::class
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
        //$schedule->call('App\Http\Controllers\SignatureController@updateSignature')
          //       ->everyTenMinutes();

       $schedule->call('App\Http\Controllers\InvestmentController@currencies')
                ->everyTenMinutes();

        $schedule->call('App\Http\Controllers\InvestmentController@worldcoin')
                 ->everyFiveMinutes();

      // $schedule->call('App\Http\Controllers\TrackingController@updateEthermine')
      //          ->everyThirtyMinutes();


        $schedule->call('App\Http\Controllers\TrackingController@updateNanopool')
                 ->everyThirtyMinutes();
       $schedule->call('App\Http\Controllers\TrackingController@updateEthereum')->everyThirtyMinutes();

       $schedule->call('App\Http\Controllers\TrackingController@updateNicehash')->everyThirtyMinutes();

       $schedule->call('App\Http\Controllers\ImportController@getCoinBasePrices')->everyMinute();

        $schedule->call(function(){

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'https://api.coinmarketcap.com/v1/ticker?convert=EUR&limit=0');
        $response = $res->getBody();
        $cryptos = json_decode($response, true);


        foreach($cryptos as $crypto){
          if($crypto['name'] != "BatCoin" && $crypto['name'] != 'Rcoin' && $crypto['name'] != "eBTC [OLD]" && $crypto['name'] != "iCoin" && $crypto['name'] != "NetCoin" && $crypto['name'] != "The Aladin" && $crypto['name'] != "BetaCoin" && $crypto['name'] != "Arcade Token" && $crypto['name'] != "Tickets" && $crypto['name'] != "Happy Creator Coin"){

            if($crypto['name'] == "Bytom")
            {
              $crypto['symbol'] = "BYTM";
            }
            if($crypto['name'] == "BlockCAT")
            {
              $crypto['symbol'] = "BCAT";
            }
            if($crypto['id'] == "kingn-coin")
            {
              $crypto['symbol'] = "KINGN";
            }
            if($crypto['name'] == "SmartCash")
            {
              $crypto['symbol'] = "SMARTC";
            }
            if($crypto['name'] == "Bitgem")
            {
              $crypto['symbol'] = "BTG2";
            }

            //event(new PriceEvent('BTC_'.$crypto['symbol'], 'Coinmarketcap', $crypto['price_usd'], $crypto['percent_change_24h']));

            if($crypto['symbol'] == "BTC")
            {
              $date = date('Y-m-d 00:00:00');
              $historical = Historical::where('created_at', $date)->first();

              if($historical)
              {
                $historical->USD = $crypto['price_usd'];
                $historical->ETH = $crypto['price_usd'] / Crypto::where('symbol', 'ETH')->select('price_usd')->first()->price_usd;
                $historical->XMR = $crypto['price_usd'] / Crypto::where('symbol', 'XMR')->select('price_usd')->first()->price_usd;
                $historical->save();
              } else {
                $historical = new Historical;
                $historical->currency = "BTC";
                $historical->USD = $crypto['price_usd'];
                $historical->ETH = $crypto['price_usd'] / Crypto::where('symbol', 'ETH')->select('price_usd')->first()->price_usd;
                $historical->XMR = $crypto['price_usd'] / Crypto::where('symbol', 'XMR')->select('price_usd')->first()->price_usd;
                $historical->created_at = $date;
                $historical->updated_at = $date;
                $historical->save();
              }

              $usd = Crypto::where('symbol', 'USD')->first();
              $usd->price_btc = 1 / $crypto['price_usd'];
              $usd->save();

              $eur = Crypto::where('symbol', 'EUR')->first();
              $eur->price_btc = 1 / $crypto['price_eur'];
              $eur->save();

              $gbp = Crypto::where('symbol', 'GBP')->first();
              $gbp->price_btc = 1 / ($crypto['price_usd'] * Multiplier::where('currency', 'GBP')->select('price')->first()->price);
              $gbp->save();
            }

            if(!file_exists(public_path('assets/logos/'.$crypto['symbol'].'.png')))
            {
              copy('https://files.coinmarketcap.com/static/img/coins/64x64/' . $crypto['id'] . '.png', public_path('assets/logos/') . $crypto['symbol'] . '.png');
            }
            if(!file_exists(public_path('icons/32x32/'.$crypto['symbol'].'.png')))
            {
              copy('https://files.coinmarketcap.com/static/img/coins/32x32/' . $crypto['id'] . '.png', public_path('icons/32x32/') . $crypto['symbol'] . '.png');
            }

            if(Crypto::where('symbol', $crypto['symbol'])->exists()){
                $newcrypto = Crypto::where('symbol', $crypto['symbol'])->first();
                if($crypto['symbol'] == "XLM"){
                $str =  Crypto::where('symbol', 'STR')->first();
                $str->price_usd = $crypto['price_usd'];
                $str->price_btc = $crypto['price_btc'];
                $str->price_eur = $crypto['price_eur'];
                $str->cmc_id = $crypto['id'];
                $str->percent_change_24h = $crypto['percent_change_24h'];
                $str->rank = $crypto['rank'];
                $str->market_cap_usd = $crypto['market_cap_usd'];    
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
            $newcrypto->cmc_id = $crypto['id'];
            $newcrypto->price_usd = $crypto['price_usd'];
            $newcrypto->price_btc = $crypto['price_btc'];
            $newcrypto->price_eur = $crypto['price_eur'];
            $newcrypto->percent_change_24h = $crypto['percent_change_24h'];
            $newcrypto->rank = $crypto['rank'];
            $newcrypto->market_cap_usd = $crypto['market_cap_usd'];
            $newcrypto->save();
          }
        }

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'https://api.coinmarketcap.com/v1/ticker?convert=ETH&limit=0');
        $response = $res->getBody();
        $cryptos = json_decode($response, true);


        foreach($cryptos as $crypto){
          if($crypto['name'] != "BatCoin" && $crypto['name'] != 'Rcoin' && $crypto['name'] != "eBTC [OLD]" && $crypto['name'] != "iCoin" && $crypto['name'] != "NetCoin" && $crypto['name'] != "The Aladin" && $crypto['name'] != "BetaCoin" && $crypto['name'] != "Arcade Token" && $crypto['name'] != "Tickets" && $crypto['name'] != "Happy Creator Coin"){

            if($crypto['name'] == "Bytom")
            {
              $crypto['symbol'] = "BYTM";
            }
            if($crypto['name'] == "BlockCAT")
            {
              $crypto['symbol'] = "BCAT";
            }
            if($crypto['id'] == "kingn-coin")
            {
              $crypto['symbol'] = "KINGN";
            }
            if($crypto['name'] == "SmartCash")
            {
              $crypto['symbol'] = "SMARTC";
            }
            if($crypto['name'] == "Bitgem")
            {
              $crypto['symbol'] = "BTG2";
            }

            if(Crypto::where('symbol', $crypto['symbol'])->exists()){
                $newcrypto = Crypto::where('symbol', $crypto['symbol'])->first();
                if($crypto['symbol'] == "XLM"){
                $str =  Crypto::where('symbol', 'STR')->first();
                $str->price_eth = $crypto['price_eth'];
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
            $newcrypto->price_eth = $crypto['price_eth'];
            $newcrypto->percent_change_24h = $crypto['percent_change_24h'];
            $newcrypto->save();
          }
        }

            Cache::forget('cryptos');
            Cache::forget('cryptos2');
            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', 'https://bittrex.com/api/v1.1/public/getmarketsummaries');
            $response = $res->getBody();
            $cryptos = json_decode($response, true);

                foreach($cryptos['result'] as $crypto){
                    if(strpos($crypto['MarketName'], 'BTC-') !== false){
                        $symbol = str_replace('BTC-', '', $crypto['MarketName']);

                        if($symbol == "BCC")
                        {
                          $symbol = "BCH";
                        }
                        if($symbol == "USDT-BCC")
                        {
                          $symbol = "USDT-BCH";
                        }
                        if($symbol == "ETH-BCC")
                        {
                          $symbol = "ETH-BCH";
                        }

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
