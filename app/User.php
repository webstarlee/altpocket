<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Group;
use App\Muted;
use App\Investment;
use App\PoloInvestment;
use App\BittrexInvestment;
use App\awarded;
use App\Crypto;
use App\bittrex;
use App\Polo;
use App\WorldCoin;
use App\Balance;
use App\Mining;
use App\Deposit;
use App\Withdraw;
use App\Multiplier;
use DB;
use Cache;
use Overtrue\LaravelFollow\Traits\CanFollow;
use Overtrue\LaravelFollow\Traits\CanLike;
use Overtrue\LaravelFollow\Traits\CanBeFollowed;

class User extends Authenticatable
{
    use Notifiable;
    use CanFollow, CanBeFollowed, CanLike;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'affiliate_id', 'referred_by', 'reg_ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'google2fa_secret',
    ];

    public function getAwards(){
        $awards = awarded::where('userid', $this->id)->get();

        return $awards;
    }



    public function roles() {
        return $this->belongsToMany(Group::class, 'user_group');
    }

    public function isAdmin() {
       return $this->roles()->where('name', 'Admin')->exists();
    }

    public function isDonator() {
       return $this->roles()->where('name', 'Donator')->exists();
    }

    public function isMod() {
       return $this->roles()->where('name', 'Moderator')->exists();
    }

    public function isVIP() {
       return $this->roles()->where('name', 'VIP')->exists();
    }

    public function isPremium() {
       return $this->roles()->where('name', 'Premium')->exists();
    }

    public function isFounder() {
      return $this->roles()->where('name', 'Founder')->select('id')->exists();
    }
    public function isStaff() {
      return $this->roles()->where('name', 'Staff')->select('id')->exists();
    }
    public function isBeta() {
       return $this->roles()->where('name', 'Tester')->exists();
    }

    public function groupColor() {
        if($this->isFounder()){
            return Group::where('name', 'Founder')->select('color')->first()->color;
        }
        if($this->isAdmin()){
            return Group::where('name', 'Admin')->select('color')->first()->color;
        }
        if($this->isStaff()){
            return Group::where('name', 'Staff')->select('color')->first()->color;
        }
        if($this->isDonator()){
            return Group::where('name', 'Donator')->select('color')->first()->color;
        }
    }

    public function groupName() {
        if($this->isFounder()){
            return Group::where('name', 'Founder')->first()->name;
        }
        if($this->isAdmin()){
            return Group::where('name', 'Admin')->first()->name;
        }
        if($this->isMod()){
            return Group::where('name', 'Mod')->first()->name;
        }
        if($this->isVIP()){
            return Group::where('name', 'VIP')->first()->name;
        }
        if($this->isPremium()){
            return Group::where('name', 'Premium')->first()->name;
        }
        if($this->isStaff()){
            return Group::where('name', 'Staff')->first()->name;
        }
        if($this->isBeta()){
            return Group::where('name', 'Tester')->first()->name;
        }
        if($this->isDonator()){
            return Group::where('name', 'Donator')->first()->name;
        }
    }

    public function groupStyle() {
        if($this->isFounder()){
            return Group::where('name', 'Founder')->first()->style;
        }
        if($this->isAdmin()){
            return Group::where('name', 'Admin')->first()->style;
        }
        if($this->isMod()){
            return Group::where('name', 'Mod')->first()->style;
        }
        if($this->isDonator()){
            return Group::where('name', 'Donator')->first()->style;
        }
        if($this->isVIP()){
            return Group::where('name', 'VIP')->first()->style;
        }
        if($this->isPremium()){
            return Group::where('name', 'Premium')->first()->style;
        }
        if($this->isStaff()){
            return Group::where('name', 'Staff')->first()->style;
        }
    }

    public function isMuted(){
        return Muted::where('userid', $this->id)->exists();
    }

    public function commentsOn(){
        if($this->comments == "on"){
            return 1;
        } else {
            return 0;
        }
    }


    //Old not used
    public function hasNotifiction(){
        $count = 0;

        if($this->unreadNotifications->take(1))
        {
          $count = 1;
        }
        if($count != 0){
            return 1;
        } else {
            return 0;
        }
    }

    public function getCurrency(){
        return $this->currency;
    }
    public function getInvestedBTC(){
        $investments = Investment::where([['userid', '=', $this->id], ['sold_at', '=', null]])->get();
        $btcinvested = 0;
        foreach($investments as $investment){
            $btcinvested += ($investment->bought_at * $investment->amount);
        }
        return $btcinvested;
    }

    public function getSpentBTC(){
        $investments = Investment::where([['userid', '=', $this->id]])->get();
        $btcinvested = 0;
        foreach($investments as $investment){
            $btcinvested += ($investment->bought_at * $investment->amount);
        }
        return $btcinvested;
    }
    public function getSoldBTC(){
        $investments = Investment::where([['userid', '=', $this->id], ['sold_at', '!=', null]])->get();
        $btcinvested = 0;
        foreach($investments as $investment){
            $btcinvested += ($investment->sold_at * $investment->amount);
        }
        return $btcinvested;
    }


    public function getProfit(){
        $investments = Investment::where([['userid', '=', $this->id], ['sold_at', '=', null]])->get();
        $networth = 0;
        foreach($investments as $investment){
            $networth += $investment->amount * DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_usd;
        }
        $profit = $networth - $this->invested;

        return $profit;


    }

    public function getNetWorth()
    {
      $investments = Investment::where([['userid', '=', $this->id], ['sold_at', '=', null]])->get();
      $networth = 0;
      foreach($investments as $investment){
          $networth += $investment->amount * DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_usd;
        }
      return $networth;
    }

    // Dynamic
    public function getNetWorthNew($api)
    {
      $balances = Cache::remember('balances'.$this->id, 30, function()
      {
        return Balance::where('userid', $this->id)->get();
      });


      $minings = Mining::where([['userid', '=', $this->id]])->select('amount', 'currency')->get();
      $networth = 0;

      foreach($balances as $balance)
      {
        if($balance->exchange == "Manual")
        {
            $networth += $balance->amount * $this->getPrice($balance->currency, 'BTC', $balance->exchange);
        } elseif($balance->exchange == "Poloniex")
        {
            $networth += $balance->amount * $this->getPrice($balance->currency, 'BTC', $balance->exchange);
        } elseif($balance->exchange == "Bittrex")
        {
            $networth += $balance->amount * $this->getPrice($balance->currency, 'BTC', $balance->exchange);
        }

      }

      foreach($minings as $mining)
      {
        if($api == "coinmarketcap")
        {
          if(Crypto::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * Crypto::where('symbol', $mining->currency)->first()->price_btc;
          } elseif(Polo::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * Polo::where('symbol', $mining->currency)->first()->price_btc;
          } elseif(bittrex::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * bittrex::where('symbol', $mining->currency)->first()->price_btc;
          }
        } elseif($api == "worldcoinindex")
        {
          if(Worldcoin::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * Worldcoin::where('symbol', $mining->currency)->first()->price_btc;
          } elseif(bittrex::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * bittrex::where('symbol', $mining->currency)->first()->price_btc;
          } elseif(Crypto::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * Crypto::where('symbol', $mining->currency)->first()->price_btc;
          }
        } elseif($api == "poloniex")
        {
          if(Polo::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * Polo::where('symbol', $mining->currency)->first()->price_btc;
          } elseif(bittrex::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * bittrex::where('symbol', $mining->currency)->first()->price_btc;
          } elseif(Crypto::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * Crypto::where('symbol', $mining->currency)->first()->price_btc;
          }
        } elseif($api == "bittrex")
        {
          if(bittrex::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * bittrex::where('symbol', $mining->currency)->first()->price_btc;
          } elseif(Polo::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * Polo::where('symbol', $mining->currency)->first()->price_btc;
          } elseif(Crypto::where('symbol', $mining->currency)->exists())
          {
            $networth += $mining->amount * Crypto::where('symbol', $mining->currency)->first()->price_btc;
          }
        }
      }

      return $networth;
    }

    public function getSoldProfit($currency)
    {
      $m_investments = Cache::remember('m_investments'.$this->id, 60, function()
      {
        return DB::table('manual_investments')->where([['userid', '=', $this->id]])->get();
      });

      $networth = 0;

      if(count($m_investments) >= 1)
      {
        if($currency == "BTC")
        {
          foreach($m_investments as $investment)
          {
            if($investment->sold_at != null)
            {
              $networth += (($investment->sold_at * $investment->amount) - ($investment->bought_at * $investment->amount));
            }
          }
        } elseif($currency == "USD")
        {
          foreach($m_investments as $investment)
          {
            if($investment->sold_at != null)
            {
              $networth += (($investment->sold_at * $investment->amount) * $investment->btc_price_sold_usd - ($investment->bought_at * $investment->amount) * $investment->btc_price_bought_usd);
            }
          }
        } else
        {
          foreach($m_investments as $investment)
          {
            if($investment->sold_at != null)
            {
              $networth += (($investment->sold_at * $investment->amount) * $investment->btc_price_sold_usd - ($investment->bought_at * $investment->amount) * $investment->btc_price_bought_usd) * Multiplier::where('currency', $currency)->select('price')->first()->price;
            }
          }
        }
    }


      return $networth;
    }

    // Dynamic
    public function getActiveWorth($api)
    {


      $p_investments = Cache::remember('p_investments'.$this->id, 60, function()
      {
        return DB::table('polo_investments')->where([['userid', '=', $this->id]])->get();
      });

      $b_investments = Cache::remember('b_investments'.$this->id, 60, function()
      {
        return DB::table('bittrex_investments')->where([['userid', '=', $this->id]])->get();
      });

      $m_investments = Cache::remember('m_investments'.$this->id, 60, function()
      {
        return DB::table('manual_investments')->where([['userid', '=', $this->id]])->get();
      });



      $networth = 0;

      foreach($p_investments as $polo)
      {
        if($polo->date_sold == null){
          if($polo->market == "BTC")
          {
            $networth += $polo->amount * $this->getPrice($polo->currency, $polo->market, 'Poloniex');
          }elseif($polo->market == 'ETH')
          {
            $networth += ($polo->amount * (Polo::where('symbol', 'ETH')->select('price_btc')->first()->price_btc * $this->getPrice($polo->currency, $polo->market, 'Poloniex')));
          } elseif($polo->market == "USDT")
          {
            $networth += ($polo->amount * $this->getPrice($polo->currency, $polo->market, 'Poloniex')) / Polo::where('symbol', 'USDT_BTC')->select('price_btc')->first()->price_btc;
          }

}
      }

      foreach($b_investments as $bit)
      {
        if($bit->date_sold == null){
          if($bit->market == "BTC")
          {
            $networth += $bit->amount * $this->getPrice($bit->currency, $bit->market, 'Bittrex');
          } elseif($bit->market == "ETH")
          {
            $networth += ($bit->amount * (bittrex::where('symbol', 'ETH')->select('price_btc')->first()->price_btc * $this->getPrice($bit->currency, $bit->market, 'Bittrex')));
          } elseif($bit->market == "USDT")
          {
            $networth += ($bit->amount * $this->getPrice($bit->currency, $bit->market, 'Bittrex')) / bittrex::where('symbol', 'USDT-BTC')->select('price_btc')->first()->price_btc;
          }
        }
    }

      foreach($m_investments as $bit)
      {
        if($bit->date_sold == null){
          $networth += $bit->amount * $this->getPrice($bit->currency, 'BTC', 'Manual');
        }
      }

      return $networth;
    }

    public function getMoney()
    {
      $balances = Balance::where([['userid', '=', $this->id], ['exchange', '!=', 'Manual']])->get();
      $networth = 0;
      foreach($balances as $balance)
      {
        $networth += $balance->amount * $this->getPrice($balance->currency, 'BTC', $balance->exchange);
      }
      return $networth;
    }


    public function getActiveWorth2($api)
    {
      $p_investments = PoloInvestment::where([['userid', '=', $this->id], ['date_sold', '=', null]])->get();
      $b_investments = BittrexInvestment::where([['userid', '=', $this->id], ['date_sold', '=', null]])->get();
            $networth = 0;

      foreach($p_investments as $polo)
      {
          if($polo->market == "BTC")
          {
            if(Polo::where('symbol', $polo->currency)->exists())
            {
              $networth += $polo->amount * Polo::where('symbol', $polo->currency)->first()->price_btc;
            } elseif(bittrex::where('symbol', $polo->currency)->exists())
            {
              $networth += $polo->amount * bittrex::where('symbol', $polo->currency)->first()->price_btc;
            } elseif(Crypto::where('symbol', $polo->currency)->exists())
            {
              $networth += $polo->amount * Crypto::where('symbol', $polo->currency)->first()->price_btc;
            }
          }elseif($polo->market == 'ETH')
          {
            $networth += ($polo->amount * (Polo::where('symbol', 'ETH')->first()->price_btc * $this->getPrice($polo->currency, $polo->market, 'Poloniex')));
          } elseif($polo->market == "USDT")
          {
            $networth += ($polo->amount * $this->getPrice($polo->currency, $polo->market, 'Poloniex')) / Polo::where('symbol', 'USDT_BTC')->first()->price_btc;
          }


      }

      foreach($b_investments as $bit)
      {
        if($bit->market == "BTC")
        {
          if(bittrex::where('symbol', $bit->currency)->exists())
          {
            $networth += $bit->amount * bittrex::where('symbol', $bit->currency)->first()->price_btc;
          } elseif(Polo::where('symbol', $bit->currency)->exists())
          {
            $networth += $bit->amount * Polo::where('symbol', $bit->currency)->first()->price_btc;
          } elseif(Crypto::where('symbol', $bit->currency)->exists())
          {
            $networth += $bit->amount * Crypto::where('symbol', $bit->currency)->first()->price_btc;
          }
        } elseif($bit->market == "ETH")
        {
          $networth += ($bit->amount * (bittrex::where('symbol', 'ETH')->first()->price_btc * $this->getPrice($bit->currency, $bit->market, 'Bittrex')));
        } elseif($bit->market == "USDT")
        {
          $networth += ($bit->amount * $this->getPrice($bit->currency, $bit->market, 'Bittrex')) / bittrex::where('symbol', 'USDT-BTC')->first()->price_btc;
        }
    }

      return $networth;
    }



    // Dynamic [CLEANED 2017-08-01]
    public function getPrice($currency, $market, $exchange)
      {
        $api = $this->api;
        $price = 0;

      if(!Cache::tags(['coins', $api, $market, $exchange])->get($currency)) {
        if($market == "Manual" || $market == "BTC" || $market == "Balance" || $market == "Mining")
        {
        if($exchange == "Manual" || $exchange == "Ethereum" || $exchange == "Nanopool" || $exchange == "Ethermine" || $exchange == "NiceHash")
        {
          if($api == "coinmarketcap")
          {
            // CMC
            $price = Crypto::where('symbol', $currency)->select('price_btc')->first();

            if($price === null)
            {
              $price = Polo::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price === null)
            {
              $price = bittrex::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price === null)
            {
              $price = WorldCoin::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price !== null)
            {
              $price = $price->price_btc;
            }

          } elseif($api == "worldcoinindex")
          {
            // WCI
            $price = WorldCoin::where('symbol', $currency)->select('price_btc')->first();

            if($price === null)
            {
              $price = Crypto::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price === null)
            {
              $price = Polo::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price === null)
            {
              $price = Bittrex::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price !== null)
            {
              $price = $price->price_btc;
            }
          } elseif($api == "bittrex")
          {
            // Bittrex
            $price = bittrex::where('symbol', $currency)->select('price_btc')->first();

            if($price === null)
            {
              $price = Polo::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price === null)
            {
              $price = Crypto::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price === null)
            {
              $price = WorldCoin::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price !== null)
            {
              $price = $price->price_btc;
            }
          } elseif($api == "poloniex")
          {
            // Poloniex
            $price = Polo::where('symbol', $currency)->select('price_btc')->first();

            if($price === null)
            {
              $price = bittrex::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price === null)
            {
              $price = Crypto::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price === null)
            {
              $price = WorldCoin::where('symbol', $currency)->select('price_btc')->first();
            }
            if($price !== null)
            {
              $price = $price->price_btc;
            }
          }
        } elseif($market == "BTC" && $exchange == "Poloniex" || $market == "Balance" && $exchange == "Poloniex")
        {
          // Poloniex
          $price = Polo::where('symbol', $currency)->select('price_btc')->first();

          if($price === null)
          {
            $price = bittrex::where('symbol', $currency)->select('price_btc')->first();
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_btc')->first();
          }
          if($price === null)
          {
            $price = WorldCoin::where('symbol', $currency)->select('price_btc')->first();
          }
          if($price !== null)
          {
            $price = $price->price_btc;
          }
        } elseif($market == "BTC" && $exchange == "Bittrex" || $market == "Balance" && $exchange == "Bittrex")
        {
          // Bittrex
          $price = bittrex::where('symbol', $currency)->select('price_btc')->first();

          if($price === null)
          {
            $price = Polo::where('symbol', $currency)->select('price_btc')->first();
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_btc')->first();
          }
          if($price === null)
          {
            $price = WorldCoin::where('symbol', $currency)->select('price_btc')->first();
          }
          if($price !== null)
          {
            $price = $price->price_btc;
          }
        }
      } elseif($market == "USDT" && $exchange == "Poloniex")
      {
        $symbol = "USDT_".$currency;
        $price = Polo::where('symbol', $symbol)->select('price_btc')->first();
        if($price !== null)
        {
          $price = $price->price_btc;
        }

      } elseif($market == "ETH" && $exchange == "Bittrex")
      {
        $symbol = "ETH-".$currency;
        $price = bittrex::where('symbol', $symbol)->select('price_btc')->first();
        if($price !== null)
        {
          $price = $price->price_btc;
        }
      } elseif($market == "ETH" && $exchange == "Poloniex")
      {
        $symbol = "ETH_".$currency;
        $price = Polo::where('symbol', $symbol)->select('price_btc')->first();
        if($price !== null)
        {
          $price = $price->price_btc;
        }
      } elseif($market == "USDT" && $exchange == "Bittrex")
      {
        $symbol = "USDT-".$currency;
        $price = bittrex::where('symbol', $symbol)->select('price_btc')->first();
        if($price !== null)
        {
          $price = $price->price_btc;
        }
      }
      Cache::tags(['coins', $api, $market, $exchange])->put($currency, $price, 1);
      return $price;
    } else {
      $price = Cache::tags(['coins', $api, $market, $exchange])->get($currency);
      return $price;
    }

    }


    public function getInvestments()
    {
      $summed = Cache::remember('investments'.$this->id, 60, function()
      {
        $poloniex = PoloInvestment::where([['userid', '=', $this->id]])->SelectRaw('*, "Poloniex" as exchange, comment as note');
        $bittrex = BittrexInvestment::where([['userid', '=', $this->id]])->SelectRaw('*, "Bittrex" as exchange, comment as note');
        return ManualInvestment::where([['userid', '=', $this->id]])->SelectRaw('*, "Manual" as exchange, comment as note')->union($poloniex)->union($bittrex)->get();
      });

      return $summed;
    }




    // Dynamic [CLEANED]
    public function getMultiplier()
    {
      $multiplier = 1;


      if($this->currency == 'BTC')
      {
        $multiplier = 1;
      } elseif($this->currency == 'USD')
      {
        $multiplier = Cache::remember('btc', 60, function()
        {
          return Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd;
        });
      } else {
        $multiplier = Cache::remember('btc', 60, function()
        {
          return Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd;
        });

        $fiat = Cache::remember($this->currency, 5, function()
        {
          return Multiplier::where('currency', $this->currency)->select('price')->first()->price;
        });

        $multiplier = $multiplier * $fiat;
      }

      return $multiplier;
    }

    public function getEthMultiplier()
    {
      $multiplier = 1;

      if($this->currency == "USD")
      {
        $multiplier = Polo::where('symbol', 'ETH')->select('price_btc')->first()->price_btc * Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd;
      } elseif($this->currency == "BTC")
      {
        $multiplier = Polo::where('symbol', 'ETH')->select('price_btc')->first()->price_btc;
      } elseif($this->currency == "EUR")
      {
        $multiplier = Polo::where('symbol', 'ETH')->select('price_btc')->first()->price_btc * Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd * Multiplier::where('currency', $this->currency)->select('price')->first()->price;
      } else {
        $multiplier = Polo::where('symbol', 'ETH')->select('price_btc')->first()->price_btc * Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd * Multiplier::where('currency', $this->currency)->select('price')->first()->price;
      }

      return $multiplier;
    }


    public function getSymbol()
    {
      $symbol = "";
      if($this->currency == "USD" || $this->currency == "CAD")
      {
        $symbol = "$";
      } elseif($this->currency == "EUR")
      {
        $symbol = "€";
      } elseif($this->currency == "BTC")
      {
        $symbol = "<i class='fa fa-btc'></i> ";
      } elseif($this->currency == "SEK" || $this->currency == "NOK" || $this->currency == "DKK")
      {
        $symbol = "Kr";
      } elseif($this->currency == "GBP"){
        $symbol = "£";
      } elseif($this->currency == "SGD") {
        $symbol = "S$";
      } elseif($this->currency == "INR") {
        $symbol = "₹";
      }
      return $symbol;
    }

    //Get profit
    public function getSoldNew($currency)
    {
      $p_investments = PoloInvestment::where([['userid', '=', $this->id], ['date_sold', '!=', null]])->get();
      $b_investments = BittrexInvestment::where([['userid', '=', $this->id], ['date_sold', '!=', null]])->get();
      $m_investments = ManualInvestment::where([['userid', '=', $this->id], ['date_sold', '!=', null]])->get();

      $profit = 0;

      foreach($p_investments as $p)
      {
          if($p->soldmarket == "BTC" && $p->market == "BTC")
          {
          if($currency == "USD")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_usd) - (($p->amount * $p->bought_at) * $p->btc_price_bought_usd);
          } elseif($currency == "EUR")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_eur) - (($p->amount * $p->bought_at) * $p->btc_price_bought_eur);
          } elseif($currency == "GPB")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_gpb) - (($p->amount * $p->bought_at) * $p->btc_price_bought_gpb);
          } elseif($currency == "BTC")
          {
            $profit += ($p->amount * $p->sold_at) * ($p->amount * $p->bought_at);
          } else {
            $profit += ((($p->amount * $p->sold_at) * $p->btc_price_sold_usd) - (($p->amount * $p->bought_at) * $p->btc_price_bought_usd)) * Multiplier::where('currency', $currency)->first()->price;
          }
        } elseif($p->soldmarket == "BTC" && $p->market == "USDT")
        {
          if($currency == "USD")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_usd) - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_usd);
          } elseif($currency == "EUR")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_eur) - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_eur);
          } elseif($currency == "GPB")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_gpb) - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_gpb);
          } elseif($currency == "BTC")
          {
            $profit += (($p->amount * $p->sold_at)) - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt));
          }
        } elseif($p->soldmarket == "USDT" && $p->market == "USDT")
          {
            if($currency == "USD")
            {
              $profit += (($p->amount * $p->sold_at) / $p->btc_price_sold_usdt) * $p->btc_price_sold_usd - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_usd);
            } elseif($currency == "EUR")
            {
              $profit += (($p->amount * $p->sold_at) / $p->btc_price_sold_usdt) * $p->btc_price_sold_eur - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_eur);
            } elseif($currency == "GPB")
            {
              $profit += (($p->amount * $p->sold_at) / $p->btc_price_sold_usdt) * $p->btc_price_sold_gpb - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_gpb);
            } elseif($currency == "BTC")
            {
              $profit += (($p->amount * $p->sold_at) / $p->btc_price_sold_usdt) - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt));
            }
          }
      }

      foreach($b_investments as $p)
      {
          if($p->soldmarket == "BTC" && $p->market == "BTC")
          {
          if($currency == "USD")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_usd) - (($p->amount * $p->bought_at) * $p->btc_price_bought_usd);
          } elseif($currency == "EUR")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_eur) - (($p->amount * $p->bought_at) * $p->btc_price_bought_eur);
          } elseif($currency == "GPB")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_gpb) - (($p->amount * $p->bought_at) * $p->btc_price_bought_gpb);
          } elseif($currency == "BTC")
          {
            $profit += ($p->amount * $p->sold_at) * ($p->amount * $p->bought_at);
          }
        } elseif($p->soldmarket == "BTC" && $p->market == "USDT")
        {
          if($currency == "USD")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_usd) - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_usd);
          } elseif($currency == "EUR")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_eur) - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_eur);
          } elseif($currency == "GPB")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_gpb) - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_gpb);
          } elseif($currency == "BTC")
          {
            $profit += (($p->amount * $p->sold_at)) - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt));
          }
        } elseif($p->soldmarket == "USDT" && $p->market == "USDT")
          {
            if($currency == "USD")
            {
              $profit += (($p->amount * $p->sold_at) / $p->btc_price_sold_usdt) * $p->btc_price_sold_usd - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_usd);
            } elseif($currency == "EUR")
            {
              $profit += (($p->amount * $p->sold_at) / $p->btc_price_sold_usdt) * $p->btc_price_sold_eur - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_eur);
            } elseif($currency == "GPB")
            {
              $profit += (($p->amount * $p->sold_at) / $p->btc_price_sold_usdt) * $p->btc_price_sold_gpb - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt) * $p->btc_price_bought_gpb);
            } elseif($currency == "BTC")
            {
              $profit += (($p->amount * $p->sold_at) / $p->btc_price_sold_usdt) - ((($p->bought_at * $p->amount) / $p->btc_price_bought_usdt));
            }
          }
      }

      foreach($m_investments as $p)
      {
          if($currency == "USD")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_usd) - (($p->amount * $p->bought_at) * $p->btc_price_bought_usd);
          } elseif($currency == "EUR")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_eur) - (($p->amount * $p->bought_at) * $p->btc_price_bought_eur);
          } elseif($currency == "GPB")
          {
            $profit += (($p->amount * $p->sold_at) * $p->btc_price_sold_gpb) - (($p->amount * $p->bought_at) * $p->btc_price_bought_gpb);
          } elseif($currency == "BTC")
          {
            $profit += ($p->amount * $p->sold_at) * ($p->amount * $p->bought_at);
          }
      }


      return $profit;

    }


    // Dynamic
    public function getInvested($currency)
    {


      if($this->algorithm == 2)
      {
      $deposits = Cache::remember('deposits'.$this->id, 60, function()
      {
        return Deposit::where('userid', $this->id)->get();
      });

      $withdraws = Cache::remember('withdraws'.$this->id, 60, function()
      {
        return Withdraw::where('userid', $this->id)->get();
      });

      $d_amount = 0;
      $w_amount = 0;

      foreach($deposits as $deposit)
      {
        if($currency == "USD")
        {
          $d_amount += ($deposit->amount * $deposit->price) * $deposit->btc_price_deposit_usd;
        } elseif($currency == "EUR")
        {
          $d_amount += ($deposit->amount * $deposit->price) * $deposit->btc_price_deposit_usd * Multiplier::where('currency', $currency)->first()->price;
        } elseif($currency == "BTC")
        {
          $d_amount += ($deposit->amount * $deposit->price);
        } else {
          $d_amount += ($deposit->amount * $deposit->price) * $deposit->btc_price_deposit_usd * Multiplier::where('currency', $currency)->first()->price;
        }
      }

      foreach($withdraws as $withdraw)
      {
        if($currency == "USD")
        {
        $w_amount += ($withdraw->amount * $withdraw->price) * $withdraw->btc_price_deposit_usd;
      } elseif($currency == "EUR")
      {
        $w_amount += ($withdraw->amount * $withdraw->price) * $withdraw->btc_price_deposit_usd * Multiplier::where('currency', $currency)->first()->price;

      } elseif($currency == "BTC")
      {
        $w_amount += ($withdraw->amount * $withdraw->price);
      } else {
        $w_amount += ($withdraw->amount * $withdraw->price) * $withdraw->btc_price_deposit_usd * Multiplier::where('currency', $currency)->first()->price;
      }

      }

      $invested = $d_amount - $w_amount;

      if($invested < 0)
      {
        $invested = 0;
      }

      return $invested;


      }
      else {
          $paid = $this->getPaid($currency);
          return $paid;
      }
    }
    // Dynamic
    public function getPaid($currency)
    {
      $p_investments = Cache::remember('p_investments'.$this->id, 60, function()
      {
        return DB::table('polo_investments')->where([['userid', '=', $this->id]])->get();
      });

      $b_investments = Cache::remember('b_investments'.$this->id, 60, function()
      {
        return DB::table('bittrex_investments')->where([['userid', '=', $this->id]])->get();
      });

      $m_investments = Cache::remember('m_investments'.$this->id, 60, function()
      {
        return DB::table('manual_investments')->where([['userid', '=', $this->id]])->get();
      });

      $paid = 0;

      foreach($m_investments as $m)
      {
        if($m->sold_at == null){
        if($currency == "USD")
        {
          $paid += ($m->amount * $m->bought_at) * $m->btc_price_bought_usd;
        } elseif($currency == "EUR")
        {
          $paid += ($m->amount * $m->bought_at) * $m->btc_price_bought_usd * Multiplier::Where('currency', $currency)->select('price')->first()->price;
        } elseif($currency == "GPB")
        {
          $paid += ($m->amount * $m->bought_at) * $m->btc_price_bought_usd * Multiplier::Where('currency', $currency)->select('price')->first()->price;
        } elseif($currency == "BTC")
        {
          $paid += ($m->amount * $m->bought_at);
        } else {
          $paid += ($m->amount * $m->bought_at) * $m->btc_price_bought_usd * Multiplier::Where('currency', $currency)->select('price')->first()->price;
        }
      }
      }

      foreach($p_investments as $p)
      {
        if($p->sold_at == null){
          if($p->market == "BTC")
          {
          if($currency == "USD")
          {
            $paid += ($p->amount * $p->bought_at) * $p->btc_price_bought_usd;
          } elseif($currency == "BTC")
          {
            $paid += ($p->amount * $p->bought_at);
          } else
          {
            $paid += ($p->amount * $p->bought_at) * $p->btc_price_bought_usd * Multiplier::Where('currency', $currency)->select('price')->first()->price;
          }
        } elseif($p->market == "USDT")
        {
          if($currency == "USD")
          {
            $paid += ($p->amount * $p->bought_at) * ($p->btc_price_bought_usd / $p->btc_price_bought_usdt);
          } elseif($currency == "BTC")
          {
            $paid += ($p->amount * $p->bought_at) / $p->btc_price_bought_usdt;
          } else
          {
            $paid += ($p->amount * $p->bought_at) * ($p->btc_price_bought_usd / $p->btc_price_bought_usdt) * Multiplier::Where('currency', $currency)->select('price')->first()->price;
          }
        } elseif($p->market == "ETH")
        {
          if($currency == "USD")
          {
            $paid += ($p->amount * $p->bought_at) * ($p->btc_price_bought_usd / $p->btc_price_bought_eth);
          } elseif($currency == "BTC")
          {
            $paid +=  (1 / $p->btc_price_bought_eth) * ($p->amount * $p->bought_at);
          } else
          {
            $paid += ($p->amount * $p->bought_at) * ($p->btc_price_bought_usd / $p->btc_price_bought_eth) * Multiplier::Where('currency', $currency)->select('price')->first()->price;
          }
        }
      }
      }

      foreach($b_investments as $b)
      {
        if($b->sold_at == null){
          if($b->market == "BTC")
          {
          if($currency == "USD")
          {
            $paid += ($b->amount * $b->bought_at) * $b->btc_price_bought_usd;
          } elseif($currency == "BTC")
          {
            $paid += ($b->amount * $b->bought_at);
          } else
          {
            $paid += ($b->amount * $b->bought_at) * $b->btc_price_bought_usd * Multiplier::Where('currency', $currency)->select('price')->first()->price;
          }
        } elseif($b->market == "USDT")
        {
          if($currency == "USD")
          {
            $paid += ($b->amount * $b->bought_at) * ($b->btc_price_bought_usd / $b->btc_price_bought_usdt);
          } elseif($currency == "BTC")
          {
            $paid += ($b->amount * $b->bought_at) / $b->btc_price_bought_usdt;
          } else
          {
            $paid += ($b->amount * $b->bought_at) * ($b->btc_price_bought_usd / $b->btc_price_bought_usdt) * Multiplier::Where('currency', $currency)->first()->price;
          }
        } elseif($b->market == "ETH")
        {
          if($currency == "USD")
          {
            $paid += ($b->amount * $b->bought_at) * ($b->btc_price_bought_usd / $b->btc_price_bought_eth);
          } elseif($currency == "BTC")
          {
            $paid +=  (1 / $b->btc_price_bought_eth) * ($b->amount * $b->bought_at);
          } else
          {
            $paid += ($b->amount * $b->bought_at) * ($b->btc_price_bought_usd / $b->btc_price_bought_eth) * Multiplier::Where('currency', $currency)->first()->price;;
          }
        }
      }

      }

      return $paid;
    }

    public function getPaid2($currency)
    {
      $p_investments = PoloInvestment::where([['userid', '=', $this->id], ['date_sold', '=', null]])->get();
      $b_investments = BittrexInvestment::where([['userid', '=', $this->id], ['date_sold', '=', null]])->get();
      $paid = 0;

      foreach($p_investments as $p)
      {
          if($p->market == "BTC")
          {
          if($currency == "USD")
          {
            $paid += ($p->amount * $p->bought_at) * $p->btc_price_bought_usd;
          } elseif($currency == "BTC")
          {
            $paid += ($p->amount * $p->bought_at);
          } else
          {
            $paid += ($p->amount * $p->bought_at) * $p->btc_price_bought_usd * Multiplier::Where('currency', $currency)->first()->price;
          }
        } elseif($p->market == "USDT")
        {
          if($currency == "USD")
          {
            $paid += ($p->amount * $p->bought_at) * ($p->btc_price_bought_usd / $p->btc_price_bought_usdt);
          } elseif($currency == "BTC")
          {
            $paid += ($p->amount * $p->bought_at) / $p->btc_price_bought_usdt;
          } else
          {
            $paid += ($p->amount * $p->bought_at) * ($p->btc_price_bought_usd / $p->btc_price_bought_usdt) * Multiplier::Where('currency', $currency)->first()->price;
          }
        } elseif($p->market == "ETH")
        {
          if($currency == "USD")
          {
            $paid += ($p->amount * $p->bought_at) * ($p->btc_price_bought_usd / $p->btc_price_bought_eth);
          } elseif($currency == "BTC")
          {
            $paid +=  (1 / $p->btc_price_bought_eth) * ($p->amount * $p->bought_at);
          } else
          {
            $paid += ($p->amount * $p->bought_at) * ($p->btc_price_bought_usd / $p->btc_price_bought_eth) * Multiplier::Where('currency', $currency)->first()->price;
          }
        }
      }

      foreach($b_investments as $b)
      {
          if($b->market == "BTC")
          {
          if($currency == "USD")
          {
            $paid += ($b->amount * $b->bought_at) * $b->btc_price_bought_usd;
          } elseif($currency == "BTC")
          {
            $paid += ($b->amount * $b->bought_at);
          } else
          {
            $paid += ($b->amount * $b->bought_at) * $b->btc_price_bought_usd * Multiplier::Where('currency', $currency)->first()->price;
          }
        } elseif($b->market == "USDT")
        {
          if($currency == "USD")
          {
            $paid += ($b->amount * $b->bought_at) * ($b->btc_price_bought_usd / $b->btc_price_bought_usdt);
          } elseif($currency == "BTC")
          {
            $paid += $b->btc_price_bought_usdt / ($b->amount * $b->bought_at);
          } else
          {
            $paid += ($b->amount * $b->bought_at) * ($b->btc_price_bought_usd / $b->btc_price_bought_usdt) * Multiplier::Where('currency', $currency)->first()->price;
          }
        } elseif($b->market == "ETH")
        {
          if($currency == "USD")
          {
            $paid += ($b->amount * $b->bought_at) * ($b->btc_price_bought_usd / $b->btc_price_bought_eth);
          } elseif($currency == "BTC")
          {
            $paid +=  (1 / $b->btc_price_bought_eth) * ($b->amount * $b->bought_at);
          } else
          {
            $paid += ($b->amount * $b->bought_at) * ($b->btc_price_bought_usd / $b->btc_price_bought_eth) * Multiplier::Where('currency', $currency)->first()->price;;
          }
        }

      }

      return $paid;
    }



}
