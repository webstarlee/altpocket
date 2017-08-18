<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Cache;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Requests\ValidateSecretRequest;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */


//2fa



    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    public function username()
{
        return 'username';
}

/**
 *
 * @return \Illuminate\Http\Response
 */
public function getValidateToken()
{
    if (session('2fa:user:id')) {
        return view('2fa/validate');
    }

    return redirect('login');
}

/**
 *
 * @param  App\Http\Requests\ValidateSecretRequest $request
 * @return \Illuminate\Http\Response
 */
public function postValidateToken(ValidateSecretRequest $request)
{
    //get user id and create cache key
    $userId = $request->session()->pull('2fa:user:id');
    $key    = $userId . ':' . $request->totp;

    //use cache to store token to blacklist
    Cache::add($key, true, 4);

    //login and redirect user
    Auth::loginUsingId($userId);

    return redirect()->intended($this->redirectTo);
}



public function login(Request $request)
{
    $this->validate($request, [
        'username'    => 'required',
        'password' => 'required',
    ]);

    $login_type = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL )
        ? 'email'
        : 'username';

    $request->merge([
        $login_type => $request->input('username')
    ]);

    $remember = 0;
    if(!$request->has('remember'))
    {
      $remember = 0;
    } else {
      $remember = 1;
    }

    if (Auth::attempt($request->only($login_type, 'password'), $remember)) {
        $user = Auth::user();
      if ($user->google2fa_secret) {
          Auth::logout();

          $request->session()->put('2fa:user:id', $user->id);

          return redirect('2fa/validate');
      }
        return redirect()->intended($this->redirectPath());
    }

    return redirect()->back()
        ->withInput()
        ->withErrors([
            'login' => 'These credentials do not match our records.',
        ]);
    }
}
