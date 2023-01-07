<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Repair\Http\Controllers\DeviceModelController@update', [$model->id]), 'method' => 'put', 'id' => 'device_model' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="myModalLabel">
                @lang('repair::lang.add_device_model')
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                   <div class="form-group">
                        {!! Form::label('name', __('repair::lang.model_name') . ':*' )!!}
                        {!! Form::text('name', $model->name, ['class' => 'form-control', 'required' ]) !!}
                   </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                   <div class="form-group">
                        {!! Form::label('brand_id', __('product.brand') .':') !!}
                        {!! Form::select('brand_id', $brands, $model->brand_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;', 'id' => 'model_brand_id']); !!}
                   </div>
                </div>
                <div class="col-md-6">
                   <div class="form-group">
                        {!! Form::label('device_id', __('repair::lang.device') .':') !!}
                        {!! Form::select('device_id', $devices, $model->device_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;', 'id' => 'model_device_id']); !!}
                   </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('repair_checklist', __('repair::lang.repair_checklist') . ':') !!} @show_tooltip(__('repair::lang.repair_checklist_tooltip'))
                        {!! Form::textarea('repair_checklist', $model->repair_checklist, ['class' => 'form-control ', 'id' => 'repair_checklist', 'rows' => '3']); !!}
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
        {!! Form::close() !!}
    </div>
</div>