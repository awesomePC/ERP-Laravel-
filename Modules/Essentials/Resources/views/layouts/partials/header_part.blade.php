@if($__is_essentials_enabled && $is_employee_allowed)
	<button 
		type="button" 
		class="btn bg-blue btn-flat 
		pull-left m-8 btn-sm mt-10 
		clock_in_btn
		@if(!empty($clock_in))
	    	hide
	    @endif
		"
	    data-type="clock_in"
	    data-toggle="tooltip"
	    data-placement="bottom"
	    title="@lang('essentials::lang.clock_in')" 
	    >
	    <i class="fas fa-arrow-circle-down"></i>
	</button>

	<button 
		type="button" 
		class="btn bg-yellow btn-flat pull-left m-8 
		 btn-sm mt-10 clock_out_btn
		@if(empty($clock_in))
	    	hide
	    @endif
		" 	
	    data-type="clock_out"
	    data-toggle="tooltip"
	    data-placement="bottom"
	    data-html="true"
	    title="@lang('essentials::lang.clock_out') @if(!empty($clock_in))
                    <br>
                    <small>
                    	<b>@lang('essentials::lang.clocked_in_at'):</b> {{@format_datetime($clock_in->clock_in_time)}}
                    </small>
                    <br>
                    <small><b>@lang('essentials::lang.shift'):</b> {{ucfirst($clock_in->shift_name)}}</small>
                    @if(!empty($clock_in->start_time) && !empty($clock_in->end_time))
                    	<br>
                    	<small>
                    		<b>@lang('restaurant.start_time'):</b> {{@format_time($clock_in->start_time)}}<br>
                    		<b>@lang('restaurant.end_time'):</b> {{@format_time($clock_in->end_time)}}
                    	</small>
                    @endif
                @endif" 
	    >
	    <i class="fas fa-hourglass-half fa-spin"></i>
	</button>
@endif