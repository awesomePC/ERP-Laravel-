@if($__is_woo_enabled)
	@if(auth()->user()->can('superadmin') || auth()->user()->can('woocommerce.syc_categories') || auth()->user()->can('woocommerce.sync_products') || auth()->user()->can('woocommerce.sync_orders') || auth()->user()->can('woocommerce.map_tax_rates') || auth()->user()->can('woocommerce.access_woocommerce_api_settings'))
		<li class="bg-woocommerce treeview {{ in_array($request->segment(1), ['woocommerce']) ? 'active active-sub' : '' }}">
		    <a href="#">
		        <i class="fa fa-wordpress"></i>
		        <span class="title">@lang('woocommerce::lang.woocommerce')</span>
		        <span class="pull-right-container">
		            <i class="fa fa-angle-left pull-right"></i>
		        </span>
		    </a>

		    <ul class="treeview-menu">
		    	<li class="{{ $request->segment(1) == 'woocommerce' && empty($request->segment(2)) ? 'active active-sub' : '' }}">
					<a href="{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@index')}}">
						<i class="fa fa-refresh"></i>
						<span class="title">
							@lang('woocommerce::lang.sync')
						</span>
				  	</a>
				</li>
				<li class="{{ $request->segment(1) == 'woocommerce' && $request->segment(2) == 'view-sync-log' ? 'active active-sub' : '' }}">
					<a href="{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@viewSyncLog')}}">
						<i class="fa fa-history"></i>
						<span class="title">
							@lang('woocommerce::lang.sync_log')
						</span>
				  	</a>
				</li>
				@if(auth()->user()->can('woocommerce.access_woocommerce_api_settings') )
				<li class="{{ $request->segment(1) == 'woocommerce' && $request->segment(2) == 'api-settings' ? 'active active-sub' : '' }}">
					<a href="{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@apiSettings')}}">
						<i class="fa fa-cogs"></i>
						<span class="title">
							@lang('woocommerce::lang.api_settings')
						</span>
				  	</a>
				</li>
				@endif
	        </ul>
		</li>
	@endif
@endif