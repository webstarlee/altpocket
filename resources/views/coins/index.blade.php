@extends('layouts.main')

@section('title')
My Investments
@endsection

@section('css')
	<link rel="stylesheet" type="text/css" href="/version2/css/blocks.css?v=1.92">
  <link rel="stylesheet" type="text/css" href="/version2/css/swiper.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
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
.select2-container--bootstrap .select2-selection{ border:none; border-bottom: 1px solid #e6e6e6; box-shadow:none; border-radius:0; background-color:transparent;}
.select2-container--bootstrap .select2-selection--single{ padding:6px 24px 6px 0}
.select2-container--bootstrap.select2-container--focus .select2-selection, .select2-container--bootstrap.select2-container--open .select2-selection{ box-shadow:none; border-color:#e6e6e6;}
.select2-container--bootstrap .select2-selection--multiple .select2-search--inline .select2-search__field{padding: 0;}
.select2-container--bootstrap .select2-selection--multiple .select2-selection__choice{margin: 5px 5px 0 0; border: 1px solid #e6e6e6;}

.card.card-data-tables .mdl-data-table td, .card.card-data-tables .mdl-data-table th {
	padding: 0 40px 12px!important;
}
.ui-block {
	-webkit-transition: border-color 0.5s ease-in-out; /* Saf3.2+, Chrome */
	 -moz-transition: border-color 0.5s ease-in-out; /* FF3.7+ */
		 -o-transition: border-color 0.5s ease-in-out; /* Opera 10.5 */
				transition: border-color 0.5s ease-in-out;
}
.flash {
	border: 1px solid #74e282;
	-webkit-transition: border-color 0.5s ease-in-out; /* Saf3.2+, Chrome */
	 -moz-transition: border-color 0.5s ease-in-out; /* FF3.7+ */
		 -o-transition: border-color 0.5s ease-in-out; /* Opera 10.5 */
				transition: border-color 0.5s ease-in-out;
}
.redflash {
	border: 1px solid #e27474;
	-webkit-transition: border-color 0.5s ease-in-out; /* Saf3.2+, Chrome */
	 -moz-transition: border-color 0.5s ease-in-out; /* FF3.7+ */
		 -o-transition: border-color 0.5s ease-in-out; /* Opera 10.5 */
				transition: border-color 0.5s ease-in-out;
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
  $fiat = Auth::user()->getFiat();
	if($currency != "GBP")
	{
		$symbol2 = Auth::user()->getSymbol();
		$symbol = "";
	} else {
		$symbol2 = "";
		$symbol = Auth::user()->getSymbol();
	}
} else {
  $fiat = 1;
  $symbol = Auth::user()->getSymbol();
  $symbol2 = "";
}



?>
    <div id="content_wrapper">
		<div class="m-subheader ">
						<div class="d-flex align-items-center">
							<div class="mr-auto">
								<h3 class="m-subheader__title ">
									Dashboard
								</h3>
							</div>
							<div>
								<button id="tourmenu" class="btn btn-primary btn-fab fab-menu" data-drawer="open-left" style="border:none!important;">
									<i class="zmdi zmdi-plus"></i>
								</button>
							</div>
						</div>
					</div>
        <div id="content" class="container-fluid">
            <div class="content-body">
							<aside class="drawer-left">
														<div class="card profile-menu">
															<header class="card-heading card-img alt-heading" style="min-height:40px!important;">
																	<a href="javascript:void(0)" class="info" data-profile="open-menu"><span>My Portfolio</span></a>
																</header>
																<div class="card-body">
																	<nav class="submenu accounts" style="display: none;">
																		<ul>
																			<li>
																				<a href="javascript:void(0)" class="font-size-12">
																					<div class="switch" data-name="Main Portfolio"><canvas width="30" height="30"></canvas></div> My Portfolio</a>
																				</li>
																						<li><a href="javascript:void(0)" class="p-b-15"><i class="zmdi zmdi-settings"></i>Manage Portfolios (Soon)</a></li>
																						<li class="divider"></li>
																					</ul>
																				</nav>
																				<nav class="submenu">
																					<ul>
																						<li><a href="javascript:void(0)" data-toggle="modal" data-target="#add_modal"><i class="zmdi zmdi-plus"></i> Add New Investment</a></li>
																						<li><a href="javascript:void(0)" data-toggle="modal" data-target="#balance_modal"><i class="zmdi zmdi-plus"></i> Add New Balance</a></li>
																						<li><a href="javascript:void(0)" data-toggle="modal" data-target="#mining_modal"><i class="zmdi zmdi-plus"></i> Add New Mining</a></li>
																						<li class="divider"></li>
																						<li><a href="javascript:void(0)" data-toggle="modal" data-target="#sell_modal"><i class="zmdi zmdi-money-box"></i> Sell Amount</a></li>
																						<li class="divider"></li>
																						@if(Auth::user()->hasKey())
																							<li><a href="javascript:void(0)" id="import_ui2"><i class="zmdi zmdi-cloud-upload"></i> Import Investments</a></li>
																						@endif
																						<li><a href="javascript:void(0)" id="balances2"><i class="zmdi zmdi-balance-wallet"></i> API Connections</a></li>
																						<li class="divider"></li>
																						<li><a href="/investments/clear/cache"><i class="zmdi zmdi-floppy"></i> Reset Cached Investments</a></li>
																						<li><a href="javascript:void(0)" data-toggle="modal" data-target="#reset_modal"><i class="zmdi zmdi-delete"></i> Reset Data</a></li>
																					</ul>
																					<nav>
																					</div>
																				</div>
																			</aside>
							@if(env('IMPORT_SYSTEM') != "on")
							<div class="alert alert-danger" role="alert">
                <strong>Announcement:</strong> The import system is currently down for maintenance and should be up very soon.
              </div>
							@endif
							@if(env('IMPORT_BITTREX') != "on")
							<div class="alert alert-danger" role="alert">
								<strong>Announcement:</strong> Our Bittrex import is under maintenance, please stand by while we work on some improvements.
							</div>
							@endif
							@if(Auth::user()->summed == 1 && \App\Key::where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Coinbase']])->exists())
								<div class="alert alert-danger" role="alert">
	                <strong>Note:</strong> Condensed Investments does not support Coinbase investments yet, please turn condensed off if you have Coinbase Investments.
	              </div>
							@endif
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
									<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12" >
										<div class="ui-block" id="totalinvestment">
											<div class="ui-block-content">
												<ul class="card-actions icons right-top" style="right:20px;top:0px;">
                            <li class="dropdown">
                              <a href="#" class="set-invested" id="1" data-toggle="modal" data-target="#invested_modal">
                                <i class="fa fa-cogs" style="font-size:12px;"></i>
                              </a>
                            </li>
                          </ul>
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
									<div class="ui-block" id="tournetworth">
										<div class="ui-block-content">
											<ul class="statistics-list-count" style="list-style:none;">
												<li>
													<div class="points">
														<span>
															Net Worth
														</span>
													</div>
												<div class="count-stat">
													@php
														$networth2 = $networth * $multiplier + Auth::user()->getSoldProfit(Auth::user()->getCurrency());
													@endphp
                        	<span style="color:#73c04d;cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Holdings on exchanges and manual investments. (Balances)">{!! $symbol !!}<span id="networth" data-networth="{{$networth2}}">{{number_format($networth2, $decimal)}}</span> {!! $symbol2 !!}</span>
												</div>
												</li>
											</ul>
										</div>
									</div>
								</div>


								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
									<div class="ui-block" id="tourprofit">
										<div class="ui-block-content">
											@php
												$profit = (($networth * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency()) + Auth::user()->getSoldProfit(Auth::user()->getCurrency()));
											@endphp
											<ul class="statistics-list-count" style="list-style:none;">
												<li>
													<div class="points">
														<span>
															@if($profit >= 0) Profit @else Loss @endif
														</span>
													</div>
												<div class="count-stat">
                        <span @if($profit >= 0) style="color:#73c04d;cursor:pointer;" @else style="color:#de6b6b;cursor:pointer;"; @endif  data-placement="top" data-toggle="tooltip" title="Networth substracted with your deposits.">{!! $symbol !!}<span id="profit" data-profit="{{$profit}}">{{number_format($profit, $decimal)}}</span> {!! $symbol2 !!}</span>
												</div>
												</li>
											</ul>
										</div>
									</div>
								</div>

								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
									<div class="ui-block" id="touractiveprofit">
										<div class="ui-block-content">
											@php
												$activeprofit2 = ($activeworth * Auth::user()->getMultiplier() - Auth::user()->getPaid(Auth::user()->currency));
											@endphp
											<ul class="statistics-list-count" style="list-style:none;">
												<li>
													<div class="points">
														<span>
															@if($activeprofit2 >= 0) Active Profit @else Active Loss @endif
														</span>
													</div>
													<div class="count-stat">
													<span @if($activeprofit2 >= 0) style="color:#73c04d;cursor:pointer;" @else style="color:#de6b6b;cursor:pointer;"; @endif  data-placement="top" data-toggle="tooltip" title="Profit made on active investments.">{!! $symbol !!}<span id="activeprofit" data-activeprofit="{{$activeprofit2}}">{{number_format($activeprofit2, $decimal)}}</span> {!! $symbol2 !!}</span>
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
															<ul class="nav nav-tabs nav-justified" style="display:block;">
																@if(Auth::user()->tableview == 1)
																	<li class="active" role="presentation" style="float:none;"><a href="#investments-active-table" data-toggle="tab" aria-expanded="true">Active Investments</a></li>
																	<li class="" role="presentation" style="float:none;"><a href="#investments-sold-table" data-toggle="tab" aria-expanded="true">Sold Investments</a></li>
																@else
																	<li class="active " role="presentation" style="float:none;"><a href="#investments-active" data-toggle="tab" aria-expanded="true">Active Investments</a></li>
																	<li role="presentation"><a href="#investments-sold" data-toggle="tab" aria-expanded="true" id="soldtab">Sold Investments</a></li>
																@endif
																<li role="presentation" style="float:none;"><a href="#balance" data-toggle="tab" aria-expanded="true">Balances</a></li>
															</ul>
														</div>
														<div class="card-body">
															<div class="tab-content">
                                <div class="tab-pane fadeIn" id="balance">
																		@if(count($balances) >= 1)
																		<h2 style="text-align:center">Balances</h2>
                                    <hr>
                                    @endif
                                	<div class="row">
	                                    @foreach($balances as $balance)
	                                      <?php
	                                        $price = Auth::user()->getPrice($balance->currency, 'Balance', $balance->exchange);
	                                        $multiplier = Auth::user()->getMultiplier();
																					$btc_divider = 1;

																					if($balance->config == "static")
																					{

																						if($balance->currency == "USD")
																						{
																							$multiplier = $fiat;
																						}

																						if(Auth::user()->currency == "BTC")
																						{
																							$btc_divider = $btc;
																						}

																					}
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
																										<div class="more">
	                                                          <svg class="olymp-three-dots-icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/version2/icons/icons.svg#olymp-three-dots-icon"></use></svg>
	                                                          <ul class="more-dropdown">
																															@if($balance->exchange == "Manual")
																																<li>
																																	<a href="javascript:void(0)" class="set-balance" id="{{$balance->id}}" data-toggle="modal" data-target="#set_modal">Set</a>
																																</li>
																															@endif
																																<li>
																																	<a href="javascript:void(0)" class="make-investment" data-token="{{$balance->currency}}" data-amount="{{$balance->amount}}" data-toggle="modal" data-target="#make_modal">Make {{$balance->currency}} Investment</a>
																																</li>
																															<li>
																																<a href="/balances/delete/{{$balance->id}}">Remove</a>
																															</li>
	                                                          </ul>
	                                                        </div>
	                                                  </ul>
	                                           </header>
	                                         <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
	                                             <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$balance->currency}}.png" itemprop="thumbnail" alt="Image description">
	                                         </div>
	                                         <div class="card-body">
																						 @if($balance->config == "static")
																							 <h4 class="card-title text-center">{!! $symbol !!}{{number_format((($balance->amount / $btc_divider) * $fiat),2)}}{!! $symbol2 !!}</h4>
																							@else
		                                           <h4 class="card-title text-center">{!! $symbol !!}{{number_format(($balance->amount * $price * $multiplier),2)}}{!! $symbol2 !!}</h4>
																						 @endif
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
																		@if(count($p_investments) == 0)
																			<div class="empty-portfolio" style="text-align:center;">
																			<h1>Your portfolio is currently empty.<h3>
																				<button class="btn btn-green" style="background-color:#4fc5ea" data-toggle="modal" data-target="#add_modal">Add Investment<div class="ripple-container"></div></button> <button style="background-color:#4fc5ea" class="btn btn-green" data-toggle="modal" data-target="#balance_modal">Add Balance<div class="ripple-container"></div> </button> <button class="btn btn-green" @if(\App\Key::where([['userid', '=', Auth::user()->id], ['type', '=', 'Exchange']])->first()) data-toggle="modal" data-target="#import2_modal"  @else data-toggle="modal" data-target="#balances-modal" @endif style="background-color:#4fc5ea">Import Investments<div class="ripple-container"></div> </button>
																			</div>
																		@endif
                                  <div class="row">
																		@php
																			$lastcurrency = "";
																			$profit = 0;
																		@endphp
																		@foreach($p_investments as $investment)
																			@if($investment->date_sold == null)
                                      @php
																					$exchange = $investment->exchange;
																					if($exchange == "Poloniex")
																					{
																						$data = $investment->getData('poloniex', $multiplier, $fiat);
																					} elseif($exchange == "Bittrex")
																					{
																						$data = $investment->getData('bittrex', $multiplier, $fiat);
																					} elseif($exchange == "Coinbase")
																					{
																						$data = $investment->getData('coinbase', $multiplier, $fiat);
																					} else {
																						$data = $investment->getData(Auth::user()->api, $multiplier, $fiat);
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

																					if($data->bought_at > 10)
																					{
																						$decimal6 = 2; // Then (Bought At)
																						$decimal7 = 2; // Now (Price)
																					}
																					if($data->bought_at < 0.1)
																					{
																						$decimal6 = 5; // Then (Bought At)
																						$decimal7 = 5; // Now (Price)
																					}
																					if($data->price_og > 10)
																					{
																						$decimal8 = 3; // Then (Bought At)
																						$decimal9 = 3; // Now (Price)
																					}

	                                        if($exchange == "Manual")
	                                        {
	                                          $name = DB::table('cryptos')->where('symbol', $investment->currency)->first()->name;
	                                        }
                                      @endphp
                                      <div class=" col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <div class="ui-block" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;" id="{{$investment->id}}">
                                                    <div class="friend-item">
                                                      <div class="friend-header-thumb" style="height:100px;">
                                                        @if($investment->exchange != "Manual")
                                                          <ul class="card-actions icons left-top">
                                                            <li>
                                                              <i class="material-icons" style="color:#5ecbf7;cursor:pointer;margin-left:15px;"  data-toggle="tooltip"  title="Verified investment from {{$exchange}}.">verified_user</i>
                                                              </li>
                                                            </ul>
                                                            <ul class="card-actions icons left-top">
                                                              <li>
                                                                <span style="font-size:11px;margin-left:40px;display:table;">{{$investment->date_bought}}</span>
                                                                </li>
                                                              </ul>
                                                          @endif
                                                          <ul class="card-actions icons right-top">
                                                            <li>
                                                              @if($exchange == "Poloniex")
                                                                <a href="https://poloniex.com/exchange#{{$investment->market}}_{{$investment->currency}}" style="margin-right:15px;margin-top:-5px;"><img src="/icons/32x32/{{$investment->market}}.png" alt="author"  data-toggle="tooltip" title="This investment was purchased using {{$investment->market}}."></a>
                                                              @elseif($exchange == "Bittrex")
                                                                <a href="https://bittrex.com/Market/Index?MarketName={{$investment->market}}-{{$investment->currency}}" style="margin-right:15px;margin-top:-5px;"><img src="/icons/32x32/{{$investment->market}}.png" alt="author"  data-toggle="tooltip" title="This investment was purchased using {{$investment->market}}."></a>
                                                              @elseif($exchange == "Coinbase")
                                                                <a href="https://www.coinbase.com/charts" style="margin-right:15px;margin-top:-5px;"><img src="/icons/32x32/{{$investment->market}}.png" alt="author"  data-toggle="tooltip" title="This investment was purchased using {{$investment->market}}."></a>
                                                              @else
                                                                <a href="https://www.worldcoinindex.com/coin/{{$name}}" style="margin-right:15px;margin-top:-5px;"><img src="/icons/32x32/{{$investment->market}}.png" alt="author"  data-toggle="tooltip" title="This investment was purchased using {{$investment->market}}."></a>
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
                                                              @elseif($exchange == "Coinbase")
                                                                <a href="/investments/remove/coinbase/{{$investment->id}}">Block Investment</a>
                                                              @elseif($exchange == "Manual")
                                                                <a href="javascript:void(0)" class="sell-coin" id="{{$investment->id}}" amount="{{$investment->amount}}" data-toggle="modal" data-target="#sold_modal">Sell Investment</a>
                                                                <a href="javascript:void(0)" class="edit-coin" id="{{$investment->id}}" amount="{{$investment->amount}}" bought_at="{{number_format($investment->bought_at, 8, '.', '')}}" date="{{ date('Y-m-d', strtotime($investment->date_bought)) }}"  data-toggle="modal" data-target="#edit_modal">Edit Investment</a>
                                                                <a href="javascript:void(0)" class="deleteinvestment" id="{{$investment->id}}">Delete Investment</a>
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
                                                            <span class="h3 author-name" data-initial-profit="{{($data->amount * $data->price) - ($data->amount * $data->bought_at)}}" data-initial="{{ $data->amount * $data->price }}" data-then="{{$data->amount * $data->bought_at}}" data-investment-id="{{$investment->id}}" data-id="{{$api}}-{{$investment->currency}}" data-amount="{{$data->amount}}"  data-price="{{$data->price_og}}" data-multiplier="{{$multiplier}}" data-pair="{{$investment->market}}_{{$investment->currency}}" data-placement="bottom" data-toggle="tooltip" data-original-title="{!! $data->inner !!} {{ number_format($data->now_og, 3) }}" data-html="true">{!! $symbol !!}<span id="price">{{ number_format($data->amount * $data->price, $decimal1) }}</span>{!! $symbol2 !!} <span class="{{$data->color}}" style="font-size:12px;vertical-align:middle;"> <span id="{{$investment->id}}-percent">{{number_format(($data->now - $data->then) / $data->then * 100, 2)}}</span>%</span></span>
                                                            <div class="h6">({{ number_format($data->amount, $decimal4) }} {{$investment->currency}})</div>
                                                          </div>
                                                        </div>
                                                        @if(Auth::user()->oldinvestments == 1)
                                                          <div class="swiper-container">
                                                            <div class="swiper-wrapper">
                                                              <div class="swiper-slide">
                                                                <span class="text-center label label-{{$label}}" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer"  data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($now_og - $then_og, 3)}}" data-html="true">{!! $symbol !!}{{number_format($now - $then, $decimal3)}}{!! $symbol2 !!}</span>
                                                                <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;cursor:pointer"  data-placement="bottom" data-toggle="tooltip" title="Exchange" data-html="true">{{$investment->exchange}}</span>

                                                                <hr style="margin-top:40px;">
                                                                <div class="usd">
                                                                <span style="float:left;color:#545353;font-weight:600;">Then</span>
                                                                <span style="float:right;color:#545353;font-weight:600;">Now</span>
                                                                <br>
                                                                <span style="float:left;cursor:pointer" data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($then_og, 3)}}" data-html="true">{!! $symbol !!}{{number_format($then, $decimal2)}}{!! $symbol2 !!}</span>
                                                                <span style="float:right;cursor:pointer" data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{(number_format($now_og, 3))}}" data-html="true">{!! $symbol !!}{{number_format($now, $decimal5)}}{!! $symbol2 !!}</span>
                                                                <br>
                                                                <span style="float:left;cursor:pointer" data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($bought_at_og, $decimal8)}}" data-html="true">{!! $symbol !!}{{number_format($bought_at, $decimal6)}}{!! $symbol2 !!}</span>
                                                                <span style="float:right;cursor:pointer" data-placement="bottom" data-toggle="tooltip" title="{!! $inner !!} {{number_format($price_og, $decimal9)}}" data-html="true">{!! $symbol !!}{{number_format($price, $decimal7)}}{!! $symbol2 !!}</span>
                                                                <br>
                                                                </div>
                                                              </div>

                                                              @if($investment->note)
                                                                <div class="swiper-slide">
                                                                  <h4>Investment Note:</h2>
                                                                  <p class="friend-about" data-swiper-parallax="-500" style="text-align:center;">
                                                                    {{$investment->note}}
                                                                  </p>

                                                                </div>
                                                              @endif
                                                            </div>
                                                            <div class="swiper-pagination"></div>
                                                          </div>

                                                        @else
                                                        <div class="swiper-container">
                                                          <div class="swiper-wrapper">
                                                            <div class="swiper-slide">
                                                              <div class="friend-count" data-swiper-parallax="-500">
                                                                <a href="#" class="friend-count-item">
                                                                  <div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $data->inner !!} {{ number_format($data->then_og, 3) }}" data-html="true">{!! $symbol !!}{{ number_format($data->amount * $data->bought_at, $decimal2) }}{!! $symbol2 !!}</div>
                                                                  <div class="title">Then</div>
                                                                </a>
                                                                <a href="#" class="friend-count-item">
                                                                  <div class="h6 positive" style="font-size: 1rem;" data-placement="bottom" data-toggle="tooltip" title="{!! $data->inner !!} {{ number_format($data->profit_og, 3) }}" data-html="true"><span class="label label-{{$data->label}}" style="color:white;">{!! $symbol !!} <span id="{{$investment->id}}-profit">{{ number_format(($data->amount * $data->price) - ($data->amount * $data->bought_at), $decimal3) }}</span>{!! $symbol2 !!}</span></div>
                                                                  <div class="title">Profit</div>
                                                                </a>
                                                                <a href="#" class="friend-count-item">
                                                                  <div class="h6" style="font-size: 1rem;" data-placement="bottom" data-toggle="tooltip" title="{!! $data->inner !!} {{ number_format($data->now_og, 3) }}" data-html="true">{!! $symbol !!}<span id="{{$investment->id}}-now">{{ number_format($data->amount * $data->price, $decimal5) }}</span> {!! $symbol2 !!}</div>
                                                                  <div class="title">Now</div>
                                                                </a>
                                                              </div>
                                                            </div>

                                                            <div class="swiper-slide">
                                                              <div class="friend-count" data-swiper-parallax="-500">
                                                                <a href="#" class="friend-count-item">
                                                                  <div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $data->inner !!} {{ number_format($data->bought_at_og, $decimal8) }}" data-html="true">{!! $symbol !!}{{ number_format($data->bought_at, $decimal6) }}{!! $symbol2 !!}</div>
                                                                  <div class="title">Then</div>
                                                                </a>

                                                                  <a href="#" class="friend-count-item">
                                                                    <div class="h6" style="font-size: 1rem;">{{$exchange}}</div>
                                                                    <div class="title">Exchange</div>
                                                                  </a>

                                                                <a href="#" class="friend-count-item">
                                                                  <div class="h6" style="font-size: 1rem;"  data-placement="bottom" data-toggle="tooltip" title="{!! $data->inner !!} {{ number_format($data->price_og, $decimal9) }}" data-html="true">{!! $symbol !!}<span id="{{$investment->id}}-now-small">{{ number_format($data->price, $decimal7) }}</span>{!! $symbol2 !!}</div>
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
                                                        @endif
                                                      </div>
                                                    </div>
                                                  </div>
                                              </div>

																		@endif
																		@endforeach

                                  </div>
                                </div>

                              		<div class="tab-pane fadeIn" id="investments-sold">
		                                <div class="row">
																			hi
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

