<option value="">@lang("messages.please_select")</option>
@if(!empty($models))
	@foreach($models as $key => $value)
		<option value="{{$key}}">{{$value}}</option>
	@endforeach
@endif