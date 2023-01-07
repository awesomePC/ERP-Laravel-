@extends('layouts.app')
@section('title', __( 'repair::lang.repair_label' ))
@section('content')
<style type="text/css">
</style>
<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-md-12 text-center">
			@php
				$loop_count = 0;
			@endphp
			@foreach($product_details as $details)
				@while($details['qty'] > 0)
					@php
						$loop_count += 1;
						$is_new_row = ($barcode_details->is_continuous) && (($loop_count == 1) || ($loop_count % $barcode_details->stickers_in_one_row) == 1) ? true : false;

						$is_new_row = true;
					@endphp

					@if(($barcode_details->is_continuous) || (!$barcode_details->is_continuous && ($loop_count % $barcode_details->stickers_in_one_sheet) == 1))

						{{-- Actual Paper --}}
					<div style="@if(!$barcode_details->is_continuous) height:{{$barcode_details->paper_height*0.95}}in !important; @else height:{{$barcode_details->height*0.95}}in !important; @endif width:{{$barcode_details->paper_width}}in !important; line-height: 16px !important; page-break-after: always;">


						{{-- Paper Internal --}}
						<div style="@if(!$barcode_details->is_continuous)margin-top:{{$barcode_details->top_margin}}in !important; margin-bottom:{{$barcode_details->top_margin}}in !important; margin-left:{{$barcode_details->left_margin}}in !important;margin-right:{{$barcode_details->left_margin}}in !important;@endif" class="label-border-internal">
					@endif

					@if((!$barcode_details->is_continuous) && ($loop_count % $barcode_details->stickers_in_one_sheet) <= $barcode_details->stickers_in_one_row)
						@php $first_row = true @endphp
					@elseif($barcode_details->is_continuous && ($loop_count <= $barcode_details->stickers_in_one_row) )
						@php $first_row = true @endphp
					@else
						@php $first_row = false @endphp
					@endif

					<div style="height:{{$barcode_details->height}}in !important; line-height: {{$barcode_details->height}}in; width:{{$barcode_details->width*0.97}}in !important; display: inline-block; @if(!$is_new_row) margin-left:{{$barcode_details->col_distance}}in !important; @endif @if(!$first_row)margin-top:{{$barcode_details->row_distance}}in !important; @endif" class="sticker-border text-center">
					<div style="display:inline-block;vertical-align:middle;line-height:16px !important;     text-align: center;" class="barcode">
						{{-- Business Name --}}
						@if(!empty($business_name))
							<b style="display: block !important" class="text-uppercase">{{$business_name}}</b>
						@endif
						@if(!empty($details['details']->contact->name))
							<small>@lang('contact.customer'): {{$details['details']->contact->name}}</small><br>
						@endif
						@if(!empty($details['details']->repair_model))
							<small>@lang('repair::lang.model'): {{$details['details']->repair_model}}</small><br>
						@endif

						{{-- Barcode --}}
						<img class="center-block" style="width:90% !important;height: {{$barcode_details->height*0.24}}in !important;" src="data:image/png;base64,{{DNS1D::getBarcodePNG($details['details']->invoice_no, $barcode_type, 2,30,array(39, 48, 54), true)}}">

					</div>
					</div>

					@if($barcode_details->is_continuous || ($loop_count % $barcode_details->stickers_in_one_sheet) == 0)
						{{-- Actual Paper --}}
						</div>

						{{-- Paper Internal --}}
						</div>
					@endif

					@php
						$details['qty'] = $details['qty'] - 1;
					@endphp
				@endwhile
				<br>
				<br>
				<a href="#" class="print-invoice no-print btn btn-success" data-href="{{route('sell.printInvoice', [$details['details']->id])}}"><i class="fa fa-print"></i> {{__("repair::lang.print_invoice")}}</a>

				<a href="#" id="print-barcode" class="no-print btn btn-primary"><i class="fa fa-barcode"></i> {{__("repair::lang.print_barcode")}}</a>
			@endforeach

			</div>
    </div>
</div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script type="text/javascript">
        setTimeout(function(){ $('.barcode').printThis(); }, 1000);
        $('.print-invoice').click( function() {
        	$('.content').addClass('no-print');
        });
        $('#print-barcode').click( function() {
        	$('.content').removeClass('no-print');
        	$('.barcode').printThis();
        });
    </script>
@endsection