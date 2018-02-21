$(document).ready(function(){

    var global_target_form = null;
    var global_comment_data_for_edit = "";
    var post_contents_count = 0;

    $("div.group-post-single-div").slice(0, 5).show();

    $("div.group-post-single-div").each(function(){
        post_contents_count += 1;
    });

    if (post_contents_count < 6) {
        $("#load-more-button").css({"display": 'none'});
    }

    $('.js-zoom-gallery').each(function(){
        var $this = $(this);
        $this.lightGallery({
            thumbnail: true,
            selector: 'a'
        });
    });

    $("#load-more-button").on('click', function (e) {
        e.preventDefault();
        if ($("div.group-post-single-div:hidden").length == 0) {
            $("#load-more-button").fadeOut();
        }else {
            var button = $(this);
            button.find('.show_text').css({'display': 'none'});
            button.css({'background-color':'#577ef5'});
            button.find('.now_loading').css({'display': 'block'});

            setTimeout(function(e) {
                button.find('.show_text').css({'display': 'block'});
                button.css({'background-color':'#ccd1e0'});
                button.find('.now_loading').css({'display': 'none'});
                $("div.group-post-single-div:hidden").slice(0, 5).slideDown();

                if ($("div.group-post-single-div:hidden").length == 0) {
                    $("#load-more-button").fadeOut('slow');
                }
            }, 2000);
        }
    });

    $('.group-cover-photo-view-container').mouseover( function(){
		$('.group-cover-photo-setting-btn-div').css({'display':'block'});
	}).mouseout(function(event) {
        var $this = $(this);
        var e = event.toElement || event.relatedTarget;
        var current_parent = $(e).parents('.group-cover-photo-view-container');
        if (!current_parent.length) {
            $('.group-cover-photo-setting-btn-div').css({'display':'none'});
        }
	});

    $('.remove-group-couver-pic').on('click', function(){
        var $this = $(this);
		swal({
		  title: "Are you sure?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#0ed2d0",
		  confirmButtonText: "Yes, Delete!",
		  cancelButtonText: "No, cancel!",
		  showLoaderOnConfirm: true,
		  closeOnConfirm: true,
		  closeOnCancel: true
		}, function (isConfirm) {
			if (isConfirm) {
				var group_id = $('#group_id_for_photo').val();
				var delete_url = $this.data('delete_url');
				var final_delete_url = delete_url+"/"+group_id;
				$.ajax({
					url: final_delete_url,
					type: 'get',
					success: function(result){
						location.reload();
					},
					error: function(error){
						console.log(error);
					}
				});
			} else {

			}
		});
	});

    $('#group_join_request_decline_btn').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var decline_url = $this.attr('href');
        swal({
		  title: "Are you sure?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#0ed2d0",
		  confirmButtonText: "Yes, Decline!",
		  cancelButtonText: "No, cancel!",
		  closeOnConfirm: true,
		  closeOnCancel: true
		}, function (isConfirm) {
			if (isConfirm) {
				//console.log("confirmed");
                window.location.href = decline_url;
			} else {

			}
		});
    });

    $('#add-group-member-modal-form').submit(function(e){
        e.preventDefault();
        var form = $(this)[0];
        var selected_user = $('#autocomplete-user').val();
        if (selected_user) {
            $('#group-add-member-send-btn').html('SENDING ...');

            $.ajaxSetup({
    			headers: {
    				'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
    			}
    		});
    		var url = $(form).attr( 'action' );

    		var formData = new FormData($(form)[0]);

			$.ajax({
				url: url,
				type: 'POST',
				data: formData,
				success: function (data) {
                    $('#autocomplete-user').select2("val", "");
                    $('#group-add-member-send-btn').html('ADD MEMBER');
                    $('#add-group-member-modal').modal('hide');
                    if (data != "fail") {
                        var memeber_list_ul = $('div.ui-block-content.group-member-contents ul');
                        data.forEach(function(user){
                            var new_user_li = '<li style="text-align:center;display:inline-block;">'+
                    						'<a href="'+user['user_url']+'" target="_blank">'+
        									'<img src="'+user['avatar_url']+'" alt="" class="photo"></a></li>';
                            memeber_list_ul.append(new_user_li);
                        });
                        swal({
                            title: "Success!",
                            text: "Request has been sent successfully.",
                            timer: 1000,
                            type: "success",
                            showCancelButton: false,
                            showConfirmButton: false
                        });
                    }else {
                        swal({
                            title: "Failed!",
                            text: "Went Something Wrong.",
                            timer: 1000,
                            type: "error",
                            showCancelButton: false,
                            showConfirmButton: false
                        });
                    }

				},
				processData: false,
				contentType: false,
				error: function(data)
			   {
				   console.log(data);
			   }
			});
        }
        else {
            alert('please select');
        }
    })

    $('.send-group-join-request-user').each(function() {
        var $this = $(this);
        $this.on('click', function(e) {
            var user_join_url = $this.data('join_url');
            $.ajax({
                url: user_join_url,
                type: 'get',
                success: function(data){
                    //console.log(data);
                    if (data == "success") {
                        $this.css({'display': 'none'});
                        $this.parent().find('.cancel-group-join-request-user').css({'display': 'block'});
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        });
    });

    $('.cancel-group-join-request-user').each(function() {
        var $this = $(this);
        $this.on('click', function(e) {
            var user_join_url = $this.data('join_cancel_url');
            $.ajax({
                url: user_join_url,
                type: 'get',
                success: function(data){
                    //console.log(data);
                    if (data == "success") {
                        $this.css({'display': 'none'});
                        $this.parent().find('.send-group-join-request-user').css({'display': 'block'});
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        });
    });

    $('.own-group-delete-btn').each(function(){
        var $this = $(this);
        $this.on('click', function(e) {
            e.preventDefault();
            var own_group_delete_url = $this.attr('href');
            swal({
    		  title: "Are you sure?",
    		  type: "warning",
    		  showCancelButton: true,
    		  confirmButtonColor: "#0ed2d0",
    		  confirmButtonText: "Yes, Delete!",
    		  cancelButtonText: "No, cancel!",
              showLoaderOnConfirm: true,
    		  closeOnConfirm: false,
    		  closeOnCancel: true
    		}, function (isConfirm) {
    			if (isConfirm) {
                    $.ajax({
                        url: own_group_delete_url,
                        type: 'get',
                        success: function(data){
                            //console.log(data);
                            if (data != "fail") {
                                window.location.href = data;
                            }
                        },
                        error: function(error){
                            console.log(error);
                        }
                    });
    			} else {

    			}
    		});
        });
    });

    $('#own-group-delete-btn-on').on('click', function(e) {
        e.preventDefault();
        var own_group_delete_url = $(this).attr('href');
        swal({
          title: "Are you sure?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#0ed2d0",
          confirmButtonText: "Yes, Delete!",
          cancelButtonText: "No, cancel!",
          showLoaderOnConfirm: true,
          closeOnConfirm: false,
          closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: own_group_delete_url,
                    type: 'get',
                    success: function(data){
                        //console.log(data);
                        if (data != "fail") {
                            window.location.href = data;
                        }
                    },
                    error: function(error){
                        console.log(error);
                    }
                });
            } else {

            }
        });
    })

    $('#group_join_request_decline_btn_on').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var decline_url = $this.attr('href');
        swal({
		  title: "Are you sure?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#0ed2d0",
		  confirmButtonText: "Yes, Decline!",
		  cancelButtonText: "No, cancel!",
		  closeOnConfirm: true,
		  closeOnCancel: true
		}, function (isConfirm) {
			if (isConfirm) {
                window.location.href = decline_url;
			} else {

			}
		});
    });

    $('#chooseimge-from-photo').submit(function(e){
        e.preventDefault();
		var $this = $(this);

		var ischecked = 0
		$("input[name=group_photo_radio]:radio").each(function(){
			if ($(this).prop('checked')) {
				ischecked += 1;
			}
		});
		if (ischecked == 0) {
			alert("Please select photo");
		}
		if (ischecked > 0) {
			$.ajaxSetup({
				headers: {
					'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
				}
			});
			var form = $this[0];
			var url = $(form).attr( 'action' );

			var formData = new FormData($(form)[0]);

			$.ajax({
				url: url,
				type: 'POST',
				data: formData,
				success: function (data) {
					//console.log(data);
                    // var img_html = '<img src="'+data.img_url+'">';
                    $('#group_cover_photo_upload_form div.slim').find('img').attr('src', data.img_url);
					$('#choose-from-my-photo').modal('hide');
                    setTimeout(function(){
                        $('#group_cover_photo_upload').modal('show');
                        // $('#group_cover_photo_upload_form').find('.slim').slim();
                    }, 500);
				},
				processData: false,
				contentType: false,
				error: function(data)
			   {
				   console.log(data);
			   }
			});
		}
    });

    $('.user_leave_group_btn').each(function(){
        var $this = $(this);
        $this.on('click', function(e) {
            e.preventDefault();
            var group_leave_url = $this.attr('href');
            swal({
    		  title: "Are you sure?",
    		  type: "warning",
    		  showCancelButton: true,
    		  confirmButtonColor: "#0ed2d0",
    		  confirmButtonText: "Yes, Leave!",
    		  cancelButtonText: "No, cancel!",
    		  closeOnConfirm: true,
    		  closeOnCancel: true
    		}, function (isConfirm) {
    			if (isConfirm) {
                    window.location.href = group_leave_url;
    			} else {

    			}
    		});
        });
    });

    $('#user_leave_group_btn_on').on('click', function(e) {
        e.preventDefault();
        var group_leave_url = $(this).attr('href');
        swal({
          title: "Are you sure?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#0ed2d0",
          confirmButtonText: "Yes, Leave!",
          cancelButtonText: "No, cancel!",
          closeOnConfirm: true,
          closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                window.location.href = group_leave_url;
            } else {

            }
        });
    })

    $('#group-post-upload-photo-btn').on('click', function(e) {
        // e.preventDefault();
        $('#group_post_photo').modal('hide');
        setTimeout(function(){$('#group_post_photo_upload').modal('show');}, 500);
    });

    $('#group-post-choose-photo-btn').on('click', function(e) {
        // e.preventDefault();
        $('#group_post_photo').modal('hide');
        setTimeout(function(){$('#choose-from-my-photo-group-post').modal('show');}, 500);
    });

    $('#chooseimge-from-photo-for-group-post').submit(function(e) {
        e.preventDefault();
		var $this = $(this);

		var photo_id = null;
        var photo_url = null;
		$this.find("input[name=group_photo_radio]:radio").each(function(){
            $that = $(this);
			if ($that.prop('checked')) {
				photo_id = $that.val();
                photo_url = $that.parent().find('img').attr('src');
			}
		});

        if (photo_id != null && photo_url != null) {
            photo_add_for_post(photo_id, photo_url, global_target_form);
            $('#choose-from-my-photo-group-post').modal('hide');
        }
    });

    $('#group-post-container-form').submit(function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var istext = $(form).find('#group_post_text').val();
        var element = $(form).find('.post_photo_container');
        if (element.html().length > 0 || istext != "") {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
                }
            });

            var url = $(form).attr('action');
            var formData = new FormData(form);
            var post_button = $(form).find('#group-post-satff-submit-btn');
            post_button.button('loading');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    post_button.button('reset');
                    // swal("Complete!", "Comment reply Posted successfully.", "success");
                    $(form).find('#group_post_text').css({'height': '60px'});
                    $(form).find('.post_photo_container').html("");
                    form.reset();
                },
                processData: false,
                contentType: false,
                error: function(data)
               {
                   console.log(data);
               }
            });
        }
    });

    $('#edit-group-post-modal-form').submit(function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var istext = $(form).find('#post_text_textarea').val();
        var element = $(form).find('.post_photo_container');
        if (element.html().length > 0 || istext != "") {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
                }
            });

            var url = $(form).attr('action');
            var formData = new FormData(form);
            var update_button = $(form).find('#edit-group-post-modal-submit-btn');
            update_button.button('loading');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    update_button.button('reset');
                    // swal("Complete!", "Comment reply Posted successfully.", "success");
                    $(form).find('#post_text_textarea').css({'height': '60px'});
                    $(form).find('.post_photo_container').html("");
                    form.reset();
                    $('#edit-group-post-modal').modal('hide');
                },
                processData: false,
                contentType: false,
                error: function(data)
               {
                   console.log(data);
               }
            });
        }
    });

    $('textarea').each(function() {
        autosize(this);
    });

    $('.group-post-comment-form').each(function(e){
        var form = $(this);
        form.submit(function(e) {
            e.preventDefault();
        })
    })

    $(document).on('keypress', '.group-post-comment-form textarea[name=post_comment]', function(e) {
        var input = $(this);
        var form = input.parents('form.group-post-comment-form')[0];
        if (isCharacterKeyPress(e)) {
            input.css({'border': '1px solid #b9b8b8'});
        }
        if (e.which == 13) {

            if(!e.shiftKey){
                e.preventDefault();
                if (input.val().length <= 2000) {
                    post_new_comment(form);
                }
            }
        }
    });

    $(document).on('keyup keydown', '.group-post-comment-form textarea[name=post_comment]', function(e) {
        var input = $(this);
        var form = input.parents('form.group-post-comment-form')[0];
        $(form).find('p.limit-comment-text>.current_length').html(input.val().length);
        if (input.val().length <= 2000) {
            $(form).find('p.limit-comment-text').css({'color': 'rgb(26, 26, 26)'});
            input.css({'border': '1px solid #b9b8b8'});
        } else {
            $(form).find('p.limit-comment-text').css({'color': 'rgb(255, 76, 65)'});
            input.css({'border': '1px solid #e83d3d'});
        }
    });

    $(document).on('keypress', '.comment-reply-form textarea[name=post_reply]', function(e) {
        var input = $(this);
        if (isCharacterKeyPress(e)) {
            input.css({'border': '1px solid #b9b8b8'});
        }
        if (e.which == 13) {
            if(!e.shiftKey){
                e.preventDefault();
                var form = input.parents('form.comment-reply-form')[0];
                if (input.val().length <= 2000) {
                    post_new_reply(form);
                }
            }
        }
    });

    $(document).on('keyup keydown', '.comment-reply-form textarea[name=post_reply]', function(e) {
        var input = $(this);
        var form = input.parents('form.comment-reply-form')[0];
        $(form).find('p.limit-comment-text>.current_length').html(input.val().length);
        if (input.val().length <= 2000) {
            $(form).find('p.limit-comment-text').css({'color': 'rgb(26, 26, 26)'});
            input.css({'border': '1px solid #b9b8b8'});
        } else {
            $(form).find('p.limit-comment-text').css({'color': 'rgb(255, 76, 65)'});
            input.css({'border': '1px solid #e83d3d'});
        }
    });

    $(document).on('keypress', '.comment-reply-form textarea[name=post_reply_edit]', function(e) {
        var input = $(this);
        if (isCharacterKeyPress(e)) {
            input.css({'border': '1px solid #b9b8b8'});
        }
        if (e.which == 13) {
            if(!e.shiftKey){
                e.preventDefault();
                var form = input.parents('form.comment-reply-form')[0];
                global_comment_data_for_edit = "";
                if (input.val().length <= 2000) {
                    post_edit_reply(form);
                }
            }
        }
    });

    $(document).on('keyup keydown', '.comment-reply-form textarea[name=post_reply_edit]', function(e) {
        var input = $(this);
        var form = input.parents('form.comment-reply-form')[0];
        $(form).find('p.limit-comment-text>.current_length').html(input.val().length);
        if (input.val().length <= 2000) {
            $(form).find('p.limit-comment-text').css({'color': 'rgb(26, 26, 26)'});
            input.css({'border': '1px solid #b9b8b8'});
        } else {
            $(form).find('p.limit-comment-text').css({'color': 'rgb(255, 76, 65)'});
            input.css({'border': '1px solid #e83d3d'});
        }
    });

    $(document).on('click', '.group-post-comment-form a.post-comment-add-btn', function(e) {
        e.preventDefault();
        var form = $(this).parents('form.group-post-comment-form')[0];
        if ($(form).find('textarea[name=post_comment]').val().length <= 2000) {
            post_new_comment(form);
        }
    });

    $(document).on('click', 'a.reply-send-btn', function(e) {
        e.preventDefault();
        var form = $(this).parents('form.comment-reply-form')[0];
        if ($(form).find('textarea[name=post_reply]').val().length <= 2000) {
            post_new_reply(form);
        }
    });

    $(document).on('mouseover','.posted-comment-single-container',function(){
        var comment_setting_btn = $(this);
        comment_setting_btn.find('.comment-action-more').css({'display': 'block'});
    });

    $(document).on('mouseout','.posted-comment-single-container',function(){
        var comment_setting_btn = $(this);
        comment_setting_btn.find('.comment-action-more').css({'display': 'none'});
    });

    $(document).on('mouseover','.posted-reply-single-container',function(){
        var reply_setting_btn = $(this);
        reply_setting_btn.find('.reply-action-more').css({'display': 'block'});
    });

    $(document).on('mouseout','.posted-reply-single-container',function(){
        var reply_setting_btn = $(this);
        reply_setting_btn.find('.reply-action-more').css({'display': 'none'});
    });

    $(document).on('click', '.group-post-delete-btn', function(e) {
        e.preventDefault();
        var $this = $(this);
        var post_delete_url = $this.attr('href');
        var post_id = $this.data('post_id');
        swal({
		  title: "Are you sure?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#0ed2d0",
		  confirmButtonText: "Yes, Delete!",
		  cancelButtonText: "No, cancel!",
          showLoaderOnConfirm: true,
		  closeOnConfirm: false,
		  closeOnCancel: true
		}, function (isConfirm) {
			if (isConfirm) {
				// console.log(post_delete_url);
                $.ajax({
                    url: post_delete_url,
                    type: 'get',
                    success: function(data){
                        if (data == "success") {
                            swal("Deleted!", "Post Deleted.", "success");
                            $('#group_post_number_'+post_id).remove();
                            $("div.group-post-single-div").slice(0, 5).show();
                            if ($("div.group-post-single-div:hidden").length == 0) {
                                $("#load-more-button").fadeOut('slow');
                            }
                        }else {
                            swal("Failed!", "Deleted Failed.", "error");
                        }
                    },
                    error: function(error){
                        console.log(error);
                    }
                });
			} else {

			}
		});
    });

    $(document).on('click', '.group-post-edit-btn', function(e) {
        e.preventDefault();
        var $this = $(this);
        var post_get_url = $this.attr('href');

        $.ajax({
            url: post_get_url,
            type: 'get',
            success: function(data){
                if (data != "fail") {
                    console.log(data);
                    $('#edit-group-post-modal-form #group_post_id_for_edit').val(data.post_id);
                    $('#edit-group-post-modal-form textarea').val(data.post_text);
                    global_target_form = $('#edit-group-post-modal-form');
                    data.photo_datas.forEach(function(photo) {
                        // console.log(photo.photo_url);
                        photo_add_for_post(photo.photo_id, photo.photo_url, global_target_form)
                    });
                    data.giphy_datas.forEach(function(giphy) {
                        var html = '<div class="post-img-div">'+
                                    '<img src="'+giphy+'" alt="">'+
                                    '<input type="hidden" name="post_giphy[]" value="'+giphy+'">'+
                                    '<a class="selected-single-photo-delete-btn"><i class="zmdi zmdi-close"></i></a></div>';
                        global_target_form.find('.post_photo_container').append(html);
                    });
                    data.youtube_datas.forEach(function(youtube) {
                        $.getJSON("https://www.googleapis.com/youtube/v3/videos", {
                			key: "AIzaSyDgj1UaGgYELaZSvMAu1MST5i7oAyFwndQ",
                			part: "snippet,statistics",
                			id: youtube
                		}, function(data) {
                			if (data.items.length === 0) {
                				alert('something went wrong');
                				return;
                			}
                            var media_container = global_target_form.find('.post_photo_container');
                            var string = data.items[0].snippet.description;
                            var length = 100;
                            var description = string.substring(0, length);
                            var html = '<div class="youtube youtube-single">'+
                                        '<input type="hidden" name="post_youtube[]" value="'+youtube+'">'+
                                        '<a class="single-media-close-btn" href="javascript:void(0);"><i class="zmdi zmdi-close"></i></a>'+
                                        '<a href="https://www.youtube.com/watch?v='+youtube+'" target="_blank">'+
                                        '<div class="youtube img-container" style="background-image: url('+data.items[0].snippet.thumbnails.medium.url+');"></div>'+
                                        '<div class="youtube title-description-container">'+
                                        '<p class="title">'+data.items[0].snippet.title+'</p>'+
                                        '<p class="description">'+description+' ...</p>'+
                                        '<p class="youtube-com">youtube.com</p>'+
                                        '</div></a></div>';
                            media_container.html(html);
                		}).fail(function(jqXHR, textStatus, errorThrown) {
                			alert('something went wrong');
                		});
                    });
                    $('#edit-group-post-modal').modal('show');
                }
            },
            error: function(error){
                console.log(error);
            }
        });
    });

    $(document).on('click', '.selected-single-photo-delete-btn', function(e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    $(document).on('click', '.single-media-close-btn', function(e){
        e.preventDefault();
        $(this).parent().remove();
    });

    $('#photo-choose-btn-for-post-edit').on('click', function(e) {
        e.preventDefault();
        $this = $(this);
        global_target_form = $('#edit-group-post-modal-form');

        $('#choose-from-my-photo-group-post').modal('show');
    });

    $('#photo-choose-btn-for-post-add').on('click', function(e) {
        e.preventDefault();
        $this = $(this);
        global_target_form = $('#group-post-container-form');

        $('#choose-from-my-photo-group-post').modal('show');
    });

    $('#photo-upload-btn-for-post-add').on('click', function(e) {
        e.preventDefault();
        global_target_form = $('#group-post-container-form');

        $('#group_post_photo_upload').modal('show');
    });

    $('#photo-select-btn-for-add').on('click', function(e) {
        e.preventDefault();
        global_target_form = $('#group-post-container-form');

        $('#group_post_photo').modal('show');
    });

    $('#photo-select-btn-for-edit').on('click', function(e) {
        e.preventDefault();
        global_target_form = $('#edit-group-post-modal-form');

        $('#group_post_photo').modal('show');
    });

    $('#edit-group-post-modal-cancel-btn').on('click', function(e){
        e.preventDefault();
        $('#edit-group-post-modal-form')[0].reset();
        $('#edit-group-post-modal-form').find('.post-img-div').each(function(){
            $this = $(this);
            $this.remove();
        });
        $('#edit-group-post-modal-form').find('.choose-photo-for-post-big-container').css({'display': 'none'});
        $('#edit-group-post-modal').modal('hide');
    });

    $(document).on('click', '.group_post_comment_delete_btn', function(e) {
        e.preventDefault();
        var $this = $(this);
        var comment_id = $this.data('comment_id');
        var comment_div = $('#group_post_comment_'+comment_id);
        var default_delete_url = comment_div.parents('div.posted-comment-container').find('input[name=default_delete_url]:hidden').val();
        var comment_delete_url = default_delete_url+'/'+comment_id;

        swal({
          title: "Are you sure?",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#0ed2d0",
          confirmButtonText: "Yes, Delete!",
          cancelButtonText: "No, cancel!",
          showLoaderOnConfirm: true,
          closeOnConfirm: false,
          closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: comment_delete_url,
                    type: 'get',
                    success: function(data){
                        // console.log(data);
                        if (data == "success") {
                            var comment_container_div = comment_div.closest('div.posted-comment-container');
                            $('#group_post_comment_'+comment_id).remove();
                            if (comment_container_div.find('div.posted-comment-single-container').length == false) {
                                comment_container_div.find('div.posted-comment-separator').remove();
                            }

                            swal("Deleted!", "Comment Deleted.", "success");
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            } else {

            }
        });
    });

    $(document).on('click', '.group_post_comment_edit_btn', function() {
        $this = $(this);
        var comment_id = $this.data('comment_id');
        var comment_div = $('#group_post_comment_'+comment_id);
        var default_delete_url = comment_div.parents('div.posted-comment-container').find('input[name=default_edit_url]:hidden').val();
        var comment_text_container_div = comment_div.find('div.posted-comment-text-container');
        global_comment_data_for_edit = "";
        var div = comment_text_container_div[0];
        var children = div.childNodes;
        var elements = [];
        for (var i=0; i<div.childNodes.length; i++) {
            var child = div.childNodes[i];
            if (child.nodeType == 1) {
                var str = $(child).prop('outerHTML')
                global_comment_data_for_edit = global_comment_data_for_edit.concat(str);
            }
        }
        // console.log(global_comment_data_for_edit);
        // var html_string = $.parseHTML( global_comment_data_for_edit );
        var current_comment_text = comment_text_container_div.find('div.single_comment_text').html();

        var single_editform_html = '<form class="form-group with-icon-right is-empty group-post-comment_edit-form" action="'+default_delete_url+'" method="post">'+
                                    '<p class="limit-comment-text"><span class="current_length">0</span>/2000</p>'+
                                    '<input type="hidden" name="group_post_comment_id_for_edit" value="'+comment_id+'">'+
                                    '<textarea class="form-control form-control-2" style="background-image:none!important;" type="text" name="post_comment_edit"/></textarea>'+
                                    '<div class="add-group-post-photo"><a class="group-post-photo post-comment-edit-btn" data-loading-text="<i class=\'fa fa-spinner fa-spin\'></i>"><i class="zmdi zmdi-check"></i></a></div></form>'+
                                    '<a class="comment-edit-cancel-btn" data-comment_id="'+comment_id+'">cancel</a>';
        comment_text_container_div.addClass('edit-open');
        comment_text_container_div.html(single_editform_html);
        comment_text_container_div.find('textarea[name=post_comment_edit]').val(current_comment_text);
        comment_text_container_div.find('p.limit-comment-text>.current_length').html(current_comment_text.length);
        $('textarea').each(function(){
            autosize(this);
        });
    });

    $(document).on('click', '.post-comment-edit-btn', function() {
        var edit_input = $(this);
        var form = edit_input.parents('form.group-post-comment_edit-form')[0];
        if ($(form).find('textarea[name=post_comment_edit]').val().length <= 2000) {
            post_edit_comment(form);
        }
    })

    $(document).on('click', '.comment-edit-cancel-btn', function(e){
        e.preventDefault();
        var $this = $(this);
        var comment_id = $this.data('comment_id');
        var comment_div = $('#group_post_comment_'+comment_id);
        var comment_text_container_div = comment_div.find('div.posted-comment-text-container');
        comment_text_container_div.removeClass('edit-open');
        comment_text_container_div.html(global_comment_data_for_edit);
        global_comment_data_for_edit = "";
    })

    $(document).on('keypress', 'textarea[name=post_comment_edit]', function(e){
        var edit_input = $(this);
        var form = edit_input.parents('form.group-post-comment_edit-form')[0];
        if (isCharacterKeyPress(e)) {
            edit_input.css({'border': '1px solid #b9b8b8'});
        }
        if (e.which == 13) {
            if(!e.shiftKey){
                e.preventDefault();
                if (edit_input.val().length <= 2000) {
                    post_edit_comment(form);
                }
            }
        }
    });

    $(document).on('keyup keydown', 'textarea[name=post_comment_edit]', function(e){
        var edit_input = $(this);
        var form = edit_input.parents('form.group-post-comment_edit-form')[0];
        $(form).find('p.limit-comment-text>.current_length').html(edit_input.val().length);
        if (edit_input.val().length <= 2000) {
            $(form).find('p.limit-comment-text').css({'color': 'rgb(26, 26, 26)'});
            edit_input.css({'border': '1px solid #b9b8b8'});
        } else {
            $(form).find('p.limit-comment-text').css({'color': 'rgb(255, 76, 65)'});
            edit_input.css({'border': '1px solid #e83d3d'});
        }
    });

    $('#group_post_photo_upload_form').submit(function(e) {
        e.preventDefault();
        var form = $(this)[0];
        var is_selected_img = $(form).find('input.upload-img-slim').val();
        console.log(is_selected_img);
        if (is_selected_img != "") {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
                }
            });

            var url = $(form).attr('action');

            // console.log(url);

            var formData = new FormData(form);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    cropper_group_post.destroy();
                    photo_add_for_post(data.photo_id, data.photo_url, global_target_form);
                    $('#group_post_photo_upload').modal('hide');
                    cropper_group_post = new Slim(document.getElementById('group-slim-img'));
                },
                processData: false,
                contentType: false,
                error: function(data)
               {
                   console.log(data);
               }
            });
        }
    });

    $('#io-group-new-group-create-btn').on('click',function(e){
        e.preventDefault();
        var form = $(this).parents('#io-group-new-group-create');
        var ischeck_send_request = form.find('select.autocomplete-user').val();
        var ischeck_group_name = form.find('input[name=group_name]:text').val();
        if (ischeck_group_name != "") {
            if (ischeck_send_request == null) {
                swal("Warning!", "Please add at least one member to your group.", "warning");
            }else {
                form.submit();
            }
        }
        else {
            swal("Warning!", "Please write your group name.", "warning");
        }
    })
});

