@php
	$index = isset($index) ? (int) $index : '';
@endphp
<div class="row">
	<div class="col-md-12">
		<hr>
		<button type="button" class="btn btn-primary more_btn" data-target="#add_contact_person_div_{{$index}}">@lang('crm::lang.add_contact_person', ['number' => $index + 1]) <i class="fa fa-chevron-down"></i></button>
	</div>
</div>
<br>
<div class="row @if($index !== 0)hide @endif" id="add_contact_person_div_{{$index}}">
	<div class="col-md-2">
        <div class="form-group">
         	{!! Form::label("surname$index", __( 'business.prefix' ) . ':') !!}
         	{!! Form::text($index === '' ? 'surname' : "contact_persons[$index][surname]", null, ['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ), 'id' => "surname$index" ]); !!}
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
			{!! Form::label("first_name$index", __( 'business.first_name' ) . ':*') !!}
			{!! Form::text($index === '' ? 'first_name' : "contact_persons[$index][first_name]", null, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ), 'id' => "first_name$index" ]); !!}
        </div>
	</div>
	<div class="col-md-5">
		<div class="form-group">
			{!! Form::label("last_name$index", __( 'business.last_name' ) . ':') !!}
			{!! Form::text($index === '' ? 'last_name' : "contact_persons[$index][last_name]", null, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ), 'id' => "last_name$index" ]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label("email$index", __( 'business.email' ) . ':') !!}
			{!! Form::text($index ==='' ? 'email' : "contact_persons[$index][email]", null, ['class' => 'form-control', 'placeholder' => __( 'business.email' ), 'id' => "email$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		    {!! Form::label("contact_number$index", __( 'lang_v1.mobile_number' ) . ':') !!}
		    {!! Form::text($index === '' ? 'contact_number' : "contact_persons[$index][contact_number]", !empty($user->contact_number) ? $user->contact_number : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.mobile_number'), 'id'=>"contact_number$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		    {!! Form::label("alt_number$index", __( 'business.alternate_number' ) . ':') !!}
		    {!! Form::text($index === '' ? 'alt_number' : "contact_persons[$index][alt_number]", !empty($user->alt_number) ? $user->alt_number : null, ['class' => 'form-control', 'placeholder' => __( 'business.alternate_number'), 'id'=>"alt_number$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		    {!! Form::label("family_number$index", __( 'lang_v1.family_contact_number' ) . ':') !!}
		    {!! Form::text($index === '' ? 'family_number' : "contact_persons[$index][family_number]", !empty($user->family_number) ? $user->family_number : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.family_contact_number'), 'id'=>"family_number$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		    {!! Form::label("department$index", __( 'lang_v1.department' ) . ':') !!}
		    {!! Form::text($index === '' ? 'crm_department' : "contact_persons[$index][crm_department]", !empty($user->crm_department) ? $user->crm_department : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.department'), 'id'=>"department$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
		    {!! Form::label("designation$index", __( 'lang_v1.designation' ) . ':') !!}
		    {!! Form::text($index === '' ? 'crm_designation' : "contact_persons[$index][crm_designation]", !empty($user->crm_designation) ? $user->crm_designation : null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.designation'), 'id'=>"designation$index" ]); !!}
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-md-12">
		<div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox($index === '' ? 'allow_login' : "contact_persons[$index][allow_login]", 1, false, 
                [ 'class' => 'input-icheck allow_login', "data-loginDiv" => "loginDiv$index"]); !!} {{ __( 'lang_v1.allow_login' ) }}
              </label>
            </div>
        </div>
	</div>
</div>
<div class="row hide" id="loginDiv{{$index}}">
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label("username$index", __( 'business.username' ) . ':*') !!}
			{!! Form::text($index ==='' ? 'username' : "contact_persons[$index][username]", null, ['class' => 'form-control', 'placeholder' => __( 'business.username' ), 'required', 'id'=>"username$index"]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label("password$index", __( 'business.password' ) . ':*') !!}
			{!! Form::password($index === '' ? 'password' : "contact_persons[$index][password]", ['class' => 'form-control', 'required', 'placeholder' => __( 'business.password' ), 'id'=>"password$index" ]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label("confirm_password$index", __( 'business.confirm_password' ) . ':*') !!}
			{!! Form::password($index === '' ? 'confirm_password' : "contact_persons[$index][confirm_password]", ['class' => 'form-control', 'required', 'placeholder' => __( 'business.confirm_password' ), 'id' => "confirm_password$index", 'data-rule-equalTo' => "#password$index" ]); !!}
		</div>
	</div>
  	<div class="clearfix"></div>
	<div class="col-md-4">
		<div class="form-group">
			<label>
				{!! Form::checkbox($index === '' ? 'is_active' : "contact_persons[$index][is_active]", 'active', true, ['class' => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
			</label>
			@show_tooltip(__('lang_v1.tooltip_enable_user_active'))
		</div>
	</div>
</div>