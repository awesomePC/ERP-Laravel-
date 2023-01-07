<div class="row">
	<div class="col-md-4">
		<div class="input-group date">
    		{!! Form::text('attendance_by_shift_date_filter', @format_date('today'), ['class' => 'form-control', 'id' => 'attendance_by_shift_date_filter', 'readonly' ]); !!}
    		<span class="input-group-addon"><i class="fas fa-calendar"></i></span>
    	</div>
	</div>
	<div class="col-md-12">
		<br>
		<table class="table" id="attendance_by_shift_table">
			<thead>
				<tr>
					<th>
						@lang('essentials::lang.shift')
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