<div class="tab-pane" id="ouput-tax-project-invoice">
	<table class="table table-bordered table-striped" id="output_tax_project_invoice_table" width="100%">
        <thead>
            <tr>
                <th>@lang('messages.date')</th>
                <th>@lang('sale.invoice_no')</th>
                <th>@lang('contact.customer')</th>
                <th>@lang('contact.tax_no')</th>
                <th>@lang('sale.total_amount')</th>
                <th>@lang('receipt.discount')</th>
                @foreach($taxes as $tax)
                    <th>
                        {{$tax['name']}}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tfoot>
            <tr class="bg-gray font-17 text-center footer-total">
                <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                <td><span class="display_currency" id="project_invoice_total" data-currency_symbol ="true"></span></td>
                <td>&nbsp;</td>
                @foreach($taxes as $tax)
                    <td>
                        <span class="display_currency" id="total_output_pi_{{$tax['id']}}" data-currency_symbol ="true"></span>
                    </td>
                @endforeach
            </tr>
        </tfoot>
    </table>
</div>