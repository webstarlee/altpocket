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
use App\Token;
use App\Balance;
use App\Mining;
use App\Deposit;
use App\Withdraw;
use App\Multiplier;
use App\Block;
use App\Holding;
use App\HoldingLog;
use DB;
use Cache;
use App\Key;
use Overtrue\LaravelFollow\Traits\CanFollow;
use Overtrue\LaravelFollow\Traits\CanLike;
use Overtrue\LaravelFollow\Traits\CanBeFollowed;
use App\Notifications\ResetPassword;
use Hootlex\Friendships\Traits\Friendable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Lab404\Impersonate\Models\Impersonate;
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use CanFollow, CanBeFollowed, CanLike;
    use Friendable;
    use HasRoles;
    use Impersonate;

    // JWTAuth

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public function hasLikedStatus($status)
    {
      $liked = Cache::rememberForever($this->id.':hasLiked:'.$status->id, function() use ($status)
      {
        return $this->hasLiked($status);
      });

      return $liked;
    }

    public function hasLikedComment($comment)
    {
      $liked = Cache::rememberForever($this->id.':hasLikedComment:'.$comment->id, function() use ($comment) {
        return $this->hasLiked($comment);
      });

      return $liked;
    }

    public function hasLikedCommentReply($reply)
    {
      $liked = Cache::rememberForever($this->id.':hasLikedCommentReply:'.$reply->id, function() use ($reply) {
        return $this->hasLiked($reply);
      });

      return $liked;
    }

    public function hasLikedGroupComment($comment)
    {
      $liked = Cache::rememberForever($this->id.':hasLikedGroupComment:'.$comment->id, function() use ($comment) {
        return $this->hasLiked($comment);
      });

      return $liked;
    }

    public function hasLikedGroupReply($reply)
    {
      $liked = Cache::rememberForever($this->id.':hasLikedGroupReply:'.$reply->id, function() use ($reply) {
        return $this->hasLiked($reply);
      });

      return $liked;
    }

    public function getRefs()
    {
      $refs = Cache::rememberForever($this->affiliate_id, function()
      {
        return User::where('referred_by', $this->affiliate_id)->orderby('id', 'DESC')->select('id', 'username', 'created_at', 'avatar')->get();
      });

      return $refs;
    }




    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

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

    public function getGroups(){
        $groups = DB::table('user_group')->where('user_id', $this->id)->get();

        return $groups;
    }

    public function hasBlocked($id)
    {
      if(Block::where([['userid', '=', $this->id], ['blocked', '=', $id]])->exists())
      {
        return true;
      }
    }


    public function isOnline()
    {
      return Cache::has('2user-is-online-' . $this->id);
    }

    public function getAvatar()
    {
      if($this->avatar == "default.jpg")
      {
        $avatar = 'https://altpocket.io/assets/img/default.png';
      } else {
        $avatar = 'https://altpocket.io/uploads/avatars/'.$this->id.'/'.$this->avatar;
      }

      return $avatar;
    }


    public function hasDonated()
    {
      if($this->isDonator())
      {
        return true;
      } elseif($this->isSponsor())
      {
        return true;
      } elseif($this->isVIP())
      {
        return true;
      } else {
        return false;
      }
    }

    public function getEmblem()
    {
      if($this->isFounder()){
        $icon = Cache::rememberForever('founderIcon', function()
        {
          return Group::where('name', 'Founder')->select('icon')->first()->icon;
        });
        return $icon;
      }
      if($this->isAdmin()){
        $icon = Cache::rememberForever('adminIcon', function()
        {
          return Group::where('name', 'Admin')->select('icon')->first()->icon;
        });
        return $icon;
      }
      if($this->isBotDev()){
        return Group::where('name', 'Bot Developer')->first()->icon;
      }
      if($this->isMod()){
          return Group::where('name', 'Mod')->first()->icon;
      }
      if($this->isVIP()){
        $icon = Cache::rememberForever('vipIcon', function()
        {
          return Group::where('name', 'VIP')->select('icon')->first()->icon;
        });
        return $icon;
      }
      if($this->isPremium()){
          return Group::where('name', 'Premium')->first()->icon;
      }
      if($this->isStaff()){
        $icon = Cache::rememberForever('staffIcon6', function()
        {
          return Group::where('name', 'Staff')->select('icon')->first()->icon;
        });
        return $icon;
      }
      if($this->isBeta()){
          return Group::where('name', 'Tester')->first()->icon;
      }
      if($this->isSponsor()){
        $icon = Cache::rememberForever('sponsorIcon3', function()
        {
          return Group::where('name', 'Sponsor')->select('icon')->first()->icon;
        });
        return $icon;
      }
      if($this->isDonator()){
        $icon = Cache::rememberForever('donatorIcon', function()
        {
          return Group::where('name', 'Donator')->select('icon')->first()->icon;
        });
        return $icon;
      }
    }





    public function groups() {
        return $this->belongsToMany(Group::class, 'user_group');
    }

    public function isAdmin() {
      $role = Cache::remember('isAdmin-2'.$this->id, 7600, function()
      {
        return $this->groups()->where('name', 'Admin')->select('id')->exists();
      });

      return $role;
    }

    public function isDonator() {
      $role = Cache::remember('isDonator'.$this->id, 7600, function()
      {
        return $this->groups()->where('name', 'Donator')->select('id')->exists();
      });

      return $role;
    }

    public function isSponsor() {
      $role = Cache::remember('isSponsor5'.$this->id, 7600, function()
      {
        return $this->groups()->where('name', 'Sponsor')->select('id')->exists();
      });

      return $role;
    }

    public function isMod() {
      $role = Cache::remember('isMod2'.$this->id, 7600, function()
      {
        return $this->groups()->where('name', 'Mod')->select('id')->exists();
      });

      return $role;
    }

    public function isBotDev() {
      $role = Cache::remember('isBotDev'.$this->id, 7600, function()
      {
        return $this->groups()->where('name', 'Bot Developer')->select('id')->exists();
      });

      return $role;
    }

    public function isVIP() {
      $role = Cache::remember('isVIP6'.$this->id, 7600, function()
      {
        return $this->groups()->where('name', 'VIP')->select('id')->exists();
      });

      return $role;
    }

    public function isPremium() {
      $role = Cache::remember('isPremium'.$this->id, 7600, function()
      {
        return $this->groups()->where('name', 'Premium')->select('id')->exists();
      });

      return $role;
    }

    public function isFounder() {
      $role = Cache::remember('isFounder555'.$this->id, 7600, function()
      {
        return $this->groups()->where('name', 'Founder')->select('id')->exists();
      });

      return $role;
    }
    public function isStaff() {
      $role = Cache::remember('isStaff8'.$this->id, 7600, function()
      {
        return $this->groups()->where('name', 'Staff')->select('id')->exists();
      });

      return $role;
    }

    public function hasRights()
    {
      if($this->isStaff() || $this->isAdmin() || $this->isFounder() || $this->isMod() || $this->isBotDev())
      {
        return true;
      } else {
        return false;
      }
    }


    public function isBeta() {
      $role = Cache::remember('isTester'.$this->id, 7600, function()
      {
        return $this->groups()->where('name', 'Tester')->select('id')->exists();
      });

      return $role;
    }

    public function groupColor() {
        if($this->isFounder()){
          $color = Cache::remember('founderGroupColor', 7600, function()
          {
            return Group::where('name', 'Founder')->select('color')->first()->color;
          });
          return $color;
        }
        if($this->isAdmin()){
          $color = Cache::remember('adminGroupColor', 7600, function()
          {
            return Group::where('name', 'Admin')->select('color')->first()->color;
          });
          return $color;
        }
        if($this->isStaff()){
          $color = Cache::remember('staffGroupColor', 7600, function()
          {
            return Group::where('name', 'Staff')->select('color')->first()->color;
          });
          return $color;
        }
        if($this->isBotDev()){
          $color = Cache::remember('botGroupColor', 7600, function()
          {
            return Group::where('name', 'Bot Developer')->select('color')->first()->color;
          });
          return $color;
        }
        if($this->isVIP()){
          $color = Cache::remember('vipGroupColor', 7600, function()
          {
            return Group::where('name', 'VIP')->select('color')->first()->color;
          });
          return $color;
        }
        if($this->isSponsor()){
          $color = Cache::remember('sponsorGroupColor', 7600, function()
          {
            return Group::where('name', 'Sponsor')->select('color')->first()->color;
          });
          return $color;
        }
        if($this->isDonator()){
          $color = Cache::remember('donatorGroupColor', 7600, function()
          {
            return Group::where('name', 'Donator')->select('color')->first()->color;
          });
          return $color;
        }
    }

    public function groupName() {
        if($this->isFounder()){
          $name = Cache::remember('founderGroupName', 7600, function()
          {
            return Group::where('name', 'Founder')->select('name')->first()->name;
          });
          return $name;
        }
        if($this->isAdmin()){
          $name = Cache::remember('adminGroupName', 7600, function()
          {
            return Group::where('name', 'Admin')->select('name')->first()->name;
          });
          return $name;
        }
        if($this->isBotDev()){
          $name = Cache::remember('botGroupName', 7600, function()
          {
            return Group::where('name', 'Bot Developer')->select('name')->first()->name;
          });
          return $name;
        }
        if($this->isMod()){
          $name = Cache::remember('modGroupName', 7600, function()
          {
            return Group::where('name', 'Mod')->select('name')->first()->name;
          });
          return $name;
        }

        if($this->isPremium()){
          $name = Cache::remember('premiumGroupName', 7600, function()
          {
            return Group::where('name', 'Premium')->select('name')->first()->name;
          });
          return $name;
        }
        if($this->isStaff()){
          $name = Cache::remember('staffGroupName', 7600, function()
          {
            return Group::where('name', 'Staff')->select('name')->first()->name;
          });
          return $name;
        }
        if($this->isBeta()){
          $name = Cache::remember('testerGroupName', 7600, function()
          {
            return Group::where('name', 'Tester')->select('name')->first()->name;
          });
          return $name;
        }
        if($this->isVIP()){
          $name = Cache::remember('vipGroupName', 7600, function()
          {
            return Group::where('name', 'VIP')->select('name')->first()->name;
          });
          return $name;
        }
        if($this->isSponsor()){
          $name = Cache::remember('sponsorGroupName', 7600, function()
          {
            return Group::where('name', 'Sponsor')->select('name')->first()->name;
          });
          return $name;
        }
        if($this->isDonator()){
          $name = Cache::remember('donatorGroupName', 7600, function()
          {
            return Group::where('name', 'Donator')->select('name')->first()->name;
          });
          return $name;
        }
    }

    public function groupStyle() {
        if($this->isFounder()){
          $style = Cache::remember('founderStyle', 7600, function()
          {
            return Group::where('name', 'Founder')->select('style')->first()->style;
          });
          return $style;
        }
        if($this->isAdmin()){
          $style = Cache::remember('adminStyle', 7600, function()
          {
            return Group::where('name', 'Admin')->select('style')->first()->style;
          });
          return $style;
        }
        if($this->isMod()){
          $style = Cache::remember('modStyle', 7600, function()
          {
            return Group::where('name', 'Mod')->select('style')->first()->style;
          });
          return $style;
        }
        if($this->isVIP()){
          $style = Cache::remember('vipStyle', 7600, function()
          {
            return Group::where('name', 'VIP')->select('style')->first()->style;
          });
          return $style;
        }
        if($this->isSponsor()){
          $style = Cache::remember('sponsorStyle', 7600, function()
          {
            return Group::where('name', 'Sponsor')->select('style')->first()->style;
          });
          return $style;
        }
        if($this->isDonator()){
          $style = Cache::rememberForever('donatorStyle2', function()
          {
            return Group::where('name', 'Donator')->select('style')->first()->style;
          });
          return $style;
        }
        if($this->isPremium()){
          $style = Cache::remember('premiumStyle', 7600, function()
          {
            return Group::where('name', 'Premium')->select('style')->first()->style;
          });
          return $style;
        }
        if($this->isStaff()){
          $style = Cache::remember('staffStyle', 7600, function()
          {
            return Group::where('name', 'Staff')->select('style')->first()->style;
          });
          return $style;
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


    public function hasKey()
    {
      if(DB::table('keys')->where([['userid', '=', $this->id], ['type', '=', 'Exchange']])->exists())
      {
        return true;
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

    public function getWorth($api, $multiplier, $fiat)
    {
      $balances = Cache::remember('balances'.$this->id, 30, function()
      {
        return Balance::where('userid', $this->id)->get();
      });

      $minings = Cache::remember('minings'.$this->id, 30, function()
      {
        return Mining::where([['userid', '=', $this->id]])->select('amount', 'currency')->get();
      });

      $networth = 0;

      foreach($balances as $balance)
      {
        $data = $balance->getData($api, $multiplier, $fiat);

        $networth += $data->worth;
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

      $minings = Cache::remember('minings'.$this->id, 30, function()
      {
        return Mining::where([['userid', '=', $this->id]])->select('amount', 'currency')->get();
      });

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
        } elseif($balance->exchange == "Coinbase")
        {
            $networth += $balance->amount * $this->getPrice($balance->currency, 'Balance', $balance->exchange);
        } elseif($balance->exchange == "Ethereum" || $balance->exchange == "Ethermine" || $balance->exchange == "NiceHash" || $balance->exchange == "Nanopool")
        {
            $networth += $balance->amount * $this->getPrice($balance->currency, 'Balance', $balance->exchange);
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
        return DB::table('manual_investments')->where([['userid', '=', $this->id], ['amount', '>', '0']])->get();
      });

      $networth = 0;

      if(count($m_investments) >= 1)
      {
        if($currency == "BTC")
        {
          foreach($m_investments as $investment)
          {
            if($investment->sold_at != null && $investment->sold_market == "BTC" && $investment->market == "BTC")
            {
              $networth += (($investment->sold_at * $investment->amount) - ($investment->bought_at * $investment->amount));
            } elseif($investment->sold_at != null && $investment->sold_market == "ETH" && $investment->market == "ETH")
            {
              $networth += (($investment->sold_at * $investment->amount) * (1 / $investment->btc_price_sold_eth) - ($investment->bought_at * $investment->amount) * (1 / $investment->btc_price_bought_eth));
            } elseif($investment->sold_at != null && $investment->sold_market == "BTC" && $investment->market == "ETH")
            {
              $networth += (($investment->sold_at * $investment->amount) - ($investment->bought_at * $investment->amount) * (1 / $investment->btc_price_bought_eth));
            } elseif($investment->sold_at != null && $investment->sold_market == "ETH" && $investment->market == "BTC")
            {
              $networth += (($investment->sold_at * $investment->amount) * (1 / $investment->btc_price_bought_eth) - ($investment->bought_at * $investment->amount));
            }
          }
        } elseif($currency == "USD")
        {
          foreach($m_investments as $investment)
          {
            if($investment->sold_at != null && $investment->sold_market == "BTC" && $investment->market == "BTC")
            {
              $networth += (($investment->sold_at * $investment->amount) * $investment->btc_price_sold_usd - ($investment->bought_at * $investment->amount) * $investment->btc_price_bought_usd);
            } elseif($investment->sold_at != null && $investment->sold_market == "ETH" && $investment->market == "ETH")
            {
              $networth += (($investment->sold_at * $investment->amount) * ($investment->btc_price_sold_usd / $investment->btc_price_sold_eth) - ($investment->bought_at * $investment->amount) * ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth));
            } elseif($investment->sold_at != null && $investment->sold_market == "BTC" && $investment->market == "ETH")
            {
              $networth += (($investment->sold_at * $investment->amount) * ($investment->btc_price_sold_usd) - ($investment->bought_at * $investment->amount) * ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth));
            } elseif($investment->sold_at != null && $investment->sold_market == "ETH" && $investment->market == "BTC")
            {
              $networth += (($investment->sold_at * $investment->amount) * ($investment->btc_price_sold_usd / $investment->btc_price_sold_eth) - ($investment->bought_at * $investment->amount) * ($investment->btc_price_bought_usd));
            }
          }
        } else
        {
          foreach($m_investments as $investment)
          {
            if($investment->sold_at != null && $investment->sold_market == "BTC" && $investment->market == "BTC")
            {
              $networth += (($investment->sold_at * $investment->amount) * $investment->btc_price_sold_usd - ($investment->bought_at * $investment->amount) * $investment->btc_price_bought_usd) * Multiplier::where('currency', $currency)->select('price')->first()->price;
            } elseif($investment->sold_at != null && $investment->sold_market == "ETH" && $investment->market == "ETH")
            {
              $networth += (($investment->sold_at * $investment->amount) * ($investment->btc_price_sold_usd / $investment->btc_price_sold_eth) - ($investment->bought_at * $investment->amount) * ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth)) * Multiplier::where('currency', $currency)->select('price')->first()->price;
            } elseif($investment->sold_at != null && $investment->sold_market == "BTC" && $investment->market == "ETH")
            {
              $networth += (($investment->sold_at * $investment->amount) * ($investment->btc_price_sold_usd) - ($investment->bought_at * $investment->amount) * ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth)) * Multiplier::where('currency', $currency)->select('price')->first()->price;
            } elseif($investment->sold_at != null && $investment->sold_market == "ETH" && $investment->market == "BTC")
            {
              $networth += (($investment->sold_at * $investment->amount) * ($investment->btc_price_sold_usd / $investment->btc_price_sold_eth) - ($investment->bought_at * $investment->amount) * ($investment->btc_price_bought_usd)) * Multiplier::where('currency', $currency)->select('price')->first()->price;
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
        return DB::table('polo_investments')->where([['userid', '=', $this->id], ['amount', '>', '0']])->get();
      });

      $b_investments = Cache::remember('b_investments'.$this->id, 60, function()
      {
        return DB::table('bittrex_investments')->where([['userid', '=', $this->id], ['amount', '>', '0']])->get();
      });

      $m_investments = Cache::remember('m_investments'.$this->id, 60, function()
      {
        return DB::table('manual_investments')->where([['userid', '=', $this->id], ['amount', '>', '0']])->get();
      });

      $c_investments = Cache::remember('c_investments'.$this->id, 60, function()
      {
        return DB::table('investments')->where([['userid', '=', $this->id], ['exchange', '=', 'Coinbase'], ['amount', '>', '0']])->get();
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

      foreach($c_investments as $c)
      {
        if($c->date_sold == null){
          if($c->market == "USD")
          {
            $networth += $c->amount * $this->getPrice($c->currency, $c->market, 'Coinbase');
          } elseif($c->market == 'EUR')
          {
            $networth += $c->amount * $this->getPrice($c->currency, $c->market, 'Coinbase');
          }

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
      $p_investments = PoloInvestment::where([['userid', '=', $this->id], ['date_sold', '=', null], ['amount', '>', '0']])->get();
      $b_investments = BittrexInvestment::where([['userid', '=', $this->id], ['date_sold', '=', null], ['amount', '>', '0']])->get();
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
        } elseif($market == "USD" && $exchange == "Coinbase" || $market == "EUR" && $exchange == "Coinbase" || $market == "GBP" && $exchange == "Coinbase" || $market == "AUD" && $exchange == "Coinbase" || $market == "Balance" && $exchange == "Coinbase")
        {
          // Poloniex
          $price = Token::where([['currency', '=', $currency], ['exchange', '=', $exchange]])->select('price_btc')->first();

          if($price === null)
          {
            $price = bittrex::where('symbol', $currency)->select('price_btc')->first();
          }
          if($price === null)
          {
            $price = Polo::where('symbol', $currency)->select('price_btc')->first();
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_btc')->first();
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
      } elseif($market == "ETH" && $exchange == "Manual")
      {
        if($api == "coinmarketcap") {
          // CMC
          $price = Crypto::where('symbol', $currency)->select('price_eth')->first();
          if($price !== null)
          {
            $price = $price->price_eth;
          } else {
            $price = 0;
          }
        } elseif($api == "worldcoinindex")
        {
          $price = WorldCoin::where('symbol', $currency)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc / WorldCoin::where('symbol', 'ETH')->select('price_btc')->first()->price_btc;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_eth')->first();
            if($price !== null)
            {
              $price = $price->price_eth;
            } else {
              $price = 0;
            }
          }
        } elseif($api == "bittrex") {
          $symbol = "ETH-".$currency;
          $price = bittrex::where('symbol', $symbol)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_eth')->first();
            if($price !== null)
            {
              $price = $price->price_eth;
            } else {
              $price = 0;
            }
          }
        } elseif($api == "poloniex") {
          $symbol = "ETH_".$currency;
          $price = Polo::where('symbol', $symbol)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_eth')->first();
            if($price !== null)
            {
              $price = $price->price_eth;
            } else {
              $price = 0;
            }
          }
        }
      } elseif($market == "EUR" && $exchange == "Manual")
      {
        if($api == "coinmarketcap") {
          // CMC
          $price = Crypto::where('symbol', $currency)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc;
          } else {
            $price = 0;
          }
        } elseif($api == "worldcoinindex")
        {
          $price = WorldCoin::where('symbol', $currency)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_btc')->first();
            if($price !== null)
            {
              $price = $price->price_btc;
            } else {
              $price = 0;
            }
          }
        } elseif($api == "bittrex") {
          $price = bittrex::where('symbol', $currency)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_btc')->first();
            if($price !== null)
            {
              $price = $price->price_btc;
            } else {
              $price = 0;
            }
          }
        } elseif($api == "poloniex") {
          $price = Polo::where('symbol', $currency)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_btc')->first();
            if($price !== null)
            {
              $price = $price->price_btc;
            } else {
              $price = 0;
            }
          }
        }
      } elseif($market == "USDT" && $exchange == "Manual")
      {
        if($api == "coinmarketcap") {
          // CMC
          $price = Crypto::where('symbol', $currency)->select('price_usd')->first();
          if($price !== null)
          {
            $price = $price->price_usd;
          } else {
            $price = 0;
          }
        } elseif($api == "worldcoinindex")
        {
          $price = WorldCoin::where('symbol', $currency)->select('price_usd')->first();
          if($price !== null)
          {
            $price = $price->price_usd;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_usd')->first();
            if($price !== null)
            {
              $price = $price->price_usd;
            } else {
              $price = 0;
            }
          }
        } elseif($api == "bittrex") {
          $symbol = "USDT-".$currency;
          $price = bittrex::where('symbol', $symbol)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_usd')->first();
            if($price !== null)
            {
              $price = $price->price_usd;
            } else {
              $price = 0;
            }
          }
        } elseif($api == "poloniex") {
          $symbol = "USDT_".$currency;
          $price = Polo::where('symbol', $symbol)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_usd')->first();
            if($price !== null)
            {
              $price = $price->price_usd;
            } else {
              $price = 0;
            }
          }
        }
      }  elseif($market == "USD" && $exchange == "Manual")
      {
        if($api == "coinmarketcap") {
          // CMC
          $price = Crypto::where('symbol', $currency)->select('price_usd')->first();
          if($price !== null)
          {
            $price = $price->price_usd;
          } else {
            $price = 0;
          }
        } elseif($api == "worldcoinindex")
        {
          $price = WorldCoin::where('symbol', $currency)->select('price_usd')->first();
          if($price !== null)
          {
            $price = $price->price_usd;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_usd')->first();
            if($price !== null)
            {
              $price = $price->price_usd;
            } else {
              $price = 0;
            }
          }
        } elseif($api == "bittrex") {
          $symbol = "USDT-".$currency;
          $price = bittrex::where('symbol', $symbol)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_usd')->first();
            if($price !== null)
            {
              $price = $price->price_usd;
            } else {
              $price = 0;
            }
          }
        } elseif($api == "poloniex") {
          $symbol = "USDT_".$currency;
          $price = Polo::where('symbol', $symbol)->select('price_btc')->first();
          if($price !== null)
          {
            $price = $price->price_btc;
          }
          if($price === null)
          {
            $price = Crypto::where('symbol', $currency)->select('price_usd')->first();
            if($price !== null)
            {
              $price = $price->price_usd;
            } else {
              $price = 0;
            }
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
      } elseif($market == "XMR" && $exchange == "Poloniex")
      {
        $symbol = "XMR_".$currency;
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
      } elseif($market == "EUR" && $exchange == "Coinbase")
      {
        $price = Token::where('currency', $currency)->select('price_btc')->first();
        if($price !== null)
        {
          $price = $price->price_btc;
        }
      } elseif($market == "USD" && $exchange == "Coinbase")
      {
        $price = Token::where('currency', $currency)->select('price_btc')->first();
        if($price !== null)
        {
          $price = $price->price_btc;
        }
      } elseif($market == "GBP" && $exchange == "Coinbase")
      {
        $price = Token::where('currency', $currency)->select('price_btc')->first();
        if($price !== null)
        {
          $price = $price->price_btc;
        }
      } elseif($market == "AUD" && $exchange == "Coinbase")
      {
        $price = Token::where('currency', $currency)->select('price_btc')->first();
        if($price !== null)
        {
          $price = $price->price_btc;
        }
      } elseif($market == "CAD" && $exchange == "Coinbase")
      {
        $price = Token::where('currency', $currency)->select('price_btc')->first();
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
        $poloniex = PoloInvestment::where([['userid', '=', $this->id], ['amount', '>', '0']])->SelectRaw('*, "Poloniex" as exchange, comment as note');
        $bittrex = BittrexInvestment::where([['userid', '=', $this->id], ['amount', '>', '0']])->SelectRaw('*, "Bittrex" as exchange, comment as note');
        $coinbase = Investment::where([['userid', '=', $this->id], ['amount', '>', '0']])->SelectRaw('*, comment as note');
        return ManualInvestment::where([['userid', '=', $this->id], ['amount', '>', '0']])->SelectRaw('*, "Manual" as exchange, comment as note')->union($poloniex)->union($bittrex)->union($coinbase)->orderBy('date_bought', 'desc')->get();
      });

      return $summed;
    }


    public function getFiat()
    {
      if($this->currency != "USD" && $this->currency != "BTC")
      {
        $fiat = Cache::remember('userFiat:'.$this->id, 7600, function()
        {
          return Multiplier::where('currency', $this->currency)->select('price')->first()->price;
        });
        return $fiat;
      } else {
        return 1;
      }
    }

    public function hasExchange($key)
    {
      if($key == "poloniex")
      {
        $api = Cache::remember('hasPoloKey2:'.$this->id, 7600, function()
        {
          return Key::where([['userid', '=', $this->id], ['exchange', '=', 'Poloniex']])->exists();
        });

        return $api;
      } elseif($key == "bittrex")
      {
        $api = Cache::remember('hasBittrexKey2:'.$this->id, 7600, function()
        {
          return Key::where([['userid', '=', $this->id], ['exchange', '=', 'Bittrex']])->exists();
        });

        return $api;
      } elseif($key == "coinbase")
      {
        $api = Cache::remember('hasCoinbaseKey2:'.$this->id, 7600, function()
        {
          return Key::where([['userid', '=', $this->id], ['exchange', '=', 'Coinbase']])->exists();
        });

        return $api;
      }
    }

    public function getBtcCache()
    {
      $btc = Cache::remember('btcCache', 15, function()
      {
        return Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd;
      });

      return $btc;
    }

    public function getEthCache()
    {
      $eth = Cache::remember('ethCache', 15, function()
      {
        return Crypto::where('symbol', 'ETH')->select('price_usd')->first()->price_usd;
      });

      return $eth;
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
        $multiplier = Polo::where('symbol', 'ETH')->select('price_btc')->first()->price_btc * Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd * $this->getFiat();
      } else {
        $multiplier = Polo::where('symbol', 'ETH')->select('price_btc')->first()->price_btc * Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd * $this->getFiat();
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
      } elseif($this->currency == "ZAR") {
        $symbol = "R";
      } elseif($this->currency == "HKD") {
        $symbol = "HK$";
      }
      return $symbol;
    }

    //Get profit
    public function getSoldNew($currency)
    {
      $p_investments = PoloInvestment::where([['userid', '=', $this->id], ['date_sold', '!=', null], ['amount', '>', '0']])->get();
      $b_investments = BittrexInvestment::where([['userid', '=', $this->id], ['date_sold', '!=', null], ['amount', '>', '0']])->get();
      $m_investments = ManualInvestment::where([['userid', '=', $this->id], ['date_sold', '!=', null], ['amount', '>', '0']])->get();

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
      if($this->invested == 0){
        if($this->algorithm == 2){
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
    } else {
      $paid = $this->getPaid($currency);
      return $paid;
    }
  } else {
    if($currency == "BTC")
    {
       return $this->invested / Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd;
     } elseif($currency == "USD") {
       return $this->invested;
     } else {
       return $this->invested * Multiplier::where('currency', $currency)->first()->price;
     }
  }
    }
    // Dynamic
    public function getPaid($currency)
    {
      $p_investments = Cache::remember('p_investments'.$this->id, 60, function()
      {
        return DB::table('polo_investments')->where([['userid', '=', $this->id], ['amount', '>', '0']])->get();
      });

      $b_investments = Cache::remember('b_investments'.$this->id, 60, function()
      {
        return DB::table('bittrex_investments')->where([['userid', '=', $this->id], ['amount', '>', '0']])->get();
      });

      $m_investments = Cache::remember('m_investments'.$this->id, 60, function()
      {
        return DB::table('manual_investments')->where([['userid', '=', $this->id], ['amount', '>', '0']])->get();
      });

      $c_investments = Cache::remember('c_investments'.$this->id, 60, function()
      {
        return DB::table('investments')->where([['userid', '=', $this->id], ['exchange', '=', 'Coinbase'], ['amount', '>', '0']])->get();
      });

      $paid = 0;

      foreach($m_investments as $m)
      {
        if($m->sold_at == null){
          if($m->market == "BTC")
          {
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
          } else {
            if($currency == "USD")
            {
              $paid += ($m->amount * $m->bought_at) * ($m->btc_price_bought_usd / $m->btc_price_bought_eth);
            } elseif($currency == "EUR")
            {
              $paid += ($m->amount * $m->bought_at) * ($m->btc_price_bought_usd / $m->btc_price_bought_eth) * Multiplier::Where('currency', $currency)->select('price')->first()->price;
            } elseif($currency == "GPB")
            {
              $paid += ($m->amount * $m->bought_at) * ($m->btc_price_bought_usd / $m->btc_price_bought_eth) * Multiplier::Where('currency', $currency)->select('price')->first()->price;
            } elseif($currency == "ETH")
            {
              $paid += ($m->amount * $m->bought_at);
            } elseif($currency == "BTC")
            {
              $paid += ($m->amount * $m->bought_at);
            } else {
              $paid += ($m->amount * $m->bought_at) * ($m->btc_price_bought_usd / $m->btc_price_bought_eth) * Multiplier::Where('currency', $currency)->select('price')->first()->price;
            }
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
        } elseif($p->market == "XMR")
        {
          if($currency == "USD")
          {
            $paid += ($p->amount * $p->bought_at) * ($p->btc_price_bought_usd / DB::table('historicals')->where('created_at', date('Y-m-d 00:00:00', strtotime($p->date_bought)))->select('XMR')->first()->XMR);
          } elseif($currency == "BTC")
          {
            $paid +=  (1 / DB::table('historicals')->where('created_at', date('Y-m-d 00:00:00', strtotime($p->date_bought)))->select('XMR')->first()->XMR) * ($p->amount * $p->bought_at);
          } else
          {
            $paid += ($p->amount * $p->bought_at) * ($p->btc_price_bought_usd / DB::table('historicals')->where('created_at', date('Y-m-d 00:00:00', strtotime($p->date_bought)))->select('XMR')->first()->XMR) * Multiplier::Where('currency', $currency)->select('price')->first()->price;
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

      foreach($c_investments as $c)
      {
        if($c->sold_at == null){
          if($c->market == "EUR")
          {
            if($currency == "USD")
            {
              $paid += (($c->amount * $c->bought_at) / $c->btc_price_bought_eur) * $c->btc_price_bought_usd;
            } elseif($currency == "BTC")
            {
              $paid += (($c->amount * $c->bought_at) / $c->btc_price_bought_eur);
            } else
            {
              $paid += ((($c->amount * $c->bought_at) / $c->btc_price_bought_eur) * $c->btc_price_bought_usd) * Multiplier::Where('currency', $currency)->select('price')->first()->price;
            }
          } elseif($c->market == "USD")
          {
            if($currency == "USD")
            {
              $paid += (($c->amount * $c->bought_at) / $c->btc_price_bought_usd) * $c->btc_price_bought_usd;
            } elseif($currency == "BTC")
            {
              $paid += (($c->amount * $c->bought_at) / $c->btc_price_bought_usd);
            } else
            {
              $paid += ((($c->amount * $c->bought_at) / $c->btc_price_bought_usd) * $c->btc_price_bought_usd) * Multiplier::Where('currency', $currency)->select('price')->first()->price;
            }
          }
      }

      }

      return $paid;
    }

    public function getPaid2($currency)
    {
      $p_investments = PoloInvestment::where([['userid', '=', $this->id], ['date_sold', '=', null], ['amount', '>', '0']])->get();
      $b_investments = BittrexInvestment::where([['userid', '=', $this->id], ['date_sold', '=', null], ['amount', '>', '0']])->get();
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


    public function getHoldings()
    {
      $holdings = Cache::remember('myHoldings:'.$this->id, 7200, function(){
        return Holding::where('userid', $this->id)->get();
      });

      return $holdings;
    }

    public function getValue($portfolio, $currency)
    {
      $holdings = $this->getHoldings();
      $value = 0;
      $btc = $this->getBtcCache();
      $eth = $this->getEthCache();
      foreach($holdings as $holding)
      {
        if($holding->market != "ETH")
        {
          $multiplier = $btc;
        } else {
          $multiplier = $eth;
        }
        $value += $holding->getValue($this->api, $currency, $multiplier, $this->getFiat());
      }

      return $value;
    }

    public function getPortfolioInvested($portfolio, $currency)
    {
      if($this->invested == 0)
      {
        $deposits = Cache::remember('transaction_deposits:'.$this->id, 60, function()
        {
          return Transaction::where([['userid', '=', $this->id], ['type', '=', 'DEPOSIT']])->get();
        });

        $d_amount = 0;


        foreach($deposits as $deposit)
        {
          if($deposit->toggled == 1)
          {
            if($currency == "BTC")
            {
              $d_amount += ($deposit->amount * $deposit->price);
            } else {
              $d_amount += (($deposit->amount * $deposit->price) * $deposit->btc) * $this->getFiat();
            }
          }
        }
        return $d_amount;
      } else {
        return $this->invested;
      }
    }

    public function getPortfolioProfit($portfolio, $currency)
    {
      if($this->algorithm == 1)
      {
        $holdings = $this->getHoldings();
        $value = 0;
        $btc = $this->getBtcCache();
        $eth = $this->getEthCache();
        foreach($holdings as $holding)
        {
          if($holding->market != "ETH")
          {
            $multiplier = $btc;
          } else {
            $multiplier = $eth;
          }
          $value += $holding->getProfit($this->api, $currency, $multiplier, $this->getFiat());
        }
      } else {
        $value = $this->getValue($portfolio, $currency);

        $value = $value - $this->getPortfolioInvested($portfolio, $currency);
      }

      return $value;
    }


    // Function Returns price array.
    public function getHistorical($coin)
    {
      $obj = Cache::remember('historical3:'.$coin, 1440, function() use ($coin){
        $client = new Client(['http_errors' => false]);
        $res = $client->request('GET', "https://graphs.coinmarketcap.com/currencies/" . $coin);
        $code = $res->getStatusCode();
        if ($code == 200) {
            $obj = json_decode($res->getBody());

            return $obj;
        } else {
            return "false";
        }
        });

        if($obj == "false")
        {
          Cache::forget('historical:'.$coin);
        }
        return $obj;
    }

    public function returnPrice($array, $when)
    {
      $when = date('Y-m-d', strtotime($when));
      $price = array();
      foreach ($array->price_usd as $prices) {
          $date = explode("T", date('c', $prices[0] / 1000))[0];
          if ($date == $when) {
              $price["USD"] = $prices[1];
          }
      }

      if (count($price) > 0) {
          return $price;
      } else {
          return array("error" => "No price found!");
      }
    }

    public function getHoldingLogs()
    {
      $logs = Cache::remember('holdingsLogs:'.$this->id, 16000, function(){
        return HoldingLog::where([['userid', '=', $this->id]])->orderBy('date')->get();
      });

      return $logs;
    }




    public function getPortfolioHistory($days, $currency, $type)
    {
      if(Transaction::where('userid', $this->id)->count() != 0)
      {
      $history = Cache::remember('portfolioChart:'.$this->id, 1440, function() use ($days, $currency){
      $trades = Transaction::where('userid', $this->id)->orderBy('date')->get(); // Get all logs
      $fromdate = strtotime(\Carbon\Carbon::today()->subDays($days)); // Gets the last day it should get prices from
      $array = array(); // Array that handles dates
      $token = array(); // Array that handles token amounts for calculations
      $cmc_id = array(); //Array that handles coinmarketcap ids
      $pricelog = array(); //Array that handles coinmarketcap historical prices
      $paid_usd = 0;

      foreach($trades as $trade)
      {
        // This block checks if the holdinglogs date is older than the fromdate, it should set its date to the $fromdate (starting date)
        if(strtotime($trade->date) < $fromdate)
        {
          $trade->date = date('Y-m-d H:i:s', $fromdate);
        }

        // Just a variable for resetting $worth_usd for each $log
        $worth_usd = 0;
        $paid_usd = 0;
        // Reformat the date
        $date = date('Y-m-d', strtotime($trade->date));

        // Checks if this token has been set or not in the $token array, if it has been set we update its amount.
        if($trade->type == "BUY") // DONE
        {
          $new_amount = $trade->amount - $trade->fee;
          if(!isSet($token[$trade->token]))
          {
            $token[$trade->token] = array('token' => $trade->token, 'amount' => $new_amount, 'cmc_id' => $trade->token_cmc_id, 'paid' => $trade->paid_usd);
          } else {
            $token[$trade->token] = array('token' => $trade->token, 'amount' => $token[$trade->token]['amount'] + $new_amount, 'cmc_id' => $trade->token_cmc_id, 'paid' => $token[$trade->token]['paid'] + $trade->paid_usd);
          }

          // This counts as a sell of the currency.
          if($trade->deduct == "on") {

            if(!isSet($token[$trade->market]))
            {
              $token[$trade->market] = array('token' => $trade->market, 'amount' => 0 - ($trade->total), 'cmc_id' => 'Bitcoin', 'paid' => 0 - $trade->paid_usd);
            } else {

              if(($token[$trade->market]['paid'] - $trade->paid_usd) < 0.00000001)
              {
                $paid = 0;
              } else {
                $paid = ($token[$trade->market]['paid'] - $trade->paid_usd);
              }

              if(($token[$trade->market]['amount'] - ($trade->total)) < 0.00000001)
              {
                $amount = 0;
                $paid = 0;
              } else {
                $amount = ($token[$trade->market]['amount'] - ($trade->total));
              }

              $token[$trade->market] = array('token' => $trade->market, 'amount' => $amount, 'cmc_id' => 'Bitcoin', 'paid' => $paid);
            }
          }
        } elseif($trade->type == "SELL") { // DONE

          if(isSet($token[$trade->token]))
          {
            $removal = (($token[$trade->token]['paid'] / $token[$trade->token]['amount']) * (abs($trade->amount)));

            // Confirm if amount is negative set to 0
            if(($token[$trade->token]['paid'] - $removal) < 0.00000001)
            {
              $paid = 0;
            } else {
              $paid = $token[$trade->token]['paid'] - $removal;
            }

            if(($token[$trade->token]['amount'] - $trade->amount) < 0.00000001)
            {
              $amount = 0;
              $paid = 0;
            } else {
              $amount = $token[$trade->token]['amount'] - $trade->amount;
            }


            $token[$trade->token] = array('token' => $trade->token, 'amount' => $amount, 'cmc_id' => $trade->token_cmc_id, 'paid' => $paid);
          } else {
            $token[$trade->token] = array('token' => $trade->token, 'amount' => 0, 'cmc_id' => $trade->token_cmc_id, 'paid' => 0);
          }
          // This counts as a purchase of the currency.
          if($trade->fee_currency != $trade->market)
          {
            $trade->fee = 0;
          }
          if($trade->deduct == "on") {
            if(!isSet($token[$trade->market]))
            {
              $token[$trade->market] = array('token' => $trade->market, 'amount' => $trade->total - $trade->fee, 'cmc_id' => 'Bitcoin', 'paid' => $trade->paid_usd);
            } else {
              $token[$trade->market] = array('token' => $trade->market, 'amount' => $token[$trade->market]['amount'] + ($trade->total - $trade->fee), 'cmc_id' => 'Bitcoin', 'paid' => $token[$trade->market]['paid'] + $trade->paid_usd);
            }
          }
        } elseif($trade->type == "DEPOSIT") // DONE
        {
          if(!isSet($token[$trade->token]))
          {
            $token[$trade->token] = array('token' => $trade->token, 'amount' => $trade->amount, 'cmc_id' => $trade->token_cmc_id, 'paid' => $trade->paid_usd);
          } else {
            $token[$trade->token] = array('token' => $trade->token, 'amount' => $token[$trade->token]['amount'] + $trade->amount, 'cmc_id' => $trade->token_cmc_id, 'paid' => $token[$trade->token]['paid'] + $trade->paid_usd);
          }
        } elseif($trade->type == "WITHDRAW") // DONE
        {

          $removal = (($token[$trade->token]['paid'] / $token[$trade->token]['amount']) * (abs($trade->amount)));

          // Confirm if amount is negative set to 0
          if(($token[$trade->token]['paid'] - $removal) < 0.00000001)
          {
            $paid = 0;
          } else {
            $paid = $token[$trade->token]['paid'] - $removal;
          }

          if(($token[$trade->token]['amount'] - $trade->amount) < 0.00000001)
          {
            $amount = 0;
            $paid = 0;
          } else {
            $amount = $token[$trade->token]['amount'] - $trade->amount;
          }

          $token[$trade->token] = array('token' => $trade->token, 'amount' => $amount, 'cmc_id' => $trade->token_cmc_id, 'paid' => $paid);
        }

        // Since we need to recalculate using the amounts each day, we loop through the tokens in $token
        foreach($token as $key => $coin)
        {

          // This checks if we have already read in the price array for this token, if it has not been set we get it.
          if(!isSet($pricelog[$coin['cmc_id']]) && $coin['cmc_id'] != "FIAT")
          {
            $pricelog[$coin['cmc_id']] = $this->getHistorical($coin['cmc_id']);
          }

          // If the token is a fiat we set its price to 1 and then we multiply the amount * price * fiat later in the code.
          if($coin['cmc_id'] != "FIAT")
          {
            $price = $this->returnPrice($pricelog[$coin['cmc_id']], $trade->date);
          } else {
            $price = 1;
          }

          // Here we calculate the worth.
          if($coin['cmc_id'] != "FIAT")
          {
            $worth_usd += $coin['amount'] * $price['USD'];
          } else {
            if($key == "USD")
            {
              $worth_usd += $coin['amount'];
            } else {
              $worth_usd += $coin['amount'] / (1 / Multiplier::where('currency', $coin['token'])->select('price')->first()->price); // If its a fiat and its not USD we convert it to usd.
            }
          }
          $paid_usd += $coin['paid'];
        }

        // Sets the record in the array with all the needed data.
        if($date != date('Y-m-d'))
        {
          $array = array_set($array, strtotime($date), array('worth_usd' => $worth_usd, 'tokens' => $token, 'date' => $date, 'paid' => $paid_usd, 'generated' => "no"));
        }
      }

      // Sort the array by key.
      ksort($array);

      // This is the method depixel wrote to add missing dates.
      for($i = key($array); $i <= strtotime(date('Y-m-d')); $i += 86400)
      {

        if(isSet($array[$i]) == false)
        {
          // This gets the record before $i, so we can read in the latest holdings.
          $latest = null;
          foreach ($array as $key => $date) {
             if ($latest === null || $key < $i && $key > key($latest)) {
                $latest = $date;
             }
          }


          // Set worth_usd to 0.
          $worth_usd = 0;
          $paid_usd = 0;

          // Formats the $i unix time to a suitable date.
          $missingdate = date('Y-m-d', $i);

          foreach($latest['tokens'] as $key => $coin)
          {

          if(!isSet($pricelog[$coin['cmc_id']]) && $coin['cmc_id'] != "FIAT")
          {
            $pricelog[$coin['cmc_id']] = $this->getHistorical($coin['cmc_id']);
          }
          if($coin['cmc_id'] != "FIAT")
          {
            $price = $this->returnPrice($pricelog[$coin['cmc_id']], $missingdate);
          } else {
            $price = 1;
          }

          // Worth of this holding this day
          if($coin['cmc_id'] != "FIAT")
          {
            $worth_usd += $coin['amount'] * $price['USD'];
          } else {
            if($key == "USD")
            {
              $worth_usd += $coin['amount'];
            } else {
              $worth_usd += $coin['amount'] / (1 / Multiplier::where('currency', $coin['token'])->select('price')->first()->price);
            }
          }
          $paid_usd += $coin['paid'];
        }
        if($missingdate != date('Y-m-d'))
        {
          $array = array_set($array, $i, array('worth_usd' => $worth_usd, 'tokens' => $latest['tokens'], 'date' => $missingdate, 'paid' => $latest['paid'], 'generated' => 'yes'));
          ksort($array);
        }
        }
      }
      // Sort array by time
      ksort($array);
      // Print it out
      return $array;
      });


      foreach($history as $worth)
      {
        if($type == "Value")
        {
          echo $worth['worth_usd'].", ";
        } else {
          echo $worth['worth_usd'] - $worth['paid'],", ";
        }
      }
    }
    }


}
