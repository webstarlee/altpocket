<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Donation;
use App\awarded;
use App\Progress;
use DB;
use Award;
use Cache;
class DonationController extends Controller
{

    //Process Donation
    public function postDonation(Request $request)
    {
        $merchantid = "c386109d2fec3ae0327d66e05f705754";
        $secret = "3091041612";

        //Stuff
        $og_currency = "USD";


        //Start validation
        $hmac_sig = $request->header('HMAC');

        if(!$request->get('ipn_mode') || $request->get('ipn_mode') != "hmac")
        {
          die('IPN Mode is not HMAC');
        }

        if(!$hmac_sig)
        {
          die('No HMAC Signature');
        }

        if(!$request->get('merchant') || $request->get('merchant') != $merchantid)
        {
          die('No or incorrect Merchant ID passed');
        }

        $requests = file_get_contents('php://input');
        if ($requests === FALSE || empty($requests)) {
          die('Failed to read post request');
        }


        $hmac = hash_hmac("sha512", $requests, trim($secret));

        if($hmac != $hmac_sig)
        {
          die('HMAC signature does not match');
        }

        if ($request->get('currency1') != $og_currency) {
            die('Original currency mismatch!');
        }

        $donation = Donation::where('tx', $request->get('txn_id'))->first();

        if(!$donation)
        {
          $donation = new Donation;
          $donation->userid = $request->get('item_number'); // User ID
          $donation->tx = $request->get('txn_id'); // Transaction ID
          $donation->currency1 = $request->get('currency1'); // Original currency from button
          $donation->currency2 = $request->get('currency2'); // Donated currency
          $donation->amount1 = $request->get('amount1'); // Amount in original currency
          $donation->amount2 = $request->get('amount2'); // Amount in donated currency
          $donation->fee = $request->get('fee'); // Fee in donated currency
          $donation->status = $request->get('status'); // Status
          $donation->save();
        } else {
          $donation = Donation::where('tx', $request->get('txn_id'))->first();
          $donation->status = $request->get('status');
          $donation->save();
        }



        if($request->get('status') >= 100 || $request->get('status') == 2 || $request->get('status') == 1)
        {
          // Give group if donation is more or equals to 5$
          if(!DB::table('user_group')->where([['user_id', '=', $request->get('item_number')], ['group_id', '=', 9]])->first())
          {
            if($request->get('amount1') >= 5)
            {
            Cache::forget('isDonator'.$request->get('item_number'));
            DB::table('user_group')->insert(['user_id' => $request->get('item_number'), 'group_id' => 9]);
            }
          }
          if(!DB::table('user_group')->where([['user_id', '=', $request->get('item_number')], ['group_id', '=', 13]])->first())
          {
            if($request->get('amount1') >= 50)
            {
            Cache::forget('isSponsor5'.$request->get('item_number'));
            DB::table('user_group')->insert(['user_id' => $request->get('item_number'), 'group_id' => 13]);
            }
          }
          if(!DB::table('user_group')->where([['user_id', '=', $request->get('item_number')], ['group_id', '=', 2]])->first())
          {
            if($request->get('amount1') >= 100)
            {
            Cache::forget('isVIP6'.$request->get('item_number'));
            DB::table('user_group')->insert(['user_id' => $request->get('item_number'), 'group_id' => 2]);
            }
          }


          //Award time
          $award = awarded::where([['userid', '=', $request->get('item_number')], ['award_id', '=', 6]])->first();

          if(!$award)
          {
            $award = new awarded;
            $award->userid = $request->get('item_number');
            $award->award_id = 6;
            $award->reason = "Thank you for your donation.";
            $award->save();
          }

          // This one handles if you have done multiple donations
          $donations = Donation::where([['userid', '=', $request->get('item_number')], ['status', '=', '100']])->get();
          $donationamount = 0;

          foreach($donations as $donation)
          {
            $donationamount += $donation->amount1;
          }

          if($donationamount >= 50)
          {
            if(!DB::table('user_group')->where([['user_id', '=', $request->get('item_number')], ['group_id', '=', 13]])->first())
            {
              if($request->get('amount1') >= 50)
              {
              Cache::forget('isSponsor5'.$request->get('item_number'));
              DB::table('user_group')->insert(['user_id' => $request->get('item_number'), 'group_id' => 13]);
              }
            }
          }

          if($donationamount >= 100)
          {
            $award = new awarded;
            $award->userid = $request->get('item_number');
            $award->award_id = 7;
            $award->reason = "Thank you so much for your amazing support.";
            $award->save();

            if(!DB::table('user_group')->where([['user_id', '=', $request->get('item_number')], ['group_id', '=', 2]])->first())
            {
              if($request->get('amount1') >= 100)
              {
              Cache::forget('isVIP6'.$request->get('item_number'));
              DB::table('user_group')->insert(['user_id' => $request->get('item_number'), 'group_id' => 2]);
              }
            }
          }
          if($request->get('status') >= 100)
          {
          $progress = Progress::first();
          $progress->USD += $request->get('amount1');
          $progress->save();
        }
        } elseif($request->get('status') < 0)
        {
          // Payment error.
        } else {
          //Payment pending...
        }




    }




}
