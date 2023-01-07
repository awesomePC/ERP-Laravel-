<div class="modal-dialog modal-xl no-print" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<h4 class="modal-title">
				@lang('project::lang.invoice_details')
				(<b>@lang('sale.invoice_no'):</b> {{$transaction->invoice_no}})
			</h4>
		</div>
		<div class="modal-body">
			<div class="row">
		      <div class="col-xs-12">
		          <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($transaction->transaction_date) }}</p>
		      </div>
		    </div>
		    <!-- invoice details -->
		    <div class="row">
		    	<div class="col-md-4">
		    		<b>@lang('project::lang.title'):</b>
		    			{{ $transaction->pjt_title }}<br>
		    		<b>{{ __('sale.invoice_no') }}:</b>
		    			#{{ $transaction->invoice_no }}<br>
			        <b>{{ __('sale.status') }}:</b> 
			           {{ __('sale.' . $transaction->status) }}
			        <br>
			        <b>{{ __('sale.payment_status') }}:</b>
			        @if(!empty($transaction->payment_status))
			        	{{ __('lang_v1.'.$transaction->payment_status) }}
			        	<br>
			        @endif
		    	</div>
		    	<div class="col-md-4">
		    		<b>{{ __('sale.customer_name') }}:</b>
		    			{{ $transaction->contact->name }}<br>
			        <b>{{ __('business.address') }}:</b><br>
			        @if(!empty($transaction->billing_address()))
			          {{$transaction->billing_address()}}
			        @else
			          @if($transaction->contact->landmark)
			              {{ $transaction->contact->landmark }},
			          @endif

			          {{ $transaction->contact->city }}

			          @if($transaction->contact->state)
			              {{ ', ' . $transaction->contact->state }}
			          @endif
			          <br>
			          @if($transaction->contact->country)
			              {{ $transaction->contact->country }}
			          @endif
			          @if($transaction->contact->mobile)
			          <br>
			              {{__('contact.mobile')}}: {{ $transaction->contact->mobile }}
			          @endif
			          @if($transaction->contact->alternate_number)
			          <br>
			              {{__('contact.alternate_contact_number')}}:
			              {{ $transaction->contact->alternate_number }}
			          @endif
			          @if($transaction->contact->landline)
			            <br>
			              {{__('contact.landline')}}:
			              {{ $transaction->contact->landline }}
			          @endif
			        @endif
		    	</div>
		    	<div class="col-md-4">
		    		<b>@lang('project::lang.project'):</b>
		    		{{$transaction->project->name}} <br>
		    		<b>@lang('sale.status'):</b>
		    		@lang('project::lang.'.$transaction->project->status)
		    		<br>
		    		@if(isset($transaction->project->start_date))
						<b>@lang('business.start_date'):</b>
						{{@format_date($transaction->project->start_date)}}
					@endif <br>
					@if(isset($transaction->project->end_date))
						<b>@lang('project::lang.end_date'):</b>
						{{@format_date($transaction->project->end_date)}}
					@endif
		    	</div>
		    </div> <br>
		    <!-- /invoice details -->

		    <!-- invoice lines -->
		    <div class="row">
		      <div class="col-sm-12 col-xs-12">
		        <h4>{{ __('project::lang.tasks') }}:</h4>
		      </div>

		      <div class="col-sm-12 col-xs-12">
		        <div class="table-responsive">
		          @includeIf('project::invoice.partials.invoice_line_table')
		        </div>
		      </div>
		    </div>
		    <!-- /invoice lines -->

		    <!-- payment info & invoice total -->
		    <div class="row">
		    	<div class="col-sm-12 col-xs-12">
				    <h4>{{ __('sale.payment_info') }}:</h4>
				</div>
				<div class="col-md-6 col-sm-12 col-xs-12">
				    <div class="table-responsive">
				        <table class="table bg-gray">
				            <tr class="bg-green">
				                <th>#</th>
				                <th>{{ __('messages.date') }}</th>
				                <th>{{ __('purchase.ref_no') }}</th>
				                <th>{{ __('sale.amount') }}</th>
				                <th>{{ __('sale.payment_mode') }}</th>
				                <th>{{ __('sale.payment_note') }}</th>
				            </tr>
				            @php
				                $total_paid = 0;
				            @endphp
				            @foreach($transaction->payment_lines as $payment_line)
				                @php
				                    if($payment_line->is_return == 1){
				                      $total_paid -= $payment_line->amount;
				                    } else {
				                      $total_paid += $payment_line->amount;
				                    }
				                @endphp
				            <tr>
				                <td>{{ $loop->iteration }}</td>
				                <td>{{ @format_date($payment_line->paid_on) }}</td>
				                <td>{{ $payment_line->payment_ref_no }}</td>
				                <td>
				                    <span class="display_currency" data-currency_symbol="true">
				                        {{ $payment_line->amount }}
				                    </span>
				                </td>
				                <td>
				                  {{ $payment_types[$payment_line->method] ?? $payment_line->method }}
				                  @if($payment_line->is_return == 1)
				                    <br/>
				                    ( {{ __('lang_v1.change_return') }} )
				                  @endif
				                </td>
				                <td>@if($payment_line->note) 
				                  {{ ucfirst($payment_line->note) }}
				                  @else
				                  --
				                  @endif
				                </td>
				            </tr>
				            @endforeach
				        </table>
				    </div>
				</div>
		    	<div class="col-md-6 col-sm-12 col-xs-12">
		    		<div class="table-responsive">
		    			<table class="table bg-gray">
		    				<tr>
			    				<th>{{ __('sale.subtotal') }}: </th>
					            <td></td>
					            <td>
					            	<span class="display_currency pull-right" data-currency_symbol="true">
					            		{{ $transaction->total_before_tax }}
					            	</span>
					            </td>
				            </tr>
				            <tr>
				              	<th>{{ __('sale.discount') }}:</th>
				              	<td><b>(-)</b></td>
				              	<td>
					              	<div class="pull-right">
					              		<span class="display_currency"
					              			@if( $transaction->discount_type == 'fixed') data-currency_symbol="true" @endif>
					              			{{ $transaction->discount_amount }}
					              		</span>
					              		@if($transaction->discount_type == 'percentage')
					              			{{ '%'}}
					              		@endif
					              	</div>
				              	</td>
				            </tr>
							<tr>
								<th>{{ __('sale.total_payable') }}: </th>
								<td></td>
								<td>
									<span class="display_currency pull-right" data-currency_symbol="true">
										{{ $transaction->final_total }}
									</span>
								</td>
							</tr>
							<tr>
								<th>{{ __('sale.total_paid') }}:</th>
								<td></td>
								<td>
									<span class="display_currency pull-right" data-currency_symbol="true" >
										{{ $total_paid }}
									</span>
								</td>
							</tr>
							<tr>
								<th>
									{{ __('sale.total_remaining') }}:
								</th>
								<td></td>
								<td>
									<span class="display_currency pull-right" data-currency_symbol="true" >
										{{ $transaction->final_total - $total_paid }}
									</span>
								</td>
							</tr>
		    			</table>
		    		</div>
		    	</div>
		    </div>
		    <!-- /payment info & invoice total -->

		    <!-- terms/notes -->
		    <div class="row">
				<div class="col-sm-6">
					<strong>
						{{ __('project::lang.terms')}}:
					</strong><br>
					<p class="well well-sm no-shadow bg-gray">
					  @if($transaction->staff_note)
					    {{ $transaction->staff_note }}
					  @else
					    --
					  @endif
					</p>
				</div>
				<div class="col-sm-6">
					<strong>
						{{ __('project::lang.notes')}}:
					</strong><br>
					<p class="well well-sm no-shadow bg-gray">
					  @if($transaction->additional_notes)
					    {{ $transaction->additional_notes }}
					  @else
					    --
					  @endif
					</p>
				</div>
		    </div>
		    <!-- / terms/notes -->
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default no-print" data-dismiss="modal">
				@lang( 'messages.close' )
			</button>
		</div>
	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script type="text/javascript">
  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);
  });
</script>