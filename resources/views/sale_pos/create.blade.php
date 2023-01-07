@extends('layouts.app')

@section('title', __('sale.pos_sale'))

@section('content')
{{-- total payable amount --}}
<style>
	.ms-payableAmount {
		z-index: 1;
		position: fixed;
		display: block;
		background: #001f3f;
		color: #FFFFFF;
		top: 90%;
		right: 0;
		padding: 8px 15px;
		border-radius: 5px;
		font-weight: bold;
	}
	#total_payable {
		font-size: 25px;
	}

	.col-sm-12.pos_product_div {
		min-height: 60vh !important;
	}
</style>

<section class="content no-print">
	<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? ''}}">
	@if(!empty($pos_settings['allow_overselling']))
		<input type="hidden" id="is_overselling_allowed">
	@endif
	@if(session('business.enable_rp') == 1)
        <input type="hidden" id="reward_point_enabled">
    @endif
    @php
		$is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
		$is_rp_enabled = session('business.enable_rp') == 1 ? true : false;
	@endphp
	{!! Form::open(['url' => action('SellPosController@store'), 'method' => 'post', 'id' => 'add_pos_sell_form' ]) !!}
	<div class="row mb-12">
		<div class="col-md-12">
			<div class="row">
				<div class="@if(empty($pos_settings['hide_product_suggestion'])) col-md-7 no-padding pr-12 @else col-md-12 p-12 @endif">
					<div class="box box-solid mb-12 @if(!isMobile()) mb-40 @endif">
						<div class="box-body pb-0">
							{!! Form::hidden('location_id', $default_location->id ?? null , ['id' => 'location_id', 'data-receipt_printer_type' => !empty($default_location->receipt_printer_type) ? $default_location->receipt_printer_type : 'browser', 'data-default_payment_accounts' => $default_location->default_payment_accounts ?? '']); !!}
							<!-- sub_type -->
							{!! Form::hidden('sub_type', isset($sub_type) ? $sub_type : null) !!}
							<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
							@include('sale_pos.partials.pos_form')

							<div class="col-md-12">
								<div class="box box-solid" style="background: rgba(0, 0, 0, 0.1)">
									<div class="box-body text-center" style="padding: 5px 10px;">
										<div class="row">
											<div class="col-lg-3" style="padding-right: 0">
												@php
												$discount_type = 'percentage';
												@endphp
												<!-- New Section Added & Update DISCOUNT -->
										        <div class="form-group">
													<div class="row">
														<div class="col-lg-6">
															{!! Form::label('discount_type_modal', __('sale.discount_type')) !!}
															<div class="input-group">
																<span class="input-group-addon">
																	<i class="fa fa-info"></i>
																</span>
																{!! Form::select('discount_type', ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], 'fixed' , ['class' => 'form-control','placeholder' => __('messages.please_select'), 'required', 'data-default' => 'fixed']); !!}
															</div>
														</div>
														<div class="col-lg-6">
															@php
																$max_discount = !is_null(auth()->user()->max_sales_discount_percent) ? auth()->user()->max_sales_discount_percent : '';
																//if sale discount is more than user max discount change it to max discount
																if($discount_type == 'percentage' && $max_discount != '' && $business_details->default_sales_discount > $max_discount) $business_details->default_sales_discount = $max_discount;
															@endphp
															{!! Form::label('discount_amount_modal', __('sale.discount')) !!}
															<div class="input-group">
																<span class="input-group-addon">
																	<i class="fa fa-info"></i>
																</span>
																{!! Form::text('discount_amount_modal', @num_format($business_details->default_sales_discount), ['class' => 'form-control input_number', 'data-max-discount' => $max_discount, 'data-max-discount-error_msg' => __('lang_v1.max_discount_error_msg', ['discount' => $max_discount != '' ? @num_format($max_discount) : '']) ]); !!}
															</div>
														</div>
													</div>
										            
										        </div>
										    </div>
											<!-- neew -->
											<div class="col-lg-2">
												<div class="form-group">
													<label for="gt_mfs_amount">Card Amount:</label>
													<input class="form-control valid" id="gt_mfs_amount" />
												</div>
											</div>
											<div class="col-lg-2">
												<div class="form-group">
													<label for="gt_mfs_card">Card Ref.:</label>
													<input class="form-control valid" maxlength="19" id="gt_mfs_card" />
												</div>
											</div>
											<div class="col-lg-2">
										        <div class="form-group">
													<label for="cash_received">Cash Received:</label>
													<input class="form-control valid" id="cash_received" />
												</div>
										    </div>
											<div class="col-lg-2">
										        <div class="form-group">
													<label for="cash_to_return">Cash to Return:</label>
													<input class="form-control valid" id="cash_to_return" readonly />
												</div>
										    </div>
											<div class="col">
												<button type="submit" class="btn btn-primary" style="margin-top: 20px;" id="gt-save-print">
													<i class="fa fa-print"></i>
												</button>
											</div>
										</div>

										@include('sale_pos.partials.pos_form_totals')
									</div>
								</div>
							</div>

							@include('sale_pos.partials.payment_modal')

							@if(empty($pos_settings['disable_suspend']))
								@include('sale_pos.partials.suspend_note_modal')
							@endif

							@if(empty($pos_settings['disable_recurring_invoice']))
								@include('sale_pos.partials.recurring_invoice_modal')
							@endif
						</div>
					</div>
				</div>
				@if(empty($pos_settings['hide_product_suggestion']) && !isMobile())
				<div class="col-md-5 no-padding">
					@include('sale_pos.partials.pos_sidebar')
				</div>
				@endif
			</div>
		</div>
	</div>
	@include('sale_pos.partials.pos_form_actions')
	{!! Form::close() !!}
</section>

<div class="ms-payableAmount no-print">
	<span class="text">@lang('sale.total_payable')</span> <br>
	<span id="total_payable" class="number">0</span>
</div>

<!-- This will be printed -->
<section class="invoice print_section" id="receipt_section">
</section>
<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
@if(empty($pos_settings['hide_product_suggestion']) && isMobile())
	@include('sale_pos.partials.mobile_product_suggestions')
@endif
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

<div class="modal fade" id="expense_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

@include('sale_pos.partials.configure_search_modal')

@include('sale_pos.partials.recent_transactions_modal')

@include('sale_pos.partials.weighing_scale_modal')

@stop
@section('css')
	<!-- include module css -->
    @if(!empty($pos_module_data))
        @foreach($pos_module_data as $key => $value)
            @if(!empty($value['module_css_path']))
                @includeIf($value['module_css_path'])
            @endif
        @endforeach
    @endif
@stop
@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
	@include('sale_pos.partials.keyboard_shortcuts')

	<!-- Call restaurant module if defined -->
    @if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
    	<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <!-- include module js -->
    @if(!empty($pos_module_data))
	    @foreach($pos_module_data as $key => $value)
            @if(!empty($value['module_js_path']))
                @includeIf($value['module_js_path'], ['view_data' => $value['view_data']])
            @endif
	    @endforeach
	@endif
@endsection