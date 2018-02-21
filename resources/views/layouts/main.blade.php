<!DOCTYPE html>

<?php

if(Auth::user())
{
  $multiplier = Auth::user()->getMultiplier();
  $api = Auth::user()->api;
  $currency = Auth::user()->getCurrency();

  // Notifications
  $notifications = Auth::user()->unreadnotifications;


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
    <link type="text/css" rel="stylesheet" href="/assets/css/vendor.bundle.css?version=1.0" >
    <link type="text/css" rel="stylesheet" href="/assets/css/app.bundle.css?version=1.2">
      <link type="text/css" rel="stylesheet" href="/design/style.bundle.css?version=1.2">
    <link type="text/css" rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome-animation/0.1.0/font-awesome-animation.min.css" rel="stylesheet" />
		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
          WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
          });
		</script>

    @if(Auth::user() && Auth::user()->username == "Edwin" || Auth::user() && Auth::user()->username == "resetpw")
      <link href="/css/bootstrap-tour.css" rel="stylesheet">
    @endif
    @if(Auth::user())
        @if(Auth::user()->theme == "dark")
        <link type="text/css" rel="stylesheet" href="/assets/css/theme-dark.css?v=22">
        <script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>
        @else
        <link type="text/css" rel="stylesheet" href="/assets/css/theme-c.css">
        <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
        @endif

    @else
    <link rel="stylesheet" href="/assets/css/theme-c.css">
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
    @endif
    <link type="text/css" rel="stylesheet" href="/assets/css/sweetalert.min.css">
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
    <!-- Facebook Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window,document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
     fbq('init', '1244131522348990');
    fbq('track', 'PageView');
    </script>
    <noscript>
     <img height="1" width="1"
    src="https://www.facebook.com/tr?id=1244131522348990&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->

    <style>
    .tooltip-inner {
        max-width: 400px;
        width: auto;
        }
        .action {
        cursor: pointer;
        color: #1e9ee0;
        text-decoration: none;
    }
    .mining-divide {
        font-size: 80%;
        color: #eee;
    }
    #mining-stats-canvas {
  	z-index: 1;
  	position: absolute;
  	left: 0;
  	top: 0;
  	width: 100%;
  	height: 100%;
  }
  #mining-stats-container {
  	position: relative;
  	text-align: center;
  	height: 96px;
  	border-bottom: 1px solid #f0f0f0;
  }
  .form-control {
    padding: 0px;
  }
  #content_wrapper.open-left .drawer-left {
    left: 255px;
  }
  .card.profile-menu a.info:after, .card.profile-menu.open a.info:after {
    top: 0px;
  }
  .app_sidebar-menu-collapsed #content_wrapper.open-left .drawer-left {
    left: 80px;
  }
  .m-aside-left--minimize #content_wrapper.open-left .drawer-left {
    left: 80px;
  }
  .dropdownjs>input {
    padding-bottom: 0px;
  }
  .nav-tabs>li>a:hover {
    border: none!important;
    border-top-left-radius: 0px!important;
    border-top-right-radius: 0px!important;
  }
  .btn-primary:hover {
    border-color: none;
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


<style>
a:hover {
  text-decoration: none;
}
/*# sourceMappingURL=chartist.css.map */
@font-face {
  font-family: "socicon";
  src: url("/design/fonts/socicon/socicon.eot");
  src: url("/design/fonts/socicon/socicon.eot?#iefix") format("embedded-opentype"), url("/design/fonts/socicon/socicon.woff") format("woff"), url("/design/fonts/socicon/socicon.ttf") format("truetype"), url("/design/fonts/socicon/socicon.svg#socicon") format("svg");
  font-weight: normal;
  font-style: normal; }

[data-icon]:before {
  font-family: "socicon" !important;
  content: attr(data-icon);
  font-style: normal !important;
  font-weight: normal !important;
  font-variant: normal !important;
  text-transform: none !important;
  speak: none;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale; }

[class^="socicon-"]:before,
[class*=" socicon-"]:before {
  font-family: "socicon" !important;
  font-style: normal !important;
  font-weight: normal !important;
  font-variant: normal !important;
  text-transform: none !important;
  speak: none;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale; }

.socicon-modelmayhem:before {
  content: "\e000"; }

.socicon-mixcloud:before {
  content: "\e001"; }

.socicon-drupal:before {
  content: "\e002"; }

.socicon-swarm:before {
  content: "\e003"; }

.socicon-istock:before {
  content: "\e004"; }

.socicon-yammer:before {
  content: "\e005"; }

.socicon-ello:before {
  content: "\e006"; }

.socicon-stackoverflow:before {
  content: "\e007"; }

.socicon-persona:before {
  content: "\e008"; }

.socicon-triplej:before {
  content: "\e009"; }

.socicon-houzz:before {
  content: "\e00a"; }

.socicon-rss:before {
  content: "\e00b"; }

.socicon-paypal:before {
  content: "\e00c"; }

.socicon-odnoklassniki:before {
  content: "\e00d"; }

.socicon-airbnb:before {
  content: "\e00e"; }

.socicon-periscope:before {
  content: "\e00f"; }

.socicon-outlook:before {
  content: "\e010"; }

.socicon-coderwall:before {
  content: "\e011"; }

.socicon-tripadvisor:before {
  content: "\e012"; }

.socicon-appnet:before {
  content: "\e013"; }

.socicon-goodreads:before {
  content: "\e014"; }

.socicon-tripit:before {
  content: "\e015"; }

.socicon-lanyrd:before {
  content: "\e016"; }

.socicon-slideshare:before {
  content: "\e017"; }

.socicon-buffer:before {
  content: "\e018"; }

.socicon-disqus:before {
  content: "\e019"; }

.socicon-vkontakte:before {
  content: "\e01a"; }

.socicon-whatsapp:before {
  content: "\e01b"; }

.socicon-patreon:before {
  content: "\e01c"; }

.socicon-storehouse:before {
  content: "\e01d"; }

.socicon-pocket:before {
  content: "\e01e"; }

.socicon-mail:before {
  content: "\e01f"; }

.socicon-blogger:before {
  content: "\e020"; }

.socicon-technorati:before {
  content: "\e021"; }

.socicon-reddit:before {
  content: "\e022"; }

.socicon-dribbble:before {
  content: "\e023"; }

.socicon-stumbleupon:before {
  content: "\e024"; }

.socicon-digg:before {
  content: "\e025"; }

.socicon-envato:before {
  content: "\e026"; }

.socicon-behance:before {
  content: "\e027"; }

.socicon-delicious:before {
  content: "\e028"; }

.socicon-deviantart:before {
  content: "\e029"; }

.socicon-forrst:before {
  content: "\e02a"; }

.socicon-play:before {
  content: "\e02b"; }

.socicon-zerply:before {
  content: "\e02c"; }

.socicon-wikipedia:before {
  content: "\e02d"; }

.socicon-apple:before {
  content: "\e02e"; }

.socicon-flattr:before {
  content: "\e02f"; }

.socicon-github:before {
  content: "\e030"; }

.socicon-renren:before {
  content: "\e031"; }

.socicon-friendfeed:before {
  content: "\e032"; }

.socicon-newsvine:before {
  content: "\e033"; }

.socicon-identica:before {
  content: "\e034"; }

.socicon-bebo:before {
  content: "\e035"; }

.socicon-zynga:before {
  content: "\e036"; }

.socicon-steam:before {
  content: "\e037"; }

.socicon-xbox:before {
  content: "\e038"; }

.socicon-windows:before {
  content: "\e039"; }

.socicon-qq:before {
  content: "\e03a"; }

.socicon-douban:before {
  content: "\e03b"; }

.socicon-meetup:before {
  content: "\e03c"; }

.socicon-playstation:before {
  content: "\e03d"; }

.socicon-android:before {
  content: "\e03e"; }

.socicon-snapchat:before {
  content: "\e03f"; }

.socicon-twitter:before {
  content: "\e040"; }

.socicon-facebook:before {
  content: "\e041"; }

.socicon-googleplus:before {
  content: "\e042"; }

.socicon-pinterest:before {
  content: "\e043"; }

.socicon-foursquare:before {
  content: "\e044"; }

.socicon-yahoo:before {
  content: "\e045"; }

.socicon-skype:before {
  content: "\e046"; }

.socicon-yelp:before {
  content: "\e047"; }

.socicon-feedburner:before {
  content: "\e048"; }

.socicon-linkedin:before {
  content: "\e049"; }

.socicon-viadeo:before {
  content: "\e04a"; }

.socicon-xing:before {
  content: "\e04b"; }

.socicon-myspace:before {
  content: "\e04c"; }

.socicon-soundcloud:before {
  content: "\e04d"; }

.socicon-spotify:before {
  content: "\e04e"; }

.socicon-grooveshark:before {
  content: "\e04f"; }

.socicon-lastfm:before {
  content: "\e050"; }

.socicon-youtube:before {
  content: "\e051"; }

.socicon-vimeo:before {
  content: "\e052"; }

.socicon-dailymotion:before {
  content: "\e053"; }

.socicon-vine:before {
  content: "\e054"; }

.socicon-flickr:before {
  content: "\e055"; }

.socicon-500px:before {
  content: "\e056"; }

.socicon-wordpress:before {
  content: "\e058"; }

.socicon-tumblr:before {
  content: "\e059"; }

.socicon-twitch:before {
  content: "\e05a"; }

.socicon-8tracks:before {
  content: "\e05b"; }

.socicon-amazon:before {
  content: "\e05c"; }

.socicon-icq:before {
  content: "\e05d"; }

.socicon-smugmug:before {
  content: "\e05e"; }

.socicon-ravelry:before {
  content: "\e05f"; }

.socicon-weibo:before {
  content: "\e060"; }

.socicon-baidu:before {
  content: "\e061"; }

.socicon-angellist:before {
  content: "\e062"; }

.socicon-ebay:before {
  content: "\e063"; }

.socicon-imdb:before {
  content: "\e064"; }

.socicon-stayfriends:before {
  content: "\e065"; }

.socicon-residentadvisor:before {
  content: "\e066"; }

.socicon-google:before {
  content: "\e067"; }

.socicon-yandex:before {
  content: "\e068"; }

.socicon-sharethis:before {
  content: "\e069"; }

.socicon-bandcamp:before {
  content: "\e06a"; }

.socicon-itunes:before {
  content: "\e06b"; }

.socicon-deezer:before {
  content: "\e06c"; }

.socicon-telegram:before {
  content: "\e06e"; }

.socicon-openid:before {
  content: "\e06f"; }

.socicon-amplement:before {
  content: "\e070"; }

.socicon-viber:before {
  content: "\e071"; }

.socicon-zomato:before {
  content: "\e072"; }

.socicon-draugiem:before {
  content: "\e074"; }

.socicon-endomodo:before {
  content: "\e075"; }

.socicon-filmweb:before {
  content: "\e076"; }

.socicon-stackexchange:before {
  content: "\e077"; }

.socicon-wykop:before {
  content: "\e078"; }

.socicon-teamspeak:before {
  content: "\e079"; }

.socicon-teamviewer:before {
  content: "\e07a"; }

.socicon-ventrilo:before {
  content: "\e07b"; }

.socicon-younow:before {
  content: "\e07c"; }

.socicon-raidcall:before {
  content: "\e07d"; }

.socicon-mumble:before {
  content: "\e07e"; }

.socicon-medium:before {
  content: "\e06d"; }

.socicon-bebee:before {
  content: "\e07f"; }

.socicon-hitbox:before {
  content: "\e080"; }

.socicon-reverbnation:before {
  content: "\e081"; }

.socicon-formulr:before {
  content: "\e082"; }

.socicon-instagram:before {
  content: "\e057"; }

.socicon-battlenet:before {
  content: "\e083"; }

.socicon-chrome:before {
  content: "\e084"; }

.socicon-discord:before {
  content: "\e086"; }

.socicon-issuu:before {
  content: "\e087"; }

.socicon-macos:before {
  content: "\e088"; }

.socicon-firefox:before {
  content: "\e089"; }

.socicon-opera:before {
  content: "\e08d"; }

.socicon-keybase:before {
  content: "\e090"; }

.socicon-alliance:before {
  content: "\e091"; }

.socicon-livejournal:before {
  content: "\e092"; }

.socicon-googlephotos:before {
  content: "\e093"; }

.socicon-horde:before {
  content: "\e094"; }

.socicon-etsy:before {
  content: "\e095"; }

.socicon-zapier:before {
  content: "\e096"; }

.socicon-google-scholar:before {
  content: "\e097"; }

.socicon-researchgate:before {
  content: "\e098"; }

.socicon-wechat:before {
  content: "\e099"; }

.socicon-strava:before {
  content: "\e09a"; }

.socicon-line:before {
  content: "\e09b"; }

.socicon-lyft:before {
  content: "\e09c"; }

.socicon-uber:before {
  content: "\e09d"; }

.socicon-songkick:before {
  content: "\e09e"; }

.socicon-viewbug:before {
  content: "\e09f"; }

.socicon-googlegroups:before {
  content: "\e0a0"; }

.socicon-quora:before {
  content: "\e073"; }

.socicon-diablo:before {
  content: "\e085"; }

.socicon-blizzard:before {
  content: "\e0a1"; }

.socicon-hearthstone:before {
  content: "\e08b"; }

.socicon-heroes:before {
  content: "\e08a"; }

.socicon-overwatch:before {
  content: "\e08c"; }

.socicon-warcraft:before {
  content: "\e08e"; }

.socicon-starcraft:before {
  content: "\e08f"; }

.socicon-beam:before {
  content: "\e0a2"; }

.socicon-curse:before {
  content: "\e0a3"; }

.socicon-player:before {
  content: "\e0a4"; }

.socicon-streamjar:before {
  content: "\e0a5"; }

.socicon-nintendo:before {
  content: "\e0a6"; }

.socicon-hellocoton:before {
  content: "\e0a7"; }

/*!
 *  Font Awesome 4.7.0 by @davegandy - http://fontawesome.io - @fontawesome
 *  License - http://fontawesome.io/license (Font: SIL OFL 1.1, CSS: MIT License)
 */
/* FONT PATH
 * -------------------------- */
@font-face {
  font-family: 'FontAwesome';
  src: url("/design/fonts/font-awesome/fontawesome-webfont.eot?v=4.7.0");
  src: url("/design/fonts/font-awesome/fontawesome-webfont.eot?#iefix&v=4.7.0") format("embedded-opentype"), url("/design/fonts/font-awesome/fontawesome-webfont.woff2?v=4.7.0") format("woff2"), url("/design/fonts/font-awesome/fontawesome-webfont.woff?v=4.7.0") format("woff"), url("/design/fonts/font-awesome/fontawesome-webfont.ttf?v=4.7.0") format("truetype"), url("/design/fonts/font-awesome/fontawesome-webfont.svg?v=4.7.0#fontawesomeregular") format("svg");
  font-weight: normal;
  font-style: normal; }

.fa {
  display: inline-block;
  font: normal normal normal 14px/1 FontAwesome;
  font-size: inherit;
  text-rendering: auto;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale; }

/* makes the font 33% larger relative to the icon container */
.fa-lg {
  font-size: 1.33333333em;
  line-height: 0.75em;
  vertical-align: -15%; }

.fa-2x {
  font-size: 2em; }

.fa-3x {
  font-size: 3em; }

.fa-4x {
  font-size: 4em; }

.fa-5x {
  font-size: 5em; }

.fa-fw {
  width: 1.28571429em;
  text-align: center; }

.fa-ul {
  padding-left: 0;
  margin-left: 2.14285714em;
  list-style-type: none; }

.fa-ul > li {
  position: relative; }

.fa-li {
  position: absolute;
  left: -2.14285714em;
  width: 2.14285714em;
  top: 0.14285714em;
  text-align: center; }

.fa-li.fa-lg {
  left: -1.85714286em; }

.fa-border {
  padding: .2em .25em .15em;
  border: solid 0.08em #eeeeee;
  border-radius: .1em; }

.fa-pull-left {
  float: left; }

.fa-pull-right {
  float: right; }

.fa.fa-pull-left {
  margin-right: .3em; }

.fa.fa-pull-right {
  margin-left: .3em; }

/* Deprecated as of 4.4.0 */
.pull-right {
  float: right; }

.pull-left {
  float: left; }

.fa.pull-left {
  margin-right: .3em; }

.fa.pull-right {
  margin-left: .3em; }

.fa-spin {
  -webkit-animation: fa-spin 2s infinite linear;
  animation: fa-spin 2s infinite linear; }

.fa-pulse {
  -webkit-animation: fa-spin 1s infinite steps(8);
  animation: fa-spin 1s infinite steps(8); }



  @-webkit-keyframes m-animate-blink {
    50% {
      opacity: 0.0; } }

  @-moz-keyframes m-animate-blink {
    50% {
      opacity: 0.0; } }

  @-o-keyframes m-animate-blink {
    50% {
      opacity: 0.0; } }

  @keyframes m-animate-blink {
    50% {
      opacity: 0.0; } }

  @-webkit-keyframes m-animate-shake {
    from {
      -webkit-transform: rotate(13deg);
      -moz-transform: rotate(13deg);
      -ms-transform: rotate(13deg);
      -o-transform: rotate(13deg);
      transform: rotate(13deg); }
    to {
      -webkit-transform-origin: center center;
      -webkit-transform: rotate(-13deg);
      -moz-transform: rotate(-13deg);
      -ms-transform: rotate(-13deg);
      -o-transform: rotate(-13deg);
      transform: rotate(-13deg); } }

  @-moz-keyframes m-animate-shake {
    from {
      -webkit-transform: rotate(13deg);
      -moz-transform: rotate(13deg);
      -ms-transform: rotate(13deg);
      -o-transform: rotate(13deg);
      transform: rotate(13deg); }
    to {
      -webkit-transform-origin: center center;
      -webkit-transform: rotate(-13deg);
      -moz-transform: rotate(-13deg);
      -ms-transform: rotate(-13deg);
      -o-transform: rotate(-13deg);
      transform: rotate(-13deg); } }

  @-o-keyframes m-animate-shake {
    from {
      -webkit-transform: rotate(13deg);
      -moz-transform: rotate(13deg);
      -ms-transform: rotate(13deg);
      -o-transform: rotate(13deg);
      transform: rotate(13deg); }
    to {
      -webkit-transform-origin: center center;
      -webkit-transform: rotate(-13deg);
      -moz-transform: rotate(-13deg);
      -ms-transform: rotate(-13deg);
      -o-transform: rotate(-13deg);
      transform: rotate(-13deg); } }

  @keyframes m-animate-shake {
    from {
      -webkit-transform: rotate(13deg);
      -moz-transform: rotate(13deg);
      -ms-transform: rotate(13deg);
      -o-transform: rotate(13deg);
      transform: rotate(13deg); }
    to {
      -webkit-transform-origin: center center;
      -webkit-transform: rotate(-13deg);
      -moz-transform: rotate(-13deg);
      -ms-transform: rotate(-13deg);
      -o-transform: rotate(-13deg);
      transform: rotate(-13deg); } }

@-webkit-keyframes fa-spin {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg); }
  100% {
    -webkit-transform: rotate(359deg);
    transform: rotate(359deg); } }

@keyframes fa-spin {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg); }
  100% {
    -webkit-transform: rotate(359deg);
    transform: rotate(359deg); } }

.fa-rotate-90 {
  -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=1)";
  -webkit-transform: rotate(90deg);
  -ms-transform: rotate(90deg);
  transform: rotate(90deg); }

.fa-rotate-180 {
  -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=2)";
  -webkit-transform: rotate(180deg);
  -ms-transform: rotate(180deg);
  transform: rotate(180deg); }

.fa-rotate-270 {
  -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=3)";
  -webkit-transform: rotate(270deg);
  -ms-transform: rotate(270deg);
  transform: rotate(270deg); }

.fa-flip-horizontal {
  -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0, mirror=1)";
  -webkit-transform: scale(-1, 1);
  -ms-transform: scale(-1, 1);
  transform: scale(-1, 1); }

.fa-flip-vertical {
  -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=2, mirror=1)";
  -webkit-transform: scale(1, -1);
  -ms-transform: scale(1, -1);
  transform: scale(1, -1); }

:root .fa-rotate-90,
:root .fa-rotate-180,
:root .fa-rotate-270,
:root .fa-flip-horizontal,
:root .fa-flip-vertical {
  filter: none; }

.fa-stack {
  position: relative;
  display: inline-block;
  width: 2em;
  height: 2em;
  line-height: 2em;
  vertical-align: middle; }

.fa-stack-1x,
.fa-stack-2x {
  position: absolute;
  left: 0;
  width: 100%;
  text-align: center; }

.fa-stack-1x {
  line-height: inherit; }

.fa-stack-2x {
  font-size: 2em; }

