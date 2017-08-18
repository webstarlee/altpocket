<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DiscordKey;
use Redirect;
use Auth;

class DiscordController extends Controller
{
    public function index()
    {
      return view('discord.index');
    }

    public function generate()
    {
      $key = new DiscordKey;
      $key->key = "ALTP-DISC-".substr(md5(microtime()),rand(0,26),9);
      $key->userid = Auth::user()->id;
      $key->serverid = "";
      $key->save();

      return Redirect::back();
    }
}
