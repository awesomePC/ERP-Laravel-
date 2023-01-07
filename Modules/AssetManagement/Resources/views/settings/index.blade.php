@extends('layouts.app')
@section('title', __('role.settings'))
@section('content')
	@includeIf('assetmanagement::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	    <h1>
	    	@lang('assetmanagement::lang.asset_settings')
	    </h1>
	</section>
	<!-- Main content -->
	<section class="content no-print">
		{!! Form::open(['action' => '\Modules\AssetManagement\Http\Controllers\AssetSettingsController@store', 'id' => 'asset_settings_form', 'method' => 'post']) !!}
			<div class="row">
		        <div class="col-xs-12">
		           <!--  <pos-tab-container> -->
		            <div class="col-xs-12 pos-tab-container">
		                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
		                    <div class="list-group">
		                        <a href="#" class="list-group-item text-center active">@lang('lang_v1.prefixes')</a>
		                        <a href="#" class="list-group-item text-center">@lang('lang_v1.notifications')</a>
		                    </div>
		                </div>
		                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
		                    @include('assetmanagement::settings.prefix_settings')
		                    @include('assetmanagement::settings.notification_settings')
		                </div>
		            </div>
		        </div>
		    </div>
		    <div class="row">
		    	<div class="col-md-12 text-center">
		    		<button type="submit" class="btn btn-danger btn-lg">
                    	@lang('messages.update')
                	</button>
		    	</div>
		    </div>
		{!! Form::close() !!}
	</section>
@stop
@section('javascript')
<script type="text/javascript">
	$(document).ready(function () {
		$('textarea.ckeditor').each( function(){
	        var editor_id = $(this).attr('id');
	        tinymce.init({
	            selector: 'textarea#'+editor_id,
	        });
	    });
	});

	$(document).on('ifChecked', '#enable_asset_send_for_maintenance_email', function(){
        $('#asset_send_for_maintenance_email_div').removeClass('hide');
    });
    $(document).on('ifUnchecked', '#enable_asset_send_for_maintenance_email', function(){
        $('#asset_send_for_maintenance_email_div').addClass('hide');
    });

    $(document).on('ifChecked', '#enable_asset_assigned_for_maintenance_email', function(){
        $('#asset_assigned_for_maintenance_email_div').removeClass('hide');
    });
    $(document).on('ifUnchecked', '#enable_asset_assigned_for_maintenance_email', function(){
        $('#asset_assigned_for_maintenance_email_div').addClass('hide');
    });
</script>
@endsection