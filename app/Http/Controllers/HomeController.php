<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Investment;
use App\Crypto;
use App\bittrex;
use App\Polo;
use App\Trade;
use App\Exchange;
use App\User;
use App\Impressed;
use App\Shoutbox;
use App\Bug;
use App\Slim;
use App\Stat;
use App\Comment;
use App\PoloInvestment;
use App\BittrexInvestment;
use App\ManualInvestment;
use App\Status;
use App\Balance;
use App\Tracking;
use Auth;
use File;
use Redirect;
use Alert;
use Hash;
use Cache;
use Validator;
use DB;
use GuzzleHttp\Exception\ClientException;
use Yajra\Datatables\Datatables;
use App\Jobs\UpdateProfit;
use App\DiscordKey;



class HomeController extends Controller
{

    public function changePassword(Request $request){
        $user = Auth::user();
        $oldpwd = $request->get('currentpwd');
        $newpwd = $request->get('newpwd');
        $cnewpwd = $request->get('cnewpwd');

        if(Hash::check($oldpwd, Auth::user()->getAuthPassword())){
            if($newpwd == $cnewpwd){
                $user->password = Hash::make($newpwd);
                $user->save();

                Alert::success('Your password was successfully changed.', 'Password changed');
                return redirect('/user/'.$user->username);
            } else {
                Alert::success('You need to have the same password in the fields "New Password" and "Confirm New Password"!', 'Password change failed');
                return redirect('/user/'.$user->username);
            }
        } else {
                Alert::success('Your current password was wrong!', 'Password change failed');
                return redirect('/user/'.$user->username);
        }


    }

    public function reportBug(Request $request){
        $bug = new Bug;
        $bug->username = Auth::user()->username;
        $bug->type = $request->get('type');
        $bug->explanation = $request->get('comment');
        $bug->save();

        Alert::success('Your bug has been successfully reported and will be smashed shortly!', 'Bug Reported');
        return Redirect::back();
    }

    public function changeTheme(){
        $user = Auth::user();

        if($user->theme == "normal"){
            $user->theme = "dark";
        } else {
            $user->theme = "normal";
        }

        $user->save();

        Alert::success('Your theme was successfully changed.', 'Theme changed');
        return Redirect::back();
    }

    public function changeCurrency($currency){
      if(Auth::user()){
          $user = Auth::user();
          $user->currency = $currency;
          $user->save();

        Alert::success('Your currency was successfully changed.', 'Currency changed');
        return Redirect::back();
        } else {
            return redirect('/');
        }

    }

    public function updateProfit(){



       dispatch(new UpdateProfit());



    }

    public function calcInvested(){
        $user = Auth::user();
        $user->invested = 0;
        $investments = Investment::where([['userid', '=', $user->id], ['sold_at', '=', null]])->get();

        foreach($investments as $investment){
            $user->invested += $investment->usd_total;
        }
        $user->save();
    }

    public function allTimeSpent($id){
        $investments = Investment::where([['userid', $id]])->get();
        $user = User::where('id', $id)->first();
        $spent = 0;
        foreach($investments as $investment){
            $spent += $investment->usd_total;
        }
        return $spent;
    }

    public function allTimeSold($id){
        $investments = Investment::where([['userid', $id]])->get();
        $user = User::where('id', $id)->first();

        $spent = 0;
        foreach($investments as $investment){
            if($investment->sold_at != null){
                $spent += $investment->sold_for;
            }
        }
        return $spent;
    }

    public function changeAPI($api){
        if(Auth::user()){
            $user = Auth::user();
            $user->api = $api;
            $user->save();

        Alert::success('You have successfully changed your API.', 'API Changed');
        return Redirect::back();
        } else {
            return redirect('/');
        }
    }

        public function isDiscordAuth($serverid)
        {
          $server = DiscordKey::where('serverid', $serverid)->first();

          if(!$server)
          {
            return "Failed";
          }
        }


        public function discordAuth($key, $serverid, $servername)
        {
          $key = DiscordKey::where('key', $key)->first();
          $server = DiscordKey::where('serverid', $serverid)->first();

          if($server)
          {
            return "Complete";
          }


          if($key)
          {
            if($key->used == 0)
            {
              $key->used = 1;
              $key->serverid = $serverid;
              $key->servername = $servername;
              $key->save();
              return "Valid";
            } else {
              return "Used";
            }

          } else {
            return "Invalid";
          }
        }





    public function viewHighscores(){
        return view('highscores', ['users' => User::where([['public', '=', 'on']], ['hasVerified', '=', 'Yes'])->get(), 'btc' => HomeController::btcUsd()]);
    }

    public function getProfit($username, $serverid){

        if($this->isDiscordAuth($serverid) != "Failed")
        {
          $user = User::where('username', $username)->first();
          if($user)
          {
          if($user->public != "off")
          {
            $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
            $profit = number_format((($user->getNetWorthNew('coinmarketcap') * $multiplier) - $user->getInvested('USD')), 2);
            $json = json_encode($profit);

            return $profit;
          } else {
            return "private";
          }
        } else {
          return "none";
        }
        } else {
          return "Failed";
        }
    }
    public function getWorth($username, $serverid){
      if($this->isDiscordAuth($serverid) != "Failed")
      {
        $user = User::where('username', $username)->first();
        if($user)
        {
        if($user->public != "off")
        {
          $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
          $profit = number_format((($user->getNetWorthNew('coinmarketcap') * $multiplier)), 2);
          $json = json_encode($profit);

          return $profit;
        } else {
          return "private";
        }
      } else {
        return "none";
      }
      } else {
        return "Failed";
      }
    }

