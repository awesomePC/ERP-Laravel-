<div class="table-responsive">
	<table class="table bg-gray">
		<thead>
			<tr class="bg-green">
	            <th>#</th>
	            <th class="col-md-4">@lang('project::lang.task')</th>
	            <th>@lang('project::lang.rate')</th>
	            <th>@lang('sale.qty')</th>
	            <th>@lang('business.tax')</th>
	            <th>@lang('receipt.total')</th>
	        </tr>
	    </thead>
	    <tbody>
	    	@foreach($transaction->invoiceLines as $key => $invoiceLine)
		    	<tr>
		    		<td>{{$loop->iteration}}</td>
		    		<td>
		    			{{$invoiceLine->task}}
		    			@if(isset($invoiceLine->description))
		    				<br>
		    				({!! $invoiceLine->description !!})
		    			@endif
		    		</td>
		    		<td>
		    			<span class="display_currency" data-currency_symbol="true">
		    				{{$invoiceLine->rate}}
		    			</span>
		    		</td>
		    		<td>{{@format_quantity($invoiceLine->quantity)}}</td>
		    		<td>
		    			@if(isset($invoiceLine->tax->name))
		    				{{$invoiceLine->tax->name}}
		    			@else
		    				@lang('lang_v1.none')
		    			@endif
		    		</td>
		    		<td>
		    			<span class="display_currency" data-currency_symbol="true">
		    				{{$invoiceLine->total}}
		    			</span>
		    		</td>
		    	</tr>
		    @endforeach
	    </tbody>
	</table>
</div>