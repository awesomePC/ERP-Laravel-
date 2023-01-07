<div class="modal-dialog" role="document">
    {!! Form::open(['url' => action('\Modules\AssetManagement\Http\Controllers\AssetMaitenanceController@update', [$maintenance->id]), 'method' => 'put', 'id' => 'asset_maintenance_form', 'files' => true]) !!}
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    @lang('assetmanagement::lang.asset_maintenance') - #{{$maintenance->maitenance_id}}
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('status', __('sale.status') . ':' )!!}
                            {!! Form::select('status', $statuses, $maintenance->status, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('priority', __('lang_v1.priority') . ':' )!!}
                            {!! Form::select('priority', $priorities, $maintenance->priority, ['class' => 'form-control', 'placeholder' => __('messages.please_select')]); !!}
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
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('assigned_to', __('lang_v1.assigned_to') . ':' )!!}
                            {!! Form::select('assigned_to', $users, $maintenance->assigned_to, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        @include('sell.partials.media_table', ['medias' => $maintenance->media, 'delete' => true])
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('maintenance_note', __('assetmanagement::lang.maintenance_note') . ':' )!!}
                            {!! Form::textarea('maintenance_note', $maintenance->maintenance_note, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.maintenance_note'), 'rows' => 3, 'readonly']); !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('details', __('assetmanagement::lang.send_for_maintenance_details') . ':' )!!}
                            {!! Form::textarea('details', $maintenance->details, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.send_for_maintenance_details'), 'rows' => 3]); !!}
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
        </div><!-- /.modal-content -->
    {!! Form::close() !!}
</div><!-- /.modal-dialog -->