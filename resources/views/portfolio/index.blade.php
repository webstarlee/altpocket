@extends('layouts.admin')

@php
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
@endphp

@section('css')
<style>
@media (max-width: 768px) {
  .m-portlet.m-portlet--tabs .m-portlet__head {
    display: block;
    height: auto;
    padding-top: 0.50rem;
    padding-bottom: 0rem;
    }
    .m-portlet.m-portlet--tabs .m-portlet__head .m-portlet__head-tools .m-tabs-line {
      height: 50px;
    }
  }

</style>

@endsection


@section('content')
<div id="content_wrapper">
  <div id="content" class="container-fluid">
    <div class="m-subheader ">
      <div class="d-flex align-items-center">
        <div class="mr-auto">
          <h3 class="m-subheader__title ">

          </h3>
        </div>
        <div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <div class="row">
      <div class="col-xl-12">
  								<!--begin:: Widgets/Product Sales-->
  								<div class="m-portlet m-portlet--bordered-semi m-portlet--space">
  									<div class="m-portlet__head">
  										<div class="m-portlet__head-caption">
  											<div class="m-portlet__head-title">
  												<h3 class="m-portlet__head-text">
  													Main Portfolio
  													<span class="m-portlet__head-desc">
  														Your Portfolio Description
  													</span>
  												</h3>
  											</div>
  										</div>
  										<div class="m-portlet__head-tools">
  											<ul class="m-portlet__nav">
  												<li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
  													<a href="#" class="m-portlet__nav-link m-dropdown__toggle dropdown-toggle btn btn--sm m-btn--pill btn-secondary m-btn m-btn--label-brand">
  														Main Portfolio
  													</a>
  													<div class="m-dropdown__wrapper">
  														<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 49px;"></span>
  														<div class="m-dropdown__inner">
  															<div class="m-dropdown__body">
  																<div class="m-dropdown__content">
  																	<ul class="m-nav">
                                      <li class="m-nav__item">
                                        <a href="javascript:void(0)" class="m-nav__link" data-toggle="modal" data-target="#m_modal_1">
                                          <i class="m-nav__link-icon flaticon-add"></i>
                                          <span class="m-nav__link-text">
                                            New Transaction
                                          </span>
                                        </a>
                                      </li>
                                      <li class="m-nav__item">
                                        <a href="javascript:void(0)" class="m-nav__link toggle-btc" >
                                          <i class="m-nav__link-icon la la-btc"></i>
                                          <span class="m-nav__link-text">
                                            Toggle Bitcoin Prices
                                          </span>
                                        </a>
                                      </li>
                                      <li class="m-nav__item">
                                        <a href="javascript:void(0)" class="m-nav__link toggle-zero" >
                                          <i class="m-nav__link-icon la la-archive"></i>
                                          <span class="m-nav__link-text">
                                            Toggle Zero Holdings
                                          </span>
                                        </a>
                                      </li>
                                      <li class="m-nav__item">
  																			<a href="" class="m-nav__link">
  																				<i class="m-nav__link-icon flaticon-share"></i>
  																				<span class="m-nav__link-text">
  																					Create New Portfolio
  																				</span>
  																			</a>
  																		</li>
                                      <li class="m-nav__item">
                                        <a href="/portfolio/clear" class="m-nav__link">
                                          <i class="m-nav__link-icon la la-trash"></i>
                                          <span class="m-nav__link-text">
                                            Clear Portfolio
                                          </span>
                                        </a>
                                      </li>
  																	</ul>
  																</div>
  															</div>
  														</div>
  													</div>
  												</li>
  											</ul>
  										</div>
  									</div>
                    @if(count($holdings) == 0)
  									<div class="m-portlet__body">
  										<div class="m-widget25" style="text-align:center;">
  											<span class="m-widget25__price m--font-brand">
  												Your Portfolio is empty!
  											</span><br>
  											<span class="m-widget25__desc">
  												Start tracking today by adding transactions or importing from an exchange.
  											</span>
  											<div class="m-widget25--progress">
                          <span class="m-widget25__desc" style="display:block;margin-bottom:15px;margin-top:-25px;">Click the button below to start building your portfolio.</span>
                              <a href="javascript:void(0)" data-toggle="modal" data-target="#m_modal_1" style="color:white;" class="btn btn-brand m-btn m-btn--icon btn-lg m-btn--icon-only  m-btn--pill m-btn--air">
															<i class="flaticon-add"></i>
														</a>
  											</div>
  										</div>
  									</div>
                  @else
                    <div class="m-portlet__body">
										<div class="m-widget25" style="text-align:center;">
                      <div class="row">
                        @if(Auth::user()->algorithm == 2)
                          <div class="col-md-4">
                            <div class="m-portlet m-portlet--border-bottom-info ">
                              <a href="javascript:void(0)" class="m-portlet__nav-link m-portlet__nav-link--icon" style="text-decoration:none;color:#afb0c7;float:right;padding:5px;" data-toggle="modal" data-target="#invested_modal">
                                <i class="la la-cog"></i>
                              </a>
                              <div class="m-portlet__body">
                                <div class="m-widget26">
                                  <div class="m-widget26__number price-toggle">
                                    {{$symbol}}{{number_format(Auth::user()->getPortfolioInvested(1, $currency), 2)}}{{$symbol2}}
                                    <small>
                                      Invested
                                    </small>
                                  </div>
                                  <div class="m-widget26__number price-toggle" style="display:none;">
                                    <i class="la la-btc" style="display:inline;font-size:35px;"></i>{{number_format(Auth::user()->getPortfolioInvested(1, 'BTC'), 8)}}
                                    <small>
                                      Invested
                                    </small>
                                  </div>
                                  <!--
                                  <div class="m-widget26__chart" style="height:90px; width: 220px;margin: 0 auto;">
                                    <canvas id="portfolio_chart"></canvas>
                                  </div>
                                -->
                                </div>
                              </div>
                            </div>
                          </div>
                        @else
                          <div class="col-md-2">
                          </div>
                        @endif
                        <div class="col-md-4">
                          <div class="m-portlet m-portlet--border-bottom-accent ">
      											<div class="m-portlet__body">
      												<div class="m-widget26">
                                <div class="m-widget26__number price-toggle">
      														{{$symbol}}{{number_format(Auth::user()->getValue(1, $currency), 2)}}{{$symbol2}}
      														<small>
      															Portfolio Value
      														</small>
      													</div>
                                <div class="m-widget26__number price-toggle" style="display:none;">
                                  <i class="la la-btc" style="display:inline;font-size:35px;"></i>{{number_format(Auth::user()->getValue(1, 'BTC'), 8)}}
                                  <small>
                                    Portfolio Value
                                  </small>
                                </div>
                                <!--
                                <div class="m-widget26__chart" style="height:90px; width: 220px;margin: 0 auto;">
                                  <canvas id="portfolio_chart"></canvas>
                                </div>
                              -->
      												</div>
      											</div>
      										</div>
                        </div>
                        @php
                          $profit = Auth::user()->getPortfolioProfit(1, $currency);
                        @endphp
                          <div class="col-md-4">
                            <div class="m-portlet @if($profit < 0) m-portlet--border-bottom-danger @else m-portlet--border-bottom-success @endif">
                              <a href="javascript:void(0)" class="m-portlet__nav-link m-portlet__nav-link--icon" style="text-decoration:none;color:#afb0c7;float:right;padding:5px;" data-toggle="modal" data-target="#profit_modal">
    														<i class="la la-cog"></i>
    													</a>
        											<div class="m-portlet__body">
        												<div class="m-widget26">
                                  <div class="m-widget26__number price-toggle">
        														{{$symbol}}{{number_format($profit, 2)}}{{$symbol2}}
        														<small>
        															Profit
        														</small>
        													</div>
                                  <div class="m-widget26__number price-toggle btc-field" style="display:none;">
                                    <i class="la la-btc" style="display:inline;font-size:35px;"></i>{{number_format(Auth::user()->getPortfolioProfit(1, 'BTC'), 8)}}
                                    <small>
                                      Profit
                                    </small>
                                  </div>
                                <!--
                                  <div class="m-widget26__chart" style="height:90px; width: 220px;margin: 0 auto;">
                                    <canvas id="profit_chart"></canvas>
                                  </div>
                                -->
        												</div>
        											</div>
        										</div>
                          </div>
                    </div>
										</div>
									</div>
                @endif
  								</div>
  								<!--end:: Widgets/Product Sales-->
  							</div>
      </div>

      <div class="row">
        <div class="col-xl-12">

        <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs-line m-tabs-line--brand m-tabs-line--2x" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#holding_tabs" role="tab" aria-expanded="true">
                                <i class="la la-archive"></i> Holding
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link" data-toggle="tab" href="#statistics_tab" role="tab" aria-expanded="false">
                                <i class="la la-area-chart"></i> Statistics
                            </a>
                        </li>
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link" data-toggle="tab" href="#watchlist" role="tab" aria-expanded="false">
                                <i class="la la-bars"></i>Watchlist
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="m-portlet__body">
                <div class="tab-content">
                    <div class="tab-pane active" id="holding_tabs" role="tabpanel">
                      <div class="row" style="text-align:initial!important;">
                        @php
                          $array = array();
                          $profit = 0;
                          $profit_fiat = 0;
                          $amount = 0;
                          $paid = 0;
                          $paid_fiat = 0;
                          $last_token = "";
                          $worth = 0;
                        @endphp
                      @foreach($holdings as $holding)
                        @php
                          if($holding->token != $last_token)
                          {
                            $profit = 0;
                            $profit_fiat = 0;
                            $amount = 0;
                            $paid = 0;
                            $paid_fiat = 0;
                            $worth = 0;
                            $last_token = "";
                          }
                          if($holding->market == "ETH")
                          {
                            $multiplier = $eth;
                          } else {
                            $multiplier = $btc;
                          }
                          $profit += $holding->getProfit(Auth::user()->api, 'BTC', $multiplier, $fiat);
                          $profit_fiat += $holding->getProfit(Auth::user()->api, Auth::user()->currency, $multiplier, $fiat);
                          $amount += $holding->amount;
                          $paid += $holding->getValue(Auth::user()->api, 'BTC', $multiplier, $fiat);
                          $paid_fiat += $holding->getValue(Auth::user()->api, Auth::user()->currency, $multiplier, $fiat);
                          $array = array_set($array, $holding->token, array('profit' => $profit, 'profit_fiat' => $profit_fiat, 'amount' => $amount, 'paid_fiat' => $paid_fiat, 'paid' => $paid, 'name' => $holding->name, 'token' => $holding->token, 'tokenid' => $holding->tokenid));
                          $last_token = $holding->token;

                        @endphp
                      @endforeach

                      @foreach($array as $holding)
                        <div class="col-md-4 holding @if($holding['amount'] <= 0) zero @endif" data-token="{{$holding['token']}}">
                        <div class="m-portlet m-portlet--bordered-semi" style="box-shadow:0px 1px 15px 1px rgba(113, 113, 113, 0.31)!important;">
                          <div class="m-portlet__body" style="padding:1rem!important;">
                            <div class="m-widget4">
                              <div class="m-widget4__item">
                                <div class="m-widget4__img m-widget4__img--logo">
                                  <img src="/icons/32x32/{{$holding['token']}}.png" alt="">
                                </div>
                                <div class="m-widget4__info">
                                  <span class="m-widget4__title">
                                    {{$holding['name']}}
                                  </span>
                                  <br>
                                  <span class="m-widget4__sub price-toggle">
                                    <span style="font-weight:700">{{number_format($holding['amount'], 4)}} {{$holding['token']}}</span> (<span style="font-size:12px;font-weight:600;">{{$symbol}}</span>{{number_format($holding['paid_fiat'], 2)}}<span style="font-size:12px;font-weight:600;">{{$symbol2}}</span>)
                                  </span>
                                  <span class="m-widget4__sub price-toggle" style="display:none;">
                                    <span style="font-weight:700">{{number_format($holding['amount'], 4)}} {{$holding['token']}}</span> <span class="btc-buy">(<i style="font-size:12px;font-weight:600;" class="la la-btc"></i>{{number_format($holding['paid'], 8)}} )</span>
                                  </span>
                                </div>
                                @if($holding['tokenid'] != 0)
                                <span class="m-widget4__ext">
                                  <span class="m-widget4__number @if($holding['profit_fiat'] >= 0) m--font-brand @else m--font-danger @endif price-toggle">
                                    {{$symbol}}{{number_format($holding['profit_fiat'], 2)}}{{$symbol2}}
                                  </span>
                                  <span class="m-widget4__number @if($holding['profit'] >= 0) m--font-brand @else m--font-danger @endif price-toggle" style="display:none;">
                                    <i class="la la-btc" style="display:inline;"></i>{{number_format($holding['profit'], 8)}}
                                  </span>
                                </span>
                              @endif
                              </div>
                            </div>
                          </div>
                        </div>
                        </div>
                      @endforeach
                    </div>
                    </div>
                    <div class="tab-pane" id="statistics_tab" role="tabpanel">
                        It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                    </div>
                    <div class="tab-pane" id="watchlist" role="tabpanel">
                      <div class="m-widget11">
                        <div class="m-widget11__action m--align-right">
                          <button type="button" class="btn m-btn--pill btn-secondary m-btn m-btn--custom m-btn--hover-brand" data-toggle="modal" data-target="#m_modal_watchlist">
                            Add to Watchlist
                          </button>
                        </div>
													<div class="table-responsive">
														<!--begin::Table-->
														<table class="table">
															<!--begin::Thead-->
															<thead>
																<tr>
																	<td class="m-widget11__label">
																		Symbol
																	</td>
																	<td class="m-widget11__app">
																		Token
																	</td>
																	<td class="m-widget11__sales">
																		Market Cap
																	</td>
																	<td class="m-widget11__change">
																		Change
																	</td>
																	<td class="m-widget11__price">
																		Price
																	</td>
                                  <td class="m-widget11__total m--align-right">
																		Change
																	</td>
                                  <td class="m-widget11__total m--align-right">
                                    Actions
                                  </td>
																</tr>
															</thead>
															<!--end::Thead-->
							<!--begin::Tbody-->
															<tbody>
                                @foreach($watchlists as $watchlist)
                                  @php
                                    $data = $watchlist->getData();
                                  @endphp
																<tr>
																	<td>
                                    <span class="m-widget11__title" style="margin-left:15px;">
                                      {{$watchlist->token}}
                                    </span>
																	</td>
																	<td>
																		<span class="m-widget11__title">
																			<img src="/icons/32x32/{{$watchlist->token}}.png" style="width:18px;"> {{$watchlist->token_name}}
																		</span>
																		<span class="m-widget11__sub">
																			Rank #{{$data->rank}} on CoinMarketCap
																		</span>
																	</td>
																	<td>
																		${{number_format($data->market_cap_usd)}}
																	</td>
																	<td>
																		<div class="m-widget11__chart" style="height:50px; width: 100px">
																			<iframe class="chartjs-hidden-iframe" tabindex="-1" style="display: block; overflow: hidden; border: 0px; margin: 0px; top: 0px; left: 0px; bottom: 0px; right: 0px; height: 100%; width: 100%; position: absolute; pointer-events: none; z-index: -1;"></iframe>
																			<canvas id="{{$watchlist->token_cmc_id}}" style="display: block; width: 100px; height: 50px;" width="100" height="50"></canvas>
																		</div>
																	</td>
																	<td>
																		${{$data->price_usd}}
																	</td>
																	<td class="m--align-right @if($data->percent_change_24h > 0) m--font-brand @else m--font-danger @endif">
                                    {{$data->percent_change_24h}}%
																	</td>
                                  <td class="m--align-right">
                                    <a href="/watchlist/delete/{{$watchlist->id}}" class="delete-role m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Delete">							<i class="la la-trash"></i>						</a>
                                  </td>
																</tr>
                              @endforeach
															</tbody>
															<!--end::Tbody-->
														</table>
														<!--end::Table-->
													</div>
												</div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</div>





