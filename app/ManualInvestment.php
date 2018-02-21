<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Crypto;
use App\bittrex;
use App\Polo;
use App\WorldCoin;
use App\Historical;
use App\Token;
use App\Multiplier;
use Auth;

class ManualInvestment extends Model
{
    protected $dates = ['created_at', 'updated_at', 'date_bought', 'date_sold'];
    protected $markets = ['BTC', 'ETH', 'EUR', 'USD', 'USDT', 'CAD', 'AUD'];


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
      if($this->market == "USD")
      {
        $inner = "<i class='fa fa-usd'></i>";
        $variable = $this->btc_price_bought_usd;
        $price_og = $price;
        $this->bought_at = $this->bought_at / $this->btc_price_bought_usd;
        $usdt = Auth::user()->getPrice('BTC', 'USDT', 'Manual');
        $market_price_bought = $this->btc_price_bought_usd;
        $multiplier = $fiat;
      }
      if($this->market == "EUR")
      {
        $marketfiat = Multiplier::where('currency', 'eur')->first()->price;
        if(Auth::user()->currency == "EUR")
        {
          $fiat = 1;
          $marketfiat = 1;
        }

        $inner = "<i class='fa fa-eur'></i>";
        $variable = $this->btc_price_bought_usd * $marketfiat;
        $price_og = $price * Auth::user()->getBtcCache() * Multiplier::where('currency', 'EUR')->first()->price;
        $this->bought_at = $this->bought_at / ($this->btc_price_bought_usd * $marketfiat);
        $market_price_bought = $this->btc_price_bought_usd;
      }
      if($this->market == "GBP")
      {
        $marketfiat = Multiplier::where('currency', 'gbp')->first()->price;
        if(Auth::user()->currency == "GBP")
        {
          $fiat = 1;
          $marketfiat = 1;
        }

        $inner = "<i class='fa fa-gbp'></i>";
        $variable = $this->btc_price_bought_usd * $marketfiat;
        $price_og = $price * Auth::user()->getBtcCache() * Multiplier::where('currency', 'GBP')->first()->price;
        $this->bought_at = $this->bought_at / ($this->btc_price_bought_usd * $marketfiat);
        $market_price_bought = $this->btc_price_bought_usd;
      }
      if($this->market == "AUD")
      {
        $marketfiat = Multiplier::where('currency', 'AUD')->first()->price;
        if(Auth::user()->currency == "AUD")
        {
          $fiat = 1;
          $marketfiat = 1;
        }

        $inner = "<i class='fa fa-usd'></i>";
        $variable = $this->btc_price_bought_usd * $marketfiat;
        $price_og = $price * Auth::user()->getBtcCache() * Multiplier::where('currency', 'AUD')->first()->price;
        $this->bought_at = $this->bought_at / ($this->btc_price_bought_usd * $marketfiat);
        $market_price_bought = $this->btc_price_bought_usd;
      }
      if($this->market == "CAD")
      {
        $marketfiat = Multiplier::where('currency', 'CAD')->first()->price;
        if(Auth::user()->currency == "CAD")
        {
          $fiat = 1;
          $marketfiat = 1;
        }

        $inner = "<i class='fa fa-usd'></i>";
        $variable = $this->btc_price_bought_usd * $marketfiat;
        $price_og = $price * Auth::user()->getBtcCache() * Multiplier::where('currency', 'CAD')->first()->price;
        $this->bought_at = $this->bought_at / ($this->btc_price_bought_usd * $marketfiat);
        $market_price_bought = $this->btc_price_bought_usd;
      }
      if($this->market == "SGD")
      {
        $marketfiat = Multiplier::where('currency', 'SGD')->first()->price;
        if(Auth::user()->currency == "SGD")
        {
          $fiat = 1;
          $marketfiat = 1;
        }

        $inner = "<i class='fa fa-usd'></i>";
        $variable = $this->btc_price_bought_usd * $marketfiat;
        $price_og = $price * Auth::user()->getBtcCache() * Multiplier::where('currency', 'SGD')->first()->price;
        $this->bought_at = $this->bought_at / ($this->btc_price_bought_usd * $marketfiat);
        $market_price_bought = $this->btc_price_bought_usd;
      }
      if($this->market == "XMR")
      {
        $inner = "<img src='/icons/32x32/XMR.png' width='16' height='16'>";
        $xmr_history = Historical::where('created_at', date('Y-m-d 00:00:00', strtotime($this->date_bought)))->select('XMR')->first()->XMR;
        $variable = $xmr_history;
        $price_og = $price;
        $this->bought_at = ($this->bought_at / $xmr_history);
        $market_price_bought = $this->btc_price_bought_usd;
        $xmr = Auth::user()->getPrice('XMR', 'BTC', 'Manual');
        $price = $price * $xmr;
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
        }
        // Fallback to coinmarketcap if null
        if($price === null)
        {
          $price = Crypto::where('symbol', $this->currency)->select('price_btc')->first();
        }

