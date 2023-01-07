<div class="col-md-12">
    @if($system_currency->country == 'Nigeria')
        @php
            $currency_code = 'NGN';
        @endphp
    @elseif($system_currency->country == 'Ghana')
        @php
            $currency_code = 'GHS';
        @endphp
    @else
        @php
            $currency_code = $system_currency->code
        @endphp
    @endif
    <form>
        <script src="https://checkout.flutterwave.com/v3.js"></script>
        <button type="button" class="btn btn-sm text-white btn-warning" onClick="makePayment()">
            <i class="fas fa-money-bill-alt text-white"></i>
            {{$v}}
        </button>
    </form>
</div>
<script>
    function makePayment() {
        FlutterwaveCheckout({
            public_key: "{{env('FLUTTERWAVE_PUBLIC_KEY')}}",
            tx_ref: "{{str_random(15)}}", //generate randomly
            amount: {{$package->price}},
            currency: "{{$currency_code}}",
            payment_options: "card, mobilemoneyghana, ussd",
            redirect_url: "{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@postFlutterwavePaymentCallback')}}",// specified redirect URL
            meta: {
                package_id: "{{$package->id}}",
                gateway: "{{$v}}",
                business_id: "{{$user['business_id']}}",
                user_id: "{{$user['id']}}"
            },
            customer: {
                email: "{{$user['email']}}"
            },
            customizations: {
                title: "{{Session::get('business.name')}}",
                logo: "{{asset('uploads/business_logos/'.Session::get('business.logo'))}}",
            },
        });
    }
</script>