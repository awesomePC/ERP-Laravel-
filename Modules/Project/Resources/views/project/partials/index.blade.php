<div class="row eq-height-row">
	@if($projects->count() > 0)
		@foreach($projects as $project)
			<div class="col-md-4 eq-height-col">
				<div class="box box-solid box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">
							<a href="{{action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project->id])}}">
					    		{{ucFirst($project->name)}}
					    	</a>
						</h3>
						<div class="box-tools pull-right">
							<div class="dropdown">
								  <button class="btn dropdown-toggle btn-sm btn-default" type="button" id="action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								  	<i class="fa fa-ellipsis-v"></i>
								  	&nbsp;@lang('messages.action')
								  </button>
								  <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="action">
								    <li>
								    	<a href="{{action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project->id])}}">
								    		<i class="fas fa-external-link-alt"></i>
								    		@lang('messages.view')
								    	</a>
								    </li>
								    @can('project.edit_project')
									    <li>
									    	<a data-href="{{action('\Modules\Project\Http\Controllers\ProjectController@edit', ['id' => $project->id])}}" class="cursor-pointer edit_a_project">
									    		<i class="fa fa-edit"></i>
									    		@lang('messages.edit')
									    	</a>
									    </li>
									@endcan
								    @can('project.delete_project')
									    <li>
									    	<a data-href="{{action('\Modules\Project\Http\Controllers\ProjectController@destroy', ['id' => $project->id])}}" class="cursor-pointer delete_a_project">
									    		<i class="fas fa-trash"></i>
									    		@lang('messages.delete')
									    	</a>
									    </li>
									@endcan
									<!-- more menus -->
									<li class="divider"></li>
									<li>
								    	<a href="{{action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project->id]).'?view=overview'}}">
								    		<i class="fas fa-tachometer-alt"></i>
								    		@lang('project::lang.overview')
								    	</a>
								    </li>
								    <li>
								    	<a href="{{action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project->id]).'?view=activities'}}">
								    		<i class="fas fa-chart-line"></i>
								    		@lang('lang_v1.activities')
								    	</a>
								    </li>
								    <li>
								    	<a href="{{action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project->id]).'?view=project_task'}}">
								    		<i class="fa fa-tasks"></i>
								    		@lang('project::lang.task')
								    	</a>
								    </li>
								    @if(isset($project->settings['enable_timelog']) && $project->settings['enable_timelog'])
								    	<li>
									    	<a href="{{action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project->id]).'?view=time_log'}}">
									    		<i class="fas fa-clock"></i>
									    		@lang('project::lang.time_logs')
									    	</a>
									    </li>
								    @endif

								    @if(isset($project->settings['enable_notes_documents']) && $project->settings['enable_notes_documents'])
								    	<li>
									    	<a href="{{action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project->id]).'?view=documents_and_notes'}}">
									    		<i class="fas fa-file-image"></i>
									    		@lang('project::lang.documents_and_notes')
									    	</a>
									    </li>
								    @endif

								    @if((isset($project->settings['enable_invoice']) && $project->settings['enable_invoice']) && $project->is_lead_or_admin)
								    	<li>
									    	<a href="{{action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project->id]).'?view=project_invoices'}}">
									    		<i class="fa fa-file"></i>
									    		@lang('project::lang.invoices')
									    	</a>
									    </li>
								    @endif

								    @if($project->is_lead_or_admin)
								    	<li>
								    		<a href="{{action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project->id]).'?view=project_settings'}}">
									    		<i class="fa fa-cogs"></i>
									    		@lang('role.settings')
									    	</a>
								    	</li>
								    @endif
								  </ul>
							</div>
						</div>
						<!-- /.box-tools -->
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="row">
							<div class="col-md-8">
								@if(isset($project->customer->name))
									<i class="fa fa-briefcase"></i>
									{{$project->customer->name}}
								@endif <br>
								<i class="fas fa-user-tie"></i>
								@lang('project::lang.lead'):
								{{$project->lead->user_full_name}}
								<br>
								<i class="fas fa-check-circle"></i>
								@lang('sale.status'):
								@lang('project::lang.'.$project->status)
								<br>
								@if(isset($project->start_date))
								<i class="fas fa-calendar-check"></i>
									@lang('business.start_date'):
									{{@format_date($project->start_date)}}
								@endif <br>
								@if(isset($project->end_date))
									<i class="fas fa-calendar-check"></i>
									@lang('project::lang.end_date'):
									{{@format_date($project->end_date)}}
								@endif
								@if($project->categories->count() > 0)
									<br>
									<i class="fa fas fa-gem"></i>
									@lang('category.categories'):
									<span>
									@foreach($project->categories as $categories)
										
										@if(!$loop->last)
											{{$categories->name . ','}}
										@else
											{{$categories->name}}
									    @endif
									@endforeach
									</span>
								@endif
							</div>
							<div class="col-md-4">
								<!-- progress bar -->
							</div>
						</div>
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						@includeIf('project::avatar.create', ['max_count' => '10', 'members' => $project->members])
					</div>
					<!-- box-footer -->
				</div>
				<!-- /.box -->
			</div>
			@if($loop->iteration%3 == 0)
				<div class="clearfix"></div>
			@endif
		@endforeach
	@else
		<div class="col-md-12">
			<div class="callout callout-info">
                <h4>
                	<i class="fa fa-warning"></i>
                	@lang('project::lang.project_not_found')
                </h4>
            </div>
		</div>
	@endif
</div>
@if($projects->nextPageUrl())
    <a data-href="{{$projects->nextPageUrl()}}" class="btn btn-block btn-sm btn-info load_more_project">
		@lang('project::lang.load_more')
	</a>
@endif