    public function getFollowers($username, $serverid){
      if($this->isDiscordAuth($serverid) != "Failed")
      {
        $user = User::where('username', $username)->first();
        if($user)
        {
        if($user->public != "off")
        {
          $followers = count($user->followers()->get());

          return $followers;
        } else {
          return "private";
        }
      } else {
        return "none";
      }
      } else {
        return "Failed";
      }
    }
    public function getInvested($username, $serverid){
      if($this->isDiscordAuth($serverid) != "Failed")
      {
        $user = User::where('username', $username)->first();
        if($user)
        {
        if($user->public != "off")
        {
          $invested = number_format($user->getInvested('USD'), 2);

          return $invested;
        } else {
          return "private";
        }
      } else {
        return "none";
      }
      } else {
        return "Failed";
      }
    }

    public function getProfile($username, $serverid)
    {
      if($this->isDiscordAuth($serverid) != "Failed")
      {
      $user = User::where('username', $username)->first();
      if($user)
        {
        if($user->public != "off")
        {
          $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
          $invested = $user->getInvested('USD');
          $worth = (($user->getNetWorthNew('coinmarketcap') * $multiplier));
          $profit = $worth - $invested;
          $followers = count($user->followers()->get());
          $impressions = $user->impressed;

          $response = ['profit' => number_format($profit, 2), 'invested' => number_format($invested, 2),'worth' =>  number_format($worth, 2),'followers' =>  $followers,'impressions' =>  $impressions, 'bio' => $user->bio, 'avatar' => $user->avatar, 'id' => $user->id];

          return $response;
        } else {
          return "private";
        }
      } else {
        return "none";
      }
    } else {
      return "Failed";
    }
    }


    public function getProfit2($username){
        $user = User::where('username', $username)->first();

        $networth = HomeController::netWorth($user->id) * Crypto::where('symbol', 'btc')->first()->price_usd;
        $invested = $user->invested;
        $profit = $networth - $invested;
        $json = json_encode($profit);

        return $profit;
    }

    public function getInvestments($username){
        $user = User::where('username', $username)->first();
        $investments = Investment::where('userid', $user->id)->get();
        $json = json_encode($investments);
        return $json;



    }


    public static function netWorth($id){
        $investments = Investment::where([['userid', $id], ['sold_at', '=', null]])->get();
        $networth = 0;
        foreach($investments as $investment){
            if($investment->sold_at == null){

                if(Auth::user()){
                    if(Auth::user()->api == "coinmarketcap"){

                        if(Crypto::where('symbol', $investment->crypto)->first()){
                            $value = $investment->amount * Crypto::where('symbol', $investment->crypto)->first()->price_btc;
                        } elseif($investment->amount * Polo::where('symbol', $investment->crypto)->first()) {
                            $value = $investment->amount * Crypto::where('symbol', $investment->crypto)->first()->price_btc;
                        } else {
                            $value = $investment->amount * Bittrex::where('symbol', $investment->crypto)->first()->price_btc;
                        }


                    } elseif(Auth::user()->api == "bittrex") {
                        if(bittrex::where('symbol', $investment->crypto)->first()){
                            $value = $investment->amount * bittrex::where('symbol', $investment->crypto)->first()->price_btc;
                        } else {
                            $value = $investment->amount * Crypto::where('symbol', $investment->crypto)->first()->price_btc;
                        }
                    } else {
                        if(Polo::where('symbol', $investment->crypto)->first()){
                            $value = $investment->amount * Polo::where('symbol', $investment->crypto)->first()->price_btc;
                        } elseif(Crypto::where('symbol', $investment->crypto)->first()) {
                            $value = $investment->amount * Crypto::where('symbol', $investment->crypto)->first()->price_btc;
                        } else {

                        }
                    }
                } else {
                    if(Crypto::where('symbol', $investment->crypto)->first()){
                        $value = $investment->amount * Crypto::where('symbol', $investment->crypto)->first()->price_btc;
                    } elseif(Polo::where('symbol', $investment->crypto)->first()) {
                        $value = $investment->amount * Polo::where('symbol', $investment->crypto)->first()->price_btc;
                    } elseif(Bittrex::where('symbol', $investment->crypto)->first()) {
                        $value = $investment->amount * Bittrex::where('symbol', $investment->crypto)->first()->price_btc;
                    } else {
                        $value = 0;
                    }
                    }



                $networth += $value;
            }
        }
        return $networth;
    }