var closable = false;
var selected_container = null;
var default_giphy_url = null;
var clicked_element = null;
var clicked_element_width = null;

var default_loading_img = $('#loading-gif-img-for-all').val();
var pop_up_html = '<div class="post-giphy-container" id="add_post_giphy">'+
                '<i class="giphy-arrow"></i>'+
                '<div class="form-group is-empty" style="margin:0;width:100%;">'+
                '<input type="text" class="form-control typeahead" id="giphy_search_string_box" placeholder="Write here to search"/>'+
                '</div><div class="post-all-giphy-container" id="post-all-giphy-container">'+
                '<ul><li style="text-align:center">'+
                '<img src="'+default_loading_img+'" style="width: 100px;" alt="">'+
                '</li></ul></div></div>';

$('body').on('click', function(event){
    if($('#add_post_giphy').length && closable == true) {
        var e = event.toElement || event.relatedTarget;
        var current_parent = $(e).parents('#add_post_giphy');
        if (!current_parent.length) {
            $('#add_post_giphy').remove();
            closable = false;
        }
    }
});

$('#post-giphy-add').click(function(e){
    e.preventDefault();
    var current_parent = $(this).parent();
    if (current_parent.find('#add_post_giphy').length > 0) {
        $('#add_post_giphy').remove();
        closable = false;
    } else {
        $('#add_post_giphy').remove();
        current_parent.append(pop_up_html);
        $('#post-all-giphy-container')[0].onscroll = load_moredata;
        clicked_element = $(this);
        clicked_element_width = 40;
        set_giphy_pos();

        selected_container = $('#group-post-container-form .post_photo_container');
        default_giphy_url = $(this).attr('href');
        var final_giphy_url = default_giphy_url+"/null/0";
        $.ajax({
            url: final_giphy_url,
            type: 'get',
            success: function(result){
                console.log(result);
                var giphy_ing_container = $('#post-all-giphy-container ul');

                setTimeout(function(){
                    giphy_ing_container.html("");
                    result.data.forEach(function(image){
                        var preview_img_url = image.images.preview_gif.url;
                        var source_img_url = image.images.original.url;
                        var html = '<li><a href="'+source_img_url+'" class="add-this-giphy-to-container">'+
                                    '<img src="'+preview_img_url+'" alt="">'+
                                    '</a></li>';
                        giphy_ing_container.append(html);
                        closable = true;
                    });
                }, 1000);
            },
            error: function(error){
                console.log(error);
            }
        });
    }
});

