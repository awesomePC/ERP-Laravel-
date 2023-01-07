<div class="row">
	<div class="col-md-12">
		<a type="button" class="btn btn-sm btn-primary task_btn pull-right" href="{{action('\Modules\Project\Http\Controllers\InvoiceController@create', ['project_id' => $project->id])}}">
		    @lang('messages.add')&nbsp;
		    <i class="fa fa-plus"></i>
		</a>
	</div> <br><br>
</div>
<div class="table-responsive">
	<table class="table table-bordered table-striped" id="project_invoice_table">
		<thead>
			<tr>
				<th>@lang('messages.action')</th>
				<th>@lang('sale.invoice_no')</th>
				<th>@lang('receipt.date')</th>
				<th>@lang('role.customer')</th>
				<th>@lang('project::lang.title')</th>
				<th>@lang('purchase.payment_status')</th>
				<th>@lang('sale.total_amount')</th>
				<th>@lang('sale.status')</th>
			</tr>
		</thead>
	</table>
</div>