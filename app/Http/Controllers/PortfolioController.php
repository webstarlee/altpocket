<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Illuminate\Http\Request;
use App\Multiplier;
use Auth;
use App\History;
use Redirect;
use Alert;
use Validator;
use App\Crypto;
use App\Transaction;
use App\Holding;
use App\HoldingLog;
use App\Watchlist;
use App\Deposit;
use App\Withdraw;
use App\CoinBase;
use Cache;
use App\Exchanges\Poloniex;
use App\Exchanges\Bittrex;
use App\Key;
use Excel;
use \Coinbase\Wallet\Client as ClientCoinbase;
use \Coinbase\Wallet\Configuration;
class PortfolioController extends Controller
{
    public function index()
    {
      $btc = Auth::user()->getBtcCache();
      $eth = Auth::user()->getEthCache();
      $holdings = Holding::where('userid', Auth::user()->id)->orderBy('token')->get();
      $watchlists = Watchlist::where('userid', '=', Auth::user()->id)->get();

      $deposits = Cache::remember('transaction_deposits:'.Auth::user()->id, 60, function()
      {
        return Transaction::where([['userid', '=', Auth::user()->id], ['type', '=', 'DEPOSIT']])->get();
      });

      return view('portfolio.index', ['btc' => $btc, 'holdings' => $holdings, 'eth' => $eth, 'watchlists' => $watchlists, 'deposits' => $deposits]);
    }

    public function setProfit(Request $request)
    {
      if($request->get('type'))
      {
        $type = $request->get('type');
        $user = Auth::user();
        if($type == "default")
        {
          $user->algorithm = 1;
        } else {
          $user->algorithm = 2;
        }
        $user->save();
        return Redirect::back();
      }
    }

    public function clearPortfolio()
    {
      $holdings = Holding::where('userid', Auth::user()->id)->get();
      $transactions = Transaction::where('userid', Auth::user()->id)->get();
      $logs = HoldingLog::where('userid', Auth::user()->id)->get();
      $deposits = Deposit::where('userid', Auth::user()->id)->get();

      Cache::forget('myHistory:'.Auth::user()->id);
      Cache::forget('holdingsLogs:'.Auth::user()->id);
      Cache::forget('myHoldings:'.Auth::user()->id);
      Cache::forget('portfolioChart:'.Auth::user()->id);

      foreach($holdings as $holding)
      {
        $holding->delete();
      }
      foreach($transactions as $transaction)
      {
        $transaction->delete();
      }
      foreach($logs as $log)
      {
        $log->delete();
      }
      foreach($deposits as $deposit)
      {
        $deposit->delete();
      }

      return Redirect::back();
    }


    public function getExchanges()
    {
      include(base_path('vendor/ccxt/ccxt/ccxt.php'));
      $exchanges = \ccxt\Exchange::$exchanges;
      foreach($exchanges as $exchange)
      {
        $class = '\ccxt\\' . $exchange;
        $exchange = new $class();
        if($exchange->id != "_1broker")
        {
          $markets = $exchange->load_markets();

        echo $exchange->id . "<br>";
        echo "<pre>";
        var_dump($markets);
        echo "</pre>";
                }
      }
    }

    public function toggleDeposit($id)
    {
      $deposit = Transaction::where([['userid', '=', Auth::user()->id], ['id', '=', $id]])->first();
      Cache::forget('transaction_deposits:'.Auth::user()->id);
      if($deposit)
      {
        if($deposit->toggled == 0)
        {
          $deposit->toggled = 1;
        } else {
          $deposit->toggled = 0;
        }
        $deposit->save();
      }
    }


    public function test()
    {
      Cache::forget('myHoldings:'.Auth::user()->id);
      Cache::forget('portfolioChart:'.Auth::user()->id);
      $userid = Auth::user()->id;

      if($request->get('remove'))
      {
        $remove_withdraws = 1;
      } else {
        $remove_withdraws = 0;
      }


      if(Key::where([['userid', '=', $userid], ['exchange', '=', 'Poloniex']])->exists())
      {
        $key = Key::where([['userid', '=', $userid], ['exchange', '=', 'Poloniex']])->first();
      } else {
        return "no key";
      }

      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);
      $end = time();

      $poloniex = new Poloniex($apikey, $apisecret);

      $dw = $poloniex->returnDepositsWithdrawals('1420070400', $end);
      $trades = $poloniex->returnTradeHistory('all', '1420070400', $end);


      // Deposits

