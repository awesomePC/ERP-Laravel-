@extends('layouts.app')

@section('title', __('crm::lang.crm'))

@section('content')

@include('crm::layouts.nav')

<section class="content no-print">
    <div class="row row-custom">
        @can('customer.view')
        <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
          <div class="info-box info-box-new-style">
            <span class="info-box-icon bg-aqua"><i class="fas fa-user-friends"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ __('lang_v1.customers') }}</span>
              <span class="info-box-number">{{$total_customers}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        @endcan
        <!-- /.col -->
        @if(auth()->user()->can('crm.access_all_leads') || auth()->user()->can('crm.access_own_leads'))
        <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
          <div class="info-box info-box-new-style">
            <span class="info-box-icon bg-aqua"><i class="fas fa-user-check"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ __('crm::lang.leads') }}</span>
              <span class="info-box-number">{{$total_leads}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        @endif
        <!-- /.col -->
        @can('crm.access_sources')
        <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
          <div class="info-box info-box-new-style">
            <span class="info-box-icon bg-yellow">
                <i class="fas fa fa-search"></i>
            </span>

            <div class="info-box-content">
                <span class="info-box-text">{{ __('crm::lang.sources') }}</span>
              <span class="info-box-number">{{$total_sources}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        @endcan
        <!-- /.col -->

        <!-- fix for small devices only -->
        <!-- <div class="clearfix visible-sm-block"></div> -->
        @can('crm.access_life_stage')
        <div class="col-md-3 col-sm-6 col-xs-12 col-custom">
          <div class="info-box info-box-new-style">
            <span class="info-box-icon bg-yellow">
                <i class="fas fa-life-ring"></i>
            </span>

            <div class="info-box-content">
              <span class="info-box-text">{{ __('crm::lang.life_stages') }}</span>
              <span class="info-box-number invoice_due">{{$total_life_stage}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        @endcan
        <!-- /.col -->
    </div>
    <div class="row">
        @can('crm.access_sources')
        <div class="col-md-3">
            <div class="box box-solid">
                <div class="box-body p-10">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>{{ __('crm::lang.sources') }}</th>
                                <th>{{ __('sale.total') }}</th>
                                <th>{{ __('crm::lang.conversion') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sources as $source)
                                <tr>
                                    <td>{{$source->name}}</td>
                                    <td>
                                        @if(!empty($leads_count_by_source[$source->id]))
                                            {{$leads_count_by_source[$source->id]['count']}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($customers_count_by_source[$source->id]) && !empty($contacts_count_by_source[$source->id]))
                                            @php
                                                $conversion = ($customers_count_by_source[$source->id]['count']/$contacts_count_by_source[$source->id]['count']) * 100;
                                            @endphp
                                            {{$conversion . '%'}}
                                        @else 
                                            {{'0 %'}}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">@lang('lang_v1.no_data')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        @can('crm.access_life_stage')
        <div class="col-md-3">
            <div class="box box-solid">
                <div class="box-body p-10">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>{{ __('crm::lang.life_stages') }}</th>
                                <th>{{ __('sale.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($life_stages as $life_stage)
                                <tr>
                                    <td>{{$life_stage->name}}</td>
                                    <td>@if(!empty($leads_by_life_stage[$life_stage->id])){{count($leads_by_life_stage[$life_stage->id])}} @else 0 @endif</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">@lang('lang_v1.no_data')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endcan
        <div class="col-md-6">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <i class="fas fa fa-birthday-cake"></i>
                    <h3 class="box-title">@lang('crm::lang.birthdays')</h3>
                    <a data-href="{{action('\Modules\Crm\Http\Controllers\CampaignController@create')}}" class="btn btn-success btn-xs" id="wish_birthday">
                        <i class="fas fa-paper-plane"></i>
                        @lang('crm::lang.send_wishes')
                    </a>
                </div>
                <div class="box-body p-10">
                    <table class="table no-margin table-striped">
                        <caption>@lang('home.today')</caption>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('user.name')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todays_birthdays as $key => $birthday)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="contat_id" name="contat_id[]" value="{{$birthday['id']}}" id="contat_id_{{$birthday['id']}}">
                                    </td>
                                    <td>
                                        <label for="contat_id_{{$birthday['id']}}" class="cursor-pointer fw-100">
                                            {{$birthday['name']}}
                                        </label>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">@lang('lang_v1.no_data')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if(!empty($upcoming_birthdays))
                        <hr class="m-2">
                    @endif
                    <table class="table no-margin table-striped">
                        <caption>
                            @lang('crm::lang.upcoming')
                        </caption>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('user.name')</th>
                                <th>@lang('crm::lang.birthday_on')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcoming_birthdays as $key => $birthday)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="contat_id" name="contat_id[]" value="{{$birthday['id']}}" id="contat_id_{{$birthday['id']}}">
                                    </td>
                                    <td>
                                        <label for="contat_id_{{$birthday['id']}}" class="cursor-pointer fw-100">
                                            {{$birthday['name']}}
                                        </label>
                                    </td>
                                    <td>
                                        {{Carbon::createFromFormat('m-d', $birthday['dob'])->format('jS M')}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">@lang('lang_v1.no_data')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('css')
<style type="text/css">
    .fw-100 {
        font-weight: 100;
    }
    
</style>
@stop
@section('javascript')
	<script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(document).on('click', '#wish_birthday', function () {
                var url = $(this).data('href');
                var contact_ids = [];
                $("input.contat_id").each(function(){
                    if ($(this).is(":checked")) {
                        contact_ids.push($(this).val());
                    }
                });

                if (_.isEmpty(contact_ids)) {
                    alert("{{__('crm::lang.plz_select_user')}}");
                } else {
                    location.href = url+'?contact_ids='+contact_ids;
                }
            });
        });
    </script>
@endsection