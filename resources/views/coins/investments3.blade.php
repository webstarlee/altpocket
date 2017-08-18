@extends('layouts.app2')

@section('title')
My Investments
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="/version2/css/blocks.css?v=1.92">
  <link rel="stylesheet" type="text/css" href="/version2/css/swiper.min.css">

  <style>
svg:not(:root) {
    overflow: hidden;
}
svg:not(:root) {
    overflow: hidden;
}
.olymp-three-dots-icon {
    width: 16px;
    height: 4px;
}
.tooltip > .tooltip-inner {
	text-transform: none!important;
}
.card-actions.icons>a:before, .card-actions.icons>li>a:before {
	background-color: transparent!important;
}

  </style>
@endsection


@section('content')
<?php

$multiplier = Auth::user()->getMultiplier();
$ethmultiplier = Auth::user()->getEthMultiplier();
$api = Auth::user()->api;
$currency = Auth::user()->getCurrency();
if($currency != 'BTC' && $currency != 'USD')
{
  $fiat = DB::table('multipliers')->where('currency', $currency)->select('price')->first()->price;
  $symbol2 = Auth::user()->getSymbol();
  $symbol = "";
} else {
  $fiat = 1;
  $symbol = Auth::user()->getSymbol();
  $symbol2 = "";
}



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
                            <li  data-placement="left" data-toggle="tooltip" title="New Investment"> <a data-toggle="modal" data-target="#add_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm"><i class="zmdi zmdi-plus"></i></a></li>
                            <li  data-placement="left" data-toggle="tooltip" title="Add Mining/Free asset"> <a data-toggle="modal" data-target="#mining_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm">M</a></li>
                            <li  data-placement="left" data-toggle="tooltip" title="Sell Amount"> <a data-toggle="modal" data-target="#sell_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm"><i class="fa fa-money"></i></a></li>
                            <li> <a id="import_ui" href="javascript:void(0)"  data-placement="left" data-toggle="tooltip" title="Import interface" class="btn btn-fab btn-green btn-fab-sm"><i class="zmdi zmdi-cloud-download"></i></a></li>
                            @if(DB::table('keys')->where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Poloniex']])->exists())
                            <li> <a id="import_polo" href="javascript:void(0)"  data-placement="left" data-toggle="tooltip" title="Import orders from Poloniex" class="btn btn-fab btn-green btn-fab-sm">P</a></li>
                              @endif
                            @if(DB::table('keys')->where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Bittrex']])->exists())
                            <li  data-placement="left" data-toggle="tooltip" title="Import orders from Bittrex"> <a id="import_bittrex" href="javascript:void(0)" class="btn btn-info btn-fab btn-fab-sm">B</a></li>
                              @endif
                            <li  data-placement="left" data-toggle="tooltip" title="Reset Data"> <a data-toggle="modal" data-target="#reset_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm"><i class="zmdi zmdi-delete"></i></a></li>
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
									<?php
											$decimal = 2;

											if(Auth::user()->getCurrency() == "BTC")
											{
													$decimal = 5;
											}
									?>
									<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
										<div class="ui-block">
											<div class="ui-block-content">
												<ul class="statistics-list-count" style="list-style:none;">
													<li>
														<div class="points">
															<span>
																Total Investment
															</span>
														</div>
															@if(Auth::user()->algorithm == 2)
																<div class="count-stat">
																<span style="color:#73c04d;cursor:pointer;"  data-placement="top" data-toggle="tooltip" title="Invested alltime overall. (Deposits)">{!! $symbol !!}{{number_format(Auth::user()->getInvested(Auth::user()->getCurrency()), $decimal)}} {!! $symbol2 !!}</span>
															</div>
															@else
																<div class="count-stat">
																<span style="color:#73c04d;cursor:pointer;"  data-placement="top" data-toggle="tooltip" title="Paid amount for active investments.">{!! $symbol !!}{{number_format(Auth::user()->getPaid(Auth::user()->getCurrency()), $decimal)}} {!! $symbol2 !!}</span>
															</div>
															@endif
													</li>
												</ul>
											</div>
										</div>
									</div>

								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
									<div class="ui-block">
										<div class="ui-block-content">
											<ul class="statistics-list-count" style="list-style:none;">
												<li>
													<div class="points">
														<span>
															Net Worth
														</span>
													</div>
												<div class="count-stat">
                        	<span style="color:#73c04d;cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Holdings on exchanges and manual investments. (Balances)">{!! $symbol !!}{{number_format($networth * $multiplier + Auth::user()->getSoldProfit(Auth::user()->getCurrency()), $decimal)}} {!! $symbol2 !!}</span>
												</div>
												</li>
											</ul>
										</div>
									</div>
								</div>


								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
									<div class="ui-block">
										<div class="ui-block-content">
											<ul class="statistics-list-count" style="list-style:none;">
												<li>
													<div class="points">
														<span>
															Profit
														</span>
													</div>
												<div class="count-stat">
                        <span @if((($networth * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency()) + Auth::user()->getSoldProfit(Auth::user()->getCurrency())) > 0) style="color:#73c04d;cursor:pointer;" @else style="color:#de6b6b;cursor:pointer;"; @endif  data-placement="top" data-toggle="tooltip" title="Networth substracted with your deposits.">{!! $symbol !!}{{number_format((($networth * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency())) + Auth::user()->getSoldProfit(Auth::user()->getCurrency()), $decimal)}} {!! $symbol2 !!}</span>
												</div>
												</li>
											</ul>
										</div>
									</div>
								</div>

								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
									<div class="ui-block">
										<div class="ui-block-content">
											<ul class="statistics-list-count" style="list-style:none;">
												<li>
													<div class="points">
														<span>
															Active Profit
														</span>
													</div>
													<div class="count-stat">
													<span @if(($activeworth * Auth::user()->getMultiplier() - Auth::user()->getPaid(Auth::user()->currency)) > 0) style="color:#73c04d;cursor:pointer;" @else style="color:#de6b6b;cursor:pointer;"; @endif  data-placement="top" data-toggle="tooltip" title="Profit made on active investments.">{!! $symbol !!}{{number_format(($activeworth * Auth::user()->getMultiplier() - Auth::user()->getPaid(Auth::user()->currency)), $decimal)}} {!! $symbol2 !!}</span>
													</div>
												</li>
											</ul>
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
																<li role="presentation"><a href="#investments-sold" data-toggle="tab" aria-expanded="true" id="soldtab">Sold Investments</a></li>
																<li role="presentation"><a href="#balance" data-toggle="tab" aria-expanded="true">Balances</a></li>
															</ul>
														</div>
														<div class="card-body">
															<div class="tab-content">

				                        <div class="tab-pane fadeIn" id="summary">
                                </div>

                                <div class="tab-pane fadeIn" id="balance">
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
                                                 @if($balance->exchange != "Manual")
                                               <ul class="card-actions icons left-top">
                                                 <li>
                                                   <i class="material-icons" style="color:#5ecbf7;cursor:pointer;"  data-toggle="tooltip" title="Verified balance from {{$balance->exchange}}.">verified_user</i>
                                                   </li>
                                                 </ul>
                                                @endif
																								<ul class="card-actions icons right-top">
                                                  <li class="dropdown">
                                                    <a href="#" data-toggle="dropdown" aria-expanded="false">
                                                      <i class="zmdi zmdi-more-vert"></i>
                                                    </a>
                                                    <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                                      <li>
                                                        <a href="/balances/delete/{{$balance->id}}">Remove</a>
                                                      </li>
                                                    </ul>
                                                  </li>
                                                  </ul>
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
                                                   <i class="fa fa-microchip" style="color:#f77b5e;cursor:pointer;"  data-toggle="tooltip" title="Mined Investment"></i>
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
                                           <h4 class="card-title text-center" style="cursor:pointer"  data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($mining->amount * $price),5)}}" data-html="true">{!! $symbol !!}{{number_format(($mining->amount * $price * $multiplier),2)}}{!! $symbol2 !!}</h4>
                                           <p class="text-center" style="font-size:11px;">({{$mining->amount}} {{$mining->currency}} Mined)</p>

                                           <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer"  data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($mining->amount * $price),5)}}" data-html="true">{!! $symbol !!}{{number_format((($mining->amount * $price) * $multiplier) - (($mining->amount * $mining->bought_at) * $mining->btc_price_bought_btc), 2)}}{!! $symbol2 !!}</span>
                                             <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                             {{DB::table('cryptos')->where('symbol', $mining->currency)->first()->percent_change_24h}}%
                                             </span>

                                             <hr style="margin-top:40px;">
                                             <div class="usd">
                                             <span style="float:left;">Before</span>
                                             <span style="float:right;">After</span>
                                             <br>
                                             <span style="float:left">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer"  data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($mining->amount * $price),8)}}" data-html="true">{!! $symbol !!}{{number_format(($mining->amount * $price) * $multiplier, 2)}}{!! $symbol2 !!}</span>
                                             <br>
                                             <span style="float:left">{!! $symbol !!}0{!! $symbol2 !!}</span>
                                             <span style="float:right;cursor:pointer"  data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($price),8)}}" data-html="true">{!! $symbol !!}{{number_format($price * $multiplier,5)}}{!! $symbol2 !!}</span>
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

																	@if(Auth::user()->summed == 0)
																		@foreach($p_investments as $investment)
																			@if($investment->sold_at == null)
																				@php
																					//Variables we need
																					$market = $investment->market;
																					$amount = $investment->amount;
																					$exchange = $investment->exchange;
																					$price_og = Auth::user()->getPrice($investment->currency, $investment->market, $exchange);

																					// Name
																					if($exchange == "Manual")
																					{
																						$name = DB::table('cryptos')->where('symbol', $investment->currency)->first()->name;
																					}




																					//Decimals
																					$decimal1 = 2; // Worth
																					$decimal2 = 2; // Then
																					$decimal3 = 2; // profit
																					$decimal4 = 2; // Amount
																					$decimal5 = 2; // Now
																					$decimal6 = 5; // Then (Bought At)
																					$decimal7 = 5; // Now (Price)
																					$decimal8 = 8; // Then (Bought_at Inner)
																					$decimal9 = 8; // Now (Now Inner)

																					//Currency Settings
																					if(Auth::user()->currency == "BTC")
																					{
																						//Decimals
																						$decimal1 = 5; // Worth
																						$decimal2 = 5; // Then
																						$decimal3 = 5; // profit
																						$decimal4 = 3; // Amount
																						$decimal5 = 5; // Now
																						$decimal6 = 8; // Then (Bought At)
																						$decimal7 = 8; // Now (Price)

																						//Multipliers
																						$btc = 1;
																						$btc_usdt = $investment->btc_price_bought_usd;

																					}
																					else
																					{
																						//multipliers
																						$btc = $investment->btc_price_bought_usd * $fiat;
																						$btc_usdt = 1;

																					}

																					if($market == "BTC")
																					{
																						$inner = "<i class='fa fa-btc'></i>"; // Inner Symbol
																						$bought_at = ($investment->bought_at * $btc); // Bought at price current currency
																						$bought_at_og = $investment->bought_at; // Bought at original currency
																						$price = ($price_og * $multiplier); // Current price
																						$now = $price * $amount; // Now (Current Currency)
																						$now_og = $price_og * $amount; // Now (Original Currency)
																						$then = $bought_at * $amount; // Then (Current Currency)
																						$then_og = $bought_at_og * $amount; // Then (Original Currency)

																						if(($now - $then) > 0)
																						{
																							$color = "positive";
																							$label = "success";
																						}
																						else
																						{
																							$color = "negative";
																							$label = "danger";
																						}
																					}
																					elseif($market == "ETH")
																					{
																						$eth = Auth::user()->getPrice('ETH', 'BTC', $exchange);
																						$inner = "<img src='/icons/32x32/ETH.png' width='16' height='16'>"; // Inner Symbol
																						$bought_at = ($investment->bought_at / $investment->btc_price_bought_eth) * $btc; // Bought at price current currency
																						$bought_at_og = $investment->bought_at; // Bought at original currency
																						$price = ($price_og * $eth) * $multiplier; // Current price
																						$now = $price * $amount; // Now (Current Currency)
																						$now_og = $price_og * $amount; // Now (Original Currency)
																						$then = $bought_at * $amount; // Then (Current Currency)
																						$then_og = $bought_at_og * $amount; // Then (Original Currency)

																						if(($now - $then) > 0)
																						{
																							$color = "positive";
																							$label = "success";
																						}
																						else
																						{
																							$color = "negative";
																							$label = "danger";
																						}

																					}
																					elseif($market == "USDT")
																					{
																						$inner = "<i class='fa fa-usd'></i>"; // Inner Symbol
																						$bought_at = ($investment->bought_at / $btc_usdt) * $fiat; // Bought at price current currency
																						$bought_at_og = $investment->bought_at; // Bought at original currency

																						if(Auth::user()->currency == "BTC")
																						{
																							$price = $price_og / Auth::user()->getPrice('BTC', 'USDT', $exchange);
																						} else
																						{
																							$price = ($price_og) * $fiat; // Current price
																						}


																						$now = $price * $amount; // Now (Current Currency)
																						$now_og = $price_og * $amount; // Now (Original Currency)
																						$then = $bought_at * $amount; // Then (Current Currency)
																						$then_og = $bought_at_og * $amount; // Then (Original Currency)

																						if(($now - $then) > 0)
																						{
																							$color = "positive";
																							$label = "success";
																						}
																						else
																						{
																							$color = "negative";
																							$label = "danger";
																						}
																					}

																					// Last Decimal Check

																					if($bought_at > 10)
																					{
																						$decimal6 = 2; // Then (Bought At)
																						$decimal7 = 2; // Now (Price)
																					}
																					if($price_og > 10)
																					{
																						$decimal8 = 3; // Then (Bought_at Inner)
																						$decimal9 = 3; // Now (Now Inner)
																					}


																				@endphp



																				<div class=" col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
																					<div class="ui-block" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
																											<div class="friend-item">
																												<div class="friend-header-thumb" style="height:100px;">
																													@if($investment->exchange != "Manual")
																														<ul class="card-actions icons left-top">
																															<li>
																																<i class="material-icons" style="color:#5ecbf7;cursor:pointer;margin-left:15px;"  data-toggle="tooltip"  title="Verified investment from {{$exchange}}.">verified_user</i>
																																</li>
																															</ul>
																														@endif
																														<ul class="card-actions icons right-top">
																															<li>
																																@if($exchange == "Poloniex")
																																	<a href="https://poloniex.com/exchange#{{$market}}_{{$investment->currency}}" style="margin-right:15px;margin-top:-5px;"><img src="/icons/32x32/{{$market}}.png" alt="author"  data-toggle="tooltip" title="This investment was purchased using {{$market}}."></a>
																																@elseif($exchange == "Bittrex")
																																	<a href="https://bittrex.com/Market/Index?MarketName={{$market}}-{{$investment->currency}}" style="margin-right:15px;margin-top:-5px;"><img src="/icons/32x32/{{$market}}.png" alt="author"  data-toggle="tooltip" title="This investment was purchased using {{$market}}."></a>
																																@else
																																	<a href="https://www.worldcoinindex.com/coin/{{$name}}" style="margin-right:15px;margin-top:-5px;"><img src="/icons/32x32/{{$market}}.png" alt="author"  data-toggle="tooltip" title="This investment was purchased using {{$market}}."></a>
																																@endif																																</li>
																															</ul>
																													<img src="/assets/logos/{{$investment->currency}}.png" alt="friend" style="max-height:60px;display:block;margin:0 auto;margin-top:20px;width:initial;">
																												</div>

																												<div class="friend-item-content">

																													<div class="more">
																														<svg class="olymp-three-dots-icon"><use xlink:href="/version2/icons/icons.svg#olymp-three-dots-icon"></use></svg>
																														<ul class="more-dropdown">
																															<li>
																																@if($exchange == "Poloniex")
																																	<a href="/investments/remove/polo/{{$investment->id}}">Block Investment</a>
																																@elseif($exchange == "Bittrex")
																																	<a href="/investments/remove/bittrex/{{$investment->id}}">Block Investment</a>
																																@elseif($exchange == "Manual")
																																	<a href="/investments/remove/{{$investment->id}}">Delete Investment</a>
																																@endif
																															</li>
																															<li>
																																<a href="/investment/private/{{$exchange}}/{{$investment->id}}">Make @if($investment->private == 0)Private @else Public @endif</a>
																															</li>
																															<li>
																																<a href="javascript:void(0)" data-toggle="modal" data-target="#write_note" id="{{$investment->id}}" class="write_note_button" exchange="{{$exchange}}">Write Note</a>
																															</li>
																														</ul>
																													</div>
																													<div class="friend-avatar">
																														<div class="author-thumb" style="margin-top:30px;height:40px;">
																														</div>
																														<div class="author-content">
																															<span class="h3 author-name"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($now_og, 3)}}" data-html="true">{!! $symbol !!}{{number_format($amount * $price, $decimal1)}}{!! $symbol2 !!} <span class="{{$color}}" style="font-size:12px;vertical-align:middle;"> {{number_format(($now - $then) / $then * 100, 2)}}%</span></span>
																															<div class="h6">({{number_format($amount, $decimal4)}} {{$investment->currency}})</div>
																														</div>
																													</div>

																													<div class="swiper-container">
																														<div class="swiper-wrapper">
																															<div class="swiper-slide">
																																<div class="friend-count" data-swiper-parallax="-500">
																																	<a href="#" class="friend-count-item">
																																		<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($then_og, 3)}}" data-html="true">{!! $symbol !!}{{number_format($then, $decimal2)}}{!! $symbol2 !!}</div>
																																		<div class="title">Then</div>
																																	</a>
																																	<a href="#" class="friend-count-item">
																																		<div class="h6 positive" style="font-size: 1rem;" data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($now_og - $then_og, 3)}}" data-html="true"><span class="label label-{{$label}}" style="color:white;">{!! $symbol !!}{{number_format($now - $then, $decimal3)}}{!! $symbol2 !!}</span></div>
																																		<div class="title">Profit</div>
																																	</a>
																																	<a href="#" class="friend-count-item">
																																		<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{(number_format($now_og, 3))}}" data-html="true">{!! $symbol !!}{{number_format($now, $decimal5)}}{!! $symbol2 !!}</div>
																																		<div class="title">Now</div>
																																	</a>
																																</div>

																															</div>

																															<div class="swiper-slide">
																																<div class="friend-count" data-swiper-parallax="-500">
																																	<a href="#" class="friend-count-item">
																																		<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($bought_at_og, $decimal8)}}" data-html="true">{!! $symbol !!}{{number_format($bought_at, $decimal6)}}{!! $symbol2 !!}</div>
																																		<div class="title">Then</div>
																																	</a>
																																	@if($decimal7 != 8)
																																		<a href="#" class="friend-count-item">
																																			<div class="h6" style="font-size: 1rem;">{{$exchange}}</div>
																																			<div class="title">Exchange</div>
																																		</a>
																																	@endif
																																	<a href="#" class="friend-count-item">
																																		<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($price_og, $decimal9)}}" data-html="true">{!! $symbol !!}{{number_format($price, $decimal7)}}{!! $symbol2 !!}</div>
																																		<div class="title">Now</div>
																																	</a>
																																</div>
																															</div>
																															@if($investment->note)
																																<div class="swiper-slide">
																																	<p class="friend-about" data-swiper-parallax="-500" style="text-align:center;">
																																		{{$investment->note}}
																																	</p>

																																</div>
																															@endif
																														</div>

																														<!-- If we need pagination -->
																														<div class="swiper-pagination"></div>
																													</div>
																												</div>
																											</div>
																										</div>
																								</div>
																							@endif
																					@endforeach
																				@else
																					@foreach($p_investments as $investment)
																						@if($investment->sold_at == null)
																							@php
																								//Variables we need
																								$market = $investment->market;
																								$amount = $investment->amount;
																								$price_og = Auth::user()->getPrice($investment->currency, $investment->market, 'Poloniex');
                                                $manualprice_og = Auth::user()->getPrice($investment->currency, $investment->market, 'Manual');
                                                $bitprice_og = Auth::user()->getPrice($investment->currency, $investment->market, 'Bittrex');




																								//Decimals
																								$decimal1 = 2; // Worth
																								$decimal2 = 2; // Then
																								$decimal3 = 2; // profit
																								$decimal4 = 2; // Amount
																								$decimal5 = 2; // Now
																								$decimal6 = 5; // Then (Bought At)
																								$decimal7 = 5; // Now (Price)
																								$decimal8 = 8;
																								$decimal9 = 8;


																								//Currency Settings
																								if(Auth::user()->currency == "BTC")
																								{
																									//Decimals
																									$decimal1 = 5; // Worth
																									$decimal2 = 5; // Then
																									$decimal3 = 5; // profit
																									$decimal4 = 3; // Amount
																									$decimal5 = 5; // Now
																									$decimal6 = 8; // Then (Bought At)
																									$decimal7 = 8; // Now (Price)

																									//Multipliers
																									$btc = 1;
																								}
																								else
																								{
																									//multipliers
																									$btc = $investment->btc_price_bought_usd * $fiat;
																								}

																								if($market == "BTC")
																								{
																									$inner = "<i class='fa fa-btc'></i>"; // Inner Symbol
																									$bought_at = ($investment->total / $amount) * $fiat; // Bought at price current currency
																									if(Auth::user()->currency == "BTC")
																									{
																									$bought_at = $investment->bought_at;
																									}
																									$bought_at_og = $investment->bought_at; // Bought at original currency
																									$price = ($price_og * $multiplier); // Current price (Poloniex Investments)
																									$manualprice = ($manualprice_og * $multiplier); // Current price (Manual investments)
																									$bitprice = ($bitprice_og * $multiplier); // Current price (Bittrex investments)
																									$now = ($investment->poloniex_amount * $price) + ($investment->bittrex_amount * $bitprice) + ($investment->manual_amount * $manualprice); // Now (Current Currency)
																									$now_og = $price_og * $amount; // Now (Original Currency)
																									$then = $bought_at * $amount; // Then (Current Currency)
																									$then_og = $bought_at_og * $amount; // Then (Original Currency)

																									if(($now - $then) > 0)
																									{
																										$color = "positive";
																										$label = "success";
																									}
																									else
																									{
																										$color = "negative";
																										$label = "danger";
																									}
																								}
																								elseif($market == "ETH")
																								{
																									$eth = Auth::user()->getPrice('ETH', 'BTC', 'Poloniex');
																									$inner = "<img src='/icons/32x32/ETH.png' width='16' height='16'>"; // Inner Symbol
																									if(Auth::user()->currency == "BTC")
																									{
																										$bought_at = ($investment->total_eth_btc / $amount); // Bought at price current currency
																									} else
																									{
																										$bought_at = (($investment->total_eth_btc / $amount) * $btc); // Bought at price current currency
																									}

																									$bought_at_og = $investment->bought_at; // Bought at original currency
																									$price = ($price_og * $eth) * $multiplier; // Current price (Poloniex Investments)
																									$manualprice = ($manualprice_og * $eth) * $multiplier; // Current price (Manual investments)
																									$bitprice = ($bitprice_og * $eth) * $multiplier; // Current price (Bittrex investments)
																									$now = ($investment->poloniex_amount * $price) + ($investment->bittrex_amount * $bitprice) + ($investment->manual_amount * $manualprice); // Now (Current Currency)
																									$now_og = $price_og * $amount; // Now (Original Currency)
																									$then = $bought_at * $amount; // Then (Current Currency)
																									$then_og = $bought_at_og * $amount; // Then (Original Currency)

																									if(($now - $then) > 0)
																									{
																										$color = "positive";
																										$label = "success";
																									}
																									else
																									{
																										$color = "negative";
																										$label = "danger";
																									}
																								}
																								elseif($market == "USDT")
																								{
																									$inner = "<i class='fa fa-usd'></i>"; // Inner Symbol
																									if(Auth::user()->currency == "BTC")
																									{
																										$bought_at = ($investment->total_usdt_btc / $amount); // Bought at price current currency
																										$btc = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
																									} else
																									{
																										$bought_at = ($investment->total_usdt / $amount) * $fiat; // Bought at price current currency
																										$btc = 1;
																									}
																									$bought_at_og = $investment->bought_at; // Bought at original currency

																									//USDT To BTC calculations
																									if(Auth::user()->currency == "BTC")
																									{
																										$price = ($price_og / Auth::user()->getPrice('BTC', 'USDT', 'Poloniex')); // Current price (Poloniex Investments)
																										$manualprice = ($manualprice_og / Auth::user()->getPrice('BTC', 'USDT', 'Poloniex')); // Current price (Manual investments)
																										$bitprice = ($bitprice_og / Auth::user()->getPrice('BTC', 'USDT', 'Bittrex')); // Current price (Bittrex investments)
																									} else
																									{
																										$price = ($price_og / $btc) * $fiat; // Current price (Poloniex Investments)
																										$manualprice = ($manualprice_og / $btc) * $fiat; // Current price (Manual investments)
																										$bitprice = ($bitprice_og / $btc) * $fiat; // Current price (Bittrex investments)
																									}


																									$now = ($investment->poloniex_amount * $price) + ($investment->bittrex_amount * $bitprice) + ($investment->manual_amount * $manualprice); // Now (Current Currency)
																									$now_og = $price_og * $amount; // Now (Original Currency)
																									$then = $bought_at * $amount; // Then (Current Currency)
																									$then_og = $bought_at_og * $amount; // Then (Original Currency)

																									if(($now - $then) > 0)
																									{
																										$color = "positive";
																										$label = "success";
																									}
																									else
																									{
																										$color = "negative";
																										$label = "danger";
																									}
																								}

																								// Last Decimal Check

																								if($bought_at > 10)
																								{
																									$decimal6 = 2; // Then (Bought At)
																									$decimal7 = 2; // Now (Price)
																								}

																								if($price_og > 10)
																								{
																									$decimal8 = 3; // Then (Bought_at Inner)
																									$decimal9 = 3; // Now (Now Inner)
																								}


																							@endphp

																							<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																								<div class="ui-block" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
																														<div class="friend-item">
																															<div class="friend-header-thumb" style="height:100px;">
																																@if($investment->bittrex_amount > 0.01 || $investment->poloniex_amount > 0.01)
																																	<ul class="card-actions icons left-top">
																																		<li>
																																			<i class="material-icons" style="color:#5ecbf7;cursor:pointer;margin-left:15px;"  data-toggle="tooltip"   title="Some or all of the summed investments are verified.">verified_user</i>
																																			</li>
																																		</ul>
																																	@endif
																																	<ul class="card-actions icons right-top">
																																		<li>
																																			<a href="javascript:void(0)" style="margin-right:15px;margin-top:-10px;"><img src="/icons/32x32/{{$market}}.png" alt="author"  data-toggle="tooltip" title="These investment were purchased using {{$market}}."></a>
																																			</li>
																																		</ul>
																																<img src="/assets/logos/{{$investment->currency}}.png" alt="friend" style="max-height:60px;display:block;margin:0 auto;margin-top:20px;width:initial;">
																															</div>

																															<div class="friend-item-content" style="margin-top:40px;">
																																<div class="friend-avatar">
																																	<div class="author-thumb" style="margin-top:30px;height:40px;width:100%;">
																																	<div class="author-content">
																																		<span class="h3 author-name"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($now_og, 3)}}" data-html="true">{!! $symbol !!}{{number_format($now, $decimal1)}}{!! $symbol2 !!} <span class="{{$color}}" style="font-size:12px;vertical-align:middle;"> {{number_format(($now - $then) / $then * 100, 2)}}%</span></span>
																																		<div class="h6">({{number_format($amount, $decimal4)}} {{$investment->currency}})</div>
																																	</div>
																																</div>

																																<div class="swiper-container">
																																	<div class="swiper-wrapper">
																																		<div class="swiper-slide">
																																			<div class="friend-count" data-swiper-parallax="-500" style="margin-top:25px;margin-bottom:0px;">
																																				<a href="#" class="friend-count-item">
																																					<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($then_og, 3)}}" data-html="true">{!! $symbol !!}{{number_format($then, $decimal2)}}{!! $symbol2 !!}</div>
																																					<div class="title">Then</div>
																																				</a>
																																				<a href="#" class="friend-count-item">
																																					<div class="h6 positive" style="font-size: 1rem;" data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($now_og - $then_og, 3)}}" data-html="true"><span class="label label-{{$label}}" style="color:white;">{!! $symbol !!}{{number_format($now - $then, $decimal3)}}{!! $symbol2 !!}</span></div>
																																					<div class="title">Profit</div>
																																				</a>
																																				<a href="#" class="friend-count-item">
																																					<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{(number_format($now_og, 3))}}" data-html="true">{!! $symbol !!}{{number_format($now, $decimal5)}}{!! $symbol2 !!}</div>
																																					<div class="title">Now</div>
																																				</a>
																																			</div>

																																		</div>

																																		<div class="swiper-slide">
																																			<div class="friend-count" data-swiper-parallax="-500" style="margin-top:25px;margin-bottom:0px;">
																																				<a href="#" class="friend-count-item">
																																					<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($bought_at_og, $decimal8)}}" data-html="true">{!! $symbol !!}{{number_format($bought_at, $decimal6)}}{!! $symbol2 !!}</div>
																																					<div class="title">Then</div>
																																				</a>
																																				@if($decimal7 != 8)
																																					<a href="#" class="friend-count-item">
																																						<div class="h6" style="font-size: 1rem;">Mixed</div>
																																						<div class="title">Exchange</div>
																																					</a>
																																				@endif
																																				<a href="#" class="friend-count-item">
																																					<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($price_og, $decimal9)}}" data-html="true">{!! $symbol !!}{{number_format($price, $decimal7)}}{!! $symbol2 !!}</div>
																																					<div class="title">Now</div>
																																				</a>
																																			</div>
																																		</div>
																																			<div class="swiper-slide">
																																					<table style="width:100%;font-size:10px;height:100px!important;overflow-y:scroll;display:grid;">
																																						<tbody style="margin:0 auto;">
																																						<tr>
																																							<th style="padding:0px 20px 0px 20px">Amount</th>
																																							<th style="padding:0px 20px 0px 20px">Rate</th>
																																							<th style="padding:0px 20px 0px 20px">Exchange</th>
																																						</tr>

																																					@foreach(DB::table('polo_investments')->where([['userid', '=', Auth::user()->id], ['currency', '=', $investment->currency]])->get() as $i)
																																					<tr>
																																						<td>{{number_format($i->amount,2)}}</td>
																																						<td>{{number_format($i->bought_at, 8)}}</td>
																																						<td>Poloniex</td>
																																					@endforeach
																																					@foreach(DB::table('bittrex_investments')->where([['userid', '=', Auth::user()->id], ['currency', '=', $investment->currency]])->get() as $i)
																																					<tr>
																																						<td>{{number_format($i->amount,2)}}</td>
																																						<td>{{number_format($i->bought_at, 8)}}</td>
																																						<td>Bittrex</td>
																																					@endforeach
																																					@foreach(DB::table('manual_investments')->where([['userid', '=', Auth::user()->id], ['currency', '=', $investment->currency]])->get() as $i)
																																					<tr>
																																						<td>{{number_format($i->amount,2)}}</td>
																																						<td>{{number_format($i->bought_at, 8)}}</td>
																																						<td>Manual</td>
																																					@endforeach
																																				</tbody>
																																				</table>

																																			</div>
																																	</div>

																																	<!-- If we need pagination -->
																																	<div class="swiper-pagination"></div>
																																</div>
																															</div>
																														</div>
																													</div>
																											</div>
																										</div>
																										@endif
																								@endforeach
																							@endif

                                  </div>
                                </div>

                              <div class="tab-pane fadeIn" id="investments-sold">
                                <div class="row">
																@if(Auth::user()->summed == 0)
																	@foreach($p_investments as $investment)
																		@if($investment->sold_at != null)
																			@php
																				//Variables we need
																				$market = $investment->market;
																				$soldmarket = $investment->sold_market;
																				$amount = $investment->amount;
																				$exchange = $investment->exchange;
																				$price_og = $investment->sold_at;

																				// Name
																				if($exchange == "Manual")
																				{
																					$name = DB::table('cryptos')->where('symbol', $investment->currency)->first()->name;
																				}




																				//Decimals
																				$decimal1 = 2; // Worth
																				$decimal2 = 2; // Then
																				$decimal3 = 2; // profit
																				$decimal4 = 2; // Amount
																				$decimal5 = 2; // Now
																				$decimal6 = 5; // Then (Bought At)
																				$decimal7 = 5; // Now (Price)
																				$decimal8 = 8;
																				$decimal9 = 8;


																				//Currency Settings
																				if(Auth::user()->currency == "BTC")
																				{
																					//Decimals
																					$decimal1 = 5; // Worth
																					$decimal2 = 5; // Then
																					$decimal3 = 5; // profit
																					$decimal4 = 3; // Amount
																					$decimal5 = 5; // Now
																					$decimal6 = 8; // Then (Bought At)
																					$decimal7 = 8; // Now (Price)

																					//Multipliers
																					$btc = 1;
																					$btc_sold = 1;
																					$btc_usdt = $investment->btc_price_bought_usd;
																				}
																				else
																				{
																					//multipliers
																					$btc = $investment->btc_price_bought_usd * $fiat;
																					$btc_sold = $investment->btc_price_sold_usd * $fiat;
																					$btc_usdt = 1;

																				}

																				if($market == "BTC")
																				{
																					$inner = "<i class='fa fa-btc'></i>"; // Inner Symbol
																					$bought_at = ($investment->bought_at * $btc); // Bought at price current currency
																					$bought_at_og = $investment->bought_at; // Bought at original currency
																					$then = $bought_at * $amount; // Then (Current Currency)
																					$then_og = $bought_at_og * $amount; // Then (Original Currency)
																				}
																				elseif($market == "ETH")
																				{
																					$inner = "<img src='/icons/32x32/ETH.png' width='16' height='16'>"; // Inner Symbol
																					$bought_at = ($investment->bought_at / $investment->btc_price_bought_eth) * $btc; // Bought at price current currency
																					$bought_at_og = $investment->bought_at; // Bought at original currency
																					$then = $bought_at * $amount; // Then (Current Currency)
																					$then_og = $bought_at_og * $amount; // Then (Original Currency)

																				}
																				elseif($market == "USDT")
																				{
																					$inner = "<i class='fa fa-usd'></i>"; // Inner Symbol
																					$bought_at = ($investment->bought_at / $btc_usdt) * $fiat; // Bought at price current currency
																					$bought_at_og = $investment->bought_at; // Bought at original currency
																					$then = $bought_at * $amount; // Then (Current Currency)
																					$then_og = $bought_at_og * $amount; // Then (Original Currency)
																				} elseif($market == "Deposit")
																				{
																					$inner = "<i class='fa fa-usd'></i>"; // Inner Symbol
																					$bought_at = 0; // Bought at price current currency
																					$bought_at_og = 0; // Bought at original currency
																					$then = 0; // Then (Current Currency)
																					$then_og = 0; // Then (Original Currency)
																				}


																				// Soldmarkets
																				if($soldmarket == "BTC")
																				{
																					$price = ($price_og * $btc_sold); // Current price
																					$now = $price * $amount; // Now (Current Currency)
																					$now_og = $price_og * $amount; // Now (Original Currency)

																					if(($now - $then) > 0)
																					{
																						$color = "positive";
																						$label = "success";
																					}
																					else
																					{
																						$color = "negative";
																						$label = "danger";
																					}
																					$inner2 = "<i class='fa fa-btc'></i>"; // Inner Symbol


																					//Profit to og currency

																					if($market == "USDT")
																					{
																						$profit = $now_og - ($then_og / $investment->btc_price_bought_usd);
																					}
																					elseif($market == "ETH")
																					{
																						$profit = $now_og - ((1 / $investment->btc_price_bought_eth) * $then_og);
																					} else {
																						$profit = ($now_og - $then_og);
																					}



																				} elseif($soldmarket == "USDT")
																				{
																					if(Auth::user()->currency == "BTC")
																					{
																						$price = $price_og / $investment->btc_price_sold_usdt;
																					} else
																					{
																						$price = ($price_og) * $fiat; // Current price
																					}

																					$now = $price * $amount; // Now (Current Currency)
																					$now_og = $price_og * $amount; // Now (Original Currency)

																					if(($now - $then) > 0)
																					{
																						$color = "positive";
																						$label = "success";
																					}
																					else
																					{
																						$color = "negative";
																						$label = "danger";
																					}
																					$inner2 = "<i class='fa fa-usd'></i>"; // Inner Symbol

																					//Profit to og currency

																					if($market == "BTC")
																					{
																						$profit = $now_og - ($then_og * $investment->btc_price_bought_usd);
																					}
																					elseif($market == "ETH")
																					{
																						$profit = $now_og - (($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $then_og);
																					} else {
																						$profit = ($now_og - $then_og);
																					}
																				}
																				elseif($soldmarket == "ETH")
																				{
																					$inner2 = "<img src='/icons/32x32/ETH.png' width='16' height='16'>"; // Inner Symbol
																					$eth = $investment->btc_price_sold_usd / $investment->btc_price_sold_eth;

																					if(Auth::user()->currency == "BTC")
																					{
																						$price = (1 / $investment->btc_price_sold_eth) * $price_og;
																					} else {
																					$price = ($price_og * $eth) * $fiat; // Current price
																					}

																					$now = $price * $amount; // Now (Current Currency)
																					$now_og = $price_og * $amount; // Now (Original Currency)

																					if(($now - $then) > 0)
																					{
																						$color = "positive";
																						$label = "success";
																					}
																					else
																					{
																						$color = "negative";
																						$label = "danger";
																					}

																					//Profit to og currency

																					if($market == "BTC")
																					{
																						$profit = $now_og - ($then_og * $investment->btc_price_bought_eth);
																					}
																					elseif($market == "USDT")
																					{
																						$profit = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $then;
																					} else {
																						$profit = ($now_og - $then_og);
																					}
																				}





																				// Last Decimal Check

																				if($bought_at > 10)
																				{
																					$decimal6 = 2; // Then (Bought At)
																					$decimal7 = 2; // Now (Price)
																				}
																				if($price_og > 10)
																				{
																					$decimal8 = 3; // Then (Bought At)
																					$decimal9 = 3; // Now (Price)
																				}


																			@endphp


																			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																				<div class="ui-block" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
																										<div class="friend-item">
																											<div class="friend-header-thumb" style="height:100px;">
																												@if($investment->exchange != "Manual")
																													<ul class="card-actions icons left-top">
																														<li>
																															<i class="material-icons" style="color:#5ecbf7;cursor:pointer;margin-left:15px;"  data-toggle="tooltip"   title="Verified investment from {{$exchange}}.">verified_user</i>
																															</li>
																														</ul>
																													@endif
																													<ul class="card-actions icons right-top">
																														<li>
																															@if($market != "Deposit")
																																@if($exchange == "Poloniex")
																																	<a style="margin-top:-10px;" href="https://poloniex.com/exchange#{{$market}}_{{$investment->currency}}"><img src="/icons/32x32/{{$market}}.png" alt="author"  data-toggle="tooltip" title="This investment was purchased using {{$market}}."></a>
																																	<a style="margin-top:-10px;margin-right:15px;" href="https://poloniex.com/exchange#{{$market}}_{{$investment->currency}}"><img src="/icons/32x32/{{$soldmarket}}.png" alt="author"  data-toggle="tooltip" title="This investment was sold to {{$soldmarket}}."></a>
																																@elseif($exchange == "Bittrex")
																																	<a style="margin-top:-10px;" href="https://bittrex.com/Market/Index?MarketName={{$market}}-{{$investment->currency}}"><img src="/icons/32x32/{{$market}}.png" alt="author"  data-toggle="tooltip" title="This investment was purchased using {{$market}}."></a>
																																	<a style="margin-top:-10px;margin-right:15px;" href="https://bittrex.com/Market/Index?MarketName={{$market}}-{{$investment->currency}}"><img src="/icons/32x32/{{$soldmarket}}.png" alt="author"  data-toggle="tooltip" title="This investment was sold to {{$soldmarket}}."></a>
																																@else
																																	<a style="margin-top:-10px;" href="https://www.worldcoinindex.com/coin/{{$name}}"><img src="/icons/32x32/{{$market}}.png" alt="author"  data-toggle="tooltip" title="This investment was purchased using {{$market}}."></a>
																																	<a style="margin-top:-10px;margin-right:15px;" href="https://www.worldcoinindex.com/coin/{{$name}}"><img src="/icons/32x32/{{$soldmarket}}.png" alt="author"  data-toggle="tooltip" title="This investment was sold to {{$soldmarket}}."></a>
																																@endif
																															@else
																																@if($exchange == "Poloniex")
																																	<a style="margin-top:-10px;margin-right:15px;" href="https://poloniex.com/exchange#{{$market}}_{{$investment->currency}}"><img src="/icons/32x32/{{$soldmarket}}.png" alt="author"  data-toggle="tooltip" title="This investment was deposited then sold to {{$soldmarket}}."></a>
																																@elseif($exchange == "Bittrex")
																																	<a style="margin-top:-10px;margin-right:15px;" href="https://bittrex.com/Market/Index?MarketName={{$market}}-{{$investment->currency}}"><img src="/icons/32x32/{{$soldmarket}}.png" alt="author"  data-toggle="tooltip" title="This investment was deposited then sold to {{$soldmarket}}."></a>
																																@else
																																	<a style="margin-top:-10px;margin-right:15px;" href="https://www.worldcoinindex.com/coin/{{$name}}"><img src="/icons/32x32/{{$soldmarket}}.png" alt="author"  data-toggle="tooltip" title="This investment was deposited then sold to {{$soldmarket}}."></a>
																																@endif
																															@endif
																														</li>
																														</ul>
																												<img src="/assets/logos/{{$investment->currency}}.png" alt="friend" style="max-height:60px;display:block;margin:0 auto;margin-top:20px;width:initial;">
																											</div>

																											<div class="friend-item-content">

																												<div class="more">
																													<svg class="olymp-three-dots-icon"><use xlink:href="/version2/icons/icons.svg#olymp-three-dots-icon"></use></svg>
																													<ul class="more-dropdown">
																														<li>
																															@if($exchange == "Poloniex")
																																<a href="/investments/remove/polo/{{$investment->id}}">Block Investment</a>
																															@elseif($exchange == "Bittrex")
																																<a href="/investments/remove/bittrex/{{$investment->id}}">Block Investment</a>
																															@elseif($exchange == "Manual")
																																<a href="/investments/remove/{{$investment->id}}">Delete Investment</a>
																															@endif
																														</li>
																														<li>
																															<a href="/investment/private/{{$exchange}}/{{$investment->id}}">Make @if($investment->private == 0)Private @else Public @endif</a>
																														</li>
																														<li>
																															<a href="javascript:void(0)" data-toggle="modal" data-target="#write_note" id="{{$investment->id}}" class="write_note_button" exchange="{{$exchange}}">Write Note</a>
																														</li>
																													</ul>
																												</div>
																												<div class="friend-avatar">
																													<div class="author-thumb" style="margin-top:30px;height:40px;">
																													</div>
																													<div class="author-content">
																														<span class="h3 author-name"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner2 !!} {{number_format($now_og, 3)}}" data-html="true">{!! $symbol !!}{{number_format($amount * $price, $decimal1)}}{!! $symbol2 !!} <span class="{{$color}}" style="font-size:12px;vertical-align:middle;"> @if($investment->market != "Deposit"){{number_format(($now - $then) / $then * 100, 2)}}% @else 100% @endif</span></span>
																														<div class="h6">({{number_format($amount, $decimal4)}} {{$investment->currency}})</div>
																													</div>
																												</div>

																												<div class="swiper-container">
																													<div class="swiper-wrapper">
																														<div class="swiper-slide">
																															<div class="friend-count" data-swiper-parallax="-500">
																																<a href="#" class="friend-count-item">
																																	<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($then_og, 3)}}" data-html="true">{!! $symbol !!}{{number_format($then, $decimal2)}}{!! $symbol2 !!}</div>
																																	<div class="title">Then</div>
																																</a>
																																<a href="#" class="friend-count-item">
																																	<div class="h6 positive" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner2 !!} {{number_format($profit, 3)}}" data-html="true"><span class="label label-{{$label}}" style="color:white;">{!! $symbol !!}{{number_format($now - $then, $decimal3)}}{!! $symbol2 !!}</span></div>
																																	<div class="title">Profit</div>
																																</a>
																																<a href="#" class="friend-count-item">
																																	<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner2 !!} {{(number_format($now_og, 3))}}" data-html="true">{!! $symbol !!}{{number_format($now, $decimal5)}}{!! $symbol2 !!}</div>
																																	<div class="title">Sold</div>
																																</a>
																															</div>

																														</div>

																														<div class="swiper-slide">
																															<div class="friend-count" data-swiper-parallax="-500">
																																<a href="#" class="friend-count-item">
																																	<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($bought_at_og, 8)}}" data-html="true">{!! $symbol !!}{{number_format($bought_at, $decimal6)}}{!! $symbol2 !!}</div>
																																	<div class="title">Then</div>
																																</a>
																																@if($decimal7 != 8)
																																	<a href="#" class="friend-count-item">
																																		<div class="h6" style="font-size: 1rem;">{{$exchange}}</div>
																																		<div class="title">Exchange</div>
																																	</a>
																																@endif
																																<a href="#" class="friend-count-item">
																																	<div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner2 !!} {{number_format($price_og, 8)}}" data-html="true">{!! $symbol !!}{{number_format($price, $decimal7)}}{!! $symbol2 !!}</div>
																																	<div class="title">Sold</div>
																																</a>
																															</div>
																														</div>
																														@if($investment->note)
																															<div class="swiper-slide">
																																<p class="friend-about" data-swiper-parallax="-500" style="text-align:center;">
																																	{{$investment->note}}
																																</p>

																															</div>
																														@endif
																													</div>

																													<!-- If we need pagination -->
																													<div class="swiper-pagination"></div>
																												</div>
																											</div>
																										</div>
																									</div>
																							</div>
																						@endif
																				@endforeach
																@else
																	@foreach($p_investments as $investment)
																		@if($investment->sold_at != null)
																			@php
																				//Variables we need
																				$market = $investment->market;
																				$soldmarket = $investment->soldmarket;
																				$amount = $investment->amount;




																				//Decimals
																				$decimal1 = 2; // Worth
																				$decimal2 = 2; // Then
																				$decimal3 = 2; // profit
																				$decimal4 = 2; // Amount
																				$decimal5 = 2; // Now
																				$decimal6 = 5; // Then (Bought At)
																				$decimal7 = 5; // Now (Price)
																				$decimal8 = 8;
																				$decimal9 = 8;


																				//Currency Settings
																				if(Auth::user()->currency == "BTC")
																				{
																					//Decimals
																					$decimal1 = 5; // Worth
																					$decimal2 = 5; // Then
																					$decimal3 = 5; // profit
																					$decimal4 = 3; // Amount
																					$decimal5 = 5; // Now
																					$decimal6 = 8; // Then (Bought At)
																					$decimal7 = 8; // Now (Price)

																					//Multipliers
																					$btc = 1;
																				}
																				else
																				{
																					//multipliers
																					$btc = $investment->btc_price_bought_usd * $fiat;
																				}

																				if($market == "BTC")
																				{
																					$inner = "<i class='fa fa-btc'></i>"; // Inner Symbol
																					$bought_at = ($investment->total / $amount) * $fiat; // Bought at price current currency
																					if(Auth::user()->currency == "BTC")
																					{
																					$bought_at = $investment->bought_at;
																					}
																					$bought_at_og = $investment->bought_at; // Bought at original currency
																					$then = $bought_at * $amount; // Then (Current Currency)
																					$then_og = $bought_at_og * $amount; // Then (Original Currency)
																				}
																				elseif($market == "ETH")
																				{
																					$eth = Auth::user()->getPrice('ETH', 'BTC', 'Poloniex');
																					$inner = "<img src='/icons/32x32/ETH.png' width='16' height='16'>"; // Inner Symbol
																					if(Auth::user()->currency == "BTC")
																					{
																						$bought_at = ($investment->total_eth_btc / $amount); // Bought at price current currency
																					} else
																					{
																						$bought_at = (($investment->total_eth_btc / $amount) * $btc); // Bought at price current currency
																					}

																					$bought_at_og = $investment->bought_at; // Bought at original currency
																					$then = $bought_at * $amount; // Then (Current Currency)
																					$then_og = $bought_at_og * $amount; // Then (Original Currency)
																				}
																				elseif($market == "USDT")
																				{
																					$inner = "<i class='fa fa-usd'></i>"; // Inner Symbol
																					if(Auth::user()->currency == "BTC")
																					{
																						$bought_at = ($investment->total_usdt_btc / $amount); // Bought at price current currency
																						$btc = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
																					} else
																					{
																						$bought_at = ($investment->total_usdt / $amount) * $fiat; // Bought at price current currency
																						$btc = 1;
																					}
																					$bought_at_og = $investment->bought_at; // Bought at original currency
																					$then = $bought_at * $amount; // Then (Current Currency)
																					$then_og = $bought_at_og * $amount; // Then (Original Currency)
																				} elseif($market == "Deposit")
																				{
																					$inner = "<i class='fa fa-usd'></i>"; // Inner Symbol
																					$bought_at = 0; // Bought at price current currency
																					if(Auth::user()->currency == "BTC")
																					{
																						$bought_at = 0;
																					}
																					$bought_at_og = 0; // Bought at original currency
																					$then = $bought_at * $amount; // Then (Current Currency)
																					$then_og = $bought_at_og * $amount; // Then (Original Currency)
																				}


																				if($soldmarket == "BTC")
																				{
																						if(Auth::user()->currency == "BTC")
																						{
																							$sold_at = $investment->sold_at;
																						} else {
																							$sold_at = ($investment->total_sold / $amount) * $fiat;
																						}

																						$sold_at_og = $investment->sold_at;
																						$now = $sold_at * $amount;
																						$now_og = $sold_at_og * $amount;

																						if(($now - $then) > 0)
																						{
																							$color = "positive";
																							$label = "success";
																						}
																						else
																						{
																							$color = "negative";
																							$label = "danger";
																						}

																						$inner2 = "<i class='fa fa-btc'></i>"; // Inner Symbol


																						//Profit to og currency

																						if($market == "USDT")
																						{
																							$profit = $now_og - ($then_og / $investment->btc_price_bought_usd);
																						}
																						elseif($market == "ETH")
																						{
																							$profit = $now_og - ((1 / $investment->btc_price_bought_eth) * $then_og);
																						} else {
																							$profit = ($now_og - $then_og);
																						}

																				}
																				elseif($soldmarket == "USDT")
																				{
																					if(Auth::user()->currency == "BTC")
																					{
																						$sold_at = $investment->sold_at / $investment->btc_price_sold_usd;
																					} else {
																						$sold_at = ($investment->total_usdt_sold / $amount) * $fiat;
																					}

																					$sold_at_og = $investment->sold_at;
																					$now = $sold_at * $amount;
																					$now_og = $sold_at_og * $amount;

																					if(($now - $then) > 0)
																					{
																						$color = "positive";
																						$label = "success";
																					}
																					else
																					{
																						$color = "negative";
																						$label = "danger";
																					}

																					if(($now - $then) > 0)
																					{
																						$color = "positive";
																						$label = "success";
																					}
																					else
																					{
																						$color = "negative";
																						$label = "danger";
																					}
																					$inner2 = "<i class='fa fa-usd'></i>"; // Inner Symbol

																					//Profit to og currency

																					if($market == "BTC")
																					{
																						$profit = $now_og - ($then_og * $investment->btc_price_bought_usd);
																					}
																					elseif($market == "ETH")
																					{
																						$profit = $now_og - (($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $then_og);
																					} else {
																						$profit = ($now_og - $then_og);
																					}
																				}
																				elseif($soldmarket == "ETH")
																				{
																					$inner2 = "<img src='/icons/32x32/ETH.png' width='16' height='16'>"; // Inner Symbol
																					$eth = $investment->btc_price_sold_usd / $investment->btc_price_sold_eth;

																					if(Auth::user()->currency == "BTC")
																					{
																						$sold_at = (1 / $investment->btc_price_sold_eth) * $investment->sold_at;
																					} else {
																						$sold_at = (($investment->total_eth_sold / $amount) * $eth) * $fiat;
																					}


																					$sold_at_og = $investment->sold_at;
																					$now = $sold_at * $amount;
																					$now_og = $sold_at_og * $amount;

																					if(($now - $then) > 0)
																					{
																						$color = "positive";
																						$label = "success";
																					}
																					else
																					{
																						$color = "negative";
																						$label = "danger";
																					}

																					//Profit to og currency

																					if($market == "BTC")
																					{
																						$profit = $now_og - ($then_og * $investment->btc_price_bought_eth);
																					}
																					elseif($market == "USDT")
																					{
																						$profit = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $then;
																					} else {
																						$profit = ($now_og - $then_og);
																					}
																				}






																				// Last Decimal Check

																				if($bought_at > 10)
																				{
																					$decimal6 = 2; // Then (Bought At)
																					$decimal7 = 2; // Now (Price)
																				}

																				if($price_og > 10)
																				{
																					$decimal8 = 3; // Then (Bought_at Inner)
																					$decimal9 = 3; // Now (Now Inner)
																				}


																			@endphp

																			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
																				<div class="ui-block" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
																										<div class="friend-item">
																											<div class="friend-header-thumb" style="height:100px;">
																												@if($investment->bittrex_amount > 0.01 || $investment->poloniex_amount > 0.01)
																													<ul class="card-actions icons left-top">
																														<li>
																															<i class="material-icons" style="color:#5ecbf7;cursor:pointer;margin-left:15px;"  data-toggle="tooltip"   title="Some or all of the summed investments are verified.">verified_user</i>
																															</li>
																														</ul>
																													@endif
																													<ul class="card-actions icons right-top">
																														<li>
																															<a style="margin-top:-10px;margin-right:15px;" href="javascript:void(0)"><img src="/icons/32x32/{{$soldmarket}}.png" alt="author"  data-toggle="tooltip" title="This investment was deposited then sold to {{$soldmarket}}."></a>
																														</li>
																														</ul>
																												<img src="/assets/logos/{{$investment->currency}}.png" alt="friend" style="max-height:60px;display:block;margin:0 auto;margin-top:20px;width:initial;">
																											</div>

																											<div class="friend-item-content">
																												<div class="friend-avatar">
																													<div class="author-thumb" style="margin-top:30px;height:40px;width:100%;">
																													<div class="author-content">
																														<span class="h3 author-name"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($now_og, 3)}}" data-html="true">{!! $symbol !!}{{number_format($now, $decimal1)}}{!! $symbol2 !!} <span class="{{$color}}" style="font-size:12px;vertical-align:middle;"> @if($investment->market != "Deposit"){{number_format(($now - $then) / $then * 100, 2)}}% @else 100% @endif</span></span>
																														<div class="h6">({{number_format($amount, $decimal4)}} {{$investment->currency}})</div>
																													</div>
																												</div>

																												<div class="swiper-container">
																													<div class="swiper-wrapper">
																														<div class="swiper-slide">
																															<div class="friend-count" data-swiper-parallax="-500" style="margin-top:25px;margin-bottom:0px;">
																																<a href="#" class="friend-count-item">
																																	<div class="h6" style="font-size: 0.875rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($then_og, 3)}}" data-html="true">{!! $symbol !!}{{number_format($then, $decimal2)}}{!! $symbol2 !!}</div>
																																	<div class="title">Then</div>
																																</a>
																																<a href="#" class="friend-count-item">
																																	<div class="h6 positive"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner2 !!} {{number_format($profit, 3)}}" data-html="true"><span class="label label-{{$label}}" style="color:white;">{!! $symbol !!}{{number_format($now - $then, $decimal3)}}{!! $symbol2 !!}</span></div>
																																	<div class="title">Profit</div>
																																</a>
																																<a href="#" class="friend-count-item">
																																	<div class="h6" style="font-size: 0.875rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner2 !!} {{(number_format($now_og, 3))}}" data-html="true">{!! $symbol !!}{{number_format($now, $decimal5)}}{!! $symbol2 !!}</div>
																																	<div class="title">Now</div>
																																</a>
																															</div>

																														</div>

																														<div class="swiper-slide">
																															<div class="friend-count" data-swiper-parallax="-500" style="margin-top:25px;margin-bottom:0px;">
																																<a href="#" class="friend-count-item">
																																	<div class="h6" style="font-size: 0.875rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($bought_at_og, $decimal8)}}" data-html="true">{!! $symbol !!}{{number_format($bought_at, $decimal6)}}{!! $symbol2 !!}</div>
																																	<div class="title">Then</div>
																																</a>
																																@if($decimal7 != 8)
																																	<a href="#" class="friend-count-item">
																																		<div class="h6" style="font-size: 0.875rem;">Mixed</div>
																																		<div class="title">Exchange</div>
																																	</a>
																																@endif
																																<a href="#" class="friend-count-item">
																																	<div class="h6" style="font-size: 0.875rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner2 !!} {{number_format($sold_at_og, $decimal9)}}" data-html="true">{!! $symbol !!}{{number_format($sold_at, $decimal7)}}{!! $symbol2 !!}</div>
																																	<div class="title">Now</div>
																																</a>
																															</div>
																														</div>
																															<div class="swiper-slide">
																																	<table style="width:100%;font-size:10px;height:100px!important;overflow-y:scroll;display:grid;">
																																		<tbody style="margin:0 auto;">
																																		<tr>
																																			<th style="padding:0px 20px 0px 20px">Amount</th>
																																			<th style="padding:0px 20px 0px 20px">Rate</th>
																																			<th style="padding:0px 20px 0px 20px">Exchange</th>
																																		</tr>

																																	@foreach(DB::table('polo_investments')->where([['userid', '=', Auth::user()->id], ['currency', '=', $investment->currency]])->select('amount', 'bought_at')->get() as $i)
																																	<tr>
																																		<td>{{number_format($i->amount,2)}}</td>
																																		<td>{{number_format($i->bought_at, 8)}}</td>
																																		<td>Poloniex</td>
																																	@endforeach
																																	@foreach(DB::table('bittrex_investments')->where([['userid', '=', Auth::user()->id], ['currency', '=', $investment->currency]])->select('amount', 'bought_at')->get() as $i)
																																	<tr>
																																		<td>{{number_format($i->amount,2)}}</td>
																																		<td>{{number_format($i->bought_at, 8)}}</td>
																																		<td>Bittrex</td>
																																	@endforeach
																																	@foreach(DB::table('manual_investments')->where([['userid', '=', Auth::user()->id], ['currency', '=', $investment->currency]])->select('amount', 'bought_at')->get() as $i)
																																	<tr>
																																		<td>{{number_format($i->amount,2)}}</td>
																																		<td>{{number_format($i->bought_at, 8)}}</td>
																																		<td>Manual</td>
																																	@endforeach
																																</tbody>
																																</table>

																															</div>
																													</div>

																													<!-- If we need pagination -->
																													<div class="swiper-pagination"></div>
																												</div>
																											</div>
																										</div>
																									</div>
																							</div>
																						</div>
																						@endif
																				@endforeach
																@endif
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

