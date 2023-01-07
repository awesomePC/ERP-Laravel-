<div class="col-md-12">
    <form method="POST" action="{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@getRedirectToPaystack')}}">
        {{ csrf_field() }}
        <!-- customer details -->
        <input type="hidden" name="email" value="{{$user['email']}}">{{-- required --}}

        <!-- order info -->
        <input type="hidden" name="amount" value="{{$package->price*100}}">{{-- required in kobo --}}
        <input type="hidden" name="quantity" value="1">

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

        <input type="hidden" name="currency" value="{{$currency_code}}"> {{--Ghana:GHS, Nigeria:NGN, USD--}}

        <!-- additional info -->
        <input type="hidden" name="metadata" value="{{ json_encode($array = ['package_id' => $package->id, 'gateway' => $v, 'business_id' => $user['business_id'], 'user_id' => $user['id']]) }}" > {{-- For other necessary things you want to add to your payload. it is optional though --}}

        <!-- transaction ref -->
        <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}

        <button class="btn btn-sm text-white" type="submit" style="background: #08A5DB;border-color: #08A5DB;">
            <i class="fas fa-align-left text-white"></i>
            {{$v}}
        </button>
    </form>
</div>