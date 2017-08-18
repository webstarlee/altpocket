<!DOCTYPE html>

<?php

if(Auth::user())
{
  $multiplier = Auth::user()->getMultiplier();
  $api = Auth::user()->api;
  $currency = Auth::user()->getCurrency();

  // Keys
  $polokey = DB::table('keys')->where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Poloniex']])->first();
  $bittrexkey = DB::table('keys')->where([['userid', '=', Auth::user()->id], ['exchange', '=', 'Bittrex']])->first();

  // Notifications
  $notifications = Auth::user()->unreadnotifications->take(4);


  if($currency != 'BTC' && $currency != 'USD' && $currency != 'CAD')
  {
    $fiat = DB::table('multipliers')->where('currency', $currency)->select('price')->first()->price;
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

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="Altpocket.io is the best portfolio available for showcasing altcoin and cryptocurrency investments. Register now for free! Import orders from Bittrex and Poloniex.">
        <meta name="keywords" content="portfolio,altcoins,cryptocurrency,xrp,btc,showcase,investments,bitcoins,ripple,litecoin,show,altpocket,alt pocket, alt,pocket,altpocket.io,altcoin portfolio,tracking,import,bittrex,poloniex">
    <meta name="author" content="Edwin">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="og:image" content="/share.png?v=1.1" />
    <meta name="og:url" content="https://altpocket.io" />
    <meta name="og:title" content="Altpocket.io - @yield('title')" />
    <meta name="og:description" content="Altpocket.io is the best portfolio available for showcasing altcoin and cryptocurrency investments. Register now for free! Import orders from Bittrex and Poloniex." />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@altpocket" />
    <meta name="twitter:title" content="Altpocket.io - @yield('title')" />
    <meta name="twitter:description" content="Altpocket.io is the best portfolio available for showcasing altcoin and cryptocurrency investments. Register now for free! Import orders from Bittrex and Poloniex." />
    <meta name="twitter:image" content="https://altpocket.io/share.png?v=1.1" />
    <meta name="twitter:image:alt" content="Altpocket.io - The social portfolio" />
    <title>Altpocket.io - @yield('title')</title>
    <link type="text/css" rel="stylesheet" href="/assets/css/vendor.bundle.css" >
    <link type="text/css" rel="stylesheet" href="/assets/css/app.bundle.css">
    <link type="text/css" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    @if(Auth::user())
        @if(Auth::user()->theme == "dark")
    <link rel="stylesheet" href="/assets/css/theme-dark.css?v=22">
        @else
        <link rel="stylesheet" href="/assets/css/theme-c.css">
        @endif

    @else
    <link rel="stylesheet" href="/assets/css/theme-c.css">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <link type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
    <link type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
      rel="stylesheet">
    <link rel="icon"
      type="image/gif"
      href="/assets/logo.png">

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-98999843-1', 'auto');
      @if(Auth::user())
      ga('set', 'userId', {{Auth::user()->id}}); // Ange användar-id med inloggat user_id.
      @endif
      ga('send', 'pageview');
    </script>
    <style>
    .tooltip-inner {
        max-width: 400px;
        width: auto;
        }

    </style>

    @if(Auth::user())
    @if(Auth::user()->theme != "dark")
    <style>
    body::-webkit-scrollbar {
        width: 1em;
    }
    body::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    }
    body::-webkit-scrollbar-thumb {
        background-color: rgb(34, 47, 60);
        outline: 1px solid slategrey;
    }
    </style>
    @else
      <style>
    body::-webkit-scrollbar {
        width: 1em;
    }
    body::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    }
    body::-webkit-scrollbar-thumb {
        background-color: rgb(45, 45, 45);
        outline: 1px solid slategrey;
    }
    </style>
    @endif
    @endif

    @yield('css')
