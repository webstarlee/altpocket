@extends('layouts.app')

@section('title')
Dashboard
@endsection




<?php
use Jenssegers\Agent\Agent;

$agent = new Agent();
?>
@section('css')
    <link rel="stylesheet" href="/assets/css/shoutbox.css?v=1.3">

<style>
    #currencies > span {
        padding-left:25px;
        padding-right:25px;
    }
    .positive {
        color:#73c04d;
    }
    .negative {
        color:#de6b6b;
    }
    @if(!$agent->isMobile())
    #currencies > li {
        display:inline;
        list-style-type:none;
        padding-left:25px;
        padding-right:25px;
    }
    @endif


    @media screen and (max-width: 992px){
    #currencies > li {
        display:block;
        list-style-type:none;
        padding-left:0px;
        padding-right:0px;
    }
    }



</style>

@endsection

@section('js')
<script>
var scollBox    = $('.shoutbox');
//var height = $("ul .list-group")[0].scrollHeight;
var height=0;
$("ul li").each(function() {
   height += $(this).outerHeight(true); // to include margins
});
$('.chat-messages .list-group').animate({scrollTop: height});
load_data = {'fetch':1};
$.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('input[name=_token]').val()
      }
  });
window.setInterval(function(){
  $('.chat-messages .list-group').animate({scrollTop: height});
  $.ajax({
      url: "{{ route('shoutbox-fetch') }}",
      type: 'post',
      data: load_data,
      dataType: 'json',
      success: function (data) {
      //console.info(data.data);
      $('.chat-messages .list-group').html(data.data);
      }
  });
}, 3000);
$("#chat-message").keypress(function(evt) {
    if(evt.which == 13) {
          var message = $('#chat-message').val();
          post_data = {'message':message};

    $.ajax({
        url: "{{ route('shoutbox-send') }}",
        type: 'post',
        data: post_data,
        dataType: 'json',
        success: function (data) {
            //console.info(data.data);
            $(data.data).hide().appendTo('.chat-messages .list-group').fadeIn();
                $('#chat-error').addClass('hidden');
                $('#chat-message').removeClass('invalid');
                $('#chat-message').val('');
                $('.chat-messages .list-group').animate({scrollTop: height});
        },
        error: function (data) {
          //console.log(data);
          $('#chat-message').addClass('invalid');
          $('#chat-error').removeClass('hidden');
          $('#chat-error').text(data.responseText);
        }
    });
    }
});
</script>

