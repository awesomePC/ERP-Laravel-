@extends('layouts.app')

@section('title', __('repair::lang.view_job_sheet'))

@section('content')
@include('repair::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>
    	@lang('repair::lang.job_sheet')
    	(<code>{{$job_sheet->job_sheet_no}}</code>)
    </h1>
</section>
<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-solid">
				<div class="box-header no-print">
					<div class="box-tools">
						@if(auth()->user()->can("job_sheet.edit"))
							<a href="{{action('\Modules\Repair\Http\Controllers\JobSheetController@edit', ['id' => $job_sheet->id])}}" class="btn btn-info cursor-pointer">
			                    <i class="fa fa-edit"></i>
			                    @lang("messages.edit")
			                </a>
			            @endif
						<button type="button" class="btn btn-primary" aria-label="Print" id="print_jobsheet">
							<i class="fa fa-print"></i>
							@lang( 'repair::lang.print_format_1' )
				      	</button>

				      	<a class="btn btn-success" href="{{action('\Modules\Repair\Http\Controllers\JobSheetController@print', ['id' => $job_sheet->id])}}" target="_blank">
							<i class="fas fa-file-pdf"></i>
							@lang( 'repair::lang.print_format_2' )
				      	</a>
			      </div>
			    </div>
				<div class="box-body" id="job_sheet">
					{{-- business address --}}
					<div class="width-100">
						<div class="width-50 f-left" style="padding-top: 40px;">
							@if(!empty(Session::get('business.logo')))
			                  <img src="{{ asset( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo" style="width: auto; max-height: 90px; margin: auto;">
			                @endif
						</div>
						<div class="width-50 f-left" >
							<p style="text-align: center;padding-top: 40px;padding-left: 110px;">
								<strong class="font-23">
									{{$job_sheet->customer->business->name}}
								</strong>
								<br>
								@if(!empty($job_sheet->businessLocation))
									{{$job_sheet->businessLocation->name}}<br>
								@endif
								<span>
									{!!$job_sheet->businessLocation->location_address!!}
								</span>
								@if(!empty($job_sheet->businessLocation->mobile))
								<br>
									@lang('business.mobile'): {{$job_sheet->businessLocation->mobile}},
								@endif
								@if(!empty($job_sheet->businessLocation->alternate_number))
									@lang('invoice.show_alternate_number'): {{$job_sheet->businessLocation->alternate_number}},
								@endif
								@if(!empty($job_sheet->businessLocation->email))
								<br>
									@lang('business.email'): {{$job_sheet->businessLocation->email}},
								@endif

								@if(!empty($job_sheet->businessLocation->website))
									@lang('lang_v1.website'): {{$job_sheet->businessLocation->website}}
								@endif
							</p>
						</div>	
					</div>
					{{-- Job sheet details --}}
					<table class="table table-bordered" style="margin-top: 15px;">
						<tr>
							<th rowspan="3">
								@lang('receipt.date'):
								<span style="font-weight: 100">
									{{@format_datetime($job_sheet->created_at)}}
								</span>
							</th>
						</tr>
						<tr>
							<td>
								<b>@lang('repair::lang.service_type'):</b>
								@lang('repair::lang.'.$job_sheet->service_type)
							</td>
							<th rowspan="2">
								<b>
									@lang('repair::lang.expected_delivery_date'):
								</b>
								@if(!empty($job_sheet->delivery_date))
									<span style="font-weight: 100">
										{{@format_datetime($job_sheet->delivery_date)}}
									</span>
								@endif
							</th>
						</tr>
						<tr>
							<td>
								<b>@lang('repair::lang.job_sheet_no'):</b>
								{{$job_sheet->job_sheet_no}}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<strong>@lang('role.customer'):</strong><br>
								<p>
									{{$job_sheet->customer->name}} <br>
									{!! $job_sheet->customer->contact_address !!}
									@if(!empty($contact->email))
										<br>@lang('business.email'):
										{{$job_sheet->customer->email}}
									@endif
									<br>@lang('contact.mobile'):
									{{$job_sheet->customer->mobile}}
									@if(!empty($contact->tax_number))
										<br>@lang('contact.tax_no'):
										{{$job_sheet->customer->tax_number}}
									@endif
								</p>
							</td>
							<td>
								<b>@lang('product.brand'):</b>
								{{optional($job_sheet->brand)->name}}
								<br>
								<b>@lang('repair::lang.device'):</b>
								{{optional($job_sheet->device)->name}}
								<br>
								<b>@lang('repair::lang.device_model'):</b>
								{{optional($job_sheet->deviceModel)->name}}
								<br>
								<b>@lang('repair::lang.serial_no'):</b>
								{{$job_sheet->serial_no}}
								<br>
								<b>@lang('lang_v1.password'):</b>
								{{$job_sheet->security_pwd}}
								<br>
								<b>
									@lang('repair::lang.security_pattern_code'):
								</b>
								{{$job_sheet->security_pattern}}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('sale.invoice_no'):
								</b>
							</td>
							<td>
								@if($job_sheet->invoices->count() > 0)
									@foreach($job_sheet->invoices as $invoice)
										{{$invoice->invoice_no}}
										@if (!$loop->last)
									        {{', '}}
									    @endif
									@endforeach
								@endif
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('repair::lang.estimated_cost'):
								</b>
							</td>
							<td>
								<span class="display_currency" data-currency_symbol="true">
									{{$job_sheet->estimated_cost}}
								</span>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('sale.status'):
								</b>
							</td>
							<td>
								{{optional($job_sheet->status)->name}}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('business.location'):
								</b>
							</td>
							<td>
								{{optional($job_sheet->businessLocation)->name}}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('repair::lang.technician'):
								</b>
							</td>
							<td>
								{{optional($job_sheet->technician)->user_full_name}}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('repair::lang.comment_by_ss'):
								</b>
							</td>
							<td>
								{{$job_sheet->comment_by_ss}}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('repair::lang.pre_repair_checklist'):
								</b>
							</td>
							<td>
								@php
									$checklists = [];
									if (!empty($job_sheet->deviceModel) && !empty($job_sheet->deviceModel->repair_checklist)) {
										$checklists = explode('|', $job_sheet->deviceModel->repair_checklist);
									}
								@endphp
								@if(!empty($checklists))
									@foreach($checklists as $check)
										@php
									     	if(!isset($job_sheet->checklist[$check])) {
									        	continue;
									    	}
									    @endphp
			                            <div class="col-xs-4">
			                                @if($job_sheet->checklist[$check] == 'yes')
			                                    <i class="fas fa-check-square text-success fa-lg"></i>
			                                @elseif($job_sheet->checklist[$check] == 'no')
			                                  <i class="fas fa-window-close text-danger fa-lg"></i>
			                                @elseif($job_sheet->checklist[$check] == 'not_applicable')
			                                  <i class="fas fa-square fa-lg"></i>
			                                @endif
			                                {{$check}}
			                                <br>
			                            </div>
			                        @endforeach
			                    @endif
							</td>
						</tr>
						@if($job_sheet->service_type == 'pick_up' || $job_sheet->service_type == 'on_site')
							<tr>
								<td colspan="3">
									<b>
										@lang('repair::lang.pick_up_on_site_addr'):
									</b> <br>
									{!!$job_sheet->pick_up_on_site_addr!!}
								</td>
							</tr>
						@endif
						<tr>
							<td colspan="3">
								<b>
									@lang('repair::lang.product_configuration'):
								</b> <br>
								@php
									$product_configuration = json_decode($job_sheet->product_configuration, true);
								@endphp
								@if(!empty($product_configuration))
									@foreach($product_configuration as $product_conf)
										{{$product_conf['value']}}
										@if(!$loop->last)
											{{','}}
										@endif
									@endforeach
								@endif
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<b>
									@lang('repair::lang.condition_of_product'):
								</b> <br>
								@php
									$product_condition = json_decode($job_sheet->product_condition, true);
								@endphp
								@if(!empty($product_condition))
									@foreach($product_condition as $product_cond)
										{{$product_cond['value']}}
										@if(!$loop->last)
											{{','}}
										@endif
									@endforeach
								@endif
							</td>
						</tr>
						@if(!empty($job_sheet->custom_field_1))
							<tr>
								<td colspan="2">
									<b>
										{{$repair_settings['job_sheet_custom_field_1'] ?? __('lang_v1.custom_field', ['number' => 1])}}:
									</b>
								</td>
								<td>
									{{$job_sheet->custom_field_1}}
								</td>
							</tr>
						@endif
					</td>
				</tr>
				<tr>
					<th colspan="2">@lang('repair::lang.parts_used'):</th>
					<td>
						@if(!empty($parts))
						<table>
							@foreach($parts as $part)
								<tr>
									<td>{{$part['variation_name']}}: &nbsp;</td>
									<td>{{$part['quantity']}} {{$part['unit']}}</td>
								</tr>
							@endforeach
						</table>
						@endif
					</td>
				</tr>
				@if(!empty($job_sheet->custom_field_2))
					<tr>
						<td colspan="2">
							<b>
								{{$repair_settings['job_sheet_custom_field_2'] ?? __('lang_v1.custom_field', ['number' => 2])}}:
							</b>
						</td>
						<td>
							{{$job_sheet->custom_field_2}}
						</td>
					</tr>
				@endif
				@if(!empty($job_sheet->custom_field_3))
					<tr>
						<td colspan="2">
							<b>
								{{$repair_settings['job_sheet_custom_field_3'] ?? __('lang_v1.custom_field', ['number' => 3])}}:
							</b>
						</td>
						<td>
							{{$job_sheet->custom_field_3}}
						</td>
					</tr>
				@endif
				@if(!empty($job_sheet->custom_field_4))
					<tr>
						<td colspan="2">
							<b>
								{{$repair_settings['job_sheet_custom_field_4'] ?? __('lang_v1.custom_field', ['number' => 4])}}:
							</b>
						</td>
						<td>
							{{$job_sheet->custom_field_4}}
						</td>
					</tr>
				@endif
				@if(!empty($job_sheet->custom_field_5))
					<tr>
						<td colspan="2">
							<b>
								{{$repair_settings['job_sheet_custom_field_5'] ?? __('lang_v1.custom_field', ['number' => 5])}}:
							</b>
						</td>
						<td>
							{{$job_sheet->custom_field_5}}
						</td>
					</tr>
				@endif
				<tr>
							<td colspan="3">
								<b>
									@lang('repair::lang.problem_reported_by_customer'):
								</b> <br>
								@php
									$defects = json_decode($job_sheet->defects, true);
								@endphp
								@if(!empty($defects))
									@foreach($defects as $product_defect)
										{{$product_defect['value']}}
										@if(!$loop->last)
											{{','}}
										@endif
									@endforeach
								@endif
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<strong>
									@lang("lang_v1.terms_conditions"):
								</strong>
								@if(!empty($repair_settings['repair_tc_condition']))
									{!!$repair_settings['repair_tc_condition']!!}
								@endif
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<b>
									@lang('repair::lang.customer_signature'):
								</b>
							</td>
							<td>
								<b>
									@lang('repair::lang.authorized_signature'):
								</b>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		@if($job_sheet->media->count() > 0)
			<div class="col-md-6">
				<div class="box box-solid no-print">
					<div class="box-header with-border">
						<h4 class="box-title">
							@lang('repair::lang.uploaded_image_for', ['job_sheet_no' => $job_sheet->job_sheet_no])
						</h4>
				    </div>
					<div class="box-body">
						@includeIf('repair::job_sheet.partials.document_table_view', ['medias' => $job_sheet->media])
					</div>
				</div>
			</div>
		@endif
		<div class="col-md-6">
			<div class="box box-solid box-solid no-print">
		        <div class="box-header with-border">
		            <h3 class="box-title">{{ __('repair::lang.activities') }}:</h3>
		        </div>
		        <!-- /.box-header -->
		        @include('repair::repair.partials.activities')
		    </div>
		</div>
	</div>
</section>
<!-- /.content -->
@stop
@section('css')
<style type="text/css">
	.table-bordered>thead>tr>th, .table-bordered>tbody>tr>th,
	.table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td,
	.table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
		border: 1px solid #1d1a1a;
	}
</style>
@stop
@section('javascript')
<script type="text/javascript">
	$(document).ready(function () {
		$('#print_jobsheet').click( function(){
			$('#job_sheet').printThis();
		});
		$(document).on('click', '.delete_media', function (e) {
            e.preventDefault();
            var url = $(this).data('href');
            var this_btn = $(this);
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((confirmed) => {
                if (confirmed) {
                    $.ajax({
                        method: 'GET',
                        url: url,
                        dataType: 'json',
                        success: function(result) {
                            if(result.success == true){
			                    this_btn.closest('tr').remove();
			                    toastr.success(result.msg);
			                } else {
			                    toastr.error(result.msg);
			                }
                        }
                    });
                }
            });
        });
	});
</script>
@stop