</head>
<body>
<div id="app_wrapper">
    <header id="app_topnavbar-wrapper">
        <nav role="navigation" class="navbar topnavbar">
            <div class="nav-wrapper">
                <ul class="nav navbar-nav pull-left left-menu">
                    <li class="app_menu-open">
                        <a href="javascript:void(0)" data-toggle-state="app_sidebar-left-open" data-key="rightSideBar">
                            <i class="zmdi zmdi-menu"></i>
                        </a>
                    </li>
                </ul>
                @if(Auth::user())
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown avatar-menu">
                        <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                <span class="meta">
                  <span class="avatar">
                    @if(Auth::user()->avatar != "default.jpg")
                        <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="" class="img-circle max-w-35">
                    @else
                        <img src="/assets/img/logo.png" alt="" class="img-circle max-w-35">
                    @endif

                    <i class="badge mini success status"></i>
                  </span>
                  <span class="name">{{Auth::user()->username}}</span>
                  <span class="caret"></span>
                </span>
                        </a>
                        <ul class="dropdown-menu btn-primary dropdown-menu-right">
                            <li>
                                <a href="/user/{{Auth::user()->username}}"><i class="zmdi zmdi-account"></i> Profile</a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="zmdi zmdi-sign-in"></i> Logout</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown hidden-xs hidden-sm">
                        <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                            @if(count($notifications) >= 1)
				                        <span class="badge mini status danger"></span>
                            @endif
                            <i class="zmdi zmdi-notifications"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-lg-menu dropdown-menu-right dropdown-alt">
                            <li class="dropdown-menu-header">
                                <ul class="card-actions icons  left-top">
                                </ul>
                                <h5>Notifications</h5>
                                <ul class="card-actions icons right-top">
                                    <li>
                                        <a href="/notification/readall/">
                                            <i class="zmdi zmdi-check-all"></i>
                                        </a>
                                    </li>
                                </ul>
                            </li>
							<li>

                                        <?php $count = 0; ?>
                                        @foreach($notifications as $notification)

                                        @if($count <= 4)
                                            @if($notification->data[0]["type"] == "investment")
                                                <div class="card {{$notification->id}}">
                                                    <a href="javascript:void(0)" class="pull-right dismiss remove-notification" id="{{$notification->id}}" data-dismiss="close">
                                                        <i class="zmdi zmdi-close"></i>
                                                    </a>
                                                    <div class="card-body new_investment" id="{{$notification->data[0]['user']}}">
                                                        <ul class="list-group ">
                                                            <li class="list-group-item ">
                                                                <span class="pull-left"><i style="font-size:25px;margin-right:15px" class="{{$notification->data[0]["icon"]}}"></i></span>
                                                                <div class="list-group-item-body">
                                                                    <div class="list-group-item-heading">{{$notification->data[0]["title"]}}</div>
                                                                    <div class="list-group-item-text">{{$notification->data[0]["data"]}}</div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($notification->data[0]["type"] == "comment")
                                                <div class="card {{$notification->id}}">
                                                    <a href="javascript:void(0)" class="pull-right dismiss remove-notification" id="{{$notification->id}}" data-dismiss="close">
                                                        <i class="zmdi zmdi-close"></i>
                                                    </a>
                                                    <div class="card-body new_comment">
                                                        <ul class="list-group ">
                                                            <li class="list-group-item ">
                                                                <span class="pull-left"><i style="font-size:25px;margin-right:15px" class="{{$notification->data[0]["icon"]}}"></i></span>
                                                                <div class="list-group-item-body">
                                                                    <div class="list-group-item-heading"><a href="#" style="padding:0px!important;line-height:20px!important;font-size:11.375px!important;">{{$notification->data[0]["title"]}}</a></div>
                                                                    <div class="list-group-item-text">{{$notification->data[0]["data"]}}</div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($notification->data[0]["type"] == "tag")
                                                <div class="card {{$notification->id}}">
                                                    <a href="javascript:void(0)" class="pull-right dismiss remove-notification" id="{{$notification->id}}" data-dismiss="close">
                                                        <i class="zmdi zmdi-close"></i>
                                                    </a>
                                                    <div class="card-body new_comment">
                                                        <ul class="list-group ">
                                                          <a href="/dashboard2?status={{$notification->data[0]['status']}}" style="padding:0px!important;line-height:20px!important;">
                                                            <li class="list-group-item ">
                                                                <span class="pull-left"><i style="font-size:25px;margin-right:15px" class="{{$notification->data[0]["icon"]}}"></i></span>
                                                                <div class="list-group-item-body">
                                                                    <div class="list-group-item-heading">{{$notification->data[0]["title"]}}</div>
                                                                    <div class="list-group-item-text">{{$notification->data[0]["data"]}}</div>
                                                                </div>
                                                            </li>
                                                          </a>
                                                        </ul>

                                                    </div>
                                                </div>
                                            @endif
                                            @if($notification->data[0]["type"] == "statuscomment")
                                                <div class="card {{$notification->id}}">
                                                    <a href="javascript:void(0)" class="pull-right dismiss remove-notification" id="{{$notification->id}}" data-dismiss="close">
                                                        <i class="zmdi zmdi-close"></i>
                                                    </a>
                                                    <div class="card-body new_comment">
                                                        <ul class="list-group ">
                                                          <a href="/dashboard2?status={{$notification->data[0]['status']}}" style="padding:0px!important;line-height:20px!important;">
                                                            <li class="list-group-item ">
                                                                <span class="pull-left"><i style="font-size:25px;margin-right:15px" class="{{$notification->data[0]["icon"]}}"></i></span>
                                                                <div class="list-group-item-body">
                                                                    <div class="list-group-item-heading">{{$notification->data[0]["title"]}}</div>
                                                                    <div class="list-group-item-text">{{$notification->data[0]["data"]}}</div>
                                                                </div>
                                                            </li>
                                                          </a>
                                                        </ul>

                                                    </div>
                                                </div>
                                            @endif
                                            @if($notification->data[0]["type"] == "question")
                                                <div class="card {{$notification->id}}">
                                                    <a href="javascript:void(0)" class="pull-right dismiss remove-notification" id="{{$notification->id}}" data-dismiss="close">
                                                        <i class="zmdi zmdi-close"></i>
                                                    </a>
                                                    <div class="card-body new_comment">
                                                        <ul class="list-group ">
                                                          <a href="/question/{{$notification->data[0]["question"]}}" style="padding:0px!important;line-height:20px!important;">
                                                            <li class="list-group-item ">
                                                                <span class="pull-left"><i style="font-size:25px;margin-right:15px" class="{{$notification->data[0]["icon"]}}"></i></span>
                                                                <div class="list-group-item-body">
                                                                    <div class="list-group-item-heading">{{$notification->data[0]["title"]}}</div>
                                                                    <div class="list-group-item-text">{{$notification->data[0]["data"]}}</div>
                                                                </div>
                                                            </li>
                                                          </a>
                                                        </ul>

                                                    </div>
                                                </div>
                                            @endif
                                            @if($notification->data[0]["type"] == "news")
                                                <div class="card {{$notification->id}}">
                                                    <a href="javascript:void(0)" class="pull-right dismiss remove-notification" id="{{$notification->id}}" data-dismiss="close">
                                                        <i class="zmdi zmdi-close"></i>
                                                    </a>
                                                    <div class="card-body">
                                                        <ul class="list-group ">
                                                            <li class="list-group-item ">
                                                                <span class="pull-left"><i style="font-size:25px;margin-right:15px" class="{{$notification->data[0]["icon"]}}"></i></span>
                                                                <div class="list-group-item-body">
                                                                    <div class="list-group-item-heading">{{$notification->data[0]["title"]}}</div>
                                                                    <div class="list-group-item-text">{{$notification->data[0]["data"]}}</div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                            <?php $count += 1; ?>
                                        @endforeach
								</li>

                        </ul>
                    </li>
            <li class="last">
              <a href="javascript:void(0)" data-toggle-state="sidebar-overlay-open" data-key="rightSideBar">
                <i class="mdi mdi-playlist-plus"></i>
              </a>
            </li>
                </ul>
              @else
                          <ul class="nav navbar-nav pull-right">
                            <li class="last">
                              <a href="/login" id="signup">
                                Sign up/Login   <i style="font-size:15px!important;" class="zmdi zmdi-sign-in"></i>
                              </a>
                            </li>
                          </ul>
              @endif
            </div>
        </nav>
    </header>

    <aside id="app_sidebar-left">
        <div id="logo_wrapper">
            <ul>
                <li class="logo-icon">
                    <a href="/dashboard">
                        <div class="logo">
                            <img src="/assets/logo.png" alt="Logo" style="width:40px;height:40px;left:5px;">
                        </div>
                        <h1 class="brand-text" style="padding:8px 0 0 55px;">Altpocket.io</h1>
                    </a>
                </li>
					<li class="menu-icon">
						<a href="javascript:void(0)" role="button" data-toggle-state="app_sidebar-menu-collapsed" data-key="leftSideBar">
							<i class="mdi mdi-backburger"></i>
						</a>
					</li>
            </ul>
        </div>
        <nav id="app_main-menu-wrapper" class="fadeInLeft scrollbar">
            <div class="sidebar-inner sidebar-push">
                <ul class="nav nav-pills nav-stacked">
                    <li class="sidebar-header">Altpocket</li>
                    @if(Auth::user())
                    <li><a href="/dashboard"><i class="zmdi zmdi-view-dashboard"></i>Dashboard</a></li>
                    <li><a href="/blog"><i class="fa fa-rss"></i>Blog</a></li>
                    <li><a href="javascript:void(0)" id="stat-widget"><i class="zmdi zmdi-image-alt"></i>Stat Widget</a></li>
                    <li><a href="javascript:void(0)" id="api-keys"><i class="zmdi zmdi-shield-security"></i>API Keys</a></li>
                    @if(Auth::user()->isFounder())
                    <li><a href="javascript:void(0)" id="balances"><i class="zmdi zmdi-balance-wallet"></i>Sources</a></li>
                    @endif
                    @if(Auth::user()->google2fa_secret)
                    <li><a href="/2fa/request/disable"><i class="fa fa-unlock-alt" aria-hidden="true"></i>Disable 2FA</a></li>
                    @else
                    <li><a href="/2fa/request"><i class="fa fa-lock" aria-hidden="true"></i>Enable 2FA</a></li>
                    @endif
                    <li class="sidebar-header">Coins</li>
                    <li class="nav-dropdown"><a href="#"><i class="zmdi zmdi-trending-up"></i>My Investments</a>
							<ul class="nav-sub">
								<li><a href="/investments/"><i class="zmdi zmdi-folder"></i> Investments</a></li>
								<li><a href="/investments2/"><i class="zmdi zmdi-folder"></i> Investments 2.0 (BETA)</a></li>
                                  @foreach(Auth::user()->getInvestments() as $investment)
                                    @if($investment->sold_at == null)
                                      @if($investment->market == "BTC")
                                      <?php
                                          $price = Auth::user()->getPrice($investment->currency, $investment->market, $investment->exchange);
                                          $previous = 0;
                                          $decimal1 = 2;
                                          if(Auth::user()->getCurrency() == "USD")
                                          {
                                            $previous = $investment->btc_price_bought_usd;
                                          } elseif(Auth::user()->getCurrency() == "BTC")
                                          {
                                            $decimal1 = 5;
                                            $previous = 1;
                                          } else {
                                            $previous = $investment->btc_price_bought_usd * $fiat;
                                          }
                                          $profit = (($investment->amount * $price) * $multiplier) - (($investment->amount * $investment->bought_at) * $previous);
                                       ?>

                                          <li><a href="javascript:void(0)">
                                            <img src="/icons/32x32/{{$investment->currency}}.png" style="width:16px;"/>
                                           {{$investment->currency}} <span @if($profit > 0) style="color:#73c04d;" @else style="color:#de6b6b" @endif>({!! $symbol !!}{{number_format($profit, $decimal1)}}{!! $symbol2 !!})</span></a></li>
                                       @endif
                                     @if($investment->market == "USDT")
                                       <?php
                                           $price = Auth::user()->getPrice($investment->currency, $investment->market, $investment->exchange);
                                           $previous = 0;
                                           $btc = DB::table('cryptos')->where('symbol', 'BTC')->first()->price_usd;
                                           $decimal1 = 2;
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
                                           $profit = ((($investment->amount * $price) / ($btc)) * $multiplier) - ((($investment->amount * $investment->bought_at) / ($previous) * $previousmultiplier));
                                        ?>

                                            <li><a href="javascript:void(0)">
                                              <img src="/icons/32x32/{{$investment->currency}}.png" style="width:16px;"/>
                                            {{$investment->currency}} <span @if($profit > 0) style="color:#73c04d;" @else style="color:#de6b6b" @endif>({!! $symbol !!}{{number_format($profit, $decimal1)}}{!! $symbol2 !!})</span></a></li>
                                     @endif

                                     @if($investment->market == "ETH")
                                       <?php
                                           $price = Auth::user()->getPrice($investment->currency, $investment->market, $investment->exchange);
                                           $multiplier = Auth::user()->getEthMultiplier();
                                           $previous = 0;
                                           $eth = DB::table('cryptos')->where('symbol', 'ETH')->first()->price_usd;
                                           $decimal1 = 2;
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
                                             $previous = $investment->btc_price_bought_usd;
                                             $prevmultiplier = ($investment->btc_price_bought_usd / $investment->btc_price_bought_eth) * $fiat;
                                           }
                                           $profit = ((($investment->amount * $price)) * $multiplier) - ((($investment->amount * $investment->bought_at) * $prevmultiplier));
                                        ?>

                                        <li><a href="javascript:void(0)">
                                          <img src="/icons/32x32/{{$investment->currency}}.png" style="width:16px;"/>
                                            {{$investment->currency}} <span @if($profit > 0) style="color:#73c04d;" @else style="color:#de6b6b" @endif>({!! $symbol !!}{{number_format($profit,$decimal1)}}{!! $symbol2 !!})</span></a></li>
                                     @endif
                                   @endif
                                  @endforeach





							</ul>
                    </li>
                    <li class="sidebar-header">Social</li>
                    <li><a href="/leaderboards"><i class="fa fa-trophy"></i>Leaderboards</a></li>
                    <li><a href="https://twitter.com/altpocket"><i class="fa fa-twitter"></i>Our Twitter</a></li>
                    <li><a href="https://altpocket.io/discord"><i class="fa fa-comments"></i>Discord Bot</a></li>
                    <li class="sidebar-header">Other</li>

                    <li class="nav-dropdown"><a href="#"><i class="zmdi zmdi-help"></i>Support</a>
        							<ul class="nav-sub">
                        <li><a href="/support">Support Center</a></li>
                        <li><a href="https://discord.gg/wJXwRrU">Support Discord</a></li>
                      </ul>
                    </li>

                    <li><a href="/importing-orders"><i class="fa fa-download"></i>Importing Orders</a></li>
                    <li><a href="/about"><i class="fa fa-shield"></i>About</a></li>
                    <li><a href="#" id="secret-message"><i class="fa fa-heart" style="color:#ef5350"></i>Support Us</a>
                    </li>






                    <li><a href="/badges/"><i class="zmdi zmdi-star"></i>Badges</a></li>
                    @else
                    <li><a href="/login"><i class="zmdi zmdi-view-dashboard"></i>Login/Register</a></li>
                    <li><a href="/blog"><i class="fa fa-rss"></i>Blog</a></li>
                    <li class="sidebar-header">Social</li>
                    <li><a href="/leaderboards"><i class="fa fa-trophy"></i>Leaderboards</a></li>
                    <li><a href="https://twitter.com/altpocket"><i class="fa fa-twitter"></i>Our Twitter</a></li>
                    <li><a href="https://altpocket.io/discord"><i class="fa fa-comments"></i>Discord Bot</a></li>
                    <li class="sidebar-header">Other</li>
                    <li class="nav-dropdown"><a href="#"><i class="zmdi zmdi-help"></i>Support</a>
        							<ul class="nav-sub">
                        <li><a href="/support">Support Center</a></li>
                        <li><a href="https://discord.gg/wJXwRrU">Support Discord</a></li>
                      </ul>
                    </li>
                    <li><a href="/importing-orders"><i class="fa fa-download"></i>Importing Orders</a></li>
                    <li><a href="/staff"><i class="fa fa-shield"></i>The team</a></li>
                    <li><a href="/badges/"><i class="zmdi zmdi-star"></i>Badges</a></li>
                    @endif
                </ul>
            </div>
        </nav>
    </aside>
    <section id="content_outer_wrapper" style="padding-bottom:0px;">
    @yield('content')



    </section>
    @if(Auth::user())
                                <aside id="app_sidebar-right" style="z-index:1500">
                                  <div class="sidebar-inner sidebar-overlay" style="z-index:1500">
                                    <div class="tabpanel">
                                      <ul class="nav nav-tabs nav-justified">
                                        <li class="active" role="presentation"><a href="#sidebar_settings" data-toggle="tab">Settings</a></li>
                                      </ul>
                                      <div class="tab-content">
                                        <div class="tab-pane fade active in" id="sidebar_settings">
                                          <div class="color-container">
                                            <h3 class="title">Theme Settings</h3>
                                            <div class="row">
                                               <div class="togglebutton m-b-15 ">
                                                  <label>
                                                    <input id="change-theme" type="checkbox" class="toggle-primary" @if(Auth::user()->theme == "dark") checked @endif> Enable night theme
                                                  </label>
                                                </div>
                                              </div>
                                              <h3 class="title m-t-30">Currency</h3>
                                              <div class="row">
                                                    <select class="select form-control" id="change-currency" name="currency" style="padding:15px;">
                                                          <option value="BTC" @if(Auth::user()->currency == "BTC") selected="selected" @endif>Bitcoins (BTC)</option>
                                                          <option value="USD" @if(Auth::user()->currency == "USD") selected="selected" @endif>US Dollars (USD)</option>
                                                          <option value="EUR" @if(Auth::user()->currency == "EUR") selected="selected" @endif>Euro (EUR)</option>
                                                          <option value="GBP" @if(Auth::user()->currency == "GBP") selected="selected" @endif>British Pound (GBP)</option>
                                                          <option value="SEK" @if(Auth::user()->currency == "SEK") selected="selected" @endif>Swedish Krona (SEK)</option>
                                                          <option value="NOK" @if(Auth::user()->currency == "NOK") selected="selected" @endif>Norwegian Kroner (NOK)</option>
                                                          <option value="DKK" @if(Auth::user()->currency == "DKK") selected="selected" @endif>Danish Krona (DKK)</option>
                                                          <option value="SGD" @if(Auth::user()->currency == "SGD") selected="selected" @endif>Singapore Dollar (SGD)</option>
                                                          <option value="CAD" @if(Auth::user()->currency == "CAD") selected="selected" @endif>Canadian Dollar (CAD)</option>
                                                          <option value="AUD" @if(Auth::user()->currency == "AUD") selected="selected" @endif>Australian Dollar (AUD)</option>
                                                          <option value="INR" @if(Auth::user()->currency == "INR") selected="selected" @endif>Indian Rupee (INR)</option>
                                                          <option value="MYR" @if(Auth::user()->currency == "MYR") selected="selected" @endif>Malaysian Ringgit (MYR)</option>
                                                        </select>
                                                </div>
                                              <h3 class="title m-t-30">Price Gathering API</h3>
                                            <div class="row">
                                                  <select class="select form-control" id="change-api" name="api" style="padding:15px;">
                                                        <option value="coinmarketcap" @if(Auth::user()->api == "coinmarketcap") selected="selected" @endif>CoinMarketCap.com (Recommended)</option>
                                                        <option value="worldcoinindex" @if(Auth::user()->api == "worldcoinindex") selected="selected" @endif>WorldCoinIndex.com (Recommended)</option>
                                                        <option value="bittrex" @if(Auth::user()->api == "bittrex") selected="selected" @endif>Bittrex.com</option>
                                                        <option value="poloniex" @if(Auth::user()->api == "poloniex") selected="selected" @endif>Poloniex.com</option>
                                                      </select>
                                              </div>
                                              <h3 class="title m-t-30">Email Notifications</h3>
                                            <div class="row">
                                               <div class="togglebutton m-b-15 ">
                                                  <label>
                                                    <input id="toggle-noti" type="checkbox" class="toggle-primary" @if(Auth::user()->email_notifications == "on") checked @endif> Email Notifications
                                                  </label>
                                                </div>
                                              </div>
                                              <h3 class="title m-t-30">Condensed Investments</h3>
                                            <div class="row">
                                               <div class="togglebutton m-b-15 ">
                                                  <label>
                                                    <input id="toggle-summed" type="checkbox" class="toggle-primary" @if(Auth::user()->summed == 1) checked @endif> Condensed Investments
                                                  </label>
                                                </div>
                                              </div>
                                              <h3 class="title m-t-30">Referral link</h3>
                                                    @if(Auth::user()->affiliate_id)
                                                                    <div class="form-group">
                                                                        <div class="col-md-12">
                                                                          <input readonly="readonly" type="text" class="form-control" id="name" placeholder="Name" value="{{url('/').'/?ref='.Auth::user()->affiliate_id}}">
                                                                        </div>
                                                                      </div>
                                                    @else
                                                    @php
                                                        $user = Auth::user();
                                                        $user->affiliate_id = str_random(10);
                                                        $user->save();
                                                    @endphp
                                                    @endif
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </aside>




                                <div class="modal fade" id="stat_modal" tabindex="-1" role="dialog" aria-labelledby="stat_modal">
                  				<div class="modal-dialog" role="document">
                  					<div class="modal-content">
                  						<div class="modal-header">

                  							<h4 class="modal-title" id="myModalLabel-2">Altpocket Stat Widget/Image</h4>
                  							<ul class="card-actions icons right-top">
                  							<li>
                  								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                  									<i class="zmdi zmdi-close"></i>
                  								</a>
                  							</li>
                  						</ul>
                  					</div>
                  					<div class="modal-body">
                              <p>This is where you can generate an Altpocket.io Stat Widget, the widget automaticially updates every 10 minutes. Remember that you must activate the widget to actually use it.<p>
                                <div class="row">
                                   <div class="togglebutton m-b-15 " style="text-align:center;">
                                      <label>
                                        <input id="toggle-widget" type="checkbox" class="toggle-primary" @if(Auth::user()->widget == "on") checked @endif> Enable Widget
                                      </label>
                                    </div>
                                  </div>
                                  @if(Auth::user()->widget == "on")
                                  <div class="row">
                                    <img src="https://altpocket.io/uploads/signatures/{{Auth::user()->username}}.png" style="padding:15px;">
                                    <br>
                                    <p>Image URL</img>
                                    <textarea style="width:100%" rows=1>https://altpocket.io/uploads/signatures/{{Auth::user()->username}}.png</textarea>
                                    <p>BB Code</p>
                                    <textarea style="width:100%" rows=3>[align=center][url=http://altpocket.io/user/{{Auth::user()->username}}/][img]https://altpocket.io/uploads/signatures/{{Auth::user()->username}}.png[/img][/url][/align]</textarea>
                                    <p>HTML Code</p>
                                    <textarea style="width:100%" rows=3><center><a href="http://altpocket.io/user/{{Auth::user()->username}}/"><img src="https://altpocket.io/uploads/signatures/{{Auth::user()->username}}.png"/></a></center></textarea>
                                  </div>
                                @endif


                              </div>

                  					</div>
                  					<!-- modal-content -->
                  				</div>
                  				<!-- modal-dialog -->
                  			</div>

    @endif

</div>

@if(Auth::user())
  <div class="modal fade" id="api-modal" tabindex="-1" role="dialog" aria-labelledby="api-modal">
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
  <p>Your API keys are always safely stored and encrypted, please also make sure you are not using the same API key somewhere else than Altpocket.</p>
  <div class="tabpanel">
    <ul class="nav nav-tabs nav-justified">
      <li class="active" role="presentation"><a href="#tab-13" data-toggle="tab" aria-expanded="true">Poloniex</a></li>
      <li role="presentation"><a href="#tab-14" data-toggle="tab" aria-expanded="true">Bittrex</a></li>
    </ul>
  </div>
<div class="tab-content">
<div class="tab-pane fadeIn active" id="tab-13">
  <form id="form-horizontal" role="form" method="post" action="/keys/save/polo">
                 {{ csrf_field() }}
                    <div class="form-group is-empty">
                <label for="" class="control-label">Poloniex Public API key</label>
                          <input type="text" class="form-control" id="publickey" autocomplete="off" @if($polokey) value="{{decrypt($polokey->public)}}" @endif name="polo_api_public"/>
                    </div>
                    <div class="form-group is-empty">
                <label for="" class="control-label">Poloniex Secret API Key</label>
                          <input type="text" class="form-control" id="privatekey" autocomplete="off" @if($polokey) value="{{decrypt($polokey->private)}}" @endif name="polo_api_private"/>
                    </div>
                  <button type="submit" class="btn btn-primary">Save Poloniex Keys</button>
              </form>
</div>
<div class="tab-pane fadeIn" id="tab-14">
  <form id="form-horizontal" role="form" method="post" action="/keys/save/bittrex">
                 {{ csrf_field() }}
                    <div class="form-group is-empty">
                <label for="" class="control-label">Bittrex Public API key</label>
                          <input type="text" class="form-control" id="publickey" autocomplete="off" @if($bittrexkey) value="{{decrypt($bittrexkey->public)}}" @endif name="bittrex_api_public"/>
                    </div>
                    <div class="form-group is-empty">
                <label for="" class="control-label">Bittrex Secret API Key</label>
                          <input type="text" class="form-control" id="privatekey" autocomplete="off" @if($bittrexkey) value="{{decrypt($bittrexkey->private)}}" @endif name="bittrex_api_private"/>
                    </div>
                  <button type="submit" class="btn btn-primary">Save Bittrex Keys</button>
              </form>
</div>

</div>

</div>
</div>
<!-- modal-content -->
</div>
<!-- modal-dialog -->
</div>



@if(Auth::user()->isFounder())
  <div class="modal fade" id="balances-modal" tabindex="-1" role="dialog" aria-labelledby="balances-modal">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">

  <h4 class="modal-title" id="myModalLabel-2">Sources</h4>
  <ul class="card-actions icons right-top">
  <li>
    <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
      <i class="zmdi zmdi-close"></i>
    </a>
  </li>
</ul>
</div>
<div class="modal-body">
  <button class="btn btn-info btn-fab btn-fab-sm" style="float:right;" data-toggle="modal" data-target="#add_new_modal"><i class="zmdi zmdi-plus"></i><div class="ripple-container"></div></button>
  <p>Here is a list of all your sources that are connected to Altpocket.</p>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Type</th>
        <th>Public Key</th>
        <th>Platform</th>
        <th>Options</th>
      </tr>
    </thead>
    <tbody>
      @foreach(\App\Key::where('userid', 1)->get() as $key)
        <tr class="zel-{{$key->id}}">
          <th scope="row">{{$key->type}}</th>
          <td>{{decrypt($key->public)}}</td>
          <td>{{$key->exchange}}</td>
          <td style="text-align:center;"><a href="javascript:void(0)" id="{{$key->id}}" class="remove-key"><i class="zmdi zmdi-delete"></i></a></td>
        </tr>
      @endforeach
    </tbody>
  </table>

</div>
</div>
<!-- modal-content -->
</div>
<!-- modal-dialog -->
</div>

<div class="modal fade" id="add_new_modal" tabindex="-1" role="dialog" aria-labelledby="add_new_modal">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">

<h4 class="modal-title" id="myModalLabel-2">Add new Source</h4>
<ul class="card-actions icons right-top">
<li>
  <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
    <i class="zmdi zmdi-close"></i>
  </a>
</li>
</ul>
</div>
<div class="modal-body">
<p>Your API keys are always safely stored and encrypted, please also make sure you are not using the same API key somewhere else than Altpocket.</p>

<form id="form-horizontal" role="form" method="post" action="/sources/add/">
               {{ csrf_field() }}
                    <div class="form-group">
                        <label for="" class="control-label">Type</label>
                        <select class="select form-control" id="sourcetype" name="sourcetype">
                          <option value="">Select type</option>
                          <option value="Poloniex">Poloniex API Keys</option>
                          <option value="Bittrex">Bittrex API Keys</option>
                          <option value="Coinbase">Coinbase API Keys</option>
                          <option value="Ethwallet">Ethereum Wallet</option>
                          <option value="Ethnano">Ethereum Nanopool</option>
                          <option value="Ethermine">Ethermine</option>
                          <option value="Nicehash">Nicehash</option>
                        </select>
                  </div>

              <div id="exchange" style="display:none;">
                  <div class="form-group is-empty">
                    <label for="" class="control-label">Public API Key</label>
                    <input type="text" class="form-control" id="publickey" autocomplete="off" value="" name="publickey"/>
                  </div>
                  <div class="form-group is-empty">
                    <label for="" class="control-label">Secret API key</label>
                    <input type="text" class="form-control" id="privatekey" autocomplete="off" value="" name="privatekey"/>
                  </div>
              </div>
              <div id="nicehash" style="display:none;">
                  <div class="form-group is-empty">
                    <label for="" class="control-label">API ID</label>
                    <input type="text" class="form-control" id="publickey" autocomplete="off" value="" name="apiid"/>
                  </div>
                  <div class="form-group is-empty">
                    <label for="" class="control-label">Read Only API key</label>
                    <input type="text" class="form-control" id="privatekey" autocomplete="off" value="" name="readOnly"/>
                  </div>
              </div>
              <div id="wallet" style="display:none;">
                <div class="form-group is-empty">
                  <label for="" class="control-label">Address</label>
                  <input type="text" class="form-control" id="privatekey" autocomplete="off" value="" name="address"/>
                </div>
              </div>
              <div id="miner" style="display:none;">
                <div class="form-group is-empty">
                  <label for="" class="control-label">Miner address/account</label>
                  <input type="text" class="form-control" id="privatekey" autocomplete="off" value="" name="account"/>
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Add Source</button>
</form>



</div>
</div>
<!-- modal-content -->
</div>
<!-- modal-dialog -->
</div>
@endif

@endif






        			<div class="modal fade" id="secret_modal" tabindex="-1" role="dialog" aria-labelledby="secret_modal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">

							<h4 class="modal-title" id="myModalLabel-2">Supporting Altpocket.io</h4>
							<ul class="card-actions icons right-top">
							<li>
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
            <p>Thank you for considerating a donation. We strongly believe in Altpocket and that's we quit our jobs to work on it fulltime.<br>
              All costs are currently being payed by Edwin and Svensson's own pockets as of today so please help us with expenses such as:
            </p>
              <ul style="list-style:circle;margin-left:15px;">
                <li>Food</li>
                <li>Red Bull</li>
                <li>Server Costs</li>
                <li>Savings for more developers</li>
              </ul>
              <p>Donating $5+ gives you a <strong>Donator rank</strong> and badge. (Donating over $100 gives you secret access and a "big guy" badge.)</p>
            <form action="https://www.coinpayments.net/index.php" method="post" style="text-align:center;">
            	<input type="hidden" name="cmd" value="_donate">
            	<input type="hidden" name="reset" value="1">
            	<input type="hidden" name="merchant" value="c386109d2fec3ae0327d66e05f705754">
            	<input type="hidden" name="item_name" value="Support Altpocket">
            	<input type="hidden" name="currency" value="USD">
	            <input type="hidden" name="amountf" value="5">
            	<input type="hidden" name="allow_amount" value="1">
            	<input type="hidden" name="want_shipping" value="0">
            	<input type="hidden" name="item_number" value="@if(Auth::user()){{Auth::user()->id}}@else{{1-1}}@endif">
            	<input type="hidden" name="ipn_url" value="https://altpocket.io/donate/post">
            	<input type="hidden" name="allow_extra" value="1">
            	<input type="image" src="https://www.coinpayments.net/images/pub/donate-med-grey.png" alt="Donate with CoinPayments.net">
            </form>
						</div>
					</div>
					<!-- modal-content -->
				</div>
				<!-- modal-dialog -->
			</div>
@yield('earlyjs')

<script src="/assets/js/vendor.bundle.js"></script>
<script src="/assets/js/app.bundle.js?v=3.1"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.min.js"></script>
<script src="https://js.pusher.com/4.0/pusher.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@include('sweet::alert')


<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>

        @stack('scripts')

@yield('js')
<script>
@if(Auth::user())
// Enable pusher logging - don't include this in production

var pusher = new Pusher('bb16a7f59cce954fea75', {
  cluster: 'eu',
  encrypted: true,
  authEndpoint: '/pusher/auth',
  auth: {
    headers: {
      'X-CSRF-Token': "{{csrf_token()}}"
    }
  }
});


var channel = pusher.subscribe('private-App.User.{{Auth::user()->id}}');
var channel2 = pusher.subscribe('announcements');
channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "0",
    "hideDuration": "0",
    "timeOut": "0",
    "extendedTimeOut": "0",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }
    Command: toastr[data.category](data.value)

    if(data.value == "Your import is complete! Go to your investments to see your import" && window.location.pathname == "/investments")
    {
      location.reload();
    }
    if(data.value == "Your import is complete! Go to your investments to see your import" && window.location.pathname == "/investments/")
    {
      location.reload();
    }

});
channel2.bind('announcement', function(data) {

  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "0",
    "hideDuration": "0",
    "timeOut": "0",
    "extendedTimeOut": "0",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }
    Command: toastr["success"](data.value)
});




