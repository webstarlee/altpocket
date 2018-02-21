<div class="modal fade" id="create-friend-group-1" tabindex="-1" role="dialog" aria-labelledby="edit_comment">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="io-group-new-group-create" action="{{route('group.post')}}" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Group</h4>
                    <ul class="card-actions icons right-top">
                        <li>
                            <a href="javascript:void(0)" data-dismiss="modal" class="text-white" aria-label="Close">
                                <i class="zmdi zmdi-close"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                {{ csrf_field() }}
                <div class="modal-body p-b-0">
                    <div class="form-group">
                        <label for="group_name" class="control-label">Name your group</label>
                        <input type="text" class="form-control" name="group_name" id="group_name" placeholder="E.g : Altpoket Employee" value="" required="" aria-required="true">
                    </div>
                    <div class="form-group">
                        <label for="private_group" class="control-label">Select privacy</label>
                        <select class="select form-control" id="private_group" name="private_group">
                            <option value="0">Public</option>
                            <option value="1">Private</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="group_description" class="control-label">Group Description</label>
                        <textarea class="form-control form-control-2" style="background-image:none!important;" id="group_description" name="group_description" placeholder=""></textarea>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="control-label">Add some people</label>
                        <select name="request_user[]" class="form-control autocomplete-user" data-ajax-url="{{route('group.autouser')}}" multiple>
                        </select>
                    </div>
                    <button type="button" id="io-group-new-group-create-btn" class="btn btn-primary">Create Group</button>
                </div>
            </form>
        </div>
        <!-- modal-content -->
    </div>
    <!-- modal-dialog -->
</div>
