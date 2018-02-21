<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Historical;
use App\BittrexInvestment;
use App\PoloInvestment;
use App\Multiplier;
use App\Crypto;

class HistoryController extends Controller
{
  public function importHistorical()
  {
    $client = new \GuzzleHttp\Client();

    $res = $client->request('GET', 'https://apiv2.bitcoinaverage.com/indices/global/history/BTCUSD?period=alltime&?format=json');
    $response = $res->getBody();
    $prices = json_decode($response, true);

    foreach($prices as $price)
    {
      if(!Historical::where('created_at', $price['time'])->first())
      {
        $input = new Historical;
        $input->currency = "BTC";
        $input->USD = $price['average'];
        $input->ETH = 0;
        $input->created_at = $price['time'];
        $input->updated_at = $price['time'];
        $input->save();
      }
    }
  }

  public function importHistocialETH()
  {
    $client = new \GuzzleHttp\Client();

    $res = $client->request('GET', 'https://poloniex.com/public?command=returnChartData&currencyPair=USDT_XMR&start=1435699200&end=9999999999&period=14400');
    $response = $res->getBody();
    $prices = json_decode($response, true);

    foreach($prices as $price)
    {
      $date = date('Y-m-d 00:00:00', $price['date']);

      $input = Historical::where('created_at', $date)->first();
      if($input->XMR == 0)
      {
        $input->XMR = $input->USD / $price['open'];
        $input->save();
      }
    }
  }


  public function getHistorical($date)
  {
    $datum = date('Y-m-d 00:00:00', $date);

    $historical = Historical::where([['currency', '=', 'BTC'], ['created_at', '=', $date]])->first();

    return $historical;


  }

  public function fix()
  {
    $investments = PoloInvestment::where([['btc_price_sold_usd', '=', '0'], ['market', '!=', 'Deposit']])->get();

    foreach($investments as $investment)
    {
      $date = strtotime($investment->date_bought);
      $datum = date('Y-m-d 00:00:00', $date);

      echo $date;

      $historical = Historical::where([['currency', '=', 'BTC'], ['created_at', '=', $datum]])->first();

      if($historical != null)
      {
        $btc_usd = $historical->USD;
        $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
        $btc_gbp = 0;
        $btc_usdt = $historical->USD;
        $btc_eth = $historical->ETH;
      } else {
        $btc_usd = Crypto::where('symbol', 'BTC')->first()->price_usd;
        $btc_eur = Crypto::where('symbol', 'BTC')->first()->price_usd * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
        $btc_gbp = 0;
        $btc_usdt = Crypto::where('symbol', 'BTC')->first()->price_usd;
        $btc_eth = Crypto::where('symbol', 'BTC')->first()->price_eth;;
      }

      $investment->btc_price_bought_usd = $btc_usd;
      $investment->btc_price_bought_eur = $btc_eur;
      $investment->btc_price_bought_gbp = $btc_gbp;
      $investment->btc_price_bought_eth = $btc_eth;
      $investment->btc_price_bought_usdt = $btc_usdt;
      $investment->save();

    }
  }

}
