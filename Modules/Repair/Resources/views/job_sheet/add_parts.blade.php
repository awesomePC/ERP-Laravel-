@extends('layouts.app')
@section('title', __('repair::lang.add_jobsheet_parts'))

@section('content')
@include('repair::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('repair::lang.add_jobsheet_parts')</h1>
</section>

<!-- Main content -->
<section class="content">
	@component('components.widget', ['class' => 'box-solid'])
		<table class="table">
			<tr>
				<th>@lang('repair::lang.job_sheet_no'):</th>
				<td>{{$job_sheet->job_sheet_no}}</td>
				<th>@lang('receipt.date'):</th>
				<td>{{@format_datetime($job_sheet->created_at)}}</td>
			</tr>
			<tr>
				<th>
					@lang('role.customer'):
				</th>
				<td>{{$job_sheet->customer->name}}</td>
				<th>@lang('business.location'):</th>
				<td>
					{{optional($job_sheet->businessLocation)->name}}
				</td>
			</tr>
		</table>
	@endcomponent
	{!! Form::open(['url' => action('\Modules\Repair\Http\Controllers\JobSheetController@saveParts', $job_sheet->id), 'method' => 'post', 'id' => 'add_part_form' ]) !!}
	@component('components.widget', ['class' => 'box-solid', 'title' => __('repair::lang.add_parts')])
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-search"></i>
						</span>
						{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_job_sheet_parts', 'placeholder' => __('repair::lang.search_parts')]); !!}
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-10 col-sm-offset-1">
				<div class="table-responsive">
				<table class="table table-bordered table-striped table-condensed" 
				id="job_sheet_parts_table">
					<thead>
						<tr>
							<th class="col-sm-4 text-center">	
								@lang('repair::lang.part')
							</th>
							<th class="col-sm-2 text-center">
								@lang('sale.qty')
							</th>
							<th class="col-sm-2 text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
						</tr>
					</thead>
					<tbody>
						@if(!empty($parts))
							@foreach($parts as $part)
								@include('repair::job_sheet.partials.job_sheet_part_row', ['variation_name' => $part['variation_name'], 'unit' => $part['unit'], 'quantity' => $part['quantity'], 'variation_id' => $part['variation_id']])
							@endforeach
						@endif
					</tbody>
				</table>
				</div>
			</div>
		</div>
	@endcomponent
	@if(!empty($status_update_data) && $status_update_data['job_sheet_id'] == $job_sheet->id)
		@component('components.widget', ['class' => 'box-solid'])
			@include('repair::job_sheet.partials.edit_status_form', ['status_update_data' => $status_update_data])
		@endcomponent
	@endif
	<div class="row">
		<div class="col-sm-12">
			<button type="button" id="submit_add_part_form" class="btn btn-primary pull-right">@lang('messages.save')</button>
		</div>
	</div>
	{!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
	$(document).ready( function(){
		$('#search_job_sheet_parts')
            .autocomplete({
                source: function(request, response) {
                    $.getJSON(
                        '/products/list',
                        { term: request.term },
                        response
                    );
                },
                minLength: 2,
                response: function(event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        $(this)
                                .data('ui-autocomplete')
                                ._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                    } else if (ui.content.length == 0) {
                        swal(LANG.no_products_found);
                    }
                },
                select: function(event, ui) {
                   job_sheet_parts_row(ui.item.variation_id);
                },
            })
            .autocomplete('instance')._renderItem = function(ul, item) {
	            var string = '<div>' + item.name;
	                if (item.type == 'variable') {
	                    string += '-' + item.variation;
	                }
	                string += ' (' + item.sub_sku + ') </div>';
	                return $('<li>')
	                    .append(string)
	                    .appendTo(ul);
        	};

       	//initialize editor
        tinymce.init({
            selector: 'textarea#email_body',
        });

        $('#send_sms').change(function() {
            if ($(this). is(":checked")) {
                $('div.sms_body').fadeIn();
            } else {
                $('div.sms_body').fadeOut();
            }
        });

        $('#send_email').change(function() {
            if ($(this). is(":checked")) {
                $('div.email_template').fadeIn();
            } else {
                $('div.email_template').fadeOut();
            }
        });

        if ($('#status_id_modal').length) {
            ;
            $("#sms_body").val($("#status_id_modal :selected").data('sms_template'));
            $("#email_subject").val($("#status_id_modal :selected").data('email_subject'));
            tinymce.activeEditor.setContent($("#status_id_modal :selected").data('email_body'));  
        }

        $('#status_id_modal').on('change', function() {
            var sms_template = $(this).find(':selected').data('sms_template');
            var email_subject = $(this).find(':selected').data('email_subject');
            var email_body = $(this).find(':selected').data('email_body');

            $("#sms_body").val(sms_template);
            $("#email_subject").val(email_subject);
            tinymce.activeEditor.setContent(email_body);

            if ($('#status_modal .mark-as-complete-btn').length) {
                if ($(this).find(':selected').data('is_completed_status') == 1) 
                {
                    $('#status_modal').find('.mark-as-complete-btn').removeClass('hide');
                    $('#status_modal').find('.mark-as-incomplete-btn').addClass('hide');
                } else {
                    $('#status_modal').find('.mark-as-complete-btn').addClass('hide');
                    $('#status_modal').find('.mark-as-incomplete-btn').removeClass('hide');
                }
            }
        });
	});

	function job_sheet_parts_row(variation_id) {
		var row_index = parseInt($('#product_row_index').val());
	    var location_id = $('select#location_id').val();
	    $.ajax({
	        method: 'POST',
	        url: "{{action('\Modules\Repair\Http\Controllers\JobSheetController@jobsheetPartRow')}}",
	        data: { variation_id: variation_id },
	        dataType: 'html',
	        success: function(result) {
	            $('table#job_sheet_parts_table tbody').append(result);

	            $('input#search_job_sheet_parts').val('')
	            $('input#search_job_sheet_parts')
                        .focus()
                        .select();
	            
	        },
	    });
	}

	$(document).on('click', '.remove_product_row', function(){
		$(this).closest('tr').remove();
	})

	$(document).on('click', '#submit_add_part_form', function(e){
		$('form#add_part_form').submit();
	})
</script>
@endsection
