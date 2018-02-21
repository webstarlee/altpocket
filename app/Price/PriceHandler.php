<?php

namespace App\Price;


class PriceHandler {


  // ETH, BTC, EURO, USD returns Bitcoins and converts in front end
  // USDT returns USDT


  // New function for getting prices
  public function getPrice($currency, $market, $exchange, $api)
  {

    if($exchange == "Bittrex")
    {
      $token = $this->getToken($currency, $exchange);

    } elseif($exchange == "Poloniex")
    {
      $token = $this->getToken($currency, $exchange);

    } elseif($exchange == "Coinbase")
    {
      $token = $this->getToken($currency, $exchange);

    } else {
      $token = $this->getToken($currency, $api);

    }

  }


  public function getToken($currency, $api)
  {
    if($api == "coinmarketcap")
    {
      $token = Crypto::where('symbol', $currency)->first();

      if($token === null)
      {
        $token = Polo::where('symbol', $currency)->first();
      }
      if($token === null)
      {
        $token = bittrex::where('symbol', $currency)->first();
      }
      if($token === null)
      {
        $token = WorldCoin::where('symbol', $currency)->first();
      }
    } elseif($api == "worldcoinindex")
    {
      $token = WorldCoin::where('symbol', $currency)->first();

      if($token === null)
      {
        $token = Crypto::where('symbol', $currency)->>first();
      }
      if($token === null)
      {
        $token = Polo::where('symbol', $currency)->first();
      }
      if($token === null)
      {
        $token = Bittrex::where('symbol', $currency)->first();
      }
    } elseif($api == "bittrex")
    {
      $token = bittrex::where('symbol', $currency)->first();

      if($token === null)
      {
        $token = Crypto::where('symbol', $currency)->first();
      }

    } elseif($api == "Coinbase")
    {
      $price = Token::where([['currency', '=', $currency], ['exchange', '=', $api]])->first();

      if($price === null)
      {
        $price = bittrex::where('symbol', $currency)->first();
      }
      if($price === null)
      {
        $price = Polo::where('symbol', $currency)->first();
      }
      if($price === null)
      {
        $price = Crypto::where('symbol', $currency)->first();
      }
    }

    return $token;
  }




}