<div class="modal fade" id="balance-chart" tabindex="-1" role="dialog" aria-labelledby="balance-chart">
	<div class="modal-dialog" style="width:75%;" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div id="balance-chartdiv" style="height:500px;width:100%"></div>
			</div>
			<!-- modal-content -->
		</div>
		<!-- modal-dialog -->
	</div>
</div>

<div class="modal fade" id="invested_modal" tabindex="-1" role="dialog" aria-labelledby="invested_modal">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel-2">Set Invested</h4>
				<ul class="card-actions icons right-top">
				<li>
					<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
						<i class="zmdi zmdi-close"></i>
					</a>
				</li>
			</ul>
		</div>
		<div class="modal-body">
			<p><strong>Keep in mind that this does override any other invested calculations Altpocket does, you may leave it empty to let us do the calculations for you. Please note that you need to enter the invested in $ USD.</strong></p>
			<form id="set-invested-form" role="form" method="post" action="/invested/set">
								{{ csrf_field() }}
			<div class="form-group">
				<label class="control-label" id="paidinputedit2">Invested in USD ($)</label>
				<input type="text" class="form-control" name="invested" id="invested" value="{{Auth::user()->invested}}">
			</div>
			<button type="submit" class="btn btn-primary">Set Invested</button>
						</form>
			</div>
		</div>
		<!-- modal-content -->
	</div>
	<!-- modal-dialog -->
			 </div>


