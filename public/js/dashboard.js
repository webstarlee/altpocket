var closable = false;
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

$("textarea").atwho({
    at: "@", limit: 5,
    callbacks: {
        remoteFilter: function (t, e) {
            t.length <= 2 || $.getJSON("/api/users", {q: t}, function (t) {e(t)})
        }
    }
});

$("textarea").atwho({
    at: "::", limit: 5, displayTpl: "<li>${name} <img src='/icons/32x32/${symbol}.png' style='width:20px;'/>'", insertTpl: '::${symbol}::',
    callbacks: {
        remoteFilter: function (t, e) {
            t.length <= 2 || $.getJSON("/api/coins", {q: t}, function (t) {e(t)})
        }
    }
});

$('body').on("click", ".edit-post", function(){
    $.ajax({
        dataType: "json",
        url: '/status/get/'+$(this).attr('id'),
        success: function(data){
            if (data != "fail") {
                $('#edit-status-form #status_id_for_edit').val(data.status_id);
                $('#edit-status-form #edit-status-field').val(data.status_text);
                var edit_media_container = $('#edit-status-form #status-edit-media-container');
                edit_media_container.html("");
                if (data.status_imgs.length > 0) {
                    data.status_imgs.forEach(function(photo){
                        var html_img = '<div class="media-single">'+
                                    '<input type="hidden" name="status_image[]" value="'+photo.img_name+'">'+
                                    '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                    '<a class="image-popup-link" href="'+photo.img_url+'">'+
                                    '<img src="'+photo.img_url+'" alt=""></a></div>';
                        edit_media_container.append(html_img);
                    });
                }

                if (data.status_giphys.length > 0) {
                    data.status_giphys.forEach(function(giphy){
                        var html_img = '<div class="media-single">'+
                                    '<input type="hidden" name="status_giphy[]" value="'+giphy+'">'+
                                    '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                    '<a class="image-popup-link" href="'+giphy+'">'+
                                    '<img src="'+giphy+'" alt=""></a></div>';
                        edit_media_container.append(html_img);
                    });
                }

                if (data.status_urls.length > 0) {
                    data.status_urls.forEach(function(video){
                        $.getJSON("https://www.googleapis.com/youtube/v3/videos", {
                			key: "AIzaSyDgj1UaGgYELaZSvMAu1MST5i7oAyFwndQ",
                			part: "snippet,statistics",
                			id: video
                		}, function(data) {
                			if (data.items.length === 0) {
                				alert('something went wrong');
                				return;
                			}
                            var string = data.items[0].snippet.description;
                            var length = 100;
                            var description = string.substring(0, length);
                            var html = '<div class="youtube youtube-single">'+
                                        '<input type="hidden" name="status_youtubeurl[]" value="'+video+'">'+
                                        '<a class="single-media-close-btn" href="javascript:void(0);"><i class="zmdi zmdi-close"></i></a>'+
                                        '<a href="https://www.youtube.com/watch?v='+video+'" target="_blank">'+
                                        '<div class="youtube img-container" style="background-image: url('+data.items[0].snippet.thumbnails.medium.url+');"></div>'+
                                        '<div class="youtube title-description-container">'+
                                        '<p class="title">'+data.items[0].snippet.title+'</p>'+
                                        '<p class="description">'+description+' ...</p>'+
                                        '<p class="youtube-com">youtube.com</p>'+
                                        '</div></a></div>';
                            edit_media_container.html(html);
                		}).fail(function(jqXHR, textStatus, errorThrown) {
                			alert('something went wrong');
                		});
                    });
                }
                $('.image-popup-link').magnificPopup({
                    type: 'image'
                });
                $('.youtube-popup-link').magnificPopup({
            		type: 'iframe'
            	});

                $('#edit_post').modal('show');
            }
        }
    });
});

