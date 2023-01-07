<div class="modal-dialog" role="document">
  	<div class="modal-content">
  		<div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title">@lang( 'essentials::lang.activity' )</h4>
	    </div>
  		<div class="modal-body">
  			<div class="row">
  				<div class="col-md-12">
  					<h4>@lang('essentials::lang.leave'): {{$leave->ref_no}}</h4>
  					<strong>@lang( 'essentials::lang.start_date' ):</strong> {{@format_date($leave->start_date)}} &nbsp; &nbsp;
  					<strong>@lang( 'essentials::lang.end_date' ):</strong> @if(!empty($leave->end_date)){{@format_date($leave->end_date)}}@endif 
  				</div>
  			</div>
  			<br>
  			<div class="row">
  				<div class="col-md-12">
		  			<table class="table table-condensed bg-gray">
		                <tr>
		                    <th>@lang('lang_v1.date')</th>
		                    <th>@lang('messages.action')</th>
		                    <th>@lang('lang_v1.by')</th>
		                    <th>@lang('brand.note')</th>
		                </tr>
		                @forelse($activities as $activity)
		                    <tr>
		                        <td>{{@format_datetime($activity->created_at)}}</td>
		                        <td>
		                        	@lang('lang_v1.' . $activity->description)
		                        </td>
		                        <td>{{$activity->causer->user_full_name}}</td>
		                        <td>
		                        	@if(!empty($activity->changes['attributes']['status_note']))
		                        	{{$activity->changes['attributes']['status_note']}}
		                        	<br>
		                        	@endif
		                            @if($activity->description == 'updated')
		                            	@if(!empty($activity->changes['attributes']['status']))
		                                	@lang('essentials::lang.status_changed_to', ['status' => $activity->changes['attributes']['status']])
		                                @endif
		                            @endif
		                        </td>
		                    </tr>
		                @empty
		                    <tr>
		                      <td colspan="4" class="text-center">
		                        @lang('purchase.no_records_found')
		                      </td>
		                    </tr>
		                @endforelse
		            </table>
		        </div>
		    </div>
  		</div>
  		<div class="modal-footer">
	      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
  	</div>
</div>