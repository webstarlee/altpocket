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
use App\PoloInvestment;
use App\PoloTrade;
use App\Balance;

use App\Events\PushEvent;

use App\Multiplier;
use App\Key;
use App\User;
use App\History;

use Cache;
class NewPoloImport implements ShouldQueue
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
      $this->cachekey = 'Polo-Import-Cache:'.$this->userid;
      if(Cache::has($this->cachekey)) {
         event(new PushEvent('You already have an import running!', 'error', $this->userid));
         return $this->userid;
     }

      Cache::put($this->cachekey, 1, 5);

      /* GET API KEY */

      if(Key::where([['userid', '=', $this->userid], ['exchange', '=', 'Poloniex']])->exists())
      {
        $key = Key::where([['userid', '=', $this->userid], ['exchange', '=', 'Poloniex']])->first();
      } else
      {
        event(new PushEvent('No API keys found, please add your Poloniex API keys.', 'error', $this->userid));
        Cache::forget($this->cachekey);
      }
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);

      /* END API KEY */

      /* GUZZLE */

      $client = new \GuzzleHttp\Client();

      /* GUZZLE */


      /* Import Deposits */

      try {
          $nonce = round(microtime(true) * 1000);
          $req = ['command' => 'returnDepositsWithdrawals', 'start' => '1420070400', 'end' => time(), 'nonce' => $nonce];
          $post_data = http_build_query($req, '', '&');
          $sign = hash_hmac('sha512', $post_data, $apisecret);
          $res = $client->request('POST', 'https://poloniex.com/tradingApi', [
          'headers' => [
          'Sign' => $sign,
          'Key' => $apikey
        ], 'form_params' => ['command' => 'returnDepositsWithdrawals', 'start' => '1420070400', 'end' => time(), 'nonce' => $nonce]]);
          $response = $res->getBody();
          $deposits = json_decode($response, true);

          event(new PushEvent('Your import has started!', 'success', $this->userid));

          foreach($deposits as $key => $deposit)
          {
            foreach($deposit as $key2 => $d)
            {
              if($key != "withdrawals" && $d['status'] == "COMPLETE")
              {
                if(!Deposit::where('txid', $this->userid . $d['txid'])->exists())
                {
                  $date = date('Y-m-d H:i:s', $d['timestamp']);
                  $date2 = date('Y-m-d', $d['timestamp']);
                  // Get bitcoin price of day:
                  $historical = History::getHistorical($date2);
                  $btc_usd = $historical->USD;
                  $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
                  $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
                  $btc_usdt = $historical->USD;
                  $btc_eth = $historical->ETH;

                    $client = new \GuzzleHttp\Client();
                    $res = $client->request('GET', 'https://poloniex.com/public?command=returnChartData&currencyPair=BTC_'.$d['currency'].'&start='.strtotime($date2).'&end='.strtotime($date2).'&period=14400');
                    $response = $res->getBody();
                    $prices = json_decode($response, true);
                    $value = 0;

                    if($d['currency'] != "BTC")
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





                  $deposit = new Deposit;
                  $deposit->userid = $this->userid;
                  $deposit->exchange = "Poloniex";
                  $deposit->txid = $this->userid . $d['txid'];
                  $deposit->date = date('Y-m-d H:i:s', $d['timestamp']);
                  $deposit->currency = $d['currency'];
                  $deposit->amount = $d['amount'];
                  $deposit->btc_price_deposit_usd = $btc_usd;
                  $deposit->btc_price_deposit_eur = $btc_eur;
                  $deposit->btc_price_deposit_gbp = $btc_gbp;
                  $deposit->btc_price_deposit_eth = $btc_eth;
                  $deposit->btc_price_deposit_usdt = $btc_usdt;
                  $deposit->price = $value;
                  $deposit->save();

                  if(!Balance::where([['userid', '=', $this->userid], ['currency', '=', $d['currency']], ['exchange', '=', 'poloniex']])->exists())
                  {
                    $balance = new Balance;
                    $balance->exchange = "Poloniex";
                    $balance->userid = $this->userid;
                    $balance->currency = $d['currency'];
                    $balance->amount = $d['amount'];
                    $balance->save();
                  } else
                  {
                    $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $d['currency']], ['exchange', '=', 'poloniex']])->first();
                    $balance->amount += $d['amount'];
                    $balance->save();
                  }


                }



              }
            }


          }


      } catch(\GuzzleHttp\Exception\RequestException $e){
        if(strpos($e->getMessage(), 'Invalid API key\/secret pair.') !== false)
        {
          Cache::forget($this->cachekey);
          event(new PushEvent('Invalid API key combination.', 'error', $this->userid));
          return true;
        } elseif(strpos($e->getMessage(), 'Nonce') !== false)
        {
          Cache::forget($this->cachekey);
          event(new PushEvent('API Key used somewhere else, please make a new one!', 'error', $this->userid));
          return true;
        } elseif(strpos($e->getMessage(), 'Permission denied') !== false)
        {
          Cache::forget($this->cachekey);
          event(new PushEvent('Insufficient permissions, please turn of IP Restriction.', 'error', $this->userid));
          return true;
        } else {
          Cache::forget($this->cachekey);
          event(new PushEvent($e->getMessage(), 'error', $this->userid));
          return true;
        }
      } catch(\GuzzleHttp\Exception\ClientException $e) {
        event(new PushEvent($e->getMessage(), 'error', $this->userid));
        Cache::forget($this->cachekey);
        return true;
      } catch(\GuzzleHttp\Exception\BadResponseException $e) {
        event(new PushEvent($e->getMessage(), 'error', $this->userid));
        Cache::forget($this->cachekey);
        return true;
      } catch(\GuzzleHttp\Exception\ServerException $e) {
        event(new PushEvent($e->getMessage(), 'error', $this->userid));
        Cache::forget($this->cachekey);
        return true;
      }

      /* End Import Deposits */

      /* Insert Trades */

      try {
        $nonce = round(microtime(true) * 1000);
        $req = ['command' => 'returnTradeHistory', 'currencyPair' => 'all', 'start' => '1420070400', 'nonce' => $nonce];
        $post_data = http_build_query($req, '', '&');
        $sign = hash_hmac('sha512', $post_data, $apisecret);
        $res = $client->request('POST', 'https://poloniex.com/tradingApi', [
        'headers' => [
        'Sign' => $sign,
        'Key' => $apikey
        ], 'form_params' => ['command' => 'returnTradeHistory', 'currencyPair' => 'all', 'start' => '1420070400', 'nonce' => $nonce]]);
        $response = $res->getBody();
        $trades = json_decode($response, true);
      } catch(GuzzleHttp\Exception\ClientException $e){
        event(new PushEvent('Undefined error occured, please contact Edwin.', 'error', $this->userid));
        return true;
      }

      foreach($trades as $key => $trade)
      {
        foreach($trade as $t)
        {
          if($t['category'] == "exchange")
          {
            // Make sure the trade doesn't exist.
            if(!PoloTrade::where('tradeid', $this->userid . $t['tradeID'])->exists())
            {
              // Trade Variables
              $currencies = explode('_', $key);
              $fee = number_format($t['amount'] * $t['fee'], 8, '.', '');

              // Makes sure the amount is correct
              if($t['type'] == "buy"){
                  $amount = number_format($t['amount'] - $fee, 8 ,'.', '');
              } else {
                  $amount = $t['amount']; // number_format($amount, 7)
              }

              // Insert data into the database
              $trade = new PoloTrade;
              $trade->userid = $this->userid;
              $trade->tradeid = $this->userid . $t['tradeID'];
              $trade->orderid = $t['orderNumber'];
              $trade->date = $t['date'];
              $trade->type = $t['type'];
              $trade->market = $currencies[0];
              $trade->currency = $currencies[1];
              $trade->price = $t['rate'];
              $trade->amount = $amount;
              $trade->fee = $fee;
              $trade->total = $t['total'];
              $trade->save();
          }
          }
        }
      }

      /* End Trades */

      /* Insert Buys */

      $trades = PoloTrade::where([['userid', '=', $this->userid], ['handled', '=', '0'], ['type', '=', 'buy']])->orderBy('date')->get();
      event(new PushEvent(count($trades) . ' new buy orders imported, starting conversion to investments.', 'success', $this->userid));
      foreach($trades as $trade)
      {
        $investment = PoloInvestment::where('orderid', $trade->orderid)->first();
        $date = strtotime($trade->date);
        $newformat = date('Y-m-d', $date);
        $date = $newformat;

        $historical = History::getHistorical($date);
        $btc_usd = $historical->USD;
        $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
        $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
        $btc_usdt = $historical->USD;
        $btc_eth = $historical->ETH;


        if($trade->market == "ETH")
        {
          if(PoloInvestment::where([['currency', '=', 'ETH'], ['date_sold', '=', null], ['userid', '=', $this->userid]])->exists())
          {
          $test = PoloInvestment::where([['currency', '=', 'ETH'], ['date_sold', '=', null], ['userid', '=', $this->userid]])->first();
          $test->amount -= $trade->total;
          $test->bought_for = $test->amount * $test->bought_at;
          $test->edited = 1;
          $test->save();
          }
        }




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
          $investment = new PoloInvestment;

          $investment->userid = $this->userid;
          $investment->currency = $trade->currency;
          $investment->market = $trade->market;


          $investment->orderid = $trade->orderid;
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

        if(!Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'Poloniex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Poloniex";
          $balance->userid = $this->userid;
          $balance->currency = $trade->currency;
          $balance->amount = $trade->amount;
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'Poloniex']])->first();
          $balance->amount += $trade->amount;
          $balance->save();
        }

        if(!Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->market], ['exchange', '=', 'Poloniex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Poloniex";
          $balance->userid = $this->userid;
          $balance->currency = $trade->market;
          $balance->amount = 0 - $trade->total;
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->market], ['exchange', '=', 'Poloniex']])->first();
          $balance->amount -= $trade->total;
          $balance->save();
        }





      }

      /* End Insert Buys */

      /* Insert Sells */

      $trades = PoloTrade::where([['userid', '=', $this->userid], ['handled', '=', '0'], ['type', '=', 'sell']])->orderBy('date')->get();
      event(new PushEvent(count($trades) . ' new sell orders imported, starting conversion to investments.', 'success', $this->userid));
      $lasttrade = 0;

      foreach($trades as $trade)
      {
        $amount = $trade->amount;

        $investments = PoloInvestment::where([['currency', '=', $trade->currency], ['userid', '=', $this->userid], ['saleid', '=', null], ['date_bought', '<=', $trade->date]])->orderBy('date_bought')->get();
        //Make sure the amount is more than 0 otherwise we do not continue!!
        if($amount > 0)
        {
          if(count($investments) >= 1)
          {
          foreach($investments as $investment)
          {
            if($investment->amount >= $amount && $amount > 0)
            {
              $sale = PoloInvestment::where([['saleid', '=', $trade->orderid], ['userid', '=', $this->userid], ['soldmarket', '=', $trade->market]])->first();

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

                  $inv = new PoloInvestment;
                  $inv->userid = $this->userid;
                  $inv->currency = $investment->currency;
                  $inv->market = $investment->market;
                  $inv->soldmarket = $trade->market;
                  $inv->orderid = $investment->orderid;
                  $inv->saleid = $trade->orderid;
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



                  if($trade->id != $lasttrade)
                  {
                  $sale->sold_for += $trade->total;


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

                  $lasttrade = $trade->id;
                  $sale->save();

                  $amount = 0;

                  $trade->handled = 1;
                  $trade->save();

              }
            } elseif($investment->amount <= $amount && $amount > 0)
            {
              $sale = PoloInvestment::where([['saleid', '=', $trade->orderid], ['userid', '=', $this->userid], ['soldmarket', '=', $trade->market]])->first();

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
                  $investment->saleid = $trade->orderid;
                  $investment->save();
                  $lasttrade = $trade->id;


                  $amount -= $investment->amount;

                  $trade->handled = 1;
                  $trade->save();
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
                    echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                $investment->delete();
                } else {
                    echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                    $investment->save();
                }




                $trade->handled = 1;
                $trade->save();



              }





            }
            if($investment->amount <= 0.000001){
                $investment->delete();
            }
          }
        } else
        {
            if(Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'poloniex']])->exists())
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

              $investment = new PoloInvestment;
              $investment->userid = $this->userid;
              $investment->currency = $trade->currency;
              $investment->market = 'Deposit';
              $investment->soldmarket = $trade->market;
              $investment->orderid = "Deposit-".$trade->orderid;
              $investment->saleid = $trade->orderid;
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
          $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'Poloniex']])->first();
          $balance->amount -= $trade->amount;
          $balance->save();


          if(Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->market], ['exchange', '=', 'Poloniex']])->first())
          {
          $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->market], ['exchange', '=', 'Poloniex']])->first();
          $balance->amount += $trade->total;
          $balance->save();
        } else {
          $balance = new Balance;
          $balance->exchange = "Poloniex";
          $balance->userid = $this->userid;
          $balance->currency = $trade->market;
          $balance->amount = $trade->total;
          $balance->save();
        }
        }
      }

      /* End Insert Sells */

      /* Import Withdrawals */

      try {
        $nonce = round(microtime(true) * 1000);
        $req = ['command' => 'returnDepositsWithdrawals', 'start' => '1420070400', 'end' => time(), 'nonce' => $nonce];
        $post_data = http_build_query($req, '', '&');
        $sign = hash_hmac('sha512', $post_data, $apisecret);
        $res = $client->request('POST', 'https://poloniex.com/tradingApi', [
        'headers' => [
        'Sign' => $sign,
        'Key' => $apikey
      ], 'form_params' => ['command' => 'returnDepositsWithdrawals', 'start' => '1420070400', 'end' => time(), 'nonce' => $nonce]]);
        $response = $res->getBody();
        $withdraws = json_decode($response, true);
      } catch(GuzzleHttp\Exception\ClientException $e){
        event(new PushEvent('Undefined error occured, please contact Edwin.', 'error', $this->userid));
        return true;
      }

      foreach($withdraws as $key => $withdraw)
      {
        if($key == "withdrawals")
        {
          foreach($withdraw as $w)
          {
            if(strpos($w['status'], 'COMPLETE') !== false)
            {
              if(!Withdraw::where('txid', $this->userid . $w['withdrawalNumber'])->exists())
              {
                $date = date('Y-m-d H:i:s', $w['timestamp']);
                $date2 = date('Y-m-d', $w['timestamp']);
                // Get bitcoin price of day:
                $historical = History::getHistorical($date2);
                $btc_usd = $historical->USD;
                $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
                $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
                $btc_usdt = $historical->USD;
                $btc_eth = $historical->ETH;

                  $client = new \GuzzleHttp\Client();
                  $res = $client->request('GET', 'https://poloniex.com/public?command=returnChartData&currencyPair=BTC_'.$w['currency'].'&start='.strtotime($date2).'&end='.strtotime($date2).'&period=14400');
                  $response = $res->getBody();
                  $prices = json_decode($response, true);
                  $value = 0;

                  if($w['currency'] != "BTC")
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



                $withdrawal = new Withdraw;
                $withdrawal->exchange = "Poloniex";
                $withdrawal->userid = $this->userid;
                $withdrawal->txid = $this->userid . $w['withdrawalNumber'];
                $withdrawal->date = date('Y-m-d H:i:s', $w['timestamp']);
                $withdrawal->currency = $w['currency'];
                $withdrawal->amount = $w['amount'];
                $withdrawal->btc_price_deposit_usd = $btc_usd;
                $withdrawal->btc_price_deposit_eur = $btc_eur;
                $withdrawal->btc_price_deposit_gbp = $btc_gbp;
                $withdrawal->btc_price_deposit_eth = $btc_eth;
                $withdrawal->btc_price_deposit_usdt = $btc_usdt;
                $withdrawal->price = $value;
                $withdrawal->save();

                if(!Balance::where([['userid', '=', $this->userid], ['currency', '=', $w['currency']], ['exchange', '=', 'Poloniex']])->exists())
                {
                  $balance = new Balance;
                  $balance->exchange = "Poloniex";
                  $balance->userid = $this->userid;
                  $balance->currency = $w['currency'];
                  $balance->amount = 0 - $w['amount'];
                  $balance->save();
                } else
                {

                  $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $w['currency']], ['exchange', '=', 'Poloniex']])->first();
                  $balance->amount -= $w['amount'];
                  $balance->save();
                }


              }
            }

          }
        } else {
          # code...
        }

        $withdraws = Withdraw::where([['userid', '=', $this->userid], ['handled', '=', 0], ['exchange', '=', 'Poloniex']])->get();

        foreach($withdraws as $withdraw)
        {
          $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $withdraw->currency], ['exchange', '=', 'Poloniex']])->first();
          $holdings = 0;
          $investments = PoloInvestment::where([['userid', '=', $this->userid], ['currency', '=', $withdraw->currency], ['saleid', '=', null]])->orderBy('date_bought', 'desc')->get();
          $investmentexact = PoloInvestment::where([['userid', '=', $this->userid], ['currency', '=', $withdraw->currency], ['saleid', '=', null], ['amount', '=', $withdraw->amount]])->orderBy('date_bought', 'desc')->first();
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
            $newbalance = 0;
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
                }



              } else {
                $withdraw->handled = 1;
                $withdraw->save();
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

      /* End Import Withdrawals */

      /* Import Balances */

      try {
          $nonce = round(microtime(true) * 1000);
          $req = ['command' => 'returnCompleteBalances', 'nonce' => $nonce, 'account' => 'all'];
          $post_data = http_build_query($req, '', '&');
          $sign = hash_hmac('sha512', $post_data, $apisecret);
          $res = $client->request('POST', 'https://poloniex.com/tradingApi', [
          'headers' => [
          'Sign' => $sign,
          'Key' => $apikey
        ], 'form_params' => ['command' => 'returnCompleteBalances', 'nonce' => $nonce, 'account' => 'all']]);
          $response = $res->getBody();
          $balances = json_decode($response, true);
      } catch(GuzzleHttp\Exception\ClientException $e){
        event(new PushEvent('Undefined error occured, please contact Edwin.', 'error', $this->userid));
        return true;
      }

      $bs = Balance::where([['userid', '=', $this->userid], ['exchange', '=', 'Poloniex']])->get();

      foreach($bs as $bss)
      {
        $bss->delete();
      }

      foreach($balances as $key => $balance)
      {
        if($balance['available'] + $balance['onOrders'] > 0)
        {
        if(Balance::where([['userid', '=', $this->userid], ['currency', '=', $key], ['exchange', '=', 'Poloniex']])->exists())
        {
          $b = Balance::where([['userid', '=', $this->userid], ['currency', '=', $key], ['exchange', '=', 'Poloniex']])->first();
          $b->amount = $balance['available'] + $balance['onOrders'];
          $b->save();
        } else
        {
          $b = new Balance;
          $b->exchange = "Poloniex";
          $b->currency = $key;
          $b->amount = $balance['available'] + $balance['onOrders'];
          $b->userid = $this->userid;
          $b->save();
        }
      }
      }


        /* End Import Balances */

        /* Start Reset Cache */

        Cache::forget('investments'.$this->userid);
        Cache::forget('p_investments'.$this->userid);
        Cache::forget('deposits'.$this->userid);
        Cache::forget('withdraws'.$this->userid);
        Cache::forget('balances'.$this->userid);
        Cache::forget('balances-summed2'.$this->userid);
        Cache::forget('deposits'.$this->userid);
        Cache::forget('withdraws'.$this->userid);

        /* End Reset Cache */

        Cache::forget($this->cachekey);

        event(new PushEvent('Your import is complete! Go to your investments to see your import', 'success', $this->userid));
        return;

}
}
}
