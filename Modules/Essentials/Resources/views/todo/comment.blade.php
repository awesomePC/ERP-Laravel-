<div class="direct-chat-msg">
  	<div class="direct-chat-info clearfix">
    	<span class="direct-chat-name pull-left">{{$comment->added_by->user_full_name}}</span>
    	<span class="direct-chat-timestamp pull-right">{{@format_datetime($comment->created_at)}}</span>
  	</div>

  	<div class="direct-chat-text" style="margin-left: 0;">
    	{{$comment->comment}}

    	@if(auth()->user()->id == $comment->comment_by)
    		<i class="delete-comment fa fa-trash pull-right text-danger" style="cursor: pointer;" data-comment_id="{{$comment->id}}"></i>
    	@endif
  	</div>
</div>