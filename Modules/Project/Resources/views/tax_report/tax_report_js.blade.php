<script type="text/javascript">
	$(document).ready(function(){
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($(e.target).attr('href') == '#ouput-tax-project-invoice') {
                if (typeof (output_tax_project_invoice_datatable) == 'undefined') {
                    output_tax_project_invoice_datatable = $('#output_tax_project_invoice_table').DataTable({
                        processing: true,
                        serverSide: true,
                        aaSorting: [[0, 'desc']],
                        ajax: {
                            url: '/project/project-invoice-tax-report',
                            data: function(d) {
                                var start = $('input#tax_report_date_range')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                var end = $('input#tax_report_date_range')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                                d.start_date = start;
                                d.end_date = end;
                            }
                        },
                        columns: [
                            { data: 'transaction_date', name: 'transaction_date' },
                            { data: 'invoice_no', name: 'invoice_no' },
                            { data: 'contact_name', name: 'c.name' },
                            { data: 'tax_number', name: 'c.tax_number' },
                            { data: 'total_before_tax', name: 'total_before_tax' },
                            { data: 'discount_amount', name: 'discount_amount' },
                            @foreach($taxes as $tax)
                            { data: "tax_{{$tax['id']}}", searchable: false, orderable: false },
                            @endforeach
                        ],
                        fnDrawCallback: function(oSettings) {
                            $('#project_invoice_total').text(
                                sum_table_col($('#output_tax_project_invoice_table'), 'total_before_tax')
                            );
                            @foreach($taxes as $tax)
                                $("#total_output_pi_{{$tax['id']}}").text(
                                    sum_table_col($('#output_tax_project_invoice_table'), "tax_{{$tax['id']}}")
                                );
                            @endforeach
                            __currency_convert_recursively($('#output_tax_project_invoice_table'));
                        },
                    });
                } else {
                	output_tax_project_invoice_datatable.ajax.reload();
                }
            }
        });

        $('#tax_report_date_range').change( function(){
            if ($("#ouput-tax-project-invoice").hasClass('active')) {
                output_tax_project_invoice_datatable.ajax.reload();
            }
        });
	});
</script>