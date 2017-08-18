<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Alert;
use Redirect;
use Overtrue\LaravelFollow\Traits\CanFollow;

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
