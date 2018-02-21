<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Deposit;
use App\Withdraw;
use App\BittrexInvestment;
use App\BittrexTrade;
use App\Balance;

use App\Events\PushEvent;

use App\Multiplier;
use App\Key;
use App\User;
use App\History;

use Cache;

class NewBittrexImport implements ShouldQueue
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

     public $tries = 2;
     public $timeout = 0;


    public function __construct($userid, $withdraws)
    {
        $this->userid = $userid;
        $this->withdraws = $withdraws;
    }


    public function failed(Exception $exception)
    {
      event(new PushEvent('The import job failed, please make as support ticket for assistance.', 'error', $this->userid));
      Cache::forget($this->cachekey);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $this->cachekey = 'Bittrex-Import-Cache:'.$this->userid;
      if(Cache::get($this->cachekey) !== null) {
         event(new PushEvent('You already have an import running!', 'error', $this->userid));
         return $this->userid;
     }
      Cache::put($this->cachekey, 1, 5);

      /* GET API KEY */

      if(Key::where([['userid', '=', $this->userid], ['exchange', '=', 'Bittrex']])->exists())
      {
        $key = Key::where([['userid', '=', $this->userid], ['exchange', '=', 'Bittrex']])->first();
        $apikey = decrypt($key->public);
        $apisecret = decrypt($key->private);
      } else
      {
        event(new PushEvent('No API keys found, please add your Bittrex API keys.', 'error', $this->userid));
        Cache::forget($this->cachekey);
      }

      /* END API KEY */

      /* GUZZLE */

      $client = new \GuzzleHttp\Client();

      /* GUZZLE */

      /* Import Deposits */

      try{
        $nonce=time();
        $uri='https://bittrex.com/api/v1.1/account/getdeposithistory?apikey='.$apikey.'&nonce='.$nonce;
        $sign=hash_hmac('sha512',$uri,$apisecret);
        $res = $client->request('GET', $uri, [
        'headers' => [
        'apisign' => $sign,
        ]]);
        $response = $res->getBody();
        $deposits = json_decode($response, true);
        if($deposits['success'] == "true")
        {
          // Send a push notification to the user that the import has started
          event(new PushEvent('Your import has started!', 'success', $this->userid));

          // Loop through the data we received from the API.
        foreach($deposits['result'] as $d)
        {
          $ddate = strtotime($d['LastUpdated']);
          $newformat = date('Y-m-d H:i:s', $ddate);
          $newformat2 = date('Y-m-d', $ddate);

          // This renames the Currency to work properly with updates names and such.
          if($d['Currency'] == "ANS")
          {
            $d['Currency'] = "NEO";
          } elseif($d['Currency'] == "BCC") {
            $d['Currency'] = "BCH";
          } elseif($d['Currency'] == "SEC") {
            $d['Currency'] = "SAFEX";
          }

          // This makes sure that the imported data has not been processed in a previous import.
          if(!Deposit::where('txid', $this->userid . $d['TxId'])->exists())
          {
            // Get bitcoin price of day:
              $historical = History::getHistorical($newformat);
              $btc_usd = $historical->USD;
              $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
              $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
              $btc_usdt = $historical->USD;
              $btc_eth = $historical->ETH;
              $value = 0;

              // This gets historical prices
              $client = new \GuzzleHttp\Client();
              $res = $client->request('GET', 'https://poloniex.com/public?command=returnChartData&currencyPair=BTC_'.$d['Currency'].'&start='.strtotime($newformat2).'&end='.strtotime($newformat2).'&period=14400');
              $response = $res->getBody();
              $prices = json_decode($response, true);

              if($d['Currency'] != "BTC")
              {
                foreach($prices as $price)
                {
                  if(is_array($prices) && isset($price['open']))
                  {
                    $value = $price['open'];
                  } else {
                    $value = 0;
                  }
                }
              } else {
                $value = 1;
              }



          // Insert the deposit to the database
          $deposit = new Deposit;
          $deposit->userid = $this->userid;
          $deposit->exchange = "Bittrex";
          $deposit->txid = $this->userid . $d['TxId'];
          $deposit->date = $newformat;
          $deposit->currency = $d['Currency'];
          $deposit->amount = $d['Amount'];
          $deposit->btc_price_deposit_usd = $btc_usd;
          $deposit->btc_price_deposit_eur = $btc_eur;
          $deposit->btc_price_deposit_gbp = $btc_gbp;
          $deposit->btc_price_deposit_eth = $btc_eth;
          $deposit->btc_price_deposit_usdt = $btc_usdt;
          $deposit->price = $value;
          $deposit->save();

          // This checks if a balance exists, if it doesn't we create a new balance


          if(!Balance::where([['userid', '=', $this->userid], ['currency', '=', $d['Currency']], ['exchange', '=', 'Bittrex']])->exists())
          {
            $balance = new Balance;
            $balance->exchange = "Bittrex";
            $balance->userid = $this->userid;
            $balance->currency = $d['Currency'];
            $balance->amount = $d['Amount'];
            $balance->save();
          } else
          {
            $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $d['Currency']], ['exchange', '=', 'Bittrex']])->first();
            $balance->amount += $d['Amount'];
            $balance->save();


          }

        }
      }
        } else {

          // Here is some fallback stuff
          if($deposits['message'] == "APIKEY_INVALID")
          {
            Cache::forget($this->cachekey);
            event(new PushEvent('No API keys found or invalid combination.', 'error', $this->userid));
            return true;
          } else {
            Cache::forget($this->cachekey);
            event(new PushEvent($deposits['message'], 'error', $this->userid));
            return true;
          }
          }
        } catch(\GuzzleHttp\Exception\RequestException $e){
          event(new PushEvent('No API keys found or invalid combination.', 'error', $this->userid));
          Cache::forget($this->cachekey);
          return true;
        } catch(\GuzzleHttp\Exception\ClientException $e) {
          event(new PushEvent('No API keys found or invalid combination.', 'error', $this->userid));
          Cache::forget($this->cachekey);
          return true;
        } catch(\GuzzleHttp\Exception\BadResponseException $e) {
          event(new PushEvent('No API keys found or invalid combination.', 'error', $this->userid));
          Cache::forget($this->cachekey);
          return true;
        } catch(\GuzzleHttp\Exception\ServerException $e) {
          event(new PushEvent('No API keys found or invalid combination.', 'error', $this->userid));
          Cache::forget($this->cachekey);
          return true;
        }


        /* End Import Deposits */

      /* Insert Trades */

      try{
      $nonce=time();
      $uri='https://bittrex.com/api/v1.1/account/getorderhistory?apikey='.$apikey.'&nonce='.$nonce;
      $sign=hash_hmac('sha512',$uri,$apisecret);
      $res = $client->request('GET', $uri, [
      'headers' => [
      'apisign' => $sign,
      ]]);
      $response = $res->getBody();
      $trades = json_decode($response, true);
      }  catch (\GuzzleHttp\Exception\ClientException $e) {
        event(new PushEvent('Undefined error occured, please contact Edwin.', 'error', $this->userid));
        return true;
      }


      if($trades['result'])
      {
        foreach($trades['result'] as $trade)
        {
          if(!BittrexTrade::where('tradeid', $this->userid . $trade['OrderUuid'])->exists())
          {
            $currencies = explode("-", $trade['Exchange']);

            $ddate = strtotime($trade['TimeStamp']);
            $newformat = date('Y-m-d H:i:s', $ddate);

            $t = new BittrexTrade;
            $t->userid = $this->userid;
            $t->tradeid = $this->userid . $trade['OrderUuid'];
            $t->date = $newformat;
            $t->type = $trade['OrderType'];
            $t->market = $currencies[0];

            if($currencies[1] == "ANS")
            {
              $t->currency = "NEO";
            } elseif($currencies[1] == "BCC") {
              $t->currency = "BCH";
            } elseif($currencies[1] == "SEC")
            {
              $t->currency = "SAFEX";
            } else {
              $t->currency = $currencies[1];
            }

            if($t->currency == "BCC")
            {
              $t->currency = "BCH";
            }
            $t->price = $trade['PricePerUnit'];
            $t->amount = $trade['Quantity'] - $trade['QuantityRemaining'];
            $t->fee = $trade['Commission'];
            $t->total = $trade['Price'];
            $t->save();

          }

        }
    }

      /* End Insert Trades */

      /* Insert Buys */



      $trades = BittrexTrade::where([['userid', '=', $this->userid], ['handled', '=', '0'], ['type', '=', 'LIMIT_BUY']])->orderBy('date')->get();
      event(new PushEvent(count($trades) . ' new buy orders imported, starting conversion to investments.', 'success', $this->userid));
      foreach($trades as $trade)
      {
        $investment = BittrexInvestment::where('orderid', $trade->tradeid)->first();
        $date = strtotime($trade->date);
        $newformat = date('Y-m-d', $date);
        $date = $newformat;

        $historical = History::getHistorical($date);
        $btc_usd = $historical->USD;
        $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
        $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
        $btc_usdt = $historical->USD;
        $btc_eth = $historical->ETH;



        if($investment)
        {
          $investment->amount += $trade->amount;
          $investment->bought_for += $trade->total;

          if($investment->market == "BTC")
          {
            $investment->bought_for_usd = $investment->bought_for * $btc_usd;
          } elseif($investment->market == "ETH")
          {
            $investment->bought_for_usd = ($btc_usd / $btc_eth) * $investment->bought_for;
          } elseif($investment->market == "USDT")
          {
            $investment->bought_for_usd = ($btc_usd / $btc_usdt) * $investment->bought_for;
          }


          $investment->save();

          $trade->handled = 1;
          $trade->save();
        } else {
          $investment = new BittrexInvestment;

          $investment->userid = $this->userid;
          $investment->currency = $trade->currency;
          $investment->market = $trade->market;


          $investment->orderid = $trade->tradeid;
          $investment->date_bought = $trade->date;
          $investment->bought_at = $trade->price;

          $investment->amount = $trade->amount;
          $investment->bought_for = $trade->total;

          if($investment->market == "BTC")
          {
            $investment->bought_for_usd = $trade->total * $btc_usd;
          } elseif($investment->market == "ETH")
          {
            $investment->bought_for_usd = ($btc_usd / $btc_eth) * $trade->total;
          } elseif($investment->market == "USDT")
          {
            $investment->bought_for_usd = ($btc_usd / $btc_usdt) * $trade->total;
          }

          $investment->btc_price_bought_usd = $btc_usd;
          $investment->btc_price_bought_eur = $btc_eur;
          $investment->btc_price_bought_gbp = $btc_gbp;
          $investment->btc_price_bought_eth = $btc_eth;
          $investment->btc_price_bought_usdt = $btc_usdt;

          $investment->type = "Investment";

          $investment->save();

          $trade->handled = 1;
          $trade->save();

        }

        if(!Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Bittrex";
          $balance->userid = $this->userid;
          $balance->currency = $trade->currency;
          $balance->amount = $trade->amount;
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->first();
          $balance->amount += $trade->amount;
          $balance->save();
        }

        if(!Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->market], ['exchange', '=', 'Bittrex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Bittrex";
          $balance->userid = $this->userid;
          $balance->currency = $trade->market;
          $balance->amount = 0 - $trade->total;
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->market], ['exchange', '=', 'Bittrex']])->first();
          $balance->amount -= $trade->total;
          $balance->save();
        }


      }



      /* End Insert Buys */

      /* Insert Withdraws */


      try{
      $nonce=time();
      $uri='https://bittrex.com/api/v1.1/account/getwithdrawalhistory?apikey='.$apikey.'&nonce='.$nonce;
      $sign=hash_hmac('sha512',$uri,$apisecret);
      $res = $client->request('GET', $uri, [
      'headers' => [
      'apisign' => $sign,
      ]]);
      $response = $res->getBody();
      $withdraws = json_decode($response, true);
      }  catch (\GuzzleHttp\Exception\ClientException $e) {
        event(new PushEvent('Undefined error occured, please contact Edwin.', 'error', $this->userid));
        return true;
      }


      if(is_array($withdraws) && isset($withdraws['result']))
      {
      foreach($withdraws['result'] as $w)
      {
        $ddate = strtotime($w['Opened']);
        $newformat = date('Y-m-d H:i:s', $ddate);
        $newformat2 = date('Y-m-d', $ddate);

        if($w['Currency'] == "ANS")
        {
          $w['Currency'] = "NEO";
        } elseif($w['Currency'] == "BCC") {
          $w['Currency'] = "BCH";
        } elseif($w['Currency'] == "SEC") {
          $w['Currency'] = "SAFEX";
        }

        if(!Withdraw::where('txid', $this->userid . $w['TxId'])->exists())
        {

          $historical = History::getHistorical($newformat);
          $btc_usd = $historical->USD;
          $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
          $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
          $btc_usdt = $historical->USD;
          $btc_eth = $historical->ETH;

            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', 'https://poloniex.com/public?command=returnChartData&currencyPair=BTC_'.$w['Currency'].'&start='.strtotime($newformat2).'&end='.strtotime($newformat2).'&period=14400');
            $response = $res->getBody();
            $prices = json_decode($response, true);
            $value = 0;

            if($w['Currency'] != "BTC")
            {
              foreach($prices as $price)
              {
                if(is_array($prices) && isset($price['open']))
                {
                  $value = $price['open'];
                } else {
                  $value = 0;
                }
              }
            } else {
              $value = 1;
            }



        $with = new Withdraw;
        $with->userid = $this->userid;
        $with->exchange = "Bittrex";
        $with->txid = $this->userid . $w['TxId'];
        $with->date = $newformat;
        $with->currency = $w['Currency'];
        $with->amount = $w['Amount'] + $w['TxCost'];
        $with->btc_price_deposit_usd = $btc_usd;
        $with->btc_price_deposit_eur = $btc_eur;
        $with->btc_price_deposit_gbp = $btc_gbp;
        $with->btc_price_deposit_eth = $btc_eth;
        $with->btc_price_deposit_usdt = $btc_usdt;
        $with->price = $value;
        $with->save();

        if(!Balance::where([['userid', '=', $this->userid], ['currency', '=', $w['Currency']], ['exchange', '=', 'Bittrex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Bittrex";
          $balance->userid = $this->userid;
          $balance->currency = $w['Currency'];
          $balance->amount = 0 - ($w['Amount'] + $w['TxCost']);
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $w['Currency']], ['exchange', '=', 'Bittrex']])->first();
          $balance->amount -= ($w['Amount'] + $w['TxCost']);;
          $balance->save();
        }

        }

      }
      }

      $withdraws = Withdraw::where([['userid', '=', $this->userid], ['handled', '=', 0], ['exchange', '=', 'Bittrex']])->get();

      foreach($withdraws as $withdraw)
      {
        if($withdraw->handled != 1)
        {
        $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $withdraw->currency], ['exchange', '=', 'Bittrex']])->first();
        $holdings = 0;
        $investments = BittrexInvestment::where([['userid', '=', $this->userid], ['currency', '=', $withdraw->currency], ['saleid', '=', null]])->orderBy('date_bought', 'desc')->get();
        $investmentexact = BittrexInvestment::where([['userid', '=', $this->userid], ['currency', '=', $withdraw->currency], ['saleid', '=', null], ['amount', '=', $withdraw->amount]])->orderBy('date_bought', 'desc')->first();
        $amount = $withdraw->amount;

        if(!$investmentexact)
        {
        foreach($investments as $investment)
        {
          $holdings += $investment->amount;
        }


        if($balance){
          $newbalance = $balance->amount - $holdings;
        } else {
          $newbalance = 0 - $holdings;
        }

        if($newbalance > $withdraw->amount)
        {
          $balance->amount -= $withdraw->amount;
          $balance->save();
          $withdraw->handled = 1;
          $withdraw->save();
        } else {
          foreach($investments as $investment)
          {
            if($amount >= 0)
            {
              if($investment->amount >= $amount)
              {
                if($this->withdraws == 1){ $investment->amount -= $amount; }
                $amount = 0;
                $investment->withdrew = 1;
                $investment->save();
                $withdraw->handled = 1;
                $withdraw->save();


                  if($investment->amount <= 0)
                  {
                    if($this->withdraws == 1){ $investment->delete(); }
                  }


              } elseif($investment->amount <= $amount) {
                $amount -= $investment->amount;
                $investment->withdrew = 1;
                $investment->save();
                if($this->withdraws == 1){ $investment->delete(); }

                $withdraw->handled = 1;
                $withdraw->save();
              }



            }
          }
        }
        } else {
          $investmentexact->withdrew = 1;
          $investmentexact->save();
          $withdraw->handled = 1;
          $withdraw->save();
          $amount = 0;

        if($this->withdraws == 1){ $investmentexact->delete(); }

      }
      }
      }

      /* End Withdraws */

      /* Insert Sells */

      $trades = BittrexTrade::where([['userid', '=', $this->userid], ['handled', '=', '0'], ['type', '=', 'LIMIT_SELL']])->orderBy('date')->get();
      event(new PushEvent(count($trades) . ' new sell orders imported, starting conversion to investments.', 'success', $this->userid));
      $lasttrade = 0;

      foreach($trades as $trade)
      {
        $amount = $trade->amount;

        $investments = BittrexInvestment::where([['currency', '=', $trade->currency], ['userid', '=', $this->userid], ['saleid', '=', null], ['date_bought', '<=', $trade->date]])->orderBy('date_bought')->get();

        if($amount > 0)
        {
          if(count($investments) >= 1)
          {
          foreach($investments as $investment)
          {
            if($investment->amount >= $amount && $amount > 0)
            {
              $sale = BittrexInvestment::where([['saleid', '=', $trade->tradeid], ['userid', '=', $this->userid], ['soldmarket', '=', $trade->market]])->first();

              if(!$sale && $investment->amount != 0)
              {
                $date = strtotime($trade->date);
                $newformat = date('Y-m-d', $date);
                $date = $newformat;

                $historical = History::getHistorical($date);
                $btc_usd = $historical->USD;
                $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
                $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
                $btc_usdt = $historical->USD;
                $btc_eth = $historical->ETH;

                  $inv = new BittrexInvestment;
                  $inv->userid = $this->userid;
                  $inv->currency = $investment->currency;
                  $inv->market = $investment->market;
                  $inv->soldmarket = $trade->market;
                  $inv->orderid = $investment->orderid;
                  $inv->saleid = $trade->tradeid;
                  $inv->date_bought = $investment->date_bought;
                  $inv->date_sold = $trade->date;
                  $inv->bought_at = $investment->bought_at;
                  $inv->sold_at = $trade->price;
                  $inv->amount = $amount;
                  $inv->bought_for = $investment->bought_at * $amount;
                  $inv->bought_for_usd = $inv->bought_for * $investment->btc_price_bought_usd;
                  $inv->sold_for = $trade->total;
                  if($inv->soldmarket == "BTC")
                  {
                    $inv->sold_for_usd = $trade->total * $btc_usd;
                  } elseif($inv->soldmarket == "USDT")
                  {
                      $inv->sold_for_usd = $trade->total * ($btc_usd / $btc_usdt);
                  } elseif($inv->soldmarket == "ETH")
                  {
                      $inv->sold_for_usd = $trade->total * ($btc_usd / $btc_eth);
                  }
                  $inv->btc_price_bought_usd = $investment->btc_price_bought_usd;
                  $inv->btc_price_bought_eur = $investment->btc_price_bought_eur;
                  $inv->btc_price_bought_gbp = $investment->btc_price_bought_gbp;
                  $inv->btc_price_bought_eth = $investment->btc_price_bought_eth;
                  $inv->btc_price_bought_usdt = $investment->btc_price_bought_usdt;


                  // sales
                  $inv->btc_price_sold_usd = $btc_usd;
                  $inv->btc_price_sold_eur = $btc_eur;
                  $inv->btc_price_sold_gbp = $btc_gbp;
                  $inv->btc_price_sold_eth = $btc_eth;
                  $inv->btc_price_sold_usdt = $btc_usdt;

                  $inv->type = $investment->type;
                  $inv->save();
                  $lasttrade = $trade->id;

                  $investment->bought_for -= ($amount * $investment->bought_at);

                  if($investment->market == "BTC")
                  {
                    $investment->bought_for_usd = $investment->bought_for * $investment->btc_price_bought_usd;
                  } elseif($investment->market == "ETH")
                  {
                    $investment->bought_for_usd = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $investment->bought_for;
                  } elseif($investment->market == "USDT")
                  {
                    $investment->bought_for_usd = ($investment->btc_price_bought_usd / $investment->btc_price_bought_usdt) * $investment->bought_for;
                  }

                  $investment->edited = 1;
                  $investment->amount -= $amount;
                  if($investment->amount <= 0.000001)
                  {
                    $investment->delete();
                  } else
                  {
                    $investment->save();
                  }


                  $trade->handled = 1;
                  $trade->save();
                  $amount = 0;







              } else
              {
                $date = strtotime($trade->date);
                $newformat = date('Y-m-d', $date);
                $date = $newformat;

                  // Remove from the buy order
                  $investment->bought_for -= ($amount * $investment->bought_at);
                  $investment->edited = 1;
                  if($investment->market == "BTC")
                  {
                    $investment->bought_for_usd = $investment->bought_for * $investment->btc_price_bought_usd;
                  } elseif($investment->market == "ETH")
                  {
                    $investment->bought_for_usd = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $investment->bought_for;
                  } elseif($investment->market == "USDT")
                  {
                    $investment->bought_for_usd = ($investment->btc_price_bought_usd / $investment->btc_price_bought_usdt) * $investment->bought_for;
                  }
                  $investment->amount -= $amount;
                  if($investment->amount <= 0.000001)
                  {
                    $investment->delete();
                  } else
                  {
                    $investment->save();
                  }

                  $sale->amount += $amount;
                  $sale->bought_for += ($amount * $investment->bought_at);
                  $sale->bought_at = $sale->bought_for / $sale->amount;
                  $sale->btc_price_bought_usd = ($sale->btc_price_bought_usd + $investment->btc_price_bought_usd) / 2;
                  $sale->btc_price_bought_eur = ($sale->btc_price_bought_eur + $investment->btc_price_bought_eur) / 2;
                  $sale->btc_price_bought_gbp = ($sale->btc_price_bought_gpb + $investment->btc_price_bought_gpb) / 2;
                  $sale->btc_price_bought_eth = ($sale->btc_price_bought_eth + $investment->btc_price_bought_eth) / 2;
                  $sale->btc_price_bought_usdt = ($sale->btc_price_bought_usdt + $investment->btc_price_bought_usdt) / 2;






                  if($sale->market == "BTC")
                  {
                    $sale->bought_for_usd = $sale->bought_for * $sale->btc_price_bought_usd;
                  } elseif($sale->market == "ETH")
                  {
                    $sale->bought_for_usd = ($sale->btc_price_bought_usd / $sale->btc_price_bought_eth) * $sale->bought_for;
                  } elseif($sale->market == "USDT")
                  {
                    $sale->bought_for_usd = ($sale->btc_price_bought_usd / $sale->btc_price_bought_usdt) * $sale->bought_for;
                  }

                  if($trade->id != $lasttrade)
                  {
                  $sale->sold_for += $trade->total;

                  if($sale->soldmarket == "BTC")
                  {
                    $sale->sold_for_usd = $sale->sold_for * $sale->btc_price_sold_usd;
                  } elseif($sale->soldmarket == "ETH")
                  {
                    $sale->sold_for_usd = ($sale->btc_price_sold_usd / $sale->btc_price_sold_eth) * $sale->sold_for;
                  } elseif($sale->soldmarket == "USDT")
                  {
                    $sale->sold_for_usd = ($sale->btc_price_sold_usd / $sale->btc_price_sold_usdt) * $sale->sold_for;
                  }
                  }

                  $lasttrade = $trade->id;
                  $sale->save();

                  $amount = 0;

                  $trade->handled = 1;
                  $trade->save();







              }
            } elseif($investment->amount <= $amount && $amount > 0)
            {
              $sale = BittrexInvestment::where([['saleid', '=', $trade->tradeid], ['userid', '=', $this->userid], ['soldmarket', '=', $trade->market]])->first();

              if(!$sale)
              {
                $date = strtotime($trade->date);
                $newformat = date('Y-m-d', $date);
                $date = $newformat;

                $historical = History::getHistorical($date);
                $btc_usd = $historical->USD;
                $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
                $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
                $btc_usdt = $historical->USD;
                $btc_eth = $historical->ETH;
                  $investment->bought_for = $investment->bought_at * $investment->amount;
                  $investment->soldmarket = $trade->market;
                  $investment->sold_at = $trade->price;
                  $investment->sold_for = $trade->total;
                  $investment->btc_price_sold_usd = $btc_usd;
                  $investment->btc_price_sold_eur = $btc_eur;
                  $investment->btc_price_sold_gbp = $btc_gbp;
                  $investment->btc_price_sold_eth = $btc_eth;
                  $investment->btc_price_sold_usdt = $btc_usdt;

                  if($investment->soldmarket == "BTC")
                  {
                    $investment->sold_for_usd = $investment->sold_for * $investment->btc_price_sold_usd;
                  } elseif($investment->soldmarket == "ETH")
                  {
                    $investment->sold_for_usd = ($investment->btc_price_sold_usd / $investment->btc_price_sold_eth) * $investment->sold_for;
                  } elseif($investment->soldmarket == "USDT")
                  {
                    $investment->sold_for_usd = ($investment->btc_price_sold_usd / $investment->btc_price_sold_usdt) * $investment->sold_for;
                  }




                  $investment->date_sold = $trade->date;
                  $investment->saleid = $trade->tradeid;
                  $investment->save();
                  $lasttrade = $trade->id;


                  $amount -= $investment->amount;
                  if($amount <= 0)
                  {
                  $trade->handled = 1;
                  $trade->save();
                  }
              } else
              {

                if($sale->amount != 0)
                {
                  $sale->amount += $investment->amount;
                  $sale->bought_for += ($investment->amount * $investment->bought_at);
                  $sale->bought_at = $sale->bought_for / $sale->amount;
                  $sale->btc_price_bought_usd = ($sale->btc_price_bought_usd + $investment->btc_price_bought_usd) / 2;
                  $sale->btc_price_bought_eur = ($sale->btc_price_bought_eur + $investment->btc_price_bought_eur) / 2;
                  $sale->btc_price_bought_gbp = ($sale->btc_price_bought_gpb + $investment->btc_price_bought_gpb) / 2;
                  $sale->btc_price_bought_eth = ($sale->btc_price_bought_eth + $investment->btc_price_bought_eth) / 2;
                  $sale->btc_price_bought_usdt = ($sale->btc_price_bought_usdt + $investment->btc_price_bought_usdt) / 2;


                  if($sale->market == "BTC")
                  {
                    $sale->bought_for_usd = $sale->bought_for * $sale->btc_price_bought_usd;
                  } elseif($sale->market == "ETH")
                  {
                    $sale->bought_for_usd = ($sale->btc_price_bought_usd / $sale->btc_price_bought_eth) * $sale->bought_for;
                  } elseif($sale->market == "USDT")
                  {
                    $sale->bought_for_usd = ($sale->btc_price_bought_usd / $sale->btc_price_bought_usdt) * $sale->bought_for;
                  }
                }
                if($trade->id != $lasttrade){
                    $sale->sold_for += ($trade->total);

                    if($sale->soldmarket == "BTC")
                    {
                      $sale->sold_for_usd = $sale->sold_for * $sale->btc_price_sold_usd;
                    } elseif($sale->soldmarket == "ETH")
                    {
                      $sale->sold_for_usd = ($sale->btc_price_sold_usd / $sale->btc_price_sold_eth) * $sale->sold_for;
                    } elseif($sale->soldmarket == "USDT")
                    {
                      $sale->sold_for_usd = ($sale->btc_price_sold_usd / $sale->btc_price_sold_usdt) * $sale->sold_for;
                    }
                }
                $lasttrade = $trade->id;

                $sale->save();

                $debugamount5 = $amount;
                $amount -= $investment->amount;
                $investment->amount -= $debugamount5;
                $investment->edited = 1;
                if($investment->amount <= 0.000001){
                    echo $investment->amount." ROW: 412 ID: ".$investment->id." TRADEID: ".$trade->id."<br>";
                    $investment->delete();
                } else {
                    echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                    $investment->save();
                }




                $trade->handled = 1;
                $trade->save();


              }




            }
          }

          if($amount > 0.001 && Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->exists())
          {
            $date = strtotime($trade->date);
            $newformat = date('Y-m-d', $date);
            $date = $newformat;

            $historical = History::getHistorical($date);
            $btc_usd = $historical->USD;
            $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
            $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
            $btc_usdt = $historical->USD;
            $btc_eth = $historical->ETH;

            $investment = new BittrexInvestment;
            $investment->userid = $this->userid;
            $investment->currency = $trade->currency;
            $investment->market = 'Deposit';
            $investment->soldmarket = $trade->market;
            $investment->orderid = "Deposit-".$trade->tradeid;
            $investment->saleid = $trade->tradeid;
            $investment->date_sold = $trade->date;
            $investment->bought_at = 0;
            $investment->sold_at = $trade->price;
            $investment->amount = $amount;
            $investment->bought_for = 0;
            $investment->bought_for_usd = 0;
            $investment->sold_for = $amount * $trade->price;
            $investment->type = "DEPOSIT_SALE".$amount;
            $investment->btc_price_bought_usd = 0;
            $investment->btc_price_bought_eur = 0;
            $investment->btc_price_bought_eth = 0;
            $investment->btc_price_bought_usdt = 0;
            $investment->btc_price_bought_gbp = 0;

            // sales
            $investment->btc_price_sold_usd = $btc_usd;
            $investment->btc_price_sold_eur = $btc_eur;
            $investment->btc_price_sold_gbp = $btc_gbp;
            $investment->btc_price_sold_eth = $btc_eth;
            $investment->btc_price_sold_usdt = $btc_usdt;

            if($investment->soldmarket == "BTC")
            {
              $investment->sold_for_usd = $investment->sold_for * $investment->btc_price_sold_usd;
            } elseif($investment->soldmarket == "ETH")
            {
              $investment->sold_for_usd = ($investment->btc_price_sold_usd / $investment->btc_price_sold_eth) * $investment->sold_for;
            } elseif($investment->soldmarket == "USDT")
            {
              $investment->sold_for_usd = ($investment->btc_price_sold_usd / $investment->btc_price_sold_usdt) * $investment->sold_for;
            }

            $investment->save();

            $trade->handled = 1;
            $trade->save();


          }




        } else
        {
            if(Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->exists())
            {
              $date = strtotime($trade->date);
              $newformat = date('Y-m-d', $date);
              $date = $newformat;

              $historical = History::getHistorical($date);
              $btc_usd = $historical->USD;
              $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
              $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
              $btc_usdt = $historical->USD;
              $btc_eth = $historical->ETH;

              $investment = new BittrexInvestment;
              $investment->userid = $this->userid;
              $investment->currency = $trade->currency;
              $investment->market = 'Deposit';
              $investment->soldmarket = $trade->market;
              $investment->orderid = "Deposit-".$trade->tradeid;
              $investment->saleid = $trade->tradeid;
              $investment->date_sold = $trade->date;
              $investment->bought_at = 0;
              $investment->sold_at = $trade->price;
              $investment->amount = $trade->amount;
              $investment->bought_for = 0;
              $investment->bought_for_usd = 0;
              $investment->sold_for = $trade->total;
              $investment->type = "DEPOSIT_SALE";
              $investment->btc_price_bought_usd = 0;
              $investment->btc_price_bought_eur = 0;
              $investment->btc_price_bought_eth = 0;
              $investment->btc_price_bought_usdt = 0;
              $investment->btc_price_bought_gbp = 0;

              // sales
              $investment->btc_price_sold_usd = $btc_usd;
              $investment->btc_price_sold_eur = $btc_eur;
              $investment->btc_price_sold_gbp = $btc_gbp;
              $investment->btc_price_sold_eth = $btc_eth;
              $investment->btc_price_sold_usdt = $btc_usdt;

              if($investment->soldmarket == "BTC")
              {
                $investment->sold_for_usd = $investment->sold_for * $investment->btc_price_sold_usd;
              } elseif($investment->soldmarket == "ETH")
              {
                $investment->sold_for_usd = ($investment->btc_price_sold_usd / $investment->btc_price_sold_eth) * $investment->sold_for;
              } elseif($investment->soldmarket == "USDT")
              {
                $investment->sold_for_usd = ($investment->btc_price_sold_usd / $investment->btc_price_sold_usdt) * $investment->sold_for;
              }

              $investment->save();

              $trade->handled = 1;
              $trade->save();


            }
        }
        }
        if($trade->handled == 1)
        {
          if(Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->exists())
          {
            $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->first();
            $balance->amount -= $trade->amount;
            $balance->save();
          }

          if(Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->market], ['exchange', '=', 'Bittrex']])->first())
          {
            $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->market], ['exchange', '=', 'Bittrex']])->first();
            $balance->amount += $trade->total;
            $balance->save();
          } else {
            $balance = new Balance;
            $balance->exchange = "Bittrex";
            $balance->userid = $this->userid;
            $balance->currency = $trade->market;
            $balance->amount = $trade->total;
            $balance->save();
        }
        }
      }

      /* End Insert Sells */

      /* Import Balances */


      try{
      $nonce=time();
      $uri='https://bittrex.com/api/v1.1/account/getbalances?apikey='.$apikey.'&nonce='.$nonce;
      $sign=hash_hmac('sha512',$uri,$apisecret);
      $res = $client->request('GET', $uri, [
      'headers' => [
      'apisign' => $sign,
      ]]);
      $response = $res->getBody();
      $balances = json_decode($response, true);
      }  catch (\GuzzleHttp\Exception\ClientException $e) {
        event(new PushEvent('Undefined error occured, please contact Edwin.', 'error', $this->userid));
        return true;
      }

      foreach($balances['result'] as $balance)
      {
        if($balance['Currency'] == "BCC")
        {
          $balance['Currency'] = "BCH";
        } elseif($balance['Currency'] == "ANS")
        {
          $balance['Currency'] = "NEO";
        } elseif($balance['Currency'] == "SEC")
        {
          $balance['Currency'] = "SAFEX";
        }

        if($balance['Balance'] > 0 && $balance['Balance'] != 0)
        {
          if(Balance::where([['userid', '=', $this->userid], ['currency', '=', $balance['Currency']], ['exchange', '=', 'Bittrex']])->exists())
          {
            $b = Balance::where([['userid', '=', $this->userid], ['currency', '=', $balance['Currency']], ['exchange', '=', 'Bittrex']])->first();
            $b->amount = $balance['Balance'];
            if($b->amount > 0)
            {
              $b->save();
            } else {
              $b->delete();
            }
          } else
          {
            $b = new Balance;
            $b->exchange = "Bittrex";
            $b->currency = $balance['Currency'];
            $b->amount = $balance['Balance'];
            $b->userid = $this->userid;
            $b->save();
          }
        } else {
          if(Balance::where([['userid', '=', $this->userid], ['currency', '=', $balance['Currency']], ['exchange', '=', 'Bittrex']])->exists())
          {
            $b = Balance::where([['userid', '=', $this->userid], ['currency', '=', $balance['Currency']], ['exchange', '=', 'Bittrex']])->first();
            $b->amount = $balance['Balance'];
            if($b->amount > 0)
            {
              $b->save();
            } else {
              $b->delete();
            }
          }
        }
      }


      Cache::forget('investments'.$this->userid);
      Cache::forget('b_investments'.$this->userid);
      Cache::forget('deposits'.$this->userid);
      Cache::forget('withdraws'.$this->userid);
      Cache::forget('balances'.$this->userid);
      Cache::forget('balances-summed2'.$this->userid);
      Cache::forget('deposits'.$this->userid);
      Cache::forget('withdraws'.$this->userid);

      Cache::forget($this->cachekey);

      event(new PushEvent('Your import is complete! Go to your investments to see your import', 'success', $this->userid));
      return;
      }
}
