@component('components.widget', ['title' => __('essentials::lang.hrm_details')])
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
              {!! Form::label('essentials_department_id', __('essentials::lang.department') . ':') !!}
              <div class="form-group">
                  {!! Form::select('essentials_department_id', $departments, !empty($user->essentials_department_id) ? $user->essentials_department_id : null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select') ]); !!}
              </div>
          </div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
            {!! Form::label('essentials_designation_id', __('essentials::lang.designation') . ':') !!}
            <div class="form-group">
                {!! Form::select('essentials_designation_id', $designations, !empty($user->essentials_designation_id) ? $user->essentials_designation_id : null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('messages.please_select') ]); !!}
            </div>
        </div>
	</div>
</div>
@endcomponent
@component('components.widget', ['title' => __('essentials::lang.payroll')])
<div class="row">
    <div class="col-md-4">
        {!! Form::label('location_id', __('lang_v1.primary_work_location') . ':') !!}
        {!! Form::select('location_id', $locations, !empty($user->location_id) ? $user->location_id : null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <div class="multi-input">
                {!! Form::label('essentials_salary', __('essentials::lang.salary') . ':') !!}
                <br/>
                {!! Form::number('essentials_salary', !empty($user->essentials_salary) ? $user->essentials_salary : null, ['class' => 'form-control width-40 pull-left', 'placeholder' => __('essentials::lang.salary')]); !!}

                {!! Form::select('essentials_pay_period', ['month' => __('essentials::lang.per'). ' '.__('lang_v1.month'), 'week' => __('essentials::lang.per'). ' '.__('essentials::lang.week'), 'day' => __('essentials::lang.per'). ' '.__('lang_v1.day')], !empty($user->essentials_pay_period) ? $user->essentials_pay_period : null, ['class' => 'form-control width-60 pull-left']); !!}
            </div>
        </div>
    </div>
    <div class="form-group col-md-4">
        {!! Form::label('pay_components', __('essentials::lang.pay_components') . ':') !!}
        {!! Form::select('pay_components[]', $pay_comoponenets, !empty($allowance_deduction_ids) ? $allowance_deduction_ids : [], ['class' => 'form-control select2', 'multiple' ]); !!}
    </div>
</div>
@endcomponent