<div class="modal fade" id="write_note" tabindex="-1" role="dialog" aria-labelledby="write_note">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="card m-0">
				<header class="card-heading p-b-20">
					@if(Auth::user()->avatar == "default.jpg")
				<img src="/assets/img/default.png" alt="" class="img-circle img-sm pull-left m-r-10">
				@else
					<img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="" class="img-circle img-sm pull-left m-r-10">
				@endif
					<h2 class="card-title m-t-5">{{Auth::user()->username}}</h2>
					<ul class="card-actions icons right-top">
						<li>
							<a href="javascript:void(0)" data-dismiss="modal" aria-label="Close">
								<i class="zmdi zmdi-close"></i>
							</a>
						</li>
					</ul>
				</header>
			</div>
			<div class="modal-body p-b-0 bg-white">
				<form class="form-horizontal" action="/investment/NA/note" method="post" id="write-note-form">
																						{{ csrf_field() }}
					<div class="form-group m-0 row is-empty">
						<label class="sr-only">Note: </label>
						<div class="col-md-12 p-0">
							<textarea class="form-control" rows="5" id="edit-comment-field" name="comment" placeholder="Write a note here.."></textarea>
						</div>
					</div>

			</div>
			<div class="modal-footer p-t-0 bg-white">
				<ul class="card-actions icons left-bottom m-b-15">
				</ul>
				<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
				<button type="submit" class="btn btn-primary">Save Note</button>
			</div>
																				</form>
		</div>
		<!-- modal-content -->
	</div>
	<!-- modal-dialog -->
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
                 <form id="form-horizontal" role="form" method="post" action="/investments2/add">
                           {{ csrf_field() }}
                         <div class="form-group is-empty">
                         <label for="" class="control-label">Coin/Currency</label>
                         <input type="text" class="form-control typeahead" id="autocomplete_coins" autocomplete="off" placeholder="Enter a coin" id="coin" name="coin" required/>
                 </div>

								 <div class="row">
									 <div class="col-md-4">
										 <div class="form-group">
												 <label for="" class="control-label">Currency</label>
												 <select class="select form-control" id="priceinputeditcurrency" name="priceinputeditcurrency">
													 <option value="BTC">BTC</option>
													 <option value="USD">USD</option>
													 <option value="EUR">EUR</option>
													 <option value="GBP">GBP</option>
													 <option value="SEK">SEK</option>
													 <option value="NOK">NOK</option>
													 <option value="DKK">DKK</option>
													 <option value="SGD">SGD</option>
													 <option value="CAD">CAD</option>
													 <option value="AUD">AUD</option>
													 <option value="INR">INR</option>
													 <option value="MYR">MYR</option>
												 </select>
									 </div>
								 </div>
									 <div class="col-md-8">
		                 <div class="form-group">
		                     <label for="" class="control-label">Price Input</label>
		                     <select class="select form-control" id="priceinputedit" name="priceinput">
		                       <option value="paidper">Paid per coin</option>
		                       <option value="totalpaid">Total paid</option>
		                     </select>
		               </div>
								 </div>
							 </div>


                 <div class="form-group">
                   <label class="control-label" id="paidinputedit">BTC paid per coin</label>
                   <input type="text" class="form-control" name="bought_at" id="bought_at">
                 </div>

                 <div class="form-group">
                   <label class="control-label">Number of coins bought</label>
                   <input type="text" class="form-control" name="amount" id="amount" required>
                 </div>
                   <div class="form-group is-empty">
                       <label class="control-label">Date when bought</label>
                     <div class="input-group" style="width:100%;">
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


			<div class="modal fade" id="import2_modal" tabindex="-1" role="dialog" aria-labelledby="import2_modal">
						<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">

			<h4 class="modal-title" id="myModalLabel-2">Import Panel</h4>
			<ul class="card-actions icons right-top">
			<li>
				<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
					<i class="zmdi zmdi-close"></i>
				</a>
			</li>
		</ul>
	</div>
	<div class="modal-body">
		<p>From here you can import orders from your connected exchanges, keep in mind that if you are using Bittrex it is recommended to upload the "Full History CSV" log to get trades past 30 days.</p>
					<form id="import-form" role="form" method="post" action="/import/dispatch" enctype="multipart/form-data">
										{{ csrf_field() }}
										<div class="form-group">
												<label for="" class="control-label">Exchange</label>
												<select class="select form-control" id="exchangeinput" name="exchangeinput">
                        	@if(DB::table('keys')->where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Poloniex']])->exists())<option value="Poloniex">Poloniex</option>@endif
                        	@if(DB::table('keys')->where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Bittrex']])->exists())<option value="Bittrex">Bittrex</option>@endif
												</select>
									</div>

					<div id="bittreximport" style="display:none;">
						<div class="form-group is-empty is-fileinput">
							<label class="control-label">CSV Log <i class="fa fa-info-circle" data-toggle="tooltip" title="To import orders older than 30 days you will need to upload your FULL CSV Log, click Load All on bittrex."></i></label>
              <div class="input-group">
                <input type="file" class="form-control" name="csv" placeholder="File Upload...">
                <div class="input-group">
                  <input type="text" readonly="" class="form-control" placeholder="Full CSV Log">
                  <span class="input-group-btn input-group-sm">
                    <button type="button" class="btn btn-info btn-fab btn-fab-sm">
                      <i class="zmdi zmdi-attachment-alt"></i>
                    </button>
                  </span>
                </div>
              </div>
            </div>
					</div>

						<div class="form-group is-empty">
								<label class="control-label">Delete withdrawals <i class="fa fa-info-circle" data-toggle="tooltip" title="Delete investments that has been withdrawn, leave at no if you still want to track them."></i></label>
								<select class="select form-control" id="exchangeinput" name="withdraws">
									<option value="no">No</option>
									<option value="Yes">Yes</option>
								</select>
						</div>
						<button type="submit" id="import-form-button" class="btn btn-primary">Import</button>
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

							<h4 class="modal-title" id="myModalLabel-2">Add a new mining/free asset</h4>
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
                                This is for when you either mined a coin or recieved it for free, adding it to balance and showing as a mined asset.
                              </div>
						<form id="form-horizontal" role="form" method="post" action="/investments/add/mining">
                            {{ csrf_field() }}
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Coin/Currency</label>
                                    <input type="text" class="form-control typeahead" id="autocomplete_coins_mining" autocomplete="off" placeholder="Enter a coin" id="coin" name="coin" required/>
                              </div>
                      <div class="form-group">
                        <label class="control-label">Number of coins mined</label>
                        <input type="text" class="form-control" name="amount" id="amount" required>
                      </div>
                        <div class="form-group is-empty">
                            <label class="control-label">Date Mined</label>
                          <div class="input-group" style="width:100%;">
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
  <script src="/assets/js/investments.js?v=1.4"></script>
<script>

// process the form
$('#import-form').submit(function(event) {
        swal("Import added to queue", "You no longer have to wait for the import to complete, we have added the import to our queue and we will notify you on-site when it is completed!")
		// get the form data
		// there are many ways to get this data using jQuery (you can use the class or id also)
		var formData = {
				'exchangeinput'              : $("#exchangeinput").val(),
				'csv'             : $('input[name=csv]').val(),
				'withdraws'    : $('input[name=withdraws]').val(),
				'_token' : "{{csrf_token()}}"
		};

	var formData = new FormData(this);
		// process the form
		$.ajax({
				type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
				url         : '/import/dispatch', // the url where we want to POST
				data        : formData, // our data object
				dataType    : 'json', // what type of data do we expect back from the server
				encode          : true,
				cache: false,
        contentType: false,
        processData: false
		})
				// using the done promise callback
				.done(function(data) {

						// log data to the console so we can see
						console.log(data);

						// here we will handle errors and validation messages
				});

		// stop the form from submitting the normal way and refreshing the page
		event.preventDefault();
});



$("#priceinputeditcurrency").change(function(){

	var currency = $(this).val();

	var priceinput = $("#priceinputedit").val();

	if(priceinput == "paidper")
	{
		priceinput = "paid per coin";
	} else {
		priceinput = "paid for investment";
	}

	$("#paidinputedit").text(currency + " " + priceinput);

});

$("#priceinputedit").change(function(){

	var currency = $("#priceinputeditcurrency").val();

	var priceinput = $(this).val();

	if(priceinput == "paidper")
	{
		priceinput = "paid per coin";
	} else {
		priceinput = "paid for investment";
	}

	$("#paidinputedit").text(currency + " " + priceinput);

});

</script>

@endsection
