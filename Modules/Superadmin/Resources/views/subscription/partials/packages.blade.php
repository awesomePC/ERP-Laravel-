@foreach ($packages as $package)
	@if($package->is_private == 1 && !auth()->user()->can('superadmin'))
		@php
			continue;
		@endphp
	@endif
    <div class="col-md-4">
    	
		<div class="box box-success hvr-grow-shadow">
			<div class="box-header with-border text-center">
				<h2 class="box-title">{{$package->name}}</h2>
			</div>
			
			<!-- /.box-header -->
			<div class="box-body text-center">

				<i class="fa fa-check text-success"></i>
				@if($package->location_count == 0)
					@lang('superadmin::lang.unlimited')
				@else
					{{$package->location_count}}
				@endif

				@lang('business.business_locations')
				<br/><br/>

				<i class="fa fa-check text-success"></i>
				@if($package->user_count == 0)
					@lang('superadmin::lang.unlimited')
				@else
					{{$package->user_count}}
				@endif

				@lang('superadmin::lang.users')
				<br/><br/>

				<i class="fa fa-check text-success"></i>
				@if($package->product_count == 0)
					@lang('superadmin::lang.unlimited')
				@else
					{{$package->product_count}}
				@endif

				@lang('superadmin::lang.products')
				<br/><br/>

				<i class="fa fa-check text-success"></i>
				@if($package->invoice_count == 0)
					@lang('superadmin::lang.unlimited')
				@else
					{{$package->invoice_count}}
				@endif

				@lang('superadmin::lang.invoices')
				<br/><br/>

				@if(!empty($package->custom_permissions))
					@foreach($package->custom_permissions as $permission => $value)
						@isset($permission_formatted[$permission])
							<i class="fa fa-check text-success"></i>
							{{$permission_formatted[$permission]}}
							<br/><br/>
						@endisset
					@endforeach
				@endif

				@if($package->trial_days != 0)
					<i class="fa fa-check text-success"></i>
					{{$package->trial_days}} @lang('superadmin::lang.trial_days')
					<br/><br/>
				@endif
				
				<h3 class="text-center">
				@php
					$interval_type = !empty($intervals[$package->interval]) ? $intervals[$package->interval] : __('lang_v1.' . $package->interval);
				@endphp
					@if($package->price != 0)
						<span class="display_currency" data-currency_symbol="true">
							{{$package->price}}
						</span>

						<small>
							/ {{$package->interval_count}} {{$interval_type}}
						</small>
					@else
						@lang('superadmin::lang.free_for_duration', ['duration' => $package->interval_count . ' ' . $interval_type])
					@endif
				</h3>
			</div>
			<!-- /.box-body -->

			<div class="box-footer bg-gray disabled text-center">
				@if($package->enable_custom_link == 1)
					<a href="{{$package->custom_link}}" class="btn btn-block btn-success">{{$package->custom_link_text}}</a>
				@else
					@if(isset($action_type) && $action_type == 'register')
						<a href="{{ route('business.getRegister') }}?package={{$package->id}}" 
						class="btn btn-block btn-success">
		    				@if($package->price != 0)
		    					@lang('superadmin::lang.register_subscribe')
		    				@else
		    					@lang('superadmin::lang.register_free')
		    				@endif
	    				</a>
					@else
	    				<a href="{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@pay', [$package->id])}}" 
						class="btn btn-block btn-success">
		    				@if($package->price != 0)
		    					@lang('superadmin::lang.pay_and_subscribe')
		    				@else
		    					@lang('superadmin::lang.subscribe')
		    				@endif
	    				</a>
					@endif
				@endif

    			{{$package->description}}
			</div>
		</div>
		<!-- /.box -->
    </div>
    @if($loop->iteration%3 == 0)
    	<div class="clearfix"></div>
    @endif
@endforeach