@extends('layouts.app')
@section('title', __('crm::lang.proposal_template'))
@section('content')
	@include('crm::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	   <h1>@lang('crm::lang.proposal_template')</h1>
	</section>
	<!-- Main content -->
	<section class="content">
		@component('components.widget', ['class' => 'box-solid'])
			@if(empty($proposal_template) && auth()->user()->can('superadmin'))
		        @slot('tool')
		            <div class="box-tools">
		                <a class="btn btn-primary pull-right m-5" href="{{action('\Modules\Crm\Http\Controllers\ProposalTemplateController@create')}}">
		                	<i class="fa fa-plus"></i> @lang('messages.add')
		                </a>
		            </div>
		        @endslot
	        @endif
	        @if(!empty($proposal_template))
		        <div class="row">
		        	<div class="col-md-4 col-md-offset-4">
		        		<div class="box box-info box-solid">
		        			<div class="box-body">
		        				<strong>
		        					{{$proposal_template->subject}}
		        				</strong>
		        			</div>
		        			<div class="box-footer clearfix">
		        				<div class="row">
		        					@if(auth()->user()->can('superadmin'))
			        					<div class="col-md-4">
			        						<a href="{{action('\Modules\Crm\Http\Controllers\ProposalTemplateController@getEdit')}}" class="btn btn-primary pull-left">
			        							@lang('messages.edit')
			        						</a>
			        					</div>
			        				@endif
			        				@can('crm.access_proposal')
			        					<div class="col-md-4">
			        						<a href="{{action('\Modules\Crm\Http\Controllers\ProposalTemplateController@getView')}}" class="btn btn-info">
			        							@lang('messages.view')
			        						</a>
			        					</div>
			        					<div class="col-md-4">
			        						<a href="{{action('\Modules\Crm\Http\Controllers\ProposalTemplateController@send')}}" class="btn btn-success pull-right">
			        							@lang('crm::lang.send')
			        						</a>
			        					</div>
			        				@endcan
		        				</div>
		        			</div>
		        		</div>
		        	</div>
		        </div>
		    @else
		    	<div class="callout callout-info">
		            <h4>
		            	{{__('crm::lang.no_template_found')}}
		            </h4>
		        </div>
		    @endif
    	@endcomponent
	</section>
@endsection