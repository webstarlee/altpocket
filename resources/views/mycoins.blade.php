@extends('layouts.app')

@section('title')
My Investments
@endsection

@section('content')
<?php

use Jenssegers\Agent\Agent;

$agent = new Agent();

?>
    <div id="content_wrapper" class="">
    <div id="header_wrapper" class="header-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <header id="header">
                        <h1>My coins</h1>
                        <nav class="btn-fab-group" style="float:right;margin-top:15px;margin-bottom:20px;">
                          <button class="btn btn-primary btn-fab fab-menu" data-fab="down">
                            <i class="zmdi zmdi-plus"></i>
                          </button>
                          <ul class="nav-sub">
                            <li data-toggle="tooltip" data-placement="left" title="New Investment"> <a data-toggle="modal" data-target="#basic_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm"><i class="zmdi zmdi-plus"></i></a></li>
                            <li data-toggle="tooltip" data-placement="left" title="Add Mining"> <a data-toggle="modal" data-target="#mining_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm">M</a></li>
                            <li data-toggle="tooltip" data-placement="left" title="Sell Amount"> <a data-toggle="modal" data-target="#sell_modal" href="javascript:void(0)" class="btn btn-danger btn-fab btn-fab-sm"><i class="fa fa-money"></i></a></li>



                            <li data-toggle="tooltip" data-placement="left" title="Reset all data"> <a href="javascript:void(0)" id="reset-data" class="btn btn-danger btn-fab btn-fab-sm"><i class="zmdi zmdi-delete"></i></a></li>
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
                  <div class="alert alert-danger" role="alert">
                                <strong>Hey you!</strong> We have recently rewritten and reconstructed our investment system from scratch, we advise you to move over directly! As the new system is more accurate and is overall better. (Especially importing).<br><br>
                                You can find the guide on how the new system works here: (Make sure you follow it!) <a style="color:white;text-decoration:underline;" href="https://altpocket.io/importing-orders">https://altpocket.io/importing-orders</a>.<br>
                                You can also migrate all your manual orders from this old system to the new system here: <a style="color:white;text-decoration:underline;" href="https://altpocket.io/migrate">https://altpocket.io/migrate</a>.
                              </div>
                </div>
                @if(Auth::user()->getCurrency() == "USD")
                <div class="row">
                  <div class="col-xs-12 col-sm-4">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Total Investment</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;">${{number_format(Auth::user()->invested, 2)}}</h1>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-4">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Net Worth</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;">${{number_format($networth * $btc, 2)}}</h1>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-4">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Profit</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 @if((($networth * $btc) - Auth::user()->invested) > 0) style="color:#73c04d"; @else style="color:#de6b6b"; @endif >${{number_format((($networth * $btc) - Auth::user()->invested), 2)}}</h1>
                        </div>
                    </div>
                  </div>
                </div>
                @elseif(Auth::user()->getCurrency() == "BTC")
                <div class="row">
                  <div class="col-xs-12 col-sm-4">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Total Investment</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;"><i class="fa fa-btc"></i> {{number_format(Auth::user()->getInvestedBTC(), 5)}}</h1>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-4">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Net Worth</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;"><i class="fa fa-btc"></i> {{number_format($networth, 5)}}</h1>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-4">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Profit</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 @if((($networth * $btc) - Auth::user()->invested) > 0) style="color:#73c04d"; @else style="color:#de6b6b"; @endif ><i class="fa fa-btc"></i> {{number_format((($networth) - Auth::user()->getInvestedBTC()), 5)}}</h1>
                        </div>
                    </div>
                  </div>
                </div>
                @endif
                <div class="row">
											<div class="col-md-12 col-lg-12">
												<div class="card">
													<header class="card-heading p-0">
														<div class="tabpanel m-b-30">
															<ul class="nav nav-tabs nav-justified">
																<li class="active " role="presentation"><a href="#profile-timeline" data-toggle="tab" aria-expanded="true">Active Investments</a></li>
																<li role="presentation"><a href="#profile-about" data-toggle="tab" aria-expanded="true">Sold Investments</a></li>
																<li role="presentation"><a href="#summary" data-toggle="tab" aria-expanded="true">Summary</a></li>
															</ul>
														</div>
														<div class="card-body">
															<div class="tab-content">

																<div class="tab-pane fadeIn" id="summary">
                                                                    <div class="row">
                                                                        <div class="container col-md-6">
                                                                          <h2>Profit last 7 days</h2>
                                                                            <canvas id="profit7"></canvas>
                                                                        </div>
                                                                        <div class="container col-md-offset-2 col-md-3">
                                                                          <h2>USD invested per investment</h2>
                                                                            <canvas id="holding"></canvas>
                                                                          </div>
                                                                        </div>

                                                                    <div class="row">

                  @if(Auth::user()->getCurrency() == "USD")
                  <div class="card card-data-tables product-table-wrapper">
                      <br>
                    <header class="card-heading">
                        <h1>Active Investments</h1>
                      <small class="dataTables_info">
                        </small>

                      <div class="card-search">
                        <div id="productsTable_wrapper" class="form-group label-floating is-empty">
                          <i class="zmdi zmdi-search search-icon-left"></i>
                          <input type="text" class="form-control filter-input" placeholder="Filter Products..." autocomplete="off">
                          <a href="javascript:void(0)" class="close-search" data-card-search="close" data-toggle="tooltip" data-placement="top" title="Close"><i class="zmdi zmdi-close"></i></a>
                        </div>
                      </div>
                      <ul class="card-actions icons right-top">
                        <li id="deleteItems" style="display: none;">
                          <span class="label label-info pull-left m-t-5 m-r-10 text-white"></span>
                          <a href="javascript:void(0)" id="confirmDelete" data-toggle="tooltip" data-placement="top" data-original-title="Delete Product(s)">
                            <i class="zmdi zmdi-delete"></i>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" data-card-search="open" data-toggle="tooltip" data-placement="top" data-original-title="Filter Products">
                            <i class="zmdi zmdi-filter-list"></i>
                          </a>
                        </li>
                        <li class="dropdown" data-toggle="tooltip" data-placement="top" data-original-title="Show Entries">
                          <a href="javascript:void(0)" data-toggle="dropdown">
                            <i class="zmdi zmdi-more-vert"></i>
                          </a>
                          <div id="dataTablesLength">
                          </div>
                        </li>
                      </ul>
                    </header>
                    <div class="card-body p-0">
                      <div class="alert alert-info m-20 hidden-md hidden-lg" role="alert">
                        <p>
                          Heads up! You can Swipe table Left to Right on Mobile devices.
                        </p>
                      </div>
                      <div class="table-responsive">
                        <table id="productsTable" class="mdl-data-table product-table m-t-30" cellspacing="0" width="100%">
                          <thead>
                            <tr>
                              <th class="col-xs-2">Currency</th>
                              <th class="col-xs-2">Type</th>
                              <th class="col-xs-2">Amount</th>
                              <th class="col-xs-2">Value in USD</th>
                              <th class="col-xs-2">Price in USD</th>
                              <th class="col-xs-2">Profit in $</th>
                              <th class="col-xs-2">Change in %</th>
                              <th class="col-xs-2">Date Bought</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($investments as $investment)
                              @if($investment->sold_at == null)

                                <?php
                                    if(Auth::user()->api == "coinmarketcap"){
                                        if(DB::table('cryptos')->where('symbol', $investment->crypto)->first()){
                                            if($investment->crypto == "STR"){
                                                $coinbtc = DB::table('cryptos')->where('symbol', 'XLM')->first()->price_btc;
                                            } else {
                                                $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                            }
                                        } else {
                                        $coinbtc = DB::table('polos')->where('symbol', $investment->crypto)->first()->price_btc;
                                        }
                                    } elseif(Auth::user()->api == "poloniex"){
                                        if(DB::table('polos')->where('symbol', $investment->crypto)->first()){
                                        $coinbtc = DB::table('polos')->where('symbol', $investment->crypto)->first()->price_btc;
                                        } else {
                                        $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                        }
                                    }
                                        else {
                                        if(DB::table('bittrexes')->where('symbol', $investment->crypto)->first()){
                                            $coinbtc = DB::table('bittrexes')->where('symbol', $investment->crypto)->first()->price_btc;
                                        } else {
                                            $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                        }
                                    }


                                ?>



                            <tr>
                                @if($investment->crypto == "SJCX")
                                <td><img style="width:10%" src="/icons/storj.png"/>                                         {{$investment->crypto}}</td>
                                @elseif($investment->crypto == "XLM")
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/stellar.png"/>                                         {{$investment->crypto}}</td>                                                      @elseif($investment->crypto == "GNT")
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/golem-network-tokens.png"/>                                         {{$investment->crypto}}</td>
                                @else
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/{{strtolower(DB::table('cryptos')->where('symbol', $investment->crypto)->first()->name)}}.png"/>                                         {{$investment->crypto}}</td>
                                @endif
                                <td>Buy</td>
                                <td>{{$investment->amount}}</td>
                                <td>${{DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_usd * $investment->amount}}</td>
                                <td>${{DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_usd}}</td>
                        @if((($investment->amount * $coinbtc) * $btc) > $investment->usd_total)
                        <td><span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">${{number_format((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at) * $investment->btc_price_bought), 2)}}</span></td>
                          <td><span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/(($investment->amount * $investment->bought_at) * $investment->btc_price_bought)) * ((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at)) * $investment->btc_price_bought), 2)}}%
                              </span></td>
                        @else
                                <td><span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">{{number_format((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at) * $investment->btc_price_bought), 2)}}$</span></td>
                          <td><span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/(($investment->amount * $investment->bought_at) * $investment->btc_price_bought)) * ((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at)) * $investment->btc_price_bought), 2)}}%
                              </span></td>
                        @endif
                                @if($investment->bittrex_id != null)
                                <td>{{$investment->date}}</td>
                                @else
                                <td>{{$investment->date_bought}}</td>
                                @endif
                            </tr>
                            @endif
                              @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>


                  <div class="card card-data-tables sold-table-wrapper">
                      <br>
                    <header class="card-heading">
                        <h1>Sold Investments</h1>
                      <small class="dataTables_info">
                        </small>

                      <div class="card-search">
                        <div id="sold_wrapper" class="form-group label-floating is-empty">
                          <i class="zmdi zmdi-search search-icon-left"></i>
                          <input type="text" class="form-control filter-input" placeholder="Filter Products..." autocomplete="off">
                          <a href="javascript:void(0)" class="close-search" data-card-search="close" data-toggle="tooltip" data-placement="top" title="Close"><i class="zmdi zmdi-close"></i></a>
                        </div>
                      </div>
                      <ul class="card-actions icons right-top">
                        <li id="deleteItems" style="display: none;">
                          <span class="label label-info pull-left m-t-5 m-r-10 text-white"></span>
                          <a href="javascript:void(0)" id="confirmDelete" data-toggle="tooltip" data-placement="top" data-original-title="Delete Product(s)">
                            <i class="zmdi zmdi-delete"></i>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" data-card-search="open" data-toggle="tooltip" data-placement="top" data-original-title="Filter Products">
                            <i class="zmdi zmdi-filter-list"></i>
                          </a>
                        </li>
                        <li class="dropdown" data-toggle="tooltip" data-placement="top" data-original-title="Show Entries">
                          <a href="javascript:void(0)" data-toggle="dropdown">
                            <i class="zmdi zmdi-more-vert"></i>
                          </a>
                          <div id="dataTablesLength">
                          </div>
                        </li>
                      </ul>
                    </header>
                    <div class="card-body p-0">
                      <div class="alert alert-info m-20 hidden-md hidden-lg" role="alert">
                        <p>
                          Heads up! You can Swipe table Left to Right on Mobile devices.
                        </p>
                      </div>
                      <div class="table-responsive">
                        <table id="soldTable" class="mdl-data-table sold-table m-t-30" cellspacing="0" width="100%">
                          <thead>
                            <tr>
                              <th class="col-xs-2">Currency</th>
                              <th class="col-xs-2">Type</th>
                              <th class="col-xs-2">Amount</th>
                              <th class="col-xs-2">Bought for in USD</th>
                              <th class="col-xs-2">Sold for in USD</th>
                              <th class="col-xs-2">Profit in $</th>
                              <th class="col-xs-2">Change in %</th>
                              <th class="col-xs-2">Date Sold</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($investments as $investment)
                              @if($investment->sold_at != null)

                            <tr>
                                @if($investment->crypto == "SJCX")
                                <td><img style="width:10%" src="/icons/storj.png"/>                                         {{$investment->crypto}}</td>
                                @elseif($investment->crypto == "XLM")
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/stellar.png"/>                                         {{$investment->crypto}}</td>                                                      @elseif($investment->crypto == "GNT")
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/golem-network-tokens.png"/>                                         {{$investment->crypto}}</td>
                                @else
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/{{strtolower(DB::table('cryptos')->where('symbol', $investment->crypto)->first()->name)}}.png"/>                                         {{$investment->crypto}}</td>
                                @endif
                                <td>Sell</td>
                                <td>{{$investment->amount}}</td>
                                <td>${{$investment->usd_total}}</td>
                                <td>${{$investment->sold_for}}</td>
                        @if($investment->sold_for > $investment->usd_total)
                        <td><span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">${{number_format($investment->sold_for - $investment->usd_total, 2)}}</span></td>
                          <td><span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/$investment->usd_total) * ($investment->sold_for - $investment->usd_total), 2)}}%
                              </span></td>
                        @else
                                <td><span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">${{number_format($investment->sold_for - $investment->usd_total, 2)}}</span></td>
                          <td><span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/$investment->usd_total) * ($investment->sold_for - $investment->usd_total), 2)}}%
                              </span></td>
                        @endif
                                @if($investment->bittrex_id != null)
                                <td>{{$investment->date}}</td>
                                @else
                                <td>{{$investment->date_sold}}</td>
                                @endif
                            </tr>
                            @endif
                              @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  @elseif(Auth::user()->getCurrency() == "BTC")
                  <div class="card card-data-tables product-table-wrapper">
                      <br>
                    <header class="card-heading">
                        <h1>Active Investments</h1>
                      <small class="dataTables_info">
                        </small>

                      <div class="card-search">
                        <div id="productsTable_wrapper" class="form-group label-floating is-empty">
                          <i class="zmdi zmdi-search search-icon-left"></i>
                          <input type="text" class="form-control filter-input" placeholder="Filter Products..." autocomplete="off">
                          <a href="javascript:void(0)" class="close-search" data-card-search="close" data-toggle="tooltip" data-placement="top" title="Close"><i class="zmdi zmdi-close"></i></a>
                        </div>
                      </div>
                      <ul class="card-actions icons right-top">
                        <li id="deleteItems" style="display: none;">
                          <span class="label label-info pull-left m-t-5 m-r-10 text-white"></span>
                          <a href="javascript:void(0)" id="confirmDelete" data-toggle="tooltip" data-placement="top" data-original-title="Delete Product(s)">
                            <i class="zmdi zmdi-delete"></i>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" data-card-search="open" data-toggle="tooltip" data-placement="top" data-original-title="Filter Products">
                            <i class="zmdi zmdi-filter-list"></i>
                          </a>
                        </li>
                        <li class="dropdown" data-toggle="tooltip" data-placement="top" data-original-title="Show Entries">
                          <a href="javascript:void(0)" data-toggle="dropdown">
                            <i class="zmdi zmdi-more-vert"></i>
                          </a>
                          <div id="dataTablesLength">
                          </div>
                        </li>
                      </ul>
                    </header>
                    <div class="card-body p-0">
                      <div class="alert alert-info m-20 hidden-md hidden-lg" role="alert">
                        <p>
                          Heads up! You can Swipe table Left to Right on Mobile devices.
                        </p>
                      </div>
                      <div class="table-responsive">
                        <table id="productsTable" class="mdl-data-table product-table m-t-30" cellspacing="0" width="100%">
                          <thead>
                            <tr>
                              <th class="col-xs-2">Currency</th>
                              <th class="col-xs-2">Type</th>
                              <th class="col-xs-2">Amount</th>
                              <th class="col-xs-2">Value in BTC</th>
                              <th class="col-xs-2">Price in BTC</th>
                              <th class="col-xs-2">Profit in BTC</th>
                              <th class="col-xs-2">Change in %</th>
                              <th class="col-xs-2">Date Bought</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($investments as $investment)
                              @if($investment->sold_at == null)

                                <?php
                                    if(Auth::user()->api == "coinmarketcap"){
                                        if(DB::table('cryptos')->where('symbol', $investment->crypto)->first()){
                                            if($investment->crypto == "STR"){
                                                $coinbtc = DB::table('cryptos')->where('symbol', 'XLM')->first()->price_btc;
                                            } else {
                                                $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                            }
                                        } else {
                                        $coinbtc = DB::table('polos')->where('symbol', $investment->crypto)->first()->price_btc;
                                        }
                                    } elseif(Auth::user()->api == "poloniex"){
                                        if(DB::table('polos')->where('symbol', $investment->crypto)->first()){
                                        $coinbtc = DB::table('polos')->where('symbol', $investment->crypto)->first()->price_btc;
                                        } else {
                                        $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                        }
                                    }
                                        else {
                                        if(DB::table('bittrexes')->where('symbol', $investment->crypto)->first()){
                                            $coinbtc = DB::table('bittrexes')->where('symbol', $investment->crypto)->first()->price_btc;
                                        } else {
                                            $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                        }
                                    }


                                ?>



                            <tr>
                                @if($investment->crypto == "SJCX")
                                <td><img style="width:10%" src="/icons/storj.png"/>                                         {{$investment->crypto}}</td>
                                @elseif($investment->crypto == "XLM")
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/stellar.png"/>                                         {{$investment->crypto}}</td>                                                      @elseif($investment->crypto == "GNT")
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/golem-network-tokens.png"/>                                         {{$investment->crypto}}</td>
                                @else
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/{{strtolower(DB::table('cryptos')->where('symbol', $investment->crypto)->first()->name)}}.png"/>                                         {{$investment->crypto}}</td>
                                @endif
                                <td>Buy</td>
                                <td>{{$investment->amount}}</td>
                                <td><i class="fa fa-btc"></i> {{DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc * $investment->amount}}</td>
                                <td><i class="fa fa-btc"></i> {{number_format(DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc, 8,'','')}}</td>
                        @if((($investment->amount * $coinbtc) * $btc) > $investment->usd_total)
                        <td><span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;"><i class="fa fa-btc"></i> {{number_format((($investment->amount * $coinbtc)) - (($investment->amount * $investment->bought_at)), 5)}}</span></td>
                          <td><span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/(($investment->amount * $investment->bought_at) * $investment->btc_price_bought)) * ((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at)) * $investment->btc_price_bought), 2)}}%
                              </span></td>
                        @else
                                <td><span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">{{number_format((($investment->amount * $coinbtc)) - (($investment->amount * $investment->bought_at)), 2)}}$</span></td>
                          <td><span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/(($investment->amount * $investment->bought_at) * $investment->btc_price_bought)) * ((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at)) * $investment->btc_price_bought), 2)}}%
                              </span></td>
                        @endif
                                @if($investment->bittrex_id != null)
                                <td>{{$investment->date}}</td>
                                @else
                                <td>{{$investment->date_bought}}</td>
                                @endif
                            </tr>
                            @endif
                              @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>


                  <div class="card card-data-tables sold-table-wrapper">
                      <br>
                    <header class="card-heading">
                        <h1>Sold Investments</h1>
                      <small class="dataTables_info">
                        </small>

                      <div class="card-search">
                        <div id="sold_wrapper" class="form-group label-floating is-empty">
                          <i class="zmdi zmdi-search search-icon-left"></i>
                          <input type="text" class="form-control filter-input" placeholder="Filter Products..." autocomplete="off">
                          <a href="javascript:void(0)" class="close-search" data-card-search="close" data-toggle="tooltip" data-placement="top" title="Close"><i class="zmdi zmdi-close"></i></a>
                        </div>
                      </div>
                      <ul class="card-actions icons right-top">
                        <li id="deleteItems" style="display: none;">
                          <span class="label label-info pull-left m-t-5 m-r-10 text-white"></span>
                          <a href="javascript:void(0)" id="confirmDelete" data-toggle="tooltip" data-placement="top" data-original-title="Delete Product(s)">
                            <i class="zmdi zmdi-delete"></i>
                          </a>
                        </li>
                        <li>
                          <a href="javascript:void(0)" data-card-search="open" data-toggle="tooltip" data-placement="top" data-original-title="Filter Products">
                            <i class="zmdi zmdi-filter-list"></i>
                          </a>
                        </li>
                        <li class="dropdown" data-toggle="tooltip" data-placement="top" data-original-title="Show Entries">
                          <a href="javascript:void(0)" data-toggle="dropdown">
                            <i class="zmdi zmdi-more-vert"></i>
                          </a>
                          <div id="dataTablesLength">
                          </div>
                        </li>
                      </ul>
                    </header>
                    <div class="card-body p-0">
                      <div class="alert alert-info m-20 hidden-md hidden-lg" role="alert">
                        <p>
                          Heads up! You can Swipe table Left to Right on Mobile devices.
                        </p>
                      </div>
                      <div class="table-responsive">
                        <table id="soldTable" class="mdl-data-table sold-table m-t-30" cellspacing="0" width="100%">
                          <thead>
                            <tr>
                              <th class="col-xs-2">Currency</th>
                              <th class="col-xs-2">Type</th>
                              <th class="col-xs-2">Amount</th>
                              <th class="col-xs-2">Bought for in BTC</th>
                              <th class="col-xs-2">Sold for in BTC</th>
                              <th class="col-xs-2">Profit in BTC</th>
                              <th class="col-xs-2">Change in %</th>
                              <th class="col-xs-2">Date Sold</th>
                            </tr>
                          </thead>
                          <tbody>
                              @foreach($investments as $investment)
                              @if($investment->sold_at != null)

                            <tr>
                                @if($investment->crypto == "SJCX")
                                <td><img style="width:10%" src="/icons/storj.png"/>                                         {{$investment->crypto}}</td>
                                @elseif($investment->crypto == "XLM")
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/stellar.png"/>                                         {{$investment->crypto}}</td>                                                      @elseif($investment->crypto == "GNT")
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/golem-network-tokens.png"/>                                         {{$investment->crypto}}</td>
                                @else
                                <td><img style="width:10%" src="https://files.coinmarketcap.com/static/img/coins/16x16/{{strtolower(DB::table('cryptos')->where('symbol', $investment->crypto)->first()->name)}}.png"/>                                         {{$investment->crypto}}</td>
                                @endif
                                <td>Sell</td>
                                <td>{{$investment->amount}}</td>
                                <td><i class="fa fa-btc"></i> {{$investment->amount * $investment->bought_at, 8}}</td>
                                <td><i class="fa fa-btc"></i> {{$investment->amount * $investment->sold_at, 8}}</td>
                        @if($investment->sold_for > $investment->usd_total)
                        <td><span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;"><i class="fa fa-btc"></i> {{number_format(($investment->amount * $investment->sold_at) - ($investment->amount * $investment->bought_at), 8)}}</span></td>
                          <td><span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/$investment->usd_total) * ($investment->sold_for - $investment->usd_total), 2)}}%
                              </span></td>
                        @else
                                <td><span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;"><i class="fa fa-btc"></i> {{number_format(($investment->amount * $investment->sold_at) - ($investment->amount * $investment->bought_at), 8)}}</span></td>
                          <td><span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/$investment->usd_total) * ($investment->sold_for - $investment->usd_total), 2)}}%
                              </span></td>
                        @endif
                                @if($investment->bittrex_id != null)
                                <td>{{$investment->date}}</td>
                                @else
                                <td>{{$investment->date_sold}}</td>
                                @endif
                            </tr>
                            @endif
                              @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  @endif
                                                                    </div>
                                                                </div>


																<div class="tab-pane fadeIn active" id="profile-timeline">
                                                                    <div class="row">
                                                                @foreach($minings as $mining)
                                                                    <?php
                                                                        if(Auth::user()->api == "coinmarketcap"){
                                                                            if(DB::table('cryptos')->where('symbol', $mining->crypto)->first()){
                                                                            $coinbtc = DB::table('cryptos')->where('symbol', $mining->crypto)->first()->price_btc;
                                                                            } else {
                                                                            $coinbtc = DB::table('polos')->where('symbol', $mining->crypto)->first()->price_btc;
                                                                            }
                                                                        } elseif(Auth::user()->api == "poloniex"){
                                                                            if(DB::table('polos')->where('symbol', $mining->crypto)->first()){
                                                                            $coinbtc = DB::table('polos')->where('symbol', $mining->crypto)->first()->price_btc;
                                                                            } else {
                                                                            $coinbtc = DB::table('cryptos')->where('symbol', $mining->crypto)->first()->price_btc;
                                                                            }
                                                                        }

                                                                            else {
                                                                            if(DB::table('bittrexes')->where('symbol', $mining->crypto)->first()){
                                                                                $coinbtc = DB::table('bittrexes')->where('symbol', $mining->crypto)->first()->price_btc;
                                                                            } else {
                                                                                $coinbtc = DB::table('cryptos')->where('symbol', $mining->crypto)->first()->price_btc;
                                                                            }
                                                                        }

                                                                    ?>

                  <figure class="col-xs-12 col-sm-4 col-md-4">
                    <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                        <header class="card-heading">
                                                                                                    @if($mining->bittrex_id != "")
                                                                                                      <ul class="card-actions icons left-top">
                                                                                                        <li>
                                                                                                          <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified Investment">verified_user</i>
                                                                                                          </li>
                                                                                                        </ul>
                                                                                                      <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($mining->date))}}</span></li>
                                                                                                        </ul>
                                                                                                    @else
                                                                                                      <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($mining->date_bought))}}</span></li>
                                                                                                        </ul>
                                                                                                    @endif
                          <ul class="card-actions icons right-top">
                            <li class="dropdown">
                              <a href="#" data-toggle="dropdown" aria-expanded="false">
                                <i class="zmdi zmdi-more-vert"></i>
                              </a>
                              <ul class="dropdown-menu btn-primary dropdown-menu-right">

                                <li>
                                  <a href="/coins/remove/{{$mining->id}}">Delete</a>
                                </li>
                              </ul>
                            </li>
                          </ul>
                        </header>
                      <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                          <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$mining->crypto}}.png" itemprop="thumbnail" alt="Image description">
                      </div>
                      <div class="card-body">
                        @if(Auth::user()->getCurrency() == "USD")
                        <h4 class="card-title text-center">${{number_format(($mining->amount * $coinbtc * $btc),2)}}</h4>
                        <p class="text-center" style="font-size:11px;">({{$mining->amount}} {{$mining->crypto}} Mined)</p>

                        <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">${{number_format((($mining->amount * $coinbtc) * $btc) - (($mining->amount * $mining->bought_at) * $mining->btc_price_bought), 2)}}</span>
                          <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{DB::table('cryptos')->where('symbol', $mining->crypto)->first()->percent_change_24h}}%
                          </span>

                          <hr style="margin-top:40px;">
                          <div class="usd">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left">$0</span>
                          <span style="float:right">${{number_format(($mining->amount * $coinbtc) * $btc, 2)}}</span>
                          <br>
                          <span style="float:left">$0</span>
                          <span style="float:right">${{number_format($coinbtc * $btc,5)}}</span>
                          <br>
                          </div>
                        @elseif(Auth::user()->getCurrency() == "BTC")
                        <h4 class="card-title text-center"><i class="fa fa-btc"></i> {{number_format(($mining->amount * $coinbtc),2)}}</h4>
                        <p class="text-center" style="font-size:11px;">({{$mining->amount}} {{$mining->crypto}} Mined)</p>

                        <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;"><i class="fa fa-btc"></i> {{number_format((($mining->amount * $coinbtc)) - (($mining->amount * $mining->bought_at)), 5)}}</span>
                          <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{DB::table('cryptos')->where('symbol', $mining->crypto)->first()->percent_change_24h}}%
                          </span>

                          <hr style="margin-top:40px;">
                          <div class="usd">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left"><i class="fa fa-btc"></i> 0</span>
                          <span style="float:right"><i class="fa fa-btc"></i> {{number_format(($mining->amount * $coinbtc), 5)}}</span>
                          <br>
                          <span style="float:left"><i class="fa fa-btc"></i> 0</span>
                          <span style="float:right"><i class="fa fa-btc"></i> {{number_format($coinbtc,8)}}</span>
                              <br>
                           </div>
                        @endif
                      </div>
                    </div>
                  </figure>
                                                                        @endforeach








                    @foreach($investments as $investment)
                    @if($investment->sold_at == null)
                    <?php
                        if(Auth::user()->api == "coinmarketcap"){
                            if(DB::table('cryptos')->where('symbol', $investment->crypto)->first()){
                            $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                            } else {
                            $coinbtc = DB::table('polos')->where('symbol', $investment->crypto)->first()->price_btc;
                            }
                        } elseif(Auth::user()->api == "poloniex"){
                            if(DB::table('polos')->where('symbol', $investment->crypto)->first()){
                            $coinbtc = DB::table('polos')->where('symbol', $investment->crypto)->first()->price_btc;
                            } else {
                            $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                            }
                        }

                            else {
                            if(DB::table('bittrexes')->where('symbol', $investment->crypto)->first()){
                                $coinbtc = DB::table('bittrexes')->where('symbol', $investment->crypto)->first()->price_btc;
                            } else {
                                $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                            }
                        }

                    ?>

                @if(Auth::user()->getCurrency() == "USD")
                  <figure class="col-xs-12 col-sm-4 col-md-4">
                    <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                        <header class="card-heading">
                                                                                                    @if($investment->bittrex_id != "")
                                                                                                      <ul class="card-actions icons left-top">
                                                                                                        <li>
                                                                                                          <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified Investment">verified_user</i>
                                                                                                          </li>
                                                                                                        </ul>
                                                                                                      <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date))}}</span></li>
                                                                                                        </ul>
                                                                                                    @else
                                                                                                      <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_bought))}}</span></li>
                                                                                                        </ul>
                                                                                                    @endif
                          <ul class="card-actions icons right-top">
                            <li class="dropdown">
                              <a href="#" data-toggle="dropdown" aria-expanded="false">
                                <i class="zmdi zmdi-more-vert"></i>
                              </a>
                              <ul class="dropdown-menu btn-primary dropdown-menu-right">
                                @if($investment->bittrex_id == "")
                                <li>
                                  <a href="javascript:void(0)" class="edit-coin" id="{{$investment->id}}" data-toggle="modal" data-target="#edit_modal" >Edit</a>
                                </li>
                                <li>
                                  <a href="javascript:void(0)" class="sell-coin" id="{{$investment->id}}" data-toggle="modal" data-target="#sold_modal">Mark as Sold</a>
                                </li>
                                @endif

                                <li>
                                  <a href="/coins/remove/{{$investment->id}}">Delete</a>
                                </li>
                              </ul>
                            </li>
                          </ul>
                        </header>
                      <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                          <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->crypto}}.png" itemprop="thumbnail" alt="Image description">
                      </div>
                      <div class="card-body">
                        <h4 class="card-title text-center">${{number_format(($investment->amount * $coinbtc * $btc),2)}}</h4>
                        <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->crypto}})</p>

                        @if((($investment->amount * $coinbtc) * $btc) > $investment->usd_total)
                        <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">${{number_format((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at) * $investment->btc_price_bought), 2)}}</span>
                          <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/(($investment->amount * $investment->bought_at) * $investment->btc_price_bought)) * ((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at)) * $investment->btc_price_bought), 2)}}%
                          </span>

                        @else
                        <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">{{number_format((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at) * $investment->btc_price_bought), 2)}}$</span>
                          <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/(($investment->amount * $investment->bought_at) * $investment->btc_price_bought)) * ((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at)) * $investment->btc_price_bought), 2)}}%
                          </span>
                        @endif
                          <hr style="margin-top:40px;">
                          <div class="usd">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left">${{number_format(($investment->amount * $investment->bought_at) * $investment->btc_price_bought, 2)}}</span>
                          <span style="float:right">${{number_format(($investment->amount * $coinbtc) * $btc, 2)}}</span>
                          <br>
                          <span style="float:left">${{number_format($investment->bought_at * $investment->btc_price_bought,5)}}</span>
                          <span style="float:right">${{number_format($coinbtc * $btc,5)}}</span>
                          <br>
                          </div>
                          <div class="btc" style="display:none;">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left">{{number_format(($investment->amount * $investment->bought_at), 5)}} <i class="fa fa-btc"></i></span>
                          <span style="float:right">{{number_format(($investment->amount * $coinbtc), 5)}} <i class="fa fa-btc"></i></span>
                          <br>
                          <span style="float:left">{{number_format($investment->bought_at,8)}} <i class="fa fa-btc"></i></span>
                          <span style="float:right">{{number_format($coinbtc,8)}} <i class="fa fa-btc"></i></span>
                          <br>
                          </div>
                      </div>
                    </div>
                  </figure>
                @elseif(Auth::user()->getCurrency() == "BTC")
                  <figure class="col-xs-12 col-sm-4 col-md-4">
                    <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                        <header class="card-heading">
                                                                                                    @if($investment->bittrex_id != "")
                                                                                                      <ul class="card-actions icons left-top">
                                                                                                        <li>
                                                                                                          <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified Investment">verified_user</i>
                                                                                                          </li>
                                                                                                        </ul>
                                                                                                      <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date))}}</span></li>
                                                                                                        </ul>
                                                                                                    @else
                                                                                                      <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_bought))}}</span></li>
                                                                                                        </ul>
                                                                                                    @endif
                          <ul class="card-actions icons right-top">
                            <li class="dropdown">
                              <a href="#" data-toggle="dropdown" aria-expanded="false">
                                <i class="zmdi zmdi-more-vert"></i>
                              </a>
                              <ul class="dropdown-menu btn-primary dropdown-menu-right">
                                @if($investment->bittrex_id == "")
                                <li>
                                  <a href="javascript:void(0)" class="edit-coin" id="{{$investment->id}}" data-toggle="modal" data-target="#edit_modal" >Edit</a>
                                </li>
                                <li>
                                  <a href="javascript:void(0)" class="sell-coin" id="{{$investment->id}}" data-toggle="modal" data-target="#sold_modal">Mark as Sold</a>
                                </li>
                                @endif

                                <li>
                                  <a href="/coins/remove/{{$investment->id}}">Delete</a>
                                </li>
                              </ul>
                            </li>
                          </ul>
                        </header>
                      <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                          <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->crypto}}.png" itemprop="thumbnail" alt="Image description">
                      </div>
                      <div class="card-body">
                        <h4 class="card-title text-center"><i class="fa fa-btc"></i> {{number_format(($investment->amount * $coinbtc),5)}}</h4>
                        <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->crypto}})</p>

                        @if((($investment->amount * $coinbtc) * $btc) > $investment->usd_total)
                        <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;"><i class="fa fa-btc"></i> {{number_format((($investment->amount * $coinbtc)) - (($investment->amount * $investment->bought_at)), 5)}}</span>
                          <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/(($investment->amount * $investment->bought_at) * $investment->btc_price_bought)) * ((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at)) * $investment->btc_price_bought), 2)}}%
                          </span>

                        @else
                        <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;"><i class="fa fa-btc"></i> {{number_format((($investment->amount * $coinbtc)) - (($investment->amount * $investment->bought_at)), 5)}}</span>
                          <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/(($investment->amount * $investment->bought_at) * $investment->btc_price_bought)) * ((($investment->amount * $coinbtc) * $btc) - (($investment->amount * $investment->bought_at)) * $investment->btc_price_bought), 2)}}%
                          </span>
                        @endif
                          <hr style="margin-top:40px;">
                          <div class="usd">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left"><i class="fa fa-btc"></i> {{number_format(($investment->amount * $investment->bought_at), 5)}}</span>
                          <span style="float:right"><i class="fa fa-btc"></i> {{number_format(($investment->amount * $coinbtc), 5)}}</span>
                          <br>
                          <span style="float:left"><i class="fa fa-btc"></i> {{number_format($investment->bought_at,8)}}</span>
                          <span style="float:right"><i class="fa fa-btc"></i> {{number_format($coinbtc,8)}}</span>
                          <br>
                          </div>
                          <div class="btc" style="display:none;">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left">{{number_format(($investment->amount * $investment->bought_at), 5)}} <i class="fa fa-btc"></i></span>
                          <span style="float:right">{{number_format(($investment->amount * $coinbtc), 5)}} <i class="fa fa-btc"></i></span>
                          <br>
                          <span style="float:left">{{number_format($investment->bought_at,8)}} <i class="fa fa-btc"></i></span>
                          <span style="float:right">{{number_format($coinbtc,8)}} <i class="fa fa-btc"></i></span>
                          <br>
                          </div>
                      </div>
                    </div>
                  </figure>

                @endif




                                                                        @endif
                                                                        @endforeach
                                                                    </div>
                                                                </div>

                                                                <div class="tab-pane fadeIn" id="profile-about">
                                                                <div class="row">
                                            @foreach($investments as $investment)
                                                                @if($investment->sold_at != null)


                  @if(Auth::user()->getCurrency() == "USD")
                  <figure class="col-xs-12 col-sm-4 col-md-4">
                    <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                        <header class="card-heading">
                                                                                                    @if($investment->bittrex_id != "")
                                                                                                      <ul class="card-actions icons left-top">
                                                                                                        <li>
                                                                                                          <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified Investment">verified_user</i>
                                                                                                          </li>
                                                                                                        </ul>
                                                                                                      <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date))}}</span></li>
                                                                                                        </ul>
                                                                                                    @else
                                                                                                      <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                                                                                        </ul>
                                                                                                    @endif
                          <ul class="card-actions icons" style="width:90%">
                            <li style="margin:0 auto;display:block;width:35%;margin-top:-10px">
                              <span class="label label-success text-center" style="color:white;">Investment sold</span></li>
                            </ul>
                          <ul class="card-actions icons right-top">
                            <li class="dropdown">
                              <a href="#" data-toggle="dropdown" aria-expanded="false">
                                <i class="zmdi zmdi-more-vert"></i>
                              </a>
                              <ul class="dropdown-menu btn-primary dropdown-menu-right">
                                <li>
                                  <a href="/coins/remove/{{$investment->id}}">Delete</a>
                                </li>
                              </ul>
                            </li>
                          </ul>
                        </header>
                      <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;">
                          <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->crypto}}.png" itemprop="thumbnail" alt="Image description">
                      </div>

                      <div class="card-body">
                        <h4 class="card-title text-center">{{number_format($investment->sold_for,2)}}$</h4>
                        <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->crypto}})</p>

                        @if($investment->sold_for > $investment->usd_total)

                        @if($investment->sale_id == null)
                        <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">${{number_format($investment->sold_for - $investment->usd_total, 2)}}</span>
                        @else
                        <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">${{number_format($investment->sold_for - $investment->usd_total, 2)}}</span>
                        @endif

                          <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/$investment->usd_total) * ($investment->sold_for - $investment->usd_total), 2)}}%
                          </span>
                        @else


                        <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;">${{number_format($investment->sold_for - $investment->usd_total, 2)}}</span>
                          <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/$investment->usd_total) * ($investment->sold_for - $investment->usd_total), 2)}}%
                          </span>
                        @endif
                          <hr style="margin-top:40px;">
                          <div class="usd">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left">${{number_format($investment->usd_total, 2)}}</span>
                          <span style="float:right">${{number_format($investment->sold_for, 2)}}</span>
                          <br>
                          <span style="float:left">${{number_format($investment->bought_at * $investment->btc_price_bought,5)}}</span>
                          <span style="float:right">${{number_format($investment->sold_for/$investment->amount,5)}}</span>
                          <br>
                          </div>
                          <div class="btc" style="display:none;">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left">{{number_format(($investment->amount * $investment->bought_at), 5)}} <i class="fa fa-btc"></i></span>
                          <span style="float:right">{{number_format($investment->amount * $investment->sold_at, 5)}} <i class="fa fa-btc"></i></span>
                          <br>
                          <span style="float:left">{{number_format($investment->bought_at,8)}} <i class="fa fa-btc"></i></span>
                          <span style="float:right">{{number_format($investment->sold_at,8)}} <i class="fa fa-btc"></i></span>
                          <br>
                          </div>


                      </div>
                    </div>
                  </figure>
                  @elseif(Auth::user()->getCurrency() == "BTC")
                  <figure class="col-xs-12 col-sm-4 col-md-4">
                    <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                        <header class="card-heading">
                                                                                                    @if($investment->bittrex_id != "")
                                                                                                      <ul class="card-actions icons left-top">
                                                                                                        <li>
                                                                                                          <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified Investment">verified_user</i>
                                                                                                          </li>
                                                                                                        </ul>
                                                                                                      <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date))}}</span></li>
                                                                                                        </ul>
                                                                                                    @else
                                                                                                      <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                                                                                        </ul>
                                                                                                    @endif
                          <ul class="card-actions icons" style="width:90%">
                            <li style="margin:0 auto;display:block;width:35%;margin-top:-10px">
                              <span class="label label-success text-center" style="color:white;">Investment sold</span></li>
                            </ul>
                          <ul class="card-actions icons right-top">
                            <li class="dropdown">
                              <a href="#" data-toggle="dropdown" aria-expanded="false">
                                <i class="zmdi zmdi-more-vert"></i>
                              </a>
                              <ul class="dropdown-menu btn-primary dropdown-menu-right">
                                <li>
                                  <a href="/coins/remove/{{$investment->id}}">Delete</a>
                                </li>
                              </ul>
                            </li>
                          </ul>
                        </header>
                      <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;">
                          <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->crypto}}.png" itemprop="thumbnail" alt="Image description">
                      </div>

                      <div class="card-body">
                        <h4 class="card-title text-center"><i class="fa fa-btc"></i> {{number_format($investment->sold_at * $investment->amount,5)}}</h4>
                        <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->crypto}})</p>

                        @if($investment->sold_for > $investment->usd_total)

                        @if($investment->sale_id == null)
                        <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;"><i class="fa fa-btc"></i> {{number_format(($investment->sold_at * $investment->amount) - ($investment->bought_at * $investment->amount), 5)}}</span>
                        @else
                        <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;"><i class="fa fa-btc"></i> {{number_format(($investment->sold_at * $investment->amount) - ($investment->bought_at * $investment->amount), 5)}}</span>
                          @endif

                          <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/$investment->usd_total) * ($investment->sold_for - $investment->usd_total), 2)}}%
                          </span>
                        @else


                        <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;"><i class="fa fa-btc"></i> {{number_format(($investment->sold_at * $investment->amount) - ($investment->bought_at * $investment->amount), 5)}}</span>
                          <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                          {{number_format((100/$investment->usd_total) * ($investment->sold_for - $investment->usd_total), 2)}}%
                          </span>
                        @endif
                          <hr style="margin-top:40px;">
                          <div class="usd">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left"><i class="fa fa-btc"></i> {{number_format($investment->amount * $investment->bought_at, 5)}}</span>
                          <span style="float:right"><i class="fa fa-btc"></i> {{number_format($investment->amount * $investment->sold_at, 5)}}</span>
                          <br>
                          <span style="float:left"><i class="fa fa-btc"></i> {{number_format($investment->bought_at,8)}}</span>
                          <span style="float:right"><i class="fa fa-btc"></i> {{number_format($investment->sold_at,8)}}</span>
                          <br>
                          </div>
                          <div class="btc" style="display:none;">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left">{{number_format(($investment->amount * $investment->bought_at), 5)}} <i class="fa fa-btc"></i></span>
                          <span style="float:right">{{number_format($investment->amount * $investment->sold_at, 5)}} <i class="fa fa-btc"></i></span>
                          <br>
                          <span style="float:left">{{number_format($investment->bought_at,8)}} <i class="fa fa-btc"></i></span>
                          <span style="float:right">{{number_format($investment->sold_at,8)}} <i class="fa fa-btc"></i></span>
                          <br>
                          </div>


                      </div>
                    </div>
                  </figure>
                  @endif


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
                    <label class="control-label">BTC paid per coin</label>
                    <input type="text" class="form-control" name="bought_at" id="bought_at">
                  </div>
                  <div class="form-group">
                    <label class="control-label">USD paid per coin (Not required)</label>
                    <input type="text" class="form-control" name="bought_at_usd" id="bought_at_usd">
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
						<form id="sell-form" role="form" method="post" action="/coins/sell/">
                            {{ csrf_field() }}

                          <div class="form-group">
                            <label class="control-label">BTC paid per coin</label>
                            <input type="text" class="form-control" name="sold_at" id="sold_at">
                          </div>
                          <div class="form-group">
                            <label class="control-label">USD paid per coin (Not required)</label>
                            <input type="text" class="form-control" name="sold_at_usd" id="sold_at_usd">
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
						<form id="form-horizontal" role="form" method="post" action="/coins/addmining">
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
						<form id="sell-form" role="form" method="post" action="/coins/sellMultiple">
                            {{ csrf_field() }}
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Coin/Currency</label>
                                    <input type="text" class="form-control typeahead" id="autocomplete_states2" autocomplete="off" placeholder="Enter a coin" id="coin" name="coin" required/>
                              </div>
                          <div class="form-group">
                            <label class="control-label">BTC paid per coin</label>
                            <input type="text" class="form-control" name="sold_at" id="sold_at">
                          </div>
                          <div class="form-group">
                            <label class="control-label">USD paid per coin (Not required)</label>
                            <input type="text" class="form-control" name="sold_at_usd" id="sold_at_usd">
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
                                <strong>Oh no!</strong> It seems like you have entered an invalid API key!
                              </div>
                        <div class="polo-text">
                        <p>Please wait while we import your trades.</p>
                        <p>Are you stuck here? Be sure that you are not using the same key elsewhere!</p>

                        <span id="load_trades">Importing trades  <i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><br></span>
                        <span id="load_orders" style="display:none;">Inserting buy orders... <i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><br></span>
                        <span id="load_sales" style="display:none;">Inserting sale orders... <i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><br></span>
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
<script>
$(".edit-coin").click(function(){
    $.ajax({
        dataType: "json",
        url: '/coins/get/'+$(this).attr('id'),
        success: function(data){
        $("#bought_at").val(data["bought_at"]);
        $("#bought_at_usd").val(data["bought_at_usd"]);
        $("#amount").val(data["amount"]);
        $("#md_input_date").val(data["date_bought"]);
        $("#edit-form").attr('action', '/coins/edit/'+data["id"]);
    }
    });

});

    $(".sell-coin").click(function(){
        $("#sell-form").attr('action', '/coins/sell/'+$(this).attr('id'));
    });




    $("#import_polo").click(function(){

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        $("#import_modal").modal('toggle').after(function(){

                var jqxhr = $.post( "/api/polo/1", {_token: CSRF_TOKEN }, function() {
                  $("#load_trades").html('Successfully imported trades.<br>');
                  $("#load_orders").css('display', '');
                    var test = $.post( "/api/polo/2", {_token: CSRF_TOKEN }, function() {
                      $("#load_orders").html('Successfully inserted buy orders.<br>');
                      $("#load_sales").css('display', '');
                        var test2 = $.post( "/api/polo/3", {_token: CSRF_TOKEN }, function() {
                          $("#load_sales").html('Successfully inserted sale orders.<br>');
                          location.reload();
                        });
                    });
                }).fail(function(){
                    $(".polo-text").css('display', 'none');
                    $(".polo-error").css('display', '');
                });


        });

    });


    $("#import_bittrex").click(function(){

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        $("#import_modal_bittrex").modal('toggle').after(function(){

                var jqxhr2 = $.post( "/api/bittrex/1", {_token: CSRF_TOKEN }, function() {
                  $("#load_trades2").html('Successfully imported trades.<br>');
                  $("#load_orders2").css('display', '');
                    var test2 = $.post( "/api/bittrex/2", {_token: CSRF_TOKEN }, function() {
                      $("#load_orders2").html('Successfully inserted buy orders.<br>');
                      $("#load_sales2").css('display', '');
                        var test22 = $.post( "/api/bittrex/3", {_token: CSRF_TOKEN }, function() {
                          $("#load_sales2").html('Successfully inserted sale orders.<br>');
                          location.reload();
                        });
                    });
                }).fail(function(){
                    $(".bittrex-text").css('display', 'none');
                    $(".bittrex-error").css('display', '');
                });


        });

    });


    $("#reset-data").click(function(){
        swal({
          title: "Are you sure?",
          text: "You will not be able to undo a reset.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
        },
        function(){
            window.location.replace("/coins/reset/");
        });
            });



   //Charts lets go
