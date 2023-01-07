@extends('layouts.app')
@section('title', __('woocommerce::lang.woocommerce'))

@section('content')
@include('woocommerce::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('woocommerce::lang.woocommerce')</h1>
</section>

<!-- Main content -->
<section class="content">
    @php
        $is_superadmin = auth()->user()->can('superadmin');
    @endphp
    <div class="row">
        @if(!empty($alerts['connection_failed']))
        <div class="col-sm-12">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <ul>
                    <li>{{$alerts['connection_failed']}}</li>
                </ul>
            </div>
        </div>
        @endif

        <div class="col-sm-6">
            @if($is_superadmin || auth()->user()->can('woocommerce.syc_categories') )
            <div class="col-sm-12">
               <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-tags"></i>
                        <h3 class="box-title">@lang('woocommerce::lang.sync_product_categories'):</h3>
                    </div>
                    <div class="box-body">
                        @if(!empty($alerts['not_synced_cat']) || !empty($alerts['updated_cat']))
                        <div class="col-sm-12">
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <ul>
                                    @if(!empty($alerts['not_synced_cat']))
                                        <li>{{$alerts['not_synced_cat']}}</li>
                                    @endif
                                    @if(!empty($alerts['updated_cat']))
                                        <li>{{$alerts['updated_cat']}}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        @endif
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-primary btn-block" id="sync_product_categories"> <i class="fa fa-refresh"></i> @lang('woocommerce::lang.sync')</button>
                            <span class="last_sync_cat"></span>
                        </div>
                        <div class="col-sm-12">
                            <br>
                            <button type="button" class="btn btn-danger btn-xs" id="reset_categories"> <i class="fa fa-undo"></i> @lang('woocommerce::lang.reset_synced_cat')</button>
                        </div>
                    </div>
               </div>
            </div>
            @endif
            @if($is_superadmin || auth()->user()->can('woocommerce.map_tax_rates') )
            <div class="col-sm-12">
               <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-percent"></i>
                        <h3 class="box-title">@lang('woocommerce::lang.map_tax_rates'):</h3>
                    </div>
                    <div class="box-body">
                        {!! Form::open(['action' => '\Modules\Woocommerce\Http\Controllers\WoocommerceController@mapTaxRates', 'method' => 'post']) !!}
                        <div class="col-xs-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>@lang('woocommerce::lang.pos_tax_rate')</th>
                                        <th>@lang('woocommerce::lang.equivalent_woocommerce_tax_rate')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($tax_rates))
                                        @foreach($tax_rates as $tax_rate)
                                            <tr>
                                                <td>{{$tax_rate->name}}:</td>
                                                <td>{!! Form::select('taxes[' . $tax_rate->id . ']', $woocommerce_tax_rates, $tax_rate->woocommerce_tax_rate_id, ['class' => 'form-control']) !!}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" >
                                            <button type="submit" class="btn btn-danger pull-right">
                                                @lang('messages.save')
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="col-sm-6">
            @if($is_superadmin || auth()->user()->can('woocommerce.sync_products') )
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-cubes"></i>
                        <h3 class="box-title">@lang('woocommerce::lang.sync_products'):</h3>
                    </div>
                    <div class="box-body">
                        @if(!empty($alerts['not_synced_product']) || !empty($alerts['not_updated_product']))
                        <div class="col-sm-12">
                            <div class="alert alert-warning alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <ul>
                                    @if(!empty($alerts['not_synced_product']))
                                        <li>{{$alerts['not_synced_product']}}</li>
                                    @endif
                                    @if(!empty($alerts['not_updated_product']))
                                        <li>{{$alerts['not_updated_product']}}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        @endif
                        <div class="col-sm-6">
                            <div style="display: inline-flex; width: 100%;">
                                <button type="button" class="btn btn-warning btn-block sync_products" data-sync-type="new"> <i class="fa fa-refresh"></i> @lang('woocommerce::lang.sync_only_new')</button> &nbsp;@show_tooltip(__('woocommerce::lang.sync_new_help'))
                            </div>
                            <span class="last_sync_new_products"></span>
                        </div>
                        <div class="col-sm-6">
                            <div style="display: inline-flex; width: 100%;">
                                <button type="button" class="btn btn-primary btn-block sync_products" data-sync-type="all"> <i class="fa fa-refresh"></i> @lang('woocommerce::lang.sync_all')</button> &nbsp;@show_tooltip(__('woocommerce::lang.sync_all_help'))
                            </div>
                            <span class="last_sync_all_products"></span>
                        </div>
                        <div class="col-sm-12">
                            <br>
                            <button type="button" class="btn btn-danger btn-xs" id="reset_products"> <i class="fa fa-undo"></i> @lang('woocommerce::lang.reset_synced_products')</button>
                        </div>
                    </div>
               </div>
           </div>
           @endif
           @if($is_superadmin || auth()->user()->can('woocommerce.sync_orders') )
           <div class="col-sm-12">
               <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-cart-plus"></i>
                        <h3 class="box-title">@lang('woocommerce::lang.sync_orders'):</h3>
                    </div>
                    <div class="box-body">
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-success btn-block" id="sync_orders"> <i class="fa fa-refresh"></i> @lang('woocommerce::lang.sync')</button>
                            <span class="last_sync_orders"></span>
                        </div>
                    </div>
               </div>
            </div>
            @endif
        </div>
    </div>
    
