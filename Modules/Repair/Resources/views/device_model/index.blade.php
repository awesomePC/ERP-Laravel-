 <a class="btn btn-sm btn-primary pull-right" data-href="{{action('\Modules\Repair\Http\Controllers\DeviceModelController@create')}}" id="add_device_model">
	<i class="fa fa-plus"></i>
	@lang('messages.add')
</a>
<br><br>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="model_table" style="width: 100%">
        <thead>
            <tr>
                <th>@lang('messages.action')</th>
                <th>@lang('repair::lang.model_name')</th>
                <th>@lang('repair::lang.repair_checklist')</th>
                <th>@lang('repair::lang.device')</th>
                <th>@lang('product.brand')</th>
            </tr>
        </thead>
    </table>
</div>
<div class="modal fade" id="device_model_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>