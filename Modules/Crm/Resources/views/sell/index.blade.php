@extends('crm::layouts.app')

@section('title', __('lang_v1.all_sales'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
   <h1>@lang( 'sale.sells')</h1>
</section>
<!-- Main content -->
<section class="content no-print">
    @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('payment_status_filter',  __('purchase.payment_status') . ':') !!}
                {!! Form::select('payment_status_filter', ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial'), 'overdue' => __('lang_v1.overdue')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('date_range_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
    @endcomponent
	@component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.all_sales')])
        <div class="table-responsive">
            <table class="table table-bordered table-striped ajax_view" id="contact_sell_table">
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('messages.date')</th>
                        <th>@lang('sale.invoice_no')</th>
                        <th>@lang('sale.payment_status')</th>
                        <th>@lang('lang_v1.payment_method')</th>
                        <th>@lang('sale.total_amount')</th>
                        <th>@lang('sale.total_paid')</th>
                        <th>@lang('lang_v1.sell_due')</th>
                        <th>@lang('lang_v1.sell_return_due')</th>
                        <th>@lang('lang_v1.shipping_status')</th>
                        <th>@lang('lang_v1.total_items')</th>
                        <th>@lang('lang_v1.types_of_service')</th>
                        <th>@lang('lang_v1.service_custom_field_1' )</th>
                        <th>@lang('lang_v1.added_by')</th>
                        <th>@lang('sale.sell_note')</th>
                        <th>@lang('sale.staff_note')</th>
                        <th>@lang('sale.shipping_details')</th>
                        <th>@lang('restaurant.table')</th>
                        <th>@lang('restaurant.service_staff')</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="3"><strong>@lang('sale.total'):</strong></td>
                        <td id="footer_payment_status_count"></td>
                        <td id="payment_method_count"></td>
                        <td><span class="display_currency" id="footer_sale_total" data-currency_symbol ="true"></span></td>
                        <td><span class="display_currency" id="footer_total_paid" data-currency_symbol ="true"></span></td>
                        <td><span class="display_currency" id="footer_total_remaining" data-currency_symbol ="true"></span></td>
                        <td><span class="display_currency" id="footer_total_sell_return_due" data-currency_symbol ="true"></span></td>
                        <td colspan="2"></td>
                        <td id="service_type_count"></td>
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endcomponent
</section>
@endsection
@section('javascript')
<script type="text/javascript">
	$(document).ready(function() {

        $('#date_range_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#date_range_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                contact_sell_datatable.ajax.reload();
            }
        );

        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#date_range_filter').val('');
            contact_sell_datatable.ajax.reload();
        });

		contact_sell_datatable = $("#contact_sell_table").DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            "ajax": {
                "url": "/contact/contact-sells",
                "data": function ( d ) {
                    if($('#date_range_filter').val()) {
                        var start = $('#date_range_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#date_range_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                    d.payment_status = $('#payment_status_filter').val();
                    d = __datatable_ajax_callback(d);
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, "searchable": false},
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'payment_methods', orderable: false, "searchable": false},
                { data: 'final_total', name: 'final_total'},
                { data: 'total_paid', name: 'total_paid', "searchable": false},
                { data: 'total_remaining', name: 'total_remaining'},
                { data: 'return_due', orderable: false, "searchable": false},
                { data: 'shipping_status', name: 'shipping_status'},
                { data: 'total_items', name: 'total_items', "searchable": false},
                { data: 'types_of_service_name', name: 'tos.name', @if(empty($is_types_service_enabled)) visible: false @endif},
                { data: 'service_custom_field_1', name: 'service_custom_field_1', @if(empty($is_types_service_enabled)) visible: false @endif},
                { data: 'added_by', name: 'u.first_name'},
                { data: 'additional_notes', name: 'additional_notes'},
                { data: 'staff_note', name: 'staff_note'},
                { data: 'shipping_details', name: 'shipping_details'},
                { data: 'table_name', name: 'tables.name', @if(empty($is_tables_enabled)) visible: false @endif },
                { data: 'waiter', name: 'ss.first_name', @if(empty($is_service_staff_enabled)) visible: false @endif },
            ],
            "fnDrawCallback": function (oSettings) {

                $('#footer_sale_total').text(sum_table_col($('#contact_sell_table'), 'final-total'));
                
                $('#footer_total_paid').text(sum_table_col($('#contact_sell_table'), 'total-paid'));

                $('#footer_total_remaining').text(sum_table_col($('#contact_sell_table'), 'payment_due'));

                $('#footer_total_sell_return_due').text(sum_table_col($('#contact_sell_table'), 'sell_return_due'));

                $('#footer_payment_status_count').html(__sum_status_html($('#contact_sell_table'), 'payment-status-label'));

                $('#service_type_count').html(__sum_status_html($('#contact_sell_table'), 'service-type-label'));
                $('#payment_method_count').html(__sum_status_html($('#contact_sell_table'), 'payment-method'));

                __currency_convert_recursively($('#contact_sell_table'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(3)').attr('class', 'clickable_td');
            }
        });

        $(document).on('change', '#payment_status_filter',  function() {
            contact_sell_datatable.ajax.reload();
        });

	});
</script>
@endsection