@extends('layouts.app2')
@section('title')
Explore Groups
@endsection
@section('css_plugin')
	<link rel="stylesheet" type="text/css" href="{{asset('Bootstrap/dist/css/bootstrap-grid.css')}}">
	<link href="{{asset('js/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('js/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />

	<!-- Main Styles CSS -->
	<link rel="stylesheet" type="text/css" href="{{asset('css/main.min.css')}}">
	{{-- <link rel="stylesheet" type="text/css" href="{{asset('css/fonts.min.css')}}"> --}}
	<link rel="stylesheet" type="text/css" href="{{asset('js/img_crop/imgareaselect.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('css/blocks.css')}}" >
	<link rel="stylesheet" type="text/css" href="{{asset('css/slim.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('js/light-gallery/css/lightgallery.css')}}">

	<!-- Main Font -->
	<script src="{{asset('js/webfontloader.min.js')}}"></script>
	<script>
		WebFont.load({
			google: {
				families: ['Roboto:300,400,500,700:latin']
			}
		});
	</script>
@endsection
@section('content')
	<div id="content_wrapper">
		<input type="hidden" id="loading-gif-img-for-all" value="{{asset('assets/images/group_component/ajax_loading_.gif')}}">
		<?php
			$member_check = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
			$request_check = \App\IoGroupRequest::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('method', 1)->count();
			$user_request_check = \App\IoGroupRequest::where('group_id', $group->id)->where('user_id', Auth::user()->id)->where('method', 0)->count();

			$group_user_count = \App\IoGroupUser::where('group_id', $group->id)->count();
			$group_ba_user_count = \App\IoGroupUser::where('group_id', $group->id)->where('user_level', 2)->count();
			$group_request_check = \App\IoGroupRequest::where('group_id', $group->id)->where('method', 1)->count();
			$total_member_count = $group_user_count - $group_ba_user_count + $group_request_check + 1;
		?>
		<div id="header_wrapper" class="header-sm">
	        <div class="container-fluid">
	            <div class="row">
	                <div class="col-xs-12">
	                    <header id="header">
	                        <h1>Group: {{$group->name}}</h1>
	                    </header>
	                </div>
	            </div>
	        </div>
	    </div>
		<div class="header-spacer-very-small"></div>
		<div class="container-fluid">
			<div class="group-view-container">
				<div class="group-left-sidebar-content">
					@include('module.groupIntro')
				</div>
				<div class="group-main-container">
					<div class="row">
						<div class="col-xs-12">
							<div class="row">
								@include('module.groupHeader')
							</div>
							<div class="row">
								@if(Route::currentRouteName()=='group.single.view')
									@include('module.groupPosts')
									@include('module.groupAction')
								@elseif(Route::currentRouteName()=='group.single.view.about')
									@include('module.aboutGroup')
									@include('module.groupAction')
								@elseif(Route::currentRouteName()=='group.single.view.members')
									@include('module.membersGroup')
									@include('module.groupAction')
								@elseif(Route::currentRouteName()=='group.single.view.blocked')
									@include('module.blockedGroup')
									@include('module.groupAction')
								@elseif(Route::currentRouteName()=='group.single.view.photos')
									@include('module.groupPhotos')
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="invite-link-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Invite people to our group ({{$group->name}})</h4>
					<ul class="card-actions icons right-top">
						<li>
							<a href="javascript:void(0)" class="text-white" data-dismiss="modal" aria-label="Close">
								<i class="zmdi zmdi-close"></i>
							</a>
						</li>
					</ul>
				</div>
				<div class="modal-body">
					<div class="form-group group-invite-modal-form-group">
						<a href="javascript:;" id="group-invite-link-copy-btn" class="invite-link-copy-btn">Copy</a>
						<input type="text" id="group-invite-link-copy-input" value="{{url('group/invite/'.$group->invite)}}" readonly>
					</div>
					<div class="form-group">
						<div class="togglebutton">
						  <label><input type="checkbox" @if ($group->expire == 1) checked @endif class="toggle-info" id="set_never_expire_invite_key" data-set_url="{{url('group/invite-expire/'.$group->id.'/')}}">Set this link to never expire</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="group_info_edit_modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="edit_avatar">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title" id="myModalLabel-2">Group Edite</h4>
	                <ul class="card-actions icons right-top">
	                    <li>
	                        <a href="javascript: void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
	                            <i class="zmdi zmdi-close"></i>
	                        </a>
	                    </li>
	                </ul>
	            </div>
				<form id="group_info_edit_modal_form" role="form" method="post" action="{{route('group.update')}}" accept-charset="UTF-8" enctype="multipart/form-data">
		            <div class="modal-body">
		                {{ csrf_field() }}
						<input type="hidden" name="group_id_for_edit" value="{{$group->id}}">
						<div class="form-group">
		                    <label for="group_name" class="control-label">Name your group</label>
		                    <input type="text" class="form-control" name="_group_name" id="_group_name" placeholder="E.g : Altpoket Employee" value="{{$group->name}}" required="" aria-required="true">
		                </div>
		                <div class="form-group">
		                    <label for="private_group" class="control-label">Select privacy</label>
		                    <select class="select form-control" id="_private_group" name="_private_group">
		                        <option value="0" @if ($group->private == 0) selected @endif>Public</option>
		                        <option value="1" @if ($group->private == 1) selected @endif>Private</option>
		                    </select>
		                </div>
		                <div class="form-group">
		                    <label for="group_description" class="control-label">Group Description</label>
		                    <textarea class="form-control" style="background-image:none!important;" id="_group_description" name="_group_description" placeholder="">{{$group->description}}</textarea>
		                </div>
						<button type="submit" class="btn btn-primary">Update Group</button>
		            </div>
				</form>
	        </div>
	        <!-- modal-content -->
	    </div>
	    <!-- modal-dialog -->
	</div>

	<div class="modal fade" id="group_cover_photo_upload" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="group_cover_photo_upload">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">

	                <h4 class="modal-title" id="myModalLabel-2">Photo Upload</h4>
	                <ul class="card-actions icons right-top">
	                    <li>
	                        <a href="#" data-dismiss="modal" class="text-white" aria-label="Close">
	                            <i class="zmdi zmdi-close"></i>
	                        </a>
	                    </li>
	                </ul>
	            </div>
	            <div class="modal-body">
	                <form id="group_cover_photo_upload_form" role="form" method="post" action="{{route('group.coverphoto.upload')}}" accept-charset="UTF-8" enctype="multipart/form-data">
	                    {{ csrf_field() }}
						<input type="hidden" name="group_id_for_photo" id="group_id_for_photo" value="{{$group->id}}">
	                    <div class="slim" data-ratio="3:1">
	                        <input type="file" name="slim[]" required/>
	                    </div>
						<button type="submit" class="btn btn-primary">Upload Photo</button>
	                </form>
	            </div>
	        </div>
	        <!-- modal-content -->
	    </div>
	    <!-- modal-dialog -->
	</div>

	<div class="modal fade" id="choose-from-my-photo">
		<div class="modal-dialog choose-from-my-photo">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Select from group photo</h4>
					<ul class="card-actions icons right-top">
						<li>
							<a href="javascript:void(0)" class="text-white" data-dismiss="modal" aria-label="Close">
								<i class="zmdi zmdi-close"></i>
							</a>
						</li>
					</ul>
				</div>
				<div class="modal-body">
					<form id="chooseimge-from-photo" action="{{route('group.coverphoto.choose')}}" method="post">
						<input type="hidden" name="choose_photo_group_id" id="choose_photo_group_id" value="{{$group->id}}">
						<div class="row" id="choose-from-group-photo-container">
							@foreach ($group_photos as $group_photo)
								@if (file_exists('assets/images/group/'.$group->url.'/'.$group_photo->photo))
									<div class="col-sm-4">
										<div class="choose-photo-item">
											<div class="radio">
												<label class="custom-radio">
													<img src="{{asset('assets/images/group/'.$group->url.'/'.$group_photo->photo)}}" alt="photo">
													<input type="radio" name="group_photo_radio" value="{{$group_photo->id}}">
												</label>
											</div>
										</div>
									</div>
								@endif
							@endforeach
						</div>
						<div class="row">
							<div class="col-md-12" style="text-align:center;padding-top:20px;">
								<button type="submit" class="btn btn-primary btn--half-width">Confirm Photo</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="add-group-member-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="add-group-member-modal-form" action="{{route('group.add.member')}}" method="post">
					<div class="modal-header">
						<h4 class="modal-title">Add Member</h4>
						<ul class="card-actions icons right-top">
							<li>
								<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
									<i class="zmdi zmdi-close"></i>
								</a>
							</li>
						</ul>
					</div>
					<div class="modal-body">
						<input type="hidden" name="add_member_group_id" value="{{$group->id}}">
						<div class="form-group">
							<label for="inputEmail3">Add some people</label>
							<select id="autocomplete-user" name="request_user[]" class="form-control autocomplete-user" data-ajax-url="{{url('group/agroupadd-utouser/'.$group->id)}}" multiple>
							</select>
						</div>
						<button type="submit" id="group-add-member-send-btn" class="btn btn-primary">Add Member</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Window-popup Create Friends Group -->
	@include('module.newGroupForm')
	<!-- ... end Window-popup Create Friends Group -->
