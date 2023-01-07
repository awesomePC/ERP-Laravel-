@extends('layouts.app')
@section('title', __('assetmanagement::lang.assets'))
@section('content')
	@includeIf('assetmanagement::layouts.nav')
	<!-- Main content -->
	<section class="content no-print">
		<div class="row">
			<div class="col-md-4">
				<div class="info-box info-box-new-style">
	            	<span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>

	            	<div class="info-box-content">
	              		<span class="info-box-text">@lang('assetmanagement::lang.total_assets_allocated_to_you')</span>
	              		<span class="info-box-number">{{@num_format($total_assets_allocated)}}</span>
	            	</div>
	            	<!-- /.info-box-content -->
	          	</div>
			</div>

			<div class="col-md-4">
				<div class="box box-solid">
					<div class="box-body">
						<table class="table">
							<thead>
								<tr>
									<th>@lang('product.category')</th>
									<th>@lang('assetmanagement::lang.total_assets_allocated_to_you')</th>
								</tr>
							</thead>
							<tbody>
								@foreach($asset_allocation_by_category as $asset)
									<tr>
										<td>{{$asset->category}}</td>
										<td>{{@num_format($asset->total_quantity_allocated)}}</td>
									</tr>
								@endforeach

								@if(count($asset_allocation_by_category) == 0)
									<tr>
										<td colspan="2" class="text-center">@lang('lang_v1.no_data')</td>
									</tr>
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		@if($is_admin)
			<hr>
			<div class="row">
				<div class="col-md-4">
					<div class="info-box info-box-new-style">
		            	<span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>

		            	<div class="info-box-content">
		              		<span class="info-box-text">@lang('assetmanagement::lang.total_assets')</span>
		              		<span class="info-box-number">{{@num_format($total_assets)}}</span>
		            	</div>
		          	</div>

		          	<div class="info-box info-box-new-style">
		            	<span class="info-box-icon bg-aqua"><i class="fas fa-boxes"></i></span>

		            	<div class="info-box-content">
		              		<span class="info-box-text">@lang('assetmanagement::lang.total_assets_allocated')</span>
		              		<span class="info-box-number">{{@num_format($total_assets_allocated_for_all_users)}}</span>
		            	</div>
		            	<!-- /.info-box-content -->
		          	</div>
				</div>

				<div class="col-md-4">
					<div class="box box-solid">
						<div class="box-header">
							<h3 class="box-title">@lang('assetmanagement::lang.assets_by_category')</h3>
						</div>
						<div class="box-body">
							<table class="table">
								<thead>
									<tr>
										<th>@lang('product.category')</th>
										<th>@lang('assetmanagement::lang.total_assets')</th>
									</tr>
								</thead>
								<tbody>
									@foreach($assets_by_category as $asset)
										<tr>
											<td>{{$asset->category}}</td>
											<td>{{@num_format($asset->total_quantity)}}</td>
										</tr>
									@endforeach

									@if(count($assets_by_category) == 0)
										<tr>
											<td colspan="2" class="text-center">@lang('lang_v1.no_data')</td>
										</tr>
									@endif
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="box box-solid">
						<div class="box-header">
							<h3 class="box-title">@lang('assetmanagement::lang.expired_or_expiring_in_one_month')</h3>
						</div>
						<div class="box-body">
							<table class="table">
								<thead>
									<tr>
										<th>@lang('assetmanagement::lang.assets')</th>
										<th>@lang('assetmanagement::lang.warranty_status')</th>
									</tr>
								</thead>
								<tbody>
									@foreach($expiring_assets as $asset)
										<tr>
											<td>{{$asset->name}} - {{$asset->asset_code}}</td>
											<td>@if(empty($asset->end_date)) <span class="label bg-red">@lang('report.expired')</span> @else  <span class="label bg-yellow">@lang('assetmanagement::lang.expiring_on'): {{@format_date($asset->end_date)}}</span> @endif</td>
										</tr>
									@endforeach

									@if(count($expiring_assets) == 0)
										<tr>
											<td colspan="2" class="text-center">@lang('lang_v1.no_data')</td>
										</tr>
									@endif

								</tbody>
							</table>
						</div>
					</div>
				</div>


			</div>
		@endif
	</section>
@endsection