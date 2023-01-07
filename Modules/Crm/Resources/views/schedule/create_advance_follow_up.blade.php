@extends('layouts.app')
@section('title', __('crm::lang.add_schedule'))
@section('content')
	@include('crm::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	   <h1>@lang('crm::lang.add_schedule')</h1>
	</section>
	<section class="content no-print">
		{!! Form::open(['url' => action('\Modules\Crm\Http\Controllers\ScheduleController@store'), 'method' => 'post', 'id' => 'add_advance_schedule' ]) !!}

		<div class="box box-solid">
        	<div class="box-body">
        		<div class="row">
        			<div class="col-md-4">
		               <div class="form-group">
		                    {!! Form::label('follow_up_by', __('crm::lang.follow_up_by') .':') !!}
		                    <select class="form-control" id="follow_up_by">
		                    		<option value="">@lang('messages.please_select')</option>
								  	<optgroup label="@lang('sale.payment_status')">
								    	<option value="all" data-category="payment_status">@lang('lang_v1.all')</option>
								    	<option value="due" data-category="payment_status">@lang('lang_v1.due')</option>
								    	<option value="partial" data-category="payment_status">@lang('lang_v1.partial')</option>
								    	<option value="overdue" data-category="payment_status">@lang('lang_v1.overdue')</option>
								  	</optgroup>
								  	<optgroup label="@lang('restaurant.orders')">
								    	<option value="has_transactions" data-category="orders">
								    		@lang('crm::lang.has_transactions')
								    	</option>
								    	<option value="has_no_transactions" data-category="orders">
								    		@lang('crm::lang.has_no_transactions')
								    	</option>
								  	</optgroup>
								  	<optgroup label="@lang('contact.contact')">
								    	<option value="contact_name" data-category="contact_name">
								    		@lang('user.name')
								    	</option>
								  	</optgroup>
							</select>

							<input type="hidden" name="follow_up_by" id="follow_up_by_category" value="">

		               </div>
		            </div>

		            <div class="col-md-4 hide follow_up_by_order_field">
		               	<div class="form-group">
		                    <label for="in_days">{{__('crm::lang.in_days')}}:*</label>
		                    <div class="input-group">
		                        <div class="input-group-addon">{{__('crm::lang.in')}}</div>
		                            <input type="text" class="form-control input_number" id="in_days" placeholder="@lang('crm::lang.enter_days')" name="in_days" required>
		                        <div class="input-group-addon">{{__('lang_v1.days')}}</div>
		                    </div>
		                </div>
		            </div>
		            <div class="col-md-2 hide follow_up_by_order_field" >
		               <div class="form-group">
		               		<br>
		               		<button type="button" class="btn btn-primary" id="group_customers">@lang('pagination.next')</button>
		               </div>
		            </div>
		            <div class="col-md-6 hide follow_up_by_payment_field" >
		               <div class="form-group">
		                    {!! Form::label('invoices', __('lang_v1.invoices') .':') !!}
		                    <button type="button" class="btn btn-primary btn-xs select-all">
		                        @lang('lang_v1.select_all')
		                    </button>
		                    <button type="button" class="btn btn-primary btn-xs deselect-all">
		                        @lang('lang_v1.deselect_all')
		                    </button>
		                    {!! Form::select('invoices', [], null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'invoices', 'multiple']); !!}
		               </div>
		            </div>
		            <div class="col-md-2 hide follow_up_by_payment_field" >
		               <div class="form-group">
		               		<br>
		               		<button type="button" class="btn btn-primary" id="group_invoices">@lang('pagination.next')</button>
		               </div>
		            </div>
		            <div class="col-md-6 hide follow_up_by_contact_name" >
		            	<div class="form-group">
		                    {!! Form::label('contact_ids', __('lang_v1.customers') .':') !!}
		                    <button type="button" class="btn btn-primary btn-xs select-all">
		                        @lang('lang_v1.select_all')
		                    </button>
		                    <button type="button" class="btn btn-primary btn-xs deselect-all">
		                        @lang('lang_v1.deselect_all')
		                    </button>
		                    {!! Form::select('contact_ids', $customers, null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'contact_ids', 'multiple']); !!}
		               </div>
		            </div>
		            <div class="col-md-2 hide follow_up_by_contact_name" >
		               <div class="form-group">
		               		<br>
		               		<button type="button" class="btn btn-primary" id="group_customers_by_name">@lang('pagination.next')</button>
		               </div>
		            </div>
        		</div>
        	</div>
       	</div>

       	<div class="box box-solid hide hidden_box">
        	<div class="box-body">
        		<div class="row">
        			<div class="col-md-12" id="group_invoices_div">
		            </div>
        		</div>
        	</div>
        </div>
        <div class="box box-solid hide hidden_box">
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
								<span class="text-primary invoice_tags" id="title_invoice_tags">
									{{implode(', ', $followup_tags['invoice'])}}
								</span>
								<span class="text-primary trans_days_tags" id="title_trans_days_tags">
									{{implode(', ', $followup_tags['trans_days'])}}
								</span>
								<span class="text-primary contact_name_tags" id="title_contact_name_tags">
									{{implode(', ', $followup_tags['contact_name'])}}
								</span>
							</p>
		               </div>
		            </div>
		        </div>
		        <div class="row">
		            <div class="col-md-4">
		               <div class="form-group">
		                    {!! Form::label('status', __('sale.status') .':') !!}
		                    {!! Form::select('status', $statuses, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;', 'id' => 'follow_up_create_status']); !!}
		               </div>
		            </div>
		            <div class="col-md-4">
		               <div class="form-group">
		                    {!! Form::label('start_datetime', __('crm::lang.start_datetime') . ':*' )!!}
		                    {!! Form::text('start_datetime', null, ['class' => 'form-control datetimepicker', 'required', 'readonly']) !!}
		               </div>
		            </div>
		            <div class="col-md-4">
		               <div class="form-group">
		                    {!! Form::label('end_datetime', __('crm::lang.end_datetime') . ':*' )!!}
		                    {!! Form::text('end_datetime', null, ['class' => 'form-control datetimepicker', 'required', 'readonly']) !!}
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
								<span class="text-primary invoice_tags">
									{{implode(', ', $followup_tags['invoice'])}}
								</span>
								<span class="text-primary trans_days_tags">
									{{implode(', ', $followup_tags['trans_days'])}}
								</span>
								<span class="text-primary contact_name_tags">
									{{implode(', ', $followup_tags['contact_name'])}}
								</span>
							</p>
		                </div>
		            </div>
		        </div>
		        <div class="row">
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
		                <small class="pull-right text-muted mt-5 mr-8">
		        			@lang('crm::lang.multiple_followups_will_be_created')
		        		</small>
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
	    $('#invoices').change( function(){
	    	$('#group_invoices_div').html('');
	    	$('.hidden_box').addClass('hide');
	    });
		$('#follow_up_by').change( function(){
			$('#group_invoices_div').html('');
			$('.hidden_box').addClass('hide');
			var follow_up_by = $(this).val();
			
			let category = $("#follow_up_by :selected").data('category');
			
			if (category == 'payment_status') {
				$('span.trans_days_tags').hide();
				$('span.contact_name_tags').hide();
				$('span.invoice_tags').show();
				$("input#title").val('Payment followup: ' +$('span#title_invoice_tags').text().trim());
			} else if (category == 'contact_name') {
				$('span.invoice_tags').hide();
				$('span.trans_days_tags').hide();
				$('span.contact_name_tags').show();
				$("input#title").val('Followup: ' + $('span#title_contact_name_tags').text().trim());
			} else if (category == 'orders') {
				$('span.invoice_tags').hide();
				$('span.contact_name_tags').hide();
				$('span.trans_days_tags').show();
				$("input#title").val('Sales followup: ' +$('span#title_trans_days_tags').text().trim());
			}

			$("input#follow_up_by_category").val(category);
			if (follow_up_by !== '') {
				var arr = ['all', 'due', 'partial', 'overdue'];
				if (arr.indexOf(follow_up_by) >= 0) {
					$('.follow_up_by_payment_field').removeClass('hide');
					$('.follow_up_by_order_field').addClass('hide');
					var data = {
						follow_up_by: 'payment_status',
						payment_status: follow_up_by
					}

					$.ajax({
			            url: '{{action("\Modules\Crm\Http\Controllers\ScheduleController@getInvoicesForFollowUp")}}',
			            dataType: 'json',
			            data: data,
			            success: function(data) {
			                $('#invoices').select2('destroy').empty().select2({data: data});
			            },
			        });
				} else if(follow_up_by == 'contact_name') {
					$('.follow_up_by_payment_field').addClass('hide');
					$('.follow_up_by_order_field').addClass('hide');
					$('.follow_up_by_contact_name').removeClass('hide');
				} else {
					$('.follow_up_by_payment_field').addClass('hide');
					$('.follow_up_by_contact_name').addClass('hide');
					$('.follow_up_by_order_field').removeClass('hide');
				}
			}  else {
				$('.follow_up_by_payment_field').addClass('hide');
				$('.follow_up_by_order_field').addClass('hide');
				$('.follow_up_by_contact_name').addClass('hide');
			}
		});
	});

	$(document).on('click', '#group_customers', function(){
		$('#group_invoices_div').html('');
		var days = $('#in_days').val();
		var follow_up_by = $('#follow_up_by').val();

		if (days != '') {
			$('.hidden_box').removeClass('hide');
			$.ajax({
	            url: '{{action("\Modules\Crm\Http\Controllers\ScheduleController@getFollowUpGroups")}}',
	            dataType: 'html',
	            data: {days: days, follow_up_by: follow_up_by},
	            success: function(result) {
	                $('#group_invoices_div').html(result);
	                $('#group_invoices_div').find('.select2').each( function(){
	                	$(this).select2();
	                });
	            },
	        });
		} else {
			$('.hidden_box').addClass('hide');
			alert('{{__("crm::lang.enter_days")}}');
		}
	});

	$(document).on('click', '#group_customers_by_name', function(){
		$('#group_invoices_div').html('');
		var follow_up_by = $('#follow_up_by').val();
		var contact_ids = $('#contact_ids').val();
		if (contact_ids != '') {
			$('.hidden_box').removeClass('hide');
			$.ajax({
	            url: '{{action("\Modules\Crm\Http\Controllers\ScheduleController@getFollowUpGroups")}}',
	            dataType: 'html',
	            data: {contact_ids: contact_ids, follow_up_by: follow_up_by},
	            success: function(result) {
	                $('#group_invoices_div').html(result);
	                $('#group_invoices_div').find('.select2').each( function(){
	                	$(this).select2();
	                });
	            },
	        });
		} else {
			$('.hidden_box').addClass('hide');
		}
	});

	$(document).on('click', '#group_invoices', function(){
		var invoices = $('#invoices').val();
		$('#group_invoices_div').html('');
		if (invoices != '') {
			$('.hidden_box').removeClass('hide');
			$.ajax({
	            url: '{{action("\Modules\Crm\Http\Controllers\ScheduleController@getFollowUpGroups")}}',
	            dataType: 'html',
	            data: {invoices: invoices, follow_up_by: 'payment_status'},
	            success: function(result) {
	                $('#group_invoices_div').html(result);
	                $('#group_invoices_div').find('.select2').each( function(){
	                	$(this).select2();
	                });
	            },
	        });
		} else {
			alert('{{__("crm::lang.select_invoices")}}');
			$('.hidden_box').addClass('hide');
		}
	});

	$(document).on('submit', '#add_advance_schedule', function(){
		if ($("#customer_invoice_table tr").length <= 1) {
			alert('{{__("crm::lang.there_is_no_customer_to_add_follow_up")}}');
			$(this).find('button[type="submit"]').attr('disabled', false);
			return false;
		}
	});

	$(document).on('click', '.remove-follow-up', function(){
		$(this).closest('tr').remove();
	});
</script>
@endsection