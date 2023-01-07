<table class="table table-striped">
	<thead>
		<tr>
			<th>@lang('crm::lang.document')</th>
			<th>@lang('messages.action')</th>
		</tr>
	</thead>
	<tbody>
		@foreach($medias as $media)
			<tr class="media_row">
				<td>
					@if(isFileImage($media->display_url))
						{!! $media->thumbnail() !!}
						<br>
					@endif
					<a href="{{$media->display_url}}" class="cursor-pointer"target="_blank">
						{{$media->display_name}}	
					</a>
				</td>
				<td>
					<a href="{{$media->display_url}}" download="{{$media->display_name}}" class="btn btn-success btn-sm">
						<i class="fas fa-download"></i>
					</a>
					<a data-href="{{action('\Modules\Crm\Http\Controllers\ProposalTemplateController@deleteProposalMedia', ['id' => $media->id])}}" class="btn btn-danger btn-sm delete_attachment">
						<i class="fas fa-trash-alt"></i>
					</a>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>