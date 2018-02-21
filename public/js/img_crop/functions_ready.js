jQuery(document).ready(function(){
	/* When click on change profile pic */
	// jQuery('#change-profile-pic').on('click', function(e){
    //     jQuery('#profile_pic_modal').modal({show:true});
    // });
	
	jQuery('#upload-group-couver-pic').unbind("click").bind("click", function(e){
        jQuery('#group_media_photo').click();
    });
	
	var crop_img = null;
	
	jQuery('#group_media_photo').on('change', function()	{
		$.ajaxSetup({
			headers: {
				'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
			}
		});
		var form = jQuery("#cropimage")[0];
		var url = $(form).attr( 'action' );

		var formData = new FormData($(form)[0]);
		var img_selected = $(this).val();
		
		if (img_selected != "") {
			if (crop_img != null) {
				$(".imgareaselect-selection").parent().remove();
				$(".imgareaselect-outer").remove();
			}
			jQuery("#preview-profile-pic").html('');
			jQuery("#preview-profile-pic").html('Uploading....');
			$.ajax({
				url: url,
				type: 'POST',
				data: formData,
				success: function (data) {
					if (data == "small_img") {
						$('#preview-profile-pic').html("Image demention too low");
					}else {
						var d = new Date();
						var now_time = d.getTime();
						$('#close_modal_btn').css({'display': 'none'});
						$('#save_crop').css({'display': 'inline-block'});
						var img_html = "<img id='photo' file-name='"+data.real_name+"' class='' src='"+data.img_url+'?'+now_time+"' class='preview'/>";
						
						$('#preview-profile-pic').html(img_html);
						crop_img = "start";
						$('img#photo').imgAreaSelect({
							aspectRatio: '5:2',
							onSelectEnd: getSizes,
						});
						
						jQuery('#image_name').val(jQuery('#photo').attr('file-name'));
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
		
		// jQuery("#cropimage").ajaxForm(
		// {
		// target: '#preview-profile-pic',
		// success:    function(result) {
		// 	console.log(result);
			// 	jQuery('img#photo').imgAreaSelect({
			// 	aspectRatio: '3:1',
			// 	onSelectEnd: getSizes,
			// });
			// jQuery('#image_name').val(jQuery('#photo').attr('file-name'));
		// 	}
		// }).submit();

	});
	
	// function
	/* handle functionality when click crop button  */
	jQuery('#save_crop').on('click', function(e){
    e.preventDefault();
    params = {
            targetUrl: 'change_pic.php?action=save',
            action: 'save',
            x_axis: jQuery('#hdn-x1-axis').val(),
            y_axis : jQuery('#hdn-y1-axis').val(),
            x2_axis: jQuery('#hdn-x2-axis').val(),
            y2_axis : jQuery('#hdn-y2-axis').val(),
            thumb_width : jQuery('#hdn-thumb-width').val(),
            thumb_height:jQuery('#hdn-thumb-height').val()
        };
        saveCropImage(params);
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
			jQuery('#hdn-x1-axis').val(x_axis);
			jQuery('#hdn-y1-axis').val(y_axis);
			jQuery('#hdn-x2-axis').val(x2_axis);
			jQuery('#hdn-y2-axis').val(y2_axis);
			jQuery('#hdn-thumb-width').val(thumb_width);
			jQuery('#hdn-thumb-height').val(thumb_height);
        } else {
            alert("Please select portion..!");
		}
    }
    /* Function to save crop images */
    function saveCropImage(params) {
		jQuery.ajax({
			url: params['targetUrl'],
			cache: false,
			dataType: "html",
			data: {
				action: params['action'],
				id: jQuery('#hdn-profile-id').val(),
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
