<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Stat;
use App\Investment;

class LeaderController extends Controller
{
   public function index(){
       return view('leaderboards');
       
       
   }

    
    public static function netWorth($id){
        $investments = Investment::where([['userid', $id], ['sold_at', '=', null]])->get();
        $user = User::where('id', $id)->first();
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
   
    
    
    
    
}
