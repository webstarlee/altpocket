@extends('layouts.app')

@section('title')
My Investments
@endsection

@section('css')
  <link rel="stylesheet" type="text/css" href="/version2/css/blocks.css?v=1.5">
  <link rel="stylesheet" type="text/css" href="/version2/css/swiper.min.css">
<style>


.more-comments {
    text-align: center;
    padding: 3px 0;
    font-size: 12px;
    color: #515365;
    display: block;
    font-weight: 700;
    margin: 0 auto;
}

</style>

@endsection

@section('content')
<?php

use Jenssegers\Agent\Agent;

$multiplier = Auth::user()->getMultiplier();
$api = Auth::user()->api;
$currency = Auth::user()->getCurrency();
if($currency != 'BTC' && $currency != 'USD')
{
  $fiat = DB::table('multipliers')->where('currency', $currency)->first()->price;
  $symbol2 = Auth::user()->getSymbol();
  $symbol = "";
} else {
  $fiat = 1;
  $symbol = Auth::user()->getSymbol();
  $symbol2 = "";
}



$agent = new Agent();

?>
    <div id="content_wrapper" class="">
    <div id="header_wrapper" class="header-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <header id="header">
                        <h1>My investments</h1>
                        <nav class="btn-fab-group" style="float:right;margin-top:15px;margin-bottom:20px;z-index:50!important;">
                          <button class="btn btn-primary btn-fab fab-menu" data-fab="down">
                            <i class="zmdi zmdi-plus"></i>
                          </button>
                          <ul class="nav-sub">
                            <li data-toggle="tooltip" data-placement="left" title="New Investment"> <a data-toggle="modal" data-target="#add_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm"><i class="zmdi zmdi-plus"></i></a></li>
                            <li data-toggle="tooltip" data-placement="left" title="Add Mining"> <a data-toggle="modal" data-target="#mining_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm">M</a></li>
                            <li data-toggle="tooltip" data-placement="left" title="Sell Amount"> <a data-toggle="modal" data-target="#sell_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm"><i class="fa fa-money"></i></a></li>
                            @if(DB::table('keys')->where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Poloniex']])->exists())
                            <li> <a id="import_polo" href="javascript:void(0)" data-toggle="tooltip" data-placement="left" title="Import orders from Poloniex" class="btn btn-fab btn-green btn-fab-sm">P</a></li>
                              @endif
                            @if(DB::table('keys')->where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Bittrex']])->exists())
                            <li data-toggle="tooltip" data-placement="left" title="Import orders from Bittrex"> <a id="import_bittrex" href="javascript:void(0)" class="btn btn-info btn-fab btn-fab-sm">B</a></li>
                              @endif
                            <li data-toggle="tooltip" data-placement="left" title="Reset Data"> <a data-toggle="modal" data-target="#reset_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm"><i class="zmdi zmdi-delete"></i></a></li>
                          </ul>
                        </nav>
                    </header>
                </div>
            </div>
        </div>
    </div>
        <div id="content" class="container-fluid">
            <div class="content-body">
                <div class="row">
                </div>
                <div class="row">
                  <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Total Investment</h2>
                      </header>
                        <?php
                            $decimal = 2;

                            if(Auth::user()->getCurrency() == "BTC")
                            {
                                $decimal = 5;
                            }
                        ?>
                        @if(Auth::user()->algorithm == 2)
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;cursor:pointer;font-size:1.2em!important;" data-toggle="tooltip" data-placement="top" title="Invested alltime overall. (Deposits)">{!! $symbol !!}{{number_format(Auth::user()->getInvested(Auth::user()->getCurrency()), $decimal)}} {!! $symbol2 !!}</h1>
                      </div>
                        @else
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Paid amount for active investments.">{!! $symbol !!}{{number_format(Auth::user()->getPaid(Auth::user()->getCurrency()), $decimal)}} {!! $symbol2 !!}</h1>
                      </div>
                        @endif
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Net Worth</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;cursor:pointer;font-size:1.2em!important;" data-toggle="tooltip" data-placement="top" title="Holdings on exchanges and manual investments. (Balances)">{!! $symbol !!}{{number_format($networth * $multiplier, $decimal)}} {!! $symbol2 !!}</h1>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Profit</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 @if(($networth * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency()) > 0) style="color:#73c04d;cursor:pointer;font-size:1.2em!important;" @else style="color:#de6b6b;cursor:pointer;font-size:1.2em!important;"; @endif data-toggle="tooltip" data-placement="top" title="Networth substracted with your deposits.">{!! $symbol !!}{{number_format((($networth * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency())), $decimal)}} {!! $symbol2 !!}</h1>
                        </div>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Current Profit</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 @if(((Auth::user()->getActiveWorth(Auth::user()->api)) - (Auth::user()->getPaid('BTC'))) > 0) style="color:#73c04d;cursor:pointer;font-size:1.2em!important;" @else style="color:#de6b6b;cursor:pointer;font-size:1.2em!important;"; @endif data-toggle="tooltip" data-placement="top" title="Profit made on active investments.">{!! $symbol !!}{{number_format((Auth::user()->getActiveWorth(Auth::user()->api) * Auth::user()->getMultiplier() - Auth::user()->getPaid(Auth::user()->currency)), $decimal)}} {!! $symbol2 !!}</h1>



                        </div>
                    </div>
                  </div>
                </div>
                <div class="row">
											<div class="col-md-12 col-lg-12">
												<div class="card">
													<header class="card-heading p-0">
														<div class="tabpanel m-b-30">
															<ul class="nav nav-tabs nav-justified">
																<li class="active " role="presentation"><a href="#investments-active" data-toggle="tab" aria-expanded="true">Active Investments</a></li>
																<li role="presentation"><a href="#investments-sold" data-toggle="tab" aria-expanded="true">Sold Investments</a></li>
																<li role="presentation"><a href="#balances" data-toggle="tab" aria-expanded="true">Balances</a></li>															</ul>
														</div>
														<div class="card-body">
															<div class="tab-content">

				                        <div class="tab-pane fadeIn" id="summary">
                                </div>

                                <div class="tab-pane fadeIn" id="balances">
                                    <div class="row">
                                    @if(count($balances) >= 1)
                                    <h2 style="text-align:center">Balances</h2>
                                    <hr>
                                    @endif
                                    @foreach($balances as $balance)
                                      <?php
                                        $price = Auth::user()->getPrice($balance->currency, 'Balance', $balance->exchange);
                                        $multiplier = Auth::user()->getMultiplier();
                                       ?>

                                       <figure class="col-xs-12 col-sm-4 col-md-4">
                                         <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                             <header class="card-heading">
                                               <ul class="card-actions icons left-top">
                                                 @if($balance->exchange != "Manual")
                                                 <li>
                                                   <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified balance from {{$balance->exchange}}.">verified_user</i>
                                                   </li>
                                                 </ul>
                                                @endif
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$balance->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center">{!! $symbol !!}{{number_format(($balance->amount * $price * $multiplier),2)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{number_format($balance->amount, 5)}} {{$balance->currency}})</p>
                                          </div>
                                       </div>
                                       </figure>
                                    @endforeach
                                    </div>
                                    @if(count($minings) >= 1)
                                    <div class="row">
                                    <h2 style="text-align:center">Mined Assets</h2>
                                    <hr>
                                    @foreach($minings as $mining)
                                      <?php
                                        $price = Auth::user()->getPrice($mining->currency, 'Mining', 'Manual');
                                        $multiplier = Auth::user()->getMultiplier();
                                       ?>

                                       <figure class="col-xs-12 col-sm-4 col-md-4">
                                         <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                             <header class="card-heading">
                                               <ul class="card-actions icons left-top">
                                                 <li>
                                                   <i class="fa fa-microchip" style="color:#f77b5e;cursor:pointer;" data-toggle="tooltip" title="Mined Investment"></i>
                                                   </li>
                                                 </ul>
                                                   <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                       <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($mining->date_mined))}}</span></li>
                                                     </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/mining/{{$mining->id}}">Delete</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                             </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$mining->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($mining->amount * $price),5)}}" data-html="true">{!! $symbol !!}{{number_format(($mining->amount * $price * $multiplier),2)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$mining->amount}} {{$mining->currency}} Mined)</p>

                                           <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($mining->amount * $price),5)}}" data-html="true">{!! $symbol !!}{{number_format((($mining->amount * $price) * $multiplier) - (($mining->amount * $mining->bought_at) * $mining->btc_price_bought_btc), 2)}}{!! $symbol2 !!}</span>
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                             {{DB::table('cryptos')->where('symbol', $mining->currency)->first()->percent_change_24h}}%
                                             </span>

                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($mining->amount * $price),8)}}" data-html="true">{!! $symbol !!}{{number_format(($mining->amount * $price) * $multiplier, 2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($price),8)}}" data-html="true">{!! $symbol !!}{{number_format($price * $multiplier,5)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                       </figure>
                                    @endforeach
                                    </div>
                                  @endif
                                </div>

																<div class="tab-pane fadeIn active" id="investments-active">
                                  <div class="row">

                                            @if(Auth::user()->summed != 0)
                                            @foreach($buys_summed as $investment)
                                              <?php
                                                //Lets rewrite this nicely..

                                                //Investment Market
                                                $market = $investment->market;

                                                //Basic stuff
                                                $price = Auth::user()->getPrice($investment->currency, $investment->market, 'Poloniex') * $investment->poloniex_amount;
                                                $manualprice = Auth::user()->getPrice($investment->currency, $investment->market, 'Manual') * $investment->manual_amount;
                                                $bitprice = Auth::user()->getPrice($investment->currency, $investment->market, 'Bittrex') * $investment->bittrex_amount;

                                                $price = ($price + $manualprice + $bitprice) / $investment->amount;

                                                $multiplier = Auth::user()->getMultiplier();
                                                $previous = 0;
                                                $decimal1 = 2;
                                                $decimal2 = 5;
                                                $decimal3 = 9;

                                                if($market == "BTC")
                                                {
                                                  $btc = 1;
                                                  $innersymbol = "<i class='fa fa-btc'></i>";
                                                } elseif($market == "ETH")
                                                {
                                                  $btc = 1;
                                                  $innersymbol = "<img src='/icons/32x32/ETH.png' width='24' height='24'> ";
                                                } elseif($market == "USDT")
                                                {
                                                  $btc = \App\Crypto::where('symbol', 'BTC')->first()->price_usd;
                                                  $innersymbol = "<i class='fa fa-usd'></i>";
                                                }

                                                //Different depending on the currency!
                                                if(Auth::user()->getCurrency() == "USD")
                                                {
                                                  if($market == "BTC")
                                                  {
                                                    $decimal1 = 2;
                                                    $decimal2 = 5;
                                                    $decimal3 = 9;


                                                    $previous = 1;
                                                    $previousmultiplier = $investment->btc_price_bought_usd;
                                                  } elseif($market == "USDT")
                                                  {
                                                    $decimal1 = 2;
                                                    $decimal2 = 2;
                                                    $decimal3 = 9;

                                                    $previous = $investment->btc_price_bought_usd;
                                                    $previousmultiplier = $investment->btc_price_bought_usd;
                                                  } elseif($market == "ETH")
                                                  {
                                                    $decimal1 = 5;
                                                    $decimal2 = 5;
                                                    $decimal3 = 9;

                                                    $multiplier = Auth::user()->getEthMultiplier();
                                                    $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                                    $previous = 1;
                                                  }
                                                } elseif(Auth::user()->getCurrency() == "BTC")
                                                {
                                                  if($market == "BTC")
                                                  {
                                                    $previous = 1;
                                                    $decimal1 = 5;
                                                    $decimal2 = 5;
                                                    $decimal3 = 9;
                                                    $previousmultiplier = 1;
                                                  } elseif($market == "USDT")
                                                  {
                                                    $previous = $investment->btc_price_bought_usd;
                                                    $decimal1 = 5;
                                                    $decimal2 = 9;
                                                    $previousmultiplier = 1;
                                                  } elseif($market == "ETH")
                                                  {
                                                    $multiplier = Auth::user()->getEthMultiplier();
                                                    $decimal1 = 5;
                                                    $decimal2 = 2;
                                                    $decimal3 = 9;
                                                    $previousmultiplier = 1 / $investment->btc_price_bought_eth;
                                                    $previous = 1;
                                                  }
                                                } else {
                                                  if($market == "BTC")
                                                  {
                                                    $previous = 1;
                                                    $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                                  } elseif($market == "USDT")
                                                  {
                                                    $decimal1 = 2;
                                                    $decimal2 = 2;
                                                    $decimal3 = 9;
                                                    $previous = $investment->btc_price_bought_usd;
                                                    $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                                  } elseif($market == "ETH")
                                                  {
                                                    $multiplier = Auth::user()->getEthMultiplier();
                                                    $previousmultiplier = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $fiat;
                                                    $previous = 1;
                                                  }
                                                }
                                               ?>
                                               <figure class="col-xs-12 col-sm-4 col-md-4">
                                                 <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                                     <header class="card-heading">
                                                       <ul class="card-actions icons left-top">
                                                         <li>
                                                           <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex/Bittrex.">verified_user</i>
                                                           </li>
                                                         </ul>
                                                       <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                           <li><span style="font-size:11px" class="text-muted">Summed Up</span></li>
                                                       </ul>
                                                       <ul class="card-actions icons right-top">
                                                         <li>
                                                           <a href="https://poloniex.com/exchange#BTC_{{$investment->currency}}"><img src="/icons/32x32/{{$investment->market}}.png" style="cursor:pointer;margin-top:-5px!important" data-toggle="tooltip" title="This investment was done with BTC." width="24" height="24"></a>
                                                         </li>
                                                         </ul>
                                                     </header>
                                                   <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                       <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                                   </div>
                                                   <div class="card-body">
                                                     <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format(($investment->amount * $price),$decimal2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier,$decimal1)}}{!! $symbol2 !!}</h4>


                                                      @if($investment->amount <= 10)
                                                      <p class="text-center" style="font-size:11px;">({{$investment->amount}}
                                                      @elseif($investment->amount <= 100)
                                                      <p class="text-center" style="font-size:11px;">({{number_format($investment->amount, 2)}}
                                                      @elseif($investment->amount <= 1000)
                                                      <p class="text-center" style="font-size:11px;">({{number_format($investment->amount, 1)}}
                                                      @elseif($investment->amount <= 10000)
                                                      <p class="text-center" style="font-size:11px;">({{number_format($investment->amount, 1)}}
                                                      @else
                                                      <p class="text-center" style="font-size:11px;">({{number_format($investment->amount, 0)}}
                                                      @endif
                                                        {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

                                                      @if((($investment->amount * $price) / ($btc) * $multiplier) > (($investment->amount * $investment->bought_at) / ($previous) * $previousmultiplier))


                                                       <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol}}
                                                         {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), $decimal1)}}" data-html="true">


                                                         {!! $symbol !!}{{number_format((($investment->amount * $price) / ($btc) * $multiplier) - (($investment->amount * $investment->bought_at) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}



                                                       </span>
                                                       <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                         {{number_format((100/((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))), 2)}}%
                                                       </span>

                                                     @else
                                                     <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol}}
                                                        {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), $decimal1)}}" data-html="true">



                                                        {!! $symbol !!}{{number_format((($investment->amount * $price) / ($btc) * $multiplier) - (($investment->amount * $investment->bought_at) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}



                                                    </span>
                                                       <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                         {{number_format((100/((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))), 2)}}%
                                                       </span>
                                                     @endif
                                                       <hr style="margin-top:40px;">
                                                       <a href="#" class="more-comments">View more</a>
                                                 <div class="swiper-container extra">
																										<div class="swiper-wrapper">
																											<div class="swiper-slide">
                                                       <div class="friend-count"  data-swiper-parallax="-500">
                                                       <span style="float:left;">Then</span>
                                                       <span style="float:right;">Now</span>
                                                       <br>
                                                       <span style="float:left;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format(($investment->bought_at * $investment->amount), $decimal1)}}" data-html="true">{!! $symbol !!}{{number_format((($investment->bought_at * $investment->amount) / ($previous)) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                       <span style="float:right;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format((($investment->amount * $price)), $decimal1)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                       <br>
                                                       <span style="float:left;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format((($investment->bought_at)), $decimal3)}}" data-html="true">{!! $symbol !!}{{number_format( ($investment->bought_at) / ($previous) * $previousmultiplier  ,$decimal2)}}{!! $symbol2 !!}</span>
                                                       <span style="float:right;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format((($price)), $decimal3)}}" data-html="true">{!! $symbol !!}{{number_format(($price) / ($btc) * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                                       <br>
                                                       </div>
                                                     </div>
                                                       <div class="swiper-slide">
                                                         <p class="friend-about" data-swiper-parallax="-500" style="text-align:center;">
                                                           Made this as my first investment ever. Wish me good luck!
                                                         </p>
                                                      </div>
                                                   </div>
																									<div class="swiper-pagination"></div>
                                                 </div>
                                                   </div>
                                                 </div>
                                               </figure>
                                            @endforeach

                                            @else

                                            @foreach($p_investments as $investment)
                                              @if($investment->sold_at == null)
                                              <?php
                                                //Lets rewrite this nicely..

                                                //Investment Market
                                                $market = $investment->market;

                                                //Basic stuff
                                                $price = Auth::user()->getPrice($investment->currency, $investment->market, 'Poloniex');
                                                $multiplier = Auth::user()->getMultiplier();
                                                $previous = 1;
                                                $decimal1 = 2;
                                                $decimal2 = 5;
                                                $decimal3 = 9;

                                                if($market == "BTC")
                                                {
                                                  $btc = 1;
                                                  $innersymbol = "<i class='fa fa-btc'></i>";
                                                } elseif($market == "ETH")
                                                {
                                                  $btc = 1;
                                                  $innersymbol = "<img src='/icons/32x32/ETH.png' width='24' height='24'> ";
                                                } elseif($market == "USDT")
                                                {
                                                  $btc = \App\Crypto::where('symbol', 'BTC')->first()->price_usd;
                                                  $innersymbol = "<i class='fa fa-usd'></i>";
                                                }

                                                //Different depending on the currency!
                                                if(Auth::user()->getCurrency() == "USD")
                                                {
                                                  if($market == "BTC")
                                                  {
                                                    $decimal1 = 2;
                                                    $decimal2 = 5;
                                                    $decimal3 = 9;


                                                    $previous = 1;
                                                    $previousmultiplier = $investment->btc_price_bought_usd;
                                                  } elseif($market == "USDT")
                                                  {
                                                    $decimal1 = 2;
                                                    $decimal2 = 2;
                                                    $decimal3 = 9;

                                                    $previous = $investment->btc_price_bought_usd;
                                                    $previousmultiplier = $investment->btc_price_bought_usd;
                                                  } elseif($market == "ETH")
                                                  {
                                                    $decimal1 = 5;
                                                    $decimal2 = 5;
                                                    $decimal3 = 9;

                                                    $multiplier = Auth::user()->getEthMultiplier();
                                                    $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                                    $previous = 1;
                                                  }
                                                } elseif(Auth::user()->getCurrency() == "BTC")
                                                {
                                                  if($market == "BTC")
                                                  {
                                                    $previous = 1;
                                                    $decimal1 = 5;
                                                    $decimal2 = 5;
                                                    $decimal3 = 9;
                                                  } elseif($market == "USDT")
                                                  {
                                                    $previous = $investment->btc_price_bought_usd;
                                                    $decimal1 = 5;
                                                    $decimal2 = 9;
                                                    $previousmultiplier = 1;
                                                  } elseif($market == "ETH")
                                                  {
                                                    $multiplier = Auth::user()->getEthMultiplier();
                                                    $decimal1 = 5;
                                                    $decimal2 = 2;
                                                    $decimal3 = 9;
                                                    $previousmultiplier = 1 / $investment->btc_price_bought_eth;
                                                    $previous = 1;
                                                  }
                                                } else {
                                                  if($market == "BTC")
                                                  {
                                                    $previous = 1;
                                                    $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                                  } elseif($market == "USDT")
                                                  {
                                                    $decimal1 = 2;
                                                    $decimal2 = 2;
                                                    $decimal3 = 9;
                                                    $previous = $investment->btc_price_bought_usd;
                                                    $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                                  } elseif($market == "ETH")
                                                  {
                                                    $multiplier = Auth::user()->getEthMultiplier();
                                                    $previousmultiplier = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $fiat;
                                                    $previous = 1;
                                                  }
                                                }
                                               ?>
                                               <figure class="col-xs-12 col-sm-4 col-md-4">
                                                 <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                                     <header class="card-heading">
                                                       <ul class="card-actions icons left-top">
                                                         <li>
                                                           <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex.">verified_user</i>
                                                           </li>
                                                         </ul>
                                                       <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                           <li><span style="font-size:11px" class="text-muted">{{$investment->date_bought}}</span></li>
                                                       </ul>
                                                       <ul class="card-actions icons right-top">
                                                         <li class="dropdown">
                                                           <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                             <i class="zmdi zmdi-more-vert"></i>
                                                           </a>
                                                           <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                             <li>
                                                               <a href="/investments/remove/polo/{{$investment->id}}">Hide</a>
                                                             </li>
                                                           </ul>
                                                         </li>
                                                         <li>
                                                           <a href="https://poloniex.com/exchange#BTC_{{$investment->currency}}"><img src="/icons/32x32/{{$investment->market}}.png" style="cursor:pointer;margin-top:-5px!important" data-toggle="tooltip" title="This investment was done with BTC." width="24" height="24"></a>
                                                         </li>
                                                         </ul>
                                                     </header>

                                                   <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                       <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                                   </div>
                                                   <div class="card-body">
                                                     <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format(($investment->amount * $price),$decimal2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier,$decimal1)}}{!! $symbol2 !!}</h4>



                                                     @if($investment->amount <= 10)
                                                     <p class="text-center" style="font-size:11px;">({{$investment->amount}}
                                                     @elseif($investment->amount <= 100)
                                                     <p class="text-center" style="font-size:11px;">({{number_format($investment->amount, 2)}}
                                                     @elseif($investment->amount <= 1000)
                                                     <p class="text-center" style="font-size:11px;">({{number_format($investment->amount, 1)}}
                                                     @elseif($investment->amount <= 10000)
                                                     <p class="text-center" style="font-size:11px;">({{number_format($investment->amount, 1)}}
                                                     @else
                                                     <p class="text-center" style="font-size:11px;">({{number_format($investment->amount, 0)}}
                                                     @endif



                                                       {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

                                                      @if((($investment->amount * $price) / ($btc) * $multiplier) > (($investment->amount * $investment->bought_at) / ($previous) * $previousmultiplier))


                                                       <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol}}
                                                         {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), $decimal1)}}" data-html="true">


                                                         {!! $symbol !!}{{number_format((($investment->amount * $price) / ($btc) * $multiplier) - (($investment->amount * $investment->bought_at) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}



                                                       </span>
                                                       <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                         {{number_format((100/((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))), 2)}}%
                                                       </span>

                                                     @else
                                                     <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol}}
                                                        {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), $decimal1)}}" data-html="true">



                                                        {!! $symbol !!}{{number_format((($investment->amount * $price) / ($btc) * $multiplier) - (($investment->amount * $investment->bought_at) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}



                                                    </span>
                                                       <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                         {{number_format((100/((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))), 2)}}%
                                                       </span>
                                                     @endif
                                                       <hr style="margin-top:40px;">
                                                       <div class="usd">
                                                       <span style="float:left;">Before</span>
                                                       <span style="float:right;">After</span>
                                                       <br>
                                                       <span style="float:left;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format(($investment->bought_at * $investment->amount), $decimal1)}}" data-html="true">{!! $symbol !!}{{number_format((($investment->bought_at * $investment->amount) / ($previous)) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                       <span style="float:right;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format((($investment->amount * $price)), $decimal1)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                       <br>
                                                       <span style="float:left;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format((($investment->bought_at)), $decimal3)}}" data-html="true">{!! $symbol !!}{{number_format( ($investment->bought_at) / ($previous) * $previousmultiplier  ,$decimal2)}}{!! $symbol2 !!}</span>
                                                       <span style="float:right;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format((($price)), $decimal3)}}" data-html="true">{!! $symbol !!}{{number_format(($price) / ($btc) * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                                       <br>
                                                       </div>
                                                   </div>
                                                 </div>
                                               </figure>
                                             @endif
                                            @endforeach

                                            @foreach($b_investments as $investment)
                                              @if($investment->sold_at == null)
                                              <?php
                                                //Lets rewrite this nicely..

                                                //Investment Market
                                                $market = $investment->market;

                                                //Basic stuff
                                                $price = Auth::user()->getPrice($investment->currency, $investment->market, 'Bittrex');
                                                $multiplier = Auth::user()->getMultiplier();
                                                $previous = 1;
                                                $decimal1 = 2;
                                                $decimal2 = 5;
                                                $decimal3 = 9;

                                                if($market == "BTC")
                                                {
                                                  $btc = 1;
                                                  $innersymbol = "<i class='fa fa-btc'></i>";
                                                } elseif($market == "ETH")
                                                {
                                                  $btc = 1;
                                                  $innersymbol = "<img src='/icons/32x32/ETH.png' width='24' height='24'> ";
                                                } elseif($market == "USDT")
                                                {
                                                  $btc = \App\Crypto::where('symbol', 'BTC')->first()->price_usd;
                                                  $innersymbol = "<i class='fa fa-usd'></i>";
                                                }

                                                //Different depending on the currency!
                                                if(Auth::user()->getCurrency() == "USD")
                                                {
                                                  if($market == "BTC")
                                                  {
                                                    $decimal1 = 2;
                                                    $decimal2 = 5;
                                                    $decimal3 = 9;


                                                    $previous = 1;
                                                    $previousmultiplier = $investment->btc_price_bought_usd;
                                                  } elseif($market == "USDT")
                                                  {
                                                    $decimal1 = 2;
                                                    $decimal2 = 2;
                                                    $decimal3 = 9;

                                                    $previous = $investment->btc_price_bought_usd;
                                                    $previousmultiplier = $investment->btc_price_bought_usd;
                                                  } elseif($market == "ETH")
                                                  {
                                                    $decimal1 = 5;
                                                    $decimal2 = 5;
                                                    $decimal3 = 9;

                                                    $multiplier = Auth::user()->getEthMultiplier();
                                                    $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                                    $previous = 1;
                                                  }
                                                } elseif(Auth::user()->getCurrency() == "BTC")
                                                {
                                                  if($market == "BTC")
                                                  {
                                                    $previous = 1;
                                                    $decimal1 = 5;
                                                    $decimal2 = 5;
                                                    $decimal3 = 9;
                                                  } elseif($market == "USDT")
                                                  {
                                                    $previous = $investment->btc_price_bought_usd;
                                                    $decimal1 = 5;
                                                    $decimal2 = 9;
                                                    $previousmultiplier = 1;
                                                  } elseif($market == "ETH")
                                                  {
                                                    $multiplier = Auth::user()->getEthMultiplier();
                                                    $decimal1 = 5;
                                                    $decimal2 = 2;
                                                    $decimal3 = 9;
                                                    $previousmultiplier = 1 / $investment->btc_price_bought_eth;
                                                    $previous = 1;
                                                  }
                                                } else {
                                                  if($market == "BTC")
                                                  {
                                                    $previous = 1;
                                                    $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                                  } elseif($market == "USDT")
                                                  {
                                                    $decimal1 = 2;
                                                    $decimal2 = 2;
                                                    $decimal3 = 9;
                                                    $previous = $investment->btc_price_bought_usd;
                                                    $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                                  } elseif($market == "ETH")
                                                  {
                                                    $multiplier = Auth::user()->getEthMultiplier();
                                                    $previousmultiplier = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $fiat;
                                                    $previous = 1;
                                                  }
                                                }
                                               ?>
                                               <figure class="col-xs-12 col-sm-4 col-md-4">
                                                 <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                                     <header class="card-heading">
                                                       <ul class="card-actions icons left-top">
                                                         <li>
                                                           <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Bittrex.">verified_user</i>
                                                           </li>
                                                         </ul>
                                                       <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                           <li><span style="font-size:11px" class="text-muted">{{$investment->date_bought}}</span></li>
                                                       </ul>
                                                       <ul class="card-actions icons right-top">
                                                         <li class="dropdown">
                                                           <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                             <i class="zmdi zmdi-more-vert"></i>
                                                           </a>
                                                           <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                             <li>
                                                               <a href="/investments/remove/polo/{{$investment->id}}">Hide</a>
                                                             </li>
                                                           </ul>
                                                         </li>
                                                         <li>
                                                           <a href="https://poloniex.com/exchange#BTC_{{$investment->currency}}"><img src="/icons/32x32/{{$investment->market}}.png" style="cursor:pointer;margin-top:-5px!important" data-toggle="tooltip" title="This investment was done with BTC." width="24" height="24"></a>
                                                         </li>
                                                         </ul>
                                                     </header>

                                                   <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                       <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                                   </div>
                                                   <div class="card-body">
                                                     <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format(($investment->amount * $price),$decimal2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier,$decimal1)}}{!! $symbol2 !!}</h4>



                                                     <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

                                                      @if((($investment->amount * $price) / ($btc) * $multiplier) > (($investment->amount * $investment->bought_at) / ($previous) * $previousmultiplier))


                                                       <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol}}
                                                         {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), $decimal1)}}" data-html="true">


                                                         {!! $symbol !!}{{number_format((($investment->amount * $price) / ($btc) * $multiplier) - (($investment->amount * $investment->bought_at) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}



                                                       </span>
                                                       <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                         {{number_format((100/((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))), 2)}}%
                                                       </span>

                                                     @else
                                                     <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol}}
                                                        {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), $decimal1)}}" data-html="true">



                                                        {!! $symbol !!}{{number_format((($investment->amount * $price) / ($btc) * $multiplier) - (($investment->amount * $investment->bought_at) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}



                                                    </span>
                                                       <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                         {{number_format((100/((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_at * $investment->amount) / ($previous) * $previousmultiplier))), 2)}}%
                                                       </span>
                                                     @endif
                                                       <hr style="margin-top:40px;">
                                                       <div class="usd">
                                                       <span style="float:left;">Before</span>
                                                       <span style="float:right;">After</span>
                                                       <br>
                                                       <span style="float:left;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format(($investment->bought_at * $investment->amount), $decimal1)}}" data-html="true">{!! $symbol !!}{{number_format((($investment->bought_at * $investment->amount) / ($previous)) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                       <span style="float:right;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format((($investment->amount * $price)), $decimal1)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                       <br>
                                                       <span style="float:left;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format((($investment->bought_at)), $decimal3)}}" data-html="true">{!! $symbol !!}{{number_format( ($investment->bought_at) / ($previous) * $previousmultiplier  ,$decimal2)}}{!! $symbol2 !!}</span>
                                                       <span style="float:right;cursor:pointer" data-toggle="tooltip" title="{{$innersymbol}} {{number_format((($price)), $decimal3)}}" data-html="true">{!! $symbol !!}{{number_format(($price) / ($btc) * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                                       <br>
                                                       </div>
                                                   </div>
                                                 </div>
                                               </figure>
                                             @endif
                                            @endforeach


                                            @foreach($investments as $investment)
                                              @if($investment->date_sold == null)
                                              <?php
                                                if(Auth::user())
                                                {
                                                  $price = Auth::user()->getPrice($investment->currency, 'Manual', 'Manual');
                                                  $multiplier = Auth::user()->getMultiplier();
                                                  $previous = 0;
                                                  $decimal1 = 2;
                                                  $decimal2 = 5;


                                                  if(Auth::user()->getCurrency() == "USD")
                                                  {
                                                    $previous = $investment->btc_price_bought_usd;
                                                  } elseif(Auth::user()->getCurrency() == "BTC")
                                                  {
                                                    $previous = 1;
                                                    $decimal1 = 5;
                                                    $decimal2 = 9;
                                                  } else {
                                                    $previous = $investment->btc_price_bought_usd * $fiat;
                                                  }
                                                } else {
                                                  $price = $user->getPrice($investment->currency, 'Manual', 'Manual');
                                                  $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                                  $previous = 0;
                                                  $decimal1 = 2;
                                                  $decimal2 = 5;
                                                  $symbol = "$";
                                                  $previous = $investment->btc_price_bought_usd;
                                                }
                                               ?>
                                               <figure class="col-xs-12 col-sm-4 col-md-4">
                                                 <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                                     <header class="card-heading">
                                                         <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                             <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_bought))}}</span></li>
                                                           </ul>
                                                           <ul class="card-actions icons right-top">
                                                             <li class="dropdown">
                                                               <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                                 <i class="zmdi zmdi-more-vert"></i>
                                                               </a>
                                                               <ul class="dropdown-menu btn-primary dropdown-menu-right">
                                                                 <li>
                                                                 <a href="javascript:void(0)" class="edit-coin" id="{{$investment->id}}" data-toggle="modal" data-target="#edit_modal" >Edit</a>
                                                               </li>
                                                               <li>
                                                                 <a href="javascript:void(0)" class="sell-coin" id="{{$investment->id}}" data-toggle="modal" data-target="#sold_modal">Mark as Sold</a>
                                                               </li>
                                                                 <li>
                                                                   <a href="/investments/remove/{{$investment->id}}">Remove</a>
                                                                 </li>
                                                               </ul>
                                                             </li>
                                                             </ul>
                                                     </header>
                                                   <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                       <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                                   </div>
                                                   <div class="card-body">
                                                     <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $price),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price * $multiplier),$decimal1)}}{!! $symbol2 !!}</h4>
                                                     <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}})</p>

                                                      @if((($investment->amount * $price) * $multiplier) > ($investment->amount * $investment->bought_at) * $previous)
                                                       <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                                         {!! $symbol !!}{{number_format((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>

                                                       <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                         {{number_format((100/(($investment->amount * $investment->bought_at) * $previous)) * ((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at)) * $previous), 2)}}%
                                                       </span>

                                                     @else
                                                     <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                                       {!! $symbol !!}{{number_format((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}
                                                     </span>
                                                       <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                         {{number_format((100/(($investment->amount * $investment->bought_at) * $previous)) * ((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at)) * $previous), 2)}}%
                                                       </span>
                                                     @endif
                                                       <hr style="margin-top:40px;">
                                                       <div class="usd">
                                                       <span style="float:left;">Before</span>
                                                       <span style="float:right;">After</span>
                                                       <br>
                                                       <span style="float:left;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $investment->bought_at), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $investment->bought_at) * $previous, $decimal1)}}{!! $symbol2 !!}</span>
                                                       <span style="float:right;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $price), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                       <br>
                                                       <span style="float:left;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format($investment->bought_at,8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->bought_at * $previous,$decimal2)}}{!! $symbol2 !!}</span>
                                                       <span style="float:right;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format($price ,8)}}" data-html="true">{!! $symbol !!}{{number_format($price * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                                       <br>
                                                       </div>
                                                   </div>
                                                 </div>
                                               </figure>
                                             @endif
                                            @endforeach

                                            @endif





                                  </div>
                                </div>

                              <div class="tab-pane fadeIn" id="investments-sold">
                                <div class="row">


                                  @foreach($sells_summed as $investment)
                                    @if($investment->market != "Deposit")
                                    <?php
                                      //Lets rewrite this nicely..

                                      //Investment Market
                                      $market = $investment->market;

                                      //Basic stuff
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 5;
                                      $decimal3 = 5;
                                      $add1 = 1;
                                      $add2 = 1;

                                      //Try this, before i print out shit we calculate the price in BTC then do stuff
                                      $bought_at = 0;
                                      $sold_at = 0;

                                      if($market == "BTC")
                                      {
                                        $btc = 1;
                                        $innersymbol = "<i class='fa fa-btc'></i>";
                                        $price = $investment->sold_at;
                                      } elseif($market == "ETH")
                                      {
                                        $btc = 1;
                                        $innersymbol = "<img src='/icons/32x32/ETH.png' width='24' height='24'> ";
                                      } elseif($market == "USDT")
                                      {
                                        $btc = \App\Crypto::where('symbol', 'BTC')->first()->price_usd;
                                        $innersymbol = "<i class='fa fa-usd'></i>";
                                        $price = $investment->sold_at;
                                      }

                                      if($investment->soldmarket == "BTC")
                                      {
                                        $innersymbol2 = "<i class='fa fa-btc'></i>";
                                      } elseif($investment->soldmarket == "ETH")
                                      {
                                        $innersymbol2 = "<img src='/icons/32x32/ETH.png' width='24' height='24'> ";
                                      } elseif($investment->soldmarket == "USDT")
                                      {
                                        $innersymbol2 = "<i class='fa fa-usd'></i>";
                                      }

                                      //Different depending on the currency!
                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        if($market == "BTC")
                                        {
                                          // BTC to BTC in USD
                                          if($investment->soldmarket == "BTC")
                                          {
                                            $decimal1 = 2;
                                            $decimal2 = 7;
                                            $decimal3 = 5;

                                            $previousmultiplier = $investment->btc_price_bought_usd;
                                            $prevsold = $investment->btc_price_sold_usd;

                                            $bought_at = ($investment->bought_at);
                                            $sold_at = ($investment->sold_at);
                                          //BTC to USDT in USD
                                          } elseif($investment->soldmarket == "USDT")
                                          {
                                            $decimal1 = 2;
                                            $decimal2 = 5;
                                            $decimal3 = 5;
                                            $add1 = $investment->btc_price_bought_usdt;

                                            $previousmultiplier = $investment->btc_price_bought_usd;
                                            $prevsold = $investment->btc_price_sold_usd / $investment->btc_price_sold_usdt;

                                            $bought_at = $investment->bought_at;
                                            $sold_at = $investment->sold_at;

                                          // BTC to ETH in USD
                                          }  elseif($investment->soldmarket == "ETH")
                                          {
                                            $decimal1 = 2;
                                            $decimal2 = 5;
                                            $decimal3 = 5;
                                            $add1 = $investment->btc_price_bought_eth;
                                            // Only ones we need.
                                            $previousmultiplier = $investment->btc_price_bought_usd;
                                            $prevsold =  $investment->btc_price_sold_usd / $investment->btc_price_sold_eth;

                                            $bought_at = $investment->bought_at;
                                            $sold_at = $investment->sold_at;
                                          }
                                        } elseif($market == "USDT")
                                        {
                                          //USDT TO BTC in USD
                                          if($investment->soldmarket == "BTC")
                                          {
                                            //General shit
                                            $decimal1 = 2;
                                            $decimal2 = 5;
                                            $decimal3 = 5;
                                            $add1 = 1 / $investment->btc_price_bought_usdt;
                                            $add2 = 1;

                                            $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_usdt;
                                            $prevsold = $investment->btc_price_sold_usd;

                                            // Buy & sell Prices
                                            $bought_at = ($investment->bought_at);
                                            $sold_at = ($investment->sold_at);
                                          //USDT to USDT in USD
                                          } elseif($investment->soldmarket == "USDT")
                                          {
                                            //General shit
                                            $decimal1 = 2;
                                            $decimal2 = 5;
                                            $decimal3 = 5;

                                            $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_usdt;
                                            $prevsold = $investment->btc_price_sold_usd / $investment->btc_price_sold_usdt;

                                            $bought_at = ($investment->bought_at);
                                            $sold_at = ($investment->sold_at);
                                          }
                                        } elseif($market == "ETH")
                                        {
                                          //ETH to BTC IN USD
                                          if($investment->soldmarket == "BTC")
                                          {
                                            $decimal1 = 2;
                                            $decimal2 = 5;
                                            $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                            $prevsold = $investment->btc_price_sold_usd;
                                            $add1 = 1 / $investment->btc_price_bought_eth;

                                            $bought_at = ($investment->bought_at);
                                            $sold_at = ($investment->sold_at);
                                            // ETH TO ETH IN USD
                                          } elseif($investment->soldmarket == "ETH")
                                          {
                                            $decimal1 = 2;
                                            $decimal2 = 5;
                                            $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                            $prevsold =  $investment->btc_price_sold_usd / $investment->btc_price_sold_eth;

                                            $bought_at = ($investment->bought_at);
                                            $sold_at = ($investment->sold_at);
                                            // ETH TO USDT in USD
                                          } elseif($investment->soldmarket == "USDT")
                                          {
                                            $decimal1 = 3;
                                            $decimal2 = 5;

                                            $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                            $prevsold = $investment->btc_price_sold_usd / $investment->btc_price_sold_usdt;

                                            $add1 = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                            $add2 = $investment->btc_price_sold_usd / $investment->btc_price_sold_usdt;

                                            $bought_at = ($investment->bought_at);
                                            $sold_at = ($investment->sold_at);


                                          }
                                        }
                                      }
                                      elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                          if($market == "BTC")
                                          {
                                              // BTC TO BTC in BTC
                                              if($investment->soldmarket == "BTC")
                                              {
                                                $decimal1 = 5;
                                                $decimal2 = 7;
                                                $decimal3 = 5;

                                                $previousmultiplier = 1;
                                                $prevsold = 1;

                                                $bought_at = ($investment->bought_at);
                                                $sold_at = ($investment->sold_at);
                                              //BTC TO USDT in BTC
                                              } elseif($investment->soldmarket == "USDT")
                                              {
                                                $decimal1 = 5;
                                                $decimal2 = 7;
                                                $decimal3 = 5;
                                                $add1 = $investment->btc_price_bought_usdt;

                                                $previousmultiplier = 1;
                                                $prevsold = 1 / $investment->btc_price_sold_usdt;

                                                $bought_at = $investment->bought_at;
                                                $sold_at = $investment->sold_at;
                                                //BTC TO ETH in BTC
                                              } elseif($investment->soldmarket == "ETH")
                                              {
                                                $decimal1 = 5;
                                                $decimal2 = 7;
                                                $decimal3 = 5;
                                                $add1 = $investment->btc_price_bought_eth;

                                                // Only ones we need.
                                                $previousmultiplier = 1;
                                                $prevsold =  1 / $investment->btc_price_sold_eth;

                                                $bought_at = $investment->bought_at;
                                                $sold_at = $investment->sold_at;
                                              }
                                          }
                                          elseif($market == "USDT")
                                          {
                                              // USDT TO BTC in BTC
                                              if($investment->soldmarket == "BTC")
                                              {
                                                $decimal1 = 5;
                                                $decimal2 = 7;
                                                $prevsold = 1;
                                                $previousmultiplier = 1 / $investment->btc_price_bought_usdt;
                                                $add1 = 1 / $investment->btc_price_bought_usdt;
                                                $add2 = 1;


                                                // Buy & sell Prices
                                                $bought_at = ($investment->bought_at);
                                                $sold_at = ($investment->sold_at);

                                              //USDT TO USDT in BTC
                                              } elseif($investment->soldmarket == "USDT")
                                              {
                                                $previous = $investment->btc_price_bought_usdt;
                                                $decimal1 = 5;
                                                $decimal2 = 9;
                                                $previousmultiplier = 1 / $investment->btc_price_bought_usdt;
                                                $prevsold = 1 / $investment->btc_price_sold_usdt;

                                                $bought_at = ($investment->bought_at);
                                                $sold_at = ($investment->sold_at);
                                              }
                                          }
                                          elseif($market == "ETH")
                                          {
                                            // ETH TO BTC IN BTC
                                            if($investment->soldmarket == "BTC")
                                            {
                                              $decimal1 = 5;
                                              $decimal2 = 7;
                                              $previousmultiplier = 1 / $investment->btc_price_bought_eth;
                                              $prevsold = 1;
                                              $add1 = 1/ $investment->btc_price_bought_eth;

                                              $bought_at = ($investment->bought_at);
                                              $sold_at = ($investment->sold_at);
                                              // ETH TO ETH IN BTC
                                            } elseif($investment->soldmarket == "ETH")
                                            {
                                              $decimal1 = 5;
                                              $decimal2 = 9;
                                              $previousmultiplier = 1 / $investment->btc_price_bought_eth;
                                              $prevsold =  1 / $investment->btc_price_sold_eth;

                                              $bought_at = ($investment->bought_at);
                                              $sold_at = ($investment->sold_at);
                                              // ETH TO USDT IN BTC
                                            } elseif($investment->soldmarket == "USDT")
                                            {
                                              $decimal1 = 3;
                                              $decimal2 = 5;

                                              $previousmultiplier = 1 / $investment->btc_price_bought_eth;
                                              $prevsold = 1 / $investment->btc_price_sold_usdt;

                                              $add1 = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                              $add2 = $investment->btc_price_sold_usd / $investment->btc_price_sold_usdt;

                                              $bought_at = ($investment->bought_at);
                                              $sold_at = ($investment->sold_at);
                                            }
                                          }
                                      }
                                      else {
                                        if($market == "BTC")
                                        {
                                            // BTC to BTC in EUR
                                            if($investment->soldmarket == "BTC")
                                            {
                                              //Decimals:
                                              $decimal1 = 2;
                                              $decimal2 = 5;
                                              $decimal3 = 5;

                                              $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                              $prevsold = $investment->btc_price_sold_usd * $fiat;

                                              $bought_at = ($investment->bought_at);
                                              $sold_at = ($investment->sold_at);
                                              // BTC TO USDT in EUR
                                            } elseif($investment->soldmarket == "USDT")
                                            {
                                              $decimal1 = 2;
                                              $decimal2 = 5;
                                              $decimal3 = 5;
                                              $add1 = $investment->btc_price_bought_usdt;

                                              $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                              $prevsold = $investment->btc_price_sold_usd / $investment->btc_price_sold_usdt * $fiat;

                                              $bought_at = $investment->bought_at;
                                              $sold_at = $investment->sold_at;

                                            // BTC to ETH in EUR
                                            }  elseif($investment->soldmarket == "ETH")
                                            {
                                              $add1 = $investment->btc_price_bought_eth;

                                              // Only ones we need.
                                              $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                              $prevsold = ($investment->btc_price_sold_usd / $investment->btc_price_sold_eth) * $fiat;

                                              $bought_at = $investment->bought_at;
                                              $sold_at = $investment->sold_at;
                                            }
                                        }
                                        elseif($market == "USDT")
                                        {
                                            //USDT TO BTC in EUR
                                            if($investment->soldmarket == "BTC")
                                            {
                                              //General shit
                                              $decimal1 = 2;
                                              $decimal2 = 5;
                                              $decimal3 = 5;
                                              $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                              $prevsold = $investment->btc_price_sold_usd * $fiat;
                                              // Buy & sell Prices
                                              $bought_at = ($investment->bought_at) / $investment->btc_price_bought_usdt;
                                              $sold_at = ($investment->sold_at);
                                              //USDT TO USDT in EUR
                                            } elseif($investment->soldmarket == "USDT")
                                            {
                                              $decimal1 = 2;
                                              $decimal2 = 5;
                                              $decimal3 = 5;

                                              $previousmultiplier = ($investment->btc_price_bought_usd / $investment->btc_price_bought_usdt) * $fiat;
                                              $prevsold = ($investment->btc_price_sold_usd / $investment->btc_price_sold_usdt) * $fiat;

                                              $bought_at = ($investment->bought_at);
                                              $sold_at = ($investment->sold_at);
                                            }
                                        }
                                        elseif($market == "ETH")
                                        {
                                          //ETH TO BTC IN EUR
                                          if($investment->soldmarket == "BTC")
                                          {
                                            $decimal1 = 2;
                                            $decimal2 = 5;
                                            $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth * $fiat;
                                            $prevsold = $investment->btc_price_sold_usd * $fiat;
                                            $add1 = 1 / $investment->btc_price_bought_eth;

                                            $bought_at = ($investment->bought_at);
                                            $sold_at = ($investment->sold_at);
                                          // ETH TO ETH IN EUR
                                          } elseif($investment->soldmarket == "ETH")
                                          {
                                            $decimal1 = 2;
                                            $decimal2 = 5;
                                            $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth * $fiat;
                                            $prevsold =  $investment->btc_price_sold_usd / $investment->btc_price_sold_eth * $fiat;

                                            $bought_at = ($investment->bought_at);
                                            $sold_at = ($investment->sold_at);
                                          // ETH TO USDT IN EUR
                                          } elseif($investment->soldmarket == "USDT")
                                          {
                                            $decimal1 = 3;
                                            $decimal2 = 5;

                                            $previousmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth * $fiat;
                                            $prevsold = $investment->btc_price_sold_usd / $investment->btc_price_sold_usdt * $fiat;

                                            $add1 = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                            $add2 = $investment->btc_price_sold_usd / $investment->btc_price_sold_usdt;

                                            $bought_at = ($investment->bought_at);
                                            $sold_at = ($investment->sold_at);
                                          }
                                        }
                                      }


                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex/Bittrex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">Summed Up</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                                 <a href="https://poloniex.com/exchange#BTC_{{$investment->currency}}"><img src="/icons/32x32/{{$investment->market}}.png" style="cursor:pointer;margin-top:-5px!important" data-toggle="tooltip" title="This investment was bought with {{$market}} and sold to {{$investment->soldmarket}}." width="24" height="24"></a>
                                               </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="{{$innersymbol2}} {{number_format(($investment->amount * $price),$decimal2)}}" data-html="true">

                                             {!! $symbol !!}{{number_format(($investment->amount * $sold_at) * $prevsold,$decimal1)}}{!! $symbol2 !!}

                                           </h4>



                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

                                            @if((($investment->amount * $sold_at) * $prevsold) > ($investment->amount * $bought_at) * $previousmultiplier)



                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol2}}
                                               {{number_format((($investment->amount * $sold_at)) * $add2 - (($investment->amount * $bought_at)) * $add1, $decimal3)}}" data-html="true">


                                               {!! $symbol !!}{{number_format(($investment->amount * $sold_at) * $prevsold - ($investment->amount * $bought_at) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}


                                             </span>
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                              {{number_format((100/((($bought_at * $investment->amount) * $previousmultiplier))) * (((($sold_at * $investment->amount) * $prevsold)) - ((($bought_at * $investment->amount) * $previousmultiplier))), 2)}}%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol2}}
                                              {{number_format((($investment->amount * $sold_at)) * $add2 - (($investment->amount * $bought_at)) * $add1, $decimal3)}}" data-html="true">

                                              {!! $symbol !!}{{number_format(($investment->amount * $sold_at) * $prevsold - ($investment->amount * $bought_at) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}

                                          </span>
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($bought_at * $investment->amount) * $previousmultiplier))) * (((($sold_at * $investment->amount) * $prevsold)) - ((($bought_at * $investment->amount) * $previousmultiplier))), 2)}}%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol}} {{number_format((($investment->amount * $bought_at)), 5)}}" data-html="true">{!! $symbol !!}{{number_format((($investment->amount * $bought_at) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol2}} {{number_format(($investment->amount * $sold_at), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $sold_at) * $prevsold, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol}} {{number_format(($bought_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($bought_at * $previousmultiplier,$decimal2)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="{{$innersymbol2}} {{number_format(($sold_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($sold_at * $prevsold ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                  @endif
                                  @endforeach



                                  @foreach($investments as $investment)
                                    @if($investment->date_sold != null)
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 5;

                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $previous = $investment->btc_price_bought_usd;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $previous = 1;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = 1;
                                      } else {
                                        $previous = $investment->btc_price_bought_usd * $fiat;
                                        $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                        $prevsold = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                               <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                   <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                                 </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">
                                                   <li>
                                                     <a href="/investments/remove/{{$investment->id}}">Remove</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                             </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $investment->sold_at),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for * $prevsold),$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if($investment->sold_for_usd > $investment->bought_for_usd)
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->amount * $investment->sold_at)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                               {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/(($investment->amount * $investment->bought_at) * $previous)) * ((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at)) * $previous), 2)}}%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->amount * $investment->sold_at)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>
                                           </span>
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/(($investment->amount * $investment->bought_at) * $previous)) * ((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at)) * $previous), 2)}}%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $investment->bought_at), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $investment->bought_at) * $previous, $decimal1)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $investment->sold_at), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $investment->sold_at) * $prevsold, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->bought_at * $previous,$decimal2)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->sold_at * $prevsold ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                   @endif
                                  @endforeach

                                  @foreach($p_investments as $investment)
                                    @if($investment->date_sold != null && $investment->soldmarket == "BTC" && $investment->market == "BTC")
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 5;

                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $previous = $investment->btc_price_bought_usd;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $previous = 1;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = 1;
                                      } else {
                                        $previous = $investment->btc_price_bought_usd * $fiat;
                                        $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                        $prevsold = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/polo/{{$investment->id}}">Hide</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                               <img src="https://png.icons8.com/bitcoin/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was sold to BTC." width="24" height="24">
                                                 </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $investment->sold_at),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for * $prevsold),$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if(((($investment->amount * $investment->sold_at) * $prevsold)) > (($investment->amount * $investment->bought_at) * $previous))
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                               {{number_format((($investment->amount * $investment->sold_at)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                               {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->amount * $investment->bought_at) * $previous))) * (((($investment->amount * $investment->sold_at) * $prevsold)) - ((($investment->amount * $investment->bought_at) * $previous))), 2)}}%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                             {{number_format((($investment->amount * $investment->sold_at)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->amount * $investment->bought_at) * $previous))) * (((($investment->amount * $investment->sold_at) * $prevsold)) - ((($investment->amount * $investment->bought_at) * $previous))), 2)}}%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_for), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->bought_for) * $previous, $decimal1)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for) * $prevsold, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->bought_at * $previous,$decimal2)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->sold_at * $prevsold ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                   @endif

                                     @if($investment->date_sold != null && $investment->soldmarket == "BTC" && $investment->market == "USDT")
                                     <?php
                                       $multiplier = Auth::user()->getMultiplier();
                                       $previous = 0;
                                       $decimal1 = 2;
                                       $decimal2 = 5;
                                       $bought_for = ($investment->bought_at * $investment->amount) / $investment->btc_price_bought_usdt;
                                       $bought_at = $investment->bought_at / $investment->btc_price_bought_usdt;
                                       if(Auth::user()->getCurrency() == "USD")
                                       {
                                         $previous = $investment->btc_price_bought_usd;
                                         $previousmultiplier = $investment->btc_price_bought_usd;
                                         $prevsold = $investment->btc_price_sold_usd;
                                       } elseif(Auth::user()->getCurrency() == "BTC")
                                       {
                                         $previous = $investment->btc_price_bought_usd;
                                         $decimal1 = 5;
                                         $decimal2 = 9;
                                         $previousmultiplier = 1;
                                         $prevsold = 1;
                                       } else {
                                         $previous = $investment->btc_price_bought_usd;
                                         $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                         $prevsold = $investment->btc_price_sold_usd * $fiat;
                                       }
                                      ?>
                                      <figure class="col-xs-12 col-sm-4 col-md-4">
                                        <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                            <header class="card-heading">
                                              <ul class="card-actions icons left-top">
                                                <li>
                                                  <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex.">verified_user</i>
                                                  </li>
                                                </ul>
                                              <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                  <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                              </ul>
                                              <ul class="card-actions icons right-top">
                                                <li class="dropdown">
                                                  <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="zmdi zmdi-more-vert"></i>
                                                  </a>
                                                  <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                    <li>
                                                      <a href="/investments/remove/polo/{{$investment->id}}">Hide</a>
                                                    </li>
                                                  </ul>
                                                </li>
                                                <li>
                                                <img src="https://png.icons8.com/bitcoin/color/24" style="cursor:pointer;margin-top;-5px!important;" data-toggle="tooltip" title="This investment was bought with USDT and sold to BTC." width="24" height="24">
                                                  </li>
                                                </ul>
                                            </header>
                                          <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                              <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                          </div>
                                          <div class="card-body">
                                            <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for * $prevsold),$decimal1)}}{!! $symbol2 !!}</h4>
                                            <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                             @if((($investment->sold_for * $prevsold)) > ($bought_for * $previousmultiplier))
                                              <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->sold_for)) - (($bought_for)), 5)}}" data-html="true">
                                                {!! $symbol !!}{{number_format(($investment->sold_for * $prevsold) - ($bought_for * $previousmultiplier), 8)}}{!! $symbol2 !!}
                                              </span>

                                              <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                {{number_format((100/(($bought_for * $previousmultiplier))) * ((($investment->sold_for * $prevsold)) - (($bought_for * $previousmultiplier))), 2)}}%
                                              </span>

                                            @else
                                            <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->sold_for)) - (($investment->amount * ($investment->bought_at / $investment->btc_price_sold_usd))), 5)}}" data-html="true">
                                              {!! $symbol !!}{{number_format(($investment->sold_for * $prevsold) - ($bought_for * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}
                                            </span>
                                              <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                {{number_format((100/(($bought_for * $previousmultiplier))) * ((($investment->sold_for * $prevsold)) - (($bought_for * $previousmultiplier))), 2)}}%
                                              </span>
                                            @endif
                                              <hr style="margin-top:40px;">
                                              <div class="usd">
                                              <span style="float:left;">Before</span>
                                              <span style="float:right;">After</span>
                                              <br>
                                              <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($bought_for * $previous), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($bought_for * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}</span>
                                              <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for) * $prevsold, $decimal1)}}{!! $symbol2 !!}</span>
                                              <br>
                                              <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($bought_at * $previous), 8)}}" data-html="true">{!! $symbol !!}{{number_format($bought_at * $previousmultiplier,$decimal2)}}{!! $symbol2 !!}</span>
                                              <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->sold_at * $prevsold ,$decimal2)}}{!! $symbol2 !!}</span>
                                              <br>
                                              </div>
                                          </div>
                                        </div>
                                      </figure>
                                      @endif

                                    @if($investment->date_sold != null && $investment->soldmarket == "USDT" && $investment->market == "USDT")
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $investment->sold_for = $investment->amount * $investment->sold_at;
                                      $investment->bought_for = $investment->amount * $investment->bought_at;
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 2;
                                      $bought_for = ($investment->bought_at * $investment->amount) / $investment->btc_price_bought_usdt;
                                      $bought_at = $investment->bought_at / $investment->btc_price_bought_usdt;
                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $previous = $investment->btc_price_bought_usdt;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevsold = $investment->btc_price_sold_usdt;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $previous = $investment->btc_price_bought_usdt;
                                        $prevsoldmultiplier = 1;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = $investment->btc_price_sold_usdt;
                                      } else {
                                        $previous = $investment->btc_price_bought_usdt;
                                        $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                        $prevsold = $investment->btc_price_sold_usdt;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/polo/{{$investment->id}}">Hide</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                               <img src="https://png.icons8.com/us-dollar/color/24" style="cursor:pointer;margin-top;-15px!important;" data-toggle="tooltip" title="This investment was bought with USDT and sold to USDT." width="24" height="24">
                                                 </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_for),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for / ($prevsold)) * $prevsoldmultiplier,$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if((($investment->sold_for / $prevsold) * $prevsoldmultiplier) > (($investment->bought_for / $previous) * $previousmultiplier))
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_for)) - (($investment->bought_for)), $decimal1)}}" data-html="true">
                                               {!! $symbol !!}{{number_format((($investment->sold_for / $prevsold) * $prevsoldmultiplier) - (($investment->bought_for / $previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}
                                             </span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->bought_for / $previous) * $previousmultiplier))) * (((($investment->sold_for / $prevsold) * $prevsoldmultiplier)) - ((($investment->bought_for / $previous) * $previousmultiplier))), 2)}}%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_for)) - (($investment->bought_for)), $decimal1)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->sold_for / $prevsold) * $prevsoldmultiplier) - (($investment->bought_for / $previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}
                                           </span>
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->bought_for / $previous) * $previousmultiplier))) * (((($investment->sold_for / $prevsold) * $prevsoldmultiplier)) - ((($investment->bought_for / $previous) * $previousmultiplier))), 2)}}%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_for), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->bought_for / $previous) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_for), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for / $prevsold) * $prevsoldmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_at), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->bought_at / $previous) * $previousmultiplier,$decimal2)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_at), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_at / $prevsold) * $prevsoldmultiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                    @endif

                                    @if($investment->date_sold != null && $investment->soldmarket == "USDT" && $investment->market == "Deposit")
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 2;
                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $prevsoldmultiplier = 1;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } else {
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/polo/{{$investment->id}}">Hide</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                               <img src="https://png.icons8.com/us-dollar/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was deposited and sold to USDT." width="24" height="24">
                                                 </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_for),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for / ($prevsold)) * $prevsoldmultiplier,$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if((($investment->sold_for_usd)) > ($investment->bought_for_usd))
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_for)), $decimal1)}}" data-html="true">
                                               {!! $symbol !!}{{number_format((($investment->sold_for / $prevsold) * $prevsoldmultiplier), $decimal1)}}{!! $symbol2 !!}
                                             </span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               100%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_for)), $decimal1)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->sold_for / $prevsold) * $prevsoldmultiplier), $decimal1)}}{!! $symbol2 !!}
                                           </span>
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               100%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_for), 2)}}" data-html="true">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_for), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for / $prevsold) * $prevsoldmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_at), 2)}}" data-html="true">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_at), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_at / $prevsold) * $prevsoldmultiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                    @endif

                                    @if($investment->date_sold != null && $investment->soldmarket == "BTC" && $investment->market == "Deposit")
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 2;
                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $prevsoldmultiplier = 1;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } else {
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/polo/{{$investment->id}}">Hide</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                               <img src="https://png.icons8.com/bitcoin/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was deposited and sold to BTC." width="24" height="24">
                                                 </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for_usd / ($prevsold)) * $prevsoldmultiplier,$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if((($investment->sold_for_usd)) > ($investment->bought_for_usd))
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->sold_for)), $decimal1)}}" data-html="true">
                                               {!! $symbol !!}{{number_format((($investment->sold_for_usd / $prevsold) * $prevsoldmultiplier), $decimal1)}}{!! $symbol2 !!}
                                             </span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               100%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->sold_for)), $decimal1)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->sold_for_usd / $prevsold) * $prevsoldmultiplier), $decimal1)}}{!! $symbol2 !!}
                                           </span>
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               100%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_for), 2)}}" data-html="true">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for_usd / $prevsold) * $prevsoldmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_at), 2)}}" data-html="true">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_at), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for_usd / $prevsold) * $prevsoldmultiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                    @endif

                                    @if($investment->date_sold != null && $investment->soldmarket == "BTC" && $investment->market == "ETH")
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 5;

                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $previous = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $previous = $investment->btc_price_bought_eth;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = 1;
                                        $prevmultiplier = 1 / $investment->btc_price_bought_eth;
                                      } else {
                                        $previous = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                        $prevmultiplier = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $fiat;
                                        $prevsold = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Bittrex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/polo/{{$investment->id}}">Hide</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                               <img src="https://png.icons8.com/bitcoin/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was bought with ETH and sold to BTC." width="24" height="24">
                                                 </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $investment->sold_at),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for * $prevsold),$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if(((($investment->amount * $investment->sold_at) * $prevsold)) > (($investment->amount * $investment->bought_at) * $prevmultiplier))
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                             {{number_format((($investment->amount * $investment->sold_at)) - (($investment->amount * $investment->bought_at) / $investment->btc_price_bought_eth), 5)}}" data-html="true">
                                            {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $prevmultiplier), $decimal1)}}{!! $symbol2 !!}</span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->amount * $investment->bought_at) * $prevmultiplier))) * (((($investment->amount * $investment->sold_at) * $prevsold)) - ((($investment->amount * $investment->bought_at) * $prevmultiplier))), 2)}}%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                             {{number_format((($investment->amount * $investment->sold_at)) - (($investment->amount * $investment->bought_at) / $investment->btc_price_bought_eth), 5)}}" data-html="true">

                                             {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $prevmultiplier), $decimal1)}}{!! $symbol2 !!}</span>

                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->amount * $investment->bought_at) * $prevmultiplier))) * (((($investment->amount * $investment->sold_at) * $prevsold)) - ((($investment->amount * $investment->bought_at) * $prevmultiplier))), 2)}}%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->bought_for), 5)}}" data-html="true">{!! $symbol !!}{{number_format((($investment->amount * $investment->bought_at)) * $prevmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for) * $prevsold, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->bought_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->bought_at) * $previous,$decimal2)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->sold_at * $prevsold ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                    @endif


                                  @endforeach



                                  @foreach($b_investments as $investment)
                                    @if($investment->date_sold != null && $investment->soldmarket == "BTC" && $investment->market == "BTC")
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 5;

                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $previous = $investment->btc_price_bought_usd;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $previous = 1;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = 1;
                                      } else {
                                        $previous = $investment->btc_price_bought_usd * $fiat;
                                        $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                        $prevsold = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Bittrex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/bittrex/{{$investment->id}}">Hide</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                               <img src="https://png.icons8.com/bitcoin/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was sold to BTC." width="24" height="24">
                                                 </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $investment->sold_at),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for * $prevsold),$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if(((($investment->amount * $investment->sold_at) * $prevsold)) > (($investment->amount * $investment->bought_at) * $previous))
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                               {{number_format((($investment->amount * $investment->sold_at)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                               {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->amount * $investment->bought_at) * $previous))) * (((($investment->amount * $investment->sold_at) * $prevsold)) - ((($investment->amount * $investment->bought_at) * $previous))), 2)}}%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                             {{number_format((($investment->amount * $investment->sold_at)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->amount * $investment->bought_at) * $previous))) * (((($investment->amount * $investment->sold_at) * $prevsold)) - ((($investment->amount * $investment->bought_at) * $previous))), 2)}}%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_for), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $investment->bought_at) * $previous, $decimal1)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $investment->sold_at) * $prevsold, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->bought_at * $previous,$decimal2)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->sold_at * $prevsold ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                   @endif

                                     @if($investment->date_sold != null && $investment->soldmarket == "BTC" && $investment->market == "USDT")
                                     <?php
                                       $multiplier = Auth::user()->getMultiplier();
                                       $previous = 0;
                                       $decimal1 = 2;
                                       $decimal2 = 5;
                                       $bought_for = ($investment->bought_at * $investment->amount) / $investment->btc_price_bought_usdt;
                                       $bought_at = $investment->bought_at / $investment->btc_price_bought_usdt;
                                       if(Auth::user()->getCurrency() == "USD")
                                       {
                                         $previous = $investment->btc_price_bought_usd;
                                         $previousmultiplier = $investment->btc_price_bought_usd;
                                         $prevsold = $investment->btc_price_sold_usd;
                                       }elseif(Auth::user()->getCurrency() == "BTC")
                                       {
                                         $previous = $investment->btc_price_bought_usd;
                                         $decimal1 = 5;
                                         $decimal2 = 9;
                                         $previousmultiplier = 1;
                                         $prevsold = 1;
                                       } else {
                                         $previous = $investment->btc_price_bought_usd;
                                         $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                         $prevsold = $investment->btc_price_sold_usd * $fiat;
                                       }
                                      ?>
                                      <figure class="col-xs-12 col-sm-4 col-md-4">
                                        <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                            <header class="card-heading">
                                              <ul class="card-actions icons left-top">
                                                <li>
                                                  <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Bittrex.">verified_user</i>
                                                  </li>
                                                </ul>
                                              <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                  <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                              </ul>
                                              <ul class="card-actions icons right-top">
                                                <li class="dropdown">
                                                  <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="zmdi zmdi-more-vert"></i>
                                                  </a>
                                                  <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                    <li>
                                                      <a href="/investments/remove/bittrex/{{$investment->id}}">Hide</a>
                                                    </li>
                                                  </ul>
                                                </li>
                                                <li>
                                                <img src="https://png.icons8.com/bitcoin/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was bought with USDT and sold to BTC." width="24" height="24">
                                                  </li>
                                                </ul>
                                            </header>
                                          <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                              <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                          </div>
                                          <div class="card-body">
                                            <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for * $prevsold),$decimal1)}}{!! $symbol2 !!}</h4>
                                            <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                             @if((($investment->sold_for * $prevsold)) > ($bought_for * $previousmultiplier))
                                              <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->sold_for)) - (($bought_for)), 5)}}" data-html="true">
                                                {!! $symbol !!}{{number_format(($investment->sold_for * $prevsold) - ($bought_for * $previousmultiplier), 8)}}{!! $symbol2 !!}
                                              </span>

                                              <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                {{number_format((100/(($investment->sold_for * $prevsold))) * ((($investment->sold_for * $prevsold)) - (($bought_for * $previousmultiplier))), 2)}}%
                                              </span>

                                            @else
                                            <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->sold_for)) - (($investment->amount * ($investment->bought_at / $investment->btc_price_sold_usd))), 5)}}" data-html="true">
                                              {!! $symbol !!}{{number_format(($investment->sold_for * $prevsold) - ($bought_for * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}
                                            </span>
                                              <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                {{number_format((100/(($investment->sold_for * $prevsold))) * ((($investment->sold_for * $prevsold)) - (($bought_for * $previousmultiplier))), 2)}}%
                                              </span>
                                            @endif
                                              <hr style="margin-top:40px;">
                                              <div class="usd">
                                              <span style="float:left;">Before</span>
                                              <span style="float:right;">After</span>
                                              <br>
                                              <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($bought_for * $previous), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($bought_for * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}</span>
                                              <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for) * $prevsold, $decimal1)}}{!! $symbol2 !!}</span>
                                              <br>
                                              <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($bought_at * $previous), 8)}}" data-html="true">{!! $symbol !!}{{number_format($bought_at * $previousmultiplier,$decimal2)}}{!! $symbol2 !!}</span>
                                              <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->sold_at * $prevsold ,$decimal2)}}{!! $symbol2 !!}</span>
                                              <br>
                                              </div>
                                          </div>
                                        </div>
                                      </figure>
                                      @endif

                                    @if($investment->date_sold != null && $investment->soldmarket == "BTC" && $investment->market == "ETH")
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 5;

                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $previous = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $previous = $investment->btc_price_bought_eth;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = 1;
                                        $prevmultiplier = 1 / $investment->btc_price_bought_eth;
                                      } else {
                                        $previous = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                        $prevmultiplier = ($investment->btc_price_bought_eur / $investment->btc_price_bought_eth) * $fiat;
                                        $prevsold = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Bittrex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/bittrex/{{$investment->id}}">Hide</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                               <img src="https://png.icons8.com/bitcoin/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was bought with ETH and sold to BTC." width="24" height="24">
                                                 </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $investment->sold_at),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for * $prevsold),$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if(((($investment->amount * $investment->sold_at) * $prevsold)) > (($investment->amount * $investment->bought_at) * $prevmultiplier))
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                             {{number_format((($investment->amount * $investment->sold_at)) - (($investment->amount * $investment->bought_at) / $investment->btc_price_bought_eth), 5)}}" data-html="true">
                                            {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $prevmultiplier), $decimal1)}}{!! $symbol2 !!}</span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->amount * $investment->bought_at) * $prevmultiplier))) * (((($investment->amount * $investment->sold_at) * $prevsold)) - ((($investment->amount * $investment->bought_at) * $prevmultiplier))), 2)}}%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                             {{number_format((($investment->amount * $investment->sold_at)) - (($investment->amount * $investment->bought_at) / $investment->btc_price_bought_eth), 5)}}" data-html="true">

                                             {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $prevmultiplier), $decimal1)}}{!! $symbol2 !!}</span>

                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->amount * $investment->bought_at) * $prevmultiplier))) * (((($investment->amount * $investment->sold_at) * $prevsold)) - ((($investment->amount * $investment->bought_at) * $prevmultiplier))), 2)}}%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->bought_for), 5)}}" data-html="true">{!! $symbol !!}{{number_format((($investment->amount * $investment->bought_at)) * $prevmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for) * $prevsold, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->bought_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->bought_at) * $previous,$decimal2)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->sold_at * $prevsold ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                    @endif

                                    @if($investment->date_sold != null && $investment->soldmarket == "USDT" && $investment->market == "USDT")
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 2;
                                      $bought_for = ($investment->bought_at * $investment->amount) / $investment->btc_price_bought_usdt;
                                      $bought_at = $investment->bought_at / $investment->btc_price_bought_usdt;
                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $previous = $investment->btc_price_bought_usd;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $previous = $investment->btc_price_bought_usd;
                                        $prevsoldmultiplier = 1;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } else {
                                        $previous = $investment->btc_price_bought_usd;
                                        $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Bittrex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/bittrex/{{$investment->id}}">Hide</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                               <img src="https://png.icons8.com/us-dollar/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was bought with USDT and sold to USDT." width="24" height="24">
                                                 </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_for),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for / ($prevsold)) * $prevsoldmultiplier,$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if(((($investment->sold_for / $prevsold) * $prevsoldmultiplier)) > (($investment->bought_for / $previous) * $previousmultiplier))
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_for)) - (($investment->bought_for)), $decimal1)}}" data-html="true">
                                               {!! $symbol !!}{{number_format((($investment->sold_for / $prevsold) * $prevsoldmultiplier) - (($investment->bought_for / $previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}
                                             </span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->bought_for / $previous) * $previousmultiplier))) * (((($investment->sold_for / $prevsold) * $prevsoldmultiplier)) - ((($investment->bought_for / $previous) * $previousmultiplier))), 2)}}%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_for)) - (($investment->bought_for)), $decimal1)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->sold_for / $prevsold) * $prevsoldmultiplier) - (($investment->bought_for / $previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}
                                           </span>
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->bought_for / $previous) * $previousmultiplier))) * (((($investment->sold_for / $prevsold) * $prevsoldmultiplier)) - ((($investment->bought_for / $previous) * $previousmultiplier))), 2)}}%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_for), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->bought_for / $previous) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_for), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for / $prevsold) * $prevsoldmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_at), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->bought_at / $previous) * $previousmultiplier,$decimal2)}}{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_at), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_at / $prevsold) * $prevsoldmultiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                    @endif

                                    @if($investment->date_sold != null && $investment->soldmarket == "USDT" && $investment->market == "Deposit")
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 2;
                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $prevsoldmultiplier = 1;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } else {
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Bittrex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/bittrex/{{$investment->id}}">Hide</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                               <img src="https://png.icons8.com/us-dollar/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was deposited and sold to USDT." width="24" height="24">
                                                 </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_for),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for / ($prevsold)) * $prevsoldmultiplier,$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if((($investment->sold_for_usd)) > ($investment->bought_for_usd))
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_for)), $decimal1)}}" data-html="true">
                                               {!! $symbol !!}{{number_format((($investment->sold_for / $prevsold) * $prevsoldmultiplier), $decimal1)}}{!! $symbol2 !!}
                                             </span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               100%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_for)), $decimal1)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->sold_for / $prevsold) * $prevsoldmultiplier), $decimal1)}}{!! $symbol2 !!}
                                           </span>
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               100%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_for), 2)}}" data-html="true">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_for), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for / $prevsold) * $prevsoldmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_at), 2)}}" data-html="true">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_at), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_at / $prevsold) * $prevsoldmultiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                    @endif

                                    @if($investment->date_sold != null && $investment->soldmarket == "BTC" && $investment->market == "Deposit")
                                    <?php
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 2;
                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $prevsoldmultiplier = 1;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } else {
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                      }
                                     ?>
                                     <figure class="col-xs-12 col-sm-4 col-md-4">
                                       <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                           <header class="card-heading">
                                             <ul class="card-actions icons left-top">
                                               <li>
                                                 <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex.">verified_user</i>
                                                 </li>
                                               </ul>
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                             </ul>
                                             <ul class="card-actions icons right-top">
                                               <li class="dropdown">
                                                 <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="zmdi zmdi-more-vert"></i>
                                                 </a>
                                                 <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                   <li>
                                                     <a href="/investments/remove/bittrex/{{$investment->id}}">Hide</a>
                                                   </li>
                                                 </ul>
                                               </li>
                                               <li>
                                               <img src="https://png.icons8.com/bitcoin/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was deposited and sold to BTC." width="24" height="24">
                                                 </li>
                                               </ul>
                                           </header>
                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                         </div>
                                         <div class="card-body">
                                           <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for_usd / ($prevsold)) * $prevsoldmultiplier,$decimal1)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                            @if((($investment->sold_for_usd)) > ($investment->bought_for_usd))
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->sold_for)), $decimal1)}}" data-html="true">
                                               {!! $symbol !!}{{number_format((($investment->sold_for_usd / $prevsold) * $prevsoldmultiplier), $decimal1)}}{!! $symbol2 !!}
                                             </span>

                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               100%
                                             </span>

                                           @else
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->sold_for)), $decimal1)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->sold_for_usd / $prevsold) * $prevsoldmultiplier), $decimal1)}}{!! $symbol2 !!}
                                           </span>
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               100%
                                             </span>
                                           @endif
                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_for), 2)}}" data-html="true">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_for), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for_usd / $prevsold) * $prevsoldmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_at), 2)}}" data-html="true">{!! $symbol !!}0<{!! $symbol2 !!}/span>
                                             <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->sold_at), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_for_usd / $prevsold) * $prevsoldmultiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             </div>
                                         </div>
                                       </div>
                                     </figure>
                                    @endif


                                  @endforeach
                                </div>
                              </div>

                                                            </div>
                                                        </div>
                                                    </header>
                                                </div>
                    </div>


                </div>

            </div>
        </div>
