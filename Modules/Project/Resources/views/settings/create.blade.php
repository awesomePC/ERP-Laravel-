{!! Form::open(['url' => action('\Modules\Project\Http\Controllers\ProjectController@postSettings', ['project_id' => $project->id]), 'id' => 'settings_form', 'method' => 'put']) !!}
	<div class="row">
		<div class="col-md-4">
			<div class="checkbox">
				<label>
				  <input type="checkbox" name="enable_timelog" value="1" @if(isset($project->settings['enable_timelog'])
				  && $project->settings['enable_timelog']) checked @endif> @lang('project::lang.enable_timelog')
				</label>
			</div>
		</div>
		
		<div class="col-md-4">
			<div class="checkbox">
				<label>
				  <input type="checkbox" name="enable_notes_documents" value="1" @if(isset($project->settings['enable_notes_documents']) && $project->settings['enable_notes_documents']) checked @endif> @lang('project::lang.enable_notes_documents')
				</label>
			</div>
		</div>

		<div class="col-md-4">
			<div class="checkbox">
				<label>
				  <input type="checkbox" name="enable_invoice" value="1" @if(isset($project->settings['enable_invoice']) && $project->settings['enable_invoice']) checked @endif> @lang('project::lang.enable_invoice')
				</label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<label>@lang('project::lang.task_view'):&nbsp;</label>
			<label class="radio-inline">
				<input type="radio" name="task_view" value="list_view" required @if(isset($project->settings['task_view']) && $project->settings['task_view'] == 'list_view') checked @endif>
				@lang('project::lang.list_view')
			</label>
			<label class="radio-inline">
				<input type="radio" name="task_view" value="kanban" required @if(isset($project->settings['task_view']) && $project->settings['task_view'] == 'kanban') checked @endif>
				@lang('project::lang.kanban_board')
			</label>
		</div>
		<div class="col-md-4">
			@php
				$task_id_prefix = !empty($project->settings['task_id_prefix']) ? $project->settings['task_id_prefix'] : '';
			@endphp
            <div class="form-group form-inline">
                {!! Form::label('task_id_prefix', __('project::lang.task_id_prefix') . ':*' )!!}
                {!! Form::text('task_id_prefix', $task_id_prefix, ['class' => 'form-control', 'required']) !!}
           </div>
        </div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3">
			<label>@lang('user.permissions')</label>
		</div>
		<div class="col-md-3">
			<label>@lang('project::lang.members')</label>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<label for="members_crud_task">
				@lang('project::lang.add_a_task')
			</label>
		</div>
		<div class="col-md-3">
			<input type="checkbox" id="members_crud_task" name="members_crud_task" value="1" @if(isset($project->settings['members_crud_task']) && $project->settings['members_crud_task']) checked @endif>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<label for="members_crud_timelog">
				@lang('project::lang.add_time_log')
			</label>
		</div>
		<div class="col-md-3">
			<input type="checkbox" id="members_crud_timelog" name="members_crud_timelog" value="1" @if(isset($project->settings['members_crud_timelog'])
		  	&& $project->settings['members_crud_timelog']) checked @endif> 
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<label for="members_crud_note">
				@lang('project::lang.add_notes_docs')
			</label>
		</div>
		<div class="col-md-3">
			<input type="checkbox" id="members_crud_note" name="members_crud_note" value="1" @if(isset($project->settings['members_crud_note'])
		  && $project->settings['members_crud_note']) checked @endif> 
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<button type="submit" class="btn btn-primary btn-sm pull-right">
		        @lang('messages.update')
		    </button>
	    </div>
	</div>
{!! Form::close() !!}