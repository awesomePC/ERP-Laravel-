@extends('layouts.app')

@section('title', __('essentials::lang.memos'))

@section('content')
@include('essentials::layouts.nav_essentials')
<section class="content">
	<h4>
		@lang('essentials::lang.all_memos')
		<small> @lang('essentials::lang.manage_memos')</small>
	</h4>
		<div class="box box-solid">
			<div class="box-header">
				<h4 class="box-title">@lang('essentials::lang.all_memos')</h4>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-sm btn-primary add_memo">
					<i class="fa fa-plus"></i> 
					@lang( 'messages.add' )
				</button>
			</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						{!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\DocumentController@store'), 'id' => 'upload_document_form','files' => true, 'style' => 'display:none']) !!}
						<div class="row">
                            <div class="col-sm-12">
	                            <div class="col-sm-6">
	                                <div class="form-group">
                                   		{!! Form::label('name', __('essentials::lang.heading') . ":*") !!}

                                   		{!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
	                                 </div>
	                            </div>
	                            <div class="clearfix"></div>
	                            <div class="col-sm-6">
	                                <div class="form-group">
	                                    {!! Form::label('description', __('essentials::lang.description') . ":")!!}
	                                    {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '4', 'cols' => '50']) !!}
	                                 </div>
	                            </div>
	                            <div class="clearfix"></div>
                        		<div class="col-sm-4">
                                	<button type="submit" class="btn btn-primary btn-sm">
                                		@lang('essentials::lang.submit')
                                	</button>
                                	&nbsp;
									<button type="button" class="btn btn-danger btn-sm cancel_btn">
										@lang('essentials::lang.cancel')
									</button>
                        		</div>
                            </div>
                        </div>
                        <br><hr>
					{!! Form::close() !!}
					</div>

				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
					<table class="table table-bordered table-striped documents">
						<thead>
							<tr>
								<th> @lang('essentials::lang.heading')</th>
								<th> @lang('essentials::lang.description')</th>
								<th> @lang('essentials::lang.created_at')</th>
								<th> @lang('essentials::lang.action')</th>
							</tr>
						</thead>
					</table>
				</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- document share model -->
	<div class="modal fade document" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
	<!-- memos view model -->
	<div class="modal fade memos" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
@endsection

@section('javascript')
<script type="text/javascript">
	$(document).ready(function(){
		//dataTable(memos)
		var documents = $(".documents").DataTable({
			processing: true,
			ajax: "/essentials/document"+'?type=memos',
			columns: [
						{data: "name", name:"documents.name"},
						{data: "description", name:"documents.description"},
						{data: "created_at", name:"documents.created_at"},
						{data: "action", name:"action", "orderable": false},
					]
		});

		//destroy a document
		$(document).on('click', '.delete_doc', function(){
			url = $(this).data("href");
			swal({
		      title: LANG.sure,
		      icon: "warning",
		      buttons: true,
		      dangerMode: true,
		    }).then((confirmed) => {
		        if (confirmed) {
		        $.ajax({
					method: "DELETE",
					url: url,
					dataType: "json",
					success: function(result)
					{
						if(result.success == true)
						{
							toastr.success(result.msg);
	                        documents.ajax.reload();
						} else {
							toastr.error(result.msg);
						}
					}
				});
			    }
		    });
		});

		//opening share_doc model
		$(document).on('click', '.share_doc', function(){
			var url = $(this).data('href')+'?type=memos';
			$.ajax({
				method: "GET",
				dataType: "html",
				url: url,
				success: function(result){
					$(".document").html(result).modal("show");
				}
			});
		});

		//sharing document(save in DB)
		$(document).on('submit', 'form#share_document_form', function(e){
			e.preventDefault();
			var url = $(this).attr("action");
			var data = $("form#share_document_form").serialize();
			$.ajax({
				method: "PUT",
				url: url,
				dataType: "json",
				data: data,
				success:function(result){
					if(result.success == true){
						$(".document").html(result).modal("hide");
						toastr.success(result.msg);
					} else {
						toastr.error(result.msg);
					}
				}
			});
		});

		//opening memos view model
		$(document).on('click', '.view_memos', function(){
			var url = $(this).data('href')+'?type=memos';
			$.ajax({
				method: "GET",
				dataType: "html",
				url: url,
				success: function(result){
					$(".memos").html(result).modal("show");
				}
			});
		});

		//show a hidden form on add_memo click
		$(document).on('click', '.add_memo', function(){
			$("form#upload_document_form").fadeIn();
		});

		//form cancel_btn
	    $(document).on('click', '.cancel_btn', function(){
	    	$("form#upload_document_form")[0].reset();
			$("form#upload_document_form").fadeOut();
	    });

	});
</script>
@endsection