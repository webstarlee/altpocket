<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Crypto;
use App\bittrex;
use App\Polo;
use App\Trade;
use App\Investment;
use App\Exchange;
use App\Error;
use Auth;
use App\User;
use Alert;
use Input;
use Storage;
use Excel;
use Overtrue\LaravelFollow\Traits\CanBeFollowed;
use App\Notifications\NewInvestment;

class CryptoController extends Controller
{

    public function poloLoadOrders(){

        $user = Auth::user();

        $lol = new Error;
        $lol->userid = $user->id;
        $lol->save();



    }

    public function poloInsertBuys(){
        set_time_limit(3600);
        $user = Auth::user();
        $trades = Exchange::where([['userid', '=', $user->id], ['handled', '=', '0'], ['type', '=', 'buy'], ['market', '=', 'poloniex']])->orderBy('date')->get();
        $count = 0;
        foreach($trades as $trade)
        {
            // Variables
            $investment = Investment::where([['bittrex_id', $trade->orderid], ['userid', '=', $user->id]])->first();
            $date = strtotime($trade->date);
            $newformat = date('Y-m-d', $date);
            $date = $newformat;

            $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
            if($date != date('Y-m-d')){ //If its today then we succeeded.
                $client = new \GuzzleHttp\Client();
                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                $response = $res->getBody();
                $prices = json_decode($response, true);
                $btc = 0;
                if($prices['BTC']){
                    foreach($prices['BTC'] as $key => $price){
                        $btc = $price;
                    }
                } else {
                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                }
                } else {
                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                }

            if($investment)
            {
                $investment->amount += $trade->amount;
                $investment->usd_total += ($trade->total * $btc);
                $investment->save();

                $trade->handled = 1;
                $trade->save();

            } else
            {
                $count += 1;
                $investment = new Investment;
                $investment->userid = Auth::user()->id;
                $investment->crypto = $trade->crypto;
                $investment->bought_at = $trade->rate;
                $investment->amount = $trade->amount;
                $investment->date_bought = $date;
                $investment->usd_total = ($trade->total * $btc);
                $investment->date = $trade['date'];
                $investment->bittrex_id = $trade->orderid;
                $investment->market = 'poloniex';
                $investment->btc_price_bought = $btc;
                $investment->save();

                $trade->handled = 1;
                $trade->save();

            }

        }

            if($count > 0){
            $followers = $user->followers()->get();

            if(count($followers) > 0) {
            $notification = [
                'icon' => 'fa fa-btc',
                'title' => 'New investment',
                'data' => $user->username.' has a new investment.',
                'type' => 'investment',
                'user' => $user->username
            ];
                foreach($followers as $follower){
                    $follower->notify(new NewInvestment($notification));
                }
            }
            }


    }

