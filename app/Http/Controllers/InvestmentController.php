<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Alert;
use Redirect;
use Validator;

//models
use App\Crypto;
use App\WorldCoin;
use App\ManualInvestment;
use App\PoloInvestment;
use App\BittrexInvestment;
use App\Mining;
use App\Balance;
use App\Multiplier;
use App\User;
use App\Key;
use DB;
use Cache;

class InvestmentController extends Controller
{

  //Toggle Condensed
  public function toggleCondensed(){

      if(Auth::user()){
          $user = Auth::user();
          if($user->summed == 1){
              $user->summed = 0;
              $user->save();
              return Redirect::back();
          } else {
              $user->summed = 1;
              $user->save();
              return Redirect::back();
          }
      }
  }

  public function deleteBalance($id)
  {
    $balance = Balance::where('id', $id)->first();

    if($balance->userid == Auth::user()->id)
    {
      $balance->delete();
      Alert::success('You have successfully removed the balance.', 'Balance removed');
      return Redirect::back();
    } else {
      Alert::error('You can not remove someone elses balance.', 'Oops..');
      return Redirect::back();
    }
  }

  //Get coins for manual investment list
  public function getCoins()
  {
    return Crypto::select('name')->get();
  }


  public function addSource(Request $request)
  {
    if($request->get('sourcetype') == "Poloniex" || $request->get('sourcetype') == "Bittrex")
    {
      $key = new Key;
      $key->userid = Auth::user()->id;
      $key->type = "Exchange";
      $key->exchange = $request->get('sourcetype');
      $key->public = encrypt($request->get('publickey'));
      $key->private = encrypt($request->get('privatekey'));
      $key->save();
    }
    if($request->get('sourcetype') == "Ethwallet")
    {
      $key = new Key;
      $key->userid = Auth::user()->id;
      $key->type = "Wallet";
      $key->exchange = "Ethereum";
      $key->public = encrypt($request->get('address'));
      $key->save();

      //Get initial ETH balance
      $amount = 0;

      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'https://api.etherscan.io/api?module=account&action=balance&address='.$request->get('address').'&tag=latest&apikey=A5WHVGRX2JMRIUSIS3J77CKPJ8CJ9W44HA');
      $response = $res->getBody();
      $balance = json_decode($response, true);

      $amount = $balance['result'] / 1000000000000000000;

      $balance = new Balance;
      $balance->userid = Auth::user()->id;
      $balance->exchange = "Ethereum";
      $balance->currency = "ETH";
      $balance->amount = $amount;
      $balance->save();
    }
    if($request->get('sourcetype') == "Ethnano")
    {
      $key = new Key;
      $key->userid = Auth::user()->id;
      $key->type = "Miner";
      $key->exchange = "ETH-Nano";
      $key->public = encrypt($request->get('account'));
      $key->save();

      //Get initial ETH balance
      $amount = 0;

      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'https://api.nanopool.org/v1/eth/balance/'.$request->get('account'));
      $response = $res->getBody();
      $balance = json_decode($response, true);

      $amount = $balance['data'];

