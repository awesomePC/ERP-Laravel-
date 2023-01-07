<div class="modal-dialog" role="document">
    {!! Form::open(['action' => '\Modules\AssetManagement\Http\Controllers\AssetMaitenanceController@store', 'id' => 'asset_maintenance_form', 'method' => 'post', 'files' => true]) !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    @lang('assetmanagement::lang.send_to_maintenance')
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <strong>@lang('assetmanagement::lang.asset'): </strong> {{$asset->name}} ({{$asset->asset_code}})
                        <br>
                        @if($asset->is_in_warranty)
                            <span class="label bg-green">@lang('assetmanagement::lang.in_warranty')</span>
                            <br>
                            ({{\Carbon::now()->diffInDays(\Carbon::parse($asset->is_in_warranty->end_date), false)}} @lang('assetmanagement::lang.days_left')) </small>
                        @else
                            <span class="label bg-red">@lang('assetmanagement::lang.not_in_warranty')</span>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="asset_id" value="{{$asset->id}}">
                        <div class="form-group">
                            {!! Form::label('status', __('sale.status') . ':' )!!}
                            {!! Form::select('status', $statuses, null, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('priority', __('lang_v1.priority') . ':' )!!}
                            {!! Form::select('priority', $priorities, null, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('attachments', __('assetmanagement::lang.attachments') . ':') !!}
                            {!! Form::file('attachments[]', ['id' => 'attachments', 'multiple', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
                            <p class="help-block">
                                @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                @includeIf('components.document_help_text')
                            </p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('maintenance_note', __('assetmanagement::lang.maintenance_note') . ':' )!!}
                            {!! Form::textarea('maintenance_note', null, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.maintenance_note'), 'rows' => 3]); !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('details', __('assetmanagement::lang.send_for_maintenance_details') . ':' )!!}
                            {!! Form::textarea('details', null, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.send_for_maintenance_details'), 'rows' => 3, 'readonly']); !!}
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