<div class="modal fade" id="add_discount_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action('LedgerDiscountController@store'), 'method' => 'post', 'id' => 'add_discount_form' ]) !!}
            <input type="hidden" name="contact_id" value="{{$contact->id}}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.add_discount')</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('discount_date', __( 'lang_v1.date' ) . ':*') !!}
                      {!! Form::text('date', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.date' ), 'id' => 'discount_date']); !!}
                </div>

                <div class="form-group">
                    {!! Form::label('amount', __( 'sale.amount' ) . ':*') !!}
                      {!! Form::text('amount', null, ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'sale.amount' ) ]); !!}
                </div>

                @if($contact->type == 'both')
                <div class="form-group">
                    {!! Form::label('sub_type', __( 'lang_v1.discount_for' ) . ':') !!}
                      {!! Form::select('sub_type', ['sell_discount' => __('sale.sale'), 'purchase_discount' => __('lang_v1.purchase')], 'sell', ['class' => 'form-control', 'required' ]); !!}
                </div>
                @endif
                <div class="form-group">
                    {!! Form::label('note', __( 'brand.note' ) . ':') !!}
                      {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note'), 'rows' => 3 ]); !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->   
</div>