</div>
        			<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="edit_modal">
				          <div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">

							<h4 class="modal-title" id="myModalLabel-2">Edit investment</h4>
							<ul class="card-actions icons right-top">
							<li>
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
						<form id="edit-form" role="form" method="post" action="/coins/edit/">
                            {{ csrf_field() }}

                    <div class="form-group">
                        <label for="" class="control-label">Price Input</label>
                        <select class="select form-control" id="priceinput" name="priceinput">
                          <option value="">Select an Option</option>
                          <option value="btcper">BTC paid per coin</option>
                          <option value="usdper">USD paid per coin</option>
                          <option value="eurper">EUR paid per coin</option>
                          <option value="total">Total BTC paid</option>
                        </select>
                  </div>


                    <div class="form-group" style="display:none" id="btc_per">
                      <label class="control-label">BTC paid per coin</label>
                      <input type="text" class="form-control" name="bought_at_btc" id="bought_at_btc">
                    </div>
                    <div class="form-group" style="display:none" id="usd_per">
                      <label class="control-label">USD paid per coin</label>
                      <input type="text" class="form-control" name="bought_at_usd" id="bought_at_usd">
                    </div>
                    <div class="form-group" style="display:none" id="eur_per">
                      <label class="control-label">EUR paid per coin</label>
                      <input type="text" class="form-control" name="bought_at_eur" id="bought_at_eur">
                    </div>
                    <div class="form-group" style="display:none" id="total">
                      <label class="control-label">Total paid for investment</label>
                      <input type="text" class="form-control" name="total" id="total">
                    </div>

                  <div class="form-group">
                    <label class="control-label">Number of coins bought</label>
                    <input type="text" class="form-control" name="amount" id="amount" required>
                  </div>
                    <div class="form-group is-empty">
                        <label class="control-label">Date when bought</label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                        <input type="text" class="form-control datepicker" id="md_input_date" type="date" placeholder="Date when bought" name="date" required>
                      </div>
                    </div>
                            <button type="submit" class="btn btn-primary">Save investment</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			       </div>

             <div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="basic_modal">
                   <div class="modal-dialog" role="document">
         <div class="modal-content">
           <div class="modal-header">

             <h4 class="modal-title" id="myModalLabel-2">Add a new coin/investment</h4>
             <ul class="card-actions icons right-top">
             <li>
               <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                 <i class="zmdi zmdi-close"></i>
               </a>
             </li>
           </ul>
         </div>
         <div class="modal-body">
                 <form id="form-horizontal" role="form" method="post" action="/investments/add">
                           {{ csrf_field() }}
                         <div class="form-group is-empty">
                         <label for="" class="control-label">Coin/Currency</label>
                         <input type="text" class="form-control typeahead" id="autocomplete_states" autocomplete="off" placeholder="Enter a coin" id="coin" name="coin" required/>
                 </div>

                 <div class="form-group">
                     <label for="" class="control-label">Price Input</label>
                     <select class="select form-control" id="priceinputedit" name="priceinput">
                       <option value="">Select an Option</option>
                       <option value="btcper">BTC paid per coin</option>
                       <option value="usdper">USD paid per coin</option>
                       <option value="eurper">EUR paid per coin</option>
                       <option value="total">Total BTC paid</option>
                     </select>
               </div>


                 <div class="form-group" style="display:none" id="btc_peredit">
                   <label class="control-label">BTC paid per coin</label>
                   <input type="text" class="form-control" name="bought_at_btc" id="bought_at_btc">
                 </div>
                 <div class="form-group" style="display:none" id="usd_peredit">
                   <label class="control-label">USD paid per coin</label>
                   <input type="text" class="form-control" name="bought_at_usd" id="bought_at_usd">
                 </div>
                 <div class="form-group" style="display:none" id="eur_peredit">
                   <label class="control-label">EUR paid per coin</label>
                   <input type="text" class="form-control" name="bought_at_eur" id="bought_at_eur">
                 </div>
                 <div class="form-group" style="display:none" id="totaledit">
                   <label class="control-label">Total paid for investment</label>
                   <input type="text" class="form-control" name="total" id="total">
                 </div>

                 <div class="form-group">
                   <label class="control-label">Number of coins bought</label>
                   <input type="text" class="form-control" name="amount" id="amount" required>
                 </div>
                   <div class="form-group is-empty">
                       <label class="control-label">Date when bought</label>
                     <div class="input-group">
                       <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                       <input type="text" class="form-control datepicker" id="md_input_date2" type="date" placeholder="Date when bought" name="date" required>
                     </div>
                   </div>
                           <button type="submit" class="btn btn-primary">Add coin</button>
                       </form>
           </div>
         </div>
         <!-- modal-content -->
       </div>
       <!-- modal-dialog -->
            </div>




  			<div class="modal fade" id="sold_modal" tabindex="-1" role="dialog" aria-labelledby="sold_modal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">

							<h4 class="modal-title" id="myModalLabel-2">Mark Investment As Sold</h4>
							<ul class="card-actions icons right-top">
							<li>
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
						<form id="sell-form" role="form" method="post" action="/investments/sell/">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="" class="control-label">Price Input</label>
                                <select class="select form-control" id="priceinputsell" name="priceinput">
                                  <option value="">Select an Option</option>
                                  <option value="btcper">BTC paid per coin</option>
                                  <option value="usdper">USD paid per coin</option>
                                  <option value="eurper">EUR paid per coin</option>
                                  <option value="total">Total BTC paid</option>
                                </select>
                          </div>


                            <div class="form-group" style="display:none" id="btc_persell">
                              <label class="control-label">BTC paid per coin</label>
                              <input type="text" class="form-control" name="sold_at_btc" id="sold_at_btc">
                            </div>
                            <div class="form-group" style="display:none" id="usd_persell">
                              <label class="control-label">USD paid per coin</label>
                              <input type="text" class="form-control" name="sold_at_usd" id="sold_at_usd">
                            </div>
                            <div class="form-group" style="display:none" id="eur_persell">
                              <label class="control-label">EUR paid per coin</label>
                              <input type="text" class="form-control" name="sold_at_eur" id="sold_at_eur">
                            </div>
                            <div class="form-group" style="display:none" id="totalsell">
                              <label class="control-label">Total paid for investment</label>
                              <input type="text" class="form-control" name="total" id="total">
                            </div>
                            <div class="form-group is-empty">
                                <label class="control-label">Date when sold</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                <input type="text" class="form-control datepicker" id="md_input_date3" type="date" placeholder="Date when sold" name="date" required>
                              </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Mark as sold</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>

      <div class="modal fade" id="reset_modal" tabindex="-1" role="dialog" aria-labelledby="reset_modal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">

							<h4 class="modal-title" id="myModalLabel-2">Reset Data</h4>
							<ul class="card-actions icons right-top">
							<li>
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
                        <p>From here you can decide which data you would like to reset.</p>
                        <br>
                        <div class="row">
                          <div class="col-md-6">
                            <a href="javascript:void(0)" id="reset-data-polo"><img src="https://poloniex.com/images/theme_light/poloniex.png"/></a>
                          </div>
                          <div class="col-md-6" style="margin-top:-15px;">
                            <a href="javascript:void(0)" id="reset-data-bittrex"><img src="https://bittrex.com/Content/img/logos/bittrex-logo-transparent.png"/></a>
                          </div>
                        </div>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>

			<div class="modal fade" id="bittrex_modal" tabindex="-1" role="dialog" aria-labelledby="bittrex_modal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">

							<h4 class="modal-title" id="myModalLabel-2">Bittrex Import</h4>
							<ul class="card-actions icons right-top">
							<li>
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
                        <p>Since 2017-05-25 the bittrex importing has changed, this is due to that we can only recieve orders from the past 30 days using our previous method, so from now until they fix their issues, if you want to retrieve your whole bittrex order history you need to upload your full order history in CSV format, you may do this from your Orders on the Bittrex website.</p>
						<form id="bittrex-form" role="form" method="post" action="/api/bittrex/newimport" enctype="multipart/form-data">
                            {{ csrf_field() }}
                      <div class="form-group is-empty">
                        <div class="input-group">
                          <input type="file" class="form-control" placeholder="File Upload..." name="csv" id="csv">
                          <div class="input-group">
                            <input type="text" readonly="" class="form-control" placeholder="Placeholder w/file chooser...">
                            <span class="input-group-btn input-group-sm">
                              <button type="button" class="btn btn-info btn-fab btn-fab-sm">
                                <i class="zmdi zmdi-attachment-alt"></i>
                              </button>
                            </span>
                          </div>
                        </div>
                      </div>
                            <button type="submit" class="btn btn-primary">Import Orders</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>

			<div class="modal fade" id="mining_modal" tabindex="-1" role="dialog" aria-labelledby="mining_modal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">

							<h4 class="modal-title" id="myModalLabel-2">Add a new mining deposit</h4>
							<ul class="card-actions icons right-top">
							<li>
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                This is for when you have mined a coin, making in an asset rather than an Investment.
                              </div>
						<form id="form-horizontal" role="form" method="post" action="/investments/add/mining">
                            {{ csrf_field() }}
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Coin/Currency</label>
                                    <input type="text" class="form-control typeahead" id="autocomplete_states3" autocomplete="off" placeholder="Enter a coin" id="coin" name="coin" required/>
                              </div>
                      <div class="form-group">
                        <label class="control-label">Number of coins mined</label>
                        <input type="text" class="form-control" name="amount" id="amount" required>
                      </div>
                        <div class="form-group is-empty">
                            <label class="control-label">Date Mined</label>
                          <div class="input-group">
                            <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                            <input type="text" class="form-control datepicker" id="md_input_date5" type="date" placeholder="Date when mined" name="date" required>
                          </div>
                        </div>
                            <button type="submit" class="btn btn-primary">Add coin</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>



      <div class="modal fade" id="sell_modal" tabindex="-1" role="dialog" aria-labelledby="sell_modal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">

							<h4 class="modal-title" id="myModalLabel-2">Sell amount</h4>
							<ul class="card-actions icons right-top">
							<li>
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
                        <p>This is for when you have multiple investments of a currency and want to enter an amount sold instead of marking as sold, after you entered the coin, amount, price and date we will calcualte how much you profited and remove from your active investments.</p>
						<form id="sell-form" role="form" method="post" action="/investments/sellMultiple">
                            {{ csrf_field() }}
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Coin/Currency</label>
                                    <input type="text" class="form-control typeahead" id="autocomplete_states2" autocomplete="off" placeholder="Enter a coin" id="coin" name="coin" required/>
                              </div>
                              <div class="form-group">
                                  <label for="" class="control-label">Price Input</label>
                                  <select class="select form-control" id="priceinputmulti" name="priceinput">
                                    <option value="">Select an Option</option>
                                    <option value="btcper">BTC paid per coin</option>
                                    <option value="usdper">USD paid per coin</option>
                                    <option value="eurper">EUR paid per coin</option>
                                    <option value="total">Total BTC paid</option>
                                  </select>
                            </div>


                              <div class="form-group" style="display:none" id="btc_permulti">
                                <label class="control-label">BTC paid per coin</label>
                                <input type="text" class="form-control" name="bought_at_btc" id="bought_at_btc">
                              </div>
                              <div class="form-group" style="display:none" id="usd_permulti">
                                <label class="control-label">USD paid per coin</label>
                                <input type="text" class="form-control" name="bought_at_usd" id="bought_at_usd">
                              </div>
                              <div class="form-group" style="display:none" id="eur_permulti">
                                <label class="control-label">EUR paid per coin</label>
                                <input type="text" class="form-control" name="bought_at_eur" id="bought_at_eur">
                              </div>
                              <div class="form-group" style="display:none" id="totalmulti">
                                <label class="control-label">Total paid for investment</label>
                                <input type="text" class="form-control" name="total" id="total">
                              </div>
                          <div class="form-group">
                            <label class="control-label">Number of coins sold</label>
                            <input type="text" class="form-control" name="amount" id="amount" required>
                          </div>
                            <div class="form-group is-empty">
                                <label class="control-label">Date when sold</label>
                              <div class="input-group">
                                <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                <input type="text" class="form-control datepicker" id="md_input_date4" type="date" placeholder="Date when sold" name="date" required>
                              </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Mark as sold</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>


			<div class="modal fade" id="import_modal" tabindex="-1" role="dialog" aria-labelledby="import_modal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
					<div class="modal-body" style="text-align:center;">
                    <div class="preloader pl-lg pls-blue" style="display:block;margin:0 auto;">
                      <svg class="pl-circular" viewBox="25 25 50 50">
                        <circle class="plc-path" cx="50" cy="50" r="20"/>
                      </svg>
                    </div>
                              <div class="alert alert-danger polo-error" style="display:none;" role="alert">
                                <strong>Oh no!</strong> It seems like there was an issue with the import.
                              </div>
                        <div class="polo-text">
                        <p>Importing has been improved!</p>
                        <p>We just added your import to the queue and we will notify you on-site when it is done!</p>

                        <span id="load1">Adding import..  <i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><br></span>
                        </div>
						</div>
						<div class="modal-footer" style="display:none;">
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-primary">Ok</button>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>


			<div class="modal fade" id="import_modal_bittrex" tabindex="-1" role="dialog" aria-labelledby="import_modal_bittrex">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
					<div class="modal-body" style="text-align:center;">
                    <div class="preloader pl-lg pls-blue" style="display:block;margin:0 auto;">
                      <svg class="pl-circular" viewBox="25 25 50 50">
                        <circle class="plc-path" cx="50" cy="50" r="20"/>
                      </svg>
                    </div>
                              <div class="alert alert-danger bittrex-error" style="display:none;" role="alert">
                                <strong>Oh no!</strong> It seems like you have entered an invalid API key!
                              </div>
                        <div class="bittrex-text">
                        <p>Please wait while we import your trades.</p>
                        <p>Are you stuck here? Be sure that you are not using the same key elsewhere!</p>

                        <span id="load_trades2">Importing trades  <i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><br></span>
                        <span id="load_orders2" style="display:none;">Inserting buy orders... <i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><br></span>
                        <span id="load_sales2" style="display:none;">Inserting sale orders... <i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><br></span>
                        </div>
						</div>
						<div class="modal-footer" style="display:none;">
							<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
							<button type="button" class="btn btn-primary">Ok</button>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>





