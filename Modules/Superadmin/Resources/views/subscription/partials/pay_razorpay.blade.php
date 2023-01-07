<div class="col-md-12">

	<form action="{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@confirm', [$package->id])}}" method="POST">
		<!-- Note that the amount is in paise -->
		<script
		    src="https://checkout.razorpay.com/v1/checkout.js"
		    data-key="{{env('RAZORPAY_KEY_ID')}}"
		    data-amount="{{$package->price*100}}"
		    data-buttontext="Pay with Razorpay"
		    data-name="{{env('APP_NAME')}}"
		    data-description="{{$package->name}}"
		    data-theme.color="#3c8dbc"
		></script>
		{{ csrf_field() }}
		<input type="hidden" name="gateway" value="{{$k}}">
	</form>
</div>