<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{

  public function getData($api, $multiplier, $fiat)
  {
    if($this->exchange == "Poloniex" || $this->exchange == "Bittrex" || $this->exchange == "Coinbase")
    {
      $api = $this->exchange;
    }
    $price = $this->getPrice($api, "BTC");
    $data = new \StdClass();

    $data->worth = ($price * $multiplier) * $this->amount;
  }




  public function getPrice($api)
  {
    if($api == "coinmarketcap")
    {
      $price = Crypto::where('symbol', $this->currency)->select('price_btc')->first();
    } elseif($api == "bittrex")
    {
      $price = bittrex::where('symbol', $this->currency)->select('price_btc')->first();
    } elseif($api == "poloniex")
    {
      $price = Polo::where('symbol', $this->currency)->select('price_btc')->first();
    } elseif($api == "worldcoinindex")
    {
      $price = WorldCoin::where('symbol', $this->currency)->select('price_btc')->first();
    } elseif($api == "coinbase")
    {
      $price = Token::where('symbol', $this->currency)->select('price_btc')->first();
    }
    // Fallback to coinmarketcap if null
    if($price === null)
    {
      $price = Crypto::where('symbol', $this->currency)->select('price_btc')->first();
    }

    return $price->price_btc;
  }
}
