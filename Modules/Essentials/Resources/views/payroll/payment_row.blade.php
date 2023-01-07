{!! Form::label("paid_on_".$id , __('lang_v1.paid_on') . ':*') !!}
<div class="input-group">
  <span class="input-group-addon">
    <i class="fa fa-calendar"></i>
  </span>
  {!! Form::text('payments['.$id.'][paid_on]', @format_datetime($payroll['paid_on']), ['class' => 'form-control input-sm paid_on', 'readonly', 'required', 'id' => 'paid_on_'.$id, 'data-id' => $id]); !!}
</div>

@if(!empty($accounts))
	{!! Form::label("account_id_".$id , __('lang_v1.payment_account') . ':') !!}
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fas fa-money-bill-alt"></i>
        </span>
        {!! Form::select('payments['.$id.'][account_id]', $accounts, null , ['class' => 'form-control input-sm select2', 'id' => "account_id_".$id, 'style' => 'width:100%;', 'data-id' => $id]); !!}
      </div>
@endif

{!! Form::label("method_".$id , __('purchase.payment_method') . ':*') !!}
<div class="input-group">
  <span class="input-group-addon">
    <i class="fas fa-money-bill-alt"></i>
  </span>
  {!! Form::select('payments['.$id.'][method]', $payment_types, null, ['class' => 'form-control select2 payment_types input-sm', 'id' => 'method_'.$id, 'style' => 'width:100%;', 'data-id' => $id, 'placeholder' => __('messages.please_select')]); !!}
</div>

<div id="cheque_{{$id}}" style="display: none;">
	{!! Form::label("cheque_number_".$id,__('lang_v1.cheque_no')) !!}
	{!! Form::text('payments['.$id.'][cheque_number]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.cheque_no'), 'id' => 'cheque_number_'.$id, 'data-id' => $id]); !!}
</div>

<div id="bank_transfer_{{$id}}" style="display: none;">
	{!! Form::label("bank_account_number_".$id,__('lang_v1.bank_account_number')) !!}
	{!! Form::text('payments['.$id.'][bank_account_number]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.bank_account_number'), 'data-id' => $id, 'id' => 'bank_account_number_'.$id]); !!}
</div>
<div id="custom_pay_1_{{$id}}" style="display: none;">
	{!! Form::label("transaction_no_1_".$id, __('lang_v1.transaction_no')) !!}
	{!! Form::text('payments['.$id.'][transaction_no_1]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.transaction_no'), 'data-id' => $id, 'id' => 'transaction_no_1_'.$id]); !!}
</div>
<div id="custom_pay_2_{{$id}}" style="display: none;">
	{!! Form::label("transaction_no_2_".$id, __('lang_v1.transaction_no')) !!}
	{!! Form::text('payments['.$id.'][transaction_no_2]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.transaction_no'), 'data-id' => $id, 'id' => 'transaction_no_2_'.$id]); !!}
</div>
<div id="custom_pay_3_{{$id}}" style="display: none;">
	{!! Form::label("transaction_no_3_".$id, __('lang_v1.transaction_no')) !!}
	{!! Form::text('payments['.$id.'][transaction_no_3]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.transaction_no'), 'data-id' => $id, 'id' => 'transaction_no_3_'.$id]); !!}
</div>
<div id="card_{{$id}}" style="display: none;">
    <table class="table">
        <tr>
            <td>
            	{!! Form::label("card_number", __('lang_v1.card_no')) !!}
				{!! Form::text('payments['.$id.'][card_number]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.card_no')]); !!}
            </td>
            <td>
            	{!! Form::label("card_holder_name", __('lang_v1.card_holder_name')) !!}
				{!! Form::text('payments['.$id.'][card_holder_name]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.card_holder_name')]); !!}
        	</td>
        </tr>
        
        <tr>
            <td>
            	{!! Form::label("card_transaction_number",__('lang_v1.card_transaction_no')) !!}
				{!! Form::text('payments['.$id.'][card_transaction_number]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.card_transaction_no')]); !!}
            </td>
            <td>
            	{!! Form::label("card_type", __('lang_v1.card_type')) !!}
				{!! Form::select('payments['.$id.'][card_type]', ['credit' => 'Credit Card', 'debit' => 'Debit Card', 'visa' => 'Visa', 'master' => 'MasterCard'], null,['class' => 'form-control input-sm']); !!}
            </td>
        </tr>
        <tr>
        	<td>
        		{!! Form::label("card_month", __('lang_v1.month')) !!}
				{!! Form::text('payments['.$id.'][card_month]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.month') ]); !!}
        	</td>
        	<td>
        		{!! Form::label("card_year", __('lang_v1.year')) !!}
				{!! Form::text('payments['.$id.'][card_year]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.year') ]); !!}
        	</td>
        </tr>
        <tr>
        	<td colspan="2">
        		{!! Form::label("card_security",__('lang_v1.security_code')) !!}
				{!! Form::text('payments['.$id.'][card_security]', null, ['class' => 'form-control input-sm', 'placeholder' => __('lang_v1.security_code')]); !!}
        	</td>
        </tr>
	</table>
</div>