<div class="modal fade" id="set_price" tabindex="-1" role="dialog" aria-labelledby="set_price">
		<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel-2">Set Price</h4>
				<ul class="card-actions icons right-top">
				<li>
					<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
						<i class="zmdi zmdi-close"></i>
					</a>
				</li>
			</ul>
		</div>
		<div class="modal-body">
			<p>This is for setting a price on an sell order that was deposited then sold, since we do not know the buy price of the deposited tokens, we mark it as a deposit sale, with this form you can set it's buy price but doing so will remove it's verified mark.</p>
			<form id="set-price-form" role="form" method="post" action="/investments/add">
								{{ csrf_field() }}

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
							<label for="" class="control-label">Currency</label>
							<select class="select form-control" id="priceinputeditcurrency2" name="priceinputeditcurrency">
								<option value="BTC">BTC</option>
								<option value="ETH">ETH</option>
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
								<option value="ZAR">ZAR</option>
							</select>
				</div>
			</div>
				<div class="col-md-8">
					<div class="form-group">
							<label for="" class="control-label">Price Input</label>
							<select class="select form-control" id="priceinputedit2" name="priceinput">
								<option value="paidper">Paid per coin</option>
								<option value="totalpaid">Total paid</option>
							</select>
				</div>
			</div>
		</div>


			<div class="form-group">
				<label class="control-label" id="paidinputedit2">BTC paid per coin</label>
				<input type="text" class="form-control" name="bought_at" id="bought_at2">
			</div>
				<div class="form-group is-empty">
						<label class="control-label">Date when bought</label>
					<div class="input-group" style="width:100%;">
						<span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
						<input type="text" class="form-control datepicker" id="md_input_date7" type="date" placeholder="Date when bought" name="date" required>
					</div>
				</div>
								<button type="submit" class="btn btn-primary">Set price</button>
						</form>
			</div>
		</div>
		<!-- modal-content -->
	</div>
	<!-- modal-dialog -->
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
							<textarea class="form-control" rows="5" maxlength="300" id="edit-comment-field" name="comment" placeholder="Write a note here.."></textarea>
						</div>
					</div>

			</div>
			<div class="modal-footer p-t-0 bg-white">
				<ul class="card-actions icons left-bottom m-b-15">
					<li><span id="note_characters">0/300</span></li>
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


			<div class="modal fade" id="edit_modal" role="dialog" aria-labelledby="edit_modal">
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
						<form id="edit-form" role="form" method="post" action="/investments/edit/">
                            {{ csrf_field() }}

														<div class="row">
					 									 <div class="col-md-4">
					 										 <div class="form-group">
					 												 <label for="" class="control-label">Currency</label>
					 												 <select class="select form-control" id="priceinputeditcurrency3" name="priceinputeditcurrency">
					 													 <option value="BTC">BTC</option>
					 													 <option value="ETH">ETH</option>
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
					 													 <option value="ZAR">ZAR</option>
					 												 </select>
					 									 </div>
					 								 </div>
					 									 <div class="col-md-8">
					 		                 <div class="form-group">
					 		                     <label for="" class="control-label">Price Input</label>
					 		                     <select class="select form-control" id="priceinputedit3" name="priceinput">
					 		                       <option value="paidper">Paid per coin</option>
					 		                       <option value="totalpaid">Total paid</option>
					 		                     </select>
					 		               </div>
					 								 </div>
					 							 </div>
												 <div class="form-group">
													 <label class="control-label" id="paidinputedit3">BTC paid per coin</label>
													 <input type="text" class="form-control" name="bought_at" id="bought_at_edit">
												 </div>
												 <div class="form-group">
													 <label class="control-label">Amount</label>
													 <input type="text" class="form-control" name="amount" id="amountedit3">
												 </div>
                            <div class="form-group is-empty">
                                <label class="control-label">Date when bought</label>
                              <div class="input-group" style="width:100%!important;">
                                <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                <input type="text" class="form-control datepicker" id="md_input_date6" type="date" placeholder="Date when bought" name="date" required>
                              </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Edit Investment</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			       </div>

						 <div class="modal fade" id="make_modal" role="dialog" aria-labelledby="make_modal">
			           <div class="modal-dialog" role="document">
			 					<div class="modal-content">
			 						<div class="modal-header">

			 							<h4 class="modal-title" id="make_modal_title">Make Investment</h4>
			 							<ul class="card-actions icons right-top">
			 							<li>
			 								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
			 									<i class="zmdi zmdi-close"></i>
			 								</a>
			 							</li>
			 						</ul>
			 					</div>
			 					<div class="modal-body">
			 						<form id="make-investment-form" role="form" method="post" action="/investments/make/">
			                             {{ csrf_field() }}

			 														<div class="row">
			 					 									 <div class="col-md-4">
			 					 										 <div class="form-group">
			 					 												 <label for="" class="control-label">Price Input</label>
			 					 												 <select class="select form-control" id="priceinputeditcurrency4" name="priceinputeditcurrency">
			 					 													 <option value="BTC">BTC</option>
			 					 													 <option value="ETH">ETH</option>
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
			 					 													 <option value="ZAR">ZAR</option>
			 					 												 </select>
			 					 									 </div>
			 					 								 </div>
			 					 									 <div class="col-md-8">
			 					 		                 <div class="form-group">
			 					 		                     <label for="" class="control-label">Price Input 2</label>
			 					 		                     <select class="select form-control" id="priceinputedit4" name="priceinput">
			 					 		                       <option value="paidper">Paid per coin</option>
			 					 		                       <option value="totalpaid">Total paid</option>
			 					 		                     </select>
			 					 		               </div>
			 					 								 </div>
			 					 							 </div>
			 												 <div class="form-group">
			 													 <label class="control-label" id="paidinputedit4">BTC paid per coin</label>
			 													 <input type="text" class="form-control" name="bought_at" id="bought_at_edit">
			 												 </div>
			 												 <div class="form-group">
			 													 <label class="control-label">Amount</label>
			 													 <input type="text" class="form-control" name="amount" id="amountedit4">
			 												 </div>
			                             <div class="form-group is-empty">
			                                 <label class="control-label">Date when bought</label>
			                               <div class="input-group" style="width:100%!important;">
			                                 <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
			                                 <input type="text" class="form-control datepicker" id="md_input_date8" type="date" placeholder="Date when bought" name="date" required>
			                               </div>
			                             </div>
			                             <button type="submit" class="btn btn-primary">Make Investment</button>
			                         </form>
			 						</div>
			 					</div>
			 					<!-- modal-content -->
			 				</div>
			 				<!-- modal-dialog -->
			 			       </div>


		<div class="modal fade" id="balance_modal" role="dialog" aria-labelledby="balance_modal">
	<div class="modal-dialog" role="document">
