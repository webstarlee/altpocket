<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Auth;
use App\Events\MessageEvent;
use App\Events\PushEvent;
use App\Message;
use App\Block;
use App\User;
Use DB;

class ChatController extends Controller
{
  public function index()
  {
    return view('chat.index');
  }

  public function sendMessage(Request $request)
  {
    $to = $request->get('to');
    $message = $request->get('message');

    //Parse message
    $message = $this->parseMessage($message);


    if($to != Auth::user()->id && !Block::where([['userid', '=', $to], ['blocked', '=', Auth::user()->id]])->exists())
    {
      // Sending events
      event(new MessageEvent($message, Auth::user()->id, $to, Auth::user()->avatar));
      // Log in DB
      $log = new Message;
      $log->sender = Auth::user()->id;
      $log->receiver = $to;
      $log->message = $message;
      $log->save();
    } else {
      echo "blocked";
    }
  }

  public function blockUser($id)
  {
    $block = Block::where([['userid', '=', Auth::user()->id], ['blocked', '=', $id]])->first();

    if($block)
    {
      $block->delete();

      event(new PushEvent('User was successfully unblocked from messaging you.', 'success', Auth::user()->id));
    } else {
      $block = new Block;
      $block->userid = Auth::user()->id;
      $block->blocked = $id;
      $block->save();

      event(new PushEvent('User was successfully blocked from messaging you.', 'success', Auth::user()->id));
    }
  }

  public function getMessages($user1, $user2)
  {
    $myid = Auth::user()->id;
    if($user1 == $myid || $user2 = $myid)
    {
      $messages["messages"] = Message::where([['sender', '=', $user1], ['receiver', '=', $user2]])->orWhere([['sender', '=', $user2], ['receiver', '=', $user1]])->orderBy('created_at', 'desc')->paginate(10);




      // Send along other users avatar
      if($user1 != $myid)
      {
        $sender = User::where('id', $user1)->select('avatar')->first();
        $messages["avatar"] = $sender->avatar;
      } else {

        $sender = User::where('id', $user2)->select('avatar')->first();
        $messages["avatar"] = $sender->avatar;
      }

      return json_encode($messages);
    }
  }


  public function parseMessage($message)
  {
    // This will check the message for different commands and what not.


    /* Check for ::COIN:: command */

    preg_match_all("/\::[^::]*\::/", $message, $matches);

    foreach($matches[0] as $key => $match)
    {
      $brackets = array('::');
      $coin = str_replace($brackets, '', $match);
      $coin = strtoupper($coin);
      if(DB::table('cryptos')->where('symbol', $coin)->exists())
      {
        $crypto = DB::table('cryptos')->where('symbol', $coin)->first();
        $message = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" data-placement="top" data-toggle="tooltip" title="Coin: '.$crypto->name.' | Price: $'.number_format($crypto->price_usd, 8).' | 24H: '.$crypto->percent_change_24h.'%" style="width:20px;cursor:pointer;"/>', $message);
      } else {

      }
    }

    return $message;

  }
}
