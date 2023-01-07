<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-7">
        	@php
        		$pos_sell_statuses = [
        			'final' => __('lang_v1.final'),
        			'draft' => __('sale.draft'),
        			'quotation' => __('lang_v1.quotation')
        		];

        		$woo_order_statuses = [
        			'pending' => __('woocommerce::lang.pending'),
        			'processing' => __('woocommerce::lang.processing'),
        			'on-hold' => __('woocommerce::lang.on-hold'),
        			'completed' => __('woocommerce::lang.completed'),
        			'cancelled' => __('woocommerce::lang.cancelled'),
        			'refunded' => __('woocommerce::lang.refunded'),
        			'failed' => __('woocommerce::lang.failed'),
        			'shipped' => __('woocommerce::lang.shipped')
        		];

        	@endphp
        	<table class="table">
        		<tr>
        			<th>@lang('woocommerce::lang.woocommerce_order_status')</th>
        			<th>@lang('woocommerce::lang.equivalent_pos_sell_status')</th>
                    <th>@lang('woocommerce::lang.equivalent_shipping_status')</th>
        		</tr>
        		@foreach($woo_order_statuses as $key => $value)
        		<tr>
        			<td>
        				{{$value}}
        			</td>
        			<td>
        				{!! Form::select("order_statuses[$key]", $pos_sell_statuses, $default_settings['order_statuses'][$key] ?? null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
        			</td>
                    <td>
                        {!! Form::select("shipping_statuses[$key]", $shipping_statuses, $default_settings['shipping_statuses'][$key] ?? null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
                    </td>
        		</tr>
        		@endforeach
        	</table>
        </div>
    </div>
</div>