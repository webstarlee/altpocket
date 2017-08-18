@extends('layouts.app')


@section('css')
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700%7CMaterial+Icons" media="all">
<style>
* {
    font-family: Roboto,sans-serif;
    font-weight: 400;
    line-height: 1.42857;
}

</style>
@endsection


@section('content')

  <div id="content_wrapper" class="">
  <div id="header_wrapper" class="header-sm">
      <div class="container-fluid">
          <div class="row">
              <div class="col-xs-12">
                  <header id="header">
                      <h1>Statistics</h1>
                  </header>
              </div>
          </div>
      </div>
  </div>

<div id="content" class="container-fluid">
  <div class="content-body">
    <div class="row">
      <div class="col-md-3">
        <div class="card">
          <header class="card-heading">
            <h2 class="card-title">Balance</h2>
            <ul class="card-actions icons right-top">
              <li>
                <a href="javascript:void(0)" data-toggle="refresh">
                  <i class="zmdi zmdi-plus-circle"></i>
                </a>
              </li>
            </ul>
          </header>
          <div class="card-body" style="color:white!important;">
            <h1 style="text-align:center;font-size:48px;color:white;">$ 106,994.64</h1>
            <p style="text-align:center;">Your portfolio is composed of <span style="font-size:24px;color:#227fd0;font-weight:600;">3</span> coins.
            <div class="row">
              <div class="col-md-3">
                <canvas id="donut" width="50" height="50"></canvas>
              </div>
              <div class="col-md-5">
                <p>ETH</p>
                <p style="margin-top:-20px;font-size:20px;font-weight:600;">1,273.714</p>
              </div>

              <div class="col-md-4">
                <p style="margin-top:20px;font-weight:600;font-size:18px;">$85,669.99</p>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <canvas id="donut2" width="50" height="50"></canvas>
              </div>
              <div class="col-md-5">
                <p>GNT</p>
                <p style="margin-top:-20px;font-size:20px;font-weight:600;">100,452.99</p>
              </div>

              <div class="col-md-4">
                <p style="margin-top:20px;font-weight:600;font-size:18px;">$14,254.28</p>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3">
                <canvas id="donut3" width="50" height="50"></canvas>
              </div>
              <div class="col-md-5">
                <p>MUN</p>
                <p style="margin-top:-20px;font-size:20px;font-weight:600;">208.997</p>
              </div>

              <div class="col-md-4">
                <p style="margin-top:20px;font-weight:600;font-size:18px;">$7,070.37</p>
              </div>
            </div>




          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <header class="card-heading">
            <h2 class="card-title">Benchmark</h2>
            <ul class="card-actions icons right-top">
              <li>
                <a href="javascript:void(0)" data-toggle="refresh">
                  <i class="zmdi zmdi-plus-circle"></i>
                </a>
              </li>
            </ul>
          </header>
          <div class="card-body" style="color:white!important;">
            <div id="c3_spline-chart"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>


@endsection

@section('js')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
  <script>
  $(document).ready(function () {
    var donutEl = document.getElementById("donut").getContext("2d");
    var donut = new Chart(donutEl).Doughnut(
    	// Datas
    	[
    		{
    			value: 80,
    			color:"#9634d1",
    			highlight: "#3454d1"
    		},
    		{
    			value: 100-80,
    			color: "#444444",
    			highlight: "#5AD3D1"
    		},
    	],
    	// Options
    	{
    		segmentShowStroke : false,
        showTooltips: false,
    		segmentStrokeColor : "#fff",
    		segmentStrokeWidth : 2,
    		percentageInnerCutout : 60,
    		animationSteps : 100,
    		animationEasing : "easeOutBounce",
    		animateRotate : true,
    		animateScale : false,
    		responsive: false,
    		maintainAspectRatio: false,
    		showScale: true,
    		animateScale: true
    	});
  var donutE2 = document.getElementById("donut2").getContext("2d");
  var donut2 = new Chart(donutE2).Doughnut(
    // Datas
    [
      {
        value: 15,
        color:"#63c544",
        highlight: "#3454d1"
      },
      {
        value: 100-10,
        color: "#444444",
        highlight: "#5AD3D1"
      },
    ],
    // Options
    {
      segmentShowStroke : false,
      showTooltips: false,
      segmentStrokeColor : "#fff",
      segmentStrokeWidth : 2,
      percentageInnerCutout : 60,
      animationSteps : 100,
      animationEasing : "easeOutBounce",
      animateRotate : true,
      animateScale : false,
      responsive: false,
      maintainAspectRatio: false,
      showScale: true,
      animateScale: true
    });

  var donutE3 = document.getElementById("donut3").getContext("2d");
  var donut3 = new Chart(donutE3).Doughnut(
    // Datas
    [
      {
        value: 10,
        color:"#5894e0",
        highlight: "#3454d1"
      },
      {
        value: 100-10,
        color: "#444444",
        highlight: "#5AD3D1"
      },
    ],
    // Options
    {
      segmentShowStroke : false,
      showTooltips: false,
      segmentStrokeColor : "#fff",
      segmentStrokeWidth : 2,
      percentageInnerCutout : 60,
      animationSteps : 100,
      animationEasing : "easeOutBounce",
      animateRotate : true,
      animateScale : false,
      responsive: false,
      maintainAspectRatio: false,
      showScale: true,
      animateScale: true
    });
  });
  </script>
@endsection
