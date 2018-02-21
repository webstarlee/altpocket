<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Crypto;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Cache;

class Watchlist extends Model
{
  public function getData()
  {
    return Crypto::where('symbol', $this->token)->selectRaw('price_usd, market_cap_usd, rank, percent_change_24h')->first();
  }

  public function getPriceHistory($coin, $since)
  {
    $obj = Cache::remember('historical2:'.$coin, 1440, function() use ($coin){
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

    $list = array();
    $x=0;
    foreach ($obj->price_usd as $prices) {
        $date = explode("T", date('c', $prices[0] / 1000))[0];
        if (strtotime($date) >= strtotime($since)) {
            $list[$x] = $prices[1];
            $x++;
        }
    }
    return $list;

  }

}
