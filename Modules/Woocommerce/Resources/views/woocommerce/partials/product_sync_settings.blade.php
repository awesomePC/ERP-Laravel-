<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('default_tax_class',  __('woocommerce::lang.default_tax_class') . ':') !!} @show_tooltip(__('woocommerce::lang.default_tax_class_help'))
                {!! Form::text('default_tax_class',$default_settings['default_tax_class'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('product_tax_type',  __('woocommerce::lang.sync_product_price') . ':') !!}
                {!! Form::select('product_tax_type', ['inc' => __('woocommerce::lang.inc_tax'), 'exc' => __('woocommerce::lang.exc_tax') ], $default_settings['product_tax_type'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('default_selling_price_group',  __('woocommerce::lang.default_selling_price_group') . ':') !!}
                {!! Form::select('default_selling_price_group', $price_groups, $default_settings['default_selling_price_group'], ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('sync_description_as',  __('woocommerce::lang.sync_description_as') . ':') !!}
                {!! Form::select('sync_description_as', ['short' => __('woocommerce::lang.short_description'), 'long' => __('woocommerce::lang.long_description'), 'both' => __('woocommerce::lang.both')], !empty($default_settings['sync_description_as']) ? $default_settings['sync_description_as'] : 'long', ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <hr>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
                <label for="#">@lang('woocommerce::lang.product_fields_to_be_synced_for_create'):</label><br>
                <label class="checkbox-inline">
                  {!! Form::checkbox('product_fields_for_create[]', 'name', true, ['class' => 'input-icheck', 'disabled'] ); !!}
                  @lang('product.product_name'),
                </label>
                <label class="checkbox-inline">
                  {!! Form::checkbox('product_fields_for_create[]', 'price', true, ['class' => 'input-icheck', 'disabled'] ); !!}
                  @lang('woocommerce::lang.price'),
                </label>
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_create[]', 'category', in_array('category', $default_settings['product_fields_for_create']), ['class' => 'input-icheck'] ); !!} @lang('product.category'),
                </label>
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_create[]', 'quantity', in_array('quantity', $default_settings['product_fields_for_create']), ['class' => 'input-icheck create_quantity'] ); !!} @lang('sale.qty') 
                </label>
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_create[]', 'weight', in_array('weight', $default_settings['product_fields_for_create']), ['class' => 'input-icheck'] ); !!} @lang('lang_v1.weight')
                </label>
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_create[]', 'image', in_array('image', $default_settings['product_fields_for_create']), ['class' => 'input-icheck'] ); !!} @lang('woocommerce::lang.images')
                </label>
            </div>
            <div class="form-group">
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_create[]', 'description', in_array('description', $default_settings['product_fields_for_create']), ['class' => 'input-icheck'] ); !!} @lang('lang_v1.description')
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4 @if(in_array('quantity', $default_settings['product_fields_for_create'])) hide @endif create_stock_settings">
            <div class="form-group">
                {!! Form::label('manage_stock_for_create',  __('product.manage_stock') . ':') !!}
                {!! Form::select('manage_stock_for_create', ['true' => 'true', 'false' => 'false', 'none' => __('woocommerce::lang.dont_update') ], isset($default_settings['manage_stock_for_create']) ? $default_settings['manage_stock_for_create'] : 'none', ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4 @if(in_array('quantity', $default_settings['product_fields_for_create'])) hide @endif create_stock_settings">
            <div class="form-group">
                {!! Form::label('in_stock_for_create',  __('woocommerce::lang.in_stock') . ':') !!}
                {!! Form::select('in_stock_for_create', ['true' => 'true', 'false' => 'false', 'none' => __('woocommerce::lang.dont_update') ], isset($default_settings['in_stock_for_create']) ? $default_settings['in_stock_for_create'] : 'none', ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <hr>
        </div>
        <div class="col-xs-12">
            <br/>
            <div class="form-group">
                <label for="#">@lang('woocommerce::lang.product_fields_to_be_synced_for_update'):</label><br>
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_update[]', 'name', in_array('name', $default_settings['product_fields_for_update']), ['class' => 'input-icheck'] ); !!}
                  @lang('product.product_name'),
                </label>
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_update[]', 'price', in_array('price', $default_settings['product_fields_for_update']), ['class' => 'input-icheck'] ); !!}
                  @lang('woocommerce::lang.price'),
                </label>
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_update[]', 'category', in_array('category', $default_settings['product_fields_for_update']), ['class' => 'input-icheck'] ); !!} @lang('product.category'),
                </label>
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_update[]', 'quantity', in_array('quantity', $default_settings['product_fields_for_update']), ['class' => 'input-icheck update_quantity'] ); !!} @lang('sale.qty') 
                </label>
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_update[]', 'weight', in_array('weight', $default_settings['product_fields_for_update']), ['class' => 'input-icheck'] ); !!} @lang('lang_v1.weight')
                </label>
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_update[]', 'image', in_array('image', $default_settings['product_fields_for_update']), ['class' => 'input-icheck'] ); !!} @lang('woocommerce::lang.images')
                </label>
            </div>
            <div class="form-group">
                <label class="checkbox-inline">
                    {!! Form::checkbox('product_fields_for_update[]', 'description', in_array('description', $default_settings['product_fields_for_create']), ['class' => 'input-icheck'] ); !!} @lang('lang_v1.description')
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4 @if(in_array('quantity', $default_settings['product_fields_for_update'])) hide @endif update_stock_settings">
            <div class="form-group">
                {!! Form::label('manage_stock_for_update',  __('product.manage_stock') . ':') !!}
                {!! Form::select('manage_stock_for_update', ['true' => 'true', 'false' => 'false', 'none' => __('woocommerce::lang.dont_update') ], isset($default_settings['manage_stock_for_update']) ? $default_settings['manage_stock_for_update'] : 'none', ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4 @if(in_array('quantity', $default_settings['product_fields_for_update'])) hide @endif update_stock_settings">
            <div class="form-group">
                {!! Form::label('in_stock_for_update',  __('woocommerce::lang.in_stock') . ':') !!}
                {!! Form::select('in_stock_for_update', ['true' => 'true', 'false' => 'false', 'none' => __('woocommerce::lang.dont_update') ], isset($default_settings['in_stock_for_update']) ? $default_settings['in_stock_for_update'] : 'none', ['class' => 'form-control']); !!}
            </div>
        </div>
    </div>
</div>