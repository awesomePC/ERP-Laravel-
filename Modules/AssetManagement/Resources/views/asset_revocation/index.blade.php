@extends('layouts.app')
@section('title', __('assetmanagement::lang.revoked_asset'))
@section('content')
	@includeIf('assetmanagement::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	    <h1>
	    	@lang('assetmanagement::lang.revoked_asset')
	    </h1>
	</section>
	<!-- Main content -->
	<section class="content no-print">
		<div class="box box-solid">
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped" id="assest_revoked_table">
						<thead>
							<tr>
								<th>@lang('messages.action')</th>
								<th>@lang('assetmanagement::lang.revoke_code')</th>
								<th>@lang('assetmanagement::lang.revoked_for')</th>
								<th>@lang('assetmanagement::lang.allocation_code')</th>
								<th>@lang('assetmanagement::lang.asset_name')</th>
								<th>@lang('assetmanagement::lang.series_model')</th>
								<th>@lang('lang_v1.quantity')</th>
								<th>@lang('assetmanagement::lang.revoked_at')</th>
								<th>@lang('assetmanagement::lang.revoked_by')</th>
								<th>@lang('assetmanagement::lang.asset_category')</th>
								<th>
									@lang('assetmanagement::lang.reason')
								</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
@stop
@section('javascript')
<script type="text/javascript">
	$(document).ready(function () {
		assest_revoked_datatable = $("#assest_revoked_table").DataTable({
			processing: true,
            serverSide: true,
            ajax:{
                url: '/asset/revocation',
                "data": function ( d ) {
                    //
                }
            },
            columnDefs: [{
                targets: [0, 2, 8],
                orderable: false,
                searchable: false
            }],
            aaSorting:[[7, 'desc']],
            columns:[
                { data: 'action', name: 'action' },
                { data: 'ref_no', name: 'asset_transactions.ref_no'},
                { data: 'revoked_for', name: 'revoked_for' },
                { data: 'allocation_code', name: 'PT.ref_no'},
                { data: 'asset', name: 'assets.name' },
                { data: 'model', name: 'assets.model' },
                { data: 'quantity', name: 'asset_transactions.quantity' },
                { data: 'revoked_at', name : 'asset_transactions.transaction_datetime' },
                { data: 'revoked_by_name', name: 'revoked_by_name' },
                { data: 'category', name: 'CAT.name' },
                { data: 'reason', name: 'asset_transactions.reason' },
            ]
		});

		$(document).on('click', '#delete_revoked_asset', function () {
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
		                        assest_revoked_datatable.ajax.reload();
		                    } else {
		                        toastr.error(result.msg);
		                    }
		                }
		            });
		        }
		    });
		});
	})
</script>
@endsection