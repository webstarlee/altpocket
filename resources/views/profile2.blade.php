@extends('layouts.app')

@section('title')
{{$user->username}}s Profile
@endsection

@section('css')
<link href="/css/slim.min.css" rel="stylesheet" type="text/css">
<style>
        .slim {
            height:200px;
            width:200px;
            margin:0 auto;
        }
    .card.card-comment[data-timeline=comment]:before {
        content: "\f25b"!important;
        background: #90aeb4;
        border: 4px solid #e1e9ee;
    }
</style>

@endsection

@section('content')

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.9&appId=206742916502535";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>



<?php
use Jenssegers\Agent\Agent;

$agent = new Agent();
?>
				<div id="content_wrapper" class="card-overlay">
                    @if($user->header == "default")
					<div id="header_wrapper" class="header-xl  profile-header" style="background-image:-webkit-gradient(linear,left top,left bottom,color-stop(0,transparent),color-stop(30%,transparent),color-stop(100%,rgba(0,0,0,.45))),url(/assets/img/headers/header-lg-03.jpg)!important;background-position:0 90%!important;">
						<ul class="card-actions fab-action right">
							<li>
								<button class="btn btn-primary btn-fab" data-toggle="modal" data-target="#comment_modal">
									<i class="zmdi zmdi-comment-alt-text"></i>
								</button>
							</li>
						</ul>
					</div>
                    @else
					<div id="header_wrapper" class="header-xl  profile-header" style="background-image:-webkit-gradient(linear,left top,left bottom,color-stop(0,transparent),color-stop(30%,transparent),color-stop(100%,rgba(0,0,0,.45))),url(/uploads/headers/{{$user->id}}/{{$user->header}})!important;background-position:0 50%!important;">
						<ul class="card-actions fab-action right">
							<li>
								<button class="btn btn-primary btn-fab" data-toggle="modal" data-target="#comment_modal">
									<i class="zmdi zmdi-comment-alt-text"></i>
								</button>
							</li>
						</ul>
                        					</div>
                    @endif
					<div id="content" class="container-fluid">
						<div class="row">
							<div class="col-xs-12">
								<div class="card card-transparent">
									<div class="card-body wrapper">
										<div class="row">
											<div class="col-md-12 col-lg-3">
												<div class="card type--profile">
													<header class="card-heading">
                                                        @if($user->avatar == "default.jpg")
														<img src="/assets/img/logo.png" alt="" class="img-circle">
                                                        @else
														<img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="" class="img-circle">
                                                        @endif
                                                        @if(Auth::user())
                                                        @if(Auth::user()->username == $user->username)
														<ul class="card-actions icons right-top">
															<li class="dropdown">
																<a href="javascript:void(0)" data-toggle="dropdown">
																	<i class="zmdi zmdi-more-vert"></i>
																</a>
																<ul class="dropdown-menu dropdown-menu-right btn-primary">
																	<li>
																		<a href="javascript:void(0)" data-toggle="modal" data-target="#edit_profile">Edit Profile</a>
																	</li>
																	<li>
																		<a href="javascript:void(0)" data-toggle="modal" data-target="#change_password">Change Password</a>
																	</li>         
																	<li>
																		<a href="javascript:void(0)" data-toggle="modal" data-target="#edit_avatar">Change Avatar</a>
																	</li>
																	<li>
																		<a href="javascript:void(0)" data-toggle="modal" data-target="#edit_header">Change Header</a>
																	</li>
																	<li>
																		<a href="javascript:void(0)" data-toggle="modal" data-target="#settings">Settings</a>
																	</li>
																	<li>
																		<a href="javascript:void(0)" data-toggle="modal" data-target="#import">Import Investments</a>
																	</li>
																</ul>
															</li>
														</ul>
                                                        @endif
                                                        @endif
                                                        <ul class="card-actions icons left-top">
                                                        <li>
                                                            <a style="color:#55acee;" data-toggle="tooltip" data-placement="top" title="Share profile on twitter." onclick="window.open('https://twitter.com/intent/tweet?text=https://altpocket.io/user/{{$user->username}}', 'newwindow', 'width=600, height=300');" href="#"><i class="zmdi zmdi-twitter"></i><div class="ripple-container"></div></a>
                                                            </li>
                                                            <li>