$('body').on("click", ".edit-comment", function(){
    $.ajax({
        dataType: "json",
        url: '/statuscomment/get/'+$(this).attr('id'),
        success: function(data){
            $('#edit-comment-form #comment_id_for_edit').val(data.comment_id);
            $('#edit-comment-form #edit-comment-field').val(data.comment_text);
            var edit_media_container = $('#edit-comment-form #comment-edit-media-container');
            edit_media_container.html("");

            if (data.comment_imgs.length > 0) {
                data.comment_imgs.forEach(function(photo){
                    var html_img = '<div class="media-single">'+
                                '<input type="hidden" name="comment_image[]" value="'+photo.img_name+'">'+
                                '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                '<a class="image-popup-link" href="'+photo.img_url+'">'+
                                '<img src="'+photo.img_url+'" alt=""></a></div>';
                    edit_media_container.append(html_img);
                });
            }

            if (data.comment_giphys.length > 0) {
                data.comment_giphys.forEach(function(giphy){
                    var html_img = '<div class="media-single">'+
                                '<input type="hidden" name="status_giphy[]" value="'+giphy+'">'+
                                '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                '<a class="image-popup-link" href="'+giphy+'">'+
                                '<img src="'+giphy+'" alt=""></a></div>';
                    edit_media_container.append(html_img);
                });
            }
            $('.image-popup-link').magnificPopup({
                type: 'image'
            });
            $('.youtube-popup-link').magnificPopup({
                type: 'iframe'
            });

            $('#edit_comment').modal('show');
        }
    });
});

$('body').on("click", ".edit-comment-reply", function(){
    $.ajax({
        dataType: "json",
        url: '/statuscommentreply/get/'+$(this).attr('id'),
        success: function(data){
            console.log(data);
            $('#edit-comment-reply-form #reply_id_for_edit').val(data.reply_id);
            $('#edit-comment-reply-form #edit-comment-reply-field').val(data.reply_text);
            var edit_media_container = $('#edit-comment-reply-form #reply-edit-media-container');
            edit_media_container.html("");

            if (data.reply_giphys.length > 0) {
                data.reply_giphys.forEach(function(giphy){
                    var html_img = '<div class="media-single">'+
                                '<input type="hidden" name="status_giphy[]" value="'+giphy+'">'+
                                '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                '<a class="image-popup-link" href="'+giphy+'">'+
                                '<img src="'+giphy+'" alt=""></a></div>';
                    edit_media_container.append(html_img);
                });
            }
            $('.image-popup-link').magnificPopup({
                type: 'image'
            });
            $('.youtube-popup-link').magnificPopup({
                type: 'iframe'
            });

            $('#edit_comment_reply').modal('show');
        }
    });
});

$('body').on("click", ".more-comments", function(){
    id = $(this).attr('id');
    $("."+id).css('display',' block');
    $(this).hide();
});

$('body').on("click", ".like-status", function(){
    status = $(this).attr('status');

    if($(this).hasClass("liked")){
        $(this).children("span").text(parseInt($(this).children().text())-1);
        $(this).removeClass("liked");
    } else {
        $(this).children("span").text(parseInt($(this).children().text())+1);
        $(this).addClass("liked");
    }

    $.ajax({
        url: "/status/"+status+"/like",
    }).done(function() {
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
        url: "/comment/"+status+"/like",
    }).done(function() {
    });
});

$('body').on("click", ".like-comment-reply", function(){
    status = $(this).attr('status');

    if($(this).hasClass("liked")){
        $(this).children("span").text(parseInt($(this).children().text())-1);
        $(this).removeClass("liked");
    } else {
        $(this).children("span").text(parseInt($(this).children().text())+1);
        $(this).addClass("liked");
    }

    $.ajax({
        url: "/commentreply/"+status+"/like",
    }).done(function() {
    });
});

$('body').on('click', '.delete-comment', function(e){
    e.preventDefault();
    var delete_url = $(this).attr('href');
    var comment_delete_button = $(this);
    comment_delete_button.button('loading');
    $.ajax({
        url: delete_url,
        type: 'get',
        success: function(result){
            console.log(result);
            if (result != "fail") {
                comment_delete_button.button('reset');
            }
        },
        error: function(error){
            console.log(error);
        }
    });
})

$(".more-referral").click(function(){
    if($(".referral-tab").hasClass( "hide" )){
        $(".referral-tab").removeClass('hide');
        $(".referral-tab").css('display', 'block');
        $(".referral-option").removeClass('fa-plus');
        $(".referral-option").addClass('fa-minus');
    } else {
        $(".referral-tab").css('display', 'none');
        $(".referral-tab").addClass('hide');
        $(".referral-option").removeClass('fa-minus');
        $(".referral-option").addClass('fa-plus');
    }
});

