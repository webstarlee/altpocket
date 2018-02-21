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
use Cache;

class Holding extends Model
{

    // Gets this holdings profit
    public function getProfit($api, $currency, $btc, $fiat)
    {
      if($this->tokenid != 0) // Checks if the currency is fiat
      {
        if($this->exchange != "Manual")
        {
          $api = $this->exchange;
          $btc = $this->getBtc($btc);
        }
        if($currency == "BTC")
        {
          if($this->market != "ETH")
          {
            $price = $this->getPrice($api, 'BTC');
            $profit = ($this->amount * $price) - $this->paid_btc;
          } else {
            $price = $this->getPrice($api, 'BTC');
            $profit = ($this->amount * $price) - $this->paid_btc;
          }
        } elseif($currency == "USD")
        {
          if($this->market == "BTC")
          {
            if($this->token != "USDT")
            {
              $price = $this->getPrice($api, 'BTC') * $btc;
              $profit = ($this->amount * $price) - $this->paid_usd;
            } else {
              $price = $this->getPrice($api, 'USD');
              $profit = ($this->amount * $price) - $this->paid_usd;
            }
          } elseif($this->market == "USD")
          {
            $price = $this->getPrice($api, 'BTC') * $btc;
            $profit = ($this->amount * $price) - $this->paid_usd;
          } elseif($this->market == "USDT")
          {
            $price = $this->getPrice($api, 'USDT');
            $profit = ($this->amount * $price) - $this->paid_usd;
          } elseif($this->market == "ETH")
          {
            $price = $this->getPrice($api, 'ETH') * $btc;
            $profit = (($this->amount * $price) - $this->paid_usd);
          } else {
            $price = $this->getPrice($api, 'BTC') * $btc;
            $profit = ($this->amount * $price) - $this->paid_usd;
          }
        } else {
          // If market is same as display currency use paid_market
          if($this->market == $currency)
          {
            $price = $this->getPrice($api, 'BTC') * $btc * $fiat;
            $profit = ($this->amount * $price) - $this->paid_market;
          } elseif($this->market == "USD")
          {
            $price = $this->getPrice($api, 'BTC') * $btc;
            $profit = (($this->amount * $price) - $this->paid_market) * $fiat;
          } elseif($this->market == "BTC")
          {
            if($this->token != "USDT")
            {
              $price = $this->getPrice($api, 'BTC') * $btc;
              $profit = (($this->amount * $price) - $this->paid_usd) * $fiat;
            } else {
              $price = $this->getPrice($api, 'USD');
              $profit = (($this->amount * $price) - $this->paid_usd) * $fiat;
            }
          } elseif($this->market == "ETH")
          {
            $price = $this->getPrice($api, 'ETH') * $btc;
            $profit = (($this->amount * $price) - $this->paid_usd) * $fiat;
          } else {
            $price = $this->getPrice($api, 'BTC') * $btc;
            $profit = (($this->amount * $price) - $this->paid_usd) * $fiat;
          }
        }
      } else {
        $profit = 0;
      }
      return $profit;
    }

    public function getBtc($btc)
    {
      if($this->exchange == "Bittrex")
      {
        $bittrex_btc = Cache::remember('bittrex_btc', 1, function()
        {
          return bittrex::where('symbol', 'USDT-BTC')->select('price_btc')->first()->price_btc;
        });

        $btc = $bittrex_btc * 1.01;
      } elseif($this->exchange == "Poloniex") {
        $btc = Polo::where('symbol', 'USDT_BTC')->select('price_btc')->first()->price_btc * 1.01;
      } else {
        $btc = $btc;
      }

      return $btc;
    }

