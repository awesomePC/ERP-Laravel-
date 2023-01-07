@php
	$start_datetime = null;
	$end_datetime = null;
	if(isset($activity->properties['attributes']['start_datetime'])) {
		$start_datetime = $activity->properties['attributes']['start_datetime'];
	}
	else{
		$start_datetime = $activity->subject->start_datetime;
	}
	

	if(isset($activity->properties['attributes']['end_datetime'])){
		$end_datetime = $activity->properties['attributes']['end_datetime'];
	}
	else{
		$end_datetime = $activity->subject->end_datetime;
	}
@endphp

{{@format_datetime($start_datetime)}}
- 
{{@format_datetime($end_datetime)}}
<code>
({{ \Carbon::parse($start_datetime)->diffForHumans(\Carbon::parse($end_datetime), true)}})
</code>