@endsection



@section('js')
  <script src="/version2/js/swiper.jquery.min.js"></script>
    <script src="/js/farbtastic.js"></script>

  <script>

  var mySwiper = new Swiper ('.swiper-container', {
    // Optional parameters
    direction: 'horizontal',
    loop: true,

    // If we need pagination
    pagination: '.swiper-pagination',

    // Navigation arrows
    nextButton: '.swiper-button-next',
    prevButton: '.swiper-button-prev'
  })
$(".edit-coin").click(function(){
    $.ajax({
        dataType: "json",
        url: '/investments/get/'+$(this).attr('id'),
        success: function(data){
        $("#amount").val(data["amount"]);
        $("#md_input_date").val(data["date_bought"]);
        $("#edit-form").attr('action', '/investments/edit/'+data["id"]);
    }
    });

});

    $(".sell-coin").click(function(){
        $("#sell-form").attr('action', '/investments/sell/'+$(this).attr('id'));
    });




    $("#import_polo").click(function(){

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        swal("Import added to queue", "You no longer have to wait for the import to complete, we have added the import to our queue and we will notify you on-site when it is completed!")
        var jqxhr = $.post( "/importpolo", {_token: CSRF_TOKEN }, function() {
        }).fail(function(){;
        });

    });


    $("#import_bittrex").click(function(){

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        swal("Import added to queue", "You no longer have to wait for the import to complete, we have added the import to our queue and we will notify you on-site when it is completed!")
        var jqxhr = $.post( "/importbittrex", {_token: CSRF_TOKEN }, function() {
        }).fail(function(){;
        });

    });



    $("#reset-data-polo").click(function(){
        swal({
          title: "Are you sure you want to reset Poloniex data?",
          text: "You will not be able to undo a reset.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, reset it!",
          closeOnConfirm: false
        },
        function(){
            window.location.replace("/polo/reset/");
        });
            });


            $("#reset-data-bittrex").click(function(){
                swal({
                  title: "Are you sure you want to reset Bittrex data?",
                  text: "You will not be able to undo a reset.",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonColor: "#DD6B55",
                  confirmButtonText: "Yes, reset it!",
                  closeOnConfirm: false
                },
                function(){
                    window.location.replace("/bittrex/reset/");
                });
                    });



              $("#priceinput").change(function(){

                $("#total").css('display', 'none');
                $("#eur_per").css('display', 'none');
                $("#usd_per").css('display', 'none');
                $("#btc_per").css('display', 'none');

              if($(this).val() == "btcper")
              {
                $("#btc_per").css('display', '');
              }
              if($(this).val() == "usdper")
              {
                $("#usd_per").css('display', '');
              }
              if($(this).val() == "eurper")
              {
                $("#eur_per").css('display', '');
              }
              if($(this).val() == "total")
              {
                $("#total").css('display', '');
              }
            });

            $("#priceinputmulti").change(function(){

                $("#totalmulti").css('display', 'none');
                $("#eur_permulti").css('display', 'none');
                $("#usd_permulti").css('display', 'none');
                $("#btc_permulti").css('display', 'none');

              if($(this).val() == "btcper")
              {
                $("#btc_permulti").css('display', '');
              }
              if($(this).val() == "usdper")
              {
                $("#usd_permulti").css('display', '');
              }
              if($(this).val() == "eurper")
              {
                $("#eur_permulti").css('display', '');
              }
              if($(this).val() == "total")
              {
                $("#totalmulti").css('display', '');
              }
            });


            $("#priceinputedit").change(function(){

                $("#totaledit").css('display', 'none');
                $("#eur_peredit").css('display', 'none');
                $("#usd_peredit").css('display', 'none');
                $("#btc_peredit").css('display', 'none');

              if($(this).val() == "btcper")
              {
                $("#btc_peredit").css('display', '');
              }
              if($(this).val() == "usdper")
              {
                $("#usd_peredit").css('display', '');
              }
              if($(this).val() == "eurper")
              {
                $("#eur_peredit").css('display', '');
              }
              if($(this).val() == "total")
              {
                $("#totaledit").css('display', '');
              }
            });

            $("#priceinputsell").change(function(){

                $("#totalsell").css('display', 'none');
                $("#eur_persell").css('display', 'none');
                $("#usd_persell").css('display', 'none');
                $("#btc_persell").css('display', 'none');

              if($(this).val() == "btcper")
              {
                $("#btc_persell").css('display', '');
              }
              if($(this).val() == "usdper")
              {
                $("#usd_persell").css('display', '');
              }
              if($(this).val() == "eurper")
              {
                $("#eur_persell").css('display', '');
              }
              if($(this).val() == "total")
              {
                $("#totalsell").css('display', '');
              }
            });


            $(".more-comments").click(function(){

                if($(this).next('.extra:visible').length == 0)
                {
                $(this).next('.extra').css('display', 'block');
                $(this).text('View less');
              } else {
                $(this).next('.extra').css('display', 'none');
                $(this).text('View more');
              }
            });
</script>


@endsection