    // Gets this holdings value/worth
    public function getValue($api, $currency, $btc, $fiat)
    {
      if($this->exchange != "Manual")
      {
        $api = $this->exchange;
        $btc = $this->getBtc($btc);
      }
        if($this->tokenid != 0)
        {
          if($currency == "BTC")
          {
              $price = $this->getPrice($api, 'BTC');
              $worth = ($this->amount * $price);
          } elseif($currency == "USD")
          {
            if($this->market == "BTC")
            {
              if($this->token != "USDT")
              {
                $price = $this->getPrice($api, 'BTC') * $btc;
                $worth = ($this->amount * $price);
              } else {
                $price = $this->getPrice($api, 'USD');
                $worth = ($this->amount * $price);
              }
            } elseif($this->market == "USD")
            {
              $price = $this->getPrice($api, 'BTC') * $btc;
              $worth = ($this->amount * $price);
            } elseif($this->market == "ETH")
            {
              $price = $this->getPrice($api, 'ETH') * $btc;
              $worth = ($this->amount * $price);
            } elseif($this->market == "USDT") {
              $price = $this->getPrice($api, 'USDT');
              $worth = ($this->amount * $price);
            } else {
              $price = $this->getPrice($api, 'BTC') * $btc;
              $worth = ($this->amount * $price);
            }
          } else {
            // If market is same as display currency use paid_market
            if($this->market == $currency)
            {
              $price = $this->getPrice($api, 'BTC') * $btc * $fiat;
              $worth = ($this->amount * $price);
            } elseif($this->market == "USD")
            {
              $price = $this->getPrice($api, 'BTC') * $btc;
              $worth = (($this->amount * $price)) * $fiat;
            } elseif($this->market == "BTC")
            {
              if($this->token != "USDT")
              {
                $price = $this->getPrice($api, 'BTC') * $btc;
                $worth = (($this->amount * $price)) * $fiat;
              } else {
                $price = $this->getPrice($api, 'USD') * $fiat;
                $worth = ($this->amount * $price);
              }
            } elseif($this->market == "ETH")
            {
              $price = $this->getPrice($api, 'ETH') * $btc;
              $worth = ($this->amount * $price);
            } else {
              $price = $this->getPrice($api, 'BTC') * $btc;
              $worth = (($this->amount * $price)) * $fiat;
            }
          }
      } else { // This is if the holding is a fiat, we need logic for this.
        if($currency == "BTC")
        {
          if($this->token == "USD")
          {
            $worth = $this->amount / $btc;
          } else {
            $worth = $this->amount / ($btc * $fiat);
          }
        } elseif($currency == "USD")
        {
          if($this->token == "USD")
          {
            $worth = $this->amount;
          } else {
            $worth = $this->amount * (1 / Multiplier::where('currency', $this->token)->select('price')->first()->price);
          }
        } else {
          if($this->token == $currency) {
            $worth = $this->amount;
          } elseif($this->token == "USD")
          {
            $worth = $this->amount * $fiat;
          } else {
            // To convert a fiat from one to another, for example if the holding is EUR and we have SEK display currency, we need to convert it to USD first and then to sek.
            $worth = $this->amount * (Multiplier::where('currency', $this->token)->select('price')->first()->price / 1) * $fiat;
          }
        }
      }

      return $worth;
    }

