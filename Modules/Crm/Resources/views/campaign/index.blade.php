@extends('layouts.app')

@section('title', __('crm::lang.campaigns'))

@section('content')
@include('crm::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
   <h1>@lang('crm::lang.campaigns')</h1>
</section>
<section class="content no-print">
	@component('components.filters', ['title' => __('report.filters')])
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('campaign_type', __('crm::lang.campaign_type') . ':') !!}
                    {!! Form::select('campaign_type', ['sms' => __('crm::lang.sms'), 'email' => __('business.email')], null, ['class' => 'form-control select2', 'id' => 'campaign_type_filter', 'placeholder' => __('messages.all')]); !!}
                </div>    
            </div>
        </div>
    @endcomponent
	@component('components.widget', ['class' => 'box-primary', 'title' => __('crm::lang.all_campaigns')])
        @slot('tool')
        	<div class="box-tools">
                <a class="btn btn-sm btn-primary pull-right m-5" href="{{action('\Modules\Crm\Http\Controllers\CampaignController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </a>
            </div>
        @endslot
        <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="campaigns_table">
		        <thead>
		            <tr>
		                <th> @lang('messages.action')</th>
		                <th>@lang('crm::lang.campaign_name')</th>
		                <th>@lang('crm::lang.campaign_type')</th>
		                <th>@lang('business.created_by')</th>
                        <th>@lang('lang_v1.created_at')</th>
		            </tr>
		        </thead>
		    </table>
        </div>
    @endcomponent
    <div class="modal fade campaign_modal" tabindex="-1" role="dialog"></div>
    <div class="modal fade campaign_view_modal" tabindex="-1" role="dialog"></div>
</section>
@endsection
@section('javascript')
	<script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			initializeCampaignDatatable();
		});
	</script>
@endsection