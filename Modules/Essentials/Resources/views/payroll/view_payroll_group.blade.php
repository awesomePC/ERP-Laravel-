@extends('layouts.app')
@section('title', __('essentials::lang.view_payroll_group'))
@section('content')
@include('essentials::layouts.nav_hrm')
<section class="content-header">
	<h1>
    	@lang('essentials::lang.view_payroll_group')
    	<small><code>({{$payroll_group->name}})</code></small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-solid" id="payroll-group">
				<div class="box-header no-print">
					<div class="box-tools">
						<button type="button" class="btn btn-primary" aria-label="Print" id="print_payrollgroup">
							<i class="fa fa-print"></i>
							@lang( 'messages.print' )
				      	</button>
			      	</div>
			    </div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<h3 class="text-center">
	                            <u>{!! __('essentials::lang.payroll_for_month', ['date' => $month_name . ' ' . $year]) !!}</u>
	                        </h3>
						</div>
					</div>
					<div class="row margin-bottom-20">
						<div class="col-md-6 text-center mt-5">
							<strong class="font-23">{{$payroll_group->business->name}}</strong> <br>
							@if(!empty($payroll_group->businessLocation))
								{{$payroll_group->businessLocation->name}} <br>
								{!!$payroll_group->businessLocation->location_address!!}
							@else
								{{__('report.all_locations')}}
							@endif
						</div>
						<div class="col-md-6 text-center mt-5">
							<b class="font-17">
								@lang('essentials::lang.payroll_group'):
							</b>
							{{$payroll_group->name}} <br>
							<b class="font-17">
								@lang('sale.status'):
							</b>
							@lang('sale.'.$payroll_group->status)
						</div>
					</div>
	                <div class="table-responsive mt-15">
	                    <table class="table" id="payroll-group-table" style="width: 100% !important;">
	                        <thead>
	                            <tr>
	                                <th style="width: 33% !important;">@lang( 'essentials::lang.employee' )</th>
	                                <th style="width: 33% !important;">@lang( 'essentials::lang.gross_amount' )</th>
	                                <th style="width: 33% !important;">@lang('lang_v1.bank_details')</th>
	                                <th>@lang('sale.payment_status')</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        	@foreach($payrolls as $id => $payroll)
		                        	<tr>
		                        		<td>
		                        			{{$payroll['employee']}}
		                        		</td>
		                        		<td>
		                        			@format_currency($payroll['final_total'])
		                        		</td>
		                        		<td>
		                        			<strong>@lang('lang_v1.bank_name'):</strong>
					      					{{$payroll['bank_details']['bank_name'] ?? ''}}
					      					<br>

					      					<strong>@lang('lang_v1.branch'):</strong>
					      					{{$payroll['bank_details']['branch'] ?? ''}}
					      					<br>

					      					<strong>@lang('lang_v1.bank_code'):</strong>
					      					{{$payroll['bank_details']['bank_code'] ?? ''}}
					      					<br>
					      					
					      					<strong>@lang('lang_v1.account_holder_name'):</strong>
					      					{{$payroll['bank_details']['account_holder_name'] ?? ''}}
					      					<br>

					      					<strong>@lang('lang_v1.bank_account_no'):</strong>
					      					{{$payroll['bank_details']['account_number'] ?? ''}}
					      					<br>
					      					<strong>@lang('lang_v1.tax_payer_id'):</strong>
					      					{{$payroll['bank_details']['tax_payer_id'] ?? ''}}
					      					<br>
		                        		</td>
		                        		<td>
		                        			{{__('lang_v1.'. $payroll['payment_status'])}}
		                        		</td>
		                        	</tr>
		                        @endforeach
	                        </tbody>
	                    </table>
	                </div>
            	</div>
            </div>
		</div>
  	</div>
</section>
@endsection
<style type="text/css">
	#payroll-group-table>thead>tr>th, #payroll-group-table>tbody>tr>th,
	#payroll-group-table>tfoot>tr>th, #payroll-group-table>thead>tr>td,
	#payroll-group-table>tbody>tr>td, #payroll-group-table>tfoot>tr>td {
		border: 1px solid #1d1a1a;
	}
</style>
@section('javascript')
<script type="text/javascript">
	$(document).ready(function () {
		$('#print_payrollgroup').click( function(){
			$('#payroll-group').printThis();
		});
	});
</script>
@stop