var data = [{
  id: "",
  text: '@lang("messages.please_select")',
  html: '@lang("messages.please_select")',
}, 
@foreach($view_data['repair_statuses'] as $repair_status)
	{
	id: {{$repair_status->id}},
	@if(!empty($repair_status->color))
		text: '<i class="fa fa-circle" aria-hidden="true" style="color: {{$repair_status->color}};"></i> {{$repair_status->name}}',
		title: '{{$repair_status->name}}'
	@else
		text: "{{$repair_status->name}}"
	@endif
	},
@endforeach
];

$("select#repair_status_id").select2({
  data: data,
  escapeMarkup: function(markup) {
    return markup;
  }
});