@extends('crm::layouts.app')

@php
	$title = __('crm::lang.add_order_request');
@endphp

@section('title', $title)

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{$title}}</h1>
</section>
<!-- Main content -->
<section class="content no-print">
<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? ''}}">
@if(count($business_locations) > 0)
<div class="row">
	<div class="col-sm-3">
		<div class="form-group">
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-map-marker"></i>
				</span>
			{!! Form::select('select_location_id', $business_locations, $default_location->id ?? null, ['class' => 'form-control input-sm',
			'id' => 'select_location_id', 
			'required', 'autofocus'], $bl_attributes); !!}
			<span class="input-group-addon">
					@show_tooltip(__('tooltip.sale_location'))
				</span> 
			</div>
		</div>
	</div>
</div>
@endif

@php
	$custom_labels = json_decode(session('business.custom_labels'), true);
	$common_settings = session()->get('business.common_settings');
@endphp
<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
	{!! Form::open(['url' => action('\Modules\Crm\Http\Controllers\OrderRequestController@store'), 'method' => 'post', 'id' => 'add_sell_form', 'files' => true ]) !!}
	<input type="hidden" id="customer_id" name="contact_id" value="{{$contact->id}}">
	 <input type="hidden" id="sale_type" name="type" value="crm_order_request">
	<div class="row">
		<div class="col-md-12 col-sm-12">
			{!! Form::hidden('location_id', !empty($default_location) ? $default_location->id : null , ['id' => 'location_id', 'data-receipt_printer_type' => !empty($default_location->receipt_printer_type) ? $default_location->receipt_printer_type : 'browser', 'data-default_payment_accounts' => !empty($default_location) ? $default_location->default_payment_accounts : '']); !!}

			@component('components.widget', ['class' => 'box-solid'])
				<div class="col-sm-10 col-sm-offset-1">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fas fa-search-plus"></i></button>
							</div>
							{!! Form::text('or_search_product', null, ['class' => 'form-control mousetrap', 'id' => 'or_search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
							'disabled' => is_null($default_location)? true : false,
							'autofocus' => is_null($default_location)? false : true,
							]); !!}
						</div>
					</div>
				</div>

				<div class="row col-sm-12 pos_product_div" style="min-height: 0">

					<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

					<!-- Keeps count of product rows -->
					<input type="hidden" id="product_row_count" 
						value="0">
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
								<th class="text-center">
									@lang('sale.price_inc_tax')
								</th>
								<th class="text-center">
									@lang('sale.subtotal')
								</th>
								<th class="text-center"><i class="fas fa-times" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					</div>
					<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped">
						<tr>
							<td>
								<div class="pull-right">
								<b>@lang('sale.item'):</b> 
								<span class="total_quantity">0</span>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<b>@lang('sale.total'): </b>
									<span class="price_total">0</span>
								</div>
							</td>
						</tr>
					</table>
					</div>
				</div>
			@endcomponent
			@component('components.widget', ['class' => 'box-solid'])
			    <div class="col-md-6">
			    	<div class="form-group">
						{!! Form::label('sell_note',__('purchase.additional_notes')) !!}
						{!! Form::textarea('sale_note', null, ['class' => 'form-control', 'rows' => 3]); !!}
					</div>
			    </div>
			    <div class="col-md-6">
					<div class="form-group">
			            {!! Form::label('shipping_address', __('lang_v1.shipping_address')) !!}
			            {!! Form::textarea('shipping_address',null, ['class' => 'form-control','placeholder' => __('lang_v1.shipping_address') ,'rows' => '3', 'cols'=>'30']); !!}
			        </div>
				</div>
				<div class="col-md-4 col-md-offset-8">
			    	@if(!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
			    	<small id="round_off"><br>(@lang('lang_v1.round_off'): <span id="round_off_text">0</span>)</small>
					<br/>
					<input type="hidden" name="round_off_amount" 
						id="round_off_amount" value=0>
					@endif
			    	<div><b>@lang('sale.total_payable'): </b>
						<input type="hidden" name="final_total" id="final_total_input">
						<span id="total_payable">0</span>
					</div>
			    </div>
				<input type="hidden" name="is_direct_sale" value="1">
			@endcomponent
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-12 text-center">
			<button type="button" id="submit-sell" class="btn btn-primary btn-big">@lang('messages.save')</button>
		</div>
	</div>
	
	{!! Form::close() !!}
</section>

@include('sale_pos.partials.configure_search_modal')

@stop

@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
    	$(document).ready( function() {

    		$('#or_search_product')
	            .autocomplete({
	                delay: 1000,
	                source: function(request, response) {
	                    var price_group = '';
	                    var search_fields = [];
	                    $('.search_fields:checked').each(function(i){
	                      search_fields[i] = $(this).val();
	                    });

	                    if ($('#price_group').length > 0) {
	                        price_group = $('#price_group').val();
	                    }
	                    $.getJSON(
	                        '/contact/products/list',
	                        {
	                            price_group: price_group,
	                            location_id: $('input#location_id').val(),
	                            term: request.term,
	                            not_for_selling: 0,
	                            search_fields: search_fields
	                        },
	                        response
	                    );
	                },
	                minLength: 2,
	                response: function(event, ui) {
	                    if (ui.content.length == 1) {
	                        ui.item = ui.content[0];
	                        if ((ui.item.enable_stock == 1 && ui.item.qty_available > 0) || 
	                                (ui.item.enable_stock == 0)) {
	                            $(this)
	                                .data('ui-autocomplete')
	                                ._trigger('select', 'autocompleteselect', ui);
	                            $(this).autocomplete('close');
	                        }
	                    } else if (ui.content.length == 0) {
	                        toastr.error(LANG.no_products_found);
	                        $('input#search_product').select();
	                    }
	                },
	                focus: function(event, ui) {
	                    if (ui.item.qty_available <= 0) {
	                        return false;
	                    }
	                },
	                select: function(event, ui) {
	                    var searched_term = $(this).val();
	                    var is_overselling_allowed = false;
	                    if($('input#is_overselling_allowed').length) {
	                        is_overselling_allowed = true;
	                    }

	                    if (ui.item.enable_stock != 1 || ui.item.qty_available > 0 || is_overselling_allowed) {
	                        $(this).val(null);

	                        //Pre select lot number only if the searched term is same as the lot number
	                        var purchase_line_id = ui.item.purchase_line_id && searched_term == ui.item.lot_number ? ui.item.purchase_line_id : null;
	                        or_product_row(ui.item.variation_id, purchase_line_id);
	                    } else {
	                        alert(LANG.out_of_stock);
	                    }
	                },
	            })
	            .autocomplete('instance')._renderItem = function(ul, item) {
	                var is_overselling_allowed = false;
	                if($('input#is_overselling_allowed').length) {
	                    is_overselling_allowed = true;
	                }
	            if (item.enable_stock == 1 && item.qty_available <= 0 && !is_overselling_allowed) {
	                var string = '<li class="ui-state-disabled">' + item.name;
	                if (item.type == 'variable') {
	                    string += '-' + item.variation;
	                }
	                var selling_price = item.selling_price;
	                if (item.variation_group_price) {
	                    selling_price = item.variation_group_price;
	                }
	                string +=
	                    ' (' +
	                    item.sub_sku +
	                    ')' +
	                    '<br> Price: ' +
	                    selling_price +
	                    ' (Out of stock) </li>';
	                return $(string).appendTo(ul);
	            } else {
	                var string = '<div>' + item.name;
	                if (item.type == 'variable') {
	                    string += '-' + item.variation;
	                }

	                var selling_price = item.selling_price;
	                if (item.variation_group_price) {
	                    selling_price = item.variation_group_price;
	                }

	                string += ' (' + item.sub_sku + ')' + '<br> Price: ' + selling_price;
	                string += '</div>';

	                return $('<li>')
	                    .append(string)
	                    .appendTo(ul);
	            }
	        };

	        //variation_id is null when weighing_scale_barcode is used.
function or_product_row(variation_id = null, purchase_line_id = null, weighing_scale_barcode = null, quantity = 1) {

    //Get item addition method
    var item_addtn_method = 0;
    var add_via_ajax = true;

    if (variation_id != null && $('#item_addition_method').length) {
        item_addtn_method = $('#item_addition_method').val();
    }

    if (item_addtn_method == 0) {
        add_via_ajax = true;
    } else {
        var is_added = false;

        //Search for variation id in each row of pos table
        $('#pos_table tbody')
            .find('tr')
            .each(function() {
                var row_v_id = $(this)
                    .find('.row_variation_id')
                    .val();
                var enable_sr_no = $(this)
                    .find('.enable_sr_no')
                    .val();
                var modifiers_exist = false;
                if ($(this).find('input.modifiers_exist').length > 0) {
                    modifiers_exist = true;
                }

                if (
                    row_v_id == variation_id &&
                    enable_sr_no !== '1' &&
                    !modifiers_exist &&
                    !is_added
                ) {
                    add_via_ajax = false;
                    is_added = true;

                    //Increment product quantity
                    qty_element = $(this).find('.pos_quantity');
                    var qty = __read_number(qty_element);
                    __write_number(qty_element, qty + 1);
                    qty_element.change();

                    round_row_to_iraqi_dinnar($(this));

                    $('input#search_product')
                        .focus()
                        .select();
                }
        });
    }

    if (add_via_ajax) {
        var product_row = $('input#product_row_count').val();
        var location_id = $('input#location_id').val();
        var customer_id = $('select#customer_id').val();
        var is_direct_sell = false;
        if (
            $('input[name="is_direct_sale"]').length > 0 &&
            $('input[name="is_direct_sale"]').val() == 1
        ) {
            is_direct_sell = true;
        }

        var is_sales_order = $('#sale_type').length && $('#sale_type').val() == 'sales_order' ? true : false;

        var price_group = '';
        if ($('#price_group').length > 0) {
            price_group = parseInt($('#price_group').val());
        }

        //If default price group present
        if ($('#default_price_group').length > 0 && 
            price_group === '') {
            price_group = $('#default_price_group').val();
        }

        //If types of service selected give more priority
        if ($('#types_of_service_price_group').length > 0 && 
            $('#types_of_service_price_group').val()) {
            price_group = $('#types_of_service_price_group').val();
        }
        
        $.ajax({
            method: 'GET',
            url: '/contact/order-request/get_product_row/' + variation_id + '/' + location_id,
            async: false,
            data: {
                product_row: product_row,
                customer_id: customer_id,
                is_direct_sell: true,
                quantity: quantity,
            },
            dataType: 'json',
            success: function(result) {
                if (result.success) {
                    $('table#pos_table tbody')
                        .append(result.html_content)
                        .find('input.pos_quantity');
                    //increment row count
                    $('input#product_row_count').val(parseInt(product_row) + 1);
                    var this_row = $('table#pos_table tbody')
                        .find('tr')
                        .last();
                    pos_each_row(this_row);

                    //For initial discount if present
                    var line_total = __read_number(this_row.find('input.pos_line_total'));
                    this_row.find('span.pos_line_total_text').text(line_total);

                    pos_total_row();

                    //Check if multipler is present then multiply it when a new row is added.
                    if(__getUnitMultiplier(this_row) > 1){
                        this_row.find('select.sub_unit').trigger('change');
                    }

                    if (result.enable_sr_no == '1') {
                        var new_row = $('table#pos_table tbody')
                            .find('tr')
                            .last();
                        new_row.find('.row_edit_product_price_model').modal('show');
                    }

                    round_row_to_iraqi_dinnar(this_row);
                    __currency_convert_recursively(this_row);

                    $('input#search_product')
                        .focus()
                        .select();

                    //Used in restaurant module
                    if (result.html_modifier) {
                        $('table#pos_table tbody')
                            .find('tr')
                            .last()
                            .find('td:first')
                            .append(result.html_modifier);
                    }

                    //scroll bottom of items list
                    $(".pos_product_div").animate({ scrollTop: $('.pos_product_div').prop("scrollHeight")}, 1000);
                } else {
                    toastr.error(result.msg);
                    $('input#search_product')
                        .focus()
                        .select();
                }
            },
        });
    }
}

            $('#shipping_documents').fileinput({
		        showUpload: false,
		        showPreview: false,
		        browseLabel: LANG.file_browse_label,
		        removeLabel: LANG.remove,
		    });
    	});
    </script>
@endsection