$(".change-tracking").click(function(){
    $("#edit_tracking").modal('toggle');
    $(".tracking-form").attr('action', '/track/coin/'+$(this).attr('id'));
});

var comment_img_form = null;

$("#add-youtubeurl").click(function(){
    $("#add_youtubeurl").modal('toggle');
});

$("#add-youtubeurl-edit").click(function(){
    $("#add_youtubeurl-edit").modal('toggle');
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
            var media_container = $('#status-post-media-container');
            var string = data.items[0].snippet.description;
            var length = 100;
            var description = string.substring(0, length);
            var html = '<div class="youtube youtube-single">'+
                        '<input type="hidden" name="status_youtubeurl[]" value="'+current_youtube_id+'">'+
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

$("#add-youtubeurl-button-edit").click(function(){
    var youtube_url = $("#youtube_url-edit").val();
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
            var media_container = $('#status-edit-media-container');
            var string = data.items[0].snippet.description;
            var length = 100;
            var description = string.substring(0, length);
            var html = '<div class="youtube youtube-single">'+
                        '<input type="hidden" name="status_youtubeurl[]" value="'+current_youtube_id+'">'+
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
            $("#youtube_url-edit").val("");
            $("#add_youtubeurl-edit").modal('toggle');
		}).fail(function(jqXHR, textStatus, errorThrown) {
			alert('something went wrong');
		});
    }else {
        alert('please input correct youtube url');
    }
});

var selected_container = null;
var default_giphy_url = null;
var clicked_element = null;
var clicked_element_width = null;

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

$('#add-image-gif').click(function(e){
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

        selected_container = $('#status-post-media-container');
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
    var load_img_url = $('#loading-gif-img-for-all').val();
    var html = '<div class="media-single" id="'+timestamp_now+'">'+
                '<img src="'+load_img_url+'" class="loading-img" alt=""></div>';
    selected_container.find('.youtube.youtube-single').remove();
    selected_container.append(html);
    $('#add_post_giphy').remove();
    closable = false;

    var real_html = '<input type="hidden" name="status_giphy[]" value="'+selecte_giphy+'">'+
                    '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                    '<a class="image-popup-link" href="'+selecte_giphy+'">'+
                    '<img src="'+selecte_giphy+'" alt=""></a>';
    var real_html_container = $('#'+timestamp_now);
    setTimeout(function(){
        real_html_container.html(real_html);
        $('.image-popup-link').magnificPopup({
            type: 'image'
        });
        $('#giphy_search_string_box').val("");
    }, 500);

})

$('#add-image-gif-edit').click(function(e){
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

        selected_container = $('#status-edit-media-container');
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

$('#post_status_gif').on('change', function(e){
    e.preventDefault();
    var $this = $(this);
    var file = this.files[0];
    if (file) {
        var fileType = file["type"];
        var ValidImageTypes = ["image/gif"];
        if ($.inArray(fileType, ValidImageTypes) < 0) {
             swal("Error!", "Please select gif image.", "error");
        }else {
            var form = $(this).parent()[0];
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
                }
            });

            var url = $(form).attr('action');
            var formData = new FormData(form);

            var d = new Date();
            var timestamp_now = d.getTime();
            var load_img_url = $(form).find('input[name=loading_img]').val();
            var media_container = $('#status-post-media-container');
            var html = '<div class="media-single" id="'+timestamp_now+'">'+
                        '<img src="'+load_img_url+'" class="loading-img" alt=""></div>';
            media_container.append(html);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data == "fail" || data == "validate") {
                        alert('image not correct');
                    }else {
                        var real_html = '<input type="hidden" name="status_image[]" value="'+data.img_name+'">'+
                                        '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                        '<a class="image-popup-link" href="'+data.img_url+'">'+
                                        '<img src="'+data.img_url+'" alt=""></a>';
                        var real_html_container = $('#'+timestamp_now);
                        setTimeout(function(){
                            real_html_container.html(real_html);
                            $('.image-popup-link').magnificPopup({
                                type: 'image'
                            });
                            $this.val("");
                        }, 500);
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
    }
});

