@extends('layouts.app')
@section('title', __( 'manufacturing::lang.manufacturing_report' ))

@section('content')
@include('manufacturing::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'manufacturing::lang.manufacturing_report' )
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="print_section"><h2>{{session()->get('business.name')}} - @lang( 'manufacturing::lang.manufacturing_report' )</h2></div>
    
    <div class="row no-print">
        <div class="col-md-3 col-md-offset-7 col-xs-6">
            <div class="input-group">
                <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>
                 <select class="form-control select2" id="mfg_report_location_filter">
                    @foreach($business_locations as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 col-xs-6">
            <div class="form-group pull-right">
                <div class="input-group">
                  <button type="button" class="btn btn-primary" id="mfg_report_date_filter">
                    <span>
                      <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-6">
            @component('components.widget')
                <table class="table table-striped">
                    <tr>
                        <th>{{ __('manufacturing::lang.total_production') }}:</th>
                        <td>
                            <span class="total_production">
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr> 
                    <tr>
                        <th>{{ __('manufacturing::lang.total_production_cost') }}:</th>
                        <td>
                            <span class="total_production_cost">
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>      
                </table>
            @endcomponent
        </div>

        <div class="col-xs-6">
            @component('components.widget')
                <table class="table table-striped">
                    <tr>
                        <th>{{ __('lang_v1.total_sold') }}:</th>
                        <td>
                            <span class="total_sold">
                                <i class="fa fa-refresh fa-spin fa-fw"></i>
                            </span>
                        </td>
                    </tr>
                </table>
            @endcomponent
        </div>
    </div>
    <br>
    <div class="row no-print">
        <div class="col-md-3">
            <a href="{{action('ReportController@getStockReport')}}?only_mfg=true" class="btn btn-info btn-flat btn-block">@lang('report.stock_report')</a>
        </div>
        @if(session('business.enable_lot_number') == 1)
        <div class="col-md-3">
            <a href="{{action('ReportController@getLotReport')}}?only_mfg=true" class="btn btn-success btn-flat btn-block">@lang('lang_v1.lot_report')</a>
        </div>
        @endif
        @if(session('business.enable_product_expiry') == 1)
        <div class="col-md-3">
            <a href="{{action('ReportController@getStockExpiryReport')}}?only_mfg=true" class="btn btn-warning btn-flat btn-block">@lang('report.stock_expiry_report')</a>
        </div>
        @endif
        <div class="col-md-3">
            <a href="{{action('ReportController@itemsReport')}}?only_mfg=true" class="btn btn-danger btn-flat btn-block">@lang('lang_v1.items_report')</a>
        </div>
    </div>
    <br>
    <div class="row no-print">
        <div class="col-sm-12">
            <button type="button" class="btn btn-primary pull-right" 
            aria-label="Print" onclick="window.print();"
            ><i class="fa fa-print"></i> @lang( 'messages.print' )</button>
        </div>
    </div>
	

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function() {
        if ($('#mfg_report_date_filter').length == 1) {
            $('#mfg_report_date_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#mfg_report_date_filter span').html(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                updateMfgReport();
            });
            $('#mfg_report_date_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#mfg_report_date_filter').html(
                    '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
                );
            });
        }
        updateMfgReport();
        $('#mfg_report_location_filter').change(function() {
            updateMfgReport();
        });

        function updateMfgReport() {
            var start = $('#mfg_report_date_filter')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end = $('#mfg_report_date_filter')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            var location_id = $('#mfg_report_location_filter').val();

            var data = { start_date: start, end_date: end, location_id: location_id };

            var loader = __fa_awesome();
            $(
                '.total_production, .total_sold, .total_production_cost'
            ).html(loader);

            $.ajax({
                method: 'GET',
                url: '/manufacturing/report',
                dataType: 'json',
                data: data,
                success: function(data) {
                    $('.total_production').html(__currency_trans_from_en(data.total_production, true));
                    $('.total_sold').html(__currency_trans_from_en(data.total_sold, true));
                    $('.total_production_cost').html(__currency_trans_from_en(data.total_production_cost, true));
                },
            });
        }
    });
</script>

@endsection
