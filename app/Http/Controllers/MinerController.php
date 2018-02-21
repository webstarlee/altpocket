<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class MinerController extends Controller
{

  public function toggleMiner()
  {
    $user = Auth::user();

    if($user->miner == 1)
    {
      $user->miner = 0;
    } else {
      $user->miner = 1;
    }
    $user->save();

    return $user->miner;
  }

  public function addThread()
  {
    $user = Auth::user();

    $user->threads += 1;
    $user->save();

    return $user->threads;
  }

  public function removeThread()
  {
    $user = Auth::user();

    if($user->threads != 1)
    {
      $user->threads -= 1;
      $user->save();
    }

    return $user->threads;
  }

  public function refreshMiner()
  {
    $user = Auth::user();

    $client = new \GuzzleHttp\Client();
    $res = $client->request('POST', 'https://api.coin-hive.com/user/balance?secret=zHsuY070u8Byf3cZJBYrhY5zqdJRVCNk&name='.Auth::user()->username);
    $response = $res->getBody();
    $response = json_decode($response, true);

    $user->hashes = $response['balance'];
    $user->save();

    return $user->hashes;

  }

}