    public function btcUsd(){
        return Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

      $shoutboxItems = Cache::remember('shoutbox_messages', 60, function()
      {
          return Shoutbox::orderBy('created_at', 'desc')->take(25)->get();
      });

      $shoutboxItems = $shoutboxItems->reverse();

        if(Auth::user()){
        return view('home', ['shoutboxItems' => $shoutboxItems, 'networth' => HomeController::netWorth(Auth::user()->id), 'btc' => HomeController::btcUsd(), 'investments' => DB::table('investments')->where('sold_at', null)->latest('date')->paginate(5), 'users' => User::orderByRaw('RAND()')->where('public', 'on')->take(4)->get()]);
        } else {
            return view('auth.login');
        }
    }

    public function index2(Request $request)
    {

      $summed = Cache::remember('investments'.Auth::user()->id, 60, function()
      {
        $poloniex = PoloInvestment::where([['userid', '=', Auth::user()->id]])->SelectRaw('*, "Poloniex" as exchange, comment as note');
        $bittrex = BittrexInvestment::where([['userid', '=', Auth::user()->id]])->SelectRaw('*, "Bittrex" as exchange, comment as note');
        return ManualInvestment::where([['userid', '=', Auth::user()->id]])->SelectRaw('*, "Manual" as exchange, comment as note')->union($poloniex)->union($bittrex)->get();
      });

      $balances = Cache::remember('balances'.Auth::user()->id, 30, function()
      {
        return Balance::where('userid', Auth::user()->id)->get();
      });


        $statuses = Status::orderBy('sticky', 'desc')->orderBy('created_at', 'desc')->where('moderate', 'no')->paginate(10);

        if ($request->ajax()) {
          if(!$request->get('type'))
          {
      		    $view = view('module.posts',compact('statuses'))->render();
              return response()->json(['html'=>$view]);
          } else {
            if($request->get('type') == "me")
            {
              $statuses = Status::orderBy('sticky', 'desc')->orderBy('created_at', 'desc')->where('userid', Auth::user()->id)->paginate(10);
              $view = view('module.posts',compact('statuses'))->render();
              return response()->json(['html'=>$view]);
            } elseif($request->get('type') == "following")
            {
              $followers = Auth::user()->first()->followings()->select('id')->get();
              $array = array();
              foreach($followers as $follower)
              {
                array_push($array, $follower->id);
              }
              $statuses = Status::orderBy('sticky', 'desc')->orderBy('created_at', 'desc')->whereIn('userid', $array)->paginate(10);
              $view = view('module.posts',compact('statuses'))->render();
              return response()->json(['html'=>$view]);
            }
          }
          }

        if(Auth::user()){

        if($request->get('status') == "" && $request->get('tag') == ""){
        return view('home2', ['statuses' => $statuses, 'btc' => HomeController::btcUsd(), 'balances2' => Balance::where([['userid', '=', Auth::user()->id], ['amount', '>', 0.0001]])->selectRaw('SUM(amount) as amount, currency, color')->groupBy('currency', 'color')->get(), 'trackings' => Tracking::where('userid', Auth::user()->id)->get(), 'unions' => $summed->where('sold_at', null)->take(5)]);
      } elseif($request->get('status') != "") {
        return view('home2', ['statuses' => Status::where('id', $request->get('status'))->where('moderate', 'no')->get(), 'btc' => HomeController::btcUsd(), 'balances2' => Balance::where([['userid', '=', Auth::user()->id], ['amount', '>', 0.0001]])->selectRaw('SUM(amount) as amount, currency, color')->groupBy('currency', 'color')->get(), 'trackings' => Tracking::where('userid', Auth::user()->id)->get(), 'unions' => $summed->where('sold_at', null)->take(5)]);
      } elseif($request->get('tag') != "")
      {
        return view('home2', ['statuses' => Status::where('status', 'like', $request->get('tag')."%")->where('moderate', 'no')->get(), 'btc' => HomeController::btcUsd(), 'balances2' => Balance::where([['userid', '=', Auth::user()->id], ['amount', '>', 0.0001]])->selectRaw('SUM(amount) as amount, currency, color')->groupBy('currency', 'color')->get(), 'trackings' => Tracking::where('userid', Auth::user()->id)->get(), 'unions' => $summed->where('sold_at', null)->take(5)]);
      }

        } else {
            return view('auth.login');
        }
    }

    public function testA()
    {
      $followers = User::where('id', 6759)->first()->followings()->get();
      $array = array();
      foreach($followers as $follower)
      {
      array_push($array, $follower->id);
      }

      $statuses = Status::orderBy('created_at', 'desc')->whereIn('userid', $array)->get();

      echo $statuses;
    }

    public function landingPage(){
      if(Auth::user())
      {
        return redirect('/dashboard');
      } else {
        return view('newlanding');
      }
    }

        public function viewProfile($username)
    {
        $user = User::where('username', $username)->first();
        if($user != null){
            if(User::where('username', $username)->first()->public == "on"){
                return view('profile', ['user' => User::where('username', $username)->first(), 'networth' => HomeController::netWorth($user->id), 'btc' => HomeController::btcUsd(), 'investments' => Investment::where('userid', $user->id)->get(), 'spent' => HomeController::allTimeSpent($user->id), 'alltimesold' => HomeController::allTimeSold($user->id)]);
            } else {
                if(Auth::user()){
                    if($username == Auth::user()->username || Auth::user()->isFounder()){
                        return view('profile', ['user' => User::where('username', $username)->first(), 'networth' => HomeController::netWorth($user->id), 'btc' => HomeController::btcUsd(), 'investments' => Investment::where('userid', $user->id)->get(), 'spent' => HomeController::allTimeSpent($user->id), 'alltimesold' => HomeController::allTimeSold($user->id)]);
                    } else {
                    return view('private');
                    }
                } else {
                    return view('private');
                }
            }
        } else {
            return view('404');
        }
    }

