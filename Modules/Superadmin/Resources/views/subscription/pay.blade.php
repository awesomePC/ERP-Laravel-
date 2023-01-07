@extends($layout)

@section('title', __('superadmin::lang.subscription'))

@section('content')

<!-- Main content -->
<section class="content">

	@include('superadmin::layouts.partials.currency')

	<div class="box box-success">
        <div class="box-header">
            <h3 class="box-title">@lang('superadmin::lang.pay_and_subscribe')</h3>
        </div>

        <div class="box-body">
    		<div class="col-md-8">
        		<h3>
        			{{$package->name}}

        			(<span class="display_currency" data-currency_symbol="true">{{$package->price}}</span>

					<small>
						/ {{$package->interval_count}} {{ucfirst($package->interval)}}
					</small>)
        		</h3>
        		<ul>
					<li>
						@if($package->location_count == 0)
							@lang('superadmin::lang.unlimited')
						@else
							{{$package->location_count}}
						@endif

						@lang('business.business_locations')
					</li>

					<li>
						@if($package->user_count == 0)
							@lang('superadmin::lang.unlimited')
						@else
							{{$package->user_count}}
						@endif

						@lang('superadmin::lang.users')
					</li>

					<li>
						@if($package->product_count == 0)
							@lang('superadmin::lang.unlimited')
						@else
							{{$package->product_count}}
						@endif

						@lang('superadmin::lang.products')
					</li>

					<li>
						@if($package->invoice_count == 0)
							@lang('superadmin::lang.unlimited')
						@else
							{{$package->invoice_count}}
						@endif

						@lang('superadmin::lang.invoices')
					</li>

					@if($package->trial_days != 0)
						<li>
							{{$package->trial_days}} @lang('superadmin::lang.trial_days')
						</li>
					@endif
				</ul>

				<ul class="list-group">
					@foreach($gateways as $k => $v)
						<div class="list-group-item">
							<b>@lang('superadmin::lang.pay_via', ['method' => $v])</b>
							
							<div class="row" id="paymentdiv_{{$k}}">
								@php 
									$view = 'superadmin::subscription.partials.pay_'.$k;
								@endphp
								@includeIf($view)
							</div>
						</div>
					@endforeach
				</ul>
			</div>
        </div>
    </div>
</section>
@endsection