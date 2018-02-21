<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Needed
use Auth;

// models
use App\Balance;
use App\Deposit;

use App\User;
use App\Crypto;

class TradeController extends Controller
{
    public function newTransaction(Request $request)
    {
      $type = $request->get('type');
      $currencytype = $request->get('currencytype'); // Either fiat or crypto
      $token = $request->get('crypto'); // Token
      $currency = $request->get('currency'); // Fiat Currency
      $amount = $request->get('amount');
      $exchange = $request->get('exchange');
      $date = $request->get('date');
      $user = Auth::user();




      /* Handle the different types of transactions starting with deposits */

      if($type == "deposit")
      {
        // This is a deposit, a deposit can be either be in fiat or in crypto. Everything an deposit does is make an deposit in the database and a balance representing the exchange and currency.

        // We need to know what currency the user is depositing, since they can deposit both fiat and crypto we need to find out which one the user is depositing.

        if($currencytype == "fiat")
        {
          // If it's a fiat deposit
          $todeposit = $currency;
          $priceondeposit = 1;
        } else {
          // If it's a crypto deposit
          $crypto = Crypto::where('id', $token)->select('symbol')->first();
          $todeposit = $crypto->symbol;
        }

        // Also we need to find out what the price of the token was the day of deposit, this doesn't always work so then we return 1, fiat should always be 1.
        if($currencytype != "fiat")
        {
          $client = new \GuzzleHttp\Client();
          $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym='.$todeposit.'&tsyms=BTC&ts='.strtotime($date).'&extraParams=Altpocket');
          $response = $res->getBody();
          $prices = json_decode($response, true);
          $priceondeposit = 1; // If we dont find a price then set it to 1.
            foreach($prices[(string)$todeposit] as $key => $price){
                if($key == "BTC")
                {
                  $priceondeposit = $price;
                }
            }
          }

          // On top of that we need to find out what the price was on bitcoins

        // On to pof that we need to find out what the BTC price was in usd, eur, gbp, eth and usdt for some reason.
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP,ETH,USDT&ts='.strtotime($date).'&extraParams=Altpocket');
        $response = $res->getBody();
        $prices = json_decode($response, true);
        $btc_usd = 0;
        $btc_eur = 0;
        $btc_gbp = 0;
        $btc_eth = 0;
        $btc_usdt = 0;

          foreach($prices['BTC'] as $key => $price)
          {
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




        // lets start with making the deposit.
        $deposit = new Deposit;
        $deposit->userid = $user->id;
        $deposit->exchange = "Manual-".$exchange;
        $deposit->txid = "Manual-".time();
        $deposit->date = $date;
        $deposit->currency = $todeposit;
        $deposit->amount = $amount;
        $deposit->price = $priceondeposit; // I'm not even sure if this is used but this is the price of the token when deposited.
        $deposit->handled = 0;
        $deposit->btc_price_deposit_eur = $btc_eur;
        $deposit->btc_price_deposit_gbp = $btc_gbp;
        $deposit->btc_price_deposit_usd = $btc_usd;
        $deposit->btc_price_deposit_usdt = $btc_usdt;
        $deposit->btc_price_deposit_eth = $btc_eth;
        $deposit->save();

        // Deposit has been put into the system, now it's time to make a balance of it.

        $balance = Balance::where([['userid', '=', $user->id], ['exchange', '=', 'Manual-'.$exchange]])->first();

        if($balance)
        {
          $balance->amount += $amount;
          $balance->save();
        } else {
          $balance = new Balance;
          $balance->userid = $user->id;
          $balance->exchange = $exchange;
          $balance->type = "Manual";
          $balance->currency = $todeposit;
          $balance->amount = $amount;
          $balance->save();
        }

        // Handle deposit
        $deposit->handled = 1;
        $deposit->save();

        // Deposit fully handled and finished =)

      } // This ends deposits.


    }
}