@php
    $stats = DB::table('stats')->where('userid', Auth::user()->id)->first();
    $investments = DB::table('investments')->where([['userid', '=', Auth::user()->id], ['sold_at', '=', null]])->get();
@endphp
@if($stats)
 var ctx = document.getElementById("profit7").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ["{{date("F j, Y", time() - 60 * 60 * 168)}}", "{{date("F j, Y", time() - 60 * 60 * 144)}}", "{{date("F j, Y", time() - 60 * 60 * 120)}}", "{{date("F j, Y", time() - 60 * 60 * 96)}}", "{{date("F j, Y", time() - 60 * 60 * 72)}}", "{{date("F j, Y", time() - 60 * 60 * 48)}}", "{{date("F j, Y", time() - 60 * 60 * 24)}}"],
    datasets: [{
      label: 'Profit in $',
      data: [{{$stats->profit7}}, {{$stats->profit6}}, {{$stats->profit5}}, {{$stats->profit4}}, {{$stats->profit3}}, {{$stats->profit2}}, {{$stats->profit1}}],
    backgroundColor: "rgba(88, 103, 195,0.4)"
    }]
  },
    options: {
    scales: {
        yAxes: [{
            ticks: {
                beginAtZero: true
            }
        }]
    }
}
});
@endif

var ctx2 = document.getElementById("holding").getContext('2d');
var myChart2 = new Chart(ctx2, {
  type: 'pie',
  data: {
    labels: [ @foreach($investments as $investment) "{{$investment->crypto}}", @endforeach ],
    datasets: [{
      backgroundColor: [
        @foreach($investments as $investment) @php echo '"'.sprintf('#%06X', mt_rand(0, 0xFFFFFF)).'",' @endphp @endforeach
      ],
      data: [@foreach($investments as $investment) "{{$investment->usd_total}}", @endforeach]
    }]
  }
});

</script>


@endsection
