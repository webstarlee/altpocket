@extends('layouts.app2')

@section('title')
Data
@endsection

@php
  $data = Analytics::getAnalyticsService()->data_realtime->get('ga:156576021',  'rt:activeVisitors')->totalsForAllResults['rt:activeVisitors'];

@endphp



@section('content')
    <div id="content_wrapper" class="">
    <div id="header_wrapper" class="header-sm">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <header id="header">
                        <h1>Altpocket.io Stats</h1>
                    </header>
                </div>
            </div>
        </div>
    </div>
       <div id="content" class="container-fluid">
            <div class="content-body">

                                <div class="row">
                  <div class="col-xs-12 col-sm-6">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Registered users</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;">{{count(DB::table('users')->select('id')->get())}}</h1>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-6">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Registered Today</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;">{{count(DB::table('users')->select('id')->whereDate('created_at', DB::raw('CURDATE()'))->get())}}</h1>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 col-sm-6">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Current CPU usage</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;">
                          @php
                          $loads = sys_getloadavg();
                          $core_nums = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
                          $load = round($loads[0]/($core_nums + 1)*100, 2);
                          echo $load.'%';
                          @endphp
                        </h1>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-12 col-sm-6">
                    <div class="card" style="text-align:center;">
                      <header class="card-heading">
                        <h2 class="card-title">Users Online</h2>
                      </header>
                      <div class="card-body" style="font-size:40px;">
                        <h1 style="color:#73c04d;">{{$data}}</h1>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 col-sm-12">
                      <div class="card" style="text-align:center;">
                          <header class="card-heading">
                              <h2 class="card-title">Registered users Chart</h2>
                          </header>
                          <div class="card-body" style="font-size:40px;">
                              <div style="width:100%;height: 500px;" id="chartdiv"> </div>
                          </div>

                      </div>
                  </div>
                </div>
           </div>
        </div>


</div>



@endsection

@section('js')
  <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
  <script src="https://www.amcharts.com/lib/3/serial.js"></script>
  <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
  <script>
          getChart();
      function getChart() {
          $.ajax({
              type: 'GET',
              url: "https://altpocket.io/realtimechart",
              context: document.body,
              global: false,
              async:true,
              success: function(data) {
                  if(data.error == undefined) {
                      chart.dataProvider = data;
                      chart.validateData();
                  }else{
                      alert(data.error);
                  }
              }
          });
      }
      var chart = AmCharts.makeChart("chartdiv", {
          "type": "serial",
          "theme": "dark",
          "marginRight": 80,
          "valueAxes": [{
              "position": "left",
              "title": "Users"
          }],
          "graphs": [{
              "bulletSize": 14,
              "bulletField": "bullet",
              "descriptionField": "desc",
              "colorField": "bulletColor",
              "id": "g1",
              "fillAlphas": 0.4,
              "valueField": "Users",
              "balloonText": "<div style='margin:5px; font-size:19px;'>Users:<b>[[value]]</b></div>"
          }],
          "chartScrollbar": {
              "graph": "g1",
              "scrollbarHeight": 80,
              "backgroundAlpha": 0,
              "selectedBackgroundAlpha": 0.1,
              "selectedBackgroundColor": "#888888",
              "graphFillAlpha": 0,
              "graphLineAlpha": 0.5,
              "selectedGraphFillAlpha": 0,
              "selectedGraphLineAlpha": 1,
              "autoGridCount": true,
              "color": "#AAAAAA"
          },
          "chartCursor": {
              "categoryBalloonDateFormat": "JJ:NN, DD MMMM",
              "cursorPosition": "mouse"
          },
          "categoryField": "TimeStamp",
          "categoryAxis": {
              "minPeriod": "mm",
              "parseDates": true
          },
          "export": {
              "enabled": false,
              "dateFormat": "YYYY-MM-DD HH:NN:SS"
          }
      });
      chart.addListener("dataUpdated", zoomChart);
      function zoomChart() {
              chart.graphs[0].lineColor = "#4FC5EA";
              chart.zoomToIndexes(chart.dataProvider.length - 250, chart.dataProvider.length - 1);

      }
  </script>

@endsection