    public function showProfile2($username){
        $user = User::where('username', $username)->first();

        if($user != null)
        {
            if($user->public == "on")
            {
                return view('profile2', ['user' => $user, 'networth' => HomeController::netWorth($user->id), 'btc' => HomeController::btcUsd(), 'investments' => Investment::where('userid', $user->id)->get(), 'spent' => HomeController::allTimeSpent($user->id), 'alltimesold' => HomeController::allTimeSold($user->id), 'comments' => Comment::where('userid', $user->id)->paginate(5)]);

            } else
            {
                if(Auth::user())
                {
                    if(Auth::user()->username = $user->username)
                    {
                return view('profile2', ['user' => $user, 'networth' => HomeController::netWorth($user->id), 'btc' => HomeController::btcUsd(), 'investments' => Investment::where('userid', $user->id)->get(), 'spent' => HomeController::allTimeSpent($user->id), 'alltimesold' => HomeController::allTimeSold($user->id), 'comments' => Comment::where('userid', $user->id)->paginate(5)]);
                    } else
                    {
                        return view('private');
                    }
                } else
                {
                    return view('private');
                }

            }
        } else
        {
            return view('404');

        }
    }





        public function coins()
    {
        return view('mycoins', ['networth' => HomeController::netWorth(Auth::user()->id), 'btc' => HomeController::btcUsd(), 'investments' => Investment::where([['userid', '=', Auth::user()->id], ['type', '=', 'investment']])->get(), 'minings' => Investment::where([['userid', '=', Auth::user()->id], ['type', '=', 'mining']])->get()]);
    }

    public function removeCoin($id){
        $coin = Investment::where('id', $id)->first();
        $user = Auth::user();

        //First remove investment from user
        if($coin){
        if($coin->userid == $user->id){
            if($coin->sold_at == null){
                $user->invested -= $coin->usd_total;
                $user->save();
            }
        // Now Delete coin
        $coin->delete();
        Alert::success('Your investment was successfully deleted.', 'Investment deleted');
        return redirect('/coins');
        } else {
        Alert::error('You can not delete another persons investment!', 'Deletion failed');
        return redirect('/coins');
        }
        } else {
        Alert::error('We could not find any investment to delete!', 'Deletion failed');
        return redirect('/coins');
        }
    }