<div class="modal-content">
	<div class="modal-header">

		<h4 class="modal-title" id="myModalLabel-2">Add a new balance/Add to balance</h4>
		<ul class="card-actions icons right-top">
		<li>
			<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
				<i class="zmdi zmdi-close"></i>
			</a>
		</li>
	</ul>
</div>
<div class="modal-body">
	<p>This is for adding a manual balance, this may be useful if you are tracking investments on a exchange and you have sent the balances to an off-site wallet or else. If you already have a balance it will add to it, you may also set a balance from the balance tab.</p>
				<form id="form-horizontal" role="form" method="post" action="/investments/addbalance">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-md-12">
							<div class="form-group is-empty">
							<label for="" class="control-label">Coin/Token</label>
							<select class="select-with-search form-control js-data-example-ajax" style="width:100%!important;" name="crypto">
								<option value="" selected="selected">Select a Coin/Token</option>
							</select>
						 </div>
					 </div>
					</div>

				<div class="form-group">
					<label class="control-label">Amount</label>
					<input type="text" class="form-control" name="amount" id="amount" required>
				</div>
									<button type="submit" class="btn btn-primary">Add Balance</button>
							</form>
	</div>
</div>
<!-- modal-content -->
</div>
<!-- modal-dialog -->
</div>

<div class="modal fade" id="set_modal" tabindex="-1" role="dialog" aria-labelledby="set_modal">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">

