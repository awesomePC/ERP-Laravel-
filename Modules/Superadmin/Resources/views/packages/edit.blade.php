@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.packages'))

@section('content')
@include('superadmin::layouts.nav')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.packages') <small>@lang('superadmin::lang.edit_package')</small></h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>


<!-- Main content -->
<section class="content">
	{!! Form::open(['route' => ['packages.update', $packages->id], 'method' => 'put', 'id' => 'edit_package_form']) !!}
	<div class="box box-solid">
		<div class="box-body">

	    	<div class="row">
				
				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('name', __('lang_v1.name').':') !!}
						{!! Form::text('name',$packages->name, ['class' => 'form-control', 'required']); !!}
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('description', __('superadmin::lang.description').':') !!}
						{!! Form::text('description', $packages->description, ['class' => 'form-control', 'required']); !!}
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('location_count', __('superadmin::lang.location_count').':') !!}
						{!! Form::number('location_count', $packages->location_count, ['class' => 'form-control', 'required', 'min' => 0]); !!}

						<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('user_count', __('superadmin::lang.user_count').':') !!}
						{!! Form::number('user_count', $packages->user_count, ['class' => 'form-control', 'required', 'min' => 0]); !!}

						<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('product_count', __('superadmin::lang.product_count').':') !!}
						{!! Form::number('product_count', $packages->product_count, ['class' => 'form-control', 'required', 'min' => 0]); !!}

						<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('invoice_count', __('superadmin::lang.invoice_count').':') !!}
						{!! Form::number('invoice_count', $packages->invoice_count, ['class' => 'form-control', 'required', 'min' => 0]); !!}

						<span class="help-block">
							@lang('superadmin::lang.infinite_help')
						</span>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('interval', __('superadmin::lang.interval').':') !!}

						{!! Form::select('interval', $intervals, $packages->interval, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('interval_count	', __('superadmin::lang.interval_count').':') !!}
						{!! Form::number('interval_count', $packages->interval_count, ['class' => 'form-control', 'required']); !!}
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('trial_days	', __('superadmin::lang.trial_days').':') !!}
						{!! Form::number('trial_days', $packages->trial_days, ['class' => 'form-control', 'required', 'min' => 0]); !!}
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('price', __('superadmin::lang.price').':') !!}
						{!! Form::text('price', $packages->price, ['class' => 'form-control input_number', 'required']); !!}
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group">
						{!! Form::label('sort_order	', __('superadmin::lang.sort_order').':') !!}
						{!! Form::number('sort_order', $packages->sort_order, ['class' => 'form-control', 'required']); !!}
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-6">
					<div class="checkbox">
					<label>
						{!! Form::checkbox('is_private', 1, $packages->is_private, ['class' => 'input-icheck']); !!}
                        {{__('superadmin::lang.private_superadmin_only')}}
					</label>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="checkbox">
					<label>
						{!! Form::checkbox('is_one_time', 1, $packages->is_one_time, ['class' => 'input-icheck']); !!}
                        {{__('superadmin::lang.one_time_only_subscription')}}
					</label>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-4">
					<div class="checkbox">
					<label>
						{!! Form::checkbox('enable_custom_link', 1, $packages->enable_custom_link, ['class' => 'input-icheck', 'id' => 'enable_custom_link']); !!}
                        {{__('superadmin::lang.enable_custom_subscription_link')}}
					</label>
					</div>
				</div>
				<div id="custom_link_div" @if(empty($packages->enable_custom_link)) class="hide" @endif>
					<div class="col-sm-4">
						<div class="form-group">
							{!! Form::label('custom_link', __('superadmin::lang.custom_link').':') !!}
							{!! Form::text('custom_link', $packages->custom_link, ['class' => 'form-control']); !!}
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							{!! Form::label('custom_link_text', __('superadmin::lang.custom_link_text').':') !!}
							{!! Form::text('custom_link_text', $packages->custom_link_text, ['class' => 'form-control']); !!}
						</div>
					</div>
				</div>
				<div class="clearfix"></div>

				@foreach($permissions as $module => $module_permissions)
					@foreach($module_permissions as $permission)
					@php
						$value = isset($packages->custom_permissions[$permission['name']]) ? $packages->custom_permissions[$permission['name']] : false;
					@endphp
					<div class="col-sm-3">
						<div class="checkbox">
						<label>
							{!! Form::checkbox("custom_permissions[$permission[name]]", 1, $value, ['class' => 'input-icheck']); !!}
	                        {{$permission['label']}}
						</label>
						</div>
					</div>
					@endforeach
				@endforeach

				<div class="col-sm-3 ">

					<div class="checkbox">
					<label>
                        {!! Form::checkbox('is_active', 1, $packages->is_active, ['class' => 'input-icheck']); !!}
                        {{__('superadmin::lang.is_active')}}
					</label>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-sm-4">
					<div class="checkbox">
					<label>
                        {!! Form::checkbox('update_subscriptions', 1, false, ['class' => 'input-icheck']); !!}
                        {{__('superadmin::lang.update_existing_subscriptions')}}
					</label>
					@show_tooltip(__('superadmin::lang.update_existing_subscriptions_tooltip'))
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
				</div>
			</div>

		</div>
	</div>

	{!! Form::close() !!}
</section>

@endsection

@section('javascript')
	<script type="text/javascript">
		$(document).ready(function(){
			$('form#edit_package_form').validate();
		});
		$('#enable_custom_link').on('ifChecked', function(event){
		   $("div#custom_link_div").removeClass('hide');
		});
		$('#enable_custom_link').on('ifUnchecked', function(event){
		   $("div#custom_link_div").addClass('hide');
		});
	</script>
@endsection