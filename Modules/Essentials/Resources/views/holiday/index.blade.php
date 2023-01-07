@extends('layouts.app')
@section('title', __('essentials::lang.holiday'))

@section('content')
@include('essentials::layouts.nav_hrm')
<section class="content-header">
    <h1>@lang('essentials::lang.holiday')
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters'), 'class' => 'box-solid'])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}

                    {!! Form::select('location_id', $locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('holiday_filter_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('holiday_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
        @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid', 'title' => __( 'essentials::lang.all_holidays' )])
                @if($is_admin)
                @slot('tool')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal" data-href="{{action('\Modules\Essentials\Http\Controllers\EssentialsHolidayController@create')}}" data-container="#add_holiday_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endslot
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="holidays_table">
                        <thead>
                            <tr>
                                <th>@lang( 'lang_v1.name' )</th>
                                <th>@lang( 'lang_v1.date' )</th>
                                <th>@lang( 'business.business_location' )</th>
                                <th>@lang( 'brand.note' )</th>
                                @if($is_admin)
                                    <th>@lang( 'messages.action' )</th>
                                @endif
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade" id="add_holiday_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            holidays_table = $('#holidays_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{action('\Modules\Essentials\Http\Controllers\EssentialsHolidayController@index')}}",
                    "data" : function(d) {
                        d.location_id = $('#location_id').val();
                        if($('#holiday_filter_date_range').val()) {
                            var start = $('#holiday_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#holiday_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                    }
                },
                @if($is_admin)
                columnDefs: [
                    {
                        targets: 4,
                        orderable: false,
                        searchable: false,
                    },
                ],
                @endif
                columns: [
                    { data: 'name', name: 'essentials_holidays.name' },
                    { data: 'start_date', name: 'start_date'},
                    { data: 'location', name: 'bl.name' },
                    { data: 'note', name: 'note'},
                    @if($is_admin)
                    { data: 'action', name: 'action' },
                    @endif
                ],
            });

            $('#holiday_filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#holiday_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                }
            );
            $('#holiday_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#holiday_filter_date_range').val('');
                holidays_table.ajax.reload();
            });

            $(document).on( 'change', '#holiday_filter_date_range, #location_id', function() {
                holidays_table.ajax.reload();
            });

            $('#add_holiday_modal').on('shown.bs.modal', function(e) {
                $('#add_holiday_modal .select2').select2();

                $('form#add_holiday_form #start_date, form#add_holiday_form #end_date').datepicker({
                    autoclose: true,
                });
            });

            $(document).on('submit', 'form#add_holiday_form', function(e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();

                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $('div#add_holiday_modal').modal('hide');
                            toastr.success(result.msg);
                            holidays_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });

        $(document).on('click', 'button.delete-holiday', function() {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                holidays_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
    </script>
@endsection