<div class="modal fade" id="m_modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          Add Transactions
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            &times;
          </span>
        </button>
      </div>
      <div class="modal-body" style="padding:0px!important;">
        <div class="m-scrollable" data-scrollable="false" data-max-height="380" data-mobile-max-height="200">
  				<div class="m-nav-grid m-nav-grid--skin-light">
  					<div class="m-nav-grid__row">
  						<a href="javascript:void(0)" class="m-nav-grid__item" data-toggle="modal" data-target="#transaction_modal">
  							<i class="m-nav-grid__icon flaticon-add"></i>
  							<span class="m-nav-grid__text">
  								Add Transaction
  							</span>
  						</a>
  						<a href="javascript:void(0)" class="m-nav-grid__item" data-toggle="modal" data-target="#import_modal">
  							<i class="m-nav-grid__icon flaticon-multimedia-1"></i>
  							<span class="m-nav-grid__text">
  								Import Transactions
  							</span>
  						</a>
  					</div>
  				</div>
  			</div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="import_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          Import Data
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            &times;
          </span>
        </button>
      </div>
      <div class="modal-body" style="padding:0px!important;">
        <div class="m-scrollable" data-scrollable="false" data-max-height="380" data-mobile-max-height="200">
  				<div class="m-nav-grid m-nav-grid--skin-light">
            <div class="m-nav-grid__row">
              @if(Auth::user()->hasExchange('poloniex'))
    						<a href="/polo-test" class="m-nav-grid__item">
    							<img src="/exchanges/poloniex.png" style="width:140px;">
                  <i class="la la-check" style="float:right;position:relative;margin-top:-50px;display:block;margin-right:-3px;font-size:15px;color:#55ce55"></i>
                  <span class="delete-exchange m--font-danger" exchange="poloniex"><i class="la la-trash" style="float:left;position:relative;margin-top:-50px;display:block;margin-right:3px;font-size:15px;"></i></span>
    						</a>
              @else
                <a href="#" class="m-nav-grid__item add-exchange" exchange="Poloniex" data-toggle="modal" data-target="#connect_modal">
    							<img src="/exchanges/poloniex.png" style="width:140px;">
    						</a>
              @endif
              @if(Auth::user()->hasExchange('bittrex'))
                <a href="#" class="m-nav-grid__item" exchange="Bittrex" data-toggle="modal" data-target="#option_modal">
                  <img src="/exchanges/bittrex.png?v=12" style="width:140px;">
                  <i class="la la-check" style="float:right;position:relative;margin-top:-50px;display:block;margin-right:-3px;font-size:15px;color:#55ce55"></i>
                  <span class="delete-exchange m--font-danger" exchange="bittrex"><i class="la la-trash" style="float:left;position:relative;margin-top:-50px;display:block;margin-right:3px;font-size:15px;"></i></span>
                </a>
              @else
                <a href="#" class="m-nav-grid__item add-exchange" exchange="bittrex" data-toggle="modal" data-target="#connect_modal">
                  <img src="/exchanges/bittrex.png?v=12" style="width:140px;">
                </a>
              @endif
  					</div>
            <div class="m-nav-grid__row">
              @if(Auth::user()->hasExchange('coinbase'))
                <a href="/coinbase-test/" class="m-nav-grid__item">
                  <img src="/exchanges/coinbase.png" style="width:140px;">
                  <i class="la la-check" style="float:right;position:relative;margin-top:-50px;display:block;margin-right:-3px;font-size:15px;color:#55ce55"></i>
                  <span class="delete-exchange m--font-danger" exchange="Coinbase"><i class="la la-trash" style="float:left;position:relative;margin-top:-50px;display:block;margin-right:3px;font-size:15px;"></i></span>
                </a>
              @else
                <a href="https://www.coinbase.com/oauth/authorize?response_type=code&client_id=7684855d375a8aa4486c183903573f28ea098779df9c970cc4ad7b36a0ea748e&redirect_uri=https://altpocket.io/coinbase/callback&scope=wallet:accounts:read,wallet:transactions:read,wallet:buys:read,wallet:deposits:read,wallet:sells:read,wallet:withdrawals:read,wallet:payment-methods:read&account=all" class="m-nav-grid__item">
                  <img src="/exchanges/coinbase.png" style="width:140px;">
                </a>
              @endif
              <a href="#" class="m-nav-grid__item">
                <img src="/exchanges/kraken.png" style="width:140px;">
              </a>
            </div>
            <div class="m-nav-grid__row">
              <a href="#" class="m-nav-grid__item" data-toggle="modal" data-target="#transaction_modal">
                <img src="/exchanges/gdax.png" style="width:80px;">
              </a>
              <a href="#" class="m-nav-grid__item">
                <img src="/exchanges/hitbtc.png?v=1" style="width:140px;margin-top:-30px;">
              </a>
            </div>
            <div class="m-nav-grid__row">
              <a href="#" class="m-nav-grid__item" data-toggle="modal" data-target="#transaction_modal">
                <img src="/exchanges/binance.svg" style="width:140px;">
              </a>
              <a href="#" class="m-nav-grid__item">
                <img src="/exchanges/bitfinex.svg" style="width:140px;">
              </a>
            </div>
  				</div>
  			</div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="connect_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          Connect Exchange
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            &times;
          </span>
        </button>
      </div>
      <div class="modal-body">
      <form id="form-horizontal" role="form" method="post" action="/exchange/add">
        <input id="exchange" type="hidden" name="exchange" value="">
        {{csrf_field()}}
        <div class="form-group m-form__group row">
					<label for="example-text-input" class="col-2 col-form-label">
						Public Key
					</label>
					<div class="col-10">
						<input class="form-control m-input" type="text" name="public" value="" id="example-text-input">
					</div>
				</div>
        <div class="form-group m-form__group row">
          <label for="example-text-input" class="col-2 col-form-label">
            Private Key
          </label>
          <div class="col-10">
            <input class="form-control m-input" type="text" name="private" value="" id="example-text-input">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" type="submit">
            Connect Exchange
          </button>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="option_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          Import Options
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            &times;
          </span>
        </button>
      </div>
      <div class="modal-body">
      <form id="form-horizontal" role="form" method="post" action="/bittrex-test/" enctype="multipart/form-data">
        <input id="exchange" type="hidden" name="exchange" value="Bittrex">
        {{csrf_field()}}
        <div class="m-form__group form-group row">
          <label class="col-6 col-form-label">
            Full CSV Log
          </label>
          <div class="col-6">
            <label class="custom-file">
							<input type="file" id="file2" name="csv" class="custom-file-input">
							<span class="custom-file-control"></span>
						</label>
          </div>
        </div>
        <br>
        <div class="m-form__group form-group row">
					<label class="col-6 col-form-label">
						Remove Withdrawals
					</label>
					<div class="col-6">
						<span class="m-switch" style="float:right;">
							<label>
								<input type="checkbox" checked="checked" name="remove" name="">
								<span></span>
							</label>
						</span>
					</div>
				</div>
        <div class="modal-footer">
          <button class="btn btn-primary" type="submit">
            Import Data
          </button>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="m_modal_watchlist" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          Add to Watchlist
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            &times;
          </span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form-horizontal" role="form" method="post" action="/watchlist/add">
          {{ csrf_field() }}
          <div class="form-group m-form__group">
            <label for="param">
              Token
            </label>
            <select class="form-control m-select2 m_token_select"  name="token" style="width:100%!important;" required>
              <option></option>
            </select>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit">
              Add To Watchlist
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="profit_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          Select Profit Type
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            &times;
          </span>
        </button>
      </div>
      <div class="modal-body">
        <p>Here you select how you want Altpocket to calculate your profit.</p>
        <form id="form-horizontal" role="form" method="post" action="/portfolio/profit">
          {{ csrf_field() }}
          <div class="form-group m-form__group">
            <label for="param">
              Type
            </label>
            <select class="form-control m-select2" id="m_select2_1" name="type" style="width:100%!important;" required>
              <option value="default">Portfolio Value - Portfolio Paid</option>
              <option value="invested" @if(Auth::user()->algorithm == 2) selected @endif>Portfolio Value - Invested</option>
            </select>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit">
              Set profit type
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="invested_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          Set Invested
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            &times;
          </span>
        </button>
      </div>
      <div class="modal-body">
        <p>Calculating your initial or total invested can be tricky, by default we use all your deposits and withdraws and substract your total withdraw from your total deposit, if you have withdrawn more than you deposited then it will be set at 0. <br><strong>Therefor it is recommended to select which deposits should be calculated in your invested.</strong></p>
        <div class="table-responsive">
          <!--begin::Table-->
          <table class="table">
            <!--begin::Thead-->
            <thead>
              <tr>
                <td class="m-widget11__label">
                  Toggled
                </td>
                <td class="m-widget11__app">
                  Token
                </td>
                <td class="m-widget11__sales">
                  Amount
                </td>
                <td class="m-widget11__price">
                  Worth at Deposit
                </td>
                <td class="m-widget11__price">
                  Exchange
                </td>
                <td class="m-widget11__total m--align-right">
                  Date
                </td>
              </tr>
            </thead>
            <!--end::Thead-->
