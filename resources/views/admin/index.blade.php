@extends('layouts.admin');

@section('title', 'Admin | Dashboard')

@section('content')
  <div class="row">
    <div class="col-md-4">
      <div class="m-portlet m-portlet--border-bottom-danger ">
        <div class="m-portlet__body">
          <div class="m-widget26">
            <div class="m-widget26__number">
              {{$users}}
              <small>
                Registered Users
              </small>
            </div>
            <div class="m-widget26__chart" style="height:90px; width: 220px;">
              <canvas id="users_chart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="m-portlet m-portlet--border-bottom-info ">
        <div class="m-portlet__body">
          <div class="m-widget26">
            <div class="m-widget26__number">
              {{$today}}
              <small>
                Registered Today
              </small>
            </div>
            <div class="m-widget26__chart" style="height:90px; width: 220px;">
              <canvas id="users_today"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
        <div class="m-portlet m-portlet--border-bottom-success ">
          <div class="m-portlet__body">
            <div class="m-widget26">
              <div class="m-widget26__number">
                {{$online}}
                <small>
                  Users Online
                </small>
              </div>
              <div class="m-widget26__chart" style="height:90px; width: 220px;">
                <canvas id="users_online"></canvas>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-6">
				<!--begin:: Widgets/Support Tickets -->
				<div class="m-portlet">
					<div class="m-portlet__head">
						<div class="m-portlet__head-caption">
							<div class="m-portlet__head-title">
								<h3 class="m-portlet__head-text">
									Latest Questions
								</h3>
							</div>
						</div>
						<div class="m-portlet__head-tools">
							<ul class="m-portlet__nav">
								<li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
									<a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl m-dropdown__toggle">
										<i class="la la-ellipsis-h m--font-brand"></i>
									</a>
									<div class="m-dropdown__wrapper">
										<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
										<div class="m-dropdown__inner">
											<div class="m-dropdown__body">
												<div class="m-dropdown__content">
													<ul class="m-nav">
														<li class="m-nav__item">
															<a href="/support" class="m-nav__link">
																<i class="m-nav__link-icon flaticon-lifebuoy"></i>
																<span class="m-nav__link-text">
																	Support
																</span>
															</a>
														</li>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<div class="m-portlet__body">
						<div class="m-widget3">
              @foreach($questions as $question)
                @php
                  $user = $question->getPoster();
                @endphp
							<div class="m-widget3__item">
								<div class="m-widget3__header">
									<div class="m-widget3__user-img">
                    @if($user->avatar != "default.jpg")
										<img class="m-widget3__img" src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="">
                  @else
										<img class="m-widget3__img" src="https://altpocket.io/assets/img/default.png" alt="">
                  @endif
									</div>
									<div class="m-widget3__info">
										<span class="m-widget3__username">
											<a href="/user/{{$user->username}}" style="text-decoration:none;color:inherit;" target="_blank">{{$user->username}}</a>
										</span>
										<br>
										<span class="m-widget3__time">
											{{$question->created_at->diffForHumans()}}
										</span>
									</div>
									<span class="m-widget3__status m--font-info">
                    @if($question->tag != "Resolved")
  										<a href="/question/{{$question->id}}" target="_blank" style="text-decoration:none;color:inherit;">Pending</a>
                    @else
                      <a href="/question/{{$question->id}}" target="_blank" class="m--font-success" style="text-decoration:none;color:inherit;">Answered</a>
                    @endif
									</span>
								</div>
								<div class="m-widget3__body">
									<p class="m-widget3__text">
										{{substr(strip_tags($question->question), 0, 150)}}..
									</p>
								</div>
							</div>
            @endforeach
						</div>
					</div>
				</div>
				<!--end:: Widgets/Support Tickets -->
			</div>

      <div class="col-xl-6">
								<!--begin:: Widgets/Authors Profit-->
								<div class="m-portlet m-portlet--bordered-semi">
									<div class="m-portlet__head">
										<div class="m-portlet__head-caption">
											<div class="m-portlet__head-title">
												<h3 class="m-portlet__head-text">
													Top Performing Staff
												</h3>
											</div>
										</div>
									</div>
									<div class="m-portlet__body">
										<div class="m-widget4">
                      @foreach($answers as $answer)
                        @php
                          $user = $answer->getPoster();
                        @endphp
  											<div class="m-widget4__item">
  												<div class="m-widget4__img m-widget4__img--logo">
  													<img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="">
  												</div>
  												<div class="m-widget4__info">
  													<span class="m-widget4__title">
  														{{$user->username}}
  													</span>
  													<br>
  													<span class="m-widget4__sub">

  													</span>
  												</div>
  												<span class="m-widget4__ext">
  													<span class="m-widget4__number m--font-brand" style="    display: block;
    width: 200px;
    float: right;
    text-align: end;">
  														{{$answer->amount}} Answers
  													</span>
  												</span>
  											</div>
                      @endforeach
										</div>
									</div>
								</div>
								<!--end:: Widgets/Authors Profit-->
							</div>
						</div>


@endsection

@section('js')
<script>
var _initSparklineChart = function(src, data, color, border) {
    if (src.length == 0) {
        return;
    }

    var config = {
        type: 'line',
        data: {
            labels: [@foreach($userchart as $count) "{{$count}}", @endforeach],
            datasets: [{
                label: "",
                borderColor: color,
                borderWidth: border,

                pointHoverRadius: 4,
                pointHoverBorderWidth: 12,
                pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                pointHoverBackgroundColor: mUtil.getColor('danger'),
                pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),
                fill: false,
                data: data,
                spanGaps: true
            }]
        },
        options: {
            title: {
                display: false,
            },
            tooltips: {
                enabled: false,
                intersect: false,
                mode: 'nearest',
                xPadding: 10,
                yPadding: 10,
                caretPadding: 10
            },
            legend: {
                display: false,
                labels: {
                    usePointStyle: false
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            hover: {
                mode: 'index'
            },
            scales: {
                xAxes: [{
                    display: false,
                    gridLines: false,
                    scaleLabel: {
                        display: false,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: false,
                    gridLines: false,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    },
                    ticks: {
                        beginAtZero: false
                    }
                }]
            },

            elements: {
              point: {
                  radius: 1,
                  borderWidth: 12
              },
            },

            layout: {
                padding: {
                    left: 0,
                    right: 0,
                    top: 10,
                    bottom: 0
                }
            }
        }
    };

    return new Chart(src, config);
}


_initSparklineChart($('#users_chart'), [@foreach($userchart as $count) {{$count}}, @endforeach], mUtil.getColor('danger'), 3);
_initSparklineChart($('#users_today'), [@foreach($userstoday as $count) {{$count}}, @endforeach], mUtil.getColor('info'), 3);
_initSparklineChart($('#users_online'), [@foreach($last24 as $count) {{$count['visitors']}}, @endforeach], mUtil.getColor('success'), 3);
</script>

@endsection
