<div class="modal-dialog" role="document">
    {!! Form::open(['url' => action('\Modules\AssetManagement\Http\Controllers\AssetAllocationController@update', [$asset_allocated->id]), 'method' => 'put', 'id' => 'asset_allocation_form']) !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    @lang('assetmanagement::lang.edit')
                    <code>({{$asset_allocated->ref_no}})</code>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('asset_id', __('assetmanagement::lang.asset') . ':*' )!!}
                            {!! Form::select('asset_id', $assets['assets'], $asset_allocated->asset_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;', 'disabled'], $assets['asset_quantity']); !!}
                        </div>
                        <input type="hidden" name="asset_id" value="{{$asset_allocated->asset_id}}">
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('receiver', __('assetmanagement::lang.allocate_to') . ':*' )!!}
                            {!! Form::select('receiver', $users, $asset_allocated->receiver, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    @if(!empty($asset_allocated->revokeTransaction))
                        @php
                            $revoked_quantities = $asset_allocated->revokeTransaction->pluck('quantity')->toArray();
                            $total_rq = 0;
                            foreach ($revoked_quantities as $key => $quantity) {
                                $total_rq += (int)$quantity;
                            }

                            $current_allocated_qty = $asset_allocated->quantity - $total_rq;
                            $max_qty = $current_allocated_qty + $total_available_asset;
                        @endphp                    
                    @endif
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('quantity', __('lang_v1.quantity') . ':*' )!!}
                            {!! Form::text('quantity', !empty($asset_allocated->quantity) ? @format_quantity($asset_allocated->quantity): null, ['class' => 'form-control input_number', 'placeholder' => __('lang_v1.quantity'), 'required', 'min' => 1, 'max' => (int)$max_qty]); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('transaction_datetime', __('assetmanagement::lang.allocate_from') . ':*' )!!}
                            {!! Form::text('transaction_datetime', !empty($asset_allocated->transaction_datetime) ? @format_datetime($asset_allocated->transaction_datetime) : null, ['class' => 'form-control datetimepicker', 'placeholder' => __('assetmanagement::lang.allocate_from'), 'readonly', 'required']); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('allocated_upto', __('assetmanagement::lang.allocated_upto') . ':' )!!}
                            {!! Form::text('allocated_upto', !empty($asset_allocated->allocated_upto) ? @format_date($asset_allocated->allocated_upto) : null, ['class' => 'form-control datepicker', 'placeholder' => __('assetmanagement::lang.allocated_upto'), 'readonly']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('reason', __('assetmanagement::lang.reason') . ':') !!}
                            {!! Form::textarea('reason', $asset_allocated->reason, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('assetmanagement::lang.reason')]); !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    @lang('messages.close')
                </button>
                <button type="submit" class="btn btn-primary">
                    @lang('messages.update')
                </button>
            </div>
        </div>
    {!! Form::close() !!} <!-- /form close -->
</div><!-- /.modal-dialog -->