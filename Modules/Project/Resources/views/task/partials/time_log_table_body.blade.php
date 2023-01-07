@foreach($project_task->timeLogs as $timeLog)
<tr>
    <td>{{$loop->iteration}}</td>
    <td>
        {{@format_datetime($timeLog->start_datetime)}}
    </td>
    <td>
        {{@format_datetime($timeLog->end_datetime)}}
    </td>
    <td>
        @php
            $start_datetime = \Carbon::parse($timeLog->start_datetime);
            $end_datetime = \Carbon::parse($timeLog->end_datetime);
        @endphp
        {{$start_datetime->diffForHumans($end_datetime, true)}}
    </td>
    <td>
       {{$timeLog->user->user_full_name}}
    </td>
    <td>
       {!! $timeLog->note !!}
    </td>
</tr>
@endforeach