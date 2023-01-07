@extends('layouts.app')
@section('title', __('essentials::lang.my_payrolls'))
@section('content')
@include('essentials::layouts.nav_hrm')
<section class="content-header">
    <h1>
        @lang('essentials::lang.my_payrolls')
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#pay_components_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fab fa-gg-circle" aria-hidden="true"></i>
                            @lang('essentials::lang.pay_components')
                        </a>
                    </li>
                    <li class="">
                        <a href="#payrolls_tab" data-toggle="tab">
                            <i class="fas fa-coins" aria-hidden="true"></i>
                            @lang('essentials::lang.all_payrolls')
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="pay_components_tab">                        
                        <div class="row mt-5">
                           <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>@lang( 'lang_v1.description' )</th>
                                                <th>@lang( 'lang_v1.type' )</th>
                                                <th>@lang( 'sale.amount' )</th>
                                                <th>@lang( 'essentials::lang.applicable_date' )</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pay_components as $pay_component)
                                            <tr>
                                                <td>
                                                    {{$pay_component->description}}
                                                </td>
                                                <td>
                                                    {{__("essentials::lang." . $pay_component->type)}}
                                                </td>
                                                <td>
                                                    @if(!empty($pay_component->amount))
                                                        {{@num_format($pay_component->amount)}}
                                                    @endif
                                                    @if($pay_component->amount_type =="percent") {{'%'}} @endif
                                                </td>
                                                <td>
                                                    @if(!empty($pay_component->applicable_date))
                                                        {{@format_date($pay_component->applicable_date)}}
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    @lang('essentials::lang.no_data_found')
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                           </div>
                        </div>
                    </div>
                   <div class="tab-pane" id="payrolls_tab">
                        <div class="row mt-5">
                           <div class="col-md-12">
                               <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="my_payrolls" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>@lang( 'essentials::lang.month_year' )</th>
                                                <th>@lang( 'purchase.ref_no' )</th>
                                                <th>@lang( 'sale.total_amount' )</th>
                                                <th>@lang( 'sale.payment_status' )</th>
                                                <th>@lang( 'messages.action' )</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>         
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready( function(){
            my_payrolls_table = $('#my_payrolls').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{action('\Modules\Essentials\Http\Controllers\PayrollController@getMyPayrolls')}}"
                    },
                    columnDefs: [
                        {
                            targets: 4,
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    aaSorting: [[1, 'desc']],
                    columns: [
                        { data: 'transaction_date', name: 'transaction_date'},
                        { data: 'ref_no', name: 'ref_no'},
                        { data: 'final_total', name: 'final_total'},
                        { data: 'payment_status', name: 'payment_status'},
                        { data: 'action', name: 'action' },
                    ],
                    fnDrawCallback: function(oSettings) {
                        __currency_convert_recursively($('#my_payrolls'));
                    }
            });

            $('div.view_modal').on('shown.bs.modal', function(e) {
                __currency_convert_recursively($('.view_modal'));
            });
        });
    </script>
@endsection