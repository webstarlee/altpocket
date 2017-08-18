<!DOCTYPE html>
<html lang="en">
<head>

	<title>Altpocket - Login/Register</title>

	<!-- Required meta tags always come first -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<!-- Main Font -->
	<script src="js/webfontloader.min.js"></script>
	<script>
		WebFont.load({
			google: {
				families: ['Roboto:300,400,500,700:latin']
			}
		});
	</script>
  <link rel="icon"
    type="image/gif"
    href="/assets/logo.png">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="/version2/Bootstrap/dist/css/bootstrap-reboot.css?v=1.1">
	<link rel="stylesheet" type="text/css" href="/version2/Bootstrap/dist/css/bootstrap.css?v=1.1">
	<link rel="stylesheet" type="text/css" href="/version2/Bootstrap/dist/css/bootstrap-grid.css?v=1.1">

	<!-- Theme Styles CSS -->
	<link rel="stylesheet" type="text/css" href="/version2/css/theme-styles.css?v=1.0">
	<link rel="stylesheet" type="text/css" href="/version2/css/blocks.css?v=1.4">
	<link rel="stylesheet" type="text/css" href="/version2/css/fonts.css">

	<!-- Styles for plugins -->
	<link rel="stylesheet" type="text/css" href="/version2/css/jquery.mCustomScrollbar.min.css">
	<link rel="stylesheet" type="text/css" href="/version2/css/daterangepicker.css">
	<link rel="stylesheet" type="text/css" href="/version2/css/bootstrap-select.css">

  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-98999843-1', 'auto');
    ga('send', 'pageview');

  </script>
</head>

<body class="landing-page">

<div class="content-bg-wrap">
</div>


<!-- Landing Header -->

<div class="container">
	<div class="row">
		<div class="col-xl-12 col-lg-12 col-md-12">
			<div id="site-header-landing" class="header-landing">
				<a href="/" class="logo">
					<img src="/assets/logo_white_text.png" alt="Olympus" style="width:200px;margin-top:-5px;">

				</a>

				<ul class="profile-menu">
					<li>
						<a href="/about" style="color:white!important;">About Us</a>
					</li>
					<li>
						<a href="#" style="color:white!important;">Careers (Coming Soon)</a>
					</li>
					<li>
						<a href="/support" style="color:white!important;">Help &amp; Support</a>
					</li>
					<li>
						<a href="#" class="js-expanded-menu">
							<svg class="olymp-menu-icon"><use xlink:href="/version2/icons/icons.svg#olymp-menu-icon"></use></svg>
							<svg class="olymp-close-icon"><use xlink:href="/version2/icons/icons.svg#olymp-close-icon"></use></svg>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<!-- ... end Landing Header -->

<!-- Login-Registration Form  -->

