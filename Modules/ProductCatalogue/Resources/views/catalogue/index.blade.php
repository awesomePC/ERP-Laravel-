@extends('layouts.guest')
@section('title', $business->name)

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header text-center" id="top">
    <h2>{{$business->name}}</h2>
    <h4 class="mb-0">{{$business_location->name}}</h4>
    <p>{!! $business_location->location_address !!}</p>
</section>
<section class="no-print">
    <div class="container">
        <!-- Static navbar -->
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand menu" href="#top">
                        @if(!empty($business->logo))
                            <img src="{{asset( 'uploads/business_logos/' . $business->logo)}}" alt="Logo" width="30">
                        @else
                            <i class="fas fa-boxes"></i>
                        @endif
                    </a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                    @foreach($categories as $key => $value)
                        <li><a href="#category{{$key}}" class="menu">{{$value}}</a></li>
                    @endforeach 
                        <li><a href="#category0" class="menu">Uncategorized</a></li>           
                    </ul>
                </div><!--/.nav-collapse -->
            </div><!--/.container-fluid -->
        </nav>
    </div> <!-- /container -->
</section>
<!-- Main content -->
<section class="content pt-0">
    <div class="container">
        @foreach($products as $product_category)
            <div class="row">
                <div class="col-md-12">
                    <h2 class="page-header" id="category{{$product_category->first()->category->id ?? 0}}">{{$product_category->first()->category->name ?? 'Uncategorized'}}</h2>
                </div>
            </div>
            <div class="row eq-height-row">
            @foreach($product_category as $product)
                <div class="col-md-3 eq-height-col col-xs-12">
                    <div class="box box-solid product-box">
                        <div class="box-body">
                            <a href="#" class="show-product-details" data-href="{{action('\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController@show',  [$business->id, $product->id])}}?location_id={{$business_location->id}}">
                            <img src="{{$product->image_url}}" class="img-responsive catalogue"></a>

                            @php
                                $discount = $discounts->firstWhere('brand_id', $product->brand_id);
                                if(empty($discount)){
                                    $discount = $discounts->firstWhere('category_id', $product->category_id);
                                }
                            @endphp

                            @if(!empty($discount))
                                <span class="label label-warning discount-badge">- {{($discount->discount_amount)}}%</span>
                            @endif

                            @php
                                $max_price = $product->variations->max('sell_price_inc_tax');
                                $min_price = $product->variations->min('sell_price_inc_tax');
                            @endphp
                            <h2 class="catalogue-title">
                                <a href="#" class="show-product-details" data-href="{{action('\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController@show',  [$business->id, $product->id])}}?location_id={{$business_location->id}}">
                                    {{$product->name}}
                                </a>
                            </h2>
                            <table class="table no-border product-info-table">
                                <tr>
                                    <th class="pb-0"> @lang('lang_v1.price'):</th>
                                    <td class="pb-0">
                                        <span class="display_currency" data-currency_symbol="true">{{($max_price)}}</span> @if($max_price != $min_price) - <span class="display_currency" data-currency_symbol="true">{{($min_price)}}</span> @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="pb-0"> @lang('product.sku'):</th>
                                    <td class="pb-0">{{$product->sku}}</td>
                                </tr>
                            @if($product->type == 'variable')
                                @php
                                    $variations = $product->variations->groupBy('product_variation_id');
                                @endphp
                                @foreach($variations as $product_variation)
                                    <tr>
                                        <th>{{$product_variation->first()->product_variation->name}}:</th>
                                        <td>
                                            <select class="form-control input-sm">
                                            @foreach($product_variation as $variation)
                                                <option value="{{$variation->id}}">{{$variation->name}} ({{$variation->sub_sku}}) - {{($variation->sell_price_inc_tax)}}</option>
                                            @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </table>
                        </div>
                    </div>
                </div>
            @if($loop->iteration%4 == 0)
                <div class="clearfix"></div>
            @endif
            @endforeach
            </div>
        @endforeach
    </div>
    <div class='scrolltop no-print'>
        <div class='scroll icon'><i class="fas fa-angle-up"></i></div>
    </div>
</section>
<!-- /.content -->
<!-- Add currency related field-->
<input type="hidden" id="__code" value="{{$business->currency->code}}">
<input type="hidden" id="__symbol" value="{{$business->currency->symbol}}">
<input type="hidden" id="__thousand" value="{{$business->currency->thousand_separator}}">
<input type="hidden" id="__decimal" value="{{$business->currency->decimal_separator}}">
<input type="hidden" id="__symbol_placement" value="{{$business->currency->currency_symbol_placement}}">
<input type="hidden" id="__precision" value="{{config('constants.currency_precision', 2)}}">
<input type="hidden" id="__quantity_precision" value="{{config('constants.quantity_precision', 2)}}">
<div class="modal fade product_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
@stop
@section('javascript')
<script type="text/javascript">

    (function($) {
    $(document).ready( function() {
        //Set global currency to be used in the application
        __currency_symbol = $('input#__symbol').val();
        __currency_thousand_separator = $('input#__thousand').val();
        __currency_decimal_separator = $('input#__decimal').val();
        __currency_symbol_placement = $('input#__symbol_placement').val();
        if ($('input#__precision').length > 0) {
            __currency_precision = $('input#__precision').val();
        } else {
            __currency_precision = 2;
        }

        if ($('input#__quantity_precision').length > 0) {
            __quantity_precision = $('input#__quantity_precision').val();
        } else {
            __quantity_precision = 2;
        }

        //Set page level currency to be used for some pages. (Purchase page)
        if ($('input#p_symbol').length > 0) {
            __p_currency_symbol = $('input#p_symbol').val();
            __p_currency_thousand_separator = $('input#p_thousand').val();
            __p_currency_decimal_separator = $('input#p_decimal').val();
        }

        __currency_convert_recursively($('.content'));
    });

    $(document).on('click', '.show-product-details', function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function(result) {
                $('.product_modal')
                    .html(result)
                    .modal('show');
                __currency_convert_recursively($('.product_modal'));
            },
        });
    });

    $(document).on('click', '.menu', function(e){
        e.preventDefault();
        $('.navbar-toggle').addClass('collapsed');
        $('.navbar-collapse').removeClass('in');

        var cat_id = $(this).attr('href');
        if ($(cat_id).length) {
            $('html, body').animate({
                scrollTop: $(cat_id).offset().top
            }, 1000);
        }
    });

    })(jQuery);

    $(window).scroll(function() {
        var height = $(window).scrollTop();

        if(height  > 180) {
            $('nav').addClass('navbar-fixed-top');
            $('.scrolltop:hidden').stop(true, true).fadeIn();
        } else {
            $('nav').removeClass('navbar-fixed-top');
            $('.scrolltop').stop(true, true).fadeOut();
        }
    });

    $(document).on('click', '.scroll', function(e){
        $("html,body").animate({scrollTop:$("#top").offset().top},"1000");
        return false;
    });
</script>
@endsection
