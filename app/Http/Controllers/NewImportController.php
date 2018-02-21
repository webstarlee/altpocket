<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exchanges\HitBTC;
use App\Exchanges\Bitfinex;
use App\User;
use App\Key;
class NewImportController extends Controller
{
  public function importTradesHitBTC($userid)
  {
    $user = User::where('id', $userid)->first();
    $key = Key::where([['userid', '=', $user->id], ['exchange', '=', 'HitBTC']])->first();
    $apikey = decrypt($key->public);
    $apisecret = decrypt($key->private);


    $hit = new BitFinex('UesX2R7y5uYWecIzvduetWqpavGZv6vWzsJvG7fS0fu', '9ZjUQlIae6uxucvGC9GmE1vzJlIM6Q21109AJiHVwtC');


    var_dump($hit->get_history('LTC', '*'));



  }
}
