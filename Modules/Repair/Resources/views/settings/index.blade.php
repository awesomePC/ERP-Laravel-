@extends('layouts.app')
@section('title', __('messages.settings'))
@section('content')
@include('repair::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fas fa-tools"></i>
        @lang('messages.settings')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        @php
            $cat_code_enabled = isset($module_category_data['enable_taxonomy_code']) && !$module_category_data['enable_taxonomy_code'] ? false : true;
        @endphp
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#repair_status_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fa fas fa-check-circle"></i>
                            @lang('sale.status')
                            @show_tooltip(__('repair::lang.all_js_status_tooltip'))
                        </a>
                    </li>
                    <li>
                        <a href="#repair_device_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fas fa fa-desktop"></i>
                            @lang('repair::lang.devices')
                            @show_tooltip(__('repair::lang.device_tooltip'))
                        </a>
                    </li>
                    <li>
                        <a href="#repair_device_models_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fas fa fa-bolt"></i>
                            @lang('repair::lang.device_models')
                            @show_tooltip(__('repair::lang.device_models_tooltip'))
                        </a>
                    </li>
                    <li>
                        <a href="#repair_settings_tab" data-toggle="tab" aria-expanded="true">
                            <i class="fa fas fa-cogs"></i>
                            @lang('repair::lang.repair_settings')
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="repair_status_tab"> 
                        @includeIf('repair::status.index')
                    </div>
                    <!-- Device (Taxonomy)-->
                    <input type="hidden" name="category_type" id="category_type" value="device">
                    <div class="tab-pane taxonomy_body" id="repair_device_tab">
                    </div>
                    <!-- /Device (Taxonomy)-->
                    <div class="tab-pane" id="repair_device_models_tab">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('brand_id',  __('product.brand') . ':') !!}
                                    {!! Form::select('brand_id', $brands, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('device_id',  __('repair::lang.device') . ':') !!}
                                    {!! Form::select('device_id', $devices, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                        </div>
                        @includeIf('repair::device_model.index')
                    </div>
                    <div class="tab-pane" id="repair_settings_tab"> 
                        @includeIf('repair::settings.partials.repair_settings_tab')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('href');
            if ( target == '#repair_settings_tab') {
                //Repair Settings Tab Code
                $('#search_product').autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: '/purchases/get_products?check_enable_stock=false',
                            dataType: 'json',
                            data: {
                                term: request.term,
                            },
                            success: function(data) {
                                response(
                                    $.map(data, function(v, i) {
                                        if (v.variation_id) {
                                            return { label: v.text, value: v.variation_id };
                                        }
                                        return false;
                                    })
                                );
                            },
                        });
                    },
                    minLength: 2,
                    select: function(event, ui) {
                        $('#default_product')
                            .val(ui.item.value);
                        event.preventDefault();
                        $('#selected_default_product').text(ui.item.label);
                        $(this).val(ui.item.label);
                    },
                    focus: function(event, ui) {
                        event.preventDefault();
                        $(this).val(ui.item.label);
                    },
                });

                var data = [{
                  id: "",
                  text: '@lang("messages.please_select")',
                  html: '@lang("messages.please_select")',
                }, 
                @foreach($repair_statuses as $repair_status)
                    {
                    id: {{$repair_status->id}},
                    @if(!empty($repair_status->color))
                        text: '<i class="fa fa-circle" aria-hidden="true" style="color: {{$repair_status->color}};"></i> {{$repair_status->name}}',
                        title: '{{$repair_status->name}}'
                    @else
                        text: "{{$repair_status->name}}"
                    @endif
                    },
                @endforeach
                ];

                $("select#repair_status_id").select2({
                  data: data,
                  escapeMarkup: function(markup) {
                    return markup;
                  }
                });

                @if(!empty($repair_settings['default_status']))
                    $("select#repair_status_id").val({{$repair_settings['default_status']}}).change();
                @endif

                if ($('#repair_tc_condition').length) {
                    tinymce.init({
                        selector: 'textarea#repair_tc_condition',
                    });
                }
            }
        });
        //Repair Status Tab Code
        var status_table = $('#status_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{action('\Modules\Repair\Http\Controllers\RepairStatusController@index')}}",
                aaSorting: [[2, 'desc']],
                columnDefs: [ {
                    "targets": 3,
                    "orderable": false,
                    "searchable": false
                } ]
            });

        $(document).on('submit', 'form#status_form', function(e){
            e.preventDefault();
            $(this).find('button[type="submit"]').attr('disabled', true);
            var data = $(this).serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success == true){
                        $('div.view_modal').modal('hide');
                        toastr.success(result.msg);
                        status_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
        $(document).on('shown.bs.modal', '.view_modal', function() {
            $('input#color').colorpicker({format: 'hex'});
        })
        //Repair Device Model Code
        model_datatable = $("#model_table").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "/repair/device-models",
                        data:function(d) {
                            d.brand_id = $("#brand_id").val();
                            d.device_id = $("#device_id").val();
                        }
                    },
                    columnDefs: [
                        {
                            targets: [0, 2],
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    aaSorting: [[1, 'desc']],
                    columns: [
                        { data: 'action', name: 'action' },
                        { data: 'name', name: 'name' },
                        { data: 'repair_checklist', name: 'repair_checklist' },
                        { data: 'device_id', name: 'device_id' },
                        { data: 'brand_id', name: 'brand_id' },
                    ]
            });

        $(document).on('change', "#brand_id, #device_id", function(){
            model_datatable.ajax.reload();
        });

        $(document).on('click', '#add_device_model', function () {
            var url = $(this).data('href');
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('#device_model_modal').html(result).modal('show');
                }
            });
        });

        $(document).on('click', '.edit_device_model', function () {
            var url = $(this).data('href');
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'html',
                success: function(result) {
                    $('#device_model_modal').html(result).modal('show');
                }
            });
        });

        $('#device_model_modal').on('show.bs.modal', function (event) {
            $('form#device_model').validate();
            $("form#device_model .select2").select2();
        });

        $(document).on('submit', 'form#device_model', function(e){
            e.preventDefault();
            var url = $('form#device_model').attr('action');
            var method = $('form#device_model').attr('method');
            var data = $('form#device_model').serialize();
            $.ajax({
                method: method,
                dataType: "json",
                url: url,
                data:data,
                success: function(result){
                    if (result.success) {
                        $('#device_model_modal').modal("hide");
                        toastr.success(result.msg);
                        model_datatable.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });

        $(document).on('click', '#delete_a_model', function(e) {
            e.preventDefault();
            var url = $(this).data('href');
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((confirmed) => {
                if (confirmed) {
                    $.ajax({
                        method: 'DELETE',
                        url: url,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                toastr.success(result.msg);
                                model_datatable.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@includeIf('taxonomy.taxonomies_js')
@endsection