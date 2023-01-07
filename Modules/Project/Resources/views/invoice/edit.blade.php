@extends('layouts.app')
@section('title', __('project::lang.invoices'))

@section('content')
<section class="content">
	<h1>
		<i class="fa fa-file"></i>
    	@lang('project::lang.invoice')
    	<small>@lang('messages.edit')</small>
    </h1>
    <!-- form open -->
    {!! Form::open(['url' => action('\Modules\Project\Http\Controllers\InvoiceController@update', $transaction->id), 'id' => 'invoice_form', 'method' => 'put']) !!}
		<div class="box box-primary">
			<div class="box-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							{!! Form::label('pjt_title', __('project::lang.title') . ':*' )!!}
	                        {!! Form::text('pjt_title', $transaction->pjt_title, ['class' => 'form-control', 'required' ]) !!}
						</div>
					</div>
					<!-- project_id -->
					{!! Form::hidden('pjt_project_id', $transaction->pjt_project_id, ['class' => 'form-control']) !!}
					<div class="col-md-4">
						<div class="form-group">
							{!! Form::label('contact_id', __('role.customer') . ':*' )!!}
	                        {!! Form::select('contact_id', $customers, $transaction->contact_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							{!! Form::label('location_id', __('business.business_location') . ':*' )!!}
	                        {!! Form::select('location_id', $business_locations, $transaction->location_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							{!! Form::label('transaction_date', __('project::lang.invoice_date') . ':*' )!!}
	                        {!! Form::text('transaction_date', @format_date($transaction->transaction_date), ['class' => 'form-control date-picker','required', 'readonly']); !!}
						</div>
					</div>
					<div class="col-md-4">
	                    <div class="form-group">
	                       <div class="multi-input">
				              {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
				              <br/>
				              {!! Form::number('pay_term_number', $transaction->pay_term_number, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('contact.pay_term')]); !!}
				              {!! Form::select('pay_term_type', 
				              	['months' => __('lang_v1.months'), 
				              		'days' => __('lang_v1.days')], 
				              		$transaction->pay_term_type, 
				              	['class' => 'form-control width-60 pull-left','placeholder' => __('messages.please_select')]); !!}
				            </div>
	                    </div>
	                </div>
	                <div class="col-md-4">
						<div class="form-group">
							{!! Form::label('status', __('sale.status') . ':*' )!!}
	                        {!! Form::select('status', $statuses, $transaction->status, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
						</div>
					</div>
				</div>
			</div>
		</div> <!-- /box -->
		<div class="box box-primary">
			<div class="box-body">
				<div class="col-md-12">
					<div class="col-md-3">
						<label>@lang('project::lang.task'):*</label>
					</div>
					<div class="col-md-2">
						<label>@lang('project::lang.rate'):*</label>
					</div>
					<div class="col-md-2">
						<label>@lang('project::lang.qty'):*</label>
					</div>
					<div class="col-md-2">
						<label>@lang('business.tax')(%):*</label>
					</div>
					<div class="col-md-2">
						<label>@lang('receipt.total'):*</label>
					</div>
					<div class="col-md-1">
					</div>
				</div>
				<div class="invoice_lines">
					@foreach($transaction->invoiceLines as $key => $invoiceLine)
						<div class="col-md-12 il-bg invoice_line">
							<div class="mt-10">
							<!-- invoice line id -->
							{!! Form::hidden('invoice_line_id[]', $invoiceLine->id, ['class' => 'form-control']) !!}
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										{!! Form::text('existing_task[]', $invoiceLine->task, ['class' => 'form-control', 'required' ]) !!}
										<span class="input-group-btn">
									        <button class="btn btn-default toggle_description" type="button">
												<i class="fa fa-info-circle text-info" data-toggle="tooltip" title="@lang('project::lang.toggle_invoice_task_description')"></i>
									        </button>
									    </span>
									</div>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									{!! Form::text('existing_rate[]', @num_format($invoiceLine->rate), ['class' => 'form-control rate input_number', 'required' ]) !!}
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									{!! Form::text('existing_quantity[]', @format_quantity($invoiceLine->quantity), ['class' => 'form-control quantity input_number', 'required' ]) !!}
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									{!! Form::select('existing_tax_rate_id[]', $taxes, $invoiceLine->tax_rate_id, ['class' => 'form-control tax'], $tax_attributes); !!}

								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									{!! Form::text('existing_total[]', @num_format($invoiceLine->total), ['class' => 'form-control total input_number', 'required', 'readonly']) !!}
								</div>
							</div>
							@if($key != 0)
								<div class="col-md-1">
									<i class="fa fa-trash text-danger cursor-pointer remove_this_row"></i>
								</div>
							@endif
							<div class="col-md-11">
								<div class="form-group description" style="
									@if(!isset($invoiceLine->description))display: none;
									@endif">
									{!! Form::textarea('existing_description[]', $invoiceLine->description, ['class' => 'form-control ', 'placeholder' => __('lang_v1.description'), 'rows' => '3']); !!}
								</div>
							</div>
							</div>
						</div>
					@endforeach
				</div>
				<div class="col-md-4 col-md-offset-4">
					<br>
					<button type="button" class="btn btn-block btn-primary btn-sm add_invoice_line">
						@lang('project::lang.add_a_row')
						<i class="fa fa-plus-circle"></i>
					</button>
				</div>
			</div>
			<!-- including invoice line row -->
			@includeIf('project::invoice.partials.invoice_line_row')
		</div>  <!-- /box -->
		<div class="box box-primary">
			<div class="box-body">
				<div class="row">
					<div class="col-md-6 col-md-offset-10">
						<b>@lang('sale.subtotal'):</b>
						<span class="subtotal display_currency" data-currency_symbol="true" >0.00</span>
						<input type="hidden" name="total_before_tax" id="subtotal" value="0.00">
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						{!! Form::label('discount_type', __('sale.discount_type') . ':' )!!}
	                    {!! Form::select('discount_type', $discount_types, $transaction->discount_type, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
					</div>
					<div class="col-md-6">
						{!! Form::label('discount_amount', __('sale.discount_amount') . ':' )!!}
	                    {!! Form::text('discount_amount', @num_format($transaction->discount_amount), ['class' => 'form-control input_number']) !!}
					</div>
				</div> <br>

				<div class="row">
					<div class="col-md-6 col-md-offset-6">
						<b>@lang('project::lang.invoice_total'):</b>
						<span class="invoice_total display_currency" data-currency_symbol="true" >0.00</span>
						<input type="hidden" name="final_total" id="invoice_total" value="0.00">
					</div>
				</div> <br>

				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
	                        {!! Form::label('staff_note', __('project::lang.terms') . ':') !!}
	                        {!! Form::textarea('staff_note', $transaction->staff_note, ['class' => 'form-control ', 'rows' => '3']); !!}
	                    </div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
	                        {!! Form::label('additional_notes', __('project::lang.notes') . ':') !!}
	                        {!! Form::textarea('additional_notes', $transaction->additional_notes, ['class' => 'form-control ', 'rows' => '3']); !!}
	                    </div>
					</div>
				</div>
				<button type="submit" class="btn btn-primary pull-right">
	                @lang('messages.update')
	            </button>
			</div>
		</div> <!-- /box -->
	{!! Form::close() !!} <!-- /form close -->
</section>
<link rel="stylesheet" href="{{ asset('modules/project/sass/project.css?v=' . $asset_v) }}">
@endsection
@section('javascript')
<script src="{{ asset('modules/project/js/project.js?v=' . $asset_v) }}"></script>
<!-- call a function to calculate subtotal once edit page is loaded -->
<script type="text/javascript">
	$(document).ready(function() {
		calculateSubtotal();
	});
</script>
@endsection