<!--begin::Tbody-->
            <tbody>
              @foreach($deposits as $deposit)
                <tr>
                  <td>
                    <label class="m-checkbox m-checkbox--solid m-checkbox--single m-checkbox--brand">
                      <input type="checkbox" name="toggle_deposit" class="toggle_deposit" data-id="{{$deposit->id}}" @if($deposit->toggled == 1) checked @endif>
                      <span></span>
                    </label>
                  </td>
                  <td>
                    <span class="m-widget11__title">
                      {{$deposit->token_name}}
                    </span>
                  </td>
                  <td>
                    {{number_format($deposit->amount, 8)}}
                  </td>
                  <td>
                    ${{number_format($deposit->amount * $deposit->price * $deposit->btc, 2)}}
                  </td>
                  <td>
                    {{$deposit->exchange}}
                  </td>
                  <td class="m--align-right m--font-brand">
                    {{date('Y-m-d', strtotime($deposit->date))}}
                  </td>
                </tr>
              @endforeach

            </tbody>
            <!--end::Tbody-->
          </table>
          <!--end::Table-->
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="transaction_modal" role="dialog" aria-labelledby="TransactionModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          New Transaction
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">
            &times;
          </span>
        </button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs  m-tabs-line" role="tablist">
					<li class="nav-item m-tabs__item">
						<a class="nav-link m-tabs__link active" data-toggle="tab" href="#buy_transaction" id="toggle_buy" role="tab" aria-expanded="true">
							Buy
						</a>
					</li>
          <li class="nav-item m-tabs__item">
						<a class="nav-link m-tabs__link" data-toggle="tab" href="#buy_transaction" id="toggle_sell" role="tab" aria-expanded="false">
							Sell
						</a>
					</li>
          <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link" data-toggle="tab" href="#deposit" id="toggle_deposit" role="tab" aria-expanded="false">
              Deposit (Soon)
            </a>
          </li>
          <li class="nav-item m-tabs__item">
            <a class="nav-link m-tabs__link" data-toggle="tab" href="#deposit" id="toggle_withdraw" role="tab" aria-expanded="false">
              Withdraw (Soon)
            </a>
          </li>
				</ul>
        <div class="tab-content">
          <div class="tab-pane active" id="buy_transaction" role="tabpanel" aria-expanded="true">
            <form id="form-horizontal" role="form" method="post" action="/portfolio/add">
              {{ csrf_field() }}
              <input id="transaction_type" type="hidden" name="type" value="BUY">
              <div class="form-group m-form__group">
    						<label for="param">
    							Token
    						</label>
                <select class="form-control m-select2" id="m_select2_6" name="token" style="width:100%!important;" required>
                  <option></option>
                </select>
    					</div>

              <div class="form-group m-form__group">
                <label for="param">
    							Exchange
    						</label>
                  <select class="form-control m-select2" id="m_select2_1" name="exchange" style="width:100%!important;" required>
                    <option value="Manual">
                      Manual
                    </option>
                    <option value="Bittrex">
                      Bittrex
                    </option>
                    <option value="Bitfinex">
                      Bitfinex
                    </option>
                    <option value="Binance">
                      Binance
                    </option>
                    <option value="Coinbase">
                      Coinbase
                    </option>
                    <option value="Poloniex">
                      Poloniex
                    </option>
                  </select>
              </div>

              <div class="form-group m-form__group">
                <label for="param">
                  Market
                </label>
                  <select class="form-control m-select2 market" id="m_select2_1" name="market" style="width:100%!important;">
                    <option value="BTC">
                      BTC
                    </option>
                    <option value="ETH">
                      ETH
                    </option>
                    <option value="USDT">
                      USDT
                    </option>
                    <option value="USD">
                      USD
                    </option>
                    <option value="EUR">
                      EUR
                    </option>
                    <option value="GBP">
                      GBP
                    </option>
                    <option value="AUD">
                      AUD
                    </option>
                    <option value="CAD">
                      CAD
                    </option>
                  </select>
              </div>

              <div class="form-group m-form__group">
                <label for="param" id="price_title">
                  Buy Price
                </label>
                <div class="row">
                  <div class="col-md-8">
                    <input class="form-control m-input price-input" id="example-number-input" type="number" step="0.00000000001" placeholder="In BTC" name="price" required>
                  </div>
                  <div class="col-md-4">
                    <select class="form-control m-select2" id="m_select2_10" name="price_input" style="width:100%!important;" required>
                      <option value="paidper">
                        Paid Per
                      </option>
                      <option value="intotal">
                        In Total
                      </option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group m-form__group">
                <label for="param" id="amount_title">
                  Amount Bought
                </label>
                  <input class="form-control m-input" type="amount" id="transaction_amount" name="amount" required>
              </div>

              <div class="form-group m-form__group">
                <label for="param">
                  Date & Time
                </label>
                  <input type='text' class="form-control" id="m_datetimepicker_1" readonly placeholder="Select date & time" name="date" value="{{date('Y-m-d H:i:s')}}"/>
              </div>
              <br>
              <div class="form-group m-form__group">
                <div class="row">
                  <div class="col-md-12">
                  <button type="button" class="btn btn-primary btn-sm" id="displaymore" style="display:block;margin:0 auto;">Show More</button>
                </div>
                </div>
              </div>

              <div id="additional" style="display:none;">
                <hr>
                <br>
                <div class="form-group m-form__group">
                  <div class="row">
                  <label class="col-9 col-form-label" id="holding_title">
                    Deduct from <span id="holding_span">BTC</span> holdings
                  </label>
                  <div class="col-3">
                    <span class="m-switch m-switch--icon" style="float:right;">
                      <label>
                        <input type="checkbox" name="deduct">
                        <span></span>
                      </label>
                    </span>
                  </div>
                  </div>
                </div>

                <div class="form-group m-form__group">
                  <label for="param">
                    Fee
                  </label>
                  <div class="row">
                    <div class="col-md-8">
                      <input class="form-control m-input" type="number" id="example-number-input" name="fee" step="0.00000000001" value="0" placeholder="In BTC">
                    </div>
                    <div class="col-md-4">
                      <select class="form-control m-select2" id="m_select2_10_2" name="fee_currency" style="width:100%!important;">
                        <option value="BTC" id="fee_market">
                          BTC
                        </option>
                        <option value="ETH" id="fee_currency">
                          ETH
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group m-form__group">
      						<label for="exampleTextarea">
      							Notes
      						</label>
      						<textarea class="form-control m-input" id="exampleTextarea" name="notes" rows="3"></textarea>
      					</div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-primary" type="submit">
                Add Transaction
              </button>
            </div>
            </form>
          </div>
          <div class="tab-pane" id="deposit" role="tabpanel" aria-expanded="true">
            <form>
              <div class="form-group m-form__group">
    						<label for="param">
    							Token
    						</label>
                <select class="form-control m-select2" id="m_select2_6_2" name="token" style="width:100%!important;">
                  <option></option>
                </select>
    					</div>

              <div class="form-group m-form__group">
                <label for="param">
    							Exchange
    						</label>
                  <select class="form-control m-select2" id="m_select2_1_2" name="exchange" style="width:100%!important;">
                    <option value="Manual">
                      Manual
                    </option>
                    <option value="Bittrex">
                      Bittrex
                    </option>
                    <option value="Bitfinex">
                      Bitfinex
                    </option>
                    <option value="Binance">
                      Binance
                    </option>
                    <option value="Coinbase">
                      Coinbase
                    </option>
                    <option value="Poloniex">
                      Poloniex
                    </option>
                  </select>
              </div>

              <div class="form-group m-form__group">
                <label for="param" id="">
                  Amount
                </label>
                  <input class="form-control m-input" type="amount" id="example-number-input">
              </div>

              <div class="form-group m-form__group">
                <label for="param">
                  Date & Time
                </label>
                  <input type='text' class="form-control" id="m_datetimepicker_1_2" readonly placeholder="Select date & time" name="date"/>
              </div>
              <br>
              <div class="form-group m-form__group">
                <div class="row">
                  <div class="col-md-12">
                  <button type="button" class="btn btn-primary btn-sm" id="displaymore2" style="display:block;margin:0 auto;">Show More</button>
                </div>
                </div>
              </div>

              <div id="additional2" style="display:none;">
                <hr>
                <br>
                <div class="form-group m-form__group">
      						<label for="exampleTextarea">
      							Notes
      						</label>
      						<textarea class="form-control m-input" id="exampleTextarea" name="notes" rows="3"></textarea>
      					</div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>