</div>
@endsection
@section('js_plugin')
	<script src="{{asset('js/slim.kickstart.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/select2/components-select2.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/light-gallery/js/lightgallery-all.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/magnific/jquery.magnific-popup.min.js')}}"></script>
	<script src="{{asset('js/groups/autosize.min.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/groups/group.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/groups/group_member.js')}}" type="text/javascript"></script>
@endsection
@section('js2')
	<script type="text/javascript">
		if ($('#group-slim-img').length > 0) {
			var cropper_group_post = new Slim(document.getElementById('group-slim-img'));
		}
		var group_id = {{$group->id}};
		$('.youtube-popup-link').magnificPopup({
    		type: 'iframe'
    	});

		var new_post = pusher.subscribe('newGroupPostEvent');
        new_post.bind('newGroupPostListner', function(data){
            var new_post_base_url = "{{url('/group/post/get-push/')}}";
            var new_post_final_url = new_post_base_url+"/"+data.postId;
            var my_id = {{Auth::user()->id}};
			if (data.groupId == group_id) {
				$.ajax({
	                url: new_post_final_url,
	                type: "get"
	            }).done(function(result){
	                $('#newsfeed-items-grid').find('.group-post-container-div').prepend(result.html);
					$('#group_post_number_'+data.postId).css({'display': 'block'});
					$('textarea').each(function() {
				        autosize(this);
				    });

					$('.js-zoom-gallery').each(function(){
				        var $this = $(this);
				        $this.lightGallery({
				            thumbnail: true,
				            selector: 'a'
				        });
				    });

					$('.youtube-popup-link').magnificPopup({
	                    type: 'iframe'
	                });
					set_imgcontainer_size();
	            }).fail(function(jqXHR, ajaxOptions, thrownError){
	                console.log('server not responding...');
	            });
			}
        });

		var edit_post = pusher.subscribe('editGroupPostEvent');
        edit_post.bind('editGroupPostListner', function(data){
            var new_post_base_url = "{{url('/group/post/get-push-edit/')}}";
            var new_post_final_url = new_post_base_url+"/"+data.postId;
            var my_id = {{Auth::user()->id}};
			if (data.groupId == group_id) {
				$.ajax({
	                url: new_post_final_url,
	                type: "get"
	            }).done(function(result){
	                $('#group_post_number_'+data.postId).html(result.html);
					$('#group_post_number_'+data.postId).css({'display': 'block'});
					$('textarea').each(function() {
				        autosize(this);
				    });

					$('.js-zoom-gallery').each(function(){
				        var $this = $(this);
				        $this.lightGallery({
				            thumbnail: true,
				            selector: 'a'
				        });
				    });

					$('.youtube-popup-link').magnificPopup({
	                    type: 'iframe'
	                });
					set_imgcontainer_size();
	            }).fail(function(jqXHR, ajaxOptions, thrownError){
	                console.log('server not responding...');
	            });
			}
        });

		var delete_post = pusher.subscribe('deleteGroupPostEvent');
        delete_post.bind('deleteGroupPostListner', function(data){
            var my_id = {{Auth::user()->id}};
			if (data.groupId == group_id) {
				if (my_id != data.userId) {
					$('#group_post_number_'+data.postId).remove();
				}
			}
        });

		var new_comment = pusher.subscribe('newGroupCommentEvent');
        new_comment.bind('newGroupCommentListner', function(data){
            var new_comment_base_url = "{{url('/group/comment/get-push/')}}";
            var new_comment_final_url = new_comment_base_url+"/"+data.commentId;
            var my_id = {{Auth::user()->id}};
			if (my_id != data.userId) {
				$.ajax({
	                url: new_comment_final_url,
	                type: "get"
	            }).done(function(result){
	                $('#group_post_number_'+data.postId).find('.posted-comment-container').append(result.html);
					$('#group_post_number_'+data.postId).find('.posted-comment-container').find('.posted-comment-separator').css({'display': 'block'});
					$('textarea').each(function() {
				        autosize(this);
				    });
					$('.youtube-popup-link').magnificPopup({
	                    type: 'iframe'
	                });
	            }).fail(function(jqXHR, ajaxOptions, thrownError){
	                console.log('server not responding...');
	            });
			}
        });

		var edit_comment = pusher.subscribe('editGroupCommentEvent');
        edit_comment.bind('editGroupCommentListner', function(data){
            var edit_comment_base_url = "{{url('/group/comment/get-push-edit/')}}";
            var edit_comment_final_url = edit_comment_base_url+"/"+data.commentId;
            var my_id = {{Auth::user()->id}};
			if (my_id != data.userId) {
				$.ajax({
	                url: edit_comment_final_url,
	                type: "get"
	            }).done(function(result){
					console.log(result);
	                $('#group_post_comment_'+data.commentId).html(result.html);
	            }).fail(function(jqXHR, ajaxOptions, thrownError){
	                console.log('server not responding...');
	            });
			}
        });

		var delete_comment = pusher.subscribe('deleteGroupCommentEvent');
        delete_comment.bind('deleteGroupCommentListner', function(data){
            var my_id = {{Auth::user()->id}};
			if (my_id != data.userId) {
				$('#group_post_comment_'+data.commentId).remove();
			}
        });

		var new_reply = pusher.subscribe('newGroupCommentReplyEvent');
        new_reply.bind('newGroupCommentReplyListner', function(data){
            var new_reply_base_url = "{{url('/group/reply/get-push/')}}";
            var new_reply_final_url = new_reply_base_url+"/"+data.replyId;
            var my_id = {{Auth::user()->id}};
			if (my_id != data.userId) {
				$.ajax({
	                url: new_reply_final_url,
	                type: "get"
	            }).done(function(result){
					var reply_container = $('#group_post_comment_'+data.commentId).find('.posted-reply-container');
					reply_container.append(result.html);
	            }).fail(function(jqXHR, ajaxOptions, thrownError){
	                console.log('server not responding...');
	            });
			}
        });

		var edit_reply = pusher.subscribe('editGroupCommentReplyEvent');
        edit_reply.bind('editGroupCommentReplyListner', function(data){
            var edit_reply_base_url = "{{url('/group/reply/get-push-edit/')}}";
            var edit_reply_final_url = edit_reply_base_url+"/"+data.replyId;
            var my_id = {{Auth::user()->id}};
			if (my_id != data.userId) {
				$.ajax({
	                url: edit_reply_final_url,
	                type: "get"
	            }).done(function(result){
					var current_reply = $('#group-reply_'+data.replyId);
					current_reply.html(result.html);
	            }).fail(function(jqXHR, ajaxOptions, thrownError){
	                console.log('server not responding...');
	            });
			}
        });

		var delete_reply = pusher.subscribe('deleteGroupCommentReplyEvent');
        delete_reply.bind('deleteGroupCommentReplyListner', function(data){
            var my_id = {{Auth::user()->id}};
			if (my_id != data.userId) {
				$('#group-reply_'+data.replyId).remove();
			}
        });

		var new_vote = pusher.subscribe('newGroupVoteEvent');
        new_vote.bind('newGroupVoteListner', function(data){
            var new_vote_base_url = "{{url('/group/poll/vote-get/')}}";
            var new_vote_final_url = new_vote_base_url+"/"+data.postId;
            $.ajax({
                url: new_vote_final_url,
                type: 'get',
                success: function(result){
                    if (result == "fail") {
                        console.log("failed");
                    }else {
                        result.poll_answers.forEach(function(answer) {
                            // console.log(answer);
                            var answer_container = $('li.answer-container_'+answer.answer_id);
                            answer_container.find('.skills-item-info>.skills-item-count>.units').html(answer.vote_percent+"%");
                            // answer_container.find('.skills-item-info>.skills-item-title input.vote.option-input').attr('checked', true);
                            answer_container.find('.skills-item-meter>.skills-item-meter-active').css({'width': answer.vote_percent+"%"});
                            var counter_html = "";
                            if (answer.vote_count == 0) {
                                counter_html = '<span class="answer-'+answer.answer_id+'">0</span> users voted on this';
                            } else if (answer.vote_count == 1) {
                                counter_html = '<span class="answer-'+answer.answer_id+'">1</span> user voted on this';
                            } else if (answer.vote_count > 1) {
                                counter_html = '<span class="answer-'+answer.answer_id+'">'+answer.vote_count+'</span> users voted on this';
                            }
                            answer_container.find('.counter-friends').html(counter_html);
                            var users_htmls = "";
                            var show_user_count = 0;
                            answer.vote_users.forEach(function(user) {
                                if (show_user_count <= 10) {
                                    users_htmls += '<li><a href="/user/'+user.username+'">';
                                    if (user.avatar != "default.jpg") {
                                        users_htmls += '<img src="/uploads/avatars/'+user.username+'/'+user.avatar+'" alt="friend">';
                                    } else {
                                        users_htmls += '<img src="/assets/img/default.png" alt="friend">';
                                    }
                                    users_htmls += '</a></li>';
                                }
                            });

                            if (answer.vote_count > 10) {
                                var minused_count = answer.vote_count - 10;
                                users_htmls += '<li><a href="#" class="all-users">+'+minused_count+'</a></li>';
                            }
                            answer_container.find('ul.friends-harmonic').html(users_htmls);
                        });
                        $('#group_post_number_'+data.postId).find('article.group-post-single-container').removeClass('voting');
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        });
	</script>
@endsection
