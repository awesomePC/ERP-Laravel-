@extends('layouts.app')

@section('title', __('crm::lang.call_log'))

@section('content')
@include('crm::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
   <h1>@lang('crm::lang.call_log')</h1>
</section>

<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('contact_id', __('contact.contact') . ':') !!}
                    {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2', 'id' => 'contact_id', 'placeholder' => __('messages.all')]); !!}
                </div>    
            </div>
            @can('crm.view_all_call_log')
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('user_id', __('crm::lang.call_log_created_by') . ':') !!}
                        {!! Form::select('user_id', $users, null, ['class' => 'form-control select2', 'id' => 'user_id', 'placeholder' => __('messages.all')]); !!}
                    </div>    
                </div>
            @endcan
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('call_log_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('call_log_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
        </div>
    @endcomponent

	@component('components.widget', ['class' => 'box-solid'])
        <table class="table table-bordered table-striped" id="call_logs_table" style="width: 100%;">
            <thead>
                <tr>
                    @if($is_admin)
                        <th><input type="checkbox" id="select-all-row" data-table-id="call_logs_table"></th>
                    @endif
                    <th>@lang('restaurant.start_time')</th>
                    <th>@lang('restaurant.end_time')</th>
                    <th>@lang('crm::lang.call_duration')</th>
                    <th>@lang('crm::lang.call_type')</th>
                    <th>@lang('lang_v1.contact_no')</th>
                    <th>@lang('report.contact')</th>
                    <th>@lang('role.user')</th>
                    <th>@lang('crm::lang.call_log_created_by')</th>
                </tr>
            </thead>
            @if($is_admin)
            <tfoot>
                <tr>
                    <td colspan="9">
                    <div style="display: flex; width: 100%;">
                        {!! Form::open(['url' => action('\Modules\Crm\Http\Controllers\CallLogController@massDestroy'), 'method' => 'post', 'id' => 'mass_delete_form' ]) !!}
                        {!! Form::hidden('selected_rows', null, ['id' => 'selected_rows']); !!}
                        {!! Form::submit(__('lang_v1.delete_selected'), array('class' => 'btn btn-xs btn-danger', 'id' => 'delete-selected')) !!}
                        {!! Form::close() !!}
                    </div>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    @endcomponent
</section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function(){

            $('#call_log_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#call_log_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    call_logs_table.ajax.reload();
                }
            );
            $('#call_log_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#call_log_date_range').val('');
                call_logs_table.ajax.reload();
            });

            call_logs_table = 
            $("#call_logs_table").DataTable({
                @if($is_admin)
                    aaSorting: [[1, 'desc']],
                @endif
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                fixedHeader: false,
                'ajax': {
                    url: "{{action('\Modules\Crm\Http\Controllers\CallLogController@index')}}",
                    data: function(d){
                        d.contact_id = $('#contact_id').val();
                        d.user_id = $('#user_id').val();

                        if ($('#call_log_date_range').val()) {
                            d.start_time = $('#call_log_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            d.end_time = $('#call_log_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        }
                    }
                },
                columns: [
                    @if($is_admin)
                        { data: 'mass_delete',searchable: false, orderable: false  },
                    @endif
                    { data: 'start_time', name: 'start_time' },
                    { data: 'end_time', name: 'end_time' },
                    { data: 'duration', name: 'duration' },
                    { data: 'call_type', name: 'call_type' },
                    { data: 'contact_number', name: 'contact_number' },
                    { data: 'contact_name', name: 'contact_name' },
                    { data: 'user_full_name', name: 'user_full_name' },
                    { data: 'created_user_name', name: 'created_user_name'}
                ],
                createdRow: function( row, data, dataIndex ) {
                    $( row ).find('td:eq(0)').attr('class', 'selectable_td');
                }
            });

            $(document).on('change', '#contact_id, #user_id', function(e){
                call_logs_table.ajax.reload();
            })
        });

        $(document).on('click', '#delete-selected', function(e){
            e.preventDefault();
            var selected_rows = getSelectedRows();
            
            if(selected_rows.length > 0){
                $('input#selected_rows').val(selected_rows);
                swal({
                    title: LANG.sure,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('form#mass_delete_form').submit();
                    }
                });
            } else{
                $('input#selected_rows').val('');
                swal('@lang("lang_v1.no_row_selected")');
            }    
        });
    </script>
@endsection