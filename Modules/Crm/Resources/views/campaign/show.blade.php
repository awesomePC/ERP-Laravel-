<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">{{$campaign->name}}</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-6">
                    <b>@lang('crm::lang.campaign_type'):</b>
                    @if($campaign->campaign_type == "sms")
                        {{__("crm::lang.sms")}}
                    @elseif($campaign->campaign_type == "email")
                        {{__("business.email")}}
                    @endif
                </div>
                @if(!empty($campaign->sent_on))
                    <div class="col-sm-6">
                      <p class="pull-right">
                        <b>@lang('crm::lang.sent_on'):</b> 
                            {{ @format_datetime($campaign->sent_on) }}
                        </p>
                    </div>
                @endif
            </div>
            
            @if($campaign->campaign_type == 'email')
                <div class="row mt-5">
                    <div class="col-sm-12">
                        <b>@lang('crm::lang.subject'):</b> {{$campaign->subject}}
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-sm-12">
                        <b>@lang('crm::lang.email_body'):</b>
                        {!! $campaign->email_body !!}
                    </div>
                </div>
            @elseif($campaign->campaign_type == 'sms')
                <div class="row">
                    <div class="col-sm-12">
                        <b>@lang('crm::lang.sms_body'):</b>
                       <p>{!! $campaign->sms_body !!}</p>
                    </div>
                </div>
            @endif

            @if(!empty($campaign->additional_info['to']) && $campaign->additional_info['to'] == 'transaction_activity')
                <div class="row">
                    <div class="col-md-6">
                        <b>@lang('crm::lang.transaction_activity'):</b>
                        @lang('crm::lang.'.$campaign->additional_info['trans_activity'])
                    </div>
                    <div class="col-md-6">
                        <b>@lang('crm::lang.in_days'):</b>
                        {{$campaign->additional_info['in_days']}}
                    </div>
                </div>
            @endif

            @php
                $leads = [];
                $customers = [];
            @endphp
            @if(count($notifiable_users) > 0) 
                @foreach($notifiable_users as $contact) 
                    @php
                        if($contact->type == 'lead') {
                            $leads[] = $contact->name; 
                        } else if($contact->type == 'customer') {
                            $customers[] = $contact->name; 
                        }
                    @endphp
                @endforeach 
            @endif
            <div class="row mt-5">
                @if(!empty($customers))
                    <div class="col-sm-12">
                        <strong>@lang('lang_v1.customers'): </strong>
                        <p>
                            {{implode(', ', $customers)}}
                        </p>
                    </div>
                @endif
                @if(!empty($leads))
                    <div class="col-sm-12">
                        <strong>@lang('crm::lang.leads'): </strong>
                        <p>
                            {{implode(', ', $leads)}}
                        </p>
                    </div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <span class="pull-left">
                <i class="fas fa-pencil-alt"></i>
                @lang('crm::lang.created_this_campaign_on', [
                    'name' => $campaign->createdBy->user_full_name
                ])
                {{@format_date($campaign->created_at)}}
            </span>
            <button type="button" class="btn btn-default" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->