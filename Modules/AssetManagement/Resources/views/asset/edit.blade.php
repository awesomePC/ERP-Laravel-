<!-- Modal -->
<div class="modal-dialog modal-lg" role="document">
    {!! Form::open(['url' => action('\Modules\AssetManagement\Http\Controllers\AssetController@update', [$asset->id]), 'method' => 'put', 'id' => 'asset_form', 'files' => true]) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">    <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="myModalLabel">
                @lang('assetmanagement::lang.edit_asset')
                <small><code>({{$asset->asset_code}})</code></small>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('location_id', __('business.business_location') . ':*' )!!}
                        {!! Form::select('location_id', $business_locations, $asset->location_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', __('assetmanagement::lang.asset_name') . ':*' )!!}
                        {!! Form::text('name', $asset->name, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.asset_name'), 'required']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('quantity', __('lang_v1.quantity') . ':*' )!!}
                        {!! Form::text('quantity', !empty($asset->quantity) ? @format_quantity($asset->quantity): null, ['class' => 'form-control input_number', 'placeholder' => __('lang_v1.quantity'), 'required', 'min' => 1]); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('model', __('assetmanagement::lang.model_no') . ':' )!!}
                        {!! Form::text('model', $asset->model, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.model_no')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    {!! Form::label('serial_no', __('assetmanagement::lang.serial_no') . ':' )!!}
                    {!! Form::text('serial_no', $asset->serial_no, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.serial_no')]); !!}
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('category_id', __('assetmanagement::lang.asset_category') . ':' )!!}
                        {!! Form::select('category_id', $asset_category, $asset->category_id, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('purchase_date', __('purchase.purchase_date') . ':' )!!}
                        {!! Form::text('purchase_date', !empty($asset->purchase_date) ? @format_date($asset->purchase_date) : null, ['class' => 'form-control datepicker', 'placeholder' => __('purchase.purchase_date'), 'readonly']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    {!! Form::label('purchase_type', __('assetmanagement::lang.purchase_type') . ':' )!!}
                    {!! Form::select('purchase_type', $purchase_types, $asset->purchase_type, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('unit_price', __('sale.unit_price') . ':*' )!!}
                        {!! Form::text('unit_price', !empty($asset->unit_price) ? @num_format($asset->unit_price) : null, ['class' => 'form-control input_number', 'placeholder' => __('sale.unit_price'), 'required']); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('depreciation', __('assetmanagement::lang.depreciation') . ':' )!!}
                        {!! Form::text('depreciation', !empty($asset->depreciation) ? @num_format($asset->depreciation) : null, ['class' => 'form-control input_number', 'placeholder' => __('assetmanagement::lang.depreciation')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="form-group">
                            {!! Form::label('image', __('lang_v1.image') . ':') !!}
                            {!! Form::file('image', ['id' => 'upload_asset_image', 'accept' => 'image/*']); !!}
                            <small>
                                <p class="help-block">
                                    @lang('assetmanagement::lang.image_replace_help_text')<br>
                                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                </p>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_allocatable" value="1" @if($asset->is_allocatable) checked @endif>
                                @lang('assetmanagement::lang.is_allocatable')
                            </label>
                            @show_tooltip(__('assetmanagement::lang.allocatable_tooltip'))
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12">
                    {!! Form::label('description', __('lang_v1.description') . ':') !!}
                    {!! Form::textarea('description', $asset->description, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('lang_v1.description')]); !!}
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h4>@lang('lang_v1.warranties')</h4>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-primary btn-sm pull-right" id="add_more_warranty"><i class="fas fa-plus"></i> @lang('assetmanagement::lang.add_more')</button>
                </div>
                <div class="col-md-12">
                <table class="table" id="asset_warranty_table">
                    <thead>
                        <tr>
                            <th>@lang('business.start_date')</th>
                            <th>@lang('assetmanagement::lang.warranty_months')</th>
                            <th>@lang('assetmanagement::lang.additional_cost')</th>
                            <th>@lang('purchase.additional_notes')</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($asset->warranties as $warranty)
                        <tr>
                            <td>
                                {!! Form::text('edit_warranty[' . $warranty->id . '][start_date]', @format_date($warranty->start_date), ['class' => 'form-control datepicker', 'placeholder' => __('business.start_date'), 'readonly' , 'required']); !!}
                            </td>
                            <td>
                                {!! Form::text('edit_warranty[' . $warranty->id . '][months]', @num_format($warranty->months), ['class' => 'form-control input_number', 'placeholder' => __('assetmanagement::lang.warranty_months'), 'required']); !!}
                            </td>
                            <td>
                                {!! Form::text('edit_warranty[' . $warranty->id . '][additional_cost]', @num_format($warranty->additional_cost), ['class' => 'form-control input_number', 'placeholder' => __('assetmanagement::lang.additional_cost')]); !!}
                            </td>
                            <td>
                                {!! Form::textarea('edit_warranty[' . $warranty->id . '][additional_note]', $warranty->additional_note, ['class' => 'form-control', 'placeholder' => __('purchase.additional_notes'), 'rows' => 3]); !!}
                            </td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-warranty"><i class="fas fa-times"></i></button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
    {!! Form::close() !!}
</div>