<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Cookie;
use Cache;
use Illuminate\Http\Request;
use App\Notifications\Welcome;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:12|unique:users|alpha_dash',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

        $referred_by = Cookie::get('referral');
        Cache::forget($referred_by);
        Cache::forget('userCount');
        Cache::forget('userCountToday');

        $user = User::create([
            'username' => $data['username'],
            'name' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'affiliate_id' => str_random(10),
            'referred_by'   => $referred_by,
            'reg_ip' => geoip()->getClientIP()
        ]);

        $user->notify(new Welcome($user));
        return $user;
    }
}
