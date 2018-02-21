<?php
namespace App;

use App\Historical;
use App\Crypto;
use Auth;
use Illuminate\Http\Request;
use Cache;

// History Wrapper
class History {

  public static function getHistorical($date)
  {
    $date = strtotime($date);
    $datum = date('Y-m-d 00:00:00', $date);


    if(date('Y-m-d') != date('Y-m-d', $date))

    {

    $historical = Cache::remember('history-'.$datum, 1, function() use ($datum)
    {
      return Historical::where([['currency', '=', 'BTC'], ['created_at', '=', $datum]])->first();
    });


    if($historical === null)
    {
      $crypto = Crypto::where('symbol', 'BTC')->first();
      $historical = new Historical;
      $historical->USD = $crypto->price_usd;
      $historical->ETH = $crypto->price_eth;
    }

  } else {

    $crypto = Crypto::where('symbol', 'BTC')->first();
    $historical = new Historical;
    $historical->USD = $crypto->price_usd;
    $historical->ETH = $crypto->price_eth;

  }

    return $historical;
  }


}
