<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Manufacturing\Http\Controllers\RecipeController@addIngredients'), 'method' => 'get', 'id' => 'choose_product_form']) !!}

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang( 'manufacturing::lang.choose_product' )</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('variation_id', __('manufacturing::lang.choose_product').':') !!}
                    {!! Form::select('variation_id', [], null, ['class' => 'form-control', 'id' => 'variation_id', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                </div>
                <div class="form-group" id="recipe_selection">
                    {!! Form::label('copy_recipe_id', __('manufacturing::lang.copy_from_recipe').':') !!}
                    {!! Form::select('copy_recipe_id', $recipes, null, ['class' => 'form-control', 'placeholder' => __('lang_v1.none'), 'style' => 'width: 100%;']); !!}
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
                <button type="submit" class="btn btn-primary">@lang( 'manufacturing::lang.continue' )</button>
            </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->