$('#post-giphy-add-edit').click(function(e){
    e.preventDefault();
    var current_parent = $(this).parent();
    if (current_parent.find('#add_post_giphy').length > 0) {
        $('#add_post_giphy').remove();
        closable = false;
    } else {
        $('#add_post_giphy').remove();
        current_parent.append(pop_up_html);
        $('#post-all-giphy-container')[0].onscroll = load_moredata;
        clicked_element = $(this);
        clicked_element_width = 75;
        set_giphy_pos();

        selected_container = $('#edit-group-post-modal-form .post_photo_container');
        default_giphy_url = $(this).attr('href');
        var final_giphy_url = default_giphy_url+"/null/0";
        $.ajax({
            url: final_giphy_url,
            type: 'get',
            success: function(result){
                var giphy_ing_container = $('#post-all-giphy-container ul');

                setTimeout(function(){
                    giphy_ing_container.html("");
                    result.data.forEach(function(image){
                        var preview_img_url = image.images.preview_gif.url;
                        var source_img_url = image.images.original.url;
                        var html = '<li><a href="'+source_img_url+'" class="add-this-giphy-to-container">'+
                                    '<img src="'+preview_img_url+'" alt="">'+
                                    '</a></li>';
                        giphy_ing_container.append(html);
                        closable = true;
                    });
                }, 1000);
            },
            error: function(error){
                console.log(error);
            }
        });
    }
});

