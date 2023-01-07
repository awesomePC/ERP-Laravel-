$(document).ready(function(){
	/**
	 * CRM MODULE
	 * contact login related code
	 */
	all_contact_login_datatable = $("#all_contact_login_table").DataTable({
            processing: true,
            serverSide: true,
            'ajax': {
                url: "/crm/contact-login",
                data: function (d) {
                    if ($("#contact_id").length > 0) {
                    	d.crm_contact_id = $("#contact_id").val();
                    }
                }
            },
            columns: [
                { data: 'action', name: 'action', searchable: false, sortable: false },
                { data: 'contact', name: 'contact', searchable: false, sortable: false },
                { data: 'username', name: 'username' },
                { data: 'name', name: 'name', searchable: false, sortable: false },
                { data: 'email', name: 'email' },
                { data: 'crm_department', name: 'crm_department' },
                { data: 'crm_designation', name: 'crm_designation' }
            ],
	});

	contact_login_datatable = $("#contact_login_table").DataTable({
            processing: true,
            serverSide: true,
            'ajax': {
                url: "/crm/contact-login",
                data: function (d) {
                    d.contact_id = $('input#contact_id_for_login').val();
                }
            },
            columns: [
					{ data: 'action', name: 'action', searchable: false, sortable: false },
	                { data: 'username', name: 'username' },
	                { data: 'name', name: 'name', searchable: false, sortable: false },
	                { data: 'email', name: 'email' },
	                { data: 'crm_department', name: 'crm_department' },
	            	{ data: 'crm_designation', name: 'crm_designation' }
	            ]
	});

	$(document).on('change', '#contact_id', function() {
		all_contact_login_datatable.ajax.reload();
	});

	$(document).on('click', '.contact-login-add', function () {
	    var url = $(this).data('href');
	    var data = {
	    			contact_id : $('input#contact_id_for_login').val(),
	    			crud_type: $("input#login_view_type").val()
	    		};
	    $.ajax({
	        method: 'GET',
	        url: url,
	        dataType: 'html',
	        data: data,
	        success: function(result) {
	            $('.contact_login_modal').html(result).modal('show');
	        }
	    });
	});

	$('.contact_login_modal').on('shown.bs.modal', function (e) {
	    $('.input-icheck').iCheck({
	        checkboxClass: 'icheckbox_square-blue'
	    });

	    if ($('form#contact_login_add').length > 0) {
	        $("form#contact_login_add").validate({
	            rules: {
	                first_name: {
	                    required: true,
	                },
	                email: {
	                    email: true,
	                    remote: {
	                        url: "/business/register/check-email",
	                        type: "post",
	                        data: {
	                            email: function() {
	                                return $( "#email" ).val();
	                            }
	                        }
	                    }
	                },
	                password: {
	                    required: true,
	                    minlength: 5
	                },
	                confirm_password: {
	                    equalTo: "#password"
	                },
	                username: {
	                    minlength: 5,
	                    remote: {
	                        url: "/business/register/check-username",
	                        type: "post",
	                        data: {
	                            username: function() {
	                                return $( "#username" ).val();
	                            }
	                        }
	                    }
	                }
	            },
	            messages: {
	                password: {
	                    minlength: 'Password should be minimum 5 characters',
	                },
	                confirm_password: {
	                    equalTo: 'Should be same as password'
	                },
	                username: {
	                    remote: 'Invalid username or User already exist'
	                },
	                email: {
	                    remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
	                }
	            }
	        });
	    }

	    if ($('form#contact_login_edit').length > 0) {
	        $("form#contact_login_edit").validate({
	            rules: {
	                first_name: {
	                    required: true,
	                },
	                email: {
	                    email: true,
	                    remote: {
	                        url: "/business/register/check-email",
	                        type: "post",
	                        data: {
	                            email: function() {
	                                return $( "#email" ).val();
	                            },
	                            user_id: $('input#user_id').val()
	                        }
	                    }
	                },
	                password: {
	                    minlength: 5
	                },
	                confirm_password: {
	                    equalTo: "#password"
	                }
	            },
	            messages: {
	                password: {
	                    minlength: 'Password should be minimum 5 characters',
	                },
	                confirm_password: {
	                    equalTo: 'Should be same as password'
	                },
	                email: {
	                    remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
	                }
	            }
	        });
	    }
	});

	$(document).on('submit', 'form#contact_login_add', function(e) {
	    e.preventDefault();
	    var data = $('form#contact_login_add').serialize();
	    var url = $('form#contact_login_add').attr('action');
	    $.ajax({
	        method: 'POST',
	        url: url,
	        dataType: 'json',
	        data: data,
	        success: function(result) {
	            if (result.success) {
	                $('.contact_login_modal').modal('hide');
	                toastr.success(result.msg);
	                all_contact_login_datatable.ajax.reload();
	                contact_login_datatable.ajax.reload();
	            } else {
	                toastr.error(result.msg);
	            }
	        }
	    });
	});

	$(document).on('click', '#delete_contact_login', function(e) {
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
	                        all_contact_login_datatable.ajax.reload();
	                        contact_login_datatable.ajax.reload();
	                    } else {
	                        toastr.error(result.msg);
	                    }
	                }
	            });
	        }
	    });
	});
	
	$(document).on('click', '.edit_contact_login', function() {
	    var url = $(this).data('href');
	    var data = {
	    			crud_type: $("input#login_view_type").val()
	    		};
	    $.ajax({
	        method: 'GET',
	        url: url,
	        dataType: 'html',
	        data: data,
	        success: function(result) {
	            $('.contact_login_modal').html(result).modal('show');
	        }
	    });
	});

	$(document).on('submit', 'form#contact_login_edit', function(e) {
	    e.preventDefault();
	    var data = $('form#contact_login_edit').serialize();
	    var url = $('form#contact_login_edit').attr('action');
	    $.ajax({
	        method: 'PUT',
	        url: url,
	        dataType: 'json',
	        data: data,
	        success: function(result) {
	            if (result.success) {
	                $('.contact_login_modal').modal('hide');
	                toastr.success(result.msg);
	                all_contact_login_datatable.ajax.reload();
	                contact_login_datatable.ajax.reload();
	            } else {
	                toastr.error(result.msg);
	            }
	        }
	    });
	});

	/**
	* Crm Ledger
	* related code
	*/

	$('#ledger_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#ledger_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
        }
    );

    $('#ledger_date_range').change( function(){
        getLedger();
    });

	$(document).on('click', '#create_ledger_pdf', function() {
	    var start_date = $('#ledger_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
	    var end_date = $('#ledger_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

	    var url = $(this).data('href') + '&start_date=' + start_date + '&end_date=' + end_date;
	    window.location = url;
	});

	/**
	* Crm Profile
	* edit/update related
	* code
	*/
	$('form#update_password').validate({
		errorPlacement: function(error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            current_password: {
                required: true,
                minlength: 5,
            },
            new_password: {
                required: true,
                minlength: 5,
            },
            confirm_password: {
                equalTo: '#new_password',
            },
        },
    });

    $("form#edit_contact_profile").validate({
    	errorPlacement: function(error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            first_name: {
                required: true,
            },
            email: {
                email: true,
                remote: {
                    url: "/business/register/check-email",
                    type: "post",
                    data: {
                        email: function() {
                            return $( "#email" ).val();
                        },
                        user_id: $('input#user_id').val()
                    }
                }
            }
        },
        messages: {
            email: {
                remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
            }
        }
    });

	/**
	* Crm Purchase
	* related code
	*/
	$('#date_range_filter').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#date_range_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           contact_purchase_datatable.ajax.reload();
        }
    );

	$('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range_filter').val('');
        contact_purchase_datatable.ajax.reload();
    });

	contact_purchase_datatable = $("#contact_purchase_table").DataTable({
		processing: true,
		serverSide: true,
		ajax: {
        url: '/contact/contact-purchases',
        data: function(d) {
            if ($('#payment_status_filter').length) {
                d.payment_status = $('#payment_status_filter').val();
            }
            if ($('#status_filter').length) {
                d.status = $('#status_filter').val();
            }

            var start = '';
            var end = '';
            if ($('#date_range_filter').val()) {
                start = $('input#date_range_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                end = $('input#date_range_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
            }
            d.start_date = start;
            d.end_date = end;
            },
        },
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'status', name: 'status' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'final_total', name: 'final_total' },
            { data: 'payment_due', name: 'payment_due', orderable: false, searchable: false },
            { data: 'added_by', name: 'u.first_name' },
        ],
        fnDrawCallback: function(oSettings) {
            var total_purchase = sum_table_col($('#contact_purchase_table'), 'final_total');
            $('#footer_purchase_total').text(total_purchase);

            var total_due = sum_table_col($('#contact_purchase_table'), 'payment_due');
            $('#footer_total_due').text(total_due);

            var total_purchase_return_due = sum_table_col($('#contact_purchase_table'), 'purchase_return');
            $('#footer_total_purchase_return_due').text(total_purchase_return_due);

            $('#footer_status_count').html(__sum_status_html($('#contact_purchase_table'), 'status-label'));

            $('#footer_payment_status_count').html(
                __sum_status_html($('#contact_purchase_table'), 'payment-status-label')
            );

            __currency_convert_recursively($('#contact_purchase_table'));
        },
        createdRow: function(row, data, dataIndex) {
            $(row)
                .find('td:eq(3)')
                .attr('class', 'clickable_td');
        },

	});

	$(document).on('change', '#status_filter, #payment_status_filter', function() {
        contact_purchase_datatable.ajax.reload();
    });

	/**
	* Crm schedule
	* related code
	*/
	$(document).on('click', '.btn-add-schedule', function() {
	    load_schedule_modal();
	});

	function load_schedule_modal() {
		var url = $("#schedule_create_url").val();
		$.ajax({
	        method: 'GET',
	        url: url,
	        async: false,
	        dataType: 'html',
	        success: function(result) {
	            $('.schedule').html(result).modal('show');
	        }
	    });
	}

	$('.schedule').on('show.bs.modal', function (event) {
		$('form#add_schedule').validate();

		$('form#add_schedule .datetimepicker').datetimepicker({
	        ignoreReadonly: true,
	        format: moment_date_format + ' ' + moment_time_format
	    });

	    $(".select2").select2();

	    $('input[type="checkbox"].input-icheck').iCheck({
	        checkboxClass: 'icheckbox_square-blue',
	    });

	    //initialize editor
	    tinymce.init({
	        selector: 'textarea#description',
	    });

	    $(document).on('ifChecked', '#allow_notification', function() {
	    	$("div").find('.allow_notification_elements').removeClass('hide');
	    });

	    $(document).on('ifUnchecked', '#allow_notification', function() {
	       $("div").find('.allow_notification_elements').addClass('hide');
	    });
	});

	$('.schedule').on('hidden.bs.modal', function(){
	    tinymce.remove("textarea#description");
	});

	$(document).on('submit', 'form#add_schedule', function(e){
	    e.preventDefault();
	    var url = $('form#add_schedule').attr('action');
	    var method = $('form#add_schedule').attr('method');
	    var data = $('form#add_schedule').serialize();
	    $.ajax({
	        method: method,
	        dataType: "json",
	        url: url,
	        data:data,
	        success: function(result){
	            if (result.success) {
	                $('.schedule').modal("hide");
	                toastr.success(result.msg);
	                if (result.schedule_for == 'lead') {
	                	initializeLeadScheduleDatatable();
	                }

	                if (typeof(follow_up_datatable) != 'undefined') {
					    follow_up_datatable.ajax.reload();
					}

	                if (typeof(leads_datatable) != 'undefined') {
					    leads_datatable.ajax.reload();
					}
	            } else {
	                toastr.error(result.msg);
	            }
	        }
	    });
	});

	$(document).on('click', '.schedule_delete', function(e) {
		e.preventDefault();
		var url = $(this).data('href');
		var view_type = $("#view_type").val();
		var data = {'view_type' : view_type};
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
	                data: data,
	                success: function(result) {
	                    if (result.success) {
	                        toastr.success(result.msg);
	                        if (result.view_type == 'lead_info') {
	                        	initializeLeadScheduleDatatable();
	                        } else if (result.view_type == 'schedule_info') {
	                        	setTimeout(() => {
	                        		location.replace(result.action);
								}, 6000);
                            }

	                        if (typeof(follow_up_datatable) != 'undefined') {
							    follow_up_datatable.ajax.reload();
							}

							if (typeof(recursive_follow_up_table) != 'undefined') {
							    recursive_follow_up_table.ajax.reload();
							}
	                    } else {
	                        toastr.error(result.msg);
	                    }
	                }
	            });
	        }
	    });
	});

	$(document).on('click', '.schedule_edit', function() {
		var url = $(this).data('href');
		var schedule_for = $("#schedule_for").val();
		var data = {'schedule_for' : schedule_for};
	    $.ajax({
	        method: 'GET',
	        url: url,
	        dataType: 'html',
	        data: data,
	        success: function(result) {
	            $('.edit_schedule').html(result).modal('show');
	        }
	    });
	});

	$('.edit_schedule').on('show.bs.modal', function (event) {
		$('form#edit_schedule').validate();

		$('form#edit_schedule .datetimepicker').datetimepicker({
	        ignoreReadonly: true,
	        format: moment_date_format + ' ' + moment_time_format
	    });

	    $(".select2").select2();

	    $('input[type="checkbox"].input-icheck').iCheck({
	        checkboxClass: 'icheckbox_square-blue',
	    });

	    //initialize editor
	    tinymce.init({
	        selector: 'textarea#schedule_description',
	    });

	    $(document).on('ifChecked', '#allow_notification', function() {
	    	$("div").find('.allow_notification_elements').removeClass('hide');
	    });

	    $(document).on('ifUnchecked', '#allow_notification', function() {
	       $("div").find('.allow_notification_elements').addClass('hide');
	    });

	    if (!$("#allow_notification").prop("checked")) {
	    	$("div").find('.allow_notification_elements').addClass('hide');
	    }
	});

	$('.edit_schedule').on('hidden.bs.modal', function(){
	    tinymce.remove("textarea#schedule_description");
	});

	$(document).on('submit', 'form#edit_schedule', function(e){
	    e.preventDefault();
	    var url = $('form#edit_schedule').attr('action');
	    var method = $('form#edit_schedule').attr('method');
	    var data = $('form#edit_schedule').serialize();
	    $.ajax({
	        method: method,
	        dataType: "json",
	        url: url,
	        data:data,
	        success: function(result){
	            if (result.success) {
	                $('.edit_schedule').modal("hide");
	                toastr.success(result.msg);
	                if (result.schedule_for == 'lead') {
	                	initializeLeadScheduleDatatable();
	                } else if(result.schedule_for == 'schedule_info'){
	                	location.reload();
	                }

	                if (typeof(follow_up_datatable) != 'undefined') {
					    follow_up_datatable.ajax.reload();
					}
	            } else {
	                toastr.error(result.msg);
	            }
	        }
	    });
	});


	/**
	* Crm schedule log
	* related code
	*/
	$(document).on('click', '.schedule_log_add, .add-schedule-log', function(e) {
		e.preventDefault();
		var url = $(this).data('href');
		if (typeof url == 'undefined') {
			url = $(this).attr('href');
		}
	    $.ajax({
	        method: 'GET',
	        url: url,
	        dataType: 'html',
	        success: function(result) {
	            $('.schedule_log_modal').html(result).modal('show');
	        }
	    });
	});

	$(document).on('click', '.edit_schedule_log', function() {
		var url = $(this).data('href');
	    $.ajax({
	        method: 'GET',
	        url: url,
	        dataType: 'html',
	        success: function(result) {
	            $('.schedule_log_modal').html(result).modal('show');
	        }
	    });
	});

	$('.schedule_log_modal').on('show.bs.modal', function (event) {
		$('form#schedule_log_form').validate();

		$('form#schedule_log_form .datetimepicker').datetimepicker({
	        ignoreReadonly: true,
	        format: moment_date_format + ' ' + moment_time_format
	    });

	    $(".select2").select2();

	    //initialize editor
	    tinymce.init({
	        selector: 'textarea#description',
	    });
	});

	$('.schedule_log_modal').on('hide.bs.modal', function(){
	    tinymce.remove("textarea#description");
	});

	$(document).on('submit', 'form#schedule_log_form', function(e){
	    e.preventDefault();
	    var url = $('form#schedule_log_form').attr('action');
	    var method = $('form#schedule_log_form').attr('method');
	    var data = $('form#schedule_log_form').serialize();
	    $.ajax({
	        method: method,
	        dataType: "json",
	        url: url,
	        data:data,
	        success: function(result){
	            if (result.success) {
	                $('.schedule_log_modal').modal("hide");
	                toastr.success(result.msg);
	                getScheduleLog();
	            } else {
	                toastr.error(result.msg);
	            }
	        }
	    });
	});

	$(document).on('click', '.delete_schedule_log', function(e) {
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
	                        getScheduleLog();
	                    } else {
	                        toastr.error(result.msg);
	                    }
	                }
	            });
	        }
	    });
	});

	$(document).on('click', '.view_a_schedule_log', function() {
		var url = $(this).data('href');
	    $.ajax({
	        method: 'GET',
	        url: url,
	        dataType: 'html',
	        success: function(result) {
	            $('.view_modal').html(result).modal('show');
	        }
	    });
	});

	$(document).on('click', '.load_more_log', function() {
	    var url = $(this).data('href');
	    var data = {schedule_id : $("input#schedule_id").val()};
	    $.ajax({
	        method:'GET',
	        dataType: 'json',
	        url: url,
	        data:data,
	        success: function(result){
	            if (result.success) {
	                $('.load_more_log').hide();
	                $(".timeline").append(result.log);
	            } else {
	                toastr.error(result.msg);
	            }
	        }
	    });
	});

	/**
	* Crm lead
	* related code
	*/
	$(document).on('click', '.btn-add-lead', function() {
		var url = $(this).data('href');
		$.ajax({
	        method: 'GET',
	        url: url,
	        dataType: 'html',
	        success: function(result) {
	            $('.contact_modal').html(result).modal('show');
	        }
	    });
	});

	$(document).on('click', '.delete_a_lead', function(e) {
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
	                        
	                        var lead_view = urlSearchParam('lead_view');

							if (lead_view == 'kanban') {
							    initializeLeadKanbanBoard();
							} else if(lead_view == 'list_view') {
							    leads_datatable.ajax.reload();
							}

	                    } else {
	                        toastr.error(result.msg);
	                    }
	                }
	            });
	        }
	    });
	});

	$(document).on('click', '.convert_to_customer', function() {
    	var url = $(this).data('href');
		$.ajax({
	        method: 'GET',
	        url: url,
	        dataType: 'json',
	        success: function(result) {
	            if (result.success) {
                    toastr.success(result.msg);
                    leads_datatable.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
	        }
	    });
    });

    $(document).on('click', '.edit_lead', function() {
        var url = $(this).data('href');
        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'html',
            success: function(result) {
                $('.contact_modal').html(result).modal('show');
            }
        });
    });
    
    $(document).on('change', "#life_stage, #source, #user_id", function(){
	   var lead_view = urlSearchParam('lead_view');
		if (lead_view == 'kanban') {
		    initializeLeadKanbanBoard();
		} else if(lead_view == 'list_view') {
		    leads_datatable.ajax.reload();
		}
	});

    $(document).on('change', '.lead_view', function() {
	    window.location.href = $(this).data('href');
	});

	KanbanBoard.prototype.initLeadKanban = function (boards, jKanbanElemSelector) {
	    var _this = this;
	    var kanban = new jKanban({
        element: jKanbanElemSelector,
        gutter: '5px',
        widthBoard: '320px',
        dragBoards: false,
        click: function (el) {
            //TODO: implement card clickable
            // _this.listApi.
            // getCard(el.dataset.eid).
            // then(_this.openCardModal.bind(_this));
        },
        dragEl: function (el, source) {
            $(el).addClass('dragging');
            isDraggingCard = true;
        },
        dragendEl: function (el) {
            $(el).removeClass('dragging');
            isDraggingCard = false;
        },
        dropEl: function (el, target, source, sibling) {
            var $el = $(el);

            $el.closest('.kanban-drag')[0]._ps.update();

            var $newParentLifeStage = $(target).parent('div.kanban-board').data('id');
            var $lifeStage = $(el).attr('data-parentid');

            //CRM MODULE:update life stage of lead in db
            if (!$('div.lead-kanban-board').hasClass('hide')) {
                if ($newParentLifeStage !== $lifeStage) {
                    var data = {
                        crm_life_stage : $newParentLifeStage,
                        lead_id: $(el).data('eid')
                    };

                    updateLeadLifeStageForKanban(data, $el);
                }
            }
        },

        addItemButton: false,
        boards: boards
	    });

	    initializeAutoScrollOnKanbanWhileCardDragging(kanban);

	    return kanban;
	};

	function updateLeadLifeStageForKanban(data, el) {
		$.ajax({
	        method: 'GET',
	        dataType: 'json',
	        url: '/crm/lead/' + data.lead_id + '/post-life-stage',
	        data:data,
	        success: function(result){
	            if (result.success) {
	                $(el).attr('data-parentid', data.crm_life_stage);
	                toastr.success(result.msg);
	            } else {
	                toastr.error(result.msg);
	            }
	        }
	    });
	}

	/**
	 * CRM MODULE
	 * campaign related code
	 */
	$(document).on('click', '.delete_a_campaign', function(e) {
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
	                        campaigns_datatable.ajax.reload();
	                    } else {
	                        toastr.error(result.msg);
	                    }
	                }
	            });
	        }
	    });
	});

	$(document).on('change', '#campaign_type_filter', function() {
	    campaigns_datatable.ajax.reload();
	});

	$('.campaign_modal').on('hidden.bs.modal', function(){
	    tinymce.remove("textarea#email_body");
	});

	if ($('form#campaign_form').length) {
		$('form#campaign_form').validate({
			rules: {
				'contact_id[]': {
					required: true
				},
				'lead_id[]': {
					required: true
				}
			},
			submitHandler: function(form) {
				if ($(form).valid()) {
					form.submit();
					$(".submit-button").prop( "disabled", true );
				}
			}
		});
		$(".select2").select2();

	    tinymce.init({
	        selector: 'textarea#email_body'
	    });

	    if ($('select#campaign_type').val() == 'sms') {
            $('div.email_div').hide();
            $('div.sms_div').show();
        } else if ($('select#campaign_type').val() == 'email') {
            $('div.email_div').show();
            $('div.sms_div').hide();
        }

        $('select#campaign_type').change(function() {
            var campaign_type = $(this).val();
            if (campaign_type == 'sms') {
                $('div.sms_div').show();
                $('div.email_div').hide();
            } else if (campaign_type == 'email') {
                $('div.email_div').show();
                $('div.sms_div').hide();
            }
        });
	}
	
	$(document).on('click', '.send_campaign_notification', function() {
		var url = $(this).data('href');
		$.ajax({
	        method: 'GET',
	        url: url,
	        dataType: 'json',
	        success: function(result) {
	            if (result.success) {
	                toastr.success(result.msg);
	                campaigns_datatable.ajax.reload();
	            } else {
	                toastr.error(result.msg);
	            }
	        }
	    });
	});

	$(document).on('click', '.view_a_campaign', function() {
		var url = $(this).data('href');
	    $.ajax({
	        method: 'GET',
	        url: url,
	        dataType: 'html',
	        success: function(result) {
	            $('.campaign_view_modal').html(result).modal('show');
	        }
	    });
	});
});