<h4 class="modal-title" id="myModalLabel-2">Set Balance</h4>
<ul class="card-actions icons right-top">
<li>
	<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
		<i class="zmdi zmdi-close"></i>
	</a>
</li>
</ul>
</div>
<div class="modal-body">
<p>This is for setting the amount of an manual balance.</p>
		<form id="set-balance-form" role="form" method="post" action="/balances/set/">
			{{ csrf_field() }}
		<div class="form-group">
			<label class="control-label">Amount</label>
			<input type="text" class="form-control" name="amount" id="amount" required>
		</div>
							<button type="submit" class="btn btn-primary">Set Balance</button>
					</form>
</div>
</div>
<!-- modal-content -->
</div>
<!-- modal-dialog -->
</div>

             <div class="modal fade" id="add_modal" role="dialog" aria-labelledby="basic_modal">
           <div class="modal-dialog" role="document">
         <div class="modal-content">
           <div class="modal-header" style="background-color:#4fc5ea">

             <h4 class="modal-title" id="myModalLabel-2" style="color:white;">Add a new investment</h4>
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


								 <div class="row">
									 <div class="col-md-12">
										 <div class="form-group is-empty">
										 <label for="" class="control-label">Coin/Token</label>
										 <select class="select-with-search form-control js-data-example-ajax" style="width:100%!important;" name="crypto">
											 <option value="" selected="selected">Select a Coin/Token</option>
										 </select>
										</div>
									</div>
								 </div>

								 <div class="row">
									 <div class="col-md-12">
										 <div class="form-group">
												 <label for="" class="control-label">Market</label>
												 <select class="select form-control" id="market" name="market">
													 <option value="BTC">BTC</option>
													 <option value="ETH">ETH</option>
													 <option value="USDT">USDT</option>
													 <option value="EUR">EUR</option>
													 <option value="USD">USD</option>
													 <option value="CAD">CAD</option>
													 <option value="AUD">AUD</option>
												 </select>
									 </div>
								 </div>
								 </div>

								 <div class="row">
									 <div class="col-md-4">
										 <div class="form-group">
		                     <label for="" class="control-label">Price Input</label>
												 <select class="select form-control" id="priceinputeditcurrency" name="priceinputeditcurrency">
													 <option value="BTC">BTC</option>
													 <option value="ETH">ETH</option>
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
													 <option value="ZAR">ZAR</option>
												 </select>
									 </div>
								 </div>
									 <div class="col-md-8">
		                 <div class="form-group">
		                     <label for="" class="control-label"></label>
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
													@if(env('IMPORT_POLONIEX') == "on")
                        		@if(Auth::user()->hasExchange('poloniex'))<option value="Poloniex">Poloniex</option>@endif
													@endif
													@if(env('IMPORT_BITTREX') == "on" || Auth::user()->isFounder())
                        		@if(Auth::user()->hasExchange('bittrex'))<option value="Bittrex">Bittrex</option>@endif
													@endif
													@if(env('IMPORT_COINBASE') == "on")
														@if(Auth::user()->hasExchange('coinbase'))<option value="Coinbase">Coinbase</option>@endif
													@endif
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




  			<div class="modal fade" id="sold_modal" role="dialog" aria-labelledby="sold_modal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">

							<h4 class="modal-title" id="myModalLabel-2">Sell Investment</h4>
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

														<div class="row">
					 									 <div class="col-md-4">
					 										 <div class="form-group">
					 												 <label for="" class="control-label">Currency</label>
					 												 <select class="select form-control" id="priceinputsellcurrency" name="priceinputeditcurrency">
					 													 <option value="BTC">BTC</option>
					 													 <option value="ETH">ETH</option>
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
					 													 <option value="ZAR">ZAR</option>
					 												 </select>
					 									 </div>
					 								 </div>
					 									 <div class="col-md-8">
					 		                 <div class="form-group">
					 		                     <label for="" class="control-label">Price Input</label>
					 		                     <select class="select form-control" id="priceinputsell" name="priceinput">
					 		                       <option value="paidper">Paid per coin</option>
					 		                       <option value="totalpaid">Total paid</option>
					 		                     </select>
					 		               </div>
					 								 </div>
					 							 </div>
												 <div class="form-group">
													 <label class="control-label" id="paidinputsell">BTC paid per coin</label>
													 <input type="text" class="form-control" name="bought_at" id="bought_at">
												 </div>
												 <div class="form-group">
													 <label class="control-label">Amount Sold</label>
													 <input type="text" class="form-control" name="amount" id="amountsell">
												 </div>
                            <div class="form-group is-empty">
                                <label class="control-label">Date when sold</label>
                              <div class="input-group" style="width:100%!important;">
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
                            <a href="javascript:void(0)" id="reset-data-bittrex"><img src="/img/logos/bittrex.png"/></a>
                          </div>
                        </div>
												<div class="row">
													<div class="col-md-6">
														<a href="javascript:void(0)" id="reset-data-coinbase"><img src="https://www.coinbase.com/assets/press/coinbase-logos/coinbase.png"/></a>
													</div>
												</div>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>

			<div class="modal fade" id="mining_modal" role="dialog" aria-labelledby="mining_modal">
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
														<div class="row">
					 									 <div class="col-md-12">
					 										 <div class="form-group is-empty">
					 										 <label for="" class="control-label">Coin/Token</label>
					 										 <select class="select-with-search form-control js-data-example-ajax" style="width:100%!important;" name="crypto">
					 											 <option value="" selected="selected">Select a Coin/Token</option>
					 										 </select>
					 										</div>
					 									</div>
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



      <div class="modal fade" id="sell_modal" role="dialog" aria-labelledby="sell_modal">
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
														<div class="row">
					 									 <div class="col-md-12">
					 										 <div class="form-group is-empty">
					 										 <label for="" class="control-label">Coin/Token</label>
					 										 <select class="select-with-search form-control js-data-example-ajax" style="width:100%!important;" name="crypto">
					 											 <option value="" selected="selected">Select a Coin/Token</option>
					 										 </select>
					 										</div>
					 									</div>
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
                              <div class="input-group" style="width:100%">
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



