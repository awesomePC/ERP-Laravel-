<a class="btn btn-sm btn-primary pull-right contact-login-add" data-href="{{action('\Modules\Crm\Http\Controllers\ContactLoginController@create')}}" >
	<i class="fa fa-plus"></i>
	@lang( 'messages.add' )
</a>
<br><br>
<div class="table-responsive">
	<table class="table table-bordered table-striped" id="contact_login_table" style="width: 100%;">
		<thead>
			<tr>
				<th>@lang('messages.action')</th>
				<th>@lang('business.username')</th>
                <th>@lang('user.name')</th>
                <th>@lang( 'business.email' )</th>
                <th>@lang( 'lang_v1.department' )</th>
                <th>@lang( 'lang_v1.designation' )</th>
			</tr>
		</thead>
	</table>
</div>
<!-- modal -->
<div class="modal fade contact_login_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>