$('#post_status_gif-edit').on('change', function(e){
    e.preventDefault();
    var $this = $(this);
    var file = this.files[0];
    if (file) {
        var fileType = file["type"];
        var ValidImageTypes = ["image/gif"];
        if ($.inArray(fileType, ValidImageTypes) < 0) {
             swal("Error!", "Please select gif image.", "error");
        }else {
            var form = $(this).parent()[0];
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
                }
            });

            var url = $(form).attr('action');
            var formData = new FormData(form);

            var d = new Date();
            var timestamp_now = d.getTime();
            var load_img_url = $(form).find('input[name=loading_img]').val();
            var media_container = $('#status-edit-media-container');
            var html = '<div class="media-single" id="'+timestamp_now+'">'+
                        '<img src="'+load_img_url+'" class="loading-img" alt=""></div>';
            media_container.append(html);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data == "fail" || data == "validate") {
                        alert('image not correct');
                    }else {
                        var real_html = '<input type="hidden" name="status_image[]" value="'+data.img_name+'">'+
                                        '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                        '<a class="image-popup-link" href="'+data.img_url+'">'+
                                        '<img src="'+data.img_url+'" alt=""></a>';
                        var real_html_container = $('#'+timestamp_now);
                        setTimeout(function(){
                            real_html_container.html(real_html);
                            $('.image-popup-link').magnificPopup({
                                type: 'image'
                            });
                            $this.val("");
                        }, 500);
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
    }
})

$('#add-image').click(function(){
    $("#status_post_photo_upload").modal('toggle');
});

$('#add-image-edit').click(function(){
    $("#status_edit_photo_upload").modal('toggle');
});

$('#status_post_photo_upload_form').submit(function(e) {
    e.preventDefault();
    var form = $(this)[0];
    var is_selected_img = $(form).find('input.upload-img-slim').val();

    if (is_selected_img != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });
        var url = $(form).attr('action');
        var formData = new FormData(form);

        var d = new Date();
        var timestamp_now = d.getTime();
        var load_img_url = $(form).find('input[name=loading_img]').val();
        var media_container = $('#status-post-media-container');
        media_container.find('.youtube.youtube-single').remove();
        var html = '<div class="media-single" id="'+timestamp_now+'">'+
                    '<img src="'+load_img_url+'" class="loading-img" alt=""></div>';
        media_container.append(html);
        $("#status_post_photo_upload").modal('toggle');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data != "image_fail") {
                    cropper_status.destroy();
                    var real_html_container = $('#'+timestamp_now);
                    var real_html = '<input type="hidden" name="status_image[]" value="'+data.img_name+'">'+
                                    '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                    '<a class="image-popup-link" href="'+data.img_url+'">'+
                                    '<img src="'+data.img_url+'" alt=""></a>';
                    setTimeout(function(){
                        real_html_container.html(real_html);
                        $('.image-popup-link').magnificPopup({
                    		type: 'image'
                    	});
                        cropper_status = new Slim(document.getElementById('status-post-slim'));
                    }, 500);
                }else {
                    alert('image not correct');
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
});

$('#status_edit_photo_upload_form').submit(function(e) {
    e.preventDefault();
    var form = $(this)[0];
    var is_selected_img = $(form).find('input.upload-img-slim').val();

    if (is_selected_img != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });
        var url = $(form).attr('action');
        var formData = new FormData(form);

        var d = new Date();
        var timestamp_now = d.getTime();
        var load_img_url = $(form).find('input[name=loading_img]').val();
        var media_container = $('#status-edit-media-container');
        media_container.find('.youtube.youtube-single').remove();
        var html = '<div class="media-single" id="'+timestamp_now+'">'+
                    '<img src="'+load_img_url+'" class="loading-img" alt=""></div>';
        media_container.append(html);
        $("#status_edit_photo_upload").modal('toggle');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data != "image_fail") {
                    cropper_status_edit.destroy();
                    var real_html_container = $('#'+timestamp_now);
                    var real_html = '<input type="hidden" name="status_image[]" value="'+data.img_name+'">'+
                                    '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                    '<a class="image-popup-link" href="'+data.img_url+'">'+
                                    '<img src="'+data.img_url+'" alt=""></a>';
                    setTimeout(function(){
                        real_html_container.html(real_html);
                        $('.image-popup-link').magnificPopup({
                    		type: 'image'
                    	});
                        cropper_status_edit = new Slim(document.getElementById('status-edit-slim'));
                    }, 500);
                }else {
                    alert('image not correct');
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
});

