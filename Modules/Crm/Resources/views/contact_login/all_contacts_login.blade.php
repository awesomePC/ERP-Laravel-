@extends('layouts.app')

@section('title', __('crm::lang.contacts_login'))

@section('content')
@include('crm::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
   <h1>@lang('crm::lang.contacts_login')</h1>
</section>
<section class="content no-print">
	<input type="hidden" id="login_view_type" value="all_contacts_login">
	@component('components.filters', ['title' => __('report.filters')])
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('contact_id', __('contact.contact') . ':') !!}
                    {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2', 'id' => 'contact_id', 'placeholder' => __('messages.all')]); !!}
                </div>    
            </div>
        </div>
    @endcomponent
	@component('components.widget', ['class' => 'box-primary', 'title' => __('crm::lang.all_contacts_login')])
		@slot('tool')
			<div class="box-tools">
				<a class="btn btn-sm btn-primary pull-right contact-login-add" data-href="{{action('\Modules\Crm\Http\Controllers\ContactLoginController@create')}}" >
					<i class="fa fa-plus"></i>
					@lang( 'messages.add' )
				</a>
			</div>
		@endslot
		<div class="table-responsive">
			<table class="table table-bordered table-striped" id="all_contact_login_table" style="width: 100%;">
				<thead>
					<tr>
						<th>@lang('messages.action')</th>
						<th>@lang('contact.contact')</th>
						<th>@lang('business.username')</th>
		                <th>@lang('user.name')</th>
		                <th>@lang( 'business.email' )</th>
		                <th>@lang( 'lang_v1.department' )</th>
                		<th>@lang( 'lang_v1.designation' )</th>
					</tr>
				</thead>
			</table>
		</div>
	@endcomponent
	<div class="modal fade contact_login_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
</section>
@endsection
@section('javascript')
	<script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
@endsection