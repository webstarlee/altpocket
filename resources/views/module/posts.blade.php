@foreach($statuses as $status)
  @if($status->type == "status")
  <?php
    $user = \App\User::where('id', $status->userid)->select('username', 'avatar', 'id')->first();
    $comments = \App\StatusComment::where('statusid', $status->id)->get();
    $text = $status->status;
    $code = array('<', '>');
    $text = str_replace($code, '', $text);
    $likers = $status->likers()->select('avatar', 'username', 'id')->get();
    preg_match_all("/\::[^::]*\::/", $text, $matches);
    preg_match_all("/\[youtube\](.*?)\[\/youtube\]/", $text, $youtubes);

    foreach($matches[0] as $key => $match)
    {
      $brackets = array('::');
      $coin = str_replace($brackets, '', $match);
      $coin = strtoupper($coin);
      if(DB::table('cryptos')->where('symbol', $coin)->exists())
      {
        $crypto = DB::table('cryptos')->where('symbol', $coin)->first();
        $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" data-toggle="tooltip" title="Coin: '.$crypto->name.' | 24H: '.$crypto->percent_change_24h.'%" style="width:20px;cursor:pointer;"/>', $text);
      } else {
        $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" style="width:20px;cursor:pointer;"/>', $text);
      }
    }
    $text = preg_replace('/(\#)([^\s]+)/', '<a href="?tag=#$2">#$2</a>', $text);
    $text = preg_replace("/@(\w+)/i", '<a style="color:#42a5f5" href="/user/$1">@$1</a>', $text);
    $text = str_replace('[img]', '<div class="post-thumb"><img src="', $text);
    $text = str_replace('[/img]', '"></div><p>', $text);

    $replacement = '<a href="$1">$2</a>';

    $text = preg_replace('~\[url(?|=[\'"]?([^]"\']+)[\'"]?]([^[]+)|](([^[]+)))\[/url]~', $replacement, $text);
   ?>
