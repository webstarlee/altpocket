<!DOCTYPE html>
<html lang="en">
<head>

	<title>Altpocket - Validate 2FA</title>

	<!-- Required meta tags always come first -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<!-- Main Font -->
	<script src="/version2/js/webfontloader.min.js"></script>
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
	<link rel="stylesheet" type="text/css" href="/version2/css/blocks.css?v=1.5">
	<link rel="stylesheet" type="text/css" href="/version2/css/fonts.css">

	<!-- Styles for plugins -->
	<link rel="stylesheet" type="text/css" href="/version2/css/jquery.mCustomScrollbar.min.css">
	<link rel="stylesheet" type="text/css" href="/version2/css/daterangepicker.css">
	<link rel="stylesheet" type="text/css" href="/version2/css/bootstrap-select.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-98999843-1', 'auto');
    ga('send', 'pageview');

  </script>
	<style>
		.form-group2 {
			margin-bottom:10px!important;
		}

	</style>
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
		<div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-xs-12">

		</div>

		<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12" style="margin-top:150px;">
			<div class="registration-login-form" style="padding-left:0px!important;min-height:0;">

				<!-- Tab panes -->
				<div class="tab-content">
					<div class="tab-pane active" id="profile" role="tabpanel" data-mh="log-tab">
						<div class="title h6">Validate 2FA</div>
						<form class="content" role="form" method="POST" action="/2fa/validate" style="text-align:center;">
            <p>Your account is proteted by 2FA, enter the 2FA code provided by your application.</p>
            <br /><br />
             {{ csrf_field() }}							<div class="row">
								<div class="col-xl-12 col-lg-12 col-md-12">
									<div class="form-group2 label-floating is-empty">
										<label class="control-label">2FA Code</label>
										<input class="form-control2" placeholder="" type="text" name="totp" required>
									</div>

									<button type="submit" class="btn btn-lg full-width" style="background-color:#3a94ff;color:white!important;">Validate</button>
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
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.min.js"></script>
@include('sweet::alert')



</body>
</html>
