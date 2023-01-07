@if($__is_mfg_enabled)
	<li class="treeview bg-manufacturing {{ in_array($request->segment(1), ['manufacturing']) ? 'active active-sub' : '' }}">
	    <a href="#">
	        <i class="fa fa-industry"></i>
	        <span class="title">@lang('manufacturing::lang.manufacturing')</span>
	        <span class="pull-right-container">
	            <i class="fa fa-angle-left pull-right"></i>
	        </span>
	    </a>

	    <ul class="treeview-menu">
	    	@can('manufacturing.access_recipe')
		    	<li class="{{ $request->segment(1) == 'manufacturing' && in_array($request->segment(2), ['recipe', 'add-ingredient']) ? 'active active-sub' : '' }}">
					<a href="{{action('\Modules\Manufacturing\Http\Controllers\RecipeController@index')}}">
						<i class="fa fa-cutlery"></i>
						<span class="title">
							@lang('manufacturing::lang.recipe')
						</span>
				  	</a>
				</li>
			@endcan
			@can('manufacturing.access_production')
				<li class="{{ $request->segment(2) == 'production' && empty($request->segment(3))  ? 'active active-sub' : '' }}">
					<a href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@index')}}">
						<i class="fa fa-cogs"></i>
						<span class="title">
							@lang('manufacturing::lang.production')
						</span>
				  	</a>
				</li>
				<li class="{{ $request->segment(2) == 'production' && $request->segment(3) == 'create'  ? 'active active-sub' : '' }}">
					<a href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@create')}}">
						<i class="fa fa-plus"></i>
						<span class="title">
							@lang('manufacturing::lang.add_production')
						</span>
				  	</a>
				</li>
				<li class="{{ $request->segment(1) == 'manufacturing' && $request->segment(2) == 'settings' ? 'active active-sub' : '' }}">
					<a href="{{action('\Modules\Manufacturing\Http\Controllers\SettingsController@index')}}">
						<i class="fa fa-wrench"></i>
						<span class="title">
							@lang('messages.settings')
						</span>
				  	</a>
				</li>
				<li class="{{ $request->segment(2) == 'report' ? 'active active-sub' : '' }}">
					<a href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@getManufacturingReport')}}">
						<i class="fa fa-line-chart"></i>
						<span class="title">
							@lang('manufacturing::lang.manufacturing_report')
						</span>
				  	</a>
				</li>
			@endcan
        </ul>
	</li>
@endif