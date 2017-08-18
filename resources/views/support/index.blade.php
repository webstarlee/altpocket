@extends('layouts.support')

@section('title', 'Support Center')


@section('content')
  <!-- Start Slider Area -->
  <div class="slider-area">
      <div class="container-fluid">
          <div class="row">
              <div class="slider-wrapper-2" style="background:none;">
                 <div id="particles-js">
                 <div class="slider-fixed-text">
                     <h1>How can we help you today?</h1>
                     <p>We are here to help you out when you need it.</p>
                       <div class="slider-buttons">
                          <a href="/updates" class="themeix-btn white-bg">Latests Updates</a>
                          <a href="/questions" class="themeix-btn primary-bg">Ask Questions</a>
                      </div>
                 </div>
                 </div>
              </div>
          </div>
      </div>
  </div>
  <!--End Slider Area -->
  <!-- Start Document Source Area -->
  <section>
      <div class="document-source-area themeix-ptb">
          <div class="container">
              <div class="row">
                  <div class="col-sm-4">
                      <div class="single-box">
                          <div class="box-inner">
                              <img src="images/icon1.png" alt="box img">
                              <h3>QA Section</h3>
                              <p>{{count(DB::table('questions')->get())}} Questions / {{count(DB::table('answers')->get())}} Answer</p>
                              <a href="/questions" class="themeix-btn danger-bg">See All Questions</a>
                          </div>
                          <div class="box-flip">
                            <div class="flip-box-innner">
                              <h3>Question Answer Section</h3>
                              <p>Here you can post a question and get it answered by one of the Altpocket officials or by the community!</p>
                              <a href="/questions" class="themeix-btn danger-bg">See All Questions</a>
                            </div>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-4">
                      <div class="single-box">
                          <div class="box-inner">
                              <img src="images/icon2.png" alt="box img">
                              <h3>Our Blog</h3>
                              <p>0 Posts / 0 Comments</p>
                              <a href="blog.html" class="themeix-btn hover-bg">Visit the Blog</a>
                          </div>
                          <div class="box-flip">
                            <div class="flip-box-innner">
                              <h3>Our Blog</h3>
                              <p>On our blog you can read up on what we are currently doing, whats new and how we do it. Also some general tips and tricks on Altpocket.</p>
                              <a href="blog.html" class="themeix-btn danger-bg">Visit Blog</a>
                            </div>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-4">
                      <div class="single-box">
                          <div class="box-inner">
                              <img src="images/icon3.png" alt="box img">
                              <h3>Latest Updates</h3>
                              <p>{{count(DB::table('updates')->get())}} Updates / {{count(DB::table('changes')->get())}} Changes</p>
                              <a href="/updates" class="themeix-btn primary-bg">See All Updates</a>
                          </div>
                          <div class="box-flip">
                             <div class="flip-box-innner">
                              <h3>Latest Updates</h3>
                              <p>Here we post our changelogs, we usually update Altpocket everyday and sometimes updates are bigger and sometimes smaller.</p>
                              <a href="/updates" class="themeix-btn danger-bg">See All Updates</a>
                            </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </section>
  <!-- End Document Source Area -->
  <!-- Start Topics Section Area -->
  <section>
      <div class="topics-section-area themeix-ptb">
          <div class="container">
              <div class="row">
                  <div class="col-sm-12 col-md-12">
                      <!-- Start Topics Link Area -->
                      <div class="topics-links">
                          <div class="themeix-section-title">
                              <h2>Explore Topics</h2>
                              <p>Here you can find information regarding Altpocket written by the team, if there is anything missing please let us know.</p>
                          </div>
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="single-links">
                                      <h4 class="list-title">Introduction (0)</h4>
                                      <ul>
                                      </ul>
                                  </div>
                                  <div class="single-links">
                                      <h4 class="list-title">Tips &amp; Tricks (0)</h4>
                                      <ul>
                                      </ul>
                                  </div>
                                  <div class="topics-btn">
                                      <a href="/questions" class="themeix-btn hover-bg">See All Topics</a>
                                      <a href="/ask" class="themeix-btn primary-bg">Ask Question</a>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="single-links">
                                      <h4 class="list-title">Security (0)</h4>
                                      <ul>
                                      </ul>
                                  </div>
                                  <div class="single-links">
                                      <h4 class="list-title">Information (0)</h4>
                                      <ul>

                                      </ul>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <!-- End Topics Link Area -->
                  </div>
              </div>
          </div>
      </div>
  </section>
  <!-- End Topics Section Area -->
  <!-- Start FAQ Section -->
  <section class="faq-section themeix-ptb">
      <div class="container">
          <div class="row">

              <div class="col-sm-12">
                  <div class="themeix-section-title">
                      <h2>Frequently Asked Questions</h2>
                      <p>We know you have a lot of questions, here is our most frequently questions asked and answers.</p>
                  </div>
                  <div class="faq-tab-wrapper">
                      <div class="panel-group" id="accordion">
                          <div class="panel panel-default themeix-panel">
                              <div class="panel-heading">
                                  <h4 class="panel-title">
                                     <a  data-toggle="collapse" data-parent="#accordion" class="minus-sign" href="#accordion-one">What is Altpocket?</a>
                                 </h4>
                              </div>
                              <div id="accordion-one" class="panel-collapse collapse in">
                                  <div class="panel-body">
                                      <p>Altpocket.io is a platform where you can showcase, track and soon manage all your cryptocurrency investments. But it isn't just a regular platform, we want to gamify the whole investment part by adding aspects to the platform which makes the whole experience gamified.</p>
                                  </div>
                              </div>
                          </div>
                          <div class="panel panel-default themeix-panel">
                              <div class="panel-heading">
                                  <h4 class="panel-title">
                                     <a data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#accordion-two">What is a verified investment?</a>
                                 </h4>
                              </div>
                              <div id="accordion-two" class="panel-collapse collapse">
                                  <div class="panel-body">
                                    <p>A verified investment is an investmenst that has been verified by the system automaticially, rather than entering all the values manually, the system reads the exact values from your exchange by importing your trade history.</p>
                                  </div>
                              </div>
                          </div>
                          <div class="panel panel-default themeix-panel">
                              <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#accordion-three">How safe are my API keys?</a>
                                 </h4>
                              </div>
                              <div id="accordion-three" class="panel-collapse collapse">
                                  <div class="panel-body">
                                    <p>Your API keys are 100% safely stored offshore and encrypted, we also took extra safety measures by making it so it is not visible for anyone to see who the API key is connected with. However we still recommend you to enable Read Rights only.</p>
                                  </div>
                              </div>
                          </div>
                          <div class="panel panel-default themeix-panel">
                              <div class="panel-heading">
                                  <h4 class="panel-title">
                                     <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#accordion-four">Do you plan to monetize Altpocket?</a>
                                 </h4>
                              </div>
                              <div id="accordion-four" class="panel-collapse collapse">
                                  <div class="panel-body">
                                    <p>Like all successful websites do, we grow. Our server costs are no longer the cheap $5 a month it was 6 weeks ago. Our plans is to introduce some kind of monetization smoothly that will fit the website and it's purpose. However keep in mind that Altpocket will always be AD-free.</p>
                                  </div>
                              </div>
                          </div>

                      </div>
                  </div>
              </div>

          </div>
      </div>
  </section>
  <!-- End FAQ Section -->
  <!-- Start Call To Action Area -->
  <div class="call-to-action-area hover-bg">
      <div class="container">
          <div class="row">
              <div class="col-md-8">
                  <div class="action-content">
                      <h2>Not registered to Altpocket yet?</h2>
                      <p>Altpocket is completely free to use and free of ads.</p>
                  </div>
              </div>
              <div class="col-md-4">
                  <div class="purchase-link text-right">
                      <a href="/login" class="themeix-purchase-btn-3">Register Today</a>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End Call To Action Area -->

@endsection
