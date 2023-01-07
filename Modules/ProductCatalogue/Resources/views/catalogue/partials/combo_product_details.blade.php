<div class="row">
	<div class="col-md-12">
		<h4>@lang('lang_v1.combo'):</h4>
	</div>
	<div class="col-md-12">
		@foreach($combo_variations as $variation)
			<div class="col-md-3 mb-12">
				<div class="attachment-block clearfix">
	                @if(!empty($variation['variation']->media->first()))
						<img src="{{$variation['variation']->media->first()->display_url}}" class="attachment-img" alt="Product image">
					@else
						<img src="{{$product->image_url}}" alt="Product image" class="attachment-img">
					@endif
	                <div class="attachment-pushed">
	                  <h4 class="attachment-heading">{{$variation['variation']['product']->name}} 

						@if($variation['variation']['product']->type == 'variable')
							- {{$variation['variation']->name}}
						@endif</h4>

	                  <div class="attachment-text">
	                  	<br>
	                    <table class="table table-slim no-border">
							<tr>
								<th>@lang('product.sku'):</th>
			      				<td>{{$variation['variation']->sub_sku}}</td>
							</tr>
							<tr>
								<th>@lang('lang_v1.price'):</th>
			      				<td><span class="display_currency" data-currency_symbol="true">{{ $variation['variation']->sell_price_inc_tax }}</span></td>
							</tr>
							<tr>
								<th>@lang('sale.qty'):</th>
			      				<td><span class="display_currency" data-currency_symbol="false" data-is_quantity=true >{{$variation['quantity']}}</span> {{$variation['unit_name']}}</td>
							</tr>
						</table>
	                  </div>
	                </div>
	            </div>
			</div>
		@endforeach
	</div>
</div>