</section>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function() {
        syncing_text = '<i class="fa fa-refresh fa-spin"></i> ' + "{{__('woocommerce::lang.syncing')}}...";
        update_sync_date();

        //Sync Product Categories
        $('#sync_product_categories').click( function(){
            $(window).bind('beforeunload', function(){
                return true;
            });
            var btn_html = $(this).html(); 
            $(this).html(syncing_text); 
            $(this).attr('disabled', true);
            $.ajax({
                url: "{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@syncCategories')}}",
                dataType: "json",
                timeout: 0,
                success: function(result){
                    if(result.success){
                        toastr.success(result.msg);
                        update_sync_date();
                    } else {
                        toastr.error(result.msg);
                    }
                    $('#sync_product_categories').html(btn_html);
                    $('#sync_product_categories').removeAttr('disabled');
                    $(window).unbind('beforeunload');
                }
            });          
        });

        //Sync Products
        $('.sync_products').click( function(){
            $(window).bind('beforeunload', function(){
                return true;
            });
            var btn = $(this);
            var btn_html = btn.html();
            btn.html(syncing_text); 
            btn.attr('disabled', true);

            sync_products(btn, btn_html);     
        });

        //Sync Orders
        $('#sync_orders').click( function(){
            $(window).bind('beforeunload', function(){
                return true;
            });
            var btn = $(this);
            var btn_html = btn.html(); 
            btn.html(syncing_text); 
            btn.attr('disabled', true);

            $.ajax({
                url: "{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@syncOrders')}}",
                dataType: "json",
                timeout: 0,
                success: function(result){
                    if(result.success){
                        toastr.success(result.msg);
                        update_sync_date();
                    } else {
                        toastr.error(result.msg);
                    }
                    btn.html(btn_html);
                    btn.removeAttr('disabled');
                    $(window).unbind('beforeunload');
                }
            });            
        });
    });

    function update_sync_date() {
        $.ajax({
            url: "{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@getSyncLog')}}",
            dataType: "json",
            timeout: 0,
            success: function(data){
                if(data.categories){
                    $('span.last_sync_cat').html('<small>{{__("woocommerce::lang.last_synced")}}: ' + data.categories + '</small>');
                }
                if(data.new_products){
                    $('span.last_sync_new_products').html('<small>{{__("woocommerce::lang.last_synced")}}: ' + data.new_products + '</small>');
                }
                if(data.all_products){
                    $('span.last_sync_all_products').html('<small>{{__("woocommerce::lang.last_synced")}}: ' + data.all_products + '</small>');
                }
                if(data.orders){
                    $('span.last_sync_orders').html('<small>{{__("woocommerce::lang.last_synced")}}: ' + data.orders + '</small>');
                }
                
            }
        });     
    }

    //Reset Synced Categories
    $(document).on('click', 'button#reset_categories', function(){
        var checkbox = document.createElement("div");
        checkbox.setAttribute('class', 'checkbox');
        checkbox.innerHTML = '<label><input type="checkbox" id="yes_reset_cat"> {{__("woocommerce::lang.yes_reset")}}</label>';
        swal({
          title: LANG.sure,
          text: "{{__('woocommerce::lang.confirm_reset_cat')}}",
          icon: "warning",
          content: checkbox,
          buttons: true,
          dangerMode: true,
        }).then((confirm) => {
            if(confirm) {
                if($('#yes_reset_cat').is(":checked")) {
                    $(window).bind('beforeunload', function(){
                        return true;
                    });
                    var btn = $(this);
                    btn.attr('disabled', true);
                    $.ajax({
                        url: "{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@resetCategories')}}",
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                            btn.removeAttr('disabled');
                            $(window).unbind('beforeunload');
                            location.reload();
                        }
                    });
                }
            }
        });
    });

    //Reset Synced products
    $(document).on('click', 'button#reset_products', function(){
        var checkbox = document.createElement("div");
        checkbox.setAttribute('class', 'checkbox');
        checkbox.innerHTML = '<label><input type="checkbox" id="yes_reset_product"> {{__("woocommerce::lang.yes_reset")}}</label>';
        swal({
          title: LANG.sure,
          text: "{{__('woocommerce::lang.confirm_reset_product')}}",
          icon: "warning",
          content: checkbox,
          buttons: true,
          dangerMode: true,
        }).then((confirm) => {
            if(confirm) {
                if($('#yes_reset_product').is(":checked")) {
                    $(window).bind('beforeunload', function(){
                        return true;
                    });
                    var btn = $(this);
                    btn.attr('disabled', true);
                    $.ajax({
                        url: "{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@resetProducts')}}",
                        dataType: "json",
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                            btn.removeAttr('disabled');
                            $(window).unbind('beforeunload');
                            location.reload();
                        }
                    });
                }
            }
        });
    });

    function sync_products(btn, btn_html, offset = 0) {
        var type = btn.data('sync-type');
        $.ajax({
            url: "{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@syncProducts')}}?type=" + type + "&offset=" + offset,
            dataType: "json",
            timeout: 0,
            success: function(result){
                if(result.success){
                    if (result.total_products > 0) {
                        offset++;
                        sync_products(btn, btn_html, offset)
                    } else {
                        update_sync_date();
                        btn.html(btn_html);
                        btn.removeAttr('disabled');
                        $(window).unbind('beforeunload');
                    }
                    toastr.success(result.msg);
                    
                } else {
                    toastr.error(result.msg);
                    btn.html(btn_html);
                    btn.removeAttr('disabled');
                    $(window).unbind('beforeunload');
                }
            }
        });     
    }

</script>
@endsection