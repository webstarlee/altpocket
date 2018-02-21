<div class="col-xl-8 order-xl-1 col-lg-8 col-md-8 col-sm-12 col-xs-12">
	<div id="newsfeed-items-grid">
		<?php
			$member_check = \App\IoGroupUser::where('group_id', $group->id)->where('user_id', Auth::user()->id)->count();
		?>
		@if ($member_check > 0 || $group->user_id == Auth::user()->id)
			<div class="ui-block">
	            <form class="comment-form group-post-form" id="group-post-container-form" action="{{route('group.post.create')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
					<div class="post__author author vcard">
						{{ csrf_field() }}
						@if(Auth::user()->avatar == "default.jpg")
							<img src="{{asset('assets/img/default.png')}}" alt="" class="img-circle">
						@else
							@if (file_exists('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar))
								<img src="{{asset('uploads/avatars/'.Auth::user()->id.'/'.Auth::user()->avatar)}}" alt="author">
							@else
								<img src="/assets/img/default.png" alt="" class="img-circle">
							@endif
						@endif
						<input type="hidden" name="for_post_group_id" value="{{$group->id}}">
						<div class="form-group with-icon-right is-empty">
							<textarea class="form-control form-control-2" style="background-image:none!important;"  name="group_post_text" id="group_post_text" placeholder="Write something ..."></textarea>
						</div>
					</div>
					<div class="post_photo_container post-photo-container-div">
						<div class="choose-photo-for-post-big-container" style="display:none;">

						</div>
					</div>
					<div class="comment-btn-container">
						<a class="group-post-photo" href="javascript:;" id="photo-upload-btn-for-post-add" style="float:left; margin-top:5px;margin-right:15px;"><i class="zmdi zmdi-camera"></i></a>
						<a class="group-post-photo" href="javascript:;" id="post-youtube-add" style="float:left; margin-top:5px;margin-right:13px;"><i class="zmdi zmdi-youtube"></i></a>
						<a class="group-post-photo" href="{{url('status/get-giphy')}}" id="post-giphy-add" style="float:left; margin-top: -1px;"><i class="zmdi zmdi-gif" style="font-size: 35px;"></i></a>
                    </div>
					<button type="submit" class="btn btn-blue comment-post-btn" id="group-post-satff-submit-btn" style="float:unset;" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Posting">Post<div class="ripple-container"></div></button>
					<button type="button" class="btn btn-primary" id="group-post-poll-popup" style="float:unset;" data-toggle="modal" href="#group_poll_modal">Create Poll<div class="ripple-container"></div></button>
				</form>
	        </div>
		@endif
		<div class="group-post-container-div">
			@foreach ($group_posts as $group_post)
				@include('module.groupSinglePost')
			@endforeach
		</div>
	</div>
	<a id="load-more-button" href="#" class="btn-more load-more-group-post" data-load-link="items-to-load.html" data-container="newsfeed-items-grid"> <span class="now_loading" style="display:none;"><img src="{{asset('assets/images/group_component/ajax_loading.gif')}}" alt=""></span> <span class="show_text">Load More</span> </a>
</div>

<div class="modal fade" id="edit-group-post-modal" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Edit Post</h4>
			</div>
			<div class="modal-body">
				<form id="edit-group-post-modal-form" action="{{route('group.post.update')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
					{{ csrf_field() }}
					<input type="hidden" name="group_post_id_for_edit" id="group_post_id_for_edit" value="">
					<div class="form-group with-icon-right is-empty">
						<textarea class="form-control form-control-2" style="background-image:none!important;" name="post_text_textarea" id="post_text_textarea" placeholder="Write something ..."></textarea>
					</div>
					<div class="post_photo_container post-photo-container-div">
					</div>
					<div class="media-upload-btn-container">
                        <a href="javascript:;" id="post-youtube-add-edit"><i class="zmdi zmdi-youtube" style="font-size:27px;"></i></a>
                        <a href="javascript:;" data-toggle="modal" data-target="#group_post_photo_upload"><i class="zmdi zmdi-camera" style="font-size:22px;"></i></a>
                        <a href="{{url('status/get-giphy')}}" id="post-giphy-add-edit"><i class="zmdi zmdi-gif" style="font-size:35px;position:absolute;"></i></a>
                    </div>
					<button class="btn btn-danger" id="edit-group-post-modal-cancel-btn">Cancel</button>
					<button type="submit" class="btn btn-primary" id="edit-group-post-modal-submit-btn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Updating">Update post</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="group_post_photo_upload" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="edit_avatar">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel-2">Photo Upload</h4>
                <ul class="card-actions icons right-top">
                    <li>
                        <a href="javascript: void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                            <i class="zmdi zmdi-close"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="modal-body">
                <form id="group_post_photo_upload_form" role="form" method="post" action="{{route('group.post.photo.upload')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                    {{ csrf_field() }}
					<input type="hidden" name="group_id_for_photo" value="{{$group->id}}">
                    <div class="slims" id="group-slim-img">
                        <input type="file" class="upload-img-slim" name="slim[]"/>
                    </div>
					<button type="submit" class="btn btn-primary">Upload Photo</button>
                </form>
            </div>
        </div>
        <!-- modal-content -->
    </div>
    <!-- modal-dialog -->
</div>

<div class="modal fade" id="add_youtubeurl" tabindex="-1" role="dialog" aria-labelledby="add_image">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel-2">Add Youtube url to post</h4>
				<ul class="card-actions icons right-top">
					<li>
						<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
							<i class="zmdi zmdi-close"></i>
						</a>
					</li>
				</ul>
			</div>
			<div class="modal-body">
				{{ csrf_field() }}
				<div class="form-group is-empty">
					<label for="" class="control-label">Youtube URL</label>
					<input type="text" class="form-control typeahead" id="youtube_url" placeholder="Youtube url" required/>
				</div>
				<button class="btn btn-primary" id="add-youtubeurl-button">Add Url</button>
			</div>
		</div>
		<!-- modal-content -->
	</div>
	<!-- modal-dialog -->
</div>

<div class="modal fade" id="group_poll_modal" tabindex="-1" role="dialog" aria-labelledby="poll_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel-2">Create Poll</h4>
				<ul class="card-actions icons right-top">
					<li>
						<a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
							<i class="zmdi zmdi-close"></i>
						</a>
					</li>
				</ul>
			</div>
			<div class="modal-body">
				<form id="group-poll-create-form" class="tracking-form" role="form" method="post" action="/group/poll-create">
					{{ csrf_field() }}
					<input type="hidden" name="group_id_for_poll" value="{{$group->id}}">
					<div class="form-group is-empty">
						<label for="" class="control-label">Question/Title</label>
						<input type="text" class="form-control" id="question" autocomplete="off" placeholder="Poll Title" value="" name="question" required/>
					</div>
					<div class="form-group is-empty">
						<label for="" class="control-label answer-number">Option/Answer #<span>1</span></label>
						<input type="text" class="form-control poll-answer-field" autocomplete="off" placeholder="Answer" value="" name="answers[]" required/>
					</div>
					<div class="form-group is-empty">
						<label for="" class="control-label answer-number">Option/Answer #<span>2</span></label>
						<input type="text" class="second-field form-control poll-answer-field last" autocomplete="off" placeholder="Answer" value="" name="answers[]" required/>
					</div>
					<button type="submit" class="btn btn-primary group-poll-create-submit-btn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Creating">Create Poll</button>
				</form>
			</div>
		</div>
		<!-- modal-content -->
	</div>
	<!-- modal-dialog -->
</div>
