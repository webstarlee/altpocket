<!DOCTYPE html>
<html lang="en">
<head>

	<title>Altpocket - Redirecting... </title>

	<!-- Required meta tags always come first -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="/version3/Bootstrap/dist/css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="/version3/Bootstrap/dist/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="/version3/Bootstrap/dist/css/bootstrap-grid.css">

	<!-- Theme Styles CSS -->
	<link rel="stylesheet" type="text/css" href="/version3/css/theme-styles.css">
	<link rel="stylesheet" type="text/css" href="/version3/css/blocks.css">

	<!-- Main Font -->
	<script src="/version3/js/webfontloader.min.js"></script>
	<script>
		WebFont.load({
			google: {
				families: ['Roboto:300,400,500,700:latin']
			}
		});
	</script>

	<link rel="stylesheet" type="text/css" href="/version3/css/fonts.css">

	<!-- Styles for plugins -->
	<link rel="stylesheet" type="text/css" href="/version3/css/jquery.mCustomScrollbar.min.css">
	<link rel="stylesheet" type="text/css" href="/version3/css/swiper.min.css">
	<link rel="stylesheet" type="text/css" href="/version3/css/daterangepicker.css">
	<link rel="stylesheet" type="text/css" href="/version3/css/bootstrap-select.css">
  <link rel="icon"
    type="image/gif"
    href="/assets/logo.png">
</head>
<body class="body-bg-white">



<section class="medium-padding120">
	<div class="container">
		<div class="row">
			<div class="col-xl-6 m-auto col-lg-6 col-md-12 col-sm-12 col-xs-12">
				<div class="page-404-content">
					<img src="https://altpocket.io/assets/logo_blue_text.png"  style="width:80%" alt="photo">
					<div class="crumina-module crumina-heading align-center">
						<h2 class="h1 heading-title">Be <span class="c-primary" style="color:#3a94ff!important;">careful</span> out there, leaving altpocket in <span id="seconds">5</span> seconds..</h2>
						<p class="heading-text">We are about to redirect you to URL, if you don't want to wait you can click the url below to reach your endpoint.<br>
              <a href="{{app('request')->input('redirect')}}">{{app('request')->input('redirect')}}</a>
						</p>
					</div>

					<a href="/dashboard" class="btn btn-primary btn-lg" style="background-color:#3a94ff!important;border-color:#3a94ff!important;">Go to dashboard</a>
				</div>
			</div>
		</div>
	</div>
</section>



<!-- jQuery first, then Other JS. -->
<script src="/version3/js/jquery-3.2.0.min.js"></script>
<!-- Js effects for material design. + Tooltips -->
<script src="/version3/js/material.min.js"></script>
<!-- Helper scripts (Tabs, Equal height, Scrollbar, etc) -->
<script src="/version3/js/theme-plugins.js"></script>
<!-- Init functions -->
<script src="/version3/js/main.js"></script>

<!-- Select / Sorting script -->
<script src="/version3/js/selectize.min.js"></script>

<!-- Swiper / Sliders -->
<script src="/version3/js/swiper.jquery.min.js"></script>

<script src="/version3/js/mediaelement-and-player.min.js"></script>
<script src="/version3/js/mediaelement-playlist-plugin.min.js"></script>

<!-- Datepicker input field script-->
<script src="/version3/js/moment.min.js"></script>
<script src="/version3/js/daterangepicker.min.js"></script>
<script>
$(document).ready(function(){

      var counter = 0;
      var interval = setInterval(function() {
          counter++;
          $("#seconds").text(5 - counter);
          // Display 'counter' wherever you want to display it.
          if (counter == 5) {
              // Display a login box
              window.location.href = "{{app('request')->input('redirect')}}";
          }
      }, 1000);


})
</script>

</body>
</html>