$('#post-youtube-add').click(function(e) {
    e.preventDefault();
    selected_container = $('#group-post-container-form .post_photo_container');
    $('#add_youtubeurl').modal('show');
});

$('#post-youtube-add-edit').click(function(e) {
    e.preventDefault();
    selected_container = $('#edit-group-post-modal-form .post_photo_container');
    $('#add_youtubeurl').modal('show');
});

$("#add-youtubeurl-button").click(function(){
    var youtube_url = $("#youtube_url").val();
    var get_btn = $(this);

    var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
    var match = youtube_url.match(regExp);
    if (match && match[2].length == 11) {
        var current_youtube_id = YouTubeGetID(youtube_url);
        get_btn.button('loading');
        $.getJSON("https://www.googleapis.com/youtube/v3/videos", {
			key: "AIzaSyDgj1UaGgYELaZSvMAu1MST5i7oAyFwndQ",
			part: "snippet,statistics",
			id: current_youtube_id
		}, function(data) {
			if (data.items.length === 0) {
				alert('something went wrong');
				return;
			}
            var media_container = selected_container;
            var string = data.items[0].snippet.description;
            var length = 100;
            var description = string.substring(0, length);
            var html = '<div class="youtube youtube-single">'+
                        '<input type="hidden" name="post_youtube[]" value="'+current_youtube_id+'">'+
                        '<a class="single-media-close-btn" href="javascript:void(0);"><i class="zmdi zmdi-close"></i></a>'+
                        '<a href="https://www.youtube.com/watch?v='+current_youtube_id+'" target="_blank">'+
                        '<div class="youtube img-container" style="background-image: url('+data.items[0].snippet.thumbnails.medium.url+');"></div>'+
                        '<div class="youtube title-description-container">'+
                        '<p class="title">'+data.items[0].snippet.title+'</p>'+
                        '<p class="description">'+description+' ...</p>'+
                        '<p class="youtube-com">youtube.com</p>'+
                        '</div></a></div>';
            media_container.html(html);
            get_btn.button('reset');
            $("#youtube_url").val("");
            $("#add_youtubeurl").modal('toggle');
		}).fail(function(jqXHR, textStatus, errorThrown) {
			alert('something went wrong');
		});
    }else {
        alert('please input correct youtube url');
    }
});

