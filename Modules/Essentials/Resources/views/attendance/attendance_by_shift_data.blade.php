@forelse($attendance_by_shift as $data)
<tr>
	<td>{{$data['shift']}}</td>
	<td>{{$data['present']}}<br><small><span class="label bg-info">{!!implode('</span>,  <span class="label bg-info">', $data['present_users'])!!} </span></small></td>
	<td>{{$data['total'] - $data['present']}} <br><small><span class="label bg-info">{!! implode('</span>, <span class="label bg-info">', array_diff($data['all_users'], $data['present_users'])) !!}</span></small></td>
</tr>
@empty
	<tr>
		<td colspan="3" class="text-center">@lang('essentials::lang.no_data_found')</td>
	</tr>
@endforelse