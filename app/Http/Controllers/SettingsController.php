<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Slim;
use App\User;
use Auth;
use Redirect;
use Alert;
use File;
use Validator;
use Cache;
class SettingsController extends Controller
{
    // View Settings page
    public function index()
    {
      return view('settings.index', ['user' => Auth::user()]);
    }

    // Update/Change headers
    public function changeHeader(Request $request)
    {
      // If a header already exists, delete it so we don't take up too much space on our tiny server.
       if (Auth::user()->header != "default") {
           $path = 'uploads/headers/'.Auth::user()->id."/";
           $lastpath = Auth::user()->header;
           File::Delete( $path . $lastpath );
       }

          $rules = [
              'file' => 'image',
              'slim[]' => 'image'
              ];

          $validator = Validator::make($request->all(), $rules);
          $errors = $validator->errors();

          if($validator->fails()){
              Alert::error('You are not allowed to upload anything but an image.', 'Upload failed');
              return Redirect::back();
          }



      // Get posted data
      $images = Slim::getImages();

      // No image found under the supplied input name
      if ($images == false) {

          // inject your own auto crop or fallback script here
          echo '<p>Slim was not used to upload these images.</p>';

      }
      else {
          foreach ($images as $image) {

              $files = array();

              // save output data if set
              if (isset($image['output']['data'])) {

                  // Save the file
                  $name = $image['input']['name'];

                  // We'll use the output crop data
                  $data = $image['output']['data'];

                  // If you want to store the file in another directory pass the directory name as the third parameter.
                  // $file = Slim::saveFile($data, $name, 'my-directory/');

                  // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                  // $file = Slim::saveFile($data, $name, 'tmp/', false);
                  $output = Slim::saveFile($data, $name, 'uploads/headers/'.Auth::user()->id."/", false);
                  $user = Auth::user();
                  $user->header = $name;
                  $user->save();
                  Alert::success('Your header has been updated.', 'Header updated');
                  return Redirect::back();
                  array_push($files, $output);
              }

              // save input data if set
              if (isset ($image['input']['data'])) {

                  // Save the file
                  $name = $image['input']['name'];

                  // We'll use the output crop data
                  $data = $image['input']['data'];

                  // If you want to store the file in another directory pass the directory name as the third parameter.
                  // $file = Slim::saveFile($data, $name, 'my-directory/');

                  // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                  // $file = Slim::saveFile($data, $name, 'tmp/', false);
                  $input = Slim::saveFile($data, $name, 'uploads/headers/'.Auth::user()->id."/", false);
                  $user = Auth::user();
                  $user->header = $name;
                  $user->save();
                  Alert::success('Your header has been updated.', 'Header updated');
                  return Redirect::back();
                  array_push($files, $input);
              }


          }
      }

    }

    // Update/change Avatar
    public function changeAvatar(Request $request){
        Cache::forget('userData:'.Auth::user()->id);
        // Ifall det redan finns en profilbild, ta bort den.
         if (Auth::user()->avatar != "default.jpg") {
             $path = 'uploads/avatars/'.Auth::user()->id."/";
             $lastpath = Auth::user()->avatar;
             File::Delete( $path . $lastpath );
         }

            $rules = [
                'file' => 'image',
                'slim[]' => 'image'
                ];

            $validator = Validator::make($request->all(), $rules);
            $errors = $validator->errors();

            if($validator->fails()){
                Alert::error('You are not allowed to upload anything but an image.', 'Upload failed');
                return Redirect::back();
            }





        // Get posted data
        $images = Slim::getImages();

        // No image found under the supplied input name
        if ($images == false) {

            // inject your own auto crop or fallback script here
            Alert::error('You did not select an avatar to upload.', 'Failed.');
            return Redirect::back();

        }
        else {
            foreach ($images as $image) {

                $files = array();

                // save output data if set
                if (isset($image['output']['data'])) {

                    // Save the file
                    $name = $image['input']['name'];

                    // We'll use the output crop data
                    $data = $image['output']['data'];

                    // If you want to store the file in another directory pass the directory name as the third parameter.
                    // $file = Slim::saveFile($data, $name, 'my-directory/');

                    // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                    // $file = Slim::saveFile($data, $name, 'tmp/', false);
                    $output = Slim::saveFile($data, $name, 'uploads/avatars/'.Auth::user()->id."/", false);
                    $user = Auth::user();
                    $user->avatar = $name;
                    $user->save();
                    Alert::success('Your avatar has been updated.', 'Avatar updated');
                    return Redirect::back();
                    array_push($files, $output);
                }

                // save input data if set
                if (isset ($image['input']['data'])) {

                    // Save the file
                    $name = $image['input']['name'];

                    // We'll use the output crop data
                    $data = $image['input']['data'];

                    // If you want to store the file in another directory pass the directory name as the third parameter.
                    // $file = Slim::saveFile($data, $name, 'my-directory/');

                    // If you want to prevent Slim from adding a unique id to the file name add false as the fourth parameter.
                    // $file = Slim::saveFile($data, $name, 'tmp/', false);
                    $input = Slim::saveFile($data, $name, 'uploads/avatars/'.Auth::user()->id."/", false);
                    $user = Auth::user();
                    $user->avatar = $name;
                    $user->save();
                    Alert::success('Your avatar has been updated.', 'Avatar updated');
                    return Redirect::back();
                    array_push($files, $input);
                }


            }
        }

    }

