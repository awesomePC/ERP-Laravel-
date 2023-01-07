@php
    $sub_total_amount = 0;
@endphp

<!-- business information here -->
<div class="col-12 print_area">
    {{-- watermark --}}
    @if (!empty($receipt_details->watermark))
        <div class="watermark">
            <img src="{{ asset('uploads/invoice_watermarks/' . $receipt_details->watermark) }}">
        </div>
    @endif

    <div class="row ms-margin">

        <!-- Logo -->
        @if (!empty($receipt_details->logo))
            <img style="max-height: 120px; width: auto;" src="{{ $receipt_details->logo }}"
                class="img img-responsive center-block">
        @endif

        <!-- Header text -->
        @if (!empty($receipt_details->header_text))
            <div class="col-xs-12">
                {!! $receipt_details->header_text !!}
            </div>
        @endif

        <!-- business information here -->
        <div class="col-xs-12 text-center">
            <h2 class="text-center">
                <!-- Shop & Location Name  -->
                @if (!empty($receipt_details->display_name))
                    {{ $receipt_details->display_name }}
                @endif
            </h2>

            <!-- Address -->
            <p>
                @if (!empty($receipt_details->address))
                    <small class="text-center">
                        {!! $receipt_details->address !!}
                    </small>
                @endif
                @if (!empty($receipt_details->contact))
                    <br />{!! $receipt_details->contact !!}
                @endif
                @if (!empty($receipt_details->contact) && !empty($receipt_details->website))
                    ,
                @endif
                @if (!empty($receipt_details->website))
                    {{ $receipt_details->website }}
                @endif
                @if (!empty($receipt_details->location_custom_fields))
                    <br>{{ $receipt_details->location_custom_fields }}
                @endif
            </p>
            <p>
                @if (!empty($receipt_details->sub_heading_line1))
                    {{ $receipt_details->sub_heading_line1 }}
                @endif
                @if (!empty($receipt_details->sub_heading_line2))
                    <br>{{ $receipt_details->sub_heading_line2 }}
                @endif
                @if (!empty($receipt_details->sub_heading_line3))
                    <br>{{ $receipt_details->sub_heading_line3 }}
                @endif
                @if (!empty($receipt_details->sub_heading_line4))
                    <br>{{ $receipt_details->sub_heading_line4 }}
                @endif
                @if (!empty($receipt_details->sub_heading_line5))
                    <br>{{ $receipt_details->sub_heading_line5 }}
                @endif
            </p>
            <p>
                @if (!empty($receipt_details->tax_info1))
                    <b>{{ $receipt_details->tax_label1 }}</b> {{ $receipt_details->tax_info1 }}
                @endif

                @if (!empty($receipt_details->tax_info2))
                    <b>{{ $receipt_details->tax_label2 }}</b> {{ $receipt_details->tax_info2 }}
                @endif
            </p>

            <!-- Title of receipt -->
            @if (!empty($receipt_details->invoice_heading))
                <h3 class="text-center">
                    {!! $receipt_details->invoice_heading !!}
                </h3>
            @endif

            <!-- Invoice  number, Date  -->
            <p style="width: 100% !important" class="word-wrap">
                <span class="pull-left text-left word-wrap">
                    @if (!empty($receipt_details->invoice_no_prefix))
                        <b>{!! $receipt_details->invoice_no_prefix !!}</b>
                    @endif
                    {{ $receipt_details->invoice_no }}

                    @if (!empty($receipt_details->types_of_service))
                        <br />
                        <span class="pull-left text-left">
                            <strong>{!! $receipt_details->types_of_service_label !!}:</strong>
                            {{ $receipt_details->types_of_service }}
                            <!-- Waiter info -->
                            @if (!empty($receipt_details->types_of_service_custom_fields))
                                @foreach ($receipt_details->types_of_service_custom_fields as $key => $value)
                                    <br><strong>{{ $key }}: </strong> {{ $value }}
                                @endforeach
                            @endif
                        </span>
                    @endif

                    <!-- Table information-->
                    @if (!empty($receipt_details->table_label) || !empty($receipt_details->table))
                        <br />
                        <span class="pull-left text-left">
                            @if (!empty($receipt_details->table_label))
                                <b>{!! $receipt_details->table_label !!}</b>
                            @endif
                            {{ $receipt_details->table }}

                            <!-- Waiter info -->
                        </span>
                    @endif

                    <!-- customer info -->
                    @if (!empty($receipt_details->customer_info))
                        <br />
                        <b>{{ $receipt_details->customer_label }}</b> <br> {!! $receipt_details->customer_info !!} <br>
                    @endif
                    @if (!empty($receipt_details->client_id_label))
                        <br />
                        <b>{{ $receipt_details->client_id_label }}</b> {{ $receipt_details->client_id }}
                    @endif
                    @if (!empty($receipt_details->customer_tax_label))
                        <br />
                        <b>{{ $receipt_details->customer_tax_label }}</b> {{ $receipt_details->customer_tax_number }}
                    @endif
                    @if (!empty($receipt_details->customer_custom_fields))
                        <br />{!! $receipt_details->customer_custom_fields !!}
                    @endif
                    @if (!empty($receipt_details->commission_agent_label))
                        <br />
                        <strong>{{ $receipt_details->commission_agent_label }}</strong>
                        {{ $receipt_details->commission_agent }}
                    @endif
                    @if (!empty($receipt_details->customer_rp_label))
                        <br />
                        <strong>{{ $receipt_details->customer_rp_label }}</strong>
                        {{ $receipt_details->customer_total_rp }}
                    @endif
                </span>

                <span class="pull-right text-left">
                    <b>{{ $receipt_details->date_label }}</b> {{ $receipt_details->invoice_date }}

                    @if (!empty($receipt_details->due_date_label))
                        <br><b>{{ $receipt_details->due_date_label }}</b> {{ $receipt_details->due_date ?? '' }}
                    @endif

                    @if (!empty($receipt_details->brand_label) || !empty($receipt_details->repair_brand))
                        <br>
                        @if (!empty($receipt_details->brand_label))
                            <b>{!! $receipt_details->brand_label !!}</b>
                        @endif
                        {{ $receipt_details->repair_brand }}
                    @endif


                    @if (!empty($receipt_details->device_label) || !empty($receipt_details->repair_device))
                        <br>
                        @if (!empty($receipt_details->device_label))
                            <b>{!! $receipt_details->device_label !!}</b>
                        @endif
                        {{ $receipt_details->repair_device }}
                    @endif

                    @if (!empty($receipt_details->model_no_label) || !empty($receipt_details->repair_model_no))
                        <br>
                        @if (!empty($receipt_details->model_no_label))
                            <b>{!! $receipt_details->model_no_label !!}</b>
                        @endif
                        {{ $receipt_details->repair_model_no }}
                    @endif

                    @if (!empty($receipt_details->serial_no_label) || !empty($receipt_details->repair_serial_no))
                        <br>
                        @if (!empty($receipt_details->serial_no_label))
                            <b>{!! $receipt_details->serial_no_label !!}</b>
                        @endif
                        {{ $receipt_details->repair_serial_no }}<br>
                    @endif
                    @if (!empty($receipt_details->repair_status_label) || !empty($receipt_details->repair_status))
                        @if (!empty($receipt_details->repair_status_label))
                            <b>{!! $receipt_details->repair_status_label !!}</b>
                        @endif
                        {{ $receipt_details->repair_status }}<br>
                    @endif

                    @if (!empty($receipt_details->repair_warranty_label) || !empty($receipt_details->repair_warranty))
                        @if (!empty($receipt_details->repair_warranty_label))
                            <b>{!! $receipt_details->repair_warranty_label !!}</b>
                        @endif
                        {{ $receipt_details->repair_warranty }}
                        <br>
                    @endif

                    <!-- Waiter info -->
                    @if (!empty($receipt_details->service_staff_label) || !empty($receipt_details->service_staff))
                        <br />
                        @if (!empty($receipt_details->service_staff_label))
                            <b>{!! $receipt_details->service_staff_label !!}</b>
                        @endif
                        {{ $receipt_details->service_staff }}
                    @endif
                    @if (!empty($receipt_details->shipping_custom_field_1_label))
                        <br><strong>{!! $receipt_details->shipping_custom_field_1_label !!} :</strong> {!! $receipt_details->shipping_custom_field_1_value ?? '' !!}
                    @endif

                    @if (!empty($receipt_details->shipping_custom_field_2_label))
                        <br><strong>{!! $receipt_details->shipping_custom_field_2_label !!}:</strong> {!! $receipt_details->shipping_custom_field_2_value ?? '' !!}
                    @endif

                    @if (!empty($receipt_details->shipping_custom_field_3_label))
                        <br><strong>{!! $receipt_details->shipping_custom_field_3_label !!}:</strong> {!! $receipt_details->shipping_custom_field_3_value ?? '' !!}
                    @endif

                    @if (!empty($receipt_details->shipping_custom_field_4_label))
                        <br><strong>{!! $receipt_details->shipping_custom_field_4_label !!}:</strong> {!! $receipt_details->shipping_custom_field_4_value ?? '' !!}
                    @endif

                    @if (!empty($receipt_details->shipping_custom_field_5_label))
                        <br><strong>{!! $receipt_details->shipping_custom_field_2_label !!}:</strong> {!! $receipt_details->shipping_custom_field_5_value ?? '' !!}
                    @endif
                    {{-- sale order --}}
                    @if (!empty($receipt_details->sale_orders_invoice_no))
                        <br>
                        <strong>@lang('restaurant.order_no'):</strong> {!! $receipt_details->sale_orders_invoice_no ?? '' !!}
                    @endif

                    @if (!empty($receipt_details->sale_orders_invoice_date))
                        <br>
                        <strong>@lang('lang_v1.order_dates'):</strong> {!! $receipt_details->sale_orders_invoice_date ?? '' !!}
                    @endif
                    @if (!empty($receipt_details->sales_person_label))
                        <br />
                        <b>{{ $receipt_details->sales_person_label }}</b> {{ $receipt_details->sales_person }}
                    @endif
                </span>
            </p>
        </div>
    </div>

    <div class="row ms-margin">
        @includeIf('sale_pos.receipts.partial.common_repair_invoice')
    </div>

    <div class="row ms-margin">
        <div class="col-xs-12">
            <br />
            @php
                $p_width = 45;
            @endphp
            @if (!empty($receipt_details->item_discount_label))
                @php
                    $p_width -= 10;
                @endphp
            @endif
            @if (!empty($receipt_details->discounted_unit_price_label))
                @php
                    $p_width -= 10;
                @endphp
            @endif
            <table class="table table-responsive table-slim">
                <thead>
                    <tr>
                        <th width="{{ $p_width }}%">{{ $receipt_details->table_product_label }}</th>
                        <th class="text-right" width="15%">{{ $receipt_details->table_qty_label }}</th>
                        <th class="text-right" width="15%">{{ $receipt_details->table_unit_price_label }}</th>
                        {{-- @if (!empty($receipt_details->discounted_unit_price_label))
                            <th class="text-right" width="10%">{{ $receipt_details->discounted_unit_price_label }}
                            </th>
                        @endif
                        @if (!empty($receipt_details->item_discount_label))
                            <th class="text-right" width="10%">{{ $receipt_details->item_discount_label }}</th>
                        @endif --}}
                        <th class="text-right" width="15%">{{ $receipt_details->table_subtotal_label }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipt_details->lines as $line)
                        <tr>
                            <td>
                                @if (!empty($line['image']))
                                    <img src="{{ $line['image'] }}" alt="Image" width="50"
                                        style="float: left; margin-right: 8px;">
                                @endif
                                {{ $line['name'] }} {{ $line['product_variation'] }} {{ $line['variation'] }}
                                @if (!empty($line['sub_sku']))
                                    , {{ $line['sub_sku'] }}
                                    @endif @if (!empty($line['brand']))
                                        , {{ $line['brand'] }}
                                        @endif @if (!empty($line['cat_code']))
                                            , {{ $line['cat_code'] }}
                                        @endif
                                        @if (!empty($line['product_custom_fields']))
                                            , {{ $line['product_custom_fields'] }}
                                        @endif
                                        @if (!empty($line['sell_line_note']))
                                            <br>
                                            <small>
                                                {!! $line['sell_line_note'] !!}
                                            </small>
                                        @endif
                                        @if (!empty($line['lot_number']))
                                            <br> {{ $line['lot_number_label'] }}: {{ $line['lot_number'] }}
                                        @endif
                                        @if (!empty($line['product_expiry']))
                                            , {{ $line['product_expiry_label'] }}: {{ $line['product_expiry'] }}
                                        @endif

                                        @if (!empty($line['warranty_name']))
                                            <br><small>{{ $line['warranty_name'] }} </small>
                                            @endif @if (!empty($line['warranty_exp_date']))
                                                <small>- {{ @format_date($line['warranty_exp_date']) }} </small>
                                            @endif
                                            @if (!empty($line['warranty_description']))
                                                <small> {{ $line['warranty_description'] ?? '' }}</small>
                                            @endif

                                            @if ($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
                                                <br><small>
                                                    1 {{ $line['units'] }} = {{ $line['base_unit_multiplier'] }}
                                                    {{ $line['base_unit_name'] }} <br>
                                                    {{ $line['unit_price_inc_tax'] }} x {{ $line['quantity'] }} =
                                                    {{ $line['line_total'] }}
                                                </small>
                                            @endif
                            </td>
                            <td class="text-right">
                                {{ $line['quantity'] }} {{ $line['units'] }}

                                @if ($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
                                    <br><small>
                                        {{ $line['quantity'] }} x {{ $line['base_unit_multiplier'] }} =
                                        {{ $line['orig_quantity'] }} {{ $line['base_unit_name'] }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-right">{{ $line['unit_price_before_discount'] }}</td>
                            {{-- @if (!empty($receipt_details->discounted_unit_price_label))
                                <td class="text-right">
                                    {{ $line['unit_price_inc_tax'] }}
                                </td>
                            @endif --}}
                            {{-- @if (!empty($receipt_details->item_discount_label))
                                <td class="text-right">
                                    {{ $line['total_line_discount'] ?? '0.00' }}

                                    @if (!empty($line['line_discount_percent']))
                                        ({{ $line['line_discount_percent'] }}%)
                                    @endif
                                </td>
                            @endif --}}
                            @php
                                $sub_total = $line['unit_price_before_discount_uf'] * $line['quantity_uf'];
                                $sub_total_amount += $sub_total;
                            @endphp
                            <td class="text-right">{{ number_format($sub_total, 2) }}</td>
                        </tr>
                        @if (!empty($line['modifiers']))
                            @foreach ($line['modifiers'] as $modifier)
                                <tr>
                                    <td>
                                        {{ $modifier['name'] }} {{ $modifier['variation'] }}
                                        @if (!empty($modifier['sub_sku']))
                                            , {{ $modifier['sub_sku'] }}
                                            @endif @if (!empty($modifier['cat_code']))
                                                , {{ $modifier['cat_code'] }}
                                            @endif
                                            @if (!empty($modifier['sell_line_note']))
                                                ({!! $modifier['sell_line_note'] !!})
                                            @endif
                                    </td>
                                    <td class="text-right">{{ $modifier['quantity'] }} {{ $modifier['units'] }} </td>
                                    <td class="text-right">{{ $modifier['unit_price_inc_tax'] }}</td>
                                    @if (!empty($receipt_details->discounted_unit_price_label))
                                        <td class="text-right">{{ $modifier['unit_price_exc_tax'] }}</td>
                                    @endif
                                    @if (!empty($receipt_details->item_discount_label))
                                        <td class="text-right">0.00</td>
                                    @endif
                                    <td class="text-right">
                                        {{ $modifier['line_total'] }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @empty
                        <tr>
                            <td colspan="4">&nbsp;</td>
                            @if (!empty($receipt_details->discounted_unit_price_label))
                                <td></td>
                            @endif
                            @if (!empty($receipt_details->item_discount_label))
                                <td></td>
                            @endif
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row ms-margin">
        <div class="col-md-12">
            <hr />
        </div>
        <div class="col-xs-6">

            <table class="table table-slim">

                @if (!empty($receipt_details->payments))
                    @foreach ($receipt_details->payments as $payment)
                        <tr>
                            <td>{{ $payment['method'] }}</td>
                            <td class="text-right">{{ $payment['amount'] }}</td>
                            <td class="text-right">{{ $payment['date'] }}</td>
                        </tr>
                    @endforeach
                @endif

                <!-- Total Paid-->
                @if (!empty($receipt_details->total_paid))
                    <tr>
                        <th>
                            {!! $receipt_details->total_paid_label !!}
                        </th>
                        <td class="text-right">
                            {{ $receipt_details->total_paid }}
                        </td>
                    </tr>
                @endif

                <!-- Total Due-->
                @if (!empty($receipt_details->total_due) && !empty($receipt_details->total_due_label))
                    <tr>
                        <th>
                            {!! $receipt_details->total_due_label !!}
                        </th>
                        <td class="text-right">
                            {{ $receipt_details->total_due }}
                        </td>
                    </tr>
                @endif

                @if (!empty($receipt_details->all_due))
                    <tr>
                        <th>
                            {!! $receipt_details->all_bal_label !!}
                        </th>
                        <td class="text-right">
                            {{ $receipt_details->all_due }}
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="col-xs-6">
            <div class="table-responsive">
                <table class="table table-slim">
                    <tbody>
                        @if (!empty($receipt_details->total_quantity_label))
                            <tr>
                                <th style="width:70%">
                                    {!! $receipt_details->total_quantity_label !!}
                                </th>
                                <td class="text-right">
                                    {{ $receipt_details->total_quantity }}
                                </td>
                            </tr>
                        @endif

                        @if (!empty($receipt_details->total_items_label))
                            <tr>
                                <th style="width:70%">
                                    {!! $receipt_details->total_items_label !!}
                                </th>
                                <td class="text-right">
                                    {{ $receipt_details->total_items }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th style="width:70%">
                                {!! $receipt_details->subtotal_label !!}
                            </th>
                            <td class="text-right">
                                {{-- {{ $receipt_details->subtotal }} --}}
                                {{ number_format($sub_total_amount, 2) }}
                            </td>
                        </tr>
                        @if (!empty($receipt_details->total_exempt_uf))
                            <tr>
                                <th style="width:70%">
                                    @lang('lang_v1.exempt')
                                </th>
                                <td class="text-right">
                                    {{ $receipt_details->total_exempt }}
                                </td>
                            </tr>
                        @endif
                        <!-- Shipping Charges -->
                        @if (!empty($receipt_details->shipping_charges))
                            <tr>
                                <th style="width:70%">
                                    {!! $receipt_details->shipping_charges_label !!}
                                </th>
                                <td class="text-right">
                                    {{ $receipt_details->shipping_charges }}
                                </td>
                            </tr>
                        @endif

                        @if (!empty($receipt_details->packing_charge))
                            <tr>
                                <th style="width:70%">
                                    {!! $receipt_details->packing_charge_label !!}
                                </th>
                                <td class="text-right">
                                    {{ $receipt_details->packing_charge }}
                                </td>
                            </tr>
                        @endif

                        <!-- Discount -->
                        @if (!empty($receipt_details->discount))
                            <tr>
                                <th>
                                    {!! $receipt_details->discount_label !!}
                                </th>

                                <td class="text-right">
                                    (-) {{ $receipt_details->discount }}
                                </td>
                            </tr>
                        @endif

                        @if (!empty($receipt_details->total_line_discount))
                            <tr>
                                <th>
                                    {!! $receipt_details->line_discount_label !!}
                                </th>

                                <td class="text-right">
                                    (-) {{ $receipt_details->total_line_discount }}
                                </td>
                            </tr>
                        @endif

                        @if (!empty($receipt_details->additional_expenses))
                            @foreach ($receipt_details->additional_expenses as $key => $val)
                                <tr>
                                    <td>
                                        {{ $key }}:
                                    </td>

                                    <td class="text-right">
                                        (+)
                                        {{ $val }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @if (!empty($receipt_details->reward_point_label))
                            <tr>
                                <th>
                                    {!! $receipt_details->reward_point_label !!}
                                </th>

                                <td class="text-right">
                                    (-) {{ $receipt_details->reward_point_amount }}
                                </td>
                            </tr>
                        @endif

                        <!-- Tax -->
                        @if (!empty($receipt_details->tax))
                            <tr>
                                <th>
                                    {!! $receipt_details->tax_label !!}
                                </th>
                                <td class="text-right">
                                    (+) {{ $receipt_details->tax }}
                                </td>
                            </tr>
                        @endif

                        @if ($receipt_details->round_off_amount > 0)
                            <tr>
                                <th>
                                    {!! $receipt_details->round_off_label !!}
                                </th>
                                <td class="text-right">
                                    {{ $receipt_details->round_off }}
                                </td>
                            </tr>
                        @endif

                        <!-- Total -->
                        <tr>
                            <th>
                                {!! $receipt_details->total_label !!}
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xs-12" style="padding-left: 20px">
            @if (!empty($receipt_details->total_in_words))
                <br>
                <p>In word: <b>{{ $receipt_details->total_in_words . ' taka only.' }}</b></p>
            @endif
        </div>

        <div class="border-bottom col-md-12">
            @if (empty($receipt_details->hide_price) && !empty($receipt_details->tax_summary_label))
                <!-- tax -->
                @if (!empty($receipt_details->taxes))
                    <table class="table table-slim table-bordered">
                        <tr>
                            <th colspan="2" class="text-center">{{ $receipt_details->tax_summary_label }}</th>
                        </tr>
                        @foreach ($receipt_details->taxes as $key => $val)
                            <tr>
                                <td class="text-center"><b>{{ $key }}</b></td>
                                <td class="text-center">{{ $val }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            @endif
        </div>

        @if (!empty($receipt_details->additional_notes))
            <div class="col-xs-12">
                <p>{!! nl2br($receipt_details->additional_notes) !!}</p>
            </div>
        @endif

    </div>

    @if (auth()->user()->business->id != 20)
        @if ($receipt_details->signature == 1)
            <div class="ms-signArea">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-xs-4">
                                <div class="ms-signItem">
                                    <p>Authorized Signature</p>
                                </div>
                            </div>
                            <div class="col-xs-5 pull-right">
                                <div class="ms-signItem">
                                    <p>Customer Signature</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="footer-img">
            <img src="{{ asset('uploads/bm_computers.jpeg') }}" alt="">
        </div>
    @endif

    <div class="row">
        @if (!empty($receipt_details->footer_text))
            <div class="@if ($receipt_details->show_barcode || $receipt_details->show_qr_code) col-xs-8 @else col-xs-12 @endif">
                {!! $receipt_details->footer_text !!}
            </div>
        @endif
        @if ($receipt_details->show_barcode || $receipt_details->show_qr_code)
            <div class="@if (!empty($receipt_details->footer_text)) col-xs-4 @else col-xs-12 @endif text-center">
                @if ($receipt_details->show_barcode)
                    {{-- Barcode --}}
                    <img class="center-block"
                        src="data:image/png;base64,{{ DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2, 30, [39, 48, 54], true) }}">
                @endif

                @if ($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text))
                    <img class="center-block mt-5"
                        src="data:image/png;base64,{{ DNS2D::getBarcodePNG($receipt_details->qr_code_text, 'QRCODE', 3, 3, [39, 48, 54]) }}">
                @endif
            </div>
        @endif
    </div>
    <div class="credit_area">
        <small>Dev by <a href="https://bybsasolutions.com">www.bybsasolutions.com</a></small>
    </div>
</div>

<style type="text/css">
    body {
        color: #000000;
    }

    .ms-margin {
        margin: 0 20px !important;
    }

    .print_area {
        position: relative;
        padding: 0 20px;
        height: 29.7cm;
    }

    .print_area div,
    .print_area table,
    .print_area tr,
    .print_area td,
    .print_area th {
        background-color: transparent !important;
    }

    /* credit area css */
    .credit_area {
        position: absolute;
        display: block;
        width: 100vw;
        text-align: center;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
    }

    /* signature area css */
    .ms-signArea {
        /* margin-top: 500px !important; */
        position: fixed;
        width: 100% !important;
        display: block !important;
        bottom: 30px;
        left: 0;
        z-index: 1;
    }

    .ms-signItem {
        border-top: 1px solid #000000;
        text-align: center;
    }

    /* watermark area css */
    .watermark {
        position: absolute;
        display: block;
        width: 100vw;
        height: 1000px;
        text-align: center;
        top: 25%;
        left: 50%;
        transform: translateX(-50%);
    }

    .watermark img {
        width: 50%;
        opacity: .1;
    }

    /* footer img css */
    .footer-img {
        width: 100vw;
        z-index: 10;
        position: fixed;
        bottom: 0;
        left: 0;
    }

    .footer-img img {
        width: 100%;
        object-fit: cover;
    }
</style>