$(document).on('click', '.comment-add-img-btn', function(){
    var current_status = $(this).data('status_id');
    $('#comment_post_photo_upload #comment_status_id_img').val(current_status);
    $('#comment_post_photo_upload').modal('toggle');
    comment_img_form = $(this).parents('form.comment-post-form');
});

$(document).on('click', '#comment-add-img-btn-edit', function(){
    $('#comment_edit_photo_upload').modal('toggle');
});

$(document).on('click', '.comment-add-gif-btn', function(e){
    e.preventDefault();
    var current_status = $(this).data('status_id');
    comment_img_form = $(this).parent();
    if (comment_img_form.find('#add_post_giphy').length > 0) {
        $('#add_post_giphy').remove();
        closable = false;
    } else {
        $('#add_post_giphy').remove();
        comment_img_form.append(pop_up_html);
        $('#post-all-giphy-container')[0].onscroll = load_moredata;
        clicked_element = $(this);
        clicked_element_width = 25;
        set_giphy_pos();
        selected_container = comment_img_form.parent().find('.comment-post-media_'+current_status);
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

$(document).on('click', '.commentreply-add-gif-btn', function(e){
    e.preventDefault();
    var current_comment = $(this).data('comment_id');
    comment_img_form = $(this).parent();
    if (comment_img_form.find('#add_post_giphy').length > 0) {
        $('#add_post_giphy').remove();
        closable = false;
    } else {
        $('#add_post_giphy').remove();
        comment_img_form.append(pop_up_html);
        $('#post-all-giphy-container')[0].onscroll = load_moredata;
        clicked_element = $(this);
        clicked_element_width = -20;
        set_giphy_pos();
        selected_container = comment_img_form.parent().find('.comment-post-media_'+current_comment);
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
})

$(document).on('submit', '.comment-reply-form', function(e){
    e.preventDefault();
    var form = $(this)[0];

    var comment_reply_string = $(form).find('.comment-reply-text').val();
    var element = $(form).find('.post-media-container');
    if (element.html().length > 0 || comment_reply_string != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });

        var url = $(form).attr('action');
        var formData = new FormData(form);
        var comment_reply_post_button = $(form).find('.commentreply-post-btn');
        comment_reply_post_button.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                console.log(data);
                comment_reply_post_button.button('reset');
                // swal("Complete!", "Comment reply Posted successfully.", "success");
                $(form).find('.post-media-container').html("");
                form.reset();
                $(form).toggleClass('open');
            },
            processData: false,
            contentType: false,
            error: function(data)
           {
               console.log(data);
           }
        });
    }else {
        swal("Alert!", "You have to select one image, one giphy or write some text at least.", "warning");
    }
})

$('#post_comment_gif').on('change', function(e){
    e.preventDefault();
    var $this = $(this);
    var file = this.files[0];
    if (file) {
        var fileType = file["type"];
        var ValidImageTypes = ["image/gif"];
        if ($.inArray(fileType, ValidImageTypes) < 0) {
             swal("Error!", "Please select gif image.", "error");
        }else {
            var form = $(this).parent()[0];
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
                }
            });

            var url = $(form).attr('action');
            var formData = new FormData(form);

            var d = new Date();
            var status_id = $(form).find('#comment_status_id_gif').val();
            var timestamp_now = d.getTime();
            var load_img_url = $(form).find('input[name=loading_img]').val();
            var media_container = comment_img_form.find('.comment-post-media_'+status_id);
            console.log(status_id);
            var html = '<div class="media-single" id="'+timestamp_now+'">'+
                        '<img src="'+load_img_url+'" class="loading-img" alt=""></div>';
            media_container.append(html);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data == "fail" || data == "validate") {
                        alert('image not correct');
                    }else {
                        var real_html = '<input type="hidden" name="comment_image[]" value="'+data.img_name+'">'+
                                        '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                        '<a class="image-popup-link" href="'+data.img_url+'">'+
                                        '<img src="'+data.img_url+'" alt=""></a>';
                        var real_html_container = $('#'+timestamp_now);
                        setTimeout(function(){
                            real_html_container.html(real_html);
                            $('.image-popup-link').magnificPopup({
                                type: 'image'
                            });
                            $this.val("");
                        }, 500);
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
    }
});

