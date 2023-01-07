<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action('\Modules\Crm\Http\Controllers\CrmDashboardController@index')}}"><i class="fas fa fa-broadcast-tower"></i> {{__('crm::lang.crm')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @if(auth()->user()->can('crm.access_all_leads') || auth()->user()->can('crm.access_own_leads'))
                    <li @if(request()->segment(2) == 'leads') class="active" @endif><a href="{{action('\Modules\Crm\Http\Controllers\LeadController@index'). '?lead_view=list_view'}}">@lang('crm::lang.leads')</a></li>
                    @endif
                    @if(auth()->user()->can('crm.access_all_schedule') || auth()->user()->can('crm.access_own_schedule'))
                    <li @if(request()->segment(2) == 'follow-ups') class="active" @endif><a href="{{action('\Modules\Crm\Http\Controllers\ScheduleController@index')}}">@lang('crm::lang.follow_ups')</a></li>
                    @endif
                    @if(auth()->user()->can('crm.access_all_campaigns') || auth()->user()->can('crm.access_own_campaigns'))
                        <li @if(request()->segment(2) == 'campaigns') class="active" @endif><a href="{{action('\Modules\Crm\Http\Controllers\CampaignController@index')}}">@lang('crm::lang.campaigns')</a></li>
                    @endif
                    @can('crm.access_contact_login')
                        <li @if(request()->segment(2) == 'all-contacts-login') class="active" @endif><a href="{{action('\Modules\Crm\Http\Controllers\ContactLoginController@allContactsLoginList')}}">@lang('crm::lang.contacts_login')</a></li>
                    @endcan
                    @can('crm.access_sources')
                        <li @if(request()->get('type') == 'source') class="active" @endif><a href="{{action('TaxonomyController@index') . '?type=source'}}">@lang('crm::lang.sources')</a></li>
                    @endcan
                    @can('crm.access_life_stage')
                        <li @if(request()->get('type') == 'life_stage') class="active" @endif><a href="{{action('TaxonomyController@index') . '?type=life_stage'}}">@lang('crm::lang.life_stage')</a></li>
                    @endcan

                    @if((auth()->user()->can('crm.view_all_call_log') || auth()->user()->can('crm.view_own_call_log')) && config('constants.enable_crm_call_log'))
                        <li @if(request()->segment(2) == 'call-log') class="active" @endif><a href="{{action('\Modules\Crm\Http\Controllers\CallLogController@index')}}">@lang('crm::lang.call_log')</a></li>
                    @endif

                    @can('crm.view_reports')
                    <li @if(request()->segment(2) == 'reports') class="active" @endif><a href="{{action('\Modules\Crm\Http\Controllers\ReportController@index')}}">@lang('report.reports')</a></li>
                    @endcan
                    <li @if(request()->segment(2) == 'proposal-template') class="active" @endif>
                        <a href="{{action('\Modules\Crm\Http\Controllers\ProposalTemplateController@index')}}">
                            @lang('crm::lang.proposal_template')
                        </a>
                    </li>
                    <li @if(request()->segment(2) == 'proposals') class="active" @endif>
                        <a href="{{action('\Modules\Crm\Http\Controllers\ProposalController@index')}}">
                            @lang('crm::lang.proposals')
                        </a>
                    </li>
                    <li @if(request()->segment(2) == 'settings') class="active" @endif>
                        <a href="{{action('\Modules\Crm\Http\Controllers\CrmSettingsController@index')}}">
                            @lang('business.settings')
                        </a>
                    </li>
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>