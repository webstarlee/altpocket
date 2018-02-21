<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\ManualInvestment;
use App\PoloInvestment;
use App\BittrexInvestment;
use App\Crypto;
use Auth;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use Cache;
use DB;
use App\Multiplier;
use Guzzle\Http\Exception\ClientErrorResponseException;

use App\Transaction;

class ChartController extends Controller
{
  public function roundTime($timestamp, $precision = 5)
  {
      return date('Y-m-d H:i:s', round(strtotime($timestamp) / (60 * $precision)) * (60 * $precision));
  }

  public function date_compare($a, $b)
  {
      return strtotime($a['TimeStamp']) - strtotime($b['TimeStamp']);
  }

  public function charts()
  {
      $investments["manual"] = ManualInvestment::select("*")->where('date_bought', '<=', 'NOW()-INTERVAL 7 DAY')->get();
      foreach ($investments["manual"] as $investment) {
          if (isSet($count[$investment->currency])) {
              $count[$investment->currency]++;
          } else {
              $count[$investment->currency] = 1;
          }
      }
      asort($count);
      $result = array_reverse($count);
      return view("charts")->with("top", json_encode($result));
  }

  public function getBTC()
  {
      $client = new Client();
      $uri = 'https://bittrex.com/Api/v2.0/pub/market/GetTicks?marketName=USDT-BTC&tickInterval=day&_=1451606400';
      $obj = Cache::remember("DAY-USDT-BTC", 60, function () use ($client, $uri) {
          return json_decode($client->get($uri)->getBody());
      });
      foreach ($obj->result as $key => $price) {
          $result[strtotime(explode("T", $price->T)[0])] = $price->L;
      }
      for ($i = key($result); $i <= time(); $i += 86400) {
          if (isSet($result[$i]) == false) { // Fill up the empty dates cause sometimes the damn exchanges were Offline and there's no price so we avg. it
              for ($x = 0; $x <= 10; $x++) {
                  if (isSet($result[strtotime(date("Y-m-d", strtotime("+" . $x . " day", $i)))]) && isSet($result[strtotime(date("Y-m-d", strtotime("-" . $x . " day", $i)))])) {
                      $result[$i] = ($result[strtotime(date("Y-m-d", strtotime("+" . $x . " day", $i)))] + $result[strtotime(date("Y-m-d", strtotime("-" . $x . " day", $i)))]) / 2;
                  }
              }
          }
      }
      return $result;
  }
  public function getAllTime($type,$coin)
  {
      $client = new Client(['http_errors' => false]);
      $res = $client->request('GET', "https://graphs.coinmarketcap.com/currencies/" . $coin);
      $code = $res->getStatusCode();
      if ($code == 200) {
          $obj = json_decode($res->getBody());
          $price = array();
          $x = 0;
          foreach ($obj->price_usd as $prices) {
              $price[$x]["USD"] = $prices[1];
              $price[$x]["Time"] = $date = explode("T", date('c', $prices[0] / 1000))[0];
              $x++;
          }
          $y = 0;
          foreach ($obj->price_btc as $prices) {
              $price[$y]["BTC"] = $prices[1];
              $price[$y]["Time"] = $date = explode("T", date('c', $prices[0] / 1000))[0];
              $y++;
          }

          if ($type == "ATH") {
              $max = -9999999999;
              $maxB = -9999999999;
              foreach ($price as $k => $v) {
                  if ($v['USD'] > $max) {
                      $max = $v['USD'];
                      $athUSD = $v;
                  }
              }
              foreach ($price as $k => $v) {
                  if ($v['BTC'] > $maxB) {
                      $maxB = $v['BTC'];
                      $athBTC = $v;
                  }
              }
          } elseif ($type == "ATL") {
              $max = 9999999999;
              $maxB = 9999999999;
              foreach ($price as $k => $v) {
                  if ($v['USD'] < $max) {
                      $max = $v['USD'];
                      $athUSD = $v;
                  }
              }
              foreach ($price as $k => $v) {
                  if ($v['BTC'] < $maxB) {
                      $maxB = $v['BTC'];
                      $athBTC = $v;
                  }
              }
          }
          // print_r($athUSD["USD"]);
          // print_r($athBTC["BTC"]);
          $result = array("BTC" => $athBTC["BTC"], "USD" => $athUSD["USD"], "Times" => array("USD" => $athUSD["Time"], "BTC" => $athBTC["Time"]));
          // print_r($result);
          return $result;
      } else {
          return array("error" => $code);
      }
  }
  public function getHistoric($coin,$when)
  {
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
              return array("error" => "No price for !");
          }
      } else {
          return array("error" => $code);
      }
  }
  public function candles($pair,$interval)
  {
      if (strpos($interval, 'd') !== false || strpos($interval, 'day') !== false) {
          $count = explode("d", $interval)[0];
          $minPerioid = "DD";
          $interval = "day";
      }
      if (strpos($interval, 'h') !== false || strpos($interval, 'hour') !== false) {
          $count = explode("h", $interval)[0];
          $minPerioid = "hh";
          $interval = "hour";
      }
      if (strpos($interval, 'min') !== false || strpos($interval, 'm') !== false) {
          $minPerioid = "mm";
          $count = explode("m", $interval)[0];
          $interval = "onemin";
      }
      $client = new Client();
      $uri = 'https://bittrex.com/Api/v2.0/pub/market/GetTicks?marketName=' . $pair . '&tickInterval=' . $interval . '&_=1451606400';
      $obj = Cache::remember("BITTREX" . "-" . strtoupper($interval) . "-" . $pair, 60, function () use ($client, $uri) {
          return json_decode($client->get($uri)->getBody());
      });
      if (isSet($obj->result)) {
          $x = 0;
          $sliced = array_slice($obj->result, $count - ($count * 2), $count, true);

          foreach ($sliced as $prices) {
              if (strtotime(explode("T", $prices->T)[0]) >= 1451606400) { // Lets make sure that we dont get prices older than that
                  $time = str_replace("T", ", ", $prices->T);
                  $converted[$x]["TimeStamp"] = $time;
                  $converted[$x]["open"] = $prices->O;
                  $converted[$x]["high"] = $prices->H;
                  $converted[$x]["low"] = $prices->L;
                  $converted[$x]["close"] = $prices->C;
                  $x++;
              }
          }
          return view('pricechart')->with("data", json_encode($converted))->with("minPerioid", $minPerioid)->with("last", $count);                                 //Messing arround with server-side image generation

      } else {
          return $obj->message;
      }
  }
  public function getPrices($pair, $interval, $exchange)
  {
      $client = new Client();
      $exchange = strtolower($exchange);
      $BTC = $this->getBTC();
      $converted = array();

      if (explode("-", strtolower($pair))[1] == "btc") {
          $pair = "USDT-BTC";
      }
      $uri = 'https://bittrex.com/Api/v2.0/pub/market/GetTicks?marketName=' . $pair . '&tickInterval=' . $interval . '&_=1451606400'; ///Making it default
      if ($exchange == "bittrex") {
          $uri = 'https://bittrex.com/Api/v2.0/pub/market/GetTicks?marketName=' . $pair . '&tickInterval=' . $interval . '&_=1451606400';
      }
      if ($exchange == "poloniex") {
          $uri = "https://poloniex.com/public?command=returnChartData&currencyPair=" . str_replace("-", "_", $pair) . "&start=" . (time() - 7776000) . "&end=9999999999&period=900";
      }
      if ($exchange == "coinmarketcap" || $exchange == "manual") {
          $uri = "https://graphs.coinmarketcap.com/currencies/" . Crypto::where('symbol', explode("-", $pair)[1])->first()->cmc_id;
      }
      $obj = Cache::remember($exchange . "-" . strtoupper($interval) . "-" . $pair . "2", 60, function () use ($client, $uri) {
          return json_decode($client->get($uri)->getBody());
      });

      if ($exchange == "poloniex") {
          if (isSet($obj->error) == false) {
              foreach ($obj as $prices) {
                  $converted[$this->roundTime(date('c', $prices->date), 5)]["Price"] = $prices->weightedAverage;
                  $converted[$this->roundTime(date('c', $prices->date), 5)]["desc"] = number_format($prices->weightedAverage, 2) . " BTC";
                  $converted[$this->roundTime(date('c', $prices->date), 5)]["TimeStamp"] = $this->roundTime(date('c', $prices->date), 5);

              }
              return $converted;
          } else {
              return $obj->error;
          }
      }
      if ($exchange == "coinmarketcap" || $exchange == "manual") {
          foreach ($obj->price_usd as $prices) {
              $date = explode(" ", $this->roundTime(date('c', $prices[0] / 1000), 5))[0];
              if (strtotime($date) > 1451606400) {
                  $converted[$date] = ["USD" => $prices[1], "BTC" => number_format(($prices[1] / $BTC[strtotime($date)]), 8)];
                  $converted[$date]["TimeStamp"] = $date;
                  $converted[$date]["coin"] = explode("-", $pair)[1];
              }
          }
          return $converted;
      }
      if ($exchange == "bittrex") { /// Follow this format to add exchanges
          if (isSet($obj->result)) {
              foreach ($obj->result as $prices) {
                  if (strtotime(explode("T", $prices->T)[0]) >= 1451606400) { // Lets make sure that we dont get prices older than that
                      if ($pair == "USDT-BTC") {
                          $converted[$this->roundTime($prices->T, 5)] = ["USD" => number_format($prices->L, 2, ".", ""), "BTC" => 1];
                      } else {
                          $converted[$this->roundTime($prices->T, 5)] = ["USD" => number_format($prices->L * $BTC[strtotime(explode(" ", $this->roundTime($prices->T, 5)) [0])], 2), "BTC" => number_format($prices->L, 8)];
                      }
                      $converted[$this->roundTime($prices->T, 5)]["TimeStamp"] = $this->roundTime($prices->T, 5);
                      $converted[$this->roundTime($prices->T, 5)]["coin"] = explode("-", $pair)[1];
                  }
              }
              return $converted;
          } else {
              return $obj->message;
          }
      }
  }

  public function getInvestments($exchange, $user, $coin, $all)
  {
      $query = [['userid', '=', $user->id], ['currency', '=', $coin]];
      if ($all) {
          $query = [['userid', '=', $user->id]];
      }
      $exchange = strtolower($exchange);
      if ($exchange == "poloniex") {
          return PoloInvestment::where($query)->get();
      }
      if ($exchange == "bittrex") {
          return BittrexInvestment::where($query)->get();
      }
      if ($exchange == "manual") {
          return ManualInvestment::where($query)->get();
      }
      if ($exchange == "all") {
          $data["Poloniex"] = PoloInvestment::where($query)->get();
          $data["Manual"] = ManualInvestment::where($query)->get();
          $data["Bittrex"] = BittrexInvestment::where($query)->get();
          return $data;
      }else{
          return array("error"=>"Not Found");
      }
  }

  public function getCharts($username, $coin, $exchange)
  {
      $user = User::where('username', $username)->first();
      $BTC = $this->getBTC();
      if (isSet($user->username)) {
              foreach ($this->getInvestments($exchange, $user, strtoupper($coin), false) as $key => $investment) {
                  if ($key != "error") {
                      if ($investment->private == 0 || $investment->userid == (Auth::User()->id ?: 0)) {
                          $coin = $investment->currency;
                          $market = $investment->market;
                          $bought = $this->roundTime(date("Y-m-d H:i:s", strtotime($investment->date_bought)), 5);
                          $trades[$bought] = ["bullet" => "diamond", "bulletColor" => "green", "desc" => "Bought <b>" . $investment->amount . "</b>   $coin  for \r\n <b>" . $investment->bought_for_usd . " </b> USD \r\n<b>" . number_format($investment->bought_for_usd / $BTC[strtotime(explode(" ", $bought)[0])], 8, ".", ",") . "</b> <i class='fa fa-btc'></i>", "TimeStamp" => $bought];
                          if (isSet($investment->date_sold)) {
                              $sold_date = $this->roundTime(date("Y-m-d H:i:s", strtotime($investment->date_sold)), 5);
                              $trades[$sold_date] = ["bullet" => "diamond", "bulletColor" => "red", "desc" => "Sold  <b>" . $investment->amount . "</b> " . $coin . "  for \r\n<b>" . number_format($investment->sold_for_usd, 2, ".", ",") . "</b> USD \r\n<b>" . number_format($investment->sold_for_usd / $BTC[strtotime(explode(" ", $sold_date)[0])], 8, ".", ",") . "</b> <i class='fa fa-btc'></i>", "TimeStamp" => $sold_date];
                          }
                      }
                  }
              }

              if (isSet($market) && isSet($coin)) {
                  $prices = $this->getPrices($market . "-" . $coin, "day", $exchange);//strtolower($exchange));
                  if (is_array($prices)) {
                      if (count($trades) > 0) {
                          $output = array_values(array_replace_recursive($prices, $trades));
                          usort($output, array($this, "date_compare"));
                          return $output;
                      } else {
                          return $prices;
                      }
                  } else {
                      return array("error" => $prices);
                  }
              } else {
                  return array("error" => "No Trades Found!");
              }
      } else {
          return array("error" => "User not found");
      }
  }

  public function discord($user, $coin, $exchange)
  {
      return view('chartexport')->with("data", json_encode($this->getCharts($user, $coin, $exchange)));                                 //Messing arround with server-side image generation
  }

  public function getHistory($username)
  {
      $user = User::where('username', $username)->first();
      $BTC = $this->getBTC();                                                                                                           //Get USDT-BTC Prices for more accurate prices
      foreach ($this->getInvestments("all", $user, "", true) as $where => $exchange) {                                //Getting all the Investments and coin prices $where is the exchange name
          foreach ($exchange as $investment) {
            if($investment->market != "Deposit"){
              $investments[$investment->id] = $investment;
            }
              if (isSet($prices[$investment->currency]) == false && $investment->market != "Deposit") {                                                                     // Let's check if we already have those prices

                  foreach ($this->getPrices($investment->market . "-" . $investment->currency, "day", $where) as $price) {  //Formatting the price array
                      $allprices[$price["coin"]][$price["TimeStamp"]] = $price["USD"];                                                  //Setting just the USD
                  }
              }
          }
      }

      foreach ($allprices as $current => $date) { // for each price
          foreach ($date as $date => $coin) {
              foreach ($investments as $investment) {

                  if ($investment->currency == strtoupper($current)) {                                                                //If It's the correct currency
                      if ($date >= $investment->date_bought && $date <= ($investment->date_sold ?: time())) {                         //If It's in the correct time
                        //echo "Date = " . $date . " | Coin = " . $investment->currency . "<br>";
                          $total[strtotime($date)]["USD"] = ((isSet($total[strtotime($date)]["USD"]) ? $total[strtotime($date)]["USD"] : 0)) + ($investment->amount * number_format(str_replace(",", "", $coin), 2, ".", ""));
                          $total[strtotime($date)]["BTC"] = ((isSet($total[strtotime($date)]["BTC"]) ? $total[strtotime($date)]["BTC"] : 0)) + ($investment->amount * number_format(str_replace(",", "", $coin), 8, ".", "") / $BTC[strtotime(explode(" ", $date)[0])]);
                          $total[strtotime($date)]["TimeStamp"] = explode(" ", $date)[0];

                          if (strtotime($date) > strtotime($investment->date_bought) && strtotime($date) < strtotime($investment->date_sold)) {                                      //Set the "Holding" tooltip

                            $total[strtotime($date)]["desc"] = ((isSet($total[strtotime($date)]["desc"]) ? $total[strtotime($date)]["desc"] : "")) . "\r\nHolding: " . $investment->amount . " " . $investment->currency;

                          }
                          if (date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime($investment->date_sold))) {                                                                      //Sold Tooltip
                              $total[strtotime($date)]["desc"] = ((isSet($total[strtotime($date)]["desc"]) ? $total[strtotime($date)]["desc"] : "")) . "\r\nSold " . $investment->amount . " " . $investment->currency;
                              $total[strtotime($date)] += ["bullet" => "diamond", "bulletColor" => "red"];
                          }
                          //echo "DATE: " . $date . " -- DATE BOUGHT: " . $investment->date_bought;
                          if (date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime($investment->date_bought))) {                                                                 //Bought Tooltip
                              $total[strtotime($date)]["desc"] = ((isSet($total[strtotime($date)]["desc"]) ? $total[strtotime($date)]["desc"] : "")) . "\r\nBought " . $investment->amount . " " . $investment->currency;
                              $total[strtotime($date)] += ["bullet" => "diamond", "bulletColor" => "green"];
                          }
                      }
                  }
              }
          }
      }


      for ($i = key($total); $i <= time(); $i += 86400) {
          if (isSet($total[$i]) == false) { /////// Fill up the empty dates
              $total[$i] = ["TimeStamp" => date("Y-m-d", $i), "USD" => -0, "BTC" => 0];
          }
      }
      return $total;
  }



  /* New Portfolio Stuff */

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


  public function getPortfolioHistory($days, $currency)
  {
    $user = Auth::user();
    $BTC = $this->getBTC();
    $fromdate = \Carbon\Carbon::today()->subDays($days);
    $transactions = Transaction::where([['userid', $user->id], ['date', '>', $fromdate]])->orderBy('date')->get();
    $array = array();
    $worth_usd = 0;
    foreach($transactions as $transaction)
    {
      $date = date('Y-m-d', strtotime($transaction->date));
      $price = $this->getHistorical('Ethereum', $date);
      $worth_usd += $transaction->amount * $price['USD'];
      $array = array_set($array, $date, array('worth_usd' => $worth_usd, 'worth_btc' => $transaction->amount * $price['BTC']));
    }

    foreach($array as $worth)
    {
      echo $worth['worth_usd'].", ";
    }

  }


}
