<table class="table table-condensed" id="customer_invoice_table">
	<tr>
		<th>@lang('contact.customer')</th>
		<th>@lang('lang_v1.invoices')</th>
		<th>@lang('crm::lang.assgined')</th>
		<th  class="text-center"><i class="fas fa-times text-danger"></i></th>
	</tr>
	@foreach($sells_by_customer as $key => $value)
	<tr>
		<td>
			@php
				$contact = $value[0]->contact;
			@endphp
			@if(!empty($contact->supplier_business_name))
				{{$contact->supplier_business_name}}<br>
			@endif
			{{$contact->name}}
		</td>
		<td>
			@foreach($value as $sell)
				{{$sell->invoice_no}} @if(!$loop->last),@endif
				<input type="hidden" name="follow_ups[{{$key}}][invoices][]" value="{{$sell->id}}">
			@endforeach
		</td>
		<td>
			<div class="form-group">
                {!! Form::select('follow_ups[' . $key . '][user_id][]', $users, $contact->created_by, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
            </div>
		</td>
		<td class="text-center">
			<i class="fas fa-times text-danger remove-follow-up" style="cursor: pointer;"></i>
		</td>
	</tr>
	@endforeach
</table>