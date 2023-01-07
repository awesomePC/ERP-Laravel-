<table class="table table-striped table-th-green text-center" id="ingredients_for_unit_recipe_table">
	<thead>
		<tr>
			<th>@lang('manufacturing::lang.ingredient')</th>
			<th>@lang('manufacturing::lang.input_quantity')</th>
			<th>@lang('manufacturing::lang.waste_percent')</th>
			<th>@lang('manufacturing::lang.final_quantity') @show_tooltip(__('manufacturing::lang.final_quantity_tooltip'))</th>
			<th>@lang('manufacturing::lang.total_price') @show_tooltip(__('manufacturing::lang.total_price_tooltip'))</th>
		</tr>
	</thead>
	<tbody>
		@php
			$total_ingredient_price = 0;
			$ingredient_groups = [];
		@endphp
		@if(!empty($ingredients))
			@foreach($ingredients as $ingredient)
				@if(empty($ingredient['mfg_ingredient_group_id']))
					@include('manufacturing::recipe.ingredient_row_for_production')
				@else
					@php
						$ingredient_groups[$ingredient['mfg_ingredient_group_id']][] = $ingredient;
					@endphp
				@endif
			@endforeach
			@foreach($ingredient_groups as $ingredient_group)
				<tr class="bg-gray ingredient_group_row">
					<td colspan="5" style="text-align: left;"><strong>{{$ingredient_group[0]['ingredient_group_name'] ?? ''}}</strong></td>
				</tr>
				@foreach($ingredient_group as $ingredient)
					@include('manufacturing::recipe.ingredient_row_for_production')
				@endforeach
			@endforeach
		@endif
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4" style="text-align: right;"><strong>@lang('manufacturing::lang.ingredients_cost')</strong></td>
			<td><span class="display_currency" data-currency_symbol="true" id="total_ingredient_price">{{$total_ingredient_price}}</span>
			<input type="hidden" id="waste_percent" value="{{$recipe->waste_percent ?? 0}}">
			</td>
		</tr>
	</tfoot>
</table>