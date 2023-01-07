@extends('layouts.app')
@section('title', __('assetmanagement::lang.asset_maintenance'))
@section('content')
	@includeIf('assetmanagement::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	    <h1>
	    	@lang('assetmanagement::lang.asset_maintenance')
	    </h1>
	</section>
	<!-- Main content -->
	<section class="content no-print">
		@component('components.filters', ['title' => __('report.filters')])
			<div class="col-md-4">
	            <div class="form-group">
	                {!! Form::label('status_filter',  __('sale.status') . ':') !!}
	                {!! Form::select('status_filter', $statuses, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
	            </div>
	        </div>
	        <div class="col-md-4">
	            <div class="form-group">
	                {!! Form::label('priority_filter',  __('lang_v1.priority') . ':') !!}
	                {!! Form::select('priority_filter', $priorities, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
	            </div>
	        </div>
	        <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('assigned_to_filter', __('lang_v1.assigned_to') . ':' )!!}
                    {!! Form::select('assigned_to_filter', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
		@endcomponent
		<div class="box box-solid">
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-striped" id="asset_maintenance_table">
						<thead>
							<tr>
								<th>
									@lang('assetmanagement::lang.maintenance_id')
								</th>
								<th>
									@lang('assetmanagement::lang.asset')
								</th>
								<th>
									@lang('sale.status')
								</th>
								<th>
									@lang('lang_v1.priority')
								</th>
								<th>@lang('lang_v1.warranty')</th>
								<th>
									@lang('assetmanagement::lang.details')
								</th>
								<th>
									@lang('assetmanagement::lang.datetime')
								</th>
								<th>
									@lang('lang_v1.assigned_to')
								</th>
								<th>
									@lang('business.created_by')
								</th>
								<th>
									@lang('messages.action')
								</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</section>
	<div class="modal fade" id="asset_maintenance_modal" tabindex="-1" role="dialog"></div>
@stop
@section('javascript')
<script type="text/javascript">
	$(document).ready( function(){
		asset_maintenance_table = $('#asset_maintenance_table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: '/asset/asset-maintenance',
	        ajax:{
                url: '/asset/asset-maintenance',
                "data": function ( d ) {
                    d.status = $('#status_filter').val();
                    d.priority = $('#priority_filter').val();
                    d.assigned_to = $('#assigned_to_filter').val();
                }
            },
	        columns: [
	            { data: 'maitenance_id', name: 'maitenance_id' },
	            { data: 'asset_name', name: 'asset_name', searchable: false },
	            { data: 'status', name: 'status' },
	            { data: 'priority', name: 'priority' },
	            { data: 'warranty', name: 'warranty', searchable: false,  orderable: false},
	            { data: 'details', name: 'details' },
	            { data: 'created_at', name: 'created_at' },
	            { data: 'assigned_to_user', name: 'assigned_to_user' },
	            { data: 'created_by_user', name: 'created_by_user' },
	            { data: 'action', orderable: false, searchable: false,}
	        ],
	    });

	    $(document).on('change', '#status_filter, #priority_filter, #assigned_to_filter', function(){
			asset_maintenance_table.ajax.reload();
		});

	    $(document).on('click', '.edit_maintenance', function () {
			var url = $(this).data('href');
			$.ajax({
				method: 'GET',
				dataType: 'html',
				url: url,
				success: function (response) {
					$("#asset_maintenance_modal").html(response).modal('show');
				}
			});
		});

	    $('#asset_maintenance_modal').on('shown.bs.modal', function () {
			var fileinput_setting = {
		        showUpload: false,
		        showPreview: false,
		        browseLabel: LANG.file_browse_label,
		        removeLabel: LANG.remove,
		    };
			$('#attachments').fileinput(fileinput_setting);

			$('#asset_maintenance_modal').find('.select2').select2({
				dropdownParent : $('#asset_maintenance_modal')
			});
		});

		$(document).on('click', '#delete_asset_maintenance', function () {
			var url = $(this).data('href');
			swal({
		      title: LANG.sure,
		      icon: "warning",
		      buttons: true,
		      dangerMode: true,
		    }).then((confirmed) => {
		        if (confirmed) {
		            $.ajax({
		                method:'DELETE',
		                dataType: 'json',
		                url: url,
		                success: function(result){
		                    if (result.success) {
		                        toastr.success(result.msg);
		                        asset_maintenance_table.ajax.reload();
		                    } else {
		                        toastr.error(result.msg);
		                    }
		                }
		            });
		        }
		    });
		});
	});
</script>
@endsection