<div class="container">
	<div class="row display-flex">
		<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="landing-content">
				<h1>Welcome to Altpocket.</h1>
				<p>We are the only tool you need for tracking, showcasing and manging your cryptocurrency investments.
          Now with a touch of socialization and Gamification.
				</p>
				<a href="#" class="btn btn-md btn-border c-white" style="background-color:white;">Register Now!</a>
			</div>
		</div>

		<div class="col-xl-5 col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="registration-login-form">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#home" role="tab">
							<svg class="olymp-login-icon"><use xlink:href="/version2/icons/icons.svg#olymp-login-icon"></use></svg>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#profile" role="tab">
							<svg class="olymp-register-icon"><use xlink:href="/version2/icons/icons.svg#olymp-register-icon"></use></svg>
						</a>
					</li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane" id="home" role="tabpanel" data-mh="log-tab">
						<div class="title h6">Register to Altpocket</div>
						<form class="content" role="form" method="POST" action="{{ route('register') }}" id="createaccountt">
							<div class="row">
                @if (count($errors))
                        @foreach($errors->all() as $error)
                <div class="col-xl-12 col-lg-12 col-md-12">
                  <div class="alert alert-danger" role="alert">
                    <strong>Oh snap!</strong>
                  </div>
                </div>
                        @endforeach
                @endif
                {{ csrf_field() }}
								<div class="col-xl-12 col-lg-12 col-md-12">
									<div class="form-group label-floating is-empty">
										<label class="control-label">Username</label>
										<input class="form-control" placeholder="" type="text" name="username">
									</div>
								</div>
								<div class="col-xl-12 col-lg-12 col-md-12">
									<div class="form-group label-floating is-empty">
										<label class="control-label">Email</label>
										<input class="form-control" placeholder="" type="email" name="email">
									</div>
									<div class="form-group label-floating is-empty">
										<label class="control-label">Password</label>
										<input class="form-control" placeholder="" type="password" name="password">
									</div>
                  <div class="form-group label-floating is-empty">
										<label class="control-label">Confirm Password</label>
										<input class="form-control" placeholder="" type="password" name="password_confirmation">
									</div>
									<div class="remember">
										<div class="checkbox">
											<label>
												<input name="optionsCheckboxes" type="checkbox">
												I accept the <a href="#">Terms and Conditions</a> of the website
											</label>
										</div>
									</div>

									<button type="submit" class="btn btn-lg full-width" style="color:white!important;background-color:#3a94ff">Complete Registration!</button>
								</div>
							</div>
						</form>
					</div>

					<div class="tab-pane active" id="profile" role="tabpanel" data-mh="log-tab">
						<div class="title h6">Login to your Account</div>
						<form class="content" style="margin-top:50px;" role="form" method="POST" action="{{ url('/login') }}">

              @if (count($errors))
                      @foreach($errors->all() as $error)
              <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="alert alert-danger" role="alert">
                  <strong>Oh snap!</strong> {{$error}}
                </div>
              </div>
                      @endforeach
              @endif
             {{ csrf_field() }}
							<div class="row">
								<div class="col-xl-12 col-lg-12 col-md-12">
									<div class="form-group label-floating is-empty">
										<label class="control-label">Username or Email</label>
										<input class="form-control" placeholder="" type="text" name="username">
									</div>
									<div class="form-group label-floating is-empty">
										<label class="control-label">Password</label>
										<input class="form-control" placeholder="" type="password" name="password">
									</div>

									<div class="remember">

										<div class="checkbox">
											<label>
												<input name="optionsCheckboxes" type="checkbox" value="0" name="remember">
												Remember Me
											</label>
										</div>
										<a href="/reset" class="forgot">Forgot my Password</a>
									</div>

									<button type="submit" class="btn btn-lg full-width" style="background-color:#3a94ff;color:white!important;">Login</button>


									<p>Don’t you have an account? <a href="#home" role="tab" aria-expanded="false" data-toggle="tab">Register Now!</a> it’s really simple and you can start enjoing all the benefits!</p>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- ... end Login-Registration Form  -->





<!-- jQuery first, then Other JS. -->
<script src="/version2/js/jquery-3.2.0.min.js"></script>
<!-- Js effects for material design. + Tooltips -->
<script src="/version2/js/material.min.js"></script>
<!-- Helper scripts (Tabs, Equal height, Scrollbar, etc) -->
<script src="/version2/js/theme-plugins.js"></script>
<!-- Init functions -->
<script src="/version2/js/main.js"></script>

<!-- Select / Sorting script -->
<script src="/version2/js/selectize.min.js"></script>

<!-- Swiper / Sliders -->
<script src="/version2/js/swiper.jquery.min.js"></script>

<!-- Datepicker input field script-->
<script src="/version2/js/moment.min.js"></script>
<script src="/version2/js/daterangepicker.min.js"></script>

<script src="/version2/js/mediaelement-and-player.min.js"></script>
<script src="/version2/js/mediaelement-playlist-plugin.min.js"></script>




</body>
</html>