<div class="ui-block">
<article class="altpocket post">
  <div class="post__author author vcard inline-items">
                @if($user->avatar == "default.jpg")
                  <img src="/assets/img/default.png" alt="author">
                @else
                   <img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="author">
                @endif
                <div class="author-date">
                  <a class="h6 post__author-name fn" style="color:{{$user->groupColor()}}!important" href="/user/{{$user->username}}">@if($user->isDonator())<img src="/awards/diamondd.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user has supported Altpocket through a donaton.">@endif @if($user->isStaff() || $user->isFounder())<img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">@endif{{$user->username}}</a>
                  <div class="post__date">
                    <time class="published" datetime="{{$status->created_at}}">
                      {{$status->created_at->diffForHumans()}}
                    </time>
                  </div>
                </div>
              @if(Auth::user()->id == $status->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
                <ul class="card-actions icons right-top">
                  <li class="dropdown">
                    <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                      <i class="zmdi zmdi-more"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right btn-primary">
                      <li>
                        <a href="#" data-toggle="modal" data-target="#edit_post" class="edit-post" id="{{$status->id}}">Edit Post</a>
                      </li>
                      <li>
                        <a href="/status/{{$status->id}}/delete">Delete Post</a>
                      </li>
                    </ul>

                  </li>
                </ul>
              @endif

  </div>
  <p>{!! nl2br($text) !!}</p>
  <div class="post-additional-info inline-items">

                <a href="javascript:void(0)" class="post-add-icon inline-items heart like-status @if(Auth::user()->hasLiked($status)) liked @endif" status="{{$status->id}}">
                  <i class="fa fa-heart"></i>
                  <span>{{count($likers)}}</span>
                </a>
                @if(count($likers) >= 1)
                  <?php
                    $likers = $likers->take(5);
                    $like1 = $likers->first();
                    $count = count($likers);
                   ?>
                <ul class="friends-harmonic">
                  @foreach($likers as $like)
                  <li>
                    <a href="/user/{{$like->username}}">
                      @if($like->avatar != "default.jpg")
                        <img src="/uploads/avatars/{{$like->id}}/{{$like->avatar}}" alt="friend">
                      @else
                        <img src="/assets/img/default.png" alt="friend">
                      @endif
                    </a>
                  </li>
                @endforeach
                </ul>

                <div class="names-people-likes">
                  @if(Auth::user()->hasLiked($status))<a href="#">You</a> @else <a href="/user/{{$like1->username}}">{{$like1->username}}</a> @endif and
                  <br>{{$count - 1}} more liked this
                </div>
              @endif


                <div class="comments-shared">
                  <a href="?status={{$status->id}}"class="post-add-icon inline-items">
                    <i class="zmdi zmdi-comments"></i>
                    <span>{{count($comments)}}</span>
                  </a>
                </div>
              </div>
</article>
<ul class="comments-list">
  <ul class="comments-list">
    <?php
    $count = 0;
    ?>
              @foreach($comments as $comment)
                <?php
                  $count += 1;

                  $u = \App\User::where('id', $comment->userid)->select('avatar', 'username', 'id')->first();
                  $text = $comment->comment;
                  $code = array('<', '>');
                  $text = str_replace($code, '', $text);
                  preg_match_all("/\::[^::]*\::/", $text, $matches);

                  foreach($matches[0] as $key => $match)
                  {
                    $brackets = array('::');
                    $coin = str_replace($brackets, '', $match);
                    $coin = strtoupper($coin);

                    if(DB::table('cryptos')->where('symbol', $coin)->exists())
                    {
                      $crypto = DB::table('cryptos')->where('symbol', $coin)->first();
                      $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" data-toggle="tooltip" title="Coin: '.$crypto->name.' | 24H: '.$crypto->percent_change_24h.'%" style="width:20px;cursor:pointer;"/>', $text);
                    } else {
                      $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" style="width:20px;cursor:pointer;"/>', $text);
                    }
                  }

                    $text = preg_replace("/@(\w+)/i", '<a style="color:#42a5f5" href="/user/$1">@$1</a>', $text);
                 ?>
              <li class="li-comment status{{$status->id}}" @if($count > 5) style="display:none;" @endif>
                <div class="post__author author vcard inline-items">
                  @if($u->avatar != "default.jpg")
                     <img src="/uploads/avatars/{{$u->id}}/{{$u->avatar}}" alt="author">
                  @else
                    <img src="/assets/img/default.png" alt="author">
                  @endif

                  <div class="author-date">
                    <a class="h6 post__author-name fn" style="color:{{$u->groupColor()}}!important" href="/user/{{$u->username}}">@if($u->isStaff() || $u->isFounder())<img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">@elseif($u->isDonator())<img src="/awards/diamondd.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user has supported Altpocket through a donaton.">@endif{{$u->username}}</a>
                    <div class="post__date">
                      <time class="published" datetime="{{$comment->created_at}}">
                        {{$comment->created_at->diffForHumans()}}
                      </time>
                    </div>
                  </div>

                  @if(Auth::user()->id == $comment->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
                  <ul class="card-actions icons right-top">
                    <li class="dropdown">
                      <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                        <i class="zmdi zmdi-more"></i>
                      </a>
                      <ul class="dropdown-menu dropdown-menu-right btn-primary">
                        <li>
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#edit_comment" class="edit-comment" id="{{$comment->id}}">Edit Comment</a>
                        </li>
                        <li>
                          <a href="/statuscomment/{{$comment->id}}/delete">Delete Comment</a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                @endif

                </div>

                <p>{!!nl2br($text)!!}</p>

                <a href="javascript:void(0)" class="post-add-icon inline-items heart like-comment @if(Auth::user()->hasLiked($comment)) liked @endif" status="{{$comment->id}}">
                  <i class="fa fa-heart"></i>
                  <span>{{count($comment->likers()->select('id')->get())}}</span>
                </a>
              </li>
            @endforeach

            </ul>
            @if(count($comments) > 5)
            <a href="javascript:void(0)" class="more-comments" id="status{{$status->id}}">View more comments <span>+</span></a>
            @endif
            <form class="comment-form inline-items" action="/comment/post/{{$status->id}}" method="POST">

              {{csrf_field()}}

            <div class="post__author author vcard inline-items">
              @if(Auth::user()->avatar != "default.jpg")
                 <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="author">
              @else
                <img src="/assets/img/default.png " alt="author">
              @endif
            </div>

            <div class="form-group with-icon-right is-empty">
              <textarea class="form-control form-control-2" id="comment-form" placeholder="" style="background-image:none!important;" name="comment"></textarea>

              <span class="material-input"></span><span class="material-input"></span><span class="material-input"></span></div>
                <button type="submit" class="btn btn-blue" style="float:right;">Post<div class="ripple-container"></div></button>
          </form>
</ul>
</div>
@elseif($status->type == "poll")
  <?php
    $user = \App\User::where('id', $status->userid)->select('username', 'avatar', 'id')->first();
    $likers = $status->likers()->select('avatar', 'username', 'id')->get();
    $poll = \App\Poll::where('id', $status->status)->first();
    $pollanswers = \App\PollAnswer::where('pollid', $poll->id)->get();
    $votecount = \App\PollVote::where('pollid', $poll->id)->count();
    $comments = \App\StatusComment::where('statusid', $status->id)->get();

   ?>
<div class="ui-block">
<article class="altpocket post">
  <div class="post__author author vcard inline-items">
                @if($user->avatar == "default.jpg")
                  <img src="/assets/img/default.png" alt="author">
                @else
                   <img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="author">
                @endif
                <div class="author-date">
                  <a class="h6 post__author-name fn" style="color:{{$user->groupColor()}}!important" href="/user/{{$user->username}}">@if($user->isStaff() || $user->isFounder())<img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">@endif{{$user->username}}</a>
                  <div class="post__date">
                    <time class="published" datetime="{{$status->created_at}}">
                      {{$status->created_at->diffForHumans()}}
                    </time>
                  </div>
                </div>
              @if(Auth::user()->id == $status->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
                <ul class="card-actions icons right-top">
                  <li class="dropdown">
                    <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                      <i class="zmdi zmdi-more"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right btn-primary">
                      <li>
                        <a href="/poll/{{$poll->id}}/{{$status->id}}/delete">Delete Poll</a>
                      </li>
                    </ul>

                  </li>
                </ul>
              @endif

  </div>
  <ul class="widget w-pool">
  						<li>
  							<p>{{$poll->question}}</p>
  						</li>
              <hr>
              @foreach($pollanswers as $answer)
                <?php
                  $votes = \App\PollVote::where('answerid', $answer->id)->get();
                  if($votecount != 0){
                    $percentvote = (100 / $votecount) * count($votes);
                  } else {
                    $percentvote = 0;
                  }

                 ?>
  						<li>
  							<div class="skills-item">
  								<div class="skills-item-info">
  									<span class="skills-item-title">

  										<span class="radio">
  											<label>
  												<input class="vote" id="{{$answer->id}}" type="radio" name="optionsRadios" @if(\App\PollVote::where([['userid', '=', Auth::user()->id], ['answerid', '=', $answer->id]])->exists()) checked @endif>
  											{{$answer->answer}}
  											</label>
  										</span>
  									</span>
  									<span class="skills-item-count" style="margin-top:-20px;"><span class="count-animate" data-speed="1000" data-refresh-interval="50" data-to="{{$percentvote}}" data-from="0"></span><span class="units">{{number_format(min($percentvote,100))}}%</span></span>
  								</div>
  								<div class="skills-item-meter">
  									<span class="skills-item-meter-active bg-primary skills-animate" style="width: {{min($percentvote, 100)}}%; opacity: 1;background-color:#42a5f5!important;"></span>
  								</div>

  								<div class="counter-friends"><span class="answer-{{$answer->id}}">{{count($votes)}}</span> @if(count($votes) > 1)users @elseif(count($votes) == 0) users @else user @endif voted on this</div>

  								<ul class="friends-harmonic">
                    <?php
                      $count = 0;
                     ?>
                    @foreach($votes as $vote)
                      <?php
                        $user = \App\User::where('id', $vote->userid)->select('username', 'avatar', 'id')->first();
                        $count++;
                       ?>
                       @if($count <= 10)
    									<li>
    										<a href="/user/{{$user->username}}">
                          @if($user->avatar != "default.jpg")
      											<img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="friend">
                          @else
                            <img src="/assets/img/default.png" alt="friend">
                          @endif
    										</a>
    									</li>
                    @endif
                    @endforeach
                    @if(count($votes) > 10)
    									<li>
    										<a href="#" class="all-users">+{{count($votes) - 10}}</a>
    									</li>
                    @endif
  								</ul>

  							</div>
  						</li>
            @endforeach
  					</ul>



  <div class="post-additional-info inline-items">

                <a href="javascript:void(0)" class="post-add-icon inline-items heart like-status @if(Auth::user()->hasLiked($status)) liked @endif" status="{{$status->id}}">
                  <i class="fa fa-heart"></i>
                  <span>{{count($likers)}}</span>
                </a>
                @if(count($likers) >= 1)
                  <?php
                    $likers = $likers->take(5);
                    $like1 = $likers->first();
                    $count = count($likers);
                   ?>
                <ul class="friends-harmonic">
                  @foreach($likers as $like)
                  <li>
                    <a href="/user/{{$like->username}}">
                      @if($like->avatar != "default.jpg")
                        <img src="/uploads/avatars/{{$like->id}}/{{$like->avatar}}" alt="friend">
                      @else
                        <img src="/assets/img/default.png" alt="friend">
                      @endif
                    </a>
                  </li>
                @endforeach
                </ul>

                <div class="names-people-likes">
                  @if(Auth::user()->hasLiked($status))<a href="#">You</a> @else <a href="/user/{{$like1->username}}">{{$like1->username}}</a> @endif and
                  <br>{{$count - 1}} more liked this
                </div>
              @endif
              </div>
</article>
<ul class="comments-list">
  <ul class="comments-list">
    <?php
    $count = 0;
    ?>
              @foreach($comments as $comment)
                <?php
                  $count += 1;

                  $u = \App\User::where('id', $comment->userid)->select('avatar', 'username', 'id')->first();
                  $text = $comment->comment;
                  $code = array('<', '>');
                  $text = str_replace($code, '', $text);
                  preg_match_all("/\::[^::]*\::/", $text, $matches);

                  foreach($matches[0] as $key => $match)
                  {
                    $brackets = array('::');
                    $coin = str_replace($brackets, '', $match);
                    $coin = strtoupper($coin);

                    if(DB::table('cryptos')->where('symbol', $coin)->exists())
                    {
                      $crypto = DB::table('cryptos')->where('symbol', $coin)->first();
                      $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" data-toggle="tooltip" title="Coin: '.$crypto->name.' | 24H: '.$crypto->percent_change_24h.'%" style="width:20px;cursor:pointer;"/>', $text);
                    } else {
                      $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" style="width:20px;cursor:pointer;"/>', $text);
                    }
                  }

                    $text = preg_replace("/@(\w+)/i", '<a style="color:#42a5f5" href="/user/$1">@$1</a>', $text);
                 ?>
              <li class="li-comment status{{$status->id}}" @if($count > 5) style="display:none;" @endif>
                <div class="post__author author vcard inline-items">
                  @if($u->avatar != "default.jpg")
                     <img src="/uploads/avatars/{{$u->id}}/{{$u->avatar}}" alt="author">
                  @else
                    <img src="/assets/img/default.png" alt="author">
                  @endif

                  <div class="author-date">
                    <a class="h6 post__author-name fn" style="color:{{$u->groupColor()}}!important" href="/user/{{$u->username}}">@if($u->isStaff() || $u->isFounder())<img src="/awards/admin.png" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="This user is a Altpocket.io staff member.">@endif{{$u->username}}</a>
                    <div class="post__date">
                      <time class="published" datetime="{{$comment->created_at}}">
                        {{$comment->created_at->diffForHumans()}}
                      </time>
                    </div>
                  </div>

                  @if(Auth::user()->id == $comment->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
                  <ul class="card-actions icons right-top">
                    <li class="dropdown">
                      <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                        <i class="zmdi zmdi-more"></i>
                      </a>
                      <ul class="dropdown-menu dropdown-menu-right btn-primary">
                        <li>
                          <a href="javascript:void(0)" data-toggle="modal" data-target="#edit_comment" class="edit-comment" id="{{$comment->id}}">Edit Comment</a>
                        </li>
                        <li>
                          <a href="/statuscomment/{{$comment->id}}/delete">Delete Comment</a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                @endif

                </div>

                <p>{!!nl2br($text)!!}</p>

                <a href="javascript:void(0)" class="post-add-icon inline-items heart like-comment @if(Auth::user()->hasLiked($comment)) liked @endif" status="{{$comment->id}}">
                  <i class="fa fa-heart"></i>
                  <span>{{count($comment->likers()->select('id')->get())}}</span>
                </a>
              </li>
            @endforeach

            </ul>
            @if(count($comments) > 5)
            <a href="javascript:void(0)" class="more-comments" id="status{{$status->id}}">View more comments <span>+</span></a>
            @endif
            <form class="comment-form inline-items" action="/comment/post/{{$status->id}}" method="POST">

              {{csrf_field()}}

            <div class="post__author author vcard inline-items">
              @if(Auth::user()->avatar != "default.jpg")
                 <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="author">
              @else
                <img src="/assets/img/default.png " alt="author">
              @endif
            </div>

            <div class="form-group with-icon-right is-empty">
              <textarea class="form-control form-control-2" placeholder="" style="background-image:none!important;" name="comment"></textarea>

              <span class="material-input"></span><span class="material-input"></span><span class="material-input"></span></div>
                <button type="submit" class="btn btn-blue" style="float:right;">Post<div class="ripple-container"></div></button>
          </form>
</ul>
</div>


@endif
@endforeach
