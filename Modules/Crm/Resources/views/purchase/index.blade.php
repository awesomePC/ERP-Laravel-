@extends('crm::layouts.app')

@section('title', __('purchase.purchases'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
   <h1>@lang('purchase.purchases')</h1>
</section>
<!-- Main content -->
<section class="content no-print">
	@component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('status_filter',  __('purchase.purchase_status') . ':') !!}
                {!! Form::select('status_filter', $orderStatuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('payment_status_filter',  __('purchase.payment_status') . ':') !!}
                {!! Form::select('payment_status_filter', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('date_range_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.all_purchases')])
        <div class="table-responsive">
        	<table class="table table-bordered table-striped ajax_view" id="contact_purchase_table">
        		<thead>
        			<tr>
        				<th>@lang('messages.action')</th>
        				<th>@lang('messages.date')</th>
		                <th>@lang('purchase.ref_no')</th>
		                <th>@lang('purchase.purchase_status')</th>
		                <th>@lang('purchase.payment_status')</th>
		                <th>@lang('purchase.grand_total')</th>
		                <th>@lang('purchase.payment_due') &nbsp;&nbsp;<i class="fa fa-info-circle text-info no-print" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="{{ __('messages.purchase_due_tooltip')}}" aria-hidden="true"></i></th>
		                <th>@lang('lang_v1.added_by')</th>
        			</tr>
        		</thead>
        		<tfoot>
		            <tr class="bg-gray font-17 text-center footer-total">
						<td colspan="3">
							<strong>@lang('sale.total'):</strong>
						</td>
						<td id="footer_status_count"></td>
						<td id="footer_payment_status_count"></td>
						<td>
							<span class="display_currency" id="footer_purchase_total" data-currency_symbol ="true">
							</span>
						</td>
						<td class="text-left">
							<small>@lang('report.purchase_due') -
								<span class="display_currency" id="footer_total_due" data-currency_symbol ="true"></span>
								<br>
								@lang('lang_v1.purchase_return') - 
									<span class="display_currency" id="footer_total_purchase_return_due" data-currency_symbol ="true"></span>
							</small>
						</td>	
						<td></td>
		            </tr>
		        </tfoot>
        	</table>
        </div>
    @endcomponent
</section>
@endsection
@section('javascript')
<script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
@endsection