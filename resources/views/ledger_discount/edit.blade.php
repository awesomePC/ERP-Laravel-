<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('LedgerDiscountController@update', $discount->id), 'method' => 'put', 'id' => 'edit_discount_form' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('lang_v1.edit_discount')</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('edit_discount_date', __( 'lang_v1.date' ) . ':*') !!}
                  {!! Form::text('date', @format_datetime($discount->transaction_date), ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.date' ), 'id' => 'edit_discount_date']); !!}
            </div>

            <div class="form-group">
                {!! Form::label('amount', __( 'sale.amount' ) . ':*') !!}
                  {!! Form::text('amount', @num_format($discount->final_total), ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'sale.amount' ) ]); !!}
            </div>

            @if($contact->type == 'both')
            <div class="form-group">
                {!! Form::label('sub_type', __( 'lang_v1.discount_for' ) . ':') !!}
                  {!! Form::select('sub_type', ['sell_discount' => __('sale.sale'), 'purchase_discount' => __('lang_v1.purchase')], $discount->sub_type, ['class' => 'form-control', 'required' ]); !!}
            </div>
            @endif

            <div class="form-group">
                {!! Form::label('note', __( 'brand.note' ) . ':') !!}
                  {!! Form::textarea('note', $discount->additional_notes, ['class' => 'form-control', 'placeholder' => __( 'brand.note'), 'rows' => 3 ]); !!}
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->   