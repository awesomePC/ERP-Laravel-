@extends('layouts.app')

@section('title', __('crm::lang.campaigns'))

@section('content')
@include('crm::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
   <h1>
        @lang('crm::lang.campaigns')
        <small>@lang('messages.edit')</small>
    </h1>
</section>
<section class="content no-print">
    <div class="box box-solid">
        <div class="box-body">
            {!! Form::open(['url' => action('\Modules\Crm\Http\Controllers\CampaignController@update', ['campaign' => $campaign->id]), 'method' => 'put', 'id' => 'campaign_form' ]) !!}
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            {!! Form::label('name', __('crm::lang.campaign_name') . ':*' )!!}
                            {!! Form::text('name', $campaign->name, ['class' => 'form-control', 'required' ]) !!}
                       </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('campaign_type', __('crm::lang.campaign_type') .':*') !!}
                            {!! Form::select('campaign_type', ['sms' => __('crm::lang.sms'), 'email' => __('business.email')], $campaign->campaign_type, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('to', __('crm::lang.to').':*') !!}
                            {!! Form::select('to', ['customer' => __('lang_v1.customers'), 'lead' => __('crm::lang.leads'), 'transaction_activity' => __('crm::lang.transaction_activity'), 'contact' => __('contact.contact')], $campaign->additional_info['to'] ?? null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-8 customer_div" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('contact_id', __('lang_v1.customers') .':*') !!}
                            <button type="button" class="btn btn-primary btn-xs select-all">
                                @lang('lang_v1.select_all')
                            </button>
                            <button type="button" class="btn btn-primary btn-xs deselect-all">
                                @lang('lang_v1.deselect_all')
                            </button>
                            {!! Form::select('contact_id[]', $customers, $campaign->contact_ids, ['class' => 'form-control select2', 'multiple', 'id' => 'contact_id', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-8 lead_div" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('lead_id', __('crm::lang.leads') .':*') !!}
                            <button type="button" class="btn btn-primary btn-xs select-all">
                                @lang('lang_v1.select_all')
                            </button>
                            <button type="button" class="btn btn-primary btn-xs deselect-all">
                                @lang('lang_v1.deselect_all')
                            </button>
                            {!! Form::select('lead_id[]', $leads, $campaign->contact_ids, ['class' => 'form-control select2', 'multiple', 'id' => 'lead_id', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-8 contact_div" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('contact', __('contact.contact') .':*') !!}
                            <button type="button" class="btn btn-primary btn-xs select-all">
                                @lang('lang_v1.select_all')
                            </button>
                            <button type="button" class="btn btn-primary btn-xs deselect-all">
                                @lang('lang_v1.deselect_all')
                            </button>
                            {!! Form::select('contact[]', $contacts, $campaign->contact_ids, ['class' => 'form-control select2', 'multiple', 'id' => 'contact', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-4 transaction_activity_div" style="display: none;">
                        <div class="form-group">
                            {!! Form::label('trans_activity', __('crm::lang.transaction_activity').':*') !!}
                            {!! Form::select('trans_activity', ['has_transactions' => __('crm::lang.has_transactions'), 'has_no_transactions' => __('crm::lang.has_no_transactions')], $campaign->additional_info['trans_activity'] ?? null, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-4 transaction_activity_div" style="display: none;">
                        <div class="form-group">
                            <label for="in_days">{{__('crm::lang.in_days')}}:*</label>
                            <div class="input-group">
                                <div class="input-group-addon">{{__('crm::lang.in')}}</div>
                                    <input type="text" class="form-control input_number" id="in_days" placeholder="0" name="in_days" required
                                        value="{{$campaign->additional_info['in_days'] ?? null}}">
                                <div class="input-group-addon">{{__('lang_v1.days')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="row email_div">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('subject', __('crm::lang.subject') . ':*' )!!}
                            {!! Form::text('subject', $campaign->subject, ['class' => 'form-control', 'required' ]) !!}
                       </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('email_body', __('crm::lang.email_body') . ':*') !!}
                            {!! Form::textarea('email_body', $campaign->email_body, ['class' => 'form-control ', 'id' => 'email_body', 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="row sms_div">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('sms_body', __('crm::lang.sms_body') . ':') !!}
                            {!! Form::textarea('sms_body', $campaign->sms_body, ['class' => 'form-control ', 'id' => 'sms_body', 'rows' => '6', 'required']); !!}
                        </div>
                    </div>
                </div>
                <strong>@lang('lang_v1.available_tags'):</strong>
                <p class="help-block">
                    {{implode(', ', $tags)}}
                </p>

                <button type="submit" class="btn btn-primary btn-sm submit-button pull-right draft m-5" name="send_notification" value="0" data-style="expand-right">
                    <span class="ladda-label">
                        <i class="fas fa-save"></i>
                        @lang('messages.update')
                    </span>
                </button>

                <button type="submit" class="btn btn-warning btn-sm pull-right submit-button notif m-5" name="send_notification" value="1" data-style="expand-right">
                    <span class="ladda-label">
                        <i class="fas fa-envelope-square"></i>
                        @lang('crm::lang.send_notification')
                    </span>
                </button>
            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('javascript')
    <script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(function () {
            
            $('select#to').change(function() {
                toggleFieldBasedOnTo($(this).val());
            });

            function toggleFieldBasedOnTo (to) {
                if (to == 'customer') {
                    $('div.customer_div').show();
                    $('div.lead_div').hide();
                    $('div.transaction_activity_div').hide();
                    $('div.contact_div').hide();
                } else if (to == 'lead') {
                    $('div.lead_div').show();
                    $('div.customer_div').hide();
                    $('div.transaction_activity_div').hide();
                    $('div.contact_div').hide();
                } else if (to == 'transaction_activity') {
                    $('div.transaction_activity_div').show();
                    $('div.customer_div').hide();
                    $('div.lead_div').hide();
                    $('div.contact_div').hide();
                } else if (to == 'contact') {
                    $('div.contact_div').show();
                    $('div.transaction_activity_div').hide();
                    $('div.customer_div').hide();
                    $('div.lead_div').hide();
                } else {
                    $('div.transaction_activity_div, div.customer_div, div.lead_div, div.contact_div').hide();
                }
            }

            toggleFieldBasedOnTo($('select#to').val());
        });
    </script>
@endsection 