<div class="row">
	<div class="col-md-4">
		<div class="input-group date">
			{!! Form::text('attendance_by_date_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'id' => 'attendance_by_date_filter', 'class' => 'form-control', 'readonly']); !!}
    		<span class="input-group-addon"><i class="fas fa-calendar"></i></span>
    	</div>
	</div>
	<div class="col-md-12">
		<br>
		<table class="table" id="attendance_by_date_table">
			<thead>
				<tr>
					<th>
						@lang('lang_v1.date')
					</th>
					<th>
						@lang('essentials::lang.present')
					</th>
					<th>
						@lang('essentials::lang.absent')
					</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>