$('#comment_post_photo_upload_form').submit(function(e) {
    e.preventDefault();
    var form = $(this)[0];
    var is_selected_img = $(form).find('input.upload-img-slim').val();
    if (is_selected_img != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });

        var url = $(form).attr('action');

        var status_id = $(this).find('#comment_status_id_img').val();

        var formData = new FormData(form);

        var d = new Date();
        var timestamp_now = d.getTime();
        var load_img_url = $(form).find('input[name=loading_img]').val();
        var media_container = comment_img_form.find('.comment-post-media_'+status_id);
        var html = '<div class="media-single" id="'+timestamp_now+'">'+
                    '<img src="'+load_img_url+'" class="loading-img" alt=""></div>';
        media_container.append(html);

        $("#comment_post_photo_upload").modal('toggle');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data != "image_fail") {
                    cropper_comment.destroy();
                    if (comment_img_form != null) {
                        var real_html_container = $('#'+timestamp_now);
                        var real_html = '<input type="hidden" name="comment_image[]" value="'+data.img_name+'">'+
                                        '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                        '<a class="image-popup-link" href="'+data.img_url+'">'+
                                        '<img src="'+data.img_url+'" alt=""></a>';
                        setTimeout(function(){
                            real_html_container.html(real_html);
                            $('.image-popup-link').magnificPopup({
                        		type: 'image'
                        	});
                            cropper_comment = new Slim(document.getElementById('comment-post-slim'));
                        }, 500);
                        $('.image-popup-link').magnificPopup({
                    		type: 'image'
                    	});
                    }
                }else {
                    alert('image not correct');
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
});

$('#comment_edit_photo_upload_form').submit(function(e) {
    e.preventDefault();
    var form = $(this)[0];
    var is_selected_img = $(form).find('input.upload-img-slim').val();
    if (is_selected_img != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });

        var url = $(form).attr('action');

        var status_id = $(this).find('#comment_status_id_img').val();

        var formData = new FormData(form);

        var d = new Date();
        var timestamp_now = d.getTime();
        var load_img_url = $('#loading-gif-img-for-all').val();
        var media_container = $('#comment-edit-media-container');
        var html = '<div class="media-single" id="'+timestamp_now+'">'+
                    '<img src="'+load_img_url+'" class="loading-img" alt=""></div>';
        media_container.append(html);

        $("#comment_edit_photo_upload").modal('toggle');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                console.log(data.img_name);
                if (data != "image_fail") {
                    cropper_comment_edit.destroy();
                    var real_html_container = $('#'+timestamp_now);
                    var real_html = '<input type="hidden" name="comment_image[]" value="'+data.img_name+'">'+
                                    '<a class="single-media-close-btn" href="javascript:;"><i class="zmdi zmdi-close"></i></a>'+
                                    '<a class="image-popup-link" href="'+data.img_url+'">'+
                                    '<img src="'+data.img_url+'" alt=""></a>';
                    setTimeout(function(){
                        real_html_container.html(real_html);
                        $('.image-popup-link').magnificPopup({
                    		type: 'image'
                    	});
                        cropper_comment_edit = new Slim(document.getElementById('comment-edit-slim'));
                    }, 500);

                    $('.image-popup-link').magnificPopup({
                		type: 'image'
                	});
                }else {
                    alert('image not correct');
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
});

$('#user-status-post-form').submit(function(e){
    e.preventDefault();
    var form = $(this)[0];
    var status_text = $(form).find('#new-post').val();
    var element = $(form).find('#status-post-media-container');
    if (element.html().length > 0 || status_text != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });

        var url = $(form).attr('action');
        var formData = new FormData(form);
        var post_button = $(form).find('#post-status-submit-btn');
        post_button.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                post_button.button('reset');
                // swal("Complete!", "Status posted successfully.", "success");
                $('#status-post-media-container').html("");
                form.reset();
            },
            processData: false,
            contentType: false,
            error: function(data)
           {
               console.log(data);
           }
        });
    }else {
        swal("Alert!", "You have to select one image, one youtube url or write some text at least.", "warning");
    }
})

