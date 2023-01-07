@extends('crm::layouts.app')

@section('title', __('lang_v1.ledger'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>@lang('lang_v1.ledger')</h1>
</section>
<!-- Main content -->
<section class="content">
	@php
	    $transaction_types = [];
	    if(in_array($contact->type, ['both', 'supplier'])){
	        $transaction_types['purchase'] = __('lang_v1.purchase');
	        $transaction_types['purchase_return'] = __('lang_v1.purchase_return');
	    }

	    if(in_array($contact->type, ['both', 'customer'])){
	        $transaction_types['sell'] = __('sale.sale');
	        $transaction_types['sell_return'] = __('lang_v1.sell_return');
	    }

	    $transaction_types['opening_balance'] = __('lang_v1.opening_balance');
	@endphp
	<div class="box box-solid">
		<div class="box-body">
		    <div class="col-md-12">
		        <div class="col-md-3">
		            <div class="form-group">
		                {!! Form::label('ledger_date_range', __('report.date_range') . ':') !!}
		                {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
		            </div>
		        </div>
		        <div class="col-md-9 text-right">
		            <button data-href="{{action('\Modules\Crm\Http\Controllers\LedgerController@getLedger')}}?action=pdf" class="btn btn-default btn-xs" id="create_ledger_pdf"><i class="fas fa-file-pdf"></i></button>
		        </div>
		    </div>
		    <div id="contact_ledger_div"></div>
		</div>
	</div>
</section>
@endsection
@section('javascript')
	<script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			getLedger();
		});
	</script>
@endsection