@endsection



@section('js')
	<script src="https://www.amcharts.com/lib/3/plugins/export/examples/export.config.default.js"></script>
@if(Auth::user()->username == "Edwin")
	<script>



        $(".show-chart").click(function(){

            getChart($(this).data("user"),$(this).data("coin"),$(this).data("type"));
        });
        function getChart(user,coin,type) {
            $.ajax({
                type: 'GET',
                url: "http://95.85.51.208/user/"+user+"/chart/"+coin+"/"+type,
                context: document.body,
                global: false,
                async:true,
                success: function(data) {
                    if(data.error == undefined) {
                        priceData = [];
                        Object.keys(data).forEach(function(key) {
                            data[key].USD= convert(data[key].USD);
                            priceData.push(data[key]);
                        });
						$("#show-chart").modal("toggle");
                        if(coin == "BTC"){
                            <?php
                                if(Auth::user()){
                                    if(Auth::user()->currency != "BTC"){
                                        echo 'chart.graphs[0].valueField = "USD";';
                                        echo 'chart.graphs[0].balloonText = "<div style=\'margin:5px; font-size:19px;\'>Price:<b>[[value]]</b> '.Auth::user()->currency.'<br>[[description]]</div>";';
                                    }else{
                                        echo 'chart.graphs[0].valueField = "USD";';
                                        echo 'chart.graphs[0].balloonText = "<div style=\'margin:5px; font-size:19px;\'>Price:<b>[[value]]</b> USD <br>[[description]]</div>";';
                                    }
                                }
                                ?>
                                chart.numberFormatter = {
                                precision:2,decimalSeparator:".",thousandsSeparator:","
                            };
                        }else {
                            chart.numberFormatter = {
                                precision: 8, decimalSeparator: ".", thousandsSeparator: ","
                            };
                        }
                        chart.dataProvider = priceData;


                        chart.validateData();
                        setTimeout(function () {
                            $(".modal-body").css("padding", "0px");
                            $(".modal-body").css("padding", "20px");
                            $(".modal-body").css("padding", "0px");

                        }, 1000);
                    }else{
                        alert(data.error);
					}
                }
            });
        }
        function dateDiffInDays(a, b) {
            var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
            var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());
            return Math.floor((utc2 - utc1) / (1000 * 60 * 60 * 24) );
        }
        Date.prototype.addDays = function(days) {
            var dat = new Date(this.valueOf());
            dat.setDate(dat.getDate() + days);
            return dat;
        }
        function exportChart() {
           balance_chart.export.capture({}, function() {
                this.toPNG({}, function(base64) {
                  console.log(base64);
                });
            });
        }
        AmCharts.exportCFG.menu[0].menu.push({
            "label": "Share",
            "click": function() {
                exportChart();
            }
        });
        function getBalanceChart(user) {
            $.ajax({
                type: 'GET',
                url: "/user/"+user+"/history",
                context: document.body,
                global: false,
                async:true,
                success: function(data) {
                    if(data.error == undefined) {
                        $("#balance-chart").modal("toggle");
                        balchartData = [];
                        Object.keys(data).forEach(function(key) {
                            data[key].USD= convert(data[key].USD);
                            balchartData.push(data[key]);
                        });
                        balance_chart.dataProvider = balchartData;
                        balance_chart.numberFormatter = {
                            precision:8,decimalSeparator:".",thousandsSeparator:","
                        };
                        balance_chart.validateData();
                    }else{
                        alert(data.error);
                    }
                }
            });
        }

		function convert(a){
			<?php
            if(Auth::user()->currency != "BTC" && Auth::user()->currency!='USD'){
            $chartmulti = App\Multiplier::where('currency', Auth::user()->currency)->first()->price;
			echo "return a*".$chartmulti;
            }else{
                echo "return a";
			}
            ?>
		}
        var balance_chart = AmCharts.makeChart("balance-chartdiv", {
            "type": "serial",
            "theme": "dark",
            "marginRight": 80,
            "valueAxes": [{
                "position": "left",
                "title": "Balance",
            }],
            "graphs": [{
                "bulletSize": 14,
                "id": "g1",
                "fillAlphas": 0.4,
				"bulletField": "bullet",
                "descriptionField": "desc",
                "colorField": "bulletColor",
				<?php
				if(Auth::user()->currency != "BTC"){
                echo '"valueField": "USD",
                		  "balloonText": "<div style=\'margin:5px; font-size:19px;\'>Balance:<b>[[value]]</b> '.Auth::user()->currency.' <br>[[description]]</div>"';
				}else{
                echo '"valueField": "BTC",
                      "balloonText": "<div style=\'margin:5px; font-size:19px;\'>Balance:<b>[[value]]</b> BTC <br>[[description]]</div>"';
          		}
				?>
            }],
            "chartScrollbar": {
                "graph": "g1",
                "scrollbarHeight": 80,
                "backgroundAlpha": 0,
                "selectedBackgroundAlpha": 0.1,
                "selectedBackgroundColor": "#888888",
                "graphFillAlpha": 0,
                "graphLineAlpha": 0.5,
                "selectedGraphFillAlpha": 0,
                "selectedGraphLineAlpha": 1,
                "autoGridCount": true,
                "color": "#AAAAAA"
            },
            "chartCursor": {
                "categoryBalloonDateFormat": "JJ:NN, DD MMMM",
                "cursorPosition": "mouse"
            },
            "categoryField": "TimeStamp",
            "categoryAxis": {
                "minPeriod": "mm",
                "parseDates": true
            },
            "export": AmCharts.exportCFG

        });
        balance_chart.addListener("dataUpdated", zoomChart);

        var chart = AmCharts.makeChart("chartdiv", {
            "type": "serial",
            "theme": "dark",
            "marginRight": 80,
            "valueAxes": [{
                "position": "left",
                "title": "Price"
            }],
            "graphs": [{
                "bulletSize": 14,
                "bulletField": "bullet",
                "descriptionField": "desc",
                "colorField": "bulletColor",
                "id": "g1",
                "fillAlphas": 0.4,
                "valueField": "Price",
                <?php
                    if(Auth::user()->currency != "BTC"){
                        echo '"valueField": "USD",
                		  "balloonText": "<div style=\'margin:5px; font-size:19px;\'>Price:<b>[[value]]</b> '.Auth::user()->currency.'<br>[[description]]</div>"';
                    }else{
                        echo '"valueField": "BTC",
                      "balloonText": "<div style=\'margin:5px; font-size:19px;\'>Price:<b>[[value]]</b> BTC<br>[[description]]</div>"';
                    }
                    ?>
            }],
            "chartScrollbar": {
                "graph": "g1",
                "scrollbarHeight": 80,
                "backgroundAlpha": 0,
                "selectedBackgroundAlpha": 0.1,
                "selectedBackgroundColor": "#888888",
                "graphFillAlpha": 0,
                "graphLineAlpha": 0.5,
                "selectedGraphFillAlpha": 0,
                "selectedGraphLineAlpha": 1,
                "autoGridCount": true,
                "color": "#AAAAAA"
            },
            "chartCursor": {
                "categoryBalloonDateFormat": "JJ:NN, DD MMMM",
                "cursorPosition": "mouse"
            },
            "categoryField": "TimeStamp",
            "categoryAxis": {
                "minPeriod": "mm",
                "parseDates": true
            },
            "export": {
                "enabled": true,
                "dateFormat": "YYYY-MM-DD HH:NN:SS"
            }
        });
        chart.addListener("dataUpdated", zoomChart);
        function zoomChart() {
            if(chart.dataProvider!=undefined) {
                chart.graphs[0].lineColor = "#4FC5EA";
                chart.zoomToIndexes(chart.dataProvider.length - 250, chart.dataProvider.length - 1);
            }
            if(balance_chart.dataProvider!=undefined) {
                balance_chart.graphs[0].lineColor = "#4FC5EA";
                balance_chart.zoomToIndexes(balance_chart.dataProvider.length - 250, balance_chart.dataProvider.length - 1);
            }

        }

	</script>
