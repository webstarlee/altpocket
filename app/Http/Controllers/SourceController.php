<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Key;
use App\Events\PushEvent;
use App\CoinBase;
use Redirect;
use Alert;
use Cache;
class SourceController extends Controller
{
    //Some good stuff
    protected $coinbase_id = "7684855d375a8aa4486c183903573f28ea098779df9c970cc4ad7b36a0ea748e";
    protected $coinbase_secret = "2275340a8e45c49c9fe0ca589838274527c7a1c290dbd80b436730714ec7f869";

    // Coinbase callback on connection
    public function coinbaseCallback(Request $request)
    {
      if(Auth::user())
      {
        //User is logged in let's get tokens
        $client = new \GuzzleHttp\Client();

        $response = CoinBase::grabAccess($request);

        $access_token = encrypt($response['access_token']);
        $refresh_token = encrypt($response['refresh_token']);

        //Add to sources

        if(!Key::where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Coinbase']])->exists())
        {
          $key = new Key;
          $key->userid = Auth::user()->id;
          $key->public = $access_token;
          $key->private = $refresh_token;
          $key->exchange = "Coinbase";
          $key->type = "Exchange";
          $key->expiry = $response['expires_in'];
          $key->save();
          Cache::forget('hasCoinbaseKey2:'.Auth::user()->id);

          Alert::success('You have successfully connected Coinbase to your account.', 'Coinbase connected');
          return redirect('/investments')->with('import', ['true']);
        } else {
          Alert::warning('You already have Coinbase connected to your account.', 'Coinbase already connected');
          return redirect('/investments')->with('import', ['true']);
        }

      } else {
        return redirect('/');
      }
    }

}
