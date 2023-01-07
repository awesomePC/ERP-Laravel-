<div class="row">
    <div class="col-sm-12">
        {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\AttendanceController@importAttendance'), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
            <div class="row">
                <div class="col-sm-6">
                <div class="col-sm-8">
                    <div class="form-group">
                        {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                        {!! Form::file('attendance', ['accept'=> '.xls', 'required' => 'required']); !!}
                      </div>
                </div>
                <div class="col-sm-4">
                <br>
                    <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
                </div>
                </div>
            </div>

        {!! Form::close() !!}
        <br><br>
        <div class="row">
            <div class="col-sm-4">
                <a href="{{ asset('modules/essentials/files/import_attendance_template.xls') }}" class="btn btn-success" download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table" width="100%">
                    <tr>
                        <th>@lang('lang_v1.col_no')</th>
                        <th>@lang('lang_v1.col_name')</th>
                        <th>@lang('lang_v1.instruction')</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>@lang('business.email') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>{!! __('essentials::lang.email_ins') !!}</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>@lang('essentials::lang.clock_in_time') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>{!! __('essentials::lang.clock_in_time_ins') !!} ({{\Carbon::now()->toDateTimeString()}})</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('essentials::lang.clock_out_time') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>{!! __('essentials::lang.clock_out_time_ins') !!} ({{\Carbon::now()->toDateTimeString()}})</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>@lang('essentials::lang.clock_in_note') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>@lang('essentials::lang.clock_out_note') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>@lang('essentials::lang.ip_address') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>