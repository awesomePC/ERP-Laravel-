@php
$code = strtolower($system_currency->code);
@endphp

<div class="col-md-12">
	<form action="{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@confirm', [$package->id])}}" method="POST">
	 	{{ csrf_field() }}
	 	<input type="hidden" name="gateway" value="{{$k}}">
		<script
		        src="https://checkout.stripe.com/checkout.js" class="stripe-button"
		        data-key="{{env('STRIPE_PUB_KEY')}}"
		        data-amount="@if(in_array($code, ['bif','clp','djf','gnf','jpy','kmf','krw','mga','pyg','rwf','ugx','vnd','vuv','xaf','xof','xpf'])) {{$package->price}} @else {{$package->price*100}} @endif"
		        data-name="{{env('APP_NAME')}}"
		        data-description="{{$package->name}}"
		        data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
		        data-locale="auto"
		        data-currency="{{$code}}">
		</script>
	</form>
</div>