.fa-inverse {
  color: #ffffff; }

/* Font Awesome uses the Unicode Private Use Area (PUA) to ensure screen
   readers do not read off random characters that represent icons */
.fa-glass:before {
  content: "\f000"; }

.fa-music:before {
  content: "\f001"; }

.fa-search:before {
  content: "\f002"; }

.fa-envelope-o:before {
  content: "\f003"; }

.fa-heart:before {
  content: "\f004"; }

.fa-star:before {
  content: "\f005"; }

.fa-star-o:before {
  content: "\f006"; }

.fa-user:before {
  content: "\f007"; }

.fa-film:before {
  content: "\f008"; }

.fa-th-large:before {
  content: "\f009"; }

.fa-th:before {
  content: "\f00a"; }

.fa-th-list:before {
  content: "\f00b"; }

.fa-check:before {
  content: "\f00c"; }

.fa-remove:before,
.fa-close:before,
.fa-times:before {
  content: "\f00d"; }

.fa-search-plus:before {
  content: "\f00e"; }

.fa-search-minus:before {
  content: "\f010"; }

.fa-power-off:before {
  content: "\f011"; }

.fa-signal:before {
  content: "\f012"; }

.fa-gear:before,
.fa-cog:before {
  content: "\f013"; }

.fa-trash-o:before {
  content: "\f014"; }

.fa-home:before {
  content: "\f015"; }

.fa-file-o:before {
  content: "\f016"; }

.fa-clock-o:before {
  content: "\f017"; }

.fa-road:before {
  content: "\f018"; }

.fa-download:before {
  content: "\f019"; }

.fa-arrow-circle-o-down:before {
  content: "\f01a"; }

.fa-arrow-circle-o-up:before {
  content: "\f01b"; }

.fa-inbox:before {
  content: "\f01c"; }

.fa-play-circle-o:before {
  content: "\f01d"; }

.fa-rotate-right:before,
.fa-repeat:before {
  content: "\f01e"; }

.fa-refresh:before {
  content: "\f021"; }

.fa-list-alt:before {
  content: "\f022"; }

.fa-lock:before {
  content: "\f023"; }

.fa-flag:before {
  content: "\f024"; }

.fa-headphones:before {
  content: "\f025"; }

.fa-volume-off:before {
  content: "\f026"; }

.fa-volume-down:before {
  content: "\f027"; }

.fa-volume-up:before {
  content: "\f028"; }

.fa-qrcode:before {
  content: "\f029"; }

.fa-barcode:before {
  content: "\f02a"; }

.fa-tag:before {
  content: "\f02b"; }

.fa-tags:before {
  content: "\f02c"; }

.fa-book:before {
  content: "\f02d"; }

.fa-bookmark:before {
  content: "\f02e"; }

.fa-print:before {
  content: "\f02f"; }

.fa-camera:before {
  content: "\f030"; }

.fa-font:before {
  content: "\f031"; }

.fa-bold:before {
  content: "\f032"; }

.fa-italic:before {
  content: "\f033"; }

.fa-text-height:before {
  content: "\f034"; }

.fa-text-width:before {
  content: "\f035"; }

.fa-align-left:before {
  content: "\f036"; }

.fa-align-center:before {
  content: "\f037"; }

.fa-align-right:before {
  content: "\f038"; }

.fa-align-justify:before {
  content: "\f039"; }

.fa-list:before {
  content: "\f03a"; }

.fa-dedent:before,
.fa-outdent:before {
  content: "\f03b"; }

.fa-indent:before {
  content: "\f03c"; }

.fa-video-camera:before {
  content: "\f03d"; }

.fa-photo:before,
.fa-image:before,
.fa-picture-o:before {
  content: "\f03e"; }

.fa-pencil:before {
  content: "\f040"; }

.fa-map-marker:before {
  content: "\f041"; }

.fa-adjust:before {
  content: "\f042"; }

.fa-tint:before {
  content: "\f043"; }

.fa-edit:before,
.fa-pencil-square-o:before {
  content: "\f044"; }

.fa-share-square-o:before {
  content: "\f045"; }

.fa-check-square-o:before {
  content: "\f046"; }

.fa-arrows:before {
  content: "\f047"; }

.fa-step-backward:before {
  content: "\f048"; }

.fa-fast-backward:before {
  content: "\f049"; }

.fa-backward:before {
  content: "\f04a"; }

.fa-play:before {
  content: "\f04b"; }

.fa-pause:before {
  content: "\f04c"; }

.fa-stop:before {
  content: "\f04d"; }

.fa-forward:before {
  content: "\f04e"; }

.fa-fast-forward:before {
  content: "\f050"; }

.fa-step-forward:before {
  content: "\f051"; }

.fa-eject:before {
  content: "\f052"; }

.fa-chevron-left:before {
  content: "\f053"; }

.fa-chevron-right:before {
  content: "\f054"; }

.fa-plus-circle:before {
  content: "\f055"; }

.fa-minus-circle:before {
  content: "\f056"; }

.fa-times-circle:before {
  content: "\f057"; }

.fa-check-circle:before {
  content: "\f058"; }

.fa-question-circle:before {
  content: "\f059"; }

.fa-info-circle:before {
  content: "\f05a"; }

.fa-crosshairs:before {
  content: "\f05b"; }

.fa-times-circle-o:before {
  content: "\f05c"; }

.fa-check-circle-o:before {
  content: "\f05d"; }

.fa-ban:before {
  content: "\f05e"; }

.fa-arrow-left:before {
  content: "\f060"; }

.fa-arrow-right:before {
  content: "\f061"; }

.fa-arrow-up:before {
  content: "\f062"; }

.fa-arrow-down:before {
  content: "\f063"; }

.fa-mail-forward:before,
.fa-share:before {
  content: "\f064"; }

.fa-expand:before {
  content: "\f065"; }

.fa-compress:before {
  content: "\f066"; }

.fa-plus:before {
  content: "\f067"; }

.fa-minus:before {
  content: "\f068"; }

.fa-asterisk:before {
  content: "\f069"; }

.fa-exclamation-circle:before {
  content: "\f06a"; }

.fa-gift:before {
  content: "\f06b"; }

.fa-leaf:before {
  content: "\f06c"; }

.fa-fire:before {
  content: "\f06d"; }

.fa-eye:before {
  content: "\f06e"; }

.fa-eye-slash:before {
  content: "\f070"; }

.fa-warning:before,
.fa-exclamation-triangle:before {
  content: "\f071"; }

.fa-plane:before {
  content: "\f072"; }

.fa-calendar:before {
  content: "\f073"; }

.fa-random:before {
  content: "\f074"; }

.fa-comment:before {
  content: "\f075"; }

.fa-magnet:before {
  content: "\f076"; }

.fa-chevron-up:before {
  content: "\f077"; }

.fa-chevron-down:before {
  content: "\f078"; }

.fa-retweet:before {
  content: "\f079"; }

.fa-shopping-cart:before {
  content: "\f07a"; }

.fa-folder:before {
  content: "\f07b"; }

.fa-folder-open:before {
  content: "\f07c"; }

.fa-arrows-v:before {
  content: "\f07d"; }

.fa-arrows-h:before {
  content: "\f07e"; }

.fa-bar-chart-o:before,
.fa-bar-chart:before {
  content: "\f080"; }

.fa-twitter-square:before {
  content: "\f081"; }

.fa-facebook-square:before {
  content: "\f082"; }

.fa-camera-retro:before {
  content: "\f083"; }

.fa-key:before {
  content: "\f084"; }

.fa-gears:before,
.fa-cogs:before {
  content: "\f085"; }

.fa-comments:before {
  content: "\f086"; }

.fa-thumbs-o-up:before {
  content: "\f087"; }

.fa-thumbs-o-down:before {
  content: "\f088"; }

.fa-star-half:before {
  content: "\f089"; }

.fa-heart-o:before {
  content: "\f08a"; }

.fa-sign-out:before {
  content: "\f08b"; }

.fa-linkedin-square:before {
  content: "\f08c"; }

.fa-thumb-tack:before {
  content: "\f08d"; }

.fa-external-link:before {
  content: "\f08e"; }

.fa-sign-in:before {
  content: "\f090"; }

.fa-trophy:before {
  content: "\f091"; }

.fa-github-square:before {
  content: "\f092"; }

.fa-upload:before {
  content: "\f093"; }

.fa-lemon-o:before {
  content: "\f094"; }

.fa-phone:before {
  content: "\f095"; }

.fa-square-o:before {
  content: "\f096"; }

.fa-bookmark-o:before {
  content: "\f097"; }

.fa-phone-square:before {
  content: "\f098"; }

.fa-twitter:before {
  content: "\f099"; }

.fa-facebook-f:before,
.fa-facebook:before {
  content: "\f09a"; }

.fa-github:before {
  content: "\f09b"; }

.fa-unlock:before {
  content: "\f09c"; }

.fa-credit-card:before {
  content: "\f09d"; }

.fa-feed:before,
.fa-rss:before {
  content: "\f09e"; }

.fa-hdd-o:before {
  content: "\f0a0"; }

.fa-bullhorn:before {
  content: "\f0a1"; }

.fa-bell:before {
  content: "\f0f3"; }

.fa-certificate:before {
  content: "\f0a3"; }

.fa-hand-o-right:before {
  content: "\f0a4"; }

.fa-hand-o-left:before {
  content: "\f0a5"; }

.fa-hand-o-up:before {
  content: "\f0a6"; }

.fa-hand-o-down:before {
  content: "\f0a7"; }

.fa-arrow-circle-left:before {
  content: "\f0a8"; }

.fa-arrow-circle-right:before {
  content: "\f0a9"; }

.fa-arrow-circle-up:before {
  content: "\f0aa"; }

.fa-arrow-circle-down:before {
  content: "\f0ab"; }

.fa-globe:before {
  content: "\f0ac"; }

.fa-wrench:before {
  content: "\f0ad"; }

.fa-tasks:before {
  content: "\f0ae"; }

.fa-filter:before {
  content: "\f0b0"; }

.fa-briefcase:before {
  content: "\f0b1"; }

.fa-arrows-alt:before {
  content: "\f0b2"; }

.fa-group:before,
.fa-users:before {
  content: "\f0c0"; }

.fa-chain:before,
.fa-link:before {
  content: "\f0c1"; }

.fa-cloud:before {
  content: "\f0c2"; }

.fa-flask:before {
  content: "\f0c3"; }

.fa-cut:before,
.fa-scissors:before {
  content: "\f0c4"; }

.fa-copy:before,
.fa-files-o:before {
  content: "\f0c5"; }

.fa-paperclip:before {
  content: "\f0c6"; }

.fa-save:before,
.fa-floppy-o:before {
  content: "\f0c7"; }

.fa-square:before {
  content: "\f0c8"; }

.fa-navicon:before,
.fa-reorder:before,
.fa-bars:before {
  content: "\f0c9"; }

.fa-list-ul:before {
  content: "\f0ca"; }

.fa-list-ol:before {
  content: "\f0cb"; }

.fa-strikethrough:before {
  content: "\f0cc"; }

.fa-underline:before {
  content: "\f0cd"; }

.fa-table:before {
  content: "\f0ce"; }

.fa-magic:before {
  content: "\f0d0"; }

.fa-truck:before {
  content: "\f0d1"; }

.fa-pinterest:before {
  content: "\f0d2"; }

.fa-pinterest-square:before {
  content: "\f0d3"; }

.fa-google-plus-square:before {
  content: "\f0d4"; }

.fa-google-plus:before {
  content: "\f0d5"; }

.fa-money:before {
  content: "\f0d6"; }

.fa-caret-down:before {
  content: "\f0d7"; }

.fa-caret-up:before {
  content: "\f0d8"; }

.fa-caret-left:before {
  content: "\f0d9"; }

.fa-caret-right:before {
  content: "\f0da"; }

.fa-columns:before {
  content: "\f0db"; }

.fa-unsorted:before,
.fa-sort:before {
  content: "\f0dc"; }

.fa-sort-down:before,
.fa-sort-desc:before {
  content: "\f0dd"; }

.fa-sort-up:before,
.fa-sort-asc:before {
  content: "\f0de"; }

.fa-envelope:before {
  content: "\f0e0"; }

.fa-linkedin:before {
  content: "\f0e1"; }

.fa-rotate-left:before,
.fa-undo:before {
  content: "\f0e2"; }

.fa-legal:before,
.fa-gavel:before {
  content: "\f0e3"; }

.fa-dashboard:before,
.fa-tachometer:before {
  content: "\f0e4"; }

.fa-comment-o:before {
  content: "\f0e5"; }

.fa-comments-o:before {
  content: "\f0e6"; }

.fa-flash:before,
.fa-bolt:before {
  content: "\f0e7"; }

.fa-sitemap:before {
  content: "\f0e8"; }

.fa-umbrella:before {
  content: "\f0e9"; }

.fa-paste:before,
.fa-clipboard:before {
  content: "\f0ea"; }

.fa-lightbulb-o:before {
  content: "\f0eb"; }

.fa-exchange:before {
  content: "\f0ec"; }

.fa-cloud-download:before {
  content: "\f0ed"; }

.fa-cloud-upload:before {
  content: "\f0ee"; }

.fa-user-md:before {
  content: "\f0f0"; }

.fa-stethoscope:before {
  content: "\f0f1"; }

.fa-suitcase:before {
  content: "\f0f2"; }

.fa-bell-o:before {
  content: "\f0a2"; }

.fa-coffee:before {
  content: "\f0f4"; }

.fa-cutlery:before {
  content: "\f0f5"; }

.fa-file-text-o:before {
  content: "\f0f6"; }

.fa-building-o:before {
  content: "\f0f7"; }

.fa-hospital-o:before {
  content: "\f0f8"; }

.fa-ambulance:before {
  content: "\f0f9"; }

.fa-medkit:before {
  content: "\f0fa"; }

.fa-fighter-jet:before {
  content: "\f0fb"; }

.fa-beer:before {
  content: "\f0fc"; }

.fa-h-square:before {
  content: "\f0fd"; }

.fa-plus-square:before {
  content: "\f0fe"; }

.fa-angle-double-left:before {
  content: "\f100"; }

.fa-angle-double-right:before {
  content: "\f101"; }

.fa-angle-double-up:before {
  content: "\f102"; }

.fa-angle-double-down:before {
  content: "\f103"; }

.fa-angle-left:before {
  content: "\f104"; }

.fa-angle-right:before {
  content: "\f105"; }

.fa-angle-up:before {
  content: "\f106"; }

.fa-angle-down:before {
  content: "\f107"; }

.fa-desktop:before {
  content: "\f108"; }

.fa-laptop:before {
  content: "\f109"; }

.fa-tablet:before {
  content: "\f10a"; }

.fa-mobile-phone:before,
.fa-mobile:before {
  content: "\f10b"; }

.fa-circle-o:before {
  content: "\f10c"; }

.fa-quote-left:before {
  content: "\f10d"; }

.fa-quote-right:before {
  content: "\f10e"; }

.fa-spinner:before {
  content: "\f110"; }

.fa-circle:before {
  content: "\f111"; }

.fa-mail-reply:before,
.fa-reply:before {
  content: "\f112"; }

.fa-github-alt:before {
  content: "\f113"; }

.fa-folder-o:before {
  content: "\f114"; }

.fa-folder-open-o:before {
  content: "\f115"; }

.fa-smile-o:before {
  content: "\f118"; }

.fa-frown-o:before {
  content: "\f119"; }

.fa-meh-o:before {
  content: "\f11a"; }

.fa-gamepad:before {
  content: "\f11b"; }

.fa-keyboard-o:before {
  content: "\f11c"; }

.fa-flag-o:before {
  content: "\f11d"; }

.fa-flag-checkered:before {
  content: "\f11e"; }

.fa-terminal:before {
  content: "\f120"; }

.fa-code:before {
  content: "\f121"; }

.fa-mail-reply-all:before,
.fa-reply-all:before {
  content: "\f122"; }

.fa-star-half-empty:before,
.fa-star-half-full:before,
.fa-star-half-o:before {
  content: "\f123"; }

.fa-location-arrow:before {
  content: "\f124"; }

.fa-crop:before {
  content: "\f125"; }

.fa-code-fork:before {
  content: "\f126"; }

.fa-unlink:before,
.fa-chain-broken:before {
  content: "\f127"; }

.fa-question:before {
  content: "\f128"; }

.fa-info:before {
  content: "\f129"; }

.fa-exclamation:before {
  content: "\f12a"; }

.fa-superscript:before {
  content: "\f12b"; }

.fa-subscript:before {
  content: "\f12c"; }

.fa-eraser:before {
  content: "\f12d"; }

.fa-puzzle-piece:before {
  content: "\f12e"; }

.fa-microphone:before {
  content: "\f130"; }

.fa-microphone-slash:before {
  content: "\f131"; }

.fa-shield:before {
  content: "\f132"; }

.fa-calendar-o:before {
  content: "\f133"; }

.fa-fire-extinguisher:before {
  content: "\f134"; }

.fa-rocket:before {
  content: "\f135"; }

.fa-maxcdn:before {
  content: "\f136"; }

.fa-chevron-circle-left:before {
  content: "\f137"; }

.fa-chevron-circle-right:before {
  content: "\f138"; }

.fa-chevron-circle-up:before {
  content: "\f139"; }

.fa-chevron-circle-down:before {
  content: "\f13a"; }

.fa-html5:before {
  content: "\f13b"; }

.fa-css3:before {
  content: "\f13c"; }

.fa-anchor:before {
  content: "\f13d"; }

.fa-unlock-alt:before {
  content: "\f13e"; }

.fa-bullseye:before {
  content: "\f140"; }

.fa-ellipsis-h:before {
  content: "\f141"; }

.fa-ellipsis-v:before {
  content: "\f142"; }

.fa-rss-square:before {
  content: "\f143"; }

.fa-play-circle:before {
  content: "\f144"; }

.fa-ticket:before {
  content: "\f145"; }

.fa-minus-square:before {
  content: "\f146"; }

.fa-minus-square-o:before {
  content: "\f147"; }

.fa-level-up:before {
  content: "\f148"; }

.fa-level-down:before {
  content: "\f149"; }

.fa-check-square:before {
  content: "\f14a"; }

.fa-pencil-square:before {
  content: "\f14b"; }

.fa-external-link-square:before {
  content: "\f14c"; }

.fa-share-square:before {
  content: "\f14d"; }

.fa-compass:before {
  content: "\f14e"; }

.fa-toggle-down:before,
.fa-caret-square-o-down:before {
  content: "\f150"; }

.fa-toggle-up:before,
.fa-caret-square-o-up:before {
  content: "\f151"; }

.fa-toggle-right:before,
.fa-caret-square-o-right:before {
  content: "\f152"; }

.fa-euro:before,
.fa-eur:before {
  content: "\f153"; }

.fa-gbp:before {
  content: "\f154"; }

.fa-dollar:before,
.fa-usd:before {
  content: "\f155"; }

.fa-rupee:before,
.fa-inr:before {
  content: "\f156"; }

.fa-cny:before,
.fa-rmb:before,
.fa-yen:before,
.fa-jpy:before {
  content: "\f157"; }

.fa-ruble:before,
.fa-rouble:before,
.fa-rub:before {
  content: "\f158"; }

.fa-won:before,
.fa-krw:before {
  content: "\f159"; }

.fa-bitcoin:before,
.fa-btc:before {
  content: "\f15a"; }

.fa-file:before {
  content: "\f15b"; }

.fa-file-text:before {
  content: "\f15c"; }

.fa-sort-alpha-asc:before {
  content: "\f15d"; }

.fa-sort-alpha-desc:before {
  content: "\f15e"; }

.fa-sort-amount-asc:before {
  content: "\f160"; }

.fa-sort-amount-desc:before {
  content: "\f161"; }

.fa-sort-numeric-asc:before {
  content: "\f162"; }

.fa-sort-numeric-desc:before {
  content: "\f163"; }

.fa-thumbs-up:before {
  content: "\f164"; }

.fa-thumbs-down:before {
  content: "\f165"; }

.fa-youtube-square:before {
  content: "\f166"; }

.fa-youtube:before {
  content: "\f167"; }

.fa-xing:before {
  content: "\f168"; }

.fa-xing-square:before {
  content: "\f169"; }

.fa-youtube-play:before {
  content: "\f16a"; }

.fa-dropbox:before {
  content: "\f16b"; }

.fa-stack-overflow:before {
  content: "\f16c"; }

.fa-instagram:before {
  content: "\f16d"; }

.fa-flickr:before {
  content: "\f16e"; }

.fa-adn:before {
  content: "\f170"; }

.fa-bitbucket:before {
  content: "\f171"; }

.fa-bitbucket-square:before {
  content: "\f172"; }

.fa-tumblr:before {
  content: "\f173"; }

.fa-tumblr-square:before {
  content: "\f174"; }

.fa-long-arrow-down:before {
  content: "\f175"; }

.fa-long-arrow-up:before {
  content: "\f176"; }

.fa-long-arrow-left:before {
  content: "\f177"; }

.fa-long-arrow-right:before {
  content: "\f178"; }

.fa-apple:before {
  content: "\f179"; }

.fa-windows:before {
  content: "\f17a"; }

.fa-android:before {
  content: "\f17b"; }

.fa-linux:before {
  content: "\f17c"; }

.fa-dribbble:before {
  content: "\f17d"; }

.fa-skype:before {
  content: "\f17e"; }

.fa-foursquare:before {
  content: "\f180"; }

.fa-trello:before {
  content: "\f181"; }

.fa-female:before {
  content: "\f182"; }

.fa-male:before {
  content: "\f183"; }

.fa-gittip:before,
.fa-gratipay:before {
  content: "\f184"; }

.fa-sun-o:before {
  content: "\f185"; }

.fa-moon-o:before {
  content: "\f186"; }

.fa-archive:before {
  content: "\f187"; }

.fa-bug:before {
  content: "\f188"; }

.fa-vk:before {
  content: "\f189"; }

.fa-weibo:before {
  content: "\f18a"; }

.fa-renren:before {
  content: "\f18b"; }

.fa-pagelines:before {
  content: "\f18c"; }

.fa-stack-exchange:before {
  content: "\f18d"; }

.fa-arrow-circle-o-right:before {
  content: "\f18e"; }

.fa-arrow-circle-o-left:before {
  content: "\f190"; }

.fa-toggle-left:before,
.fa-caret-square-o-left:before {
  content: "\f191"; }

.fa-dot-circle-o:before {
  content: "\f192"; }

.fa-wheelchair:before {
  content: "\f193"; }

.fa-vimeo-square:before {
  content: "\f194"; }

.fa-turkish-lira:before,
.fa-try:before {
  content: "\f195"; }

.fa-plus-square-o:before {
  content: "\f196"; }

.fa-space-shuttle:before {
  content: "\f197"; }

.fa-slack:before {
  content: "\f198"; }

.fa-envelope-square:before {
  content: "\f199"; }

.fa-wordpress:before {
  content: "\f19a"; }

.fa-openid:before {
  content: "\f19b"; }

.fa-institution:before,
.fa-bank:before,
.fa-university:before {
  content: "\f19c"; }

.fa-mortar-board:before,
.fa-graduation-cap:before {
  content: "\f19d"; }

.fa-yahoo:before {
  content: "\f19e"; }

.fa-google:before {
  content: "\f1a0"; }

.fa-reddit:before {
  content: "\f1a1"; }

.fa-reddit-square:before {
  content: "\f1a2"; }

.fa-stumbleupon-circle:before {
  content: "\f1a3"; }

.fa-stumbleupon:before {
  content: "\f1a4"; }

.fa-delicious:before {
  content: "\f1a5"; }

.fa-digg:before {
  content: "\f1a6"; }

.fa-pied-piper-pp:before {
  content: "\f1a7"; }

.fa-pied-piper-alt:before {
  content: "\f1a8"; }

.fa-drupal:before {
  content: "\f1a9"; }

.fa-joomla:before {
  content: "\f1aa"; }

.fa-language:before {
  content: "\f1ab"; }

.fa-fax:before {
  content: "\f1ac"; }

.fa-building:before {
  content: "\f1ad"; }

.fa-child:before {
  content: "\f1ae"; }

.fa-paw:before {
  content: "\f1b0"; }

.fa-spoon:before {
  content: "\f1b1"; }

.fa-cube:before {
  content: "\f1b2"; }

.fa-cubes:before {
  content: "\f1b3"; }

.fa-behance:before {
  content: "\f1b4"; }

.fa-behance-square:before {
  content: "\f1b5"; }

.fa-steam:before {
  content: "\f1b6"; }

.fa-steam-square:before {
  content: "\f1b7"; }

.fa-recycle:before {
  content: "\f1b8"; }

.fa-automobile:before,
.fa-car:before {
  content: "\f1b9"; }

.fa-cab:before,
.fa-taxi:before {
  content: "\f1ba"; }

.fa-tree:before {
  content: "\f1bb"; }

.fa-spotify:before {
  content: "\f1bc"; }

.fa-deviantart:before {
  content: "\f1bd"; }

.fa-soundcloud:before {
  content: "\f1be"; }

.fa-database:before {
  content: "\f1c0"; }

.fa-file-pdf-o:before {
  content: "\f1c1"; }

.fa-file-word-o:before {
  content: "\f1c2"; }

.fa-file-excel-o:before {
  content: "\f1c3"; }

.fa-file-powerpoint-o:before {
  content: "\f1c4"; }

.fa-file-photo-o:before,
.fa-file-picture-o:before,
.fa-file-image-o:before {
  content: "\f1c5"; }

.fa-file-zip-o:before,
.fa-file-archive-o:before {
  content: "\f1c6"; }

.fa-file-sound-o:before,
.fa-file-audio-o:before {
  content: "\f1c7"; }

.fa-file-movie-o:before,
.fa-file-video-o:before {
  content: "\f1c8"; }

.fa-file-code-o:before {
  content: "\f1c9"; }

.fa-vine:before {
  content: "\f1ca"; }

.fa-codepen:before {
  content: "\f1cb"; }

.fa-jsfiddle:before {
  content: "\f1cc"; }

.fa-life-bouy:before,
.fa-life-buoy:before,
.fa-life-saver:before,
.fa-support:before,
.fa-life-ring:before {
  content: "\f1cd"; }

.fa-circle-o-notch:before {
  content: "\f1ce"; }

.fa-ra:before,
.fa-resistance:before,
.fa-rebel:before {
  content: "\f1d0"; }

.fa-ge:before,
.fa-empire:before {
  content: "\f1d1"; }

.fa-git-square:before {
  content: "\f1d2"; }

.fa-git:before {
  content: "\f1d3"; }

.fa-y-combinator-square:before,
.fa-yc-square:before,
.fa-hacker-news:before {
  content: "\f1d4"; }

.fa-tencent-weibo:before {
  content: "\f1d5"; }

.fa-qq:before {
  content: "\f1d6"; }

.fa-wechat:before,
.fa-weixin:before {
  content: "\f1d7"; }

.fa-send:before,
.fa-paper-plane:before {
  content: "\f1d8"; }

.fa-send-o:before,
.fa-paper-plane-o:before {
  content: "\f1d9"; }

.fa-history:before {
  content: "\f1da"; }

.fa-circle-thin:before {
  content: "\f1db"; }

.fa-header:before {
  content: "\f1dc"; }

.fa-paragraph:before {
  content: "\f1dd"; }

.fa-sliders:before {
  content: "\f1de"; }

.fa-share-alt:before {
  content: "\f1e0"; }

.fa-share-alt-square:before {
  content: "\f1e1"; }

.fa-bomb:before {
  content: "\f1e2"; }

.fa-soccer-ball-o:before,
.fa-futbol-o:before {
  content: "\f1e3"; }

.fa-tty:before {
  content: "\f1e4"; }

.fa-binoculars:before {
  content: "\f1e5"; }

.fa-plug:before {
  content: "\f1e6"; }

.fa-slideshare:before {
  content: "\f1e7"; }

.fa-twitch:before {
  content: "\f1e8"; }

.fa-yelp:before {
  content: "\f1e9"; }

.fa-newspaper-o:before {
  content: "\f1ea"; }

.fa-wifi:before {
  content: "\f1eb"; }

.fa-calculator:before {
  content: "\f1ec"; }

.fa-paypal:before {
  content: "\f1ed"; }

.fa-google-wallet:before {
  content: "\f1ee"; }

.fa-cc-visa:before {
  content: "\f1f0"; }

.fa-cc-mastercard:before {
  content: "\f1f1"; }

.fa-cc-discover:before {
  content: "\f1f2"; }

.fa-cc-amex:before {
  content: "\f1f3"; }

.fa-cc-paypal:before {
  content: "\f1f4"; }

.fa-cc-stripe:before {
  content: "\f1f5"; }

.fa-bell-slash:before {
  content: "\f1f6"; }

.fa-bell-slash-o:before {
  content: "\f1f7"; }

.fa-trash:before {
  content: "\f1f8"; }

.fa-copyright:before {
  content: "\f1f9"; }

.fa-at:before {
  content: "\f1fa"; }

.fa-eyedropper:before {
  content: "\f1fb"; }

.fa-paint-brush:before {
  content: "\f1fc"; }

.fa-birthday-cake:before {
  content: "\f1fd"; }

.fa-area-chart:before {
  content: "\f1fe"; }

.fa-pie-chart:before {
  content: "\f200"; }

.fa-line-chart:before {
  content: "\f201"; }

.fa-lastfm:before {
  content: "\f202"; }

.fa-lastfm-square:before {
  content: "\f203"; }

.fa-toggle-off:before {
  content: "\f204"; }

.fa-toggle-on:before {
  content: "\f205"; }

.fa-bicycle:before {
  content: "\f206"; }

.fa-bus:before {
  content: "\f207"; }

.fa-ioxhost:before {
  content: "\f208"; }

.fa-angellist:before {
  content: "\f209"; }

.fa-cc:before {
  content: "\f20a"; }

.fa-shekel:before,
.fa-sheqel:before,
.fa-ils:before {
  content: "\f20b"; }

.fa-meanpath:before {
  content: "\f20c"; }

.fa-buysellads:before {
  content: "\f20d"; }

.fa-connectdevelop:before {
  content: "\f20e"; }

.fa-dashcube:before {
  content: "\f210"; }

.fa-forumbee:before {
  content: "\f211"; }

.fa-leanpub:before {
  content: "\f212"; }

.fa-sellsy:before {
  content: "\f213"; }

.fa-shirtsinbulk:before {
  content: "\f214"; }

.fa-simplybuilt:before {
  content: "\f215"; }

.fa-skyatlas:before {
  content: "\f216"; }

.fa-cart-plus:before {
  content: "\f217"; }

.fa-cart-arrow-down:before {
  content: "\f218"; }

.fa-diamond:before {
  content: "\f219"; }

.fa-ship:before {
  content: "\f21a"; }

.fa-user-secret:before {
  content: "\f21b"; }

.fa-motorcycle:before {
  content: "\f21c"; }

.fa-street-view:before {
  content: "\f21d"; }

.fa-heartbeat:before {
  content: "\f21e"; }

.fa-venus:before {
  content: "\f221"; }

.fa-mars:before {
  content: "\f222"; }

.fa-mercury:before {
  content: "\f223"; }

.fa-intersex:before,
.fa-transgender:before {
  content: "\f224"; }

.fa-transgender-alt:before {
  content: "\f225"; }

.fa-venus-double:before {
  content: "\f226"; }

.fa-mars-double:before {
  content: "\f227"; }

.fa-venus-mars:before {
  content: "\f228"; }

.fa-mars-stroke:before {
  content: "\f229"; }

.fa-mars-stroke-v:before {
  content: "\f22a"; }

.fa-mars-stroke-h:before {
  content: "\f22b"; }

.fa-neuter:before {
  content: "\f22c"; }

.fa-genderless:before {
  content: "\f22d"; }

.fa-facebook-official:before {
  content: "\f230"; }

.fa-pinterest-p:before {
  content: "\f231"; }

.fa-whatsapp:before {
  content: "\f232"; }

.fa-server:before {
  content: "\f233"; }

.fa-user-plus:before {
  content: "\f234"; }

.fa-user-times:before {
  content: "\f235"; }

.fa-hotel:before,
.fa-bed:before {
  content: "\f236"; }

.fa-viacoin:before {
  content: "\f237"; }

.fa-train:before {
  content: "\f238"; }

.fa-subway:before {
  content: "\f239"; }

.fa-medium:before {
  content: "\f23a"; }

.fa-yc:before,
.fa-y-combinator:before {
  content: "\f23b"; }

.fa-optin-monster:before {
  content: "\f23c"; }

.fa-opencart:before {
  content: "\f23d"; }

.fa-expeditedssl:before {
  content: "\f23e"; }

.fa-battery-4:before,
.fa-battery:before,
.fa-battery-full:before {
  content: "\f240"; }

.fa-battery-3:before,
.fa-battery-three-quarters:before {
  content: "\f241"; }

.fa-battery-2:before,
.fa-battery-half:before {
  content: "\f242"; }

.fa-battery-1:before,
.fa-battery-quarter:before {
  content: "\f243"; }

.fa-battery-0:before,
.fa-battery-empty:before {
  content: "\f244"; }

.fa-mouse-pointer:before {
  content: "\f245"; }

.fa-i-cursor:before {
  content: "\f246"; }

.fa-object-group:before {
  content: "\f247"; }

.fa-object-ungroup:before {
  content: "\f248"; }

.fa-sticky-note:before {
  content: "\f249"; }

.fa-sticky-note-o:before {
  content: "\f24a"; }

.fa-cc-jcb:before {
  content: "\f24b"; }

.fa-cc-diners-club:before {
  content: "\f24c"; }

.fa-clone:before {
  content: "\f24d"; }

.fa-balance-scale:before {
  content: "\f24e"; }

.fa-hourglass-o:before {
  content: "\f250"; }

.fa-hourglass-1:before,
.fa-hourglass-start:before {
  content: "\f251"; }

.fa-hourglass-2:before,
.fa-hourglass-half:before {
  content: "\f252"; }

.fa-hourglass-3:before,
.fa-hourglass-end:before {
  content: "\f253"; }

.fa-hourglass:before {
  content: "\f254"; }

.fa-hand-grab-o:before,
.fa-hand-rock-o:before {
  content: "\f255"; }

.fa-hand-stop-o:before,
.fa-hand-paper-o:before {
  content: "\f256"; }

.fa-hand-scissors-o:before {
  content: "\f257"; }

.fa-hand-lizard-o:before {
  content: "\f258"; }

.fa-hand-spock-o:before {
  content: "\f259"; }

.fa-hand-pointer-o:before {
  content: "\f25a"; }

.fa-hand-peace-o:before {
  content: "\f25b"; }

.fa-trademark:before {
  content: "\f25c"; }

.fa-registered:before {
  content: "\f25d"; }

.fa-creative-commons:before {
  content: "\f25e"; }

.fa-gg:before {
  content: "\f260"; }

.fa-gg-circle:before {
  content: "\f261"; }

.fa-tripadvisor:before {
  content: "\f262"; }

.fa-odnoklassniki:before {
  content: "\f263"; }

.fa-odnoklassniki-square:before {
  content: "\f264"; }

.fa-get-pocket:before {
  content: "\f265"; }

.fa-wikipedia-w:before {
  content: "\f266"; }

.fa-safari:before {
  content: "\f267"; }

.fa-chrome:before {
  content: "\f268"; }

.fa-firefox:before {
  content: "\f269"; }

.fa-opera:before {
  content: "\f26a"; }

.fa-internet-explorer:before {
  content: "\f26b"; }

.fa-tv:before,
.fa-television:before {
  content: "\f26c"; }

.fa-contao:before {
  content: "\f26d"; }

.fa-500px:before {
  content: "\f26e"; }

.fa-amazon:before {
  content: "\f270"; }

.fa-calendar-plus-o:before {
  content: "\f271"; }

.fa-calendar-minus-o:before {
  content: "\f272"; }

.fa-calendar-times-o:before {
  content: "\f273"; }

.fa-calendar-check-o:before {
  content: "\f274"; }

.fa-industry:before {
  content: "\f275"; }

.fa-map-pin:before {
  content: "\f276"; }

.fa-map-signs:before {
  content: "\f277"; }

.fa-map-o:before {
  content: "\f278"; }

.fa-map:before {
  content: "\f279"; }

.fa-commenting:before {
  content: "\f27a"; }

.fa-commenting-o:before {
  content: "\f27b"; }

.fa-houzz:before {
  content: "\f27c"; }

.fa-vimeo:before {
  content: "\f27d"; }

.fa-black-tie:before {
  content: "\f27e"; }

.fa-fonticons:before {
  content: "\f280"; }

.fa-reddit-alien:before {
  content: "\f281"; }

.fa-edge:before {
  content: "\f282"; }

.fa-credit-card-alt:before {
  content: "\f283"; }

.fa-codiepie:before {
  content: "\f284"; }

.fa-modx:before {
  content: "\f285"; }

.fa-fort-awesome:before {
  content: "\f286"; }

.fa-usb:before {
  content: "\f287"; }

.fa-product-hunt:before {
  content: "\f288"; }

.fa-mixcloud:before {
  content: "\f289"; }

.fa-scribd:before {
  content: "\f28a"; }

.fa-pause-circle:before {
  content: "\f28b"; }

.fa-pause-circle-o:before {
  content: "\f28c"; }

.fa-stop-circle:before {
  content: "\f28d"; }

.fa-stop-circle-o:before {
  content: "\f28e"; }

.fa-shopping-bag:before {
  content: "\f290"; }

.fa-shopping-basket:before {
  content: "\f291"; }

.fa-hashtag:before {
  content: "\f292"; }

.fa-bluetooth:before {
  content: "\f293"; }

.fa-bluetooth-b:before {
  content: "\f294"; }

.fa-percent:before {
  content: "\f295"; }

.fa-gitlab:before {
  content: "\f296"; }

.fa-wpbeginner:before {
  content: "\f297"; }

.fa-wpforms:before {
  content: "\f298"; }

.fa-envira:before {
  content: "\f299"; }

.fa-universal-access:before {
  content: "\f29a"; }

.fa-wheelchair-alt:before {
  content: "\f29b"; }

.fa-question-circle-o:before {
  content: "\f29c"; }

.fa-blind:before {
  content: "\f29d"; }

.fa-audio-description:before {
  content: "\f29e"; }

.fa-volume-control-phone:before {
  content: "\f2a0"; }

.fa-braille:before {
  content: "\f2a1"; }

.fa-assistive-listening-systems:before {
  content: "\f2a2"; }

.fa-asl-interpreting:before,
.fa-american-sign-language-interpreting:before {
  content: "\f2a3"; }

.fa-deafness:before,
.fa-hard-of-hearing:before,
.fa-deaf:before {
  content: "\f2a4"; }

.fa-glide:before {
  content: "\f2a5"; }

.fa-glide-g:before {
  content: "\f2a6"; }

.fa-signing:before,
.fa-sign-language:before {
  content: "\f2a7"; }

.fa-low-vision:before {
  content: "\f2a8"; }

.fa-viadeo:before {
  content: "\f2a9"; }

.fa-viadeo-square:before {
  content: "\f2aa"; }

.fa-snapchat:before {
  content: "\f2ab"; }

.fa-snapchat-ghost:before {
  content: "\f2ac"; }

.fa-snapchat-square:before {
  content: "\f2ad"; }

.fa-pied-piper:before {
  content: "\f2ae"; }

.fa-first-order:before {
  content: "\f2b0"; }

.fa-yoast:before {
  content: "\f2b1"; }

.fa-themeisle:before {
  content: "\f2b2"; }

.fa-google-plus-circle:before,
.fa-google-plus-official:before {
  content: "\f2b3"; }

.fa-fa:before,
.fa-font-awesome:before {
  content: "\f2b4"; }

.fa-handshake-o:before {
  content: "\f2b5"; }

.fa-envelope-open:before {
  content: "\f2b6"; }

.fa-envelope-open-o:before {
  content: "\f2b7"; }

.fa-linode:before {
  content: "\f2b8"; }

.fa-address-book:before {
  content: "\f2b9"; }

.fa-address-book-o:before {
  content: "\f2ba"; }

.fa-vcard:before,
.fa-address-card:before {
  content: "\f2bb"; }

.fa-vcard-o:before,
.fa-address-card-o:before {
  content: "\f2bc"; }

.fa-user-circle:before {
  content: "\f2bd"; }

.fa-user-circle-o:before {
  content: "\f2be"; }

.fa-user-o:before {
  content: "\f2c0"; }

.fa-id-badge:before {
  content: "\f2c1"; }

.fa-drivers-license:before,
.fa-id-card:before {
  content: "\f2c2"; }

.fa-drivers-license-o:before,
.fa-id-card-o:before {
  content: "\f2c3"; }

.fa-quora:before {
  content: "\f2c4"; }

.fa-free-code-camp:before {
  content: "\f2c5"; }

.fa-telegram:before {
  content: "\f2c6"; }

.fa-thermometer-4:before,
.fa-thermometer:before,
.fa-thermometer-full:before {
  content: "\f2c7"; }

.fa-thermometer-3:before,
.fa-thermometer-three-quarters:before {
  content: "\f2c8"; }

.fa-thermometer-2:before,
.fa-thermometer-half:before {
  content: "\f2c9"; }

.fa-thermometer-1:before,
.fa-thermometer-quarter:before {
  content: "\f2ca"; }

.fa-thermometer-0:before,
.fa-thermometer-empty:before {
  content: "\f2cb"; }

.fa-shower:before {
  content: "\f2cc"; }

.fa-bathtub:before,
.fa-s15:before,
.fa-bath:before {
  content: "\f2cd"; }

.fa-podcast:before {
  content: "\f2ce"; }

.fa-window-maximize:before {
  content: "\f2d0"; }

.fa-window-minimize:before {
  content: "\f2d1"; }

.fa-window-restore:before {
  content: "\f2d2"; }

.fa-times-rectangle:before,
.fa-window-close:before {
  content: "\f2d3"; }

.fa-times-rectangle-o:before,
.fa-window-close-o:before {
  content: "\f2d4"; }

.fa-bandcamp:before {
  content: "\f2d5"; }

.fa-grav:before {
  content: "\f2d6"; }

.fa-etsy:before {
  content: "\f2d7"; }

.fa-imdb:before {
  content: "\f2d8"; }

.fa-ravelry:before {
  content: "\f2d9"; }

.fa-eercast:before {
  content: "\f2da"; }

.fa-microchip:before {
  content: "\f2db"; }

.fa-snowflake-o:before {
  content: "\f2dc"; }

.fa-superpowers:before {
  content: "\f2dd"; }

.fa-wpexplorer:before {
  content: "\f2de"; }

.fa-meetup:before {
  content: "\f2e0"; }

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0; }

