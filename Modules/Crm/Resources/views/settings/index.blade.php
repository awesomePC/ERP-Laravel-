@extends('layouts.app')
@section('title', __('messages.settings'))
@section('content')
@include('crm::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        @lang('messages.settings')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' => action('\Modules\Crm\Http\Controllers\CrmSettingsController@updateSettings'), 'method' => 'post']) !!}
            @component('components.widget', ['class' => 'box-solid'])
                <div class="col-md-4">
                    <div class="checkbox">
                        <label>
                        {!! Form::checkbox('enable_order_request', 1, !empty($crm_settings['enable_order_request']), ['class' => 'input-icheck']); !!} @lang('crm::lang.enable_order_request')
                        </label> @show_tooltip(__('crm::lang.enable_order_request_help'))
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('order_request_prefix', __('crm::lang.order_request_prefix') . ':') !!}
                        {!! Form::text('order_request_prefix', $crm_settings['order_request_prefix'] ?? null, ['class' => 'form-control','placeholder' => __( 'crm::lang.order_request_prefix' )]); !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary pull-right">@lang( 'messages.update' )</button>
                </div>
            @endcomponent
            {!! Form::close() !!}
        </div>
    </div>
</section>
@stop
@section('javascript')
@endsection