@endif


<script src="/version2/js/swiper.jquery.min.js"></script>
  <script src="/js/farbtastic.js"></script>
  <script src="/assets/js/investments.js?v=3.9"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.js"></script>


<script>




if($("#exchangeinput").val() == "Bittrex")
{
	$("#bittreximport").show();
} else {
	$("#bittreximport").hide();
}



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

function formatRepo (repo) {
	if (repo.loading) return repo.name;

	var markup = "<div class='select2-result-repository clearfix' style='font-size:12px;'>" +
		"<div class='select2-result-repository__avatar'><img style='width:24px;' src='https://altpocket.io/icons/32x32/" + repo.symbol + ".png' /> " + repo.name + " (" + repo.symbol + ")</div>";



	return markup;
}
function formatRepoSelection (repo) {
	return repo.name || repo.text;
}
$.fn.select2.defaults.set( "theme", "bootstrap" );
$(".js-data-example-ajax").select2({
  ajax: {
    url: "/api/coins2/",
    dataType: 'json',
    delay: 250,
		theme: "bootstrap",
    data: function (params) {
      return {
        q: params.term, // search term
        page: params.page
      };
    },
    processResults: function (data, params) {
      // parse the results into the format expected by Select2
      // since we are using custom formatting functions we do not need to
      // alter the remote JSON data, except to indicate that infinite
      // scrolling can be used
      params.page = params.page || 1;
      return {
        results: data.tokens,
        pagination: {
          more: (params.page * 30) < data.total_count
        }
      };
    }
  },
  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  minimumInputLength: 1,
  templateResult: formatRepo, // omitted for brevity, see the source of this page
	templateSelection: formatRepoSelection
});




@if (session()->has('import'))
	$("#import2_modal").modal('toggle');
@endif




</script>

@endsection
