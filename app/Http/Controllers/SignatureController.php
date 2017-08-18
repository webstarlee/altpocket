<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Image;
use Alert;
use Redirect;
use Auth;
use DB;

class SignatureController extends Controller
{
    public function generateSignature($username)
    {
      $user = User::where('username', $username)->first();
      $path2 = public_path('signature/asd.png');

      $img = Image::make($path2);
      $toplayer = Image::make($path2);

      if($user->avatar != "default.jpg")
      {
        $path3 = public_path('uploads/avatars/'.$user->id.'/'.$user->avatar);
        $test = Image::make($path3)->resize(42,42);
        $img->insert($test, 'top-left', 49, 16)->height(50);
        $img->insert($toplayer, 'top-left', 0, 0);
      } else
      {
        $defaultpath = public_path('assets/img/default.png');
        $test = Image::make($defaultpath)->resize(42,42);
        $img->insert($test, 'top-left', 49, 16)->height(50);
        $img->insert($toplayer, 'top-left', 0, 0);
      }

      $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;




      $img->text($user->username, 165, 30, function($font) {
          $path4 = public_path('signature/arial.ttf');
          $font->file($path4);
          $font->size(14);
          $font->color('#000000');
          $font->align('center');
          $font->valign('top');
      });

      $img->text('$'.number_format($user->getInvested('USD'), 2), 100, 102, function($font) {
        $path5 = public_path('signature/bold.ttf');
          $font->file($path5);
          $font->size(14);
          $font->color('#73c04d');
          $font->align('center');
          $font->valign('top');
      });

      $img->text('$'.number_format((($user->getNetWorthNew('coinmarketcap') * $multiplier) - $user->getInvested('USD')), 2), 200, 102, function($font) {
          $path5 = public_path('signature/bold.ttf');
          $font->file($path5);
          $font->size(14);
          $font->color('#73c04d');
          $font->align('center');
          $font->valign('top');
      });

      $img->text('$'.number_format($user->getNetWorthNew('coinmarketcap') * $multiplier, 2), 455, 102, function($font) {
          $path5 = public_path('signature/bold.ttf');
          $font->file($path5);
          $font->size(14);
          $font->color('#73c04d');
          $font->align('center');
          $font->valign('top');
      });

      $img->text($user->impressed, 555, 102, function($font) {
          $path5 = public_path('signature/bold.ttf');
          $font->file($path5);
          $font->size(14);
          $font->color('#73c04d');
          $font->align('center');
          $font->valign('top');
      });


      $path = public_path('uploads/signatures/'.$user->id.'/'.$user->username.'.png');
      $name = $user->username.'.png';
      $img->save(public_path('uploads/signatures/'.$name));


    }
    public function toggleWidget()
    {
      $user = Auth::user();
      if($user->widget == "off")
      {
        $user->widget = "on";
        $user->save();
        SignatureController::generateSignature($user->username);
        Alert::success('You have now toggled the Altpocket.io widget.', 'Widget toggled');
        return Redirect::back();
      } else {
        $user->widget = "off";
        $user->save();
        Alert::success('You have now toggled off the Altpocket.io widget.', 'Widget toggled');
        return Redirect::back();
      }
    }


    public function updateSignature()
    {
      $users = User::where('widget', 'on')->get();

      foreach($users as $user)
      {
        SignatureController::generateSignature($user->username);
      }
    }

}