function set_giphy_pos() {
    var current_scroll_top = $(window).scrollTop();
    var pos_y = clicked_element.offset().top;
    var remained_height = pos_y - current_scroll_top;
    var current_window_height = $(window).height();
    var current_window_width = $(window).width();
    var default_left = clicked_element_width;

    if (current_window_width > 434) {
        $('#add_post_giphy').css({'left': default_left});
        $('#add_post_giphy>i.giphy-arrow').css({'left': "20px"});
    }else {
        if (default_left < -10) {
            $('#add_post_giphy>i.giphy-arrow').css({'left': "5px"});
        }else if (default_left > -10 && default_left < 23) {
            $('#add_post_giphy>i.giphy-arrow').css({'left': "45px"});
        } else if (default_left > 23 && default_left < 45) {
            $('#add_post_giphy>i.giphy-arrow').css({'left': "50px"});
        } else {
            $('#add_post_giphy>i.giphy-arrow').css({'left': "95px"});
        }
        $('#add_post_giphy').css({'left': "calc(50% - 150px)"});
    }

    if (calculate_height(remained_height)) {
        $('#add_post_giphy').css({'bottom': "-410px"});
        $('#add_post_giphy>i.giphy-arrow').css({
            'background-position': '0 -466px',
            'top': "-8px",
        });
    } else {
        $('#add_post_giphy').css({'bottom': "45px"});
        $('#add_post_giphy>i.giphy-arrow').css({
            'background-position': '0 -457px',
            'top': "410px"
        });
    }
}

