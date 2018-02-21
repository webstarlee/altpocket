<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Deposit;
use App\Withdraw;
use App\Investment;
use App\Trade;
use App\Balance;

use App\Events\PushEvent;

use App\Multiplier;
use App\Key;
use App\User;
use App\History;
use App\CoinBase;

use Cache;

class NewCoinbaseImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      // Prepare import
      $client = new \GuzzleHttp\Client();
      $key = Key::where([['userid', '=', $this->userid], ['exchange', '=', 'Coinbase']])->first();

      CoinBase::checkExpiry($key);

      $access_token = decrypt($key->public); // Access token
      $refresh_token = decrypt($key->private); // Refresh token

      /* Balances and other stuff */

      try{
        $url = 'https://api.coinbase.com/v2/accounts';

        $res = $client->request('GET', $url, [
        'headers' => [
        'Authorization' => 'Bearer '.$access_token,
        'CB-VERSION' => '2017-10-23'
        ]]);
      $response = $res->getBody();
      $response = json_decode($response, true);

      //Let's get it
      event(new PushEvent('Your import has started!', 'success', $this->userid));

      foreach($response['data'] as $key => $b)
      {
            if($b['balance']['amount'] > 0)
            {
              if(!Balance::where([['userid', '=', $this->userid], ['currency', '=', $b['currency']['code']], ['exchange', '=', 'Coinbase']])->exists())
              {
                $balance = new Balance;
                $balance->userid = $this->userid;
                $balance->exchange = "Coinbase";
                //echo $b['currency'];
                //var_dump($b['currency']);
                $balance->currency = $b['currency']['code'];
                $balance->amount = $b['balance']['amount'];
                $balance->save();
              } else {
                $balance = Balance::where([['userid', '=', $this->userid], ['currency', '=', $b['currency']['code']], ['exchange', '=', 'Coinbase']])->first();
                $balance->amount = $b['balance']['amount'];
                $balance->save();
              }
          }


            // Trades now directly somehow.. [BUYS]
            try{
              $url = 'https://api.coinbase.com/v2/accounts/'.$b['id'].'/buys';

              $res = $client->request('GET', $url, [
              'headers' => [
              'Authorization' => 'Bearer '.$access_token,
              'CB-VERSION' => '2017-10-23'
              ]]);
            $response2 = $res->getBody();
            $response2 = json_decode($response2, true);
          }  catch (\GuzzleHttp\Exception\ClientException $e) {

          }
          foreach($response2['data'] as $buy)
          {
            if(!Trade::where('tradeid', $buy['id'])->exists() && $buy['status'] != "canceled")
            {
              $trade = new Trade;
              $trade->userid = $this->userid;
              $trade->tradeid = $buy['id'];
              $trade->market = $buy['total']['currency'];
              $trade->currency = $buy['amount']['currency'];
              $trade->handled = 0;
              $trade->amount = $buy['amount']['amount'];
              $trade->price = $buy['total']['amount'] / $buy['amount']['amount'];
              $trade->total = $buy['total']['amount'];
              $trade->exchange = "Coinbase";

              $date = strtotime($buy['created_at']);
              $trade->date = date('Y-m-d H:m:s', $date);
              $trade->type = $buy['resource'];
              $trade->fee = $buy['fee']['amount'];
              $trade->save();
            }
          }


          // SELLS
          try{
            $url = 'https://api.coinbase.com/v2/accounts/'.$b['id'].'/sells';

            $res = $client->request('GET', $url, [
            'headers' => [
            'Authorization' => 'Bearer '.$access_token,
            'CB-VERSION' => '2017-10-23'
            ]]);
          $response2 = $res->getBody();
          $response2 = json_decode($response2, true);
        }  catch (\GuzzleHttp\Exception\ClientException $e) {

        }
        foreach($response2['data'] as $sell)
        {
          if(!Trade::where('tradeid', $sell['id'])->exists() && $sell['status'] != "canceled")
          {
            $trade = new Trade;
            $trade->userid = $this->userid;
            $trade->tradeid = $sell['id'];
            $trade->market = $sell['total']['currency'];
            $trade->currency = $sell['amount']['currency'];
            $trade->handled = 0;
            $trade->amount = $sell['amount']['amount'];
            $trade->price = $sell['total']['amount'] / $sell['amount']['amount'];
            $trade->total = $sell['total']['amount'];
            $trade->exchange = "Coinbase";

            $date = strtotime($sell['created_at']);
            $trade->date = date('Y-m-d H:m:s', $date);
            $trade->type = $sell['resource'];
            $trade->fee = $sell['fee']['amount'];
            $trade->save();
          }
        }

          // Transactions now directly somehow..
          try{
            $url = 'https://api.coinbase.com/v2/accounts/'.$b['id'].'/transactions';

            $res = $client->request('GET', $url, [
            'headers' => [
            'Authorization' => 'Bearer '.$access_token,
            'CB-VERSION' => '2017-10-23'
            ]]);
          $response5 = $res->getBody();
          $response5 = json_decode($response5, true);
        }  catch (\GuzzleHttp\Exception\ClientException $e) {

        }

        foreach($response5['data'] as $transaction)
        {
          if($transaction['status'] == "completed")
          {
            echo "hmm";
            if($transaction['type'] == "send" && $transaction['amount']['amount'] > 0 && !Deposit::where('txid', $transaction['id'])->exists())
            {
              $date = strtotime($transaction['created_at']);
              $date2 = date('Y-m-d', $date);
              //Get btc prices
              $client = new \GuzzleHttp\Client();

              $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP,ETH,USDT,LTC&ts='.$date.'&extraParams=Altpocket');
              $response = $res->getBody();
              $prices = json_decode($response, true);
              $btc_usd = 0;
              $btc_eur = 0;
              $btc_gbp = 0;
              $btc_eth = 0;
              $btc_usdt = 0;

                foreach($prices['BTC'] as $key => $price){
                    if($key == "USD")
                    {
                      $btc_usd = $price;
                    } elseif($key == "EUR")
                    {
                      $btc_eur = $price;
                    } elseif($key == "GBP")
                    {
                      $btc_gbp = $price;
                    } elseif($key == "ETH")
                    {
                      $btc_eth = $price;
                    } elseif($key == "USDT")
                    {
                      $btc_usdt = $price;
                    } elseif($key == "LTC")
                    {
                      $btc_ltc = $price;
                    }
                }
              $deposit = new Deposit;
              $deposit->userid = $this->userid;
              $deposit->txid = $transaction['id'];
              $deposit->date = date('Y-m-d H:m:s', $date);
              $deposit->currency = $transaction['amount']['currency'];
              $deposit->amount = $transaction['amount']['amount'];
              if($deposit->currency == "BTC")
              {
                $deposit->price = 1;
              } elseif($deposit->currency == "LTC")
              {
                $deposit->price = 1 / $btc_ltc;
              } elseif($deposit->currency == "ETH")
              {
                $deposit->price = 1 / $btc_eth;
              } else {
                $deposit->price = 1;
              }
              $deposit->handled = 0;
              $deposit->btc_price_deposit_usd = $btc_usd;
              $deposit->btc_price_deposit_eur = $btc_eur;
              $deposit->btc_price_deposit_gbp = $btc_gbp;
              $deposit->btc_price_deposit_eth = $btc_eth;
              $deposit->btc_price_deposit_usdt = $btc_usdt;
              $deposit->exchange = "Coinbase";
              $deposit->save();
            } elseif($transaction['type'] == "send" && $transaction['amount']['amount'] < 0 && !Withdraw::where('txid', $transaction['id'])->exists())
            {
              // WIFH DRAFEN
              $date = strtotime($transaction['created_at']);
              $date2 = date('Y-m-d', $date);
              //Get btc prices
              $client = new \GuzzleHttp\Client();

              $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP,ETH,USDT,LTC&ts='.$date.'&extraParams=Altpocket');
              $response = $res->getBody();
              $prices = json_decode($response, true);
              $btc_usd = 0;
              $btc_eur = 0;
              $btc_gbp = 0;
              $btc_eth = 0;
              $btc_usdt = 0;

                foreach($prices['BTC'] as $key => $price){
                    if($key == "USD")
                    {
                      $btc_usd = $price;
                    } elseif($key == "EUR")
                    {
                      $btc_eur = $price;
                    } elseif($key == "GBP")
                    {
                      $btc_gbp = $price;
                    } elseif($key == "ETH")
                    {
                      $btc_eth = $price;
                    } elseif($key == "USDT")
                    {
                      $btc_usdt = $price;
                    } elseif($key == "LTC")
                    {
                      $btc_ltc = $price;
                    }
                }

            $withdraw = new Withdraw;
            $withdraw->userid = $this->userid;
            $withdraw->exchange = "Coinbase";
            $withdraw->txid = $transaction['id'];
            $withdraw->date = date('Y-m-d H:i:s', $date);
            $withdraw->currency = $transaction['amount']['currency'];
            $withdraw->amount = abs($transaction['amount']['amount']);
            $withdraw->handled = 0;
            if($withdraw->currency == "BTC")
            {
              $withdraw->price = 1;
            } elseif($withdraw->currency == "LTC")
            {
              $withdraw->price = 1 / $btc_ltc;
            } elseif($withdraw->currency == "ETH")
            {
              $withdraw->price = 1 / $btc_eth;
            } else {
              $withdraw->price = 1;
            }
            $withdraw->btc_price_deposit_usd = $btc_usd;
            $withdraw->btc_price_deposit_eur = $btc_eur;
            $withdraw->btc_price_deposit_gbp = $btc_gbp;
            $withdraw->btc_price_deposit_eth = $btc_eth;
            $withdraw->btc_price_deposit_usdt = $btc_usdt;
            $withdraw->save();


            }
          }
        }
    }
      }  catch (\GuzzleHttp\Exception\ClientException $e) {
          event(new PushEvent('No API keys found or invalid combination, please reconnect Coinbase.', 'error', $this->userid));
          return true;
      }
      /* End Balances and other stuff */

      /* Start Insert Buys */

      $trades = Trade::where([['userid', '=', $this->userid], ['handled', '=', '0'], ['type', '=', 'buy'], ['exchange', '=', 'Coinbase']])->orderBy('date')->get();
      event(new PushEvent(count($trades) . ' new buy orders imported, starting conversion to investments.', 'success', $this->userid));
      // clear cache

      foreach($trades as $trade)
      {
        $investment = Investment::where('orderid', $trade->tradeid)->first();
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
          $investment = new Investment;

          $investment->userid = $this->userid;
          $investment->currency = $trade->currency;
          $investment->market = $trade->market;
          $investment->exchange = "Coinbase";


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
      }

      /* End insert buys */

      /* Withdraws */

      $withdraws = Withdraw::where([['userid', '=', $this->userid], ['exchange', '=', 'Coinbase'], ['handled', '=', 0]])->get();

      foreach($withdraws as $withdraw)
      {

          if($withdraw->handled != 1)
          {

          $holdings = 0;
          $investments = Investment::where([['userid', '=', $this->userid], ['currency', '=', $withdraw->currency], ['saleid', '=', null], ['exchange', '=', 'Coinbase']])->orderBy('date_bought', 'desc')->get();
          $investmentexact = Investment::where([['userid', '=', $this->userid], ['currency', '=', $withdraw->currency], ['saleid', '=', null], ['amount', '=', $withdraw->amount], ['exchange', '=', 'Coinbase']])->orderBy('date_bought', 'desc')->first();
          $amount = $withdraw->amount;

          if(!$investmentexact)
          {
            echo "no exact";
          foreach($investments as $investment)
          {
            $holdings += $investment->amount;
          }

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

      /* Sells */

      $trades = Trade::where([['userid', '=', $this->userid], ['handled', '=', '0'], ['type', '=', 'sell'], ['exchange', '=', 'Coinbase']])->orderBy('date')->get();
      event(new PushEvent(count($trades) . ' new sell orders imported, starting conversion to investments.', 'success', $this->userid));
      $lasttrade = 0;


      foreach($trades as $trade)
      {
        $amount = $trade->amount;

        $investments = Investment::where([['currency', '=', $trade->currency], ['userid', '=', $this->userid], ['saleid', '=', null], ['date_bought', '<=', $trade->date], ['exchange', '=', 'Coinbase']])->orderBy('date_bought')->get();





        //Make sure the amount is more than 0 otherwise we do not continue!!
        if($amount > 0)
        {
          if(count($investments) >= 1)
          {
          foreach($investments as $investment)
          {
            if($investment->amount >= $amount && $amount > 0)
            {
              $sale = Investment::where([['saleid', '=', $trade->tradeid], ['userid', '=', $this->userid], ['soldmarket', '=', $trade->market], ['exchange', '=', 'Coinbase']])->first();

              if(!$sale && $investment->amount != 0)
              {
                $date = strtotime($trade->date);
                $newformat = date('Y-m-d', $date);
                $date = $newformat;

                $client = new \GuzzleHttp\Client();

                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP,ETH,USDT&ts='.strtotime($date).'&extraParams=Altpocket');
                $response = $res->getBody();
                $prices = json_decode($response, true);
                $btc_usd = 0;
                $btc_eur = 0;
                $btc_gbp = 0;
                $btc_eth = 0;
                $btc_usdt = 0;

                  foreach($prices['BTC'] as $key => $price){
                      if($key == "USD")
                      {
                        $btc_usd = $price;
                      } elseif($key == "EUR")
                      {
                        $btc_eur = $price;
                      } elseif($key == "GBP")
                      {
                        $btc_gbp = $price;
                      } elseif($key == "ETH")
                      {
                        $btc_eth = $price;
                      } elseif($key == "USDT")
                      {
                        $btc_usdt = $price;
                      }
                  }

                  $inv = new investment;
                  $inv->exchange = "Coinbase";
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
              $sale = Investment::where([['saleid', '=', $trade->tradeid], ['userid', '=', $this->userid], ['soldmarket', '=', $trade->market], ['exchange', '=', 'Coinbase']])->first();

              if(!$sale)
              {
                $date = strtotime($trade->date);
                $newformat = date('Y-m-d', $date);
                $date = $newformat;

                $client = new \GuzzleHttp\Client();

                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP,ETH,USDT&ts='.strtotime($date).'&extraParams=Altpocket');
                $response = $res->getBody();
                $prices = json_decode($response, true);
                $btc_usd = 0;
                $btc_eur = 0;
                $btc_gbp = 0;
                $btc_eth = 0;
                $btc_usdt = 0;

                  foreach($prices['BTC'] as $key => $price){
                      if($key == "USD")
                      {
                        $btc_usd = $price;
                      } elseif($key == "EUR")
                      {
                        $btc_eur = $price;
                      } elseif($key == "GBP")
                      {
                        $btc_gbp = $price;
                      } elseif($key == "ETH")
                      {
                        $btc_eth = $price;
                      } elseif($key == "USDT")
                      {
                        $btc_usdt = $price;
                      }
                  }
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

          if($amount > 0.001 && Balance::where([['userid', '=', $this->userid], ['currency', '=', $trade->currency], ['exchange', '=', 'Coinbase']])->exists())
          {
            $date = strtotime($trade->date);
            $newformat = date('Y-m-d', $date);
            $date = $newformat;

            $client = new \GuzzleHttp\Client();

            $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP,ETH,USDT&ts='.strtotime($date).'&extraParams=Altpocket');
            $response = $res->getBody();
            $prices = json_decode($response, true);
            $btc_usd = 0;
            $btc_eur = 0;
            $btc_gbp = 0;
            $btc_eth = 0;
            $btc_usdt = 0;

              foreach($prices['BTC'] as $key => $price){
                  if($key == "USD")
                  {
                    $btc_usd = $price;
                  } elseif($key == "EUR")
                  {
                    $btc_eur = $price;
                  } elseif($key == "GBP")
                  {
                    $btc_gbp = $price;
                  } elseif($key == "ETH")
                  {
                    $btc_eth = $price;
                  } elseif($key == "USDT")
                  {
                    $btc_usdt = $price;
                  }
              }

            $investment = new Investment;
            $investment->exchange = "Coinbase";
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

              $date = strtotime($trade->date);
              $newformat = date('Y-m-d', $date);
              $date = $newformat;

              $client = new \GuzzleHttp\Client();

              $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP,ETH,USDT&ts='.strtotime($date).'&extraParams=Altpocket');
              $response = $res->getBody();
              $prices = json_decode($response, true);
              $btc_usd = 0;
              $btc_eur = 0;
              $btc_gbp = 0;
              $btc_eth = 0;
              $btc_usdt = 0;

                foreach($prices['BTC'] as $key => $price){
                    if($key == "USD")
                    {
                      $btc_usd = $price;
                    } elseif($key == "EUR")
                    {
                      $btc_eur = $price;
                    } elseif($key == "GBP")
                    {
                      $btc_gbp = $price;
                    } elseif($key == "ETH")
                    {
                      $btc_eth = $price;
                    } elseif($key == "USDT")
                    {
                      $btc_usdt = $price;
                    }
                }

              $investment = new investment;
              $investment->exchange = "Coinbase";
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

      /* End Sells */

      Cache::forget('investments'.$this->userid);
      Cache::forget('c_investments'.$this->userid);
      Cache::forget('deposits'.$this->userid);
      Cache::forget('withdraws'.$this->userid);
      Cache::forget('balances'.$this->userid);
      Cache::forget('balances-summed2'.$this->userid);
      Cache::forget('deposits'.$this->userid);
      Cache::forget('withdraws'.$this->userid);

      event(new PushEvent('Your import is complete! Go to your investments to see your import', 'success', $this->userid));
      return;

    }
}
