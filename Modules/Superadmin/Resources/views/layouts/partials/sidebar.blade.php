@can('superadmin')
	<li class="bg-red treeview {{ in_array($request->segment(1), ['superadmin']) ? 'active active-sub' : '' }}">
	    <a href="#">
	        <i class="fa fa-bank"></i>
	        <span class="title">@lang('superadmin::lang.superadmin')</span>
	        <span class="pull-right-container">
	            <i class="fa fa-angle-left pull-right"></i>
	        </span>
	    </a>

	    <ul class="treeview-menu">
			<li class="{{ empty($request->segment(2)) ? 'active active-sub' : '' }}">
				<a href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminController@index')}}">
					<i class="fa fa-bank"></i>
					<span class="title">
						@lang('superadmin::lang.superadmin')
					</span>
			  	</a>
			</li>

			<li class="{{ $request->segment(2) == 'business' ? 'active active-sub' : '' }}">
				<a href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@index')}}">
					<i class="fa fa-bank"></i>
					<span class="title">
						@lang('superadmin::lang.all_business')
					</span>
			  	</a>
			</li>
			<!-- superadmin subscription -->
			<li class="{{ $request->segment(2) == 'superadmin-subscription' ? 'active active-sub' : '' }}">
			<a href = "{{action('\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController@index')}}"><i class="fa fa-refresh"></i>
			<span class="title">@lang('superadmin::lang.subscription')</span>
			</a>
			</li>

			<li class="{{ $request->segment(2) == 'packages' ? 'active active-sub' : '' }}">
				<a href="{{action('\Modules\Superadmin\Http\Controllers\PackagesController@index')}}">
					<i class="fa fa-credit-card"></i>
					<span class="title">
						@lang('superadmin::lang.subscription_packages')
					</span>
			  	</a>
			</li>

			{{-- <li class="{{ $request->segment(2) == 'frontend-pages' ? 'active active-sub' : '' }}">
				<a href="{{action('\Modules\Superadmin\Http\Controllers\PageController@index')}}">
					<i class="fa fa-clone"></i>
					<span class="title">
						@lang('superadmin::lang.frontend_pages')
					</span>
			  	</a>
			</li> --}}

			<li class="{{ $request->segment(2) == 'settings' ? 'active active-sub' : '' }}">
				<a href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@edit')}}">
					<i class="fa fa-cogs"></i>
					<span class="title">
						@lang('superadmin::lang.super_admin_settings')
					</span>
			  	</a>
			</li>

			<li class="{{ $request->segment(2) == 'communicator' ? 'active active-sub' : '' }}">
				<a href="{{action('\Modules\Superadmin\Http\Controllers\CommunicatorController@index')}}">
					<i class="fa fa-envelope"></i>
					<span class="title">
						@lang('superadmin::lang.communicator')
					</span>
			  	</a>
			</li>

        </ul>
	</li>
@endcan