function calculate_height(remain_height){
    if (remain_height > 477) {
        var remain_down_height = $(window).height() - remain_height;
        if (remain_down_height > 460) {
            return true;
        }else {
            return false;
        }
    }else {
        return true;
    }
}

var typingTimer;                //timer identifier
var doneTypingInterval = 500;  //time in ms, 5 second for example

$(document).on('keyup', '#giphy_search_string_box', function () {
  clearTimeout(typingTimer);
  typingTimer = setTimeout(doneTyping, doneTypingInterval);
});

//on keydown, clear the countdown
$(document).on('keydown', '#giphy_search_string_box', function () {
  clearTimeout(typingTimer);
});

//user is "finished typing," do something
function doneTyping () {
    var search_string = "null";
    var current_string = $('#giphy_search_string_box').val();
    if (current_string != "") {
        search_string = current_string;
    }

    if(default_giphy_url != null){
        var current_giphy_url = default_giphy_url+"/"+search_string+"/0";
        setTimeout(function(){
            $.ajax({
                url: current_giphy_url,
                type: 'get',
                success: function(result){
                    console.log(result);
                    var giphy_ing_container = $('#post-all-giphy-container ul');
                    giphy_ing_container.html("");
                    if (result.data.length > 0) {
                        result.data.forEach(function(image){
                            var preview_img_url = image.images.preview_gif.url;
                            var source_img_url = image.images.original.url;
                            var html = '<li><a href="'+source_img_url+'" class="add-this-giphy-to-container">'+
                                        '<img src="'+preview_img_url+'" alt="">'+
                                        '</a></li>';
                            giphy_ing_container.append(html);
                        });
                    }else {
                        var html = '<li>Result not found</li>';
                        giphy_ing_container.html(html);
                    }
                },
                error: function(error){
                    console.log(error);
                }
            });
        }, 500);
    }
}

var loadable = true;
var count = 10;

function load_moredata() {
    var $this = $('#post-all-giphy-container');
    var container = $this.find('ul');
    var load_more_giphy_height = container.height() - 430;
    if ($this.scrollTop() > load_more_giphy_height && loadable == true) {
        loadable = false;

        var search_string = "null";
        var current_string = $('#giphy_search_string_box').val();
        if (current_string != "") {
            search_string = current_string;
        }

        if(default_giphy_url != null){
            var current_giphy_url = default_giphy_url+"/"+search_string+"/"+count;
            $.ajax({
                url: current_giphy_url,
                type: 'get',
                success: function(result){
                    console.log("scroll "+result);
                    var giphy_ing_container = $('#post-all-giphy-container ul');
                    result.data.forEach(function(image){
                        var preview_img_url = image.images.preview_gif.url;
                        var source_img_url = image.images.original.url;
                        var html = '<li><a href="'+source_img_url+'" class="add-this-giphy-to-container">'+
                                    '<img src="'+preview_img_url+'" alt="">'+
                                    '</a></li>';
                        giphy_ing_container.append(html);
                    });

                    setTimeout(function(){
                        count += 10;
                        loadable = true;
                    }, 3000);
                },
                error: function(error){
                    console.log(error);
                }
            });
        }
    }
}

$(document).on('click', '.add-this-giphy-to-container', function(e){
    e.preventDefault();
    var selecte_giphy = $(this).attr('href');
    var d = new Date();
    var timestamp_now = d.getTime();
    var html = '<div class="post-img-div" id="'+timestamp_now+'">'+
                '<img src="'+default_loading_img+'" alt=""></div>';
    selected_container.find('.youtube-div').each(function(){
        $(this).remove();
    });
    selected_container.append(html);
    $('#add_post_giphy').remove();
    closable = false;

    var real_html = '<img src="'+selecte_giphy+'" alt="">'+
                    '<input type="hidden" name="post_giphy[]" value="'+selecte_giphy+'">'+
                    '<a class="selected-single-photo-delete-btn"><i class="zmdi zmdi-close"></i></a>';
    var real_html_container = $('#'+timestamp_now);
    setTimeout(function(){
        real_html_container.html(real_html);
    }, 500);

})

function isCharacterKeyPress(evt) {
    if (typeof evt.which == "undefined") {
        return true;
    } else if (typeof evt.which == "number" && evt.which > 0) {
        return !evt.ctrlKey && !evt.metaKey && !evt.altKey && evt.which != 8;
    }
    return false;
}

function photo_add_for_post(photoId, photoUrl, form) {
    //console.log(form);
    var img_html = '<div class="post-img-div" id="post-photo-number-'+photoId+'">'+
                    '<img src="'+photoUrl+'" alt="">'+
                    '<input type="hidden" name="gourp_post_photo_id[]" class="gourp_post_photo_id" value="'+photoId+'">'+
                    '<a class="selected-single-photo-delete-btn"><i class="zmdi zmdi-close"></i></a></div>';
    var media_container = form.find('.post-photo-container-div');
    media_container.find('.youtube.youtube-single').each(function(){
        $(this).remove();
    });
    media_container.append(img_html);
}

function post_new_comment(form) {
    var comment_text_form = $(form).find('textarea[name=post_comment]');

    if (comment_text_form.val() != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });
        var url = $(form).attr( 'action' );

        var formData = new FormData($(form)[0]);

        var post_id = $(form).find('input[name=group_post_id_for_comment]').val();

        var submit_btn = $(form).find('.post-comment-add-btn');
        submit_btn.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data != "fail") {
                    submit_btn.button('reset');
                    var comment_container = $('#group_post_number_'+post_id).find('.posted-comment-container');
                    comment_container.append(data.html);
                    comment_container.find('.posted-comment-separator').css({'display': 'block'});
                    form.reset();
                    $(form).find('p.limit-comment-text>.current_length').html(0);
                    $(form).find('textarea[name=post_comment]').css({'height': '32px'});
                }
            },
            processData: false,
            contentType: false,
            error: function(data)
           {
               console.log(data);
           }
        });
    }else {
        comment_text_form.css({'border': '1px solid #ec1313'});
    }
}

