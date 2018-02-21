<?php
    if (isset($count)) {
        $single_count = $count;
    }else {
        $single_count = 0;
    }
?>
<li class="li-comment status{{$comment->statusid}} comment-id_{{$comment->id}}" @if($single_count > 5) style="display:none;" @endif>
    @include('module.single_comment_edit')
</li>