      $balance = new Balance;
      $balance->userid = Auth::user()->id;
      $balance->exchange = "Nanopool";
      $balance->currency = "ETH";
      $balance->amount = $amount;
      $balance->save();
    }
    if($request->get('sourcetype') == "Ethermine")
    {
      $key = new Key;
      $key->userid = Auth::user()->id;
      $key->type = "Miner";
      $key->exchange = "Ethermine";
      $key->public = encrypt($request->get('account'));
      $key->save();

      //Get initial ETH balance
      $amount = 0;

      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'https://ethermine.org/api/miner_new/'.$request->get('account'));
      $response = $res->getBody();
      $balance = json_decode($response, true);

      $amount = $balance['unpaid'] / 1000000000000000000;

      $balance = new Balance;
      $balance->userid = Auth::user()->id;
      $balance->exchange = "Ethermine";
      $balance->currency = "ETH";
      $balance->amount = $amount;
      $balance->save();
    }
    if($request->get('sourcetype') == "Nicehash")
    {
      $key = new Key;
      $key->userid = Auth::user()->id;
      $key->type = "Miner";
      $key->exchange = "NiceHash";
      $key->public = encrypt($request->get('apiid'));
      $key->private = encrypt($request->get('readOnly'));
      $key->save();

      //Get initial ETH balance
      $amount = 0;

      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'https://api.nicehash.com/api?method=balance&id='.$request->get('apiid').'&key='.$request->get('readOnly'));
      $response = $res->getBody();
      $balance = json_decode($response, true);

      $amount = $balance['result']['balance_confirmed'];

      $balance = new Balance;
      $balance->userid = Auth::user()->id;
      $balance->exchange = "NiceHash";
      $balance->currency = "BTC";
      $balance->amount = $amount;
      $balance->save();
    }
    Alert::success('Your source has successfully been added.', 'Source added');
    return Redirect::back();

  }


  public function deleteSource($id)
  {
    $key = Key::where('id', $id)->first();

    if($key)
    {
      $exchange = $key->exchange;
      if(Auth::user()->id == $key->userid)
      {
        if($key->exchange == "Ethereum")
        {
          $balance = Balance::where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Ethereum']])->first();
          $balance->delete();
        }
        $key->delete();
        return $exchange;
      } else {
        return redirect('/');
      }
    } else {
      return "No key found";
    }
  }


    public function saveIcons()
    {
      $crypto = Crypto::where('symbol', 'ATOM')->first();

        $name = str_replace(' ', '-', $crypto->name);
        $name = strtolower($name);
        $image_link = "https://files.coinmarketcap.com/static/img/coins/32x32/".$name.".png";//Direct link to image
        $split_image = pathinfo($image_link);

        $crypto->image1 = 1;
        $crypto->save();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL , $image_link);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response= curl_exec ($ch);
        curl_close($ch);
        $file_name = "icons/32x32/".$crypto->symbol.".".$split_image['extension'];
        $file = fopen($file_name , 'w') or die("X_x");
        fwrite($file, $response);
        fclose($file);
    }


    public function btcPrice()
    {
        return Crypto::where('symbol', 'BTC')->select('price_usd')->first()->price_usd;
    }



    //Calculate invested.

    public function calculateInvested()
    {
      $user = Auth::user();
      $user->invested = 0;

      $p_investments = PoloInvestment::where([['userid', '=', $user->id], ['date_sold', '=', null]])->get();
      $b_investments = BittrexInvestment::where([['userid', '=', $user->id], ['date_sold', '=', null]])->get();
      $m_investments = ManualInvestment::where([['userid', '=', $user->id], ['date_sold', '=', null]])->get();


      foreach($p_investments as $p)
      {
        $user->invested += $p->bought_for;
      }

      foreach($b_investments as $b)
      {
        $user->invested += $b->bought_for;
      }

      foreach($m_investments as $m)
      {
        $user->invested += $m->bought_for;
      }

      $user->save();
    }



    public function currencies()
    {
      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'http://api.fixer.io/latest?base=USD');
      $response = $res->getBody();
      $multipliers = json_decode($response, true);

      foreach($multipliers['rates'] as $key => $multiplier)
      {
        if(Multiplier::where('currency', $key)->exists())
        {
          $multi = Multiplier::where('currency', $key)->first();
        } else {
          $multi = new Multiplier;
        }
          $multi->currency = $key;
          $multi->price = $multiplier;
          $multi->save();
      }

    }

    public function worldcoin()
    {
      $client = new \GuzzleHttp\Client();
      $res = $client->request('GET', 'https://www.worldcoinindex.com/apiservice/json?key=1EJvyv728fbYOvC5Sn44SO3sP');
      $response = $res->getBody();
      $cryptos = json_decode($response, true);


      foreach($cryptos['Markets'] as $crypto){
          $symbol = str_replace('/BTC', '', $crypto['Label']);
          if(Worldcoin::where('symbol', $symbol)->first()){
              $newcrypto = Worldcoin::where('symbol', $symbol)->first();
          } else {
              $newcrypto = new Worldcoin;
          }
          $newcrypto->name = $crypto['Name'];
          $newcrypto->symbol = $symbol;
          $newcrypto->price_usd = $crypto['Price_usd'];
          $newcrypto->price_btc = $crypto['Price_btc'];
          $newcrypto->price_eur = $crypto['Price_eur'];
          $newcrypto->price_gbp = $crypto['Price_gbp'];
          $newcrypto->volume_24h = $crypto['Volume_24h'];
          $newcrypto->save();
      }
    }


    // View Your Investments
    public function viewInvestments()
    {
      $p_investments = Cache::remember('p_investments'.Auth::user()->id, 60, function()
      {
        return PoloInvestment::where([['userid', '=', Auth::user()->id]])->get();
      });

      $b_investments = Cache::remember('b_investments'.Auth::user()->id, 60, function()
      {
        return BittrexInvestment::where([['userid', '=', Auth::user()->id]])->get();
      });

      $m_investments = Cache::remember('m_investments'.Auth::user()->id, 60, function()
      {
        return ManualInvestment::where([['userid', '=', Auth::user()->id]])->get();
      });



      return view('coins.investments', ['btc' => InvestmentController::btcPrice(), 'networth' => Auth::user()->getNetWorthNew(Auth::user()->api), 'minings' => Mining::where('userid', Auth::user()->id)->get(), 'multiplier' => Auth::user()->getMultiplier(), 'investments' => $m_investments, 'p_investments' => $p_investments, 'b_investments' => $b_investments, 'balances' => Balance::where('userid', Auth::user()->id)->get()]);
    }

    // View Your Investments
    public function viewInvestments2()
    {

      if(Auth::user()->summed == 1)
      {
        $summed = DB::select(DB::raw('
        select currency, sum(amount) as amount, sum(bought_at * amount)/sum(amount) as bought_at, sum(sold_at * amount)/sum(amount) as sold_at, avg(btc_price_bought_usd) as btc_price_bought_usd, avg(btc_price_bought_eth) as btc_price_bought_eth, avg(btc_price_sold_usd) as btc_price_sold_usd, avg(btc_price_sold_eth) as btc_price_sold_eth, market, soldmarket, sum(bittrex_amount) as bittrex_amount, sum(poloniex_amount) as poloniex_amount, sum(manual_amount) as manual_amount, sum(total) as total, sum(total_usdt) as total_usdt, sum(total_usdt_btc) as total_usdt_btc, sum(total_eth) as total_eth, sum(total_eth_btc) as total_eth_btc, sum(total_sold) as total_sold, sum(total_usdt_sold) as total_usdt_sold, sum(total_usdt_btc_sold) as total_usdt_btc_sold, sum(total_eth_sold) as total_eth_sold, sum(total_eth_btc_sold)
        FROM
        (
        Select currency, amount, bought_at, sold_at, btc_price_bought_usd, btc_price_sold_usd, btc_price_bought_eth, btc_price_sold_eth, market, soldmarket, "0" as bittrex_amount, amount as poloniex_amount, "0" as manual_amount, (amount * bought_at * btc_price_bought_usd) as total, (amount * sold_at * btc_price_sold_usd) as total_sold, (amount * bought_at) as total_usdt, (amount * sold_at) as total_usdt_sold, (amount * bought_at / btc_price_bought_usd) as total_usdt_btc, (amount * sold_at / btc_price_sold_usd) as total_usdt_btc_sold, (amount * bought_at) as total_eth, (amount * sold_at) as total_eth_sold, (amount * bought_at / btc_price_bought_eth) as total_eth_btc, (amount * sold_at / btc_price_sold_eth) as total_eth_btc_sold
        From polo_investments
        where userid = '.Auth::user()->id.'
        Union All
        Select currency, amount, bought_at, sold_at, btc_price_bought_usd, btc_price_sold_usd, btc_price_bought_eth, btc_price_sold_eth, market, soldmarket, amount as bittrex_amount, "0" as poloniex_amount, "0" as manual_amount, (amount * bought_at * btc_price_bought_usd) as total, (amount * sold_at * btc_price_sold_usd) as total_sold, (amount * bought_at) as total_usdt, (amount * sold_at) as total_usdt_sold, (amount * bought_at / btc_price_bought_usd) as total_usdt_btc, (amount * sold_at / btc_price_sold_usd) as total_usdt_btc_sold, (amount * bought_at) as total_eth, (amount * sold_at) as total_eth_sold, (amount * bought_at / btc_price_bought_eth) as total_eth_btc, (amount * sold_at / btc_price_sold_eth) as total_eth_btc_sold
        From bittrex_investments
        where userid = '.Auth::user()->id.'
        Union All
        Select currency, amount, bought_at, sold_at, btc_price_bought_usd, btc_price_sold_usd, btc_price_bought_eth, btc_price_sold_eth, market, sold_market as soldmarket, "0" as bittrex_amount, "0" as poloniex_amount, amount as manual_amount, (amount * bought_at * btc_price_bought_usd) as total, (amount * sold_at * btc_price_sold_usd) as total_sold, (amount * bought_at) as total_usdt, (amount * sold_at) as total_usdt_sold, (amount * bought_at / btc_price_bought_usd) as total_usdt_btc, (amount * sold_at / btc_price_sold_usd) as total_usdt_btc_sold, (amount * bought_at) as total_eth, (amount * sold_at) as total_eth_sold, (amount * bought_at / btc_price_bought_eth) as total_eth_btc, (amount * sold_at / btc_price_sold_eth) as total_eth_btc_sold
        from manual_investments
        where userid = '.Auth::user()->id.'
        ) x
        Group by currency, market, soldmarket
        '));
      } else {
        $summed = Cache::remember('investments'.Auth::user()->id, 60, function()
        {
          $poloniex = PoloInvestment::where([['userid', '=', Auth::user()->id]])->SelectRaw('*, "Poloniex" as exchange, comment as note');
          $bittrex = BittrexInvestment::where([['userid', '=', Auth::user()->id]])->SelectRaw('*, "Bittrex" as exchange, comment as note');
          return ManualInvestment::where([['userid', '=', Auth::user()->id]])->SelectRaw('*, "Manual" as exchange, comment as note')->union($poloniex)->union($bittrex)->get();
        });

      }


      return view('coins.investments3', ['btc' => InvestmentController::btcPrice(), 'networth' => Auth::user()->getNetWorthNew(Auth::user()->api), 'minings' => Mining::where('userid', Auth::user()->id)->get(), 'multiplier' => Auth::user()->getMultiplier(), 'p_investments' => $summed,'balances' => Balance::where('userid', Auth::user()->id)->get(), 'activeworth' => Auth::user()->getActiveWorth(Auth::user()->api)]);


    }

    //Here is manual investment stuff

    //New function 2017.08.07
    public function addInvestment2(Request $request)
    {
      //Variables
      $currency = Crypto::where('name', $request->get('coin'))->first();
      $user = Auth::user();
      Cache::forget('investments'.$user->id);
      Cache::forget('m_investments'.$user->id);
      Cache::forget('balances'.$user->id);
      //inputs
      $fiat = $request->get('priceinputeditcurrency');
      $priceinput = $request->get('priceinput');

      //Calculation start
      if($fiat != "BTC" && $fiat != "USD")
      {
        $multiplier = Multiplier::where('currency', $fiat)->select('price')->first()->price;
      }
      //Remove commas replace with dots.
      if(strpos($request->get('bought_at'), ',') && strpos($request->get('bought_at'), '.'))
      {
        $paid = str_replace(',', '', $request->get('bought_at'));
      } else {
        $paid = str_replace(',', '.', $request->get('bought_at'));
      }

      if(strpos($request->get('amount'), ',') && strpos($request->get('amount'), '.'))
      {
        $amount = str_replace(',', '', $request->get('amount'));
      } else {
        $amount = str_replace(',', '.', $request->get('amount'));
      }
      //date
      $date = $request->get('date');

      //validation

      $rules = [
          'coin' => 'required',
          'amount' => 'required',
          'date' => 'required|date',
          'bought_at' => 'required'
          ];

          $messages = [
              'coin.required' => 'You must enter a coin available from the list.',
              'amount.required'  => 'You must enter an amount of coins bought.',
              'date.date'  => 'You must enter a correct date in the date field',
              'date.required'  => 'You must enter the date you made the investment.',
              'bought_at.required'  => 'You must enter a paid amount for the coin.',
          ];

          $validator = Validator::make($request->all(), $rules, $messages);
          $errors = $validator->errors();
          if($validator->fails()){
              if($errors->first('coin')){
              Alert::error($errors->first('coin'), 'Investment failed');
              } elseif($errors->first('bought_at')) {
              Alert::error($errors->first('bought_at'), 'Investment failed');

              } elseif($errors->first('amount')) {
              Alert::error($errors->first('amount'), 'Investment failed');

              } elseif($errors->first('date')) {
              Alert::error($errors->first('date'), 'Investment failed');

              }
              return Redirect::back();
          }

          // Manual validator if first fails.
          if(!$request->get('amount') || $request->get('amount') <= 0.000001){
                  Alert::error('You must enter an amount of coins bought.', 'Investment failed');
                  return Redirect::back();
          }
          if($request->get('amount') && !is_numeric($amount))
          {
            Alert::error('You must enter a numeric value in the amount field.', 'Investment failed');
            return Redirect::back();
          }
          if($request->get('bought_at') && !is_numeric($paid))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
            return Redirect::back();
          }

          //Start creation
          if($date != date('Y-m-d')){
          $client = new \GuzzleHttp\Client();
          try{
              $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP,ETH,USDT&ts='.strtotime($date).'&extraParams=Altpocket');
              $response = $res->getBody();
              $prices = json_decode($response, true);
              $btc_usd = 0;
              $btc_eur = 0;
              $btc_gbp = 0;


              foreach($prices['BTC'] as $key => $price){
                  if($key == "USD")
                  {
                    $btc_usd = $price;
                  } elseif($key == "EUR")
                  {
                    $btc_eur = $price;
                  } elseif($key == "GBP")
                  {
                    $btc_gbp = $price;
                  } elseif($key == "ETH")
                  {
                    $btc_eth = $price;
                  } elseif($key == "USDT")
                  {
                    $btc_usdt = $price;
                  }
              }
              }  catch (\GuzzleHttp\Exception\ClientException $e) {
                  $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
                  $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
                  $btc_gbp = 0;
                  $btc_eth = 0;
                  $btc_usdt = 0;
                  }
              } else {
                  $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
                  $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
                  $btc_gbp = 0;
                  $btc_eth = 0;
                  $btc_usdt = 0;
              }


          if($currency)
          {
            $investment = new ManualInvestment;
            $investment->userid = $user->id;
            $investment->currency = $currency->symbol;
            $investment->date_bought = $date;

            if($priceinput == "paidper")
            {
              if($fiat == "USD")
              {
                $investment->bought_at = $paid / ($btc_usd * 1);
              } elseif($fiat == "BTC") {
                $investment->bought_at = $paid;
              } else {
                $investment->bought_at = $paid / ($btc_usd * Multiplier::where('currency', $fiat)->first()->price);
              }
            }
            elseif($priceinput == "totalpaid")
            {
              if($fiat == "USD")
              {
                $investment->bought_at = ($paid / $amount) / ($btc_usd * 1);
              } elseif($fiat == "BTC") {
                $investment->bought_at = ($paid / $amount);
              } else {
                $investment->bought_at = ($paid / $amount) / ($btc_usd * Multiplier::where('currency', $fiat)->first()->price);
              }
            }

            $investment->amount = $amount;
            $investment->bought_for = $amount * $investment->bought_at;
            //Use this to see if you still made profit when BTC goes up
            $investment->bought_for_usd = $investment->bought_for * $btc_usd;


            //BTC prices
            $investment->btc_price_bought_usd = $btc_usd;
            $investment->btc_price_bought_eur = $btc_eur;
            $investment->btc_price_bought_gbp = $btc_gbp;
            $investment->btc_price_bought_eth = $btc_eth;
            $investment->btc_price_bought_usdt = $btc_usdt;
            $investment->type = "Investment";


            $investment->save();

            if(Balance::where([['userid', '=', $user->id], ['currency', '=', $currency->symbol], ['exchange', '=', 'Manual']])->exists())
            {
              $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $currency->symbol], ['exchange', '=', 'Manual']])->first();
              $balance->amount += $amount;
              $balance->save();
            } else {
              $balance = new Balance;
              $balance->userid = $user->id;
              $balance->currency = $currency->symbol;
              $balance->amount = $amount;
              $balance->exchange = "Manual";
              $balance->save();
            }
            Alert::success('Your investment was successfully added.', 'Investment added');
            return redirect('/investments');

          } else {
            Alert::error('You must enter a coin available from the list.', 'Investment failed');
            return redirect('/investments');
          }


    }

    public function addInvestment(Request $request)
    {
      //Variables
      $currency = Crypto::where('name', $request->get('coin'))->first();
      $user = Auth::user();
      Cache::forget('investments'.$user->id);
      Cache::forget('m_investments'.$user->id);
      Cache::forget('balances'.$user->id);


      //Fix formatting on the inputs
      $bought_btc = str_replace(',', '.', $request->get('bought_at_btc'));
      $bought_usd = str_replace(',', '.', $request->get('bought_at_usd'));
      $bought_eur = str_replace(',', '.', $request->get('bought_at_eur'));
      $bought_aud = str_replace(',', '.', $request->get('bought_at_aud'));
      $total = str_replace(',', '.', $request->get('total'));
      $totalusd = str_replace(',', '.', $request->get('usdtotal'));
      $totaleur = str_replace(',', '.', $request->get('eurtotal'));
      $totalaud = str_replace(',', '.', $request->get('audtotal'));
      $amount = str_replace(',', '.', $request->get('amount'));
      $date = $request->get('date');
      //Validator

      $messages = [
          'coin.required' => 'You must enter a coin available from the list.',
          'amount.required'  => 'You must enter an amount of coins bought.',
          'date.date'  => 'You must enter a correct date in the date field',
          'date.required'  => 'You must enter the date you made the investment.',
          'priceinput.required'  => 'You must select a price input for the investment.',
      ];

      $rules = [
          'coin' => 'required',
          'amount' => 'required',
          'date' => 'required|date',
          'priceinput' => 'required'
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


      //Manual Validator to make sure that the first one didn't fail.

      if(!$request->get('amount') || $request->amount <= 0){
              Alert::error('You must enter an amount of coins bought.', 'Investment failed');
              return Redirect::back();
      }

      if($request->get('priceinput') == "btcper")
      {
        if($request->get('bought_at_btc') && !is_numeric($bought_btc))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('bought_at_btc') != "" && $bought_btc <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }


      }
      elseif($request->get('priceinput') == "usdper")
      {
        if($request->get('bought_at_usd') && !is_numeric($bought_usd))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('bought_at_usd') != "" && $bought_usd <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }

      }
      elseif($request->get('priceinput') == "eurper")
      {
        if($request->get('bought_at_eur') && !is_numeric($bought_eur))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('bought_at_eur') != "" && $bought_eur <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "audper")
      {
        if($request->get('bought_at_aud') && !is_numeric($bought_aud))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('bought_at_aud') != "" && $bought_aud <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "total")
      {
        if($request->get('total') && !is_numeric($total))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('total') != "" && $total <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('total'), 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "usdtotal")
      {
        if($request->get('usdtotal') && !is_numeric($totalusd))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('usdtotal') != "" && $totalusd <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('usdtotal'), 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "eurtotal")
      {
        if($request->get('eurtotal') && !is_numeric($totaleur))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('eurtotal') != "" && $totaleur <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('eurtotal'), 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "audtotal")
      {
        if($request->get('audtotal') && !is_numeric($totalaud))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('audtotal') != "" && $totalaud <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('audtotal'), 'Investment failed');
          return Redirect::back();
        }
      }


      // Start the creation
      if($date != date('Y-m-d')){
      $client = new \GuzzleHttp\Client();
      try{
          $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP&ts='.strtotime($date).'&extraParams=Altpocket');
          $response = $res->getBody();
          $prices = json_decode($response, true);
          $btc_usd = 0;
          $btc_eur = 0;
          $btc_gbp = 0;


          foreach($prices['BTC'] as $key => $price){
              if($key == "USD")
              {
                $btc_usd = $price;
              } elseif($key == "EUR")
              {
                $btc_eur = $price;
              } elseif($key == "GBP")
              {
                $btc_gbp = $price;
              }
          }
          }  catch (\GuzzleHttp\Exception\ClientException $e) {
              $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
              $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
              $btc_gbp = 0;
              }
          } else {
              $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
              $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
              $btc_gbp = 0;
          }


          if($currency)
          {
            $investment = new ManualInvestment;
            $investment->userid = $user->id;
            $investment->currency = $currency->symbol;
            $investment->date_bought = $date;

            //Check what kind of input the user chose
            if($request->get('priceinput') == "btcper")
            {
              $investment->bought_at = $bought_btc;
            }
            elseif($request->get('priceinput') == "usdper")
            {
              $investment->bought_at = $bought_usd / $btc_usd;
            }
            elseif($request->get('priceinput') == "eurper")
            {
              $investment->bought_at = $bought_eur / $btc_eur;
            }
            elseif($request->get('priceinput') == "audper")
            {
              $investment->bought_at = $bought_aud / ($btc_usd * Multiplier::where('currency', 'AUD')->first()->price);
            }
            elseif($request->get('priceinput') == "total")
            {
              $investment->bought_at = $total / $amount;
            }
            elseif($request->get('priceinput') == "usdtotal")
            {
              $investment->bought_at = ($totalusd / $amount) / $btc_usd;
            }
            elseif($request->get('priceinput') == "eurtotal")
            {
              $investment->bought_at = ($totaleur / $amount) / ($btc_usd * Multiplier::where('currency', 'EUR')->first()->price);
            }
            elseif($request->get('priceinput') == "audtotal")
            {
              $investment->bought_at = ($totalaud / $amount) / ($btc_usd * Multiplier::where('currency', 'AUD')->first()->price);
            }



            $investment->amount = $amount;
            $investment->bought_for = $amount * $investment->bought_at;
            //Use this to see if you still made profit when BTC goes up
            $investment->bought_for_usd = $investment->bought_for * $btc_usd;


            //BTC prices
            $investment->btc_price_bought_usd = $btc_usd;
            $investment->btc_price_bought_eur = $btc_eur;
            $investment->btc_price_bought_gbp = $btc_gbp;
            $investment->type = "Investment";


            $investment->save();

            $user->invested += $investment->bought_for;
            $user->save();

            if(Balance::where([['userid', '=', $user->id], ['currency', '=', $currency->symbol], ['exchange', '=', 'Manual']])->exists())
            {
              $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $currency->symbol], ['exchange', '=', 'Manual']])->first();
              $balance->amount += $amount;
              $balance->save();
            } else {
              $balance = new Balance;
              $balance->userid = $user->id;
              $balance->currency = $currency->symbol;
              $balance->amount = $amount;
              $balance->exchange = "Manual";
              $balance->save();
            }
            Alert::success('Your investment was successfully added.', 'Investment added');
            return redirect('/investments');
          }
          else
          {
            Alert::error('You must enter a coin available from the list.', 'Investment failed');
            return redirect('/investments');
          }


    }

    public function getInvestment($id){
        $investment = ManualInvestment::where('id', $id)->first();

        return $investment;
    }

    public function editInvestment($id, Request $request)
    {
        //Variables
        $investment = ManualInvestment::where([['id', '=', $id], ['userid', '=', Auth::user()->id]])->first();
        $user = Auth::user();
        Cache::forget('investments'.$user->id);
        Cache::forget('m_investments'.$user->id);
        Cache::forget('balances'.$user->id);




        //Fix formatting on the inputs
        $bought_btc = str_replace(',', '.', $request->get('bought_at_btc'));
        $bought_usd = str_replace(',', '.', $request->get('bought_at_usd'));
        $bought_eur = str_replace(',', '.', $request->get('bought_at_eur'));
        $bought_aud = str_replace(',', '.', $request->get('bought_at_aud'));
        $total = str_replace(',', '.', $request->get('total'));
        $totalusd = str_replace(',', '.', $request->get('usdtotal'));
        $totaleur = str_replace(',', '.', $request->get('eurtotal'));
        $totalaud = str_replace(',', '.', $request->get('audtotal'));
        $amount = str_replace(',', '.', $request->get('amount'));
        $date = $request->get('date');

        //Validator
        //Validator

        $messages = [
            'coin.required' => 'You must enter a coin available from the list.',
            'amount.required'  => 'You must enter an amount of coins bought.',
            'date.date'  => 'You must enter a correct date in the date field',
            'date.required'  => 'You must enter the date you made the investment.',
            'priceinput.required'  => 'You must select a price input for the investment.',
        ];

        $rules = [
            'coin' => 'required',
            'amount' => 'required',
            'date' => 'required|date',
            'priceinput' => 'required'
            ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();
        if($validator->fails()){
            if($errors->first('autocomplete_states')){
            Alert::error($errors->first('autocomplete_states'), 'Investment failed');
            return Redirect::back();
            } elseif($errors->first('bought_at')) {
            Alert::error($errors->first('bought_at'), 'Investment failed');
            return Redirect::back();
            } elseif($errors->first('bought_at_usd')) {
            Alert::error($errors->first('bought_at_usd'), 'Investment failed');
            return Redirect::back();
            } elseif($errors->first('amount')) {
            Alert::error($errors->first('amount'), 'Investment failed');
            return Redirect::back();
            } elseif($errors->first('date')) {
            Alert::error($errors->first('date'), 'Investment failed');
            return Redirect::back();
            }
        }


        //Manual Validator to make sure that the first one didn't fail.

        if(!$request->get('amount') || $request->amount <= 0){
                Alert::error('You must enter an amount of coins bought.', 'Investment failed');
                return Redirect::back();
        }

        if($request->get('priceinput') == "btcper")
        {
          if($request->get('bought_at_btc') && !is_numeric($bought_btc))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
            return Redirect::back();
          }

          if($request->get('bought_at_btc') != "" && $bought_btc <= 0)
          {
            Alert::error('You must enter a price greater than 0.', 'Investment failed');
            return Redirect::back();
          }


        }
        elseif($request->get('priceinput') == "usdper")
        {
          if($request->get('bought_at_usd') && !is_numeric($bought_usd))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
            return Redirect::back();
          }

          if($request->get('bought_at_usd') != "" && $bought_usd <= 0)
          {
            Alert::error('You must enter a price greater than 0.', 'Investment failed');
            return Redirect::back();
          }

        }
        elseif($request->get('priceinput') == "eurper")
        {
          if($request->get('bought_at_eur') && !is_numeric($bought_eur))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
            return Redirect::back();
          }

          if($request->get('bought_at_eur') != "" && $bought_eur <= 0)
          {
            Alert::error('You must enter a price greater than 0.', 'Investment failed');
            return Redirect::back();
          }
        }
        elseif($request->get('priceinput') == "audper")
          {
            if($request->get('bought_at_aud') && !is_numeric($bought_aud))
            {
              Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
              return Redirect::back();
            }

            if($request->get('bought_at_aud') != "" && $bought_aud <= 0)
            {
              Alert::error('You must enter a price greater than 0.', 'Investment failed');
              return Redirect::back();
            }
          }
        elseif($request->get('priceinput') == "total")
        {
          if($request->get('total') && !is_numeric($total))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
            return Redirect::back();
          }

          if($request->get('total') != "" && $total <= 0)
          {
            Alert::error('You must enter a price greater than' . $request->get('total'), 'Investment failed');
            return Redirect::back();
          }
        }
        elseif($request->get('priceinput') == "usdtotal")
        {
          if($request->get('usdtotal') && !is_numeric($totalusd))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
            return Redirect::back();
          }

          if($request->get('usdtotal') != "" && $totalusd <= 0)
          {
            Alert::error('You must enter a price greater than' . $request->get('usdtotal'), 'Investment failed');
            return Redirect::back();
          }
        }
        elseif($request->get('priceinput') == "eurtotal")
        {
          if($request->get('eurtotal') && !is_numeric($totaleur))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
            return Redirect::back();
          }

          if($request->get('eurtotal') != "" && $totaleur <= 0)
          {
            Alert::error('You must enter a price greater than' . $request->get('eurtotal'), 'Investment failed');
            return Redirect::back();
          }
        }
        elseif($request->get('priceinput') == "audtotal")
        {
          if($request->get('audtotal') && !is_numeric($totalaud))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
            return Redirect::back();
          }

          if($request->get('audtotal') != "" && $totalaud <= 0)
          {
            Alert::error('You must enter a price greater than' . $request->get('audtotal'), 'Investment failed');
            return Redirect::back();
          }
        }

        if($investment)
        {
          // Start the creation
          if($date != date('Y-m-d')){
          $client = new \GuzzleHttp\Client();
          try{
              $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP&ts='.strtotime($date).'&extraParams=Altpocket');
              $response = $res->getBody();
              $prices = json_decode($response, true);
              $btc_usd = 0;
              $btc_eur = 0;
              $btc_gbp = 0;


              foreach($prices['BTC'] as $key => $price){
                  if($key == "USD")
                  {
                    $btc_usd = $price;
                  } elseif($key == "EUR")
                  {
                    $btc_eur = $price;
                  } elseif($key == "GBP")
                  {
                    $btc_gbp = $price;
                  }
              }
              }  catch (\GuzzleHttp\Exception\ClientException $e) {
                  $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
                  $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
                  $btc_gbp = 0;
                  }
              } else {
                  $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
                  $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
                  $btc_gbp = 0;
              }

              // Remove The invested amount from the user
              $user->invested -= $investment->bought_for;
              $user->save();

              $investment->date_bought = $date;

              //Check what kind of input the user chose
              if($request->get('priceinput') == "btcper")
              {
                $investment->bought_at = $bought_btc;
              }
              elseif($request->get('priceinput') == "usdper")
              {
                $investment->bought_at = $bought_usd / $btc_usd;
              }
              elseif($request->get('priceinput') == "eurper")
              {
                $investment->bought_at = $bought_eur / $btc_eur;
              }
              elseif($request->get('priceinput') == "audper")
              {
                $investment->bought_at = $bought_aud / ($btc_usd * Multiplier::where('currency', 'AUD')->first()->price);
              }
              elseif($request->get('priceinput') == "total")
              {
                $investment->bought_at = $total / $amount;
              }
              elseif($request->get('priceinput') == "usdtotal")
              {
                $investment->bought_at = ($totalusd / $amount) / $btc_usd;
              }
              elseif($request->get('priceinput') == "eurtotal")
              {
                $investment->bought_at = ($totaleur / $amount) / ($btc_usd * Multiplier::where('currency', 'EUR')->first()->price);
              }
              elseif($request->get('priceinput') == "audtotal")
              {
                $investment->bought_at = ($totalaud / $amount) / ($btc_usd * Multiplier::where('currency', 'AUD')->first()->price);
              }

              $investment->amount = $amount;
              $investment->bought_for = $amount * $investment->bought_at;
              //Use this to see if you still made profit when BTC goes up
              $investment->bought_for_usd = $investment->bought_for * $btc_usd;


              //BTC prices
              $investment->btc_price_bought_usd = $btc_usd;
              $investment->btc_price_bought_eur = $btc_eur;
              $investment->btc_price_bought_gbp = $btc_gbp;
              $investment->type = "Investment";
              $investment->save();

              //Balance calculation
              InvestmentController::calculateBalance($investment->currency);

              //Add back the invested amount.
              $user->invested += $investment->bought_for;
              $user->save();

              //Send user back
              Alert::success('Your investment was successfully updated.', 'Investment updated');
              return redirect('/investments');
        } else
        {
          Alert::error('No investment was found.', 'Update failed');
          return redirect('/investments');
        }


    }

    public function calculateBalance($currency)
    {
      $user = Auth::user();
      $investments = ManualInvestment::where([['userid', '=', Auth::user()->id], ['currency', '=', $currency], ['date_sold', '=', null]])->get();
      Cache::forget('balances'.$user->id);

      if(Balance::where([['userid', '=', Auth::user()->id], ['currency', '=', $currency], ['exchange', '=', 'Manual']])->exists())
      {
        $balance = Balance::where([['userid', '=', Auth::user()->id], ['currency', '=', $currency], ['exchange', '=', 'Manual']])->first();
        $balance->amount = 0;
      } else {
        $balance = new Balance;
        $balance->userid = $user->id;
        $balance->currency = $currency;
        $balance->exchange = "Manual";
      }

      foreach($investments as $investment)
      {
        $balance->amount += $investment->amount;
        $balance->save();
      }

      if($balance->amount <= 0)
      {
        $balance->delete();
      }



    }

    public function sellInvestment($id, Request $request)
    {
      //Variables
      $investment = ManualInvestment::where([['id', '=', $id], ['userid', '=', Auth::user()->id]])->first();
      $user = Auth::user();
      $date = $request->get('date');
      Cache::forget('investments'.$user->id);
      Cache::forget('m_investments'.$user->id);
      Cache::forget('balances'.$user->id);





      $sold_btc = str_replace(',', '.', $request->get('sold_at_btc'));
      $sold_usd = str_replace(',', '.', $request->get('sold_at_usd'));
      $sold_eur = str_replace(',', '.', $request->get('sold_at_eur'));
      $sold_aud = str_replace(',', '.', $request->get('sold_at_aud'));
      $total = str_replace(',', '.', $request->get('total'));
      $totalusd = str_replace(',', '.', $request->get('usdtotal'));
      $totaleur = str_replace(',', '.', $request->get('eurtotal'));
      $totalaud = str_replace(',', '.', $request->get('audtotal'));

      //Validator

      $messages = [
          'date.date'  => 'You must enter a correct date in the date field',
          'date.required'  => 'You must enter the date you made the investment.',
          'priceinput.required'  => 'You must select a price input for the investment.',
      ];

      $rules = [
          'date' => 'required|date',
          'priceinput' => 'required'
          ];

      $validator = Validator::make($request->all(), $rules, $messages);
      $errors = $validator->errors();
      if($validator->fails()){
          if($errors->first('autocomplete_states')){
          Alert::error($errors->first('autocomplete_states'), 'Investment failed');
                    return Redirect::back();
          } elseif($errors->first('bought_at')) {
          Alert::error($errors->first('bought_at'), 'Investment failed');
          return Redirect::back();
          } elseif($errors->first('bought_at_usd')) {
          Alert::error($errors->first('bought_at_usd'), 'Investment failed');
          return Redirect::back();
          } elseif($errors->first('amount')) {
          Alert::error($errors->first('amount'), 'Investment failed');
          return Redirect::back();
          } elseif($errors->first('date')) {
          Alert::error($errors->first('date'), 'Investment failed');
          return Redirect::back();
          }

      }


      //Manual Validator to make sure that the first one didn't fail.

      if($request->get('priceinput') == "btcper")
      {
        if($request->get('sold_at_btc') && !is_numeric($sold_btc))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('sold_at_btc') != "" && $sold_btc <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }


      }
      elseif($request->get('priceinput') == "usdper")
      {
        if($request->get('sold_at_usd') && !is_numeric($sold_usd))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('sold_at_usd') != "" && $sold_usd <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }

      }
      elseif($request->get('priceinput') == "eurper")
      {
        if($request->get('sold_at_eur') && !is_numeric($sold_eur))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('sold_at_eur') != "" && $sold_eur <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "audper")
        {
          if($request->get('sold_at_aud') && !is_numeric($sold_aud))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
            return Redirect::back();
          }

          if($request->get('sold_at_aud') != "" && $sold_aud <= 0)
          {
            Alert::error('You must enter a price greater than 0.', 'Investment failed');
            return Redirect::back();
          }
        }
      elseif($request->get('priceinput') == "total")
      {
        if($request->get('total') && !is_numeric($total))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('total') != "" && $total <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('total'), 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "usdtotal")
      {
        if($request->get('usdtotal') && !is_numeric($totalusd))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('usdtotal') != "" && $totalusd <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('usdtotal'), 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "eurtotal")
      {
        if($request->get('eurtotal') && !is_numeric($totaleur))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('eurtotal') != "" && $totaleur <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('eurtotal'), 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "audtotal")
      {
        if($request->get('audtotal') && !is_numeric($totalaud))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('audtotal') != "" && $totalaud <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('audtotal'), 'Investment failed');
          return Redirect::back();
        }
      }


      if($investment)
      {
      $client = new \GuzzleHttp\Client();
      try{
          $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP&ts='.strtotime($date).'&extraParams=Altpocket');
          $response = $res->getBody();
          $prices = json_decode($response, true);
          $btc_usd = 0;
          $btc_eur = 0;
          $btc_gbp = 0;


          foreach($prices['BTC'] as $key => $price){
              if($key == "USD")
              {
                $btc_usd = $price;
              } elseif($key == "EUR")
              {
                $btc_eur = $price;
              } elseif($key == "GBP")
              {
                $btc_gbp = $price;
              }
          }
          }  catch (\GuzzleHttp\Exception\ClientException $e) {
              $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
              $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
              $btc_gbp = 0;
              }

          $investment->date_sold = $date;
          if($request->get('priceinput') == "btcper")
          {
            $investment->sold_at = $sold_btc;
          }
          elseif($request->get('priceinput') == "usdper")
          {
            $investment->sold_at = $sold_usd / $btc_usd;
          }
          elseif($request->get('priceinput') == "eurper")
          {
            $investment->sold_at = $sold_eur / $btc_eur;
          }
          elseif($request->get('priceinput') == "audper")
          {
            $investment->sold_at = $sold_aud / ($btc_usd * Multiplier::where('currency', 'AUD')->first()->price);
          }
          elseif($request->get('priceinput') == "total")
          {
            $investment->sold_at = $total / $investment->amount;
          }
          elseif($request->get('priceinput') == "usdtotal")
          {
            $investment->sold_at = ($totalusd / $investment->amount) / $btc_usd;
          }
          elseif($request->get('priceinput') == "eurtotal")
          {
            $investment->sold_at = ($totaleur / $investment->amount) / ($btc_usd * Multiplier::where('currency', 'EUR')->first()->price);
          }
          elseif($request->get('priceinput') == "audtotal")
          {
            $investment->sold_at = ($totalaud / $investment->amount) / ($btc_usd * Multiplier::where('currency', 'AUD')->first()->price);
          }





          $investment->sold_for = $investment->amount * $investment->sold_at;
          $investment->btc_price_sold_usd = $btc_usd;
          $investment->btc_price_sold_eur = $btc_eur;
          $investment->btc_price_sold_gbp = $btc_gbp;
          //Use this to see if you still made profit when BTC goes up
          $investment->sold_for_usd = $investment->sold_for * $btc_usd;
          $investment->save();

          //Calculate balance
          InvestmentController::calculateBalance($investment->currency);

          // Remove from invested
          $user->invested -= $investment->usd_total;
          $user->save();

          Alert::success('Your investment was successfully marked as sold.', 'Investment sold');
          return redirect('/investments');
        } else
        {
          Alert::error('No investment was found.', 'Sale failed');
          return redirect('/investments');
        }
    }

    public function removeInvestment($id)
    {
        $investment = ManualInvestment::where([['id', '=', $id], ['userid', '=', Auth::user()->id]])->first();
        $user = Auth::user();
        Cache::forget('investments'.$user->id);
        Cache::forget('m_investments'.$user->id);
        Cache::forget('balances'.$user->id);

        if($investment)
        {
          if($investment->sold_at == null)
          {
            $user->invested -= $investment->bought_for;
            if($user->invested <= 0)
            {
              $user->invested = 0;
            }
            $user->save();
          }
        $investment->delete();
        //Calculate balance
        InvestmentController::calculateBalance($investment->currency);

        Alert::success('Your investment was successfully deleted.', 'Investment deleted');
        return redirect('/investments');
      } else
      {
        Alert::error('We could not find any investment to delete!', 'Delete failed');
        return redirect('/investments');
      }
    }

    public function removePoloInvestment($id)
    {
        $investment = PoloInvestment::where([['id', '=', $id], ['userid', '=', Auth::user()->id]])->first();
        $user = Auth::user();
        Cache::forget('investments'.$user->id);
        Cache::forget('p_investments'.$user->id);
        Cache::forget('balances'.$user->id);

        if($investment)
        {
          if($investment->sold_at == null)
          {
            $user->invested -= $investment->bought_for;
            if($user->invested <= 0)
            {
              $user->invested = 0;
            }
            $user->save();
          }
        $investment->delete();
        //Calculate balance
        InvestmentController::calculateBalance($investment->currency);

        Alert::success('Your investment was successfully deleted.', 'Investment deleted');
        return redirect('/investments');
      } else
      {
        Alert::error('We could not find any investment to delete!', 'Delete failed');
        return redirect('/investments');
      }
    }

    public function removeBittrexInvestment($id)
    {
        $investment = BittrexInvestment::where([['id', '=', $id], ['userid', '=', Auth::user()->id]])->first();
        $user = Auth::user();
        Cache::forget('investments'.$user->id);
        Cache::forget('b_investments'.$user->id);
        Cache::forget('balances'.$user->id);

        if($investment)
        {
          if($investment->sold_at == null)
          {
            $user->invested -= $investment->bought_for;
            if($user->invested <= 0)
            {
              $user->invested = 0;
            }
            $user->save();
          }
        $investment->delete();
        //Calculate balance
        InvestmentController::calculateBalance($investment->currency);

        Alert::success('Your investment was successfully deleted.', 'Investment deleted');
        return redirect('/investments');
      } else
      {
        Alert::error('We could not find any investment to delete!', 'Delete failed');
        return redirect('/investments');
      }
    }

    public function addMining(Request $request)
    {
      //Variables
      $currency = Crypto::where('name', $request->get('coin'))->first();
      $user = Auth::user();

      $amount = str_replace(',', '.', $request->get('amount'));
      $date = $request->get('date');

      //Validator

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


      // Manual Validator if the first one fails

      if(!$request->get('amount')){
              Alert::error('You must enter an amount of coins bought.', 'Investment failed');
              return Redirect::back();
      }

      if(!is_numeric($amount)){
              Alert::error('You must enter a numeric value in the amount field.', 'Investment failed');
              return Redirect::back();
      }

      if($amount <= 0) {
              Alert::error('You must enter an amount greater than 0.', 'Investment failed');
              return Redirect::back();
      }


      if($date != date('Y-m-d')){
      $client = new \GuzzleHttp\Client();
      try{
          $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP&ts='.strtotime($date).'&extraParams=Altpocket');
          $response = $res->getBody();
          $prices = json_decode($response, true);
          $btc_usd = 0;
          $btc_eur = 0;
          $btc_gbp = 0;


          foreach($prices['BTC'] as $key => $price){
              if($key == "USD")
              {
                $btc_usd = $price;
              } elseif($key == "EUR")
              {
                $btc_eur = $price;
              } elseif($key == "GBP")
              {
                $btc_gbp = $price;
              }
          }
          }  catch (\GuzzleHttp\Exception\ClientException $e) {
              $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
              $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
              $btc_gbp = 0;
              }
          } else {
              $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
              $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
              $btc_gbp = 0;
          }


          if($currency)
          {
            $mining = new Mining;
            $mining->userid = $user->id;
            $mining->currency = $currency->symbol;
            $mining->date_mined = $date;
            $mining->price_mined = 0;
            $mining->amount = $amount;
            $mining->btc_price_bought_usd = $btc_usd;
            $mining->btc_price_bought_eur = $btc_eur;
            $mining->btc_price_bought_gbp = $btc_gbp;
            $mining->type = "Mining";
            $mining->save();

            //Calculate balance
            InvestmentController::calculateBalance($mining->currency);

            Alert::success('Your mining asset was successfully added.', 'Asset added');
            return redirect('/investments');
          }
          else
          {
            Alert::error('You must enter a coin available from the list.', 'Asset failed');
            return redirect('/investments');
          }





    }

    public function removeMining($id)
    {
        $mining = Mining::where([['id', '=', $id], ['userid', '=', Auth::user()->id]])->first();
        $user = Auth::user();

        if($mining)
        {
        $mining->delete();

        //Calculate balance
        InvestmentController::calculateBalance($mining->currency);
        Alert::success('Your mining was successfully deleted.', 'Mining deleted');
        return redirect('/investments');
      } else
      {
        Alert::error('We could not find any mining to delete!', 'Delete failed');
        return redirect('/investments');
      }


    }

    public function sellMultiple(Request $request)
    {
      //Variables
      $currency = Crypto::where('name', $request->get('coin'))->first();
      $user = Auth::user();
      Cache::forget('investments'.$user->id);
      Cache::forget('m_investments'.$user->id);
      Cache::forget('balances'.$user->id);


      //Fix formatting on the inputs
      $sold_btc = str_replace(',', '.', $request->get('bought_at_btc'));
      $sold_usd = str_replace(',', '.', $request->get('bought_at_usd'));
      $sold_eur = str_replace(',', '.', $request->get('bought_at_eur'));
      $sold_aud = str_replace(',', '.', $request->get('bought_at_aud'));
      $total = str_replace(',', '.', $request->get('total'));
      $totalusd = str_replace(',', '.', $request->get('usdtotal'));
      $totaleur = str_replace(',', '.', $request->get('eurtotal'));
      $totalaud = str_replace(',', '.', $request->get('audtotal'));
      $amount = str_replace(',', '.', $request->get('amount'));
      $amountsafe = str_replace(',', '.', $request->get('amount'));
      $date = $request->get('date');

      //Validator

      $messages = [
          'coin.required' => 'You must enter a coin available from the list.',
          'amount.required'  => 'You must enter an amount of coins bought.',
          'date.date'  => 'You must enter a correct date in the date field',
          'date.required'  => 'You must enter the date you made the investment.',
          'priceinput.required'  => 'You must select a price input for the investment.',
      ];

      $rules = [
          'coin' => 'required',
          'amount' => 'required',
          'date' => 'required|date',
          'priceinput' => 'required'
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


      //Manual Validator to make sure that the first one didn't fail.

      if(!$request->get('amount') || $request->amount <= 0){
              Alert::error('You must enter an amount of coins sold.', 'Investment failed');
              return Redirect::back();
      }

      if($request->get('priceinput') == "btcper")
      {
        if($request->get('bought_at_btc') && !is_numeric($sold_btc))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('bought_at_btc') != "" && $sold_btc <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }


      }
      elseif($request->get('priceinput') == "usdper")
      {
        if($request->get('bought_at_usd') && !is_numeric($sold_usd))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('bought_at_usd') != "" && $sold_usd <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }

      }
      elseif($request->get('priceinput') == "eurper")
      {
        if($request->get('bought_at_eur') && !is_numeric($sold_eur))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('bought_at_eur') != "" && $sold_eur <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "audper")
      {
        if($request->get('bought_at_aud') && !is_numeric($sold_aud))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('bought_at_aud') != "" && $sold_aud <= 0)
        {
          Alert::error('You must enter a price greater than 0.', 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "total")
      {
        if($request->get('total') && !is_numeric($total))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('total') != "" && $total <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('total'), 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "usdtotal")
      {
        if($request->get('usdtotal') && !is_numeric($totalusd))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('usdtotal') != "" && $totalusd <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('usdtotal'), 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "eurtotal")
      {
        if($request->get('eurtotal') && !is_numeric($totaleur))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('eurtotal') != "" && $totaleur <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('eurtotal'), 'Investment failed');
          return Redirect::back();
        }
      }
      elseif($request->get('priceinput') == "audtotal")
      {
        if($request->get('audtotal') && !is_numeric($totalaud))
        {
          Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
          return Redirect::back();
        }

        if($request->get('audtotal') != "" && $totalaud <= 0)
        {
          Alert::error('You must enter a price greater than' . $request->get('audtotal'), 'Investment failed');
          return Redirect::back();
        }
      }


      if($currency)
      {
      // Start the creation
      if($date != date('Y-m-d')){
      $client = new \GuzzleHttp\Client();
      try{
            $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD,EUR,GBP&ts='.strtotime($date).'&extraParams=Altpocket');
            $response = $res->getBody();
            $prices = json_decode($response, true);
            $btc_usd = 0;
            $btc_eur = 0;
            $btc_gbp = 0;


            foreach($prices['BTC'] as $key => $price){
                if($key == "USD")
                {
                  $btc_usd = $price;
                } elseif($key == "EUR")
                {
                  $btc_eur = $price;
                } elseif($key == "GBP")
                {
                  $btc_gbp = $price;
                }
            }
            }  catch (\GuzzleHttp\Exception\ClientException $e) {
                $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
                $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
                $btc_gbp = 0;
                }
            } else {
                $btc_usd = Crypto::where('symbol', 'btc')->first()->price_usd;
                $btc_eur = Crypto::where('symbol', 'btc')->first()->price_eur;
                $btc_gbp = 0;
              }

          $investments = ManualInvestment::where([['userid', '=', $user->id], ['currency', '=', $currency->symbol], ['date_sold', '=', null]])->orderBy('date_bought')->get();

          // Variables for this sale
          $counter = 0;
          $paid = 0;
          $paideach = 0;
          $btc_price_bought_usd = 0;
          $btc_price_bought_eur = 0;
          $btc_price_bought_gbp = 0;
          $datebought = "";


          if($investments)
          {
            foreach($investments as $investment)
            {
              if($amount > 0)
              {
                if($investment->amount <= $amount)
                {
                  // Do stuff
                  $amount -= $investment->amount;
                  $paid += $investment->bought_for;
                  $paideach += $investment->bought_at;
                  $btc_price_bought_usd = $investment->btc_price_bought_usd;
                  $btc_price_bought_eur = $investment->btc_price_bought_eur;
                  $btc_price_bought_gbp = $investment->btc_price_bought_gbp;
                  $counter += 1;
                  $investment->delete();
                } elseif($investment->amount >= $amount)
                {
                  $paid += $investment->bought_at * $amount;
                  $datebought = $investment->date_bought;
                  $btc_price_bought_usd = $investment->btc_price_bought_usd;
                  $btc_price_bought_eur = $investment->btc_price_bought_eur;
                  $btc_price_bought_gbp = $investment->btc_price_bought_gbp;

                  $paideach += $investment->bought_at;
                  $investment->bought_for = $investment->bought_at * ($investment->amount - $amount);
                  $investment->bought_for_usd = ($investment->bought_for_usd / $investment->amount) * ($investment->amount - $amount);
                  $investment->amount -= $amount;
                  $investment->save();

                  $counter += 1;

                  $amount = 0;
                }
              }
            }
            // Lets make the new Sale

            if($counter != 0)
            {
              $i = new ManualInvestment;
              $i->userid = $user->id;
              $i->currency = $currency->symbol;
              $i->date_bought = date('Y-m-d', strtotime($datebought));
              $i->date_sold = $date;
              $i->bought_at = ($paideach / $counter);
              $i->amount = $amountsafe - $amount;
              if($request->get('priceinput') == "btcper")
              {
                $i->sold_at = $sold_btc;
              }
              elseif($request->get('priceinput') == "usdper")
              {
                $i->sold_at = $sold_usd / $btc_usd;
              }
              elseif($request->get('priceinput') == "eurper")
              {
                $i->sold_at = $sold_eur / $btc_eur;
              }
              elseif($request->get('priceinput') == "audper")
              {
                $i->sold_at = $sold_aud / ($btc_usd * Multiplier::where('currency', 'AUD')->first()->price);
              }
              elseif($request->get('priceinput') == "total")
              {
                $i->sold_at = $total / $i->amount;
              }
              elseif($request->get('priceinput') == "usdtotal")
              {
                $i->sold_at = ($totalusd / $i->amount) / $btc_usd;
              }
              elseif($request->get('priceinput') == "eurtotal")
              {
                $i->sold_at = ($totaleur / $i->amount) / ($btc_usd * Multiplier::where('currency', 'EUR')->first()->price);
              }
              elseif($request->get('priceinput') == "audtotal")
              {
                $i->sold_at = ($totalaud / $i->amount) / ($btc_usd * Multiplier::where('currency', 'AUD')->first()->price);
              }

              $i->bought_for = $paid;
              $i->bought_for_usd = $paid * $btc_price_bought_usd;
              $i->sold_for = ($i->amount * $i->sold_at);
              $i->sold_for_usd = ($i->amount * $i->sold_at) * $btc_usd;
              $i->btc_price_bought_usd = $btc_price_bought_usd;
              $i->btc_price_bought_eur = $btc_price_bought_eur;
              $i->btc_price_bought_gbp = $btc_price_bought_gbp;
              $i->btc_price_sold_usd = $btc_usd;
              $i->btc_price_sold_eur = $btc_eur;
              $i->btc_price_sold_gbp = $btc_gbp;
              $i->type = "Manual";
              $i->save();

            } else
            {
              Alert::error('You had no investments to sell.', 'Sell failed');
              return redirect('/investments');
            }


            //Calculate balance
            InvestmentController::calculateBalance($currency->symbol);

            Alert::success('Your investments was successfully marked as sold.', 'Investments sold');
            return redirect('/investments');



          }


        }


    }

    public function selectColor($coin, Request $request)
    {
      $balances = Balance::where([['userid', '=', Auth::user()->id], ['currency', '=', $coin]])->get();

      $rules = [
          'color' => 'hexcolor',
          ];

      $validator = Validator::make($request->all(), $rules);
      $errors = $validator->errors();
      if($validator->fails()){
          Alert::error('You must enter an valid HEX color!', 'Color selection failed.');
          return Redirect::back();
      } else {

      foreach($balances as $balance)
      {
        $balance->color = $request->get('color');
        $balance->save();
      }

      return Redirect::back();
    }
    }

    public function writeNote($exchange, $id, Request $request)
    {
      $user = Auth::user();
      Cache::forget('investments'.$user->id);
      if($exchange == "Poloniex")
      {
        $investment = PoloInvestment::where('id', $id)->first();
        if($investment->userid == Auth::user()->id)
        {
          $investment->comment = $request->get('comment');
          $investment->save();
          return Redirect::back();
        } else {
          return Redirect::back();
        }
      } elseif($exchange == "Bittrex")
      {
        $investment = BittrexInvestment::where('id', $id)->first();
        if($investment->userid == Auth::user()->id)
        {
          $investment->comment = $request->get('comment');
          $investment->save();
          return Redirect::back();
        } else {
          return Redirect::back();
        }
      } elseif($exchange == "Manual")
      {
        $investment = ManualInvestment::where('id', $id)->first();
        if($investment->userid == Auth::user()->id)
        {
          $investment->comment = $request->get('comment');
          $investment->save();
          return Redirect::back();
        } else {
          return Redirect::back();
        }
      }
    }

    public function makePrivate($exchange, $id)
    {
      $user = Auth::user();
      Cache::forget('investments'.$user->id);
      if($exchange == "Poloniex")
      {
        $investment = PoloInvestment::where('id', $id)->first();
        if($investment->userid == Auth::user()->id)
        {
          if($investment->private == 0)
          {
            $investment->private = 1;
          } else {
            $investment->private = 0;
          }
          $investment->save();
          return Redirect::back();
        } else {
          return Redirect::back();
        }
      }
      elseif($exchange == "Bittrex")
      {
        $investment = BittrexInvestment::where('id', $id)->first();
        if($investment->userid == Auth::user()->id)
        {
          if($investment->private == 0)
          {
            $investment->private = 1;
          } else {
            $investment->private = 0;
          }
          $investment->save();
          return Redirect::back();
        } else {
          return Redirect::back();
        }
      }
      elseif($exchange == "Manual")
      {
        $investment = ManualInvestment::where('id', $id)->first();
        if($investment->userid == Auth::user()->id)
        {
          if($investment->private == 0)
          {
            $investment->private = 1;
          } else {
            $investment->private = 0;
          }
          $investment->save();
          return Redirect::back();
        } else {
          return Redirect::back();
        }
      }
    }




}
