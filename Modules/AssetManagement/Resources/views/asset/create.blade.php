<!-- Modal -->
<div class="modal-dialog modal-lg" role="document">
    {!! Form::open(['action' => '\Modules\AssetManagement\Http\Controllers\AssetController@store', 'id' => 'asset_form', 'method' => 'post', 'files' => true]) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">    <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="myModalLabel">
                @lang('assetmanagement::lang.add_asset')
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    @if(count($business_locations) == 1)
                        @php 
                            $default_location = current(array_keys($business_locations->toArray()));
                        @endphp
                    @else
                        @php
                            $default_location = null;
                        @endphp
                    @endif
                    <div class="form-group">
                        {!! Form::label('location_id', __('business.business_location') . ':*' )!!}
                        {!! Form::select('location_id', $business_locations, $default_location, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('asset_code', __('assetmanagement::lang.asset_code') . ':' )!!}
                        {!! Form::text('asset_code', null, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.asset_code')]); !!}
                        <p class="help-block">
                            @lang('lang_v1.leave_empty_to_autogenerate')
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', __('assetmanagement::lang.asset_name') . ':*' )!!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.asset_name'), 'required']); !!}
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('quantity', __('lang_v1.quantity') . ':*' )!!}
                        {!! Form::text('quantity', null, ['class' => 'form-control input_number', 'placeholder' => __('lang_v1.quantity'), 'required', 'min' => 1]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    {!! Form::label('model', __('assetmanagement::lang.model_no') . ':' )!!}
                    {!! Form::text('model', null, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.model_no')]); !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('serial_no', __('assetmanagement::lang.serial_no') . ':' )!!}
                    {!! Form::text('serial_no', null, ['class' => 'form-control', 'placeholder' => __('assetmanagement::lang.serial_no')]); !!}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    {!! Form::label('category_id', __('assetmanagement::lang.asset_category') . ':' )!!}
                    {!! Form::select('category_id', $asset_category, null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('purchase_date', __('purchase.purchase_date') . ':' )!!}
                        {!! Form::text('purchase_date', null, ['class' => 'form-control datepicker', 'placeholder' => __('purchase.purchase_date'), 'readonly']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    {!! Form::label('purchase_type', __('assetmanagement::lang.purchase_type') . ':' )!!}
                    {!! Form::select('purchase_type', $purchase_types, null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('unit_price', __('sale.unit_price') . ':*' )!!}
                        {!! Form::text('unit_price', null, ['class' => 'form-control input_number', 'placeholder' => __('sale.unit_price'), 'required']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('depreciation', __('assetmanagement::lang.depreciation') . ':' )!!}
                        {!! Form::text('depreciation', null, ['class' => 'form-control input_number', 'placeholder' => __('assetmanagement::lang.depreciation')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="form-group">
                            {!! Form::label('image', __('lang_v1.image') . ':') !!}
                            {!! Form::file('image', ['id' => 'upload_asset_image', 'accept' => 'image/*']); !!}
                            <small>
                                <p class="help-block">
                                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
                                </p>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_allocatable" value="1">
                                @lang('assetmanagement::lang.is_allocatable')

                                @show_tooltip(__('assetmanagement::lang.allocatable_tooltip'))
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('description', __('lang_v1.description') . ':') !!}
                        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('lang_v1.description')]); !!}
                    </div>
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
                        <tr>
                            <td>
                                {!! Form::text('start_dates[]', null, ['class' => 'form-control datepicker', 'placeholder' => __('business.start_date'), 'readonly' ]); !!}
                            </td>
                            <td>
                                {!! Form::text('months[]', null, ['class' => 'form-control input_number', 'placeholder' => __('assetmanagement::lang.warranty_months')]); !!}
                            </td>
                            <td>
                                {!! Form::text('additional_cost[]', 0, ['class' => 'form-control input_number', 'placeholder' => __('assetmanagement::lang.additional_cost')]); !!}
                            </td>
                            <td>
                                {!! Form::textarea('additional_note[]', null, ['class' => 'form-control', 'placeholder' => __('purchase.additional_notes'), 'rows' => 3]); !!}
                            </td>
                            <td>&nbsp;</td>
                        </tr>
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
                @lang('messages.save')
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>