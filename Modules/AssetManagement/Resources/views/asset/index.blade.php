@extends('layouts.app')
@section('title', __('assetmanagement::lang.assets'))
@section('content')
	@includeIf('assetmanagement::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	    <h1>
	    	@lang('assetmanagement::lang.assets')
	    </h1>
	</section>
	<!-- Main content -->
	<section class="content no-print">
		@component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('asset_list_filter_location_id',  __('purchase.business_location') . ':') !!}
                {!! Form::select('asset_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
            </div>
        </div>
        <div class="col-md-3">
            {!! Form::label('asset_list_filter_category_id', __('assetmanagement::lang.asset_category') . ':' )!!}
            {!! Form::select('asset_list_filter_category_id', $asset_category, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']); !!}
        </div>
        <div class="col-md-3">
            {!! Form::label('asset_list_filter_purchase_type', __('assetmanagement::lang.purchase_type') . ':' )!!}
            {!! Form::select('asset_list_filter_purchase_type', $purchase_types, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']); !!}
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="asset_list_filter_allocatable" value="1" class="input-icheck" id="asset_list_filter_allocatable">
                        @lang('assetmanagement::lang.is_allocatable')
                    </label>
                </div>
            </div>
        </div>
		@endcomponent
		<div class="box box-solid">
			<div class="box-header with-border">
				<h5 class="box-title">
					@lang('assetmanagement::lang.all_assets')
				</h5>
				<div class="box-tools pull-right">
					<a type="button" class="btn btn-sm btn-primary" data-href="{{action('\Modules\AssetManagement\Http\Controllers\AssetController@create')}}" id="add_asset">
					    <i class="fa fa-plus"></i>
					    @lang('messages.add')
					</a>
				</div>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped" id="assest_table">
						<thead>
							<tr>
								<th>@lang('messages.action')</th>
								<th>@lang('assetmanagement::lang.asset_code')</th>
								<th>@lang('assetmanagement::lang.asset_name')</th>
								<th>@lang('lang_v1.quantity')</th>
								<th>@lang('lang_v1.warranty')</th>
								<th>@lang('assetmanagement::lang.is_allocatable')</th>
								<th>@lang('purchase.purchase_date')</th>
								<th>@lang('assetmanagement::lang.allocated_qty')</th>
								<th>@lang('sale.unit_price')</th>
								<th>@lang('assetmanagement::lang.series_model')</th>
								<th>
									@lang('lang_v1.image')
								</th>
								<th>@lang('business.business_location')</th>
								<th>@lang('assetmanagement::lang.asset_category')</th>
								<th>
									@lang('lang_v1.description')
								</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
	<div class="modal fade" id="asset_modal" tabindex="-1" role="dialog"></div>
	<div class="modal fade" id="allocate_asset_modal" tabindex="-1" role="dialog"></div>
	<div class="modal fade" id="asset_maintenance_modal" tabindex="-1" role="dialog"></div>
@stop
@section('javascript')
<script type="text/javascript">
	$(document).ready(function () {
		assest_datatable = $("#assest_table").DataTable({
			processing: true,
            serverSide: true,
            scrollY:        "75vh",
            scrollX:        true,
            scrollCollapse: true,
            ajax:{
                url: '/asset/assets',
                "data": function ( d ) {
                    d.location_id = $('#asset_list_filter_location_id').val();
                    d.category_id = $('#asset_list_filter_category_id').val();
                    d.purchase_type = $('#asset_list_filter_purchase_type').val();
                    if ($('#asset_list_filter_allocatable').is(':checked')) {
                        d.is_allocatable = 1;
                    }
                }
            },
            aaSorting:[[1, 'desc']],
            columns:[
                { data: 'action', name: 'action', searchable: false,  orderable: false},
                { data: 'asset_code', name: 'asset_code'},
                { data: 'asset', name: 'assets.name' },
                { data: 'quantity', name: 'assets.quantity' },
                { data: 'warranty', name: 'warranty', searchable: false,  orderable: false},
                { data: 'is_allocatable', name: 'is_allocatable' },
                { data: 'purchase_date', name : 'purchase_date' },
                { data: 'allocated_qty', name: 'allocated_qty', searchable: false},
                { data: 'unit_price', name: 'unit_price' },
                { data:'model', name: 'model' },
                { data: 'image', name: 'image', searchable: false,  orderable: false },
                { data: 'location', name: 'BL.name' },
                { data: 'category', name: 'CAT.name' },
                { data: 'description', name: 'assets.description' },
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#assest_table'));
            }
		});

		$(document).on('change', '#asset_list_filter_location_id, #asset_list_filter_category_id, #asset_list_filter_purchase_type', function(){
			assest_datatable.ajax.reload();
		});

		$(document).on('ifChanged', '#asset_list_filter_allocatable', function(){
			assest_datatable.ajax.reload();
		});

		$(document).on('click', '#delete_asset', function () {
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
		                        assest_datatable.ajax.reload();
		                    } else {
		                        toastr.error(result.msg);
		                    }
		                }
		            });
		        }
		    });
		});

		//add asset modal open
		$(document).on('click', '#add_asset', function () {
			var url = $(this).data('href');
			$.ajax({
				method: 'GET',
				dataType: 'html',
				url: url,
				success: function (response) {
					$("#asset_modal").html(response).modal('show');
				}
			});
		});

		//edit asset model open
		$(document).on('click', '.edit_asset', function () {
			var url = $(this).data('href');
			$.ajax({
				method: 'GET',
				dataType: 'html',
				url: url,
				success: function (response) {
					$("#asset_modal").html(response).modal('show');
				}
			});
		});

		$('#asset_modal').on('shown.bs.modal', function () {

			$('form#asset_form .datepicker').datepicker({
		        autoclose: true,
		        format:datepicker_date_format
		    });

		  	$("form#asset_form").validate({
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
	});
	
	$(document).on('click', '#add_more_warranty', function() {
		var html = '<tr>\
			    <td>\
			        <input type="text" name="start_dates[]" class="form-control datepicker" readonly placeholder="{{__('business.start_date')}}" required>\
			    </td>\
			    <td>\
			        <input type="text" name="months[]" class="form-control input_number" placeholder="{{__('assetmanagement::lang.warranty_months')}}" required>\
			    </td>\
			    <td>\
			        <input type="text" name="additional_cost[]" class="form-control input_number" placeholder="{{__('assetmanagement::lang.additional_cost')}}" value="0">\
			    </td>\
			    <td>\
			    	<textarea name="additional_note[]" class="form-control" rows="3" placeholder="{{__('purchase.additional_notes')}}"></textarea>\
			    </td>\
			    <td><button type="button" class="btn btn-danger btn-sm remove-warranty"><i class="fas fa-times"></i></button></td>\
			</tr>';

			$('#asset_warranty_table tbody').append(html);
			var tr = $('#asset_warranty_table tbody').find('tr:last');

			tr.find('.datepicker').datepicker({
		        autoclose: true,
		        format:datepicker_date_format
		    });
	});

	$(document).on('click', '.remove-warranty', function(){
		$(this).closest('tr').remove();
	})

	$(document).on('click', '.send_to_maintenance', function () {
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
	});
</script>
@endsection