    public function addCoin(Request $request){

        $symbol = Crypto::where('name', $request->get('coin'))->first();
        $user = Auth::user();
        $bought = str_replace(',', '.', $request->get('bought_at'));
        $bought_usd = str_replace(',', '.', $request->get('bought_at_usd'));
        $amount = str_replace(',', '.', $request->get('amount'));

            $messages = [
                'coin.required' => 'You must enter a coin available from the list.',
                'amount.required'  => 'You must enter an amount of coins bought.',
                'date.date'  => 'You must enter a correct date in the date field',
                'date.required'  => 'You must enter the date you made the investment.'
        ];

        $rules = [
            'coin' => 'required',
            'amount' => 'required',
            'date' => 'required|date',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            $errors = $validator->errors();

            if($validator->fails()){
                if($errors->first('autocomplete_states')){
                Alert::error($errors->first('autocomplete_states'), 'Investment failed');
                } elseif($errors->first('bought_at')) {
                Alert::error($errors->first('bought_at'), 'Investment failed');

                } elseif($errors->first('bought_at_usd')) {
                Alert::error($errors->first('bought_at_usd'), 'Investment failed');

                } elseif($errors->first('amount')) {
                Alert::error($errors->first('amount'), 'Investment failed');

                } elseif($errors->first('date')) {
                Alert::error($errors->first('date'), 'Investment failed');

                }
                return Redirect::back();
            }

        if(!$request->get('amount')){
                Alert::error('You must enter an amount of coins bought.', 'Investment failed');
                return Redirect::back();
        }
        if(!$request->get('bought_at') && !$request->get('bought_at_usd')){
                Alert::error('You must enter a price paid per coin.', 'Investment failed');
                return Redirect::back();
        }

        if($request->get('bought_at') && !is_numeric($bought)){
                Alert::error('You must enter a numeric value in the bought at field.', 'Investment failed');
                return Redirect::back();
        }
        if(!is_numeric($amount)){
                Alert::error('You must enter a numeric value in the coins bought field.', 'Investment failed');
                return Redirect::back();
        }
        if($request->get('bought_at_usd') && !is_numeric($bought_usd)){
                Alert::error('You must enter a price paid per coin.', 'Investment failed');
                return Redirect::back();
        }
        if($request->get('amount') <= 0) {
                Alert::error('You must enter an amount greater than 0.', 'Investment failed');
                return Redirect::back();
        }
        if($request->get('bought_at_usd') != "" && $request->get('bought_at_usd') <= 0) {
                Alert::error('You must enter a value greater than 0.', 'Investment failed');
                return Redirect::back();
        }
        if($request->get('bought_at') && $bought <= 0) {
                Alert::error('You must enter a value greater than 0.', 'Investment failed');
                return Redirect::back();
        }




        $date = $request->get('date');
        $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
        if($date != date('Y-m-d')){
        $client = new \GuzzleHttp\Client();
        try{
            $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
            $response = $res->getBody();
            $prices = json_decode($response, true);
            $value = 0;
            foreach($prices['BTC'] as $key => $price){
                $value = $price;
            }
            }  catch (\GuzzleHttp\Exception\ClientException $e) {
                $value = Crypto::where('symbol', 'btc')->first()->price_usd;
                }
            } else {
                $value = Crypto::where('symbol', 'btc')->first()->price_usd;
            }

        //Adds coin
        if($symbol){
        $coin = new Investment;
        $coin->userid = Auth::user()->id;
        $coin->crypto = $symbol->symbol;
        if($request->get('bought_at_usd')){
        $coin->bought_at_usd = str_replace(',', '.', $request->get('bought_at_usd'));
        $coin->bought_at = $coin->bought_at_usd / $value;
        } else {
        $coin->bought_at = str_replace(',', '.', $request->get('bought_at'));
        }
        $coin->amount = str_replace(',', '.', $request->get('amount'));
        $coin->date_bought = $request->get('date');
        $coin->usd_total = ($coin->bought_at * str_replace(',', '.', $request->get('amount'))) * $value;
        $coin->btc_price_bought = $value;
        $coin->save();

        //Adds investment to user
        $user->invested += $coin->usd_total;
        $user->save();

        Alert::success('Your investment was successfully added.', 'Investment added');
        return redirect('/coins');
        } else {
        Alert::error('You must enter a coin available from the list.', 'Investment failed');
        return redirect('/coins');
        }







    }


    public function addMining(Request $request){

        $symbol = Crypto::where('name', $request->get('coin'))->first();
        $user = Auth::user();
        $amount = str_replace(',', '.', $request->get('amount'));
            $messages = [
                'coin.required' => 'You must enter a coin available from the list.',
                'amount.required'  => 'You must enter an amount of coins bought.',
                'date.date'  => 'You must enter a correct date in the date field',
                'date.required'  => 'You must enter the date you made the investment.'
        ];

        $rules = [
            'coin' => 'required',
            'amount' => 'required',
            'date' => 'required|date',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            $errors = $validator->errors();

            if($validator->fails()){
                if($errors->first('autocomplete_states3')){
                Alert::error($errors->first('autocomplete_states'), 'Investment failed');
                } elseif($errors->first('amount')) {
                Alert::error($errors->first('amount'), 'Investment failed');

                } elseif($errors->first('date')) {
                Alert::error($errors->first('date'), 'Investment failed');

                }
                return Redirect::back();
            }

        if(!$request->get('amount')){
                Alert::error('You must enter an amount of coins bought.', 'Investment failed');
                return Redirect::back();
        }
        if(!is_numeric($amount)){
                Alert::error('You must enter a numeric value in the coins bought field.', 'Investment failed');
                return Redirect::back();
        }
        if($request->get('amount') <= 0) {
                Alert::error('You must enter an amount greater than 0.', 'Investment failed');
                return Redirect::back();
        }




        $date = $request->get('date');
        $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
        if($date != date('Y-m-d')){
        $client = new \GuzzleHttp\Client();
        try{
            $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
            $response = $res->getBody();
            $prices = json_decode($response, true);
            $value = 0;
            foreach($prices['BTC'] as $key => $price){
                $value = $price;
            }
            }  catch (\GuzzleHttp\Exception\ClientException $e) {
                $value = Crypto::where('symbol', 'btc')->first()->price_usd;
                }
            } else {
                $value = Crypto::where('symbol', 'btc')->first()->price_usd;
            }

        //Adds coin
        if($symbol){
        $coin = new Investment;
        $coin->userid = Auth::user()->id;
        $coin->crypto = $symbol->symbol;
        $coin->bought_at = 0;
        $coin->amount = str_replace(',', '.', $request->get('amount'));
        $coin->date_bought = $request->get('date');
        $coin->btc_price_bought = $value;
        $coin->type = "Mining";
        $coin->save();

        //Adds investment to user
        $user->invested += $coin->usd_total;
        $user->save();

        Alert::success('Your coin was successfully added.', 'Coin added');
        return redirect('/coins');
        } else {
        Alert::error('You must enter a coin available from the list.', 'Investment failed');
        return redirect('/coins');
        }

    }




    public function getCoin($id){
        $investment = Investment::where('id', $id)->first();

        return $investment;
    }

    public function getComment($id){
        $comment = Comment::where('id', $id)->first();

        return $comment;
    }


    public function editCoin($id, Request $request){
        $investment = Investment::where('id', $id)->first();
        $user = Auth::user();
        $bought = str_replace(',', '.', $request->get('bought_at'));
        $bought_usd = str_replace(',', '.', $request->get('bought_at_usd'));
        $amount = str_replace(',', '.', $request->get('amount'));


        if(!$request->get('amount')){
                Alert::error('You must enter an amount of coins bought.', 'Investment failed');
                return Redirect::back();
        }
        if(!$request->get('bought_at') && !$request->get('bought_at_usd')){
                Alert::error('You must enter a price paid per coin.', 'Investment failed');
                return Redirect::back();
        }

        if($request->get('bought_at') && !is_numeric($bought)){
                Alert::error('You must enter a numeric value in the bought at field.', 'Investment failed');
                return Redirect::back();
        }
        if(!is_numeric($amount)){
                Alert::error('You must enter a numeric value in the coins bought field.', 'Investment failed');
                return Redirect::back();
        }
        if($request->get('bought_at_usd') && !is_numeric($bought_usd)){
                Alert::error('You must enter a price paid per coin.', 'Investment failed');
                return Redirect::back();
        }
        if($request->get('amount') <= 0) {
                Alert::error('You must enter an amount greater than 0.', 'Investment failed');
                return Redirect::back();
        }
        if($request->get('bought_at_usd') && $request->get('bought_at_usd') <= 0) {
                Alert::error('You must enter a value greater than 0.', 'Investment failed');
                return Redirect::back();
        }
        if($request->get('bought_at') && $request->get('bought_at') <= 0) {
                Alert::error('You must enter a value greater than 0.', 'Investment failed');
                return Redirect::back();
        }


        if($investment){
        if($investment->userid == Auth::user()->id){
            if($investment->bittrex_id == "")   {
            $date = $investment->date_bought;
            $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
            if($date != date('Y-m-d')){
            $client = new \GuzzleHttp\Client();
            try{
                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                $response = $res->getBody();
                $prices = json_decode($response, true);
                $value = 0;
                foreach($prices['BTC'] as $key => $price){
                    $value = $price;
                }
            }  catch (\GuzzleHttp\Exception\ClientException $e) {
                $value = Crypto::where('symbol', 'btc')->first()->price_usd;
                }
            } else {
                $value = Crypto::where('symbol', 'btc')->first()->price_usd;
            }

            $user->invested -= $investment->usd_total;
            $user->save();

            if($request->get('bought_at_usd')){
            $investment->bought_at_usd = str_replace(',', '.', $request->get('bought_at_usd'));
            $investment->bought_at = $investment->bought_at_usd / $value;
            } else {
            $investment->bought_at = str_replace(',', '.', $request->get('bought_at'));
            }
            $investment->date_bought = $request->get('date');
            $investment->amount = $amount;
            $investment->usd_total = (($investment->amount * $investment->bought_at) * $value);
            $investment->btc_price_bought = $value;
            $investment->save();
            $user->invested += (($investment->amount * $investment->bought_at) * $value);
            $user->save();




            Alert::success('Your investment was successfully updated.', 'Investment updated');
            return redirect('/coins');

            } else {
            Alert::success('You can not edit verified investments.', 'Update failed');
            return redirect('/coins');
            }

        } elseif($investment->userid != Auth::user()->id) {
            Alert::error('You can not edit another persons investment!', 'Update failed');
            return redirect('/coins');
        }
        }
        else {
            Alert::error('No investment was found.', 'Update failed');
            return redirect('/coins');
        }

    }

    public function soldCoin($id, Request $request){
        $investment = Investment::where('id', $id)->first();
        $user = Auth::user();
        $sold = str_replace(',', '.', $request->get('sold_at'));
        $sold_usd = str_replace(',', '.', $request->get('sold_at_usd'));

        if(!$request->get('sold_at') && !$request->get('sold_at_usd')){
            Alert::error('You must enter a price per coin.', 'Sell failed');
            return Redirect::back();
        }

        if($request->get('sold_at_usd') && !is_numeric($sold_usd)){
                Alert::error('You must enter a numeric value in the sold at usd field.', 'Sell failed');
                return Redirect::back();
        }
        if($request->get('sold_at') && !is_numeric($sold)){
                Alert::error('You must enter a numeric value in the sold at field.', 'Sell failed');
                return Redirect::back();
        }
        if($request->get('sold_at_usd') && $request->get('sold_at_usd') <= 0) {
                Alert::error('You must enter a value greater than 0.', 'Investment failed');
                return Redirect::back();
        }
        if($request->get('sold_at') && $request->get('sold_at') <= 0) {
                Alert::error('You must enter a value greater than 0.', 'Investment failed');
                return Redirect::back();
        }






        $date2 = $request->get('date');
        $seconddate = date('Y-m-d', strtotime($date2. ' + 1 days'));
        if($date2 != date('Y-m-d')){
        $client = new \GuzzleHttp\Client();
        try{
            $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date2).'&extraParams=Altpocket');
            $response = $res->getBody();
            $prices = json_decode($response, true);
            $value2 = 0;
            foreach($prices['BTC'] as $key => $price){
                $value2 = $price;
            }
        }  catch (\GuzzleHttp\Exception\ClientException $e) {
            $value2 = Crypto::where('symbol', 'btc')->first()->price_usd;
            }
        } else {
            $value2 = Crypto::where('symbol', 'btc')->first()->price_usd;
        }


        if($investment->userid == Auth::user()->id){
            if($request->get('sold_at_usd') != ""){
            $investment->sold_at = $sold_usd / $value2;
            $investment->sold_for =$sold_usd * $investment->amount;
            } else {
            $investment->sold_for = ($investment->amount * $sold) * $value2;
            $investment->sold_at = $sold;
            }
            $investment->date_sold = $request->get('date');
            $investment->save();



        $user->invested -= $investment->usd_total;
        $user->save();


        Alert::success('Your investment was successfully marked as sold.', 'Investment sold');
        return redirect('/coins');
        } else {
        Alert::error('You can not mark another persons investment as sold!', 'Update failed');
        return redirect('/coins');
        }
    }


