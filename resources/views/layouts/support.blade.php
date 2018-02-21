<!DOCTYPE html>
<html lang="en">

<head>

    <!-- meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Knowledgebase, Documentation Website HTML5 Template">
    <meta name="keywords" content="Knowledgebase, Knowledgebase, multipurpose, onepage, Documenter, responsive">
    <meta name="author" content="Themeix">

    <!-- Site Title -->
    <title>Altpocket - @yield('title')</title>

    <!-- favicon -->
    <link rel="icon"
      type="image/gif"
      href="/assets/logo.png">

    <!-- Bootstrap CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font-Awesome CSS -->
    <link href="/css/font-awesome.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

    <!--Animate CSS -->
    <link rel="stylesheet" href="/css/animate.min.css">

    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">

    <!-- Mean Menu CSS -->
    <link rel="stylesheet" href="/css/meanmenu.min.css">

    <!-- Main Stylesheet -->
    <link href="/css/style5.css?v=1.0" rel="stylesheet">

    <!-- Responsive CSS -->
    <link href="/css/responsive3.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

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
    <!--[if lt IE 9]>
        <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        @yield('css')
</head>

<body>
    <!-- Preloader starts-->
    <div class="preloader">
        <div class="loading-center">
            <div class="loading-center-absolute">
                <div class="object object_one"></div>
                <div class="object object_two"></div>
                <div class="object object_three"></div>
            </div>
        </div>
    </div>
    <!-- Preloader ends -->

    <!-- Start Header -->
    <header>
        <!-- Start Mainmenu -->
        <div class="menu-area navbar-fixed-top ">
            <div class="container">
                <div class="row">
                    <div class="mainmenu-wrapper">
                        <!-- Start Header Logo -->
                        <div class="col-xs-12 col-md-3">
                            <div class="header-logo">
                                <a href="/support"><img style="max-height:45px!important;" src="/assets/logo_blue_text.png" alt="logo"></a>
                            </div>
                        </div>
                        <!-- End Header Logo -->
                        <!-- Start Navigation -->
                        <div class="col-xs-12 col-md-9">
                            <div class="mainmenu">
                                <div class="navbar navbar-right">
                                    <div class="navbar-header visible-xs">
                                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                                            <span class="sr-only">Toggle navigation</span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </button>
                                    </div>
                                    <div class="collapse navbar-collapse top-menu">
                                        <nav>
                                            <ul class="nav navbar-nav">
                                                <li class="active"><a href="/support">Home<span>FAQ and more</span></a>
												</li>
                                                <li><a href="/about">About<span>About Altpocket</span></a></li>
                                                <li><a href="/questions">Questions<span>Ask Your Question</span></a>
                                                    <ul class="dropdown">
                                                        <li>
                                                            <a href="/questions"> <span><i class="fa fa-question-circle"></i></span>All Questions</a>
                                                        </li>
                                                        @if(Auth::user())
                                                        <li><a href="/ask"><span><i class="fa fa-reply-all"></i></span>Ask A Question</a></li>
                                                        <li><a href="/myquestions"><span><i class="fa fa-list"></i></span>My Questions</a></li>
                                                        @else
                                                        <li><a href="/login"><span><i class="fa fa-login"></i></span>Login To Ask</a></li>
                                                        @endif
                                                    </ul>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Navigation -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Mainmenu -->
        <!-- Start Mobile Menu Area -->
        <div class="col-xs-12 visible-xs">
            <div class="mobile-menu">
                <nav>
                    <ul>
            <li class="active"><a href="/support">Home</a></li>
						<li><a href="/about">About</a></li>
              <li><a href="#">Questions</a>
							<ul>
                <li>
                    <a href="/questions">All Questions</a>
                </li>
                @if(Auth::user())
                <li><a href="/ask">Ask A Question</a></li>
                <li><a href="/myquestions">My Questions</a></li>
                @else
                <li><a href="/login">Login To Ask</a></li>
                @endif
							</ul>
						</li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- End MObile Menu Area -->
    </header>
    <!-- End Header -->
    @yield('content')

    <!-- jquery min -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <!-- Bootstrap Js -->
    <script src="/js/bootstrap.min.js"></script>

    <!-- jQuery Easing -->
    <script src="/js/jquery.easing.js"></script>

    <!-- Owl Carousel Js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>

    <!-- Mean Menu -->
    <script src="/js/jquery.meanmenu.js"></script>



    <!-- Main Js -->
    <script src="/js/main2.js?v=1.0"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.min.js"></script>

    <script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=vtpueewwykzjmhspm4w61rm6j2hn2itdv3e4k34ulcmnj67i"></script>
    @yield('js')

    @include('sweet::alert')

    <script>

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
</body>

</html>