<div  data-toggle="tooltip" data-placement="top" title="Share profile on facebook."  data-href="https://altpocket.io/user/{{$user->username}}" data-layout="button_count" data-size="small" data-mobile-iframe="true" title="Share profile on facebook."><a style="color:#3b5998;" class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Faltpocket.io%2Fuser2%2F{{$user->username}}%23&amp;src=sdkpreparse"><i class="zmdi zmdi-facebook"></i></a><div class="ripple-container"></div></div>
                                                            </li>
                                                        </ul>
                                                        
                                                        
													</header>
													<div class="card-body">
														<h3 class="name" style="color:{{$user->groupColor()}};{{$user->groupStyle()}}"   >{{$user->username}}
                                                            
                                                            
                                                            
                                                            
                                                            @if($user->hasVerified)<i class="material-icons" style="color:#5ecbf7;cursor:pointer;font-size:15px;" data-toggle="tooltip" title="User has verified investments.">verified_user</i>
                                                        @endif    
                                                        </h3>
														<span class="title">{{$user->bio}}</span>
														<a href="/user/{{$user->username}}/impressed" type="button" class="btn btn-primary btn-round">Impressed</a>
													</div>
													<footer class="card-footer border-top">
														<div class="row row p-t-10 p-b-10">
															<div class="col-xs-4"><span class="count">
                                                                
                                                                @if($user->invested > 999 && 100000 > $user->invested)
                                                                ${{number_format($user->invested)}}
                                                                @elseif($user->invested > 99999)
                                                                ${{floor($user->invested / 1000)}}K
                                                                @else
                                                                ${{number_format($user->invested, 2)}}
                                                                @endif
                                            
                                                            </span><span>Invested</span></div>
															<div class="col-xs-4"><span class="count">{{$user->impressed}}</span><span>Impressed</span></div>
                                                            @if((($networth * $btc) - $user->invested) > 0)
															<div class="col-xs-4"><span class="count" style="color:#73c04d">
                                                                
                                                                @php
                                                                $profit = (($networth * $btc) - $user->invested);
                                                                @endphp
                                                                @if($profit > 999 && $profit < 100000)
                                                                ${{number_format(($networth * $btc) - $user->invested)}}
                                                                @elseif($profit > 99999)
                                                                ${{number_format(floor((($networth * $btc) - $user->invested) / 1000))}}K
                                                                @else
                                                                ${{number_format(($networth * $btc) - $user->invested, 2)}}
                                                                @endif
                                                                
                                                                
                                                                </span><span>Profit</span></div>
                                                            @else
															<div class="col-xs-4"><span class="count" style="color:#de6b6b">
                                                                @php
                                                                $profit = (($networth * $btc) - $user->invested);
                                                                @endphp
                                                                @if($profit < -999 && -1000000 < $profit)
                                                                ${{number_format(($networth * $btc) - $user->invested)}}
                                                                @else
                                                                ${{number_format(($networth * $btc) - $user->invested, 2)}}
                                                                @endif
                                                                
                                                                </span><span>Profit</span></div>
                                                            @endif
														</div>
                                                                    <hr style="border-top:1px solid #c3c3c3">
                                                                <div class="row row p-t-10 p-b-10">
                                                                    <div class="col-xs-4"><span class="count">

                                                                        @if($spent > 999 && 100000 > $spent)
                                                                        ${{number_format($spent)}}
                                                                        @elseif($spent > 99999)
                                                                        ${{floor($spent / 1000)}}K
                                                                        @else
                                                                        ${{number_format($spent, 2)}}
                                                                        @endif

                                                                    </span><span>Total Invested</span></div>
                                                                    <div class="col-xs-4">
                                                                            
                                                                        
                                                                        @if($user->groupName())
                                                                        <span class="label" style="width:100%;display:block;margin:0 auto;text-transform:none;background:{{$user->groupColor()}}">{{$user->groupName()}}</span>
                                                                        @else
                                                                        <span class="label label-info" style="width:100%;display:block;margin:0 auto;text-transform:none;">User</span>
                                                                        @endif

                                                                        <span>Rank</span></div>
                                                                    @if((($networth * $btc) - $spent) + $alltimesold > 0)
                                                                    <div class="col-xs-4"><span class="count" style="color:#73c04d">

                                                                        @php
                                                                        $profit = (($networth * $btc) - $spent) + $alltimesold;
                                                                        @endphp
                                                                        @if($profit > 999 && $profit < 100000)
                                                                        ${{number_format($profit)}}
                                                                        @elseif($profit > 99999)
                                                                        ${{number_format(floor(($profit / 1000)))}}K
                                                                        @else
                                                                        ${{number_format($profit, 2)}}
                                                                        @endif


                                                                        </span><span>Total Profit</span></div>
                                                                    @else
                                                                    <div class="col-xs-4"><span class="count" style="color:#de6b6b">
                                                                        @php
                                                                        $profit = (($networth * $btc) - $spent) + $alltimesold;
                                                                        @endphp
                                                                        @if($profit < -999 && -1000000 < $profit)
                                                                        ${{number_format($profit)}}
                                                                        @else
                                                                        ${{number_format($profit, 2)}}
                                                                        @endif

                                                                        </span><span>Total Profit</span></div>
                                                                    </div>
                                                                    @endif
    
													</footer>
												</div>
											</div>
											<div class="col-md-12 col-lg-8">
												<div class="card">
													<header class="card-heading p-0">
														<div class="tabpanel m-b-30">
															<ul class="nav nav-tabs nav-justified">
																<li class="active " role="presentation"><a href="#profile-timeline" data-toggle="tab" aria-expanded="true">Active Investments</a></li>
																<li role="presentation"><a href="#profile-about" data-toggle="tab" aria-expanded="true">Sold Investments</a></li>
																<li role="presentation"><a href="#profile-contacts" data-toggle="tab" aria-expanded="true">About</a></li>
                                                                @if($user->commentsOn())
																<li role="presentation"><a href="#profile-comments" data-toggle="tab" aria-expanded="true">Comments</a></li>
                                                                @endif
															</ul>
														</div>
														<div class="card-body">
															<div class="tab-content">
                                                                @if($user->commentsOn())
                                                                <div class="tab-pane fadeIn" id="profile-comments">
                                                                    @if(count($comments) > 0)
                                                                        @include('module.comment')  
                                                                    @else
                                                                    <p style="text-align:center">There area no comments on {{$user->username}}s profile yet.</p>
                                                                    @endif
                                                                </div>    
                                                                @endif
                                                                
																<div class="tab-pane fadeIn active" id="profile-timeline">
                                                                    <div class="row">
                                                                                        @foreach($investments as $investment)
                                                                                            @if($investment->sold_at == null)
                                                                                                <?php

                                                                                                        if(Auth::user()){                                                    
                                                                                                            if(Auth::user()->api == "coinmarketcap"){
                                                                                                                if(DB::table('cryptos')->where('symbol', $investment->crypto)->first()){
                                                                                                                    $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                                                                                                    $coin = DB::table('cryptos')->where('symbol', $investment->crypto)->first();  
                                                                                                                    } elseif(DB::table('polos')->where('symbol', $investment->crypto)->first()) {
                                                                                                                         $coinbtc = DB::table('polos')->where('symbol', $investment->crypto)->first()->price_btc;
                                                                                                                        $coin = DB::table('polos')->where('symbol', $investment->crypto)->first();  
                                                                                                                    } else {
                                                                                                                         $coinbtc = DB::table('bittrexes')->where('symbol', $investment->crypto)->first()->price_btc;
                                                                                                                        $coin = DB::table('bittrexes')->where('symbol', $investment->crypto)->first();  
                                                                                                                }
                                                                                                            } 
                                                                                                            elseif(Auth::user()->api == "bittrex") {
                                                                                                                if(DB::table('bittrexes')->where('symbol', $investment->crypto)->first()){
                                                                                                                    $coinbtc = DB::table('bittrexes')->where('symbol', $investment->crypto)->first()->price_btc;
                                                                                                                    $coin = DB::table('bittrexes')->where('symbol', $investment->crypto)->first();   
                                                                                                                } else {
                                                                                                                    $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                                                                                                    $coin = DB::table('cryptos')->where('symbol', $investment->crypto)->first();
                                                                                                                }
                                                                                                            }
                                                                                                            else {
                                                                                                                if(DB::table('polos')->where('symbol', $investment->crypto)->first()){
                                                                                                                    $coinbtc = DB::table('polos')->where('symbol', $investment->crypto)->first()->price_btc;
                                                                                                                    $coin = DB::table('polos')->where('symbol', $investment->crypto)->first();   
                                                                                                                } else {
                                                                                                                    $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                                                                                                    $coin = DB::table('cryptos')->where('symbol', $investment->crypto)->first();   
                                                                                                                }
                                                                                                            }
                                                                                                        } else {
                                                                                                            if(DB::table('cryptos')->where('symbol', $investment->crypto)->first()){
                                                                                                                $coinbtc = DB::table('cryptos')->where('symbol', $investment->crypto)->first()->price_btc;
                                                                                                                $coin = DB::table('cryptos')->where('symbol', $investment->crypto)->first();
                                                                                                            } else {
                                                                                                                $coinbtc = DB::table('polos')->where('symbol', $investment->crypto)->first()->price_btc; 
                                                                                                                $coin = DB::table('polos')->where('symbol', $investment->crypto)->first();
                                                                                                                
                                                                                                            }
                                                                                                            }


                                                                                                ?>
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
                                                                                                    @if(Auth::check() && Auth::user()->isFounder())
                                                                                                      <ul class="card-actions icons right-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">ID: {{$investment->id}}</span></li>
                                                                                                        </ul>
                                                                                                    @endif
                                                                                                </header>
                                                                                              <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                                                                  @if(!$agent->isMobile())
                                                                                                      @if(file_exists('assets/logos/'.$investment->crypto.".png"))
                                                                                                      <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->crypto}}.png" itemprop="thumbnail" alt="Image description">
                                                                                                      @else
