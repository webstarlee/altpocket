<?php
    $u = $comment->getPoster();
    $text = $comment->comment;
    $code = array('<', '>');
    $text = str_replace($code, '', $text);
    preg_match_all("/\::[^::]*\::/", $text, $matches);

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
    $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
    $text = preg_replace($url, '<a href="/redirect?redirect=$0" target="_blank" title="$0">$0</a>', $text);
    $text = preg_replace("/@(\w+)/i", '<a class="usertag" style="color:#42a5f5;font-weight:600;" href="/user/$1">@$1</a>', $text);
?>
<div class="post__author author vcard inline-items">
    @if($u->avatar != "default.jpg")
        <img src="/uploads/avatars/{{$u->id}}/{{$u->avatar}}" alt="author">
    @else
        <img src="/assets/img/default.png" alt="author">
    @endif

    <div class="author-date">
        <a class="h6 post__author-name fn" style="color:{{$u->groupColor()}}!important" href="/user/{{$u->username}}">@if($u->hasDonated() || $u->hasRights())<img src="/awards/{{$u->getEmblem()}}" style="width:16px;height:16px;margin-right:2px!important;" data-toggle="tooltip" title="">@endif{{$u->username}}</a>
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
                        <a href="javascript:void(0)" class="edit-comment" id="{{$comment->id}}">Edit Comment</a>
                    </li>
                    <li>
                        <a href="/statuscomment/{{$comment->id}}/delete" class="delete-comment" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Deleting">Delete Comment</a>
                    </li>
                </ul>
            </li>
        </ul>
    @endif
</div>
<p style="word-wrap: break-word;">{!!nl2br($text)!!}</p>
@if ($comment->images || $comment->giphys)
    <div class="form-group post-media-container" id="status-post-media-container">
        @if ($comment->images)
            <?php $total_images = unserialize($comment->images) ?>
            @foreach ($total_images as $single_image)
                <div class="media-single">
                    <a class="image-popup-link" href="{{asset('assets/images/status/'.$u->username.'/'.$single_image)}}">
                        <img src="{{asset('assets/images/status/'.$u->username.'/'.$single_image)}}" alt="">
                    </a>
                </div>
            @endforeach
        @endif
        @if ($comment->giphys)
            <?php $total_giphys = unserialize($comment->giphys) ?>
            @foreach ($total_giphys as $single_giphy)
                <div class="media-single">
                    <a class="image-popup-link" href="{{$single_giphy}}">
                        <img src="{{$single_giphy}}" alt="">
                    </a>
                </div>
            @endforeach
        @endif
    </div>
@endif
<a href="javascript:void(0)" class="post-add-icon inline-items heart like-comment @if(Auth::user()->hasLikedComment($comment)) liked @endif" status="{{$comment->id}}">
    <i class="fa fa-heart"></i>
    <span>{{$comment->getLikes()}}</span>
</a>
<a href="javascript:void(0)" class="post-add-icon inline-items heart comment-reply-button" style="margin-left: 10px;font-size:13px;"> <i class="zmdi zmdi-mail-reply" style="width:auto;height:auto;"></i> Reply</a>
<?php
    $check_reply = \App\StatusCommentReply::where('commentid', $comment->id)->count();
?>
<ul class="comment-reply-container" @if ($check_reply > 0) style="display: block;" @else style="display: none;" @endif>
    @if ($check_reply > 0)
        <?php
            $replies = \App\StatusCommentReply::where('commentid', $comment->id)
            ->join('users', 'users.id', '=', 'status_comment_replies.userid')
            ->select('status_comment_replies.*', 'users.username', 'users.avatar')
            ->get();
        ?>
        @foreach ($replies as $replie)
            @include('module.single_comment_reply', ['reply'=>$replie])
        @endforeach
    @endif
</ul>
<form class="comment-form inline-items comment-reply-form" action="/commentreply/post/{{$comment->id}}" method="POST">
    {{csrf_field()}}
    <div class="form-group with-icon-right is-empty wang-custom">
        <div class="post__author author vcard inline-items">
            @if(Auth::user()->avatar != "default.jpg")
                <img src="/uploads/avatars/{{Auth::user()->id}}/{{Auth::user()->avatar}}" alt="author">
            @else
                <img src="/assets/img/default.png " alt="author">
            @endif
        </div>
        <textarea class="form-control form-control-2 comment-reply-text" placeholder="" style="background-image:none!important;" name="comment_reply"></textarea>
        <div class="form-group post-media-container comment-post-media_{{$comment->id}}"></div>
    </div>
    <div class="comment-reply-btn-container">
        <button type="submit" class="btn btn-blue commentreply-post-btn" style="float:right;" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Posting">Post<div class="ripple-container"></div></button>
        <button type="button" class="btn btn-red" onclick="$(this).parents('.comment-reply-form').removeClass('open')" style="float:right;">cancel<div class="ripple-container"></div></button>
        <a href="{{url('status/get-giphy')}}" class="commentreply-add-gif-btn" data-comment_id="{{$comment->id}}" style="float:left; margin-top:7px;margin-right:20px;"><i class="zmdi zmdi-gif" style="font-size:40px;"></i></a>
    </div>
</form>
