<?php
namespace App\Http\Controllers;
use Crypt;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use \ParagonIE\ConstantTime\Base32;
use Auth;
use App\Notifications\Enable2FA;
use App\Notifications\Disable2FA;
use Redirect;
use Alert;
use App\Http\Requests\ValidateSecretRequest;


class Google2FAController extends Controller
{
    use ValidatesRequests;
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('web');
    }
    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function enableTwoFactor(Request $request)
    {
        $google2fa = new Google2FA();

        $user = $request->user();
        $secret = $google2fa->generateSecretKey();
        //encrypt and then save secret


        $user->save();
        //generate image for QR barcode
        $google2fa_url = $google2fa->getQRCodeGoogleUrl(
            'Altpocket.io',
            $user->email,
            $secret
        );

        return view('2fa/enableTwoFactor', ['image' => $google2fa_url,
            'secret' => $secret]);
    }
    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function disableTwoFactor(Request $request)
    {
        $user = Auth::user();

        return view('2fa/disableTwoFactor', ['secret' => Crypt::decrypt($user->google2fa_secret)]);
    }






    /**
     * Generate a secret key in Base32 format
     *
     * @return string
     */
    private function generateSecret()
    {
        $randomBytes = random_bytes(10);
        return Base32::encodeUpper($randomBytes);
    }

    public function requestTwoFactor()
    {
      $notifiable = Auth::user();
      $notifiable->notify(new Enable2FA());

      Alert::success('We have sent you an email where you can enable 2FA.', 'Email sent');
      return Redirect::back();
    }

    public function requestTwoFactorDisable()
    {
      $notifiable = Auth::user();
      $notifiable->notify(new Disable2FA());

      Alert::success('We have sent you an email where you can disable 2FA.', 'Email sent');
      return Redirect::back();
    }

    public function activateTwoFactor(Request $request)
    {
      $user = Auth::user();
      $secret = $request->get('secret');
      $totp = $request->get('totp');
      if($secret)
      {
        $google2fa = new Google2FA();
        if($google2fa->verifyKey($secret, $totp))
        {
          $user->google2fa_secret = Crypt::encrypt($secret);
          $user->save();
          Alert::success('Two factor authentication has successfully been enabled on your account.', '2FA Enabled');
          return redirect('/home');
        } else {
          Alert::error('You entered an invalid 2FA code.', 'Failed');
          return Redirect::back();
        }
      } else {
        Alert::error('Something went wrong, please report to staff.', 'Failed');
        return Redirect::back();
      }
    }

    public function deactivateTwoFactor(Request $request)
    {
      $user = Auth::user();
      $secret = $request->get('secret');
      $totp = $request->get('totp');
      if($secret)
      {
        $google2fa = new Google2FA();
        if($google2fa->verifyKey($secret, $totp))
        {
          $user->google2fa_secret = null;
          $user->save();
          Alert::success('Two factor authentication has successfully been disabled.', '2FA Disabled');
          return redirect('/home');
        } else {
          Alert::error('You entered an invalid 2FA code.', 'Failed');
          return Redirect::back();
        }
      } else {
        Alert::error('Something went wrong, please report to staff.', 'Failed');
        return Redirect::back();
      }


    }

}