$('#edit-status-form').submit(function(e){
    e.preventDefault();
    var form = $(this)[0];
    var status_text = $(form).find('#edit-status-field').val();
    var element = $(form).find('#status-edit-media-container');
    if (element.html().length > 0 || status_text != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });

        var url = $(form).attr('action');
        var formData = new FormData(form);
        var edit_button = $(form).find('#edit-status-submit-btn');
        edit_button.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                console.log(data);
                edit_button.button('reset');
                // swal("Complete!", "Status updated successfully.", "success");
                $('#status-edit-media-container').html("");
                form.reset();
                $('#edit_post').modal('hide');
            },
            processData: false,
            contentType: false,
            error: function(data)
           {
               console.log(data);
           }
        });
    }else {
        swal("Alert!", "You have to select one image, one youtube url or write some text at least.", "warning");
    }
});

$('#comment-add-giphy-btn-edit').on('click', function(e) {
    e.preventDefault();
    comment_img_form = $(this).parent();
    if (comment_img_form.find('#add_post_giphy').length > 0) {
        $('#add_post_giphy').remove();
        closable = false;
    } else {
        $('#add_post_giphy').remove();
        comment_img_form.append(pop_up_html);
        $('#post-all-giphy-container')[0].onscroll = load_moredata;
        clicked_element = $(this);
        clicked_element_width = 25;
        set_giphy_pos();
        selected_container = $('#comment-edit-media-container');
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
                        closable = false;
                    });
                }, 1000);
            },
            error: function(error){
                console.log(error);
            }
        });
    }
});

$('#reply-add-giphy-btn-edit').on('click', function(e) {
    e.preventDefault();
    comment_img_form = $(this).parent();
    if (comment_img_form.find('#add_post_giphy').length > 0) {
        $('#add_post_giphy').remove();
        closable = false;
    } else {
        $('#add_post_giphy').remove();
        comment_img_form.append(pop_up_html);
        $('#post-all-giphy-container')[0].onscroll = load_moredata;
        clicked_element = $(this);
        clicked_element_width = -20;
        set_giphy_pos();
        selected_container = $('#reply-edit-media-container');
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
                    });
                }, 1000);
            },
            error: function(error){
                console.log(error);
            }
        });
    }
});

$(document).on('click', '.comment-reply-button', function(e){
    e.preventDefault();
    $(this).parent().find('form.comment-reply-form').toggleClass('open');
});

$(document).on('click', '.comment-reply-button-child', function(e){
    e.preventDefault();
    $(this).parents('.comment-reply-container').next().toggleClass('open');
});

$(document).on('click', '.single-media-close-btn', function(e){
    e.preventDefault();
    $(this).parent().remove();
});

$(document).on('submit', '.comment-post-form', function(e){
    e.preventDefault();
    var form = $(this)[0];

    var comment_string = $(form).find('#comment-form').val();
    var element = $(form).find('.post-media-container');
    if (element.html().length > 0 || comment_string != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });

        var url = $(form).attr('action');
        var formData = new FormData(form);
        var comment_post_button = $(form).find('.comment-post-btn');
        comment_post_button.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                comment_post_button.button('reset');
                // swal("Complete!", "Comment Posted successfully.", "success");
                $(form).find('.post-media-container').html("");
                form.reset();
            },
            processData: false,
            contentType: false,
            error: function(data)
           {
               console.log(data);
           }
        });
    }else {
        swal("Alert!", "You have to select one image, one giphy or write some text at least.", "warning");
    }
})

$(document).on('click', '.delete-status-btn', function(e){
    e.preventDefault();
    var delete_url = $(this).attr('href');
    var status_delete_button = $(this);
    status_delete_button.button('loading');
    $.ajax({
        url: delete_url,
        type: 'get',
        success: function(result){
            if (result != "fail") {
                status_delete_button.button('reset');
            }
        },
        error: function(error){
            console.log(error);
        }
    });
})

$(document).on('click', '.reply-delete-btn', function(e){
    e.preventDefault();
    var delete_url = $(this).attr('href');
    var reply_delete_button = $(this);
    reply_delete_button.button('loading');
    $.ajax({
        url: delete_url,
        type: 'get',
        success: function(result){
            if (result == "success") {
                reply_delete_button.button('reset');
                swal("Deleted!", "Reply Deleted successfully.", "success");
            }
        },
        error: function(error){
            console.log(error);
        }
    });
})

