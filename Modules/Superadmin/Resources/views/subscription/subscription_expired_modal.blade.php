<div class="modal-dialog modal-danger" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> @lang('superadmin::lang.subscription_expired')</h4>
    </div>

    <div class="modal-body">
      @lang('superadmin::lang.subscription_expired_modal_content', 
      ['app_name' => env('APP_NAME')])
    </div>

    <div class="modal-footer">
      <a href="{{action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index')}}" class="btn btn-outline btn-default"><i class="fa fa-refresh"></i> @lang( 'superadmin::lang.subscribe')</a>
      <button type="button" class="btn btn-outline" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->