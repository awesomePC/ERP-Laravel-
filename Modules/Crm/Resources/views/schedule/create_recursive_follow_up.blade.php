@extends('layouts.app')
@section('title', __('crm::lang.add_recursive_follow_up'))
@section('content')
	@include('crm::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	   <h1>@lang('crm::lang.add_recursive_follow_up')</h1>
	</section>
	<section class="content no-print">
		{!! Form::open(['url' => action('\Modules\Crm\Http\Controllers\ScheduleController@store'), 'method' => 'post', 'id' => 'add_advance_schedule' ]) !!}
		<input type="hidden" name="is_recursive" value="true" id="is_recursive">
		<div class="box box-solid">
        	<div class="box-body">
        		<div class="row">
        			<div class="col-md-4">
		               <div class="form-group">
		                    {!! Form::label('follow_up_by', __('crm::lang.follow_up_by') .':*') !!} @show_tooltip(__('crm::lang.follow_up_help'))
		                    <select name="follow_up_by_value" class="form-control" id="recur_follow_up_by" required>
	                    		<option value="">@lang('messages.please_select')</option>
							  	<optgroup label="@lang('sale.payment_status')">
							    	<option value="all" data-category="payment_status">@lang('lang_v1.all')</option>
							    	<option value="due" data-category="payment_status">@lang('lang_v1.due')</option>
							    	<option value="partial" data-category="payment_status">@lang('lang_v1.partial')</option>
							    	<option value="overdue" data-category="payment_status">@lang('lang_v1.overdue')</option>
							  	</optgroup>
							  	<optgroup label="@lang('restaurant.orders')">
							    	<option value="has_no_transactions" data-category="orders">
							    		@lang('crm::lang.has_no_transactions')
							    	</option>
							  	</optgroup>
							</select>
							<input type="hidden" name="follow_up_by" id="follow_up_by_category" value="">
		               </div>
		            </div>

		            <div class="col-md-4">
		               	<div class="form-group">
		                    <label for="in_days">{{__('crm::lang.in_days')}}:*</label>
		                    <div class="input-group">
		                        <div class="input-group-addon">{{__('crm::lang.in')}}</div>
		                            <input type="text" class="form-control input_number" id="in_days" placeholder="@lang('crm::lang.enter_days')" name="recursion_days" required>
		                        <div class="input-group-addon">{{__('lang_v1.days')}}</div>
		                    </div>
		                </div>
		            </div>
		            <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('user_id', __('crm::lang.assgined') .':*') !!}
                            {!! Form::select('user_id[]', $users, null, ['class' => 'form-control select2', 'multiple', 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
		        </div>
        	</div>
       	</div>
        <div class="box box-solid">
        	<div class="box-body">
				<div class="row">
		            <div class="col-md-12">
		               <div class="form-group">
		                    {!! Form::label('title', __('crm::lang.title') . ':*' )!!}
		                    {!! Form::text('title', null, ['class' => 'form-control', 'required' ]) !!}
		                    <p>
								<strong>
									{{$followup_tags['help_text']}}:
								</strong>
								<span class="text-primary invoice_tags" id="title_invoice_tags" style="display: none;">
									{{implode(', ', $followup_tags['invoice'])}}
								</span>
								<span class="text-primary trans_days_tags" id="title_trans_days_tags" style="display: none;">
									{{implode(', ', $followup_tags['trans_days'])}}
								</span>
							</p>
		               </div>
		            </div>
		        </div>
		        <div class="row">
		            <div class="col-md-12">
		                <div class="form-group">
		                    {!! Form::label('description', __('crm::lang.description') . ':') !!}
		                    {!! Form::textarea('description', null, ['class' => 'form-control ', 'id' => 'description']); !!}
		                    <p>
								<strong>
									{{$followup_tags['help_text']}}:
								</strong>
								<span class="text-primary invoice_tags" style="display: none;">
									{{implode(', ', $followup_tags['invoice'])}}
								</span>
								<span class="text-primary trans_days_tags" style="display: none;">
									{{implode(', ', $followup_tags['trans_days'])}}
								</span>
							</p>
		                </div>
		            </div>
		        </div>
		        <div class="row">
		        	<div class="col-md-6">
		               <div class="form-group">
		                    {!! Form::label('status', __('sale.status') .':') !!}
		                    {!! Form::select('status', $statuses, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;', 'id' => 'follow_up_create_status']); !!}
		               </div>
		            </div>
		            <div class="col-md-6">
		                <div class="form-group">
		                    {!! Form::label('schedule_type', __('crm::lang.schedule_type') .':*') !!}
		                    {!! Form::select('schedule_type', $follow_up_types, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
		                </div>
		            </div>
		            <div class="col-md-6">
		                <div class="form-group">
		                	<br>
		                    <label>
		                        {!! Form::checkbox('allow_notification', 1, false, ['class' => 'input-icheck', 'id' => 'allow_notification']); !!}
		                        @lang('crm::lang.send_notification')
		                    </label>
		                    @show_tooltip(__('crm::lang.send_schedule_notificatoion'))
		                </div>
		            </div>
		        </div>
		        <div class="row">
		            <div class="allow_notification_elements hide">
		                <div class="col-md-6">
		                    {!! Form::label('notify_via', __('crm::lang.notify_via') .':*') !!} 
		                    <div class="form-group checkbox-inline">
		                        <label>
		                            {!! Form::checkbox('notify_via[sms]', 1, false, ['class' => 'input-icheck']); !!}
		                            @lang('crm::lang.sms')
		                        </label>
		                    </div>
		                    <div class="form-group checkbox-inline">
		                        <label>
		                            {!! Form::checkbox('notify_via[mail]', 1, true, ['class' => 'input-icheck']); !!}
		                            @lang('business.email')
		                        </label>
		                    </div>
		                </div>
		                <div class="col-md-6">
		                    <div class="form-group">
		                        <div class="multi-input">
		                            {!! Form::label('notify_before', __('crm::lang.notify_before') . ':*') !!}
		                            <br/>
		                            {!! Form::number('notify_before', 1, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('crm::lang.notify_before'), 'required']); !!}

		                            {!! Form::select('notify_type', $notify_type, 'hour', ['class' => 'form-control width-60 pull-left']); !!}
		                        </div>
		                    </div>
		                </div>
		            </div>
		        </div>

		        <div class="row">
		        	<div class="col-md-12">
		        		<button type="submit" class="btn btn-primary pull-right">
		                    @lang('messages.save')
		                </button>
		            </div>
		        </div>
		    </div>
	     </div>

        {!! Form::close() !!}
	</section>
@endsection
@section('javascript')
<script type="text/javascript">
	$(document).ready( function(){
		$('form#add_advance_schedule').validate();
		$('form#add_advance_schedule .datetimepicker').datetimepicker({
	        ignoreReadonly: true,
	        format: moment_date_format + ' ' + moment_time_format
	    });

	    //initialize editor
	    tinymce.init({
	        selector: 'textarea#description',
	    });

	    $(document).on('ifChecked', '#allow_notification', function() {
	    	$("div").find('.allow_notification_elements').removeClass('hide');
	    });

	    $(document).on('ifUnchecked', '#allow_notification', function() {
	       $("div").find('.allow_notification_elements').addClass('hide');
	    });
		$('#recur_follow_up_by').change( function(){
			var follow_up_by = $(this).val();
			
			let category = $("#recur_follow_up_by :selected").data('category');
			if (category == 'payment_status') {
				$('span.trans_days_tags').hide();
				$('span.invoice_tags').show();
			} else if (category == 'orders') {
				$('span.invoice_tags').hide();
				$('span.trans_days_tags').show();
			}
			$("input#follow_up_by_category").val(category);
		});
	});
</script>
@endsection