function post_edit_comment(form) {
    var comment_edit_text_form = $(form).find('textarea[name=post_comment_edit]');

    if (comment_edit_text_form.val() != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });
        var url = $(form).attr( 'action' );
        var comment_id = $(form).find('input[name=group_post_comment_id_for_edit]').val();

        var formData = new FormData($(form)[0]);
        var update_btn = $(form).find('.post-comment-edit-btn');
        update_btn.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data != "fail") {
                    update_btn.button('reset');
                    var current_comment_text_div = $('#group_post_comment_'+comment_id);
                    current_comment_text_div.html(data.html);
                }
            },
            processData: false,
            contentType: false,
            error: function(data)
           {
               console.log(data);
           }
        });
    }else {
        comment_edit_text_form.css({'border': '1px solid #ec1313'});
    }
}

function post_new_reply(form) {
    var reply_text_field = $(form).find('textarea[name=post_reply]');

    if (reply_text_field.val() != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });
        var url = $(form).attr( 'action' );

        var formData = new FormData($(form)[0]);

        var submit_btn = $(form).find('.reply-send-btn');
        submit_btn.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                submit_btn.button('reset');
                if (data != "fail") {
                    form.reset();
                    $(form).parents('.comment-and-reply-container').find('.posted-reply-container').append(data.html);
                    $(form).find('p.limit-comment-text>.current_length').html(0);
                    $(form).find('textarea[name=post_reply]').css({'height': '32px'});
                }
            },
            processData: false,
            contentType: false,
            error: function(data)
           {
               console.log(data);
           }
        });
    }else {
        reply_text_field.css({'border': '1px solid #ec1313'});
    }
}

function post_edit_reply(form) {
    var reply_text_field = $(form).find('textarea[name=post_reply_edit]');

    if (reply_text_field.val() != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });
        var url = '/group/reply/edit/';

        var formData = new FormData($(form)[0]);

        var reply_id = $(form).find('input[name=reply_id_for_edit]').val();

        var submit_btn = $(form).find('.reply-edit-send-btn');
        submit_btn.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data != "fail") {
                    submit_btn.button('reset');
                    var reply_text_container = $('#group-reply_'+reply_id);
                    reply_text_container.html(data.html);
                }
            },
            processData: false,
            contentType: false,
            error: function(data)
           {
               console.log(data);
           }
        });
    }else {
        reply_text_field.css({'border': '1px solid #ec1313'});
    }
}

function YouTubeGetID(url){
    var ID = '';
    url = url.replace(/(>|<)/gi,'').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
    if(url[2] !== undefined) {
        ID = url[2].split(/[^0-9a-z_\-]/i);
        ID = ID[0];
    }
    else {
        ID = url;
    }
    return ID;
}

$(document).on('click', '.group-reply-btn', function(e) {
    e.preventDefault();
    var commenId = $(this).data('comment_id');
    var form = $('#group_post_comment_'+commenId).find('.posted-comment-reply-form-container');
    form.toggleClass('open');
});

$(document).on('click', '.group-reply-edit-btn', function(e) {
    var reply_id = $(this).data('reply_id');
    var reply_div = $('#group-reply_'+reply_id);
    var reply_text_container_div = reply_div.find('div.posted-reply-text-container');
    global_comment_data_for_edit = "";
    var div = reply_text_container_div[0];
    var children = div.childNodes;
    var elements = [];
    for (var i=0; i<div.childNodes.length; i++) {
        var child = div.childNodes[i];
        if (child.nodeType == 1) {
            var str = $(child).prop('outerHTML')
            global_comment_data_for_edit = global_comment_data_for_edit.concat(str);
        }
    }

    var current_reply_text = reply_text_container_div.find('div.single_reply_text').html();

    var single_editform_html = '<form class="form-group is-empty comment-reply-form" action="" method="post">'+
                                '<p class="limit-comment-text"><span class="current_length">0</span>/2000</p>'+
                                '<input type="hidden" name="reply_id_for_edit" value="'+reply_id+'">'+
                                '<textarea class="form-control form-control-2 reply-edit-post-field" style="background-image:none!important;" type="text" name="post_reply_edit" placeholder="Write a reply ..." /></textarea>'+
                                '<a class="reply-edit-send-btn" data-loading-text="<i class=\'fa fa-spinner fa-spin \' style=\'color:#ff5e3a;\'></i>"><i class="zmdi zmdi-check"></i></a></form>'+
                                '<a class="reply-edit-cancel-btn" data-reply_id="'+reply_id+'">cancel</a>';
    reply_text_container_div.addClass('edit-open');
    reply_text_container_div.html(single_editform_html);
    reply_text_container_div.find('textarea[name=post_reply_edit]').val(current_reply_text);
    reply_text_container_div.find('p.limit-comment-text>.current_length').html(current_reply_text.length);
    $('textarea').each(function(){
        autosize(this);
    });
});

$(document).on('click', '.reply-edit-cancel-btn', function(e) {
    var reply_text_container_div = $(this).parent();
    reply_text_container_div.html(global_comment_data_for_edit);
    reply_text_container_div.removeClass('edit-open');
    global_comment_data_for_edit = "";
});

$(document).on('click', 'a.reply-edit-send-btn', function(e) {
    e.preventDefault();
    var form = $(this).parents('form.comment-reply-form')[0];
    global_comment_data_for_edit = "";
    if ($(form).find('textarea[name=post_reply_edit]').val().length <= 2000) {
        post_edit_reply(form);
    }
});