.sr-only-focusable:active,
.sr-only-focusable:focus {
  position: static;
  width: auto;
  height: auto;
  margin: 0;
  overflow: visible;
  clip: auto; }

/*!
 *  Line Awesome 1.1.0 by @icons_8 - https://icons8.com/line-awesome
 *  License - https://icons8.com/good-boy-license/ (Font: SIL OFL 1.1, CSS: MIT License)
 *
 * Made with love by Icons8 [ https://icons8.com/ ] using FontCustom [ https://github.com/FontCustom/fontcustom ]
 *
 * Contacts:
 *    [ https://icons8.com/contact ]
 *
 * Follow Icon8 on
 *    Twitter [ https://twitter.com/icons_8 ]
 *    Facebook [ https://www.facebook.com/Icons8 ]
 *    Google+ [ https://plus.google.com/+Icons8 ]
 *    GitHub [ https://github.com/icons8 ]
 */
@font-face {
  font-family: "LineAwesome";
  src: url("/design/fonts/line-awesome/line-awesome.eot?v=1.1.");
  src: url("/design/fonts/line-awesome/line-awesome.eot??v=1.1.#iefix") format("embedded-opentype"), url("/design/fonts/line-awesome/line-awesome.woff2?v=1.1.") format("woff2"), url("/design/fonts/line-awesome/line-awesome.woff?v=1.1.") format("woff"), url("/design/line-awesome/line-awesome.ttf?v=1.1.") format("truetype"), url("/design/line-awesome/line-awesome.svg?v=1.1.#fa") format("svg");
  font-weight: normal;
  font-style: normal; }

@media screen and (-webkit-min-device-pixel-ratio: 0) {
  @font-face {
    font-family: "LineAwesome";
    src: url("/design/line-awesome/line-awesome.svg?v=1.1.#fa") format("svg"); } }

/* Thanks to http://fontawesome.io @fontawesome and @davegandy */
.la {
  display: inline-block;
  font: normal normal normal 16px/1 "LineAwesome";
  font-size: inherit;
  text-decoration: inherit;
  text-rendering: optimizeLegibility;
  text-transform: none;
  -moz-osx-font-smoothing: grayscale;
  -webkit-font-smoothing: antialiased;
  font-smoothing: antialiased; }

/* makes the font 33% larger relative to the icon container */
.la-lg {
  font-size: 1.33333333em;
  line-height: 0.75em;
  vertical-align: -15%; }

.la-2x {
  font-size: 2em; }

.la-3x {
  font-size: 3em; }

.la-4x {
  font-size: 4em; }

.la-5x {
  font-size: 5em; }

.la-fw {
  width: 1.28571429em;
  text-align: center; }

.la-ul {
  padding-left: 0;
  margin-left: 2.14285714em;
  list-style-type: none; }

.la-ul > li {
  position: relative; }

.la-li {
  position: absolute;
  left: -2.14285714em;
  width: 2.14285714em;
  top: 0.14285714em;
  text-align: center; }

.la-li.la-lg {
  left: -1.85714286em; }

.la-border {
  padding: .2em .25em .15em;
  border: solid 0.08em #eeeeee;
  border-radius: .1em; }

.pull-right {
  float: right; }

.pull-left {
  float: left; }

.li.pull-left {
  margin-right: .3em; }

.li.pull-right {
  margin-left: .3em; }

.la-spin {
  -webkit-animation: fa-spin 2s infinite linear;
  animation: fa-spin 2s infinite linear; }

@-webkit-keyframes fa-spin {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg); }
  100% {
    -webkit-transform: rotate(359deg);
    transform: rotate(359deg); } }

@keyframes fa-spin {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg); }
  100% {
    -webkit-transform: rotate(359deg);
    transform: rotate(359deg); } }

.la-rotate-90 {
  filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=1);
  -webkit-transform: rotate(90deg);
  -ms-transform: rotate(90deg);
  transform: rotate(90deg); }

.la-rotate-180 {
  filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=2);
  -webkit-transform: rotate(180deg);
  -ms-transform: rotate(180deg);
  transform: rotate(180deg); }

.la-rotate-270 {
  filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
  -webkit-transform: rotate(270deg);
  -ms-transform: rotate(270deg);
  transform: rotate(270deg); }

.la-flip-horizontal {
  filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=0, mirror=1);
  -webkit-transform: scale(-1, 1);
  -ms-transform: scale(-1, 1);
  transform: scale(-1, 1); }

.la-flip-vertical {
  filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=2, mirror=1);
  -webkit-transform: scale(1, -1);
  -ms-transform: scale(1, -1);
  transform: scale(1, -1); }

:root .la-rotate-90,
:root .la-rotate-180,
:root .la-rotate-270,
:root .la-flip-horizontal,
:root .la-flip-vertical {
  filter: none; }

.la-stack {
  position: relative;
  display: inline-block;
  width: 2em;
  height: 2em;
  line-height: 2em;
  vertical-align: middle; }

.la-stack-1x,
.la-stack-2x {
  position: absolute;
  left: 0;
  width: 100%;
  text-align: center; }

.la-stack-1x {
  line-height: inherit; }

.la-stack-2x {
  font-size: 2em; }

