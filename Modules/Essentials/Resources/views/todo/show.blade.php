@if(count($todo) > 0)
	@foreach($todo as $do)
	<li class="todo_li">
		@if($do->is_completed == 1)
		<input type="checkbox" name="todo_id" class="todo_id" value ="{{ $do->id }}" checked>

		<a href="{{action('\Modules\Essentials\Http\Controllers\ToDoController@show', $do->id)}}"><span class="text task_name" style="text-decoration:line-through;">
			{{ $do->task }}
		</span></a>
		<i class="fa fa-trash text-danger pull-right delete_task cursor-pointer" style="display:none;">
			<span class="hidden">{{ $do->id }}</span>
		</i>
		@else
		<input type="checkbox" name="todo_id" class="todo_id" value ="{{ $do->id }}">

		<a href="{{action('\Modules\Essentials\Http\Controllers\ToDoController@show', $do->id)}}"><span class="text task_name"> {{ $do->task }}</span></a>
		@endif
		<br>
		<small class="text-muted"><strong>@lang('essentials::lang.assigned_to'): </strong> {{$do->user->user_full_name}}</small>
		<div class="tools">
			<i class="fa fa-trash text-danger pull-right delete_task cursor-pointer action_btn">
				<span class="hidden">{{ $do->id }}</span>
			</i>
			<i class="fa fa-edit text-primary pull-right btn-modal cursor-pointer action_btn"  data-container="#task_modal" data-href="{{action('\Modules\Essentials\Http\Controllers\ToDoController@edit', $do->id)}}"></i>
		</div>
	</li>
	@endforeach
@else
	<h2 class="text-center text-info">
		{{ __('essentials::lang.no_task') }}
	</h2>
@endif