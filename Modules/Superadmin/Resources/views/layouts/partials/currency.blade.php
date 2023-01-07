<!-- Page level currency setting -->
@if(!empty($__system_currency))
	<input type="hidden" id="p_code" value="{{$__system_currency->code}}">
	<input type="hidden" id="p_symbol" value="{{$__system_currency->symbol}}">
	<input type="hidden" id="p_thousand" value="{{$__system_currency->thousand_separator}}">
	<input type="hidden" id="p_decimal" value="{{$__system_currency->decimal_separator}}">
@endif