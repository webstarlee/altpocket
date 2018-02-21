<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;


class UserOnlineController extends Controller
{
  public function __invoke(User $user)
  {
      $expiresAt = \Carbon\Carbon::now()->addMinutes(5);
      Cache::put('user-is-online-' . $user->id, true, $expiresAt);
      
      broadcast(new UserOnline($user));
  }
}
