<div class="clearfix"></div>
<hr>
<div class="col-md-12">
	<h4>@lang('essentials::lang.hrm_details'):</h4>
</div>
<div class="col-md-12">
	<p><strong>@lang('essentials::lang.department'):</strong> {{$user_department->name ?? ''}}</p>
	<p><strong>@lang('essentials::lang.designation'):</strong> {{$user_designstion->name ?? ''}}</p>
	<p>
		<strong>@lang('essentials::lang.salary'):</strong> 
		@if(!empty($user->essentials_salary) && !empty($user->essentials_pay_period))
			@format_currency($user->essentials_salary) @lang('essentials::lang.per')
			@if($user->essentials_pay_period == 'week')
				{{__('essentials::lang.week')}}
			@else
				{{__('lang_v1.'.$user->essentials_pay_period)}}
			@endif
		@endif
	</p>

	<p>
		<strong>@lang('essentials::lang.pay_cycle'):</strong>
		@if(!empty($user->essentials_pay_period))
			@if($user->essentials_pay_period == 'week')
				{{__('essentials::lang.week')}}
			@else
				{{__('lang_v1.month')}}
			@endif
		@endif
	</p>

	<p>
		<strong>@lang('lang_v1.primary_work_location'):</strong>
		@if(!empty($work_location))
			{{$work_location->name}}
		@else
			{{__('report.all_locations')}}
		@endif
	</p>
</div>