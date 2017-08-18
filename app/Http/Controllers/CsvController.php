<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use App\BittrexTrade;
use DB;
use Excel;
use Auth;
use User;
use App\Key;

class CsvController extends Controller
{
  public function importExport()
	{
		return view('importExport');
	}


	public function importExcel(Request $request)
	{
		if($request->hasFile('csv')){
      $client = new \GuzzleHttp\Client();
			$path = $request->file('csv')->getRealPath();
			$data = Excel::load($path, function($reader) {}, 'UTF-8')->get();

			if(!empty($data) && $data->count()){
				foreach ($data as $key => $value) {
          $exchange = trim(mb_convert_encoding($value->exchange, 'UTF-8', 'UCS-2'));
          $currencies = explode("-", $exchange);
          $price = floatval(trim(mb_convert_encoding($value->price, 'UTF-8', 'UCS-2')));
          $amount = floatval(trim(mb_convert_encoding($value->quantity, 'UTF-8', 'UCS-2')));
          $limit = trim(mb_convert_encoding($value->limit, 'UTF-8', 'UCS-2'));
          $fee = floatval(trim(mb_convert_encoding($value->commissionpaid, 'UTF-8', 'UCS-2')));
          if($price != 0 && $amount != 0)
          {
		       $insert[] = ['userid' => Auth::user()->id, 'tradeid' => trim(mb_convert_encoding($value->orderuuid, 'UTF-8', 'UCS-2')), 'date' => date('Y-m-d H:m:s', strtotime(trim(mb_convert_encoding($value->closed, 'UTF-8', 'UCS-2')))), 'type' => trim(mb_convert_encoding($value->type, 'UTF-8', 'UCS-2')), 'market' => $currencies[0], 'handled' => 0, 'currency' => $currencies[1], 'price' => $price / $amount, 'fee' => $fee, 'amount' => $amount, 'total' => $price];

           $oldtrade = BittrexTrade::where('tradeid', Auth::user()->id.trim(mb_convert_encoding($value->orderuuid, 'UTF-8', 'UCS-2')))->first();

           if(!$oldtrade)
           {
             $trade = new BittrexTrade;
             $trade->userid = Auth::user()->id;
             $trade->tradeid = Auth::user()->id.trim(mb_convert_encoding($value->orderuuid, 'UTF-8', 'UCS-2'));
             $trade->date = date('Y-m-d H:m:s', strtotime(trim(mb_convert_encoding($value->closed, 'UTF-8', 'UCS-2'))));
             $trade->type = trim(mb_convert_encoding($value->type, 'UTF-8', 'UCS-2'));
             $trade->market = $currencies[0];
             if($currencies[1] == "ANS")
             {
               $trade->currency = "NEO";
             } else {
               $trade->currency = $currencies[1];
             }
             $trade->handled = 0;
             $trade->price = $price / $amount;
             $trade->amount = $amount;
             $trade->fee = $fee;
             $trade->total = $price;
             $trade->save();
           }
          }
          echo $price."<br>";
				}

			}
		}
//		return back();
	}
}
