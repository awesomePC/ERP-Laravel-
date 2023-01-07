<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            @if(config('app.env') != 'demo')
                <p>
                    To send <mark>subscription expiry alert</mark> & <mark>automated application backup</mark> process you must setup a cron job with this command:<br/>
                    <code>{{$cron_job_command}}</code>
                </p>
                
                <p>
                    Set it in cron jobs tab in cpanel or directadmin or similar panel. <br/>Or edit crontab if using cloud/dedicated hosting. <br/>Or contact hosting for help with cron job settings.
                </p>
            @else
                @lang('lang_v1.disabled_in_demo')
            @endif
        </div>
    </div>
</div>