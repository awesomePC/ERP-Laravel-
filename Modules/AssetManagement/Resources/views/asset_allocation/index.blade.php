@extends('layouts.app')
@section('title', __('assetmanagement::lang.asset_allocated'))
@section('content')
	@includeIf('assetmanagement::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	    <h1>
	    	@lang('assetmanagement::lang.asset_allocated')
	    </h1>
	</section>
	<!-- Main content -->
	<section class="content no-print">
		<div class="box box-solid">
			<div class="box-header with-border">
				<div class="box-tools pull-right">
					<a class="btn btn-sm btn-primary" id="allocate_asset" data-href="{{action('\Modules\AssetManagement\Http\Controllers\AssetAllocationController@create')}}">
					    <i class="fa fa-plus"></i>
					    @lang('messages.add')
					</a>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped" id="assest_allocated_table">
						<thead>
							<tr>
								<th>@lang('messages.action')</th>
								<th>@lang('assetmanagement::lang.allocation_code')</th>
								<th>@lang('assetmanagement::lang.allocated_to')</th>
								<th>@lang('assetmanagement::lang.asset_name')</th>
								<th>@lang('assetmanagement::lang.series_model')</th>
								<th>@lang('lang_v1.quantity')</th>
								<th>
									@lang('assetmanagement::lang.revoked_qty')
								</th>
								<th>@lang('assetmanagement::lang.allocate_from')</th>
								<th>@lang('assetmanagement::lang.allocated_upto')</th>
								<th>@lang('assetmanagement::lang.allocated_by')</th>
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
	<div class="modal fade" id="asset_revoke_modal" tabindex="-1" role="dialog"></div>
	<div class="modal fade" id="allocate_asset_modal" tabindex="-1" role="dialog"></div>
	<div class="modal fade" id="edit_allocate_asset_modal" tabindex="-1" role="dialog"></div>
@stop
@section('javascript')
<script type="text/javascript">
	$(document).ready(function () {
		assest_allocated_datatable = $("#assest_allocated_table").DataTable({
			processing: true,
            serverSide: true,
            ajax:{
                url: '/asset/allocation',
                "data": function ( d ) {
                    //
                }
            },
            columnDefs: [{
                targets: [0, 2, 6, 9],
                orderable: false,
                searchable: false
            }],
            aaSorting:[[7, 'desc']],
            columns:[
                { data: 'action', name: 'action' },
                { data: 'ref_no', name: 'asset_transactions.ref_no'},
                { data: 'receiver_name', name: 'receiver_name' },
                { data: 'asset', name: 'assets.name' },
                { data: 'model', name: 'assets.model' },
                { data: 'quantity', name: 'asset_transactions.quantity' },
                { data: 'revoked_quantity', name: 'revoked_quantity' },
                { data: 'allocated_at', name : 'asset_transactions.transaction_datetime' },
                { data: 'allocated_upto', name : 'asset_transactions.allocated_upto' },
                { data: 'provider_name', name: 'provider_name' },
                { data: 'category', name: 'CAT.name' },
                { data: 'reason', name: 'asset_transactions.reason' },
            ]
		});

		$(document).on('click', '#delete_allocated_asset', function () {
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
		                        assest_allocated_datatable.ajax.reload();
		                    } else {
		                        toastr.error(result.msg);
		                    }
		                }
		            });
		        }
		    });
		});

		$(document).on('click', '.revoke_allocated_asset', function () {
			var url = $(this).data('href');
			$.ajax({
				method: "GET",
				url: url,
				dataType: 'html',
				success: function (result) {
					$("#asset_revoke_modal").html(result).modal('show');
				}
			});
		});

		$('#asset_revoke_modal').on('shown.bs.modal', function () {
			
			$('form#revoke_asset_form #transaction_datetime').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

		  	$("form#revoke_asset_form").validate({
		  		submitHandler: function(form) {
                    form.submit();
                }
		  	});
		})

		$(document).on('click', '#allocate_asset', function () {
			var url = $(this).data('href');
			$.ajax({
				method: "GET",
				url: url,
				dataType: 'html',
				success: function (result) {
					$("#allocate_asset_modal").html(result).modal('show');
				}
			});
		});

		$('#allocate_asset_modal').on('shown.bs.modal', function () {
			
			$('form#asset_allocation_form').validate({
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $('#transaction_datetime').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            $('#allocated_upto').datepicker({
		        autoclose: true,
		        format:datepicker_date_format
		    });

            @if(!empty($asset_id))
                var quantity = $('select#asset_id').find(':selected').data('quantity');
                if (!_.isUndefined(quantity)) {
                    $("input#quantity").attr('max', parseInt(quantity));
                }
            @endif

            $(document).on('change', 'select#asset_id', function () {
                var quantity = $(this).find(':selected').data('quantity');
                if (!_.isUndefined(quantity)) {
                    $("input#quantity").attr('max', parseInt(quantity));
                } else {
                    $("input#quantity").removeAttr('max');
                }
            });
		})

		$(document).on('click', '.edit_allocated_asset', function () {
			var url = $(this).data('href');
			console.log(url)
			$.ajax({
				method: "GET",
				url: url,
				dataType: 'html',
				success: function (result) {
					$("#allocate_asset_modal").html(result).modal('show');
				}
			});
		});

		$('#edit_allocate_asset_modal').on('shown.bs.modal', function () {
			
			$('form#asset_allocation_form').validate({
                submitHandler: function(form) {
                    form.submit();
                }
            });

            $('#transaction_datetime').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
		})
		
	})
</script>
@endsection