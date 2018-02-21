@extends('layouts.admin');

@section('title', 'Hall Of Fame')

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
  													Hall Of Fame
  													<small>
  														The people who made Altpocket possible.
  													</small>
  												</h3>
  											</div>
  										</div>
  									</div>
  									<div class="m-portlet__body" style="text-align:center;">
                      <h2>Thank you all for supporting us</h2>
                      <p>Without these people, Altpocket would not be possible, these users will forever be listed on our hall of fame based on their contributions to our beloved platform.</p>
  									</div>
  								</div>
<div class="row">
  <div class="col-md-3">
  </div>
  <div class="col-md-6">
                  <div class="m-portlet ">
                                <div class="m-portlet__head">
                                  <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                      <h3 class="m-portlet__head-text">
                                        Hall Of Fame
                                      </h3>
                                    </div>
                                  </div>
                                </div>
                                <div class="m-portlet__body">
                                  <div class="tab-content">
                                    <div class="tab-pane active" id="m_widget4_tab1_content">
                                      <!--begin::Widget 14-->
                                      <div class="m-widget4">
                                        @foreach($fame as $donation)
                                          @php
                                            $user = $donation->getDonator();
                                          @endphp
                                          @if($user && $user->public == "on" || $user && $donation->userid == "31711")
                                          <div class="m-widget4__item">
                                            <div class="m-widget4__img m-widget4__img--pic">
                                              @if($user->avatar != "default.jpg")
                                                <img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="">
                                              @else
                                                <img src="https://altpocket.io/assets/img/default.png" alt="">
                                              @endif
                                            </div>
                                            <div class="m-widget4__info">
                                              <span class="m-widget4__title" style="color:{{$user->groupColor()}};{{$user->groupStyle()}}">
                                                <img src="/awards/{{$user->getEmblem()}}" style="width:16px;">  {{$user->username}}
                                              </span>
                                              <br>
                                              <span class="m-widget4__sub">
                                                ${{number_format($donation->amount1, 2)}}
                                              </span>
                                            </div>
                                            <div class="m-widget4__ext">
                      												<a href="/user/{{$user->username}}" class="m-btn m-btn--pill m-btn--hover-brand btn btn-sm btn-secondary">
                      													Profile
                      												</a>
                      											</div>
                                          </div>
                                        @endif
                                        @endforeach
                                      </div>
                                      <!--end::Widget 14-->
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>


@endsection
