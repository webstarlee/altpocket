@extends('layouts.admin');

@section('title', 'We need your help')

@php
  $ip = geoip()->getClientIP();
  $location = geoip()->getLocation($ip);
  $country = $location['iso_code'];
@endphp



@section('css')
<style>
.m-pricing-table-1 .m-pricing-table-1__items .m-pricing-table-1__item:nth-child(1) .m-pricing-table-1__price {
  font-size: 3rem;
     margin-bottom: 0px!important;
     margin-top: 20rem;
}

</style>

@endsection


@section('content')

  <div class="m-portlet">
  									<div class="m-portlet__head">
  										<div class="m-portlet__head-caption">
  											<div class="m-portlet__head-title">
  												<span class="m-portlet__head-icon">
  													<i class="la la-heart-o"></i>
  												</span>
  												<h3 class="m-portlet__head-text">
  													We need your help
  													<small>
  														We need your help to take Altpocket to the next level.
  													</small>
  												</h3>
  											</div>
  										</div>
  									</div>
  									<div class="m-portlet__body" style="text-align:center;">
                      <h2>Merry Christmas!</h2>
                      <br>
                      <div style="padding-left:50px;padding-right:50px;">
                        <p>Thank you for the recent raise we did together, much love from us at Altpocket.</p>
                        <p>Due to a larger demand, we have decided to re-open the pre-sale of our Lifetime Premium (Schedueled to release around Q1 2018), however due to the amount of lifetime premium users we currently have and the current instability in the crypto market, we have increased it's price by $50.</p>
                        <p>Merry Chritmas everyone!</p>
                      </div>
                        <br>
                        <h5 style="text-align:left;">What will you get for helping us out? *</h5>
                        <br>
                        <ul style="text-align:left;">
                          <li style="margin-bottom:5px;">A Premium Membership subscription based on your contribution.
                          </li>
                          <li style="margin-bottom:5px;">A place in our <a href="/hall-of-fame" target="_blank">Hall of Fame</a>.
                          </li>
                          <li style="margin-bottom:5px;">An opportunity to decide how Altpocket should be developed through our ”Governance” system - where every dollar pledged gives you one vote.
                          </li>
                          <li style="margin-bottom:5px;">Everyone will receive a rank reflecting their contribution.
                          </li>
                          <li style="margin-bottom:5px;">Everyone who pledges $50 or more will receive a special badge reflecting the contribution.
                          </li>
                          <li style="margin-bottom:5px;">Everyone who pledges $50 or more will receive the opportunity to test out new features on Altpocket before others.
                          </li>
                          <li style="margin-bottom:5px;">Everyone who pledges $150 will be included in our exclusive chat dedicated to Altpocket development.
                          </li>
                          <li style="margin-bottom:5px;">Everyone who pledges $150 dollars will receive an exclusive, one-time-only Lifetime Premium Membership to Altpocket.
                          </li>
                        </ul>
                        <br>
                        <p style="text-align:left;">* Everyone who has previously pledged to Altpocket will automatically receive the features they would receive if their pledge took place today.</p>
                          <div class="row">
                            <button id="takeme" type="button" class="btn btn-success" style="display:block;margin:0 auto;margin-top:25px;">
															Take me to purchase
														</button>
                          </div>
  									</div>
  									<div class="m-portlet__foot m--hide">
  										<div class="row">
  											<div class="col-lg-6">
  												Portlet footer:
  											</div>
  											<div class="col-lg-6">
  												<button type="submit" class="btn btn-primary">
  													Submit
  												</button>
  												<span class="m--margin-left-10">
  													or
  													<a href="#" class="m-link m--font-bold">
  														Cancel
  													</a>
  												</span>
  											</div>
  										</div>
  									</div>
  								</div>
