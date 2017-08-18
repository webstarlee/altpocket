<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Investment;
use App\Deposit;
use App\Withdraw;
use App\PoloTrade;
use App\BittrexTrade;
use App\PoloInvestment;
use App\BittrexInvestment;
use App\ManualInvestment;
use App\Balance;
use App\Crypto;
use App\Key;
use App\User;
use Alert;
use Redirect;
use App\Jobs\ImportPoloniex;
use App\Jobs\ImportBittrex;
use App\Notifications\ImportComplete;
use App\Events\PushEvent;
use App\CoinBase;
use App\Trade;
use DB;
use Excel;
use Cache;

class ImportController extends Controller
{

    public function addPoloKeys(Request $request)
    {
      $user = Auth::user();
      $public = $request->get('polo_api_public');
      $private = $request->get('polo_api_private');

      if(Key::where([['userid', '=', $user->id], ['exchange', '=', 'poloniex']])->first())
      {
        $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'poloniex']])->first();
        $key->public = encrypt($public);
        $key->private = encrypt($private);
        $key->save();

        Alert::success('Your Poloniex API keys has been updated.', 'Update Successful!');
        return Redirect::back();

      } else
      {
        $key = new Key;
        $key->userid = $user->id;
        $key->exchange = "Poloniex";
        $key->public = encrypt($public);
        $key->private = encrypt($private);
        $key->save();
        Alert::success('Poloniex is now connected to your account.', 'Update Successful!');
        return Redirect::back();
      }


    }

    public function addBittrexKeys(Request $request)
    {
      $user = Auth::user();
      $public = $request->get('bittrex_api_public');
      $private = $request->get('bittrex_api_private');

      if(Key::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->first())
      {
        $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->first();
        $key->public = encrypt($public);
        $key->private = encrypt($private);
        $key->save();

        Alert::success('Your Bittrex API keys has been updated.', 'Update Successful!');
        return Redirect::back();

      } else
      {
        $key = new Key;
        $key->userid = $user->id;
        $key->exchange = "Bittrex";
        $key->public = encrypt($public);
        $key->private = encrypt($private);
        $key->save();
        Alert::success('Bittrex is now connected to your account.', 'Update Successful!');
        return Redirect::back();
      }


    }


    public function calculateInvested($userid)
    {
      $user = User::where('id', $userid)->first();

      $deposits = Deposit::where('userid', $user->id)->get();
      $withdraws = Withdraw::where('userid', $user->id)->get();

      $d_amount = 0;
      $w_amount = 0;

      foreach($deposits as $deposit)
      {
        $d_amount += ($deposit->amount * $deposit->price) * $deposit->btc_price_deposit_usd;
      }

      foreach($withdraws as $withdraw)
      {
        $w_amount += ($withdraw->amount * $withdraw->price) * $withdraw->btc_price_deposit_usd;
      }

      $user->invested = $d_amount - $w_amount;
      $user->save();



    }

    public function migrate()
    {
      $user = Auth::user();

      //Only manual.
      $investments = Investment::where([['userid','=',$user->id], ['bittrex_id','=',null], ['amount', '>', 0]])->get();

      foreach($investments as $investment)
      {
        $migrated = new ManualInvestment;
        $migrated->userid = $user->id;
        $migrated->currency = $investment->crypto;
        $migrated->date_bought = $investment->date_bought;
        $migrated->bought_at = $investment->bought_at;
        $migrated->amount = $investment->amount;
        $migrated->bought_for = $investment->amount * $investment->bought_at;
        $migrated->btc_price_bought_usd = $investment->btc_price_bought;
        $migrated->btc_price_bought_eur = $investment->btc_price_bought * DB::table('multipliers')->where('currency', 'EUR')->first()->price;
        $migrated->btc_price_bought_gbp = $investment->btc_price_bought * DB::table('multipliers')->where('currency', 'EUR')->first()->price;
        $migrated->type = "Investment";

        if($investment->sold_at != null)
        {
          $migrated->sold_at = $investment->sold_at;
          $migrated->date_sold = $investment->date_sold;
          $migrated->sold_for = $investment->sold_at * $investment->amount;
          $migrated->btc_price_sold_usd = $investment->btc_price_sold;
          $migrated->btc_price_sold_eur = $investment->btc_price_sold * DB::table('multipliers')->where('currency', 'EUR')->first()->price;
          $migrated->btc_price_sold_gbp = $investment->btc_price_sold * DB::table('multipliers')->where('currency', 'EUR')->first()->price;
        } else {
          if(Balance::where([['userid', '=', $user->id], ['currency', '=', $investment->crypto], ['exchange', '=', 'Manual']])->exists())
          {
            $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $investment->crypto], ['exchange', '=', 'Manual']])->first();
            $balance->amount += $migrated->amount;
            $balance->save();
          } else {
            $balance = new Balance;
            $balance->userid = $user->id;
            $balance->currency = $migrated->currency;
            $balance->amount = $migrated->amount;
            $balance->exchange = "Manual";
            $balance->save();
          }
        }
        $migrated->save();

      }
      Alert::success('Your manual investments was migrated.', 'Investment added');
      return redirect('/investments');


    }

    public function resetPolo()
    {
      $user = Auth::user();
      $trades = PoloTrade::where('userid', $user->id)->get();
      $investments = PoloInvestment::where('userid', $user->id)->get();
      $balances = Balance::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->get();
      $deposits = Deposit::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->get();
      $withdraws = Withdraw::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->get();


      // clear cache
      Cache::forget('investments'.$user->id);
      Cache::forget('p_investments'.$user->id);
      Cache::forget('deposits'.$user->id);
      Cache::forget('withdraws'.$user->id);
      Cache::forget('balances'.$user->id);

      foreach($trades as $trade)
      {
        $trade->delete();
      }
      foreach($balances as $balance)
      {
        $balance->delete();
      }
      foreach($investments as $investment)
      {
        $investment->delete();
      }
      foreach($deposits as $deposit)
      {
        $deposit->delete();
      }
      foreach($withdraws as $withdraw)
      {
        $withdraw->delete();
      }

      ImportController::calculateInvested($user->id);
      Alert::success('Your poloniex reset has successfully been made.', 'Reset Successful!');
      return Redirect::back();
    }

    public function resetBittrex()
    {
      $user = Auth::user();
      $trades = BittrexTrade::where('userid', $user->id)->get();
      $investments = BittrexInvestment::where('userid', $user->id)->get();
      $balances = Balance::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->get();
      $deposits = Deposit::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->get();
      $withdraws = Withdraw::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->get();
      // clear cache
      Cache::forget('investments'.$user->id);
      Cache::forget('b_investments'.$user->id);
      Cache::forget('deposits'.$user->id);
      Cache::forget('withdraws'.$user->id);
      Cache::forget('balances'.$user->id);

      foreach($trades as $trade)
      {
        $trade->delete();
      }
      foreach($balances as $balance)
      {
        $balance->delete();
      }
      foreach($investments as $investment)
      {
        $investment->delete();
      }
      foreach($deposits as $deposit)
      {
        $deposit->delete();
      }
      foreach($withdraws as $withdraw)
      {
        $withdraw->delete();
      }

      ImportController::calculateInvested($user->id);
      Alert::success('Your Bittrex reset has successfully been made.', 'Reset Successful!');
      return Redirect::back();
    }

    public function dispatchImport()
    {
       dispatch(new ImportPoloniex(Auth::user()->id, 0));
    }

    public function dispatchImportUser($userid)
    {
       dispatch(new ImportPoloniex($userid, 0));
    }


    public function dispatchImportB()
    {
       dispatch(new ImportBittrex(Auth::user()->id, 0));
    }

    public function dispatchNew(Request $request)
    {
      $exchange = $request->get('exchangeinput');

      if($exchange == "Poloniex")
      {
        if($request->get('withdraws') == "no")
        {
         dispatch(new ImportPoloniex(Auth::user()->id, 0));
       } else {
         dispatch(new ImportPoloniex(Auth::user()->id, 1));
       }

      } else {
        if($request->hasFile('csv'))
        {
          // Import CSV trades
          $client = new \GuzzleHttp\Client();
    			$path = $request->file('csv')->getRealPath();
    			$data = Excel::load($path, function($reader) {}, 'UTF-8')->get();

    			if(!empty($data) && $data->count()){
    				foreach ($data as $key => $value) {
              $exchange = trim(mb_convert_encoding($value->exchange, 'UTF-8', 'UCS-2'));
              $currencies = explode("-", $exchange);
              $price = floatval(trim(mb_convert_encoding($value->price, 'UTF-8', 'UCS-2')));
              $amount = floatval(trim(mb_convert_encoding($value->quantity, 'UTF-8', 'UCS-2')));
              $limit = trim(mb_convert_encoding($value->limit, 'UTF-8', 'UCS-2'));
              $fee = floatval(trim(mb_convert_encoding($value->commissionpaid, 'UTF-8', 'UCS-2')));
              if($price != 0 && $amount != 0)
              {
    		       $insert[] = ['userid' => Auth::user()->id, 'tradeid' => trim(mb_convert_encoding($value->orderuuid, 'UTF-8', 'UCS-2')), 'date' => date('Y-m-d H:m:s', strtotime(trim(mb_convert_encoding($value->closed, 'UTF-8', 'UCS-2')))), 'type' => trim(mb_convert_encoding($value->type, 'UTF-8', 'UCS-2')), 'market' => $currencies[0], 'handled' => 0, 'currency' => $currencies[1], 'price' => $price / $amount, 'fee' => $fee, 'amount' => $amount, 'total' => $price];

               $oldtrade = BittrexTrade::where('tradeid', Auth::user()->id.trim(mb_convert_encoding($value->orderuuid, 'UTF-8', 'UCS-2')))->first();

               if(!$oldtrade)
               {
                 $trade = new BittrexTrade;
                 $trade->userid = Auth::user()->id;
                 $trade->tradeid = Auth::user()->id.trim(mb_convert_encoding($value->orderuuid, 'UTF-8', 'UCS-2'));
                 $trade->date = date('Y-m-d H:m:s', strtotime(trim(mb_convert_encoding($value->closed, 'UTF-8', 'UCS-2'))));
                 $trade->type = trim(mb_convert_encoding($value->type, 'UTF-8', 'UCS-2'));
                 $trade->market = $currencies[0];
                 if($currencies[1] == "ANS")
                 {
                   $trade->currency = "NEO";
                 } else {
                   $trade->currency = $currencies[1];
                 }
                 $trade->handled = 0;
                 $trade->price = $price / $amount;
                 $trade->amount = $amount;
                 $trade->fee = $fee;
                 $trade->total = $price;
                 $trade->save();
               }
              }
    				}
    			}
        }
        if($request->get('withdraws') == "no")
        {
         dispatch(new ImportBittrex(Auth::user()->id, 0));
       } else {
         dispatch(new ImportBittrex(Auth::user()->id, 1));
       }
      }
    }


    /* Import Deposits Poloniex */
    public function importDeposits($userid)
    {
      // User Variables
      $user = User::where('id', $userid)->first();
      if(Key::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->exists())
      {
        $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->first();
      } else
      {
        event(new PushEvent('No API keys found, please add your Poloniex API keys.', 'error', $userid));
      }
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);

      $client = new \GuzzleHttp\Client();
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
        event(new PushEvent('Your import has started!', 'success', $userid));

        foreach($deposits as $key => $deposit)
        {
          foreach($deposit as $key2 => $d)
          {
            if($key != "withdrawals" && $d['status'] == "COMPLETE")
            {
              if(!Deposit::where('txid', $user->id . $d['txid'])->exists())
              {
                $date = date('Y-m-d H:i:s', $d['timestamp']);
                $date2 = date('Y-m-d', $d['timestamp']);
                // Get bitcoin price of day:
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
                $deposit->userid = $user->id;
                $deposit->exchange = "Poloniex";
                $deposit->txid = $user->id . $d['txid'];
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

                if(!Balance::where([['userid', '=', $user->id], ['currency', '=', $d['currency']], ['exchange', '=', 'poloniex']])->exists())
                {
                  $balance = new Balance;
                  $balance->exchange = "Poloniex";
                  $balance->userid = $user->id;
                  $balance->currency = $d['currency'];
                  $balance->amount = $d['amount'];
                  $balance->save();
                } else
                {
                  $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $d['currency']], ['exchange', '=', 'poloniex']])->first();
                  $balance->amount += $d['amount'];
                  $balance->save();
                }


              }



            }
          }


        }


      } catch(\GuzzleHttp\Exception\RequestException $e){
        //$user->notify(new ImportComplete('No API keys found or invalid combination.', 'error'));
        event(new PushEvent('No API keys found or invalid combination.', 'error', $userid));
      } catch(\GuzzleHttp\Exception\ClientException $e) {
        //$user->notify(new ImportComplete('No API keys found or invalid combination.', 'error'));
        event(new PushEvent('No API keys found or invalid combination.', 'error', $userid));
      } catch(\GuzzleHttp\Exception\BadResponseException $e) {
        //$user->notify(new ImportComplete('No API keys found or invalid combination.', 'error'));
        event(new PushEvent('No API keys found or invalid combination.', 'error', $userid));
      } catch(\GuzzleHttp\Exception\ServerException $e) {
        //$user->notify(new ImportComplete('No API keys found or invalid combination.', 'error'));
        event(new PushEvent('No API keys found or invalid combination.', 'error', $userid));
      }




    }

    public function importTrades($userid)
    {
      // User Variables
      $user = User::where('id', $userid)->first();
      $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->first();
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);

      $client = new \GuzzleHttp\Client();
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
          abort(404);
      }


      foreach($trades as $key => $trade)
      {
        foreach($trade as $t)
        {
          if($t['category'] == "exchange")
          {
            // Make sure the trade doesn't exist.
            if(!PoloTrade::where('tradeid', $user->id . $t['tradeID'])->exists())
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
              $trade->userid = $user->id;
              $trade->tradeid = $user->id . $t['tradeID'];
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
    }

    public function insertBuys($userid)
    {
      // User Variables
      $user = User::where('id', $userid)->first();

      // Get trades
      $trades = PoloTrade::where([['userid', '=', $user->id], ['handled', '=', '0'], ['type', '=', 'buy']])->orderBy('date')->get();

      foreach($trades as $trade)
      {
        $investment = PoloInvestment::where('orderid', $trade->orderid)->first();
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


        if($trade->market == "ETH")
        {
          if(PoloInvestment::where([['currency', '=', 'ETH'], ['date_sold', '=', null], ['userid', '=', $userid]])->exists())
          {
          $test = PoloInvestment::where([['currency', '=', 'ETH'], ['date_sold', '=', null], ['userid', '=', $userid]])->first();
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

          $investment->userid = $user->id;
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

        if(!Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->currency], ['exchange', '=', 'Poloniex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Poloniex";
          $balance->userid = $user->id;
          $balance->currency = $trade->currency;
          $balance->amount = $trade->amount;
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->currency], ['exchange', '=', 'Poloniex']])->first();
          $balance->amount += $trade->amount;
          $balance->save();
        }

        if(!Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->market], ['exchange', '=', 'Poloniex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Poloniex";
          $balance->userid = $user->id;
          $balance->currency = $trade->market;
          $balance->amount = 0 - $trade->total;
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->market], ['exchange', '=', 'Poloniex']])->first();
          $balance->amount -= $trade->total;
          $balance->save();
        }





      }





    }

    public function insertSells($userid)
    {
      // User variables
      $user = User::where('id', $userid)->first();

      $trades = PoloTrade::where([['userid', '=', $user->id], ['handled', '=', '0'], ['type', '=', 'sell']])->orderBy('date')->get();
      $lasttrade = 0;

      foreach($trades as $trade)
      {
        $amount = $trade->amount;

        $investments = PoloInvestment::where([['currency', '=', $trade->currency], ['userid', '=', $user->id], ['saleid', '=', null], ['date_bought', '<=', $trade->date]])->orderBy('date_bought')->get();
        //Make sure the amount is more than 0 otherwise we do not continue!!
        if($amount > 0)
        {
          echo $trade->id."<br>";
          if(count($investments) >= 1)
          {
          foreach($investments as $investment)
          {
            if($investment->amount >= $amount && $amount > 0)
            {
              $sale = PoloInvestment::where([['saleid', '=', $trade->orderid], ['userid', '=', $user->id], ['soldmarket', '=', $trade->market]])->first();

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

                  $inv = new PoloInvestment;
                  $inv->userid = $user->id;
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
              $sale = PoloInvestment::where([['saleid', '=', $trade->orderid], ['userid', '=', $user->id], ['soldmarket', '=', $trade->market]])->first();

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
            if(Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->currency], ['exchange', '=', 'poloniex']])->exists())
            {
              echo $trade->id."Here...<br>";
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

              $investment = new PoloInvestment;
              $investment->userid = $user->id;
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
          $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->currency], ['exchange', '=', 'Poloniex']])->first();
          $balance->amount -= $trade->amount;
          $balance->save();


          if(Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->market], ['exchange', '=', 'Poloniex']])->first())
          {
          $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->market], ['exchange', '=', 'Poloniex']])->first();
          $balance->amount += $trade->total;
          $balance->save();
        } else {
          $balance = new Balance;
          $balance->exchange = "Poloniex";
          $balance->userid = $user->id;
          $balance->currency = $trade->market;
          $balance->amount = $trade->total;
          $balance->save();
        }
        }
      }


    }

    public function importBalances($userid)
    {
      // User Variables
      $user = User::where('id', $userid)->first();
      $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->first();
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);

      $client = new \GuzzleHttp\Client();
      try {
        $nonce = round(microtime(true) * 1000);
        $req = ['command' => 'returnCompleteBalances', 'nonce' => $nonce];
        $post_data = http_build_query($req, '', '&');
        $sign = hash_hmac('sha512', $post_data, $apisecret);
        $res = $client->request('POST', 'https://poloniex.com/tradingApi', [
        'headers' => [
        'Sign' => $sign,
        'Key' => $apikey
      ], 'form_params' => ['command' => 'returnCompleteBalances', 'nonce' => $nonce]]);
        $response = $res->getBody();
        $balances = json_decode($response, true);
      } catch(GuzzleHttp\Exception\ClientException $e){
          abort(404);
      }

      $bs = Balance::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->get();

      foreach($bs as $bss)
      {
        $bss->delete();
      }

      foreach($balances as $key => $balance)
      {
        if($balance > 0)
        {
        if(Balance::where([['userid', '=', $user->id], ['currency', '=', $key], ['exchange', '=', 'Poloniex']])->exists())
        {
          $b = Balance::where([['userid', '=', $user->id], ['currency', '=', $key], ['exchange', '=', 'Poloniex']])->first();
          $b->amount = $balance['available'] + $balance['onOrders'];;
          $b->save();
        } else
        {
          $b = new Balance;
          $b->exchange = "Poloniex";
          $b->currency = $key;
          $b->amount = $balance['available'] + $balance['onOrders'];;
          $b->userid = $user->id;
          $b->save();
        }
      }


      }
      ImportController::calculateInvested($userid);
      $user->notify(new ImportComplete('Your import is complete! Go to your investments to see your import', 'success'));
    }

    public function importBalances2($userid)
    {
      // User Variables
      $user = User::where('id', $userid)->first();
      $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->first();
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);

      $client = new \GuzzleHttp\Client();
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
          abort(404);
      }

      $bs = Balance::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->get();

      foreach($bs as $bss)
      {
        $bss->delete();
      }

      foreach($balances as $key => $balance)
      {
        if($balance['available'] + $balance['onOrders'] > 0)
        {
        if(Balance::where([['userid', '=', $user->id], ['currency', '=', $key], ['exchange', '=', 'Poloniex']])->exists())
        {
          $b = Balance::where([['userid', '=', $user->id], ['currency', '=', $key], ['exchange', '=', 'Poloniex']])->first();
          $b->amount = $balance['available'] + $balance['onOrders'];
          $b->save();
        } else
        {
          $b = new Balance;
          $b->exchange = "Poloniex";
          $b->currency = $key;
          $b->amount = $balance['available'] + $balance['onOrders'];
          $b->userid = $user->id;
          $b->save();
        }
      }


      }
      ImportController::calculateInvested($userid);
      $user->notify(new ImportComplete('Your import is complete! Go to your investments to see your import', 'success'));
    }

    public function importBalances3()
    {
      // User Variables
      $userid = Auth::user()->id;
      $user = User::where('id', $userid)->first();
      $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->first();
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);

      $client = new \GuzzleHttp\Client();
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
          abort(404);
      }

      $bs = Balance::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->get();

      foreach($bs as $bss)
      {
        $bss->delete();
      }

      foreach($balances as $key => $balance)
      {
        if($balance['available'] + $balance['onOrders'] > 0)
        {
        if(Balance::where([['userid', '=', $user->id], ['currency', '=', $key], ['exchange', '=', 'Poloniex']])->exists())
        {
          $b = Balance::where([['userid', '=', $user->id], ['currency', '=', $key], ['exchange', '=', 'Poloniex']])->first();
          $b->amount = $balance['available'] + $balance['onOrders'];
          $b->save();
        } else
        {
          $b = new Balance;
          $b->exchange = "Poloniex";
          $b->currency = $key;
          $b->amount = $balance['available'] + $balance['onOrders'];
          $b->userid = $user->id;
          $b->save();
        }
      }


      }
      ImportController::calculateInvested($userid);
      $user->notify(new ImportComplete('Your import is complete! Go to your investments to see your import', 'success'));
    }


    public function importWithdraws($userid, $deletewithdraws)
    {
      // User Variables
      $user = User::where('id', $userid)->first();
      $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Poloniex']])->first();
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);

      $client = new \GuzzleHttp\Client();
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
          abort(404);
      }



      foreach($withdraws as $key => $withdraw)
      {
        if($key == "withdrawals")
        {
          foreach($withdraw as $w)
          {
            if(strpos($w['status'], 'COMPLETE') !== false)
            {
              if(!Withdraw::where('txid', $user->id . $w['withdrawalNumber'])->exists())
              {
                $date = date('Y-m-d H:i:s', $w['timestamp']);
                $date2 = date('Y-m-d', $w['timestamp']);
                // Get bitcoin price of day:
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
                $withdrawal->userid = $user->id;
                $withdrawal->txid = $user->id . $w['withdrawalNumber'];
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

                if(!Balance::where([['userid', '=', $user->id], ['currency', '=', $w['currency']], ['exchange', '=', 'Poloniex']])->exists())
                {
                  $balance = new Balance;
                  $balance->exchange = "Poloniex";
                  $balance->userid = $user->id;
                  $balance->currency = $w['currency'];
                  $balance->amount = 0 - $w['amount'];
                  $balance->save();
                } else
                {

                  $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $w['currency']], ['exchange', '=', 'Poloniex']])->first();
                  $balance->amount -= $w['amount'];
                  $balance->save();
                }


              }
            }

          }
        } else {
          # code...
        }
      }


      $withdraws = Withdraw::where([['userid', '=', $user->id], ['handled', '=', 0], ['exchange', '=', 'Poloniex']])->get();

      foreach($withdraws as $withdraw)
      {
        $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $withdraw->currency], ['exchange', '=', 'Poloniex']])->first();
        $holdings = 0;
        $investments = PoloInvestment::where([['userid', '=', $user->id], ['currency', '=', $withdraw->currency], ['saleid', '=', null]])->orderBy('date_bought', 'desc')->get();
        $investmentexact = PoloInvestment::where([['userid', '=', $user->id], ['currency', '=', $withdraw->currency], ['saleid', '=', null], ['amount', '=', $withdraw->amount]])->orderBy('date_bought', 'desc')->first();
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
                if($deletewithdraws == 1){ $investment->amount -= $amount; }
                $amount = 0;
                $investment->withdrew = 1;
                $investment->save();
                $withdraw->handled = 1;
                $withdraw->save();

                if($investment->amount <= 0)
                {
                  if($deletewithdraws == 1){ $investment->delete(); }
                }

              } elseif($investment->amount <= $amount) {
                $amount -= $investment->amount;
                $investment->withdrew = 1;
                $investment->save();
                if($deletewithdraws == 1){ $investment->delete(); }
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
        if($deletewithdraws == 1){ $investmentexact->delete(); }
      }




      }

    }



    public function importDepositsB($userid)
    {
      // User Variables
      $user = User::where('id', $userid)->first();
      if(Key::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->exists())
      {
        $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->first();
      } else
      {
        event(new PushEvent('No API keys found, please add your Bittrex API keys.', 'error', $userid));
      }
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);

      $client = new \GuzzleHttp\Client();
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

        event(new PushEvent('Your import has started!', 'success', $userid));

      foreach($deposits['result'] as $d)
      {
        $ddate = strtotime($d['LastUpdated']);
        $newformat = date('Y-m-d H:i:s', $ddate);
        $newformat2 = date('Y-m-d', $ddate);
        if(!Deposit::where('txid', $user->id . $d['TxId'])->exists())
        {
          // Get bitcoin price of day:
          $client = new \GuzzleHttp\Client();
          $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP,ETH,USDT&ts='.strtotime($newformat2).'&extraParams=Altpocket');
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
            $value = 0;
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




        $deposit = new Deposit;
        $deposit->userid = $user->id;
        $deposit->exchange = "Bittrex";
        $deposit->txid = $user->id . $d['TxId'];
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

        if(!Balance::where([['userid', '=', $user->id], ['currency', '=', $d['Currency']], ['exchange', '=', 'Bittrex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Bittrex";
          $balance->userid = $user->id;
          $balance->currency = $d['Currency'];
          $balance->amount = $d['Amount'];
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $d['Currency']], ['exchange', '=', 'Bittrex']])->first();
          $balance->amount += $d['Amount'];
          $balance->save();
        }

        }

      }
      } else {
        if($deposits['message'] == "APIKEY_INVALID")
        {
          event(new PushEvent('No API keys found or invalid combination.', 'error', $userid));
          abort(403);
        } else {
          event(new PushEvent('Same API keys used elsewere, please make a new one.', 'error', $userid));
          abort(403);
        }
        }
      } catch(\GuzzleHttp\Exception\RequestException $e){
        event(new PushEvent('No API keys found or invalid combination.', 'error', $userid));
      } catch(\GuzzleHttp\Exception\ClientException $e) {
        event(new PushEvent('No API keys found or invalid combination.', 'error', $userid));
      } catch(\GuzzleHttp\Exception\BadResponseException $e) {
        event(new PushEvent('No API keys found or invalid combination.', 'error', $userid));
      } catch(\GuzzleHttp\Exception\ServerException $e) {
        event(new PushEvent('No API keys found or invalid combination.', 'error', $userid));
      }




    }

    public function importTradesB($userid)
    {
      // User Variables
      $user = User::where('id', $userid)->first();
      $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->first();
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);
      $client = new \GuzzleHttp\Client();
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
      }



      foreach($trades['result'] as $trade)
      {
        if(!BittrexTrade::where('tradeid', $user->id . $trade['OrderUuid'])->exists())
        {
          $currencies = explode("-", $trade['Exchange']);

          $ddate = strtotime($trade['TimeStamp']);
          $newformat = date('Y-m-d H:i:s', $ddate);

          $t = new BittrexTrade;
          $t->userid = $user->id;
          $t->tradeid = $user->id . $trade['OrderUuid'];
          $t->date = $newformat;
          $t->type = $trade['OrderType'];
          $t->market = $currencies[0];
          if($currencies[1] == "ANS")
          {
            $t->currency = "NEO";
          } else {
            $t->currency = $currencies[1];
          }
          $t->price = $trade['PricePerUnit'];
          $t->amount = $trade['Quantity'] - $trade['QuantityRemaining'];
          $t->fee = $trade['Commission'];
          $t->total = $trade['Price'];
          $t->save();

        }

      }







    }

    public function insertBuysB($userid)
    {
      // User Variables
      $user = User::where('id', $userid)->first();

      // Get trades
      $trades = BittrexTrade::where([['userid', '=', $user->id], ['handled', '=', '0'], ['type', '=', 'LIMIT_BUY']])->orderBy('date')->get();


      foreach($trades as $trade)
      {
        $investment = BittrexInvestment::where('orderid', $trade->tradeid)->first();
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

          $investment->userid = $user->id;
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

        if(!Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Bittrex";
          $balance->userid = $user->id;
          $balance->currency = $trade->currency;
          $balance->amount = $trade->amount;
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->first();
          $balance->amount += $trade->amount;
          $balance->save();
        }

        if(!Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->market], ['exchange', '=', 'Bittrex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Bittrex";
          $balance->userid = $user->id;
          $balance->currency = $trade->market;
          $balance->amount = 0 - $trade->total;
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->market], ['exchange', '=', 'Bittrex']])->first();
          $balance->amount -= $trade->total;
          $balance->save();
        }


      }
    }

    public function insertSellsB($userid)
    {
      // User variables
      $user = User::where('id', $userid)->first();

      $trades = BittrexTrade::where([['userid', '=', $user->id], ['handled', '=', '0'], ['type', '=', 'LIMIT_SELL']])->orderBy('date')->get();
      $lasttrade = 0;

      foreach($trades as $trade)
      {
        $amount = $trade->amount;

        $investments = BittrexInvestment::where([['currency', '=', $trade->currency], ['userid', '=', $user->id], ['saleid', '=', null], ['date_bought', '<=', $trade->date]])->orderBy('date_bought')->get();





        //Make sure the amount is more than 0 otherwise we do not continue!!
        if($amount > 0)
        {
          if(count($investments) >= 1)
          {
          foreach($investments as $investment)
          {
            if($investment->amount >= $amount && $amount > 0)
            {
              $sale = BittrexInvestment::where([['saleid', '=', $trade->tradeid], ['userid', '=', $user->id], ['soldmarket', '=', $trade->market]])->first();

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

                  $inv = new BittrexInvestment;
                  $inv->userid = $user->id;
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
              $sale = BittrexInvestment::where([['saleid', '=', $trade->tradeid], ['userid', '=', $user->id], ['soldmarket', '=', $trade->market]])->first();

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

          if($amount > 0.001 && Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->exists())
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

            $investment = new BittrexInvestment;
            $investment->userid = $user->id;
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
            if(Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->exists())
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

              $investment = new BittrexInvestment;
              $investment->userid = $user->id;
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
          if(Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->exists())
          {
            $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->currency], ['exchange', '=', 'Bittrex']])->first();
            $balance->amount -= $trade->amount;
            $balance->save();
          }

          if(Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->market], ['exchange', '=', 'Bittrex']])->first())
          {
            $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $trade->market], ['exchange', '=', 'Bittrex']])->first();
            $balance->amount += $trade->total;
            $balance->save();
          } else {
            $balance = new Balance;
            $balance->exchange = "Bittrex";
            $balance->userid = $user->id;
            $balance->currency = $trade->market;
            $balance->amount = $trade->total;
            $balance->save();
        }
        }
      }





    }

    public function importBalancesB($userid)
    {
      // User Variables
      $user = User::where('id', $userid)->first();
      $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->first();
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);
      $client = new \GuzzleHttp\Client();
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
      }

      $bs = Balance::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->get();

      foreach($bs as $bss)
      {
        $bss->delete();
      }



      foreach($balances['result'] as $balance)
      {
        if($balance['Balance'] > 0)
        {
        if(Balance::where([['userid', '=', $user->id], ['currency', '=', $balance['Currency']], ['exchange', '=', 'Bittrex']])->exists())
        {
          echo $balance['Available']."<br>";
          $b = Balance::where([['userid', '=', $user->id], ['currency', '=', $balance['Currency']], ['exchange', '=', 'Bittrex']])->first();
          $b->amount = $balance['Balance'];
          $b->save();
        } else
        {
          $b = new Balance;
          $b->exchange = "Bittrex";
          $b->currency = $balance['Currency'];
          $b->amount = $balance['Balance'];
          $b->userid = $user->id;
          $b->save();
        }
      }
      }

      ImportController::calculateInvested($userid);
      $user->notify(new ImportComplete('Your import is complete! Go to your investments to see your import', 'success'));
    }

    public function importWithdrawsB($userid, $deletewithdraws)
    {
      // User Variables
      $user = User::where('id', $userid)->first();
      $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Bittrex']])->first();
      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);
      $client = new \GuzzleHttp\Client();
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

      }


      if(is_array($withdraws) && isset($withdraws['result']))
      {
      foreach($withdraws['result'] as $w)
      {
        $ddate = strtotime($w['Opened']);
        $newformat = date('Y-m-d H:i:s', $ddate);
        $newformat2 = date('Y-m-d', $ddate);
        if(!Withdraw::where('txid', $user->id . $w['TxId'])->exists())
        {

          $client = new \GuzzleHttp\Client();
          $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP,ETH,USDT&ts='.strtotime($newformat2).'&extraParams=Altpocket');
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
        $with->userid = $user->id;
        $with->exchange = "Bittrex";
        $with->txid = $user->id . $w['TxId'];
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

        if(!Balance::where([['userid', '=', $user->id], ['currency', '=', $w['Currency']], ['exchange', '=', 'Bittrex']])->exists())
        {
          $balance = new Balance;
          $balance->exchange = "Bittrex";
          $balance->userid = $user->id;
          $balance->currency = $w['Currency'];
          $balance->amount = 0 - ($w['Amount'] + $w['TxCost']);
          $balance->save();
        } else
        {
          $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $w['Currency']], ['exchange', '=', 'Bittrex']])->first();
          $balance->amount -= ($w['Amount'] + $w['TxCost']);;
          $balance->save();
        }

        }

      }
      }

      $withdraws = Withdraw::where([['userid', '=', $user->id], ['handled', '=', 0], ['exchange', '=', 'Bittrex']])->get();

      foreach($withdraws as $withdraw)
      {
        if($withdraw->handled != 1)
        {
        $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $withdraw->currency], ['exchange', '=', 'Bittrex']])->first();
        $holdings = 0;
        $investments = BittrexInvestment::where([['userid', '=', $user->id], ['currency', '=', $withdraw->currency], ['saleid', '=', null]])->orderBy('date_bought', 'desc')->get();
        $investmentexact = BittrexInvestment::where([['userid', '=', $user->id], ['currency', '=', $withdraw->currency], ['saleid', '=', null], ['amount', '=', $withdraw->amount]])->orderBy('date_bought', 'desc')->first();
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
                if($deletewithdraws == 1){ $investment->amount -= $amount; }
                $amount = 0;
                $investment->withdrew = 1;
                $investment->save();
                $withdraw->handled = 1;
                $withdraw->save();


                  if($investment->amount <= 0)
                  {
                    if($deletewithdraws == 1){ $investment->delete(); }
                  }


              } elseif($investment->amount <= $amount) {
                $amount -= $investment->amount;
                $investment->withdrew = 1;
                $investment->save();
                if($deletewithdraws == 1){ $investment->delete(); }

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

          if($deletewithdraws == 1){ $investmentexact->delete(); }

      }



      }
      }
    }



    public function importBalancesCB()
    {
      $user = Auth::user();
      $client = new \GuzzleHttp\Client();
      $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'Coinbase']])->first();

      CoinBase::checkExpiry($key);

      $access_token = decrypt($key->public); // Access token
      $refresh_token = decrypt($key->private); // Refresh token
      try{
        $url = 'https://api.coinbase.com/v2/accounts';

        $res = $client->request('GET', $url, [
        'headers' => [
        'Authorization' => 'Bearer '.$access_token,
        ]]);
      $response = $res->getBody();
      $response = json_decode($response, true);

      //Let's get it

      foreach($response['data'] as $key => $b)
      {
        if(!Balance::where([['userid', '=', $user->id], ['currency', '=', $b['currency']], ['exchange', '=', 'Coinbase']])->exists())
        {
          $balance = new Balance;
          $balance->userid = $user->id;
          $balance->exchange = "Coinbase";
          $balance->currency = $b['currency'];
          $balance->amount = $b['balance']['amount'];
          $balance->save();
        } else {
          $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $b['currency']], ['exchange', '=', 'Coinbase']])->first();
          $balance->amount = $b['balance']['amount'];
          $balance->save();
        }

        // Trades now directly somehow..
        try{
          $url = 'https://api.coinbase.com/v2/accounts/'.$b['id'].'/buys';

          $res = $client->request('GET', $url, [
          'headers' => [
          'Authorization' => 'Bearer '.$access_token,
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
          $trade->userid = Auth::user()->id;
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
          $trade->save();
        }
      }

      }

      }  catch (\GuzzleHttp\Exception\ClientException $e) {

      }
    }


}
