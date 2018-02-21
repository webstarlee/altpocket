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
use App\Investment;
use DB;
use Cache;
use App\Historical;
use App\History;

class InvestmentController extends Controller
{

  public function setInvested(Request $request)
  {
    $user = Auth::user();

      $rules = [
          'invested' => 'required|numeric',
          ];

        $messages = [
            'invested.required'  => 'You need to enter a invested value, you may leave it at 0 to let the system calculate your invested.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $errors = $validator->errors();
        if($validator->fails()){
            if($errors->first('invested')) {
            Alert::error($errors->first('invested'), 'Woops..');
            }
            return Redirect::back();
        }

        $user->invested = $request->get('invested');
        $user->save();

        return Redirect::back();
  }

  public function clearCache()
  {
    $user = Auth::user();
    Cache::forget('investments'.$user->id);
    Cache::forget('m_investments'.$user->id);
    Cache::forget('b_investments'.$user->id);
    Cache::forget('p_investments'.$user->id);
    Cache::forget('c_investments'.$user->id);
    Cache::forget('balances'.$user->id);
    Cache::forget('Bittrex-Import-Cache'.$user->id);
    Cache::forget('Polo-Import-Cache:'.$user->id);
    Cache::forget('balances-summed2'.$user->id);
    Cache::forget('minings'.$user->id);
    return Redirect::back();
  }

  public function clearCacheOther($username)
  {
    $user = User::where('username', $username)->first();
    Cache::forget('investments'.$user->id);
    Cache::forget('m_investments'.$user->id);
    Cache::forget('b_investments'.$user->id);
    Cache::forget('p_investments'.$user->id);
    Cache::forget('balances'.$user->id);
    Cache::forget('balances-summed2'.$user->id);
    Cache::forget('Import-Bittrex'.$user->id);
    Cache::forget('Import-Poloniex'.$user->id);
    Cache::forget('isSponsor2'.$user->id);
    Cache::forget('isDonator'.$user->id);
    Cache::forget('isVIP6'.$user->id);
    
    return Redirect::back();
  }

  public function deleteBalance($id)
  {
    $balance = Balance::where('id', $id)->first();
    $user = Auth::user();
    Cache::forget('investments'.$user->id);
    Cache::forget('m_investments'.$user->id);
    Cache::forget('balances'.$user->id);
    Cache::forget('balances-summed2'.$user->id);

    if($balance)
    {
      if($balance->userid == Auth::user()->id)
      {
        $balance->delete();
        Alert::success('You have successfully removed the balance.', 'Balance removed');
        return Redirect::back();
      } else {
        Alert::error('You can not remove someone elses balance.', 'Oops..');
        return Redirect::back();
      }
    } else {
      Alert::error('Could not find your balance!', 'Oops..');
      return Redirect::back();
    }
  }

  //Get coins for manual investment list
  public function getCoins()
  {
    $cryptos = Cache::remember('cryptos', 30, function()
    {
      return Crypto::select('name')->get();
    });

    return $cryptos;
  }

  //Get coins for manual investment list2
  public function getCoins2()
  {
    $cryptos = Cache::remember('cryptos2', 30, function()
    {
      return Crypto::select('name', 'symbol')->get();
    });

    return json_encode($cryptos);
  }


  public function addSource(Request $request)
  {
    if(!$request->get('sourcetype')) {
      return Redirect::back();
    }
    if($request->get('sourcetype') == "Poloniex" || $request->get('sourcetype') == "Bittrex" || $request->get('sourcetype') == "HitBTC")
    {
      Cache::forget('hasPoloKey2:'.Auth::user()->id);
      Cache::forget('hasBittrexKey2:'.Auth::user()->id);
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
      if(isSet($balance['result']))
      {
        $amount = $balance['result'] / 1000000000000000000;

        $balance = new Balance;
        $balance->userid = Auth::user()->id;
        $balance->exchange = "Ethereum";
        $balance->currency = "ETH";
        $balance->amount = $amount;
        $balance->save();
        Cache::forget('balances-summed2'.$key->userid);
        Cache::forget('balances'.$key->userid);
      } else {
        $key->delete();
        Alert::error('No balance found using your address', 'Oops..');
        return redirect('/investments');
      }
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

      if($balance['status'] != false)
      {
      $amount = $balance['data'];
      $balance = new Balance;
      $balance->userid = Auth::user()->id;
      $balance->exchange = "Nanopool";
      $balance->currency = "ETH";
      $balance->amount = $amount;
      $balance->save();
      Cache::forget('balances-summed2'.$key->userid);
      Cache::forget('balances'.$key->userid);
    } else {
      $key->delete();
      Alert::error('No balance found using your key.', 'Oops..');
      return redirect('/investments');
    }
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
      Cache::forget('balances-summed2'.$key->userid);
      Cache::forget('balances'.$key->userid);
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
      Cache::forget('balances-summed2'.$key->userid);
      Cache::forget('balances'.$key->userid);
    }
    Alert::success('Your source has successfully been added.', 'Source added');
    if($key->type == "Exchange")
    {
      return redirect('/investments')->with('import', ['true']);
    } else {
      return redirect('/investments');
    }
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
          if($balance){
            $balance->delete();
            Cache::forget('balances'.$key->userid);
            Cache::forget('balances-summed2'.$key->userid);
          }
        }
        $key->delete();
        Cache::forget('hasPoloKey2:'.Auth::user()->id);
        Cache::forget('hasBittrexKey2:'.Auth::user()->id);
        Cache::forget('hasCoinbaseKey2:'.Auth::user()->id);
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
          if($symbol == "BQX")
          {
            $crypto['name'] = "Ethos";
            $symbol = "ETHOS";
          }
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
        return PoloInvestment::where([['userid', '=', Auth::user()->id], ['amount', '>', '0']])->get();
      });

      $b_investments = Cache::remember('b_investments'.Auth::user()->id, 60, function()
      {
        return BittrexInvestment::where([['userid', '=', Auth::user()->id], ['amount', '>', '0']])->get();
      });

      $m_investments = Cache::remember('m_investments'.Auth::user()->id, 60, function()
      {
        return ManualInvestment::where([['userid', '=', Auth::user()->id], ['amount', '>', '0']])->get();
      });



      return view('coins.investments', ['btc' => InvestmentController::btcPrice(), 'networth' => Auth::user()->getNetWorthNew(Auth::user()->api), 'minings' => Mining::where('userid', Auth::user()->id)->get(), 'multiplier' => Auth::user()->getMultiplier(), 'investments' => $m_investments, 'p_investments' => $p_investments, 'b_investments' => $b_investments, 'balances' => Balance::where([['userid', '=', Auth::user()->id], ['amount', '>', '0.00001']])->get()]);
    }

    // View Your Investments
    public function viewInvestments2()
    {

      if(Auth::user()->summed == 1)
      {
        $summed = DB::select(DB::raw('
        select currency, sum(amount) as amount, sum(bought_at * amount)/sum(amount) as bought_at, sum(sold_at * amount)/sum(amount) as sold_at, avg(btc_price_bought_usd) as btc_price_bought_usd, avg(btc_price_bought_eth) as btc_price_bought_eth, avg(btc_price_sold_usd) as btc_price_sold_usd, avg(btc_price_sold_eth) as btc_price_sold_eth, market, soldmarket, sum(coinbase_amount) as coinbase_amount, sum(bittrex_amount) as bittrex_amount, sum(poloniex_amount) as poloniex_amount, sum(manual_amount) as manual_amount, sum(total) as total, sum(total_usdt) as total_usdt, sum(total_usdt_btc) as total_usdt_btc, sum(total_eur) as total_eur, sum(total_eur_btc) as total_eur_btc, sum(total_eth) as total_eth, sum(total_eth_btc) as total_eth_btc, sum(total_sold) as total_sold, sum(total_usdt_sold) as total_usdt_sold, sum(total_usdt_btc_sold) as total_usdt_btc_sold, sum(total_eth_sold) as total_eth_sold, sum(total_eth_btc_sold)
        FROM
        (
        Select currency, amount, bought_at, sold_at, btc_price_bought_usd, btc_price_sold_usd, btc_price_bought_eth, btc_price_sold_eth, market, soldmarket, "0" as coinbase_amount, "0" as bittrex_amount, amount as poloniex_amount, "0" as manual_amount, (amount * bought_at * btc_price_bought_usd) as total, (amount * sold_at * btc_price_sold_usd) as total_sold, (amount * bought_at) as total_usdt, (amount * sold_at) as total_usdt_sold, (amount * bought_at / btc_price_bought_usd) as total_usdt_btc, (amount * sold_at / btc_price_sold_usd) as total_usdt_btc_sold, (amount * bought_at) as total_eur, (amount * bought_at / btc_price_bought_eur) as total_eur_btc, (amount * bought_at) as total_eth, (amount * sold_at) as total_eth_sold, (amount * bought_at / btc_price_bought_eth) as total_eth_btc, (amount * sold_at / btc_price_sold_eth) as total_eth_btc_sold
        From polo_investments
        where userid = '.Auth::user()->id.'
        Union All
        Select currency, amount, bought_at, sold_at, btc_price_bought_usd, btc_price_sold_usd, btc_price_bought_eth, btc_price_sold_eth, market, soldmarket, "0" as coinbase_amount, amount as bittrex_amount, "0" as poloniex_amount, "0" as manual_amount, (amount * bought_at * btc_price_bought_usd) as total, (amount * sold_at * btc_price_sold_usd) as total_sold, (amount * bought_at) as total_usdt, (amount * sold_at) as total_usdt_sold, (amount * bought_at / btc_price_bought_usd) as total_usdt_btc, (amount * sold_at / btc_price_sold_usd) as total_usdt_btc_sold, (amount * bought_at) as total_eur, (amount * bought_at / btc_price_bought_eur) as total_eur_btc, (amount * bought_at) as total_eth, (amount * sold_at) as total_eth_sold, (amount * bought_at / btc_price_bought_eth) as total_eth_btc, (amount * sold_at / btc_price_sold_eth) as total_eth_btc_sold
        From bittrex_investments
        where userid = '.Auth::user()->id.'
        Union All
        Select currency, amount, bought_at, sold_at, btc_price_bought_usd, btc_price_sold_usd, btc_price_bought_eth, btc_price_sold_eth, market, sold_market as soldmarket, "0" as coinbase_amount, "0" as bittrex_amount, "0" as poloniex_amount, amount as manual_amount, (amount * bought_at * btc_price_bought_usd) as total, (amount * sold_at * btc_price_sold_usd) as total_sold, (amount * bought_at) as total_usdt, (amount * sold_at) as total_usdt_sold, (amount * bought_at / btc_price_bought_usd) as total_usdt_btc, (amount * sold_at / btc_price_sold_usd) as total_usdt_btc_sold, (amount * bought_at) as total_eur, (amount * bought_at / btc_price_bought_eur) as total_eur_btc, (amount * bought_at) as total_eth, (amount * sold_at) as total_eth_sold, (amount * bought_at / btc_price_bought_eth) as total_eth_btc, (amount * sold_at / btc_price_sold_eth) as total_eth_btc_sold
        from manual_investments
        where userid = '.Auth::user()->id.' and amount > 0
        Union All
        Select currency, amount, bought_at, sold_at, btc_price_bought_usd, btc_price_sold_usd, btc_price_bought_eth, btc_price_sold_eth, market, soldmarket,amount as coinbase_amount, "0" as bittrex_amount, "0" as poloniex_amount, "0" as manual_amount, (amount * bought_at * btc_price_bought_usd) as total, (amount * sold_at * btc_price_sold_usd) as total_sold, (amount * bought_at) as total_usdt, (amount * sold_at) as total_usdt_sold, (amount * bought_at / btc_price_bought_usd) as total_usdt_btc, (amount * sold_at / btc_price_sold_usd) as total_usdt_btc_sold, (amount * bought_at) * (btc_price_bought_usd / btc_price_bought_eur) as total_eur, (amount * bought_at / btc_price_bought_eur) as total_eur_btc, (amount * bought_at) as total_eth, (amount * sold_at) as total_eth_sold, (amount * bought_at / btc_price_bought_eth) as total_eth_btc, (amount * sold_at / btc_price_sold_eth) as total_eth_btc_sold
        from investments
        where userid = '.Auth::user()->id.' and amount > 0 and exchange = "Test"
        ) x
        Group by currency, market, soldmarket, sold_at
        '));

        $balance = Cache::remember('balances'.Auth::user()->id, 60, function()
        {
          return Balance::where([['userid', '=', Auth::user()->id], ['amount', '>', '0.00001']])->get();
        });

      } else {
        $summed = Cache::remember('investments'.Auth::user()->id, 60, function()
        {
          $poloniex = PoloInvestment::where([['userid', '=', Auth::user()->id], ['amount', '>', 0]])->SelectRaw('*, "Poloniex" as exchange, comment as note');
          $bittrex = BittrexInvestment::where([['userid', '=', Auth::user()->id], ['amount', '>', 0]])->SelectRaw('*, "Bittrex" as exchange, comment as note');
          $coinbase = Investment::where([['userid', '=', Auth::user()->id], ['amount', '>', 0]])->SelectRaw('*, comment as note');
          return ManualInvestment::where([['userid', '=', Auth::user()->id], ['amount', '>', 0]])->SelectRaw('*, "Manual" as exchange, comment as note')->union($poloniex)->union($bittrex)->union($coinbase)->get();
        });

        $balance = Cache::remember('balances'.Auth::user()->id, 60, function()
        {
          return Balance::where([['userid', '=', Auth::user()->id], ['amount', '>', '0.00001']])->get();
        });

      }

      return view('coins.investments3', ['networth' => Auth::user()->getNetWorthNew(Auth::user()->api), 'minings' => Mining::where('userid', Auth::user()->id)->get(), 'multiplier' => Auth::user()->getMultiplier(), 'p_investments' => $summed,'balances' => $balance, 'activeworth' => Auth::user()->getActiveWorth(Auth::user()->api)]);


    }

    public function viewPortfolio()
    {
      $summed = Cache::remember('investments'.Auth::user()->id, 60, function()
      {
        $poloniex = PoloInvestment::where([['userid', '=', Auth::user()->id], ['amount', '>', 0]])->SelectRaw('*, "Poloniex" as exchange, comment as note');
        $bittrex = BittrexInvestment::where([['userid', '=', Auth::user()->id], ['amount', '>', 0]])->SelectRaw('*, "Bittrex" as exchange, comment as note');
        $coinbase = Investment::where([['userid', '=', Auth::user()->id], ['amount', '>', 0]])->SelectRaw('*, comment as note');
        return ManualInvestment::where([['userid', '=', Auth::user()->id], ['amount', '>', 0]])->SelectRaw('*, "Manual" as exchange, comment as note')->union($poloniex)->union($bittrex)->union($coinbase)->get();
      });

      $balance = Cache::remember('balances'.Auth::user()->id, 60, function()
      {
        return Balance::where([['userid', '=', Auth::user()->id], ['amount', '>', '0.00001']])->get();
      });

      $minings = Cache::remember('minings'.Auth::user()->id, 60, function()
      {
        return Mining::where('userid', Auth::user()->id)->get();
      });

      return view('coins.index', ['btc' => InvestmentController::btcPrice(), 'networth' => Auth::user()->getNetWorthNew(Auth::user()->api, Auth::user()->getMultiplier(), Auth::user()->getFiat()), 'minings' => $minings, 'multiplier' => Auth::user()->getMultiplier(), 'p_investments' => $summed,'balances' => $balance, 'activeworth' => Auth::user()->getActiveWorth(Auth::user()->api)]);

    }

    public function adminInvestments($id)
    {
      $poloniex = PoloInvestment::where([['userid', '=', $id], ['amount', '>', 0]])->SelectRaw('*, "Poloniex" as exchange, comment as note');
      $bittrex = BittrexInvestment::where([['userid', '=', $id], ['amount', '>', 0]])->SelectRaw('*, "Bittrex" as exchange, comment as note');
      $coinbase = Investment::where([['userid', '=', $id], ['amount', '>', 0]])->SelectRaw('*, comment as note');
      $summed = ManualInvestment::where([['userid', '=', $id], ['amount', '>', 0]])->SelectRaw('*, "Manual" as exchange, comment as note')->union($poloniex)->union($bittrex)->union($coinbase)->get();

      $balance = Balance::where([['userid', '=', $id], ['amount', '>', '0.00001']])->get();



    return view('coins.investments3', ['btc' => InvestmentController::btcPrice(), 'networth' => Auth::user()->getNetWorthNew(Auth::user()->api), 'minings' => Mining::where('userid', Auth::user()->id)->get(), 'multiplier' => Auth::user()->getMultiplier(), 'p_investments' => $summed,'balances' => $balance, 'activeworth' => Auth::user()->getActiveWorth(Auth::user()->api)]);

    }
    //Here is manual investment stuff

    //New function 2017.08.07
    public function sellInvestment2(Request $request, $id)
    {
      // Variables
      $user = Auth::user();
      Cache::forget('investments'.$user->id);
      Cache::forget('m_investments'.$user->id);
      Cache::forget('balances'.$user->id);
      Cache::forget('balances-summed2'.$user->id);
      //inputs
      $fiat = $request->get('priceinputeditcurrency');
      $priceinput = $request->get('priceinput');
      $amount = $request->get('amount');

      //Calculation start
      if($fiat != "BTC" && $fiat != "USD" && $fiat != "ETH")
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
          'amount' => 'required',
          'date' => 'required|date',
          'bought_at' => 'required'
          ];

          $messages = [
              'amount.required'  => 'You must enter an amount of coins bought.',
              'date.date'  => 'You must enter a correct date in the date field',
              'date.required'  => 'You must enter the date you made the investment.',
              'bought_at.required'  => 'You must enter a paid amount for the coin.',
          ];

          $validator = Validator::make($request->all(), $rules, $messages);
          $errors = $validator->errors();
          if($validator->fails()){
              if($errors->first('bought_at')) {
              Alert::error($errors->first('bought_at'), 'Investment failed');

              } elseif($errors->first('date')) {
              Alert::error($errors->first('date'), 'Investment failed');

              } elseif($errors->first('amount')) {
                Alert::error($errors->first('amount'), 'Investment failed');

             }
              return Redirect::back();
          }

          // Manual validator if first fails.
          if(!$request->get('bought_at') || $request->get('bought_at') <= 0.0000001 || $paid <= 0.000001){
                  Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
                  return Redirect::back();
          }
          if($request->get('bought_at') && !is_numeric($paid))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
            return Redirect::back();
          }
          if(!$request->get('amount') || $request->get('amount') <= 0.000001 || $amount <= 0.000001){
                  Alert::error('You must enter an amount of coins bought.', 'Investment failed');
                  return Redirect::back();
          }
          if($request->get('amount') && !is_numeric($amount))
          {
            Alert::error('You must enter a numeric value in the amount field.', 'Investment failed');
            return Redirect::back();
          }

          //Now lets start editing the investment
          $investment = ManualInvestment::where([['id', '=', $id], ['userid', '=', $user->id]])->first();


          if($investment)
          {
            //Start creation
              if($amount >= $investment->amount)
              {
                $historical = History::getHistorical($request->get('date'));
                $btc_usd = $historical->USD;
                $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
                $btc_gbp = 0;
                $btc_usdt = $historical->USD;
                $btc_eth = $historical->ETH;



                if($fiat == "ETH")
                {
                  $investment->sold_market = "ETH";
                }
                $investment->date_sold = $date;

                if($priceinput == "paidper")
                {
                  if($fiat == "USD")
                  {
                    $investment->sold_at = $paid / ($btc_usd * 1);
                  } elseif($fiat == "BTC" || $fiat == "ETH") {
                    $investment->sold_at = $paid;
                  } else {
                    $investment->sold_at = $paid / ($btc_usd * Multiplier::where('currency', $fiat)->first()->price);
                  }
                }
                elseif($priceinput == "totalpaid")
                {
                  if($fiat == "USD")
                  {
                    $investment->sold_at = ($paid / $amount) / ($btc_usd * 1);
                  } elseif($fiat == "BTC" || $fiat == "ETH") {
                    $investment->sold_at = ($paid / $amount);
                  } else {
                    $investment->sold_at = ($paid / $amount) / ($btc_usd * Multiplier::where('currency', $fiat)->first()->price);
                  }
                }
                $investment->sold_for = $investment->sold_at * $investment->amount;
                $investment->sold_for_usd = $investment->sold_for * $btc_usd;
                $investment->btc_price_sold_usd = $btc_usd;
                $investment->btc_price_sold_eur = $btc_eur;
                $investment->btc_price_sold_eth = $btc_eth;
                $investment->btc_price_sold_usdt = $btc_usdt;
                $investment->btc_price_sold_gbp = $btc_gbp;
                $investment->save();


                // If the user enabled selltobalance we add a new balance to the user

                if(Auth::user()->selltobalance == 1)
                {

                  if($fiat != "ETH")
                  {
                    $sellto = "BTC";
                  } else {
                    $sellto = "ETH";
                  }



                  $balance = Balance::where([['userid', '=', Auth::user()->id], ['currency', '=', $sellto]])->first();

                  if($balance)
                  {
                    $balance->amount += $investment->amount * $investment->sold_at;
                  } else {
                    $balance = new Balance;
                    $balance->userid = Auth::user()->id;
                    $balance->currency = $sellto;
                    $balance->exchange = "Manual";
                    $balance->amount = $investment->amount * $investment->sold_at;
                  }
                  $balance->save();
                }

                if(Auth::user()->selltoinvestment == 1)
                {

                  if($fiat != "ETH")
                  {
                    $sellto = "BTC";
                  } else {
                    $sellto = "ETH";
                  }

                  $historical = History::getHistorical($request->get('date'));
                  $btc_usd = $historical->USD;
                  $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
                  $btc_gbp = 0;
                  $btc_usdt = $historical->USD;
                  $btc_eth = $historical->ETH;

                  $investment3 = new ManualInvestment;
                  $investment3->userid = Auth::user()->id;
                  $investment3->currency = $sellto;
                  $investment3->market = "BTC";
                  $investment3->date_bought = $date;
                  if($fiat == "ETH")
                  {
                    $investment3->bought_at = 1 / $btc_eth;
                  } else {
                    $investment3->bought_at = 1;
                  }
                  $investment3->amount = $investment->amount * $investment->sold_at;
                  $investment3->bought_for = $investment->bought_at * $investment->amount;
                  $investment3->bought_for_usd = $investment->bought_at * $investment->amount * $btc_usd;
                  $investment3->btc_price_bought_usd = $btc_usd;
                  $investment3->btc_price_bought_eth = $btc_eth;
                  $investment3->btc_price_bought_usdt = $btc_usdt;
                  $investment3->btc_price_bought_eur = $btc_eur;
                  $investment3->btc_price_bought_gbp = $btc_gbp;
                  $investment3->save();


                }


              } else {

                $historical = History::getHistorical($request->get('date'));
                $btc_usd = $historical->USD;
                $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
                $btc_gbp = 0;
                $btc_usdt = $historical->USD;
                $btc_eth = $historical->ETH;


                // Remove from original investment
                $investment->amount -= $amount;
                $investment->bought_for = $investment->bought_at * $investment->amount;
                $investment->bought_for_usd = ($investment->bought_at / ($investment->amount + $amount)) * $investment->amount;
                $investment->save();

                $investment2 = new ManualInvestment;
                $investment2->amount = $amount;
                $investment2->userid = Auth::user()->id;
                $investment2->currency = $investment->currency;
                $investment2->market = $investment->market;
                if($fiat == "ETH")
                {
                  $investment2->sold_market = "ETH";
                }
                $investment2->date_bought = $investment->date_bought;
                $investment2->date_sold = $date;
                $investment2->bought_at = $investment->bought_at;
                $investment2->bought_for = $investment->bought_at * $amount;

                if($priceinput == "paidper")
                {
                  if($fiat == "USD")
                  {
                    $investment2->sold_at = $paid / ($btc_usd * 1);
                  } elseif($fiat == "BTC" || $fiat == "ETH") {
                    $investment2->sold_at = $paid;
                  } else {
                    $investment2->sold_at = $paid / ($btc_usd * Multiplier::where('currency', $fiat)->first()->price);
                  }
                }
                elseif($priceinput == "totalpaid")
                {
                  if($fiat == "USD")
                  {
                    $investment2->sold_at = ($paid / $amount) / ($btc_usd * 1);
                  } elseif($fiat == "BTC" || $fiat == "ETH") {
                    $investment2->sold_at = ($paid / $amount);
                  } else {
                    $investment2->sold_at = ($paid / $amount) / ($btc_usd * Multiplier::where('currency', $fiat)->first()->price);
                  }
                }
                $investment2->sold_for = $investment2->sold_at * $investment2->amount;
                $investment2->sold_for_usd = $investment2->sold_for * $btc_usd;
                $investment2->btc_price_sold_usd = $btc_usd;
                $investment2->btc_price_sold_eur = $btc_eur;
                $investment2->btc_price_sold_eth = $btc_eth;
                $investment2->btc_price_sold_usdt = $btc_usdt;
                $investment2->btc_price_sold_gbp = $btc_gbp;
                $investment2->btc_price_bought_usd = $investment->btc_price_bought_usd;
                $investment2->btc_price_bought_eth = $investment->btc_price_bought_eth;
                $investment2->btc_price_bought_usdt = $investment->btc_price_bought_usdt;
                $investment2->btc_price_bought_eur = $investment->btc_price_bought_eur;
                $investment2->btc_price_bought_gbp = $investment->btc_price_bought_gbp;
                $investment2->save();

                // If the user enabled selltobalance we add a new balance to the user
                if(Auth::user()->selltobalance == 1)
                {

                  if($fiat != "ETH")
                  {
                    $sellto = "BTC";
                  } else {
                    $sellto = "ETH";
                  }



                  $balance = Balance::where([['userid', '=', Auth::user()->id], ['currency', '=', $sellto]])->first();

                  if($balance)
                  {
                    $balance->amount += $investment2->amount * $investment2->sold_at;
                  } else {
                    $balance = new Balance;
                    $balance->userid = Auth::user()->id;
                    $balance->currency = $sellto;
                    $balance->exchange = "Manual";
                    $balance->amount = $investment2->amount * $investment2->sold_at;
                  }
                  $balance->save();
                }

                // If the user enabled selltoinvestment we add a new investment to the user

                if(Auth::user()->selltoinvestment == 1)
                {

                  if($fiat != "ETH")
                  {
                    $sellto = "BTC";
                  } else {
                    $sellto = "ETH";
                  }

                  $historical = History::getHistorical($request->get('date'));
                  $btc_usd = $historical->USD;
                  $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
                  $btc_gbp = 0;
                  $btc_usdt = $historical->USD;
                  $btc_eth = $historical->ETH;

                  $investment3 = new ManualInvestment;
                  $investment3->userid = Auth::user()->id;
                  $investment3->currency = $sellto;
                  $investment3->market = "BTC";
                  $investment3->date_bought = $date;
                  if($fiat == "ETH")
                  {
                    $investment3->bought_at = 1 / $btc_eth;
                  } else {
                    $investment3->bought_at = 1;
                  }
                  $investment3->amount = $investment2->amount;
                  $investment3->bought_for = $investment3->bought_at * $investment3->amount;
                  $investment3->bought_for_usd = $investment->bought_at * $investment3->amount * $btc_usd;
                  $investment3->btc_price_bought_usd = $btc_usd;
                  $investment3->btc_price_bought_eth = $btc_eth;
                  $investment3->btc_price_bought_usdt = $btc_usdt;
                  $investment3->btc_price_bought_eur = $btc_eur;
                  $investment3->btc_price_bought_gbp = $btc_gbp;
                  $investment3->save();


                }
              }



                InvestmentController::calculateBalance($investment->currency);
                Alert::success('Your investment was successfully sold.', 'Investment sold');
                return redirect('/investments');

          } else {
            Alert::error('Could not sell that investment', 'Sell Failed');
            return redirect('/investments');
          }


    }

    public function setBalance(Request $request, $id)
    {
      $user = Auth::user();
      $balance = Balance::where([['userid', '=', $user->id], ['id', '=', $id]])->first();

      Cache::forget('balances'.$user->id);
      Cache::forget('balances-summed2'.$user->id);

      // Manual Validation of the amount
      if(strpos($request->get('amount'), ',') && strpos($request->get('amount'), '.'))
      {
        $amount = str_replace(',', '', $request->get('amount'));
      } else {
        $amount = str_replace(',', '.', $request->get('amount'));
      }

      // Validation
      $rules = [
          'amount' => 'required'
          ];

      $messages = [
          'amount.required'  => 'You must enter an amount of coins bought.'
      ];

      $validator = Validator::make($request->all(), $rules, $messages);
      $errors = $validator->errors();
      if($validator->fails()){
          if($errors->first('amount')) {
          Alert::error($errors->first('amount'), 'Failed');

          }
          return Redirect::back();
      }

      // Manual validator if first fails.
      if(!$request->get('amount') || $request->get('amount') <= 0.000001 || $amount <= 0.000001){
              Alert::error('You must enter an amount of coins bought.', 'Investment failed');
              return Redirect::back();
      }
      if($request->get('amount') && !is_numeric($amount))
      {
        Alert::error('You must enter a numeric value in the amount field.', 'Investment failed');
        return Redirect::back();
      }



      if($balance)
      {
        $balance->amount = $amount;
        $balance->save();
        Alert::success('Your '.$balance->currency.' balance is now set to '.$amount.'!', 'Success');
        return Redirect::Back();
      } else {
        Alert::error('You may not set someone elses balance!', 'Failed');
        return Redirect::Back();
      }

    }

    public function addBalance(Request $request)
    {
      // Variables
      $currency = Crypto::where('id', $request->get('crypto'))->first();
      $user = Auth::user();
      Cache::forget('balances'.$user->id);
      Cache::forget('balances-summed2'.$user->id);

      // Manual Validation of the amount
      if(strpos($request->get('amount'), ',') && strpos($request->get('amount'), '.'))
      {
        $amount = str_replace(',', '', $request->get('amount'));
      } else {
        $amount = str_replace(',', '.', $request->get('amount'));
      }

      // Validation
      $rules = [
          'crypto' => 'required',
          'amount' => 'required'
          ];

      $messages = [
          'crypto.required' => 'You must enter a coin available from the list.',
          'amount.required'  => 'You must enter an amount of coins bought.'
      ];

      $validator = Validator::make($request->all(), $rules, $messages);
      $errors = $validator->errors();
      if($validator->fails()){
          if($errors->first('coin')){
          Alert::error($errors->first('coin'), 'Investment failed');
          } elseif($errors->first('amount')) {
          Alert::error($errors->first('amount'), 'Investment failed');

          }
          return Redirect::back();
      }

      // Manual validator if first fails.
      if(!$request->get('amount') || $request->get('amount') <= 0.000001 || $amount <= 0.000001){
              Alert::error('You must enter an amount of coins bought.', 'Investment failed');
              return Redirect::back();
      }
      if($request->get('amount') && !is_numeric($amount))
      {
        Alert::error('You must enter a numeric value in the amount field.', 'Investment failed');
        return Redirect::back();
      }
      if(!$currency)
      {
        Alert::error('You must select a coin from the list.', 'Failed');
        return Redirect::back();
      }



      // Lets add the balanace

      $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $currency->symbol], ['exchange', '=', 'Manual']])->first();

      if(!$balance)
      {
        $balance = new Balance;
        $balance->userid = $user->id;
        $balance->exchange = "Manual";
        $balance->currency = $currency->symbol;
        $balance->amount = $amount;
        $balance->save();
        Alert::success('A new balance of '.$amount.' '.$currency->symbol.' was created.', 'Success');
      } else {
        $balance->amount += $amount;
        $balance->save();
        Alert::success($amount.' '.$currency->symbol.' was added to your balance!', 'Success');
      }

      return Redirect::back();


    }

    public function setInvestmentBuy($exchange, $id, Request $request)
    {
      $user = Auth::user();
      if($exchange == "Poloniex")
      {
        $investment = PoloInvestment::where([['id', '=', $id], ['userid', '=', $user->id]])->first();
      } elseif($exchange == "Bittrex")
      {
        $investment = BittrexInvestment::where([['id', '=', $id], ['userid', '=', $user->id]])->first();
      } else {
        Alert::warning('You can not edit set prices of this investment yet (Exchange not supported)', 'Error.');
      }

      if($investment)
      {
        if($investment->market == "Deposit")
        {
              Cache::forget('investments'.$user->id);
              Cache::forget('m_investments'.$user->id);
              Cache::forget('b_investments'.$user->id);
              Cache::forget('p_investments'.$user->id);
              Cache::forget('balances'.$user->id);
              Cache::forget('balances-summed2'.$user->id);

              //inputs
              $fiat = $request->get('priceinputeditcurrency');
              $priceinput = $request->get('priceinput');

              //Calculation start
              if($fiat != "BTC" && $fiat != "USD" && $fiat != "ETH")
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

              //date
              $date = $request->get('date');

              //validation

              $rules = [
                  'date' => 'required|date',
                  'bought_at' => 'required'
                  ];

                  $messages = [
                      'date.date'  => 'You must enter a correct date in the date field',
                      'date.required'  => 'You must enter the date you made the investment.',
                      'bought_at.required'  => 'You must enter a paid amount for the coin.',
                  ];

                  $validator = Validator::make($request->all(), $rules, $messages);
                  $errors = $validator->errors();
                  if($validator->fails()){
                      if($errors->first('bought_at')) {
                      Alert::error($errors->first('bought_at'), 'Investment failed');

                      } elseif($errors->first('date')) {
                      Alert::error($errors->first('date'), 'Investment failed');

                      }
                      return Redirect::back();
                  }


                  // Manual validator if first fails.
                  if(!$request->get('bought_at') || $request->get('bought_at') <= 0.0000001 || $paid <= 0.000001){
                          Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
                          return Redirect::back();
                  }
                  if($request->get('bought_at') && !is_numeric($paid))
                  {
                    Alert::error('You must enter a numeric value in the price field.', 'Investment failed');
                    return Redirect::back();
                  }

                  //Start set
                  $historical = History::getHistorical($request->get('date'));
                  $btc_usd = $historical->USD;
                  $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
                  $btc_gbp = 0;
                  $btc_usdt = $historical->USD;
                  $btc_eth = $historical->ETH;


                  $investment->date_bought = $date;
                  if($fiat == "ETH")
                  {
                    $investment->market = "ETH";
                  } else {
                    $investment->market = "BTC";
                  }

                  if($priceinput == "paidper")
                  {
                    if($fiat == "USD")
                    {
                      $investment->bought_at = $paid / ($btc_usd * 1);
                    } elseif($fiat == "BTC" || $fiat == "ETH") {
                      $investment->bought_at = $paid;
                    } else {
                      $investment->bought_at = $paid / ($btc_usd * Multiplier::where('currency', $fiat)->first()->price);
                    }
                  }
                  elseif($priceinput == "totalpaid")
                  {
                    if($fiat == "USD")
                    {
                      $investment->bought_at = ($paid / $investment->amount) / ($btc_usd * 1);
                    } elseif($fiat == "BTC" || $fiat == "ETH") {
                      $investment->bought_at = ($paid / $investment->amount);
                    } else {
                      $investment->bought_at = ($paid / $investment->amount) / ($btc_usd * Multiplier::where('currency', $fiat)->first()->price);
                    }
                  }

                  $investment->bought_for = $investment->amount * $investment->bought_at;
                  //Use this to see if you still made profit when BTC goes up
                  $investment->bought_for_usd = $investment->bought_for * $btc_usd;


                  //BTC prices
                  $investment->btc_price_bought_usd = $btc_usd;
                  $investment->btc_price_bought_eur = $btc_eur;
                  $investment->btc_price_bought_gbp = $btc_gbp;
                  $investment->btc_price_bought_eth = $btc_eth;
                  $investment->btc_price_bought_usdt = $btc_usdt;
                  $investment->type = "Investment";
                  $investment->verified = 0;


                  $investment->save();

                  Alert::success('Your investment now has a buy price!', 'Success');
                  return redirect('/investments');
        }
    }

    }


    public function makeInvestment($currency, Request $request)
    {
      $currency = Crypto::where('symbol', $currency)->first();
      if($currency)
      {
      $user = Auth::user();
      Cache::forget('investments'.$user->id);
      Cache::forget('m_investments'.$user->id);
      Cache::forget('balances'.$user->id);
      Cache::forget('balances-summed2'.$user->id);

      //inputs
      $fiat = $request->get('priceinputeditcurrency');
      $priceinput = $request->get('priceinput');
      $deduction = $request->get('deduct');

      //Calculation start
      if($fiat != "BTC" && $fiat != "USD" && $fiat != "ETH")
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
          'amount' => 'required',
          'date' => 'required|date',
          'bought_at' => 'required'
                ];

          $messages = [
              'amount.required'  => 'You must enter an amount of coins bought.',
              'date.date'  => 'You must enter a correct date in the date field',
              'date.required'  => 'You must enter the date you made the investment.',
              'bought_at.required'  => 'You must enter a paid amount for the coin.',
          ];

          $validator = Validator::make($request->all(), $rules, $messages);
          $errors = $validator->errors();
          if($validator->fails()){
              if($errors->first('bought_at')) {
              Alert::error($errors->first('bought_at'), 'Investment failed');

              } elseif($errors->first('amount')) {
              Alert::error($errors->first('amount'), 'Investment failed');

              } elseif($errors->first('date')) {
              Alert::error($errors->first('date'), 'Investment failed');

              }
              return Redirect::back();
          }

          // Manual validator if first fails.
          if(!$request->get('amount') || $request->get('amount') <= 0.000001 || $amount <= 0.000001){
                  Alert::error('You must enter an amount of coins bought.', 'Investment failed');
                  return Redirect::back();
          }
          if(!$request->get('bought_at') || $request->get('bought_at') <= 0.0000001 || $paid <= 0.000001){
                  Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
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
          $investment = new ManualInvestment;
          $investment->userid = $user->id;
          $investment->currency = $currency->symbol;
          $historical = History::getHistorical($request->get('date'));
          $btc_usd = $historical->USD;
          $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
          $btc_gbp = 0;
          $btc_usdt = $historical->USD;
          $btc_eth = $historical->ETH;
          if($historical->ETH == null)
          {
            $btc_eth = 5;
          }

            $investment->date_bought = $date;
            if($fiat == "ETH")
            {
              $investment->market = "ETH";
            }

            if($priceinput == "paidper")
            {
              if($fiat == "USD")
              {
                $investment->bought_at = $paid / ($btc_usd * 1);
              } elseif($fiat == "BTC" || $fiat == "ETH") {
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
              } elseif($fiat == "BTC" || $fiat == "ETH") {
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
            Alert::success('Your investment was successfully added.', 'Investment added');
            return redirect('/investments');
          } else {
            Alert::success('No token with that symbol was found.', 'Oops..');
            return redirect('/investments');
          }



    }


    public function addInvestment2(Request $request)
    {
      //Variables
      $currency = Crypto::where('id', $request->get('crypto'))->first();
      $user = Auth::user();
      Cache::forget('investments'.$user->id);
      Cache::forget('m_investments'.$user->id);
      Cache::forget('balances'.$user->id);
      Cache::forget('balances-summed2'.$user->id);

      //inputs
      $fiat = $request->get('priceinputeditcurrency');
      $priceinput = $request->get('priceinput');
      $deduction = $request->get('deduct');

      //Calculation start
      if($fiat != "BTC" && $fiat != "USD" && $fiat != "ETH")
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
          'crypto' => 'required',
          'amount' => 'required',
          'date' => 'required|date',
          'bought_at' => 'required'
          ];

          $messages = [
              'crypto.required' => 'You must enter a coin available from the list.',
              'amount.required'  => 'You must enter an amount of coins bought.',
              'date.date'  => 'You must enter a correct date in the date field',
              'date.required'  => 'You must enter the date you made the investment.',
              'bought_at.required'  => 'You must enter a paid amount for the coin.',
          ];

          $validator = Validator::make($request->all(), $rules, $messages);
          $errors = $validator->errors();
          if($validator->fails()){
              if($errors->first('crypto')){
              Alert::error($errors->first('crypto'), 'Investment failed')->persistent('Close');;
              } elseif($errors->first('bought_at')) {
              Alert::error($errors->first('bought_at'), 'Investment failed')->persistent('Close');;

              } elseif($errors->first('amount')) {
              Alert::error($errors->first('amount'), 'Investment failed')->persistent('Close');

              } elseif($errors->first('date')) {
              Alert::error($errors->first('date'), 'Investment failed')->persistent('Close');

              }
              return Redirect::back();
          }

          // Manual validator if first fails.
          if(!$request->get('amount') || $request->get('amount') <= 0.000001 || $amount <= 0.000001){
                  Alert::error('You must enter an amount of coins bought.', 'Investment failed')->persistent('Close');
                  return Redirect::back();
          }
          if(!$request->get('bought_at') || $request->get('bought_at') <= 0.000000001 || $paid <= 0.00000001){
                  Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");
                  return Redirect::back();
          }
          if($request->get('amount') && !is_numeric($amount))
          {
            Alert::error('You must enter a numeric value in the amount field.', 'Investment failed')->persistent('Close');
            return Redirect::back();
          }
          if($request->get('bought_at') && !is_numeric($paid))
          {
            Alert::error('You must enter a numeric value in the price field.', 'Investment failed')->persistent('Close');
            return Redirect::back();
          }

          //Start creation
          $historical = History::getHistorical($request->get('date'));
          $btc_usd = $historical->USD;
          $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
          $btc_gbp = 0;
          $btc_usdt = $historical->USD;
          $btc_eth = $historical->ETH;
          if($historical->ETH == null)
          {
            $btc_eth = 5;
          }


          if($currency)
          {
            $investment = new ManualInvestment;
            $investment->userid = $user->id;
            $investment->currency = $currency->symbol;
            $investment->date_bought = $date;
            if($fiat == "ETH")
            {
              $investment->market = "ETH";
            }


              if($priceinput == "paidper")
              {
                if($fiat == "USD")
                {
                  $investment->bought_at = $paid / ($btc_usd * 1);
                } elseif($fiat == "BTC" || $fiat == "ETH") {
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
                } elseif($fiat == "BTC" || $fiat == "ETH") {
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

            // Handle deduction
            if($deduction != "")
            {
              // balance deduction with btc market
              if($request->get('deduct') == "balance")
              {
                $balance = Balance::where([['userid', '=', Auth::user()->id], ['currency', '=', 'BTC'], ['exchange', '=', 'Manual']])->first();
                $balance->amount -= $investment->amount * $investment->bought_at;
                if($balance->amount <= 0)
                {
                  $balance->delete();
                } else {
                  $balance->save();
                }
              } else {
                $dd = ManualInvestment::where([['userid', '=', Auth::user()->id], ['id', '=', $request->get('deduct')]])->first();
                $dd->amount -= $investment->amount * $investment->bought_at;
                if($dd->amount <= 0)
                {
                  $dd->delete();
                } else {
                  $dd->save();
                }

                $balance = Balance::where([['userid', '=', Auth::user()->id], ['currency', '=', 'BTC'], ['exchange', '=', 'Manual']])->first();
                $balance->amount -= $investment->amount * $investment->bought_at;
                if($balance->amount <= 0)
                {
                  $balance->delete();
                } else {
                  $balance->save();
                }
              }
            }

            // If user has balance of token, we remove from it.

            if($user->addfrombalance == 1)
            {
              if($fiat != "ETH")
              {
                $removefrom = "BTC";
              } else {
                $removefrom = "ETH";
              }

              $remove = $investment->bought_at * $investment->amount;

              $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $removefrom]])->first();

              if($balance)
              {
                if($balance->amount >= $remove)
                {
                  $balance->amount -= $remove;
                  $balance->save();
                }
              }
            }

            Alert::success('Your investment was successfully added.', 'Investment added');
            return redirect('/investments');

          } else {
            Alert::error('You must enter a coin available from the list.', 'Investment failed')->persistent('Close');
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
      Cache::forget('balances-summed2'.$user->id);


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

        if(!$request->get('bought_at_btc') || $request->get('bought_at_btc') <= 0.0000001 || $bought_btc <= 0.000001)
        {
          Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
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

        if(!$request->get('bought_at_usd') || $request->get('bought_at_usd') <= 0.0000001 || $bought_usd <= 0.000001)
        {
          Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
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

        if(!$request->get('bought_at_eur') || $request->get('bought_at_eur') <= 0.0000001 || $bought_eur <= 0.000001)
        {
          Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
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

        if(!$request->get('bought_at_aud') || $request->get('bought_at_aud') <= 0.0000001 || $bought_aud <= 0.000001)
        {
          Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
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

        if(!$request->get('total') || $request->get('total') <= 0.0000001 || $total <= 0.000001)
        {
          Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
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

        if(!$request->get('usdtotal') || $request->get('usdtotal') <= 0.0000001 || $totalusd <= 0.000001)
        {
          Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
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

        if(!$request->get('eurtotal') || $request->get('eurtotal') <= 0.0000001 || $totaleur <= 0.000001)
        {
          Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
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

        if(!$request->get('audtotal') || $request->get('audtotal') <= 0.0000001 || $totalaud <= 0.000001)
        {
          Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
          return Redirect::back();
        }
      }


      // Start the creation


          $historical = History::getHistorical($request->get('date'));
          $btc_usd = $historical->USD;
          $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
          $btc_gbp = 0;






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
      $investment = ManualInvestment::where([['userid', '=', Auth::user()->id], ['id', '=', $id]])->first();
      $oldamount = $investment->amount;

      if($investment)
      {
      $user = Auth::user();
      Cache::forget('investments'.$user->id);
      Cache::forget('m_investments'.$user->id);
      Cache::forget('balances'.$user->id);
      Cache::forget('balances-summed2'.$user->id);

      //inputs
      $fiat = $request->get('priceinputeditcurrency');
      $priceinput = $request->get('priceinput');
      $deduction = $request->get('deduct');

      //Calculation start
      if($fiat != "BTC" && $fiat != "USD" && $fiat != "ETH")
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
          'amount' => 'required',
          'date' => 'required|date',
          'bought_at' => 'required'
                ];

          $messages = [
              'amount.required'  => 'You must enter an amount of coins bought.',
              'date.date'  => 'You must enter a correct date in the date field',
              'date.required'  => 'You must enter the date you made the investment.',
              'bought_at.required'  => 'You must enter a paid amount for the coin.',
          ];

          $validator = Validator::make($request->all(), $rules, $messages);
          $errors = $validator->errors();
          if($validator->fails()){
              if($errors->first('bought_at')) {
              Alert::error($errors->first('bought_at'), 'Investment failed');

              } elseif($errors->first('amount')) {
              Alert::error($errors->first('amount'), 'Investment failed');

              } elseif($errors->first('date')) {
              Alert::error($errors->first('date'), 'Investment failed');

              }
              return Redirect::back();
          }

          // Manual validator if first fails.
          if(!$request->get('amount') || $request->get('amount') <= 0.000001 || $amount <= 0.000001){
                  Alert::error('You must enter an amount of coins bought.', 'Investment failed');
                  return Redirect::back();
          }
          if(!$request->get('bought_at') || $request->get('bought_at') <= 0.0000001 || $paid <= 0.000001){
                  Alert::error('You must enter an amount paid for the investment, if it was received for free please make an Mining Asset.', 'Investment failed')->persistent("Okay");;
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
          $historical = History::getHistorical($request->get('date'));
          $btc_usd = $historical->USD;
          $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
          $btc_gbp = 0;
          $btc_usdt = $historical->USD;
          $btc_eth = $historical->ETH;
          if($historical->ETH == null)
          {
            $btc_eth = 5;
          }

            $investment->date_bought = $date;
            if($fiat == "ETH")
            {
              $investment->market = "ETH";
            }

            if($priceinput == "paidper")
            {
              if($fiat == "USD")
              {
                $investment->bought_at = $paid / ($btc_usd * 1);
              } elseif($fiat == "BTC" || $fiat == "ETH") {
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
              } elseif($fiat == "BTC" || $fiat == "ETH") {
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

            if(Balance::where([['userid', '=', $user->id], ['currency', '=', $investment->currency], ['exchange', '=', 'Manual']])->exists())
            {
              $balance = Balance::where([['userid', '=', $user->id], ['currency', '=', $investment->currency], ['exchange', '=', 'Manual']])->first();
              $balance->amount -= $oldamount;
              $balance->amount += $amount;
              $balance->save();
            } else {
              $balance = new Balance;
              $balance->userid = $user->id;
              $balance->currency = $investment->currency;
              $balance->amount = $amount;
              $balance->exchange = "Manual";
              $balance->save();
            }

            Alert::success('Your investment was successfully edited.', 'Success');
            return redirect('/investments');
          } else {
            // if no investment
            Alert::error('No investment found.', 'Oops...');
            return redirect('/investments');
          }
    }

    public function calculateBalance($currency)
    {
      $user = Auth::user();
      $investments = ManualInvestment::where([['userid', '=', Auth::user()->id], ['currency', '=', $currency], ['date_sold', '=', null]])->get();
      Cache::forget('balances'.$user->id);
      Cache::forget('balances-summed2'.$user->id);

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
      Cache::forget('balances-summed2'.$user->id);





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
        $historical = History::getHistorical($request->get('date'));
        $btc_usd = $historical->USD;
        $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
        $btc_gbp = 0;

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
        Cache::forget('balances-summed2'.$user->id);

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
        Cache::forget('balances-summed2'.$user->id);

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
        Cache::forget('balances-summed2'.$user->id);

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


    public function removeCoinbaseInvestment($id)
    {
        $investment = Investment::where([['id', '=', $id], ['userid', '=', Auth::user()->id], ['exchange', '=', 'Coinbase']])->first();
        $user = Auth::user();
        Cache::forget('investments'.$user->id);
        Cache::forget('c_investments'.$user->id);
        Cache::forget('balances'.$user->id);
        Cache::forget('balances-summed2'.$user->id);

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
      Cache::forget('minings'.Auth::user()->id);

      //Variables
      $currency = Crypto::where('id', $request->get('crypto'))->first();
      $user = Auth::user();

      $amount = str_replace(',', '.', $request->get('amount'));
      $date = $request->get('date');

      //Validator

      $messages = [
          'crypto.required' => 'You must enter a coin available from the list.',
          'amount.required'  => 'You must enter an amount of coins bought.',
          'date.date'  => 'You must enter a correct date in the date field',
          'date.required'  => 'You must enter the date you made the investment.'
        ];

      $rules = [
          'crypto' => 'required',
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


          $historical = History::getHistorical($request->get('date'));
          $btc_usd = $historical->USD;
          $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
          $btc_gbp = 0;


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
        Cache::forget('minings'.Auth::user()->id);
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
      $currency = Crypto::where('id', $request->get('crypto'))->first();
      $user = Auth::user();
      Cache::forget('investments'.$user->id);
      Cache::forget('m_investments'.$user->id);
      Cache::forget('balances'.$user->id);
      Cache::forget('balances-summed2'.$user->id);


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
          'crypto.required' => 'You must enter a coin available from the list.',
          'amount.required'  => 'You must enter an amount of coins bought.',
          'date.date'  => 'You must enter a correct date in the date field',
          'date.required'  => 'You must enter the date you made the investment.',
          'priceinput.required'  => 'You must select a price input for the investment.',
      ];

      $rules = [
          'crypto' => 'required',
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
        $historical = History::getHistorical($request->get('date'));
        $btc_usd = $historical->USD;
        $btc_eur = $historical->USD * Multiplier::where('currency', 'EUR')->select('price')->first()->price;
        $btc_gbp = 0;

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

    public function selectColorMining($coin, Request $request)
    {
      Cache::forget('minings'.Auth::user()->id);


      $minings = Mining::where([['userid', '=', Auth::user()->id], ['currency', '=', $coin]])->get();

      $rules = [
          'color' => 'hexcolor',
          ];

      $validator = Validator::make($request->all(), $rules);
      $errors = $validator->errors();
      if($validator->fails()){
          Alert::error('You must enter an valid HEX color!', 'Color selection failed.');
          return Redirect::back();
      } else {

      foreach($minings as $mining)
      {
        $mining->color = $request->get('color');
        $mining->save();
      }

      return Redirect::back();
    }
    }

    public function selectColor($coin, Request $request)
    {
      Cache::forget('balances-summed2'.Auth::user()->id);
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
      Cache::forget('p_investments'.$user->id);
      Cache::forget('b_investments'.$user->id);
      Cache::forget('c_investments'.$user->id);
      if(strlen($request->get('comment')) <= 300)
      {
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
      } else
      {
        $investment = Investment::where([['exchange', '=', $exchange], ['id', '=', $id]])->first();
        if($investment->userid == Auth::user()->id)
        {
          $investment->comment = $request->get('comment');
          $investment->save();
          return Redirect::back();
        } else {
          return Redirect::back();
        }
      }
    } else {
      Alert::error('The note may not be longer than 300 characters.', 'Oops..');
      return Redirect::back();
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