$(document).on('keyup', '.poll-answer-field', function(e) {
    var answer_field = $(this);
    var poll_form = answer_field.parents('form#poll-create-form');
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

$(document).on('submit', '#poll-create-form', function(e) {
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
    var post_post_button = $(form).find('.poll-create-submit-btn');
    post_post_button.button('loading');

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function (data) {
            console.log(data);
            post_post_button.button('reset');
            $('#poll_modal').modal('hide');
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

$(document).on('click', '.field-delete-btn', function(e) {
    e.preventDefault();
    var current_form = $(this).parent();
    if (current_form.find('.poll-answer-field').hasClass('last')) {
        var preivew_form = current_form.prev();
        preivew_form.find('input.poll-answer-field').addClass('last');
    }
    current_form.remove();
    var count = 1;
    $('#poll-create-form .answer-number>span').each(function(e) {
        $(this).html(count);
        count ++;
    });
});

$(document).on('click', '.vote', function(e) {
    var answerId = $(this).data('answer_id');

    var areticle_container = $(this).parents('article.altpocket');

    areticle_container.addClass('voting');
    $.ajax({
        url: '/poll/vote/'+answerId,
        type: 'get',
        success: function(data){
            // areticle_container.removeClass('voting');
        }
    });
});

$(document).on('click', '.poll-delete-btn', function(e){
    e.preventDefault();
    var delete_url = $(this).attr('href');
    var poll_delete_btn = $(this);
    poll_delete_btn.button('loading');
    $.ajax({
        url: delete_url,
        type: 'get',
        success: function(result){
            if (result != "fail") {
                poll_delete_btn.button('reset');
            }
        },
        error: function(error){
            console.log(error);
        }
    });
})

$('#edit-comment-form').submit(function(e) {
    e.preventDefault();

    var form = $(this)[0];

    var comment_string = $(form).find('#edit-comment-field').val();
    var element = $(form).find('#comment-edit-media-container');
    if (element.html().length > 0 || comment_string != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });

        var url = $(form).attr('action');
        var formData = new FormData(form);
        var comment_edit_button = $(form).find('#edit-comment-submit-btn');
        comment_edit_button.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                console.log(data);
                if (data == "fail") {
                    swal("Failed!", "Went something Wrong. Try again.", "error");
                }else {
                    // swal("Complete!", "Comment Updated successfully.", "success");
                    $(form).find('.post-media-container').html("");
                    form.reset();
                    $('#edit_comment').modal('hide');
                }
                comment_edit_button.button('reset');
            },
            processData: false,
            contentType: false,
            error: function(data)
           {
               console.log(data);
           }
        });
    }else {
        swal("Alert!", "You have to select one image, one giphy or write some text at least.", "warning");
    }
})

$('#edit-comment-reply-form').submit(function(e) {
    e.preventDefault();

    var form = $(this)[0];

    var reply_string = $(form).find('#edit-comment-reply-field').val();
    var element = $(form).find('#reply-edit-media-container');
    if (element.html().length > 0 || reply_string != "") {
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            }
        });

        var url = $(form).attr('action');
        var formData = new FormData(form);
        var reply_edit_button = $(form).find('#edit-reply-submit-btn');
        reply_edit_button.button('loading');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                console.log(data);
                if (data == "fail") {
                    swal("Failed!", "Went something Wrong. Try again.", "error");
                }else {
                    // swal("Complete!", "Comment Updated successfully.", "success");
                    $(form).find('.post-media-container').html("");
                    form.reset();
                    $('#edit_comment_reply').modal('hide');
                }
                reply_edit_button.button('reset');
            },
            processData: false,
            contentType: false,
            error: function(data)
           {
               console.log(data);
           }
        });
    }else {
        swal("Alert!", "You have to select one image, one giphy or write some text at least.", "warning");
    }
})

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

$(window).scroll( function() {
    if($('#add_post_giphy').length) {
        set_giphy_pos();
    }
});

$( window ).resize(function() {
    if($('#add_post_giphy').length) {
        set_giphy_pos();
    }
    set_imgcontainer_size();
});

$(window).ready( function() {
    set_imgcontainer_size();
})
