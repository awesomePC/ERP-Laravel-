<div class="pull-right">
    <button type="button" class="btn btn-sm btn-primary btn-add-schedule pull-right">
        @lang('messages.add')&nbsp;
        <i class="fa fa-plus"></i>
    </button>
    <input type="hidden" name="schedule_create_url" id="schedule_create_url" value="{{action('\Modules\Crm\Http\Controllers\ScheduleController@create')}}?schedule_for=lead&contact_id={{$contact->id}}">
    <input type="hidden" name="lead_id" value="{{$contact->id}}" id="lead_id">
    <input type="hidden" name="view_type" value="lead_info" id="view_type">
</div> <br><br>
<div class="table-responsive">
	<table class="table table-bordered table-striped" id="lead_schedule_table" style="width: 100%">
        <thead>
            <tr>
                <th> @lang('messages.action')</th>
                <th>@lang('crm::lang.title')</th>
                <th>@lang('sale.status')</th>
                <th>@lang('crm::lang.schedule_type')</th>
                <th>@lang('crm::lang.start_datetime')</th>
                <th>@lang('crm::lang.end_datetime')</th>
                <th>@lang('lang_v1.assigned_to')</th>
            </tr>
        </thead>
    </table>
</div>