    // Update/change Password
    public function changePassword(Request $request){
        $user = Auth::user();
        $oldpwd = $request->get('currentpwd');
        $newpwd = $request->get('newpwd');
        $cnewpwd = $request->get('cnewpwd');

        if(Hash::check($oldpwd, Auth::user()->getAuthPassword())){
            if($newpwd == $cnewpwd){
                $user->password = Hash::make($newpwd);
                $user->save();

                Alert::success('Your password was successfully changed.', 'Success');
                return Redirect::back();
            } else {
                Alert::warning('You need to have the same password in the fields "New Password" and "Confirm New Password"!', 'Failed');
                return Redirect::back();
            }
        } else {
                Alert::error('Your current password was wrong!', 'Failed');
                return Redirect::back();
        }


    }

    // Update/change Information
    public function changeInfo(Request $request) {
      $user = Auth::user();
      Cache::forget('userData:'.Auth::user()->id);
      // Username
      if($request->get('username') != $user->username){

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:12|unique:users|alpha_dash'
        ]);
        $errors = $validator->errors();


        if ($validator->fails()) {
            if($errors->first('username')){
              Alert::error($errors->first('username'), 'Failed');
              return Redirect::back();
            }
        }

        if(User::where('username', $request->get('username'))->first()){
          if(User::where('username', $request->get('username'))->first() == $user){
            $user->username = $request->get('username');
          } else {
            Alert::error('The username you entered is already in use.', 'Failed');
            return redirect('/user/'.$username);
          }

        } else {
            $user->username = $request->get('username');
        }

      }

      // Email
      if($request->get('email') != $user->email){
        if(User::where('email', $request->get('email'))->first()){
          if(User::where('email', $request->get('email'))->first() == $user){
            $user->email = $request->get('email');
          } else {
            Alert::error('The email you entered is already in use.', 'Failed');
                  return Redirect::back();
          }

        } else {
            $user->email = $request->get('email');
        }

      }

      // Rest of stuff
      $user->bio = $request->get('bio');
      $user->twitter = $request->get('twitter');
      $user->youtube = $request->get('youtube');
      $user->facebook = $request->get('facebook');
      //$user->name = $request->get('displayname');

      if($request->get('public')){
      $user->public = 'on';
      } else {
      $user->public = 'off';
      }
      $user->save();

      Alert::success('Your profile has been updated.', 'Sucesss');
      return Redirect::back();

    }

    public function changeSettings(Request $request) {
      $user = Auth::user();

      //theme
      $theme = $request->get('theme');

      if($theme == "Day")
      {
        $user->theme = "normal";
      } elseif($theme == "Night") {
        $user->theme = "dark";
      } else {
        $user->theme = "normal";
      }

      //Currency
      Cache::forget('userFiat:'.Auth::user()->id);
      $currency = $request->get('currency');
      $user->currency = $currency;

      //Price api
      $api = $request->get('api');
      $user->api = $api;

      //Email Notifications
      if($request->get('email-notifications'))
      {
        $user->email_notifications = "on";
      } else {
        $user->email_notifications = "off";
      }

      //summed
      if($request->get('condensed-investments'))
      {
        $user->summed = "1";
      } else {
        $user->summed = "0";
      }

      //tableview
      if($request->get('selltobalance'))
      {
        $user->selltobalance = "1";
      } else {
        $user->selltobalance = "0";
      }

      //tableview
      if($request->get('oldinvestments'))
      {
        $user->oldinvestments = "1";
      } else {
        $user->oldinvestments = "0";
      }

      //tableview
      if($request->get('selltoinvestment'))
      {
        $user->selltoinvestment = "1";
      } else {
        $user->selltoinvestment = "0";
      }

      //tableview
      if($request->get('addfrombalance'))
      {
        $user->addfrombalance = "1";
      } else {
        $user->addfrombalance = "0";
      }

      //sell to balance
      if($request->get('table-view'))
      {
        $user->tableview = "1";
      } else {
        $user->tableview = "0";
      }


      //algo
      //tableview
      if($request->get('algorithm'))
      {
        $user->algorithm = "2";
      } else {
        $user->algorithm = "1";
      }

      $user->save();
      Alert::success('Your settings has been updated.', 'Sucesss');
      return Redirect::back();
    }




}
