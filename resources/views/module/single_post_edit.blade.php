@if($status->type == "status" && !$status->isHidden())
    <?php
        $user = $status->getPoster();
        $comments = $status->getComments();
        $text = $status->status;
        $code = array('<', '>');
        $text = str_replace($code, '', $text);
        $likers = $status->getLikers();
        preg_match_all("/\::[^::]*\::/", $text, $matches);
        preg_match_all("/\[youtube\](.*?)\[\/youtube\]/", $text, $youtubes);

        foreach($matches[0] as $key => $match){
            $brackets = array('::');
            $coin = str_replace($brackets, '', $match);
            $coin = strtoupper($coin);
            if(DB::table('cryptos')->where('symbol', $coin)->exists()){
                $crypto = DB::table('cryptos')->where('symbol', $coin)->first();
                $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" data-toggle="tooltip" title="Coin: '.$crypto->name.' | 24H: '.$crypto->percent_change_24h.'%" style="width:20px;cursor:pointer;"/>', $text);
            } else {
                $text = str_replace($match, '<img src="/icons/32x32/'.$coin.'.png" style="width:20px;cursor:pointer;"/>', $text);
            }
        }
        $text = preg_replace('/(\#)([^\s]+)/', '<a class="hashtag" href="?tag=$2">#$2</a>', $text);
        $text = preg_replace('/(\$)([^\s]+)/', '<a class="cointag" href="#">$$2</a>', $text);
        $text = preg_replace("/@(\w+)/i", '<a class="usertag" style="color:#42a5f5;font-weight:600;" href="/user/$1">@$1</a>', $text);
        //images
        //$images = '~https?://\S+\.(?:jpe?g|gif|png)(?:\?\S*)?(?=\s|$|\pP)~i';
        //$text = preg_replace($images, '<a href="$0" class="swipebox" title="Status Image"><img src="$0"  itemprop="thumbnail" alt="Image description"/></a><p>', $text);
        //url
        //$url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
        //$text = preg_replace($url, '<a href="/redirect?redirect=$0" target="_blank" title="$0">$0</a>', $text);
        $replacement = '<a href="$1">$2</a>';
        $replacement2 = '<a href="$1" class="swipebox" title="Status Image"><img src="$2"  itemprop="thumbnail" alt="Image description"/></a><p>';
        $text = preg_replace('~\[img(?|=[\'"]?([^]"\']+)[\'"]?]([^[]+)|](([^[]+)))\[/img]~', $replacement2, $text);
        $text = preg_replace('~\[url(?|=[\'"]?([^]"\']+)[\'"]?]([^[]+)|](([^[]+)))\[/url]~', $replacement, $text);
    ?>
    <article class="altpocket post">
        <div class="post__author author vcard inline-items">
            @if($user->avatar == "default.jpg")
                <img src="/assets/img/default.png" alt="author">
            @else
                <img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="author">
            @endif
            <div class="author-date">
                <a class="h6 post__author-name fn" style="color:{{$user->groupColor()}}!important" href="/user/{{$user->username}}">@if($user->hasDonated() || $user->hasRights())<img src="/awards/{{$user->getEmblem()}}" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="">@endif{{$user->username}}</a>
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
                                <a href="javascript:;" class="edit-post" id="{{$status->id}}">Edit Post</a>
                            </li>
                            <li>
                                <a href="/status/{{$status->id}}/delete" class="delete-status-btn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Deleting">Delete Post</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            @else
                <ul class="card-actions icons right-top">
                    <li class="dropdown">
                        <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                            <i class="zmdi zmdi-more"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right btn-primary">
                            <li>
                                <a href="/status/{{$status->id}}/hide">Hide Post</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endif
        </div>
        @if($status->statustype == "announcement")
            <div class="announcement" style="width: 100%;height: 25px;background-color: #b93131;border: 1px solid #b52a2a;"><p style="color: white;text-align: center;margin-top:0px;">Announcement</p></div>
        @endif

        @if($status->statustype == "update")
            <div class="announcement" style="width: 100%;height: 25px;background-color: #66afef;border: 1px solid #5ea6e8;"><p style="color: white;text-align: center;margin-top:0px;">Update</p></div>
        @endif
        @if ($status->status)
            <p style="word-wrap: break-word;">{!! nl2br($text) !!}</p>
        @endif
        @if ($status->images || $status->youtubes || $status->giphys)
            <div class="form-group status-media-container">
                @if ($status->images || $status->giphys)
                    <?php
                        $count_photo = 0;
                        if ($status->images) {
                            $photo_array = unserialize($status->images);
                            foreach ($photo_array as $photos) {
                                $count_photo += 1;
                            }
                        }

                        if ($status->giphys) {
                            $giphy_array = unserialize($status->giphys);
                            foreach ($giphy_array as $giphy) {
                                $count_photo += 1;
                            }
                        }

                        $real_count = 0;
                    ?>
                    <div class="posted_img_container js-zoom-gallery">
                        @if ($status->images)
                            <?php $total_images = unserialize($status->images) ?>
                            @foreach ($total_images as $single_image)
                                <?php
                                    $real_count += 1;
                                    $class1 = "post-photo-".$count_photo;
                                    if ($count_photo > 5) {
                                        $class1 = "post-photo-more";
                                    }
                                    $class2 = "subphoto-".$real_count;
                                    if ($real_count > 5) {
                                        $class2 = "subphoto-more";
                                    }
                                    $class = $class1." ".$class2;
                                ?>
                                <a class="{{$class}}" href="{{asset('assets/images/status/'.$user->username.'/'.$single_image)}}">
                                    <img src="{{asset('assets/images/status/'.$user->username.'/'.$single_image)}}" alt="">
                                </a>
                            @endforeach
                        @endif
                        @if ($status->giphys)
                            <?php $total_giphys = unserialize($status->giphys) ?>
                            @foreach ($total_giphys as $single_giphy)
                                <?php
                                    $real_count += 1;
                                    $class1 = "post-photo-".$count_photo;
                                    if ($count_photo > 5) {
                                        $class1 = "post-photo-more";
                                    }
                                    $class2 = "subphoto-".$real_count;
                                    if ($real_count > 5) {
                                        $class2 = "subphoto-more";
                                    }
                                    $class = $class1." ".$class2;
                                ?>
                                <a class="{{$class}}" href="{{$single_giphy}}">
                                    <img src="{{$single_giphy}}" alt="">
                                </a>
                            @endforeach
                        @endif
                    </div>
                @endif
                @if ($status->youtubes)
                    <?php
                        $youtube_array = unserialize($status->youtubes);
                    ?>
                    <div class="post-youtube-container">
                        @foreach ($youtube_array as $youtube)
                            <?php
                                $info = \Alaouy\Youtube\Facades\Youtube::getVideoInfo($youtube);
                                if (strlen($info->snippet->description) > 100) { 
                                    $description = substr($info->snippet->description, 0, 100).'...';
                                } else {
                                    $description = $info->snippet->description;
                                }
                            ?>
                            <div class="youtube youtube-single">
                                <a href="https://www.youtube.com/watch?v={{$youtube}}" target="_blank">
                                    <div class="youtube img-container" style="background-image: url({{$info->snippet->thumbnails->medium->url}});"></div>
                                    <div class="youtube title-description-container">
                                        <p class="title">{{$info->snippet->title}}</p>
                                        <p class="description">{{$description}}</p>
                                        <p class="youtube-com">youtube.com</p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
        <div class="post-additional-info inline-items">
            <a href="javascript:void(0)" class="post-add-icon inline-items heart like-status @if(Auth::user()->hasLikedStatus($status)) liked @endif" status="{{$status->id}}">
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
                    @if(Auth::user()->hasLikedStatus($status))<a href="#">You</a> @else <a href="/user/{{$like1->username}}">{{$like1->username}}</a> @endif and
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
        <ul class="comments-list all-comment-container">
            <?php
                $count = 0;
            ?>
            @foreach($comments as $comment)
                <?php
                    $count += 1;
                ?>
                @include('module.single_comment')
            @endforeach
        </ul>
        @if(count($comments) > 5)
            <a href="javascript:void(0)" class="more-comments" id="status{{$status->id}}">View more comments <span>+</span></a>
        @endif
        <form class="comment-form inline-items comment-post-form" action="/comment/post/{{$status->id}}" method="POST">
            {{csrf_field()}}
            <div class="form-group with-icon-right is-empty wang-custom">
                <div class="post__author author vcard inline-items">
                    @if(Auth::user()->avatar != "default.jpg")
                        <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="author">
                    @else
                        <img src="/assets/img/default.png " alt="author">
                    @endif
                </div>
                <textarea class="form-control form-control-2" id="comment-form" placeholder="" style="background-image:none!important;" name="comment"></textarea>
                <div class="form-group post-media-container comment-post-media_{{$status->id}}"></div>
            </div>
            <div class="comment-btn-container">
                <button type="submit" class="btn btn-blue comment-post-btn" style="float:right;" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Posting">Post<div class="ripple-container"></div></button>
                <a href="javascript:void(0)" class="comment-add-img-btn" data-status_id="{{$status->id}}" style="float:left; margin-top:16px;margin-right:20px;"><i class="zmdi zmdi-camera" style="font-size:22px;"></i></a>
                <a href="{{url('status/get-giphy')}}" class="comment-add-gif-btn" data-status_id="{{$status->id}}" style="float:left; margin-top:7px;margin-right:20px;"><i class="zmdi zmdi-gif" style="font-size:40px;"></i></a>
            </div>
        </form>
    </ul>
