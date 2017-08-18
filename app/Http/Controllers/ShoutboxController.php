<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Shoutbox;
use Cache;
use App\User;
use Validator;
use Carbon\Carbon;
use Auth;

class ShoutboxController extends Controller
{

  public function send(Request $request)
  {

    $checkSendRate = Shoutbox::where('user', \Auth::user()->id)->where('created_at', '>=', Carbon::now()->subSeconds(5))->first();

    if($checkSendRate)
    {
      return 'Please do not spam.';
    }

    $validator = Validator::make($request->all(), [
      'message' => 'required|max:255',
    ]);

    if ($validator->fails()) {
      return 'There was an error with your input';
    }

    if($request->ajax())
    {

      preg_match_all('/(@\w+)/', $request->message, $mentions);

    $mentionIDs = [];

      foreach($mentions[0] as $mention)
      {

        $findUser = User::where('username', 'LIKE', '%' . str_replace('@', '', $mention) . '%')->first();

        if(!empty($findUser->id))
        {
          $mentionIDs[] = $findUser['id'];
      }
      }

      $mentions = implode(',', $mentionIDs);
    if(count($mentions) > 0)
    {
      $insertMessage = Shoutbox::create(['user' => \Auth::user()->id, 'message' => $request->message, 'mentions' => $mentions]);
    }
    else {
        $insertMessage = Shoutbox::create(['user' => \Auth::user()->id, 'message' => $request->message]);
      }

    $data = '<li class="list-group-item">
    <div class="profile-avatar tiny pull-left" style="background-image: url(https://altpocket.io/uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar.')"></div>
            <h5 class="list-group-item-heading"><a href="#">' . \Auth::user()->username . '</a></h5>
            <p class="message-content"><time>' . date("H:i", time()) . '</time>' . e($request->message) . '</p>
          </li>';

    Cache::forget('shoutbox_messages');

    return \Response::json(['success' => true, 'data' => $data]);

  }
  }

  public function fetch(Request $request)
  {
    if($request->ajax())
    {
      //$getData = Shoutbox::orderBy('created_at', 'desc')->take(25)->get();
    $getData = Cache::remember('shoutbox_messages', 60, function()
    {
        return Shoutbox::orderBy('created_at', 'desc')->take(25)->get();
    });

      $getData = $getData->reverse();

      $data = [];
      foreach($getData as $messages)
      {

       $class = '';
        if(in_array(\Auth::user()->id, explode(',', $messages->mentions)))
        {
          $class = 'mentioned';
        }
        $user = User::where('username', $messages->poster->username)->first();

        $data[] = '<li class="list-group-item ' . $class . '">
            <div class="profile-avatar tiny pull-left" style="background-image: url(https://altpocket.io/uploads/avatars/'.$user->id.'/'.$user->avatar.')"></div>
            <h5 class="list-group-item-heading"><a class="'.$user->groupName().'" href="https://altpocket.io/user/'.$messages->poster->username.'">' . e($messages->poster->username) . '</a></h5>
            <p class="message-content"><time>' . date("H:i", strtotime($messages->created_at)) . '</time>' . e($messages->message) . '</p>
          </li>';
      }

      return \Response::json(['success' => true, 'data' => $data]);
    }
  }

}
