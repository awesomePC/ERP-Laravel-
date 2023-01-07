@if(!empty($employees))
	@foreach($employees as $key => $value)
		<option value="{{$key}}">{{$value}}</option>
	@endforeach
@endif