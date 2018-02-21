<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Crypto;
use App\bittrex;
use App\Polo;
use App\Token;
use App\Historical;
use App\Multiplier;
use App\Events\PriceEvent;
use Cache;

class UpdateCmc implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     public $tries = 1;
     public $timeout = 0;
     
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
      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'https://api.coinmarketcap.com/v1/ticker?convert=EUR');
      $response = $res->getBody();
      $cryptos = json_decode($response, true);


      foreach($cryptos as $crypto){
        if($crypto['name'] != "BatCoin" && $crypto['name'] != "iCoin" && $crypto['name'] != "NetCoin" && $crypto['name'] != "The Aladin" && $crypto['name'] != "BetaCoin" && $crypto['name'] != "Arcade Token" && $crypto['name'] != "Tickets" && $crypto['name'] != "Happy Creator Coin"){

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

          //event(new PriceEvent('BTC_'.$crypto['symbol'], 'Coinmarketcap', $crypto['price_usd'], $crypto['percent_change_24h']));

          if($crypto['symbol'] == "BTC")
          {
            $date = date('Y-m-d 00:00:00');
            $historical = Historical::where('created_at', $date)->first();

            if($historical)
            {
              $historical->USD = $crypto['price_usd'];
              $historical->ETH = $crypto['price_usd'] / Crypto::where('symbol', 'ETH')->select('price_usd')->first()->price_usd;
              $historical->save();
            } else {
              $historical = new Historical;
              $historical->currency = "BTC";
              $historical->USD = $crypto['price_usd'];
              $historical->ETH = $crypto['price_usd'] / Crypto::where('symbol', 'ETH')->select('price_usd')->first()->price_usd;
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
          $newcrypto->save();
        }
      }
    }
}
