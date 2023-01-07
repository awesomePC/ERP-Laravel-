@extends('layouts.app')

@section('title', __('report.reports'))

@section('content')
@include('crm::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
   <h1>@lang('report.reports')</h1>
</section>

<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
        	@component('components.widget', ['class' => 'box-solid', 'title' => __('crm::lang.follow_ups_by_user')])
                <table class="table table-bordered table-striped" id="follow_ups_by_user_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('role.user')</th>
                            @foreach($statuses as $key => $value)
                                <th>
                                    {{$value}}
                                </th>
                            @endforeach
                            <th>
                                @lang('lang_v1.others')
                            </th>
                            <th>
                                @lang('crm::lang.total_follow_ups')
                            </th>
                        </tr>
                    </thead>
                </table>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid', 'title' => __('crm::lang.follow_ups_by_contacts')])
                <table class="table table-bordered table-striped" id="follow_ups_by_contact_table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('contact.contact')</th>
                            @foreach($statuses as $key => $value)
                                <th>
                                    {{$value}}
                                </th>
                            @endforeach
                            <th>
                                @lang('lang_v1.others')
                            </th>
                            <th>
                                @lang('crm::lang.total_follow_ups')
                            </th>
                        </tr>
                    </thead>
                </table>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid', 'title' => __('crm::lang.lead_to_customer_conversion')])
                <table class="table table-bordered table-striped" id="lead_to_customer_conversion" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>@lang('crm::lang.converted_by')</th>
                            <th>@lang('sale.total')</th>
                        </tr>
                    </thead>
                </table>
            @endcomponent
        </div>
    </div>
</section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function(){
            var follow_ups_by_user_table = 
            $("#follow_ups_by_user_table").DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                fixedHeader: false,
                'ajax': {
                    url: "{{action('\Modules\Crm\Http\Controllers\ReportController@followUpsByUser')}}"
                },
                columns: [
                    { data: 'full_name', name: 'full_name' },
                    @foreach($statuses as $key => $value)
                        { data: 'count_{{$key}}', searchable: false },
                    @endforeach
                    { data: 'count_nulled', searchable: false },
                    { data: 'total_follow_ups', searchable: false }
                ],
            });

            var follow_ups_by_contact_table = 
            $("#follow_ups_by_contact_table").DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                fixedHeader: false,
                'ajax': {
                    url: "{{action('\Modules\Crm\Http\Controllers\ReportController@followUpsContact')}}"
                },
                columns: [
                    { data: 'contact_name', name: 'contact_name' },
                    @foreach($statuses as $key => $value)
                        { data: 'count_{{$key}}', searchable: false },
                    @endforeach
                    { data: 'count_nulled', searchable: false },
                    { data: 'total_follow_ups', searchable: false }
                ],
            });

            var lead_to_customer_conversion = 
            $("#lead_to_customer_conversion").DataTable({
                processing: true,
                serverSide: true,
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                fixedHeader: false,
                aaSorting: [[1, 'desc']],
                'ajax': {
                    url: "{{action('\Modules\Crm\Http\Controllers\ReportController@leadToCustomerConversion')}}"
                },
                columns: [
                    {
                        orderable: false,
                        searchable: false,
                        data: null,
                        defaultContent: '',
                    },
                    { data: 'full_name', name: 'full_name' },
                    { data: 'total_conversions', searchable: false }
                ],
                createdRow: function(row, data, dataIndex) {
                    $(row).find('td:eq(0)')
                        .addClass('details-control');
                },
            });

            // Array to track the ids of the details displayed rows
            var ltc_detail_rows = [];

            $('#lead_to_customer_conversion tbody').on('click', 'tr td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = lead_to_customer_conversion.row(tr);
                var idx = $.inArray(tr.attr('id'), ltc_detail_rows);

                if (row.child.isShown()) {
                    tr.removeClass('details');
                    row.child.hide();

                    // Remove from the 'open' array
                    ltc_detail_rows.splice(idx, 1);
                } else {
                    tr.addClass('details');

                    row.child(show_lead_to_customer_details(row.data())).show();

                    // Add to the 'open' array
                    if (idx === -1) {
                        ltc_detail_rows.push(tr.attr('id'));
                    }
                }
            });

            // On each draw, loop over the `detailRows` array and show any child rows
            lead_to_customer_conversion.on('draw', function() {
                $.each(ltc_detail_rows, function(i, id) {
                    $('#' + id + ' td.details-control').trigger('click');
                });
            });

            function show_lead_to_customer_details(rowData) {
                var div = $('<div/>')
                    .addClass('loading')
                    .text('Loading...');
                $.ajax({
                    url: '/crm/lead-to-customer-details/' + rowData.DT_RowId,
                    dataType: 'html',
                    success: function(data) {
                        div.html(data).removeClass('loading');
                    },
                });

                return div;
            }
        });
    </script>
@endsection