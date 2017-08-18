<?php
namespace App;

use App\Key;
use App\Balance;
use Auth;
use Illuminate\Http\Request;

// Simple Coinbase Wrapper
class CoinBase {

  static protected $coinbase_id = "7684855d375a8aa4486c183903573f28ea098779df9c970cc4ad7b36a0ea748e";
  static protected $coinbase_secret = "2275340a8e45c49c9fe0ca589838274527c7a1c290dbd80b436730714ec7f869";

  //Grabs Access Token & Refresh Token
  public static function grabAccess(Request $request)
  {
    // Make sure user is logged in
    if(Auth::user())
    {
      $client = new \GuzzleHttp\Client();

      try {
        $res = $client->request('POST', 'https://api.coinbase.com/oauth/token', [
        'form_params' => ['grant_type' => 'authorization_code', 'code' => $request->get('code'), 'client_id' => self::$coinbase_id, 'client_secret' => self::$coinbase_secret, 'redirect_uri' => 'https://altpocket.io/coinbase/callback']]);
        $response = $res->getBody();
        $response = json_decode($response, true);
      } catch(\GuzzleHttp\Exception\RequestException $e){
        //$user->notify(new ImportComplete('No API keys found or invalid combination.', 'error'));
        event(new PushEvent('Oops.. something went wrong.', 'error', Auth::user()->id));
        die('wheow');
      }
      return $response;
    }
  }

  public static function grabNew($key)
  {
    // Make sure user is logged in
    if(Auth::user())
    {
      $client = new \GuzzleHttp\Client();

      try {
        $res = $client->request('POST', 'https://api.coinbase.com/oauth/token', [
        'form_params' => ['grant_type' => 'refresh_token', 'client_id' => self::$coinbase_id, 'client_secret' => self::$coinbase_secret, 'refresh_token' => decrypt($key->private)]]);
        $response = $res->getBody();
        $response = json_decode($response, true);
      } catch(\GuzzleHttp\Exception\RequestException $e){
        //$user->notify(new ImportComplete('No API keys found or invalid combination.', 'error'));
        event(new PushEvent('Oops.. something went wrong.', 'error', Auth::user()->id));
        die('wheow');
      }

      $key->public = encrypt($response['access_token']);
      $key->private = encrypt($response['refresh_token']);
      $key->save();
    }
  }



  // Get new token if expired
  public static function checkExpiry($key)
  {
    $expiry = $key->expiry;
    $date = $key->updated_at;
    $date = strtotime($date) + 60*120; // Add two hours to last updated
    $now = strtotime(date('Y-m-d h:i:sa'));

    // Check if it expired
    if($now > $date)
    {
      self::grabNew($key);

    } else
    {
      // Not expired, continue
    }

  }


}
