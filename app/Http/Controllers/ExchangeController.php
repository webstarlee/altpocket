<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Key;
use Auth;
use Redirect;
use Cache;

class ExchangeController extends Controller
{
    public function addExchange(Request $request)
    {
      $key = Key::where([['userid', '=', Auth::user()->id], ['exchange', '=', $request->get('exchange')]])->first();
      Cache::forget('hasPoloKey2:'.Auth::user()->id);
      Cache::forget('hasBittrexKey2:'.Auth::user()->id);

      if($key){
        $key->public = encrypt($request->get('public'));
        $key->private = encrypt($request->get('private'));
        $key->exchange = $request->get('exchange');
        $key->type = "Exchange";
        $key->expiry = 0;
        $key->save();
      } else {
        $key = new Key;
        $key->userid = Auth::user()->id;
        $key->public = encrypt($request->get('public'));
        $key->private = encrypt($request->get('private'));
        $key->exchange = $request->get('exchange');
        $key->type = "Exchange";
        $key->expiry = 0;
        $key->save();
       }

       return Redirect::back();
    }


    public function deleteExchange($exchange)
    {
      $key = Key::where([['userid', '=', Auth::user()->id], ['exchange', '=', $exchange]])->first();
      Cache::forget('hasPoloKey2:'.Auth::user()->id);
      Cache::forget('hasBittrexKey2:'.Auth::user()->id);
      if($key)
      {
        $key->delete();
      }
      return Redirect::back();
    }

}