@endsection



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
@section('content')
    <div id="content_wrapper" class="">
    <div id="header_wrapper" class="header-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <header id="header">
                        <h1>Dashboard</h1>
                    </header>
                </div>
            </div>
        </div>
    </div>
        <div id="content" class="container-fluid">
            <div class="content-body">
              <div class="tabpanel m-b-30">
                <ul class="nav nav-tabs nav-justified">
                  <li class="active " role="presentation"><a href="#dashboard1" data-toggle="tab" aria-expanded="true">Dashboard</a></li>
                  <li class="" role="presentation"><a href="#social" data-toggle="tab" aria-expanded="true">Social</a></li>
                </ul>
              </div>


                <div id="dashboard_content" class="tab-content">

                    <div class="tab-pane fade" id="social">

                      <div class="row">
                        <div class="col-md-4">
                          <div class="card" style="text-align:center;">
                            <header class="card-heading border-bottom">
                              <h2 class="card-title">Referrals</h2>
                            </header>
                            <div class="card-body p-0">
                            <p>Your referral URL</p>


                              <div class="row">
                                <div class="col-md-6">
                                  <div class="card">
                                    <header class="card-heading">
                                      <h2 class="card-title">Total Referrals</h2>
                                    </header>
                                    <div class="card-body p-0">
                                      <h1>@if(Auth::user()->affiliate_id){{ $count }} @else 0 @endif</h1>
                                      <br>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="card">
                                    <header class="card-heading">
                                      <h2 class="card-title">Last Referral</h2>
                                    </header>
                                    <div class="card-body p-0">
                                      <h1>@if(Auth::user()->affiliate_id && $count >= 1)<a href="/user/{{ DB::table('users')->where('referred_by', Auth::user()->affiliate_id)->orderby('id', 'DESC')->first()->username}}">{{ DB::table('users')->where('referred_by', Auth::user()->affiliate_id)->orderby('id', 'DESC')->first()->username}}  </a> @else 0 @endif</h1>
                                      <br>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-12">
                                <div class="card">
                                  <header class="card-heading">
                                    <h2 class="card-title">Next Award</h2>
                                    <small>Earn awards based on the amount you referred to Altpocket!</small>
                                  </header>
                                  <div class="card-body p-0">
                                    @if($count >= 5)
                                      <p>No remaining award to be claimed!</p>
                                    @else
                                      <img src="https://altpocket.io/awards/heart.png" data-title="This user has referred atleast 5 users." data-toggle="tooltip" style="cursor:pointer;"/> <p>Refer at least 5 people to get the "Referrer" award.</p>
                                    @endif
                                  </div>
                                </div>
                              </div>
                            </div>

                            </div>
                          </div>
                        </div>

                        <div class="col-md-8">
                              <div class="panel panel-chat shoutbox">
                               <div class="panel-heading">
                                 <h3 class="panel-title text-center">Altpocket Shoutbox</h3>
                               </div>

                               <div class="chat-messages">
                                   <ul class="list-group">
                                   @foreach($shoutboxItems as $messages)

                                      <?php
                                      $class = '';

                                       if(in_array(\Auth::user()->id, explode(',', $messages->mentions)))
                                       {
                                         $class = 'mentioned';
                                       }
                                       ?>

                                     <li class="list-group-item {{ $class }}">
                                         <div class="profile-avatar tiny pull-left" style="background-image: url()"></div>
                                         <h5 class="list-group-item-heading"><a href="#">{{ $messages->poster->username }}</a></h5>
                                         <p class="message-content"><time>{{ date("H:i", strtotime($messages->created_at)) }}</time>{{ $messages->message }}</p>

                                     </li>
                                   @endforeach
                                 </ul>
                               </div>

                               <div class="panel-footer ">
                                   {!! csrf_field() !!}
                                   <div class="form-group" style="margin:0px!important;padding-bottom:13px;">
                                     <input class="form-control" id="chat-message">
                                     <p id="chat-error" class="hidden text-danger"></p>
                                   </div>
                               </div>
                             </div>
                        </div>
                      </div>

                    </div>

                    <div class="tab-pane fade active in" id="dashboard1">
                        <div class="row">
                          <div class="col-md-12">
                              <div class="card">
                        <div class="card-body" style="text-align:center;font-size:13px;" id="currencies">
                            <ul id="currencies">
                              <li><span><img src="https://files.coinmarketcap.com/static/img/coins/16x16/bitcoin.png"/> BTC: <strong>$ {{DB::table('cryptos')->where('symbol', 'btc')->first()->price_usd}}</strong>
                                  <span @if(DB::table('cryptos')->where('symbol', 'btc')->first()->percent_change_24h > 0) class="positive" @else class="negative" @endif>({{DB::table('cryptos')->where('symbol', 'btc')->first()->percent_change_24h}}%)</span></span>  </li>

                             <li> <span><img src="https://files.coinmarketcap.com/static/img/coins/16x16/litecoin.png"/> LTC: <strong>$ {{DB::table('cryptos')->where('symbol', 'ltc')->first()->price_usd}}</strong> <span @if(DB::table('cryptos')->where('symbol', 'ltc')->first()->percent_change_24h > 0) class="positive" @else class="negative" @endif>({{DB::table('cryptos')->where('symbol', 'ltc')->first()->percent_change_24h}}%)</span></span>  </li>

                             <li> <span><img src="https://files.coinmarketcap.com/static/img/coins/16x16/ethereum.png"/> ETH: <strong>$ {{DB::table('cryptos')->where('symbol', 'eth')->first()->price_usd}}</strong> <span @if(DB::table('cryptos')->where('symbol', 'eth')->first()->percent_change_24h > 0) class="positive" @else class="negative" @endif>({{DB::table('cryptos')->where('symbol', 'eth')->first()->percent_change_24h}}%)</span></span>  </li>

                             <li> <span><img src="https://files.coinmarketcap.com/static/img/coins/16x16/ripple.png"/> XRP: <strong>$ {{DB::table('cryptos')->where('symbol', 'xrp')->first()->price_usd}}</strong> <span @if(DB::table('cryptos')->where('symbol', 'xrp')->first()->percent_change_24h > 0) class="positive" @else class="negative" @endif>({{DB::table('cryptos')->where('symbol', 'xrp')->first()->percent_change_24h}}%)</span></span>  </li>

                             <li> <span><img src="https://files.coinmarketcap.com/static/img/coins/16x16/siacoin.png"/> SIA: <strong>$ {{DB::table('cryptos')->where('symbol', 'sc')->first()->price_usd}}</strong> <span @if(DB::table('cryptos')->where('symbol', 'sc')->first()->percent_change_24h > 0) class="positive" @else class="negative" @endif>({{DB::table('cryptos')->where('symbol', 'sc')->first()->percent_change_24h}}%)</span></span> </li>

                            <li>  <span><img src="https://files.coinmarketcap.com/static/img/coins/16x16/nem.png"/> XEM: <strong>$ {{DB::table('cryptos')->where('symbol', 'xem')->first()->price_usd}}</strong> <span @if(DB::table('cryptos')->where('symbol', 'xem')->first()->percent_change_24h > 0) class="positive" @else class="negative" @endif>({{DB::table('cryptos')->where('symbol', 'xem')->first()->percent_change_24h}}%)</span></span>  </li>
                          </ul>





                        </div>
                      </div>
                          </div>
                        </div>
                        <div class="row">
  								<div class="col-lg-4">
  									<div class="card">
  										<header class="card-heading card-image app_primary_bg">
  											<!-- IMAGE GOES HERE -->
  											<img src="assets/img/headers/header-md-09.jpg" alt="" class="mCS_img_loaded">
  											<h2 class="card-title left-bottom overlay text-white">Recent update</h2>
  										</header>
  										<div class="card-body" style="height:209px;">
  											<small class="block text-muted p-t-10 p-b-10">27/06/2017</small>
  											<p>We are constantly working on improving the new investment system. We are also working on alot of new features in preparation for a re-launch.</p>
  											</div>
  										</div>
  									</div>
									<div class="col-lg-8">
  										<div class="card type--profile">
  											<header class="card-heading card-background" id="card_img_02">
                                                          @if(Auth::user()->avatar == "default.jpg")
  														<img src="/assets/img/default.png" alt="" class="img-circle">
                                                          @else
  														<img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="" class="img-circle">
                                                          @endif
  												<ul class="card-actions icons  right-top">
  													<li class="dropdown">
  													</li>
  												</ul>
  											</header>
  											<div class="card-body">
  												<h3 class="name">{{Auth::user()->username}}</h3>
  												<span class="title">{{Auth::user()->bio}}</span>
  												<a href="/user/{{Auth::user()->username}}" class="btn btn-primary btn-round">Profile</a>
  											</div>
  											<footer class="card-footer border-top">

  												<div class="row row p-t-10 p-b-10">
  													<div class="col-xs-4"><span class="count">{!! $symbol !!}{{number_format(Auth::user()->getInvested(Auth::user()->getCurrency()), 2)}}{!! $symbol2 !!}</span><span>Invested</span></div>
  													<div class="col-xs-4"><span class="count">{{Auth::user()->impressed}}</span><span>Impressed</span></div>
                                                              @if(((Auth::user()->getNetWorthNew(Auth::user()->api) * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency())) > 0)
  															<div class="col-xs-4"><span class="count" style="color:#73c04d";>{!! $symbol !!}{{number_format(((Auth::user()->getNetWorthNew(Auth::user()->api) * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency())) + Auth::user()->getSoldProfit(Auth::user()->getCurrency()), 2)}}{!! $symbol2 !!}</span><span>Profit</span></div>
                                                              @else
  															<div class="col-xs-4"><span class="count" style="color:#de6b6b";>{!! $symbol !!}{{number_format(((Auth::user()->getNetWorthNew(Auth::user()->api) * $multiplier) - Auth::user()->getInvested(Auth::user()->getCurrency())) + Auth::user()->getSoldProfit(Auth::user()->getCurrency()), 2)}}{!! $symbol2 !!}</span><span>Profit</span></div>
                                                              @endif
  												</div>
  											</footer>
							          </div>
                  </div>
                    </div>
                        <div class="row">
                        <div class="col-lg-4">