    public function impressed($username){
        $user = User::where('username', $username)->first();
        if(!Impressed::where([['ip', '=', $_SERVER["HTTP_CF_CONNECTING_IP"]], ['username', '=', $username]])->first()) {
        $impressed = new Impressed;
        $impressed->username = $username;
        $impressed->ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $impressed->save();

        $user->impressed += 1;
        $user->save();
        Alert::success('You have now shown that you were impressed by this user!', 'You were impressed!');
        return redirect('/user/'.$username);
        } else {
        Alert::warning('You have already been impressed by this user!', 'You were already impressed!');
        return redirect('/user/'.$username);
        }
    }

    public function updateInfo($username, Request $request){
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:12|unique:users|alpha_dash'
        ]);
        $errors = $validator->errors();


        if ($validator->fails()) {
            if($errors->first('username')){
              Alert::error($errors->first('username'), 'Settings failed');
              return Redirect::back();
            }
        }



        if($request->get('username') != $user->username){
          if(User::where('username', $request->get('username'))->first()){
            if(User::where('username', $request->get('username'))->first() == $user){
              $user->username = $request->get('username');
            } else {
              Alert::error('The username you entered is already in use.', 'Username change failed');
              return redirect('/user/'.$username);
            }

          } else {
              $user->username = $request->get('username');
          }

        }


        if($request->get('email') != $user->email){
          if(User::where('email', $request->get('email'))->first()){
            if(User::where('email', $request->get('email'))->first() == $user){
              $user->email = $request->get('email');
            } else {
              Alert::error('The email you entered is already in use.', 'Email change failed');
              return redirect('/user/'.$username);
            }

          } else {
              $user->email = $request->get('email');
          }

        }



        $user->bio = $request->get('bio');
        $user->twitter = $request->get('twitter');
        $user->youtube = $request->get('youtube');
        $user->algorithm = $request->get('algo');
        if($request->get('public')){
        $user->public = 'on';
        } else {
            $user->public = 'off';
        }
        if($request->get('comments')){
        $user->comments = 'on';
        } else {
            $user->comments = 'off';
        }
        $user->save();

        Alert::success('Your profile has been updated.', 'Profile Updated');
        return redirect('/user/'.$username);
    }

    public function avatar(Request $request){

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
                return redirect('/user/'.$username);
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
                    $output = Slim::saveFile($data, $name, 'uploads/avatars/'.Auth::user()->id."/", false);
                    $user = Auth::user();
                    $user->avatar = $name;
                    $user->save();
                    Alert::success('Your avatar has been updated.', 'Avatar updated');
                    return redirect('user/'.$user->username);
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
                    return redirect('user/'.$user->username);
                    array_push($files, $input);
                }


            }
        }

    }


    public function uploadLogo(Request $request){
        $user = Auth::user();

            $rules = [
                'file' => 'image',
                'slim[]' => 'image'
                ];

            $validator = Validator::make($request->all(), $rules);
            $errors = $validator->errors();

            if($validator->fails()){
                Alert::error('You are not allowed to upload anything but an image.', 'Upload failed');
                return redirect('/user/'.$username);
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
                    $output = Slim::saveFile($data, $name, 'assets/logos/', false);
                    Alert::success('The coins logo has been uplaoded', 'Coin logo uploaded');
                    return redirect('user/'.$user->username);
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
                    $input = Slim::saveFile($data, $name, 'assets/logos/', false);
                    Alert::success('The coins logo has been uplaoded', 'Coin logo uploaded');
                    return redirect('user/'.$user->username);
                    array_push($files, $input);
                }


            }
        }

    }


    public function header(Request $request){

        // Ifall det redan finns en profilbild, ta bort den.
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
                return redirect('/user/'.$username);
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
                    return redirect('user/'.$user->username);
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
                    return redirect('user/'.$user->username);
                    array_push($files, $input);
                }


            }
        }

    }

    public function updateSettings($username, Request $request){
        $user = Auth::user();
        $user->api = $request->get('api');
        $user->save();

        Alert::success('Your settings was successfully updated.', 'Settings updated');
        return redirect('/user/'.$username);
    }

    public function grabImages(){
        $cryptos = Crypto::get();
        foreach($cryptos as $crypto){

         $file = 'https://coinventory.net/img/coins/'.$crypto->symbol.'.png';
        $file_headers = @get_headers($file);
        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
        }
        else {
            copy('https://coinventory.net/img/coins/'.$crypto->symbol.'.png', 'assets/logos/'.$crypto->symbol.'.png');
        }
        }

    }

    public function updateApi($username, Request $request){
        $user = Auth::user();

        $user->api_key = $request->get('publickey');
        $user->polo_api_key = $request->get('polo_publickey');
        $user->api_secret = $request->get('privatekey');
        $user->polo_api_secret = $request->get('polo_privatekey');
        $user->save();
        Alert::success('Your api keys was successfully updated.', 'Api keys updated');
        return redirect('/user/'.$username);
    }

    public function importGuide(){
        return view('import');
    }

    public function resetCoins(){
        $user = Auth::user();

        $trades = Trade::where('userid', $user->id)->get();

        foreach($trades as $trade){
            $trade->delete();
        }

        $investments = Investment::where('userid', $user->id)->get();

        foreach($investments as $investment){
            $investment->delete();
        }

        $exchanges = Exchange::where('userid', $user->id)->get();

        foreach($exchanges as $exchange){
            $exchange->delete();
        }

        $user->invested = 0;
        $user->save();

        Alert::success('Your account has successfully been reset.', 'Reset successful');
        return redirect('/coins/');


    }


    public function sellMultiple(Request $request){
        $amount = $request->get('amount');
        $date = $request->get('date');

        $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
        if($date != date('Y-m-d')){
        $client = new \GuzzleHttp\Client();
        try{
            $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
            $response = $res->getBody();
            $prices = json_decode($response, true);
            $value = 0;
            foreach($prices['BTC'] as $key => $price){
                $value = $price;
            }
        }  catch (\GuzzleHttp\Exception\ClientException $e) {
            $value = Crypto::where('symbol', 'btc')->first()->price_usd;
            }
        } else {
            $value = Crypto::where('symbol', 'btc')->first()->price_usd;

        }



        if(Crypto::where('name', $request->get('coin'))->first()){
        $symbol = Crypto::where('name', $request->get('coin'))->first();
        $investments = Investment::where([['userid', '=', Auth::user()->id], ['crypto', '=', $symbol->symbol], ['sold_at', '=', null], ['bittrex_id', '=', null]])->orderBy('date')->get();
        $sold = str_replace(',', '.', $request->get('sold_at'));
        $sold_usd = str_replace(',', '.', $request->get('sold_at_usd'));

        if(!$request->get('sold_at') && !$request->get('sold_at_usd')){
            Alert::error('You must enter a price per coin.', 'Sell failed');
            return Redirect::back();
        }

        if($request->get('sold_at_usd') && !is_numeric($sold_usd)){
                Alert::error('You must enter a numeric value in the sold at usd field.', 'Sell failed');
                return Redirect::back();
        }
        if($request->get('sold_at') && !is_numeric($sold)){
                Alert::error('You must enter a numeric value in the sold at field.', 'Sell failed');
                return Redirect::back();
        }








        $paid = 0;
        $paideach = 0;
        $counter = 0;
        $datebought = "";
        $btcprice = 0;
        if($investments) {
        foreach($investments as $investment){
            if($amount > 0){
                if($investment->amount <= $amount){
                    $amount -= $investment->amount;
                    $paid += $investment->usd_total;
                    $paideach += $investment->bought_at;
                    $btcprice = $investment->btc_price_bought;
                    $counter += 1;
                    $investment->delete();

                } elseif($investment->amount >= $amount){

                    $paid += ($investment->usd_total / $investment->amount) * $amount;
                    $paideach += $investment->bought_at;
                    $investment->usd_total = ($investment->usd_total / $investment->amount) * ($investment->amount - $amount);
                    $investment->amount -= $amount;
                    $investment->save();

                    $counter += 1;

                    $amount = 0;

                    $datebought = $investment->date_bought;
                    $btcprice = $investment->btc_price_bought;
                }
            }
        }
        if($counter != 0){
        $investment = new Investment;
        $investment->userid = Auth::user()->id;
        $investment->crypto = $symbol->symbol;
        $investment->date_bought = date('Y-m-d', strtotime($datebought));
        $investment->bought_at = ($paideach / $counter);
        if($request->get('sold_at_usd')){
        $sold_price = str_replace(',', '.', $request->get('sold_at_usd'));
        $investment->sold_at = $sold_price / $value;
        } else {
        $investment->sold_at = str_replace(',', '.', $request->get('sold_at'));
        }
        $investment->amount = $request->get('amount') - $amount;
        $investment->date_sold = $request->get('date');
        $investment->sold_for = ($request->get('amount') * $investment->sold_at) * $value;
        $investment->usd_total = $paid;
        $investment->market = "manual";
        $investment->sale_id = time() + Auth::user()->id;
        $investment->btc_price_bought =  $btcprice;
        $investment->save();
        } else {
        Alert::error('You had no investments to sell.', 'Sell failed');
        return redirect('/coins');
        }

        HomeController::calcInvested();
        Alert::success('Your investments was successfully marked as sold.', 'Investments sold');
        return redirect('/coins');
        } else {
        Alert::error('You had no investments to sell.', 'Sell failed');
        return redirect('/coins');
        }





    } else {
        Alert::error('You must select an coin from the list.', 'Sell failed');
        return redirect('/coins');
        }
    }




}
