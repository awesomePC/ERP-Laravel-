@extends('layouts.app')
@section('title', __('essentials::lang.add_payment_for_payroll_group'))
@section('content')
@include('essentials::layouts.nav_hrm')
<section class="content-header">
	<h1>
    	@lang('essentials::lang.add_payment_for_payroll_group')
    	<small><code>({{$payroll_group->name}})</code></small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
	<div class="row">
		{!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\PayrollController@postAddPayment'), 'method' => 'post', 'id' => 'payroll_group_payment' ]) !!}
		{!! Form::hidden('payroll_group_id', $payroll_group->id); !!}
		<div class="col-md-12">
			<div class="box box-solid" id="payroll-group">
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<h3 class="text-center">
	                            <u>{!! __('essentials::lang.payroll_for_month', ['date' => $month_name . ' ' . $year]) !!}</u>
	                        </h3>
						</div>
					</div>
					<div class="row margin-bottom-20">
						<div class="col-md-6 text-center">
							<strong class="font-23">{{$payroll_group->business->name}}</strong> <br>
							@if(!empty($payroll_group->businessLocation))
								{{$payroll_group->businessLocation->name}} <br>
								{!!$payroll_group->businessLocation->location_address!!}
							@else
								{{__('report.all_locations')}}
							@endif
						</div>
						<div class="col-md-6 text-center">
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
                            <tr>
                                <th>@lang( 'essentials::lang.employee' )</th>
                                <th>@lang( 'essentials::lang.gross_amount' )</th>
                                <th>@lang('lang_v1.bank_details')</th>
                                <th>
                                	@lang('purchase.add_payment')
                                </th>
                            </tr>
                        	@foreach($payrolls as $id => $payroll)
	                        	<tr data-id="{{$id}}">
	                        		<input type="hidden" name="payments[{{$id}}][transaction_id]" value="{{$payroll['transaction_id']}}">
                        			<input type="hidden" name="payments[{{$id}}][employee_id]" value="{{$payroll['employee_id']}}">
                        			<input type="hidden" name="payments[{{$id}}][final_total]" value="{{$payroll['final_total']}}">
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
	                        			@if($payroll['payment_status'] == 'paid')
	                        				<span class="label bg-light-green">
	                        					<i class="fas fa-check-circle"></i>
	                        					@lang('lang_v1.paid')
	                        				</span>
	                        			@else
		                        			@includeIf('essentials::payroll.payment_row')
                                    	@endif
	                        		</td>
	                        	</tr>
	                        @endforeach
	                    </table>
	                </div>
            	</div>
            	<button type="submit" class="btn btn-primary pull-right m-8" id="submit_user_button">
	                {{__( 'lang_v1.pay' )}}
	            </button>
            </div>
		</div>
		{!! Form::close() !!}
  	</div>
</section>
@endsection
@section('javascript')
<script type="text/javascript">
	$(function () {
		$('.paid_on').datetimepicker({
            format: moment_date_format + ' ' + moment_time_format,
            ignoreReadonly: true,
        });

        $('select.payment_types').on('change', function () {
        	let method = $(this).find(':selected').val();
        	let id = $(this).data('id');
        	if (method == 'card') {
        		$('#card_' + id).show();
        		$(`#custom_pay_1_${id}, #custom_pay_2_${id}, #custom_pay_3_${id}, #cheque_${id}, #bank_transfer_${id}`).hide();
        	} else if (method == 'cheque') {
        		$('#cheque_' + id).show();
        		$(`#custom_pay_1_${id}, #custom_pay_2_${id}, #custom_pay_3_${id}, #bank_transfer_${id}, #card_${id}`).hide();
        	} else if (method == 'bank_transfer') {
        		$('#bank_transfer_' + id).show();
        		$(`#custom_pay_1_${id}, #custom_pay_2_${id}, #custom_pay_3_${id}, #cheque_${id}, #card_${id}`).hide();
        	} else if (method == 'custom_pay_1') {
        		$('#custom_pay_1_' + id).show();
        		$(`#custom_pay_2_${id}, #custom_pay_3_${id}, #cheque_${id}, #bank_transfer_${id}, #card_${id}`).hide();
        	} else if (method == 'custom_pay_2') {
        		$('#custom_pay_2_' + id).show();
        		$(`#custom_pay_1_${id}, #custom_pay_3_${id}, #cheque_${id}, #bank_transfer_${id}, #card_${id}`).hide();
        	} else if (method == 'custom_pay_3') {
        		$('#custom_pay_3_' + id).show();
        		$(`#custom_pay_1_${id}, #custom_pay_2_${id}, #cheque_${id}, #bank_transfer_${id}, #card_${id}`).hide();
        	} else {
        		$(`#custom_pay_1_${id}, #custom_pay_2_${id}, #custom_pay_3_${id}, #cheque_${id}, #bank_transfer_${id}, #card_${id}`).hide();
        	}
        });
	});
</script>
@endsection