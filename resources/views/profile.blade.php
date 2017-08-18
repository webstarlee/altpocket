@extends('layouts.app2')


<?php

if(Auth::user())
{
  $multiplier = Auth::user()->getMultiplier();
  $api = Auth::user()->api;
  $currency = Auth::user()->getCurrency();
  if($currency != 'BTC' && $currency != 'USD' && $currency != 'CAD')
  {
    $fiat = DB::table('multipliers')->where('currency', $currency)->first()->price;
    $symbol2 = Auth::user()->getSymbol();
    $symbol = "";
  } else {
    $fiat = 1;
    $symbol = Auth::user()->getSymbol();
    $symbol2 = "";
  }
} else {
  $symbol = "$";
  $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
  $api = "coinmarketcap";
  $currency = "USD";
  $fiat = 1;
  $symbol2 = "";
}


 ?>


@section('title')
{{$user->username}}s Profile
@endsection

@section('css')
    <meta name="og:title" content="Altpocket.io - {{$user->username}}" />
    <meta name="og:description" content="Altpocket.io - The social altcoin portfolio. {{$user->username}}s current profit: ${{number_format((($networth * $btc) - $user->invested), 2)}}. " />



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
<?php
use Jenssegers\Agent\Agent;

$agent = new Agent();
?>
				<div id="content_wrapper" class="card-overlay">
                    @if($user->header == "default")
					<div id="header_wrapper" class="header-xl  profile-header" style="background-image:-webkit-gradient(linear,left top,left bottom,color-stop(0,transparent),color-stop(30%,transparent),color-stop(100%,rgba(0,0,0,.45))),url(/assets/img/headers/header-lg-03.jpg)!important;background-position:0 90%!important;">
                        @if($user->commentsOn() && Auth::user())
						<ul class="card-actions fab-action right">
							<li>
								<button class="btn btn-primary btn-fab" data-toggle="modal" data-target="#comment_modal">
									<i class="zmdi zmdi-comment-alt-text"></i>
								</button>
							</li>
						</ul>
                        @endif
					</div>
                    @else
					<div id="header_wrapper" class="header-xl  profile-header" style="background-image:-webkit-gradient(linear,left top,left bottom,color-stop(0,transparent),color-stop(30%,transparent),color-stop(100%,rgba(0,0,0,.45))),url(/uploads/headers/{{$user->id}}/{{$user->header}})!important;background-position:0 50%!important;">
                        @if($user->commentsOn() && Auth::user())
						<ul class="card-actions fab-action right">
							<li>
								<button class="btn btn-primary btn-fab" data-toggle="modal" data-target="#comment_modal">
									<i class="zmdi zmdi-comment-alt-text"></i>
								</button>
							</li>
						</ul>
                        @endif
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
														<img src="/assets/img/default.png" alt="" class="img-circle">
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
                                  @if(Auth::user()->isStaff())
                                  <li>
																		<a href="javascript:void(0)" data-toggle="modal" data-target="#upload_logo">Upload Coin Logo</a>
																	</li>
                                  @endif
																</ul>
															</li>
														</ul>
                                                        @else
                                                        <ul class="card-actions icons right-top">
                                                            @if(Auth::user()->isFounder() || Auth::user()->isAdmin())
                                                            <li>
    																<a href="javascript:void(0)" data-toggle="modal" data-target="#award_modal">
																	<i class="zmdi zmdi-star"></i>
																</a>
                                                            </li>
                                                            <li>{{$user->id}}</li>
                                                            @endif
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
														<h3 class="name" style="color:{{$user->groupColor()}}">@if($user->isStaff() || $user->isFounder())<img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">@elseif($user->isDonator())<img src="/awards/diamondd.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user has supported Altpocket through a donaton.">@endif{{$user->username}}@if($user->hasVerified)<i class="material-icons" style="color:#5ecbf7;cursor:pointer;font-size:15px;" data-toggle="tooltip" title="User has verified investments.">verified_user</i>
                                                        @endif
                                                        </h3>
														<span class="title">{{$user->bio}}</span>
                                                        <br>
														<p>{{count($user->followers()->get())}} @if(count($user->followers()->get()) <= 1) Follower @else Followers @endif</p>
                              @if(Auth::user())
                              @if(Auth::user()->username != $user->username && !Auth::user()->isFollowing($user))
                              <a href="/follow/{{$user->username}}" type="button" class="btn btn-primary btn-round btn-xs" style="margin-top:-15px!important" data-toggle="tooltip" data-title="Follow {{$user->username}}">Follow</a>
                              @elseif(Auth::user()->username != $user->username && Auth::user()->isFollowing($user))
                              <a href="/unfollow/{{$user->username}}" type="button" class="btn btn-primary btn-round btn-xs" style="margin-top:-15px!important" data-toggle="tooltip" data-title="Unfollow {{$user->username}}">Unfollow</a>
                              @endif
                              @else
                              <a href="javascript:void(0)" type="button" class="btn btn-primary btn-round btn-xs" id="noauthfollow"  style="margin-top:-15px!important" data-toggle="tooltip" data-title="Follow {{$user->username}} for notifications on new investments!">Follow</a>
                              @endif
													</div>
													<footer class="card-footer border-top" @if($agent->isMobile()) style="font-size:13px;" @endif>

                            <div class="row row p-t-10 p-b-10">

                                @if($user->algorithm == 2)
                                <div class="col-xs-6" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Invested alltime overall. (Deposits)"><span class="count">

                                  @if(Auth::user())
                                  {!! $symbol !!}{{number_format($user->getInvested(Auth::user()->getCurrency()), 2)}}{!! $symbol2 !!}
                                  @else
                                  {!! $symbol !!}{{number_format($user->getInvested('USD'), 2)}}{!! $symbol2 !!}
                                  @endif


                                </span><span>Invested</span>
                                </div>
                                @else
                                <div class="col-xs-6" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Paid amount for active investments."><span class="count">

                                  @if(Auth::user())
                                  {!! $symbol !!}{{number_format($user->getPaid(Auth::user()->getCurrency()), 2)}}{!! $symbol2 !!}
                                  @else
                                  {!! $symbol !!}{{number_format($user->getPaid('USD'), 2)}}{!! $symbol2 !!}
                                  @endif


                                </span><span>Invested</span>
                                </div>
                                @endif
                                <div class="col-xs-6" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Holdings on exchanges and manual investments. (Balances)"><span class="count">
                                  @if(Auth::user())
                                    {!! $symbol !!}{{number_format($networth * $multiplier + $user->getSoldProfit(Auth::user()->getCurrency()), 2)}}{!! $symbol2 !!}
                                  @else
                                    {!! $symbol !!}{{number_format($networth * $multiplier + $user->getSoldProfit('USD'), 2)}}{!! $symbol2 !!}
                                  @endif
                                </span><span>Net Worth</span>
                                </div>
                            </div>
                            <hr style="border-top:1px solid #c3c3c3">
                            <div class="row row p-t-10 p-b-10">

                                @if($user->algorithm == 2)
                                  @if(Auth::user())
                                    <div class="col-xs-6"  @if((($networth * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency())) > 0) style="color:#73c04d;cursor:pointer;" @else style="color:#de6b6b;cursor:pointer;"; @endif data-toggle="tooltip" data-placement="top" title="Networth substracted with your deposits."><span class="count">
                                  @else
                                    <div class="col-xs-6"  @if((($networth * $multiplier) - $user->getInvested('USD')) + $user->getSoldProfit('USD') > 0) style="color:#73c04d;cursor:pointer;" @else style="color:#de6b6b;cursor:pointer;"; @endif data-toggle="tooltip" data-placement="top" title="Networth substracted with your deposits."><span class="count">
                                  @endif
                                @else
                                  @if(Auth::user())
                                    <div class="col-xs-6"  @if((($networth * $multiplier) - $user->getInvested(Auth::user()->getCurrency())) + $user->getSoldProfit(Auth::user()->getCurrency()) > 0) style="color:#73c04d;cursor:pointer;" @else style="color:#de6b6b;cursor:pointer;"; @endif data-toggle="tooltip" data-placement="top" title="Networth substracted with your invested."><span class="count">
                                  @else
                                  <div class="col-xs-6"  @if((($networth * $multiplier) - $user->getInvested('USD')) + $user->getSoldProfit('USD')) style="color:#73c04d;cursor:pointer;" @else style="color:#de6b6b;cursor:pointer;"; @endif data-toggle="tooltip" data-placement="top" title="Networth substracted with your invested."><span class="count">
                                  @endif
                                @endif

                                  @if(Auth::user())
                                    {!! $symbol !!}{{number_format((($networth * $multiplier) - $user->getInvested(Auth::user()->getCurrency())) + $user->getSoldProfit(Auth::user()->getCurrency()), 2)}}{!! $symbol2 !!}
                                  @else
                                    {!! $symbol !!}{{number_format((($networth * $multiplier) - $user->getInvested('USD')) + $user->getSoldProfit('USD'), 2)}}{!! $symbol2 !!}
                                  @endif


                                </span><span>Profit</span></div>
                                <div class="col-xs-6"  @if((($user->getActiveWorth($api)) * $multiplier - ($user->getPaid($currency))) > 0) style="color:#73c04d;cursor:pointer;" @else style="color:#de6b6b;cursor:pointer;"; @endif data-toggle="tooltip" data-placement="top" title="Profit made on active investments."><span class="count">
                                  @if(Auth::user())
                                    {!! $symbol !!}{{number_format(($user->getActiveWorth($api) * Auth::user()->getMultiplier() - $user->getPaid(Auth::user()->currency)), 2)}}{!! $symbol2 !!}
                                  @else
                                    {!! $symbol !!}{{number_format(($user->getActiveWorth('coinmarketcap') * DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd - $user->getPaid('USD')), 2)}}{!! $symbol2 !!}
                                  @endif
                                </span><span>Active Profit</span>
                                </div>
                            </div>
                            <hr style="border-top:1px solid #c3c3c3">
                            <div class="row row p-t-10 p-b-10">
                                <div class="col-xs-6"><span class="count">
                                  <a href="/user/{{$user->username}}/impressed" type="button" class="btn btn-primary btn-round btn-xs">{{$user->impressed}} Impressed</a>
                                </span><span>Impressions</span></div>
                                <div class="col-xs-6"><span class="count">
                                  @if($user->groupName())
                                  <a href="javascript:void(0)" class="btn btn-round btn-xs" style="background:{{$user->groupColor()}};color:white;">{{$user->groupName()}}</a>
                                  @else
                                  <a href="javascript:void(0)" class="btn btn-info btn-round btn-xs" style="color:white;">User</a>
                                  @endif
                                </span><span>User Group</span>
                                </div>
                            </div>
                            <hr style="border-top:1px solid #c3c3c3">
                            @if(count($user->getAwards()) >= 1)
                            <div class="row row p-t-10 p-b-10" style="text-align:center;">
                                <p style="text-align:center;">Awards</p>
                            </div>
                                <div class="row row p-t-10 p-b-10" style="text-align:center;">
                                @foreach($user->getAwards() as $award)
                                    <?php
                                        $a = DB::table('awards')->where('id', $award->award_id)->first();
                                    ?>
                                    <a href="javascript:void()" data-toggle="tooltip" data-title="{{$a->description}}">
                                        <img src="/awards/{{$a->image}}"/>
                                    </a>
                                @endforeach
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
                                                                @if(!$agent->isMobile())
																<li class="active " role="presentation"><a href="#profile-timeline" data-toggle="tab" aria-expanded="true">Active Investments</a></li>
																<li role="presentation"><a href="#profile-about" data-toggle="tab" aria-expanded="true">Sold Investments</a></li>
																<li role="presentation"><a href="#profile-contacts" data-toggle="tab" aria-expanded="true">Balances</a></li>
                                                                @if($user->commentsOn())
																<li role="presentation"><a href="#profile-comments" data-toggle="tab" aria-expanded="true">Comments</a></li>
                                                                @endif
                                                                @else
																<li class="active " role="presentation"><a href="#profile-timeline" data-toggle="tab" aria-expanded="true">Active Investments</a></li><br>
																<li role="presentation"><a href="#profile-about" data-toggle="tab" aria-expanded="true">Sold Investments</a></li><br>
																<li role="presentation"><a href="#profile-contacts" data-toggle="tab" aria-expanded="true">Balances</a></li><br>
                                                                @if($user->commentsOn())
																<li role="presentation"><a href="#profile-comments" data-toggle="tab" aria-expanded="true">Comments</a></li>
                                                                @endif
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


                                      <?php // Here we go with the new investment system, above will be removed 06-27-2017 ?>
                                      @foreach($m_investments as $investment)
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

                                      @foreach($p_investments as $investment)
                                        @if($investment->date_sold == null && $investment->market == "BTC")
                                        <?php
                                          if(Auth::user())
                                          {
                                            $price = Auth::user()->getPrice($investment->currency, $investment->market, 'Poloniex');
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
                                          } else // If Not logged in
                                          {
                                            $price = $user->getPrice($investment->currency, $investment->market, 'Poloniex');
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
                                                 <ul class="card-actions icons left-top">
                                                   <li>
                                                     <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Poloniex.">verified_user</i>
                                                     </li>
                                                   </ul>
                                                 <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                     <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_bought))}}</span> </li>
                                                 </ul>
                                                 <ul class="card-actions icons right-top">
                                                   <li>
                                                     <a href="https://poloniex.com/exchange#BTC_{{$investment->currency}}"><img src="https://png.icons8.com/bitcoin/color/24" style="cursor:pointer;margin-top:-15px!important" data-toggle="tooltip" title="This investment was done with BTC." width="24" height="24"></a>
                                                   </li>
                                                   </ul>
                                               </header>
                                             <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                 <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                             </div>
                                             <div class="card-body">
                                               <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $price),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price * $multiplier),$decimal1)}}{!! $symbol2 !!}</h4>
                                               <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif
                                                 @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

                                                @if((($investment->amount * $price) * $multiplier) > (($investment->amount * $investment->bought_at) * $previous))
                                                 <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                                   {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                                   {!! $symbol !!}{{number_format((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>


                                                 <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                   {{number_format((100/((($investment->amount * $investment->bought_at) * $previous))) * (((($investment->amount * $price) * $multiplier)) - ((($investment->amount * $investment->bought_at) * $previous))), 2)}}%
                                                 </span>

                                               @else
                                               <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                                  {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                                  {!! $symbol !!}{{number_format((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>
                                                 <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                   {{number_format((100/((($investment->amount * $investment->bought_at) * $previous))) * (((($investment->amount * $price) * $multiplier)) - ((($investment->amount * $investment->bought_at) * $previous))), 2)}}%
                                                 </span>
                                               @endif
                                                 <hr style="margin-top:40px;">
                                                 <div class="usd">
                                                 <span style="float:left;">Before</span>
                                                 <span style="float:right;">After</span>
                                                 <br>
                                                 <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $investment->bought_at), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $investment->bought_at) * $previous, $decimal1)}}{!! $symbol2 !!}</span>
                                                 <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $price), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                 <br>
                                                 <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->bought_at * $previous,$decimal2)}}{!! $symbol2 !!}</span>
                                                 <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($price), 8)}}" data-html="true">{!! $symbol !!}{{number_format($price * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                                 <br>
                                                 </div>
                                             </div>
                                           </div>
                                         </figure>
                                        @endif

                                        @if($investment->date_sold == null && $investment->market == "USDT")
                                        <?php
                                          if(Auth::user())
                                          {
                                            $price = Auth::user()->getPrice($investment->currency, $investment->market, 'Poloniex');
                                            $multiplier = Auth::user()->getMultiplier();
                                            $btctotal = (($investment->btc_price_bought_usdt / $investment->btc_price_bought_usd) * $investment->bought_for) / $investment->btc_price_bought_usd;
                                            $btceach = (($investment->btc_price_bought_usdt / $investment->btc_price_bought_usd) * $investment->bought_at) / $investment->btc_price_bought_usd;
                                            $btcnow = (($investment->amount * $price)) / $btc;
                                            $previous = 1;
                                            $decimal1 = 2;
                                            $decimal2 = 2;

                                            if(Auth::user()->getCurrency() == "USD")
                                            {
                                              $previous = $investment->btc_price_bought_usd;
                                              $previousmultiplier = $investment->btc_price_bought_usd;

                                            } elseif(Auth::user()->getCurrency() == "BTC")
                                            {
                                              $previous = $investment->btc_price_bought_usd;
                                              $decimal1 = 5;
                                              $decimal2 = 9;
                                              $previousmultiplier = 1;
                                            } else {
                                              $previous = $investment->btc_price_bought_usd;
                                              $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                            }
                                          } else {
                                            $price = $user->getPrice($investment->currency, $investment->market, 'Poloniex');
                                            $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                            $btctotal = (($investment->btc_price_bought_usdt / $investment->btc_price_bought_usd) * $investment->bought_for) / $investment->btc_price_bought_usd;
                                            $btceach = (($investment->btc_price_bought_usdt / $investment->btc_price_bought_usd) * $investment->bought_at) / $investment->btc_price_bought_usd;
                                            $btcnow = (($investment->amount * $price)) / $btc;
                                            $previous = 1;
                                            $decimal1 = 2;
                                            $decimal2 = 2;

                                            $previous = $investment->btc_price_bought_usd;
                                            $previousmultiplier = $investment->btc_price_bought_usd;
                                            $symbol = "$";

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
                                                     <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_bought))}}</span></li>
                                                   </ul>
                                                   <ul class="card-actions icons right-top">
                                                     <li>
                                                       <a href="https://poloniex.com/exchange#USDT_{{$investment->currency}}"><img src="https://png.icons8.com/us-dollar/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was done with USDT." width="24" height="24"></a>
                                                       </li>
                                                     </ul>
                                               </header>
                                             <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                 <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                             </div>
                                             <div class="card-body">
                                               <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->amount * $price),2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier,$decimal1)}}{!! $symbol2 !!}</h4>
                                               <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif
                                                 @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

                                                @if(((($investment->amount * $price) / ($btc) * $multiplier)) > (($investment->bought_for) / ($previous) * $previousmultiplier))
                                                 <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 2)}}" data-html="true">
                                                   {!! $symbol !!}{{number_format(((($investment->amount * $price) / ($btc) * $multiplier) - ($investment->bought_for) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}</span>

                                                 <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                 {{number_format((100/((($investment->bought_for) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_for) / ($previous) * $previousmultiplier))), 2)}}%
                                                 </span>

                                               @else
                                               <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->amount * $price) / $multiplier) - (($investment->amount * $investment->bought_at) / $multiplier), 5)}}" data-html="true">
                                                 {!! $symbol !!}{{number_format(((($investment->amount * $price) / ($btc) * $multiplier) - ($investment->bought_for) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}</span>
                                                 <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                   {{number_format((100/((($investment->bought_for) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_for) / ($previous) * $previousmultiplier))), 2)}}%
                                                 </span>
                                               @endif
                                                 <hr style="margin-top:40px;">
                                                 <div class="usd">
                                                 <span style="float:left;">Before</span>
                                                 <span style="float:right;">After</span>
                                                 <br>
                                                 <span style="float:left;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->amount * $investment->bought_at), 2)}}" data-html="true">{!! $symbol !!}{{number_format($investment->amount * $investment->bought_at / ($previous) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                 <span style="float:right;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->amount * $price)), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                 <br>
                                                 <span style="float:left;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->bought_at)), 2)}}" data-html="true">{!! $symbol !!}{{number_format( ($investment->bought_at) / ($previous) * $previousmultiplier  ,$decimal2)}}{!! $symbol2 !!}</span>
                                                 <span style="float:right;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($price)), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($price) / ($btc) * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                                 <br>
                                                 </div>
                                             </div>
                                           </div>
                                         </figure>
                                        @endif

                                        @if($investment->date_sold == null && $investment->market == "ETH")
                                        <?php
                                          if(Auth::user())
                                          {
                                            $price = Auth::user()->getPrice($investment->currency, $investment->market, 'Poloniex');
                                            $multiplier = Auth::user()->getEthMultiplier();
                                            $previous = 0;
                                            $decimal1 = 2;
                                            $decimal2 = 5;


                                            if(Auth::user()->getCurrency() == "USD")
                                            {
                                              $previous = $investment->btc_price_bought_usd;
                                              $prevmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                            }elseif(Auth::user()->getCurrency() == "BTC")
                                            {
                                              $previous = 1;
                                              $decimal1 = 5;
                                              $decimal2 = 9;
                                              $prevmultiplier = 1 / $investment->btc_price_bought_eth;
                                            } else {
                                              $previous = $investment->btc_price_bought_usd;
                                              $prevmultiplier = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $fiat;
                                            }
                                            } else {
                                              $price = $user->getPrice($investment->currency, $investment->market, 'Poloniex');
                                              $multiplier = DB::table('polos')->where('symbol', 'ETH')->first()->price_btc * DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                              $previous = 0;
                                              $decimal1 = 2;
                                              $decimal2 = 5;
                                              $symbol = "$";

                                              $previous = $investment->btc_price_bought_usd;
                                              $prevmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
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
                                                     <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_bought))}}</span></li>
                                                 </ul>
                                                 <ul class="card-actions icons right-top">
                                                   <li>
                                                     <a href="https://poloniex.com/exchange#ETH_{{$investment->currency}}"><img style="color:#f76d5e;cursor:pointer;margin-top:-5px!important;" src="https://png.icons8.com/ethereum/color/24" width="24" height="24" data-toggle="tooltip" title="This investment was done with ETH."></a>
                                                     </li>
                                                   </ul>
                                               </header>
                                             <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                 <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                             </div>
                                             <div class="card-body">
                                               <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->amount * $price),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price * $multiplier),$decimal1)}}{!! $symbol2 !!}</h4>
                                               <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif
                                                 @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

                                                @if(((($investment->amount * $price) * $multiplier)) > (($investment->amount * $investment->bought_at) * $prevmultiplier))
                                                 <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'>
                                                   {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                                   {!! $symbol !!}{{number_format((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $prevmultiplier), $decimal1)}} {!! $symbol2 !!}
                                                   </span>

                                                 <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                   {{number_format((100/((($investment->amount * $investment->bought_at) * $prevmultiplier))) * (((($investment->amount * $price) * $multiplier)) - ((($investment->amount * $investment->bought_at) * $prevmultiplier))), 2)}}%
                                                 </span>

                                               @else
                                               <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'>
                                                  {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                                  {!! $symbol !!}{{number_format((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $prevmultiplier), $decimal1)}}{!! $symbol2 !!}
                                                  </span>
                                                 <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                   {{number_format((100/((($investment->amount * $investment->bought_at) * $prevmultiplier))) * (((($investment->amount * $price) * $multiplier)) - ((($investment->amount * $investment->bought_at) * $prevmultiplier))), 2)}}%
                                                 </span>
                                               @endif
                                                 <hr style="margin-top:40px;">
                                                 <div class="usd">
                                                 <span style="float:left;">Before</span>
                                                 <span style="float:right;">After</span>
                                                 <br>
                                                 <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->amount * $investment->bought_at), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $investment->bought_at) * $prevmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                 <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->amount * $price), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                 <br>
                                                 <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->bought_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->bought_at * $prevmultiplier,$decimal2)}}{!! $symbol2 !!}</span>
                                                 <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($price), 8)}}" data-html="true">{!! $symbol !!}{{number_format($price * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                                 <br>
                                                 </div>
                                             </div>
                                           </div>
                                         </figure>
                                       @endif
                                      @endforeach

                                      @foreach($b_investments as $investment)
                                        @if($investment->date_sold == null && $investment->market == "BTC")
                                          <?php
                                            if(Auth::user())
                                            {
                                              $price = Auth::user()->getPrice($investment->currency, $investment->market, 'Bittrex');
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
                                            } else // If Not logged in
                                            {
                                              $price = $user->getPrice($investment->currency, $investment->market, 'Bittrex');
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
                                                 <ul class="card-actions icons left-top">
                                                   <li>
                                                     <i class="material-icons" style="color:#5ecbf7;cursor:pointer;" data-toggle="tooltip" title="Verified investment from Bittrex.">verified_user</i>
                                                     </li>
                                                   </ul>
                                                 <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                     <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_bought))}}</span></li>
                                                 </ul>
                                                 <ul class="card-actions icons right-top">
                                                   <li>
                                                     <a href="https://bittrex.com/Market/Index?MarketName=BTC-{{$investment->currency}}"><img src="https://png.icons8.com/bitcoin/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was done with BTC." width="24" height="24"></a>
                                                     </li>
                                                   </ul>
                                               </header>
                                             <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                 <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                             </div>
                                             <div class="card-body">
                                               <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $price),5)}}" data-html="true">
                                                 {!! $symbol !!}{{number_format(($investment->amount * $price * $multiplier),$decimal1)}}{!! $symbol2 !!}</h4>
                                               <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif
                                                 @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

                                               @if((($investment->amount * $price) * $multiplier) > (($investment->amount * $investment->bought_at) * $previous))
                                                <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                                  {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                                  {!! $symbol !!}{{number_format((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>


                                                <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                  {{number_format((100/((($investment->amount * $investment->bought_at) * $previous))) * (((($investment->amount * $price) * $multiplier)) - ((($investment->amount * $investment->bought_at) * $previous))), 2)}}%
                                                </span>

                                              @else
                                              <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i>
                                                 {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                                 {!! $symbol !!}{{number_format((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}{!! $symbol2 !!}</span>
                                                <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                  {{number_format((100/((($investment->amount * $investment->bought_at) * $previous))) * (((($investment->amount * $price) * $multiplier)) - ((($investment->amount * $investment->bought_at) * $previous))), 2)}}%
                                                </span>
                                              @endif
                                                 <hr style="margin-top:40px;">
                                                 <div class="usd">
                                                 <span style="float:left;">Before</span>
                                                 <span style="float:right;">After</span>
                                                 <br>
                                                 <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $investment->bought_at), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $investment->bought_at) * $previous, $decimal1)}}{!! $symbol2 !!}</span>
                                                 <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->amount * $price), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                 <br>
                                                 <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($investment->bought_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->bought_at * $previous,$decimal2)}}{!! $symbol2 !!}</span>
                                                 <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format(($price), 8)}}" data-html="true">{!! $symbol !!}{{number_format($price * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                                 <br>
                                                 </div>
                                             </div>
                                           </div>
                                         </figure>
                                       @endif

                                       @if($investment->date_sold == null && $investment->market == "USDT")
                                         <?php
                                           if(Auth::user())
                                           {
                                             $price = Auth::user()->getPrice($investment->currency, $investment->market, 'Bittrex');
                                             $multiplier = Auth::user()->getMultiplier();
                                             $btctotal = (($investment->btc_price_bought_usdt / $investment->btc_price_bought_usd) * $investment->bought_for) / $investment->btc_price_bought_usd;
                                             $btceach = (($investment->btc_price_bought_usdt / $investment->btc_price_bought_usd) * $investment->bought_at) / $investment->btc_price_bought_usd;
                                             $btcnow = (($investment->amount * $price)) / $btc;
                                             $previous = 1;
                                             $decimal1 = 2;
                                             $decimal2 = 2;

                                             if(Auth::user()->getCurrency() == "USD")
                                             {
                                               $previous = $investment->btc_price_bought_usd;
                                               $previousmultiplier = $investment->btc_price_bought_usd;
                                             } elseif(Auth::user()->getCurrency() == "BTC")
                                             {
                                               $previous = $investment->btc_price_bought_usd;
                                               $decimal1 = 5;
                                               $decimal2 = 9;
                                               $previousmultiplier = 1;
                                             } else {
                                               $previous = $investment->btc_price_bought_usd;
                                               $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                             }
                                           } else {
                                             $price = $user->getPrice($investment->currency, $investment->market, 'Bittrex');
                                             $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                             $btctotal = (($investment->btc_price_bought_usdt / $investment->btc_price_bought_usd) * $investment->bought_for) / $investment->btc_price_bought_usd;
                                             $btceach = (($investment->btc_price_bought_usdt / $investment->btc_price_bought_usd) * $investment->bought_at) / $investment->btc_price_bought_usd;
                                             $btcnow = (($investment->amount * $price)) / $btc;
                                             $previous = 1;
                                             $decimal1 = 2;
                                             $decimal2 = 2;

                                             $previous = $investment->btc_price_bought_usd;
                                             $previousmultiplier = $investment->btc_price_bought_usd;
                                             $symbol = "$";

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
                                                    <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_bought))}}</span></li>
                                                  </ul>
                                                  <ul class="card-actions icons right-top">
                                                    <li>
                                                     <a href="https://bittrex.com/Market/Index?MarketName=USDT-{{$investment->currency}}"><img src="https://png.icons8.com/us-dollar/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was done with USDT." width="24" height="24"></a>
                                                      </li>
                                                    </ul>
                                              </header>
                                            <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                            </div>
                                            <div class="card-body">
                                              <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->amount * $price),2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier,$decimal1)}}{!! $symbol2 !!}</h4>
                                              <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif
                                                @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

                                              @if(((($investment->amount * $price) / ($btc) * $multiplier)) > (($investment->bought_for) / ($previous) * $previousmultiplier))
                                               <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 2)}}" data-html="true">
                                                 {!! $symbol !!}{{number_format(((($investment->amount * $price) / ($btc) * $multiplier) - ($investment->bought_for) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}</span>

                                               <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                               {{number_format((100/((($investment->bought_for) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_for) / ($previous) * $previousmultiplier))), 2)}}%
                                               </span>

                                             @else
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-btc'></i> {{number_format((($investment->amount * $price) / $multiplier) - (($investment->amount * $investment->bought_at) / $multiplier), 5)}}" data-html="true">
                                               {!! $symbol !!}{{number_format(((($investment->amount * $price) / ($btc) * $multiplier) - ($investment->bought_for) / ($previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}</span>
                                               <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                 {{number_format((100/((($investment->bought_for) / ($previous) * $previousmultiplier))) * (((($investment->amount * $price) / ($btc) * $multiplier)) - ((($investment->bought_for) / ($previous) * $previousmultiplier))), 2)}}%
                                               </span>
                                             @endif
                                                <hr style="margin-top:40px;">
                                                <div class="usd">
                                                <span style="float:left;">Before</span>
                                                <span style="float:right;">After</span>
                                                <br>
                                                <span style="float:left;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_for), 2)}}" data-html="true">{!! $symbol !!}{{number_format($investment->bought_for / ($previous) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                <span style="float:right;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->amount * $price)), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) / ($btc) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                <br>
                                                <span style="float:left;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->bought_at)), 2)}}" data-html="true">{!! $symbol !!}{{number_format( ($investment->bought_at) / ($previous) * $previousmultiplier  ,$decimal2)}}{!! $symbol2 !!}</span>
                                                <span style="float:right;cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($price)), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($price) / ($btc) * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                                <br>
                                                </div>
                                            </div>
                                          </div>
                                        </figure>
                                        @endif

                                       @if($investment->date_sold == null && $investment->market == "ETH")
                                         <?php
                                           if(Auth::user())
                                           {
                                             $price = Auth::user()->getPrice($investment->currency, $investment->market, 'Bittrex');
                                             $multiplier = Auth::user()->getEthMultiplier();
                                             $previous = 0;
                                             $decimal1 = 2;
                                             $decimal2 = 5;


                                             if(Auth::user()->getCurrency() == "USD")
                                             {
                                               $previous = $investment->btc_price_bought_usd;
                                               $prevmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                             } elseif(Auth::user()->getCurrency() == "BTC")
                                             {
                                               $previous = 1;
                                               $decimal1 = 5;
                                               $decimal2 = 9;
                                               $prevmultiplier = 1 / $investment->btc_price_bought_eth;
                                             } else {
                                               $previous = $investment->btc_price_bought_eur;
                                               $prevmultiplier = $investment->btc_price_bought_usd * $fiat / $investment->btc_price_bought_eth;
                                             }
                                             } else {
                                               $price = $user->getPrice($investment->currency, $investment->market, 'Bittrex');
                                               $multiplier = DB::table('cryptos')->where('symbol', 'ETH')->first()->price_usd;
                                               $previous = 0;
                                               $decimal1 = 2;
                                               $decimal2 = 5;
                                               $symbol = "$";

                                               $previous = $investment->btc_price_bought_usd;
                                               $prevmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
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
                                                    <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_bought))}}</span></li>
                                                </ul>
                                                <ul class="card-actions icons right-top">
                                                  <li>
                                                    <a href="https://bittrex.com/Market/Index?MarketName=ETH-{{$investment->currency}}"><img style="color:#f76d5e;cursor:pointer;margin-top:-5px!important;" src="https://png.icons8.com/ethereum/color/24" width="24" height="24" data-toggle="tooltip" title="This investment was done with ETH."></a>
                                                    </li>
                                                  </ul>
                                              </header>
                                            <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                                <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                            </div>
                                            <div class="card-body">
                                              <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->amount * $price),5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price * $multiplier),$decimal1)}}{!! $symbol2 !!}</h4>
                                              <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif
                                                @if($investment->withdrew == 1)<i class="fa fa-exclamation-circle" data-toggle="tooltip" style="cursor:pointer" title="This investment has been fully or partially withdrawn."></i>@endif</p>

                                              @if(((($investment->amount * $price) * $multiplier)) > (($investment->amount * $investment->bought_at) * $prevmultiplier))
                                               <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'>
                                                 {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                                 {!! $symbol !!}{{number_format((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $prevmultiplier), $decimal1)}}{!! $symbol2 !!}
                                                 </span>

                                               <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                 {{number_format((100/((($investment->amount * $investment->bought_at) * $prevmultiplier))) * (((($investment->amount * $price) * $multiplier)) - ((($investment->amount * $investment->bought_at) * $prevmultiplier))), 2)}}%
                                               </span>

                                             @else
                                             <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'>
                                                {{number_format((($investment->amount * $price)) - (($investment->amount * $investment->bought_at)), 5)}}" data-html="true">
                                                {!! $symbol !!}{{number_format((($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $prevmultiplier), $decimal1)}}{!! $symbol2 !!}
                                                </span>
                                               <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                                 {{number_format((100/((($investment->amount * $investment->bought_at) * $prevmultiplier))) * (((($investment->amount * $price) * $multiplier)) - ((($investment->amount * $investment->bought_at) * $prevmultiplier))), 2)}}%
                                               </span>
                                             @endif
                                                <hr style="margin-top:40px;">
                                                <div class="usd">
                                                <span style="float:left;">Before</span>
                                                <span style="float:right;">After</span>
                                                <br>
                                                <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->amount * $investment->bought_at), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $investment->bought_at) * $prevmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->amount * $price), 5)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->amount * $price) * $multiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                                <br>
                                                <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($investment->bought_at), 8)}}" data-html="true">{!! $symbol !!}{{number_format($investment->bought_at * $prevmultiplier,$decimal2)}}{!! $symbol2 !!}</span>
                                                <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<img src='https://png.icons8.com/ethereum/color/24' width='24' height='24'> {{number_format(($price), 8)}}" data-html="true">{!! $symbol !!}{{number_format($price * $multiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                                <br>
                                                </div>
                                            </div>
                                          </div>
                                        </figure>
                                      @endif
                                      @endforeach


                                    </div>
																</div>



							                  <div class="tab-pane fadeIn" id="profile-about">
                              <div class="row">
                                <?php // New ones now, above will be deleted ?>

                                @foreach($m_investments as $investment)
                                  @if($investment->date_sold != null)
                                  <?php
                                    if(Auth::user())
                                    {
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
                                        $previous = $investment->btc_price_bought_usd;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = 1;
                                      } else {
                                        $previous = $investment->btc_price_bought_usd * $fiat;
                                        $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                        $prevsold = $investment->btc_price_sold_usd * $fiat;;
                                      }
                                    } else {
                                      $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 5;
                                      $previous = $investment->btc_price_bought_usd;
                                      $previousmultiplier = $investment->btc_price_bought_usd;
                                      $prevsold = $investment->btc_price_sold_usd;

                                    }
                                   ?>
                                   <figure class="col-xs-12 col-sm-4 col-md-4">
                                     <div class="card image-over-card m-t-30" style="box-shadow:0 1px 20px 6px rgba(0,0,0,.1)!important;">
                                         <header class="card-heading">
                                             <ul class="card-actions icons left-top" style="margin-top:-3px;margin-left:20px;">
                                                 <li><span style="font-size:11px" class="text-muted">{{date('Y-m-d', strtotime($investment->date_sold))}}</span></li>
                                               </ul>
                                           <ul class="card-actions icons right-top">
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
                                    if(Auth::user())
                                    {
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
                                    } else {
                                      $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 5;
                                      $previous = $investment->btc_price_bought_usd;
                                      $previousmultiplier = $investment->btc_price_bought_usd;
                                      $prevsold = $investment->btc_price_sold_usd;
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
                                             {!! $symbol !!}{{number_format((($investment->amount * $investment->sold_at) * $prevsold) - (($investment->amount * $investment->bought_at) * $previous), $decimal1)}}</span>

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
                                      if(Auth::user())
                                      {
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
                                         $previous = $investment->btc_price_bought_usd * $fiat;
                                         $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                         $prevsold = $investment->btc_price_sold_usd * $fiat;
                                       }
                                     } else {
                                       $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                       $previous = 0;
                                       $decimal1 = 2;
                                       $decimal2 = 5;
                                       $bought_for = ($investment->bought_at * $investment->amount) / $investment->btc_price_bought_usdt;
                                       $bought_at = $investment->bought_at / $investment->btc_price_bought_usdt;
                                       $previous = $investment->btc_price_bought_usd;
                                       $previousmultiplier = $investment->btc_price_bought_usd;
                                       $prevsold = $investment->btc_price_sold_usd;
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
                                    if(Auth::user())
                                    {
                                    $multiplier = Auth::user()->getMultiplier();
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
                                    }  elseif(Auth::user()->getCurrency() == "BTC")
                                    {
                                      $previous = $investment->btc_price_bought_usdt;
                                      $prevsoldmultiplier = 1;
                                      $decimal1 = 5;
                                      $decimal2 = 9;
                                      $previousmultiplier = 1;
                                      $prevsold = $investment->btc_price_sold_usdt;
                                    } else
                                    {
                                      $previous = $investment->btc_price_bought_usdt;
                                      $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                      $prevsold = $investment->btc_price_sold_usdt;
                                      $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                    }
                                    } else {
                                      $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 2;
                                      $bought_for = ($investment->bought_at * $investment->amount) / $investment->btc_price_bought_usdt;
                                      $bought_at = $investment->bought_at / $investment->btc_price_bought_usdt;
                                      $previous = $investment->btc_price_bought_usdt;
                                      $previousmultiplier = $investment->btc_price_bought_usd;
                                      $prevsold = $investment->btc_price_sold_usdt;
                                      $prevsoldmultiplier = $investment->btc_price_sold_usd;
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
                                             <li>
                                             <img src="https://png.icons8.com/us-dollar/color/24" style="cursor:pointer;margin-top;-5px!important;" data-toggle="tooltip" title="This investment was bought with USDT and sold to USDT." width="24" height="24">
                                               </li>
                                             </ul>
                                         </header>
                                       <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                           <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                       </div>
                                       <div class="card-body">
                                         <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_at * $investment->amount),5)}}" data-html="true">
                                             {!! $symbol !!}{{number_format(($investment->sold_at * $investment->amount / ($prevsold)) * $prevsoldmultiplier,$decimal1)}}{!! $symbol2 !!}</h4>
                                         <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                          @if((($investment->sold_for / $prevsold) * $prevsoldmultiplier) > (($investment->bought_for / $previous) * $previousmultiplier))
                                           <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_at * $investment->amount)) - (($investment->bought_at * $investment->amount)), $decimal1)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->sold_at * $investment->amount / $prevsold) * $prevsoldmultiplier) - (($investment->bought_at * $investment->amount / $previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}
                                           </span>

                                           <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                             {{number_format((100/((($investment->bought_for / $previous) * $previousmultiplier))) * (((($investment->sold_for / $prevsold) * $prevsoldmultiplier)) - ((($investment->bought_for / $previous) * $previousmultiplier))), 2)}}%
                                           </span>

                                         @else
                                         <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_for)) - (($investment->bought_for)), $decimal1)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->sold_at * $investment->amount / $prevsold) * $prevsoldmultiplier) - (($investment->bought_at * $investment->amount / $previous) * $previousmultiplier), $decimal1)}} {!! $symbol2 !!}                                        </span>
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                             {{number_format((100/((($investment->bought_for / $previous) * $previousmultiplier))) * (((($investment->sold_for / $prevsold) * $prevsoldmultiplier)) - ((($investment->bought_for / $previous) * $previousmultiplier))), 2)}}%
                                           </span>
                                         @endif
                                           <hr style="margin-top:40px;">
                                           <div class="usd">
                                           <span style="float:left;">Before</span>
                                           <span style="float:right;">After</span>
                                           <br>
                                           <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_at * $investment->amount), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->bought_at * $investment->amount / $previous) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                           <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_at * $investment->amount), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_at * $investment->amount / $prevsold) * $prevsoldmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
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
                                    if(Auth::user())
                                    {
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
                                      $prevsold = $investment->btc_price_sold_usd * $fiat;
                                      $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                    }
                                    } else {
                                      $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 2;
                                      $prevsold = $investment->btc_price_sold_usd;
                                      $prevsoldmultiplier = $investment->btc_price_sold_usd;
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
                                    if(Auth::user())
                                    {
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
                                        $prevsold = $investment->btc_price_sold_usd * $fiat;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                      }
                                    } else {
                                      $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 2;
                                      $prevsold = $investment->btc_price_sold_usd;
                                      $prevsoldmultiplier = $investment->btc_price_sold_usd;
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
                                    if(Auth::user())
                                    {
                                      $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 5;

                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $previous = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $prevsold = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "EUR")
                                      {
                                        $previous = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $previousmultiplier = $investment->btc_price_bought_eur;
                                        $prevmultiplier = $investment->btc_price_bought_eur / $investment->btc_price_bought_eth;
                                        $prevsold = $investment->btc_price_sold_eur;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $previous = $investment->btc_price_bought_eth;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = 1;
                                        $prevmultiplier = 1 / $investment->btc_price_bought_eth;
                                      } else {
                                        $previous = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $fiat;
                                        $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                        $prevmultiplier = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $fiat;
                                        $prevsold = $investment->btc_price_sold_usd * $fiat;
                                      }
                                    } else
                                     {
                                      $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 5;
                                      $previous = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                      $previousmultiplier = $investment->btc_price_bought_usd;
                                      $prevmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                      $prevsold = $investment->btc_price_sold_usd;
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
                                      if(Auth::user())
                                      {
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
                                      } else {
                                        $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                        $previous = 0;
                                        $decimal1 = 2;
                                        $decimal2 = 5;
                                        $previous = $investment->btc_price_bought_usd;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevsold = $investment->btc_price_sold_usd;
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
                                        if(Auth::user())
                                        {
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
                                           $previous = $investment->btc_price_bought_usd * $fiat;
                                           $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                           $prevsold = $investment->btc_price_sold_usd * $fiat;
                                         }
                                       } else {
                                         $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                         $previous = 0;
                                         $decimal1 = 2;
                                         $decimal2 = 5;
                                         $bought_for = ($investment->bought_at * $investment->amount) / $investment->btc_price_bought_usdt;
                                         $bought_at = $investment->bought_at / $investment->btc_price_bought_usdt;
                                         $previous = $investment->btc_price_bought_usd;
                                         $previousmultiplier = $investment->btc_price_bought_usd;
                                         $prevsold = $investment->btc_price_sold_usd;
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
                                      if(Auth::user())
                                      {
                                        $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
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
                                          $previous = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $fiat;
                                          $previousmultiplier = $investment->btc_price_bought_usd * $fiat;
                                          $prevmultiplier = $investment->btc_price_bought_usd * $fiat / $investment->btc_price_bought_eth;
                                          $prevsold = $investment->btc_price_sold_usd * $fiat;
                                        }
                                      } else {
                                        $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                        $previous = 0;
                                        $decimal1 = 2;
                                        $decimal2 = 5;
                                        $previous = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevmultiplier = $investment->btc_price_bought_usd / $investment->btc_price_bought_eth;
                                        $prevsold = $investment->btc_price_sold_usd;
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
                                      if(Auth::user())
                                      {
                                      $multiplier = Auth::user()->getMultiplier();
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
                                      } else {
                                        $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                        $previous = 0;
                                        $decimal1 = 2;
                                        $decimal2 = 2;
                                        $bought_for = ($investment->bought_at * $investment->amount) / $investment->btc_price_bought_usdt;
                                        $bought_at = $investment->bought_at / $investment->btc_price_bought_usdt;
                                        $previous = $investment->btc_price_bought_usdt;
                                        $previousmultiplier = $investment->btc_price_bought_usd;
                                        $prevsold = $investment->btc_price_sold_usdt;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd;
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
                                             <li>
                                             <img src="https://png.icons8.com/us-dollar/color/24" style="cursor:pointer;margin-top:-5px!important;" data-toggle="tooltip" title="This investment was bought with USDT and sold to USDT." width="24" height="24">
                                               </li>
                                             </ul>
                                         </header>
                                       <div class="card-image" style="box-shadow:none!important;margin-top:5px!important;height:45px;">
                                           <img style="max-height:45px;display:block;margin:0 auto;width:inherit!important;border-radius:0px;" src="/assets/logos/{{$investment->currency}}.png" itemprop="thumbnail" alt="Image description">
                                       </div>
                                       <div class="card-body">
                                         <h4 class="card-title text-center" style="cursor:pointer" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_for),5)}}" data-html="true">
                                             {!! $symbol !!}{{number_format(($investment->sold_at * $investment->amount / ($prevsold)) * $prevsoldmultiplier,$decimal1)}}{!! $symbol2 !!}</h4>
                                         <p class="text-center" style="font-size:11px;">({{$investment->amount}} {{$investment->currency}}) @if($investment->edited == 1)<i class="fa fa-cogs" data-toggle="tooltip" style="cursor:pointer" title="This investment has been modified by a sell or withdraw."></i>@endif</p>

                                          @if(((($investment->sold_for / $prevsold) * $prevsoldmultiplier)) > (($investment->bought_for / $previous) * $previousmultiplier))
                                           <span class="text-center label label-success" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_at * $investment->amount)) - (($investment->bought_at * $investment->amount)), $decimal1)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->sold_at * $investment->amount / $prevsold) * $prevsoldmultiplier) - (($investment->bought_at * $investment->amount / $previous) * $previousmultiplier), $decimal1)}}{!! $symbol2 !!}
                                           </span>

                                           <span class="text-center label label-success" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                             {{number_format((100/((($investment->bought_at * $investment->amount / $previous) * $previousmultiplier))) * (((($investment->sold_at * $investment->amount / $prevsold) * $prevsoldmultiplier)) - ((($investment->bought_at * $investment->amount / $previous) * $previousmultiplier))), 2)}}%
                                           </span>

                                         @else
                                         <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:left;font-size:12px;color:white;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format((($investment->sold_at * $investment->amount)) - (($investment->bought_at * $investment->amount)), $decimal1)}}" data-html="true">
                                             {!! $symbol !!}{{number_format((($investment->sold_at * $investment->amount / $prevsold) * $prevsoldmultiplier) - (($investment->bought_at * $investment->amount / $previous) * $previousmultiplier), $decimal1)}}    {!! $symbol2 !!}                                     </span>
                                           <span class="text-center label label-danger" style="display:block;margin: 0 auto;float:right;font-size:12px;color:white;">
                                             {{number_format((100/((($investment->bought_at * $investment->amount / $previous) * $previousmultiplier))) * (((($investment->sold_at * $investment->amount / $prevsold) * $prevsoldmultiplier)) - ((($investment->bought_at * $investment->amount / $previous) * $previousmultiplier))), 2)}}%
                                           </span>
                                         @endif
                                           <hr style="margin-top:40px;">
                                           <div class="usd">
                                           <span style="float:left;">Before</span>
                                           <span style="float:right;">After</span>
                                           <br>
                                           <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_at * $investment->amount), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->bought_at * $investment->amount / $previous) * $previousmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
                                           <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_at * $investment->amount), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_at * $investment->amount / $prevsold) * $prevsoldmultiplier, $decimal1)}}{!! $symbol2 !!}</span>
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
                                      if(Auth::user())
                                      {
                                      $multiplier = Auth::user()->getMultiplier();
                                      $previous = 0;
                                      $decimal1 = 2;
                                      $decimal2 = 2;
                                      if(Auth::user()->getCurrency() == "USD")
                                      {
                                        $prevsold = $investment->btc_price_sold_usdt;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd;
                                      } elseif(Auth::user()->getCurrency() == "BTC")
                                      {
                                        $prevsoldmultiplier = 1;
                                        $decimal1 = 5;
                                        $decimal2 = 9;
                                        $previousmultiplier = 1;
                                        $prevsold = $investment->btc_price_sold_usdt;
                                      } else {
                                        $prevsold = $investment->btc_price_sold_usdt;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                      }
                                      } else {
                                        $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                        $previous = 0;
                                        $decimal1 = 2;
                                        $decimal2 = 2;
                                        $prevsold = $investment->btc_price_sold_usdt;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd;
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
                                           <span style="float:left;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->bought_at), 2)}}" data-html="true">{!! $symbol !!}0</span>
                                           <span style="float:right;cursor:pointer;" data-toggle="tooltip" title="<i class='fa fa-usd'></i> {{number_format(($investment->sold_at), 2)}}" data-html="true">{!! $symbol !!}{{number_format(($investment->sold_at / $prevsold) * $prevsoldmultiplier ,$decimal2)}}{!! $symbol2 !!}</span>
                                           <br>
                                           </div>
                                       </div>
                                     </div>
                                   </figure>
                                  @endif

                                  @if($investment->date_sold != null && $investment->soldmarket == "BTC" && $investment->market == "Deposit")
                                    <?php
                                      if(Auth::user())
                                      {
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
                                          $prevsold = $investment->btc_price_sold_usd * $fiat;
                                          $prevsoldmultiplier = $investment->btc_price_sold_usd * $fiat;
                                        }
                                      } else {
                                        $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                        $previous = 0;
                                        $decimal1 = 2;
                                        $decimal2 = 2;
                                        $prevsold = $investment->btc_price_sold_usd;
                                        $prevsoldmultiplier = $investment->btc_price_sold_usd;
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
                                @endforeach
                              </div>
			                      </div>





																<div class="tab-pane fadeIn" id="profile-contacts">
                                  <div class="row">
                                  @if(count($balances) >= 1)
                                  <h2 style="text-align:center">Balances</h2>
                                  <hr>
                                  @endif
                                  @foreach($balances as $balance)
                                    <?php
                                      if(Auth::user())
                                      {
                                        $price = Auth::user()->getPrice($balance->currency, 'Balance', $balance->exchange);
                                        $multiplier = Auth::user()->getMultiplier();

                                      } else {
                                        $price = $user->getPrice($balance->currency, 'Balance', $balance->exchange);
                                        $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                        $symbol = "$";
                                      }
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
                                  @if(count($n_minings) >= 1)
                                  <div class="row">
                                  <h2 style="text-align:center">Mined Assets</h2>
                                  <hr>
                                  @foreach($n_minings as $mining)
                                    <?php
                                      if(Auth::user())
                                      {
                                        $price = Auth::user()->getPrice($mining->currency, 'Mining', 'Manual');
                                        $multiplier = Auth::user()->getMultiplier();

                                      } else {
                                        $price = $user->getPrice($mining->currency, 'Mining', 'Manual');
                                        $multiplier = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                        $symbol = "$";
                                      }
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
                             <label for="" class="control-label">Username</label>
                                 <input type="text" class="form-control" id="username" autocomplete="off" value="{{Auth::user()->username}}" name="username"/>
                           </div>
                           <div class="form-group is-empty">
                             <label for="" class="control-label">Email</label>
                                 <input type="email" class="form-control" id="email" autocomplete="off" value="{{Auth::user()->email}}" name="email"/>
                           </div>
                           <div class="form-group is-empty">
                       <label for="" class="control-label">Biography</label>
                                 <input type="text" class="form-control" id="bio" autocomplete="off" value="{{Auth::user()->bio}}" name="bio"/>
                           </div>
                          <div class="form-group">
                            <label class="control-label">About</label>
                            <textarea id="about" name="about" class="form-control">{{$user->about}}</textarea>
                          </div>
                          <div class="form-group">
                            <label class="control-label">Twitter @username</label>
                            <input type="text" class="form-control" name="twitter" id="twitter" value="{{Auth::user()->twitter}}">
                          </div>
                          <div class="form-group">
                            <label class="control-label">Youtube Channel ID/Username</label>
                            <input type="text" class="form-control" name="youtube" id="youtube" value="{{Auth::user()->youtube}}">
                          </div>
                            <div class="form-group">
                            <label class="control-label">Algorithm for invested</label>
                              <select class="select form-control" name="algo" style="padding:15px;">
                                    <option value="1" @if(Auth::user()->algorithm == "1") selected="selected" @endif>Paid amount for active investments</option>
                                    <option value="2" @if(Auth::user()->algorithm == "2") selected="selected" @endif>Deposits - Withdrawals</option>
                                  </select>
                            </div>
                            <div class="row">
                            <div class="form-group  col-md-6">
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

      @if(Auth::user() && Auth::user()->isStaff())
      <div class="modal fade" id="upload_logo" tabindex="-1" role="dialog" aria-labelledby="upload_logo">
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">

      <h4 class="modal-title" id="myModalLabel-2">Upload logo</h4>
      <ul class="card-actions icons right-top">
      <li>
        <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
          <i class="zmdi zmdi-close"></i>
        </a>
      </li>
    </ul>
  </div>
  <div class="modal-body">
    <form id="form-horizontal" role="form" method="post" action="/uploadlogo" accept-charset="UTF-8" enctype="multipart/form-data">
                   {{ csrf_field() }}
                                        <div class="slim">
                                            <input type="file"/>
                                        </div>
                    <button type="submit" class="btn btn-primary">Upload Logo</button>
                </form>
    </div>
  </div>
  <!-- modal-content -->
</div>
<!-- modal-dialog -->
</div>
@endif

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

							<h4 class="modal-title" id="myModalLabel-2">API Keys</h4>
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
                          <label for="" class="control-label">Bittrex Secret API Key</label>
                                    <input style="-webkit-text-security: disc;" type="text" class="form-control" id="privatekey" autocomplete="off" value="{{Auth::user()->api_secret}}" name="privatekey"/>
                              </div>
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Poloniex Public API key</label>
                                    <input type="text" class="form-control" id="publickey" autocomplete="off" value="{{Auth::user()->polo_api_key}}" name="polo_publickey"/>
                              </div>
                              <div class="form-group is-empty">
                          <label for="" class="control-label">Poloniex Secret API Key</label>
                                    <input type="text" style="-webkit-text-security: disc;" class="form-control" id="privatekey" autocomplete="off" value="{{Auth::user()->polo_api_secret}}" name="polo_privatekey"/>
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


            @if(Auth::user())
                @if(Auth::user()->isFounder() || Auth::user()->isAdmin())
        			<div class="modal fade" id="award_modal" tabindex="-1" role="dialog" aria-labelledby="award_modal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">

							<h4 class="modal-title" id="myModalLabel-2">Give {{$user->username}} an award</h4>
							<ul class="card-actions icons right-top">
							<li>
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
						<form id="form-horizontal" role="form" method="post" action="/award/add/{{$user->username}}">
                            {{ csrf_field() }}
                  <div class="form-group">
                    <label class="control-label">Award</label>
                          <select class="select form-control" name="award" id="award">
                            <option>Select an Award</option>
                            @foreach(DB::table('awards')->get() as $award)
                            <option value="{{$award->id}}">{{$award->name}}</option>
                            @endforeach
                          </select>
                  </div>
                  <div class="form-group">
                    <label class="control-label">Reason</label>
                    <input type="text" class="form-control" name="reason" id="reason">
                  </div>
                            <button type="submit" class="btn btn-primary">Give Award</button>
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
        console.log($page);
        var url = '/user/{{$user->username}}' + "?page=" + $page;
        console.log(url);
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