@elseif($status->type == "poll")
    <?php
        $user = \App\User::where('id', $status->userid)->select('username', 'avatar', 'id')->first();
        $likers = $status->getLikers();
        $poll = \App\Poll::where('id', $status->status)->first();
        $pollanswers = \App\PollAnswer::where('pollid', $poll->id)->get();
        $votecount = \App\PollVote::where('pollid', $poll->id)->count();
        $comments = \App\StatusComment::where('statusid', $status->id)->get();
    ?>
    <article class="altpocket post">
        <div class="post__author author vcard inline-items">
            @if($user->avatar == "default.jpg")
                <img src="/assets/img/default.png" alt="author">
            @else
                <img src="/uploads/avatars/{{$user->id}}/{{$user->avatar}}" alt="author">
            @endif
            <div class="author-date">
                <a class="h6 post__author-name fn" style="color:{{$user->groupColor()}}!important" href="/user/{{$user->username}}">@if($user->hasRights() || $user->hasDonated())<img src="/awards/{{$user->getEmblem()}}" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="">@endif{{$user->username}}</a>
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
                                <a href="/poll/{{$poll->id}}/{{$status->id}}/delete" class="poll-delete-btn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Deleting">Delete Poll</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            @endif
        </div>
        <ul class="widget w-pool" style="position:relative;">
            <li>
                <p style="word-wrap: break-word;">{{$poll->question}}</p>
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
                <li class="answer-container_{{$answer->id}}">
                    <div class="skills-item">
                        <div class="skills-item-info">
                            <span class="skills-item-title">
                                <span class="">
                                    <label>
                                        <input class="vote option-input radio" data-answer_id="{{$answer->id}}" type="radio" name="radio_{{$poll->id}}" @if(\App\PollVote::where([['userid', '=', Auth::user()->id], ['answerid', '=', $answer->id]])->exists()) checked @endif>
                                        {{$answer->answer}}
                                    </label>
                                </span>
                            </span>
                            <span class="skills-item-count">
                                <span class="count-animate" data-speed="1000" data-refresh-interval="50" data-to="{{$percentvote}}" data-from="0"></span>
                                <span class="units">{{number_format(min($percentvote,100))}}%</span>
                            </span>
                        </div>
                        <div class="skills-item-meter">
                            <span class="skills-item-meter-active bg-primary skills-animate" style="width: {{min($percentvote, 100)}}%; opacity: 1;background-color:#42a5f5!important;"></span>
                        </div>

                        <div class="counter-friends">
                            <span class="answer-{{$answer->id}}">{{count($votes)}}</span>
                            @if(count($votes) > 1)users @elseif(count($votes) == 0)
                                users
                            @else
                                user
                            @endif voted on this
                        </div>

                        <ul class="friends-harmonic">
                            <?php
                                $count = 0;
                            ?>
                            @foreach($votes as $vote)
                                <?php
                                    $user = $vote->getVoter();
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
                <a href="javascript:void(0)" class="post-add-icon inline-items heart like-status @if(Auth::user()->hasLikedStatus($status)) liked @endif" status="{{$status->id}}">
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
                        @if(Auth::user()->hasLikedStatus($status))<a href="#">You</a> @else <a href="/user/{{$like1->username}}">{{$like1->username}}</a> @endif and
                        <br>
                        {{$count - 1}} more liked this
                    </div>
                @endif
            </div>
    </article>
    <ul class="comments-list">
        <ul class="comments-list all-comment-container">
            <?php
                $count = 0;
            ?>
            @foreach($comments as $comment)
                <?php
                    $count += 1;
                ?>
                @include('module.single_comment')
            @endforeach
        </ul>
        @if(count($comments) > 5)
            <a href="javascript:void(0)" class="more-comments" id="status{{$status->id}}">View more comments <span>+</span></a>
        @endif
        <form class="comment-form inline-items comment-post-form" action="/comment/post/{{$status->id}}" method="POST">
            {{csrf_field()}}
            <div class="form-group with-icon-right is-empty wang-custom">
                <div class="post__author author vcard inline-items">
                    @if(Auth::user()->avatar != "default.jpg")
                        <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="author">
                    @else
                        <img src="/assets/img/default.png " alt="author">
                    @endif
                </div>
                <textarea class="form-control form-control-2" id="comment-form" placeholder="" style="background-image:none!important;" name="comment"></textarea>
                <div class="form-group post-media-container comment-post-media_{{$status->id}}"></div>
            </div>
            <div class="comment-btn-container">
                <button type="submit" class="btn btn-blue comment-post-btn" style="float:right;" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Posting">Post<div class="ripple-container"></div></button>
                <a href="javascript:void(0)" class="comment-add-img-btn" data-status_id="{{$status->id}}" style="float:left; margin-top:16px;margin-right:20px;"><i class="zmdi zmdi-camera" style="font-size:22px;"></i></a>
                <a href="{{url('status/get-giphy')}}" class="comment-add-gif-btn" data-status_id="{{$status->id}}" style="float:left; margin-top:7px;margin-right:20px;"><i class="zmdi zmdi-gif" style="font-size:40px;"></i></a>
            </div>
        </form>
    </ul>
@endif
