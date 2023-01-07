<div class="col-md-12">
    @component('components.widget', ['class' => 'box-solid', 'title' => __( 'essentials::lang.leaves_summary_for_user', ['user' => $user->user_full_name] )])
        <div class="table-responsive table-condensed">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th></th>
                        @foreach($statuses as $status)
                            <th>
                                {{$status['name']}}
                            </th>
                        @endforeach
                        <th>@lang('essentials::lang.max_allowed_leaves')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leave_types as $leave_type)
                        <tr>
                            <th>{{$leave_type->leave_type}}</strong></th>
                            @foreach($statuses as $k => $v)
                                <td>
                                    @if(!empty($leaves_summary[$leave_type->id][$k]))
                                        {{$leaves_summary[$leave_type->id][$k]}} @lang('lang_v1.days')
                                    @else
                                        0
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                @if(!empty($leave_type->max_leave_count))
                                    {{$leave_type->max_leave_count}} @lang('lang_v1.days')
                                    @if($leave_type->leave_count_interval == 'month')
                                        (@lang('essentials::lang.within_current_month'))
                                    @elseif($leave_type->leave_count_interval == 'year')
                                        (@lang('essentials::lang.within_current_fy'))
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th>@lang('sale.total')</th>
                        @foreach($status_summary as $count)
                            <td>{{$count}} @lang('lang_v1.days')</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endcomponent
</div>