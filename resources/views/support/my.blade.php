@extends('layouts.support')

@section('title', 'My Questions')


@section('content')
  <!-- Start Page Banner Area -->
  <div class="page-banner-area" style="background:none;">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="page-title">
                      <h2>My Questions</h2>
                      <span class="sub-title"><a href="/support">Home </a> / My Questions</span>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End Pages Banner Area -->
  <!-- Start Questions Area -->
  <div class="question-area themeix-ptb">
      <div class="container">
          <div class="row">
              <!-- Start Question -->
              <div class="col-md-12">
                  <div class="questions-wrapper">
                      <div class="dwqa-container">
                          <div class="dwqa-questions-archive">
                              <div class="dwqa-question-filter">
                                  <span>Filter:</span>
                                  <ul>
                                      <li><a href="/questions" class="active">All</a></li>
                                      <li><a href="?filter=open">Open</a></li>
                                      <li><a href="?filter=resolved">Resolved</a></li>
                                      <li><a href="?filter=closed">Closed</a></li>
                                      <li><a href="?filter=unanswered">Unanswered</a></li>
                                  </ul>
                                  <label for="dwqa-sort-by" class="dwqa-sort-by">
                                      <select id="dwqa-sort-by">
                                          <option disabled="">Sort by</option>
                                          <option value="/dwqa/?sort=views">Views</option>
                                          <option selected="selected" value="/dwqa/?sort=answers">Answers</option>
                                          <option value="/dwqa/?sort=votes">Votes</option>
                                      </select>
                                  </label>
                              </div>
                              <div class="dwqa-questions-list">
                                @foreach($questions as $question)
                                  <?php
                                    $user = DB::table('users')->where('id', $question->userid)->first();
                                   ?>
                                  <div class="dwqa-question-item @if($question->sticky == "yes") dwqa-sticky @endif">
                                      <header class="dwqa-question-title">
                                          <a href="/question/{{$question->id}}">{{$question->title}}</a>
                                      </header>
                                      <div class="dwqa-question-meta">
                                          <span class="dwqa-status dwqa-status-{{strtolower($question->tag)}}" title="{{$question->tag}}">{{$question->tag}}</span>
                                          @if($question->answers == 0)
                                            <span><a href="/user/{{$user->username}}">{{$user->username}}</a> asked {{$question->created_at->diffForHumans()}}</span>
                                          @else
                                            <?php
                                              $a = DB::table('answers')->where([['questionid', '=', $question->id]])->orderby('created_at', 'desc')->first();

                                              $answ = DB::table('users')->where('id', $a->userid)->first();
                                             ?>
                                            <span><a href="/user/{{$answ->username}}">{{$answ->username}}</a> answered <?php echo \Carbon\Carbon::parse($a->created_at)->diffForHumans(); ?></span>
                                          @endif
                                          <span class="dwqa-question-category"> • <a href="#" rel="tag">{{$question->category}}</a></span>
                                      </div>
                                      <div class="dwqa-question-stats">
                                          <span class="dwqa-views-count"><strong>{{$question->views}}</strong>views</span>
                                          <span class="dwqa-answers-count"><strong>{{$question->answers}}</strong>answers</span>
                                          <span class="dwqa-votes-count"><strong>{{$question->votes}}</strong>votes</span>
                                          <a href="/question/{{$question->id}}/sticky"><span class="dwqa-votes-count"><strong>Mark as</strong>Sticky</span></>
                                      </div>
                                  </div>
                                @endforeach
                              </div>
                              <div class="dwqa-questions-footer">
                                  <div class="dwqa-pagination"><span class="dwqa-page-numbers dwqa-current">1</span></div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <!-- End Question -->
          </div>
      </div>
  </div>
  <!-- End  Questions Area -->
  <!-- Start About-us Bg -->
  <div class="about-us-bg" style="background:none;">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="about-us-info">
                      <h2>Altpocket is here to stay, register today and track your cryptocurrency investment among thousands of users.</h2>
                      <a class="themeix-btn primary-bg" href="/login">Register Today</a>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End About-us Bg -->


@endsection