    //Gets this holdings price
    public function getPrice($api, $market)
    {
      $api = strtolower($api);
      if(!Cache::tags(['token', $api, $market])->get($this->token)) {
      if($market == "BTC")
      {
        if($api == "coinmarketcap")
        {
          $price = Crypto::where('symbol', $this->token)->select('price_btc')->first();
        } elseif($api == "bittrex")
        {
          $price = bittrex::where('symbol', $this->token)->select('price_btc')->first();
        } elseif($api == "poloniex")
        {
          $price = Polo::where('symbol', $this->token)->select('price_btc')->first();
        } elseif($api == "worldcoinindex")
        {
          $price = WorldCoin::where('symbol', $this->token)->select('price_btc')->first();
        }
        // Fallback to coinmarketcap if null
        if(!isSet($price))
        {
          $price = Crypto::where('symbol', $this->token)->select('price_btc')->first();
        }
        Cache::tags(['token', $api, $market])->put($this->token, $price->price_btc, 1);
        return $price->price_btc;

      } elseif($market == "ETH")
      {
        if($api == "coinmarketcap")
        {
          $price = Crypto::where('symbol', $this->token)->select('price_eth')->first();
        } elseif($api == "bittrex")
        {
          $price = bittrex::where('symbol', 'ETH-' . $this->token)->select('price_btc as price_eth')->first();
        } elseif($api == "poloniex")
        {
          $price = Polo::where('symbol', 'ETH_' . $this->token)->select('price_btc as price_eth')->first();
        }
        // Fallback to coinmarketcap if null
        if(!isSet($price))
        {
          $price = Crypto::where('symbol', $this->token)->select('price_eth')->first();
        }
        Cache::tags(['token', $api, $market])->put($this->token, $price->price_eth, 1);
        return $price->price_eth;

      } elseif($market == "EUR")
      {
        if($api == "coinmarketcap")
        {
          $price = Crypto::where('symbol', $this->token)->select('price_btc')->first();
        } elseif($api == "bittrex")
        {
          $price = bittrex::where('symbol', $this->token)->select('price_btc')->first();
        } elseif($api == "poloniex")
        {
          $price = Polo::where('symbol', $this->token)->select('price_btc')->first();
        } elseif($api == "coinbase")
        {
          $price = Token::where('currency', $this->token)->select('price_btc')->first();
        }
        // Fallback to coinmarketcap if null
        if(!isSet($price))
        {
          $price = Crypto::where('symbol', $this->token)->select('price_btc')->first();
        }
        Cache::tags(['token', $api, $market])->put($this->token, $price->price_btc, 1);
        return $price->price_btc;

      } elseif($market == "GBP" || $market == "AUD" || $market == "CAD")
      {
        if($api == "coinmarketcap")
        {
          $price = Crypto::where('symbol', $this->token)->select('price_btc')->first();
        } elseif($api == "bittrex")
        {
          $price = bittrex::where('symbol', $this->token)->select('price_btc')->first();
        } elseif($api == "poloniex")
        {
          $price = Polo::where('symbol', $this->token)->select('price_btc')->first();
        } elseif($api == "coinbase")
        {
          $price = Token::where('currency', $this->token)->select('price_btc')->first();
        }
        // Fallback to coinmarketcap if null
        if(!isSet($price))
        {
          $price = Crypto::where('symbol', $this->token)->select('price_btc')->first();
        }
        Cache::tags(['token', $api, $market])->put($this->token, $price->price_btc, 1);
        return $price->price_btc;

      } elseif($market == "USD")
      {
        if($api == "coinmarketcap")
        {
          $price = Crypto::where('symbol', $this->token)->select('price_usd')->first();
        } elseif($api == "bittrex")
        {
          $price = bittrex::where('symbol', 'USDT-' . $this->token)->select('price_btc as price_usd')->first();
        } elseif($api == "poloniex")
        {
          $price = Polo::where('symbol', 'USDT_' . $this->token)->select('price_btc as price_usd')->first();
        } elseif($api == "coinbase")
        {
          $price = Token::where('currency', $this->token)->select('price_usd')->first();
        }
        // Fallback to coinmarketcap if null
        if(!isSet($price))
        {
          $price = Crypto::where('symbol', $this->token)->select('price_usd')->first();
        }
        Cache::tags(['token', $api, $market])->put($this->token, $price->price_usd, 1);
        return $price->price_usd;

      } elseif($market == "USDT")
      {
        if($api == "coinmarketcap")
        {
          $price = Crypto::where('symbol', $this->token)->select('price_usd')->first();
        } elseif($api == "bittrex")
        {
          $price = bittrex::where('symbol', 'USDT-' . $this->token)->select('price_btc as price_usd')->first();
        } elseif($api == "poloniex")
        {
          $price = Polo::where('symbol', 'USDT_' . $this->token)->select('price_btc as price_usd')->first();
        }
        // Fallback to coinmarketcap if null
        if(!isSet($price))
        {
          $price = Crypto::where('symbol', $this->token)->select('price_usd')->first();
        }
        Cache::tags(['token', $api, $market])->put($this->token, $price->price_usd, 1);
        return $price->price_usd;

      } elseif($market == "XMR")
      {
        $price = Polo::where('symbol', 'XMR_' . $this->token)->select('price_btc')->first();
        Cache::tags(['token', $api, $market])->put($this->token, $price->price_btc, 1);
        return $price->price_btc;
      }
    } else {
      $price = Cache::tags(['token', $api, $market])->get($this->token);
      return $price;
    }



    }

    //Gets this holdings paid price
    public function getPaid($currency, $fiat)
    {
      if($currency == "BTC")
      {
        $paid = $this->paid_btc;
      } elseif($currency == "USD")
      {
        $paid = $this->paid_usd;
      } else {
        if($currency == $this->market)
        {
          $paid = $this->paid_market;
        } else {
          $paid = $this->paid_usd * $fiat;
        }
      }
      return $paid;
    }
}
