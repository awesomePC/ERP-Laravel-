@extends('layouts.app')
@section('title', __('crm::lang.follow_ups'))
@section('content')
	@include('crm::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	   <h1>@lang('crm::lang.follow_ups')</h1>
	</section>
	<section class="content no-print">
		@component('components.filters', ['title' => __('report.filters')])
	        <div class="row">
	            <div class="col-md-4">
	                <div class="form-group">
	                    {!! Form::label('contact_id_filter', __('contact.contact') . ':') !!}
	                    {!! Form::select('contact_id_filter', $contacts, null, ['class' => 'form-control select2', 'id' => 'contact_id_filter', 'placeholder' => __('messages.all')]); !!}
	                </div>    
	            </div>
	            @if(auth()->user()->can('crm.access_all_schedule'))
		            <div class="col-md-4">
		                <div class="form-group">
		                    {!! Form::label('assgined_to_filter', __('crm::lang.assgined') . ':') !!}
		                    {!! Form::select('assgined_to_filter', $assigned_to, null, ['class' => 'form-control select2', 'id' => 'assgined_to_filter', 'placeholder' => __('messages.all')]); !!}
		                </div>    
		            </div>
		        @endif
	            <div class="col-md-4">
	                <div class="form-group">
	                    {!! Form::label('status_filter', __('sale.status') . ':') !!}
	                    {!! Form::select('status_filter', $statuses, null, ['class' => 'form-control select2', 'id' => 'status_filter', 'placeholder' => __('messages.all')]); !!}
	                </div>    
	            </div>
	            <div class="clearfix">
	            </div>
	            <div class="col-md-4">
	                <div class="form-group">
	                    {!! Form::label('schedule_type_filter', __('crm::lang.schedule_type') . ':') !!}
	                    {!! Form::select('schedule_type_filter', $follow_up_types, null, ['class' => 'form-control select2', 'id' => 'schedule_type_filter', 'placeholder' => __('messages.all')]); !!}
	                </div>    
	            </div>
	            <div class="col-md-4">
	            	<div class="form-group">
	            		{!! Form::label('follow_up_date_range', __('report.date_range') . ':') !!}
	            		{!! Form::text('follow_up_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
	            	</div>
	            </div>
	            <div class="col-md-4">
	                <div class="form-group">
	                    {!! Form::label('follow_up_by_filter', __('crm::lang.follow_up_by') . ':') !!}
	                    {!! Form::select('follow_up_by_filter', ['payment_status' => __('sale.payment_status'), 'orders' => __('restaurant.orders')], null, ['class' => 'form-control select2', 'id' => 'follow_up_by_filter', 'placeholder' => __('messages.all')]); !!}
	                </div>    
	            </div>
	        </div>
	    @endcomponent
		<div class="row">
			<div class="col-md-12">
				@component('components.widget', ['class' => 'box box-solid', 'title' => __('crm::lang.all_schedules')])
					@slot('tool')
			            <div class="box-tools">
			                <button type="button" class="btn btn-primary btn-add-schedule">
			                <i class="fa fa-plus"></i> @lang('messages.add')</button>

			                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#advance_followup_modal">
			                <i class="fa fa-plus"></i> @lang('crm::lang.add_advance_follow_up')</button>
			            </div>
			            <input type="hidden" name="schedule_create_url" id="schedule_create_url" value="{{action('\Modules\Crm\Http\Controllers\ScheduleController@create')}}">
		        	@endslot
			        <div class="col-sm-12">
			        	<div class="nav-tabs-custom">
			                <ul class="nav nav-tabs">
			                    <li class="active">
			                        <a href="#all_followup_tab" data-toggle="tab" aria-expanded="true"> @lang('crm::lang.follow_ups')</a>
			                    </li>
			                    <li>
			                        <a href="#recur_followup_tab" data-toggle="tab" aria-expanded="true"> @lang('crm::lang.recur_follow_ups')</a>
			                    </li>
			                </ul>
			                <div class="tab-content">
                    			<div class="tab-pane active" id="all_followup_tab">
                    				<div class="table-responsive">
						            	<table class="table table-bordered table-striped" id="follow_up_table" style="width: 100%">
									        <thead>
									            <tr>
									            	<th> @lang('messages.action')</th>
									            	<th>
									            		@lang('contact.contact')
									            	</th>
									            	<th>@lang('crm::lang.start_datetime')</th>
									                <th>@lang('crm::lang.end_datetime')</th>
									                <th>@lang('sale.status')</th>
									                <th>@lang('crm::lang.schedule_type')</th>
									                <th>@lang('lang_v1.assigned_to')</th>
									                <th>
									                	@lang('crm::lang.description')
									                </th>
									                <th>
									                	@lang('crm::lang.additional_info')
									                </th>
									                <th>@lang('crm::lang.title')</th>
									                <th>
									                	@lang('lang_v1.added_by')
									                </th>
									                <th>
									                	@lang('lang_v1.added_on')
									                </th>
									            </tr>
									        </thead>
									        <tbody></tbody>
									        <tfoot>
			                                    <tr class="bg-gray font-17 footer-total text-center">
			                                        <td colspan="4">
			                                            <strong>@lang('sale.total'):</strong>
			                                        </td>
			                                        <td class="footer_follow_up_status_count"></td>
			                                        <td class="footer_follow_up_type_count"></td>
			                                        <td colspan="6"></td>
			                                    </tr>
			                                </tfoot>
									    </table>
						            </div>
                    			</div>
                    			<div class="tab-pane" id="recur_followup_tab">
                    				<div class="table-responsive">
						            	<table class="table table-bordered table-striped" id="recursive_follow_up_table" style="width: 100%">
									        <thead>
									            <tr>
									            	<th> @lang('messages.action')</th>
									                <th>@lang('sale.status')</th>
									                <th>@lang('crm::lang.schedule_type')</th>
									                <th>@lang('crm::lang.follow_up_by')</th>
									                <th>@lang('crm::lang.in_days')</th>
									                <th>@lang('lang_v1.assigned_to')</th>
									                <th>
									                	@lang('crm::lang.description')
									                </th>
									                <th>
									                	@lang('crm::lang.additional_info')
									                </th>
									                <th>@lang('crm::lang.title')</th>
									                <th>
									                	@lang('lang_v1.added_by')
									                </th>
									                <th>
									                	@lang('lang_v1.added_on')
									                </th>
									            </tr>
									        </thead>
									    </table>
						            </div>
                    			</div>
                    		</div>
			            </div>
			        </div>
		    	@endcomponent
			</div>
		</div>
	</section>
	<div class="modal fade schedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade edit_schedule" tabindex="-1" role="dialog"></div>
    @include('crm::schedule.partial.advance_followup_modal')
@endsection
@section('javascript')
	<script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(function () {
			$('#follow_up_date_range').daterangepicker(
		        dateRangeSettings,
		        function (start, end) {
		            $('#follow_up_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
		            follow_up_datatable.ajax.reload();
		        }
		    );
		    $('#follow_up_date_range').on('cancel.daterangepicker', function(ev, picker) {
		        $('#follow_up_date_range').val('');
		        follow_up_datatable.ajax.reload();
		    });

		    follow_up_datatable = $("#follow_up_table").DataTable({
				processing: true,
		        serverSide: true,
		        scrollY: "80vh",
				scrollX: true,
				scrollCollapse: true,
		        ajax: {
		            url: "/crm/follow-ups",
		            data:function(d) {
		            	d.contact_id = $("#contact_id_filter").val();
		            	d.assgined_to = $("#assgined_to_filter").val();
		            	d.status = $("#status_filter").val();
		            	d.schedule_type = $("#schedule_type_filter").val();
		            	d.follow_up_by = $("#follow_up_by_filter").val();
		            	if ($('#follow_up_date_range').val()) {
		            		d.start_date_time = $('#follow_up_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
		            		d.end_date_time = $('#follow_up_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
		            	}
		            }
		        },
		        columnDefs: [
		            {
		                targets: [0, 6, 8],
		                orderable: false,
		                searchable: false,
		            },
		        ],
		        aaSorting: [[2, 'desc']],
		        columns: [
		        	{ data: 'action', name: 'action' },
		        	{ data: 'contact', name: 'contacts.name' },
		        	{ data: 'start_datetime', name: 'start_datetime' },
		            { data: 'end_datetime', name: 'end_datetime' },
		            { data: 'status', name: 'crm_schedules.status' },
		            { data: 'schedule_type', name: 'schedule_type' },
		            { data: 'users', name: 'users' },
		            { data: 'description', name: 'description'},
		            { data: 'additional_info', name: 'additional_info' },
		            { data: 'title', name: 'title' },
		            { data: 'added_by', name: 'added_by' },
		            { data: 'added_on', name: 'crm_schedules.created_at' },
		        ],
		        "fnDrawCallback": function( oSettings ) {
		        	__show_date_diff_for_human($("#follow_up_table"));
			    },
		        "footerCallback": function ( row, data, start, end, display ) {
		        	$('.footer_follow_up_status_count').html(__count_status(data, 'status'));
		            $('.footer_follow_up_type_count').html(__count_status(data, 'schedule_type'));
		        }
			});

			recursive_follow_up_table = $("#recursive_follow_up_table").DataTable({
				processing: true,
		        serverSide: true,
		        scrollY: "80vh",
				scrollX: true,
				scrollCollapse: true,
		        ajax: {
		            url: "/crm/follow-ups",
		            data:function(d) {
		            	d.assgined_to = $("#assgined_to_filter").val();
		            	d.is_recursive = 1;
		            }
		        },
		        columnDefs: [
		            {
		                targets: [0, 5],
		                orderable: false,
		                searchable: false,
		            },
		        ],
		        aaSorting: [[2, 'desc']],
		        columns: [
		        	{ data: 'action', name: 'action' },
		            { data: 'status', name: 'crm_schedules.status' },
		            { data: 'schedule_type', name: 'schedule_type' },
		            { data: 'follow_up_by', name: 'crm_schedules.follow_up_by' },
		            { data: 'recursion_days', name: 'crm_schedules.recursion_days' },
		            { data: 'users', name: 'users' },
		            { data: 'description', name: 'description'},
		            { data: 'additional_info', name: 'additional_info' },
		            { data: 'title', name: 'title' },
		            { data: 'added_by', name: 'added_by' },
		            { data: 'added_on', name: 'crm_schedules.created_at' },
		        ]
			});

			$(document).on('change', '#contact_id_filter, #assgined_to_filter, #status_filter, #schedule_type_filter, #follow_up_by_filter', function() {
			    follow_up_datatable.ajax.reload();
			});
		});
	</script>
@endsection