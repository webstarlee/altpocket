<div class="post__author author vcard inline-items">
    @if($reply->avatar != "default.jpg")
        <img src="/uploads/avatars/{{$reply->userid}}/{{$reply->avatar}}" alt="author">
    @else
        <img src="/assets/img/default.png" alt="author">
    @endif

    <div class="author-date">
        <a class="h6 post__author-name fn" href="/user/{{$reply->username}}">{{$reply->username}}</a>
        <div class="post__date">
            <time class="published" datetime="{{$reply->created_at}}">
                {{$reply->created_at->diffForHumans()}}
            </time>
        </div>
    </div>

    @if(Auth::user()->id == $reply->userid || Auth::user()->isFounder() || Auth::user()->isStaff())
        <ul class="card-actions icons right-top">
            <li class="dropdown">
                <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">
                    <i class="zmdi zmdi-more"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right btn-primary">
                    <li>
                        <a href="javascript:void(0)" class="edit-comment-reply" id="{{$reply->id}}">Edit Reply</a>
                    </li>
                    <li>
                        <a href="/commentreply/delete/{{$reply->id}}" class="reply-delete-btn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Deleting">Delete Reply</a>
                    </li>
                </ul>
            </li>
        </ul>
    @endif
</div>
@if ($reply->reply)
    <p style="word-wrap: break-word;">{{$reply->reply}}</p>
@endif
@if ($reply->giphys)
    <div class="form-group post-media-container">
        <?php $total_giphys = unserialize($reply->giphys) ?>
        @foreach ($total_giphys as $single_giphy)
            <div class="media-single">
                <a class="image-popup-link" href="{{$single_giphy}}">
                    <img src="{{$single_giphy}}" alt="">
                </a>
            </div>
        @endforeach
    </div>
@endif
<a href="javascript:void(0)" class="post-add-icon inline-items heart like-comment-reply @if(Auth::user()->hasLikedCommentReply($reply)) liked @endif" status="{{$reply->id}}">
    <i class="fa fa-heart"></i>
    <span>{{$reply->getLikes()}}</span>
</a>
<a href="javascript:void(0)" class="post-add-icon inline-items heart comment-reply-button-child" style="margin-left: 10px;font-size:13px;"> <i class="zmdi zmdi-mail-reply" style="width:auto;height:auto;"></i> Reply</a>
