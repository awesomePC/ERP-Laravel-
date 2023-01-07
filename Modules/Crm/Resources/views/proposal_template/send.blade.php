@extends('layouts.app')
@section('title', __('crm::lang.proposal'))
@section('content')
	@include('crm::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	   <h1>
	   		@lang('crm::lang.proposal')
	   		<small>@lang('crm::lang.send')</small>
	   </h1>
	</section>
	<!-- Main content -->
	<section class="content">
		@component('components.widget', ['class' => 'box-solid'])
			{!! Form::open(['url' => action('\Modules\Crm\Http\Controllers\ProposalController@store'), 'method' => 'post', 'id' => 'proposal_form']) !!}
				<div class="row">
					<div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('contact_id', __('crm::lang.send_to') .':*') !!}
                            {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2', 'id' => 'proposal_contact', 'style' => 'width: 100%;', 'required', 'placeholder' => __('messages.please_select')]); !!}
                        </div>
                    </div>
				</div>
				@includeIf('crm::proposal_template.partials.template_form', ['proposal_template' => $proposal_template, 'attachments' => false])
				@if($proposal_template->media->count() > 0)
					<hr>
					<div class="row">
						<div class="col-md-6">
							<h4>
								{{__('crm::lang.attachments')}}
							</h4>
							@includeIf('crm::proposal_template.partials.attachment', ['medias' => $proposal_template->media])
						</div>
					</div>
				@endif
				<button type="submit" class="btn btn-primary ladda-button pull-right m-5" data-style="expand-right">
                    <span class="ladda-label">@lang('crm::lang.send')</span>
                </button>
			{!! Form::close() !!}
    	@endcomponent
	</section>
@endsection
@section('javascript')
<script type="text/javascript">
	$(function () {
		tinymce.init({
	        selector: 'textarea#proposal_email_body',
	        height: 350,
	    });

        $('form#proposal_form').validate({
	        submitHandler: function(form) {
	            form.submit();
	            let ladda = Ladda.create(document.querySelector('.ladda-button'));
    			ladda.start();
	        }
	    });
	    
	    $(document).on('click', 'a.delete_attachment', function (e) {
            e.preventDefault();
            var url = $(this).data('href');
            var this_btn = $(this);
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((confirmed) => {
                if (confirmed) {
                    $.ajax({
                        method: 'DELETE',
                        url: url,
                        dataType: 'json',
                        success: function(result) {
                            if(result.success == true){
			                    this_btn.closest('tr').remove();
			                    toastr.success(result.msg);
			                } else {
			                    toastr.error(result.msg);
			                }
                        }
                    });
                }
            });
        });
	});
</script>
@endsection