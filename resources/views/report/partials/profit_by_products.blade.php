<div class="table-responsive">
    <table class="table table-bordered table-striped" id="profit_by_products_table">
        <thead>
            <tr>
                <th>@lang('sale.product')</th>
                <th>@lang('lang_v1.gross_profit')</th>
            </tr>
        </thead>
        <tfoot>
            <tr class="bg-gray font-17 footer-total">
                <td><strong>@lang('sale.total'):</strong></td>
                <td class="footer_total"></td>
            </tr>
        </tfoot>
    </table>

    <p class="text-muted">
        @lang('lang_v1.profit_note')
    </p>
</div>