<div class="card">
											<header class="card-heading border-bottom">
												<h2 class="card-title">Last 5 investments made</h2>
												<small>These are the last 5 investments made by our users.</small>
											</header>
											<div class="card-body p-0">
												<ul class="list-group ">
                                                    @foreach($investments as $investment)
                                                    <?php
                                                        $user = DB::table('users')->where('id', $investment->userid)->first();

                                                    ?>
                          @if($user->public == "on")
													<li class="list-group-item">
														<span class="pull-left">
                                                            @if($user->avatar != 'default.jpg')
                                                            <a href="/user/{{$user->username}}/"><img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="" class="img-circle max-w-40 m-r-10 "></a>
                                                            @else
                                                            <a href="/user/{{$user->username}}/"><img src="/assets/img/default.png" alt="" class="img-circle max-w-40 m-r-10 "></a>
                                                            @endif
                                                        </span>
														<div class="list-group-item-body">
															<div class="list-group-item-heading">{{$user->username}} invested in {{$investment->crypto}}</div>
															<div class="list-group-item-text">{{$user->username}} purchased {{number_format($investment->amount, 5)}} {{$investment->crypto}} at {{number_format($investment->bought_at, 8, '', '')}} <i class="fa fa-btc"></i></div>
														</div>
													</li>
                          @else
                          <li class="list-group-item">
														<span class="pull-left">
                                                            <img src="/assets/img/default.png" alt="" class="img-circle max-w-40 m-r-10 ">
                                                        </span>
														<div class="list-group-item-body">
															<div class="list-group-item-heading">Anonymous invested in {{$investment->crypto}}</div>
															<div class="list-group-item-text">Anonymous purchased {{number_format($investment->amount, 5)}} {{$investment->crypto}} at {{number_format($investment->bought_at, 8, '', '')}} <i class="fa fa-btc"></i></div>
														</div>
													</li>
                          @endif
                                                    @endforeach
												</ul>
											</div>
										</div>
									</div>


                        <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
											<header class="card-heading border-bottom">
												<h2 class="card-title">Random users</h2>
												<small>Feel free to checkout any of these users.</small>
											</header>
                                <div class="row" style="margin-top:15px;">
                                    @foreach($users as $user)
																	<div class="col-md-3 col-sm-4 col-xs-12">
																					<div class="card type--profile m-10">
																						<header class="card-heading  card-blue">
                                                                                            @if($user->avatar != 'default.jpg')
							<img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" style="width:85px;height:85px;margin-left:-45px;" alt="" class="img-circle profile-mini">                                                                                     @else
							<img src="/assets/img/default.png" style="width:85px;height:85px;margin-left:-45px;" alt="" class="img-circle profile-mini">

                                                                                            @endif
																							</header>
																							<div class="card-body">
																								<h3 class="name">{{$user->username}}</h3>
																								<span class="title"></span>
																								<a href="/user/{{$user->username}}" class="btn btn-primary btn-round  btn-sm">Profile</a>
																							</div>
																							<div class="card-footer border-top">
																								<ul class="card-actions left-bottom">
																									<li>
																										<a href="javascript:void(0)" class="btn btn-default btn-flat">
                                                                                                            @if($user->getprofit() > 0)
																											Profit: <span style="color:#73c04d">{!! $symbol !!}{{number_format((($user->getNetWorthNew(Auth::user()->api) * $multiplier) - $user->getInvested(Auth::user()->getCurrency())), 2)}}{!! $symbol2 !!}</span>
                                                                                                            @else
																											Profit: <span style="color:#de6b6b">{!! $symbol !!}{{number_format((($user->getNetWorthNew(Auth::user()->api) * $multiplier) - $user->getInvested(Auth::user()->getCurrency())), 2)}}{!! $symbol2 !!}</span>
                                                                                                            @endif
																										</a>
																									</li>
																					<li>
																										<a href="javascript:void(0)" class="btn btn-default btn-flat">
																											Invested: <span> {{ $symbol }}{{number_format($user->getInvested(Auth::user()->getCurrency()), 2)}}{{ $symbol2 }}</span>
																										</a>
																									</li>
																								</ul>
																								<ul class="card-actions icons right-bottom">
																									<li>
																										<a href="/follow/{{$user->username}}">
																											<i class="zmdi zmdi-notifications"></i>
																										</a>
																									</li>
																								</ul>
																							</div>
																						</div>
																					</div>
                                    @endforeach
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

@endsection
