<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Alert;
use Redirect;
use Overtrue\LaravelFollow\Traits\CanFollow;
use App\Notifications\NewFollower;

class FollowController extends Controller
{
    /* Following */

    public function followUser($username)
    {
        if(Auth::user())
        {
            $user = User::where('username', $username)->first();
            Auth::user()->follow($user);
            Alert::success('You are now following '.$username.'!', 'Follow successfull');

            if(Auth::user()->avatar == "default.jpg")
            {
              $avatar = 'https://altpocket.io/assets/img/default.png';
            } else {
              $avatar = 'https://altpocket.io/uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar;
            }

            //Notification array
            $notification = [
                'icon' => 'fa fa-user',
                'title' => 'New Follower',
                'data' => Auth::user()->username.' has started to follow you!',
                'type' => 'follower',
                'username' => Auth::user()->username,
                'avatar' => $avatar
            ];

            $user->notify(new NewFollower($notification));

            return Redirect::back();
        } else {
            return redirect('/user/'.$username);
        }
    }
    public function unfollowUser($username)
    {
        if(Auth::user())
        {
            $user = User::where('username', $username)->first();

            Auth::user()->unfollow($user);
            Alert::success('You have unfollowed '.$username.'.', 'Unfollow successfull');
            return Redirect::back();
        } else {
            return redirect('/user/'.$username);
        }
    }


}
