<script type="text/javascript">
	$(document).ready(function() {
		var columns = [
					{ data: 'action', name: 'action', searchable: false, sortable: false },
	                { data: 'username', name: 'username' },
	                { data: 'name', name: 'name', searchable: false, sortable: false },
	                { data: 'email', name: 'email' },
	                { data: 'crm_department', name: 'crm_department' },
                	{ data: 'crm_designation', name: 'crm_designation' }
	            ];

		contact_login_datatable = $("#contact_login_table").DataTable({
	            processing: true,
	            serverSide: true,
	            'ajax': {
	                url: "/crm/contact-login",
	                data: function (d) {
	                    d.contact_id = $('input#contact_id_for_login').val();
	                }
	            },
	            columns: columns,
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

		    if ($("#crm_contact_id").length > 0) {
		    	$("#crm_contact_id").select2();
		    }

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
		                contact_login_datatable.ajax.reload();
		            } else {
		                toastr.error(result.msg);
		            }
		        }
		    });
		});
	});
</script>