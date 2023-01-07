<div class="row">
	<div class="col-md-12">
		<h4>@lang('product.variations'):</h4>
	</div>
	<div class="col-md-12">
		@foreach($product->variations as $variation)
			<div class="col-md-3 mb-12">
				<div class="attachment-block clearfix">
	                @if(!empty($variation->media->first()))
						<img src="{{$variation->media->first()->display_url}}" class="attachment-img" alt="Product image">
					@else
						<img src="{{$product->image_url}}" alt="Product image" class="attachment-img">
					@endif
					@if(!empty($discounts[$variation->id]))
      					<span class="label label-warning discount-badge-small">- {{@num_format($discounts[$variation->id]->discount_amount)}}%</span>
      				@endif
	                <div class="attachment-pushed">
	                  <h4 class="attachment-heading">{{$variation->product_variation->name}} - {{ $variation->name }}</h4>

	                  <div class="attachment-text">
	                  	<br>
	                    <table class="table table-slim no-border">
							<tr>
								<th>@lang('product.sku'):</th>
			      				<td>{{$variation->sub_sku }}</td>
							</tr>
							<tr>
								<th>@lang('lang_v1.price'):</th>
			      				<td><span class="display_currency" data-currency_symbol="true">{{ $variation->sell_price_inc_tax }}</span></td>
							</tr>
						</table>
	                  </div>
	                </div>
	              </div>
			</div>
		@endforeach
	</div>
</div>