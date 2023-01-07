@forelse($attendance_by_date as $data)
<tr>
	<td>{{@format_date($data['date'])}}</td>
	<td>{{$data['present']}}</td>
	<td>{{$data['absent']}}</td>
</tr>
@empty
	<tr>
		<td colspan="3" class="text-center">@lang('essentials::lang.no_data_found')</td>
	</tr>
@endforelse