      foreach($dw as $key => $deposits)
      {
        foreach($deposits as $deposit)
        {
          if($key != "withdrawals" && $deposit['status'] == "COMPLETE")
          {
            if(!Transaction::where('tradeid', $userid . $deposit['txid'])->select('id')->exists())
            {
              $date = date('Y-m-d H:i:s', $deposit['timestamp']);
              $date2 = date('Y-m-d', $deposit['timestamp']);
              $currency = $deposit['currency'];
              $amount = $deposit['amount'];
              $tx = $deposit['txid'];

              $token = Cache::remember("token:".$currency, 1440, function() use ($currency) {
                return Crypto::where('symbol', $currency)->selectRaw('symbol, cmc_id, name, id')->first();
              });

              $historical = History::getHistorical($date2);
              $btc_usd = $historical->USD;
              $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
              $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
              $btc_usdt = $historical->USD;
              $btc_eth = $historical->ETH;

              $client = new \GuzzleHttp\Client();
              $res = $client->request('GET', 'https://poloniex.com/public?command=returnChartData&currencyPair=BTC_'.$currency.'&start='.strtotime($date2).'&end='.strtotime($date2).'&period=14400');
              $response = $res->getBody();
              $prices = json_decode($response, true);
              $value = 0;

              if($deposit['currency'] != "BTC")
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

              //prepare addition of transaction
              $token = $token->symbol;
              $market = "BTC";
              $price = 0;
              $amount = $amount;
              $type = "DEPOSIT";
              $exchange = "Poloniex";
              $fee_currency = "BTC";
              $fee = 0;
              $tradeid = $tx;

              $this->makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid);
            }
          }
        }
      }


      //trades


      foreach($trades as $key => $trade)
      {
        $trade = collect($trade)->sortBy('date')->toArray();
        foreach($trade as $transaction)
        {
          if($transaction['category'] == "exchange")
          {
            $t = Transaction::where([['userid', '=', $userid], ['tradeid', '=', $userid . $transaction['tradeID']]])->first();
            //echo $transaction['date'] . "<br>";
            if(!$t)
            {

              // Split the pair
              $currencies = explode('_', $key);

              // Get the type
              if($transaction['type'] == "buy")
              {
                $type = "BUY";
              } else {
                $type = "SELL";
              }

              //prepare making of transaction
              $token = $currencies[1];
              $market = $currencies[0];
              $price = $transaction['rate'];
              $amount = $transaction['amount'];
              $date = $transaction['date'];
              $total = $transaction['total'];
              $exchange = "Poloniex";
              if($type == "BUY")
              {
                $fee_currency = $token;
                $fee = number_format($amount * $transaction['fee'], 8, '.', '');
              } else {
                $fee_currency = $market;
                $fee = number_format(($amount * $price) * $transaction['fee'], 8, '.', '');
              }
              $tradeid = $transaction['tradeID'];

              // Send to making
              $this->makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid, $total);
          }
        }
      }
      }


      //withdraws
      foreach($dw as $key => $withdraws)
      {
        foreach($withdraws as $withdraw)
        {
          if($key == "withdrawals")
          {
            if(!Transaction::where('tradeid', $userid . $withdraw['withdrawalNumber'])->select('id')->exists())
            {
              $date = date('Y-m-d H:i:s', $withdraw['timestamp']);
              $date2 = date('Y-m-d', $withdraw['timestamp']);
              $currency = $withdraw['currency'];
              $amount = $withdraw['amount'];
              $tx = $withdraw['withdrawalNumber'];

              $token = Cache::remember("token:".$currency, 1440, function() use ($currency) {
                return Crypto::where('symbol', $currency)->selectRaw('symbol, cmc_id, name, id')->first();
              });

              $historical = History::getHistorical($date2);
              $btc_usd = $historical->USD;
              $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
              $btc_gbp = $historical->USD * Multiplier::where('currency', 'GBP')->select('price')->first()->price;
              $btc_usdt = $historical->USD;
              $btc_eth = $historical->ETH;

              $client = new \GuzzleHttp\Client();
              $res = $client->request('GET', 'https://poloniex.com/public?command=returnChartData&currencyPair=BTC_'.$currency.'&start='.strtotime($date2).'&end='.strtotime($date2).'&period=14400');
              $response = $res->getBody();
              $prices = json_decode($response, true);
              $value = 0;

              if($withdraw['currency'] != "BTC")
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

              //prepare addition of transaction
              $token = $token->symbol;
              $market = "BTC";
              $price = 0;
              $amount = $amount;
              $type = "WITHDRAW";
              $exchange = "Poloniex";
              $fee_currency = "BTC";
              $fee = 0;
              $tradeid = $tx;

              $this->makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid);
            }
          }
        }
      }


      $transactions = Transaction::where([['userid', '=', $userid], ['handled', '=', '0']])->orderBy('date')->get();

      foreach($transactions as $transaction)
      {
        $this->makeHolding($transaction, $remove_withdraws);
      }

      return Redirect::back();
    }

    public function bittrex(Request $request)
    {
      Cache::forget('myHoldings:'.Auth::user()->id);
      Cache::forget('portfolioChart:'.Auth::user()->id);
      $userid = Auth::user()->id;


      if($request->get('remove'))
      {
        $remove_withdraws = 1;
      } else {
        $remove_withdraws = 0;
      }

      // Handle CSV input
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
             $t = Transaction::where([['userid', '=', $userid], ['tradeid', '=', Auth::user()->id.trim(mb_convert_encoding($value->orderuuid, 'UTF-8', 'UCS-2'))]])->first();
             if(!$t)
             {
               $tradeid = trim(mb_convert_encoding($value->orderuuid, 'UTF-8', 'UCS-2'));
               $date = date('Y-m-d H:i:s', strtotime(trim(mb_convert_encoding($value->closed, 'UTF-8', 'UCS-2'))));
               $type = trim(mb_convert_encoding($value->type, 'UTF-8', 'UCS-2'));
               $total = $price;
               $price = $price / $amount;
               $market = $currencies[0];
               $exchange = "Bittrex";
               if($currencies[1] == "ANS")
               {
                 $token = "NEO";
               } elseif($currencies[1] == "SEC") {
                 $token = "SAFEX";
               } elseif($currencies[1] == "BCC") {
                 $token = "BCH";
               } else {
                 $token = $currencies[1];
               }
               $fee_currency = $market;

               if($type == "LIMIT_BUY")
               {
                 $type = "BUY";
               } else {
                 $type = "SELL";
               }


               $this->makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid, $total);
             }
            }
          }
        }
      }

      if(Key::where([['userid', '=', $userid], ['exchange', '=', 'Bittrex']])->exists())
      {
        $key = Key::where([['userid', '=', $userid], ['exchange', '=', 'Bittrex']])->first();
      } else {
        return "no key";
      }

      $apikey = decrypt($key->public);
      $apisecret = decrypt($key->private);
      $end = time();

      $bittrex = new Bittrex($apikey, $apisecret);

      $trades = $bittrex->getOrderHistory();
      $withdraws = $bittrex->getWithdrawalHistory();
      $deposits = $bittrex->getDepositHistory();

      // Deposits
      foreach($deposits as $deposit)
      {
        if(!Transaction::where('tradeid', $userid . $deposit->TxId)->select('id')->exists())
        {
          $token = $deposit->Currency;
          $market = "BTC";
          $price = 0;
          $amount = $deposit->Amount;
          $date = strtotime($deposit->LastUpdated);
          $date = date('Y-m-d H:i:s', $date);
          $type = "DEPOSIT";
          $exchange = "Bittrex";
          $fee_currency = "BTC";
          $tradeid = $deposit->TxId;
          $fee = 0;

          if($token == "ANS")
          {
            $token = "NEO";
          } elseif($token == "SEC") {
            $token = "SAFEX";
          } elseif($token == "BCC") {
            $token = "BCH";
          }

          // This gets historical prices
          $client = new \GuzzleHttp\Client();
          $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym='.$token.'&tsyms=BTC&ts='.strtotime($date).'&extraParams=Altpocket');
          $response = $res->getBody();
          $prices = json_decode($response, true);

          if(!isSet($prices['Response']))
          {
            $price = $prices[$token]['BTC'];
          } else {
            $price = 0;
          }



          $this->makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid, $total = 0);
        }
      }

      // Trades
      foreach($trades as $trade)
      {
        $date = strtotime($trade->TimeStamp);
        $date = date('Y-m-d H:i:s', $date);
        if(!Transaction::where('tradeid', $userid . $trade->OrderUuid)->select('id')->exists())  {
          $date = strtotime($trade->TimeStamp);
          $date = date('Y-m-d H:i:s', $date);

          $pair = explode('-', $trade->Exchange);
          $market = $pair[0];
          $token = $pair[1];
          $price = $trade->PricePerUnit;
          $total = $trade->Price;
          $amount = ($trade->Quantity - $trade->QuantityRemaining);
          $exchange = "Bittrex";
          $fee_currency = $market;

          if($trade->OrderType == "LIMIT_BUY")
          {
            $type = "BUY";
          } else {
            $type = "SELL";
          }

          if($token == "ANS")
          {
            $token = "NEO";
          } elseif($token == "SEC") {
            $token = "SAFEX";
          } elseif($token == "BCC") {
            $token = "BCH";
          }

          $tradeid = $trade->OrderUuid;
          $fee = $trade->Commission;

          $this->makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid, $total);
        }
      }

      // Withdraws
      foreach($withdraws as $withdraw)
      {
        if(!Transaction::where('tradeid', $userid . $withdraw->TxId)->select('id')->exists())
        {
          $token = $withdraw->Currency;
          $market = "BTC";
          $price = 0;
          $amount = $withdraw->Amount;
          $date = strtotime($withdraw->Opened);
          $date = date('Y-m-d H:i:s', $date);
          $type = "WITHDRAW";
          $exchange = "Bittrex";
          $fee_currency = $withdraw->Currency;
          $tradeid = $withdraw->TxId;
          $fee = $withdraw->TxCost;

          if($token == "ANS")
          {
            $token = "NEO";
          } elseif($token == "SEC") {
            $token = "SAFEX";
          } elseif($token == "BCC") {
            $token = "BCH";
          }

          // This gets historical prices
          $client = new \GuzzleHttp\Client();
          $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym='.$token.'&tsyms=BTC&ts='.strtotime($date).'&extraParams=Altpocket');
          $response = $res->getBody();
          $prices = json_decode($response, true);

          if(!isSet($prices['Response']))
          {
            $price = $prices[$token]['BTC'];
          } else {
            $price = 0;
          }

          $this->makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid, $total = 0);
        }
      }

      $transactions = Transaction::where([['userid', '=', $userid], ['handled', '=', '0'], ['exchange', '=', 'Bittrex']])->orderBy('date')->get();

      foreach($transactions as $transaction)
      {
        $this->makeHolding($transaction, $remove_withdraws);
      }

      return Redirect::back();
    }

    public function coinbase(Request $request)
    {
      Cache::forget('myHoldings:'.Auth::user()->id);
      Cache::forget('portfolioChart:'.Auth::user()->id);
      $userid = Auth::user()->id;
      $key = Key::where([['userid', '=', $userid], ['exchange', '=', 'Coinbase']])->first();
      CoinBase::checkExpiry($key);
      $access_token = decrypt($key->public); // Access token
      $refresh_token = decrypt($key->private); // Refresh token

      $configuration = Configuration::oauth($access_token, $refresh_token);
      $client = ClientCoinbase::create($configuration);

      $accounts = $client->getAccounts();

      if($request->get('remove'))
      {
        $remove_withdraws = 1;
      } else {
        $remove_withdraws = 0;
      }

      foreach($accounts as $account)
      {
        $buys = $client->getAccountBuys($account);
        $sells = $client->getSells($account);
        $transactions = $client->getAccountTransactions($account);

        // Buys
        foreach($buys as $buy)
        {
          $buy = $buy->getRawData();

          if(!Transaction::where('tradeid', $userid . $buy['transaction']['id'])->select('id')->exists() && $buy['status'] == "completed")
          {

            $payment_method = $client->getPaymentMethod($buy['payment_method']['id']);
            $payment_type = $payment_method->getType();

            $token = $buy['amount']['currency'];
            $market = $buy['total']['currency'];
            $price = $buy['total']['amount'] / $buy['amount']['amount'];
            $amount = $buy['amount']['amount'];
            $date = date('Y-m-d H:i:s', strtotime($buy['created_at']));
            $type = "BUY";
            $exchange = "Coinbase";
            $fee_currency = $buy['fees'][0]['amount']['currency'];
            $fee = $buy['fees'][0]['amount']['amount'];
            $tradeid = $buy['transaction']['id'];
            $total = $price * $amount;

            if($payment_type != "fiat_account")
            {
              $deduct = "off";
            } else {
              $deduct = "on";
            }



            // Userid = User ID to link transactions with
            // Token = Tokens symbol, ex.. BTC, ETH, BCH
            // Market = Transactions Market, ex.. BTC, ETH, USD, USDT
            // Price = Price per token
            // Amount = Amount of tokens
            // Date = Transaction date in YYYY-MM-DD HH:MM:SS
            // Type = Transaction type ex.. BUY, SELL, DEPOSIT, WITHDRAW
            // Exchange = Transaction Exchange, capital first letter
            // Fee_Currency = What currency the fee is in, can either be the market or token, ex.. BTC, XVG, ETH, ETC...
            // Fee = The fee of the transaction.
            // Tradeid = The exchanges trade id.
            // Total = Price * Amount
            $this->makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid, $total, $deduct);
          }
        }

        // Sells
        foreach($sells as $sell)
        {
          $sell = $sell->getRawData();
          if(!Transaction::where('tradeid', $userid . $sell['transaction']['id'])->select('id')->exists())
          {
            $token = $sell['amount']['currency'];
            $market = $sell['total']['currency'];
            $price = $sell['total']['amount'] / $sell['amount']['amount'];
            $amount = $sell['amount']['amount'];
            $date = date('Y-m-d H:i:s', strtotime($sell['created_at']));
            $type = "SELL";
            $exchange = "Coinbase";
            $fee_currency = $sell['fees'][0]['amount']['currency'];
            $fee = $sell['fees'][0]['amount']['amount'];
            $tradeid = $sell['transaction']['id'];
            $total = $price * $amount;

            // Userid = User ID to link transactions with
            // Token = Tokens symbol, ex.. BTC, ETH, BCH
            // Market = Transactions Market, ex.. BTC, ETH, USD, USDT
            // Price = Price per token
            // Amount = Amount of tokens
            // Date = Transaction date in YYYY-MM-DD HH:MM:SS
            // Type = Transaction type ex.. BUY, SELL, DEPOSIT, WITHDRAW
            // Exchange = Transaction Exchange, capital first letter
            // Fee_Currency = What currency the fee is in, can either be the market or token, ex.. BTC, XVG, ETH, ETC...
            // Fee = The fee of the transaction.
            // Tradeid = The exchanges trade id.
            // Total = Price * Amount
            $this->makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid, $total);
          }
        }

        //Deposits & Withdraws

        foreach($transactions as $transaction)
        {
          $data = $transaction->getRawData();

          if($data['type'] == "send" && $data['status'] == "completed")
          {
            if(!Transaction::where('tradeid', $userid . $data['id'])->select('id')->exists())
            {
              // Coinbase transactions are odd, if the amount is negative that means that its a withdraw.
              $token = $data['amount']['currency'];
              $market = "BTC";
              $price = 0;
              $amount = $data['amount']['amount'];
              $date = date('Y-m-d H:i:s', strtotime($data['created_at']));
              if($amount < 0)
              {
                $type = "WITHDRAW";
              } else {
                $type = "DEPOSIT";
              }
              $exchange = "Coinbase";
              $fee_currency = "BTC";
              $fee = 0;
              $tradeid = $data['id'];
              $amount = abs($amount);

              // This gets historical prices
              $wat = new \GuzzleHttp\Client();
              $res = $wat->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym='.$token.'&tsyms=BTC&ts='.strtotime($date).'&extraParams=Altpocket');
              $response = $res->getBody();
              $prices = json_decode($response, true);

              if(!isSet($prices['Response']))
              {
                $price = $prices[$token]['BTC'];
              } else {
                $price = 0;
              }


              $this->makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid, $total = 0);
            }
          }
        }

        $transactions = Transaction::where([['userid', '=', $userid], ['handled', '=', '0']])->orderBy('date')->get();

        foreach($transactions as $transaction)
        {
          $this->makeHolding($transaction, 1);
        }

    }
    }


    public function calculate()
    {
      $transactions = Transaction::where([['userid', '=', Auth::user()->id]])->orderBy('date')->get();
      $amount = 0;
      foreach($transactions as $transaction)
      {
        if($transaction->type == "BUY")
        {
          $amount -= $transaction->total;
        } elseif($transaction->type == "SELL") {
          $amount += $transaction->total - $transaction->fee;
        } elseif($transaction->type == "DEPOSIT")
        {
          if($transaction->token == "BTC")
          {
            $amount += $transaction->amount;
          }
        } else {
          if($transaction->token == "BTC")
          {
            $amount -= $transaction->amount;
          }
        }
      }
      return number_format($amount, 8);
    }

    public function finish()
    {
      $transactions = Transaction::where([['userid', '=', Auth::user()->id], ['handled', '=', '0'], ['exchange', '=', 'Bittrex']])->orderBy('date')->get();

      foreach($transactions as $transaction)
      {
        $this->makeHolding($transaction);
      }

    }

    // API Stuff
    public function getFiat($fiat, $date)
    {
      $client = new \GuzzleHttp\Client();
      $historicdate = date('Y-m-d', strtotime($date));
      $res = $client->request('GET', 'http://api.fixer.io/'.$historicdate.'?base=USD&symbols='.$fiat);
      $response = $res->getBody();
      $multiplier = json_decode($response, true);
      $m = $multiplier['rates'][$fiat];
      return $m;
    }

    public function getHistorical($coin, $when)
    {
      $when = date('Y-m-d', strtotime($when));
        $client = new Client(['http_errors' => false]);
        $res = $client->request('GET', "https://graphs.coinmarketcap.com/currencies/" . $coin);
        $code = $res->getStatusCode();
        if ($code == 200) {
            $obj = json_decode($res->getBody());
            $price = array();
            foreach ($obj->price_usd as $prices) {
                $date = explode("T", date('c', $prices[0] / 1000))[0];
                if ($date == $when) {
                    $price["USD"] = $prices[1];
                }
            }
            foreach ($obj->price_btc as $prices) {
                $date = explode("T", date('c', $prices[0] / 1000))[0];
                if ($date == $when) {
                    $price["BTC"] = $prices[1];
                }
            }
            if (count($price) > 0) {
                return $price;
            } else {
                return array("error" => "No price found!");
            }
        } else {
            return array("error" => $code);
        }
    }

    public function getEthHistorical($when)
    {
      return History::getHistorical($when);
    }


    // Dynamic function for adding transactions into the system, no matter what exchange.

    public function makeTransaction($userid, $token, $market, $price, $amount, $date, $type, $exchange, $fee_currency, $fee, $tradeid, $total = 0, $deduct = "on") {

      $token = Cache::remember("token:".$token, 1440, function() use ($token) {
        return Crypto::where('symbol', $token)->selectRaw('symbol, cmc_id, name, id')->first();
      });

      $historical = History::getHistorical($date); // Historic price based on date of transaction
      if($market != "USD" && $market != "BTC" && $market != "ETH" && $market != "USDT")
      {
        $client = new \GuzzleHttp\Client();
        $historicdate = date('Y-m-d', strtotime($date));
        $res = $client->request('GET', 'http://api.fixer.io/'.$historicdate.'?base=USD&symbols='.$market);
        $response = $res->getBody();
        $multiplier = json_decode($response, true);
        $m = $multiplier['rates'][$market];
      } else {
        $m = 1;
      }

      $transaction = new Transaction;
      $transaction->userid = $userid;
      $transaction->tokenid = $token->id;
      $transaction->token_cmc_id = $token->cmc_id;
      $transaction->token = $token->symbol;
      $transaction->token_name = $token->name;
      $transaction->market = $market;
      $transaction->pair = $market."-".$token->symbol;
      $transaction->exchange = $exchange;
      $transaction->type = $type;
      $transaction->fee_currency = $fee_currency;
      $transaction->price = $price;
      $transaction->fee = $fee;
      $transaction->total = $total;

      if($market == "USD" || $market == "BTC" || $market == "USDT")
      {
        $transaction->btc = $historical->USD;
      } elseif($market == "ETH") {
        $transaction->btc = $historical->ETH;
      } elseif($market == "XMR") {
        $transaction->btc = $historical->XMR;
      } else {
        $transaction->btc = $historical->USD * $m;
      }
      $transaction->amount = $amount;
      $transaction->notes = "";
      $transaction->tradeid = $userid . $tradeid;
      $transaction->date = $date;
      $transaction->deduct = $deduct;

      if($market == "BTC")
      {
        $transaction->paid_btc = $transaction->price * $transaction->amount;
        $transaction->paid_usd = $transaction->paid_btc * $transaction->btc;
        $transaction->paid_market = $transaction->paid_btc;
      } elseif($market == "ETH")
      {
        $transaction->paid_market = ($transaction->amount * $transaction->price);
        $transaction->paid_btc = $transaction->paid_market / $transaction->btc;
        $transaction->paid_usd = $transaction->paid_market * ($historical->USD / $historical->ETH);
      } elseif($market == "USDT")
      {
        $usdt = Crypto::where('symbol', 'USDT')->select('price_usd', 'price_btc')->first();

        $transaction->paid_market = ($transaction->amount * $transaction->price);
        $transaction->paid_btc = $transaction->paid_market * $usdt->price_btc;
        $transaction->paid_usd = $transaction->paid_market * $usdt->price_usd;
      } elseif($market == "XMR")
      {
        $transaction->paid_market = ($transaction->amount * $transaction->price);
        $transaction->paid_btc = $transaction->paid_market / $transaction->btc;
        $transaction->paid_usd = $transaction->paid_market * ($historical->USD / $historical->XMR);
      } else {
        $transaction->paid_market = ($transaction->amount * $transaction->price);
        $transaction->paid_usd = ($transaction->paid_market) * (1 / $m);
        $transaction->paid_btc = $transaction->paid_market / $transaction->btc;
      }
        $transaction->save();
    }

    public function makeHolding($transaction, $remove_withdraws)
    {
      // Handle deduction or adding to holding
      if($transaction->deduct == "on")
      {
        if($transaction->type == "BUY") // If the type of transaction is buy, we deduct from a holding
        {
          $newholding = Holding::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['exchange', '=', $transaction->exchange], ['market', '=', $transaction->market]])->first();

          if($transaction->fee > 0 && $transaction->fee_currency == $transaction->market)
          {
            $remove = $transaction->fee;
          } else {
            $remove = 0;
          }
          // This is what will be removed!
          $newamount = $transaction->total + $remove;


          if($newholding)
          {
            if($newholding->amount > $newamount)
            {
              $newholding->paid_market = ($newholding->paid_market / $newholding->amount) * ($newholding->amount - $newamount);
              $newholding->paid_btc = ($newholding->paid_btc / $newholding->amount) * ($newholding->amount - $newamount);
              $newholding->paid_usd = ($newholding->paid_usd / $newholding->amount) * ($newholding->amount - $newamount);
              $newholding->amount -= $newamount;
              $newholding->save();
            } else {
              $newholding->paid_market = 0;
              $newholding->paid_btc = 0;
              $newholding->paid_usd = 0;
              $newholding->amount = 0;
              $newholding->save();
            }
          } else {
            $newholding = Holding::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['exchange', '!=', $transaction->exchange], ['market', '=', $transaction->market]])->first();

            if($newholding)
            {
              if($newholding->amount > $newamount)
              {
                $newholding->paid_market = ($newholding->paid_market / $newholding->amount) * ($newholding->amount - $newamount);
                $newholding->paid_btc = ($newholding->paid_btc / $newholding->amount) * ($newholding->amount - $newamount);
                $newholding->paid_usd = ($newholding->paid_usd / $newholding->amount) * ($newholding->amount - $newamount);
                $newholding->amount -= $newamount;
                $newholding->save();
              } else {
                $newholding->paid_market = 0;
                $newholding->paid_btc = 0;
                $newholding->paid_usd = 0;
                $newholding->amount = 0;
                $newholding->save();
              }
            } else {
              $newholding = Holding::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['exchange', '=', $transaction->exchange], ['market', '!=', $transaction->market]])->first();

              if($newholding)
              {
                if($newholding->amount > $newamount)
                {
                  $newholding->paid_market = ($newholding->paid_market / $newholding->amount) * ($newholding->amount - $newamount);
                  $newholding->paid_btc = ($newholding->paid_btc / $newholding->amount) * ($newholding->amount - $newamount);
                  $newholding->paid_usd = ($newholding->paid_usd / $newholding->amount) * ($newholding->amount - $newamount);
                  $newholding->amount -= $newamount;
                  $newholding->save();
                } else {
                  $newholding->paid_market = 0;
                  $newholding->paid_btc = 0;
                  $newholding->paid_usd = 0;
                  $newholding->amount = 0;
                  $newholding->save();
                }
              } else {
                $newholding = Holding::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['exchange', '!=', $transaction->exchange], ['market', '!=', $transaction->market]])->first();

                if($newholding)
                {
                  if($newholding->amount > $newamount)
                  {
                    $newholding->paid_market = ($newholding->paid_market / $newholding->amount) * ($newholding->amount - $newamount);
                    $newholding->paid_btc = ($newholding->paid_btc / $newholding->amount) * ($newholding->amount - $newamount);
                    $newholding->paid_usd = ($newholding->paid_usd / $newholding->amount) * ($newholding->amount - $newamount);
                    $newholding->amount -= $newamount;
                    $newholding->save();
                  } else {
                    $newholding->paid_market = 0;
                    $newholding->paid_btc = 0;
                    $newholding->paid_usd = 0;
                    $newholding->amount = 0;
                    $newholding->save();
                  }
                } else {
                  Alert::error('You had no holding to deduct from, please make an '. $transaction->token . ' holding before you toggle deduct from holding.', 'Oops..')->persistent('Close');
                  return Redirect::back();
                }
              }
            }
          }
          echo $transaction;
        } elseif($transaction->type == "SELL")
        {
          $newholding = Holding::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['exchange', '=', $transaction->exchange], ['market', '=', 'BTC']])->first();

          if($newholding)
          {
            if($transaction->fee > 0 && $transaction->fee_currency == $transaction->market)
            {
              $newholding->amount += $transaction->total - $transaction->fee;
            } else {
              $newholding->amount += $transaction->total;
            }
            $newholding->paid_market += $transaction->paid_market;
            $newholding->paid_btc += $transaction->paid_btc;
            $newholding->paid_usd += $transaction->paid_usd;

            $newholding->save();

          } else {
            $newholding = new Holding;
            $newholding->userid = $transaction->userid;
            $newholding->amount = $transaction->total;
            if($transaction->fee > 0 && $transaction->fee_currency == $transaction->market)
            {
              $newholding->amount -= $transaction->fee;
            }
            $newholding->token = $transaction->market;
            $newholding->market = "BTC";
            $newholding->exchange = $transaction->exchange;

            // Set token ID properly
            if($transaction->market == "BTC" || $transaction->market == "ETH" || $transaction->market == "USDT")
            {
              $newtoken = Crypto::where('symbol', $transaction->market)->select('id', 'name')->first();
              $newholding->tokenid = $newtoken->id;
              $newholding->name = $newtoken->name;
            }
            else {
              $newholding->tokenid = 0;
              $newholding->name = $transaction->market;
            }
            $newholding->paid_market = $transaction->paid_market;
            $newholding->paid_btc = $transaction->paid_btc;
            $newholding->paid_usd = $transaction->paid_usd;
            $newholding->save();
          }
        }
      }





      $holding = Holding::where([['userid', '=', $transaction->userid], ['tokenid', '=', $transaction->tokenid], ['exchange', '=', $transaction->exchange], ['market', '=', $transaction->market]])->first();

      if($transaction->type == "BUY" || $transaction->type == "DEPOSIT")
      {
        if($holding)
        {
          if($transaction->fee > 0 && $transaction->fee_currency == $transaction->token)
          {
            $remove = $transaction->fee;
          } else {
            $remove = 0;
          }
          $holding->amount += $transaction->amount - $remove;
          $holding->paid_btc += $transaction->paid_btc;
          $holding->paid_market += $transaction->paid_market;
          $holding->paid_usd += $transaction->paid_usd;

          $holding->save();
        } else {

          if($transaction->fee > 0 && $transaction->fee_currency == $transaction->token)
          {
            $remove = $transaction->fee;
          } else {
            $remove = 0;
          }

          $holding = new Holding;
          $holding->userid = $transaction->userid;
          $holding->tokenid = $transaction->tokenid;
          $holding->token = $transaction->token;
          $holding->name = $transaction->token_name;
          $holding->amount = $transaction->amount - $remove;
          $holding->exchange = $transaction->exchange;
          $holding->market = $transaction->market;

          // Holding paid has to be in USD to convert it correctly to each different fiat
          $holding->paid_btc = $transaction->paid_btc;
          $holding->paid_market = $transaction->paid_market;
          $holding->paid_usd = $transaction->paid_usd;
          $holding->save();
        }
      } elseif($transaction->type == "SELL" || $transaction->type == "WITHDRAW" && $remove_withdraws == 1)
      {
        if($holding)
        {
          if($transaction->type == "WITHDRAW" && $transaction->fee_currency == $transaction->token)
          {
            $transaction->amount += $transaction->fee;
          }


          // Make sure the amount is more than the transaction amount, otherwise set it all to 0.
          if($holding->amount > $transaction->amount)
          {
            $holding->paid_market = ($holding->paid_market / $holding->amount) * ($holding->amount - $transaction->amount);
            $holding->paid_usd = ($holding->paid_usd / $holding->amount) * ($holding->amount - $transaction->amount);
            $holding->paid_btc = ($holding->paid_btc / $holding->amount) * ($holding->amount - $transaction->amount);
            $holding->amount -= $transaction->amount;
            if($holding->amount < 0.00000001)
            {
              $holding->amount = 0;
              $holding->paid_market = 0;
              $holding->paid_usd = 0;
              $holding->paid_btc = 0;
            }
            $holding->save();
          } else {
            $holding->paid_market = 0;
            $holding->paid_usd = 0;
            $holding->paid_btc = 0;
            $holding->amount = 0;
            $holding->save();
          }
        } else {
          $holding = Holding::where([['userid', '=', $transaction->userid], ['tokenid', '=', $transaction->tokenid], ['exchange', '=', $transaction->exchange], ['market', '!=', $transaction->market]])->first();

          if($holding)
          {
            if($holding->amount > $transaction->amount)
            {
              $holding->paid_market = ($holding->paid_market / $holding->amount) * ($holding->amount - $transaction->amount);
              $holding->paid_usd = ($holding->paid_usd / $holding->amount) * ($holding->amount - $transaction->amount);
              $holding->paid_btc = ($holding->paid_btc / $holding->amount) * ($holding->amount - $transaction->amount);
              $holding->amount -= $transaction->amount;
              $holding->save();
            } else {
              $holding->paid_market = 0;
              $holding->paid_usd = 0;
              $holding->paid_btc = 0;
              $holding->amount = 0;
              $holding->save();
            }
          } else {
            $holding = new Holding;
            $holding->paid_market = 0;
            $holding->paid_usd = 0;
            $holding->amount = 0;;
            $holding->token = $transaction->token;
            $holding->name = $transaction->token_name;
            $holding->exchange = $transaction->exchange;
            $holding->tokenid = $transaction->tokenid;
            $holding->market = $transaction->market;
            $holding->userid = $transaction->userid;
            $holding->save();
          }
      }
    }

      $transaction->handled = 1;
      $transaction->save();
    }


    public function addTransaction(Request $request)
    {
        // Lets make this as pretty as possible
        $user = Auth::user();
        $token = Crypto::where('id', $request->get('token'))->first();
        $amount = $request->get('amount');
        $price = $request->get('price');
        $market = $request->get('market');
        $exchange = $request->get('exchange');
        $price_input = $request->get('price_input');
        $date = $request->get('date');
        $fee_currency = $request->get('fee_currency');
        $fee = $request->get('fee');
        $notes = $request->get('notes');
        $type = $request->get('type');
        $deduct = $request->get('deduct');

        // Clear Holding logs
        Cache::forget('myHoldings:'.Auth::user()->id);
        Cache::forget('myHistory:'.Auth::user()->id);

        /* Validation */
        $rules = [
          'token' => 'required',
          'exchange' => 'required',
          'date' => 'required|date',
          'market' => 'required',
          'price_input' => 'required',
          'amount' => 'required',
        ];

        $messages = [
            'token.required' => 'You must enter a token available from the list.',
            'amount.required'  => 'You must enter an amount of tokens bought.',
            'date.date'  => 'You must enter a correct date in the date field',
            'date.required'  => 'You must enter the date you made the investment.',
            'price.required'  => 'You must enter the price of the token at sell/buy point.',
            'exchange.required' => 'You must select an exchange.',
            'market.required' => 'You must select an market',
            'price_input.required' => 'You must select an price input.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();
        if($validator->fails()){
            if($errors->first('token')){
              Alert::error($errors->first('token'), 'Transaction failed')->persistent('Close');;
            } elseif($errors->first('price')) {
              Alert::error($errors->first('price'), 'Transaction failed')->persistent('Close');;
            } elseif($errors->first('amount')) {
              Alert::error($errors->first('amount'), 'Transaction failed')->persistent('Close');
            } elseif($errors->first('date')) {
              Alert::error($errors->first('date'), 'Transaction failed')->persistent('Close');
            } elseif($errors->first('exchange')) {
              Alert::error($errors->first('exchange'), 'Transaction failed')->persistent('Close');
            } elseif($errors->first('market')) {
              Alert::error($errors->first('market'), 'Transaction failed')->persistent('Close');
            } elseif($errors->first('price_input')) {
              Alert::error($errors->first('price_input'), 'Transaction failed')->persistent('Close');
            }
            return Redirect::back();
        }
        // Manual Validation here:
        if(!$request->get('amount') || $request->get('amount') <= 0.000000001 || $amount <= 0.000000001){
                Alert::error('You must enter an amount of tokens bought.', 'Transaction failed')->persistent('Close');
                return Redirect::back();
        }

        if($request->get('amount') && !is_numeric($amount)){
          Alert::error('You must enter a numeric value in the amount field.', 'Transaction failed')->persistent('Close');
          return Redirect::back();
        }


        /* End Validation */

        /* Start Creation */

        if($token)
        {
          $historical = History::getHistorical($date); // Historic price based on date of transaction
          if($market != "USD" && $market != "BTC" && $market != "ETH" && $market != "USDT")
          {
            $client = new \GuzzleHttp\Client();
            $historicdate = date('Y-m-d', strtotime($date));
            $res = $client->request('GET', 'http://api.fixer.io/'.$historicdate.'?base=USD&symbols='.$market);
            $response = $res->getBody();
            $multiplier = json_decode($response, true);
            $m = $multiplier['rates'][$market];
          } else {
            $m = 1;
          }

          $transaction = new Transaction;
          $transaction->userid = $user->id;
          $transaction->tokenid = $token->id;
          $transaction->token_cmc_id = $token->cmc_id;
          $transaction->token = $token->symbol;
          $transaction->market = $market;
          $transaction->pair = $market."-".$token->symbol;
          $transaction->exchange = $exchange;
          $transaction->type = $type;
          $transaction->fee_currency = $fee_currency;
          if($price_input == "intotal")
          {
            $transaction->price = $price / $amount;
          } else {
            $transaction->price = $price;
          }
          if($market == "USD" || $market == "BTC" || $market == "USDT")
          {
            $transaction->btc = $historical->USD;
          } elseif($market == "ETH") {
            $transaction->btc = $historical->ETH;
          } elseif($market == "XMR") {
            $transaction->btc = $historical->XMR;
          } else {
            $transaction->btc = $historical->USD * $m;
          }

          $transaction->amount = $amount;
          $transaction->fee = $fee;
          $transaction->notes = $notes;
          $transaction->tradeid = $user->id . "-Altpocket-" . time();
          $transaction->date = $date;
          if($deduct == "on")
          {
            $transaction->deduct = "on";
          } else {
            $transaction->deduct = "off";
          }

          // Handle paid transaction stuff

          if($market == "BTC")
          {
            $transaction->paid_btc = $transaction->price * $transaction->amount;
            $transaction->paid_usd = $transaction->paid_btc * $transaction->btc;
            $transaction->paid_market = $transaction->paid_btc;
          } elseif($market == "ETH")
          {
            $transaction->paid_market = ($transaction->amount * $transaction->price);
            $transaction->paid_btc = $transaction->paid_market / $transaction->btc;
            $transaction->paid_usd = $transaction->paid_market * ($historical->USD / $historical->ETH);
          } elseif($market == "USDT")
          {
            $usdt = Crypto::where('symbol', 'USDT')->select('price_usd', 'price_btc')->first();

            $transaction->paid_market = ($transaction->amount * $transaction->price);
            $transaction->paid_btc = $transaction->paid_market * $usdt->price_btc;
            $transaction->paid_usd = $transaction->paid_market * $usdt->price_usd;
          } elseif($market == "XMR")
          {
            $transaction->paid_market = ($transaction->amount * $transaction->price);
            $transaction->paid_btc = $transaction->paid_market / $transaction->btc;
            $transaction->paid_usd = $transaction->paid_market * ($historical->USD / $historical->XMR);
          } else {
            $transaction->paid_market = ($transaction->amount * $transaction->price);
            $transaction->paid_usd = ($transaction->paid_market) * (1 / $m);
            $transaction->paid_btc = $transaction->paid_market / $transaction->btc;
          }


          $transaction->save();
        }

        // Handle deduction or adding to holding
        if($deduct == "on")
        {
          if($type == "BUY") // If the type of transaction is buy, we deduct from a holding
          {
            $newholding = Holding::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['exchange', '=', $transaction->exchange], ['market', '=', $transaction->market]])->first();

            if($fee > 0 && $fee_currency == $transaction->market)
            {
              $remove = $fee;
            } else {
              $remove = 0;
            }
            // This is what will be removed!
            $newamount = ($transaction->amount * $transaction->price) + $remove;

            if($newholding)
            {
              if($newholding->amount > $newamount)
              {
                $newholding->paid_market = ($newholding->paid_market / $newholding->amount) * ($newholding->amount - $newamount);
                $newholding->paid_btc = ($newholding->paid_btc / $newholding->amount) * ($newholding->amount - $newamount);
                $newholding->paid_usd = ($newholding->paid_usd / $newholding->amount) * ($newholding->amount - $newamount);
                $newholding->amount -= $newamount;
                $newholding->save();
              } else {
                $newholding->paid_market = 0;
                $newholding->paid_btc = 0;
                $newholding->paid_usd = 0;
                $newholding->amount = 0;
                $newholding->save();
              }
            } else {
              $newholding = Holding::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['exchange', '!=', $transaction->exchange], ['market', '=', $transaction->market]])->first();

              if($newholding)
              {
                if($newholding->amount > $newamount)
                {
                  $newholding->paid_market = ($newholding->paid_market / $newholding->amount) * ($newholding->amount - $newamount);
                  $newholding->paid_btc = ($newholding->paid_btc / $newholding->amount) * ($newholding->amount - $newamount);
                  $newholding->paid_usd = ($newholding->paid_usd / $newholding->amount) * ($newholding->amount - $newamount);
                  $newholding->amount -= ($transaction->amount * $transaction->price);
                  $newholding->save();
                } else {
                  $newholding->paid_market = 0;
                  $newholding->paid_btc = 0;
                  $newholding->paid_usd = 0;
                  $newholding->amount = 0;
                  $newholding->save();
                }
              } else {
                $newholding = Holding::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['exchange', '=', $transaction->exchange], ['market', '!=', $transaction->market]])->first();

                if($newholding)
                {
                  if($newholding->amount > $newamount)
                  {
                    $newholding->paid_market = ($newholding->paid_market / $newholding->amount) * ($newholding->amount - $newamount);
                    $newholding->paid_btc = ($newholding->paid_btc / $newholding->amount) * ($newholding->amount - $newamount);
                    $newholding->paid_usd = ($newholding->paid_usd / $newholding->amount) * ($newholding->amount - $newamount);
                    $newholding->amount -= ($transaction->amount * $transaction->price);
                    $newholding->save();
                  } else {
                    $newholding->paid_market = 0;
                    $newholding->paid_btc = 0;
                    $newholding->paid_usd = 0;
                    $newholding->amount = 0;
                    $newholding->save();
                  }
                } else {
                  $newholding = Holding::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['exchange', '!=', $transaction->exchange], ['market', '!=', $transaction->market]])->first();

                  if($newholding)
                  {
                    if($newholding->amount > $newamount)
                    {
                      $newholding->paid_market = ($newholding->paid_market / $newholding->amount) * ($newholding->amount - $newamount);
                      $newholding->paid_btc = ($newholding->paid_btc / $newholding->amount) * ($newholding->amount - $newamount);
                      $newholding->paid_usd = ($newholding->paid_usd / $newholding->amount) * ($newholding->amount - $newamount);
                      $newholding->amount -= ($transaction->amount * $transaction->price);
                      $newholding->save();
                    } else {
                      $newholding->paid_market = 0;
                      $newholding->paid_btc = 0;
                      $newholding->paid_usd = 0;
                      $newholding->amount = 0;
                      $newholding->save();
                    }
                  } else {
                    Alert::error('You had no holding to deduct from, please make an '. $transaction->token . ' holding before you toggle deduct from holding.', 'Oops..')->persistent('Close');
                    return Redirect::back();
                  }
                }
              }
            }
          } else
          {
            $newholding = Holding::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['exchange', '=', $transaction->exchange], ['market', '=', 'BTC']])->first();

            if($newholding)
            {
              if($fee > 0 && $fee_currency == $transaction->market)
              {
                $newholding->amount += ($transaction->amount * $transaction->price) - $fee;
              } else {
                $newholding->amount += $transaction->amount * $transaction->price;
              }
              $newholding->paid_market += $transaction->paid_market;
              $newholding->paid_btc += $transaction->paid_btc;
              $newholding->paid_usd += $transaction->paid_usd;

              $newholding->save();

            } else {
              $newholding = new Holding;
              $newholding->userid = $transaction->userid;
              $newholding->amount = $transaction->amount * $transaction->price;
              if($fee != 0 && $fee_currency == $transaction->market)
              {
                $newholding->amount -= $fee;
              }
              $newholding->token = $transaction->market;
              $newholding->market = "BTC";
              $newholding->exchange = $transaction->exchange;

              // Set token ID properly
              if($transaction->market == "BTC" || $transaction->market == "ETH" || $transaction->market == "USDT")
              {
                $newtoken = Crypto::where('symbol', $transaction->market)->select('id', 'name')->first();
                $newholding->tokenid = $newtoken->id;
                $newholding->name = $newtoken->name;
              }
              else {
                $newholding->tokenid = 0;
                $newholding->name = $transaction->market;
              }
              $newholding->paid_market = $transaction->paid_market;
              $newholding->paid_btc = $transaction->paid_btc;
              $newholding->paid_usd = $transaction->paid_usd;
              $newholding->save();
            }
          }
        }

        // Add/remove from holding when selling or buying not when using "deduct from" or "add to"

        $holding = Holding::where([['userid', '=', $transaction->userid], ['tokenid', '=', $transaction->tokenid], ['exchange', '=', $transaction->exchange], ['market', '=', $transaction->market]])->first();

        if($type == "BUY")
        {
          if($holding)
          {
            if($fee > 0 && $fee_currency == $transaction->token)
            {
              $remove = $fee;
            } else {
              $remove = 0;
            }
            $holding->amount += $transaction->amount - $remove;
            $holding->paid_btc += $transaction->paid_btc;
            $holding->paid_market += $transaction->paid_market;
            $holding->paid_usd += $transaction->paid_usd;

            $holding->save();
          } else {

            if($fee > 0 && $fee_currency == $transaction->token)
            {
              $remove = $fee;
            } else {
              $remove = 0;
            }

            $holding = new Holding;
            $holding->userid = $transaction->userid;
            $holding->tokenid = $transaction->tokenid;
            $holding->token = $transaction->token;
            $holding->name = $token->name;
            $holding->amount = $transaction->amount - $remove;
            $holding->exchange = $transaction->exchange;
            $holding->market = $transaction->market;

            // Holding paid has to be in USD to convert it correctly to each different fiat
            $holding->paid_btc = $transaction->paid_btc;
            $holding->paid_market = $transaction->paid_market;
            $holding->paid_usd = $transaction->paid_usd;
            $holding->save();
          }
        } else
        {
          if($holding)
          {
            // Make sure the amount is more than the transaction amount, otherwise set it all to 0.
            if($holding->amount > $transaction->amount)
            {
              $holding->paid_market = ($holding->paid_market / $holding->amount) * ($holding->amount - $transaction->amount);
              $holding->paid_usd = ($holding->paid_usd / $holding->amount) * ($holding->amount - $transaction->amount);
              $holding->paid_btc = ($holding->paid_btc / $holding->amount) * ($holding->amount - $transaction->amount);
              $holding->amount -= $transaction->amount;
              $holding->save();
            } else {
              $holding->paid_market = 0;
              $holding->paid_usd = 0;
              $holding->paid_btc = 0;
              $holding->amount = 0;
              $holding->save();
            }
          } else {
            $holding = Holding::where([['userid', '=', $transaction->userid], ['tokenid', '=', $transaction->tokenid], ['exchange', '!=', $transaction->exchange], ['market', '=', $transaction->market]])->first();

            if($holding)
            {
              if($holding->amount > $transaction->amount)
              {
                $holding->paid_market = ($holding->paid_market / $holding->amount) * ($holding->amount - $transaction->amount);
                $holding->paid_usd = ($holding->paid_usd / $holding->amount) * ($holding->amount - $transaction->amount);
                $holding->paid_btc = ($holding->paid_btc / $holding->amount) * ($holding->amount - $transaction->amount);
                $holding->amount -= $transaction->amount;
                $holding->save();
              } else {
                $holding->paid_market = 0;
                $holding->paid_usd = 0;
                $holding->paid_btc = 0;
                $holding->amount = 0;
                $holding->save();
              }
            } else {
              $holding = Holding::where([['userid', '=', $transaction->userid], ['tokenid', '=', $transaction->tokenid], ['exchange', '=', $transaction->exchange], ['market', '!=', $transaction->market]])->first();

              if($holding)
              {
                if($holding->amount > $transaction->amount)
                {
                  $holding->paid_market = ($holding->paid_market / $holding->amount) * ($holding->amount - $transaction->amount);
                  $holding->paid_usd = ($holding->paid_usd / $holding->amount) * ($holding->amount - $transaction->amount);
                  $holding->paid_btc = ($holding->paid_btc / $holding->amount) * ($holding->amount - $transaction->amount);
                  $holding->amount -= $transaction->amount;
                  $holding->save();
                } else {
                  $holding->paid_market = 0;
                  $holding->paid_usd = 0;
                  $holding->paid_btc = 0;
                  $holding->amount = 0;
                  $holding->save();
                }
            } else {
              $holding = Holding::where([['userid', '=', $transaction->userid], ['tokenid', '=', $transaction->tokenid], ['exchange', '!=', $transaction->exchange], ['market', '!=', $transaction->market]])->first();

              if($holding)
              {
                if($holding->amount > $transaction->amount)
                {
                  $holding->paid_market = ($holding->paid_market / $holding->amount) * ($holding->amount - $transaction->amount);
                  $holding->paid_usd = ($holding->paid_usd / $holding->amount) * ($holding->amount - $transaction->amount);
                  $holding->paid_btc = ($holding->paid_btc / $holding->amount) * ($holding->amount - $transaction->amount);
                  $holding->amount -= $transaction->amount;
                  $holding->save();
                } else {
                  $holding->paid_market = 0;
                  $holding->paid_usd = 0;
                  $holding->paid_btc = 0;
                  $holding->amount = 0;
                  $holding->save();
                }
              } else {
                Alert::error('You had no holding to remove from, please make an buy transaction before doing so.', 'Oops..')->persistent('Close');
                return Redirect::back();
              }
            }
          }
        }
    }
      $this->logHoldings($transaction);
      return Redirect::back();
    }


    public function logHolding($token, $paid_usd, $paid_btc, $amount, $date)
    {
      $logdate = date('Y-m-d', strtotime($date));
      $log = HoldingLog::where([['userid', '=', Auth::user()->id], ['date', '=', $logdate]])->first();

      if($log)
      {
        $array = array(json_decode($log->holdings));
        $array = array_set($array, $token, array('paid_usd' => $paid_usd, 'paid_btc' => $paid_btc, 'amount' => $amount));

        $log->holdings = json_encode($array);
        $log->save();
      } else {
        $array = array();
        $array = array_set($array, $token, array('paid_usd' => $paid_usd, 'paid_btc' => $paid_btc, 'amount' => $amount));


        $log = new HoldingLog;
        $log->userid = Auth::user()->id;
        $log->holdings = json_encode($array);
        $log->date = $logdate;
        $log->save();
      }
    }

    public function logHoldings($transaction){

      $date = date('Y-m-d', strtotime($transaction->date));

      $log = HoldingLog::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->token], ['date', '=', $date]])->first();

      // Clear Holding logs
      Cache::forget('holdingsLogs:'.Auth::user()->id);

      if($log)
      {
        if($transaction->type == "BUY")
        {
          //this is done
          if($transaction->deduct == "on")
          {
            $deduct = HoldingLog::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['date', '=', $date]])->first();

            if($deduct)
            {
              $deduct->amount -= $transaction->amount * $transaction->price;
              $deduct->paid_usd -= 0;
              $deduct->save();
            } else {
              $deduct = new HoldingLog;
              $deduct->userid = $transaction->userid;
              $deduct->token = $transaction->market;
              if($transaction->market == "BTC")
              {
                $deduct->token_cmc_id = "Bitcoin";
              } elseif($transaction->market == "ETH")
              {
                $deduct->token_cmc_id = "Ethereum";
              } elseif($transaction->market == "USDT")
              {
                $deduct->token_cmc_id = "Tether";
              } elseif($transaction->market == "XMR")
              {
                $deduct->token_cmc_id = "Monero";
              } else {
                $deduct->token_cmc_id = "FIAT";
              }
              $deduct->amount = 0 - ($transaction->amount * $transaction->price);
              $deduct->paid_usd = 0;
              $deduct->date = $date;
              $deduct->save();
            }
          }

          //this is done
          $log->amount += $transaction->amount;
          $log->paid_usd += $transaction->paid_usd;
          $log->save();
        } else {

          // this is done
          if($transaction->deduct == "on")
          {
            $deduct = HoldingLog::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['date', '=', $date]])->first();

            if($deduct)
            {
              $deduct->amount += $transaction->amount * $transaction->price;
              $dedict->paid_usd += $transaction->paid_usd;
              $deduct->save();
            } else {
              $deduct = new HoldingLog;
              $deduct->userid = $transaction->userid;
              $deduct->token = $transaction->market;
              if($transaction->market == "BTC")
              {
                $deduct->token_cmc_id = "Bitcoin";
              } elseif($transaction->market == "ETH")
              {
                $deduct->token_cmc_id = "Ethereum";
              } elseif($transaction->market == "USDT")
              {
                $deduct->token_cmc_id = "Tether";
              } elseif($transaction->market == "XMR")
              {
                $deduct->token_cmc_id = "Monero";
              } else {
                $deduct->token_cmc_id = "FIAT";
              }
              $deduct->amount = $transaction->amount * $transaction->price;
              $deduct->paid_usd = $transaction->paid_usd;
              $deduct->date = $date;
              $deduct->save();
            }
          }

          // this is done
          $log->paid_usd = ($log->paid_usd / $log->amount) * ($log->amount - $transaction->amount);
          $log->amount -= 0;
          $log->save();
        }
      } else {
        if($transaction->type == "BUY")
        {

          if($transaction->deduct == "on")
          {
            $deduct = HoldingLog::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['date', '=', $date]])->first();

            if($deduct)
            {
              $deduct->amount -= $transaction->amount * $transaction->price;
              $deduct->paid_usd -= 0;
              $deduct->save();
            } else {
              $deduct = new HoldingLog;
              $deduct->userid = $transaction->userid;
              $deduct->token = $transaction->market;
              if($transaction->market == "BTC")
              {
                $deduct->token_cmc_id = "Bitcoin";
              } elseif($transaction->market == "ETH")
              {
                $deduct->token_cmc_id = "Ethereum";
              } elseif($transaction->market == "USDT")
              {
                $deduct->token_cmc_id = "Tether";
              } elseif($transaction->market == "XMR")
              {
                $deduct->token_cmc_id = "Monero";
              } else {
                $deduct->token_cmc_id = "FIAT";
              }
              $deduct->amount = 0 - ($transaction->amount * $transaction->price);
              $deduct->paid_usd = 0;
              $deduct->date = $date;
              $deduct->save();
            }
          }

          // This is done
          $log = new HoldingLog;
          $log->userid = $transaction->userid;
          $log->token = $transaction->token;
          $log->token_cmc_id = $transaction->token_cmc_id;
          $log->amount = $transaction->amount;
          $log->paid_usd = $transaction->paid_usd;
          $log->date = $date;
          $log->save();
        } else {

          // This is done
          if($transaction->deduct == "on")
          {
            $deduct = HoldingLog::where([['userid', '=', $transaction->userid], ['token', '=', $transaction->market], ['date', '=', $date]])->first();

            if($deduct)
            {
              $deduct->amount += $transaction->amount * $transaction->price;
              $dedict->paid_usd += $transaction->paid_usd;
              $deduct->save();
            } else {
              $deduct = new HoldingLog;
              $deduct->userid = $transaction->userid;
              $deduct->token = $transaction->market;
              if($transaction->market == "BTC")
              {
                $deduct->token_cmc_id = "Bitcoin";
              } elseif($transaction->market == "ETH")
              {
                $deduct->token_cmc_id = "Ethereum";
              } elseif($transaction->market == "USDT")
              {
                $deduct->token_cmc_id = "Tether";
              } elseif($transaction->market == "XMR")
              {
                $deduct->token_cmc_id = "Monero";
              } else {
                $deduct->token_cmc_id = "FIAT";
              }
              $deduct->amount = $transaction->amount * $transaction->price;
              $deduct->paid_usd = $transaction->paid_usd;
              $deduct->date = $date;
              $deduct->save();
            }
          }

            // This is done
            $log = new HoldingLog;
            $log->userid = $transaction->userid;
            $log->token = $transaction->token;
            $log->token_cmc_id = $transaction->token_cmc_id;
            $log->amount = 0 - $transaction->amount;
            $log->paid_usd = 0;
            $log->date = $date;
            $log->save();
        }
      }
    }





    public function getSummary($token, $currency)
    {
      if($token = "")
      {
      $holdings = Holding::where([['userid', '=', 1]])->get();
      } else {
      $holdings = Holding::where([['userid', '=', 1], ['token', '=', $token]])->get();
      }
      $paid = 0;
      $profit = 0;
      foreach($holdings as $holding)
      {
        $profit += $holding->getProfit(Auth::user()->api, Auth::user()->currency, Auth::user()->getBtcCache(), Auth::user()->getFiat());
      }
      return $profit;
    }


    public function history($days)
    {
      $today = strtotime(date('Y-m-d'));
      $fromdate = \Carbon\Carbon::today()->subDays($days); // Gets the last day it should get prices from, usually
      $trades = Transaction::where([['userid', Auth::user()->id], ['date', '>=', $fromdate]])->orderBy('date')->get();
      $holdings = Holding::where('userid', Auth::user()->id);
      $token = array();

      //Create array of holdings
      /*
      foreach($holdings as $holding)
      {
        if(!isSet($token[$holding->token]))
        {
          $cmc_id = Crypto::where('symbol', $holding->token)->select('cmc_id')->first()->cmc_id;
          $token[$holding->token] = array('token' => $holding->token, 'amount' => $holding->amount, 'cmc_id' => $cmc_id, 'paid' => $holding->paid_usd);
        } else {
          $token[$holding->token] = array('token' => $holding->token, 'amount' => $token[$holding->token]['amount'] + $holding->amount, 'cmc_id' => $cmc_id, 'paid' => $token[$holding->token]['paid'] + $holding->paid_usd);
        }
      }
      */



      // Loop through past 7 days starting from today
      for($i = $today; $i >= strtotime($fromdate); $i -= 86400)
      {
        $worth_usd = 0;
        $paid_usd = 0;
        // Make sure that we do not do today.
        if($i != $today)
        {
          foreach($trades as $trade)
          {
            $trade_date = strtotime(date('Y-m-d', strtotime($trade->date)));
            if($trade_date == $i)
            {
              $holding = $holdings->where([['token', $trade->token], ['market', $trade->market], ['exchange', $trade->exchange]])->first();

              if($trade->type == "BUY")
              {
                $holding->amount -= $trade->amount;
                $holding->paid_market -= $trade->paid_market;
                $holding->paid_usd -= $trade->paid_usd;
                $holding->paid_btc -= $trade->paid_btc;
                echo "buy";
              } else {
                $holding->amount += $trade->amount;
                $holding->paid_market -= $trade->paid_market;
                $holding->paid_usd -= $trade->paid_usd;
                $holding->paid_btc -= $trade->paid_btc;
                echo "sell";
              }
              echo date('Y-m-d', $i) . " " . $holding->amount . "<br>";
            }
          }
        }
      }

      /*
      echo "<pre>";
      var_dump($trades);
      echo "</pre>";
      */
    }


}
