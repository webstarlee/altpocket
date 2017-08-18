@extends('layouts.support')

@section('title', 'All questions')


@section('content')
  <!-- Start Page Banner Area -->
  <div class="page-banner-area" style="background:none;">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <div class="page-title">
                      <h2>Question</h2>
                      <span class="sub-title"><a href="/support">Home </a> / Questions page</span>
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
                              <form id="dwqa-search" class="dwqa-search">
                                  <input data-nonce="fc987a6f77" type="text" placeholder="What do you want to know?" name="q" class="ui-autocomplete-input" autocomplete="off" required>
                                  <button type="submit"><i class="fa fa-search"></i></button>
                              </form>
                              @if(Auth::user())
                              <a href="/ask" class="themeix-btn hover-bg" style="display:block;margin:0 auto;width:20%;text-align:center;margin-top:20px;">Ask Question</a>
                              @endif
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
                                          <option value="date" selected="selected">Date</option>
                                          <option value="views">Views</option>
                                          <option value="answers">Answers</option>
                                          <option value="votes">Votes</option>
                                      </select>
                                  </label>
                              </div>
                              <div class="dwqa-questions-list">
                                @foreach($stickys as $question)
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
                                          @if(Auth::user() && Auth::user()->isStaff() || Auth::user() && Auth::user()->isFounder())
                                          <a href="/question/{{$question->id}}/sticky"><span class="dwqa-votes-count"><strong>Mark as</strong>Sticky</span></>
                                          @endif
                                      </div>
                                  </div>
                                @endforeach

                                <div id="sort-divs">
                                @foreach($questions as $question)
                                  <?php
                                    $user = DB::table('users')->where('id', $question->userid)->first();
                                   ?>
                                  <div class="sortable-q dwqa-question-item @if($question->sticky == "yes") dwqa-sticky @endif" data-sort-views="{{$question->views}}" data-sort-answers="{{$question->answers}}" data-sort-votes="{{$question->votes}}" data-sort-date="{{strtotime($question->created_at)}}">
                                      <header class="dwqa-question-title">
                                          <a href="/question/{{$question->id}}">{{$question->title}}</a>
                                      </header>
                                      <div class="dwqa-question-meta">
                                          <span class="dwqa-status dwqa-status-{{strtolower($question->tag)}}" title="{{$question->tag}}">{{$question->tag}}</span>
                                          @if(DB::table('answers')->where([['questionid', '=', $question->id]])->orderby('created_at', 'desc')->count() == 0)
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
                                          @if(Auth::user() && Auth::user()->isStaff() || Auth::user() && Auth::user()->isFounder())
                                          <a href="/question/{{$question->id}}/sticky"><span class="dwqa-votes-count"><strong>Mark as</strong>Sticky</span></>
                                          <a href="/question/{{$question->id}}/delete"><span class="dwqa-answers-count"><strong>Delete</strong>Question</span></>
                                          @endif
                                      </div>
                                  </div>
                                @endforeach
                              </div>
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


@section('js')
<script>
$(document).ready(function(){

var $divs = $("div.sortable-q");

$("#dwqa-sort-by").change(function(){

  if($(this).val() == "views")
  {
    sortbyViews();
  }

  if($(this).val() == "answers")
  {
    sortbyAnswers();
  }

  if($(this).val() == "votes")
  {
    sortbyVotes();
  }

  if($(this).val() == "date")
  {
    sortbyDate();
  }
});

function sortbyViews(){
  var opOrder = $divs.sort(function (a, b) {
    return $(a).data('data-sort-views') < $(b).data('data-sort-views') ? -1 : 1;
  });
  $("#sort-divs").html(opOrder);
}
function sortbyAnswers(){
  var apOrder = $divs.sort(function (a, b) {
    return $(a).data('data-sort-answers') < $(b).data('data-sort-answers') ? -1 : 1;
  });
  $("#sort-divs").html(apOrder);
}
function sortbyVotes(){
  var apOrder = $divs.sort(function (a, b) {
    return $(a).data('data-sort-votes') < $(b).data('data-sort-votes') ? -1 : 1;
  });
  $("#sort-divs").html(apOrder);
}
function sortbyDate(){
  var apOrder = $divs.sort(function (a, b) {
    return $(a).data('data-sort-date') < $(b).data('data-sort-date') ? -1 : 1;
  });
  $("#sort-divs").html(apOrder);
}
});


</script>
@endsection