        return $price->price_btc;

      } elseif($market == "ETH")
      {
        if($api == "coinmarketcap")
        {
          $price = Crypto::where('symbol', $this->currency)->select('price_eth')->first();
        } elseif($api == "bittrex")
        {
          $price = bittrex::where('symbol', 'ETH-' . $this->currency)->select('price_btc as price_eth')->first();
        } elseif($api == "poloniex")
        {
          $price = Polo::where('symbol', 'ETH_' . $this->currency)->select('price_btc as price_eth')->first();
        }
        // Fallback to coinmarketcap if null
        if($price === null)
        {
          $price = Crypto::where('symbol', $this->currency)->select('price_eth')->first();
        }

        return $price->price_eth;

      } elseif($market == "EUR")
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
        } elseif($api == "coinbase")
        {
          $price = Token::where('currency', $this->currency)->select('price_btc')->first();
        }
        // Fallback to coinmarketcap if null
        if($price === null)
        {
          $price = Crypto::where('symbol', $this->currency)->select('price_btc')->first();
        }

        return $price->price_btc;

      } elseif($market == "GBP" || $market == "AUD" || $market == "CAD")
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
        } elseif($api == "coinbase")
        {
          $price = Token::where('currency', $this->currency)->select('price_btc')->first();
        }
        // Fallback to coinmarketcap if null
        if($price === null)
        {
          $price = Crypto::where('symbol', $this->currency)->select('price_btc')->first();
        }

        return $price->price_btc;

      } elseif($market == "USD")
      {
        if($api == "coinmarketcap")
        {
          $price = Crypto::where('symbol', $this->currency)->select('price_usd')->first();
        } elseif($api == "bittrex")
        {
          $price = bittrex::where('symbol', 'USDT-' . $this->currency)->select('price_btc as price_usd')->first();
        } elseif($api == "poloniex")
        {
          $price = Polo::where('symbol', 'USDT_' . $this->currency)->select('price_btc as price_usd')->first();
        } elseif($api == "coinbase")
        {
          $price = Token::where('currency', $this->currency)->select('price_usd')->first();
        }
        // Fallback to coinmarketcap if null
        if($price === null)
        {
          $price = Crypto::where('symbol', $this->currency)->select('price_usd')->first();
        }

        return $price->price_usd;

      } elseif($market == "USDT")
      {
        if($api == "coinmarketcap")
        {
          $price = Crypto::where('symbol', $this->currency)->select('price_usd')->first();
        } elseif($api == "bittrex")
        {
          $price = bittrex::where('symbol', 'USDT-' . $this->currency)->select('price_btc as price_usd')->first();
        } elseif($api == "poloniex")
        {
          $price = Polo::where('symbol', 'USDT_' . $this->currency)->select('price_btc as price_usd')->first();
        }
        // Fallback to coinmarketcap if null
        if($price === null)
        {
          $price = Crypto::where('symbol', $this->currency)->select('price_usd')->first();
        }

        return $price->price_usd;

      } elseif($market == "XMR")
      {
        $price = Polo::where('symbol', 'XMR_' . $this->currency)->select('price_btc')->first();
        return $price->price_btc;
      }
    }
}