@endif
</script>




    <script>

        $("#secret-message").click(function(){
           $("#secret_modal").modal('toggle');
           ga('send', {
            hitType: 'event',
            eventCategory: 'Click',
            eventAction: 'donate',
            eventLabel: 'Donate Button'
          });
        });

        $("#signup").click(function(){
          ga('send', {
           hitType: 'event',
           eventCategory: 'Click',
           eventAction: 'signup',
           eventLabel: 'Sign Up Button'
         });
        });

        $("#report-bug").click(function(){
           $("#bug_modal").modal('toggle');
        });
        $("#stat-widget").click(function(){
           $("#stat_modal").modal('toggle');
        });
        $("#api-keys").click(function(){
           $("#api-modal").modal('toggle');
        });
        $("#balances").click(function(){
           $("#balances-modal").modal('toggle');
        });
        @if(Auth::user())
        $(".new_comment").click(function(){
            window.location.replace("/user/{{Auth::user()->username}}");
        });
        $(".new_investment").click(function(){
            window.location.replace("/user/"+$(this).attr('id'));
        });


        $(".remove-notification").click(function(){
           var id = $(this).attr('id');
           $.ajax({
               url: '/notification/read/'+id,
               type: 'get',
               success: function(data){
                   $("."+id).remove();
               }
           });


        });

        $("#change-theme").change(function(){
            window.location.replace('/change-theme/');
        });
        $("#toggle-widget").change(function(){
            window.location.replace('/toggle-widget/');
        });
        $("#change-api").change(function(){
            window.location.replace('/change-api/'+$(this).val());
        });
        $("#toggle-noti").change(function(){
            window.location.replace('/toggle-notifications/');
        });
        $("#toggle-summed").change(function(){
            window.location.replace('/toggle-condensed/');
        });
        $("#change-currency").change(function(){
            window.location.replace('/change-currency/'+$(this).val());
        });
        @endif


        // Balance stuff (Beta - I see you sneaking in the code =)
        $(".remove-key").click(function(){
            var keyId = $(this).attr('id');

	          $.get( "/sources/delete/"+keyId, function( data ) {
              if(data != "No key found")
              {
                $(".zel-"+keyId).remove();
                swal("Key Deleted", "You successfully removed the "+data+" source.", "success");
              } else {
                swal("No key found", "No source was found.", "error");
              }
            });
        });
        $("#sourcetype").change(function(){

          if($(this).val() == "Poloniex" || $(this).val() == "Bittrex" || $(this).val() == "Coinbase")
          {
            $("#miner").css('display', 'none');
            $("#wallet").css('display', 'none');
            $("#nicehash").css('display', 'none');
            $("#exchange").css('display', 'block');
          }
          if($(this).val() == "Ethnano" || $(this).val() == "Ethermine")
          {
            $("#wallet").css('display', 'none');
            $("#exchange").css('display', 'none');
            $("#nicehash").css('display', 'none');
            $("#miner").css('display', 'block');
          }
          if($(this).val() == "Ethwallet")
          {
            $("#exchange").css('display', 'none');
            $("#miner").css('display', 'none');
            $("#nicehash").css('display', 'none');
            $("#wallet").css('display', 'block');
          }
          if($(this).val() == "Nicehash")
          {
            $("#exchange").css('display', 'none');
            $("#miner").css('display', 'none');
            $("#wallet").css('display', 'none');
            $("#nicehash").css('display', 'block');
          }
        });
    </script>

    @yield('js2')
</body>
</html>
