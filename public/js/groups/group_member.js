$(document).ready(function(){
    $('.group-user-make-admin').on('click', function(e){
        e.preventDefault();
        var $this = $(this);
        var make_user_admin_url = $this.attr('href');
        swal({
		  title: "Are you sure?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#0ed2d0",
		  confirmButtonText: "Yes, Make admin!",
		  cancelButtonText: "No, cancel!",
		  closeOnConfirm: true,
		  closeOnCancel: true
		}, function (isConfirm) {
			if (isConfirm) {
                window.location.href = make_user_admin_url;
			} else {

			}
		});
    });

    $('.group-member-remove-as-admin').on('click', function(e){
        e.preventDefault();
        var $this = $(this);
        var remove_as_admin_url = $this.attr('href');
        swal({
		  title: "Are you sure?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#0ed2d0",
		  confirmButtonText: "Yes, Remove as admin!",
		  cancelButtonText: "No, cancel!",
		  closeOnConfirm: true,
		  closeOnCancel: true
		}, function (isConfirm) {
			if (isConfirm) {
                window.location.href = remove_as_admin_url;
			} else {

			}
		});
    });

    $('.group-blocked-member-remove').on('click', function(e){
        e.preventDefault();
        var $this = $(this);
        var remove_blocked_member_url = $this.attr('href');
        swal({
		  title: "Are you sure?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#0ed2d0",
		  confirmButtonText: "Yes, Remove block user!",
		  cancelButtonText: "No, cancel!",
		  closeOnConfirm: true,
		  closeOnCancel: true
		}, function (isConfirm) {
			if (isConfirm) {
                window.location.href = remove_blocked_member_url;
			} else {

			}
		});
    });

    $('.group-member-remove-request').on('click', function(e){
        e.preventDefault();
        var $this = $(this);
        var user_name = $this.data('requester_name');
        var user_id = $this.data('request_id');
        $('#group-member-remove-modal #remove_user_id').val(user_id);
        $('#group-member-remove-modal #remove_member_level').val(3);
        $('#group-member-remove-modal').find('span.remove-member-name').each(function(){
            $(this).html(user_name);
        })
        $('#group-member-remove-modal').modal('show');
    });

    $('.group-member-remove-admin').on('click', function(e){
        e.preventDefault();
        var $this = $(this);
        var user_name = $this.data('requester_name');
        var user_id = $this.data('request_id');
        $('#group-member-remove-modal #remove_user_id').val(user_id);
        $('#group-member-remove-modal #remove_member_level').val(1);
        $('#group-member-remove-modal').find('span.remove-member-name').each(function(){
            $(this).html(user_name);
        })
        $('#group-member-remove-modal').modal('show');
    });

    $('.group-member-remove-user').on('click', function(e){
        e.preventDefault();
        var $this = $(this);
        var user_name = $this.data('requester_name');
        var user_id = $this.data('request_id');
        $('#group-member-remove-modal #remove_user_id').val(user_id);
        $('#group-member-remove-modal #remove_member_level').val(0);
        $('#group-member-remove-modal').find('span.remove-member-name').each(function(){
            $(this).html(user_name);
        })
        $('#group-member-remove-modal').modal('show');
    });
});