/**
 * CRM MODULE
 * Code after Document ready
 */

function get_todays_schedule() {
	$.ajax({
        method: 'GET',
        dataType: "json",
        url: '/crm/todays-follow-ups',
        success: function(result){
            if (result.success) {
                $(".todays_schedule_table").html(result.todays_schedule);
            } else {
                toastr.error(result.msg);
            }
        }
    });
}

function initializeLeadScheduleDatatable() {
	if((typeof lead_schedule_datatable == 'undefined')) {
		lead_schedule_datatable = $("#lead_schedule_table").DataTable({
				processing: true,
		        serverSide: true,
		        ajax: {
		            url: "/crm/lead-follow-ups",
		            data:function(d) {
		            	d.lead_id = $("#lead_id").val();
		            }
		        },
		        columnDefs: [
		            {
		                targets: [0, 6],
		                orderable: false,
		                searchable: false,
		            },
		        ],
		        aaSorting: [[1, 'desc']],
		        columns: [
		            { data: 'action', name: 'action' },
		            { data: 'title', name: 'title' },
		            { data: 'status', name: 'status' },
		            { data: 'schedule_type', name: 'schedule_type' },
		            { data: 'start_datetime', name: 'start_datetime' },
		            { data: 'end_datetime', name: 'end_datetime' },
		            { data: 'users', name: 'users' },
		        ] 
			});
	} else {
        lead_schedule_datatable.ajax.reload();
    }
}