<div class="row">
  <div class="col-lg-12">
  <div class="m-portlet">
		<div class="m-portlet__head">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<span class="m-portlet__head-icon">
						<i class="la la-heart-o"></i>
					</span>
					<h3 class="m-portlet__head-text">
						Progress
					</h3>
				</div>
			</div>
		</div>
		<div class="m-portlet__body">
      <!--<div class="progress  m--bg-brand" style="margin-top:150px;width:50%;margin:0 auto;">
        <div class="progress-bar m--bg-success" role="progressbar" style="width: {{$raised / 24000 * 100}}%; height: 30px;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <div class="row" style="text-align:middle;margin-top:15px;">
        <div class="col-md-3">
        </div>
        <div class="col-md-2" style="text-align:center">
          <strong style="font-size:22px;">${{$raised}}</strong><br>
          <span>Raised</span>
        </div>
        <div class="col-md-2" style="text-align:center">
          <strong style="font-size:22px;">{{$backers}}</strong><br>
          <span>Backers</span>
        </div>
        <div class="col-md-2" style="text-align:center">
          <strong style="font-size:22px;">$12000</strong><br>
          <span>Goal</span>
        </div>
      </div>
      <br>
      <br>-->
      <div style="padding-left:50px;padding-right:50px;">
        <div class="alert alert-info" role="alert">
        												<strong>
        													Merry Christmas!<br>
        												</strong>
        												For a limited time we will open up sales of the lifetime premium at an increased price due to the amount of lifetimes we currently have, we hope you understand and as always thank you all for the amazing support!
        											</div>
      </div>
    </div>
  </div>
