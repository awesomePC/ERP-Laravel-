@extends('layouts.app')

@section('title', __('repair::lang.edit_repair'))
@section('content')
<style type="text/css">
	.krajee-default.file-preview-frame .kv-file-content {
		height: 65px;
	}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('repair::lang.edit_repair') <small>(@lang('repair::lang.repair_no'): <span class="text-success">#{{$transaction->invoice_no}})</span></small></h1>
</section>
<!-- Main content -->
<section class="content">
<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
@if(session('business.enable_rp') == 1)
    <input type="hidden" id="reward_point_enabled">
@endif
	{!! Form::open(['url' => action('SellPosController@update', ['id' => $transaction->id ]), 'method' => 'put', 'id' => 'edit_sell_form', 'files' => true ]) !!}

	{!! Form::hidden('location_id', $transaction->location_id, ['id' => 'location_id', 'data-receipt_printer_type' => !empty($location_printer_type) ? $location_printer_type : 'browser']); !!}
	{!! Form::hidden('has_module_data', true); !!}
	{!! Form::hidden('status', 'final'); !!}
	{!! Form::hidden('sub_type', 'repair'); !!}
	<div class="row">
		<div class="col-md-12 col-sm-12">
			@component('components.widget')
				<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-4 @endif">
					<div class="form-group">
						{!! Form::label('contact_id', __('contact.customer') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-user"></i>
							</span>
							<input type="hidden" id="default_customer_id" 
							value="{{ $transaction->contact->id }}" >
							<input type="hidden" id="default_customer_name" 
							value="{{ $transaction->contact->name }}" >
							{!! Form::select('contact_id', 
								[], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder' => 'Enter Customer name / phone', 'required']); !!}
							<span class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
						</div>
					</div>
				</div>
				<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-4 @endif">
					<div class="form-group">
						{!! Form::label('transaction_date', __('repair::lang.repair_added_on') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', $transaction->transaction_date, ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('repair_completed_on', __('repair::lang.repair_completed_on') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('repair_completed_on', $transaction->repair_completed_on, ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label for="repair_status_id">{{__('repair::lang.repair_status') . ':*'}}</label>
						<select name="repair_status_id" class="form-control" id="repair_status_id"></select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('repair_brand_id', __('repair::lang.manufacturer') . ':') !!}
						{!! Form::select('repair_brand_id', $brands, $transaction->repair_brand_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('repair_model', __('repair::lang.model') . ':') !!}
						{!! Form::text('repair_model', $transaction->repair_model, ['class' => 'form-control', 'placeholder' => __('repair::lang.model')]); !!}
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('repair_serial_no', __('repair::lang.serial_no') . ':') !!}
						{!! Form::text('repair_serial_no', $transaction->repair_serial_no, ['class' => 'form-control', 'placeholder' => __('repair::lang.serial_no')]); !!}
					</div>
				</div>
				@if(in_array('service_staff' ,$enabled_modules))
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('res_waiter_id', __('repair::lang.assign_repair_to') . ':') !!}
						{!! Form::select('res_waiter_id', $waiters, $transaction->res_waiter_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
					</div>
				</div>
				@endif
				<div class="col-sm-4">
					<br>
					<div class="checkbox">
						<label>
						{!! Form::checkbox('repair_updates_email', 1, !empty($transaction->repair_updates_email), ['class' => 'input-icheck']); !!} @lang('repair::lang.auto_send_notification') (Email)
						</label> @show_tooltip(__('repair::lang.auto_send_email_tooltip'))
					</div>
				</div>
				<div class="col-sm-4">
					<br>
					<div class="checkbox">
						<label>
						{!! Form::checkbox('repair_updates_sms', 1, !empty($transaction->repair_updates_sms), ['class' => 'input-icheck']); !!} @lang('repair::lang.auto_send_notification') (SMS)
						</label> @show_tooltip(__('repair::lang.auto_send_sms_tooltip'))
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-2">
					<div class="form-group">
					<br>
						<button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#checklist_modal"><i class="fa fa-plus"></i> @lang('repair::lang.pre_repair_checklist')</button>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<br>
						<button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#security_modal"><i class="fa fa-lock"></i> @lang('repair::lang.security')</button>
					</div>
				</div>
				<div class="clearfix"></div>
			    <div class="col-md-12">
			        <div class="form-group">
			            {!! Form::label('documents', __('lang_v1.upload_documents') . ':' ) !!}
			            {!! Form::file('documents[]', ['multiple', 'id' => 'documents']); !!}
			        </div>
			    </div>
			    @php
			    	$selected_checklist = !empty($transaction->repair_checklist) ? json_decode($transaction->repair_checklist, true) : [];

			    @endphp
				@include('repair::repair.partials.security_modal')
				@include('repair::repair.partials.checklist_modal')
			@endcomponent

			@component('components.widget')
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('repair_defects',__('repair::lang.defect') . ':') !!}
						{!! Form::textarea('repair_defects', $transaction->repair_defects, ['class' => 'form-control', 'rows' => 3]); !!}
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('staff_note',__('repair::lang.noted_problems_n_technician_comments'). ':') !!}
						{!! Form::textarea('staff_note', $transaction->staff_note, ['class' => 'form-control', 'rows' => 3]); !!}
					</div>
				</div>
			@endcomponent
			
			@component('components.widget')
				<div class="col-sm-10 col-sm-offset-1">
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-barcode"></i>
							</span>
							{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
							'autofocus' => true,
							]); !!}
						</div>
					</div>
				</div>

				<div class="row col-sm-12 pos_product_div" style="min-height: 0">

					<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

					<!-- Keeps count of product rows -->
					<input type="hidden" id="product_row_count" 
						value="{{count($sell_details)}}">
					@php
						$hide_tax = '';
						if( session()->get('business.enable_inline_tax') == 0){
							$hide_tax = 'hide';
						}
					@endphp
					<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
						<thead>
							<tr>
								<th class="text-center">	
									@lang('sale.product')
								</th>
								<th class="text-center">
									@lang('sale.qty')
								</th>
								@if(!empty($pos_settings['inline_service_staff']) && in_array('service_staff' ,$enabled_modules))
									<th class="text-center col-md-2">
										@lang('restaurant.service_staff')
									</th>
								@endif
								<th class="text-center {{$hide_tax}}">
									@lang('sale.price_inc_tax')
								</th>
								<th class="text-center">
									@lang('sale.subtotal')
								</th>
								<th class="text-center"><i class="fa fa-close" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody>
							@foreach($sell_details as $sell_line)
								@include('sale_pos.product_row', ['product' => $sell_line, 'row_count' => $loop->index, 'tax_dropdown' => $taxes, 'sub_units' => !empty($sell_line->unit_details) ? $sell_line->unit_details : [] ])
							@endforeach
						</tbody>
					</table>
					</div>
					<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped table-responsive">
						<tr>
							<td>
								<div class="pull-right"><b>@lang('sale.total'): </b>
									<span class="price_total">0</span>
								</div>
							</td>
						</tr>
					</table>
					</div>
				</div>
			@endcomponent

			@component('components.widget')
				<div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('discount_type', __('sale.discount_type') . ':*' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::select('discount_type', ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], $transaction->discount_type , ['class' => 'form-control','placeholder' => __('messages.please_select'), 'required', 'data-default' => 'percentage']); !!}
			            </div>
			        </div>
			    </div>
			    <div class="col-md-4">
			        <div class="form-group">
			            {!! Form::label('discount_amount', __('sale.discount_amount') . ':*' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::text('discount_amount', @num_format($transaction->discount_amount), ['class' => 'form-control input_number', 'data-default' => $business_details->default_sales_discount]); !!}
			            </div>
			        </div>
			    </div>
			    <div class="col-md-4"><br>
			    	<b>@lang( 'sale.discount_amount' ):</b>(-) 
					<span class="display_currency" id="total_discount">0</span>
			    </div>
			    <div class="clearfix"></div>
			    <div class="col-md-12 well well-sm bg-light-gray @if(session('business.enable_rp') != 1) hide @endif">
			    	<input type="hidden" name="rp_redeemed" id="rp_redeemed" value="{{$transaction->rp_redeemed}}">
			    	<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount" value="{{$transaction->rp_redeemed_amount}}">
			    	<div class="col-md-12"><h4>{{session('business.rp_name')}}</h4></div>
			    	<div class="col-md-4">
				        <div class="form-group">
				            {!! Form::label('rp_redeemed_modal', __('lang_v1.redeemed') . ':' ) !!}
				            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa fa-gift"></i>
				                </span>
				                {!! Form::number('rp_redeemed_modal', $transaction->rp_redeemed, ['class' => 'form-control direct_sell_rp_input', 'data-amount_per_unit_point' => session('business.redeem_amount_per_unit_rp'), 'min' => 0, 'data-max_points' => !empty($redeem_details['points']) ? $redeem_details['points'] : 0, 'data-min_order_total' => session('business.min_order_total_for_redeem') ]); !!}
				                <input type="hidden" id="rp_name" value="{{session('business.rp_name')}}">
				            </div>
				        </div>
				    </div>
				    <div class="col-md-4">
				    	<p><strong>@lang('lang_v1.available'):</strong> <span id="available_rp">{{$redeem_details['points'] ?? 0}}</span></p>
				    </div>
				    <div class="col-md-4">
				    	<p><strong>@lang('lang_v1.redeemed_amount'):</strong> (-)<span id="rp_redeemed_amount_text">{{@num_format($transaction->rp_redeemed_amount)}}</span></p>
				    </div>
			    </div>
			    <div class="col-md-4">
			    	<div class="form-group">
			            {!! Form::label('tax_rate_id', __('sale.order_tax') . ':*' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::select('tax_rate_id', $taxes['tax_rates'], $transaction->tax_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control', 'data-default'=> $business_details->default_sales_tax], $taxes['attributes']); !!}

							<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
							value="{{@num_format(optional($transaction->tax)->amount)}}" data-default="{{$business_details->tax_calculation_amount}}">
			            </div>
			        </div>
			    </div>
			    <div class="col-md-4 col-md-offset-4">
			    	<b>@lang( 'sale.order_tax' ):</b>(+) 
					<span class="display_currency" id="order_tax">{{$transaction->tax_amount}}</span>
			    </div>
			    <div class="clearfix"></div>
				<div class="col-md-4">
					<div class="form-group">
			            {!! Form::label('shipping_details', __('sale.shipping_details')) !!}
			            <div class="input-group">
							<span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::textarea('shipping_details',$transaction->shipping_details, ['class' => 'form-control','placeholder' => __('sale.shipping_details') ,'rows' => '1', 'cols'=>'30']); !!}
			            </div>
			        </div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						{!!Form::label('shipping_charges', __('sale.shipping_charges'))!!}
						<div class="input-group">
						<span class="input-group-addon">
						<i class="fa fa-info"></i>
						</span>
						{!!Form::text('shipping_charges',@num_format($transaction->shipping_charges),['class'=>'form-control input_number','placeholder'=> __('sale.shipping_charges')]);!!}
						</div>
					</div>
				</div>
			    <div class="col-md-4 col-md-offset-8">
			    	<div><b>@lang('sale.total_payable'): </b>
						<input type="hidden" name="final_total" id="final_total_input">
						<span id="total_payable">0</span>
					</div>
			    </div>
			    <div class="col-md-12">
			    	<div class="form-group">
						{!! Form::label('update_note',__('repair::lang.update_note') . ':') !!}
						{!! Form::textarea('update_note', null, ['class' => 'form-control', 'rows' => 3]); !!}
					</div>
			    </div>
			    <input type="hidden" name="is_direct_sale" value="1">
			    <div class="col-md-12">
			    	<button type="button" class="btn btn-primary pull-right" id="submit-sell">@lang('messages.update')</button>
			    </div>
			@endcomponent

		</div>
	</div>
	@if(in_array('subscription', $enabled_modules))
		@include('sale_pos.partials.recurring_invoice_modal')
	@endif
	{!! Form::close() !!}
</section>

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>

@stop

@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
	@include('repair::layouts.partials.javascripts')
	<script type="text/javascript">
		$(document).ready( function() {
			// lock.setPattern('{{$transaction->repair_security_pattern}}');

			@include('repair::repair.partials.repair_status')
			
			$("select#repair_status_id").val({{$transaction->repair_status_id}}).change();
		});
	</script>
@endsection