<div class="modal-dialog" role="document">
    {!! Form::open(['action' => '\Modules\AssetManagement\Http\Controllers\RevokeAllocatedAssetController@store', 'id' => 'revoke_asset_form', 'method' => 'post']) !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    @lang('assetmanagement::lang.revoke_asset', ['allocation_code' => $allocated_asset->ref_no])
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <input type="hidden" name="parent_id" value="{{$allocated_asset->id}}">
                        <input type="hidden" name="asset_id" value="{{$allocated_asset->asset_id}}">
                        <div class="form-group">
                            {!! Form::label('ref_no', __('assetmanagement::lang.revoke_code') . ':' )!!}
                            @show_tooltip(__('lang_v1.leave_empty_to_autogenerate'))
                            {!! Form::text('ref_no', null, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.revoke_code')]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        @if(!empty($allocated_asset->quantity))
                            @php
                                $max_qty = $allocated_asset->quantity - $total_revoked_asset;
                            @endphp
                        @endif
                        <div class="form-group">
                            {!! Form::label('quantity', __('lang_v1.quantity') . ':*' )!!}
                            {!! Form::text('quantity', null, ['class' => 'form-control input_number', 'placeholder' => __('lang_v1.quantity'), 'required', 'min' => 1, 'max' => (int)$max_qty]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('transaction_datetime', __('assetmanagement::lang.revoked_at') . ':*' )!!}
                            {!! Form::text('transaction_datetime', null, ['class' => 'form-control datetimepicker', 'placeholder' => __('assetmanagement::lang.revoked_at'), 'readonly', 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('reason', __('assetmanagement::lang.reason') . ':') !!}
                            {!! Form::textarea('reason', null, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('assetmanagement::lang.reason')]); !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    @lang('messages.close')
                </button>
                <button type="submit" class="btn btn-primary">
                    @lang('messages.save')
                </button>
            </div>
        </div><!-- /.modal-content -->
    {!! Form::close() !!}
</div><!-- /.modal-dialog -->