</div>
</div>
                  <div class="row">
                    <div class="col-lg-4">
                      <div class="m-portlet ">
                  									<div class="m-portlet__head">
                  										<div class="m-portlet__head-caption">
                  											<div class="m-portlet__head-title">
                  												<h3 class="m-portlet__head-text">
                  													Recent Supporters
                  												</h3>
                  											</div>
                  										</div>
                  									</div>
                  									<div class="m-portlet__body">
                  										<div class="tab-content">
                                        <div class="tab-pane active" id="m_widget4_tab1_content">
                  												<!--begin::Widget 14-->
                  												<div class="m-widget4">
                                            @foreach($donations as $donation)
                                              @php
                                                $user = $donation->getDonator();
                                              @endphp
                    													<div class="m-widget4__item">
                    														<div class="m-widget4__img m-widget4__img--pic">
                                                  @if($user->avatar != "default.jpg")
                                                    <img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="">
                                                  @else
                                                    <img src="https://altpocket.io/assets/img/default.png" alt="">
                                                  @endif
                    														</div>
                    														<div class="m-widget4__info">
                    															<span class="m-widget4__title">
                    																<a href="/user/{{$user->username}}" style="text-align:center;color:black;">{{$user->username}}</a>
                    															</span>
                    															<br>
                    															<span class="m-widget4__sub">
                    															  ${{number_format($donation->amount1, 2)}}
                    															</span>
                    														</div>
                    													</div>
                                            @endforeach
                  												</div>
                  												<!--end::Widget 14-->
                  											</div>
                  										</div>
                  									</div>
                  								</div>
                              </div>
                                        <div class="col-lg-4">
                                          <div class="m-portlet ">
                          									<div class="m-portlet__head">
                          										<div class="m-portlet__head-caption">
                          											<div class="m-portlet__head-title">
                          												<h3 class="m-portlet__head-text">
                          													Upcoming Premium Features
                          												</h3>
                          											</div>
                          										</div>
                          									</div>
                          									<div class="m-portlet__body">
                          										<!--begin::widget 12-->
                          										<div class="m-widget4">
                          											<div class="m-widget4__item">
                          												<div class="m-widget4__ext">
                          													<span class="m-widget4__icon m--font-brand">
                          														<i class="la la-cloud-download"></i>
                          													</span>
                          												</div>
                          												<div class="m-widget4__info">
                          													<span class="m-widget4__text">
                          														Automatic Importing
                          													</span>
                          												</div>
                          											</div>
                          											<div class="m-widget4__item">
                          												<div class="m-widget4__ext">
                          													<span class="m-widget4__icon m--font-brand">
                          														<i class="la la-list"></i>
                          													</span>
                          												</div>
                          												<div class="m-widget4__info">
                          													<span class="m-widget4__text">
                          														Multiple Portfolios
                          													</span>
                          												</div>
                          											</div>
                          											<div class="m-widget4__item">
                          												<div class="m-widget4__ext">
                          													<span class="m-widget4__icon m--font-brand">
                          														<i class="la la-line-chart"></i>
                          													</span>
                          												</div>
                          												<div class="m-widget4__info">
                          													<span class="m-widget4__text">
                          														Deep Analytic Features
                          													</span>
                          												</div>
                          											</div>
                          											<div class="m-widget4__item">
                          												<div class="m-widget4__ext">
                          													<span class="m-widget4__icon m--font-brand">
                          														<i class="la la-list-ol"></i>
                          													</span>
                          												</div>
                          												<div class="m-widget4__info">
                          													<span class="m-widget4__text">
                          														Multiple API keys per Exchange
                          													</span>
                          												</div>
                          											</div>
                                                <div class="m-widget4__item m-widget4__item-border">
                          												<div class="m-widget4__ext">
                          													<span class="m-widget4__icon m--font-brand">
                          														<i class="la la-mobile"></i>
                          													</span>
                          												</div>
                          												<div class="m-widget4__info">
                          													<span class="m-widget4__text">
                          														Premium Mobile Features
                          													</span>
                          												</div>
                          											</div>
                                                <div class="m-widget4__item m-widget4__item-border">
                                                  <div class="m-widget4__ext">
                                                    <span class="m-widget4__icon m--font-brand">
                                                      <i class="la la-bell"></i>
                                                    </span>
                                                  </div>
                                                  <div class="m-widget4__info">
                                                    <span class="m-widget4__text">
                                                      Investment Alerts
                                                    </span>
                                                  </div>
                                                </div>
                                                <div class="m-widget4__item m-widget4__item-border">
                                                  <div class="m-widget4__ext">
                                                    <span class="m-widget4__icon m--font-brand">
                                                      <i class="la la-user"></i>
                                                    </span>
                                                  </div>
                                                  <div class="m-widget4__info">
                                                    <span class="m-widget4__text">
                                                      Animated Avatars
                                                    </span>
                                                  </div>
                                                </div>
                                                <div class="m-widget4__item m-widget4__item-border">
                                                  <div class="m-widget4__ext">
                                                    <span class="m-widget4__icon m--font-brand">
                                                      <i class="la la-ellipsis-h"></i>
                                                    </span>
                                                  </div>
                                                  <div class="m-widget4__info">
                                                    <span class="m-widget4__text">
                                                      And much more!
                                                    </span>
                                                  </div>
                                                </div>
                          										</div>
                          										<!--end::Widget 12-->
                          									</div>
                          								</div>
                                        </div>
                                        <div class="col-md-4">
                                          <div class="m-portlet ">
                          									<div class="m-portlet__head">
                          										<div class="m-portlet__head-caption">
                          											<div class="m-portlet__head-title">
                          												<h3 class="m-portlet__head-text">
                          													Roadmap
                          												</h3>
                          											</div>
                          										</div>
                          									</div>
                          									<div class="m-portlet__body">
                          										<div class="tab-content">
                          											<div class="tab-pane active" id="m_widget4_tab1_content">
                          												<div class="m-scrollable" data-scrollable="true" data-max-height="440" style="height: 400px; overflow: hidden;">
                          													<div class="m-list-timeline m-list-timeline--skin-light">
                          														<div class="m-list-timeline__items">
                                                        <div class="m-list-timeline__item">
                          																<span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
                          																<span class="m-list-timeline__text">
                          																	New Portfolio System
                          																</span>
                          																<span class="m-list-timeline__time">
                          																	Q4 2017
                          																</span>
                          															</div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
                                                          <span class="m-list-timeline__text">
                                                            Binance/HitBTC/Kraken Import
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            Q4 2017
                                                          </span>
                                                        </div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
                                                          <span class="m-list-timeline__text">
                                                            New Profile Page
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            Q4 2017
                                                          </span>
                                                        </div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
                                                          <span class="m-list-timeline__text">
                                                            New Dashboard
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            Q4 2017
                                                          </span>
                                                        </div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
                                                          <span class="m-list-timeline__text">
                                                            Social Functionalities
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            Q4 2017
                                                          </span>
                                                        </div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--success"></span>
                                                          <span class="m-list-timeline__text">
                                                            Altpocket 2.0
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            Q4 2018
                                                          </span>
                                                        </div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--info"></span>
                                                          <span class="m-list-timeline__text">
                                                            Premium Launch
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            Q1 2018
                                                          </span>
                                                        </div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--info"></span>
                                                          <span class="m-list-timeline__text">
                                                            ICO Tracking
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            Q1 2018
                                                          </span>
                                                        </div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--info"></span>
                                                          <span class="m-list-timeline__text">
                                                            Altpocket API
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            Q1 2018
                                                          </span>
                                                        </div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--info"></span>
                                                          <span class="m-list-timeline__text">
                                                            Mobile Application
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            Q2 2018
                                                          </span>
                                                        </div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--info"></span>
                                                          <span class="m-list-timeline__text">
                                                            On-Site Trading
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            Q3-Q4 2018
                                                          </span>
                                                        </div>
                                                        <div class="m-list-timeline__item">
                                                          <span class="m-list-timeline__badge m-list-timeline__badge--info"></span>
                                                          <span class="m-list-timeline__text">
                                                            And much more
                                                          </span>
                                                          <span class="m-list-timeline__time">
                                                            TBA
                                                          </span>
                                                        </div>
                          														</div>
                          													</div>
                          												</div>
                          											</div>
                          											<div class="tab-pane" id="m_widget4_tab2_content"></div>
                          											<div class="tab-pane" id="m_widget4_tab3_content"></div>
                          										</div>
                          									</div>
                          								</div>
                                        </div>
                            </div>
  <div class="m-portlet" id="tiers">
  							<div class="m-portlet__head">
  								<div class="m-portlet__head-caption">
  									<div class="m-portlet__head-title">
  										<span class="m-portlet__head-icon">
  											<i class="la la-space-shuttle"></i>
  										</span>
  										<h3 class="m-portlet__head-text">
  											Select your tier
  										</h3>
  									</div>
  								</div>
  							</div>
  							<div class="m-portlet__body">
  								<div class="m-pricing-table-1 m-pricing-table-1--fixed">
  									<div class="m-pricing-table-1__items row">
                      <div class="m-pricing-table-1__item col-lg-4">
  											<div class="m-pricing-table-1__visual">
  												<div class="m-pricing-table-1__hexagon1"></div>
  												<div class="m-pricing-table-1__hexagon2"></div>
  												<span class="m-pricing-table-1__icon m--font-brand">
  													<i class="fa flaticon-gift"></i>
  												</span>
  											</div>
  											<span class="m-pricing-table-1__price">
  												9.99
  												<span class="m-pricing-table-1__label">
  													$
  												</span>
  											</span>
  											<h2 class="m-pricing-table-1__subtitle">
  												1 Month of Premium & Early Bird Rank
  											</h2>
  											<span class="m-pricing-table-1__description">
                          Listed on <a href="/hall-of-fame" target="_blank">Hall Of Fame</a><br>
                          All future Premium Features<br>
                          <br>
                          <br>
                          <br>
  											</span>
  											<div class="m-pricing-table-1__btn">
                          <button type="button" class="btn m-btn--pill  m-btn--metal m-btn--wide m-btn--uppercase m-btn--bolder m-btn--sm btn-purchase">
  													Closed
  												</button>
  											</div>
  										</div>
  										<div class="m-pricing-table-1__item col-lg-4">
  											<div class="m-pricing-table-1__visual">
  												<div class="m-pricing-table-1__hexagon1"></div>
  												<div class="m-pricing-table-1__hexagon2"></div>
  												<span class="m-pricing-table-1__icon m--font-brand">
  													<i class="fa flaticon-piggy-bank"></i>
  												</span>
  											</div>
  											<span class="m-pricing-table-1__price">
  												50
  												<span class="m-pricing-table-1__label">
  													$
  												</span>
  											</span>
  											<h2 class="m-pricing-table-1__subtitle">
  												Half a year of Premium & Backer Rank
  											</h2>
  											<span class="m-pricing-table-1__description">
                          Listed on <a href="/hall-of-fame" target="_blank">Hall Of Fame</a><br>
                          6 Months Premium <br>
                          Ability to test new features before release<br>
                          Special Award based on the contribution<br>
                          <br>
  											</span>
  											<div class="m-pricing-table-1__btn">
  												<button type="button" class="btn m-btn--pill  m-btn--metal m-btn--wide m-btn--uppercase m-btn--bolder m-btn--sm btn-purchase">
  													Closed
  												</button>
  											</div>
  										</div>
  										<div class="m-pricing-table-1__item col-lg-4">
  											<div class="m-pricing-table-1__visual">
  												<div class="m-pricing-table-1__hexagon1"></div>
  												<div class="m-pricing-table-1__hexagon2"></div>
  												<span class="m-pricing-table-1__icon m--font-brand">
  													<i class="fa flaticon-gift"></i>
  												</span>
  											</div>
  											<span class="m-pricing-table-1__price">
  												150+
  												<span class="m-pricing-table-1__label">
  													$
  												</span>
  											</span>
  											<h2 class="m-pricing-table-1__subtitle">
  												Lifetime Premium & Backer Rank
  											</h2>
  											<span class="m-pricing-table-1__description">
                          Listed on <a href="/hall-of-fame" target="_blank">Hall Of Fame</a><br>
                          Access to Altpockets Roadmap and coming features<br>
                          Access to Exclusive Chat with Altpocket Core Team<br>
                          Special Award based on the contribution<br>
  											</span>
  											<div class="m-pricing-table-1__btn">
                          <button type="button" class="btn m-btn--pill  btn-success m-btn--wide m-btn--uppercase m-btn--bolder m-btn--sm btn-purchase" data-amount="150" data-toggle="modal" data-target="#m_modal_1">
  													Purchase
  												</button>
  											</div>
  										</div>
  									</div>
  								</div>
  							</div>
  						</div>


              <div class="modal fade" id="m_modal_1" role="dialog" aria-labelledby="exampleModalLabel"  style="display: none;">
    							<div class="modal-dialog" role="document">
    								<div class="modal-content">
    									<div class="modal-header">
    										<h5 class="modal-title" id="exampleModalLabel">
    											Contribute $150 or more
    										</h5>
    										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
    											<span aria-hidden="true">
    												×
    											</span>
    										</button>
    									</div>
    									<div class="modal-body">
                        <div class="form-group m-form__group row">
                            <label class="col-form-label col-lg-12 col-sm-12">
                              Select Amount to Contribute in $
                            </label>
      										<div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="input-group">
    													<span class="input-group-addon">
    														$
    													</span>
    													<input type="number" id="amount" class="form-control m-input" value="" aria-label="Amount (to the nearest dollar)">
    												</div>
                            <span class="m-form__help" style="color: #7b7e8a;font-weight: 300;font-size: 0.85rem;padding-top: 7px;">
                              Any amount between $9.99 and $150 is allowed, however any amount above $150 is also accepted, there will be a <a href="/hall-of-fame" target="_blank">Hall of Fame</a> for users who donated $150+!
                            </span>
                          </div>
                          </div>

                        <br>
                        <div class="form-group m-form__group row">
        										<label class="col-form-label col-lg-12 col-sm-12">
        											Select Country of Residence
        										</label>
        										<div class="col-lg-12 col-md-12 col-sm-12">
        											<select class="form-control m-select2" id="m_select_country" name="country_select" style="width:100%">
        											</select>
                              <span class="m-form__help" style="color: #7b7e8a;font-weight: 300;font-size: 0.85rem;padding-top: 7px;">
                                This is used by us when accounting.
                              </span>
        										</div>
        									</div>
                          <br>
                          <br>
                          <form action="https://www.coinpayments.net/index.php" method="post" style="text-align:center;">
                            <input type="hidden" name="cmd" value="_donate">
                            <input type="hidden" name="reset" value="1">
                            <input type="hidden" name="merchant" value="c386109d2fec3ae0327d66e05f705754">
                            <input type="hidden" name="item_name" value="Pre-Purchase Premium">
                            <input type="hidden" name="currency" value="USD">
                            <input type="hidden" name="amountf" value="5">
                            <input type="hidden" name="custom" value="{{$country}}">
                            <input type="hidden" name="allow_amount" value="1">
                            <input type="hidden" name="want_shipping" value="0">
                            <input type="hidden" name="item_number" value="@if(Auth::user()){{Auth::user()->id}}@else{{1-1}}@endif">
                            <input type="hidden" name="ipn_url" value="https://altpocket.io/donate/post">
                            <input type="hidden" name="allow_extra" value="1">
                            <input type="hidden" name="country" value="{{$country}}">
                            <input type="hidden" name="email" value="{{Auth::user()->email}}">
                            <button class="btn btn-success">Pre-Purchase Premium<div class="ripple-container"></div></button>
                          </form>
    									</div>
    								</div>
    							</div>
    						</div>