    public function poloInsertSales(){
        set_time_limit(3600);
        $user = Auth::user();
        $trades = Exchange::where([['userid', '=', $user->id], ['handled', '=', '0'], ['type', '=', 'sell'], ['market', '=', 'poloniex']])->orderBy('date')->get();
        $lasttrade = 0;
        foreach($trades as $trade)
        {
            $amount = $trade->amount;
            $investments = Investment::where([['crypto', '=', $trade->crypto], ['userid', '=', $user->id], ['sale_id', '=', null], ['market', '=', 'poloniex']])->orderBy('date')->get();


            if($amount > 0)
            {
                foreach($investments as $investment){
                    if($investment->amount >= $amount && $amount > 0)
                    {
                        $oldsale = Investment::where('sale_id', $trade->orderid)->first();

                        if(!$oldsale && $investment->amount != 0){
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                foreach($prices['BTC'] as $key => $price){
                                        $btc = $price;
                                }
                                } catch(GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }


                            //New Investment
                            $inv = new Investment;
                            $inv->crypto = $investment->crypto;
                            $inv->userid = $user->id;
                            $inv->bought_at = $investment->bought_at;
                            $inv->amount = $amount;
                            $inv->date_bought = $investment->date_bought;
                            $inv->usd_total = ($investment->usd_total / $investment->amount) * $amount;
                            $inv->date = $investment->date;
                            $inv->bittrex_id = $investment->bittrex_id;
                            $inv->sold_at = $trade->rate;
                            $inv->sold_for = $trade->total * $btc;
                            $inv->sale_id = $trade->orderid;
                            $inv->btc_price_bought = $investment->btc_price_bought;
                            $inv->market = "poloniex";
                            $inv->save();
                            $lasttrade = $trade->id;

                            //Remove from old investment
                            $investment->usd_total = ($investment->usd_total / $investment->amount) * ($investment->amount - $amount);
                            $investment->amount -= $amount;
                            if($investment->amount <= 0.000001){
                                echo "InvestmentID: ". $investment->id . "Amount: " . $investment->amount . "<br><br>";
                                $investment->delete();
                            } else {
                                echo "InvestmentID: ". $investment->id . "Amount: " . $investment->amount . "<br><br>";
                                $investment->save();
                            }

                            $trade->handled = 1;
                            $trade->save();
                            $amount = 0;



                        } else {
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                foreach($prices['BTC'] as $key => $price){
                                        $btc = $price;
                                }
                                } catch(\GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }


                            //Remove from old investment
                            $debugamount = $investment->amount;
                            $investment->usd_total = ($investment->usd_total / $investment->amount) * ($investment->amount - $amount);
                            $investment->amount -= $amount;
                            if($investment->amount <= 0.000001){
                                echo $investment->amount." ROW: 277 ID: ".$investment->id."<br>";
                                $investment->delete();
                            } else {
                                echo $investment->amount." ROW: 278 ID: ".$investment->id."<br>";
                                $investment->save();
                            }

                            $seconddate = date('Y-m-d', strtotime($oldsale->date_bought. ' + 1 days'));
                            if($oldsale->date_bought != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $prev = 0;
                                foreach($prices['BTC'] as $key => $price){
                                    $prev = $price;
                                }
                                } catch(GuzzleHttp\Exception\ClientException $e){
                                    $prev = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $prev = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }

                            $debugamount2 = $oldsale->amount;
                            $oldsale->amount += $amount;
                            $oldsale->usd_total = ($oldsale->bought_at * $oldsale->amount) * $prev;
                            if($trade->id != $lasttrade){
                                $oldsale->sold_for += ($trade->total * $btc);
                            }


                            $lasttrade = $trade->id;


                            $oldsale->save();

                            $amount = 0;

                            $trade->handled = 1;
                            $trade->save();


                        }
                    } elseif($investment->amount <= $amount && $amount > 0)
                    {
                        $oldsale = Investment::where([['sale_id', '=', $trade->orderid]])->first();
                        if(!$oldsale){
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                foreach($prices['BTC'] as $key => $price){
                                        $btc = $price;
                                }
                                } catch(\GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }

                            $investment->sold_at = $trade->rate;
                            $investment->sold_for = ($trade->total * $btc);
                            $investment->date = $trade->date;
                            $investment->sale_id = $trade->orderid;
                            $investment->market = "poloniex";
                            $investment->save();
                            $lasttrade = $trade->id;

                            if($investment->amount <= 0.000001){
                                echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                            } else {
                                echo $investment->amount." ROW: 416 ID: ".$investment->id."<br>";
                            }

                            $debugamount3 = $amount;
                            $amount -= $investment->amount;


                            $trade->handled = 1;
                            $trade->save();


                        } else
                        {
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                                $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
                                if($date != date('Y-m-d')){ //If its today then we succeeded.
                                    $client = new \GuzzleHttp\Client();
                                    try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                    $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                                    $response = $res->getBody();
                                    $prices = json_decode($response, true);
                                    $btc = 0;
                                    foreach($prices['BTC'] as $key => $price){
                                            $btc = $price;
                                    }
                                } catch(\GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }
                            $debugamount4 = $oldsale->amount;
                            if($oldsale->amount != 0){
                                $oldsale->usd_total += ($oldsale->usd_total / $oldsale->amount) * $investment->amount;
                            }

                            $oldsale->amount += $investment->amount;


                            if($trade->id != $lasttrade){
                                $oldsale->sold_for += ($trade->total * $btc);
                            }

                            $lasttrade = $trade->id;


                            $oldsale->save();


                            $debugamount5 = $amount;
                            $amount -= $investment->amount;
                            $investment->amount -= $debugamount5;
                            if($investment->amount <= 0.000001){
                                echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                            $investment->delete();
                            } else {
                                echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                            }



                            $trade->handled = 1;
                            $trade->save();

                        }
                }
                    if($investment->amount <= 0.000001){
                        $investment->delete();
                    }
                }
            }
        }






        CryptoController::calcInvested();
        Alert::success('Your import was successfully done.', 'Import successful');
        return redirect('/coins/');
    }

    public function bittrexLoadOrders(){

        $client = new \GuzzleHttp\Client();
        $apikey = Auth::user()->api_key;
        $apisecret = Auth::user()->api_secret;
        try{
        $nonce=time();
        $uri='https://bittrex.com/api/v1.1/account/getorderhistory?apikey='.$apikey.'&nonce='.$nonce;
        $sign=hash_hmac('sha512',$uri,$apisecret);
        $res = $client->request('GET', $uri, [
        'headers' => [
        'apisign' => $sign,
        ]]);
        $response = $res->getBody();
        $investments = json_decode($response, true);
        }  catch (\GuzzleHttp\Exception\ClientException $e) {
            Alert::error('It seems like we are having trouble connecting to Bittrex.', 'Import failed')->persistent('Close');
            return redirect('/coins/');
        }

        $user = Auth::user();

        foreach($investments['result'] as $investment){
            if (strpos($investment['Exchange'], 'BTC-') !== false) {
                $check = Exchange::where('orderuuid', $user->id.$investment['OrderUuid'])->first();

                if(!$check){
                        $symbol = str_replace('BTC-', '', $investment['Exchange']);
                        $ddate = strtotime($investment['TimeStamp']);
                        $newformat = date('Y-m-d H:i:s', $ddate);

                        if(Crypto::where('symbol', $symbol)->first()){
                            $exchange = new Exchange;
                            $exchange->type = $investment['OrderType'];
                            $exchange->orderuuid = $user->id.$investment['OrderUuid'];
                            $exchange->crypto = $symbol;
                            $exchange->date = $newformat;
                            $exchange->rate = $investment['PricePerUnit'];
                            $exchange->amount = $investment['Quantity'] - $investment['QuantityRemaining'];
                            $exchange->total = $investment['Price'];
                            $exchange->fee = $investment['Commission'];
                            $exchange->userid = Auth::user()->id;
                            $exchange->market = "bittrex";
                            $exchange->save();
                        }
                }
            }
        }
        $user->hasVerified = "Yes";
        $user->save();

    }


    public function bittrexLoadOrders2(Request $request){

        $client = new \GuzzleHttp\Client();
        $apikey = Auth::user()->api_key;
        $apisecret = Auth::user()->api_secret;

    if ($request->hasFile('csv')){

        $file = $request->file('csv');
        $path = $request->file('csv')->getRealPath();


        //check out the edit content on bottom of my answer for details on $storage

        $data = Excel::load($path, function($reader) { })->get();
        if (!empty($data) && $data->count()) {
            $count = 0;
            foreach ($data as $key => $d) {
                $order = $d->orderuuid;
                $order = trim($order);


                $nonce=time();
                $uri='https://bittrex.com/api/v1.1/account/getorder?apikey='.$apikey.'&nonce='.$nonce.'&uuid='.$order;
                $uri='https://bittrex.com/api/v1.1/account/getorder?apikey='.$apikey.'&nonce='.$nonce.'&uuid=fc092c93-ebb4-4658-acfd-b81fc6a04dac';
                echo $uri."<br>";
                $sign=hash_hmac('sha512',$uri,$apisecret);
                $res = $client->request('GET', $uri, [
                'headers' => [
                'apisign' => $sign
                ]]);
                $response = $res->getBody();
                $investments = json_decode($response, true);


                echo $response;
                echo $order."<br>";












            }
        }


    } else {
        echo "test";
    }

        /*
        try{
        $nonce=time();
        $uri='https://bittrex.com/api/v1.1/account/getorderhistory?apikey='.$apikey.'&nonce='.$nonce;
        $sign=hash_hmac('sha512',$uri,$apisecret);
        $res = $client->request('GET', $uri, [
        'headers' => [
        'apisign' => $sign,
        ]]);
        $response = $res->getBody();
        $investments = json_decode($response, true);
        }  catch (\GuzzleHttp\Exception\ClientException $e) {
            Alert::error('It seems like we are having trouble connecting to Bittrex.', 'Import failed')->persistent('Close');
            return redirect('/coins/');
        }*/





        /*
        $user = Auth::user();

        foreach($investments['result'] as $investment){
            if (strpos($investment['Exchange'], 'BTC-') !== false) {
                $check = Exchange::where('orderuuid', $user->id.$investment['OrderUuid'])->first();

                if(!$check){
                        $symbol = str_replace('BTC-', '', $investment['Exchange']);
                        $ddate = strtotime($investment['TimeStamp']);
                        $newformat = date('Y-m-d H:i:s', $ddate);

                        if(Crypto::where('symbol', $symbol)->first()){
                            $exchange = new Exchange;
                            $exchange->type = $investment['OrderType'];
                            $exchange->orderuuid = $user->id.$investment['OrderUuid'];
                            $exchange->crypto = $symbol;
                            $exchange->date = $newformat;
                            $exchange->rate = $investment['PricePerUnit'];
                            $exchange->amount = $investment['Quantity'] - $investment['QuantityRemaining'];
                            $exchange->total = $investment['Price'];
                            $exchange->fee = $investment['Commission'];
                            $exchange->userid = Auth::user()->id;
                            $exchange->market = "bittrex";
                            $exchange->save();
                        }
                }
            }
        }
        $user->hasVerified = "Yes";
        $user->save();*/

    }



    public function jonathan(){
        $investments = Investment::where([['amount', '=', 0], ['type', '!=', 'Mining']])->get();

            foreach($investments as $investment){
                $investment->delete();
            }

        $investments = Investment::where([['bought_at', '=', 0], ['type', '!=', 'Mining']])->get();

            foreach($investments as $investment){
                $investment->delete();
            }




    }





    public function bittrexInsertBuys(){

        $user = Auth::user();
        $trades = Exchange::where([['userid', '=', $user->id], ['handled', '=', '0'], ['type', '=', 'LIMIT_BUY'], ['market', '=', 'bittrex']])->orderBy('date')->get();

                foreach($trades as $trade) {
                    // Variables
                    $investment = Investment::where('bittrex_id', $trade->orderuuid)->first();
                    $date = strtotime($trade->date);
                    $newformat = date('Y-m-d', $date);
                    $date = $newformat;

                    $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
                    if($date != date('Y-m-d')){ //If its today then we succeeded.
                        $client = new \GuzzleHttp\Client();
                        try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                        $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                        $response = $res->getBody();
                        $prices = json_decode($response, true);
                        $btc = 0;
                            foreach($prices['BTC'] as $key => $price){
                                    $btc = $price;
                            }
                        } catch(\GuzzleHttp\Exception\ClientException $e){
                            $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }
                        } else {
                            $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                    }

                    if(!$investment){
                        $investment = new Investment;
                        $investment->userid = Auth::user()->id;
                        $investment->crypto = $trade->crypto;
                        $investment->bought_at = $trade->rate;
                        $investment->amount = $trade->amount;
                        $investment->date_bought = $date;
                        $investment->usd_total = ($trade->rate * $trade->amount) * $btc;
                        $investment->date = $trade['date'];
                        $investment->bittrex_id = $trade->orderuuid;
                        $investment->market = 'bittrex';
                        $investment->btc_price_bought = $btc;
                        $investment->save();

                        $trade->handled = 1;
                        $trade->save();

                    }

                }


    }

    public function bittrexInsertSales(){
        $user = Auth::user();
        $trades = Exchange::where([['userid', '=', $user->id], ['handled', '=', '0'], ['type', '=', 'LIMIT_SELL'], ['market', '=', 'bittrex']])->orderBy('date')->get();

        foreach($trades as $trade)
        {
            $amount = $trade->amount;
            $investments = Investment::where([['crypto', '=', $trade->crypto], ['userid', '=', $user->id], ['sale_id', '=', null], ['market', '=', 'bittrex']])->orderBy('date')->get();

            if($amount > 0)
            {
                foreach($investments as $investment){
                    if($investment->amount >= $amount && $amount > 0)
                    {
                        $oldsale = Investment::where('sale_id', $trade->orderuuid)->first();

                        if(!$oldsale && $investment->amount != 0){
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                    foreach($prices['BTC'] as $key => $price){
                                            $btc = $price;
                                }
                                } catch(\GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }


                            //New Investment
                            $inv = new Investment;
                            $inv->crypto = $investment->crypto;
                            $inv->userid = $user->id;
                            $inv->bought_at = $investment->bought_at;
                            $inv->amount = $amount;
                            $inv->date_bought = $investment->date_bought;
                            $inv->usd_total = ($investment->usd_total / $investment->amount) * $amount;
                            $inv->date = $investment->date;
                            $inv->bittrex_id = $investment->bittrex_id;
                            $inv->sold_at = $trade->rate;
                            $inv->sold_for = $trade->total * $btc;
                            $inv->sale_id = $trade->orderuuid;
                            $inv->btc_price_bought = $investment->btc_price_bought;
                            $inv->market = "bittrex";
                            $inv->save();
                            $lasttrade = $trade->id;

                            //Remove from old investment
                            $investment->usd_total = ($investment->usd_total / $investment->amount) * ($investment->amount - $amount);
                            $investment->amount -= $amount;
                            if($investment->amount <= 0){
                                echo "InvestmentID: ". $investment->id . "Amount: " . $investment->amount . "<br><br>";
                                $investment->delete();
                            } else {
                                echo "InvestmentID: ". $investment->id . "Amount: " . $investment->amount . "<br><br>";
                                $investment->save();
                            }

                            $trade->handled = 1;
                            $trade->save();
                            $amount = 0;
                    } else
                        {
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                    foreach($prices['BTC'] as $key => $price){
                                            $btc = $price;
                                    }
                                } catch(\GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }


                            //Remove from old investment
                            $debugamount = $investment->amount;
                            $investment->usd_total = ($investment->usd_total / $investment->amount) * ($investment->amount - $amount);
                            $investment->amount -= $amount;
                            if($investment->amount <= 0.00000001){
                                echo $investment->amount." ROW: 277 ID: ".$investment->id."<br>";
                                $investment->delete();
                            } else {
                                echo $investment->amount." ROW: 278 ID: ".$investment->id."<br>";
                                $investment->save();
                            }


                            $seconddate = date('Y-m-d', strtotime($oldsale->date_bought. ' + 1 days'));
                            if($oldsale->date_bought != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');

                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $prev = 0;
                                    foreach($prices['BTC'] as $key => $price){
                                        $prev = $price;

                                    }
                                } catch(\GuzzleHttp\Exception\ClientException $e){
                                    $prev = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $prev = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }

                            $debugamount2 = $oldsale->amount;
                            $oldsale->amount += $amount;
                            $oldsale->usd_total = ($oldsale->bought_at * $oldsale->amount) * $prev;
                            if($trade->id != $lasttrade){
                                $oldsale->sold_for += ($trade->total * $btc);
                            }


                            $lasttrade = $trade->id;


                            $oldsale->save();

                            $amount = 0;

                            $trade->handled = 1;
                            $trade->save();
                        }


                    } elseif($investment->amount <= $amount && $amount > 0)
                    {
                        $oldsale = Investment::where([['sale_id', '=', $trade->orderuuid]])->first();
                        if(!$oldsale){
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');

                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                    foreach($prices['BTC'] as $key => $price){
                                            $btc = $price;
                                    }
                                } catch(\GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }

                            $investment->sold_at = $trade->rate;
                            $investment->sold_for = ($trade->total * $btc);
                            $investment->date = $trade->date;
                            $investment->sale_id = $trade->orderuuid;
                            $investment->market = "bittrex";
                            $investment->save();
                            $lasttrade = $trade->id;

                            if($investment->amount <= 0.000001){
                                echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                            } else {
                                echo $investment->amount." ROW: 416 ID: ".$investment->id."<br>";
                            }

                            $debugamount3 = $amount;
                            $amount -= $investment->amount;


                            $trade->handled = 1;
                            $trade->save();


                        } else
                        {
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            $seconddate = date('Y-m-d', strtotime($date. ' + 1 days'));
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'https://min-api.cryptocompare.com/data/pricehistorical?fsym=BTC&tsyms=USD&ts='.strtotime($date).'&extraParams=Altpocket');
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                foreach($prices['BTC'] as $key => $price){
                                        $btc = $price;
                                }
                                } catch(\GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }
                            $debugamount4 = $oldsale->amount;
                            if($oldsale->amount != 0){
                                $oldsale->usd_total += ($oldsale->usd_total / $oldsale->amount) * $investment->amount;
                            }

                            $oldsale->amount += $investment->amount;


                            if($trade->id != $lasttrade){
                                $oldsale->sold_for += ($trade->total * $btc);
                            }

                            $lasttrade = $trade->id;


                            $oldsale->save();


                            $debugamount5 = $amount;
                            $amount -= $investment->amount;
                            $investment->amount -= $debugamount5;
                            if($investment->amount <= 0.000001){
                                echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                            $investment->delete();
                            } else {
                                echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                            }



                            $trade->handled = 1;
                            $trade->save();

                        }
                }
                if($investment->amount <= 0.000001){
                    $investment->delete();
                }
        }



    }
        }

            $followers = $user->followers()->get();

            if(count($followers) > 0) {
            $notification = [
                'icon' => 'fa fa-btc',
                'title' => 'New investment',
                'data' => $user->username.' has a new investment.',
                'type' => 'investment',
                'user' => $user->username
            ];
                foreach($followers as $follower){
                    $follower->notify(new NewInvestment($notification));
                }
            }


        CryptoController::calcInvested();
        Alert::success('Your import was successfully done.', 'Import successful');
        return redirect('/coins/');
    }

    public function poloniex()
    {
        /* Lets make this beautiful */
        $user = Auth::user();
        $apikey = Auth::user()->polo_api_key;
        //$apikey = "D3G6P1DL-Z0NFT12O-Y7QYH94J-0A3J2C46";
        //$apisecret = "b945b9b23ac3bbd2de45d663d8254dfe38c06a37f02087ff30b48e5dcf1883fffb28915a2612fb1d168710c2cde767ff33850d8f2e670a64cc0dac5883f8ef0f";
        $apisecret = Auth::user()->polo_api_secret;

        /* Lets go */
        $client = new \GuzzleHttp\Client();
        $nonce = round(microtime(true) * 1000);
        $req = ['command' => 'returnTradeHistory', 'currencyPair' => 'all', 'start' => '1420070400', 'nonce' => $nonce];
        $post_data = http_build_query($req, '', '&');
        $sign = hash_hmac('sha512', $post_data, $apisecret);
        $res = $client->request('POST', 'https://poloniex.com/tradingApi', [
        'headers' => [
        'Sign' => $sign,
        'Key' => $apikey
        ], 'form_params' => ['command' => 'returnTradeHistory', 'currencyPair' => 'all', 'start' => '1420070400', 'nonce' => $nonce]]);
        $response = $res->getBody();
        $trades = json_decode($response, true);
        // Insert Trades
        foreach($trades as $key => $trade)
        {
            foreach($trade as $t)
            {
                if(strpos($key, 'BTC_') !== false && $t['category'] == "exchange")
                {
                    $check = Exchange::where('tradeID', $t['tradeID'])->first();

                    if(!$check)
                    {
                        $crypto = str_replace('BTC_', '', $key);
                        /* Variables */
                        $fee = number_format($t['amount'] * $t['fee'], 8, '.', '');
                        if($t['type'] == "buy"){
                            $amount = number_format($t['amount'] - $fee, 8 ,'.', '');
                        } else {
                            $amount = $t['amount']; // number_format($amount, 7)
                        }
                        $exchange = new Exchange;
                        $exchange->type = $t['type'];
                        $exchange->tradeid = $t['tradeID'];
                        $exchange->crypto = $crypto;
                        $exchange->date = $t['date'];
                        $exchange->rate = $t['rate'];
                        $exchange->amount = $amount;
                        $exchange->total = $t['total'];
                        $exchange->fee = $fee;
                        $exchange->orderid = $t['orderNumber'];
                        $exchange->userid = Auth::user()->id;
                        $exchange->save();
                    } else
                    {

                    }
                }
            }
        }


        // Insert Investments
        $trades = Exchange::where([['userid', '=', $user->id], ['handled', '=', '0'], ['type', '=', 'buy']])->orderBy('date')->get();



        $asd = 1;

        if($asd == 1){
        foreach($trades as $trade)
        {
            // Variables
            $investment = Investment::where('bittrex_id', $trade->orderid)->first();
            $date = strtotime($trade->date);
            $newformat = date('Y-m-d', $date);
            $date = $newformat;

            if($date != date('Y-m-d')){ //If its today then we succeeded.
                $client = new \GuzzleHttp\Client();
                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                $response = $res->getBody();
                $prices = json_decode($response, true);
                $btc = 0;
                foreach($prices['bpi'] as $price){
                    $btc = $price;
                }
                } catch(GuzzleHttp\Exception\ClientException $e){
                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                    }
                } else {
                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
            }

            if($investment)
            {
                $investment->amount += $trade->amount;
                $investment->usd_total += ($trade->total * $btc);
                $investment->save();

                $trade->handled = 1;
                $trade->save();

            } else
            {
                $investment = new Investment;
                $investment->userid = Auth::user()->id;
                $investment->crypto = $trade->crypto;
                $investment->bought_at = $trade->rate;
                $investment->amount = $trade->amount;
                $investment->date_bought = $date;
                $investment->usd_total = ($trade->total * $btc);
                $investment->date = $trade['date'];
                $investment->bittrex_id = $trade->orderid;
                $investment->save();

                $trade->handled = 1;
                $trade->save();

            }

        }
        }

        //Insert sales
        $test = 1;
        $lasttrade = 0;
        if($test == 1){
        $trades = Exchange::where([['userid', '=', $user->id], ['handled', '=', '0'], ['type', '=', 'sell']])->orderBy('date')->get();
        foreach($trades as $trade)
        {
            $amount = $trade->amount;
            $investments = Investment::where([['crypto', '=', $trade->crypto], ['userid', '=', $user->id], ['sale_id', '=', null]])->orderBy('date')->get();


            if($amount > 0)
            {
                foreach($investments as $investment){
                    if($investment->amount >= $amount && $amount > 0)
                    {
                        $oldsale = Investment::where('sale_id', $trade->orderid)->first();

                        if(!$oldsale && $investment->amount != 0){
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                foreach($prices['bpi'] as $price){
                                    $btc = $price;
                                }
                                } catch(GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }


                            //New Investment
                            $inv = new Investment;
                            $inv->crypto = $investment->crypto;
                            $inv->userid = $user->id;
                            $inv->bought_at = $investment->bought_at;
                            $inv->amount = $amount;
                            $inv->date_bought = $investment->date_bought;
                            $inv->usd_total = ($investment->usd_total / $investment->amount) * $amount;
                            $inv->date = $investment->date;
                            $inv->bittrex_id = $investment->bittrex_id;
                            $inv->sold_at = $trade->rate;
                            $inv->sold_for = $trade->total * $btc;
                            $inv->sale_id = $trade->orderid;
                            $inv->save();
                            $lasttrade = $trade->id;

                            //Remove from old investment
                            $investment->usd_total = ($investment->usd_total / $investment->amount) * ($investment->amount - $amount);
                            $investment->amount -= $amount;
                            if($investment->amount <= 0){
                                echo "InvestmentID: ". $investment->id . "Amount: " . $investment->amount . "<br><br>";
                                $investment->delete();
                            } else {
                                echo "InvestmentID: ". $investment->id . "Amount: " . $investment->amount . "<br><br>";
                                $investment->save();
                            }

                            $trade->handled = 1;
                            $trade->save();
                            $amount = 0;



                        } else {
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                foreach($prices['bpi'] as $price){
                                    $btc = $price;
                                }
                                } catch(GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }


                            //Remove from old investment
                            $debugamount = $investment->amount;
                            $investment->usd_total = ($investment->usd_total / $investment->amount) * ($investment->amount - $amount);
                            $investment->amount -= $amount;
                            if($investment->amount <= 0.00000001){
                                echo $investment->amount." ROW: 277 ID: ".$investment->id."<br>";
                                $investment->delete();
                            } else {
                                echo $investment->amount." ROW: 278 ID: ".$investment->id."<br>";
                                $investment->save();
                            }


                            if($oldsale->date_bought != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$oldsale->date_bought.'&end='.$oldsale->date_bought);
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $prev = 0;
                                foreach($prices['bpi'] as $price){
                                    $prev = $price;
                                }
                                } catch(GuzzleHttp\Exception\ClientException $e){
                                    $prev = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $prev = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }

                            $debugamount2 = $oldsale->amount;
                            $oldsale->amount += $amount;
                            $oldsale->usd_total = ($oldsale->bought_at * $oldsale->amount) * $prev;
                            if($trade->id != $lasttrade){
                                $oldsale->sold_for += ($trade->total * $btc);
                            }


                            $lasttrade = $trade->id;


                            $oldsale->save();

                            $amount = 0;

                            $trade->handled = 1;
                            $trade->save();


                        }
                    } elseif($investment->amount <= $amount && $amount > 0)
                    {
                        $oldsale = Investment::where([['sale_id', '=', $trade->orderid]])->first();
                        if(!$oldsale){
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                foreach($prices['bpi'] as $price){
                                    $btc = $price;
                                }
                                } catch(GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }

                            $investment->sold_at = $trade->rate;
                            $investment->sold_for = ($trade->total * $btc);
                            $investment->date = $trade->date;
                            $investment->sale_id = $trade->orderid;
                            $investment->save();
                            $lasttrade = $trade->id;

                            if($investment->amount <= 0){
                                echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                            } else {
                                echo $investment->amount." ROW: 416 ID: ".$investment->id."<br>";
                            }

                            $debugamount3 = $amount;
                            $amount -= $investment->amount;


                            $trade->handled = 1;
                            $trade->save();


                        } else
                        {
                            $date = strtotime($trade->date);
                            $newformat = date('Y-m-d', $date);
                            $date = $newformat;
                            //Get BTC value of day sold.
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                                $client = new \GuzzleHttp\Client();
                                try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $btc = 0;
                                foreach($prices['bpi'] as $price){
                                    $btc = $price;
                                }
                                } catch(GuzzleHttp\Exception\ClientException $e){
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                } else {
                                    $btc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }
                            $debugamount4 = $oldsale->amount;
                            if($oldsale->amount != 0){
                                $oldsale->usd_total += ($oldsale->usd_total / $oldsale->amount) * $investment->amount;
                            }

                            $oldsale->amount += $investment->amount;


                            if($trade->id != $lasttrade){
                                $oldsale->sold_for += ($trade->total * $btc);
                            }

                            $lasttrade = $trade->id;


                            $oldsale->save();


                            $debugamount5 = $amount;
                            $amount -= $investment->amount;
                            $investment->amount -= $debugamount5;
                            if($investment->amount <= 0){
                                echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                            $investment->delete();
                            } else {
                                echo $investment->amount." ROW: 412 ID: ".$investment->id."<br>";
                            }



                            $trade->handled = 1;
                            $trade->save();

                        }
                }
                }
            }
        }
        }


        CryptoController::calcInvested();
                    Alert::success('Your import was successfully done.', 'Import successful');
                    return redirect('/coins/');
    }

    public function grabPolo(){
        $user = Auth::user();
        $client = new \GuzzleHttp\Client();
        $apikey = Auth::user()->polo_api_key;
        $apisecret = Auth::user()->polo_api_secret;
        $nonce=time();
        $req = ['command' => 'returnTradeHistory', 'currencyPair' => 'all', 'start' => '1420070400', 'nonce' => $nonce];
        $post_data = http_build_query($req, '', '&');
        $sign = hash_hmac('sha512', $post_data, $apisecret);

        $res = $client->request('POST', 'https://poloniex.com/tradingApi', [
        'headers' => [
        'Sign' => $sign,
        'Key' => $apikey
        ], 'form_params' => ['command' => 'returnTradeHistory', 'currencyPair' => 'all', 'start' => '1420070400', 'nonce' => $nonce]]);
        $response = $res->getBody();
        $investments = json_decode($response, true);

        foreach($investments as $key => $investment){
            foreach($investment as $i){
            if (strpos($key, 'BTC_') !== false) {
                    if($i['category'] == "exchange"){
                        if(Investment::where('bittrex_id', $i['orderNumber'])->first()){
                            if($i['type'] == "buy"){
                                if(Trade::where('tradeid', $i['tradeID'])->first()){

                                } else {
                                    $trade = new Trade;
                                    $trade->userid = Auth::user()->id;
                                    $trade->tradeid = $i['tradeID'];
                                    $trade->ordernumber = $i['orderNumber'];
                                    $trade->amount = $i['amount'];
                                    $trade->fee = $i['fee'];
                                    $trade->total = $i['total'];
                                    $trade->per = $i['rate'];
                                    $trade->date = $i['date'];
                                    $trade->type = $i['type'];
                                    $trade->save();

                                    $investment = Investment::where('bittrex_id', $i['orderNumber'])->first();
                                    $date = $investment->date_bought;
                                    if($date != date('Y-m-d')){
                                    $client = new \GuzzleHttp\Client();
                                    $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                                    $response = $res->getBody();
                                    $prices = json_decode($response, true);
                                    $value = 0;
                                    foreach($prices['bpi'] as $price){
                                        $value = $price;
                                    }
                                    } else {
                                        $value = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }

                                    $investment->amount += $i['amount'];
                                    $investment->usd_total += $i['total'] * $value;
                                    $investment->save();

                                    $user = Auth::user();
                                    $user->invested += $i['total'] * $value;
                                    $user->save();
                                }
                            }
                        }else {
                            $symbol = str_replace('BTC_', '', $key);
                            if(Polo::where('symbol', $symbol)->first()){
                            if($i['type'] == "buy"){
                            $trade = new Trade;
                            $trade->userid = Auth::user()->id;
                            $trade->tradeid = $i['tradeID'];
                            $trade->ordernumber = $i['orderNumber'];
                            $trade->amount = $i['amount'];
                            $trade->fee = $i['fee'];
                            $trade->total = $i['total'];
                            $trade->per = $i['rate'];
                            $trade->date = $i['date'];
                            $trade->type = $i['type'];
                            $trade->save();

                            $coin = new Investment;
                            $coin->bittrex_id = $i['orderNumber'];
                            $coin->userid = Auth::user()->id;
                            $coin->crypto = $symbol;
                            $coin->bought_at = $i['rate'];
                            $coin->amount = $i['amount'];
                            $ddate = strtotime($i['date']);
                            $newformat = date('Y-m-d', $ddate);

                            $coin->date_bought = $newformat;
                            $date = $newformat;
                            if($date != date('Y-m-d')){
                            $client = new \GuzzleHttp\Client();
                            $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                            $response = $res->getBody();
                            $prices = json_decode($response, true);
                            $value = 0;
                            foreach($prices['bpi'] as $price){
                                $value = $price;
                            }
                            } else {
                                $value = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }

                            $coin->usd_total += $i['total'] * $value;
                            $coin->save();
                            $user->invested += $i['total'] * $value;
                            $user->save();
                            }
                            }
                        }
                    }

                }
            }
        }
                    Alert::success('Your import was successfully done.', 'Import successful');
                    return redirect('/coins/');
    }

    public function privateTest(){

        $client = new \GuzzleHttp\Client();
        $apikey = Auth::user()->api_key;
        $apisecret = Auth::user()->api_secret;
        $nonce=time();
        $uri='https://bittrex.com/api/v1.1/account/getorderhistory?apikey='.$apikey.'&nonce='.$nonce;
        $sign=hash_hmac('sha512',$uri,$apisecret);
        $res = $client->request('GET', $uri, [
        'headers' => [
        'apisign' => $sign,
        ]]);



        $response = $res->getBody();
        $investments = json_decode($response, true);
        $user = Auth::user();
        foreach($investments['result'] as $investment){
            if($investment['OrderType'] != "LIMIT_SELL"){
            if (strpos($investment['Exchange'], 'BTC-') !== false) {
                if(Investment::where('bittrex_id', $investment['OrderUuid'])->first()){

                } else {


                    $symbol = str_replace('BTC-', '', $investment['Exchange']);
                    $coin = new Investment;
                    $coin->bittrex_id = $investment['OrderUuid'];
                    $coin->userid = Auth::user()->id;
                    $coin->crypto = $symbol;
                    $coin->bought_at = $investment['PricePerUnit'];
                    $coin->amount = $investment['Quantity'];

                    $ddate = strtotime($investment['TimeStamp']);
                    $newformat = date('Y-m-d', $ddate);

                    $coin->date_bought = $newformat;

                    $date = $newformat;
                    if($date != date('Y-m-d')){
                    $client = new \GuzzleHttp\Client();
                    $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                    $response = $res->getBody();
                    $prices = json_decode($response, true);
                    $value = 0;
                    foreach($prices['bpi'] as $price){
                        $value = $price;
                    }
                    } else {
                        $value = Crypto::where('symbol', 'btc')->first()->price_usd;
                    }

                    $coin->usd_total = ($investment['Quantity'] * $investment['PricePerUnit']) * $value;
                    $coin->save();


                    $user->invested += $coin->usd_total;
                    $user->save();
                }
            }
        }
        }
                    Alert::success('Your import was successfully done.', 'Import successful');
                    return redirect('/coins/');

    }


    public function test2(){

        $date = "2017-05-09";

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
        $response = $res->getBody();
        $prices = json_decode($response, true);
        $price = "";
        foreach($prices['bpi'] as $price){
            echo $price;
        }
    }

    public function importPoloniex(){

        $nonce = time();
        CryptoController::newGrab(0, $nonce);
        $nonce = time()+1;
        CryptoController::newGrab(1, $nonce);
        CryptoController::calcInvested();


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


    public function newGrab($run, $time){
        $user = Auth::user();
        $client = new \GuzzleHttp\Client();
        $apikey = $user->polo_api_key;
        //$apikey ="D3G6P1DL-Z0NFT12O-Y7QYH94J-0A3J2C46";
        $apisecret = $user->polo_api_secret;
        //$apisecret = "b945b9b23ac3bbd2de45d663d8254dfe38c06a37f02087ff30b48e5dcf1883fffb28915a2612fb1d168710c2cde767ff33850d8f2e670a64cc0dac5883f8ef0f";
        $nonce=$time;
        $req = ['command' => 'returnTradeHistory', 'currencyPair' => 'all', 'start' => '1420070400', 'nonce' => $nonce];
        $post_data = http_build_query($req, '', '&');
        $sign = hash_hmac('sha512', $post_data, $apisecret);

        $res = $client->request('POST', 'https://poloniex.com/tradingApi', [
        'headers' => [
        'Sign' => $sign,
        'Key' => $apikey
        ], 'form_params' => ['command' => 'returnTradeHistory', 'currencyPair' => 'all', 'start' => '1420070400', 'nonce' => $nonce]]);
        $response = $res->getBody();
        $investments = json_decode($response, true);

        // Last sell order Id
        $lastsell = "";
        $runsales = $run;

        foreach($investments as $key => $investment){
            foreach($investment as $int => $i){
            if (strpos($key, 'BTC_') !== false) {
                    if($i['category'] == "exchange"){
                        $symbol = str_replace('BTC_', '', $key);
                        $fee = $i['amount'] * $i['fee'];
                        if($i['type'] == "buy"){
                            $amount = number_format($i['amount'] - $fee, 7, '.', '');
                        } else {
                            $amount = $i['amount']; // number_format($amount, 7)
                        }
                         echo 'Crypto: ' . $symbol . " // GlobalTradeId: " . $i['globalTradeID'] . " // TradeID: " . $i['tradeID'] . " // Order#: " . $i['orderNumber'] . " // Type: " . $i['type'] . " // Date: " . $i['date'] . ' // Amount: ' . $i['amount'] . ' // Fee: ' . number_format($fee, 8) . ' // Calculated: ' . number_format($amount, 7) . ' // Price: ' . $i['rate'] . ' // TOTAL: ' . $i['total'] . '<br>';
                        if($i['type'] == "buy" && $runsales == 0){ //if its a buy
                            if(Investment::where('bittrex_id', $i['orderNumber'])->first()){ // If the order exists already we need to add to it
                                if(Trade::where('tradeid', $i['tradeID'])->first()){ // Check if this trade has already been processed.

                                } else { // If it haven't, then lets start
                                    /* Start by making a brand new trade */
                                    $trade = new Trade;
                                    $trade->userid = Auth::user()->id;
                                    $trade->tradeid = $i['tradeID'];
                                    $trade->ordernumber = $i['orderNumber'];
                                    $trade->amount = $i['amount'];
                                    $trade->fee = $i['fee'];
                                    $trade->total = $i['total'];
                                    $trade->per = $i['rate'];
                                    $trade->date = $i['date'];
                                    $trade->type = $i['type'];
                                    $trade->save();


                                    /* Now lets get the investment as we are going to add to it. */
                                    $coin = Investment::where('bittrex_id', $i['orderNumber'])->first();
                                    /* Since Polo has weird format we need to re-format it.. */
                                    $ddate = strtotime($i['date']);
                                    $newformat = date('Y-m-d', $ddate);
                                    $date = $newformat;
                                    /* Now lets get the price of BTC on the day the order/trade was made */
                                    if($date != date('Y-m-d')){ //If its today then we succeeded.
                                    $client = new \GuzzleHttp\Client();
                                    try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                    $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                                    $response = $res->getBody();
                                    $prices = json_decode($response, true);
                                    $prevbtc = 0;
                                    foreach($prices['bpi'] as $price){
                                        $prevbtc = $price;
                                    }
                                    } catch(GuzzleHttp\Exception\ClientException $e){
                                            $prevbtc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                        }
                                    } else {
                                        $prevbtc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                    /* Now lets add to the order. */
                                    $coin->amount += $amount;
                                    $coin->usd_total += $i['total'] * $prevbtc;
                                    $coin->save();
                                    /* And finally add to the user! */
                                    $user->invested += $i['total'] * $prevbtc;
                                    $user->save();
                                }
                            } else { //Doesnt exist we now need to make a new coin and a new trade
                                /* Check if the currency exist in the Polo DB, otherwise abort */
                                if(Polo::where('symbol', $symbol)->first()){
                                    if(!Trade::where('tradeid', $i['tradeID'])->first()){
                                    /* First make the trade */
                                    $trade = new Trade;
                                    $trade->userid = Auth::user()->id;
                                    $trade->tradeid = $i['tradeID'];
                                    $trade->ordernumber = $i['orderNumber'];
                                    $trade->amount = $amount; //This is the amount with the fees
                                    $trade->fee = $i['fee'];
                                    $trade->total = $i['total'];
                                    $trade->per = $i['rate'];
                                    $trade->date = $i['date'];
                                    $trade->type = $i['type'];
                                    $trade->save();

                                    /* Now lets make the order/Investment */
                                    $coin = new Investment;
                                    $coin->bittrex_id = $i['orderNumber'];
                                    $coin->userid = Auth::user()->id;
                                    $coin->crypto = $symbol;
                                    $coin->bought_at = $i['rate'];
                                    $coin->date = $i['date'];
                                    $coin->amount = $amount; //Amount with the fees
                                    /* Since Polo has weird format we need to re-format it.. */
                                    $ddate = strtotime($i['date']);
                                    $newformat = date('Y-m-d', $ddate);
                                    $coin->date_bought = $newformat;
                                    $date = $newformat;
                                    /* Now lets get the price of BTC on the day the order/trade was made */
                                    if($date != date('Y-m-d')){ //If its today then we succeeded.
                                    $client = new \GuzzleHttp\Client();
                                    try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                                    $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                                    $response = $res->getBody();
                                    $prices = json_decode($response, true);
                                    $prevbtc = 0;
                                    foreach($prices['bpi'] as $price){
                                        $prevbtc = $price;
                                    }
                                    } catch(GuzzleHttp\Exception\ClientException $e){
                                            $prevbtc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                        }
                                    } else {
                                        $prevbtc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                    }
                                    //Now We set the paid amount and also add it to the users invested.
                                    $coin->usd_total += $i['total'] * $prevbtc;
                                    $coin->save();
                                    $user->invested += $i['total'] * $prevbtc;
                                    $user->save();
                                } //Closes Polo Check
                                }
                            }
                        } elseif($runsales == 1) { //If its a sell
                            /* Now this is all that we have been waiting for..*/
                            if(Trade::where('tradeid', $i['tradeID'])->first()){ // Check if this trade has already been processed.
                                /* If this sale has already been processed, we dont calculate it! */
                                echo "trade found<br>";
                            } else {
                            /* Variables */

                            /* Start by getting all the invesments*/
                            $coins = Investment::where([['crypto', '=', $symbol], ['userid', '=', $user->id]])->get();
                            $coins_sorted = $coins->sortBy('date');
                            /* First make a new trade with the sale! */
                            echo "hello now we make the sell trade 1 <br>";
                            $trade = new Trade;
                            $trade->userid = Auth::user()->id;
                            $trade->tradeid = $i['tradeID'];
                            $trade->ordernumber = $i['orderNumber'];
                            $trade->amount = $amount;
                            $trade->fee = $i['fee'];
                            $trade->total = $i['total'];
                            $trade->per = $i['rate'];
                            $trade->date = $i['date'];
                            $trade->type = $i['type'];
                            $trade->save();

                            // Now lets get the BTC price of the day sold */
                            /* Since Polo has weird format we need to re-format it.. */
                            $ddate = strtotime($i['date']);
                            $newformat = date('Y-m-d', $ddate);
                            $date = $newformat;
                            /* Now lets get the price of BTC on the day the order/trade was made */
                            if($date != date('Y-m-d')){ //If its today then we succeeded.
                            $client = new \GuzzleHttp\Client();
                            try{ //There can go something wrong so we need to make sure it doesnt fuck up.
                            $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                            $response = $res->getBody();
                            $prices = json_decode($response, true);
                            $prevbtc = 0;
                            foreach($prices['bpi'] as $price){
                                $prevbtc = $price;
                            }
                            } catch(GuzzleHttp\Exception\ClientException $e){
                                    $prevbtc = Crypto::where('symbol', 'btc')->first()->price_usd;
                                }
                            } else {
                                $prevbtc = Crypto::where('symbol', 'btc')->first()->price_usd;
                            }

                            /* Now Im not sure what to do so lets test */
                            foreach($coins_sorted as $coin){
                                if($coin->sold_at == null){ //Aslong as the order is not sold we continue
                                    if($amount > 0){ // Aslong as the amount is not less than 0 we should proceed.
                                        if($coin->amount <= $amount){ //If the investments amount is equal or less than the sell amount
                                            //First lets mark the investment as sold
                                            $sale = Investment::where('sale_id', $i['orderNumber'])->first();

                                            if($sale){
                                                $user->invested -= $sale->usd_total;
                                                $sale->usd_total = ($sale->usd_total / $sale->amount) * ($sale->amount + $i['amount']);
                                                $sale->amount += $coin->amount;
                                                $sale->sold_for = ($i['rate'] * $sale->amount) * $prevbtc;
                                                $sale->save();

                                                $coin->delete();

                                                $user->invested += $sale->usd_total;
                                                $user->save();

                                            } else {
                                            $coin->sold_at = $i['rate'];
                                            $coin->sold_for = ($i['rate'] * $coin->amount) * $prevbtc;
                                            $coin->date_sold = $newformat;
                                            $coin->sale_id = $i['orderNumber'];
                                            $coin->save();
                                            //Now that the coin is marked as sold, remove the coin amount from the sell amount.
                                            $amount -= $coin->amount;

                                            //Now update the users investment
                                            $user->invested -= $coin->usd_total;
                                            $user->save();

                                            echo "I just marked a investment as sold., ID: ". $coin->id ."<br>";
                                            }
                                            //Thats it for this area... I hope.
                                        } else { //If the amount is more than the coin amount, we need to do this.
                                            //First find the sale with the same orderID
                                            $sale = Investment::where('sale_id', $i['orderNumber'])->first();
                                            if($sale){
                                                $oldamount = $coin->amount;
                                                $coin->amount -= $amount;
                                                if((($coin->amount * $coin->bought_at) * Crypto::where('symbol', 'btc')->first()->price_btc) < 0.3){
                                                    $coin->delete();
                                                } else {
                                                    $coin->save();
                                                }

                                                $sale->amount += $amount;
                                                $sale->sold_for = ($i['rate'] * $sale->amount) * $prevbtc;
                                                $sale->save();
                                            echo "I just added more to a sale, SALE ID:". $sale->id ." COIN ID: ". $coin->id . " Amount before: ". $oldamount . " Amount: ". $amount . " FEE: " . $fee ."<br>";
                                            } else {
                                                $newcoin = new Investment;
                                                $newcoin->bittrex_id = $coin->bittrex_id;
                                                $newcoin->userid = Auth::user()->id;
                                                $newcoin->crypto = $symbol;
                                                $newcoin->amount = $amount;
                                                $newcoin->bought_at = $coin->bought_at;
                                                $newcoin->sold_at = $i['rate'];
                                                $newcoin->sold_for = ($i['rate'] * $amount) * $prevbtc;
                                                $newcoin->date_sold = $newformat;
                                                $newcoin->date_bought = $coin->date_bought;
                                                $newcoin->sale_id = $i['orderNumber'];
                                                $newcoin->usd_total = ($coin->usd_total / $coin->amount) * $amount;
                                                $newcoin->save();

                                                $coin->amount -= $amount;

                                                $amount = 0;
                                                if((($coin->amount * $coin->bought_at) * Crypto::where('symbol', 'btc')->first()->price_btc) < 0.0001){
                                                    $coin->delete();
                                                } else {
                                                    $coin->save();
                                                }

                                                $user->invested -= $newcoin->usd_total;
                                                $user->save();
                                            echo "I just made a new investment INVESTMENT ID:". $coin->id ." NEW ID: ". $newcoin->id."<br>";
                                            }


                                        }
                                    }
                                }

                            }



                            }

                        }






                    }
            }
            }
        }

 /*
                         echo 'Crypto: ' . $symbol . " // GlobalTradeId: " . $i['globalTradeID'] . " // TradeID: " . $i['tradeID'] . " // Order#: " . $i['orderNumber'] . " // Type: " . $i['type'] . " // Date: " . $i['date'] . ' // Amount: ' . $i['amount'] . ' // Fee: ' . number_format($fee, 8) . ' // Calculated: ' . number_format($amount, 7) . ' // Price: ' . $i['rate'] . ' // TOTAL: ' . $i['total'] . '<br>';
                         */

    }



}
