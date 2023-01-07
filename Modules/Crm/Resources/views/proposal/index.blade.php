@extends('layouts.app')
@section('title', __('crm::lang.proposals'))
@section('content')
	@include('crm::layouts.nav')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	   <h1>@lang('crm::lang.proposals')</h1>
	</section>
	<!-- Main content -->
	<section class="content">
		@component('components.widget', ['class' => 'box-solid'])
			@if(!empty($proposal_template) && auth()->user()->can('crm.access_proposal'))
		        @slot('tool')
		            <div class="box-tools">
		                <a class="btn btn-primary pull-right m-5" href="{{action('\Modules\Crm\Http\Controllers\ProposalTemplateController@send')}}">
		                	<i class="fas fa-paper-plane"></i> @lang('crm::lang.send')
		                </a>
		            </div>
		        @endslot
	        @endif
	        <div class="table-responsive">
                <table class="table table-bordered table-striped" id="proposals" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang( 'contact.contact' )</th>
                            <th>@lang( 'crm::lang.subject' )</th>
                            <th>@lang( 'crm::lang.sent_by' )</th>
                            <th>@lang( 'receipt.date' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
    	@endcomponent
	</section>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready( function(){
            proposals_table = $('#proposals').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{action('\Modules\Crm\Http\Controllers\ProposalController@index')}}"
                    },
                    columnDefs: [
                        {
                            targets: 4,
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    aaSorting: [[3, 'desc']],
                    columns: [
                        { data: 'name', name: 'contacts.name'},
                        { data: 'subject', name: 'crm_proposals.subject'},
                        { data: 'sent_by_full_name', name: 'sent_by_full_name'},
                        { data: 'created_at', name: 'crm_proposals.created_at'},
                        { data: 'action', name: 'action' },
                    ]
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