function initializeCampaignDatatable() {
	if((typeof campaigns_datatable == 'undefined')) {
		campaigns_datatable = $("#campaigns_table").DataTable({
			processing: true,
		        serverSide: true,
		        ajax: {
		            url: "/crm/campaigns",
		            data:function(d) {
		            	d.campaign_type = $("#campaign_type_filter").val();
		            }
		        },
		        columnDefs: [
		            {
		                targets: [0, 3],
		                orderable: false,
		                searchable: false,
		            },
		        ],
		        aaSorting: [[4, 'desc']],
		        columns: [
		            { data: 'action', name: 'action' },
		            { data: 'name', name: 'name' },
		            { data: 'campaign_type', name: 'campaign_type' },
		            { data: 'createdBy', name: 'createdBy' },
		            { data: 'created_at', name: 'created_at' },
		        ] 
		});
	} else {
    	campaigns_datatable.ajax.reload();
	}
}

function getLedger() {

    var start_date = '';
    var end_date = '';

    if($('#ledger_date_range').val()) {
        start_date = $('#ledger_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
        end_date = $('#ledger_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
    $.ajax({
        url: '/contact/contact-get-ledger?start_date=' + start_date +'&end_date=' + end_date,
        dataType: 'html',
        success: function(result) {
            $('#contact_ledger_div')
                .html(result);
            __currency_convert_recursively($('#contact_ledger_div'));

            $('#ledger_table').DataTable({
                searching: false,
                ordering:false,
                paging:false,
                dom: 't'
            });
        },
    });
}

function getScheduleLog() {
	var data = {schedule_id : $("input#schedule_id").val()};
	$.ajax({
        method: 'GET',
        url: '/crm/follow-up-log',
        dataType: 'json',
        data:data,
        success: function(result) {
            if (result.success) {
                $(".timeline").html(result.log);
            } else {
                toastr.error(result.msg);
            }
        }
    });
}

function initializeLeadDatatable() {
	if((typeof leads_datatable == 'undefined')) {

		leads_datatable = $("#leads_table").DataTable({
				processing: true,
		        serverSide: true,
		        scrollY: "75vh",
		        scrollX: true,
		        scrollCollapse: true,
		        ajax: {
		            url: "/crm/leads",
		            data:function(d) {
		            	d.source = $("#source").val();
		            	d.life_stage = $("#life_stage").val();
		            	d.user_id = $("#user_id").val();
		            	d.lead_view = urlSearchParam('lead_view');
		            }
		        },
		        columnDefs: [
		            {
		                targets: [0, 5, 8, 9],
		                orderable: false,
		                searchable: false,
		            },
		        ],
		        aaSorting: [[6, 'desc']],
		        columns: [
		            { data: 'action', name: 'action' },
		            { data: 'contact_id', name: 'contact_id' },
		            { data: 'name', name: 'name' },
		            { data: 'mobile', name: 'mobile' },
		            { data: 'email', name: 'email' },
		            { data: 'crm_source', name: 'crm_source' },
		            { data: 'last_follow_up', name: 'last_follow_up', searchable: false},
		            { data: 'upcoming_follow_up', name: 'upcoming_follow_up', searchable: false},
		            { data: 'crm_life_stage', name: 'crm_life_stage' },
		            { data: 'leadUsers', name: 'leadUsers' },
		            { data: 'address', name: 'address', orderable: false },
		            { data: 'tax_number', name: 'tax_number' },
		            { data: 'created_at', name: 'created_at' },
		            { data: 'custom_field1', name: 'custom_field1' },
		            { data: 'custom_field2', name: 'custom_field2' },
		            { data: 'custom_field3', name: 'custom_field3' },
		            { data: 'custom_field4', name: 'custom_field4' },
		            { data: 'custom_field5', name: 'custom_field5'},
		            { data: 'custom_field6', name: 'custom_field6'},
		            { data: 'custom_field7', name: 'custom_field7'},
		            { data: 'custom_field8', name: 'custom_field8'},
		            { data: 'custom_field9', name: 'custom_field9'},
		            { data: 'custom_field10', name: 'custom_field10'}
		        ] 
			});
	} else {
        leads_datatable.ajax.reload();
    }
}

function initializeLeadKanbanBoard() {
	//before creating kanban board, set div to empty.
    $('div#myKanban').html('');
    lists = getLeadListForKanban();

    KanbanBoard.prototype.run = function () {
        var _this = this;
        _this.lists = lists;
        var boards = lists.
        map(function (l) {return _this.listToKanbanBoard(l);}).
        map(function (b) {return _this.processBoard(b);});
        var kanbanTest = _this.initLeadKanban(boards, '#myKanban');
        $('.meta-tasks').each(function (i, el) {
            return new PerfectScrollbar(el, { useBothWheelAxes: true });
        });

        // _this.setupUI(kanbanTest);
    };

    new KanbanBoard().run();
}

function getLeadListForKanban() {
	var lead_view = urlSearchParam('lead_view');
    var data = {
        source : $("#source").val(),
        lead_view : lead_view
    };

    var kanbanDataSet = [];
    $.ajax({
        method: 'GET',
        dataType: 'json',
        async: false,
        url: '/crm/leads',
        data: data,
        success: function(result) {
            if (result.success) {
                kanbanDataSet = result.leads_html;
            } else {
                toastr.error(result.msg);
            }
        }
    });

    return kanbanDataSet;
}

