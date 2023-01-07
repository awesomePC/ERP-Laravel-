@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | ' . __('superadmin::lang.add_page'))

@section('content')


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.add_page')</h1>
</section>

<!-- Main content -->
<section class="content">

	{!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\PageController@store'), 'method' => 'post', 'id' => 'add_page_form']) !!}

	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('title', __('superadmin::lang.page_title').':') !!}
						{!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('superadmin::lang.page_title')]); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('slug', __('superadmin::lang.slug').':') !!}
						{!! Form::text('slug', null, ['class' => 'form-control', 'placeholder' => __('superadmin::lang.slug'), 'required']); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						{!! Form::label('menu_order', __('superadmin::lang.menu_order').':') !!} @show_tooltip(__('superadmin::lang.menu_order_tooltip'))
						{!! Form::number('menu_order', 0, ['class' => 'form-control', 'placeholder' => __('superadmin::lang.menu_order')]); !!}
					</div>
				</div>
				<div class="col-sm-3">
					<div class="checkbox">
					<label>
						{!! Form::checkbox('is_shown', 1, true, ['class' => 'input-icheck']); !!}
                        {{__('superadmin::lang.is_visible')}}
					</label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						{!! Form::label('content', __('superadmin::lang.page_content').':') !!}
						{!! Form::textarea('content', null, ['class' => 'form-control', 'placeholder' => __('superadmin::lang.page_content')]); !!}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<button type="submit" class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
		</div>
	</div>

	{!! Form::close() !!}
</section>

@endsection

@section('javascript')
	@include('superadmin::pages.form_script')
@endsection