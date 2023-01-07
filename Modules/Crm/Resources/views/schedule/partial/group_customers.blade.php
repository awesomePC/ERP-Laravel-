<table class="table table-condensed" id="customer_invoice_table">
	<tr>
		<th>@lang('contact.customer')</th>
		<th>@lang('crm::lang.assgined')</th>
		<th  class="text-center"><i class="fas fa-times text-danger"></i></th>
	</tr>
	@foreach($customers as $customer)
	<tr>
		<td>
			@if(!empty($customer->supplier_business_name))
				{{$customer->supplier_business_name}}<br>
			@endif
			{{$customer->name}}
		</td>
		<td>
			<div class="form-group">
                {!! Form::select('follow_ups[' . $customer->id . '][user_id][]', $users, $customer->created_by, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
            </div>
		</td>
		<td class="text-center">
			<i class="fas fa-times text-danger remove-follow-up" style="cursor: pointer;"></i>
		</td>
	</tr>
	@endforeach
</table>