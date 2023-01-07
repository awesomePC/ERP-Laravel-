@foreach($comments as $comment)
    <div class="direct-chat-msg">
        <div class="direct-chat-info clearfix">
            <span class="direct-chat-name pull-left">
                {{$comment->commentedBy->user_full_name}}
            </span>
            <span class="direct-chat-timestamp pull-right">
                {{@format_datetime($comment->created_at)}}
            </span>
        </div>
        <!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="https://ui-avatars.com/api/?name={{$comment->commentedBy->first_name}}">
        <!-- /.direct-chat-img -->
        <div class="direct-chat-text">
            {!! $comment->comment !!}
            @if(Auth::user()->id == $comment->commented_by)
                <i class="delete-task-comment fa fa-trash pull-right text-danger cursor-pointer mt-5" data-comment_id="{{$comment->id}}" data-task_id="{{$comment->project_task_id}}"></i>
            @endif
            @if($comment->media->count() > 0)
                <br><br>
                @foreach($comment->media as $media)
                    <a href="{{$media->display_url}}" download="{{$media->display_name}}">
                        <i class="fa fa-download"></i>
                        {{$media->display_name}}
                    </a> &nbsp;&nbsp;
                @endforeach
            @endif
        </div>
    <!-- /.direct-chat-text -->
    </div>
@endforeach