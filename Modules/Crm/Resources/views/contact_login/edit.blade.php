<div class="modal-dialog" role="document">
	{!! Form::open(['url' => action('\Modules\Crm\Http\Controllers\ContactLoginController@update', ['id' => $user->id]), 'method' => 'put', 'id' => 'contact_login_edit' ]) !!}
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<h4 class="modal-title" id="myModalLabel">
				@lang('crm::lang.edit_login')
			</h4>
		</div>
		<div class="modal-body">
			<input type="hidden" name="user_id" id="user_id" value="{{$user->id}}">
			<div class="row">
				<div class="col-md-2">
			        <div class="form-group">
			         	{!! Form::label('surname', __( 'business.prefix' ) . ':') !!}
			         	{!! Form::text('surname', $user->surname, ['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
			        </div>
			    </div>
			    <div class="col-md-5">
			        <div class="form-group">
						{!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
						{!! Form::text('first_name', $user->first_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
			        </div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						{!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
						{!! Form::text('last_name', $user->last_name, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
					</div>
				</div>
		      	<div class="clearfix"></div>
		      	@if(!empty($contacts))
		      		<div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('crm_contact_id', __('contact.contact') .':*') !!}
                            {!! Form::select('crm_contact_id', $contacts, $user->crm_contact_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                @else
				<input type="hidden" name="crm_contact_id" value="{{$user->crm_contact_id}}">
		      	@endif
		      	<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('email', __( 'business.email' ) . ':*') !!}
						{!! Form::text('email', $user->email, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.email' ) ]); !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
					    {!! Form::label("contact_number", __( 'lang_v1.mobile_number' ) . ':') !!}
					    {!! Form::text('contact_number', $user->contact_number, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.mobile_number')]); !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
				    	{!! Form::label('alt_number', __( 'business.alternate_number' ) . ':') !!}
				    	{!! Form::text('alt_number', !empty($user->alt_number) ? $user->alt_number : null, ['class' => 'form-control', 'placeholder' => __( 'business.alternate_number') ]); !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
				    	{!! Form::label('family_number', __( 'lang_v1.family_contact_number' ) . ':') !!}
				    	{!! Form::text('family_number', !empty($user->family_number) ? $user->family_number : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.family_contact_number') ]); !!}
				    </div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
					    {!! Form::label("crm_department", __( 'lang_v1.department' ) . ':') !!}
					    {!! Form::text("crm_department", !empty($user->crm_department) ? $user->crm_department : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.department'), 'id'=>"crm_department" ]); !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
					    {!! Form::label("crm_designation", __( 'lang_v1.designation' ) . ':') !!}
					    {!! Form::text("crm_designation", !empty($user->crm_designation) ? $user->crm_designation : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.designation'), 'id'=>"crm_designation" ]); !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('password', __( 'business.password' ) . ':') !!}
						{!! Form::password('password', ['class' => 'form-control', 'placeholder' => __( 'business.password' ) ]); !!}
						<p class="help-block">@lang('user.leave_password_blank')</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('confirm_password', __( 'business.confirm_password' ) . ':') !!}
						{!! Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => __( 'business.confirm_password' ) ]); !!}
					</div>
				</div>
		      	<div class="clearfix"></div>
				<div class="col-md-4">
					@php
						$status = $user->status;
						$is_active = !empty($status) && $status == 'active' ? true : false;
					@endphp
					<div class="form-group">
						<label>
							{!! Form::checkbox('is_active', 'active', $is_active, ['class' => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
						</label>
						@show_tooltip(__('lang_v1.tooltip_enable_user_active'))
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">
				@lang( 'messages.close' )
			</button>
			<button type="submit" class="btn btn-primary">
				@lang( 'messages.update' )
			</button>
		</div>
	</div>
	{!! Form::close() !!}
</div>