<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Notifications\NewFriendRequest;
use Alert;
use Redirect;
class FriendController extends Controller
{

    // Sends Friend Request
    public function beFriend($username)
    {
      $user = User::where('username', $username)->first();
      $me = Auth::user();

      if(!$me->isFriendWith($user))
      {
        if(!$user->hasFriendRequestFrom($me) && !$me->hasFriendRequestFrom($user))
        {
          if(!$user->hasBlocked($me))
          {
            $me->beFriend($user);

            if($me->avatar == "default.jpg")
            {
              $avatar = 'https://altpocket.io/assets/img/default.png';
            } else {
              $avatar = 'https://altpocket.io/uploads/avatars/'.$me->id.'/'.$me->avatar;
            }


            //Notification array
            $notification = [
                'icon' => 'fa fa-user',
                'title' => 'New Friend Request',
                'data' => $me->username.' has sent you a friend request!',
                'type' => 'follower',
                'username' => $me->username,
                'avatar' => $avatar
            ];

            $user->notify(new NewFriendRequest($notification));
            return "You sent a friend request to ".$user->username."!";
          } else {
            return "You are blocked by ".$user->username.".";
          }
        } else {
          return "You already have a pending friend request with ".$user->username.".";
        }
      } else {
        return "You are already a friend with ".$user->username.".";
      }
    }

    public function accept($username)
    {
      $user = User::where('username', $username)->first();

      Auth::user()->acceptFriendRequest($user);

      Alert::success('You and ' . $user->username . ' are now friends!');
      return redirect::back();
    }
}