$(".add-exchange").click(function(){
var exchange = $(this).attr('exchange');
$("#exchange").val(exchange);
});

$(".delete-exchange").click(function(){
  window.location.replace("/exchange/delete/"+$(this).attr('exchange'));
});


$(document).ready(function(){
  window.currency = "ETH";

  var zero = localStorage.getItem('zeroToggled');
  var btc = localStorage.getItem('btcToggled');

  if(zero == "false")
  {
    $('.zero').toggle();
  }

  if(btc == "true")
  {
    $('.price-toggle').toggle();
  }


});

$(".toggle_deposit").click(function(){
  var id = $(this).attr('data-id');

  jQuery.ajax({
     url: '/portfolio/toggle/'+id,
     type: 'get',
     dataType: 'json',
     success:function(data)
     {
       console.log("success");
     }
  });
});

$(".toggle-btc").click(function(){
    localStorage.setItem('btcToggled', $('.btc-field').is(':hidden'));
  $('.price-toggle').toggle();
});

$(".toggle-zero").click(function(){
  localStorage.setItem('zeroToggled', $('.zero').is(':hidden'));
  $('.zero').toggle();
})

$("#displaymore").click(function(){
  $("#additional").toggle();
  if($(this).text() == "Show More")
  {
    $(this).text('Show Less');
  } else {
    $(this).text('Show More');
  }
});


