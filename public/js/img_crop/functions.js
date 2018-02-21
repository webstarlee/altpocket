$(document).ready(function(){
	/* When click on change profile pic */
	// jQuery('#change-profile-pic').on('click', function(e){
    //     jQuery('#profile_pic_modal').modal({show:true});
    // });
	
	// $('.upload-group-couver-pic').unbind("click").bind("click", function(e){
    //     $('#group_media_photo').click();
    // });
	
	$('#just_img_save').on("click", function(e){
		var group_photo_id = $('#group_photo_id').val();
		var photo_save_url = $(this).data('save_url');
		var photo_save_final_url = photo_save_url+"/"+group_photo_id;
		$.ajax({
			url: photo_save_final_url,
			type: 'get',
			success: function(result){
				if (result != "false") {
					$('.group-cover-photo-view-container .group-cover-photo-view-img').attr('src', result);
					$('.group-cover-photo-view-container .group-cover-photo-view-img').css({'display':'block'});
					$('.group-cover-photo-view-container .group-cover-photo-null-img').css({'display':'none'});
					$('.group-cover-photo-view-container').css({'display':'block'});
					$('.group-cover-photo-edit-container').css({'display':'none'});
				}
			},
			error: function(error){
				console.log(error);
			}
		});
        // console.log(photo_save_url);
    });
	
	$('.group-cover-photo-view-img.show-edit-btn , .group-cover-photo-setting-btn-div').mouseover( function(){
		$('.group-cover-photo-setting-btn-div').css({'display':'block'});
	}).mouseout(function() {
		$('.group-cover-photo-setting-btn-div').css({'display':'none'});
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
	
	var crop_img = null;
	
	$('#group_media_photo').on('change', function()	{
		$.ajaxSetup({
			headers: {
				'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
			}
		});
		var form = $("#group_media_photo_upload_form")[0];
		var url = $(form).attr( 'action' );

		var formData = new FormData($(form)[0]);
		var img_selected = $(this).val();
		
		if (img_selected != "") {
			$('.photo-upload-loading-img').css({'display':'block'});
			$('.photo-upload-loading-overlay').css({'display':'block'});
			$.ajax({
				url: url,
				type: 'POST',
				data: formData,
				success: function (data) {
            		// console.log(data);
					$('.photo-upload-loading-img').css({'display':'none'});
					$('.photo-upload-loading-overlay').css({'display':'none'});
					$('.group-select-image-alert-div').css({'display':'none'});
					$('.group-cover-photo-view-container').css({'display':'none'});
					$('#group_coverphoto_tag').attr('src', data.img_url);
					$('#group_photo_id').val(data.group_photo_id);
					$('.group-cover-photo-edit-container').css({'display':'block'});
					var choose_add_html = '<div class="col-sm-4">'+
										'<div class="choose-photo-item">'+
										'<div class="radio">'+
										'<label class="custom-radio">'+
										'<img src="'+data.img_url+'" alt="photo">'+
										'<input type="radio" name="group_photo_radio" value="'+data.group_photo_id+'"></label></div></div></div>';
					$('#choose-from-group-photo-container').append(choose_add_html);
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
	
	$('#open_save_crop_modal').on('click', function() {
		console.log("crop");
		var img_url = $('#group_coverphoto_tag').attr('src');
		var d = new Date();
		var now_time = d.getTime();
		var img_html = "<img id='photo' class='' src='"+img_url+'?'+now_time+"' class='preview'/>";
		$('#profile_pic_modal #preview-profile-pic').html(img_html);
		$('#profile_pic_modal form#cropimage #group_media_photo_id').val($('#group_photo_id').val());
		$('#profile_pic_modal').modal('show');
		$('img#photo').imgAreaSelect({
			aspectRatio: '7:2',
			onSelectEnd: getSizes,
		});
	});
	$('.close_modal_btn').on('click', function(){
		$(".imgareaselect-selection").parent().remove();
		$(".imgareaselect-outer").remove();
		$('#profile_pic_modal').modal('hide');
	});
	
	
	// function
	
	/* handle functionality when click crop button  */
	$('#cropimage').submit(function(e){
    	e.preventDefault();
		$.ajaxSetup({
			headers: {
				'X-CSRF-Token': $('meta[name=_token]').attr('content')
			}
		});
		var $this = $(this);
		var url = $(this).attr( 'action' );
		$('#hdn_t_width').val($('#photo').width());
		var heightere = $('#photo').outerHeight();
		$('#hdn_t_height').val(heightere);
		
		setTimeout(function () {
			var formData = new FormData($this[0]);
	        
			$.ajax({
				url: url,
				type: 'POST',
				data: formData,
				success: function (data) {
					console.log(data);
					$('.group-cover-photo-view-container .group-cover-photo-view-img').attr('src', data);
					$('.group-cover-photo-view-container .group-cover-photo-view-img').css({'display':'block'});
					$('.group-cover-photo-view-container .group-cover-photo-null-img').css({'display':'none'});
					$('.group-cover-photo-view-container').css({'display':'block'});
					$('.group-cover-photo-edit-container').css({'display':'none'});
					$(".imgareaselect-selection").parent().remove();
					$(".imgareaselect-outer").remove();
					$('#profile_pic_modal').modal('hide');
				},
				processData: false,
				contentType: false,
				error: function(data)
			   {
				   console.log(data);
			   }
			});
		}, 500);
    });
    /* Function to get images size */
    function getSizes(img, obj){
        var x_axis = obj.x1;
        var x2_axis = obj.x2;
        var y_axis = obj.y1;
        var y2_axis = obj.y2;
        var thumb_width = obj.width;
        var thumb_height = obj.height;
        if(thumb_width > 0) {
			$('#hdn-x1-axis').val(x_axis);
			$('#hdn-y1-axis').val(y_axis);
			$('#hdn-x2-axis').val(x2_axis);
			$('#hdn-y2-axis').val(y2_axis);
			$('#hdn-thumb-width').val(thumb_width);
			$('#hdn-thumb-height').val(thumb_height);
        } else {
            alert("Please select portion..!");
		}
    }
    /* Function to save crop images */
    function saveCropImage(params) {
		$.ajax({
			url: params['targetUrl'],
			cache: false,
			dataType: "html",
			data: {
				action: params['action'],
				id: $('#hdn-profile-id').val(),
				 t: 'ajax',
				w1:params['thumb_width'],
				x1:params['x_axis'],
				h1:params['thumb_height'],
				y1:params['y_axis'],
				x2:params['x2_axis'],
				y2:params['y2_axis'],
				image_name :jQuery('#image_name').val()
			},
			type: 'Post',
		   	success: function (response) {
					jQuery('#profile_pic_modal').modal('hide');
					jQuery(".imgareaselect-border1,.imgareaselect-border2,.imgareaselect-border3,.imgareaselect-border4,.imgareaselect-border2,.imgareaselect-outer").css('display', 'none');
					
					jQuery("#profile_picture").attr('src', response);
					jQuery("#preview-profile-pic").html('');
					jQuery("#profile_pic").val();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert('status Code:' + xhr.status + 'Error Message :' + thrownError);
			}
		});
    }
});
