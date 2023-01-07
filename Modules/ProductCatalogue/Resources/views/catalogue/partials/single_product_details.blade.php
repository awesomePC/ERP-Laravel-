<br>
<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table bg-gray">
				<tr class="bg-green">
					<th>@lang('product.default_selling_price') (@lang('product.exc_of_tax'))</th>
				    <th>@lang('product.default_selling_price') (@lang('product.inc_of_tax'))</th>
			        <th>@lang('lang_v1.variation_images')</th>
				</tr>
				@foreach($product->variations as $variation)
				<tr>
					<td>
						<span class="display_currency" data-currency_symbol="true">{{ $variation->default_sell_price }}</span>
					</td>
					<td>
						<span class="display_currency" data-currency_symbol="true">{{ $variation->sell_price_inc_tax }}</span>
					</td>
			        <td>
			        	@foreach($variation->media as $media)
			        		{!! $media->thumbnail([60, 60], 'img-thumbnail') !!}
			        	@endforeach
			        </td>
				</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>