.la-inverse {
  color: #ffffff; }

/* Thanks to http://fontawesome.io @fontawesome and @davegandy */
.la-500px:before {
  content: "\f100"; }

.la-adjust:before {
  content: "\f101"; }

.la-adn:before {
  content: "\f102"; }

.la-align-center:before {
  content: "\f103"; }

.la-align-justify:before {
  content: "\f104"; }

.la-align-left:before {
  content: "\f105"; }

.la-align-right:before {
  content: "\f106"; }

.la-amazon:before {
  content: "\f107"; }

.la-ambulance:before {
  content: "\f108"; }

.la-anchor:before {
  content: "\f109"; }

.la-android:before {
  content: "\f10a"; }

.la-angellist:before {
  content: "\f10b"; }

.la-angle-double-down:before {
  content: "\f10c"; }

.la-angle-double-left:before {
  content: "\f10d"; }

.la-angle-double-right:before {
  content: "\f10e"; }

.la-angle-double-up:before {
  content: "\f10f"; }

.la-angle-down:before {
  content: "\f110"; }

.la-angle-left:before {
  content: "\f111"; }

.la-angle-right:before {
  content: "\f112"; }

.la-angle-up:before {
  content: "\f113"; }

.la-apple:before {
  content: "\f114"; }

.la-archive:before {
  content: "\f115"; }

.la-area-chart:before {
  content: "\f116"; }

.la-arrow-circle-down:before {
  content: "\f117"; }

.la-arrow-circle-left:before {
  content: "\f118"; }

.la-arrow-circle-o-down:before {
  content: "\f119"; }

.la-arrow-circle-o-left:before {
  content: "\f11a"; }

.la-arrow-circle-o-right:before {
  content: "\f11b"; }

.la-arrow-circle-o-up:before {
  content: "\f11c"; }

.la-arrow-circle-right:before {
  content: "\f11d"; }

.la-arrow-circle-up:before {
  content: "\f11e"; }

.la-arrow-down:before {
  content: "\f11f"; }

.la-arrow-left:before {
  content: "\f120"; }

.la-arrow-right:before {
  content: "\f121"; }

.la-arrow-up:before {
  content: "\f122"; }

.la-arrows:before {
  content: "\f123"; }

.la-arrows-alt:before {
  content: "\f124"; }

.la-arrows-h:before {
  content: "\f125"; }

.la-arrows-v:before {
  content: "\f126"; }

.la-asterisk:before {
  content: "\f127"; }

.la-at:before {
  content: "\f128"; }

.la-automobile:before {
  content: "\f129"; }

.la-backward:before {
  content: "\f12a"; }

.la-balance-scale:before {
  content: "\f12b"; }

.la-ban:before {
  content: "\f12c"; }

.la-bank:before {
  content: "\f12d"; }

.la-bar-chart:before {
  content: "\f12e"; }

.la-bar-chart-o:before {
  content: "\f12f"; }

.la-barcode:before {
  content: "\f130"; }

.la-bars:before {
  content: "\f131"; }

.la-battery-0:before {
  content: "\f132"; }

.la-battery-1:before {
  content: "\f133"; }

.la-battery-2:before {
  content: "\f134"; }

.la-battery-3:before {
  content: "\f135"; }

.la-battery-4:before {
  content: "\f136"; }

.la-battery-empty:before {
  content: "\f137"; }

.la-battery-full:before {
  content: "\f138"; }

.la-battery-half:before {
  content: "\f139"; }

.la-battery-quarter:before {
  content: "\f13a"; }

.la-battery-three-quarters:before {
  content: "\f13b"; }

.la-bed:before {
  content: "\f13c"; }

.la-beer:before {
  content: "\f13d"; }

.la-behance:before {
  content: "\f13e"; }

.la-behance-square:before {
  content: "\f13f"; }

.la-bell:before {
  content: "\f140"; }

.la-bell-o:before {
  content: "\f141"; }

.la-bell-slash:before {
  content: "\f142"; }

.la-bell-slash-o:before {
  content: "\f143"; }

.la-bicycle:before {
  content: "\f144"; }

.la-binoculars:before {
  content: "\f145"; }

.la-birthday-cake:before {
  content: "\f146"; }

.la-bitbucket:before {
  content: "\f147"; }

.la-bitbucket-square:before {
  content: "\f148"; }

.la-bitcoin:before {
  content: "\f149"; }

.la-black-tie:before {
  content: "\f14a"; }

.la-bold:before {
  content: "\f14b"; }

.la-bolt:before {
  content: "\f14c"; }

.la-bomb:before {
  content: "\f14d"; }

.la-book:before {
  content: "\f14e"; }

.la-bookmark:before {
  content: "\f14f"; }

.la-bookmark-o:before {
  content: "\f150"; }

.la-briefcase:before {
  content: "\f151"; }

.la-btc:before {
  content: "\f152"; }

.la-bug:before {
  content: "\f153"; }

.la-building:before {
  content: "\f154"; }

.la-building-o:before {
  content: "\f155"; }

.la-bullhorn:before {
  content: "\f156"; }

.la-bullseye:before {
  content: "\f157"; }

.la-bus:before {
  content: "\f158"; }

.la-buysellads:before {
  content: "\f159"; }

.la-cab:before {
  content: "\f15a"; }

.la-calculator:before {
  content: "\f15b"; }

.la-calendar:before {
  content: "\f15c"; }

.la-calendar-check-o:before {
  content: "\f15d"; }

.la-calendar-minus-o:before {
  content: "\f15e"; }

.la-calendar-o:before {
  content: "\f15f"; }

.la-calendar-plus-o:before {
  content: "\f160"; }

.la-calendar-times-o:before {
  content: "\f161"; }

.la-camera:before {
  content: "\f162"; }

.la-camera-retro:before {
  content: "\f163"; }

.la-car:before {
  content: "\f164"; }

.la-caret-down:before {
  content: "\f165"; }

.la-caret-left:before {
  content: "\f166"; }

.la-caret-right:before {
  content: "\f167"; }

.la-caret-square-o-down:before, .la-toggle-down:before {
  content: "\f168"; }

.la-caret-square-o-left:before, .la-toggle-left:before {
  content: "\f169"; }

.la-caret-square-o-right:before, .la-toggle-right:before {
  content: "\f16a"; }

.la-caret-square-o-up:before, .la-toggle-up:before {
  content: "\f16b"; }

.la-caret-up:before {
  content: "\f16c"; }

.la-cart-arrow-down:before {
  content: "\f16d"; }

.la-cart-plus:before {
  content: "\f16e"; }

.la-cc:before {
  content: "\f16f"; }

.la-cc-amex:before {
  content: "\f170"; }

.la-cc-diners-club:before {
  content: "\f171"; }

.la-cc-discover:before {
  content: "\f172"; }

.la-cc-jcb:before {
  content: "\f173"; }

.la-cc-mastercard:before {
  content: "\f174"; }

.la-cc-paypal:before {
  content: "\f175"; }

.la-cc-stripe:before {
  content: "\f176"; }

.la-cc-visa:before {
  content: "\f177"; }

.la-certificate:before {
  content: "\f178"; }

.la-chain:before {
  content: "\f179"; }

.la-chain-broken:before {
  content: "\f17a"; }

.la-check:before {
  content: "\f17b"; }

.la-check-circle:before {
  content: "\f17c"; }

.la-check-circle-o:before {
  content: "\f17d"; }

.la-check-square:before {
  content: "\f17e"; }

.la-check-square-o:before {
  content: "\f17f"; }

.la-chevron-circle-down:before {
  content: "\f180"; }

.la-chevron-circle-left:before {
  content: "\f181"; }

.la-chevron-circle-right:before {
  content: "\f182"; }

.la-chevron-circle-up:before {
  content: "\f183"; }

.la-chevron-down:before {
  content: "\f184"; }

.la-chevron-left:before {
  content: "\f185"; }

.la-chevron-right:before {
  content: "\f186"; }

.la-chevron-up:before {
  content: "\f187"; }

.la-child:before {
  content: "\f188"; }

.la-chrome:before {
  content: "\f189"; }

.la-circle:before {
  content: "\f18a"; }

.la-circle-o:before {
  content: "\f18b"; }

.la-circle-o-notch:before {
  content: "\f18c"; }

.la-circle-thin:before {
  content: "\f18d"; }

.la-clipboard:before {
  content: "\f18e"; }

.la-clock-o:before {
  content: "\f18f"; }

.la-clone:before {
  content: "\f190"; }

.la-close:before {
  content: "\f191"; }

.la-cloud:before {
  content: "\f192"; }

.la-cloud-download:before {
  content: "\f193"; }

.la-cloud-upload:before {
  content: "\f194"; }

.la-cny:before {
  content: "\f195"; }

.la-code:before {
  content: "\f196"; }

.la-code-fork:before {
  content: "\f197"; }

.la-codepen:before {
  content: "\f198"; }

.la-coffee:before {
  content: "\f199"; }

.la-cog:before {
  content: "\f19a"; }

.la-cogs:before {
  content: "\f19b"; }

.la-columns:before {
  content: "\f19c"; }

.la-comment:before {
  content: "\f19d"; }

.la-comment-o:before {
  content: "\f19e"; }

.la-commenting:before {
  content: "\f19f"; }

.la-commenting-o:before {
  content: "\f1a0"; }

.la-comments:before {
  content: "\f1a1"; }

.la-comments-o:before {
  content: "\f1a2"; }

.la-compass:before {
  content: "\f1a3"; }

.la-compress:before {
  content: "\f1a4"; }

.la-connectdevelop:before {
  content: "\f1a5"; }

.la-contao:before {
  content: "\f1a6"; }

.la-copy:before {
  content: "\f1a7"; }

.la-copyright:before {
  content: "\f1a8"; }

.la-creative-commons:before {
  content: "\f1a9"; }

.la-credit-card:before {
  content: "\f1aa"; }

.la-crop:before {
  content: "\f1ab"; }

.la-crosshairs:before {
  content: "\f1ac"; }

.la-css3:before {
  content: "\f1ad"; }

.la-cube:before {
  content: "\f1ae"; }

.la-cubes:before {
  content: "\f1af"; }

.la-cut:before {
  content: "\f1b0"; }

.la-cutlery:before {
  content: "\f1b1"; }

.la-dashboard:before {
  content: "\f1b2"; }

.la-dashcube:before {
  content: "\f1b3"; }

.la-database:before {
  content: "\f1b4"; }

.la-dedent:before {
  content: "\f1b5"; }

.la-delicious:before {
  content: "\f1b6"; }

.la-desktop:before {
  content: "\f1b7"; }

.la-deviantart:before {
  content: "\f1b8"; }

.la-diamond:before {
  content: "\f1b9"; }

.la-digg:before {
  content: "\f1ba"; }

.la-dollar:before {
  content: "\f1bb"; }

.la-dot-circle-o:before {
  content: "\f1bc"; }

.la-download:before {
  content: "\f1bd"; }

.la-dribbble:before {
  content: "\f1be"; }

.la-dropbox:before {
  content: "\f1bf"; }

.la-drupal:before {
  content: "\f1c0"; }

.la-edit:before {
  content: "\f1c1"; }

.la-eject:before {
  content: "\f1c2"; }

.la-ellipsis-h:before {
  content: "\f1c3"; }

.la-ellipsis-v:before {
  content: "\f1c4"; }

.la-empire:before, .la-ge:before {
  content: "\f1c5"; }

.la-envelope:before {
  content: "\f1c6"; }

.la-envelope-o:before {
  content: "\f1c7"; }

.la-envelope-square:before {
  content: "\f1c8"; }

.la-eraser:before {
  content: "\f1c9"; }

.la-eur:before {
  content: "\f1ca"; }

.la-euro:before {
  content: "\f1cb"; }

.la-exchange:before {
  content: "\f1cc"; }

.la-exclamation:before {
  content: "\f1cd"; }

.la-exclamation-circle:before {
  content: "\f1ce"; }

.la-exclamation-triangle:before {
  content: "\f1cf"; }

.la-expand:before {
  content: "\f1d0"; }

.la-expeditedssl:before {
  content: "\f1d1"; }

.la-external-link:before {
  content: "\f1d2"; }

.la-external-link-square:before {
  content: "\f1d3"; }

.la-eye:before {
  content: "\f1d4"; }

.la-eye-slash:before {
  content: "\f1d5"; }

.la-eyedropper:before {
  content: "\f1d6"; }

.la-facebook:before, .la-facebook-f:before {
  content: "\f1d7"; }

.la-facebook-official:before {
  content: "\f1d8"; }

.la-facebook-square:before {
  content: "\f1d9"; }

.la-fast-backward:before {
  content: "\f1da"; }

.la-fast-forward:before {
  content: "\f1db"; }

.la-fax:before {
  content: "\f1dc"; }

.la-female:before {
  content: "\f1dd"; }

.la-fighter-jet:before {
  content: "\f1de"; }

.la-file:before {
  content: "\f1df"; }

.la-file-archive-o:before {
  content: "\f1e0"; }

.la-file-audio-o:before {
  content: "\f1e1"; }

.la-file-code-o:before {
  content: "\f1e2"; }

.la-file-excel-o:before {
  content: "\f1e3"; }

.la-file-image-o:before {
  content: "\f1e4"; }

.la-file-movie-o:before {
  content: "\f1e5"; }

.la-file-o:before {
  content: "\f1e6"; }

.la-file-pdf-o:before {
  content: "\f1e7"; }

.la-file-photo-o:before {
  content: "\f1e8"; }

.la-file-picture-o:before {
  content: "\f1e9"; }

.la-file-powerpoint-o:before {
  content: "\f1ea"; }

.la-file-sound-o:before {
  content: "\f1eb"; }

.la-file-text:before {
  content: "\f1ec"; }

.la-file-text-o:before {
  content: "\f1ed"; }

.la-file-video-o:before {
  content: "\f1ee"; }

.la-file-word-o:before {
  content: "\f1ef"; }

.la-file-zip-o:before {
  content: "\f1f0"; }

.la-files-o:before {
  content: "\f1f1"; }

.la-film:before {
  content: "\f1f2"; }

.la-filter:before {
  content: "\f1f3"; }

.la-fire:before {
  content: "\f1f4"; }

.la-fire-extinguisher:before {
  content: "\f1f5"; }

.la-firefox:before {
  content: "\f1f6"; }

.la-flag:before {
  content: "\f1f7"; }

.la-flag-checkered:before {
  content: "\f1f8"; }

.la-flag-o:before {
  content: "\f1f9"; }

.la-flash:before {
  content: "\f1fa"; }

.la-flask:before {
  content: "\f1fb"; }

.la-flickr:before {
  content: "\f1fc"; }

.la-floppy-o:before {
  content: "\f1fd"; }

.la-folder:before {
  content: "\f1fe"; }

.la-folder-o:before {
  content: "\f1ff"; }

.la-folder-open:before {
  content: "\f200"; }

.la-folder-open-o:before {
  content: "\f201"; }

.la-font:before {
  content: "\f202"; }

.la-fonticons:before {
  content: "\f203"; }

.la-forumbee:before {
  content: "\f204"; }

.la-forward:before {
  content: "\f205"; }

.la-foursquare:before {
  content: "\f206"; }

.la-frown-o:before {
  content: "\f207"; }

.la-futbol-o:before, .la-soccer-ball-o:before {
  content: "\f208"; }

.la-gamepad:before {
  content: "\f209"; }

.la-gavel:before {
  content: "\f20a"; }

.la-gbp:before {
  content: "\f20b"; }

.la-gear:before {
  content: "\f20c"; }

.la-gears:before {
  content: "\f20d"; }

.la-genderless:before {
  content: "\f20e"; }

.la-get-pocket:before {
  content: "\f20f"; }

.la-gg:before {
  content: "\f210"; }

.la-gg-circle:before {
  content: "\f211"; }

.la-gift:before {
  content: "\f212"; }

.la-git:before {
  content: "\f213"; }

.la-git-square:before {
  content: "\f214"; }

.la-github:before {
  content: "\f215"; }

.la-github-alt:before {
  content: "\f216"; }

.la-github-square:before {
  content: "\f217"; }

.la-glass:before {
  content: "\f218"; }

.la-globe:before {
  content: "\f219"; }

.la-google:before {
  content: "\f21a"; }

.la-google-plus:before {
  content: "\f21b"; }

.la-google-plus-square:before {
  content: "\f21c"; }

.la-google-wallet:before {
  content: "\f21d"; }

.la-graduation-cap:before {
  content: "\f21e"; }

.la-gratipay:before, .la-gittip:before {
  content: "\f21f"; }

.la-group:before {
  content: "\f220"; }

.la-h-square:before {
  content: "\f221"; }

.la-hacker-news:before {
  content: "\f222"; }

.la-hand-grab-o:before {
  content: "\f223"; }

.la-hand-lizard-o:before {
  content: "\f224"; }

.la-hand-o-down:before {
  content: "\f225"; }

.la-hand-o-left:before {
  content: "\f226"; }

.la-hand-o-right:before {
  content: "\f227"; }

.la-hand-o-up:before {
  content: "\f228"; }

.la-hand-paper-o:before {
  content: "\f229"; }

.la-hand-peace-o:before {
  content: "\f22a"; }

.la-hand-pointer-o:before {
  content: "\f22b"; }

.la-hand-rock-o:before {
  content: "\f22c"; }

.la-hand-scissors-o:before {
  content: "\f22d"; }

.la-hand-spock-o:before {
  content: "\f22e"; }

.la-hand-stop-o:before {
  content: "\f22f"; }

.la-hdd-o:before {
  content: "\f230"; }

.la-header:before {
  content: "\f231"; }

.la-headphones:before {
  content: "\f232"; }

.la-heart:before {
  content: "\f233"; }

.la-heart-o:before {
  content: "\f234"; }

.la-heartbeat:before {
  content: "\f235"; }

.la-history:before {
  content: "\f236"; }

.la-home:before {
  content: "\f237"; }

.la-hospital-o:before {
  content: "\f238"; }

.la-hotel:before {
  content: "\f239"; }

.la-hourglass:before {
  content: "\f23a"; }

.la-hourglass-1:before {
  content: "\f23b"; }

.la-hourglass-2:before {
  content: "\f23c"; }

.la-hourglass-3:before {
  content: "\f23d"; }

.la-hourglass-end:before {
  content: "\f23e"; }

.la-hourglass-half:before {
  content: "\f23f"; }

.la-hourglass-o:before {
  content: "\f240"; }

.la-hourglass-start:before {
  content: "\f241"; }

.la-houzz:before {
  content: "\f242"; }

.la-html5:before {
  content: "\f243"; }

.la-i-cursor:before {
  content: "\f244"; }

.la-ils:before {
  content: "\f245"; }

.la-image:before {
  content: "\f246"; }

.la-inbox:before {
  content: "\f247"; }

.la-indent:before {
  content: "\f248"; }

.la-industry:before {
  content: "\f249"; }

.la-info:before {
  content: "\f24a"; }

.la-info-circle:before {
  content: "\f24b"; }

.la-inr:before {
  content: "\f24c"; }

.la-instagram:before {
  content: "\f24d"; }

.la-institution:before {
  content: "\f24e"; }

.la-internet-explorer:before {
  content: "\f24f"; }

.la-ioxhost:before {
  content: "\f250"; }

.la-italic:before {
  content: "\f251"; }

.la-joomla:before {
  content: "\f252"; }

.la-jpy:before {
  content: "\f253"; }

.la-jsfiddle:before {
  content: "\f254"; }

.la-key:before {
  content: "\f255"; }

.la-keyboard-o:before {
  content: "\f256"; }

.la-krw:before {
  content: "\f257"; }

.la-language:before {
  content: "\f258"; }

.la-laptop:before {
  content: "\f259"; }

.la-lastfm:before {
  content: "\f25a"; }

.la-lastfm-square:before {
  content: "\f25b"; }

.la-leaf:before {
  content: "\f25c"; }

.la-leanpub:before {
  content: "\f25d"; }

.la-legal:before {
  content: "\f25e"; }

.la-lemon-o:before {
  content: "\f25f"; }

.la-level-down:before {
  content: "\f260"; }

.la-level-up:before {
  content: "\f261"; }

.la-life-bouy:before {
  content: "\f262"; }

.la-life-buoy:before {
  content: "\f263"; }

.la-life-ring:before, .la-support:before {
  content: "\f264"; }

.la-life-saver:before {
  content: "\f265"; }

.la-lightbulb-o:before {
  content: "\f266"; }

.la-line-chart:before {
  content: "\f267"; }

.la-link:before {
  content: "\f268"; }

.la-linkedin:before {
  content: "\f269"; }

.la-linkedin-square:before {
  content: "\f26a"; }

.la-linux:before {
  content: "\f26b"; }

.la-list:before {
  content: "\f26c"; }

.la-list-alt:before {
  content: "\f26d"; }

.la-list-ol:before {
  content: "\f26e"; }

.la-list-ul:before {
  content: "\f26f"; }

.la-location-arrow:before {
  content: "\f270"; }

.la-lock:before {
  content: "\f271"; }

.la-long-arrow-down:before {
  content: "\f272"; }

.la-long-arrow-left:before {
  content: "\f273"; }

.la-long-arrow-right:before {
  content: "\f274"; }

.la-long-arrow-up:before {
  content: "\f275"; }

.la-magic:before {
  content: "\f276"; }

.la-magnet:before {
  content: "\f277"; }

.la-mail-forward:before {
  content: "\f278"; }

.la-mail-reply:before {
  content: "\f279"; }

.la-mail-reply-all:before {
  content: "\f27a"; }

.la-male:before {
  content: "\f27b"; }

.la-map:before {
  content: "\f27c"; }

.la-map-marker:before {
  content: "\f27d"; }

.la-map-o:before {
  content: "\f27e"; }

.la-map-pin:before {
  content: "\f27f"; }

.la-map-signs:before {
  content: "\f280"; }

.la-mars:before {
  content: "\f281"; }

.la-mars-double:before {
  content: "\f282"; }

.la-mars-stroke:before {
  content: "\f283"; }

.la-mars-stroke-h:before {
  content: "\f284"; }

.la-mars-stroke-v:before {
  content: "\f285"; }

.la-maxcdn:before {
  content: "\f286"; }

.la-meanpath:before {
  content: "\f287"; }

.la-medium:before {
  content: "\f288"; }

.la-medkit:before {
  content: "\f289"; }

.la-meh-o:before {
  content: "\f28a"; }

.la-mercury:before {
  content: "\f28b"; }

.la-microphone:before {
  content: "\f28c"; }

.la-microphone-slash:before {
  content: "\f28d"; }

.la-minus:before {
  content: "\f28e"; }

.la-minus-circle:before {
  content: "\f28f"; }

.la-minus-square:before {
  content: "\f290"; }

.la-minus-square-o:before {
  content: "\f291"; }

.la-mobile:before {
  content: "\f292"; }

.la-mobile-phone:before {
  content: "\f293"; }

.la-money:before {
  content: "\f294"; }

.la-moon-o:before {
  content: "\f295"; }

.la-mortar-board:before {
  content: "\f296"; }

.la-motorcycle:before {
  content: "\f297"; }

.la-mouse-pointer:before {
  content: "\f298"; }

.la-music:before {
  content: "\f299"; }

.la-navicon:before {
  content: "\f29a"; }

.la-neuter:before {
  content: "\f29b"; }

.la-newspaper-o:before {
  content: "\f29c"; }

.la-object-group:before {
  content: "\f29d"; }

.la-object-ungroup:before {
  content: "\f29e"; }

.la-odnoklassniki:before {
  content: "\f29f"; }

.la-odnoklassniki-square:before {
  content: "\f2a0"; }

.la-opencart:before {
  content: "\f2a1"; }

.la-openid:before {
  content: "\f2a2"; }

.la-opera:before {
  content: "\f2a3"; }

.la-optin-monster:before {
  content: "\f2a4"; }

.la-outdent:before {
  content: "\f2a5"; }

.la-pagelines:before {
  content: "\f2a6"; }

.la-paint-brush:before {
  content: "\f2a7"; }

.la-paper-plane:before, .la-send:before {
  content: "\f2a8"; }

.la-paper-plane-o:before, .la-send-o:before {
  content: "\f2a9"; }

.la-paperclip:before {
  content: "\f2aa"; }

.la-paragraph:before {
  content: "\f2ab"; }

.la-paste:before {
  content: "\f2ac"; }

.la-pause:before {
  content: "\f2ad"; }

.la-paw:before {
  content: "\f2ae"; }

.la-paypal:before {
  content: "\f2af"; }

.la-pencil:before {
  content: "\f2b0"; }

.la-pencil-square:before {
  content: "\f2b1"; }

.la-pencil-square-o:before {
  content: "\f2b2"; }

.la-phone:before {
  content: "\f2b3"; }

.la-phone-square:before {
  content: "\f2b4"; }

.la-photo:before {
  content: "\f2b5"; }

.la-picture-o:before {
  content: "\f2b6"; }

.la-pie-chart:before {
  content: "\f2b7"; }

.la-pied-piper:before {
  content: "\f2b8"; }

.la-pied-piper-alt:before {
  content: "\f2b9"; }

.la-pinterest:before {
  content: "\f2ba"; }

.la-pinterest-p:before {
  content: "\f2bb"; }

.la-pinterest-square:before {
  content: "\f2bc"; }

.la-plane:before {
  content: "\f2bd"; }

.la-play:before {
  content: "\f2be"; }

.la-play-circle:before {
  content: "\f2bf"; }

.la-play-circle-o:before {
  content: "\f2c0"; }

.la-plug:before {
  content: "\f2c1"; }

.la-plus:before {
  content: "\f2c2"; }

.la-plus-circle:before {
  content: "\f2c3"; }

.la-plus-square:before {
  content: "\f2c4"; }

.la-plus-square-o:before {
  content: "\f2c5"; }

.la-power-off:before {
  content: "\f2c6"; }

.la-print:before {
  content: "\f2c7"; }

.la-puzzle-piece:before {
  content: "\f2c8"; }

.la-qq:before {
  content: "\f2c9"; }

.la-qrcode:before {
  content: "\f2ca"; }

.la-question:before {
  content: "\f2cb"; }

.la-question-circle:before {
  content: "\f2cc"; }

.la-quote-left:before {
  content: "\f2cd"; }

.la-quote-right:before {
  content: "\f2ce"; }

.la-ra:before {
  content: "\f2cf"; }

.la-random:before {
  content: "\f2d0"; }

.la-rebel:before {
  content: "\f2d1"; }

.la-recycle:before {
  content: "\f2d2"; }

.la-reddit:before {
  content: "\f2d3"; }

.la-reddit-square:before {
  content: "\f2d4"; }

.la-refresh:before {
  content: "\f2d5"; }

.la-registered:before {
  content: "\f2d6"; }

.la-renren:before {
  content: "\f2d7"; }

.la-reorder:before {
  content: "\f2d8"; }

.la-repeat:before {
  content: "\f2d9"; }

.la-reply:before {
  content: "\f2da"; }

.la-reply-all:before {
  content: "\f2db"; }

.la-retweet:before {
  content: "\f2dc"; }

.la-rmb:before {
  content: "\f2dd"; }

.la-road:before {
  content: "\f2de"; }

.la-rocket:before {
  content: "\f2df"; }

.la-rotate-left:before {
  content: "\f2e0"; }

.la-rotate-right:before {
  content: "\f2e1"; }

.la-rouble:before {
  content: "\f2e2"; }

.la-rss:before, .la-feed:before {
  content: "\f2e3"; }

.la-rss-square:before {
  content: "\f2e4"; }

.la-rub:before {
  content: "\f2e5"; }

.la-ruble:before {
  content: "\f2e6"; }

.la-rupee:before {
  content: "\f2e7"; }

.la-safari:before {
  content: "\f2e8"; }

.la-save:before {
  content: "\f2e9"; }

.la-scissors:before {
  content: "\f2ea"; }

.la-search:before {
  content: "\f2eb"; }

.la-search-minus:before {
  content: "\f2ec"; }

.la-search-plus:before {
  content: "\f2ed"; }

.la-sellsy:before {
  content: "\f2ee"; }

.la-server:before {
  content: "\f2ef"; }

.la-share:before {
  content: "\f2f0"; }

.la-share-alt:before {
  content: "\f2f1"; }

.la-share-alt-square:before {
  content: "\f2f2"; }

.la-share-square:before {
  content: "\f2f3"; }

.la-share-square-o:before {
  content: "\f2f4"; }

.la-shekel:before {
  content: "\f2f5"; }

.la-sheqel:before {
  content: "\f2f6"; }

.la-shield:before {
  content: "\f2f7"; }

.la-ship:before {
  content: "\f2f8"; }

.la-shirtsinbulk:before {
  content: "\f2f9"; }

.la-shopping-cart:before {
  content: "\f2fa"; }

.la-sign-in:before {
  content: "\f2fb"; }

.la-sign-out:before {
  content: "\f2fc"; }

.la-signal:before {
  content: "\f2fd"; }

.la-simplybuilt:before {
  content: "\f2fe"; }

.la-sitemap:before {
  content: "\f2ff"; }

.la-skyatlas:before {
  content: "\f300"; }

.la-skype:before {
  content: "\f301"; }

.la-slack:before {
  content: "\f302"; }

.la-sliders:before {
  content: "\f303"; }

.la-slideshare:before {
  content: "\f304"; }

.la-smile-o:before {
  content: "\f305"; }

.la-sort:before, .la-unsorted:before {
  content: "\f306"; }

.la-sort-alpha-asc:before {
  content: "\f307"; }

.la-sort-alpha-desc:before {
  content: "\f308"; }

.la-sort-amount-asc:before {
  content: "\f309"; }

.la-sort-amount-desc:before {
  content: "\f30a"; }

.la-sort-asc:before, .la-sort-up:before {
  content: "\f30b"; }

.la-sort-desc:before, .la-sort-down:before {
  content: "\f30c"; }

.la-sort-numeric-asc:before {
  content: "\f30d"; }

.la-sort-numeric-desc:before {
  content: "\f30e"; }

.la-soundcloud:before {
  content: "\f30f"; }

.la-space-shuttle:before {
  content: "\f310"; }

.la-spinner:before {
  content: "\f311"; }

.la-spoon:before {
  content: "\f312"; }

.la-spotify:before {
  content: "\f313"; }

.la-square:before {
  content: "\f314"; }

.la-square-o:before {
  content: "\f315"; }

.la-stack-exchange:before {
  content: "\f316"; }

.la-stack-overflow:before {
  content: "\f317"; }

.la-star:before {
  content: "\f318"; }

.la-star-half:before {
  content: "\f319"; }

.la-star-half-o:before, .la-star-half-full:before, .la-star-half-empty:before {
  content: "\f31a"; }

.la-star-o:before {
  content: "\f31b"; }

.la-steam:before {
  content: "\f31c"; }

.la-steam-square:before {
  content: "\f31d"; }

.la-step-backward:before {
  content: "\f31e"; }

.la-step-forward:before {
  content: "\f31f"; }

.la-stethoscope:before {
  content: "\f320"; }

.la-sticky-note:before {
  content: "\f321"; }

.la-sticky-note-o:before {
  content: "\f322"; }

.la-stop:before {
  content: "\f323"; }

.la-street-view:before {
  content: "\f324"; }

.la-strikethrough:before {
  content: "\f325"; }

.la-stumbleupon:before {
  content: "\f326"; }

.la-stumbleupon-circle:before {
  content: "\f327"; }

.la-subscript:before {
  content: "\f328"; }

.la-subway:before {
  content: "\f329"; }

.la-suitcase:before {
  content: "\f32a"; }

.la-sun-o:before {
  content: "\f32b"; }

.la-superscript:before {
  content: "\f32c"; }

.la-table:before {
  content: "\f32d"; }

.la-tablet:before {
  content: "\f32e"; }

.la-tachometer:before {
  content: "\f32f"; }

.la-tag:before {
  content: "\f330"; }

.la-tags:before {
  content: "\f331"; }

.la-tasks:before {
  content: "\f332"; }

.la-taxi:before {
  content: "\f333"; }

.la-television:before, .la-tv:before {
  content: "\f334"; }

.la-tencent-weibo:before {
  content: "\f335"; }

.la-terminal:before {
  content: "\f336"; }

.la-text-height:before {
  content: "\f337"; }

.la-text-width:before {
  content: "\f338"; }

.la-th:before {
  content: "\f339"; }

.la-th-large:before {
  content: "\f33a"; }

.la-th-list:before {
  content: "\f33b"; }

.la-thumb-tack:before {
  content: "\f33c"; }

.la-thumbs-down:before {
  content: "\f33d"; }

.la-thumbs-o-down:before {
  content: "\f33e"; }

.la-thumbs-o-up:before {
  content: "\f33f"; }

.la-thumbs-up:before {
  content: "\f340"; }

.la-ticket:before {
  content: "\f341"; }

.la-times:before, .la-remove:before {
  content: "\f342"; }

.la-times-circle:before {
  content: "\f343"; }

.la-times-circle-o:before {
  content: "\f344"; }

.la-tint:before {
  content: "\f345"; }

.la-toggle-off:before {
  content: "\f346"; }

.la-toggle-on:before {
  content: "\f347"; }

.la-trademark:before {
  content: "\f348"; }

.la-train:before {
  content: "\f349"; }

.la-transgender:before, .la-intersex:before {
  content: "\f34a"; }

.la-transgender-alt:before {
  content: "\f34b"; }

.la-trash:before {
  content: "\f34c"; }

.la-trash-o:before {
  content: "\f34d"; }

.la-tree:before {
  content: "\f34e"; }

.la-trello:before {
  content: "\f34f"; }

.la-tripadvisor:before {
  content: "\f350"; }

.la-trophy:before {
  content: "\f351"; }

.la-truck:before {
  content: "\f352"; }

.la-try:before {
  content: "\f353"; }

.la-tty:before {
  content: "\f354"; }

.la-tumblr:before {
  content: "\f355"; }

.la-tumblr-square:before {
  content: "\f356"; }

.la-turkish-lira:before {
  content: "\f357"; }

.la-twitch:before {
  content: "\f358"; }

.la-twitter:before {
  content: "\f359"; }

.la-twitter-square:before {
  content: "\f35a"; }

.la-umbrella:before {
  content: "\f35b"; }

.la-underline:before {
  content: "\f35c"; }

.la-undo:before {
  content: "\f35d"; }

.la-university:before {
  content: "\f35e"; }

.la-unlink:before {
  content: "\f35f"; }

.la-unlock:before {
  content: "\f360"; }

.la-unlock-alt:before {
  content: "\f361"; }

.la-upload:before {
  content: "\f362"; }

.la-usd:before {
  content: "\f363"; }

.la-user:before {
  content: "\f364"; }

.la-user-md:before {
  content: "\f365"; }

.la-user-plus:before {
  content: "\f366"; }

.la-user-secret:before {
  content: "\f367"; }

.la-user-times:before {
  content: "\f368"; }

.la-users:before {
  content: "\f369"; }

.la-venus:before {
  content: "\f36a"; }

.la-venus-double:before {
  content: "\f36b"; }

.la-venus-mars:before {
  content: "\f36c"; }

.la-viacoin:before {
  content: "\f36d"; }

.la-video-camera:before {
  content: "\f36e"; }

.la-vimeo:before {
  content: "\f36f"; }

.la-vimeo-square:before {
  content: "\f370"; }

.la-vine:before {
  content: "\f371"; }

.la-vk:before {
  content: "\f372"; }

.la-volume-down:before {
  content: "\f373"; }

.la-volume-off:before {
  content: "\f374"; }

.la-volume-up:before {
  content: "\f375"; }

.la-warning:before {
  content: "\f376"; }

.la-wechat:before {
  content: "\f377"; }

.la-weibo:before {
  content: "\f378"; }

.la-weixin:before {
  content: "\f379"; }

.la-whatsapp:before {
  content: "\f37a"; }

.la-wheelchair:before {
  content: "\f37b"; }

.la-wifi:before {
  content: "\f37c"; }

.la-wikipedia-w:before {
  content: "\f37d"; }

.la-windows:before {
  content: "\f37e"; }

.la-won:before {
  content: "\f37f"; }

.la-wordpress:before {
  content: "\f380"; }

.la-wrench:before {
  content: "\f381"; }

.la-xing:before {
  content: "\f382"; }

.la-xing-square:before {
  content: "\f383"; }

.la-y-combinator:before {
  content: "\f384"; }

.la-y-combinator-square:before {
  content: "\f385"; }

.la-yahoo:before {
  content: "\f386"; }

.la-yc:before {
  content: "\f387"; }

.la-yc-square:before {
  content: "\f388"; }

.la-yelp:before {
  content: "\f389"; }

.la-yen:before {
  content: "\f38a"; }

.la-youtube:before {
  content: "\f38b"; }

.la-youtube-play:before {
  content: "\f38c"; }

.la-youtube-square:before {
  content: "\f38d"; }

/*
  	Flaticon icon font: Flaticon
  	Creation date: 20/03/2017 20:02
  	*/
@font-face {
  font-family: "Flaticon";
  src: url("/design/fonts/flaticon/Flaticon.eot");
  src: url("/design/fonts/flaticon/Flaticon.eot?#iefix") format("embedded-opentype"), url("/design/fonts/flaticon/Flaticon.woff") format("woff"), url("/design/fonts/flaticon/Flaticon.ttf") format("truetype"), url("/design/fonts/flaticon/Flaticon.svg#Flaticon") format("svg");
  font-weight: normal;
  font-style: normal; }

@media screen and (-webkit-min-device-pixel-ratio: 0) {
  @font-face {
    font-family: "Flaticon";
    src: url("/design/fonts/flaticon/Flaticon.svg#Flaticon") format("svg"); } }

[class^="flaticon-"]:before,
[class*=" flaticon-"]:before {
  font-family: Flaticon;
  font-style: normal;
  font-weight: normal;
  font-variant: normal;
  line-height: 1;
  text-decoration: inherit;
  text-rendering: optimizeLegibility;
  text-transform: none;
  -moz-osx-font-smoothing: grayscale;
  -webkit-font-smoothing: antialiased;
  font-smoothing: antialiased; }

.flaticon-piggy-bank:before {
  content: "\f100"; }

.flaticon-confetti:before {
  content: "\f101"; }

.flaticon-rocket:before {
  content: "\f102"; }

.flaticon-gift:before {
  content: "\f103"; }

.flaticon-truck:before {
  content: "\f104"; }

.flaticon-user-settings:before {
  content: "\f105"; }

.flaticon-user-add:before {
  content: "\f106"; }

.flaticon-user-ok:before {
  content: "\f107"; }

.flaticon-internet:before {
  content: "\f108"; }

.flaticon-alert-2:before {
  content: "\f109"; }

.flaticon-alarm:before {
  content: "\f10a"; }

.flaticon-grid-menu:before {
  content: "\f10b"; }

.flaticon-up-arrow-1:before {
  content: "\f10c"; }

.flaticon-more-v3:before {
  content: "\f10d"; }

.flaticon-lock-1:before {
  content: "\f10e"; }

.flaticon-profile-1:before {
  content: "\f10f"; }

.flaticon-users:before {
  content: "\f110"; }

.flaticon-map-location:before {
  content: "\f111"; }

.flaticon-placeholder-2:before {
  content: "\f112"; }

.flaticon-route:before {
  content: "\f113"; }

.flaticon-more-v4:before {
  content: "\f114"; }

.flaticon-lock:before {
  content: "\f115"; }

.flaticon-multimedia-2:before {
  content: "\f116"; }

.flaticon-add:before {
  content: "\f117"; }

.flaticon-more-v5:before {
  content: "\f118"; }

.flaticon-more-v6:before {
  content: "\f119"; }

.flaticon-grid-menu-v2:before {
  content: "\f11a"; }

.flaticon-suitcase:before {
  content: "\f11b"; }

.flaticon-app:before {
  content: "\f11c"; }

.flaticon-interface-9:before {
  content: "\f11d"; }

.flaticon-time-3:before {
  content: "\f11e"; }

.flaticon-list-3:before {
  content: "\f11f"; }

.flaticon-list-2:before {
  content: "\f120"; }

.flaticon-file-1:before {
  content: "\f121"; }

.flaticon-folder-4:before {
  content: "\f122"; }

.flaticon-folder-3:before {
  content: "\f123"; }

.flaticon-folder-2:before {
  content: "\f124"; }

.flaticon-folder-1:before {
  content: "\f125"; }

.flaticon-time-2:before {
  content: "\f126"; }

.flaticon-search-1:before {
  content: "\f127"; }

.flaticon-music-1:before {
  content: "\f128"; }

.flaticon-music-2:before {
  content: "\f129"; }

.flaticon-tool-1:before {
  content: "\f12a"; }

.flaticon-security:before {
  content: "\f12b"; }

.flaticon-interface-8:before {
  content: "\f12c"; }

.flaticon-interface-7:before {
  content: "\f12d"; }

.flaticon-interface-6:before {
  content: "\f12e"; }

.flaticon-placeholder-1:before {
  content: "\f12f"; }

.flaticon-placeholder:before {
  content: "\f130"; }

.flaticon-web:before {
  content: "\f131"; }

.flaticon-multimedia-1:before {
  content: "\f132"; }

.flaticon-tabs:before {
  content: "\f133"; }

.flaticon-signs-2:before {
  content: "\f134"; }

.flaticon-interface-5:before {
  content: "\f135"; }

.flaticon-network:before {
  content: "\f136"; }

.flaticon-share:before {
  content: "\f137"; }

.flaticon-info:before {
  content: "\f138"; }

.flaticon-exclamation-2:before {
  content: "\f139"; }

.flaticon-music:before {
  content: "\f13a"; }

.flaticon-medical:before {
  content: "\f13b"; }

.flaticon-imac:before {
  content: "\f13c"; }

.flaticon-profile:before {
  content: "\f13d"; }

.flaticon-time-1:before {
  content: "\f13e"; }

.flaticon-list-1:before {
  content: "\f13f"; }

.flaticon-multimedia:before {
  content: "\f140"; }

.flaticon-interface-4:before {
  content: "\f141"; }

.flaticon-file:before {
  content: "\f142"; }

.flaticon-background:before {
  content: "\f143"; }

.flaticon-chat-1:before {
  content: "\f144"; }

.flaticon-graph:before {
  content: "\f145"; }

.flaticon-pie-chart:before {
  content: "\f146"; }

.flaticon-bag:before {
  content: "\f147"; }

.flaticon-cart:before {
  content: "\f148"; }

.flaticon-warning-2:before {
  content: "\f149"; }

.flaticon-download:before {
  content: "\f14a"; }

.flaticon-edit-1:before {
  content: "\f14b"; }

.flaticon-visible:before {
  content: "\f14c"; }

.flaticon-line-graph:before {
  content: "\f14d"; }

.flaticon-browser:before {
  content: "\f14e"; }

.flaticon-statistics:before {
  content: "\f14f"; }

.flaticon-paper-plane:before {
  content: "\f150"; }

.flaticon-cogwheel-2:before {
  content: "\f151"; }

.flaticon-lifebuoy:before {
  content: "\f152"; }

.flaticon-settings:before {
  content: "\f153"; }

.flaticon-menu-button:before {
  content: "\f154"; }

.flaticon-user:before {
  content: "\f155"; }

.flaticon-apps:before {
  content: "\f156"; }

.flaticon-clock-1:before {
  content: "\f157"; }

.flaticon-close:before {
  content: "\f158"; }

.flaticon-pin:before {
  content: "\f159"; }

.flaticon-circle:before {
  content: "\f15a"; }

.flaticon-interface-3:before {
  content: "\f15b"; }

.flaticon-technology-1:before {
  content: "\f15c"; }

.flaticon-danger:before {
  content: "\f15d"; }

.flaticon-exclamation-square:before {
  content: "\f15e"; }

.flaticon-cancel:before {
  content: "\f15f"; }

.flaticon-calendar-2:before {
  content: "\f160"; }

.flaticon-warning-sign:before {
  content: "\f161"; }

.flaticon-more:before {
  content: "\f162"; }

.flaticon-exclamation-1:before {
  content: "\f163"; }

.flaticon-cogwheel-1:before {
  content: "\f164"; }

.flaticon-more-v2:before {
  content: "\f165"; }

.flaticon-up-arrow:before {
  content: "\f166"; }

.flaticon-computer:before {
  content: "\f167"; }

.flaticon-alert-1:before {
  content: "\f168"; }

.flaticon-alert-off:before {
  content: "\f169"; }

.flaticon-map:before {
  content: "\f16a"; }

.flaticon-interface-2:before {
  content: "\f16b"; }

.flaticon-graphic-2:before {
  content: "\f16c"; }

.flaticon-cogwheel:before {
  content: "\f16d"; }

.flaticon-alert:before {
  content: "\f16e"; }

.flaticon-folder:before {
  content: "\f16f"; }

.flaticon-interface-1:before {
  content: "\f170"; }

.flaticon-interface:before {
  content: "\f171"; }

.flaticon-calendar-1:before {
  content: "\f172"; }

.flaticon-time:before {
  content: "\f173"; }

.flaticon-signs-1:before {
  content: "\f174"; }

.flaticon-calendar:before {
  content: "\f175"; }

.flaticon-chat:before {
  content: "\f176"; }

.flaticon-infinity:before {
  content: "\f177"; }

.flaticon-list:before {
  content: "\f178"; }

.flaticon-bell:before {
  content: "\f179"; }

.flaticon-delete:before {
  content: "\f17a"; }

.flaticon-squares-4:before {
  content: "\f17b"; }

.flaticon-clipboard:before {
  content: "\f17c"; }

.flaticon-shapes:before {
  content: "\f17d"; }

.flaticon-comment:before {
  content: "\f17e"; }

.flaticon-squares-3:before {
  content: "\f17f"; }

.flaticon-mark:before {
  content: "\f180"; }

.flaticon-signs:before {
  content: "\f181"; }

.flaticon-squares-2:before {
  content: "\f182"; }

.flaticon-business:before {
  content: "\f183"; }

.flaticon-car:before {
  content: "\f184"; }

.flaticon-light:before {
  content: "\f185"; }

.flaticon-information:before {
  content: "\f186"; }

.flaticon-dashboard:before {
  content: "\f187"; }

.flaticon-edit:before {
  content: "\f188"; }

.flaticon-location:before {
  content: "\f189"; }

.flaticon-technology:before {
  content: "\f18a"; }

.flaticon-exclamation:before {
  content: "\f18b"; }

.flaticon-tea-cup:before {
  content: "\f18c"; }

.flaticon-notes:before {
  content: "\f18d"; }

.flaticon-analytics:before {
  content: "\f18e"; }

.flaticon-transport:before {
  content: "\f18f"; }

.flaticon-layers:before {
  content: "\f190"; }

.flaticon-book:before {
  content: "\f191"; }

.flaticon-squares-1:before {
  content: "\f192"; }

.flaticon-clock:before {
  content: "\f193"; }

.flaticon-graphic-1:before {
  content: "\f194"; }

.flaticon-symbol:before {
  content: "\f195"; }

.flaticon-graphic:before {
  content: "\f196"; }

.flaticon-tool:before {
  content: "\f197"; }

.flaticon-laptop:before {
  content: "\f198"; }

.flaticon-event-calendar-symbol:before {
  content: "\f199"; }

.flaticon-logout:before {
  content: "\f19a"; }

.flaticon-refresh:before {
  content: "\f19b"; }

.flaticon-questions-circular-button:before {
  content: "\f19c"; }

.flaticon-search-magnifier-interface-symbol:before {
  content: "\f19d"; }

.flaticon-search:before {
  content: "\f19e"; }

.flaticon-attachment:before {
  content: "\f19f"; }

.flaticon-speech-bubble-1:before {
  content: "\f1a0"; }

.flaticon-open-box:before {
  content: "\f1a1"; }

.flaticon-coins:before {
  content: "\f1a2"; }

.flaticon-speech-bubble:before {
  content: "\f1a3"; }

.flaticon-squares:before {
  content: "\f1a4"; }

.flaticon-diagram:before {
  content: "\f1a5"; }

/*
 * Icon Font Metronic
 * Made with love by Icons8 [ https://icons8.com/ ] using FontCustom [ https://github.com/FontCustom/fontcustom ]
 *
 * Contacts:
 *    [ https://icons8.com/contact ]
 *
 * Follow Icon8 on
 *    Twitter [ https://twitter.com/icons_8 ]
 *    Facebook [ https://www.facebook.com/Icons8 ]
 *    Google+ [ https://plus.google.com/+Icons8 ]
 *    GitHub [ https://github.com/icons8 ]
 */
@font-face {
  font-family: "Metronic";
  src: url("/design/fonts/metronic/Metronic_fda1334c35d0f5fe2afb3afebbb6774a.eot");
  src: url("/design/fonts/metronic/Metronic_fda1334c35d0f5fe2afb3afebbb6774a.eot?#iefix") format("embedded-opentype"), url("/design/fonts/metronic/Metronic_fda1334c35d0f5fe2afb3afebbb6774a.woff2") format("woff2"), url("/design/fonts/metronic/Metronic_fda1334c35d0f5fe2afb3afebbb6774a.woff") format("woff"), url("/design/fonts/metronic/Metronic_fda1334c35d0f5fe2afb3afebbb6774a.ttf") format("truetype"), url("/design/fonts/metronic/Metronic_fda1334c35d0f5fe2afb3afebbb6774a.svg#Metronic") format("svg");
  font-weight: normal;
  font-style: normal; }

@media screen and (-webkit-min-device-pixel-ratio: 0) {
  @font-face {
    font-family: "Metronic";
    src: url("/design/fonts/metronic/Metronic_fda1334c35d0f5fe2afb3afebbb6774a.svg#Metronic") format("svg"); } }

[data-icons8]:before {
  content: attr(data-icons8); }

.icons8, [data-icons8]:before,
.icons8-arrows-01:before,
.icons8-arrows-02:before,
.icons8-arrows-03:before,
.icons8-arrows-04:before {
  display: inline-block;
  font-family: "Metronic";
  font-style: normal;
  font-weight: normal;
  font-variant: normal;
  line-height: 1;
  text-decoration: inherit;
  text-rendering: optimizeLegibility;
  text-transform: none;
  -moz-osx-font-smoothing: grayscale;
  -webkit-font-smoothing: antialiased;
  font-smoothing: antialiased; }

.icons8-arrows-01:before {
  content: "\f1b1"; }

.icons8-arrows-02:before {
  content: "\f1b2"; }

.icons8-arrows-03:before {
  content: "\f1b3"; }

.icons8-arrows-04:before {
  content: "\f1b4"; }

      @-webkit-keyframes m-dropdown-fade-in {
        from {
          opacity: 0; }
        to {
          opacity: 1; } }

      @-moz-keyframes m-dropdown-fade-in {
        from {
          opacity: 0; }
        to {
          opacity: 1; } }

      @-o-keyframes m-dropdown-fade-in {
        from {
          opacity: 0; }
        to {
          opacity: 1; } }

      @keyframes m-dropdown-fade-in {
        from {
          opacity: 0; }
        to {
          opacity: 1; } }

      @-webkit-keyframes m-dropdown-move-up {
        from {
          margin-top: 10px; }
        to {
          margin-top: 0; } }

      @-moz-keyframes m-dropdown-move-up {
        from {
          margin-top: 10px; }
        to {
          margin-top: 0; } }

      @-o-keyframes m-dropdown-move-up {
        from {
          margin-top: 10px; }
        to {
          margin-top: 0; } }

      @keyframes m-dropdown-move-up {
        from {
          margin-top: 10px; }
        to {
          margin-top: 0; } }

      @-webkit-keyframes m-dropdown-arrow-move-up {
        from {
          margin-top: 10px; }
        to {
          margin-top: 0px; } }

      @-moz-keyframes m-dropdown-arrow-move-up {
        from {
          margin-top: 10px; }
        to {
          margin-top: 0px; } }

      @-o-keyframes m-dropdown-arrow-move-up {
        from {
          margin-top: 10px; }
        to {
          margin-top: 0px; } }

      @keyframes m-dropdown-arrow-move-up {
        from {
          margin-top: 10px; }
        to {
          margin-top: 0px; } }

      @-webkit-keyframes m-dropdown-move-down {
        from {
          margin-bottom: 10px; }
        to {
          margin-bottom: 0; } }

      @-moz-keyframes m-dropdown-move-down {
        from {
          margin-bottom: 10px; }
        to {
          margin-bottom: 0; } }

      @-o-keyframes m-dropdown-move-down {
        from {
          margin-bottom: 10px; }
        to {
          margin-bottom: 0; } }

      @keyframes m-dropdown-move-down {
        from {
          margin-bottom: 10px; }
        to {
          margin-bottom: 0; } }

      @-webkit-keyframes m-dropdown-arrow-move-down {
        from {
          margin-bottom: 10px; }
        to {
          margin-bottom: 0px; } }

      @-moz-keyframes m-dropdown-arrow-move-down {
        from {
          margin-bottom: 10px; }
        to {
          margin-bottom: 0px; } }

      @-o-keyframes m-dropdown-arrow-move-down {
        from {
          margin-bottom: 10px; }
        to {
          margin-bottom: 0px; } }

      @keyframes m-dropdown-arrow-move-down {
        from {
          margin-bottom: 10px; }
        to {
          margin-bottom: 0px; } }

.m-grid.m-grid--hor:not(.m-grid--desktop):not(.m-grid--desktop-and-tablet):not(.m-grid--tablet):not(.m-grid--tablet-and-mobile):not(.m-grid--mobile) > .m-grid__item {
    flex: none;
}
.m-page--wide .m-header, .m-page--fluid .m-header {
    background-color: #ffffff;
}
article, aside, dialog, figcaption, figure, footer, header, hgroup, main, nav, section {
    display: block;
}
*, *::before, *::after {
    box-sizing: border-box;
}
.m-container.m-container--full-height {
    position: relative;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100%;
}
.m-container.m-container--fluid {
    width: 100%;
}
.m-container {
    width: 100%;
    margin-right: auto;
    margin-left: auto;
}
.m-brand.m-brand--skin-dark {
    background: #282a3c;
}
.m-brand {
    -webkit-transition: none;
    -moz-transition: none;
    -ms-transition: none;
    -o-transition: none;
    transition: none;
    height: 70px;
    width: 255px;
    padding: 0px 30px;
}
.m-stack.m-stack--general {
    display: table;
    table-layout: fixed;
}
.m-stack {
    display: block;
    width: 100%;
    height: 100%;
}
.m-stack.m-stack--general.m-stack--ver > .m-stack__item.m-stack__item--middle {
    vertical-align: middle;
}
.m-stack.m-stack--general.m-stack--ver > .m-stack__item {
    display: table-cell;
    vertical-align: top;
    height: 100%;
}
.m-brand .m-brand__logo {
    vertical-align: middle;
    line-height: 0;
}
.m-brand .m-brand__logo .m-brand__logo-wrapper {
    display: inline-block;
}
a, area, button, [role="button"], input, label, select, summary, textarea {
    touch-action: manipulation;
}
a {
    color: #5867dd;
    text-decoration: none;
    background-color: transparent;
    -webkit-text-decoration-skip: objects;
}
img {
    vertical-align: middle;
    border-style: none;
}
.m-brand .m-brand__tools {
    line-height: 0;
    vertical-align: middle;
    text-align: right;
}
.m-brand .m-brand__tools .m-brand__icon {
    display: inline-block;
    line-height: 0;
    vertical-align: middle;
    cursor: pointer;
}
.m-brand .m-brand__tools .m-brand__toggler {
    display: inline-block;
    position: relative;
    overflow: hidden;
    margin: 0;
    padding: 0;
    width: 26px;
    height: 26px;
    font-size: 0;
    text-indent: -9999px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    box-shadow: none;
    border-radius: none;
    border: none;
    cursor: pointer;
    background: none;
    outline: none !important;
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -ms-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
}
.m-brand.m-brand--skin-dark .m-brand__tools .m-brand__toggler span {
    background: #5d5f77;
}
.m-brand .m-brand__tools .m-brand__toggler span {
    display: block;
    position: absolute;
    top: 13px;
    height: 1px;
    min-height: 1px;
    width: 100%;
    -webkit-border-radius: 0px;
    -moz-border-radius: 0px;
    -ms-border-radius: 0px;
    -o-border-radius: 0px;
    border-radius: 0px;
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -ms-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
}
.m--visible-desktop, .m--visible-desktop-inline-block, .m--visible-desktop-inline, .m--visible-desktop-table, .m--visible-desktop-table-cell, .m--visible-tablet, .m--visible-tablet-inline-block, .m--visible-tablet-inline, .m--visible-tablet-table, .m--visible-tablet-table-cell, .m--visible-tablet-and-mobile, .m--visible-tablet-and-mobile-inline-block, .m--visible-tablet-and-mobile-inline, .m--visible-tablet-and-mobile-table, .m--visible-tablet-and-mobile-table-cell, .m--visible-mobile, .m--visible-mobile-inline-block, .m--visible-mobile-inline, .m--visible-mobile-table, .m--visible-mobile-table-cell {
    display: none !important;
}
.m-brand.m-brand--skin-dark .m-brand__tools .m-brand__icon > i {
    color: #5d5f77;
}
.m-brand .m-brand__tools .m-brand__icon > i {
    font-size: 1.4rem;
}
.m-stack.m-stack--ver > .m-stack__item {
    display: block;
    height: auto;
}
.m-aside-header-menu-mobile-close {
    display: none;
}
button, html [type="button"], [type="reset"], [type="submit"] {
    -webkit-appearance: button;
}
button, select {
    text-transform: none;
}
button, input {
    overflow: visible;
}
input, button, select, optgroup, textarea {
    margin: 0;
    font-family: inherit;
    font-size: inherit;
    line-height: inherit;
}
a, area, button, [role="button"], input, label, select, summary, textarea {
    touch-action: manipulation;
}
.la {
    display: inline-block;
    font: normal normal normal 16px/1 "LineAwesome";
    font-size: inherit;
    text-decoration: inherit;
    text-rendering: optimizeLegibility;
    text-transform: none;
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    font-smoothing: antialiased;
}
[class^="la-"], [class*=" la-"] {
    font-size: 1.3rem;
}
.m-topbar {
    width: auto;
    height: 100%;
    float: right;
    padding: 0;
    -webkit-transition: all 0.6s ease;
    -moz-transition: all 0.6s ease;
    -ms-transition: all 0.6s ease;
    -o-transition: all 0.6s ease;
    transition: all 0.6s ease;
}
.m-topbar .m-topbar__nav.m-nav {
    margin: 0 20px 0 30px;
}
.m-nav.m-nav--inline {
    display: inline-block;
    width: auto;
    height: 100%;
}
.m-nav {
    padding: 0;
    margin: 0;
    list-style: none;
}
ol, ul, dl {
    margin-top: 0;
    margin-bottom: 1rem;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item {
    padding: 0 12px;
}
.m-nav.m-nav--inline > .m-nav__item:first-child {
    padding-left: 0;
}
.m-nav.m-nav--inline > .m-nav__item {
    height: 100%;
    display: inline-block;
    vertical-align: middle;
    padding: 0 0 0 25px;
}
.m-nav > .m-nav__item {
    display: block;
}
.m-dropdown {
    position: relative;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link {
    position: relative;
    margin: 0 auto;
}
.m-nav.m-nav--inline > .m-nav__item > .m-nav__link {
    width: auto;
    padding: 9px 0;
}
.m-nav > .m-nav__item > .m-nav__link {
    display: table;
    table-layout: fixed;
    width: 100%;
    height: 100%;
    text-decoration: none;
    position: relative;
    outline: none !important;
    vertical-align: middle;
    padding: 9px 0;
}
.m-dropdown.m-dropdown--align-center.m-dropdown--large .m-dropdown__wrapper {
    width: 380px;
    margin-left: -190px;
}
.m-dropdown.m-dropdown--arrow .m-dropdown__wrapper {
    padding-top: 10px;
}
.m-dropdown.m-dropdown--align-center .m-dropdown__wrapper {
    left: 50%;
    width: 245px;
    margin-left: -122.5px;
}
.m-dropdown.m-dropdown--large .m-dropdown__wrapper {
    width: 380px;
}
.m-dropdown .m-dropdown__wrapper {
    top: 100%;
    text-align: left;
    display: none;
    position: absolute;
    z-index: 1100;
    padding-top: 0;
    width: 245px;
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    -ms-border-radius: 6px;
    -o-border-radius: 6px;
    border-radius: 6px;
    -webkit-transform: translateZ(0);
    -moz-transform: translateZ(0);
    -ms-transform: translateZ(0);
    -o-transform: translateZ(0);
    transform: translateZ(0);
    -webkit-transform-style: preserve-3d;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-nav__link-icon {
    text-align: center;
    line-height: 0;
    vertical-align: middle;
    padding: 0;
    color: #ad5beb;
}
.m-nav.m-nav--inline > .m-nav__item > .m-nav__link .m-nav__link-icon {
    padding-right: 10px;
}
.m-nav > .m-nav__item > .m-nav__link .m-nav__link-icon {
    color: #c1bfd0;
}
.m-nav > .m-nav__item > .m-nav__link .m-nav__link-icon {
    display: table-cell;
    height: 100%;
    vertical-align: middle;
    text-align: left;
    width: 35px;
    font-size: 1.4rem;
    line-height: 0;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-nav__link-icon > i {
    font-size: 1.5rem;
}
[class^="flaticon-"], [class*=" flaticon-"] {
    font-size: 1.3rem;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-nav__link-icon i:before {
    font-weight: bold;
    padding: 1px;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-nav__link-icon > i:before {
    background: -webkit-linear-gradient(180deg, #5bafeb 25%, #78a4db 50%, #8dbcf7 75%, #6e99e7 100%);
    background: linear-gradient(180deg, #5bafeb 25%, #78a4db 50%, #8dbcf7 75%, #6e99e7 100%);
    background-clip: text;
    text-fill-color: transparent;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.flaticon-search-1:before {
    content: "\f127";
}
[class^="flaticon-"]:before, [class*=" flaticon-"]:before {
    font-family: Flaticon;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    line-height: 1;
    text-decoration: inherit;
    text-rendering: optimizeLegibility;
    text-transform: none;
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    font-smoothing: antialiased;
}
.m-dropdown.m-dropdown--arrow .m-dropdown__arrow {
    color: #ffffff;
}
.m-dropdown.m-dropdown--arrow.m-dropdown--up .m-dropdown__arrow, .m-dropdown.m-dropdown--arrow .m-dropdown__arrow {
    position: absolute;
    line-height: 0;
    display: inline-block;
    overflow: hidden;
    height: 11px;
    width: 40px;
    position: relative;
    left: 50%;
    margin-left: -20px;
    top: 0;
    position: absolute;
}
.m-dropdown .m-dropdown__wrapper .m-dropdown__inner {
    background-color: #ffffff;
    box-shadow: 0px 0px 15px 1px rgba(113, 106, 202, 0.2);
}
.m-dropdown .m-dropdown__wrapper .m-dropdown__header {
    padding: 20px 20px;
    -webkit-border-radius: 6px 6px 0 0;
    -moz-border-radius: 6px 6px 0 0;
    -ms-border-radius: 6px 6px 0 0;
    -o-border-radius: 6px 6px 0 0;
    border-radius: 6px 6px 0 0;
}
.m-dropdown .m-dropdown__header {
    -webkit-box-shadow: 1px 34px 52px -19px rgba(68, 62, 84, 0.03);
    -moz-box-shadow: 1px 34px 52px -19px rgba(68, 62, 84, 0.03);
    box-shadow: 1px 34px 52px -19px rgba(68, 62, 84, 0.03);
}
.m-list-search .m-list-search__form {
    position: relative;
    display: block;
    padding: 3px 0 0 0;
}
.m-list-search.m-list-search--has-result .m-dropdown__body {
  display: block; }
.m-list-search .m-list-search__form .m-list-search__form-wrapper {
    width: 100%;
    height: 100%;
    display: table;
    table-layout: fixed;
}
.m-list-search .m-list-search__form .m-list-search__form-wrapper .m-list-search__form-input-wrapper {
    display: table-cell;
    vertical-align: middle;
}
.m-list-search .m-list-search__form .m-list-search__form-wrapper .m-list-search__form-input {
    border: 0;
    background: none;
    outline: none !important;
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
    width: 100%;
    font-size: 1rem;
    padding: 0px;
    display: block;
}
.m-list-search .m-list-search__form .m-list-search__form-input {
    color: #575962;
}
.m-list-search .m-list-search__form .m-list-search__form-wrapper .m-list-search__form-icon-close {
    text-align: right;
    display: table-cell;
    vertical-align: middle;
    line-height: 0 !important;
    cursor: pointer;
    font-size: 1.2rem;
    width: 30px;
    padding: 0 0 0 0;
}
.m-dropdown .m-dropdown__wrapper .m-dropdown__body {
    padding: 20px;
}
.m-list-search .m-dropdown__body {
    display: none;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-nav__link-badge {
    left: 50%;
    margin-left: -2px;
    position: absolute;
    top: 11px;
}
.m-badge.m-badge--danger {
    background-color: #f4516c;
    color: #ffffff;
}
.m-badge.m-badge--dot-small {
    padding: 0;
    line-height: 4px;
    min-height: 4px;
    min-width: 4px;
    height: 4px;
    width: 4px;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    -ms-border-radius: 100%;
    -o-border-radius: 100%;
    border-radius: 100%;
}
.m-badge.m-badge--dot {
    padding: 0;
    line-height: 6px;
    min-height: 6px;
    min-width: 6px;
    height: 6px;
    width: 6px;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    -ms-border-radius: 100%;
    -o-border-radius: 100%;
    border-radius: 100%;
}
.m-badge {
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    -ms-border-radius: 10px;
    -o-border-radius: 10px;
    border-radius: 10px;
    background: #eaeaea;
    color: #444;
    font-size: 0.8rem;
    line-height: 20px;
    min-height: 20px;
    min-width: 20px;
    vertical-align: middle;
    text-align: center;
    display: inline-block;
    padding: 0px 2px;
}
.m-animate-blink {
    -webkit-animation: m-animate-blink 1s step-start 0s infinite;
    -moz-animation: m-animate-blink 1s step-start 0s infinite;
    -ms-animation: m-animate-blink 1s step-start 0s infinite;
    -o-animation: m-animate-blink 1s step-start 0s infinite;
    animation: m-animate-blink 1s step-start 0s infinite;
    animation-fill-mode: initial;
}
.flaticon-music-2:before {
    content: "\f129";
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-topbar__userpic {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    margin: 0 auto;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-topbar__userpic img {
    display: block;
    vertical-align: middle;
    max-width: 41px !important;
    margin: 0 0 0 5px;
}
.m--img-centered {
    text-align: center;
}
.m--marginless {
    margin: 0 !important;
}
.m--img-rounded {
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    -ms-border-radius: 50%;
    -o-border-radius: 50%;
    border-radius: 50%;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-topbar__username {
    display: table-cell;
    vertical-align: middle;
    text-transform: uppercase;
    font-size: 0.9rem;
    font-weight: 400;
    text-align: left;
    color: #fff;
}
.m--hide {
    display: none !important;
}
.m-grid.m-grid--hor:not(.m-grid--desktop):not(.m-grid--desktop-and-tablet):not(.m-grid--tablet):not(.m-grid--tablet-and-mobile):not(.m-grid--mobile).m-grid--root {
    flex: 1;
}
.m-grid.m-grid--hor:not(.m-grid--desktop):not(.m-grid--desktop-and-tablet):not(.m-grid--tablet):not(.m-grid--tablet-and-mobile):not(.m-grid--mobile) {
    display: flex;
    flex-direction: column;
}
.m-brand.m-brand--skin-dark .m-brand__tools .m-brand__toggler span::before, .m-brand.m-brand--skin-dark .m-brand__tools .m-brand__toggler span::after {
    background: #5d5f77;
}
.m-brand .m-brand__tools .m-brand__toggler.m-brand__toggler--left span:before {
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -ms-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
    left: auto;
    right: 0px;
    width: 50%;
}
.m-brand .m-brand__tools .m-brand__toggler span::before {
    top: -7px;
}
.m-brand .m-brand__tools .m-brand__toggler span::before, .m-brand .m-brand__tools .m-brand__toggler span::after {
    position: absolute;
    display: block;
    left: 0;
    width: 100%;
    height: 1px;
    min-height: 1px;
    content: "";
    -webkit-border-radius: 0px;
    -moz-border-radius: 0px;
    -ms-border-radius: 0px;
    -o-border-radius: 0px;
    border-radius: 0px;
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -ms-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
}
.m-brand.m-brand--skin-dark .m-brand__tools .m-brand__toggler span::before, .m-brand.m-brand--skin-dark .m-brand__tools .m-brand__toggler span::after {
    background: #5d5f77;
}
.m-brand .m-brand__tools .m-brand__toggler.m-brand__toggler--left span:after {
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -ms-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
    left: auto;
    right: 0px;
    width: 75%;
}
.m-brand .m-brand__tools .m-brand__toggler span::after {
    bottom: -7px;
}
.m-brand .m-brand__tools .m-brand__toggler span::before, .m-brand .m-brand__tools .m-brand__toggler span::after {
    position: absolute;
    display: block;
    left: 0;
    width: 100%;
    height: 1px;
    min-height: 1px;
    content: "";
    -webkit-border-radius: 0px;
    -moz-border-radius: 0px;
    -ms-border-radius: 0px;
    -o-border-radius: 0px;
    border-radius: 0px;
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -ms-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
}
.m-container:before, .m-container:after {
    content: " ";
    display: table;
}
.m-container:after {
    clear: both;
}
.m-container:before, .m-container:after {
    content: " ";
    display: table;
}
.m-dropdown.m-dropdown--open .m-dropdown__wrapper, .m-dropdown.m-dropdown--hoverable:hover .m-dropdown__wrapper {
    display: block;
    -webkit-animation: m-dropdown-fade-in .3s ease 1, m-dropdown-move-up .3s ease-out 1;
    -moz-animation: m-dropdown-fade-in .3s ease 1, m-dropdown-move-up .3s ease-out 1;
    -ms-animation: m-dropdown-fade-in .3s ease 1, m-dropdown-move-up .3s ease-out 1;
    -o-animation: m-dropdown-fade-in .3s ease 1, m-dropdown-move-up .3s ease-out 1;
    animation: m-dropdown-fade-in .3s ease 1, m-dropdown-move-up .3s ease-out 1;
}
.m-dropdown.m-dropdown--arrow.m-dropdown--up .m-dropdown__arrow:before, .m-dropdown.m-dropdown--arrow .m-dropdown__arrow:before {
    position: relative;
    top: 100%;
    margin-top: 11px;
    font-size: 40px;
}
.m-dropdown.m-dropdown--arrow.m-dropdown--up .m-dropdown__arrow:before, .m-dropdown.m-dropdown--arrow .m-dropdown__arrow:before {
    display: inline-block;
    font-family: "Metronic";
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    line-height: 0px;
    text-decoration: inherit;
    text-rendering: optimizeLegibility;
    text-transform: none;
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    font-smoothing: antialiased;
    content: "";
}
.m-dropdown .m-dropdown__wrapper .m-dropdown__inner {
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    -ms-border-radius: 6px;
    -o-border-radius: 6px;
    border-radius: 6px;
}
.m-list-search .m-list-search__form .m-list-search__form-icon-close {
    color: #cfcedb;
}
.mCustomScrollbar.mCS_no_scrollbar, .mCustomScrollbar.mCS_touch_action {
    -ms-touch-action: auto;
    touch-action: auto;
}
.mCustomScrollbar {
    -ms-touch-action: pinch-zoom;
    touch-action: pinch-zoom;
}

.mCustomScrollBox {
    position: relative;
    overflow: hidden;
    height: 100%;
    max-width: 100%;
    outline: none;
    direction: ltr;
}
.m-dropdown .m-dropdown__wrapper .m-dropdown__inner .mCSB_container, .m-dropdown .m-dropdown__wrapper .m-dropdown__inner .mCustomScrollBox, .m-dropdown .m-dropdown__wrapper .m-dropdown__inner .m-dropdown__content, .m-dropdown .m-dropdown__wrapper .m-dropdown__inner .m-dropdown__scrollable {
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    -ms-border-radius: 6px;
    -o-border-radius: 6px;
    border-radius: 6px;
}
.mCSB_container.mCS_no_scrollbar_y.mCS_y_hidden {
    margin-right: 0;
}
.mCSB_container {
    overflow: hidden;
    width: auto;
    height: auto;
}
.m-scrollable .mCSB_outside + .mCS-minimal.mCSB_scrollTools_vertical, .m-scrollable .mCSB_outside + .mCS-minimal-dark.mCSB_scrollTools_vertical {
    right: -17px;
    margin: 5px 0;
}
.m-dropdown .m-dropdown__wrapper .m-dropdown__body .mCSB_scrollTools {
    right: -10px;
}
.mCSB_outside + .mCS-minimal.mCSB_scrollTools_vertical, .mCSB_outside + .mCS-minimal-dark.mCSB_scrollTools_vertical {
    right: 0;
    margin: 12px 0;
}
.mCS-autoHide > .mCustomScrollBox > .mCSB_scrollTools, .mCS-autoHide > .mCustomScrollBox ~ .mCSB_scrollTools {
    opacity: 0;
    filter: "alpha(opacity=0)";
    -ms-filter: "alpha(opacity=0)";
}
.mCSB_outside + .mCSB_scrollTools {
    right: -26px;
}
.mCSB_scrollTools {
    opacity: 0.75;
    filter: "alpha(opacity=75)";
    -ms-filter: "alpha(opacity=75)";
}
.mCSB_scrollTools, .mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar, .mCSB_scrollTools .mCSB_buttonUp, .mCSB_scrollTools .mCSB_buttonDown, .mCSB_scrollTools .mCSB_buttonLeft, .mCSB_scrollTools .mCSB_buttonRight {
    -webkit-transition: opacity .2s ease-in-out, background-color .2s ease-in-out;
    -moz-transition: opacity .2s ease-in-out, background-color .2s ease-in-out;
    -o-transition: opacity .2s ease-in-out, background-color .2s ease-in-out;
    transition: opacity .2s ease-in-out, background-color .2s ease-in-out;
}
.mCSB_scrollTools {
    position: absolute;
    width: 16px;
    height: auto;
    left: auto;
    top: 0;
    right: 0;
    bottom: 0;
}
.m-scrollable .mCS-minimal-dark.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar {
    background: #e2e5ec;
}
.m-scrollable .mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar {
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    -ms-border-radius: 2px;
    -o-border-radius: 2px;
    border-radius: 2px;
}
.m-animate-shake {
    -webkit-animation: m-animate-shake 0.1s ease-in 0.1s infinite alternate;
    -moz-animation: m-animate-shake 0.1s ease-in 0.1s infinite alternate;
    -ms-animation: m-animate-shake 0.1s ease-in 0.1s infinite alternate;
    -o-animation: m-animate-shake 0.1s ease-in 0.1s infinite alternate;
    animation: m-animate-shake 0.1s ease-in 0.1s infinite alternate;
    animation-fill-mode: initial;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item.m-topbar__notifications.m-topbar__notifications--img.m-dropdown--arrow .m-dropdown__arrow {
    color: #5591dd;
}
.m-dropdown.m-dropdown--header-bg-fill.m-dropdown--arrow .m-dropdown__arrow {
    color: transparent;
}
.m-dropdown.m-dropdown--header-bg-fill .m-dropdown__header {
    background-color: transparent;
}
.m--align-center {
    text-align: center;
}
.m-dropdown.m-dropdown--header-bg-fill .m-dropdown__header .m-dropdown__header-title {
    color: #fff;
}
.m-dropdown .m-dropdown__wrapper .m-dropdown__header .m-dropdown__header-title {
    display: block;
    padding: 0 0 5px 0;
    font-size: 1.5rem;
    font-weight: 400;
}
.m-dropdown.m-dropdown--header-bg-fill .m-dropdown__header .m-dropdown__header-subtitle {
    color: #fff;
}
.m-dropdown .m-dropdown__wrapper .m-dropdown__header .m-dropdown__header-subtitle {
    display: block;
    padding: 0px;
    font-size: 1rem;
}
.nav.nav-pills, .nav.nav-tabs {
    margin-bottom: 20px;
}
.m-tabs-line {
    border-bottom: 1px solid #ebedf2;
}
.m-tabs-line {
    margin: 0 0 25px 0;
}
.nav-tabs {
    border-bottom: 1px solid #ebedf2;
}
.nav {
    display: flex;
    flex-wrap: wrap;
    padding-left: 0;
    margin-bottom: 0;
    list-style: none;
}
ol ol, ul ul, ol ul, ul ol {
    margin-bottom: 0;
}
.nav.nav-pills .nav-item, .nav.nav-tabs .nav-item {
    margin-left: 5px;
}
.nav.nav-pills .nav-item:first-child, .nav.nav-tabs .nav-item:first-child {
    margin-left: 0;
}
.m-tabs-line .m-tabs__item {
    margin-right: 30px;
    margin-bottom: -1px;
}
.nav-tabs .nav-item {
    margin-bottom: -1px;
}
.m-tabs-line.m-tabs-line--brand.nav.nav-tabs .nav-link:hover, .m-tabs-line.m-tabs-line--brand.nav.nav-tabs .nav-link.active, .m-tabs-line.m-tabs-line--brand a.m-tabs__link:hover, .m-tabs-line.m-tabs-line--brand a.m-tabs__link.active {
    color: #63b3e4;
    border-bottom: 1px solid #63b3e4;
}
.m-tabs-line.nav.nav-tabs .nav-link:hover, .m-tabs-line.nav.nav-tabs .nav-link.active, .m-tabs-line a.m-tabs__link:hover, .m-tabs-line a.m-tabs__link.active {
    background: transparent;
    color: #3f4047;
    border-bottom: 1px solid #3f4047;
}
.m-tabs-line.nav.nav-tabs .nav-link, .m-tabs-line a.m-tabs__link {
    background: transparent;
    color: #7b7e8a;
}
.nav.nav-pills .nav-link, .nav.nav-tabs .nav-link {
    color: #6f727d;
}
.nav.nav-pills .nav-link, .nav.nav-tabs .nav-link {
    font-weight: 400;
}
.nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
    color: #495057;
    background-color: #fff!important;
}
.nav-tabs>li>a:hover {
  border: 1px solid transparent;
  border-top-left-radius: 0.25rem;
  border-top-right-radius: 0.25rem;
}
.nav-tabs>li>a:focus {
  outline: none!important;
}
.m-tabs-line .m-tabs__link {
    border: 0;
    border-bottom: 1px solid transparent;
    font-size: 1.1rem;
    font-weight: 400;
    padding: 12px 0;
}
.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}
.m-list-timeline__items:before {
    position: absolute;
    display: block;
    content: '';
    width: 1px;
    height: 100%;
    top: 0;
    bottom: 0;
    left: 3px;
}
.m-list-timeline .m-list-timeline__items:before {
    background-color: #ebedf2;
}
.m-list-timeline__items .m-list-timeline__item .m-list-timeline__badge:before {
    position: absolute;
    display: block;
    content: '';
    width: 7px;
    height: 7px;
    left: 0;
    top: 50%;
    margin-top: -3.5px;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    -ms-border-radius: 100%;
    -o-border-radius: 100%;
    border-radius: 100%;
}
.m-list-timeline .m-list-timeline__items .m-list-timeline__item .m-list-timeline__badge:before {
    background-color: #ebedf2;
}
.nav-link {
    display: block;
    padding: 0.5rem 1rem;
}
.m-tabs-line .m-tabs__item:last-child {
    margin-right: 0;
}
.tab-content > .tab-pane {
    display: none;
}
.tab-content > .active {
    display: block;
}
.m-list-timeline__items {
    position: relative;
    padding: 0;
    margin: 0;
}
.m-list-timeline__items .m-list-timeline__item:first-child {
    padding-top: 0;
    margin-top: 0;
}
.m-list-timeline__items .m-list-timeline__item {
    position: relative;
    display: table;
    table-layout: fixed;
    width: 100%;
    padding: 6px 0;
    margin: 5px 0;
}
.m-list-timeline__items .m-list-timeline__item .m-list-timeline__badge {
    text-align: left;
    vertical-align: middle;
    display: table-cell;
    position: relative;
    width: 20px;
}
.m-list-timeline .m-list-timeline__items .m-list-timeline__item .m-list-timeline__text {
    color: #575962;
}
.m-list-timeline__items .m-list-timeline__item .m-list-timeline__text {
    display: table-cell;
    text-align: left;
    vertical-align: middle;
    width: 100%;
    padding: 0 5px 0 0;
    font-size: 1rem;
}
.m-list-timeline .m-list-timeline__items .m-list-timeline__item .m-list-timeline__time {
    color: #7b7e8a;
}
.m-list-timeline__items .m-list-timeline__item .m-list-timeline__time {
    display: table-cell;
    text-align: right;
    vertical-align: middle;
    width: 80px;
    padding: 0 7px 0 0;
    font-size: 0.85rem;
}
.m-badge.m-badge--wide {
    letter-spacing: 0.6px;
    padding: 1px 10px;
}
.m-list-timeline .m-list-timeline__items .m-list-timeline__item.m-list-timeline__item--read {
    opacity: 0.5;
    filter: alpha(opacity=50);
}
.m-list-timeline__items .m-list-timeline__item:last-child {
    padding-bottom: 0;
    margin-bottom: 0;
}
.m-stack.m-stack--general.m-stack--ver > .m-stack__item.m-stack__item--center {
    text-align: center;
}
.m-dropdown .m-dropdown__wrapper .m-dropdown__body.m-dropdown__body--paddingless {
    padding: 0;
}
.m-nav-grid {
    padding: 0;
    margin: 0;
    display: table;
    table-layout: fixed;
    width: 100%;
}
.m-nav-grid > .m-nav-grid__row {
    display: table-row;
}
.m-nav-grid .m-nav-grid__row .m-nav-grid__item {
    border-right: 1px solid #f4f5f8;
    border-bottom: 1px solid #f4f5f8;
}
.m-nav-grid > .m-nav-grid__row > .m-nav-grid__item {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    padding: 50px 5px 50px 5px;
}
.m-nav-grid .m-nav-grid__row .m-nav-grid__item .m-nav-grid__icon {
    color: #c4c5d6;
}
.m-nav-grid > .m-nav-grid__row > .m-nav-grid__item .m-nav-grid__icon {
    text-align: center;
    font-size: 35px;
}
.m-nav-grid > .m-nav-grid__row > .m-nav-grid__item .m-nav-grid__text {
    display: block;
    line-height: 1;
    text-align: center;
    margin: 10px 0 0 0;
    font-size: 1rem;
    font-weight: 500;
}
.m-nav-grid .m-nav-grid__row .m-nav-grid__item .m-nav-grid__text {
    color: #63b3e4;
}
.m-nav-grid .m-nav-grid__row .m-nav-grid__item:last-child {
    border-right: 0;
}
.m-nav-grid .m-nav-grid__row:last-child .m-nav-grid__item {
    border-bottom: 0;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item.m-topbar__user-profile.m-topbar__user-profile--img.m-dropdown--arrow .m-dropdown__arrow {
    color: #487de1;
}
.m-dropdown.m-dropdown--medium .m-dropdown__wrapper {
    width: 325px;
}
.m-dropdown.m-dropdown--arrow.m-dropdown--up .m-dropdown__arrow.m-dropdown__arrow--right, .m-dropdown.m-dropdown--arrow .m-dropdown__arrow.m-dropdown__arrow--right {
    right: 15px;
    left: auto;
    margin-left: auto;
}
.m-card-user {
    padding: 5px 0;
    margin: 0;
    display: table;
    table-layout: fixed;
}
.m-card-user .m-card-user__pic {
    display: table-cell;
    text-align: right;
    padding: 0 5px 0 0;
    vertical-align: middle;
    width: 70px;
}
.m-card-user .m-card-user__pic img {
    max-width: 70px !important;
    margin: 0 !important;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    -ms-border-radius: 100%;
    -o-border-radius: 100%;
    border-radius: 100%;
}
.m-card-user .m-card-user__details {
    display: table-cell;
    width: 100%;
    text-align: left;
    vertical-align: middle;
    padding: 0 0 0 15px;
}
.m-card-user.m-card-user--skin-dark .m-card-user__details .m-card-user__name {
    color: #d9dae3;
}
.m-card-user .m-card-user__details .m-card-user__name {
    color: #1b1c1e;
}
.m-card-user .m-card-user__details .m-card-user__name {
    display: block;
    padding: 0 0 0 0;
    font-size: 1.3rem;
    font-weight: 400;
    line-height: 1;
}
.m-card-user.m-card-user--skin-dark .m-card-user__details .m-card-user__email {
    color: #a0a0a2;
}
.m-card-user .m-card-user__details .m-card-user__email {
    color: #7b7e8a;
}
.m-card-user .m-card-user__details .m-card-user__email {
    display: inline-block;
    padding: 6px 0 0 0;
    font-size: 1rem;
}
.m-link {
    color: #63b3e4;
}
.m-link {
    text-decoration: none;
    position: relative;
    display: inline-block;
}
.m-link:after {
    display: block;
    content: '';
    position: absolute;
    bottom: 0;
    top: 1rem;
    left: 0;
    width: 0%;
    -webkit-transition: width 0.3s ease;
    -moz-transition: width 0.3s ease;
    -ms-transition: width 0.3s ease;
    -o-transition: width 0.3s ease;
    transition: width 0.3s ease;
}
.m-nav > .m-nav__section {
    display: table;
    width: 100%;
    vertical-align: middle;
    margin: 20px 0 10px 0;
}
.m-nav > .m-nav__section .m-nav__section-text {
    color: #63b3e4;
}
.m-nav > .m-nav__section .m-nav__section-text {
    display: table-cell;
    margin: 0;
    vertical-align: middle;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
}
.m-nav > .m-nav__item > .m-nav__link .m-nav__link-title {
    display: table-cell;
    height: 100%;
    padding: 0;
    margin: 0;
    vertical-align: middle;
}
.m-nav > .m-nav__item > .m-nav__link .m-nav__link-title > .m-nav__link-wrap {
    display: table;
    height: 100%;
    margin: 0;
    width: 100%;
    padding: 0;
    vertical-align: middle;
}
.m-nav > .m-nav__item > .m-nav__link .m-nav__link-text {
    color: #6f727d;
    font-weight: 400;
}
.m-nav > .m-nav__item > .m-nav__link .m-nav__link-text {
    display: table-cell;
    height: 100%;
    width: 100%;
    margin: 0;
    padding: 0;
    vertical-align: middle;
    font-size: 1rem;
}
.m-nav > .m-nav__item > .m-nav__link .m-nav__link-title > .m-nav__link-wrap > .m-nav__link-badge {
    display: table-cell;
    height: 100%;
    vertical-align: middle;
    white-space: nowrap;
    padding: 0px 0px 0px 5px;
    text-align: right;
}
.m-nav > .m-nav__separator.m-nav__separator--fit {
    margin-left: -20px;
    margin-right: -20px;
}
.m-nav > .m-nav__separator {
    border-bottom: 1px solid #f4f5f8;
}
.m-nav > .m-nav__separator {
    height: 0;
    overflow: hidden;
    margin: 15px 0;
}
.m-badge.m-badge--success {
    background-color: #34bfa3;
    color: #ffffff;
}
.m-badge.m-badge--info {
    background-color: #36a3f7;
    color: #ffffff;
}
.m-dropdown.m-dropdown--align-right.m-dropdown--align-push .m-dropdown__wrapper {
    margin-right: -5px;
}
.m-dropdown.m-dropdown--align-right .m-dropdown__wrapper {
    right: 0;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item.m-topbar__quick-actions.m-topbar__quick-actions--img.m-dropdown--arrow .m-dropdown__arrow {
    color: #457abd;
}
.m-nav-grid .m-nav-grid__row .m-nav-grid__item:hover {
    background: #fafafb;
}
.btn.btn-default, .btn.btn-secondary {
    background: white;
    border-color: #ebedf2;
}
.btn.m-btn--bolder {
    font-weight: 500;
}
.btn.m-btn--label-brand {
    color: #63b3e4;
}
.btn.m-btn--custom {
    padding: 0.75rem 2rem;
    font-size: 12px;
    font-weight: 600;
}
.btn.m-btn--pill {
    -webkit-border-radius: 60px;
    -moz-border-radius: 60px;
    -ms-border-radius: 60px;
    -o-border-radius: 60px;
    border-radius: 60px;
}
.btn-secondary {
    color: #111;
    background-color: #ebedf2;
    border-color: #ebedf2;
}
.btn {
    display: inline-block;
    font-weight: normal;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: 0.65rem 1.25rem;
    font-size: 1rem;
    line-height: 1.25;
    border-radius: 0.25rem;
    transition: all 0.15s ease-in-out;
}
.m-dropdown__dropoff {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    right: 0;
    display: block;
    z-index: 1099;
}
.m-tabs-line:before, .m-tabs-line:after {
    content: " ";
    display: table;
}
.m-tabs-line:before, .m-tabs-line:after {
    content: " ";
    display: table;
}
.m-tabs-line:after {
    clear: both;
}
.nav-tabs>li {
    float: left;
    margin-bottom: -1px;
}
.nav>li, .nav>li>a {
    display: block;
    position: relative;
}
.m-list-search .m-list-search__results .m-list-search__result-message {
    color: #3f4047;
}
.m-list-search .m-list-search__results .m-list-search__result-category.m-list-search__result-category--first {
    margin-top: 0;
}
.m-list-search .m-list-search__results .m-list-search__result-category {
    color: #4fc2fb;
}
.m-list-search .m-list-search__results .m-list-search__result-category {
    display: block;
    margin: 30px 0 10px 0;
    font-weight: 600;
    font-size: 1rem;
    text-transform: uppercase;
}
.m-list-search .m-list-search__results .m-list-search__result-item {
    display: table;
    table-layout: fixed;
    width: 100%;
    padding: 5px 0;
    outline: none;
}
.m-list-search .m-list-search__results .m-list-search__result-item .m-list-search__result-item-icon {
    color: #cfcedb;
}
.m-list-search .m-list-search__results .m-list-search__result-item .m-list-search__result-item-icon {
    display: table-cell;
    height: 100%;
    vertical-align: middle;
    text-align: left;
    padding: 1px;
    width: 32px;
    font-size: 1.2rem;
}
.m-list-search .m-list-search__results .m-list-search__result-item .m-list-search__result-item-pic {
    width: 45px;
    display: table-cell;
    vertical-align: middle;
    text-align: left;
    margin: 0 auto;
}
.m-list-search .m-list-search__results .m-list-search__result-item .m-list-search__result-item-pic img {
    display: block;
    vertical-align: middle;
    max-width: 35px !important;
}
.m-list-search .m-list-search__results .m-list-search__result-item .m-list-search__result-item-text {
    color: #575962;
}
.m-list-search .m-list-search__results .m-list-search__result-item .m-list-search__result-item-text {
    display: table-cell;
    height: 100%;
    width: 100%;
    vertical-align: middle;
    font-size: 1rem;
}
.m-aside-menu .m-menu__nav {
    list-style: none;
    padding: 30px 0 30px 0;
}
.m-aside-menu .m-menu__nav > .m-menu__item {
    position: relative;
    margin: 0;
}
.m-aside-menu .m-menu__nav .m-menu__item {
    display: block;
    float: none;
    height: auto;
    padding: 0;
}
.m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__heading, .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link {
    height: 44px;
}
.m-aside-menu .m-menu__nav .m-menu__item > .m-menu__heading, .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__link {
    display: table;
    table-layout: fixed;
    width: 100%;
    margin: 0;
    text-decoration: none;
    position: relative;
    outline: none;
    padding: 0;
}
.m-aside-menu .m-menu__nav .m-menu__item > .m-menu__heading:hover, .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__link:hover {
    text-decoration: none;
    cursor: pointer;
}
.m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link {
    padding: 9px 30px;
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item:not(.m-menu__item--parent):hover {
    background-color: #292b3a;
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item.m-menu__item--active > .m-menu__heading .m-menu__link-icon, .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item.m-menu__item--active > .m-menu__link .m-menu__link-icon {
    color: #716aca;
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__link-icon, .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-icon {
    color: #525672;
}
.m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__link-icon, .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-icon {
    text-align: left;
    width: 35px;
    font-size: 1.4rem;
}
.m-aside-menu .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__link-icon, .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__link .m-menu__link-icon {
    display: table-cell;
    height: 100%;
    vertical-align: middle;
    line-height: 0;
}
.m-aside-menu .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__link-title, .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__link .m-menu__link-title {
    display: table-cell;
    height: 100%;
    padding: 0;
    vertical-align: middle;
}
.m-aside-menu .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__link-title > .m-menu__link-wrap, .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__link .m-menu__link-title > .m-menu__link-wrap {
    display: table;
    height: 100%;
    width: 100%;
    padding: 0;
    vertical-align: middle;
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item.m-menu__item--active > .m-menu__heading .m-menu__link-text, .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item.m-menu__item--active > .m-menu__link .m-menu__link-text {
    color: #716aca;
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-text {
    color: #868aa8;
}
.m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-text {
    font-weight: 400;
    font-size: 15px;
    text-transform: initial;
}
.m-aside-menu .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__link .m-menu__link-text {
    display: table-cell;
    height: 100%;
    width: 100%;
    padding: 0;
    vertical-align: middle;
}
.m-aside-menu .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__link-title > .m-menu__link-wrap > .m-menu__link-badge, .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__link .m-menu__link-title > .m-menu__link-wrap > .m-menu__link-badge {
    display: table-cell;
    height: 100%;
    vertical-align: middle;
    white-space: nowrap;
}
.m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__link-badge, .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-badge {
    padding: 0px 0px 0px 5px;
    text-align: right;
}
.m-grid.m-grid--hor:not(.m-grid--desktop):not(.m-grid--desktop-and-tablet):not(.m-grid--tablet):not(.m-grid--tablet-and-mobile):not(.m-grid--mobile) > .m-grid__item.m-grid__item--fluid {
    flex: 1 0 auto;
}
body {
  font-family: Poppins;
}
.m-aside-menu .m-menu__nav > .m-menu__section {
    margin: 20px 0 0 0;
    height: 40px;
}
.m-aside-menu .m-menu__nav .m-menu__section {
    display: table;
    width: 100%;
    vertical-align: middle;
    padding: 0 27px;
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__section .m-menu__section-text {
    color: #464b66;
}
.m-aside-menu .m-menu__nav > .m-menu__section .m-menu__section-text {
    font-size: 0.83rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.m-aside-menu .m-menu__nav .m-menu__section .m-menu__section-text {
    display: table-cell;
    margin: 0;
    padding: 0;
    vertical-align: middle;
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__section .m-menu__section-icon {
    color: #424554;
}
.m-aside-menu .m-menu__nav > .m-menu__section .m-menu__section-icon {
    font-size: 1.1rem;
}
.m-aside-menu .m-menu__nav .m-menu__section .m-menu__section-icon {
    display: none;
    text-align: center;
    vertical-align: middle;
}
.m-aside-menu .m-menu__nav .m-menu__inner, .m-aside-menu .m-menu__nav .m-menu__submenu {
    display: none;
    float: none;
    margin: 0;
    padding: 0;
}
.m-aside-menu .m-menu__nav .m-menu__subnav {
    padding: 0;
    width: 100%;
    margin: 0;
    list-style: none;
}
.m-aside-menu .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item {
    margin: 0;
}
.m-aside-menu .m-menu__nav .m-menu__item .m-menu__submenu .m-menu__item--parent {
    display: none;
}
.m-aside-menu .m-menu__nav .m-menu__item {
    display: block;
    float: none;
    height: auto;
    padding: 0;
}
.m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link {
    padding: 0 30px;
    padding-left: 50px;
}
.m-aside-menu .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__heading, .m-aside-menu .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__link {
    height: 40px;
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__link .m-menu__link-text {
    color: #686c89;
}
.m-aside-menu .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-menu .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__link .m-menu__link-text {
    font-weight: 400;
    font-size: 1rem;
    text-transform: initial;
}
.m-aside-menu .m-menu__nav:after {
    clear: both;
}
.m-aside-menu .m-menu__nav:before, .m-aside-menu .m-menu__nav:after {
    content: " ";
    display: table;
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__ver-arrow, .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__ver-arrow {
    color: #525672;
}
.m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__ver-arrow, .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__ver-arrow {
    text-align: right;
    width: 20px;
    font-size: 0.7rem;
}
.m-aside-menu .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__ver-arrow, .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__link .m-menu__ver-arrow {
    display: table-cell;
    vertical-align: middle;
    line-height: 0;
    height: 100%;
}
.m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__ver-arrow:before, .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__ver-arrow:before {
    display: inline-block;
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
}
.m-aside-menu .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__ver-arrow:before, .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__link .m-menu__ver-arrow:before {
    -webkit-transform: translate3d(0, 0, 0);
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item.m-menu__item--open {
    background-color: #292b3a;
}
.m-aside-menu .m-menu__nav .m-menu__item > .m-menu__heading:hover, .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__link:hover {
    text-decoration: none;
    cursor: pointer;
}
.m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item:not(.m-menu__item--parent):hover > .m-menu__heading .m-menu__link-text, .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item:not(.m-menu__item--parent):hover > .m-menu__link .m-menu__link-text {
    color: #868aa8;
}

@media(max-width: 768px){
  .m-topbar .m-topbar__nav.m-nav > .m-nav__item.m-dropdown {
    position: static;
}
  .m-dropdown.m-dropdown--mobile-full-width.m-dropdown--align-center > .m-dropdown__wrapper, .m-dropdown.m-dropdown--mobile-full-width > .m-dropdown__wrapper {
      width: auto;
      margin: 0 auto;
      left: 30px;
      right: 30px;
  }


}



@media (min-width: 993px) {
  .m-aside-left--minimize .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item {
      background: transparent;
  }
  .m-aside-left--minimize .m-aside-menu .m-menu__nav {
    padding: 30px 0 30px 0;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-icon {
    width: 100%;
    text-align: center;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-title {
    padding: 0;
    position: relative;
    left: -50%;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-text {
    display: none;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-title .m-badge {
    text-indent: -9999px;
    position: relative;
    padding: 0;
    min-width: 6px;
    width: 6px;
    min-height: 6px;
    height: 6px;
    right: -15px;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__section .m-menu__section-icon {
    display: table-cell;
    vertical-align: top;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__section .m-menu__section-text {
    display: none;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__section {
    padding-left: 0;
    padding-right: 0;
    text-align: center;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__ver-arrow {
    display: none;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__submenu {
    display: none !important;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item > .m-menu__submenu, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item > .m-menu__submenu {
    display: none !important;
    -webkit-transform: translateZ(0);
    -moz-transform: translateZ(0);
    -ms-transform: translateZ(0);
    -o-transform: translateZ(0);
    transform: translateZ(0);
    -webkit-transform-style: preserve-3d;
}
.m-aside-left--minimize .m-aside-menu.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav, .m-aside-menu.m-aside-menu--dropdown.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav {
    background-color: #2c2e3e;
    -webkit-box-shadow: 0px 0px 15px 1px rgba(113, 106, 202, 0.4);
    -moz-box-shadow: 0px 0px 15px 1px rgba(113, 106, 202, 0.4);
    box-shadow: 0px 0px 15px 1px rgba(113, 106, 202, 0.4);
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav {
    padding: 20px 0;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    -ms-border-radius: 4px;
    -o-border-radius: 4px;
    border-radius: 4px;
}
.m-aside-left--minimize .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item:hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item--parent {
    display: block;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item--parent {
    display: block;
    margin: 0;
}

.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item--parent > .m-menu__link {
    height: 40px;
    padding: 0 30px 10px 30px;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__heading, .m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link,
.m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__heading, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link {
    height: 40px;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item .m-menu__link, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item .m-menu__link {
    text-align: left;
    padding: 7px 30px;
}
.m-aside-left--minimize .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item:hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item--parent.m-menu__item--active > .m-menu__link .m-menu__link-text,
.m-aside-left--minimize .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item:hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item--parent:hover > .m-menu__link .m-menu__link-text, .m-aside-left--minimize .m-aside-menu.m-aside-menu--skin-dark .m-menu__nav > .m-menu__item:hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item--parent > .m-menu__link .m-menu__link-text {
    color: #7b7f9e;
}
.m-aside-left--minimize .m-aside-menu.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item > .m-menu__heading .m-menu__link-text,
.m-aside-left--minimize .m-aside-menu.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item > .m-menu__link .m-menu__link-text,
.m-aside-menu.m-aside-menu--dropdown.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-menu.m-aside-menu--dropdown.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item > .m-menu__link .m-menu__link-text {
    color: #717594;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item--parent > .m-menu__link .m-menu__link-text {
    font-weight: 400;
    font-size: 1.05rem;
    text-transform: initial;
    cursor: text !important;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link .m-menu__link-text, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link .m-menu__link-text {
    font-weight: 400;
    font-size: 1rem;
    text-transform: initial;
}
.m-aside-left--minimize .m-aside-menu.m-aside-menu--submenu-skin-dark .m-menu__nav.m-menu__nav--dropdown-submenu-arrow .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__arrow, .m-aside-menu.m-aside-menu--dropdown.m-aside-menu--submenu-skin-dark .m-menu__nav.m-menu__nav--dropdown-submenu-arrow .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__arrow {
    color: #2c2e3e;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav.m-menu__nav--dropdown-submenu-arrow .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__arrow, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav.m-menu__nav--dropdown-submenu-arrow .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__arrow {
    position: absolute;
    line-height: 0;
    display: inline-block;
    overflow: hidden;
    width: 10px;
    height: 40px;
    position: relative;
    left: 0;
    margin-left: -9px;
    left: 1px;
    top: 12px;
    position: absolute;
    margin: 0;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav.m-menu__nav--dropdown-submenu-arrow .m-menu__item.m-menu__item--hover > .m-menu__submenu, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav.m-menu__nav--dropdown-submenu-arrow .m-menu__item.m-menu__item--hover > .m-menu__submenu {
    top: -10px;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item.m-menu__item--hover > .m-menu__submenu, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav > .m-menu__item.m-menu__item--hover > .m-menu__submenu {
    margin-left: 255px;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu {
    top: 0;
    position: absolute;
    display: block !important;
    width: 245px;
    margin-left: 245px;
    -webkit-animation: m-aside-menu-submenu-fade-in .3s ease 1, m-aside-menu-submenu-move-up .3s ease-out 1;
    -moz-animation: m-aside-menu-submenu-fade-in .3s ease 1, m-aside-menu-submenu-move-up .3s ease-out 1;
    -ms-animation: m-aside-menu-submenu-fade-in .3s ease 1, m-aside-menu-submenu-move-up .3s ease-out 1;
    -o-animation: m-aside-menu-submenu-fade-in .3s ease 1, m-aside-menu-submenu-move-up .3s ease-out 1;
    animation: m-aside-menu-submenu-fade-in .3s ease 1, m-aside-menu-submenu-move-up .3s ease-out 1;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item.m-menu__item--hover > .m-menu__submenu {
    top: 0;
    bottom: auto;
    display: block !important;
    margin-left: 81px;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__heading, .m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__heading, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link {
    height: 40px;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__heading .m-menu__link-bullet, .m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link .m-menu__link-bullet, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__heading .m-menu__link-bullet, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link .m-menu__link-bullet {
    vertical-align: middle;
    text-align: left;
    width: 20px;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item .m-menu__link .m-menu__link-bullet, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item .m-menu__link .m-menu__link-bullet {
    display: table-cell;
}
.m-aside-left--minimize .m-aside-menu.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-left--minimize .m-aside-menu.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item > .m-menu__link .m-menu__link-text, .m-aside-menu.m-aside-menu--dropdown.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-menu.m-aside-menu--dropdown.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item > .m-menu__link .m-menu__link-text {
    color: #717594;
}
.m-aside-left--minimize .m-aside-menu.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item.m-menu__item--active > .m-menu__heading .m-menu__link-text, .m-aside-left--minimize .m-aside-menu.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item.m-menu__item--active > .m-menu__link .m-menu__link-text, .m-aside-menu.m-aside-menu--dropdown.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item.m-menu__item--active > .m-menu__heading .m-menu__link-text, .m-aside-menu.m-aside-menu--dropdown.m-aside-menu--submenu-skin-dark .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav .m-menu__item.m-menu__item--active > .m-menu__link .m-menu__link-text {
    color: #716aca;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item--parent + .m-menu__item {
    margin-top: 15px;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav > .m-menu__item {
    margin: 0;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav.m-menu__nav--dropdown-submenu-arrow .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav, .m-aside-menu.m-aside-menu--dropdown .m-menu__nav.m-menu__nav--dropdown-submenu-arrow .m-menu__item.m-menu__item--hover > .m-menu__submenu > .m-menu__subnav {
    margin-left: 9px;
}
.m-aside-left--minimize .m-aside-menu .m-menu__nav > .m-menu__item > .m-menu__link {
    padding-left: 0;
    padding-right: 0;
    text-align: center;
}
.m-aside-left--minimize .m-aside-left {
    width: 80px;
    -webkit-transition: none;
    -moz-transition: none;
    -ms-transition: none;
    -o-transition: none;
    transition: none;
}
  .m-aside-left--fixed .m-aside-left {
  top: 0;
  bottom: 0;
  position: fixed;
  height: auto !important;
  left: 0;
  z-index: 100;
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden; }
  .m-grid.m-grid--ver-desktop.m-grid--desktop {
    display: flex;
    flex-direction: row;
}
.m-header--fixed .m-body {
    padding-top: 70px !important;
}
  .m-grid.m-grid--ver-desktop.m-grid--desktop > .m-grid__item {
    flex: 0 0 auto;
}
.m-aside-left.m-aside-left--skin-dark {
    background-color: #2c2e3e;
}
.m-aside-left {
    -webkit-transition: none;
    -moz-transition: none;
    -ms-transition: none;
    -o-transition: none;
    transition: none;
    width: 255px;
}
.m-header--fixed .m-header {
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    z-index: 101;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
}
.m-header {
    height: 70px;
}
.m--visible-desktop-inline-block {
    display: inline-block !important;
}
.m-stack.m-stack--desktop {
    display: table;
    table-layout: fixed;
}

.m-brand .m-brand__logo, .m-brand .m-brand__tools {
    -webkit-transition: none;
    -moz-transition: none;
    -ms-transition: none;
    -o-transition: none;
    transition: none;
    height: 70px !important;
}

.m-stack.m-stack--desktop.m-stack--ver > .m-stack__item.m-stack__item--fluid {
    width: 100%;
}

.m-stack.m-stack--desktop.m-stack--ver > .m-stack__item {
    display: table-cell;
    vertical-align: top;
    height: 100%;
}

div.m-aside-left--skin-dark .m-header .m-header-head {
    -webkit-box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1);
    -moz-box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1);
    box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1);
}

.m-header .m-header-head {
    -webkit-transition: none;
    -moz-transition: none;
    -ms-transition: none;
    -o-transition: none;
    transition: none;
}
.m-brand .m-brand__logo,
.m-brand .m-brand__tools {
  -webkit-transition: none;
  -moz-transition: none;
  -ms-transition: none;
  -o-transition: none;
  transition: none;
  height: 70px !important; }
.m-brand--minimize .m-brand {
  -webkit-transition: none;
  -moz-transition: none;
  -ms-transition: none;
  -o-transition: none;
  transition: none;
  width: 80px;
  padding-left: 0;
  padding-right: 0; }
  .m-brand--minimize .m-brand .m-brand__logo {
    display: none !important; }
  .m-brand--minimize .m-brand .m-brand__tools {
    text-align: center; }
.m-header--static.m-aside-left--fixed .m-brand {
  position: fixed;
  height: 70px !important;
  top: 0;
  bottom: auto; }
  .m-header--static.m-aside-left--fixed .m-brand .m-brand__tools,
  .m-header--static.m-aside-left--fixed .m-brand .m-brand__logo {
    height: 70px !important;
    -webkit-transition: none;
    -moz-transition: none;
    -ms-transition: none;
    -o-transition: none;
    transition: none; }
}
@media (min-width: 993px) {
  .m-header {
    height: 70px; }
    .m-header--fixed .m-header {
      -webkit-backface-visibility: hidden;
      backface-visibility: hidden;
      z-index: 101;
      position: fixed;
      top: 0;
      left: 0;
      right: 0; }
    .m-header .m-header-head {
      -webkit-transition: none;
      -moz-transition: none;
      -ms-transition: none;
      -o-transition: none;
      transition: none; }
      .m-header--static.m-aside-left--fixed .m-header .m-header-head {
        -webkit-transition: none;
        -moz-transition: none;
        -ms-transition: none;
        -o-transition: none;
        transition: none;
        padding-left: 255px; }
      .m-header--static.m-aside-left--fixed.m-aside-left--minimize .m-header .m-header-head {
        -webkit-transition: none;
        -moz-transition: none;
        -ms-transition: none;
        -o-transition: none;
        transition: none;
        padding-left: 80px; }
    .m-header--fixed.m-header--hide .m-header {
      height: 70px;
      -webkit-transition: all 0.3s ease 0.5s;
      -moz-transition: all 0.3s ease 0.5s;
      -ms-transition: all 0.3s ease 0.5s;
      -o-transition: all 0.3s ease 0.5s;
      transition: all 0.3s ease 0.5s;
      -webkit-transform: translateY(-100%);
      -moz-transform: translateY(-100%);
      -ms-transform: translateY(-100%);
      -o-transform: translateY(-100%);
      transform: translateY(-100%); }
    .m-header--fixed.m-header--show .m-header {
      height: 70px;
      -webkit-transition: all 0.3s ease;
      -moz-transition: all 0.3s ease;
      -ms-transition: all 0.3s ease;
      -o-transition: all 0.3s ease;
      transition: all 0.3s ease;
      -webkit-transform: translateY(0);
      -moz-transform: translateY(0);
      -ms-transform: translateY(0);
      -o-transform: translateY(0);
      transform: translateY(0); }
  body.m-aside-left--skin-dark .m-header .m-header-head {
    -webkit-box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1);
    -moz-box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1);
    box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1); }
  body.m-aside-left--skin-light .m-header {
    -webkit-box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1);
    -moz-box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1);
    box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1); } }

    .m-aside-left.m-aside-left--skin-dark {
        background-color: #2c2e3e;
    }
@media (max-width: 992px) {
  .m-aside-header-menu-mobile .m-menu__nav .m-menu__item {
  display: block;
  float: none;
  height: auto;
  padding: 0; }
  .m-aside-menu .m-menu__nav .m-menu__item {
  display: block;
  float: none;
  height: auto;
  padding: 0; }
  .m-aside-left.m-aside-left--on {
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
    left: 0;
}
.m-aside-left.m-aside-left--skin-dark {
    background-color: #2c2e3e;
}
.m-header--fixed-mobile .m-body {
    -webkit-transition: padding-top;
    -moz-transition: padding-top;
    -ms-transition: padding-top;
    -o-transition: padding-top;
    transition: padding-top;
    padding-top: 60px;
}
.m-aside-left {
    display: block !important;
    z-index: 1001;
    position: fixed;
    top: 0;
    bottom: 0;
    overflow-y: auto;
    -webkit-transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    width: 255px !important;
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
    left: -265px;
}
.m-aside-left {
    padding-top: 10px;
}
  .m-topbar--on .m-topbar {
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
    margin-top: 0!important;
    top: 0!important; }
    .m-header--fixed-mobile.m-topbar--on .m-topbar {
      margin-top: 0;
      top: 0;
      -webkit-transition: all 0.3s ease;
      -moz-transition: all 0.3s ease;
      -ms-transition: all 0.3s ease;
      -o-transition: all 0.3s ease;
      transition: all 0.3s ease; }
  .m-header {
    height: 60px !important; }
    .m-header .m-header__nav {
      top: -100%;
      position: relative; }
    .m-header > .m-container > .m-grid {
      height: 60px; }
      .m-header > .m-container > .m-grid > .m-grid__col {
        height: 60px; }
    .m-header--fixed-mobile .m-header {
      -webkit-backface-visibility: hidden;
      backface-visibility: hidden;
      z-index: 101;
      position: fixed;
      top: 0;
      left: 0;
      right: 0; }
    .m-header--fixed-mobile.m-header--hide .m-header {
      height: 60px;
      -webkit-transition: all 0.3s ease 0.5s;
      -moz-transition: all 0.3s ease 0.5s;
      -ms-transition: all 0.3s ease 0.5s;
      -o-transition: all 0.3s ease 0.5s;
      transition: all 0.3s ease 0.5s;
      -webkit-transform: translateY(-100%);
      -moz-transform: translateY(-100%);
      -ms-transform: translateY(-100%);
      -o-transform: translateY(-100%);
      transform: translateY(-100%); }
    .m-header--fixed-mobile.m-header--show .m-header {
      height: 60px;
      -webkit-transition: all 0.3s ease;
      -moz-transition: all 0.3s ease;
      -ms-transition: all 0.3s ease;
      -o-transition: all 0.3s ease;
      transition: all 0.3s ease;
      -webkit-transform: translateY(0);
      -moz-transform: translateY(0);
      -ms-transform: translateY(0);
      -o-transform: translateY(0);
      transform: translateY(0); }
  .m-header--fixed-mobile .m-header .m-header-head {
    -webkit-box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1);
    -moz-box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1);
    box-shadow: 0px 1px 15px 1px rgba(113, 106, 202, 0.1); }
    .m-brand {
        width: 100%;
        position: relative;
        height: 60px !important;
        padding: 0px 25px;
        z-index: 2;
    }
    .m-brand .m-brand__tools .m-brand__icon {
        margin-left: 15px;
    }
    .m--visible-tablet-and-mobile-inline-block {
        display: inline-block !important;
    }
    .m-aside-header-menu-mobile-close.m-aside-header-menu-mobile-close--skin-dark {
      background-color: #323446;
  }
  .m-aside-header-menu-mobile-close {
    right: -25px;
    width: 25px;
    height: 25px;
    top: 1px;
    z-index: 1002;
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
    position: fixed;
    border: 0;
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    -ms-border-radius: 3px;
    -o-border-radius: 3px;
    border-radius: 3px;
    cursor: pointer;
    outline: none !important;
    text-align: center;
    vertical-align: center;
    display: inline-block;
}
.m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark {
    background-color: #2c2e3e;
}
.m-header-menu {
    display: none;
}
.m-aside-header-menu-mobile {
    display: block !important;
    z-index: 1001;
    position: fixed;
    top: 0;
    bottom: 0;
    overflow-y: auto;
    -webkit-transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    width: 255px !important;
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
    right: -265px;
}
.m-aside-header-menu-mobile {
    width: 255px;
    z-index: 103;
}
.m-aside-header-menu-mobile .m-menu__nav {
    list-style: none;
    padding: 30px 0 30px 0;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item {
    position: relative;
    margin: 0;
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__item {
    display: block;
    float: none;
    height: auto;
    padding: 0;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__link {
    padding: 9px 30px;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__heading, .m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__link {
    height: 44px;
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__heading, .m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__link {
    display: table;
    table-layout: fixed;
    width: 100%;
    margin: 0;
    text-decoration: none;
    position: relative;
    outline: none;
    padding: 0;
}
.m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__link-icon, .m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-icon {
    color: #525672;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__link-icon, .m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-icon {
    text-align: left;
    width: 35px;
    font-size: 1.4rem;
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__link-icon, .m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__link .m-menu__link-icon {
    display: table-cell;
    height: 100%;
    vertical-align: middle;
    line-height: 0;
}
.m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-text {
    color: #868aa8;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__link-text {
    font-weight: 400;
    font-size: 15px;
    text-transform: initial;
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__link .m-menu__link-text {
    display: table-cell;
    height: 100%;
    width: 100%;
    padding: 0;
    vertical-align: middle;
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__hor-arrow, .m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__link .m-menu__hor-arrow {
    display: none;
}
.m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__ver-arrow, .m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__ver-arrow {
    color: #525672;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__ver-arrow, .m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__ver-arrow {
    text-align: right;
    width: 20px;
    font-size: 0.7rem;
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__ver-arrow, .m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__link .m-menu__ver-arrow {
    display: table-cell;
    vertical-align: middle;
    line-height: 0;
    height: 100%;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__heading .m-menu__ver-arrow:before, .m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__link .m-menu__ver-arrow:before {
    display: inline-block;
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__ver-arrow:before, .m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__link .m-menu__ver-arrow:before {
    -webkit-transform: translate3d(0, 0, 0);
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__inner, .m-aside-header-menu-mobile .m-menu__nav .m-menu__submenu {
    display: none;
    float: none;
    margin: 0;
    padding: 0;
    width: auto !important;
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__subnav {
    padding: 0;
    width: 100%;
    margin: 0;
    list-style: none;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item {
    margin: 0;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link {
    padding: 0 30px;
    padding-left: 50px;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__heading, .m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__link {
    height: 40px;
}
.m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__heading .m-menu__link-icon, .m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__link .m-menu__link-icon {
    color: #525672;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__heading .m-menu__link-icon, .m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__link .m-menu__link-icon {
    text-align: left;
    width: 35px;
    font-size: 1.35rem;
}
.m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-header-menu-mobile.m-aside-header-menu-mobile--skin-dark .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__link .m-menu__link-text {
    color: #686c89;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__heading .m-menu__link-text, .m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__link .m-menu__link-text {
    font-weight: 400;
    font-size: 1rem;
    text-transform: initial;
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__link-title, .m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__link .m-menu__link-title {
    display: table-cell;
    height: 100%;
    padding: 0;
    vertical-align: middle;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__heading .m-menu__link-badge, .m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__link .m-menu__link-badge {
    padding: 0px 0px 0px 5px;
    text-align: right;
}
.m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__heading .m-menu__link-title > .m-menu__link-wrap > .m-menu__link-badge, .m-aside-header-menu-mobile .m-menu__nav .m-menu__item > .m-menu__link .m-menu__link-title > .m-menu__link-wrap > .m-menu__link-badge {
    display: table-cell;
    height: 100%;
    vertical-align: middle;
    white-space: nowrap;
}
.m-badge.m-badge--success {
    background-color: #34bfa3;
    color: #ffffff;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__submenu > .m-menu__subnav {
    padding: 0;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__submenu > .m-menu__subnav > .m-menu__item > .m-menu__link {
    padding: 0 30px;
    padding-left: 75px;
}
.m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__heading, .m-aside-header-menu-mobile .m-menu__nav > .m-menu__item .m-menu__submenu .m-menu__item > .m-menu__link {
    height: 40px;
}
.m-header--fixed-mobile .m-topbar {
    margin-top: 0;
    top: -50px;
}
.m-topbar {
    width: 100% !important;
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
    height: 50px !important;
    margin-top: -50px;
    position: relative;
    background-color: #fff;
    -webkit-box-shadow: 0px 3px 12px 0px rgba(0, 0, 0, 0.1);
    -moz-box-shadow: 0px 3px 12px 0px rgba(0, 0, 0, 0.1);
    box-shadow: 0px 3px 12px 0px rgba(0, 0, 0, 0.1);
}
.m-topbar .m-topbar__nav.m-nav {
    float: right;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item {
    padding: 0 4px;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-nav__link-badge {
    margin-left: -2px;
    top: 5px;
}
.m-topbar .m-topbar__nav.m-nav > .m-nav__item > .m-nav__link .m-topbar__userpic img {
    max-width: 31px !important;
}

  }

#content_outer_wrapper {
  padding-left: 0px;
  width:100%;
}
#content_outer_wrapper #content_wrapper {
  padding-top: 0px;
}
body {
  display: flex!important;
  flex-direction: column; }

  html,
body {
  height: 100%;
  margin: 0px;
  padding: 0px;
  font-size: 14px;
  font-weight: 300;
  font-family: Poppins;
  -ms-text-size-adjust: 100%;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale; }
  html a:active,
  html a:focus,
  body a:active,
  body a:focus {
    text-decoration: none !important; }


.modal .modal-header {
  background-color: #4fc5ea;
}
.modal .modal-content .modal-header .modal-title {
  color: #FFF;
}

</style>







</head>
<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">


  	<div class="m-grid m-grid--hor m-grid--root m-page">
      <header class="m-grid__item    m-header "  data-minimize-offset="200" data-minimize-mobile-offset="200" >
        <div class="m-container m-container--fluid m-container--full-height">
          <div class="m-stack m-stack--ver m-stack--desktop">
            <!-- BEGIN: Brand -->
            <div class="m-stack__item m-brand  m-brand--skin-dark ">
              <div class="m-stack m-stack--ver m-stack--general">
                <div class="m-stack__item m-stack__item--middle m-brand__logo">
                  <a href="index.html" class="m-brand__logo-wrapper">
                    <img alt="" style="width:150px;max-width:none;" src="https://altpocket.io/assets/logo_blue_text.png"/>
                  </a>
                </div>
                <div class="m-stack__item m-stack__item--middle m-brand__tools">
                  <!-- BEGIN: Left Aside Minimize Toggle -->
                  <a href="javascript:;" id="m_aside_left_minimize_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block">
                    <span></span>
                  </a>
                  <!-- END -->
              <!-- BEGIN: Responsive Aside Left Menu Toggler -->
                  <a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
                    <span></span>
                  </a>
                  <!-- END -->
              <!-- BEGIN: Responsive Header Menu Toggler -->
                  <a id="m_aside_header_menu_mobile_toggle" href="javascript:;" class="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block">
                    <span></span>
                  </a>
                  <!-- END -->
                  <!-- BEGIN: Topbar Toggler -->
                  <a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
                    <i class="flaticon-more"></i>
                  </a>
                  <!-- BEGIN: Topbar Toggler -->
                </div>
              </div>
            </div>
            <!-- END: Brand -->
            <div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
              <!-- BEGIN: Horizontal Menu -->
              <button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-dark " id="m_aside_header_menu_mobile_close_btn">
                <i class="la la-close"></i>
              </button>
              <div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general">
                <div class="m-stack__item m-topbar__nav-wrapper">
                  <ul class="m-topbar__nav m-nav m-nav--inline">
                    <li class="m-nav__item m-dropdown m-dropdown--large m-dropdown--arrow m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light	m-list-search m-list-search--skin-light" data-dropdown-toggle="click" data-dropdown-persistent="true" id="m_quicksearch" data-search-type="dropdown">
                      <a href="#" class="m-nav__link m-dropdown__toggle">
                        <span class="m-nav__link-icon">
                          <i class="flaticon-search-1"></i>
                        </span>
                      </a>
                      <div class="m-dropdown__wrapper">
                        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                        <div class="m-dropdown__inner ">
                          <div class="m-dropdown__header">
                            <form  class="m-list-search__form">
                              <div class="m-list-search__form-wrapper">
                                <span class="m-list-search__form-input-wrapper">
                                  <input id="m_quicksearch_input" autocomplete="off" type="text" name="q" class="m-list-search__form-input" value="" placeholder="Search...">
                                </span>
                                <span class="m-list-search__form-icon-close" id="m_quicksearch_close">
                                  <i class="la la-remove"></i>
                                </span>
                              </div>
                            </form>
                          </div>
                          <div class="m-dropdown__body">
                            <div class="m-dropdown__scrollable m-scrollable" data-scrollable="true" data-mobile-max-height="200">
                              <div class="m-dropdown__content"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li>
                    <li class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-right 	m-dropdown--mobile-full-width" data-dropdown-toggle="click" data-dropdown-persistent="true">
                      <a href="#" class="m-nav__link m-dropdown__toggle" id="m_topbar_notification_icon">
                        @php
                          $hasnotifications = false;
                        @endphp
                        @if(count($notifications) >= 1)
                          @php
                          $hasnotifications = true;
                          @endphp
                          <span class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-small m-badge--danger m-animate-blink"></span>
                        @endif
                        <span class="m-nav__link-icon @if($hasnotifications)m-animate-shake @endif">
                          <i class="flaticon-music-2"></i>
                        </span>
                      </a>
                      <div class="m-dropdown__wrapper">
                        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                        <div class="m-dropdown__inner">
                          <div class="m-dropdown__header m--align-center" style="background: url(/design/notification_bg2.jpg); background-size: cover;">
                            <ul class="card-actions icons right-top">
                                    <li>
                                        <a href="javascript:void(0)" style="color:white;" id="read-all">
                                            <i class="zmdi zmdi-check-all"></i>
                                        <div class="ripple-container"></div></a>
                                    </li>
                                </ul>
                            <span class="m-dropdown__header-title">
                              {{count($notifications)}} Unread
                            </span>
                            <span class="m-dropdown__header-subtitle">
                               Notifications
                            </span>
                          </div>
                          <div class="m-dropdown__body">
                            <div class="m-dropdown__content">
                              <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--brand" role="tablist">
                                <li class="nav-item m-tabs__item active">
                                  <a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_notifications" role="tab">
                                    Alerts
                                  </a>
                                </li>
                                <li class="nav-item m-tabs__item">
                                  <a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_events" role="tab">
                                    Events
                                  </a>
                                </li>
                                <li class="nav-item m-tabs__item">
                                  <a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_logs" role="tab">
                                    Logs
                                  </a>
                                </li>
                              </ul>
                              <div class="tab-content">
                                <div class="tab-pane active" id="topbar_notifications_notifications" role="tabpanel">
                                  <div class="m-scrollable" data-scrollable="true" data-max-height="250" data-mobile-max-height="200">
                                    <div class="m-list-timeline m-list-timeline--skin-light">
                                      <div class="m-list-timeline__items">
                                        @php
                                          $count = 0;
                                        @endphp
                                        @foreach($notifications->take(12) as $notification)
                                          @if($notification->data[0]['type'] != 'statuscomment' && $notification->data[0]['type'] != 'log' )
                                              @php
                                                $count++;
                                                // Extract url

                                                if($notification->data[0]['type'] == 'statuscomment' || $notification->data[0]['type'] == 'tag' ) {
                                                  $url = '/dashboard?status=' . $notification->data[0]['status'];
                                                } elseif($notification->data[0]['type'] == 'question') {
                                                  $url = '/question/' . $notification->data[0]['question'];
                                                } elseif($notification->data[0]['type'] == 'follower') {
                                                  $url = '/user/' . $notification->data[0]['username'];
                                                } else {
                                                  $url = "#";
                                                }
                                              @endphp

                                          <div class="m-list-timeline__item">
                                            <span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
                                            <span class="m-list-timeline__text" style="font-size:12px">
                                              <a href="{{$url}}">{{$notification->data[0]['data']}}</a>
                                            </span>
                                            <span class="m-list-timeline__time" style="font-size:11px;">
                                              {{$notification->created_at->diffForHumans()}}
                                            </span>
                                          </div>
                                        @endif
                                      @endforeach
                                      </div>

                                        <div class="m-stack m-stack--ver m-stack--general no-new" style="min-height: 180px;@if($count != 0)display:none;@endif">
                                          <div class="m-stack__item m-stack__item--center m-stack__item--middle">
                                            <span class="">
                                              All caught up!
                                              <br>
                                              No new alerts.
                                            </span>
                                          </div>
                                        </div>

                                    </div>
                                  </div>
                                </div>
                                <div class="tab-pane" id="topbar_notifications_events" role="tabpanel">
                                  <div class="m-scrollable" data-scrollable="true" data-max-height="250" data-mobile-max-height="200">
                                    <div class="m-list-timeline m-list-timeline--skin-light">
                                      <div class="m-list-timeline__items">
                                        @php
                                          $count = 0;
                                        @endphp
                                        @foreach($notifications->take(12) as $notification)
                                          @if($notification->data[0]['type'] == 'statuscomment')
                                              @php
                                                $count += 1;
                                                // Extract url

                                                if($notification->data[0]['type'] == 'statuscomment' || $notification->data[0]['type'] == 'tag' ) {
                                                  $url = '/dashboard?status=' . $notification->data[0]['status'];
                                                } elseif($notification->data[0]['type'] == 'question') {
                                                  $url = '/question/' . $notification->data[0]['question'];
                                                } elseif($notification->data[0]['type'] == 'follower') {
                                                  $url = '/user/' . $notification->data[0]['username'];
                                                } else {
                                                  $url = "#";
                                                }
                                              @endphp

                                          <div class="m-list-timeline__item">
                                            <span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
                                            <span class="m-list-timeline__text" style="font-size:12px">
                                              <a href="{{$url}}">{{$notification->data[0]['data']}}</a>
                                            </span>
                                            <span class="m-list-timeline__time" style="font-size:11px;">
                                              {{$notification->created_at->diffForHumans()}}
                                            </span>
                                          </div>
                                        @endif
                                      @endforeach
                                      </div>
                                        <div class="m-stack m-stack--ver m-stack--general no-new" style="min-height: 180px;@if($count != 0)display:none;@endif">
                                          <div class="m-stack__item m-stack__item--center m-stack__item--middle">
                                            <span class="">
                                              All caught up!
                                              <br>
                                              No new events.
                                            </span>
                                          </div>
                                        </div>

                                    </div>
                                  </div>
                                </div>
                                <div class="tab-pane" id="topbar_notifications_logs" role="tabpanel">
                                  <div class="m-stack m-stack--ver m-stack--general" style="min-height: 180px;">
                                    <div class="m-stack__item m-stack__item--center m-stack__item--middle">
                                      <span class="">
                                        All caught up!
                                        <br>
                                        No new logs.
                                      </span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li>
                    <li class="m-nav__item m-topbar__quick-actions m-topbar__quick-actions--img m-dropdown m-dropdown--medium m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push m-dropdown--mobile-full-width m-dropdown--skin-light" style="display:none;" data-dropdown-toggle="click">
                      <a href="#" class="m-nav__link m-dropdown__toggle">
                        <span class="m-nav__link-badge m-badge m-badge--dot m-badge--info m--hide"></span>
                        <span class="m-nav__link-icon">
                          <i class="flaticon-share"></i>
                        </span>
                      </a>
                      <div class="m-dropdown__wrapper">
                        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                        <div class="m-dropdown__inner">
                          <div class="m-dropdown__header m--align-center" style="background: url(/design/quick_actions_bg2.jpg); background-size: cover;">
                            <span class="m-dropdown__header-title">
                              Quick Actions
                            </span>
                            <span class="m-dropdown__header-subtitle">
                              Shortcuts
                            </span>
                          </div>
                          <div class="m-dropdown__body m-dropdown__body--paddingless">
                            <div class="m-dropdown__content">
                              <div class="m-scrollable" data-scrollable="false" data-max-height="380" data-mobile-max-height="200">
                                <div class="m-nav-grid m-nav-grid--skin-light">
                                  <div class="m-nav-grid__row">
                                    <a href="#" class="m-nav-grid__item">
                                      <i class="m-nav-grid__icon flaticon-folder"></i>
                                      <span class="m-nav-grid__text">
                                        Add New Investment
                                      </span>
                                    </a>
                                    <a href="#" class="m-nav-grid__item">
                                      <i class="m-nav-grid__icon flaticon-clipboard"></i>
                                      <span class="m-nav-grid__text">
                                        Quick Import
                                      </span>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li>
                    <li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--large m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"  data-dropdown-toggle="click">
                      <a href="#" class="m-nav__link m-dropdown__toggle">
                        <span class="m-topbar__userpic">
                          <img src="{{Auth::user()->getAvatar()}}" class="m--img-rounded m--marginless m--img-centered" alt=""/>
                        </span>
                        <span class="m-topbar__username m--hide">
                          Nick
                        </span>
                      </a>
                      <div class="m-dropdown__wrapper">
                        <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                        <div class="m-dropdown__inner" style="margin-right: -20px;  margin-left: -20px;">
                          <div class="m-dropdown__header m--align-center" style="background: url(/design/user_profile_bg2.jpg); background-size: cover;">
                            <div class="m-card-user m-card-user--skin-dark">
                              <div class="m-card-user__pic">
                                <img src="{{Auth::user()->getAvatar()}}" class="m--img-rounded m--marginless" alt=""/>
                              </div>
                              <div class="m-card-user__details">
                                <span class="m-card-user__name m--font-weight-500">
                                  {{Auth::user()->username}}
                                </span>
                                <a href="" class="m-card-user__email m--font-weight-300 m-link">
                                  {{Auth::user()->groupName()}}
                                </a>
                              </div>
                            </div>
                          </div>
                          <div class="m-dropdown__body">
                            <div class="m-dropdown__content">
                              <ul class="m-nav m-nav--skin-light">
                                <li class="m-nav__section m--hide">
                                  <span class="m-nav__section-text">
                                    Section
                                  </span>
                                </li>
                                <li class="m-nav__item">
                                  <a href="/user/{{Auth::user()->username}}" class="m-nav__link">
                                    <i class="m-nav__link-icon flaticon-profile-1"></i>
                                    <span class="m-nav__link-title">
                                      <span class="m-nav__link-wrap">
                                        <span class="m-nav__link-text">
                                          My Profile
                                        </span>
                                      </span>
                                    </span>
                                  </a>
                                </li>
                                <li class="m-nav__item">
                                  <a href="javascript:void(0)" class="m-nav__link">
                                    <i class="m-nav__link-icon flaticon-chat-1"></i>
                                    <span class="m-nav__link-text">
                                      Messages (Soon)
                                    </span>
                                  </a>
                                </li>
                                <li class="m-nav__separator m-nav__separator--fit"></li>
                                <li class="m-nav__item">
                                  <a href="/support" class="m-nav__link">
                                    <i class="m-nav__link-icon flaticon-lifebuoy"></i>
                                    <span class="m-nav__link-text">
                                      Support
                                    </span>
                                  </a>
                                </li>
                                <li class="m-nav__separator m-nav__separator--fit"></li>
                                <li class="m-nav__item">
                                  <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn m-btn--pill    btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder">
                                    Logout
                                  </a>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li>
                    <li id="m_quick_sidebar_toggle" class="m-nav__item" style="opacity:0;display:none;">
											<a href="#" class="m-nav__link m-dropdown__toggle">
												<span class="m-nav__link-icon">
													<i class="flaticon-grid-menu"></i>
												</span>
											</a>
										</li>
                  </ul>
                </div>
              </div>
              <!-- END: Topbar -->
            </div>
          </div>
        </div>
      </header>

      <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
		    <div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
          <div
        		id="m_ver_menu"
        		class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark "
        		data-menu-vertical="true"
        		 data-menu-scrollable="false" data-menu-dropdown-timeout="500"
        		>
            <ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
            							<li class="m-menu__item  m-menu__item--active" aria-haspopup="true">
            								<a href="/" class="m-menu__link ">
            									<i class="m-menu__link-icon flaticon-line-graph"></i>
            									<span class="m-menu__link-title">
            										<span class="m-menu__link-wrap">
            											<span class="m-menu__link-text">
            												Dashboard
            											</span>
            											<span class="m-menu__link-badge">
            												<span class="m-badge m-badge--danger">
            													2
            												</span>
            											</span>
            										</span>
            									</span>
            								</a>
            							</li>
            							<li class="m-menu__section">
            								<h4 class="m-menu__section-text">
            									Altpocket
            								</h4>
            								<i class="m-menu__section-icon flaticon-more-v3"></i>
            							</li>
                          <li class="m-menu__item" aria-haspopup="true">
            								<a href="/" class="m-menu__link ">
            									<i class="m-menu__link-icon flaticon-line-graph"></i>
            									<span class="m-menu__link-title">
            										<span class="m-menu__link-wrap">
            											<span class="m-menu__link-text">
            												API Connections
            											</span>
            										</span>
            									</span>
            								</a>
            							</li>
                          <li class="m-menu__section">
                            <h4 class="m-menu__section-text">
                              Portfolio
                            </h4>
                            <i class="m-menu__section-icon flaticon-more-v3"></i>
                          </li>
            							<li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true" data-menu-submenu-toggle="hover">
            								<a href="#" class="m-menu__link m-menu__toggle">
            									<i class="m-menu__link-icon flaticon-diagram"></i>
            									<span class="m-menu__link-text">
            										My Investments
            									</span>
            									<i class="m-menu__ver-arrow la la-angle-right"></i>
            								</a>
            								<div class="m-menu__submenu">
            									<span class="m-menu__arrow"></span>
            									<ul class="m-menu__subnav">
                                <li class="m-menu__item " aria-haspopup="true">
                                  <a href="/investments" class="m-menu__link ">
                                    <span class="m-menu__link-text">
                                      <i class="m-menu__link-icon flaticon-network" style="margin-right:5px;display:inline;"></i> My Investments
                                    </span>
                                  </a>
                                </li>
                              @if(Auth::user()->oldinvestments == 1)
            										<li class="m-menu__item " aria-haspopup="true">
            											<a href="/old-investments" class="m-menu__link ">
            												<span class="m-menu__link-text">
            													<i class="m-menu__link-icon flaticon-network" style="margin-right:5px;display:inline;"></i> Old Investments
            												</span>
            											</a>
            										</li>
                              @endif
                            @php
                              $investmentcounter = 0;
                            @endphp
                                              @foreach(Auth::user()->getInvestments() as $investment)
                                                @if($investmentcounter <= 4)
                                                @if($investment->sold_at == null && $investment->amount > 0)
                                                  @php
                                                    $investmentcounter++;
                                                  @endphp
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
                                                        <li class="m-menu__item " aria-haspopup="true">
                                                          <a href="javascript:void(0)" class="m-menu__link ">
                                                            <span class="m-menu__link-text">
                                                                <img src="/icons/32x32/{{$investment->currency}}.png" style="width:16px;"/> <span @if($profit > 0) style="color:#73c04d;" @else style="color:#de6b6b" @endif>({!! $symbol !!}{{number_format($profit, $decimal1)}}{!! $symbol2 !!})</span>
                                                            </span>
                                                          </a>
                                                        </li>
                                                   @endif
                                                   @if($investment->market == "EUR")
                                                   <?php
                                                       $price = Auth::user()->getPrice($investment->currency, $investment->market, $investment->exchange);
                                                       $previous = 0;
                                                       $decimal1 = 2;
                                                       $btc = $investment->btc_price_bought_usd * $fiat;
                                                       $btc_euro = $investment->btc_price_bought_eur;
                                                       $bought_at = ($investment->bought_at / $btc_euro) * $btc; // Bought at price current currency
                                                       $price = ($price * $multiplier); // Current price
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
                                                       $profit = (($investment->amount * $price)) - (($investment->amount * $bought_at));
                                                    ?>

                                                    <li class="m-menu__item " aria-haspopup="true">
                                                      <a href="javascript:void(0)" class="m-menu__link ">
                                                        <span class="m-menu__link-text">
                                                            <img src="/icons/32x32/{{$investment->currency}}.png" style="width:16px;"/> <span @if($profit > 0) style="color:#73c04d;" @else style="color:#de6b6b" @endif>({!! $symbol !!}{{number_format($profit, $decimal1)}}{!! $symbol2 !!})</span>
                                                        </span>
                                                      </a>
                                                    </li>
                                                    @endif
                                                    @if($investment->market == "GBP")
                                                    <?php
                                                        $price = Auth::user()->getPrice($investment->currency, $investment->market, $investment->exchange);
                                                        $previous = 0;
                                                        $decimal1 = 2;
                                                        $btc = $investment->btc_price_bought_usd * $fiat;
                                                        $btc_euro = $investment->btc_price_bought_usd * \App\Multiplier::where('currency', 'GBP')->select('price')->first()->price;
                                                        $bought_at = ($investment->bought_at / $btc_euro) * $btc; // Bought at price current currency
                                                        $price = ($price * $multiplier); // Current price
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
                                                        $profit = (($investment->amount * $price)) - (($investment->amount * $bought_at));
                                                     ?>

                                                     <li class="m-menu__item " aria-haspopup="true">
                                                       <a href="javascript:void(0)" class="m-menu__link ">
                                                         <span class="m-menu__link-text">
                                                             <img src="/icons/32x32/{{$investment->currency}}.png" style="width:16px;"/> <span @if($profit > 0) style="color:#73c04d;" @else style="color:#de6b6b" @endif>({!! $symbol !!}{{number_format($profit, $decimal1)}}{!! $symbol2 !!})</span>
                                                         </span>
                                                       </a>
                                                     </li>
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

                                                    <li class="m-menu__item " aria-haspopup="true">
                                                      <a href="javascript:void(0)" class="m-menu__link ">
                                                        <span class="m-menu__link-text">
                                                            <img src="/icons/32x32/{{$investment->currency}}.png" style="width:16px;"/> <span @if($profit > 0) style="color:#73c04d;" @else style="color:#de6b6b" @endif>({!! $symbol !!}{{number_format($profit, $decimal1)}}{!! $symbol2 !!})</span>
                                                        </span>
                                                      </a>
                                                    </li>
                                                 @endif
                                                 @if($investment->market == "USD")
                                                 <?php
                                                     $price = Auth::user()->getPrice($investment->currency, $investment->market, $investment->exchange);
                                                     $previous = 0;
                                                     $decimal1 = 2;
                                                     $btc = $investment->btc_price_bought_usd * $fiat;
                                                     $btc_euro = $investment->btc_price_bought_usd;
                                                     $bought_at = ($investment->bought_at / $btc_euro) * $btc; // Bought at price current currency
                                                     $price = ($price * $multiplier); // Current price
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
                                                     $profit = (($investment->amount * $price)) - (($investment->amount * $bought_at));
                                                  ?>

                                                  <li class="m-menu__item " aria-haspopup="true">
                                                    <a href="javascript:void(0)" class="m-menu__link ">
                                                      <span class="m-menu__link-text">
                                                          <img src="/icons/32x32/{{$investment->currency}}.png" style="width:16px;"/> <span @if($profit > 0) style="color:#73c04d;" @else style="color:#de6b6b" @endif>({!! $symbol !!}{{number_format($profit, $decimal1)}}{!! $symbol2 !!})</span>
                                                      </span>
                                                    </a>
                                                  </li>
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

                                                    <li class="m-menu__item " aria-haspopup="true">
                                                      <a href="javascript:void(0)" class="m-menu__link ">
                                                        <span class="m-menu__link-text">
                                                            <img src="/icons/32x32/{{$investment->currency}}.png" style="width:16px;"/> <span @if($profit > 0) style="color:#73c04d;" @else style="color:#de6b6b" @endif>({!! $symbol !!}{{number_format($profit, $decimal1)}}{!! $symbol2 !!})</span>
                                                        </span>
                                                      </a>
                                                    </li>
                                                 @endif
                                               @endif
                                             @endif
                                              @endforeach
                                            </ul>
                                          </div>
                          <li class="m-menu__section">
                            <h4 class="m-menu__section-text">
                              Social
                            </h4>
                            <i class="m-menu__section-icon flaticon-more-v3"></i>
                          </li>
                          <li class="m-menu__item" aria-haspopup="true">
            								<a href="https://twitter.com/altpocket" class="m-menu__link ">
            									<i class="m-menu__link-icon socicon-twitter"></i>
            									<span class="m-menu__link-title">
            										<span class="m-menu__link-wrap">
            											<span class="m-menu__link-text">
            												Our Twitter
            											</span>
            										</span>
            									</span>
            								</a>
            							</li>
                          <li class="m-menu__item" aria-haspopup="true">
                            <a href="/discord" class="m-menu__link ">
                              <i class="m-menu__link-icon socicon-discord"></i>
                              <span class="m-menu__link-title">
                                <span class="m-menu__link-wrap">
                                  <span class="m-menu__link-text">
                                    Discord Bot
                                  </span>
                                </span>
                              </span>
                            </a>
                          </li>
                          <li class="m-menu__section">
                            <h4 class="m-menu__section-text">
                              Other
                            </h4>
                            <i class="m-menu__section-icon flaticon-more-v3"></i>
                          </li>
            							<li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true" data-menu-submenu-toggle="hover">
            								<a href="#" class="m-menu__link m-menu__toggle">
            									<i class="m-menu__link-icon flaticon-questions-circular-button"></i>
            									<span class="m-menu__link-text">
            										Support
            									</span>
            									<i class="m-menu__ver-arrow la la-angle-right"></i>
            								</a>
            								<div class="m-menu__submenu">
            									<span class="m-menu__arrow"></span>
            									<ul class="m-menu__subnav">
            										<li class="m-menu__item " aria-haspopup="true">
            											<a href="/support" class="m-menu__link ">
            												<i class="m-menu__link-bullet m-menu__link-bullet--dot">
            													<span></span>
            												</i>
            												<span class="m-menu__link-text">
            													Support Center
            												</span>
            											</a>
            										</li>
                                <li class="m-menu__item " aria-haspopup="true">
            											<a href="https://discord.gg/YzdgGjG" class="m-menu__link ">
            												<i class="m-menu__link-bullet m-menu__link-bullet--dot">
            													<span></span>
            												</i>
            												<span class="m-menu__link-text">
            													Support Discord
            												</span>
            											</a>
            										</li>
            									</ul>
            								</div>
            							</li>
                          <li class="m-menu__item" aria-haspopup="true">
                            <a href="/importing-orders" class="m-menu__link ">
                              <i class="m-menu__link-icon flaticon-download"></i>
                              <span class="m-menu__link-title">
                                <span class="m-menu__link-wrap">
                                  <span class="m-menu__link-text">
                                    Importing Orders
                                  </span>
                                </span>
                              </span>
                            </a>
                          </li>
                          <li class="m-menu__item" aria-haspopup="true">
                            <a href="/about" class="m-menu__link ">
                              <i class="m-menu__link-icon flaticon-tea-cup"></i>
                              <span class="m-menu__link-title">
                                <span class="m-menu__link-wrap">
                                  <span class="m-menu__link-text">
                                    About us
                                  </span>
                                </span>
                              </span>
                            </a>
                          </li>
                          <li class="m-menu__item" aria-haspopup="true">
                            <a href="/about" class="m-menu__link ">
                              <i class="m-menu__link-icon fa fa-heart faa-tada animated" style="color:#ef5350"></i>
                              <span class="m-menu__link-title">
                                <span class="m-menu__link-wrap">
                                  <span class="m-menu__link-text">
                                    Support Us
                                  </span>
                                </span>
                              </span>
                            </a>
                          </li>
                          <li class="m-menu__item" aria-haspopup="true">
                            <a href="/badges" class="m-menu__link ">
                              <i class="m-menu__link-icon flaticon-confetti"></i>
                              <span class="m-menu__link-title">
                                <span class="m-menu__link-wrap">
                                  <span class="m-menu__link-text">
                                    Badges
                                  </span>
                                </span>
                              </span>
                            </a>
                          </li>
            						</ul>
                  </div>
                </div>


        <section id="content_outer_wrapper" style="padding-bottom:0px;">
          @yield('content')
        </section>
        </div>
    </div>





@if(Auth::user())


  <div class="modal fade" id="balances-modal" tabindex="-1" role="dialog" aria-labelledby="balances-modal">
    <div class="modal-dialog" role="document" style="width:800px!important;">
    <div class="modal-content" style="width:800px;">
    <div class="modal-header">

      <h4 class="modal-title" id="myModalLabel-2">API Connections</h4>
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
      <p>Here is a list of all your apis that are connected to Altpocket.</p>
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
          @foreach(\App\Key::where('userid', Auth::user()->id)->get() as $key)
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
                                @if(Auth::user()->isFounder())<option value="HitBTC">HitBTC API Keys</option>@endif
                                <option value="Coinbase">Coinbase Connection</option>
                                <option value="Ethwallet">Ethereum Wallet</option>
                                <option value="Ethnano">Ethereum Nanopool</option>
                                <option value="Ethermine">Ethermine</option>
                                <option value="Nicehash">Nicehash</option>
                              </select>
                        </div>

                    <div id="exchange2" style="display:none;">
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
                    <button type="submit" class="btn btn-primary" id="sourcebutton">Add Source</button>
                    <a id="coinbasebutton" style="display:none" href="https://www.coinbase.com/oauth/authorize?response_type=code&client_id=7684855d375a8aa4486c183903573f28ea098779df9c970cc4ad7b36a0ea748e&redirect_uri=https://altpocket.io/coinbase/callback&scope=wallet:accounts:read,wallet:transactions:read,wallet:buys:read,wallet:deposits:read,wallet:sells:read,wallet:withdrawals:read&account=all" class="btn btn-primary">Connect my Coinbase Account</a>
      </form>



      </div>
      </div>
      <!-- modal-content -->
      </div>
<!-- modal-dialog -->
</div>


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
                    <p>Thank you for considerating a donation. We strongly believe in Altpocket and that's why we quit our jobs to work on it full-time.<br>
                      All costs are currently being payed by Edwin and Svensson's own pockets as of today so please help us with expenses such as:
                    </p>
                      <ul style="list-style:circle;margin-left:15px;">
                        <li>Food</li>
                        <li>Red Bull</li>
                        <li>Server Costs</li>
                        <li>Savings for more developers</li>
                      </ul>
                      <p>Donating $5+ gives you a <strong>Donator rank</strong> and badge.<br>
                         Donating $50+ gives you a <strong>Sponsor rank</strong> and badge.<br>
                         Donating $100+ gives you a <strong>VIP rank</strong> and badge, it also gives you a specific "big guy" badge along with access to test new functionality first.</p>
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
                      <button class="btn btn-info">Support Altpocket through Coinpayments<div class="ripple-container"></div></button>
                    </form>
        						</div>
        					</div>
        					<!-- modal-content -->
        				</div>
				<!-- modal-dialog -->
			</div>
@yield('earlyjs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
<script type="text/javascript">

function number_format (number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

    var test = io.connect('https://coincap.io');
    test.on('trades', function (tradeMsg) {
      //console.log(tradeMsg);
        var id = tradeMsg['exchange_id'] + "-" + tradeMsg['coin'];
        var pair = tradeMsg['market_id'];

        var object = $('[data-id="'+id+'"]');
        $('[data-id="'+id+'"]').each(function() {
          console.log("hi again");
          if($(this).attr('data-pair') == pair)
          {
            var price = tradeMsg['trade']['data']['raw']['Price']; // Price in BTC
            var oldprice = $(this).attr('data-price');
            // Investment Variables
            var multiplier = $(this).attr('data-multiplier');
            var amount = $(this).attr('data-amount');
            var investmentid = $(this).attr('data-investment-id');
            var then = $(this).attr('data-then');
            var newtotal = price * multiplier * amount;

            var networth = $("#networth").attr('data-networth');
            var profit = $("#profit").attr('data-profit');
            var activeprofit = $("#activeprofit").attr('data-activeprofit');
            var initial = $(this).attr('data-initial');
            var initialprofit = $(this).attr('data-initial-profit');
            $(this).attr('data-price', price);
            $(this).attr('data-initial', newtotal);

            // Set Variables and shit
            $(this).find('#price').text(number_format(newtotal, 2)); // Big number_format
            $("#"+investmentid+"-now").text(number_format(newtotal, 2)); // Now price big
            $("#"+investmentid+"-profit").text(number_format(newtotal - then, 2)); // profit
            $("#"+investmentid+"-now-small").text(number_format(price * multiplier, 2)); // Now price small
            $("#"+investmentid+"-percent").text(number_format(((newtotal - then) / then) * 100, 2)); // percent
            $("#networth").text(number_format((networth - initial) + newtotal, 2)); // Set Networth number
            $("#networth").attr('data-networth', (networth - initial) + newtotal); // Set networth variable
            $("#profit").text(number_format((profit - initialprofit) + (newtotal - then), 2)); // Set Networth number
            $("#profit").attr('data-profit', (profit - initialprofit) + (newtotal - then)); // Set networth variable
            $("#activeprofit").text(number_format((activeprofit - initialprofit) + (newtotal - then), 2)); // Set Networth number
            $("#activeprofit").attr('data-activeprofit', (activeprofit - initialprofit) + (newtotal - then)); // Set networth variable

            if(price > oldprice)
            {
              var flash = "flash";
            } else {
              var flash = "redflash";
            }

            // Flashing
            $("#"+investmentid).addClass(flash);
            $("#tournetworth").addClass(flash);
            $("#tourprofit").addClass(flash);
            $("#touractiveprofit").addClass(flash);
            setTimeout(function() {
              $("#"+investmentid).removeClass(flash);
              $("#tournetworth").removeClass(flash);
              $("#tourprofit").removeClass(flash);
              $("#touractiveprofit").removeClass(flash);
            }, 500);

          }
        })




    })
</script>
<script src="/assets/js/vendor.bundle.js"></script>
<script src="/design/scripts.bundle.js?v=34" type="text/javascript"></script>
<script src="/assets/js/app.bundle.js?v=7.9"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
@if(Auth::user())

@endif
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="{{ elixir('js/6001-9.js') }}"></script>
@include('sweet::alert')




<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>

        @stack('scripts')

@yield('js')


@yield('javascript')
<script>
$("#m_aside_left_minimize_toggle").click(function() {
  if (Boolean(sessionStorage.getItem("sidebar-toggle-collapsed"))) {
    sessionStorage.setItem("sidebar-toggle-collapsed", "");
  } else {
    sessionStorage.setItem("sidebar-toggle-collapsed", "1");
  }
});


// Notification Sounds
//var audio = new Audio('/sounds/notification.mp3');
var title = document.title;

$(function() {
    window.isActive = true;
    $(window).focus(function() { this.isActive = true; });
    $(window).blur(function() { this.isActive = false; });
});

$(window).focus(function() {
  document.title = title;
});

$('textarea').on('keydown', event => {
    if (event.keyCode == 37 || event.keyCode == 39)
      event.stopImmediatePropagation();
  });
  $('input').on('keydown', event => {
      if (event.keyCode == 37 || event.keyCode == 39)
        event.stopImmediatePropagation();
    });

@if(Auth::user())
// Hey there, glad you are looking into the code hehe, we previously used pusher which costed 49$ a month for real time notifications but we now developed our own system!!!
$(window).load(function() {
Echo.private('App.User.{{Auth::user()->id}}')
.listen(".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated", (event) => {
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
    Command: toastr[event.category](event.value)

    if(event.value == "Your import is complete! Go to your investments to see your import" && window.location.pathname == "/investments")
    {
      location.reload();
    }
    if(event.value == "Your import is complete! Go to your investments to see your import" && window.location.pathname == "/investments/")
    {
      location.reload();
    }
        })
  .listen(".PrivateMessage", (event) => {
      //Push the message

      if(current() == event.sender)
      {
        $(".typing").hide();
        pushMsg(event.sender, event.message, event.timestamp, false, event.avatar);
        $(".popup-chat .mCustomScrollbar").mCustomScrollbar("scrollTo","-9999999999",{scrollInertia:0});

        $(function () {
          $('[data-toggle=tooltip]').tooltip();
        });
      } else if(event.sender == "{{Auth::user()->id}}"){
          pushMsg(event.sender, event.message, event.timestamp, false, event.avatar);
          $(".popup-chat .mCustomScrollbar").mCustomScrollbar("scrollTo","-9999999999",{scrollInertia:0});

          $(function () {
            $('[data-toggle=tooltip]').tooltip();
          });
      }

      if(event.sender != "{{Auth::user()->id}}" && window.isActive == false)
      {
          audio.play();
          document.title = "(1) New Message";
      }

      if(current() != event.sender)
      {
        if($("li[data-user='"+event.sender+"']").length)
        {
          if(!($("li[data-user='"+event.sender+"']").hasClass('unread-message')))
          {
            $("li[data-user='"+event.sender+"']").addClass('unread-message');
            $("li[data-user='"+event.sender+"']").eq(0).prepend('<span class="label label-danger unread">New</span>');
          }
        }
        if($("li[data-user-large='"+event.sender+"']").length)
        {
          if(!($("li[data-user-large='"+event.sender+"']").hasClass('unread-message')))
          {
            $("li[data-user-large='"+event.sender+"']").addClass('unread-message');
            $("li[data-user-large='"+event.sender+"']").eq(0).prepend('<span class="label label-danger unread" style="margin-top:27px;">New</span>');
          }
        }
      }
    }).listenForWhisper('typing', (e) => {
        if(current() == e.id)
        {
          console.log(e);
          if(e.chars > 2)
          {
            $(".typing").show();
          } else {
            $(".typing").hide();
          }
        }
    });
  });

    @if(Auth::user()->username == "Flataunet")
    Echo.join('chat')
      .joining((user) => {
        if($("span[user='"+user.id+"']").length)
        {
          if($("span[user='"+user.id+"']").hasClass('icon-status disconected'))
          {
            $("span[user='"+user.id+"']").removeClass('icon-status disconected');
            $("span[user='"+user.id+"']").addClass('icon-status online');
          }
        }
        if($("span[data-user-status='"+user.id+"']").length)
        {
          $("span[data-user-status='"+user.id+"']").text('ONLINE');
        }
      }).leaving((user) => {
        if($("span[user='"+user.id+"']").length)
        {
          if($("span[user='"+user.id+"']").hasClass('icon-status online'))
          {
            $("span[user='"+user.id+"']").removeClass('icon-status online');
            $("span[user='"+user.id+"']").addClass('icon-status disconected');
          }
        }
        if($("span[data-user-status='"+user.id+"']").length)
        {
          $("span[data-user-status='"+user.id+"']").text('OFFLINE');
        }
      }).listen('UserOnline', (e) => {
      }).here((users) => {
        for(i in users) {
          if($("span[user='"+users[i].id+"']").length)
          {
            if($("span[user='"+users[i].id+"']").hasClass('icon-status disconected'))
            {
              $("span[user='"+users[i].id+"']").removeClass('icon-status disconected');
              $("span[user='"+users[i].id+"']").addClass('icon-status online');
            } else {
              $("span[user='"+users[i].id+"']").removeClass('icon-status online');
              $("span[user='"+users[i].id+"']").addClass('icon-status disconected');
            }
          }
          if($("span[data-user-status='"+users[i].id+"']").length)
          {
            $("span[data-user-status='"+users[i].id+"']").text('ONLINE');
          }
        }
      });
      @endif


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

        $("#balances").click(function(){
           $("#balances-modal").modal('toggle');
        });
        $("#balances2").click(function(){
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


        $("#read-all").click(function(){
           var id = $(this).attr('id');
           $.ajax({
               url: '/notification/readall/',
               type: 'get',
               success: function(data){
                   $(".m-list-timeline__items").remove();
                   $('.no-new').css('display', '');
                   $('.m-nav__link-icon').removeClass('m-animate-shake');
                   $('.m-badge--danger').removeClass('m-animate-blink');
                   $('.m-badge--danger').css('display', 'none');
                   $('.m-dropdown__header-title').text('0 Unread');
               }
           });
        });

        $(".m-list-search__form").submit(function(e){
            e.preventDefault();
        });

        @endif


        // Balance stuff (Beta - I see you sneaking in the code =)
        $(".remove-key").click(function(){
            var keyId = $(this).attr('id');

	          $.get( "/sources/delete/"+keyId, function( data ) {
              if(data != "No key found")
              {
                $(".zel-"+keyId).remove();
                swal("Key Deleted", "You successfully removed the "+data+" connection.", "success");
              } else {
                swal("No key found", "No source was found.", "error");
              }
            });
        });
        $("#sourcetype").change(function(){

          if($(this).val() == "Poloniex" || $(this).val() == "Bittrex" || $(this).val() == "HitBTC")
          {
            $("#miner").css('display', 'none');
            $("#wallet").css('display', 'none');
            $("#nicehash").css('display', 'none');
            $("#exchange2").css('display', 'block');
            $("#sourcebutton").css('display', 'block');
            $("#coinbasebutton").css('display', 'none');
          }
          if($(this).val() == "Ethnano" || $(this).val() == "Ethermine")
          {
            $("#wallet").css('display', 'none');
            $("#exchange2").css('display', 'none');
            $("#nicehash").css('display', 'none');
            $("#miner").css('display', 'block');
            $("#sourcebutton").css('display', 'block');
            $("#coinbasebutton").css('display', 'none');
          }
          if($(this).val() == "Ethwallet")
          {
            $("#exchange2").css('display', 'none');
            $("#miner").css('display', 'none');
            $("#nicehash").css('display', 'none');
            $("#wallet").css('display', 'block');
            $("#sourcebutton").css('display', 'block');
            $("#coinbasebutton").css('display', 'none');
          }
          if($(this).val() == "Nicehash")
          {
            $("#sourcebutton").css('display', 'block');
            $("#coinbasebutton").css('display', 'none');
            $("#exchange2").css('display', 'none');
            $("#miner").css('display', 'none');
            $("#wallet").css('display', 'none');
            $("#nicehash").css('display', 'block');
          }
          if($(this).val() == "Coinbase")
          {
            $("#wallet").css('display', 'none');
            $("#exchange2").css('display', 'none');
            $("#nicehash").css('display', 'none');
            $("#miner").css('display', 'none');
            $("#sourcebutton").css('display', 'none');
            $("#coinbasebutton").css('display', 'block');
          }
        });

        $('.post-button').click(function(){
              e.stopPropagation();
        })
    </script>

    <script>
    $('#m_aside_header_topbar_mobile_toggle').click(function() {
        $('body').toggleClass('m-topbar--on');
    });

    $('a').click(function(){

     if($(this).hasClass('btn')){
       ga('send', {
        hitType: 'event',
        eventCategory: 'Click',
        eventAction: 'A',
        eventLabel: $(this).parent(0).attr('data-original-title')
      });
    } else {
      ga('send', {
       hitType: 'event',
       eventCategory: 'Click',
       eventAction: 'A',
       eventLabel: $(this).text()
     });
    }
    });

    $('button').click(function(){
      ga('send', {
       hitType: 'event',
       eventCategory: 'Click',
       eventAction: 'Button',
       eventLabel: $(this).text()
     });
    });

    </script>

    @yield('js2')


</body>
</html>