@endsection


@section('js')
<script src="/panel/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>


<script>
// Select country
(function($) {
        $(function() {
            var isoCountries = [
                { id: 'AF', text: 'Afghanistan'},
                { id: 'AX', text: 'Aland Islands'},
                { id: 'AL', text: 'Albania'},
                { id: 'DZ', text: 'Algeria'},
                { id: 'AS', text: 'American Samoa'},
                { id: 'AD', text: 'Andorra'},
                { id: 'AO', text: 'Angola'},
                { id: 'AI', text: 'Anguilla'},
                { id: 'AQ', text: 'Antarctica'},
                { id: 'AG', text: 'Antigua And Barbuda'},
                { id: 'AR', text: 'Argentina'},
                { id: 'AM', text: 'Armenia'},
                { id: 'AW', text: 'Aruba'},
                { id: 'AU', text: 'Australia'},
                { id: 'AT', text: 'Austria'},
                { id: 'AZ', text: 'Azerbaijan'},
                { id: 'BS', text: 'Bahamas'},
                { id: 'BH', text: 'Bahrain'},
                { id: 'BD', text: 'Bangladesh'},
                { id: 'BB', text: 'Barbados'},
                { id: 'BY', text: 'Belarus'},
                { id: 'BE', text: 'Belgium'},
                { id: 'BZ', text: 'Belize'},
                { id: 'BJ', text: 'Benin'},
                { id: 'BM', text: 'Bermuda'},
                { id: 'BT', text: 'Bhutan'},
                { id: 'BO', text: 'Bolivia'},
                { id: 'BA', text: 'Bosnia And Herzegovina'},
                { id: 'BW', text: 'Botswana'},
                { id: 'BV', text: 'Bouvet Island'},
                { id: 'BR', text: 'Brazil'},
                { id: 'IO', text: 'British Indian Ocean Territory'},
                { id: 'BN', text: 'Brunei Darussalam'},
                { id: 'BG', text: 'Bulgaria'},
                { id: 'BF', text: 'Burkina Faso'},
                { id: 'BI', text: 'Burundi'},
                { id: 'KH', text: 'Cambodia'},
                { id: 'CM', text: 'Cameroon'},
                { id: 'CA', text: 'Canada'},
                { id: 'CV', text: 'Cape Verde'},
                { id: 'KY', text: 'Cayman Islands'},
                { id: 'CF', text: 'Central African Republic'},
                { id: 'TD', text: 'Chad'},
                { id: 'CL', text: 'Chile'},
                { id: 'CN', text: 'China'},
                { id: 'CX', text: 'Christmas Island'},
                { id: 'CC', text: 'Cocos (Keeling) Islands'},
                { id: 'CO', text: 'Colombia'},
                { id: 'KM', text: 'Comoros'},
                { id: 'CG', text: 'Congo'},
                { id: 'CD', text: 'Congo}, Democratic Republic'},
                { id: 'CK', text: 'Cook Islands'},
                { id: 'CR', text: 'Costa Rica'},
                { id: 'CI', text: 'Cote D\'Ivoire'},
                { id: 'HR', text: 'Croatia'},
                { id: 'CU', text: 'Cuba'},
                { id: 'CY', text: 'Cyprus'},
                { id: 'CZ', text: 'Czech Republic'},
                { id: 'DK', text: 'Denmark'},
                { id: 'DJ', text: 'Djibouti'},
                { id: 'DM', text: 'Dominica'},
                { id: 'DO', text: 'Dominican Republic'},
                { id: 'EC', text: 'Ecuador'},
                { id: 'EG', text: 'Egypt'},
                { id: 'SV', text: 'El Salvador'},
                { id: 'GQ', text: 'Equatorial Guinea'},
                { id: 'ER', text: 'Eritrea'},
                { id: 'EE', text: 'Estonia'},
                { id: 'ET', text: 'Ethiopia'},
                { id: 'FK', text: 'Falkland Islands (Malvinas)'},
                { id: 'FO', text: 'Faroe Islands'},
                { id: 'FJ', text: 'Fiji'},
                { id: 'FI', text: 'Finland'},
                { id: 'FR', text: 'France'},
                { id: 'GF', text: 'French Guiana'},
                { id: 'PF', text: 'French Polynesia'},
                { id: 'TF', text: 'French Southern Territories'},
                { id: 'GA', text: 'Gabon'},
                { id: 'GM', text: 'Gambia'},
                { id: 'GE', text: 'Georgia'},
                { id: 'DE', text: 'Germany'},
                { id: 'GH', text: 'Ghana'},
                { id: 'GI', text: 'Gibraltar'},
                { id: 'GR', text: 'Greece'},
                { id: 'GL', text: 'Greenland'},
                { id: 'GD', text: 'Grenada'},
                { id: 'GP', text: 'Guadeloupe'},
                { id: 'GU', text: 'Guam'},
                { id: 'GT', text: 'Guatemala'},
                { id: 'GG', text: 'Guernsey'},
                { id: 'GN', text: 'Guinea'},
                { id: 'GW', text: 'Guinea-Bissau'},
                { id: 'GY', text: 'Guyana'},
                { id: 'HT', text: 'Haiti'},
                { id: 'HM', text: 'Heard Island & Mcdonald Islands'},
                { id: 'VA', text: 'Holy See (Vatican City State)'},
                { id: 'HN', text: 'Honduras'},
                { id: 'HK', text: 'Hong Kong'},
                { id: 'HU', text: 'Hungary'},
                { id: 'IS', text: 'Iceland'},
                { id: 'IN', text: 'India'},
                { id: 'ID', text: 'Indonesia'},
                { id: 'IR', text: 'Iran}, Islamic Republic Of'},
                { id: 'IQ', text: 'Iraq'},
                { id: 'IE', text: 'Ireland'},
                { id: 'IM', text: 'Isle Of Man'},
                { id: 'IL', text: 'Israel'},
                { id: 'IT', text: 'Italy'},
                { id: 'JM', text: 'Jamaica'},
                { id: 'JP', text: 'Japan'},
                { id: 'JE', text: 'Jersey'},
                { id: 'JO', text: 'Jordan'},
                { id: 'KZ', text: 'Kazakhstan'},
                { id: 'KE', text: 'Kenya'},
                { id: 'KI', text: 'Kiribati'},
                { id: 'KR', text: 'Korea'},
                { id: 'KW', text: 'Kuwait'},
                { id: 'KG', text: 'Kyrgyzstan'},
                { id: 'LA', text: 'Lao People\'s Democratic Republic'},
                { id: 'LV', text: 'Latvia'},
                { id: 'LB', text: 'Lebanon'},
                { id: 'LS', text: 'Lesotho'},
                { id: 'LR', text: 'Liberia'},
                { id: 'LY', text: 'Libyan Arab Jamahiriya'},
                { id: 'LI', text: 'Liechtenstein'},
                { id: 'LT', text: 'Lithuania'},
                { id: 'LU', text: 'Luxembourg'},
                { id: 'MO', text: 'Macao'},
                { id: 'MK', text: 'Macedonia'},
                { id: 'MG', text: 'Madagascar'},
                { id: 'MW', text: 'Malawi'},
                { id: 'MY', text: 'Malaysia'},
                { id: 'MV', text: 'Maldives'},
                { id: 'ML', text: 'Mali'},
                { id: 'MT', text: 'Malta'},
                { id: 'MH', text: 'Marshall Islands'},
                { id: 'MQ', text: 'Martinique'},
                { id: 'MR', text: 'Mauritania'},
                { id: 'MU', text: 'Mauritius'},
                { id: 'YT', text: 'Mayotte'},
                { id: 'MX', text: 'Mexico'},
                { id: 'FM', text: 'Micronesia}, Federated States Of'},
                { id: 'MD', text: 'Moldova'},
                { id: 'MC', text: 'Monaco'},
                { id: 'MN', text: 'Mongolia'},
                { id: 'ME', text: 'Montenegro'},
                { id: 'MS', text: 'Montserrat'},
                { id: 'MA', text: 'Morocco'},
                { id: 'MZ', text: 'Mozambique'},
                { id: 'MM', text: 'Myanmar'},
                { id: 'NA', text: 'Namibia'},
                { id: 'NR', text: 'Nauru'},
                { id: 'NP', text: 'Nepal'},
                { id: 'NL', text: 'Netherlands'},
                { id: 'AN', text: 'Netherlands Antilles'},
                { id: 'NC', text: 'New Caledonia'},
                { id: 'NZ', text: 'New Zealand'},
                { id: 'NI', text: 'Nicaragua'},
                { id: 'NE', text: 'Niger'},
                { id: 'NG', text: 'Nigeria'},
                { id: 'NU', text: 'Niue'},
                { id: 'NF', text: 'Norfolk Island'},
                { id: 'MP', text: 'Northern Mariana Islands'},
                { id: 'NO', text: 'Norway'},
                { id: 'OM', text: 'Oman'},
                { id: 'PK', text: 'Pakistan'},
                { id: 'PW', text: 'Palau'},
                { id: 'PS', text: 'Palestinian Territory}, Occupied'},
                { id: 'PA', text: 'Panama'},
                { id: 'PG', text: 'Papua New Guinea'},
                { id: 'PY', text: 'Paraguay'},
                { id: 'PE', text: 'Peru'},
                { id: 'PH', text: 'Philippines'},
                { id: 'PN', text: 'Pitcairn'},
                { id: 'PL', text: 'Poland'},
                { id: 'PT', text: 'Portugal'},
                { id: 'PR', text: 'Puerto Rico'},
                { id: 'QA', text: 'Qatar'},
                { id: 'RE', text: 'Reunion'},
                { id: 'RO', text: 'Romania'},
                { id: 'RU', text: 'Russian Federation'},
                { id: 'RW', text: 'Rwanda'},
                { id: 'BL', text: 'Saint Barthelemy'},
                { id: 'SH', text: 'Saint Helena'},
                { id: 'KN', text: 'Saint Kitts And Nevis'},
                { id: 'LC', text: 'Saint Lucia'},
                { id: 'MF', text: 'Saint Martin'},
                { id: 'PM', text: 'Saint Pierre And Miquelon'},
                { id: 'VC', text: 'Saint Vincent And Grenadines'},
                { id: 'WS', text: 'Samoa'},
                { id: 'SM', text: 'San Marino'},
                { id: 'ST', text: 'Sao Tome And Principe'},
                { id: 'SA', text: 'Saudi Arabia'},
                { id: 'SN', text: 'Senegal'},
                { id: 'RS', text: 'Serbia'},
                { id: 'SC', text: 'Seychelles'},
                { id: 'SL', text: 'Sierra Leone'},
                { id: 'SG', text: 'Singapore'},
                { id: 'SK', text: 'Slovakia'},
                { id: 'SI', text: 'Slovenia'},
                { id: 'SB', text: 'Solomon Islands'},
                { id: 'SO', text: 'Somalia'},
                { id: 'ZA', text: 'South Africa'},
                { id: 'GS', text: 'South Georgia And Sandwich Isl.'},
                { id: 'ES', text: 'Spain'},
                { id: 'LK', text: 'Sri Lanka'},
                { id: 'SD', text: 'Sudan'},
                { id: 'SR', text: 'Suriname'},
                { id: 'SJ', text: 'Svalbard And Jan Mayen'},
                { id: 'SZ', text: 'Swaziland'},
                { id: 'SE', text: 'Sweden'},
                { id: 'CH', text: 'Switzerland'},
                { id: 'SY', text: 'Syrian Arab Republic'},
                { id: 'TW', text: 'Taiwan'},
                { id: 'TJ', text: 'Tajikistan'},
                { id: 'TZ', text: 'Tanzania'},
                { id: 'TH', text: 'Thailand'},
                { id: 'TL', text: 'Timor-Leste'},
                { id: 'TG', text: 'Togo'},
                { id: 'TK', text: 'Tokelau'},
                { id: 'TO', text: 'Tonga'},
                { id: 'TT', text: 'Trinidad And Tobago'},
                { id: 'TN', text: 'Tunisia'},
                { id: 'TR', text: 'Turkey'},
                { id: 'TM', text: 'Turkmenistan'},
                { id: 'TC', text: 'Turks And Caicos Islands'},
                { id: 'TV', text: 'Tuvalu'},
                { id: 'UG', text: 'Uganda'},
                { id: 'UA', text: 'Ukraine'},
                { id: 'AE', text: 'United Arab Emirates'},
                { id: 'GB', text: 'United Kingdom'},
                { id: 'US', text: 'United States'},
                { id: 'UM', text: 'United States Outlying Islands'},
                { id: 'UY', text: 'Uruguay'},
                { id: 'UZ', text: 'Uzbekistan'},
                { id: 'VU', text: 'Vanuatu'},
                { id: 'VE', text: 'Venezuela'},
                { id: 'VN', text: 'Viet Nam'},
                { id: 'VG', text: 'Virgin Islands}, British'},
                { id: 'VI', text: 'Virgin Islands}, U.S.'},
                { id: 'WF', text: 'Wallis And Futuna'},
                { id: 'EH', text: 'Western Sahara'},
                { id: 'YE', text: 'Yemen'},
                { id: 'ZM', text: 'Zambia'},
                { id: 'ZW', text: 'Zimbabwe'}
            ];
            $("[name='country_select']").select2({
                placeholder: "Select a country",
                data: isoCountries
            });

            $("[name='country_select']").val('{{$country}}');
            $("[name='country_select']").trigger('change.select2');
        });
})(jQuery);


$(".btn-purchase").click(function(){

var amount = $(this).attr('data-amount');


$("#amount").val(amount);
$("#exampleModalLabel").text('Contribute $'+amount+' or more')
$("[name='amountf']").val(amount);

});

$("#amount").change(function(){
$("[name='amountf']").val($(this).val());
});

$("[name='country_select']").change(function(){
$("[name='country']").val($(this).val());
$("[name='custom']").val($(this).val());
});

$("#takeme").click(function(){
window.location.hash = '#tiers';
});

</script>
@endsection
