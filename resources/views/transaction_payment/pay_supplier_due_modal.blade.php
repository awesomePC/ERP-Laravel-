<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('TransactionPaymentController@postPayContactDue'), 'method' => 'post', 'id' => 'pay_contact_due_form', 'files' => true ]) !!}

    {!! Form::hidden("contact_id", $contact_details->contact_id); !!}
    {!! Form::hidden("due_payment_type", $due_payment_type); !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'purchase.add_payment' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        @if($due_payment_type == 'purchase')
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('purchase.supplier'): </strong>{{ $contact_details->name }}<br>
            <strong>@lang('business.business'): </strong>{{ $contact_details->supplier_business_name }}<br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('report.total_purchase'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_purchase }}</span><br>
            <strong>@lang('contact.total_paid'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_paid }}</span><br>
            <strong>@lang('contact.total_purchase_due'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_purchase - $contact_details->total_paid }}</span><br>
             @if(!empty($contact_details->opening_balance) || $contact_details->opening_balance != '0.00')
                  <strong>@lang('lang_v1.opening_balance'): </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  {{ $contact_details->opening_balance }}</span><br>
                  <strong>@lang('lang_v1.opening_balance_due'): </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  {{ $ob_due }}</span>
              @endif
          </div>
        </div>
        @elseif($due_payment_type == 'purchase_return')
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('purchase.supplier'): </strong>{{ $contact_details->name }}<br>
            <strong>@lang('business.business'): </strong>{{ $contact_details->supplier_business_name }}<br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('lang_v1.total_purchase_return'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_purchase_return }}</span><br>
            <strong>@lang('lang_v1.total_purchase_return_paid'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_return_paid }}</span><br>
            <strong>@lang('lang_v1.total_purchase_return_due'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_purchase_return - $contact_details->total_return_paid }}</span>
          </div>
        </div>
        @elseif(in_array($due_payment_type, ['sell']))
          <div class="col-md-6">
            <div class="well">
              <strong>@lang('sale.customer_name'): </strong>{{ $contact_details->name }}<br>
              <br><br>
            </div>
          </div>
          <div class="col-md-6">
            <div class="well">
              <strong>@lang('report.total_sell'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_invoice }}</span><br>
              <strong>@lang('contact.total_paid'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_paid }}</span><br>
              <strong>@lang('contact.total_sale_due'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_invoice - $contact_details->total_paid }}</span><br>
              @if(!empty($contact_details->opening_balance) || $contact_details->opening_balance != '0.00')
                  <strong>@lang('lang_v1.opening_balance'): </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  {{ $contact_details->opening_balance }}</span><br>
                  <strong>@lang('lang_v1.opening_balance_due'): </strong>
                  <span class="display_currency" data-currency_symbol="true">
                  {{ $ob_due }}</span>
              @endif
            </div>
          </div>
         @elseif(in_array($due_payment_type, ['sell_return']))
         <div class="col-md-6">
          <div class="well">
            <strong>@lang('sale.customer_name'): </strong>{{ $contact_details->name }}<br>
              <br><br>
          </div>
        </div>
        <div class="col-md-6">
          <div class="well">
            <strong>@lang('lang_v1.total_sell_return'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_sell_return }}</span><br>
            <strong>@lang('lang_v1.total_sell_return_paid'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_return_paid }}</span><br>
            <strong>@lang('lang_v1.total_sell_return_due'): </strong><span class="display_currency" data-currency_symbol="true">{{ $contact_details->total_sell_return - $contact_details->total_return_paid }}</span>
          </div>
        </div>
        @endif
      </div>
        @if(config('constants.show_payment_type_on_contact_pay') && ($due_payment_type == 'purchase' || $due_payment_type == 'sell'))
            @php
                $reverse_payment_types = [];

                if($due_payment_type == 'purchase') {
                    $reverse_payment_types = [
                        0 => __('lang_v1.pay_to_supplier'),
                        1 => __('lang_v1.receive_from_supplier')
                    ];
                } else if($due_payment_type == 'sell') {
                    $reverse_payment_types = [
                        0 => __('lang_v1.receive_from_customer'),
                        1 => __('lang_v1.pay_to_customer')
                    ];
                }
            @endphp
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label("is_reverse" , __('lang_v1.payment_type') . ':') !!}
                        {!! Form::select("is_reverse", $reverse_payment_types, 0, ['class' => 'form-control select2', 'style' => 'width:100%;']); !!}
                    </div>
                </div>
            </div>
        @endif
      <div class="row payment_row">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("method" , __('purchase.payment_method') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              {!! Form::select("method", $payment_types, $payment_line->method, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("paid_on" , __('lang_v1.paid_on') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('paid_on', @format_datetime($payment_line->paid_on), ['class' => 'form-control', 'readonly', 'required']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label("amount" , __('sale.amount') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fas fa-money-bill-alt"></i>
              </span>
              @if(in_array($due_payment_type, ['sell_return', 'purchase_return']))
              {!! Form::text("amount", @num_format($payment_line->amount), ['class' => 'form-control input_number payment_amount', 'required', 'placeholder' => __('sale.amount'), 'data-rule-max-value' => $payment_line->amount, 'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated])]); !!}
              @else
                {!! Form::text("amount", @num_format($payment_line->amount), ['class' => 'form-control input_number payment_amount', 'required', 'placeholder' => __('sale.amount')]); !!}
              @endif
            </div>
          </div>
        </div>
        @php
            $pos_settings = !empty(session()->get('business.pos_settings')) ? json_decode(session()->get('business.pos_settings'), true) : [];

            $enable_cash_denomination_for_payment_methods = !empty($pos_settings['enable_cash_denomination_for_payment_methods']) ? $pos_settings['enable_cash_denomination_for_payment_methods'] : [];
        @endphp

        @if(!empty($pos_settings['enable_cash_denomination_on']) && $pos_settings['enable_cash_denomination_on'] == 'all_screens')
            <input type="hidden" class="enable_cash_denomination_for_payment_methods" value="{{json_encode($pos_settings['enable_cash_denomination_for_payment_methods'])}}">
            <div class="clearfix"></div>
            <div class="col-md-12 cash_denomination_div @if(!in_array($payment_line->method, $enable_cash_denomination_for_payment_methods)) hide @endif">
                <hr>
                <strong>@lang( 'lang_v1.cash_denominations' )</strong>
                  @if(!empty($pos_settings['cash_denominations']))
                    <table class="table table-slim">
                      <thead>
                        <tr>
                          <th width="20%" class="text-right">@lang('lang_v1.denomination')</th>
                          <th width="20%">&nbsp;</th>
                          <th width="20%" class="text-center">@lang('lang_v1.count')</th>
                          <th width="20%">&nbsp;</th>
                          <th width="20%" class="text-left">@lang('sale.subtotal')</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach(explode(',', $pos_settings['cash_denominations']) as $dnm)
                        <tr>
                          <td class="text-right">{{$dnm}}</td>
                          <td class="text-center" >X</td>
                          <td>{!! Form::number("denominations[$dnm]", null, ['class' => 'form-control cash_denomination input-sm', 'min' => 0, 'data-denomination' => $dnm, 'style' => 'width: 100px; margin:auto;' ]); !!}</td>
                          <td class="text-center">=</td>
                          <td class="text-left">
                            <span class="denomination_subtotal">0</span>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <th colspan="4" class="text-center">@lang('sale.total')</th>
                          <td>
                            <span class="denomination_total">0</span>
                            <input type="hidden" class="denomination_total_amount" value="0">
                            <input type="hidden" class="is_strict" value="{{$pos_settings['cash_denomination_strict_check'] ?? ''}}">
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                    <p class="cash_denomination_error error hide">@lang('lang_v1.cash_denomination_error')</p>
                  @else
                    <p class="help-block">@lang('lang_v1.denomination_add_help_text')</p>
                  @endif
            </div>
        @endif

        <div class="clearfix"></div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('document', __('purchase.attach_document') . ':') !!}
            {!! Form::file('document', ['accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
            <p class="help-block">
            @includeIf('components.document_help_text')</p>
          </div>
        </div>
        @if(!empty($accounts))
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fas fa-money-bill-alt"></i>
                </span>
                {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' , ['class' => 'form-control select2', 'id' => "account_id", 'style' => 'width:100%;']); !!}
              </div>
            </div>
          </div>
        @endif
        <div class="clearfix"></div>

          @include('transaction_payment.payment_type_details')
        <div class="col-md-12">
          <div class="form-group">
            {!! Form::label("note", __('lang_v1.payment_note') . ':') !!}
            {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]); !!}
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->