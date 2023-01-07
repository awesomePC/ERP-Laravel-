<tr>
	<td>
		{{$ingredient['full_name']}}
		<input type="hidden" class="ingredient_price" value="{{$ingredient['dpp_inc_tax']}}">
		<input type="hidden" name="ingredients[{{$ingredient['id']}}][variation_id]"  class="ingredient_id" value="{{$ingredient['variation_id']}}">
		<input type="hidden" class="unit_quantity" value="{{$ingredient['unit_quantity']}}">
		<input type="hidden" name="ingredients[{{$ingredient['id']}}][mfg_ingredient_group_id]" value="{{$ingredient['mfg_ingredient_group_id']}}">
	</td>
	<td>
		@php
			$variation = $ingredient['variation'];
			$multiplier = $ingredient['multiplier'];
			$allow_decimal = $ingredient['allow_decimal'];
			$qty_available = 0;
			if($ingredient['enable_stock'] == 1) {
				$max_qty_rule = !empty($variation->variation_location_details[0]->qty_available) ? $variation->variation_location_details[0]->qty_available : 0;
				$qty_available = $max_qty_rule;
				$max_qty_rule = $max_qty_rule / $multiplier;
				$max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> number_format($max_qty_rule, 2), 'unit' => $ingredient['unit']  ]);
			}
			
		@endphp
		<div class="@if(!empty($ingredient['sub_units'])) input_inline @else input-group @endif">
			<input 
			type="text" 
			data-min="1" 
			class="form-control input-sm input_number mousetrap total_quantities" 
			value="{{@format_quantity($ingredient['quantity'])}}" 
			name="ingredients[{{$ingredient['id']}}][quantity]" 
			data-allow-overselling="@if(empty($pos_settings['allow_overselling'])){{'false'}}@else{{'true'}}@endif"
			@if($allow_decimal) 
				data-decimal=1 
			@else 
				data-decimal=0 
				data-rule-abs_digit="true" 
				data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" 
			@endif 
				data-rule-required="true" 
				data-msg-required="@lang('validation.custom-messages.this_field_is_required')" 
			@if($ingredient['enable_stock'] == 1 && empty($pos_settings['allow_overselling']) ) 	
				data-rule-max-value="{{$max_qty_rule}}"  
				data-msg-max-value="{{$max_qty_msg}}" 
				data-qty_available={{$qty_available}}
			@endif 

			@if(!empty($manufacturing_settings['disable_editing_ingredient_qty']))
			readonly
			@endif
			>
			<span class="@if(empty($ingredient['sub_units'])) input-group-addon @endif line_unit_span">
			@if(empty($ingredient['sub_units'])) 
				{{$ingredient['unit']}}
			@else
				<select name="ingredients[{{$ingredient['id']}}][sub_unit_id]" class="input-sm form-control sub_unit" 
				@if(!empty($manufacturing_settings['disable_editing_ingredient_qty']))
				disabled="" 
				@endif
			>
					@foreach($ingredient['sub_units'] as $key => $value)
						<option 
							value="{{$key}}" 
							data-allow_decimal="{{$value['allow_decimal']}}"
							data-multiplier="{{$value['multiplier']}}"
							data-unit_name="{{$value['name']}}"
							@if($ingredient['sub_unit_id'] == $key) selected @endif>{{$value['name']}}</option>
					@endforeach
				</select>

				@if(!empty($manufacturing_settings['disable_editing_ingredient_qty']))
					<input type="hidden" name="ingredients[{{$ingredient['id']}}][sub_unit_id]" value="{{$ingredient['sub_unit_id']}}">
				@endif
			@endif
			</span>
		</div>
	</td>
	<td>
		<div class="input-group">
			<input type="text" name="ingredients[{{$ingredient['id']}}][mfg_waste_percent]" value="{{@format_quantity($ingredient['waste_percent'])}}" class="form-control input-sm input_number mfg_waste_percent">
			<span class="input-group-addon"><i class="fa fa-percent"></i></span>
		</div>
	</td>
	<td>
		<span class="row_final_quantity">{{@format_quantity($ingredient['final_quantity'])}}</span> <span class="row_unit_text">{{$ingredient['unit']}}</span>
	</td>
	<td>
		<span class="ingredient_total_price display_currency" data-currency_symbol="true">{{@num_format($ingredient['total_price'])}}</span>
		<input type="hidden" class="total_price" value="{{$ingredient['total_price']}}">
	</td>
</tr>