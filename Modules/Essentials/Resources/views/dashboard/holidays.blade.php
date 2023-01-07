<div class="col-md-4 col-sm-6 col-xs-12 col-custom">
    <div class="box box-solid">
        <div class="box-header with-border">
            <i class="fas fa-suitcase-rolling"></i>
            <h3 class="box-title">@lang('essentials::lang.holidays')</h3>
        </div>
        <div class="box-body p-10">
            <table class="table no-margin">
                <tbody>
                    <tr>
                        <th class="bg-light-gray" colspan="3">@lang('home.today')</th>
                    </tr>
                    @forelse($todays_holidays as $holiday)
                        @php
                            $start_date = \Carbon::parse($holiday->start_date);
                            $end_date = \Carbon::parse($holiday->end_date);

                            $diff = $start_date->diffInDays($end_date);
                            $diff += 1;
                        @endphp
                        <tr>
                            <td>{{$holiday->name}}</td>
                            <td>{{@format_date($holiday->start_date)}} ({{ $diff . ' ' . str_plural(__('lang_v1.day'), $diff)}})</td>
                            <td>{{$holiday->location->name ?? __("lang_v1.all")}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">@lang('lang_v1.no_data')</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <th class="bg-light-gray" colspan="3">@lang('lang_v1.upcoming')</th>
                    </tr>
                    @forelse($upcoming_holidays as $holiday)
                        @php
                            $start_date = \Carbon::parse($holiday->start_date);
                            $end_date = \Carbon::parse($holiday->end_date);

                            $diff = $start_date->diffInDays($end_date);
                            $diff += 1;
                        @endphp
                        <tr>
                            <td>{{$holiday->name}}</td>
                            <td>{{@format_date($holiday->start_date)}} ({{ $diff . ' ' . str_plural(__('lang_v1.day'), $diff)}})</td>
                            <td>{{$holiday->location->name ?? __("lang_v1.all")}}</td>
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