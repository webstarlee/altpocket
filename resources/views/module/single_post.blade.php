@if($status->type == "status" && !$status->isHidden())
    <div class="ui-block posted_status_{{$status->id}}">
        @include('module.single_post_edit')
    </div>
@elseif($status->type == "poll")
    <div class="ui-block posted_status_{{$status->id}}">
        @include('module.single_post_edit')
    </div>
@endif
