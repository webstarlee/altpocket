<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Response;
use Vinkla\Pusher\Facades\Pusher;

class PusherController extends Controller
{

  public function __construct()
  {
  }

  public function auth(Request $request)
  {
    return $request->user();
  }


  public function postAuth(Request $request)
  {
      $uid = str_replace('private-App.User.', '', $request->get('channel_name'));
      if($uid == Auth::user()->id)
      {
      echo Pusher::socket_auth($request->get('channel_name'), $request->get('socket_id'));
    } else
    {
      return Response::make('Forbidden',403);
    }
  }
}