$(document).on('click', '.group-reply-delete-btn', function(e) {
    e.preventDefault();
    var $this = $(this);
    var reply_id = $this.data('reply_id');
    var reply_div = $('#group-reply_'+reply_id);
    var reply_delete_url = '/group/reply/delete/'+reply_id;

    swal({
      title: "Are you sure?",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#0ed2d0",
      confirmButtonText: "Yes, Delete!",
      cancelButtonText: "No, cancel!",
      showLoaderOnConfirm: true,
      closeOnConfirm: false,
      closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: reply_delete_url,
                type: 'get',
                success: function(data){
                    if (data == "success") {
                        console.log(data);
                        reply_div.remove();
                        swal("Deleted!", "Reply Deleted.", "success");
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        } else {

        }
    });
});

$(document).on('keyup', '.poll-answer-field', function(e) {
    var answer_field = $(this);
    var poll_form = answer_field.parents('form#group-poll-create-form');
    var now_number = poll_form.find('.poll-answer-field').length + 1;
    if (answer_field.val() != "") {
        answer_field.attr('name', 'answers[]');
    }else {
        answer_field.attr('name', '');
    }
    if (now_number <= 5) {
        if (answer_field.hasClass("last")) {
            answer_field.removeClass("last");
            var html = '<div class="form-group is-empty additional">'+
                    '<a href="javascript:;" class="field-delete-btn"><i class="zmdi zmdi-close"></i></a>'+
                    '<label for="" class="control-label answer-number">Option/Answer #<span>'+now_number+'</span></label>'+
                    '<input type="text" class="form-control poll-answer-field last" autocomplete="off" placeholder="Answer" value="" name=""/></div>';
            $(html).insertAfter(answer_field.parent());
        }
    }
});
$(document).on('click', '.field-delete-btn', function(e) {
    e.preventDefault();
    var current_form = $(this).parent();
    if (current_form.find('.poll-answer-field').hasClass('last')) {
        var preivew_form = current_form.prev();
        preivew_form.find('input.poll-answer-field').addClass('last');
    }
    current_form.remove();
    var count = 1;
    $('#group-poll-create-form .answer-number>span').each(function(e) {
        $(this).html(count);
        count ++;
    });
});
$(document).on('submit', '#group-poll-create-form', function(e) {
    e.preventDefault();
    console.log("submit");
    var form = $(this)[0];

    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
        }
    });

    var url = $(form).attr('action');
    var formData = new FormData(form);
    var poll_post_button = $(form).find('.group-poll-create-submit-btn');
    poll_post_button.button('loading');

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function (data) {
            console.log(data);
            poll_post_button.button('reset');
            $('#group_poll_modal').modal('hide');
            $(form).find('.additional').each(function(){
                $(this).remove();
            });
            $(form).find('.second-field').addClass('last');
            form.reset();
        },
        processData: false,
        contentType: false,
        error: function(data)
       {
           console.log(data);
       }
    });
});
$(document).on('click', '.vote', function(e) {
    var answerId = $(this).data('answer_id');

    var article_container = $(this).parents('article.group-post-single-container');

    article_container.addClass('voting');
    $.ajax({
        url: '/group/poll/vote/'+answerId,
        type: 'get',
        success: function(data){
            console.log(data);
            // areticle_container.removeClass('voting');
        }
    });
});

function set_imgcontainer_size () {
    $('.js-zoom-gallery').each(function(e) {
        var img_container = $(this);
        var img_total_count = img_container.find('a').length;
        if (img_total_count >= 3 && img_total_count < 5) {
            var img_count = 0;
            img_container.find('a').each(function() {
                img_count ++;
                var img_a = $(this);
                var img_size = getImgSize(img_a.attr('href'));
                var img_height = img_size[0];
                var img_width = img_size[1];
                var scaled_height = img_a.height();
                var scaled_width = img_a.width();
                var calc_real = img_height/img_width;
                var calc_vertu = scaled_height/scaled_width;
                if (img_count > 1) {
                    img_a.find('img').css({'position': 'absolute', 'max-width': 'unset', 'max-height':'unset'});
                    if (calc_real >= calc_vertu) {
                        img_a.find('img').css({'width': '100%', 'height':'auto', 'left': '0'});
                        var current_height = img_a.find('img').height();
                        var margin_top = 'calc(50% - '+current_height/2+'px)';
                        img_a.find('img').css({'top':margin_top});

                    } else if (calc_real < calc_vertu) {
                        img_a.find('img').css({'width': 'auto', 'height':'100%', 'top': '0'});
                        var current_width = img_a.find('img').width();
                        var margin_left = 'calc(50% - '+current_width/2+'px)';
                        img_a.find('img').css({'left':margin_left});
                    }
                }
            });
        } else if (img_total_count >= 5) {
            var img_count = 0;
            img_container.find('a').each(function() {
                img_count ++;
                var img_a = $(this);
                var img_size = getImgSize(img_a.attr('href'));
                var img_height = img_size[0];
                var img_width = img_size[1];
                var scaled_height = img_a.height();
                var scaled_width = img_a.width();
                var calc_real = img_height/img_width;
                var calc_vertu = scaled_height/scaled_width;
                if (img_count < 6) {
                    img_a.find('img').css({'position': 'absolute', 'max-width': 'unset', 'max-height':'unset'});
                    if (calc_real >= calc_vertu) {
                        img_a.find('img').css({'width': '100%', 'height':'auto', 'left': '0'});
                        var current_height = img_a.find('img').height();
                        var margin_top = 'calc(50% - '+current_height/2+'px)';
                        img_a.find('img').css({'top':margin_top});

                    } else if (calc_real < calc_vertu) {
                        img_a.find('img').css({'width': 'auto', 'height':'100%', 'top': '0'});
                        var current_width = img_a.find('img').width();
                        var margin_left = 'calc(50% - '+current_width/2+'px)';
                        img_a.find('img').css({'left':margin_left});
                    }
                }
            });
        }
    });
}

function getImgSize(imgSrc) {
    var newImg = new Image();
    newImg.src = imgSrc;
    var height = newImg.height;
    var width = newImg.width;
    var final_size = [height, width];
    return final_size;
}

$('#group-invite-link-copy-btn').on('click', function(e) {
    e.preventDefault();
    var copy_btn = $(this);
    var invite_link = copy_btn.parent().find('#group-invite-link-copy-input');
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(invite_link.val()).select();
    document.execCommand("copy");
    $temp.remove();

    copy_btn.text('copied');
    copy_btn.css({'background-color': '#13cc77'});
    setTimeout(function() {
        copy_btn.text('copy');
        copy_btn.css({'background-color': '#5767c3'});
    }, 1000);
});

$('.get-group-invite-key-btn').on('click', function(e) {
    e.preventDefault();
    var get_url = $(this).attr('href');
    $.ajax({
        url: get_url,
        type: 'get',
        success: function(data){
            // console.log(data);
            $('#invite-link-modal #group-invite-link-copy-input').val(data);
            $('#invite-link-modal').modal('show');
        }
    });
});

$('#set_never_expire_invite_key').on('change', function(e) {
    // console.log("asdfasdf");
    var current_checkbox = $(this);
    var current_status = null;
    if (current_checkbox.prop('checked') == true) {
        current_status = 1;
    } else {
        current_status = 0;
    }
    var set_default_url = current_checkbox.data('set_url');
    var set_url = set_default_url+'/'+current_status;
    $.ajax({
        url: set_url,
        type: 'get',
        success: function(data){
            console.log(data);
        }
    });
});

$('body').on("click", ".like-comment", function(){
    status = $(this).attr('status');

    if($(this).hasClass("liked")){
        $(this).children("span").text(parseInt($(this).children().text())-1);
        $(this).removeClass("liked");
    } else {
        $(this).children("span").text(parseInt($(this).children().text())+1);
        $(this).addClass("liked");
    }

    $.ajax({
        url: "/group/comment/"+status+"/like",
    }).done(function() {
    });
});

$('body').on("click", ".like-reply", function(){
    status = $(this).attr('status');

    if($(this).hasClass("liked")){
        $(this).children("span").text(parseInt($(this).children().text())-1);
        $(this).removeClass("liked");
    } else {
        $(this).children("span").text(parseInt($(this).children().text())+1);
        $(this).addClass("liked");
    }

    $.ajax({
        url: "/group/comment-reply/"+status+"/like",
    }).done(function() {
    });
});

$(window).scroll( function() {
    if($('#add_post_giphy').length) {
        set_giphy_pos();
    }
    set_imgcontainer_size();
});

$( window ).resize(function() {
    if($('#add_post_giphy').length) {
        set_giphy_pos();
    }
    set_imgcontainer_size();
});
