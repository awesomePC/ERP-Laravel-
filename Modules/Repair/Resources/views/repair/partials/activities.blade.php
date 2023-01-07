<!-- /.box-header -->
<div class="box-body">
    <table class="table table-condensed bg-gray">
        <tr>
            <th>@lang('lang_v1.date')</th>
            <th>@lang('messages.action')</th>
            <th>@lang('lang_v1.by')</th>
            <th>@lang('brand.note')</th>
        </tr>
        @forelse($activities as $activity)
            @if($activity->description != 'is_sent_notification')
                <tr>
                    <td>{{@format_datetime($activity->created_at)}}</td>
                    <td>
                        @if($activity->description == 'status_changed')
                            @lang('repair::lang.status_changed_to', ['status' => $activity->getExtraProperty('updated_status')])
                        @else
                            {{__('lang_v1.' . $activity->description)}}
                        @endif
                    </td>
                    <td>{{$activity->causer->user_full_name}}</td>
                    <td>
                        @if(!empty($activity->getExtraProperty('update_note')))
                            {{$activity->getExtraProperty('update_note')}}
                            <br>
                        @endif
                        @if(!empty($activity->getExtraProperty('completed_on_from')))
                            @lang('repair::lang.completed_on_changed')
                            @lang('account.from'): {{@format_datetime($activity->getExtraProperty('completed_on_from'))}}
                            @lang('account.to'): {{@format_datetime($activity->getExtraProperty('completed_on_to'))}}
                        @endif
                    </td>
                </tr>
            @endif
        @empty
            <tr>
              <td colspan="4" class="text-center">
                @lang('purchase.no_records_found')
              </td>
            </tr>
        @endforelse
    </table>
</div>
<!-- /.box-body -->