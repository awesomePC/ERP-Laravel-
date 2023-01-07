<div class="pos-tab-content active">
    <div class="row">
    	<div class="col-sm-12">
    		<ul>
    			<li>{!! __('woocommerce::lang.ins_1') !!}</li>
    			<li>{!! __('woocommerce::lang.ins_2') !!}</li>
    			<li>{!! __('woocommerce::lang.api_settings_help_text') !!}. <a href="https://docs.woocommerce.com/document/woocommerce-rest-api/#section-3" target="_blank">@lang('lang_v1.click_here')</a> @lang('lang_v1.for_more_info')</li>
    			<li>{!! __('woocommerce::lang.api_settings_help_permalink') !!}</li>
                <li>{!! __('woocommerce::lang.api_settings_help_permalink_reset') !!}</li>
                @if(config('app.env') != 'demo')
                    <li>
                        <p>
                            To <mark>Auto Sync</mark> categories, products and orders you must setup a cron job with this command:<br/>
                            <code>{{$cron_job_command}}</code>
                        </p>
                        
                        <p>
                            Set it in cron jobs tab in cpanel or directadmin or similar panel. <br/>Or edit crontab if using cloud/dedicated hosting. <br/>Or contact hosting for help with cron job settings.
                        </p>
                    </li>
                @endif
    		</ul>
    	</div>
    </div>
</div>