@php
	$pdf_generation_for = ['Original for Buyer'];
@endphp

@foreach($pdf_generation_for as $pdf_for)
	<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
	<style type="text/css">
		table.tpdf {
		  width: 100% !important;
		  border-collapse: collapse;
		  line-height: 1.1;
		}

		table.tpdf, table.tpdf tr, table.tpdf td, table.tpdf th {
		  border: 1px solid black;
		  padding-left: 10px;
		  padding-top: 6px;
		}
		.box {
			border: 1px solid black;
		}

	</style>
	<div class="width-100">
		<div class="width-100 f-left" align="center">
			<strong class="font-17">@lang('lang_v1.purchase_order')</strong>
		</div>
		{{-- <div class="width-50 f-left" align="right">
			<strong>{{$pdf_for}}</strong>
		</div> --}}
	</div>
	<div class="width-100 box">
		<div class="width-100 mb-10 mt-10" align="center">
		</div>
		<div class="width-40 f-left" style="text-align: center;">
			@if(!empty($logo))
	          <img src="{{$logo}}" alt="Logo" style="width: 85%; height: 60%; margin: auto;padding-left: 30px;">
	        @endif
	        <div style="margin-left: 30px;margin-top: 0px;padding-top: 0px;">
	        	@if(!empty($location_details->custom_field1) && !empty($custom_labels['location']['custom_field_1']))
					{{$custom_labels['location']['custom_field_1']}} : {{$location_details->custom_field1}}
		        @endif
	        	<br>
	        	@if(!empty($purchase->business->tax_number_1))
		          <br>{{$purchase->business->tax_label_1}}: {{$purchase->business->tax_number_1}}
		        @endif

		        @if(!empty($purchase->business->tax_number_2))
		          , {{$purchase->business->tax_label_2}}: {{$purchase->business->tax_number_2}}
		        @endif
	        </div>
		</div>
		<div class="width-60 f-left" align="center" style="color: #22489B;padding-top: 5px;">
			<strong class="font-23">
	    		{!!$purchase->business->name!!}
	    	</strong>
	    	<br>
	    	{{ $purchase->location->name }}
	        @if(!empty($purchase->location->landmark))
	          <br>{{$purchase->location->landmark}}
	        @endif
	        @if(!empty($purchase->location->city) || !empty($purchase->location->state) || !empty($purchase->location->country))
	          {{implode(',', array_filter([$purchase->location->city, $purchase->location->state, $purchase->location->country]))}}
	        @endif
	    	@if(!empty($location_details->mobile) || !empty($location_details->alternate_number))
	    		<br>
	    		@lang('lang_v1.contact_no') : {{!empty($location_details->mobile) ? $location_details->mobile .', ': ''}} {{$location_details->alternate_number}}
	    	@endif
	    	@if(!empty($location_details->website))
	    		<br>
	    		@lang('lang_v1.website'): 
	    		<a href="{!!$location_details->website!!}" target="_blank" style="text-decoration: none;">
					{!!$location_details->website!!}
				</a>
	    	@endif
	    	@if(!empty($location_details->email))
	    		<br>@lang('business.email'): {!!$location_details->email!!}
	    	@endif
	        @if(!empty($location_details->custom_field2) && !empty($custom_labels['location']['custom_field_2']))
	          <br>{{$custom_labels['location']['custom_field_2']}} : {{$location_details->custom_field2}}
	        @endif
	        @if(!empty($location_details->custom_field3) && !empty($custom_labels['location']['custom_field_3']))
	          <br>{{$custom_labels['location']['custom_field_3']}} : {{$location_details->custom_field3}}
	        @endif
	        @if(!empty($location_details->custom_field4) && !empty($custom_labels['location']['custom_field_4']))
	          <br>{{$custom_labels['location']['custom_field_4']}} : {{$location_details->custom_field4}}
	        @endif
		</div>
	</div>
	<table class="tpdf">
		<tr>
			<td class="width-50">
				<strong>@lang('lang_v1.po_no'):</strong> #{{ $purchase->ref_no }} <br>
				<strong>@lang('lang_v1.order_date'):</strong> {{ @format_date($purchase->transaction_date) }}
			</td>
			<td class="width-50">
				{{-- <strong>Due date:</strong> {{ @format_date($purchase->due_date) }}<br> --}}
				@if(!empty($purchase->shipping_custom_field_1))
		          <strong>
		          	{{$custom_labels['shipping']['custom_field_1'] ?? ''}}:
		          </strong>
		          	{{$purchase->shipping_custom_field_1}}
		          <br>
		        @endif


		        <strong>@lang('lang_v1.delivery_date'):</strong>
				@if(!empty($purchase->delivery_date))
					{{@format_date($purchase->delivery_date)}}
				@else
					{{'-'}}
				@endif
			</td>
		</tr>
		<tr>
			<td class="width-50">
				<strong>@lang('purchase.supplier')</strong> <br>
		        @php
		        	$customer_address = [];
		            if (!empty($purchase->contact->supplier_business_name)) {
		                $customer_address[] = $purchase->contact->supplier_business_name;
		            }
		            if (!empty($purchase->contact->address_line_1)) {
		                $customer_address[] = '<br>' . $purchase->contact->address_line_1;
		            }
		            if (!empty($purchase->contact->address_line_2)) {
		                $customer_address[] =  '<br>' . $purchase->contact->address_line_2;
		            }
		            if (!empty($purchase->contact->city)) {
		                $customer_address[] = '<br>' . $purchase->contact->city;
		            }
		            if (!empty($purchase->contact->state)) {
		                $customer_address[] = $purchase->contact->state;
		            }
		            if (!empty($purchase->contact->country)) {
		                $customer_address[] = $purchase->contact->country;
		            }
		            if (!empty($purchase->contact->zip_code)) {
		                $customer_address[] = '<br>' . $purchase->contact->zip_code;
		            }
		            if (!empty($purchase->contact->name)) {
		                $customer_address[] = '<br>' . $purchase->contact->name;
		            }
		            if (!empty($purchase->contact->mobile)) {
		                $customer_address[] = '<br>' .$purchase->contact->mobile;
		            }
		            if (!empty($purchase->contact->landline)) {
		                $customer_address[] = $purchase->contact->landline;
		            }
		        @endphp
		        {!! implode(', ', $customer_address) !!}
		        @if(!empty($purchase->contact->email))
		          <br>@lang('business.email'): {{$purchase->contact->email}}
		        @endif
		        @if(!empty($purchase->contact->tax_number))
		          <br>@lang('contact.tax_no'): {{$purchase->contact->tax_number}}
		        @endif
		        @if(!empty($custom_labels['contact']['custom_field_1']) && !empty($purchase->contact->custom_field1))
		        	<br>{{$custom_labels['contact']['custom_field_1']}} : {{$purchase->contact->custom_field1}}
		        @endif
		        @if(!empty($custom_labels['contact']['custom_field_2']) && !empty($purchase->contact->custom_field2))
		        	<br>{{$custom_labels['contact']['custom_field_2']}} : {{$purchase->contact->custom_field2}}
		        @endif
		        @if(!empty($custom_labels['contact']['custom_field_3']) && !empty($purchase->contact->custom_field3))
		        	<br>{{$custom_labels['contact']['custom_field_3']}} : {{$purchase->contact->custom_field3}}
		        @endif
		        @if(!empty($custom_labels['contact']['custom_field_4']) && !empty($purchase->contact->custom_field4))
		        	<br>{{$custom_labels['contact']['custom_field_4']}} : {{$purchase->contact->custom_field4}}
		        @endif
		        @if(!empty($custom_labels['contact']['custom_field_5']) && !empty($purchase->contact->custom_field5))
		        	<br>{{$custom_labels['contact']['custom_field_5']}} : {{$purchase->contact->custom_field5}}
		        @endif
		        @if(!empty($custom_labels['contact']['custom_field_6']) && !empty($purchase->contact->custom_field6))
		        	<br>{{$custom_labels['contact']['custom_field_6']}} : {{$purchase->contact->custom_field6}}
		        @endif
		        @if(!empty($custom_labels['contact']['custom_field_7']) && !empty($purchase->contact->custom_field7))
		        	<br>{{$custom_labels['contact']['custom_field_7']}} : {{$purchase->contact->custom_field7}}
		        @endif
		        @if(!empty($custom_labels['contact']['custom_field_8']) && !empty($purchase->contact->custom_field8))
		        	<br>{{$custom_labels['contact']['custom_field_8']}} : {{$purchase->contact->custom_field8}}
		        @endif
		        @if(!empty($custom_labels['contact']['custom_field_9']) && !empty($purchase->contact->custom_field9))
		        	<br>{{$custom_labels['contact']['custom_field_9']}} : {{$purchase->contact->custom_field9}}
		        @endif
		        @if(!empty($custom_labels['contact']['custom_field_10']) && !empty($purchase->contact->custom_field10))
		        	<br>{{$custom_labels['contact']['custom_field_10']}} : {{$purchase->contact->custom_field10}}
		        @endif
			</td>
			<td class="width-50">
				<strong>@lang('lang_v1.delivery_at')</strong><br>
				{!! $purchase->location->location_address !!}
		        <br>
		        {{--<strong>@lang('lang_v1.dispatch_from'):</strong>
				@if(!empty($purchase->contact->city))
					{{$purchase->contact->city}}
				@else
					{{'-'}}
				@endif --}}
			</td>
		</tr>
	</table>
	<div class="box">
	<table class="table-pdf td-border">
		@php
			$show_cat_code = !empty($invoice_layout->show_cat_code) && $invoice_layout->show_cat_code == 1 ? true : false;

			$show_brand = !empty($invoice_layout->show_brand) && $invoice_layout->show_brand == 1 ? true : false;

			$show_sku = !empty($invoice_layout->show_sku) && $invoice_layout->show_sku == 1 ? true : false;
		@endphp
		<thead>
			<tr class="row-border">
				<th>
					#
				</th>
				<th style="width: 40% !important;">
					{{$invoice_layout->table_product_label}}
				</th>
				@if($show_cat_code)
					<th>
						{{$invoice_layout->cat_code_label}}
					</th>
				@endif
				<th>
					{{$invoice_layout->table_qty_label}}
				</th>
				<th >
					{{$invoice_layout->table_unit_price_label}}
				</th>
				<th>
					{{$invoice_layout->table_subtotal_label}}
				</th>
		</tr>
		</thead>
	 	@php 
        	$total = 0.00;
        	$is_empty_row_looped = true;
        	$tax_array = [];
      	@endphp
		@foreach($purchase->purchase_lines as $purchase_line)
			<tr @if($loop->iteration % 2 !== 0) class="odd" @endif style="border:hidden;">
				<td>
					{{$loop->iteration}}
				</td>
				<td style="width: 40% !important;">
					{{ $purchase_line->product->name }}
	                @if( $purchase_line->product->type == 'variable')
	                  - {{ $purchase_line->variations->product_variation->name}}
	                  - {{ $purchase_line->variations->name}}
	                 @endif

	                @if($show_sku)
	                , {{$purchase_line->variations->sub_sku}}
	                @endif

	                @if($show_brand && !empty($purchase_line->product->brand))
	                , {{$purchase_line->product->brand->name ?? ''}}
	                @endif
				</td>
				@if($show_cat_code)
					<td>
						{{ $purchase_line->product->category->short_code ?? '' }}
					</td>
				@endif
				<td>
					{{@format_quantity($purchase_line->quantity)}}
				</td>
				<td>
					@format_currency($purchase_line->purchase_price)
				</td>
				<td>
					@php 
		              $total += ($purchase_line->quantity * $purchase_line->purchase_price);
		              if (!empty($purchase_line->tax_id)) {
		              	$tax_array[$purchase_line->tax_id][] = ($purchase_line->item_tax * $purchase_line->quantity);
		              }
		            @endphp
		            @format_currency($purchase_line->quantity * $purchase_line->purchase_price)
				</td>
			</tr>
			@if(count($purchase->purchase_lines) < 6 && $is_empty_row_looped && $loop->last)
				@php
					$i = 0;
					$is_empty_row_looped = false;
					$loop_until = 0;
					if (count($purchase->purchase_lines) == 1) {
						$loop_until = 5;
					} elseif (count($purchase->purchase_lines) == 2) {
						$loop_until = 4;
					} elseif (count($purchase->purchase_lines) == 3) {
						$loop_until = 3;
					} elseif (count($purchase->purchase_lines) == 4) {
						$loop_until = 3;
					}
				@endphp
				@for($i; $i<= $loop_until ; $i++)
					<tr style="border:hidden;">
						<td>
							&nbsp;
						</td>
						<td>
							&nbsp;
						</td>
						@if($show_cat_code)
							<td>
								&nbsp;
							</td>
						@endif
						<td>
							&nbsp;
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
				@endfor
			@endif
		@endforeach
		<tr>
			<td @if($show_cat_code) colspan="5" @else colspan="4" @endif style="text-align: center;">
				{{$invoice_layout->sub_total_label}}
			</td>
			<td colspan="1">
				<strong>
					@format_currency($total)
				</strong>
			</td>
		</tr>
		<tr>
			<td @if($show_cat_code) colspan="3" @else colspan="2" @endif>
				@if($purchase->additional_notes)
		          {{ $purchase->additional_notes }}
		        @else
		          --
		        @endif
			</td>
			<td colspan="3">
				@if(!empty($tax_array))
		        	@foreach($tax_array as $key => $value)
		        		{{$taxes->where('id', $key)->first()->name}} ({{$taxes->where('id', $key)->first()->amount}}%) : @format_currency(array_sum($value)) <br>
		        	@endforeach
		        @endif
				
				{{$invoice_layout->total_label}} : @format_currency($purchase->final_total)
			</td>
		</tr>
		<tr>
			<td colspan="6">
				{!!ucfirst($total_in_words)!!}
			</td>
		</tr>
		<tr>
			<td colspan="6">
				@if(!empty($invoice_layout->footer_text))
					{!!$invoice_layout->footer_text!!}
				@endif
			</td>
		</tr>
	</table>
	</div>
	<table class="tpdf">
		<tr>
			<td colspan="2" style="text-align: center;">
				@lang('lang_v1.checked_by')
			</td>
			<td colspan="2" style="text-align: center;">
				@lang('lang_v1.prepared_by') <br>{{$purchase->sales_person->user_full_name}}
			</td>
			<td colspan="2" style="text-align: center;">
				<br><br>
				@lang('lang_v1.for_business', ['business' => $purchase->business->name])
				<br><br>
				@if(!empty($last_purchase))
					{{$last_purchase->sales_person->user_full_name}}
				@endif
				<br>
				{{__('lang_v1.authorized_signatory')}}
			</td>
		</tr>
	</table>
	@php
		$bottom = '5px';
		if (count($purchase->purchase_lines) >= 3) {
			$bottom = '-15px';
		}
	@endphp
	<div align="center" class="fs-10" style="position: fixed;width: 100%;bottom: {{$bottom}};text-align: center;">
		This is a computer generated document, no signature required.
	</div>
	@if (!$loop->last)
		<pagebreak>
	@endif
@endforeach