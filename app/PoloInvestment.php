<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Crypto;
use App\bittrex;
use App\Polo;
use App\History;
use Auth;

class PoloInvestment extends Model
{
  protected $dates = ['created_at', 'updated_at', 'date_bought', 'date_sold'];
  protected $markets = ['BTC', 'ETH', 'USDT', 'XMR'];

  public function getData($api, $multiplier, $fiat)
  {
    $price = $this->getPrice($api, $this->market);
    $data = new \StdClass();

    // Market Specific variables
    if($this->market == "BTC")
    {
      $inner = "<i class='fa fa-btc'></i>";
      $market_price_bought = $this->btc_price_bought_usd;
      $variable = 1;
      $price_og = $price;
    }
    if($this->market == "ETH")
    {
      $inner = "<img src='/icons/32x32/ETH.png' width='16' height='16'>";
      $variable = $this->btc_price_bought_eth;
      $price_og = $price;
      $this->bought_at = $this->bought_at / $this->btc_price_bought_eth;
      $market_price_bought = $this->btc_price_bought_usd;
      $eth = Auth::user()->getPrice('ETH', 'BTC', 'Manual');
      $price = $price * $eth;
    }
    if($this->market == "XMR")
    {
      $inner = "<img src='/icons/32x32/XMR.png' width='16' height='16'>";
      $variable = $this->btc_price_bought_eth;
      $price_og = $price;
      $this->bought_at = $this->bought_at / History::where('created_at', date('Y-m-d 00:00:00', strtotime($this->date_bought)))->select('XMR')->first()->XMR;
      $market_price_bought = $this->btc_price_bought_usd;
      $xmr = Auth::user()->getPrice('XMR', 'BTC', 'Manual');
      $price = $price * $xmr;
    }
    if($this->market == "USDT")
    {
      $inner = "<i class='fa fa-usd'></i>";
      $variable = $this->btc_price_bought_usd;
      $price_og = $price;
      $this->bought_at = $this->bought_at / $this->btc_price_bought_usd;
      $usdt = Auth::user()->getPrice('BTC', 'USDT', 'Manual');
      $market_price_bought = $this->btc_price_bought_usd;
      $multiplier = $fiat;
    }

    $data->price = $price * $multiplier; // Current price
    $data->price_og = $price_og;
    $data->inner = $inner; // Inner Symbol
    $data->bought_at = ($this->bought_at * $market_price_bought) * $fiat; // Bought price
    $data->bought_at_og = $this->bought_at * $variable; // Bought at original currency
    $data->now = ($data->price * $this->amount); // Price worth now
    $data->now_og = $data->price_og * $this->amount; // Worth now at original currency
    $data->then = ($data->bought_at * $this->amount); // Worth when bought
    $data->then_og = $data->bought_at_og * $this->amount; // Worth when bought at original currency
    $data->percent = ($data->now - $data->then) / $data->then * 100; // percent increase/loss
    $data->amount = $this->amount; // Amount
    $data->profit_og = $data->now_og - $data->then_og; // Profit
    $data->profit = $data->now - $data->then; // Profit in original currency

    if(($data->now - $data->then) > 0 )
    {
      $data->color = "positive";
      $data->label = "success";
    } else {
      $data->color = "negative";
      $data->label = "danger";
    }


    return $data;
  }


  public function getPrice($api, $market)
  {
    if($market == "BTC")
    {
      $price = Polo::where('symbol', $this->currency)->select('price_btc')->first();
      return $price->price_btc;

    } elseif($market == "ETH")
    {
      $price = bittrex::where('symbol', 'ETH-' . $this->currency)->select('price_btc as price_eth')->first();
      return $price->price_eth;

    } elseif($market == "USDT")
    {
      $price = bittrex::where('symbol', 'USDT-' . $this->currency)->select('price_btc as price_usd')->first();
      return $price->price_usd;
    } elseif($market == "XMR")
    {
      $price = bittrex::where('symbol', 'XMR-' . $this->currency)->select('price_btc as price_xmr')->first();
      return $price->price_xmr;
    }
  }
}
