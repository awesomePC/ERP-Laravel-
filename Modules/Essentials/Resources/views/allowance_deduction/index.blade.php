@extends('layouts.app')
@section('title', __('essentials::lang.allowance_and_deduction'))

@section('content')
@include('essentials::layouts.nav_hrm')
<section class="content-header">
    <h1>@lang('essentials::lang.allowance_and_deduction')
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid'])
                @can('essentials.add_allowance_and_deduction')
                @slot('tool')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal" data-href="{{action('\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController@create')}}" data-container="#add_allowance_deduction_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endslot
                @endcan
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="ad_table">
                        <thead>
                            <tr>
                                <th>@lang( 'lang_v1.description' )</th>
                                <th>@lang( 'lang_v1.type' )</th>
                                <th>@lang( 'sale.amount' )</th>
                                <th>@lang( 'essentials::lang.applicable_date' )</th>
                                <th>@lang( 'essentials::lang.employee' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row" id="user_leave_summary"></div>
</section>
<!-- /.content -->
<div class="modal fade" id="add_allowance_deduction_modal" tabindex="-1" role="dialog"
 aria-labelledby="gridSystemModalLabel"></div>

@endsection

@section('javascript')
    <script type="text/javascript">
        $('#add_allowance_deduction_modal').on('shown.bs.modal', function(e) {
            var $p = $(this);
            $('#add_allowance_deduction_modal .select2').select2({dropdownParent:$p});
            $('#add_allowance_deduction_modal #applicable_date').datepicker();
            
        });

        $(document).on('submit', 'form#add_allowance_form', function(e) {
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
                        $('div#add_allowance_deduction_modal').modal('hide');
                        toastr.success(result.msg);
                        ad_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });

        $(document).ready(function() {
            ad_table = $('#ad_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{action('\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController@index')}}",
                columns: [
                    { data: 'description', name: 'description' },
                    { data: 'type', name: 'type' },
                    { data: 'amount', name: 'amount' },
                    { data: 'applicable_date', name: 'applicable_date' },
                    { data: 'employees', searchable: false, orderable: false },
                    { data: 'action', name: 'action' }
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#ad_table'));
                },
            });
        });

        $(document).on('click', '.delete-allowance', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                ad_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
    </script>
@endsection
