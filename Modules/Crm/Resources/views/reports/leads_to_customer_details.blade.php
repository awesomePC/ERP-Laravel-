<table class="table table-condensed bg-gray">
	<thead>
		<tr>
			<th>
				@lang('contact.customer')
			</th>
			<th>
				@lang('crm::lang.converted_on')
			</th>
		</tr>
	</thead>
	<tbody>
		@foreach($contacts as $contact)
			<tr>
				<td>
					@if(!empty($contact->supplier_business_name))
						{{$contact->supplier_business_name}}<br>
					@endif
					{{$contact->name}}
				</td>
				<td>{{@format_datetime($contact->converted_on)}}</td>
			</tr>
		@endforeach
	</tbody>
</table>