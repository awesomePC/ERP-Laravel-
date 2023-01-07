<div class="modal fade" id="update_password_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
	  	<div class="modal-content">
	  	{!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\BusinessController@updatePassword'), 'method' => 'post', 'id' => 'password_update_form' ]) !!}
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      <h4 class="modal-title"><span id="user_name"></span> - @lang( 'superadmin::lang.update_password' )</h4>
	    </div>

	    <div class="modal-body">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
					    {!! Form::label('password', __( 'business.password' ) . ':') !!}
					    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => __( 'business.password' ), 'required' ]); !!}
					    {!! Form::hidden('user_id', null, ['id' => 'user_id' ]); !!}
					</div>
				</div>
				<div class="col-md-6">
				    <div class="form-group">
					    {!! Form::label('confirm_password', __( 'business.confirm_password' ) . ':') !!}
					    {!! Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => __( 'business.confirm_password' ), 'required', 'data-rule-equalTo' => '#password' ]); !!}
					      
					</div>
				</div>
			</div>
	    </div>

	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
	      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	    {!! Form::close() !!}
	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->	
</div>