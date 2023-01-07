@extends('layouts.app')
@section('title', __('essentials::lang.sales_target'))

@section('content')
@include('essentials::layouts.nav_hrm')
<section class="content-header">
    <h1>@lang('essentials::lang.sales_target')
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="sales_target_table">
                        <thead>
                            <tr>
                                <th>@lang( 'report.user' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
<div class="modal fade" id="set_sales_target_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            sales_target_table = $('#sales_target_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{action('\Modules\Essentials\Http\Controllers\SalesTargetController@index')}}"
                },
                columns: [
                    { data: 'full_name', name: 'full_name' },
                    { data: 'action', name: 'action' },
                ],
            });

            $(document).on('submit', 'form#add_holiday_form', function(e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();

                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        if (result.success == true) {
                            $('div#add_holiday_modal').modal('hide');
                            toastr.success(result.msg);
                            holidays_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });

        $(document).on('click', '#add_target', function(e) {
            $('#target_table tbody').append($('#sales_target_row_hidden tbody').html());
        });
        $(document).on('click', '.remove_target', function(e) {
            $(this).closest('tr').remove();
        });
    </script>
@endsection
