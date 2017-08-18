<!DOCTYPE html>
<html lang="en">
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
      <meta name="description" content="Altpocket is the best portfolio available for showcasing altcoin and cryptocurrency investments. Register now for free! Import orders from Bittrex and Poloniex.">
      <meta name="keywords" content="portfolio,altcoins,cryptocurrency,xrp,btc,showcase,investments,bitcoins,ripple,litecoin,show,altpocket,alt pocket, alt,pocket,altpocket.io,altcoin portfolio,tracking,import,bittrex,poloniex,tracking,altcoin portfolio">
      <meta name="author" content="Edwin">
      <meta name="og:image" content="/share.png?v=1.1" />
      <meta name="og:url" content="https://altpocket.io" />
      <meta name="og:title" content="Altpocket - The social altcoin portfolio" />
      <meta name="og:description" content="Altpocket is the best portfolio available for showcasing altcoin and cryptocurrency investments. Register now for free! Import orders from Bittrex and Poloniex." />
      <meta name="twitter:card" content="summary" />
      <meta name="twitter:site" content="@altpocket" />
      <meta name="twitter:title" content="Altpocket- The social portfolio" />
      <meta name="twitter:description" content="Altpocket is the best portfolio available for showcasing altcoin and cryptocurrency investments. Register now for free! Import orders from Bittrex and Poloniex." />
      <meta name="twitter:image" content="https://altpocket.io/share.png?v=1.1" />
      <meta name="twitter:image:alt" content="Altpocket - The social portfolio" />

      <title>Altpocket - @yield('title')</title>
      <!--    favicon-->
      <link rel="icon" href="/img/logo.gif" alt="Altpocket.io Logo">

      <!-- Bootstrap -->
      <link href="/css/bootstrap.min.css" rel="stylesheet">
      <link href="/css/bootstrap-theme.min.css" rel="stylesheet">
      <link href="/css/font-awesome.min.css" rel="stylesheet">
      <link rel="stylesheet" href="/vendors/themify-icon/themify-icons.css">
      <!-- strock-Gap-icon -->
      <link rel="stylesheet" href="/vendors/linear-icon/style.css">
      <!-- magnific popup-->
      <link rel="stylesheet" href="/vendors/magnific-popup/magnific-popup.css">
      <!--        owl carousel css-->
      <link rel="stylesheet" href="/vendors/owl-carousel/owl.carousel.min.css">
      <link rel="stylesheet" href="/vendors/owl-carousel/animate.css">
      <!-- RS5.0 Main Stylesheet -->
      <link rel="stylesheet" type="text/css" href="/vendors/revolution/css/settings.css">
      <link href="/css/slim.min.css" rel="stylesheet" type="text/css">
      <!-- RS5.0 Layers and Navigation Styles -->
      <link rel="stylesheet" type="text/css" href="/vendors/revolution/css/layers.css">
      <link rel="stylesheet" type="text/css" href="/vendors/revolution/css/navigation.css">
      <!--    css-->
      <link rel="stylesheet" href="/css/style2.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
      <!--responsive css-->
      <link rel="stylesheet" href="/css/responsive2.css?v=1.0">
      <!--color css-->
      <link rel="stylesheet" id="triggerColor" href="/css/triggerPlate/color-0.css">

      <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->

      <style>
              .slim {
                  height:600px;
                  width:600px;
                  margin:0 auto;
              }
              .card.card-comment[data-timeline=comment]:before {
                  content: "\f25b"!important;
                  background: #90aeb4;
                  border: 4px solid #e1e9ee;
              }

      </style>

    </head>
    <body data-scroll-animation="true">
        <!--start preloader area-->
        <div class="loader-container circle-pulse-multiple">
			<div class="loader">
				<div id="loading-center-absolute">
                    <div class="object" id="object_four"></div>
                    <div class="object" id="object_three"></div>
                    <div class="object" id="object_two"></div>
                    <div class="object" id="object_one"></div>
                </div>
			</div>
		</div>
       <!--End preloader area-->

        <!--Start searchForm -->
        <div class="searchForm">
            <span class="input-group-addon cross-btn form_hide"><i class="lnr lnr-cross"></i></span>
           <div class="container">
                <form action="#" class="row search_row m0">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control" placeholder="Type & Hit Enter">
                    </div>
                    <p>Input your search keywords and press Enter.</p>
                </form>
           </div>
        </div>
        <!-- End searchForm -->
        <!--start header Area-->
        <header class="header" id="stricky">
            <nav class="navbar navbar-default">
                <div class="container">
                    <!--========== Brand and toggle get grouped for better mobile display ==========-->
                    <div class="navbar-header">
                        <a class="navbar-brand" href="/"><img src="https://altpocket.io/assets/logo.gif" style="width:60px;" alt=""><img src="https://altpocket.io/assets/logo.gif" alt="" style="width:60px;"></a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar cross"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <!--========== Collect the nav links, forms, and other content for toggling ==========-->
                    <ul class="nav navbar-nav nav-right navbar-right">
                        <li class="search_dropdown"><a href="#"><i class="ti-search"></i></a></li>
                        <li>
                           <a href="/login" class="btn g-btn">
                                Login
                            </a>
                        </li>
                    </ul>
                    <div class="collapse navbar-right navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav menu" id="nav">
                            <li>
                                <a href="/">Home</a>
                            </li>
                            <li>
                                <a href="/support">Support</a>
                            </li>
                            <li class="current">
                                <a href="/blog">Blog</a>
                            </li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div>
            </nav>
        </header>
        <!--End header Area-->

        @yield('content')


        <script type="text/javascript" src="/js/jquery-2.2.4.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <!--waypoint js-->
        <script type="text/javascript" src="/vendors/waypoints/waypoints.min.js"></script>
        <!--counterup js-->
        <script type="text/javascript" src="/vendors/counterup/jquery.counterup.min.js"></script>
        <!--nav js-->
        <script type="text/javascript" src="/js/jquery-nav.js"></script>
        <!--isotope js-->
        <script type="text/javascript" src="/vendors/isotope/isotope-min.js"></script>
        <script type="text/javascript" src="/js/jquery.stellar.js"></script>
        <!--imagesloaded js-->
        <script type="text/javascript" src="/vendors/imagesloaded/imagesloaded.pkgd.min.js"></script>
        <script type="text/javascript" src="/vendors/animated-imagegrid/js/jquery.gridrotator.js"></script>
        <!--magnific js-->
        <script type="text/javascript" src="/vendors/magnific-popup/jquery.magnific-popup.min.js"></script>

        <!--wow js-->
        <script type="text/javascript" src="/js/wow.min.js"></script>
        <script type="text/javascript" src="/js/plugins.js"></script>
        <!-- REVOLUTION JS FILES -->
		<script type="text/javascript" src="/vendors/revolution/js/jquery.themepunch.tools.min.js"></script>
		<script type="text/javascript" src="/vendors/revolution/js/jquery.themepunch.revolution.min.js"></script>
        <script type="text/javascript" src="/vendors/revolution/revolution-addons/countdown/revolution.addon.countdown.min.js"></script>
        <script type="text/javascript" src="/vendors/revolution/revolution-addons/typewriter/js/revolution.addon.typewriter.min.js"></script>
		<!-- SLIDER REVOLUTION 5.0 EXTENSIONS  (Load Extensions only on Local File Systems !  The following part can be removed on Server for On Demand Loading) -->
		<script type="text/javascript" src="/vendors/revolution/js/extensions/revolution.extension.actions.min.js"></script>
		<script type="text/javascript" src="/vendors/revolution/js/extensions/revolution.extension.carousel.min.js"></script>
		<script type="text/javascript" src="/vendors/revolution/js/extensions/revolution.extension.kenburn.min.js"></script>
		<script type="text/javascript" src="/vendors/revolution/js/extensions/revolution.extension.layeranimation.min.js"></script>
		<script type="text/javascript" src="/vendors/revolution/js/extensions/revolution.extension.migration.min.js"></script>
		<script type="text/javascript" src="/vendors/revolution/js/extensions/revolution.extension.navigation.min.js"></script>
		<script type="text/javascript" src="/vendors/revolution/js/extensions/revolution.extension.parallax.min.js"></script>
		<script type="text/javascript" src="/vendors/revolution/js/extensions/revolution.extension.slideanims.min.js"></script>
		<script type="text/javascript" src="/vendors/revolution/js/extensions/revolution.extension.video.min.js"></script>
        <!-- owl JS Files -->
        <script type="text/javascript" src="/vendors/owl-carousel/owl.carousel.min.js"></script>
        <!--custom js-->
        <script type="text/javascript" src="/js/custom.js?v=1.0"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.min.js"></script>
        <script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=vtpueewwykzjmhspm4w61rm6j2hn2itdv3e4k34ulcmnj67i"></script>
        <script src="/js/slim.kickstart.min.js" type="text/javascript"></script>
        @include('sweet::alert')

        @yield('js')

    </body>
</html>