$("#displaymore2").click(function(){
  $("#additional2").toggle();
  if($(this).text() == "Show More")
  {
    $(this).text('Show Less');
  } else {
    $(this).text('Show More');
  }
});

  $("#toggle_sell").click(function(){

    $("#price_title").text('Sell Price');
    $("#amount_title").text('Amount Sold');
    $("#holding_title").text('Add To Holdings');
    $("#transaction_type").val('SELL');

  });

  $("#toggle_buy").click(function(){

    var market = $(".market").val();

    $("#price_title").text('Bought Price');
    $("#amount_title").text('Amount Bought');
    $("#holding_title").html('Deduct from <span id="holding_span">'+market+'</span> holdings');
    $("#transaction_type").val('BUY');
  });

  // On Market Change
  $(".market").change(function(){
    var market = $(this).val();
    $(".price-input").attr('placeholder', 'In ' + market);
    $("#holding_span").text(market);
    $('#m_select2_10_2').html('').select2({data: [{id: '', text: ''}]});
    $('#m_select2_10_2').html('').select2({
        placeholder: "Select an option",
        minimumResultsForSearch: Infinity,
        data: [
         {id: market, text: market}, {id: currency, text: currency}]});

         // If global variable price is set
         if(typeof window.price != 'undefined')
         {
             var date = $("#m_datetimepicker_1").val();
             if(market == "BTC")
             {
               $(".price-input").val(window.price.BTC);
             } else if(market == "ETH")
             {
               jQuery.ajax({
                  url: '/get/historyeth/'+date,
                  type: 'get',
                  dataType: 'json',
                  success:function(data)
                  {
                      var eth = data.ETH;
                      $(".price-input").val(window.price.BTC * eth);
                  }
             });
           } else if(market == "USD" || market == "USDT") {
               $(".price-input").val(window.price.USD);
           } else {
             jQuery.ajax({
                url: '/get/fiat/'+market+'/'+date,
                type: 'get',
                dataType: 'json',
                success:function(data)
                {
                    var fiat = data;
                    var value = window.price.BTC * window.historicbtc.USD * fiat;
                    $(".price-input").val(value.toFixed(2));
                }
             });
           }
         }
       if(typeof window.token != 'undefined' && typeof window.price == 'undefined') {
         var date = new Date();
         if(market == "BTC")
         {
           $(".price-input").val(window.token.price_btc);
         } else if(market == "ETH")
         {
           $(".price-input").val(window.token.price_eth);
         } else if(market == "USD" || market == "USDT") {
           $(".price-input").val(window.token.price_usd);
         } else {
           var price = window.token.price_btc;
           var btc = {{$btc}};

           jQuery.ajax({
              url: '/get/fiat/'+market+'/'+date.toISOString().substring(0, 10),
              type: 'get',
              dataType: 'json',
              success:function(data)
              {
                  var fiat = data;
                  var value = price * btc * fiat;
                  $(".price-input").val(value.toFixed(2));
              }
           });
         }
       }

});

  // On token change
  $('#m_select2_6').on('select2:select', function (e) {
    var market = $(".market").val();
    var date = new Date();

    var data = e.params.data;
    window.currency = data.symbol;
    window.token = data;

    if(market == "BTC")
    {
      $(".price-input").val(data.price_btc);
    } else if(market == "USDT" || market == "USD")
    {
      $(".price-input").val(data.price_usd);
    } else if(market == "ETH")
    {
      $(".price-input").val(data.price_eth);
    } else {
      var price = data.price_btc;
      var btc = {{$btc}};

      jQuery.ajax({
         url: '/get/fiat/'+market+'/'+date.toISOString().substring(0, 10),
         type: 'get',
         dataType: 'json',
         success:function(data)
         {
             var fiat = data;
             var value = price * btc * fiat;
             $(".price-input").val(value.toFixed(2));
         }
      });

    }

    $('#m_select2_10_2').html('').select2({data: [{id: '', text: ''}]});
    $('#m_select2_10_2').html('').select2({
        placeholder: "Select an option",
        minimumResultsForSearch: Infinity,
        data: [
         {id: market, text: market}, {id: currency, text: currency}]});
  });

  $("#m_select2_10").change(function(){
    var type = $(this).val();
    if(type == "intotal")
    {
      if($(".price-input").val() != "" && $("#transaction_amount").val())
      {
        var value = $(".price-input").val();

        $(".price-input").val(value * $("#transaction_amount").val());
      }
    } else {
      if($(".price-input").val() != "" && $("#transaction_amount").val())
      {
        var value = $(".price-input").val();

        $(".price-input").val(value / $("#transaction_amount").val());
      }
    }

  });
  // On date change
  $("#m_datetimepicker_1").change(function(){
    var date = $(this).val();
    var market = $(".market").val();

    if(typeof token != 'undefined')
    {

      // Get historic btc
      jQuery.ajax({
         url: '/get/history/bitcoin/'+date,
         type: 'get',
         dataType: 'json',
         success:function(data)
         {
           window.historicbtc = data;
         }
         });

      //get price
      jQuery.ajax({
         url: '/get/history/'+token.cmc_id+'/'+date,
         type: 'get',
         dataType: 'json',
         success:function(data)
         {
           window.price = data;
           if(market == "BTC")
           {
             $(".price-input").val(data.BTC);
           } else if(market == "USDT" || market == "USD")
           {
             $(".price-input").val(data.USD);
           } else if(market == "ETH")
           {
             var price_btc = data.BTC;
             jQuery.ajax({
                url: '/get/historyeth/'+date,
                type: 'get',
                dataType: 'json',
                success:function(data)
                {
                    var eth = data.ETH;
                    $(".price-input").val(price_btc * eth);
                }
             });
           } else {
             var price = data.BTC;
             var btc = window.historicbtc.USD;

             jQuery.ajax({
                url: '/get/fiat/'+market+'/'+date,
                type: 'get',
                dataType: 'json',
                success:function(data)
                {
                    var fiat = data;
                    var value = price * btc * fiat;
                    $(".price-input").val(value.toFixed(2));
                }
             });
            }
         }
      });
    }
  });

  var _initSparklineChart = function(src, data, color, border) {
      if (src.length == 0) {
          return;
      }

      var config = {
          type: 'line',
          data: {
              labels: [0, 1, 2, 3, 4, 5, 6, 7],
              datasets: [{
                  label: "",
                  borderColor: color,
                  borderWidth: border,

                  pointHoverRadius: 4,
                  pointHoverBorderWidth: 12,
                  pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                  pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                  pointHoverBackgroundColor: mUtil.getColor('danger'),
                  pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),
                  fill: false,
                  data: data,
                  spanGaps: true
              }]
          },
          options: {
              title: {
                  display: false,
              },
              tooltips: {
                  enabled: false,
                  intersect: false,
                  mode: 'nearest',
                  xPadding: 10,
                  yPadding: 10,
                  caretPadding: 10
              },
              legend: {
                  display: false,
                  labels: {
                      usePointStyle: false
                  }
              },
              responsive: true,
              maintainAspectRatio: false,
              hover: {
                  mode: 'index'
              },
              scales: {
                  xAxes: [{
                      display: false,
                      gridLines: false,
                      scaleLabel: {
                          display: false,
                          labelString: 'Month'
                      }
                  }],
                  yAxes: [{
                      display: false,
                      gridLines: false,
                      scaleLabel: {
                          display: true,
                          labelString: 'Value'
                      },
                      ticks: {
                          beginAtZero: false
                      }
                  }]
              },

              elements: {
                point: {
                    radius: 1,
                    borderWidth: 12
                },
              },

              layout: {
                  padding: {
                      left: 0,
                      right: 0,
                      top: 10,
                      bottom: 0
                  }
              }
          }
      };

      return new Chart(src, config);
  }


//_initSparklineChart($('#portfolio_chart'), [], mUtil.getColor('accent'), 3);
//_initSparklineChart($('#profit_chart'), [], mUtil.getColor('danger'), 3);
@foreach($watchlists as $watchlist)
_initSparklineChart($('#{{$watchlist->token_cmc_id}}'), [

@foreach($watchlist->getPriceHistory($watchlist->token_cmc_id, '2017-12-15') as $test)
{{$test}},
@endforeach


], mUtil.getColor('accent'), 2);
@endforeach
</script>
@endsection
