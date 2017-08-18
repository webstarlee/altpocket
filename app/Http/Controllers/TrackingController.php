<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Balance;
use App\User;
use Auth;
use App\Key;

class TrackingController extends Controller
{


  public function updateEthermine()
  {
    $keys = Key::where('exchange', 'Ethermine')->get();
    //Track!
    foreach($keys as $key)
    {
      $userid = $key->userid;

      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'https://ethermine.org/api/miner_new/'.decrypt($key->public));
      $response = $res->getBody();
      $balance = json_decode($response, true);

      $amount = $balance['unpaid'] / 1000000000000000000;

      $balance = Balance::where([['userid', '=', $userid], ['exchange', '=', 'Ethermine']])->first();
      $balance->amount = $amount;
      $balance->save();
    }
  }

  public function updateNanopool()
  {
    $keys = Key::where('exchange', 'ETH-Nano')->get();
    //Track!
    foreach($keys as $key)
    {
      $userid = $key->userid;

      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'https://api.nanopool.org/v1/eth/balance/'.decrypt($key->public));
      $response = $res->getBody();
      $balance = json_decode($response, true);

      $amount = $balance['data'];

      $balance = Balance::where([['userid', '=', $userid], ['exchange', '=', 'Nanopool']])->first();
      $balance->amount = $amount;
      $balance->save();
    }
  }

  public function updateEthereum()
  {
    $keys = Key::where('exchange', 'Ethereum')->get();
    //Track!
    foreach($keys as $key)
    {
      $userid = $key->userid;

      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'https://api.etherscan.io/api?module=account&action=balance&address='.decrypt($key->public).'&tag=latest&apikey=A5WHVGRX2JMRIUSIS3J77CKPJ8CJ9W44HA');
      $response = $res->getBody();
      $balance = json_decode($response, true);

      $amount = $balance['result'] / 1000000000000000000;

      $balance = Balance::where([['userid', '=', $userid], ['exchange', '=', 'Ethereum']])->first();
      $balance->amount = $amount;
      $balance->save();
    }
  }

  public function updateNicehash()
  {
    $keys = Key::where('exchange', 'NiceHash')->get();
    //Track!
    foreach($keys as $key)
    {
      $userid = $key->userid;

      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'https://api.nicehash.com/api?method=balance&id='.decrypt($key->public).'&key='.decrypt($key->private));
      $response = $res->getBody();
      $balance = json_decode($response, true);

      $amount = $balance['result']['balance_confirmed'];

      $balance = Balance::where([['userid', '=', $userid], ['exchange', '=', 'NiceHash']])->first();
      $balance->amount = $amount;
      $balance->save();
    }
  }
}
