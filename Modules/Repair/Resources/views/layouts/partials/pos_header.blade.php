@if($__is_repair_enabled)
	@can("repair.create")
		<a href="{{ action('SellPosController@create'). '?sub_type=repair'}}" title="{{ __('repair::lang.add_repair') }}" data-toggle="tooltip" data-placement="bottom" class="btn bg-purple btn-flat m-6 btn-xs m-5 pull-right">
			<i class="fa fa-wrench fa-lg"></i>
			<strong>@lang('repair::lang.repair')</strong>
		</a>
	@endcan
@endif