<p style="
    text-align: center;
    font-weight: 600;
    font-size: 32px;
padding:5px;line-height:0.8em;color:letter-spacing:1px;color:darkcyan;">{{$coin->name}}</p>
                                                                                                      @endif
                                                                                                  @else
                                                                                                  <img style="max-height:80px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->crypto}}.png" itemprop="thumbnail" alt="Image description">
                                                                                                  @endif
                                                                                              </div>
                                                                                                  <?php
                                                                                                        $date = $investment->date_bought;
                                                                                                        if($date != date('Y-m-d')){
                                                                                                        $client = new \GuzzleHttp\Client();
                                                                                                        try{                                                                                                                
                                                                                                        $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                                                                                                        $response = $res->getBody();
                                                                                                        $prices = json_decode($response, true);
                                                                                                        $prevbtc = 0;
                                                                                                        foreach($prices['bpi'] as $price){
                                                                                                            $prevbtc = $price;
                                                                                                        }                                                                                                        
                                                                                                        } catch(GuzzleHttp\Exception\ClientException $e){
                                                                                                                $prevbtc = $btc;
                                                                                                            }
                                                                                                            } else {
                                                                                                            $prevbtc = $btc;
                                                                                                        }

                                                                                                  ?>
                                                                                              <div class="card-body">
                                                                                                <h4 class="card-title text-center">
                                                                                                    @php
                                                                                                    $cointotal = $investment->amount * $coinbtc * $btc;
                                                                                                    @endphp
                                                                                                    
                                                                                                    @if($cointotal > 999 && $cointotal < 999999)
                                                                                                    ${{number_format(($investment->amount * $coinbtc * $btc))}}
                                                                                                    @elseif($cointotal > 999999)
                                                                                                    ${{floor($cointotal / 1000)}}K
                                                                                                    @else
                                                                                                   ${{number_format(($investment->amount * $coinbtc * $btc),2)}}
                                                                                                    @endif
                                                                                                  
                                                                                                  
                                                                                                  
                                                                                                  </h4>
                                                                <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->crypto}})</p>
                                                                                                @if((($investment->amount * $coinbtc) * $btc) > $investment->usd_total)
                                                                                                <span class="text-center label label-success" style="color:white;display:block;margin: 0 auto;float:left;font-size:12px;">
                                                                                                    @php
                                                                                                    $profit = (($investment->amount * $coinbtc) * $btc) - $investment->usd_total;
                                                                                                    
                                                                                                    @endphp
                                                                                                    
                                                                                                    @if($profit > 999 && $profit < 100000)
                                                                                                    ${{number_format($profit)}}
                                                                                                    @elseif($profit > 100000)                             ${{floor($profit / 1000)}}K         
                                                                                                    @else
                                                                                                    ${{number_format((($investment->amount * $coinbtc) * $btc) - $investment->usd_total, 2)}}
                                                                                                    @endif
                                                                                                    
                                                                                                    
                                                                                                    
                                                                                                    </span>
                                                                                                  <span class="text-center label label-success" style="color:white;display:block;margin: 0 auto;float:right;font-size:12px;">
                                                                                                  {{number_format((100/$investment->usd_total) * ((($investment->amount * $coinbtc) * $btc) - $investment->usd_total), 2)}}%
                                                                                                  </span>
                                                                                                @else
                                                                                                <span class="text-center label label-danger" style="color:white;display:block;margin: 0 auto;float:left;font-size:12px;">{{number_format((($investment->amount * $coinbtc) * $btc) - $investment->usd_total, 2)}}$</span>
                                                                                                  <span class="text-center label label-danger" style="color:white;display:block;margin: 0 auto;float:right;font-size:12px;">
                                                                                                  {{number_format((100/$investment->usd_total) * ((($investment->amount * $coinbtc) * $btc) - $investment->usd_total), 2)}}%
                                                                                                  </span>
                                                                                                @endif
                                                                                                  <hr style="margin-top:40px;">
                                                                                                  <span style="float:left;">Before</span>
                                                                                                  <span style="float:right;">After</span>
                                                                                                  <br>
                                                                                                  <span style="float:left">
                                                                                                      @if($investment->usd_total > 999 && $investment->usd_total < 100000)
                                                                                                      ${{number_format($investment->usd_total)}}
                                                                                                      @elseif($investment->usd_total > 100000)
                                                                                                      ${{floor($investment->usd_total / 1000)}}K
                                                                                                      @else
                                                                                                      ${{number_format($investment->usd_total, 2)}}
                                                                                                      @endif
                                                                                                    
                                                                                                    </span>
                                                                                                  <span style="float:right">
                                                                                                      @php
                                                                                                        $after = ($investment->amount * $coinbtc) * $btc;
                                                                                                      @endphp
                                                                                                      @if($after > 999 && $after < 100000)
                                                                                                      ${{number_format($after)}}
                                                                                                      @elseif($after > 100000)
                                                                                                      ${{floor($after / 1000)}}K
                                                                                                      @else
                                                                                                      ${{number_format($after, 2)}}
                                                                                                      @endif
                                                                                                      </span>
                                                                                                  <br>
                                                                                                  @if(($investment->bought_at * $prevbtc) < 999)      
                                                                                                  <span style="float:left">${{number_format($investment->bought_at * $prevbtc,5)}}</span>
                                                                                                  @else
                                                                                                  <span style="float:left">${{number_format($investment->bought_at * $prevbtc)}}</span>
                                                                                                  @endif      
                                                                                                  @if(($coinbtc * $btc) < 999)      
                                                                                                  <span style="float:right">${{number_format($coinbtc * $btc,5)}}</span>
                                                                                                  @else
                                                                                                  <span style="float:right">${{number_format($coinbtc * $btc)}}</span>      
                                                                                                  @endif      
                                                                                                  <br>



                                                                                              </div>
                                                                                            </div>
                                                                                          </figure>
                                                                        @endif
                                                                        @endforeach
                                                                    
                                                                    
                                                                    </div>                
																		</div>
																		<div class="tab-pane fadeIn" id="profile-about">
                                                                    <div class="row">
                                                                                        @foreach($investments as $investment)
                                                                                            @if($investment->sold_at != null)
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
                                                                                                    @if(Auth::check() && Auth::user()->isFounder())
                                                                                                      <ul class="card-actions icons right-top" style="margin-top:-3px;margin-left:20px;">
                                                                                                          <li><span style="font-size:11px" class="text-muted">ID: {{$investment->id}}</span></li>
                                                                                                        </ul>
                                                                                                    @endif
                          <ul class="card-actions icons" style="width:85%">
                            <li style="margin:0 auto;display:block;width:30%;margin-top:-10px">
                              <span class="label label-success text-center" style="color:white;">Sold</span></li>
                            </ul>
                        </header>
                      <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;">
                                                                                                  @if(!$agent->isMobile())
                                                                                                  <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->crypto}}.png" itemprop="thumbnail" alt="Image description">
                                                                                                  @else
                                                                                                  <img style="max-height:80px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->crypto}}.png" itemprop="thumbnail" alt="Image description">
                                                                                                  @endif
                      </div>
                          <?php
                                $date = $investment->date_bought;
                                if($date != date('Y-m-d')){
                                $client = new \GuzzleHttp\Client();
                                try{
                                $res = $client->request('GET', 'http://api.coindesk.com/v1/bpi/historical/close.json?start='.$date.'&end='.$date);
                                $response = $res->getBody();
                                $prices = json_decode($response, true);
                                $prevbtc = 0;
                                foreach($prices['bpi'] as $price){
                                    $prevbtc = $price;
                                }
                                } catch(GuzzleHttp\Exception\ClientException $e){
                                    $prevbtc = $btc;
                                }
                                } else {
                                    $prevbtc = $btc;
                                }
                        
                            $btcsold = $investment->sold_for;
                          
                          ?>
                      <div class="card-body">
                        <h4 class="card-title text-center">{{number_format($btcsold,2)}}$</h4>
                                                                <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->crypto}})</p>
                          
                        @if($btcsold > (($investment->amount * $investment->bought_at) * $prevbtc))
                        <span class="text-center label label-success" style="color:white;display:block;margin: 0 auto;float:left;font-size:12px;">{{number_format($btcsold - (($investment->amount * $investment->bought_at) * $prevbtc), 2)}}$</span>
                          
                          
                          <span class="text-center label label-success" style="color:white;display:block;margin: 0 auto;float:right;font-size:12px;">
                          {{number_format((100/(($investment->amount * $investment->bought_at) * $prevbtc)) * ($investment->sold_for - (($investment->amount * $investment->bought_at)) * $prevbtc), 2)}}%
                          </span>
                        @else
                          
                          
                        <span class="text-center label label-danger" style="color:white;display:block;margin: 0 auto;float:left;font-size:12px;">{{number_format($btcsold - (($investment->amount * $investment->bought_at) * $prevbtc), 2)}}$</span>
                          <span class="text-center label label-danger" style="color:white;display:block;margin: 0 auto;float:right;font-size:12px;">
                          {{number_format((100/(($investment->amount * $investment->bought_at) * $prevbtc)) * (($btcsold) - (($investment->amount * $investment->bought_at)) * $prevbtc), 2)}}%
                          </span>
                        @endif
                          <hr style="margin-top:40px;">
                          <span style="float:left;">Before</span>
                          <span style="float:right;">After</span>
                          <br>
                          <span style="float:left">{{number_format(($investment->amount * $investment->bought_at) * $prevbtc, 2)}}$</span>
                          <span style="float:right">{{number_format($btcsold, 2)}}$</span>
                          <br>
                          <span style="float:left">{{number_format($investment->bought_at * $prevbtc,5)}}$</span>
                          <span style="float:right">{{number_format($btcsold/$investment->amount,5)}}$</span>
                          <br>
                          
                          
                          
                      </div>
                    </div>
                  </figure>
                                                                        @endif
                                                                        @endforeach
                                                                    
                                                                    
                                                                    </div> 
																		</div>
																		<div class="tab-pane fadeIn" id="profile-contacts">
																			<div class="card card-transparent m-b-0">
																				<header class="card-heading">
																					<h2 class="card-title m-t-0">About</h2>
																				</header>
																				<div class="card-body p-t-0">
																					<p>
                                                                                        {{$user->about}}
																					</p>
																				</div>
																			</div>
																			<div class="card card-transparent">
																				<header class="card-heading">
																					<h2 class="card-title">Social</h2>
																				</header>
																				<div class="card-body p-t-0">
																					<div class="p-l-30">
                                                                                        @if($user->hf_id != null)
																						<dl class="dl-horizontal">
																							<dt>Hackforums Profile</dt>
																							<dd><a href="https://hackforums.net/member.php?action=profile&uid={{$user->hf_id}}" target="_blank">https://hackforums.net/member.php?action=profile&uid={{$user->hf_id}}</a></dd>
																						</dl>
                                                                                        @endif
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
																</div>
															</div>
														</div>
													</div>
                    @if(Auth::user())
                                    @if($user->commentsOn())
												<div class="modal fade" id="edit_comment" tabindex="-1" role="dialog" aria-labelledby="edit_comment">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="card m-0">
																<header class="card-heading p-b-20">
																	<img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="" class="img-circle img-sm pull-left m-r-10">
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
																<form class="form-horizontal" action="/edit/comment/" method="post" id="edit-comment-form">
                                                                    {{ csrf_field() }}
																	<div class="form-group m-0 row is-empty">
																		<label class="sr-only">Description: </label>
																		<div class="col-md-12 p-0">
																			<textarea class="form-control" rows="5" id="edit-comment-field" name="comment" placeholder="Write a comment here.."></textarea>
																		</div>
																	</div>
																
															</div>
															<div class="modal-footer p-t-0 bg-white">
																<ul class="card-actions icons left-bottom m-b-15">
																</ul>
																<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
																<button type="submit" class="btn btn-primary">Save</button>
															</div>
                                                                </form>
														</div>
														<!-- modal-content -->
													</div>
													<!-- modal-dialog -->
												</div>
                                    
                                    
												<div class="modal fade" id="comment_modal" tabindex="-1" role="dialog" aria-labelledby="comment_modal">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="card m-0">
																<header class="card-heading p-b-20">
																	<img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="" class="img-circle img-sm pull-left m-r-10">
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
																<form class="form-horizontal" action="/comment/add/{{$user->id}}" method="post">
                                                                    {{ csrf_field() }}
																	<div class="form-group m-0 row is-empty">
																		<label class="sr-only">Description: </label>
																		<div class="col-md-12 p-0">
																			<textarea class="form-control" rows="5" name="comment" placeholder="Write a comment here.."></textarea>
																		</div>
																	</div>
																
															</div>
															<div class="modal-footer p-t-0 bg-white">
																<ul class="card-actions icons left-bottom m-b-15">
																</ul>
																<button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
																<button type="submit" class="btn btn-primary">Post</button>
															</div>
                                                                </form>
														</div>
														<!-- modal-content -->
													</div>
													<!-- modal-dialog -->
												</div>                                    
									@endif			                                    
                                    
                                    
                    @if(Auth::user()->username == $user->username)
        			<div class="modal fade" id="edit_profile" tabindex="-1" role="dialog" aria-labelledby="edit_profile">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							
							<h4 class="modal-title" id="myModalLabel-2">Edit Profile Information</h4>
							<ul class="card-actions icons right-top">
							<li>	
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
						<form id="form-horizontal" role="form" method="post" action="/user/{{Auth::user()->username}}/edit">
                           {{ csrf_field() }}
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Biography</label>
                                    <input type="text" class="form-control" id="bio" autocomplete="off" value="{{Auth::user()->bio}}" name="bio"/> 
                              </div>  
                          <div class="form-group">
                            <label class="control-label">About</label>
                            <textarea id="about" name="about" class="form-control">{{$user->about}}</textarea>
                          </div>
                          <div class="form-group">
                            <label class="control-label">Hackforums ID</label>
                            <input type="text" class="form-control" name="hf_id" id="hf_id" value="{{Auth::user()->hf_id}}">
                          </div>
                            <div class="row">
                            <div class="form-group col-md-6">
                                <div class="togglebutton m-b-15">
                                  <label>
                                    <input type="checkbox" class="toggle-info" id="public" name="public" @if(Auth::user()->public == "on") checked @endif> Public Profile
                                  </label>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="togglebutton m-b-15">
                                  <label>
                                    <input type="checkbox" class="toggle-info" id="comments" name="comments" @if(Auth::user()->comments == "on") checked @endif> Profile Comments
                                  </label>
                                </div>
                            </div>  
                             </div>
                            <button type="submit" class="btn btn-primary">Save Information</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>
                    
        			<div class="modal fade" id="edit_avatar" tabindex="-1" role="dialog" aria-labelledby="edit_avatar">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							
							<h4 class="modal-title" id="myModalLabel-2">Change Avatar</h4>
							<ul class="card-actions icons right-top">
							<li>	
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
						<form id="form-horizontal" role="form" method="post" action="/user/{{$user->username}}/editavatar" accept-charset="UTF-8" enctype="multipart/form-data">
                           {{ csrf_field() }}
                                                <div class="slim" data-size="300,300" data-force-size="300,300" data-crop="0,0,300,300">
                                                    <input type="file"/>
                                                </div>
                            <button type="submit" class="btn btn-primary">Save Avatar</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>

        			<div class="modal fade" id="edit_header" tabindex="-1" role="dialog" aria-labelledby="edit_header">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							
							<h4 class="modal-title" id="myModalLabel-2">Change header</h4>
							<ul class="card-actions icons right-top">
							<li>	
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
						<form id="form-horizontal" role="form" method="post" action="/user/{{$user->username}}/editheader" accept-charset="UTF-8" enctype="multipart/form-data">
                           {{ csrf_field() }}
                                                <div class="slim">
                                                    <input type="file"/>
                                                </div>
                            <button type="submit" class="btn btn-primary">Save Header</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>                    
                    
        			<div class="modal fade" id="settings" tabindex="-1" role="dialog" aria-labelledby="settings">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							
							<h4 class="modal-title" id="myModalLabel-2">Settings</h4>
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
                                <strong>Warning!</strong> Other markets than CoinMarketCap does not support all coins, therefor CoinMarketCap may be used as a fallback.
                              </div>
						<form id="form-horizontal" role="form" method="post" action="/user/{{$user->username}}/editsettings">
                           {{ csrf_field() }}
                          <label for="" class="control-label">API to use</label>
                              <select class="select form-control" name="api">
                                <option value="coinmarketcap" @if(Auth::user()->api == "coinmarketcap") selected="selected" @endif>CoinMarketCap.com (Recommended)</option>
                                <option value="bittrex" @if(Auth::user()->api == "bittrex") selected="selected" @endif>Bittrex.com</option>
                                <option value="poloniex" @if(Auth::user()->api == "poloniex") selected="selected" @endif>Poloniex.com</option>
                              </select>
                          <label for="" class="control-label">Display Currency</label>
                              <select class="select form-control" name="currency">
                                <option value="USD" @if(Auth::user()->currency == "USD") selected="selected" @endif>USD</option>
                              </select>
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>
                                    
                                    
        			<div class="modal fade" id="change_password" tabindex="-1" role="dialog" aria-labelledby="change_password">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							
							<h4 class="modal-title" id="myModalLabel-2">Change Password</h4>
							<ul class="card-actions icons right-top">
							<li>	
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
						<form id="form-horizontal" role="form" method="post" action="/changepassword">
                           {{ csrf_field() }}
                              <div class="form-group is-empty">
                                <label for="" class="control-label">Current Password</label>
                                    <input type="password" class="form-control" id="currentpwd" name="currentpwd"/> 
                              </div>
                              <div class="form-group is-empty">
                                <label for="" class="control-label">New Password</label>
                                    <input type="password" class="form-control" id="newpwd" name="newpwd"/> 
                              </div> 
                              <div class="form-group is-empty">
                                <label for="" class="control-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="cnewpwd" name="cnewpwd"/> 
                              </div> 
                            
                            
                            
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>                                    
                    
        			<div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="import">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							
							<h4 class="modal-title" id="myModalLabel-2">Import Investments</h4>
							<ul class="card-actions icons right-top">
							<li>	
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
                        <div class="alert alert-info" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <strong>Hello!</strong> To get a understand on how investment grabbing works please visit this url: <a href="/importing-orders/" style="color:white;font-weight:bold!important;">http://altpocket.io/importing-orders</a><br>
                            Keep in mind that you must make a new API key for Altpocket, you may not use same as on another application.
                              </div>
						<form id="form-horizontal" role="form" method="post" action="/user/{{$user->username}}/api">
                           {{ csrf_field() }}
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Bittrex Public API key</label>
                                    <input type="text" class="form-control" id="publickey" autocomplete="off" value="{{Auth::user()->api_key}}" name="publickey"/> 
                              </div>  
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Bittrex Private API Key</label>
                                    <input type="password" class="form-control" id="privatekey" autocomplete="off" value="{{Auth::user()->api_secret}}" name="privatekey"/> 
                              </div>  
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Poloniex Public API key</label>
                                    <input type="text" class="form-control" id="publickey" autocomplete="off" value="{{Auth::user()->polo_api_key}}" name="polo_publickey"/> 
                              </div>  
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Poloniex Private API Key</label>
                                    <input type="password" class="form-control" id="privatekey" autocomplete="off" value="{{Auth::user()->polo_api_secret}}" name="polo_privatekey"/> 
                              </div>                              
                            <button type="submit" class="btn btn-primary">Save API keys</button>
                        </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>    
                    @endif
                    @endif

@endsection
                    
@section('js')
    <script src="/js/slim.kickstart.min.js" type="text/javascript"></script>      
                                    
    <script>
$(".edit-comment").click(function(){
    $.ajax({
        dataType: "json",
        url: '/comment/get/'+$(this).attr('id'),
        success: function(data){
        $("#edit-comment-field").val(data["comment"]);
        $("#edit-comment-form").attr('action', '/comment/edit/'+data["id"]);
    }
    });

});                                    
</script>
                                    
<script type="text/javascript">

$(function() {
    $('body').on('click', '.pagination button', function(e) {
        e.preventDefault();
        $page = $(this).attr('id');
        var url = $(this).attr('href') + "?page=" + $page; 
        getComments(url);
    });

    function getComments(url) {
        $.ajax({
            url : url  
        }).done(function (data) {
            $('#profile-comments').html(data);  
        }).fail(function () {
            alert('Comments could not be loaded.');
        });
    }
});

</script>        
                                    
                                    
@endsection                    